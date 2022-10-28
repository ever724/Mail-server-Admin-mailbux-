<?php

namespace App\Repositories;

use App\Events\EstimateSentEvent;
use App\Interfaces\EstimateInterface;
use App\Mails\EstimateToCustomer;
use App\Models\Estimate;
use App\Models\Product;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EstimateRepository implements EstimateInterface
{
    /**
     * Return paginated and filtered results of estimates.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $company_id
     *
     * @return \App\Models\Estimate
     */
    public function getPaginatedFilteredEstimates(Request $request)
    {
        // Query Estimates by Company and Tab
        if ($request->tab == 'all') {
            $query = Estimate::findByCompany($request->currentCompany->id)->orderBy('estimate_number');
        } elseif ($request->tab == 'sent') {
            $query = Estimate::findByCompany($request->currentCompany->id)->nonArchived()->active()->orderBy('expiry_date');
        } else {
            $query = Estimate::findByCompany($request->currentCompany->id)->nonArchived()->drafts()->orderBy('estimate_number');
        }

        // Apply Filters and Paginate
        return QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('estimate_number'),
                AllowedFilter::exact('customer_id'),
                AllowedFilter::scope('from'),
                AllowedFilter::scope('to'),
                AllowedFilter::scope('status'),
            ])
            ->allowedIncludes([
                'customer',
                'company',
                'items',
                'taxes',
            ])
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single estimate by id.
     *
     * @param int $estimate_id
     *
     * @return \App\Models\Estimate
     */
    public function getEstimateById(Request $request, $estimate_id)
    {
        return Estimate::with(['customer', 'company', 'items', 'taxes'])->findByCompany($request->currentCompany->id)->findOrFail($estimate_id);
    }

    /**
     * Create a new Estimate Instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Estimate
     */
    public function newEstimate(Request $request)
    {
        $company = $request->currentCompany;

        // Get next Estimate number if the auto generation option is enabled
        $estimate_prefix = $company->getSetting('estimate_prefix');
        $next_estimate_number = Estimate::getNextestimateNumber($company->id, $estimate_prefix);

        // Create new Estimate model and set estimate_number and company_id
        // so that we can use them in the form
        $estimate = new Estimate();
        $estimate->estimate_number = $next_estimate_number ?? 0;
        $estimate->company_id = $company->id;

        return $estimate;
    }

    /**
     * Store a new Estimate.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Estimate
     */
    public function createEstimate(Request $request)
    {
        $company = $request->currentCompany;

        // Get company based settings
        $tax_per_item = (bool) $company->getSetting('tax_per_item');
        $discount_per_item = (bool) $company->getSetting('discount_per_item');

        // Save Estimate to Database
        $estimate = Estimate::create([
            'estimate_date' => $request->estimate_date,
            'expiry_date' => $request->expiry_date,
            'estimate_number' => $request->estimate_number,
            'reference_number' => $request->reference_number,
            'customer_id' => $request->customer_id,
            'company_id' => $company->id,
            'status' => Estimate::STATUS_DRAFT,
            'discount_type' => 'percent',
            'discount_val' => $request->total_discount ?? 0,
            'sub_total' => $request->sub_total,
            'total' => $request->grand_total,
            'notes' => $request->notes,
            'private_notes' => $request->private_notes,
            'tax_per_item' => $tax_per_item,
            'discount_per_item' => $discount_per_item,
            'template_id' => $request->template_id,
        ]);

        // Arrays of data for storing Estimate Items
        $products = $request->product;
        $quantities = $request->quantity;
        $taxes = $request->taxes;
        $prices = $request->price;
        $totals = $request->total;
        $discounts = $request->discount;

        // Add products (estimate items)
        for ($i = 0; $i < count($products); $i++) {
            $product = Product::firstOrCreate(
                ['id' => $products[$i], 'company_id' => $company->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'hide' => 1]
            );

            $item = $estimate->items()->create([
                'product_id' => $product->id,
                'company_id' => $company->id,
                'quantity' => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val' => $discounts[$i] ?? 0,
                'price' => $prices[$i],
                'total' => $totals[$i],
            ]);

            // Add taxes for Estimate Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax,
                    ]);
                }
            }
        }

        // If Estimate based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $estimate->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Update custom field values
        $estimate->addCustomFields($request->custom_fields);

        // Record product
        // $company->subscription('main')->recordFeatureUsage('estimates_per_month');

        return $estimate;
    }

    /**
     * Update an existing Estimate.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $estimate_id
     *
     * @return \App\Models\Estimate
     */
    public function updateEstimate(Request $request, $estimate_id)
    {
        // Find Estimate or Fail (404 Http Error)
        $estimate = $this->getEstimateById($request, $estimate_id);
        $company = $estimate->company;

        // Update Estimate
        $estimate->update([
            'estimate_date' => $request->estimate_date,
            'expiry_date' => $request->expiry_date,
            'estimate_number' => $request->estimate_number,
            'reference_number' => $request->reference_number,
            'customer_id' => $request->customer_id,
            'discount_type' => 'percent',
            'discount_val' => $request->total_discount ?? 0,
            'sub_total' => $request->sub_total,
            'total' => $request->grand_total,
            'notes' => $request->notes,
            'private_notes' => $request->private_notes,
            'template_id' => $request->template_id,
        ]);

        // Posted Values
        $products = $request->product;
        $quantities = $request->quantity;
        $taxes = $request->taxes;
        $prices = $request->price;
        $totals = $request->total;
        $discounts = $request->discount;

        // Remove old estimate items
        $estimate->items()->delete();

        // Add products (estimate items)
        for ($i = 0; $i < count($products); $i++) {
            $product = Product::firstOrCreate(
                ['id' => $products[$i], 'company_id' => $company->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'hide' => 1]
            );

            $item = $estimate->items()->create([
                'product_id' => $product->id,
                'company_id' => $company->id,
                'quantity' => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val' => $discounts[$i] ?? 0,
                'price' => $prices[$i],
                'total' => $totals[$i],
            ]);

            // Add taxes for Estimate Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax,
                    ]);
                }
            }
        }

        // Remove old estimate taxes
        $estimate->taxes()->delete();

        // If Estimate based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $estimate->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Update custom field values
        $estimate->updateCustomFields($request->custom_fields);

        return $estimate;
    }

    /**
     * Send email to the customer.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $estimate_id
     *
     * @return bool
     */
    public function sendEstimateEmail(Request $request, $estimate_id)
    {
        $estimate = $this->getEstimateById($request, $estimate_id);

        // Send mail to customer
        try {
            Mail::to($estimate->customer->email)->send(new EstimateToCustomer($estimate));

            // Log the activity
            activity()->on($estimate->customer)->by($estimate)->log(__('messages.activity_estimate_emailed', [
                'estimate_number' => $estimate->estimate_number,
            ]));

            // Change the status of the Estimate
            if ($estimate->status == Estimate::STATUS_DRAFT) {
                $estimate->status = Estimate::STATUS_SENT;
                $estimate->save();
            }

            // Dispatch EstimateSentEvent
            EstimateSentEvent::dispatch($estimate);

            return true;
        } catch (\Exception $e) {
            session()->flash('alert-danger', __('messages.email_could_not_sent'));

            return false;
        }
    }

    /**
     * Mark Estimate status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $estimate_id
     *
     * @return \App\Models\Estimate
     */
    public function markEstimateStatus(Request $request, $estimate_id)
    {
        $estimate = $this->getEstimateById($request, $estimate_id);

        // Mark the Estimate by given status
        if ($request->status && strtoupper($request->status) == 'SENT') {
            $estimate->status = Estimate::STATUS_SENT;
        } elseif ($request->status && strtoupper($request->status) == 'ACCEPTED') {
            $estimate->status = Estimate::STATUS_ACCEPTED;
        } elseif ($request->status && strtoupper($request->status) == 'REJECTED') {
            $estimate->status = Estimate::STATUS_REJECTED;
        } elseif ($request->status && strtoupper($request->status) == 'DRAFT') {
            $estimate->status = Estimate::STATUS_DRAFT;
        }

        // Save the status
        $estimate->save();

        return $estimate;
    }

    /**
     * Conver Estimate to Invoice.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $estimate_id
     *
     * @return \App\Models\Invoice|bool
     */
    public function convertEstimateToInvoice(Request $request, $estimate_id)
    {
        // Find Estimate or Fail (404 Http Error)
        $estimate = $this->getEstimateById($request, $estimate_id);
        $company = $estimate->company;

        // Redirect back
        /*  $canAdd = $company->subscription('main')->canUseFeature('invoices_per_month');
        if (!$canAdd) {
            session()->flash('alert-danger', __('messages.you_have_reached_the_limit'));
            return false;
        } */

        // Convert to Invoice
        return $estimate->convertToInvoice();
        // Record usage
        //$company->subscription('main')->recordFeatureUsage('invoices_per_month');
    }

    /**
     * Delete Estimate by Id.
     *
     * @param int $estimate_id
     *
     * @return bool
     */
    public function deleteEstimate(Request $request, $estimate_id)
    {
        $estimate = $this->getEstimateById($request, $estimate_id);

        // Reduce feature
        $estimate->company->subscription('main')->reduceFeatureUsage('estimates_per_month');

        return $estimate->delete();
    }
}

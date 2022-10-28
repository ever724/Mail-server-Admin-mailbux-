<?php

namespace App\Repositories;

use App\Events\CreditNoteSentEvent;
use App\Interfaces\CreditNoteInterface;
use App\Mails\CreditNoteToCustomer;
use App\Models\CreditNote;
use App\Models\Product;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CreditNoteRepository implements CreditNoteInterface
{
    /**
     * Get filtered and paginated Credit Notes.
     *
     * @return \App\Models\CreditNote
     */
    public function getPaginatedFilteredCreditNotes(Request $request)
    {
        return QueryBuilder::for(CreditNote::findByCompany($request->currentCompany->id))
            ->allowedFilters([
                AllowedFilter::partial('credit_note_number'),
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
                'refunds',
                'applied_payments',
            ])
            ->paginate()->appends(request()->query());
    }

    /**
     * Get Credit Note by given id.
     *
     * @param int $credit_note_id
     *
     * @return \App\Models\CreditNote
     */
    public function getCreditNoteById(Request $request, $credit_note_id)
    {
        return CreditNote::with(['customer', 'company', 'items', 'taxes', 'refunds', 'applied_payments'])->findByCompany($request->currentCompany->id)->findOrFail($credit_note_id);
    }

    /**
     * Create Credit Note instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\CreditNote
     */
    public function newCreditNote(Request $request)
    {
        $company = $request->currentCompany;

        // Get next Credit Note number if the auto generation option is enabled
        $credit_note_prefix = $company->getSetting('credit_note_prefix');
        $next_credit_note_number = CreditNote::getNextCreditNoteNumber($company->id, $credit_note_prefix);

        // Create new CreditNote model and set credit_note_number and company_id
        // so that we can use them in the form
        $credit_note = new CreditNote();
        $credit_note->credit_note_number = $next_credit_note_number ?? 0;
        $credit_note->company_id = $company->id;

        return $credit_note;
    }

    /**
     * Store a newly created resource in database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\CreditNote
     */
    public function createCreditNote(Request $request)
    {
        $company = $request->currentCompany;

        // Get company based settings
        $tax_per_item = (bool) $company->getSetting('tax_per_item');
        $discount_per_item = (bool) $company->getSetting('discount_per_item');

        // Save CreditNote to Database
        $credit_note = CreditNote::create([
            'credit_note_date' => $request->credit_note_date,
            'credit_note_number' => $request->credit_note_number,
            'reference_number' => $request->reference_number,
            'customer_id' => $request->customer_id,
            'company_id' => $company->id,
            'status' => CreditNote::STATUS_DRAFT,
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

        // Arrays of data for storing CreditNote Items
        $products = $request->product;
        $quantities = $request->quantity;
        $taxes = $request->taxes;
        $prices = $request->price;
        $totals = $request->total;
        $discounts = $request->discount;

        // Add products (credit_note items)
        for ($i = 0; $i < count($products); $i++) {
            $product = Product::firstOrCreate(
                ['id' => $products[$i], 'company_id' => $company->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'hide' => 1]
            );

            $item = $credit_note->items()->create([
                'product_id' => $product->id,
                'company_id' => $company->id,
                'quantity' => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val' => $discounts[$i] ?? 0,
                'price' => $prices[$i],
                'total' => $totals[$i],
            ]);

            // Add taxes for CreditNote Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax,
                    ]);
                }
            }
        }

        // If CreditNote based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $credit_note->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Update custom field values
        $credit_note->addCustomFields($request->custom_fields);

        return $credit_note;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $credit_note_id
     *
     * @return \App\Models\CreditNote
     */
    public function updateCreditNote(Request $request, $credit_note_id)
    {
        // Find CreditNote or Fail (404 Http Error)
        $credit_note = $this->getCreditNoteById($request, $credit_note_id);
        $company = $credit_note->company;

        // Update CreditNote
        $credit_note->update([
            'credit_note_date' => $request->credit_note_date,
            'credit_note_number' => $request->credit_note_number,
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

        // Remove old credit_note items
        $credit_note->items()->delete();

        // Add products (credit_note items)
        for ($i = 0; $i < count($products); $i++) {
            $product = Product::firstOrCreate(
                ['id' => $products[$i], 'company_id' => $company->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'hide' => 1]
            );

            $item = $credit_note->items()->create([
                'product_id' => $product->id,
                'company_id' => $company->id,
                'quantity' => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val' => $discounts[$i] ?? 0,
                'price' => $prices[$i],
                'total' => $totals[$i],
            ]);

            // Add taxes for CreditNote Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax,
                    ]);
                }
            }
        }

        // Remove old credit_note taxes
        $credit_note->taxes()->delete();

        // If CreditNote based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $credit_note->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Update custom field values
        $credit_note->updateCustomFields($request->custom_fields);

        return $credit_note;
    }

    /**
     * Send email to the customer.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $credit_note_id
     *
     * @return bool
     */
    public function sendCreditNoteEmail(Request $request, $credit_note_id)
    {
        $credit_note = $this->getCreditNoteById($request, $credit_note_id);

        // Send mail to customer
        try {
            Mail::to($credit_note->customer->email)->send(new CreditNoteToCustomer($credit_note));

            // Log the activity
            activity()->on($credit_note->customer)->by($credit_note)->log(__('messages.activity_credit_note_emailed', [
                'credit_note_number' => $credit_note->credit_note_number,
            ]));

            // Change the status of the CreditNote
            if ($credit_note->status == CreditNote::STATUS_DRAFT) {
                $credit_note->status = CreditNote::STATUS_SENT;
                $credit_note->save();
            }

            // Dispatch CreditNoteSentEvent
            CreditNoteSentEvent::dispatch($credit_note);

            return true;
        } catch (\Exception $e) {
            session()->flash('alert-danger', __('messages.email_could_not_sent'));

            return false;
        }
    }

    /**
     * Mark Credit Note status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $credit_note_id
     *
     * @return \App\Models\CreditNote
     */
    public function markCreditNoteStatus(Request $request, $credit_note_id)
    {
        $credit_note = $this->getCreditNoteById($request, $credit_note_id);

        // Mark the Credit Note by given status
        if ($request->status && strtoupper($request->status) == 'SENT') {
            $credit_note->status = CreditNote::STATUS_SENT;
        } elseif ($request->status && strtoupper($request->status) == 'DRAFT') {
            $credit_note->status = CreditNote::STATUS_DRAFT;
        }

        // Save the status
        $credit_note->save();

        return $credit_note;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $credit_note_id
     *
     * @return bool
     */
    public function deleteCreditNote(Request $request, $credit_note_id)
    {
        $credit_note = $this->getCreditNoteById($request, $credit_note_id);

        // Delete payments
        foreach ($credit_note->applied_payments as $payment) {
            // Delete
            $payment->deleteModel();
        }

        // Delete CreditNote from Database
        return $credit_note->delete();
    }
}

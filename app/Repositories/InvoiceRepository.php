<?php

namespace App\Repositories;

use App\Events\InvoiceSentEvent;
use App\Interfaces\InvoiceInterface;
use App\Mails\InvoiceToCustomer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceRepository implements InvoiceInterface
{
    /**
     * Return paginated and filtered results of invoices by company.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $company_id
     *
     * @return \App\Models\Invoice
     */
    public function getPaginatedFilteredInvoices(Request $request)
    {
        // Query Invoices by Company and Tab
        if ($request->tab == 'all') {
            $query = Invoice::findByCompany($request->currentCompany->id)->orderBy('invoice_number', 'desc');
        } elseif ($request->tab == 'due') {
            $query = Invoice::findByCompany($request->currentCompany->id)->nonArchived()->unpaid()->nonDraft()->whereDate('due_date', '>=', now())->orderBy('due_date');
        } elseif ($request->tab == 'overdue') {
            $query = Invoice::findByCompany($request->currentCompany->id)->nonArchived()->unpaid()->nonDraft()->whereDate('due_date', '<=', now())->orderBy('due_date');
        } elseif ($request->tab == 'recurring') {
            $query = Invoice::findByCompany($request->currentCompany->id)->nonArchived()->recurring()->nonDraft()->orderBy('invoice_number', 'desc');
        } elseif ($request->tab == 'archived') {
            $query = Invoice::findByCompany($request->currentCompany->id)->archived()->orderBy('invoice_number', 'desc');
        } else {
            $query = Invoice::findByCompany($request->currentCompany->id)->nonArchived()->drafts()->orderBy('invoice_number', 'desc');
        }

        // Apply Filters and Paginate
        return QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::partial('invoice_number'),
                AllowedFilter::exact('customer_id'),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('paid_status'),
                AllowedFilter::scope('from'),
                AllowedFilter::scope('to'),
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
     * Return a single invoice by id.
     *
     * @param int $invoice_id
     *
     * @return \App\Models\Invoice
     */
    public function getInvoiceById(Request $request, $invoice_id)
    {
        return Invoice::with(['customer', 'company', 'items', 'taxes'])->findByCompany($request->currentCompany->id)->findOrFail($invoice_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $company_id
     *
     * @return \App\Models\Invoice
     */
    public function newInvoice(Request $request)
    {
        // Get next Invoice number if the auto generation option is enabled
        $invoice_prefix = $request->currentCompany->getSetting('invoice_prefix');
        $next_invoice_number = Invoice::getNextInvoiceNumber($request->currentCompany->id, $invoice_prefix);

        // Create new number model and set invoice_number and company_id
        // so that we can use them in the form
        $invoice = new Invoice();
        $invoice->invoice_number = $next_invoice_number;
        $invoice->company_id = $request->currentCompany->id;

        return $invoice;
    }

    /**
     * Store the Invoice on the database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Invoice
     */
    public function createInvoice(Request $request)
    {
        $company = $request->currentCompany;

        // Get company based settings
        $tax_per_item = (bool) $company->getSetting('tax_per_item');
        $discount_per_item = (bool) $company->getSetting('discount_per_item');

        // Save Invoice to Database
        $invoice = Invoice::create([
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'invoice_number' => $request->invoice_number,
            'reference_number' => $request->reference_number,
            'customer_id' => $request->customer_id,
            'company_id' => $company->id,
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'sub_total' => $request->sub_total,
            'discount_type' => 'percent',
            'discount_val' => $request->total_discount ?? 0,
            'total' => $request->grand_total,
            'due_amount' => $request->grand_total,
            'notes' => $request->notes,
            'private_notes' => $request->private_notes,
            'tax_per_item' => $tax_per_item,
            'discount_per_item' => $discount_per_item,
            'is_recurring' => $request->is_recurring,
            'cycle' => $request->cycle,
            'template_id' => $request->template_id,
        ]);

        // Set next recurring date
        if ($invoice->is_recurring) {
            $invoice->next_recurring_at = Carbon::parse($invoice->invoice_date)->addMonths($invoice->is_recurring)->format('Y-m-d');
            $invoice->save();
        }

        // Arrays of data for storing Invoice Items
        $products = $request->product;
        $quantities = $request->quantity;
        $taxes = $request->taxes;
        $prices = $request->price;
        $totals = $request->total;
        $discounts = $request->discount;

        // Add products (invoice items)
        for ($i = 0; $i < count($products); $i++) {
            $product = Product::firstOrCreate(
                ['id' => $products[$i], 'company_id' => $company->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'hide' => 1]
            );

            $item = $invoice->items()->create([
                'product_id' => $product->id,
                'company_id' => $company->id,
                'quantity' => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val' => $discounts[$i] ?? 0,
                'price' => $prices[$i],
                'total' => $totals[$i],
            ]);

            // Add taxes for Invoice Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax,
                    ]);
                }
            }
        }

        // If Invoice based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $invoice->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Add custom field values
        $invoice->addCustomFields($request->custom_fields);

        // Record product
        //$company->subscription('main')->recordFeatureUsage('invoices_per_month');

        return $invoice;
    }

    /**
     * Update the Invoice on the database.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $invoice_id
     *
     * @return \App\Models\Invoice
     */
    public function updateInvoice(Request $request, $invoice_id)
    {
        // Find Invoice or Fail (404 Http Error)
        $invoice = $this->getInvoiceById($request, $invoice_id);
        $company = $invoice->company;

        // Getting old amount
        $oldAmount = $invoice->total;
        if ($oldAmount != $request->total) {
            $oldAmount = (int) round($request->grand_total) - (int) $oldAmount;
        } else {
            $oldAmount = 0;
        }

        // Update Invoice due_amount
        $invoice->due_amount = ($invoice->due_amount + $oldAmount);

        // Update Invoice status based on new due amount
        if ($invoice->due_amount == 0 && $invoice->paid_status != Invoice::STATUS_PAID) {
            $invoice->status = Invoice::STATUS_COMPLETED;
            $invoice->paid_status = Invoice::STATUS_PAID;
        } elseif ($invoice->due_amount < 0 && $invoice->paid_status != Invoice::STATUS_UNPAID) {
            session()->flash('alert-danger', __('messages.invalid_due_amount'));

            return redirect()->route('invoices.edit', ['invoice' => $invoice->id, 'company_uid' => $company->uid]);
        } elseif ($invoice->due_amount != 0 && $invoice->paid_status == Invoice::STATUS_PAID) {
            $invoice->status = $invoice->getPreviousStatus();
            $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
        }

        // Update Invoice
        $invoice->update([
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'invoice_number' => $request->invoice_number,
            'reference_number' => $request->reference_number,
            'customer_id' => $request->customer_id,
            'discount_type' => 'percent',
            'discount_val' => $request->total_discount ?? 0,
            'sub_total' => $request->sub_total,
            'total' => $request->grand_total,
            'notes' => $request->notes,
            'private_notes' => $request->private_notes,
            'is_recurring' => $request->is_recurring,
            'cycle' => $request->cycle,
            'template_id' => $request->template_id,
        ]);

        // Set next recurring date
        if ($invoice->is_recurring) {
            $invoice->next_recurring_at = Carbon::parse($invoice->invoice_date)->addMonths($invoice->is_recurring)->format('Y-m-d');
            $invoice->save();
        }

        // Posted Values
        $products = $request->product;
        $quantities = $request->quantity;
        $taxes = $request->taxes;
        $prices = $request->price;
        $totals = $request->total;
        $discounts = $request->discount;

        // Remove old invoice items
        $invoice->items()->delete();

        // Add products (invoice items)
        for ($i = 0; $i < count($products); $i++) {
            $product = Product::firstOrCreate(
                ['id' => $products[$i], 'company_id' => $company->id],
                ['name' => $products[$i], 'price' => $prices[$i], 'hide' => 1]
            );

            $item = $invoice->items()->create([
                'product_id' => $product->id,
                'company_id' => $company->id,
                'quantity' => $quantities[$i],
                'discount_type' => 'percent',
                'discount_val' => $discounts[$i] ?? 0,
                'price' => $prices[$i],
                'total' => $totals[$i],
            ]);

            // Add taxes for Invoice Item if it is given
            if ($taxes && array_key_exists($i, $taxes)) {
                foreach ($taxes[$i] as $tax) {
                    $item->taxes()->create([
                        'tax_type_id' => $tax,
                    ]);
                }
            }
        }

        // Remove old invoice taxes
        $invoice->taxes()->delete();

        // If Invoice based taxes are given
        if ($request->has('total_taxes')) {
            foreach ($request->total_taxes as $tax) {
                $invoice->taxes()->create([
                    'tax_type_id' => $tax,
                ]);
            }
        }

        // Update custom field values
        $invoice->updateCustomFields($request->custom_fields);

        return $invoice;
    }

    /**
     * Send email to the customer.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $invoice_id
     *
     * @return bool
     */
    public function sendInvoiceEmail(Request $request, $invoice_id)
    {
        $invoice = $this->getInvoiceById($request, $invoice_id);

        // Send mail to customer
        try {
            Mail::to($invoice->customer->email)->send(new InvoiceToCustomer($invoice));

            // Log the activity
            activity()->on($invoice->customer)->by($invoice)->log(__('messages.activity_invoice_emailed', [
                'invoice_number' => $invoice->invoice_number,
            ]));

            // Change the status of the Invoice
            if ($invoice->status == Invoice::STATUS_DRAFT) {
                $invoice->status = Invoice::STATUS_SENT;
                $invoice->sent = true;
                $invoice->save();
            }

            // Dispatch InvoiceSentEvent
            InvoiceSentEvent::dispatch($invoice);

            return true;
        } catch (\Exception $th) {
            session()->flash('alert-danger', __('messages.email_could_not_sent'));

            return false;
        }
    }

    /**
     * Mark Invoice status.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $invoice_id
     *
     * @return \App\Models\Invoice
     */
    public function markInvoiceStatus(Request $request, $invoice_id)
    {
        $invoice = $this->getInvoiceById($request, $invoice_id);

        // Mark the Invoice by given status
        if ($request->status && strtoupper($request->status) == 'SENT') {
            $invoice->status = Invoice::STATUS_SENT;
            $invoice->sent = true;
        } elseif ($request->status && strtoupper($request->status) == 'PAID') {
            $invoice->status = Invoice::STATUS_COMPLETED;
            $invoice->paid_status = Invoice::STATUS_PAID;
        } elseif ($request->status && strtoupper($request->status) == 'UNPAID') {
            $invoice->paid_status = Invoice::STATUS_UNPAID;
        }

        // Save the status
        $invoice->save();

        return $invoice;
    }

    /**
     * Delete the invoice.
     *
     * @param int $invoice_id
     *
     * @return bool
     */
    public function deleteInvoice(Request $request, $invoice_id)
    {
        $invoice = $this->getInvoiceById($request, $invoice_id);
        $company = $invoice->company;

        // return error if payment already exists with the invoice
        if ($invoice->payments()->exists() && $invoice->payments()->count() > 0) {
            session()->flash('alert-danger', __('messages.invoice_cant_delete'));

            return false;
        }

        // Reduce feature
        $company->subscription('main')->reduceFeatureUsage('invoices_per_month');

        return $invoice->delete();
    }
}

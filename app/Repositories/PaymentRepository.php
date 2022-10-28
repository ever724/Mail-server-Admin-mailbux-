<?php

namespace App\Repositories;

use App\Interfaces\PaymentInterface;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\QueryBuilder\AllowedFilter;
use App\Services\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class PaymentRepository implements PaymentInterface
{
    /**
     * Return paginated and filtered results of payments by company.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $company_id
     *
     * @return \App\Models\Payment
     */
    public function getPaginatedFilteredPayments(Request $request)
    {
        return QueryBuilder::for(Payment::findByCompany($request->currentCompany->id))
            ->allowedFilters([
                AllowedFilter::partial('payment_number'),
                AllowedFilter::exact('payment_method_id'),
                AllowedFilter::exact('invoice_id'),
                AllowedFilter::exact('credit_note_id'),
                AllowedFilter::exact('customer_id'),
                AllowedFilter::scope('from'),
                AllowedFilter::scope('to'),
            ])
            ->allowedIncludes([
                'company',
                'customer',
                'payment_method',
                'invoice',
                'credit_note',
            ])
            ->oldest()
            ->paginate()->appends(request()->query());
    }

    /**
     * Return a single payment by id.
     *
     * @param int $payment_id
     *
     * @return \App\Models\Payment
     */
    public function getPaymentById(Request $request, $payment_id)
    {
        return Payment::with(['customer', 'company', 'payment_method', 'invoice', 'credit_note'])->findByCompany($request->currentCompany->id)->findOrFail($payment_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company_id
     *
     * @return \App\Models\Payment
     */
    public function newPayment(Request $request)
    {
        $company = $request->currentCompany;

        // Get next Payment number if the auto generation option is enabled
        $payment_prefix = $company->getSetting('payment_prefix');
        $next_payment_number = Payment::getNextPaymentNumber($company->id, $payment_prefix);

        // Create new Payment model and set estimate_number and company_id
        // so that we can use them in the form
        $payment = new Payment();
        $payment->payment_number = $next_payment_number ?? 0;
        $payment->company_id = $company->id;

        // If the request has invoice parameter then set
        // invoice_id and customer_id from the given invoice.
        if ($request->has('invoice')) {
            $invoice = Invoice::find($request->invoice);

            // Checking if invoice is exist
            if ($invoice) {
                $payment->invoice_id = $invoice->id;
                $payment->customer_id = $invoice->customer_id;
                $payment->amount = $invoice->due_amount;
            }
        }

        // If the request has credit_note parameter then set credit_note_id
        if ($request->has('credit_note')) {
            $credit_note = CreditNote::find($request->credit_note);

            // Checking if credit_note is exist
            if ($credit_note) {
                $payment->credit_note_id = $credit_note->id;
                $payment_method = PaymentMethod::firstOrCreate(['name' => $credit_note->display_name]);
                $payment->payment_method_id = $payment_method->id;
            }
        }

        return $payment;
    }

    /**
     * Create a Payment.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\Payment
     */
    public function createPayment(Request $request)
    {
        $company = $request->currentCompany;

        // If the request has credit_note parameter
        if ($request->has('credit_note_id')) {
            $credit_note = CreditNote::find($request->credit_note_id);
            // Check payment amount less than credit note amount
            if ($credit_note && $request->amount > $credit_note->remaining_balance) {
                return redirect()->back()->withErrors(['amount' => __('messages.invalid_amount')]);
            }
        }

        // Create Payment and Store in Database
        $payment = Payment::create([
            'payment_date' => $request->payment_date,
            'payment_number' => $request->payment_number,
            'customer_id' => $request->customer_id,
            'credit_note_id' => $request->credit_note_id,
            'company_id' => $company->id,
            'invoice_id' => $request->invoice_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'private_notes' => $request->private_notes,
        ]);

        // Add custom field values
        $payment->addCustomFields($request->custom_fields);

        // Find the Invoice which belongs to Payment
        $invoice = Invoice::findOrFail($payment->invoice_id);

        // Update the status to complete and paid if the payment amount
        // is the same with the amount of invoice
        if ($invoice->due_amount == $payment->amount) {
            $invoice->status = Invoice::STATUS_COMPLETED;
            $invoice->paid_status = Invoice::STATUS_PAID;
            $invoice->due_amount = 0;

        // If it is partially paid then set status to partially paid
        } elseif ($invoice->due_amount != $payment->amount) {
            $invoice->due_amount = (int) $invoice->due_amount - (int) $payment->amount;

            // If the due_amount is negative delete the payment then go back
            if ($invoice->due_amount < 0) {
                $payment->delete();

                return redirect()->back()->withErrors(['amount' => __('messages.invalid_amount')]);
            }

            // Set status to partially paid
            $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
        }

        // Update the Invoice
        $invoice->save();

        return $payment;
    }

    /**
     * Update a Payment.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $payment_id
     *
     * @return \App\Models\Payment
     */
    public function updatePayment(Request $request, $payment_id)
    {
        $payment = $this->getPaymentById($request, $payment_id);
        $oldAmount = $payment->amount;

        // Check whether the amount is updated or not.
        // If it updated then update the invoice
        if ($oldAmount != $request->amount) {
            $amount = (int) $request->amount - (int) $oldAmount;
            $invoice = Invoice::findOrFail($request->invoice_id);
            $invoice->due_amount = (int) $invoice->due_amount - (int) $amount;

            // If the due_amount is negative then go back
            if ($invoice->due_amount < 0) {
                return redirect()->back()->withErrors(['amount' => __('messages.invalid_amount')]);
            }

            // Set new Invoice status
            if ($invoice->due_amount == 0) {
                $invoice->status = Invoice::STATUS_COMPLETED;
                $invoice->paid_status = Invoice::STATUS_PAID;
            } else {
                $invoice->status = $invoice->getPreviousStatus();
                $invoice->paid_status = Invoice::STATUS_PARTIALLY_PAID;
            }

            // Save the Invoice
            $invoice->save();
        }

        // Update the Payment
        $payment->update([
            'payment_date' => $request->payment_date,
            'payment_number' => $request->payment_number,
            'customer_id' => $request->customer_id,
            'credit_note_id' => $request->credit_note_id,
            'invoice_id' => $request->invoice_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'private_notes' => $request->private_notes,
        ]);

        // Update custom field values
        $payment->updateCustomFields($request->custom_fields);

        return $payment;
    }

    /**
     * Delete a Payment.
     *
     * @param int $payment_id
     *
     * @return bool
     */
    public function deletePayment(Request $request, $payment_id)
    {
        $payment = $this->getPaymentById($request, $payment_id);

        // Delete
        return $payment->deleteModel();
    }
}

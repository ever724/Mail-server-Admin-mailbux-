<?php

namespace App\Repositories;

use App\Interfaces\PaymentTypeInterface;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentTypeRepository implements PaymentTypeInterface
{
    /**
     * Return paginated and filtered results of payment types by company.
     *
     * @return \App\Models\PaymentMethod
     */
    public function getPaginatedFilteredPaymentTypes(Request $request)
    {
        return PaymentMethod::findByCompany($request->currentCompany->id)->latest()->paginate()->appends(request()->query());
    }

    /**
     * Return a single resource by id.
     *
     * @param mixed $payment_type_id
     *
     * @return \App\Models\PaymentMethod
     */
    public function getPaymentTypeById(Request $request, $payment_type_id)
    {
        return PaymentMethod::findByCompany($request->currentCompany->id)->findOrFail($payment_type_id);
    }

    /**
     * Create an instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\PaymentMethod
     */
    public function newPaymentType(Request $request)
    {
        $payment_type = new PaymentMethod();

        // Fill model with old input
        if (!empty($request->old())) {
            $payment_type->fill($request->old());
        }

        return $payment_type;
    }

    /**
     * Store Payment Method on database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company      $company
     *
     * @return \App\Models\PaymentMethod
     */
    public function createPaymentType(Request $request)
    {
        // Create Payment Type and Store in Database
        return PaymentMethod::create([
            'name' => $request->name,
            'company_id' => $request->currentCompany->id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $payment_type_id
     *
     * @return \App\Models\PaymentMethod
     */
    public function updatePaymentType(Request $request, $payment_type_id)
    {
        $payment_type = $this->getPaymentTypeById($request, $payment_type_id);
        $payment_type->update([
            'name' => $request->name,
        ]);

        return $payment_type;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $payment_type_id
     *
     * @return bool
     */
    public function deletePaymentType(Request $request, $payment_type_id)
    {
        $payment_type = $this->getPaymentTypeById($request, $payment_type_id);

        // Update existing payments with this payment method
        Payment::where('payment_method_id', $payment_type->id)->update(['payment_method_id' => null]);

        // Delete Payment Type from Database
        return $payment_type->delete();
    }
}

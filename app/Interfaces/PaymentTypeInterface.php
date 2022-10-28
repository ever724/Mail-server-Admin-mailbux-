<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentTypeInterface
{
    public function getPaginatedFilteredPaymentTypes(Request $request);

    public function newPaymentType(Request $request);

    public function createPaymentType(Request $request);

    public function getPaymentTypeById(Request $request, $custom_field_id);

    public function updatePaymentType(Request $request, $custom_field_id);

    public function deletePaymentType(Request $request, $custom_field_id);
}

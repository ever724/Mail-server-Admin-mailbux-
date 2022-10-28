<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentInterface
{
    public function getPaginatedFilteredPayments(Request $request);

    public function newPayment(Request $request);

    public function createPayment(Request $request);

    public function getPaymentById(Request $request, $payment_id);

    public function updatePayment(Request $request, $payment_id);

    public function deletePayment(Request $request, $payment_id);
}

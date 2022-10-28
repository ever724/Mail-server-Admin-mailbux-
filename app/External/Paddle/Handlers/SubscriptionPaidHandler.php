<?php

namespace App\External\Paddle\Handlers;

use App\Models\Client;
use App\Models\SubscriptionInvoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

final class SubscriptionPaidHandler implements PaddleWebhookHandlerInterface
{
    /**
     * @param array $payload
     *
     * @return JsonResponse
     */
    public function handle(array $payload): JsonResponse
    {
        $country = $payload['country'];
        $status = $payload['status'];
        $planId = $payload['subscription_plan_id'];
        $price = $payload['balance_gross'];
        $currency = $payload['balance_currency'];
        $orderNumber = $payload['order_id'];
        $paymentDate = $payload['event_time'];
        $nextPaymentDate = $payload['next_bill_date'];
        $nextPaymentAmount = $payload['next_payment_amount'];
        $paymentMethod = $payload['payment_method'];
        $subscriptionId = $payload['subscription_id'];
        $isFirstPayment = filter_var($payload['initial_payment'], FILTER_VALIDATE_BOOLEAN);
        $checkoutId = $payload['checkout_id'];

        $customer = Client::query()->firstWhere('email', $payload['email']);

        $paidAt = Carbon::createFromFormat('Y-m-d H:i:s', $paymentDate);

        $invoiceData = [
            'order_number' => $orderNumber,
            'client_id' => $customer->id,
            'amount' => $price,
            'currency' => $currency,
            'country' => $country,
            'paid_at' => $paymentDate,
            'next_payment_date' => $nextPaymentDate,
            'next_payment_amount' => $nextPaymentAmount,
            'is_first_payment' => $isFirstPayment,
            'payment_method' => $paymentMethod,
            'status' => $status,
            'paddle_subscription_id' => $subscriptionId,
            'paddle_plan_id' => $planId,
            'paddle_checkout_id' => $checkoutId,
            'response_data' => $payload,
        ];

        $invoiceInfo = SubscriptionInvoice::query()
            ->where('client_id', $customer->id)
            ->whereBetween('paid_at', [$paidAt->firstOfMonth(), $paidAt->lastOfMonth()])
            ->where('payment_status', '!=', SubscriptionInvoice::PAYMENT_STATUS_SUCCESS)
            ->first();

        if ($invoiceInfo) {
            $invoiceInfo->update($invoiceData);
        } else {
            $invoiceInfo = SubscriptionInvoice::query()->create($invoiceData);
        }

        return response()->json([
            'success' => ($invoiceInfo instanceof SubscriptionInvoice),
            'data' => $invoiceInfo ? $invoiceInfo->toArray() : [],
        ]);
    }
}

<?php

namespace App\External\Paddle\Handlers;

use App\Models\Client;
use App\Models\SubscriptionInvoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

final class SubscriptionPaymentFailedHandler implements PaddleWebhookHandlerInterface
{
    /**
     * @param array $payload
     *
     * @return JsonResponse
     */
    public function handle(array $payload): JsonResponse
    {
        $checkoutId = $payload['checkout_id'];
        $subscriptionId = $payload['subscription_id'];
        $subscriptionPlanId = $payload['subscription_plan_id'];
        $nextRetry = Carbon::createFromFormat('Y-m-d', $payload['next_retry_date']);
        $orderId = $payload['order_id'];
        $amount = $payload['amount'];
        $status = $payload['status'];

        $customer = Client::query()->firstWhere('email', $payload['email']);

        $previousInvoice = SubscriptionInvoice::query()
            ->where('payment_status', SubscriptionInvoice::PAYMENT_STATUS_SUCCESS)
            ->where('paddle_subscription_id', $subscriptionId)
            ->latest()
            ->first();

        /** @var SubscriptionInvoice $previousInvoice */
        $previousInvoice = optional($previousInvoice);

        $invoice = SubscriptionInvoice::query()
            ->create([
                'order_number' => $orderId,
                'client_id' => $customer->id,
                'amount' => $amount,
                'currency' => $previousInvoice->currency ?? 'USD',
                'paid_at' => now(),
                'payment_status' => SubscriptionInvoice::PAYMENT_STATUS_UNPAID,
                'next_payment_date' => $nextRetry,
                'next_payment_amount' => $amount,
                'is_first_payment' => false,
                'payment_method' => $previousInvoice->payment_method ?? 'card',
                'status' => $status,
                'paddle_subscription_id' => $subscriptionId,
                'paddle_plan_id' => $subscriptionPlanId,
                'paddle_checkout_id' => $checkoutId,
                'country' => $previousInvoice->country ?? 'US',
                'response_data' => $payload,
            ]);

        if ($invoice instanceof SubscriptionInvoice) {
            return response()->json([
                'success' => true,
                'data' => $invoice->toArray(),
            ]);
        }

        return response()->json([
            'success' => false,
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}

<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanSubscription;
use Illuminate\Support\Arr;
use Laravel\Paddle\Exceptions\PaddleException;

class SubscriptionService
{
    /**
     * @var PaddleService
     */
    private $paddleService;

    public function __construct(PaddleService $paddleService)
    {
        $this->paddleService = $paddleService;
    }

    /**
     * @param string $checkoutId
     *
     * @throws \Exception
     *
     * @return null|Order
     */
    public function storePaddleOrderDetails(string $checkoutId): ?Order
    {
        try {
            $paddlePlan = $this->paddleService->getOrderDetails($checkoutId);
        } catch (PaddleException $exception) {
            return null;
        }

        if (empty($paddlePlan)) {
            return null;
        }

        $client = Client::query()->firstWhere('email',
            Arr::get($paddlePlan, 'order.customer.email')
        );

        $productId = Arr::get($paddlePlan, 'order.product_id');

        $plan = Plan::query()
            ->where('paddle_annual_id', $productId)
            ->orWhere('paddle_monthly_id', $productId)
            ->first();

        if ($productId == $plan->paddle_annual_id) {
            $period = 'year';
        } elseif ($productId == $plan->paddle_monthly_id) {
            $period = 'month';
        } else {
            throw new \Exception("Invalid productId {$productId}");
        }

        /** @var Order */
        return Order::query()
            ->updateOrCreate(
                [
                    'order_id' => Arr::get($paddlePlan, 'order.order_id'),
                    'subscription_id' => Arr::get($paddlePlan, 'order.subscription_id'),
                    'subscription_order_id' => Arr::get($paddlePlan, 'order.subscription_order_id'),
                    'client_id' => $client->getKey(),
                    'plan_id' => $plan->id,
                    'transaction_id' => Arr::get($paddlePlan, 'checkout.checkout_id'),
                ],
                [
                    'order_id' => Arr::get($paddlePlan, 'order.order_id'),
                    'subscription_id' => Arr::get($paddlePlan, 'order.subscription_id'),
                    'subscription_order_id' => Arr::get($paddlePlan, 'order.subscription_order_id'),
                    'client_id' => $client->getKey(),
                    'plan_id' => $plan->id,
                    'price' => Arr::get($paddlePlan, 'order.total'),
                    'formatted_tax' => Arr::get($paddlePlan, 'order.formatted_tax'),
                    'formatted_total' => Arr::get($paddlePlan, 'order.formatted_total'),
                    'total_tax' => Arr::get($paddlePlan, 'order.total_tax'),
                    'currency' => Arr::get($paddlePlan, 'order.currency'),
                    'transaction_id' => Arr::get($paddlePlan, 'checkout.checkout_id'),
                    'payment_type' => 'PADDLE',
                    'payment_status' => Arr::get($paddlePlan, 'state'),
                    'complete_timezone' => Arr::get($paddlePlan, 'order.completed.timezone'),
                    'completed_date' => Arr::get($paddlePlan, 'order.completed.date'),
                    'billing_interval' => $period,
                ]
            );
    }

    /**
     * @param Order $order
     *
     * @return PlanSubscription
     */
    public function newSubscription(Order $order): PlanSubscription
    {
        $plan = $order->plan;
        $client = $order->client;

        $trial = new Period($plan->trial_interval, $plan->trial_period, now());
        $period = new Period($order->billing_interval, $plan->invoice_period, $trial->getEndDate());

        /** @var PlanSubscription $subscription */
        $subscription = PlanSubscription::query()
            ->updateOrCreate(
                [
                    'client_id' => $client->id,
                    'plan_id' => $plan->getKey(),
                    'subscription_id' => $order->subscription_id,
                ],
                [
                    'client_id' => $client->id,
                    'name' => $plan->name,
                    'slug' => $plan->name,
                    'plan_id' => $plan->getKey(),
                    'subscription_id' => $order->subscription_id,
                    'trial_ends_at' => $trial->getEndDate(),
                    'starts_at' => $period->getStartDate(),
                    'ends_at' => $period->getEndDate(),
                ]
            );

        foreach ($plan->features as $feature) {
            $subscription->recordFeatureUsage($feature->slug, 0);
        }

        return $subscription;
    }
}

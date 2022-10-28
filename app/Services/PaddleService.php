<?php

namespace App\Services;

use App\External\Paddle\References;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Laravel\Paddle\Cashier;
use Laravel\Paddle\Exceptions\PaddleException;

class PaddleService
{
    /**
     * @return array
     */
    public function getPlans(): array
    {
        try {
            $plans = Cashier::post(References::URI_SUBSCRIPTION_PLANS, $this->getPaddleOptions());

            return $plans['response'] ?? [];
        } catch (PaddleException | ConnectionException $exception) {
            return [];
        }
    }

    /**
     * @param $subscription_id
     *
     * @return array
     */
    public function getSubscriptionDetail($subscription_id): array
    {
        try {
            $subscriptionDetails = Cashier::post(
                References::URI_SUBSCRIPTION_USERS,
                $this->getPaddleOptions(['subscription_id' => $subscription_id])
            );

            return Arr::get($subscriptionDetails, 'response.0', []);
        } catch (PaddleException $exception) {
            return [];
        }
    }

    /**
     * @param string $checkout_id
     *
     * @return array|mixed
     */
    public function getOrderDetails(string $checkout_id): array
    {
        $response = Http::get(
            sprintf(
                '%s/api/1.0/order?%s',
                Cashier::checkoutUrl(),
                http_build_query([
                    'checkout_id' => $checkout_id,
                ])
            )
        );

        return $response->json();
    }

    private function getPaddleOptions(array $options = []): array
    {
        $defaultOptions = Cashier::paddleOptions();

        return array_merge($defaultOptions, $options);
    }
}

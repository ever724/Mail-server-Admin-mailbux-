<?php

namespace App\External\Paddle\Handlers;

use App\Jobs\StorePaddlePlanSubscription;
use App\Models\DomainAdmin;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class SubscriptionCreatedHandler implements PaddleWebhookHandlerInterface
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array $payload
     *
     * @return array|\Illuminate\Http\JsonResponse|mixed
     */
    public function handle(array $payload)
    {
        $checkoutId = Arr::get($payload, 'checkout_id');

        $subscription = $this->dispatcher->dispatchNow(
            new StorePaddlePlanSubscription($checkoutId)
        );

        try {
            $client = $subscription->client;
            $plan = $subscription->plan;

            $username = explode('@', $client->email)[0] ?? $client->email;

            $domainAdmin = DomainAdmin::query()
                ->create([
                    'subscription_id' => $subscription->getKey(),
                    'username' => $username . $subscription->subscription_id,
                    'password' => Str::random(8) . '1aA!',
                    'api_access' => true,
                    'enabled' => true,
                    'recovery_email' => $client->recovery_mail,
                    'language' => $client->language ?? 'en',
                    'domains' => [],
                    'storagequota_total' => (int) Arr::get($plan->mailbux_settings, 'storagequota_total'),
                    'quota_domains' => (int) Arr::get($plan->mailbux_settings, 'quota_domains'),
                    'quota_mailboxes' => (int) Arr::get($plan->mailbux_settings, 'quota_mailboxes'),
                    'quota_aliases' => (int) Arr::get($plan->mailbux_settings, 'quota_aliases'),
                    'quota_domainaliases' => (int) Arr::get($plan->mailbux_settings, 'quota_domainaliases'),
                ]);
        } catch (\Exception $e) {
            return error_response([$e->getMessage()]);
        }

        return response()->json([
            'success' => true,
            'data' => $domainAdmin->toArray(),
        ]);
    }
}

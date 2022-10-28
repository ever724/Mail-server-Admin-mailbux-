<?php

namespace App\Console\Commands;

use App\Models\PlanSubscription;
use App\Services\PaddleService;
use Illuminate\Console\Command;

class UpdateSubscriptionsMetaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbux:subscriptions:metadata:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Metadata of subscriptions that don\'t have.';

    /**
     * Execute the console command.
     */
    public function handle(PaddleService $paddleService)
    {
        PlanSubscription::query()
            ->whereNull('meta_data')
            ->each(function (PlanSubscription $planSubscription) use ($paddleService) {
                try {
                    $subscriptionDetails = $paddleService->getSubscriptionDetail($planSubscription->subscription_id);

                    if (!empty($subscriptionDetails)) {
                        $planSubscription->meta_data = $subscriptionDetails;
                        $planSubscription->save();
                    }
                } catch (\Throwable $exception) {
                    return;
                }
            });
    }
}

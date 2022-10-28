<?php

namespace App\Jobs;

use App\Models\PlanSubscription;
use App\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StorePaddlePlanSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $checkoutId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $checkoutId
    ) {
        $this->checkoutId = $checkoutId;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle(SubscriptionService $subscriptionService): ?PlanSubscription
    {
        $order = $subscriptionService->storePaddleOrderDetails($this->checkoutId);

        return $subscriptionService->newSubscription($order);
    }
}

<?php

namespace App\Events;

use App\Models\PlanSubscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentMethodUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PlanSubscription
     */
    private $subscription;

    /**
     * Create a new event instance.
     */
    public function __construct(PlanSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function getSubscription(): PlanSubscription
    {
        return $this->subscription;
    }
}

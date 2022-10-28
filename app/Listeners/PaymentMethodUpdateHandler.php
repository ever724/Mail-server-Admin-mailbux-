<?php

namespace App\Listeners;

use App\Events\PaymentMethodUpdated;

class PaymentMethodUpdateHandler
{
    public function handle(PaymentMethodUpdated $event)
    {
        $subscription = $event->getSubscription();
    }
}

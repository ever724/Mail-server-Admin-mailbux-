<?php

namespace App\Http\Controllers\API\V1;

use App\Models\PlanSubscription;
use Illuminate\Http\Request;

class PlanSubscriptionController
{
    public function index(Request $request)
    {
        $subscriptions = $request->client->plan_subscriptions;
    }

    public function show(Request $request, PlanSubscription $plan_subscription)
    {
    }
}

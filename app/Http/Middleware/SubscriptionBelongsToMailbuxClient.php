<?php

namespace App\Http\Middleware;

use App\Models\PlanSubscription;
use App\Models\SubscriptionInvoice;
use Closure;
use Illuminate\Http\JsonResponse;

class SubscriptionBelongsToMailbuxClient
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->subscription instanceof PlanSubscription) {
            $subscription = $request->subscription;
        } elseif ($request->invoice instanceof SubscriptionInvoice) {
            $subscription = $request->invoice->subscription;
        } else {
            return $next($request);
        }

        $client_id = $request->client->id ?? $request->client_id;

        if ((int) $subscription->client_id == (int) $client_id) {
            return $next($request);
        }

        abort(JsonResponse::HTTP_UNAUTHORIZED);
    }
}

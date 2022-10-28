<?php

namespace App\Http\Middleware;

use App\Models\Client;
use App\Models\PlanSubscription;
use App\Models\SupportTicket;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class VerifyIfLoggedInClientUserHasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var Client $client */
        $client = $request->client;

        if (!$client instanceof Client) {
            return error_response(['Missing Client']); // probably not thrown because it's handled by other middleware
        }

        $supportTicket = optional($request->route('support_ticket'));
        $this->verify($supportTicket instanceof SupportTicket, $supportTicket->creator_email == $client->email);

        $planSubscription = optional($request->route('plan_subscription'));
        $this->verify($planSubscription instanceof PlanSubscription, $planSubscription->client_id == $client->id);

        return $next($request);
    }

    private function verify(bool $verify, bool $access)
    {
        if ($verify && !$access) {
            throw new HttpResponseException(error_response(['No Access'], Response::HTTP_UNAUTHORIZED));
        }
    }
}

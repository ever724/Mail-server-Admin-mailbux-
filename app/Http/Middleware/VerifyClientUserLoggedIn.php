<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyClientUserLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $clientId = (int) $request->header('X-User-Id');

        $client = Client::query()->find($clientId);

        if (!$client instanceof Client) {
            return error_response(['Unauthenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $request->merge(['client' => $client]);

        return $next($request);
    }
}

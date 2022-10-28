<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class VerifyRequestFromClient
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
        $key = $request->header(get_system_setting('client_api_key_header'));

        if ($key !== get_system_setting('client_api_key')) {
            return new JsonResponse([
                'success' => false,
                'errors' => [
                    'Unauthenticated',
                ],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}

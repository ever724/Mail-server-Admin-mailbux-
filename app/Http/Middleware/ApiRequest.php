<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiRequest
{
    /**
     * Handle an incoming api request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $request->merge([
            'currentCompany' => $currentCompany,
            'user' => $user,
        ]);

        return $next($request);
    }
}

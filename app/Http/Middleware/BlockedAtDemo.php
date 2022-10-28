<?php

namespace App\Http\Middleware;

use Closure;

class BlockedAtDemo
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
        if (config('app.is_demo')) {
            session()->flash('alert-danger', __('messages.action_blocked_in_demo'));

            return redirect()->back();
        }

        return $next($request);
    }
}

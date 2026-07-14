<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($request->bearerToken()) {
            try {
                // Attempt to authenticate if token is provided
                Auth::guard($guard)->check();
            } catch (\Exception $e) {
                // Silently fail - allow request to continue as guest
            }
        }

        return $next($request);
    }
}
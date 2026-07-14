<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateStudent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->guard('student')->check()) {
            return redirect()->route('student.showlogin')
                ->with('error', 'Please sign in to access the student panel.');
        }

        return $next($request);
    }
}

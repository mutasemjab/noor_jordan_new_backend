<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->guard('teacher')->check()) {
            return redirect()->route('teacher.showlogin')
                ->with('error', 'Please sign in to access the teacher panel.');
        }

        return $next($request);
    }
}

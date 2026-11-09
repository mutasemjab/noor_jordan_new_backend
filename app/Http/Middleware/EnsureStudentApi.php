<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;

class EnsureStudentApi
{
    public function handle(Request $request, Closure $next)
    {
        if (! ($request->user() instanceof Student)) {
            // Token belongs to a different user type — revoke it so the app forces re-login
            $request->user()?->currentAccessToken()?->delete();

            return response()->json([
                'status'        => false,
                'message'       => 'انتهت الجلسة، يرجى تسجيل الدخول مجدداً.',
                'force_logout'  => true,
            ], 401);
        }

        return $next($request);
    }
}

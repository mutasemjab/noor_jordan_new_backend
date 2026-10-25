<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;

class EnsureTeacherApi
{
    public function handle(Request $request, Closure $next)
    {
        if (! ($request->user() instanceof Teacher)) {
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

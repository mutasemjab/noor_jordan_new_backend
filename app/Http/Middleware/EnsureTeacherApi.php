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
            return response()->json(['status' => false, 'message' => 'غير مصرح.'], 403);
        }

        return $next($request);
    }
}

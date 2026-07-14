<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $request->header('Accept-Language', $request->query('lang', 'ar'));
        app()->setLocale(in_array($locale, ['ar', 'en']) ? $locale : 'ar');
        return $next($request);
    }
}

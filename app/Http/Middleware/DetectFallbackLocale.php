<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;

class DetectFallbackLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$locales)
    {
        $local = ($request->hasHeader('Accept-Language')) ? $request->header('Accept-Language') : 'en';
        Lang::setFallback($local);

        return $next($request);
    }
}

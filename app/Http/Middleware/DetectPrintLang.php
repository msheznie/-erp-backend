<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;

class DetectPrintLang
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
        $local = $request->get('lang');
        if(!empty($local)) {
            app()->setLocale($local);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class MaxMemoryLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        ini_set('memory_limit', config('app.report_max_memory_limit'));
        return $next($request);
    }
}

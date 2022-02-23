<?php

namespace App\Http\Middleware;

use Closure;

class MaxExecutionLimit
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
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        return $next($request);
    }
}

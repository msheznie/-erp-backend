<?php

namespace App\Http\Middleware;

use Closure;

class SecurityHeader
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
        $response = $next($request);

        // Add the HSTS header to the response
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        $response->headers->set('X-Frame-Options', 'DENY');

        $response->headers->set('pragma', 'no-cache');
        $response->headers->set('Cache-Control', 'no-store');

        return $response;
    }
}
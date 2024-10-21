<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ThirdPartyDomain;
use Illuminate\Http\Request;
use Illuminate\Http\Response as LaravelResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Support\Facades\Log;

class Cors
{
    /**
     * List of allowed domains for CORS.
     *
     * @var array
     */
    protected $allowedOrigins = [];

    protected $allowedOriginsPatterns = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (env('ENABLE_CORS', false)) {
            $origin = $request->headers->get('Origin');
            $originPattern = env('APP_BASE_URL', '/^https:\/\/[a-z0-9-]+\.gears-int\.com$/');

            array_push($this->allowedOriginsPatterns, $originPattern);

            $allowedDomains = ThirdPartyDomain::pluck('name')->toArray();
            $this->allowedOrigins = array_merge($this->allowedOrigins, $allowedDomains);

            if (in_array($origin, $this->allowedOrigins) || $this->isAllowedOriginPattern($origin)) {
                return $this->setCorsHeaders($response, $origin);
            }
        } else {
            return $this->setCorsHeaders($response, '*');
        }

        return $response;
    }

    /**
     * Set CORS headers on the response.
     *
     * @param  mixed  $response
     * @param  string $origin
     * @return mixed
     */
    protected function setCorsHeaders($response, $origin)
    {
        if ($response instanceof LaravelResponse || method_exists($response, 'header')) {
            return $response
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // For non-Laravel responses like Symfony responses or JsonResponse
        if ($response instanceof SymfonyResponse) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        return $response;
    }

    protected function isAllowedOriginPattern($origin)
    {
        if (!$origin) {
            return false;
        }

        foreach ($this->allowedOriginsPatterns as $pattern) {
            if (preg_match($pattern, $origin)) {
                return true;
            }
        }

        return false;
    }
}


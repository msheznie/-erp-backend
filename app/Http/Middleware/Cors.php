<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ThirdPartyDomain;
use Illuminate\Http\Request;

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

            $originPattern = env('APP_BASE_URL');

            array_push($this->allowedOriginsPatterns, $originPattern);

            $alllowedDomains = ThirdPartyDomain::pluck('name')->toArray();
            $this->allowedOrigins = array_merge($this->allowedOrigins, $alllowedDomains);

            if (in_array($origin, $this->allowedOrigins)) {
                return $this->setCorsHeaders($response, $origin);
            } elseif ($this->isAllowedOriginPattern($origin)) {
                return $this->setCorsHeaders($response, $origin);
            }
        } else {
            // return $response
            //     ->header('Access-Control-Allow-Origin', '*')
            //     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        return $response;
    }

    protected function setCorsHeaders($response, $origin)
    {
        return $response
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
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

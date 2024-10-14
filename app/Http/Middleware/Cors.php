<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ThirdPartyDomain;

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
    public function handle($request, Closure $next)
    {
        if (env('ENABLE_CORS', false)) {
            $origin = $request->headers->get('Origin');

            $originPattern = env('APP_BASE_URL');

            array_push($this->allowedOriginsPatterns, $originPattern);


            $alllowedDomains = ThirdPartyDomain::pluck('name')->toArray();

            array_merge($this->allowedOrigins, $alllowedDomains);

            if (in_array($origin, $this->allowedOrigins)) {
                return $next($request)
                    ->header('Access-Control-Allow-Origin', $origin)
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            } else if ($this->isAllowedOriginPattern($origin)) {
                return $next($request)
                    ->header('Access-Control-Allow-Origin', $origin)
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            }

            return $next($request);
        } else {
            return $next($request)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
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

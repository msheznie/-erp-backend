<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class EitherAuthAPIorKeyClock
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

        if (env('IS_MULTI_TENANCY', false)) {
            $url = $request->getHttpHost();
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];

            if ($subDomain == 'www') {
                $subDomain = $url_array[1];
            }

            if ($subDomain != 'localhost:8000') {
                if (!$subDomain) {
                    return $subDomain . "Not found";
                }

                $tenant = DB::connection('main_db')->table('tenant')->where('sub_domain', 'like', $subDomain)->first();
                if (empty($tenant)) {
                    return "Sub domain {$subDomain} not found";
                }

                $loginData = DB::connection('main_db')->table('tenant_login')->where('tenantID', $tenant->id)->first();
                
                if (($request->hasHeader('From-Portal') && $request->header('From-Portal') == 1) && $loginData && $loginData->loginType == 4) {
                     // Check if the user is authenticated with Keycloak
                    if (Auth::guard('keycloak')->check()) {
                        Auth::shouldUse('keycloak');
                        Config::set("auth.defaults.guard", 'keycloak');
                        return $next($request);
                    }
                } else {
                    // Check if the user is authenticated with API
                    if (Auth::guard('api')->check()) {
                        return $next($request);
                    }
                }
            }
        } else {
            if (Auth::guard('api')->check()) {
                return $next($request);
            }
        }

        return response()->json(
            [
                'errors' => [
                    'status' => 401,
                    'message' => 'Unauthenticated',
                ],
                'message' => 'Session expired! Please login again'
            ], 401
        );
    }
}

<?php

namespace App\Http\Middleware;
use App\Services\MobileAccessRouteService;
use App\Services\SRMService;
use Closure;
use Illuminate\Support\Facades\Log;

class MobileServer
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
        $routes = MobileAccessRouteService::getRoutes();
        if (env('MOBILE_SERVER', false)) {
            
            if(in_array($request->route()->uri, $routes['routes']))
            {
                return $next($request);
            }

            return $this->errorMsgs("Unauthorized Access",401);
        } 

        if (env('SRM_API_SERVER', false)) {
            
            if(in_array($request->route()->uri, SRMService::apiRoutes()))
            {
                return $next($request);
            }

            return $this->errorMsgs("Unauthorized Access",401);
        } 

        return $next($request);
    }

    public function errorMsgs($messsage,$code){
        return response()->json([
            'success' => false,
            'message' => $messsage
        ], $code);
    }
}

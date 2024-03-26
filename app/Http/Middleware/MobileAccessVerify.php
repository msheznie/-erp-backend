<?php

namespace App\Http\Middleware;
use App\Models\MobileAccess;
use App\Models\CompanyPolicyMaster;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MobileAccessRouteService;

class MobileAccessVerify
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
   
        if (!in_array($request->route()->uri, $routes['routes'])) {
            return $next($request);
        }
      
        $policy = CompanyPolicyMaster::where('companyPolicyCategoryID', 88)
                ->where('isYesNO',1)
                ->first();

        if(!$policy)
        {
            return $next($request);
        }
        if($policy && $policy->isYesNO == 0)
        {
            return $next($request);
        }
        
        if(!$request->hasHeader('is-mobile') ) {
            return $next($request);
        }
        if($request->hasHeader('is-mobile') && $request->header('is-mobile') != 1) {
            return $next($request);
         }

         if(!$request->hasHeader('device_id')) {
            return $this->errorMsgs("Access Denied: Mobile Device Restricted ,Device Id not found",401); 
        }
         $serial_number = $request->header('device_id');

        if(in_array($request->route()->uri, $routes['nonAuthRoutes']))
        {
            $mobileAccess = MobileAccess::where('serial_number',$serial_number)->where('is_active',1)->first();
        }
        else
        {   
            $user = Auth::user();
            $mobileAccess = MobileAccess::where('employee',$user->employee_id)->where('is_active',1)->where('serial_number',$serial_number)->first();
        }
       
        if(!$mobileAccess)
        {
            return $this->errorMsgs("Access Denied: Mobile Device Restricted You dont have access to mobile devices",401); 

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

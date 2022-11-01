<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\EmployeeNavigation;
use App\Models\RoleRoute;
use App\Models\NavigationRoute;

class UserAuthorization
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
        $employeeSystemID = \Helper::getEmployeeSystemID();

        $userGroups = EmployeeNavigation::where('employeeSystemID', $employeeSystemID)
                                        ->get();

        $routeName = $request->route()->getName();

        $checkRouteName = NavigationRoute::where('routeName', $routeName)->first();

        $routeCheck = false;
        foreach ($userGroups as $key => $value) {
            $checkRoleRoute = RoleRoute::where('userGroupID', $value->userGroupID)
                                        ->where('routeName', $routeName)
                                        ->first();


            if ($checkRoleRoute) {
                $routeCheck = true;
            }
        }

        if (env('ENABLE_AUTHORIZATION', false) == true) {
            if ($routeCheck) {
                return $next($request);
            } else {
                if (!$checkRouteName) {
                    return $next($request);
                } else {
                    return errorMsgs("Unauthorized Access");
                }
            }
        } else {
            return $next($request);
        }


    }
}


function errorMsgs($messsage){
    return response()->json([
        'success' => false,
        'message' => $messsage
    ], 403);
}

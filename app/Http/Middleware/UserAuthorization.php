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
        if (!env('ENABLE_AUTHORIZATION', false)) {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        $checkRouteName = NavigationRoute::where('routeName', $routeName)->first();

        if (!$checkRouteName) {
            return $next($request);
        }

        $employeeSystemID = \Helper::getEmployeeSystemID();

        $userGroups = EmployeeNavigation::where('employeeSystemID', $employeeSystemID)
                                        ->get();

        $userGroupIDs = (count($userGroups) > 0) ? collect($userGroups)->pluck('userGroupID')->toArray() : [];

        $checkRoleRoute = RoleRoute::whereIn('userGroupID', $userGroupIDs)
                                    ->where('routeName', $routeName)
                                    ->first();

        if ($checkRoleRoute) {
            return $next($request);
        } else {
            return errorMsgs("Unauthorized Access");
        }
    }
}


function errorMsgs($messsage){
    return response()->json([
        'success' => false,
        'message' => $messsage
    ], 403);
}

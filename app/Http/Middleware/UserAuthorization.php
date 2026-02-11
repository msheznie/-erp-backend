<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\EmployeeNavigation;
use App\Models\RoleRoute;
use App\Models\NavigationRoute;
use App\helper\Helper;

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

        $exceptionRoutes = $this->getExceptionRoutes();

        if (in_array($request->route()->uri, $exceptionRoutes)) {
            return $next($request);
        }

        if ($request->header('From-Portal') && $request->header('From-Portal') == 1 && in_array($request->route()->uri, $this->portalIgnoreRoutes())) {
            return $next($request);
        }

        $checkRouteName = NavigationRoute::where('routeName', $routeName)->first();

        // if (!$checkRouteName) {
        //     return $next($request);
        // }

        $employeeSystemID = Helper::getEmployeeSystemID();

        $userGroups = EmployeeNavigation::where('employeeSystemID', $employeeSystemID)
                                        ->get();

        $userGroupIDs = (count($userGroups) > 0) ? collect($userGroups)->pluck('userGroupID')->toArray() : [];

        $checkRoleRoute = RoleRoute::whereIn('userGroupID', $userGroupIDs)
                                    ->where('routeName', $routeName)
                                    ->first();

        if ($checkRoleRoute) {
            return $next($request);
        } else {

            $navigationID = $request->header('X-nav-ID') ?? 0;
            $accessType = $request->header('X-Access-Type') ?? 'None';

            if ($routeName != 'api.' && $navigationID > 0 && self::getActionType($accessType) > 0) {

                NavigationRoute::firstOrCreate(
                    [
                        'navigationID' => $navigationID,
                        'routeName' => $routeName,
                        'action' => self::getActionType($accessType),
                    ]
                );

                foreach ($userGroupIDs as $userGroupID) {
                    RoleRoute::create([
                        'routeName' => $routeName,
                        'userGroupID' => $userGroupID,
                        'companySystemID' => 0
                    ]);
                }
                

                $checkRoleRouteAfterCreate = RoleRoute::whereIn('userGroupID', $userGroupIDs)
                                    ->where('routeName', $routeName)
                                    ->first();

                if ($checkRoleRouteAfterCreate) {
                    return $next($request);
                } else {
                    return errorMsgs("Unauthorized Access");
                }
            }

            \Log::channel('authorization')->info(json_encode([
                'navigationID' => $navigationID,
                'routeName' => $routeName,
                'routeURI' => $request->route()->uri,
                'accessType' => $accessType
            ]));


            return errorMsgs("Unauthorized Access");
        }
    }

    private function getExceptionRoutes()
    {
        return [
            'api/v1/getCurrentUserInfo',
            'api/v1/checkUserGroupAccessRights',
            'api/v1/getUserMenu',
            'api/v1/user/companies',
            'api/v1/getNotifications',
            'api/v1/erp_language_master',
            'api/v1/user/menu',
            'api/v1/getDashboardDepartment',
            'api/v1/getDashboardWidget',
            'api/v1/getAllDocumentApproval',
            'api/v1/getCustomWidgetGraphData',
            'api/v1/getAllApprovalDocuments',
            'api/v1/getAllcompaniesByDepartment',
            'api/v1/getAllNotifications',
            'api/v1/logoutApiUser',
            'api/v1/updateNotification',
        ];
    }

    private function portalIgnoreRoutes()
    {
        return [
            'api/v1/updateRouteAccess',
            'api/v1/auditLogsExternal',
        ];
    }

    private function getActionType($accessType)
    {
        return match($accessType) {
            'None' => 0,
            'Read' => 1,
            'Create' => 2,
            'Edit' => 3,
        };
    }
}


function errorMsgs($messsage){
    return response()->json([
        'success' => false,
        'message' => $messsage
    ], 403);
}

<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantEnforce
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */


    public function handle($request, Closure $next)
    {

        $apiKeyRoutes = [
            'api/v1/srmRegistrationLink',
            'api/v1/srm/fetch',
            'api/v1/suppliers/registration/approvals/status',
            'api/v1/sendSupplierInvitation',
            'api/v1/reSendInvitaitonLink',
            'api/v1/getMaterielIssueFormData',
            'api/v1/item_issue_masters/{item_issue_master}',
            'api/v1/item_return_details/{item_return_detail}',
            'api/v1/checkManWareHouse',
        ];



        $dbRoutes = ['api/v1/purchase-request-add-all-items','api/v1/poItemsUpload','api/v1/createPrMaterialRequest','api/v1/uploadItemsDeliveryOrder','api/v1/uploadItems','api/v1/posMappingRequest'];

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
                $tenant = Tenant::where('sub_domain', 'like', $subDomain)->first();
                if (!empty($tenant)) {
                    if (in_array($request->route()->uri, $apiKeyRoutes)) {
                        $request->request->add(['api_key' => $tenant->api_key]);
                    }

                    if (in_array($request->route()->uri, $dbRoutes)) {
                        $request->request->add(['db' => $tenant->database]);
                    }
                    Config::set("database.connections.mysql.database", $tenant->database);
                    //DB::purge('mysql');
                    DB::reconnect('mysql');
                } else {
                    return "Sub domain " . $subDomain . " not found";
                }
            }
        } else {
            if (in_array($request->route()->uri, $apiKeyRoutes)) {
                $request->request->add(['api_key' => "fow0lrRWCKxVIB4fW3lR"]);
            }

            if (in_array($request->route()->uri, $dbRoutes)) {
                $request->request->add(['db' => ""]);
            }
        }

        return $next($request);
    }
}

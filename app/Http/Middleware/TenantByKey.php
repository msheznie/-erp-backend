<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantByKey
{
    /**
     * switching database by identifying tenant by key.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $dbRoutes = [
            'api/v1/postEmployee',
            'api/v1/pull_company_details',
            'api/v1/postLocation',
            'api/v1/postDesignation',
            'api/v1/postDepartment',
            'api/v1/create_supplier_invoices',
//            'api/v1/post_supplier_invoice'
        ];

        if (env('IS_MULTI_TENANCY', false)) {

            if($request->hasHeader('api-key')) {
                $api_key = $request->header('api-key');
            }
            else if(!empty($request->input('api_key'))){
                $api_key = $request->input('api_key');
            } else {
                echo "Unauthorized Access";
            }

            $api_key = isset($api_key) ? $api_key : null;

            // get tenant details by api key in request
            $tenant = Tenant::whereApiKey($api_key)->first();
            if (!empty($tenant)) {

                if (in_array($request->route()->uri, $dbRoutes)) {
                    $request->request->add(['db' => $tenant->database]);
                }

            }

            if(empty($tenant)) return "Tenant not exists with provided API key ";

            // switching database
            Config::set("database.connections.mysql.database", $tenant->database);
            DB::reconnect('mysql');
        }
        return $next($request);
    }
}

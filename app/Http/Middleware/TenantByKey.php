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
        if (env('IS_MULTI_TENANCY', false)) {
            $api_key = $request->input('api_key');

            // get tenant details by api key in request
            $tenant = Tenant::whereApiKey($api_key)->first();

            if(empty($tenant)) return "Tenant not exists with provided API key ";

            // switching database
            Config::set("database.connections.mysql.database", $tenant->database);
            DB::reconnect('mysql');
        }
        return $next($request);
    }
}

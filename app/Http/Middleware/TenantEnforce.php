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
                    $request->request->add(['api_key' => $tenant->api_key]);
                    Config::set("database.connections.mysql.database", $tenant->database);
                    //DB::purge('mysql');
                    DB::reconnect('mysql');
                } else {
                    return "Sub domain " . $subDomain . " not found";
                }
            }
        }else{
            $request->request->add(['api_key' => "fow0lrRWCKxVIB4fW3lR"]);
        }
        return $next($request);
    }
}

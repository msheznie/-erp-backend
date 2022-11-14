<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Tenant;
use App\Models\TenantConfiguration;
use Illuminate\Http\Request;

class ConfigurationAPIController extends AppBaseController
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function getConfigurationInfo(Request $request){

        $isLang = 0;
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

            $isLang = TenantConfiguration::orderBy('id', 'desc')->where('tenant_id', $tenant->id)->where('configuration_id', 3)->first();

            }
        }

        $environment = TenantConfiguration::orderBy('id', 'desc')->where('configuration_id', 1)->first();

        $configuration = array('environment' => $environment->value, 'isLang' => $isLang->value);

        return $this->sendResponse($configuration, 'Configurations retrieved successfully');

    }
}

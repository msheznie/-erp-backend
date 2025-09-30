<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Tenant;
use App\Models\TenantConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

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
        $environment = 'Local';
        $version = $this->getVersion();
        $serverTime = time();
        $customRoute = env('MASK_ROUTE_PARAMS', false);
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
                if($tenant){
                    $isLang = TenantConfiguration::orderBy('id', 'desc')->where('tenant_id', $tenant->id)->where('application_id', 0)->where('configuration_id', 3)->first();
                    if($isLang){
                        $isLang = $isLang->value;
                    }
                }

                $environment = TenantConfiguration::orderBy('id', 'desc')->where('configuration_id', 1)->where('application_id', 0)->first();
                if($environment){
                    $environment = $environment->value;
                }
            }
        }

        $configuration = array('environment' => $environment, 'isLang' => $isLang, 'version' => $version, 'serverTime' => $serverTime, 'customRoute' => $customRoute);

        return $this->sendResponse($configuration, trans('custom.configurations_retrieved_successfully'));

    }

    public function getVersion()
    {
        $packageJsonPath = base_path('package.json');

        if (File::exists($packageJsonPath)) {
            $packageJsonContent = File::get($packageJsonPath);

            $packageJsonData = json_decode($packageJsonContent, true);

            $versionNumber = $packageJsonData['version'];

            return $versionNumber;
        } else {
            return null;
        }
    }
}

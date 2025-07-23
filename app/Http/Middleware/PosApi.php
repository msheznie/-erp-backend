<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use App\Models\ThirdPartySystems;
use App\Models\ThirdPartyIntegrationKeys;
use Illuminate\Support\Facades\DB;

class PosApi
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
        $header = $request->header('Authorization');
        $params = explode(' ',$header);
        if(count($params) == 2)
        {
            $key = $params[0];
            $value = $params[1];
            $is_key_valid = ThirdPartySystems::where('description','=',$key)->first();
            if(!empty($is_key_valid))
            {
                
                $system_id = $is_key_valid->id;
                $third_party_key = ThirdPartyIntegrationKeys::where('third_party_system_id','=',$system_id)->where('api_key','=',$value)->where('status','=','Active')->first();
                if(!empty($third_party_key))
                {
                    // Check if the integration key has access to the current endpoint
                    $currentPath = $request->path();
                    $hasEndpointAccess = DB::table('third_party_integration_key_apis as key_api')
                        ->join('third_party_apis as api', 'key_api.api_slug', '=', 'api.slug')
                        ->where('key_api.integration_key_id', $third_party_key->id)
                        ->where('key_api.is_active', 1)
                        ->where('api.path', $currentPath)
                        ->where('api.status', 'active')
                        ->exists();
                    
                    if (!$hasEndpointAccess) {
                        return errorMsgs("Invalid API key", 401);
                    }
                    
                    $company = Company::where('companySystemID',$third_party_key->company_id)->where('isActive', 1)->exists();
                    if ($company)
                    {
                        $request->request->add(['company_id' => $third_party_key->company_id]);
                        $request->request->add(['api_external_key' => $third_party_key->api_external_key]);
                        $request->request->add(['api_external_url' => $third_party_key->api_external_url]);
                        $request->request->add(['third_party_system_id' => $third_party_key->third_party_system_id]);
                    }
                    else
                    {
                        return errorMsgs("Company is not active",401);
                    }
                }
                else
                {
                    return errorMsgs("Invalid API key",401);
                }
               
            }
            else
            {
                return errorMsgs("Invalid API key",401);
            }
           
        }
        else
        {
            return errorMsgs("Invalid API key",401);
        }
       
        return $next($request);
    }
}

function errorMsgs($messsage,$code){
    return response()->json([
        'success' => false,
        'message' => $messsage
    ], $code);
}

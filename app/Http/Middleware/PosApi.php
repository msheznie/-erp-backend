<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ThirdPartySystems;
use App\Models\ThirdPartyIntegrationKeys;

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
            if(isset($is_key_valid))
            {
                
                $system_id = $is_key_valid->id;
                $third_party_key = ThirdPartyIntegrationKeys::where('third_party_system_id','=',$system_id)->where('api_key','=',$value)->first();
                if(isset($third_party_key))
                {
                    $request->request->add(['company_id' => $third_party_key->company_id]);
                }
                else
                {
                    return errorMsgs("third party key not valid",401);
                }
               
            }
            else
            {
                return errorMsgs("third party system not found",401);
            }
           
        }
        else
        {
            return errorMsgs("Invlaid Token",401);
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
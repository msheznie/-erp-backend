<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\UserToken;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AccessToken
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
            $token = $request->input('token');


            if(!empty($token)) {

                $userToken = UserToken::where('token',$token)->get();
                $employee_id = isset($userToken[0]->employee_id) ? $userToken[0]->employee_id : null;
                $dateTimeNow = date("Y-m-d H:i:s");

                if($employee_id) {
                    $request->request->add(['employee_id' => $userToken[0]->employee_id]);
                }
                else
                {
                    return errorMsgs("Unauthorized Access");
                }

                if($dateTimeNow > $userToken[0]->expire_time){
                    return errorMsgs("Token Expired");
                }
            }
            else
            {
                return errorMsgs("Unauthorized Access");
            }
        $userToken[0]->delete();
        return $next($request);
    }
}


function errorMsgs($messsage){
    return response()->json([
        'success' => false,
        'message' => $messsage
    ], 401);
}

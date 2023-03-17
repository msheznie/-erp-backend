<?php

namespace App\Http\Middleware;

use App\Models\UserToken;
use Closure;
use Illuminate\Http\Request;

class DetectHRMSEmployee
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
        if($request->hasHeader('user-token')) {
            $token = $request->header('user-token');
            $userToken = UserToken::where('token', $token)->first();
            if ($userToken) {
                if (date('Y-m-d H:i:s') <= $userToken->expire_time) {
                    if ($userToken->module_id == 2) {
                        $request->request->add(['employee_id' => $userToken->employee_id]);
                        $request->request->add(['user_token' => $token]);
                    } else {
                        $request->request->add(['employee_id' => 11]);
                    }
                } else {
                    return errorMsgs("Unauthorized Access", 401);
                }
            }else{
                return errorMsgs("Unauthorized Access", 401);
            }
        }else {
            return errorMsgs("Unauthorized Access", 401);
        }
        return $next($request);
    }
}



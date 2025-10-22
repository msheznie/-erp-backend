<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Response;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Utils\ResponseUtil;
use Psr\Http\Message\ServerRequestInterface;
use \Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;
use League\OAuth2\Server\Exception\OAuthServerException;
use Zend\Diactoros\Response as Psr7Response;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthAPIController extends PassportAccessTokenController
{
    use AuthenticatesUsers;

    public function auth(ServerRequestInterface $request, Request $request2)
    {
        $user = User::where('email',$request2->username)->first();
        if($user){
            $employees = Employee::find($user->employee_id);

            if(empty($employees)){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->isLock >= 4){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->empLoginActive != 1){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->empActive != 1){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }
        }
        try {
            $response = $this->server->respondToAccessTokenRequest($request, new Psr7Response);
            if($response){
                $user = User::where('email',$request2->username)->first();
                if($user){
                    Employee::find($user->employee_id)->update(['isLock' => 0]);
                }
            }
            return $response;
        } catch (OAuthServerException $exception) {
            $user = User::where('email',$request2->username)->first();
            if($user){
                $employees = Employee::find($user->employee_id);

                if(empty($employees)){
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if($employees->discharegedYN){
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if(!$employees->ActivationFlag){
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if($employees->empLoginActive != 1){
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if($employees->empActive != 1){
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                 Employee::find($user->employee_id)->increment('isLock');
            }
            return $this->withErrorHandling(function () use($exception,$user) {
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            });
        }
    }


    public function authWithToken(ServerRequestInterface $request, Request $request2)
    {

        $input = $request2->all();
        $validator = Validator::make($input, [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return Response::json(ResponseUtil::makeError($validator->messages(), array('type' => '')), 422);
        }
        $user = User::where(['login_token' => $input['token']])->first();
        if (empty($user)) {
            return Response::json(ResponseUtil::makeError(trans('custom.token_expired'), array('type' => '')), 500);
        }

        if($user){
            $employees = Employee::find($user->employee_id);

            if(empty($employees)){
                return Response::json(ResponseUtil::makeError(trans('custom.not_found', ['attribute' => trans('custom.user')]),array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_the_user_is_discharged_please_contact_admin'),array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_the_user_is_not_activated_please_contact_admin'),array('type' => '')), 401);
            }

            if($employees->isLock == 4){
                return Response::json(ResponseUtil::makeError(trans('custom.your_account_is_blocked'),array('type' => '')), 401);
            }
        }
        try {
            $user->login_token = null;
            $user->save();

            return $user->createToken('personal');
        } catch (OAuthServerException $exception) {
            return $this->withErrorHandling(function () use($exception) {
                return response(["message" => trans('custom.error')], 401);
            });
        }
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $attempts = 3;
        $lockoutMinites = 10;

        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $attempts,
            $lockoutMinites
        );
    }
}

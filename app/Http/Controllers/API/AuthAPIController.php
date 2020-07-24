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
                return Response::json(ResponseUtil::makeError('User not found',array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                return Response::json(ResponseUtil::makeError('Login failed! The user is discharged. Please contact admin.',array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                return Response::json(ResponseUtil::makeError('Login failed! The user is not activated. Please contact admin.',array('type' => '')), 401);
            }

            if($employees->isLock == 4){
                return Response::json(ResponseUtil::makeError('Your account is blocked',array('type' => '')), 401);
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
                    return Response::json(ResponseUtil::makeError('User not found',array('type' => '')), 401);
                }

                if($employees->discharegedYN){
                    return Response::json(ResponseUtil::makeError('Login failed! The user is discharged. Please contact admin.',array('type' => '')), 401);
                }

                if(!$employees->ActivationFlag){
                    return Response::json(ResponseUtil::makeError('Login failed! The user is not activated. Please contact admin.',array('type' => '')), 401);
                }

                 Employee::find($user->employee_id)->increment('isLock');
            }
            return $this->withErrorHandling(function () use($exception,$user) {

                if($user) {
                    $employees = Employee::find($user->employee_id);
                    $totalAttempt = 4 - $employees->isLock;
                    if ($totalAttempt == 0) {
                        return Response::json(ResponseUtil::makeError('Your account is blocked', array('type' => '')), 401);
                    } else {
                        return response(["message" => 'Invalid username or password. You have ' . $totalAttempt . ' more attempt'], 401);
                    }
                }else{
                    return response(["message" => 'Invalid username or password.'], 401);
                }
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
            return Response::json(ResponseUtil::makeError('Token expired', array('type' => '')), 500);
        }

        if($user){
            $employees = Employee::find($user->employee_id);

            if(empty($employees)){
                return Response::json(ResponseUtil::makeError('User not found',array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                return Response::json(ResponseUtil::makeError('Login failed! The user is discharged. Please contact admin.',array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                return Response::json(ResponseUtil::makeError('Login failed! The user is not activated. Please contact admin.',array('type' => '')), 401);
            }

            if($employees->isLock == 4){
                return Response::json(ResponseUtil::makeError('Your account is blocked',array('type' => '')), 401);
            }
        }
        try {
            $user->login_token = null;
            $user->save();
            return  $user->createToken('personal');
        } catch (OAuthServerException $exception) {
            return $this->withErrorHandling(function () use($exception) {
                return response(["message" => 'Error'], 401);
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

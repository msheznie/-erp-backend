<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            if($employees->isLock == 4){
                return Response::json(ResponseUtil::makeError('Your account is blocked',array('type' => '')), 401);
            }
        }
        try {
            /*//check if user has reached the max number of login attempts
            if ($this->hasTooManyLoginAttempts($request2))
            {
                $this->fireLockoutEvent($request2);
                return "To many attempts...";
            }
            //verify user credentials
            $credentials = ['email' => $request2->username, 'password' => $request2->password];
            if (Auth::attempt($credentials)) {
                //reset failed login attemps
                $this->clearLoginAttempts($request2);
            }*/

            $response = $this->server->respondToAccessTokenRequest($request, new Psr7Response);
            if($response){
                $user = User::where('email',$request2->username)->first();
                if($user){
                    $employees = Employee::find($user->employee_id)->update(['isLock' => 0]);
                }
            }
            return $response;
        } catch (OAuthServerException $exception) {
            //$this->incrementLoginAttempts($request2);
            $user = User::where('email',$request2->username)->first();
            if($user){
                $employees = Employee::find($user->employee_id)->increment('isLock');
            }
            return $this->withErrorHandling(function () use($exception,$user) {

                if($user) {
                    $employees = Employee::find($user->employee_id);
                    $totalAttempt = 4 - $employees->isLock;
                    //throw $exception;
                    if ($totalAttempt == 0) {
                        return Response::json(ResponseUtil::makeError('Your account is blocked', array('type' => '')), 401);
                    } else {
                        //throw new OAuthServerException('Invalid username or password. You have ' . $totalAttempt . ' more attempt', 6, 'error', 401);
                        return response(["message" => 'Invalid username or password. You have ' . $totalAttempt . ' more attempt'], 401);
                    }
                }else{
                    return response(["message" => 'Invalid username or password.'], 401);
                }
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

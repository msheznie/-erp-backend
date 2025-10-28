<?php

namespace App\Http\Controllers\API;

use App\Models\AccessTokens;
use App\Models\Employee;
use App\Models\User;
use App\Services\AuditLog\AuthAuditService;
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
                AuthAuditService::logLoginFailure($request2->username, 'Employee not found', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                AuthAuditService::logLoginFailure($request2->username, 'Employee discharged', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                AuthAuditService::logLoginFailure($request2->username, 'Account not activated', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->isLock >= 4){
                AuthAuditService::logLoginFailure($request2->username, 'Account locked', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->empLoginActive != 1){
                AuthAuditService::logLoginFailure($request2->username, 'Login disabled', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->empActive != 1){
                AuthAuditService::logLoginFailure($request2->username, 'Employee inactive', $request2);
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

            $this->logSuccessfulAuthentication($response);

            return $response;
        } catch (OAuthServerException $exception) {
            $user = User::where('email',$request2->username)->first();
            if($user){
                $employees = Employee::find($user->employee_id);

                if(empty($employees)){
                    AuthAuditService::logLoginFailure($request2->username, 'Employee not found', $request2);
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if($employees->discharegedYN){
                    AuthAuditService::logLoginFailure($request2->username, 'Employee discharged', $request2);
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if(!$employees->ActivationFlag){
                    AuthAuditService::logLoginFailure($request2->username, 'Account not activated', $request2);
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if($employees->empLoginActive != 1){
                    AuthAuditService::logLoginFailure($request2->username, 'Login disabled', $request2);
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                if($employees->empActive != 1){
                    AuthAuditService::logLoginFailure($request2->username, 'Employee inactive', $request2);
                    return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
                }

                 Employee::find($user->employee_id)->increment('isLock');
            }
            
            // Log failed login attempt
            AuthAuditService::logLoginFailure($request2->username, 'Invalid credentials', $request2);
            
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
            AuthAuditService::logLoginFailure('-', 'Token validation failed', $request2);
            return Response::json(ResponseUtil::makeError($validator->messages(), array('type' => '')), 422);
        }
        $user = User::where(['login_token' => $input['token']])->first();
        if (empty($user)) {
            AuthAuditService::logLoginFailure('-', 'Invalid or expired login token', $request2);
            return Response::json(ResponseUtil::makeError(trans('custom.token_expired'), array('type' => '')), 500);
        }

        if($user){
            $employees = Employee::find($user->employee_id);

            if(empty($employees)){
                AuthAuditService::logLoginFailure($user->email, 'Employee not found', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.not_found', ['attribute' => trans('custom.user')]),array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                AuthAuditService::logLoginFailure($user->email, 'Employee discharged', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_the_user_is_discharged_please_contact_admin'),array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                AuthAuditService::logLoginFailure($user->email, 'Account not activated', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_the_user_is_not_activated_please_contact_admin'),array('type' => '')), 401);
            }

            if($employees->isLock == 4){
                AuthAuditService::logLoginFailure($user->email, 'Account locked', $request2);
                return Response::json(ResponseUtil::makeError(trans('custom.your_account_is_blocked'),array('type' => '')), 401);
            }
        }
        try {
            $user->login_token = null;
            $user->save();

            $response = $user->createToken('personal');
            
            $this->logPersonalTokenAuthentication($response, $request2);

            return $response;
        } catch (OAuthServerException $exception) {
            AuthAuditService::logLoginFailure($user->email ?? '-', 'Token creation failed', $request2);
            return $this->withErrorHandling(function () use($exception) {
                return response(["message" => trans('custom.error')], 401);
            });
        }
    }

    /**
     * Log successful authentication to audit log (for OAuth token)
     *
     * @param  \Zend\Diactoros\Response  $response
     * @return void
     */
    private function logSuccessfulAuthentication($response)
    {
        // Parse the PSR-7 response body to get the access token
        $responseBody = json_decode($response->getBody()->__toString(), true);
        
        if (isset($responseBody['access_token'])) {
            // Extract the token ID from the access_token JWT
            $tokenParts = explode('.', $responseBody['access_token']);
            if (count($tokenParts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
                
                if (isset($payload['jti'])) {
                    // Retrieve the access token from database
                    $accessToken = AccessTokens::find($payload['jti']);
                    
                    if ($accessToken) {
                        $sessionId = $accessToken->session_id;
                        $user = User::with(['employee'])->find($accessToken->user_id);
                        
                        if ($user && $user->employee) {
                            AuthAuditService::logLoginSuccess(
                                $sessionId,
                                $user,
                                $user->employee,
                                'passport',
                                request()
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Log successful authentication to audit log (for personal access token)
     *
     * @param  \Laravel\Passport\PersonalAccessTokenResult  $tokenResult
     * @return void
     */
    private function logPersonalTokenAuthentication($tokenResult, $request2)
    {
        // Retrieve the access token from database to get session_id
        $accessToken = AccessTokens::find($tokenResult->token->id);

        $tenantUuid = isset($request2->tenant_uuid) ? $request2->tenant_uuid : 'local';
        
        if ($accessToken) {
            $sessionId = $accessToken->session_id;
            $user = User::with(['employee'])->find($accessToken->user_id);
            
            if ($user && $user->employee) {
                AuthAuditService::logLoginSuccess(
                    $sessionId,
                    $user,
                    $user->employee,
                    'passport',
                    request(),
                    $tenantUuid
                );
            }
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

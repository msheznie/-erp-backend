<?php

namespace App\Http\Controllers\API;

use App\Models\AccessTokens;
use App\Models\Employee;
use App\Models\User;
use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Utils\ResponseUtil;
use Psr\Http\Message\ServerRequestInterface;

class AuthAPIController extends Controller
{
    use AuditLogsTrait;

    public function auth(Request $request2)
    {
        // Validate required fields
        $validator = Validator::make($request2->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->log('auth', [
                'event' => 'login_failure',
                'username' => $request2->username ?? '-',
                'reason' => 'Missing required fields',
                'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
            ]);
            return Response::json(ResponseUtil::makeError($validator->messages(), array('type' => '')), 422);
        }

        // Find user by email
        $user = User::where('email', $request2->username)->first();

        // Verify password manually (since Passport TokenGuard doesn't support attempt())
        if (!$user || !Hash::check($request2->password, $user->password)) {
            if($user){
                $employees = Employee::find($user->employee_id);
                if($employees){
                    Employee::find($user->employee_id)->increment('isLock');
                }
            }
            
            $this->log('auth', [
                'event' => 'login_failure',
                'username' => $request2->username,
                'reason' => 'Invalid credentials',
                'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
            ]);
            return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
        }
        
        // Validate employee status
        if($user){
            $employees = Employee::find($user->employee_id);

            if(empty($employees)){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $request2->username,
                    'reason' => 'Employee not found',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $request2->username,
                    'reason' => 'Employee discharged',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $request2->username,
                    'reason' => 'Account not activated',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->isLock >= 4){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $request2->username,
                    'reason' => 'Account locked',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->empLoginActive != 1){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $request2->username,
                    'reason' => 'Login disabled',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }

            if($employees->empActive != 1){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $request2->username,
                    'reason' => 'Employee inactive',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_invalid_user_id_or_password'),array('type' => '')), 401);
            }
        }

        try {
            // Reset lock counter on successful login
            if($user && $user->employee_id){
                Employee::find($user->employee_id)->update(['isLock' => 0]);
            }

            // Create Passport personal access token
            $tokenResult = $user->createToken('personal');
            
            // Format response to match OAuth2 token response format
            $response = response()->json([
                'token_type' => 'Bearer',
                'expires_in' => $tokenResult->token->expires_at ? $tokenResult->token->expires_at->diffInSeconds(now()) : 3600,
                'access_token' => $tokenResult->accessToken,
            ]);

            // Extract login data BEFORE dispatching job to avoid serialization issues
            $loginData = \App\Services\AuditLog\AuthAuditService::extractLoginDataFromToken($tokenResult, $request2);
            if (!empty($loginData)) {
                $this->log('auth', $loginData);
            }

            return $response;
        } catch (\Exception $exception) {
            $this->log('auth', [
                'event' => 'login_failure',
                'username' => $request2->username,
                'reason' => 'Token creation failed: ' . $exception->getMessage(),
                'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
            ]);
            return Response::json(ResponseUtil::makeError(trans('custom.error'),array('type' => '')), 500);
        }
    }


    public function authWithToken(ServerRequestInterface $request, Request $request2)
    {

        $input = $request2->all();
        $validator = Validator::make($input, [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            $this->log('auth', [
                'event' => 'login_failure',
                'username' => '-',
                'reason' => 'Token validation failed',
                'request' => $request2
            ]);
            return Response::json(ResponseUtil::makeError($validator->messages(), array('type' => '')), 422);
        }
        $user = User::where(['login_token' => $input['token']])->first();
        if (empty($user)) {
            $this->log('auth', [
                'event' => 'login_failure',
                'username' => '-',
                'reason' => 'Invalid or expired login token',
                'request' => $request2
            ]);
            return Response::json(ResponseUtil::makeError(trans('custom.token_expired'), array('type' => '')), 500);
        }

        if($user){
            $employees = Employee::find($user->employee_id);

            if(empty($employees)){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $user->email,
                    'reason' => 'Employee not found',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.not_found', ['attribute' => trans('custom.user')]),array('type' => '')), 401);
            }

            if($employees->discharegedYN){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $user->email,
                    'reason' => 'Employee discharged',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_the_user_is_discharged_please_contact_admin'),array('type' => '')), 401);
            }

            if(!$employees->ActivationFlag){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $user->email,
                    'reason' => 'Account not activated',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.login_failed_the_user_is_not_activated_please_contact_admin'),array('type' => '')), 401);
            }

            if($employees->isLock == 4){
                $this->log('auth', [
                    'event' => 'login_failure',
                    'username' => $user->email,
                    'reason' => 'Account locked',
                    'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
                ]);
                return Response::json(ResponseUtil::makeError(trans('custom.your_account_is_blocked'),array('type' => '')), 401);
            }
        }
        try {
            $user->login_token = null;
            $user->save();

            $response = $user->createToken('personal');
            
            // Extract login data BEFORE dispatching job to avoid serialization issues
            $loginData = \App\Services\AuditLog\AuthAuditService::extractLoginDataFromToken($response, $request2);
            if (!empty($loginData)) {
                $this->log('auth', $loginData);
            }

            return $response;
        } catch (OAuthServerException $exception) {
            $this->log('auth', [
                'event' => 'login_failure',
                'username' => $user->email ?? '-',
                'reason' => 'Token creation failed',
                'request' => \App\Services\AuditLog\AuthAuditService::extractRequestData($request2)
            ]);
            return response(["message" => trans('custom.error')], 401);
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
        $lockoutMinutes = 10;

        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            $attempts,
            $lockoutMinutes
        );
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(\Illuminate\Cache\RateLimiter::class);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return mb_strtolower($request->input('username', $request->ip())).'|'.$request->ip();
    }
}

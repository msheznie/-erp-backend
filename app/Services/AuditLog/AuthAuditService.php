<?php

namespace App\Services\AuditLog;

use App\Models\Employee;
use App\Models\AccessTokens;
use App\Models\ERPLanguageMaster;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthAuditService
{
    /**
     * Prepare audit data based on event type
     *
     * @param string $event
     * @param array $parameters
     * @return array
     */
    public static function prepareAuditData($event, $parameters)
    {
        switch ($event) {
            case 'login_success':
                return self::prepareLoginSuccessData($parameters);
            case 'login_failure':
                return self::prepareLoginFailureData($parameters);
            case 'logout':
                return self::prepareLogoutData($parameters);
            case 'token_expired':
                return self::prepareTokenExpiredData($parameters);
            default:
                Log::warning('Unknown auth event: ' . $event);
                return [];
        }
    }

    /**
     * Extract login data from OAuth response (for OAuth token)
     * This must be called BEFORE dispatching the job to avoid serialization issues
     *
     * @param \Zend\Diactoros\Response $response
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function extractLoginDataFromResponse($response, $request)
    {
        $responseBody = json_decode($response->getBody()->__toString(), true);
        
        if (isset($responseBody['access_token'])) {
            $tokenParts = explode('.', $responseBody['access_token']);
            if (count($tokenParts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
                
                if (isset($payload['jti'])) {
                    $accessToken = AccessTokens::find($payload['jti']);
                    
                    if ($accessToken) {
                        $sessionId = $accessToken->session_id;
                        $user = User::with(['employee'])->find($accessToken->user_id);
                        
                        if ($user && $user->employee) {
                            return [
                                'event' => 'login_success',
                                'sessionId' => $sessionId,
                                'user' => $user,
                                'employee' => $user->employee,
                                'authType' => 'passport',
                                'request' => self::extractRequestData($request),
                                'tenantUuid' => 'local'
                            ];
                        }
                    }
                }
            }
        }

        return [];
    }

    /**
     * Extract login data from personal access token
     * This must be called BEFORE dispatching the job to avoid serialization issues
     *
     * @param \Laravel\Passport\PersonalAccessTokenResult $tokenResult
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function extractLoginDataFromToken($tokenResult, $request)
    {
        $accessToken = AccessTokens::find($tokenResult->token->id);
        $tenantUuid = isset($request->tenant_uuid) ? $request->tenant_uuid : 'local';
        
        if ($accessToken) {
            $sessionId = $accessToken->session_id;
            $user = User::with(['employee'])->find($accessToken->user_id);
            
            if ($user && $user->employee) {
                return [
                    'event' => 'login_success',
                    'sessionId' => $sessionId,
                    'user' => $user,
                    'employee' => $user->employee,
                    'authType' => 'passport',
                    'request' => self::extractRequestData($request),
                    'tenantUuid' => $tenantUuid
                ];
            }
        }

        return [];
    }

    /**
     * Extract serializable data from request object
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function extractRequestData($request)
    {
        return [
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'x_forwarded_for' => $request->header('X-Forwarded-For'),
            'x_real_ip' => $request->header('X-Real-IP'),
            'tenant_uuid' => $request->tenant_uuid ?? null,
            'db' => $request->db ?? null,
        ];
    }

    /**
     * Prepare login success audit data
     *
     * @param array $parameters
     * @return array
     */
    private static function prepareLoginSuccessData($parameters)
    {
        $sessionId = $parameters['sessionId'];
        $user = $parameters['user'];
        $employee = $parameters['employee'];
        $authType = $parameters['authType'];
        $requestData = $parameters['request'];
        $tenantUuid = $parameters['tenantUuid'] ?? 'local';

        $baseData = [
            'channel' => 'auth',
            'event' => 'login',
            'status' => 'success',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? '-',
            'employeeName' => $employee->empName ?? '-',
            'role' => Employee::getDesignation($employee->employeeSystemID ?? null),
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => self::getIpAddressFromData($requestData),
            'deviceInfo' => self::extractDeviceInfo($requestData['user_agent'] ?? null),
            'tenant_uuid' => $tenantUuid,
            'module' => 'finance',
            'auth_type' => $authType,
        ];

        return self::createMultiLanguageData($baseData);
    }

    /**
     * Prepare login failure audit data
     *
     * @param array $parameters
     * @return array
     */
    private static function prepareLoginFailureData($parameters)
    {
        $username = $parameters['username'];
        $reason = $parameters['reason'];
        $requestData = $parameters['request'];

        $employee = Employee::where('empEmail', $username)->first();
        if ($employee) {
            $emp_id = $employee->empID;
            $emp_name = $employee->empName;
            $role = Employee::getDesignation($employee->employeeSystemID ?? null);
        } else {
            $emp_id = '-';
            $emp_name = '-';
            $role = '-';
        }

        $baseData = [
            'channel' => 'auth',
            'event' => 'login_failed',
            'status' => 'failed',
            'session_id' => null,
            'employeeId' => $emp_id,
            'employeeName' => $emp_name,
            'role' => $role,
            'reason' => $reason,
            'module' => 'finance',
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => self::getIpAddressFromData($requestData),
            'deviceInfo' => self::extractDeviceInfo($requestData['user_agent'] ?? null),
            'tenant_uuid' => $requestData['tenant_uuid'] ?? 'local',
            'auth_type' => 'passport',
        ];

        return self::createMultiLanguageData($baseData);
    }

    /**
     * Prepare logout audit data
     *
     * @param array $parameters
     * @return array
     */
    private static function prepareLogoutData($parameters)
    {
        $sessionId = $parameters['sessionId'];
        $user = $parameters['user'];
        $employee = $parameters['employee'];
        $requestData = $parameters['request'];

        $baseData = [
            'channel' => 'auth',
            'event' => 'logout',
            'status' => 'success',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? 'unknown',
            'employeeName' => $employee->empName ?? 'unknown',
            'role' => Employee::getDesignation($employee->employeeSystemID ?? null),
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => self::getIpAddressFromData($requestData),
            'deviceInfo' => self::extractDeviceInfo($requestData['user_agent'] ?? null),
            'tenant_uuid' => $requestData['tenant_uuid'] ?? 'local',
            'auth_type' => 'passport',
            'module' => 'finance'
        ];

        return self::createMultiLanguageData($baseData);
    }

    /**
     * Prepare token expired audit data
     *
     * @param array $parameters
     * @return array
     */
    private static function prepareTokenExpiredData($parameters)
    {
        $sessionId = $parameters['sessionId'];
        $userId = $parameters['userId'];
        $employeeId = $parameters['employeeId'] ?? null;
        $authType = $parameters['authType'] ?? 'passport';
        $tenantUuid = $parameters['tenantUuid'] ?? 'local';

        $user = User::find($userId);
        $employee = null;

        if ($employeeId) {
            $employee = Employee::find($employeeId);
        } elseif ($user && $user->employee_id) {
            $employee = Employee::find($user->employee_id);
        }

        $baseData = [
            'channel' => 'auth',
            'event' => 'session_expired',
            'status' => 'expired',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? 'unknown',
            'employeeName' => $employee->empName ?? 'unknown',
            'role' => Employee::getDesignation($employee->employeeSystemID ?? null),
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => 'system',
            'deviceInfo' => 'system',
            'module' => 'finance',
            'tenant_uuid' => $tenantUuid,
            'auth_type' => $authType,
        ];

        return self::createMultiLanguageData($baseData);
    }

    /**
     * Create multi-language data entries
     *
     * @param array $baseData
     * @return array
     */
    private static function createMultiLanguageData($baseData)
    {
        $activeLanguages = self::getActiveLanguages();
        $eventDataByLanguage = [];
        
        foreach ($activeLanguages as $language) {
            $eventData = $baseData;
            $eventData['locale'] = $language;
            $eventDataByLanguage[] = $eventData;
        }

        return $eventDataByLanguage;
    }

    /**
     * Get active languages from ERPLanguageMaster
     *
     * @return array
     */
    private static function getActiveLanguages()
    {
        try {
            $languages = ERPLanguageMaster::where('isActive', 1)
                ->pluck('languageShortCode')
                ->toArray();
            
            // If no active languages found, return default
            return !empty($languages) ? $languages : ['en'];
        } catch (\Exception $e) {
            Log::error('Failed to fetch active languages: ' . $e->getMessage());
            return ['en']; // Default fallback
        }
    }

    /**
     * Translate event data based on language
     *
     * @param array $eventData
     * @param string $lang
     * @return array
     */
    public static function translateEventData($eventData, $lang)
    {
        $translated = $eventData;
        
        // Map common phrases to translation keys
        $keyMap = [
            'Invalid credentials' => 'invalid_credentials',
            'Account locked' => 'account_locked',
            'Login disabled' => 'login_disabled',
            'Employee not found' => 'employee_not_found',
            'Employee discharged' => 'employee_discharged',
            'Account not activated' => 'account_not_activated',
            'Employee inactive' => 'employee_inactive',
            'Token validation failed' => 'token_validation_failed',
            'Invalid or expired login token' => 'invalid_or_expired_login_token',
            'Token creation failed' => 'token_creation_failed',
            'Unknown OS' => 'unknown_os',
            'unknown browser' => 'unknown_browser',
            'unknown IP' => 'unknown_ip',
        ];
        
        // Translate event type
        if (isset($translated['event'])) {
            $eventKey = str_replace(' ', '_', strtolower($translated['event']));
            $translated['event'] = trans("audit.{$eventKey}", [], $lang);
        }

        // Translate reason if exists
        if (isset($translated['reason'])) {
            // Check if there's a direct mapping
            if (isset($keyMap[$translated['reason']])) {
                $translated['reason'] = trans("audit.{$keyMap[$translated['reason']]}", [], $lang);
            } else {
                // Try to convert to snake_case key
                $reasonKey = str_replace(' ', '_', strtolower($translated['reason']));
                $translation = trans("audit.{$reasonKey}", [], $lang);
                // Only use translation if it's not the key itself
                if ($translation !== "audit.{$reasonKey}") {
                    $translated['reason'] = $translation;
                }
            }
        }

        return $translated;
    }

    /**
     * Extract device information from user agent
     *
     * @param string|null $userAgent
     * @return string
     */
    private static function extractDeviceInfo($userAgent)
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        // OS detection
        $os = 'Unknown OS';
        if (preg_match('/windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $os = 'iOS';
        }

        // Browser detection
        $browser = 'Unknown';
        if (preg_match('/chrome\/([0-9.]+)/i', $userAgent, $match)) {
            $browser = 'Chrome/' . $match[1];
        } elseif (preg_match('/firefox\/([0-9.]+)/i', $userAgent, $match)) {
            $browser = 'Firefox/' . $match[1];
        } elseif (preg_match('/safari\/([0-9.]+)/i', $userAgent, $match)) {
            if (!preg_match('/chrome/i', $userAgent)) {
                $browser = 'Safari/' . $match[1];
            }
        } elseif (preg_match('/edge\/([0-9.]+)/i', $userAgent, $match)) {
            $browser = 'Edge/' . $match[1];
        } elseif (preg_match('/edg\/([0-9.]+)/i', $userAgent, $match)) {
            $browser = 'Edge/' . $match[1];
        }

        return "$os $browser";
    }


    /**
     * Get IP address from request data array
     *
     * @param array $requestData
     * @return string
     */
    private static function getIpAddressFromData($requestData)
    {
        if (empty($requestData)) {
            return 'unknown';
        }

        try {
            // Check for proxy headers
            if (!empty($requestData['x_forwarded_for'])) {
                $ips = explode(',', $requestData['x_forwarded_for']);
                return trim($ips[0]);
            }

            if (!empty($requestData['x_real_ip'])) {
                return $requestData['x_real_ip'];
            }

            return $requestData['ip'] ?? 'unknown';
        } catch (\Exception $e) {
            Log::error('Failed to get IP address: ' . $e->getMessage());
            return 'unknown';
        }
    }


}


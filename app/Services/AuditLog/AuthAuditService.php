<?php

namespace App\Services\AuditLog;

use App\Models\EmployeeNavigation;
use App\Models\ERPLanguageMaster;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthAuditService
{
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
     * Log successful login
     *
     * @param string $sessionId
     * @param User $user
     * @param $employee
     * @param string $authType
     * @param $request
     * @return void
     */
    public static function logLoginSuccess($sessionId, $user, $employee, $authType, $request, $tenantUuid = 'local')
    {
        $baseData = [
            'channel' => 'auth',
            'event' => 'login',
            'status' => 'success',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? '-',
            'employeeName' => $employee->empName ?? '-',
            'role' => self::getRoleFromEmployee($employee->employeeSystemID ?? null),
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => self::getIpAddress($request),
            'deviceInfo' => self::extractDeviceInfo($request->header('User-Agent')),
            'tenant_uuid' => $tenantUuid,
            'auth_type' => $authType,
        ];

        // Create log entries for all active languages
        $activeLanguages = self::getActiveLanguages();
        $eventDataByLanguage = [];
        
        foreach ($activeLanguages as $language) {
            $eventData = $baseData;
            $eventData['locale'] = $language;
            $eventDataByLanguage[] = $eventData;
        }

        self::writeToAuditLog(...$eventDataByLanguage);
    }

    /**
     * Log failed login attempt
     *
     * @param string $username
     * @param string $reason
     * @param $request
     * @return void
     */
    public static function logLoginFailure($username, $reason, $request)
    {
        $employee = \App\Models\Employee::where('empEmail', $username)->first();
        if ($employee) {
            $emp_id = $employee->empID;
            $emp_name = $employee->empName;
            $role = self::getRoleFromEmployee($employee->employeeSystemID ?? null);
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
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => self::getIpAddress($request),
            'deviceInfo' => self::extractDeviceInfo($request->header('User-Agent')),
            'tenant_uuid' => self::getTenantUuid($request),
            'auth_type' => 'passport',
        ];

        // Create log entries for all active languages
        $activeLanguages = self::getActiveLanguages();
        $eventDataByLanguage = [];
        
        foreach ($activeLanguages as $language) {
            $eventData = $baseData;
            $eventData['locale'] = $language;
            $eventDataByLanguage[] = $eventData;
        }

        self::writeToAuditLog(...$eventDataByLanguage);
    }

    /**
     * Log user logout
     *
     * @param string $sessionId
     * @param User $user
     * @param $employee
     * @param $request
     * @return void
     */
    public static function logLogout($sessionId, $user, $employee, $request)
    {
        $baseData = [
            'channel' => 'auth',
            'event' => 'logout',
            'status' => 'success',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? 'unknown',
            'employeeName' => $employee->empName ?? 'unknown',
            'role' => self::getRoleFromEmployee($employee->employeeSystemID ?? null),
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => self::getIpAddress($request),
            'deviceInfo' => self::extractDeviceInfo($request->header('User-Agent')),
            'tenant_uuid' => self::getTenantUuid($request),
            'auth_type' => 'passport',
        ];

        // Create log entries for all active languages
        $activeLanguages = self::getActiveLanguages();
        $eventDataByLanguage = [];
        
        foreach ($activeLanguages as $language) {
            $eventData = $baseData;
            $eventData['locale'] = $language;
            $eventDataByLanguage[] = $eventData;
        }

        self::writeToAuditLog(...$eventDataByLanguage);
    }

    /**
     * Log token expiration
     *
     * @param string $tokenId
     * @param int $userId
     * @param int|null $employeeId
     * @param string $authType
     * @return void
     */
    public static function logTokenExpired($sessionId, $userId, $employeeId = null, $authType = 'passport', $tenantUuid = 'local')
    {
        $user = User::find($userId);
        $employee = null;

        if ($employeeId) {
            $employee = \App\Models\Employee::find($employeeId);
        } elseif ($user && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }

        $baseData = [
            'channel' => 'auth',
            'event' => 'session_expired',
            'status' => 'expired',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? 'unknown',
            'employeeName' => $employee->empName ?? 'unknown',
            'role' => self::getRoleFromEmployee($employee->employeeSystemID ?? null),
            'date_time' => date('Y-m-d H:i:s'),
            'ipAddress' => 'system',
            'deviceInfo' => 'system',
            'tenant_uuid' => $tenantUuid,
            'auth_type' => $authType,
        ];

        // Create log entries for all active languages
        $activeLanguages = self::getActiveLanguages();
        $eventDataByLanguage = [];
        
        foreach ($activeLanguages as $language) {
            $eventData = $baseData;
            $eventData['locale'] = $language;
            $eventDataByLanguage[] = $eventData;
        }

        self::writeToAuditLog(...$eventDataByLanguage);
    }

    /**
     * Extract session ID from JWT token
     *
     * @param string|null $token
     * @param string $authType
     * @return string|null
     */
    public static function extractSessionIdFromToken($token, $authType)
    {
        if (!$token) {
            return null;
        }

        try {
            $tokenParts = explode('.', $token);
            if (count($tokenParts) !== 3) {
                return null;
            }

            $payload = json_decode(base64_decode($tokenParts[1]), true);

            if ($authType === 'passport' || $authType === 'api') {
                return $payload['jti'] ?? null;
            } elseif ($authType === 'keycloak') {
                return $payload['sid'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to extract session ID from token: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Extract device information from user agent
     *
     * @param string|null $userAgent
     * @return string
     */
    public static function extractDeviceInfo($userAgent)
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
     * Get role from employee navigation
     *
     * @param int|null $employeeId
     * @return string
     */
    public static function getRoleFromEmployee($employeeId)
    {
        if (!$employeeId) {
            return 'Unknown';
        }

        try {
            $empNav = EmployeeNavigation::with('usergroup')
                ->where('employeeSystemID', $employeeId)
                ->get();

            $roles = [];
            foreach ($empNav as $nav) {
                if ($nav->usergroup) {
                    $roles[] = $nav->usergroup->description;
                }
            }
        
            return empty($roles) ? '-' : implode(', ', $roles);
        } catch (\Exception $e) {
            Log::error('Failed to get role from employee: ' . $e->getMessage());
            return 'Unknown';
        }
    }

    /**
     * Get IP address from request
     *
     * @param $request
     * @return string
     */
    public static function getIpAddress($request)
    {
        if (!$request) {
            return 'unknown';
        }

        try {
            // Check for proxy headers
            if ($request->header('X-Forwarded-For')) {
                $ips = explode(',', $request->header('X-Forwarded-For'));
                return trim($ips[0]);
            }

            if ($request->header('X-Real-IP')) {
                return $request->header('X-Real-IP');
            }

            return $request->ip();
        } catch (\Exception $e) {
            Log::error('Failed to get IP address: ' . $e->getMessage());
            return 'unknown';
        }
    }

    /**
     * Get tenant UUID from request
     *
     * @param $request
     * @return string
     */
    public static function getTenantUuid($request)
    {
        if (!$request) {
            return 'local';
        }

        try {
            // Try to get from request parameter
            if (isset($request->tenant_uuid)) {
                return $request->tenant_uuid;
            }

            return 'local';
        } catch (\Exception $e) {
            Log::error('Failed to get tenant UUID: ' . $e->getMessage());
            return 'local';
        }
    }

    /**
     * Write event data to audit log
     *
     * @param array ...$eventDataArray
     * @return void
     */
    public static function writeToAuditLog(...$eventDataArray)
    {
        try {
            Log::useFiles(storage_path() . '/logs/audit.log');
            
            // Write logs for each language
            foreach ($eventDataArray as $eventData) {
                $locale = $eventData['locale'] ?? 'en';
                $translatedData = self::translateEventData($eventData, $locale);
                Log::info('data:', $translatedData);
            }
        } catch (\Exception $e) {
            Log::error('Failed to write to audit log: ' . $e->getMessage());
        }
    }

    /**
     * Translate event data based on language
     *
     * @param array $eventData
     * @param string $lang
     * @return array
     */
    private static function translateEventData($eventData, $lang)
    {
        $translationMap = [
            'en' => [
                'login' => 'Login',
                'logout' => 'Logout',
                'login_failed' => 'Login Failed',
                'token_expired' => 'Token Expired',
                'success' => 'Success',
                'failed' => 'Failed',
                'expired' => 'Expired',
                'Token expired' => 'Token expired',
                'Invalid credentials' => 'Invalid Credentials',
                'Account locked' => 'Account Locked',
                'Login disabled' => 'Login Disabled',
                'Employee not found' => 'Employee Not Found',
                'Employee discharged' => 'Employee Discharged',
                'Account not activated' => 'Account Not Activated',
                'Employee inactive' => 'Employee Inactive',
                'Token validation failed' => 'Token Validation Failed',
                'Invalid or expired login token' => 'Invalid or Expired Login Token',
                'Token creation failed' => 'Token Creation Failed',
                'unknown' => 'Unknown',
                'Unknown OS' => 'Unknown OS',
                'unknown browser' => 'Unknown Browser',
                'unknown IP' => 'Unknown IP',
                'Windows' => 'Windows',
                'macOS' => 'macOS',
                'Linux' => 'Linux',
                'Android' => 'Android',
                'iOS' => 'iOS',
                'Chrome' => 'Chrome',
                'Firefox' => 'Firefox',
                'Safari' => 'Safari',
                'Edge' => 'Edge',
                'session_expired' => 'Session Expired',
            ],
            'ar' => [
                'login' => 'تسجيل الدخول',
                'logout' => 'تسجيل الخروج',
                'login_failed' => 'فشل تسجيل الدخول',
                'token_expired' => 'انتهت صلاحية الجلسة',
                'success' => 'نجح',
                'failed' => 'فشل',
                'expired' => 'منتهي الصلاحية',
                'Token expired' => 'انتهت صلاحية الجلسة',
                'Invalid credentials' => 'بيانات اعتماد غير صحيحة',
                'Account locked' => 'تم قفل الحساب',
                'Login disabled' => 'تم تعطيل تسجيل الدخول',
                'Employee not found' => 'الموظف غير موجود',
                'Employee discharged' => 'تم تسريح الموظف',
                'Account not activated' => 'الحساب غير مفعل',
                'Employee inactive' => 'الموظف غير نشط',
                'Token validation failed' => 'فشل التحقق من الرمز',
                'Invalid or expired login token' => 'رمز تسجيل الدخول غير صالح أو منتهي الصلاحية',
                'Token creation failed' => 'فشل إنشاء الرمز',
                'unknown' => 'مجهول',
                'Unknown OS' => 'مجهول أوس',
                'unknown browser' => 'مجهول المتصفح',
                'unknown IP' => 'مجهول العنوان الإيبي',
                'Windows' => 'نظام ويندوز',
                'macOS' => 'ماك أو إس',
                'Linux' => 'لينكس',
                'Android' => 'أندرويد',
                'iOS' => 'آي أو إس',
                'Chrome' => 'شريط العتاد',
                'Firefox' => 'فايرفوكس',
                'Safari' => 'سفاري',
                'Edge' => 'أيجد',
                'session_expired' => 'انتهت صلاحية الجلسة',
            ]
        ];

        $translated = $eventData;
        
        // Translate event type
        if (isset($translated['event']) && isset($translationMap[$lang][$translated['event']])) {
            $translated['event'] = $translationMap[$lang][$translated['event']];
        }

        // Translate reason if exists
        if (isset($translated['reason']) && isset($translationMap[$lang][$translated['reason']])) {
            $translated['reason'] = $translationMap[$lang][$translated['reason']];
        }

        return $translated;
    }
}


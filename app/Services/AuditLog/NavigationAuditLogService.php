<?php

namespace App\Services\AuditLog;

use App\Models\Employee;
use App\Models\Company;
use App\Models\ERPLanguageMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessTokens;

class NavigationAuditLogService
{
    /**
     * Prepare navigation access audit data
     *
     * @param array $parameters
     * @return array
     */
    public static function prepareNavigationAccessData($parameters)
    {
        $sessionId = $parameters['sessionId'];
        $user = $parameters['user'];
        $employee = $parameters['employee'];
        $screenAccessed = $parameters['screenAccessed'];
        $navigationPath = $parameters['navigationPath'];
        $accessType = $parameters['accessType'];
        $company = $parameters['company'] ?? '-';
        $companyID = $parameters['companyID'] ?? null;
        $requestData = $parameters['request'];
        $tenantUuid = $parameters['tenantUuid'] ?? 'local';

        $baseData = [
            'channel' => 'navigation',
            'session_id' => (string) $sessionId,
            'employeeId' => $employee->empID ?? '-',
            'employeeName' => $employee->empName ?? '-',
            'role' => Employee::getDesignation($employee->employeeSystemID ?? null),
            'accessType' => $accessType,
            'company' => $company,
            'companyID' => $companyID,
            'date_time' => date('Y-m-d H:i:s'),
            'module' => 'finance',
            'tenant_uuid' => $tenantUuid,
            'screenAccessed' => $screenAccessed,
            'navigationPath' => $navigationPath,
        ];

        return self::createMultiLanguageData($baseData);
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
            'action' => $request->input('action') ?? null,
            'accessType' => $request->input('accessType') ?? null,
        ];
    }

    /**
     * Determine access type from request
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public static function determineAccessType($request)
    {
        // Check if accessType is directly provided
        if ($request->has('accessType')) {
            return $request->input('accessType');
        }
        
        // Map common actions to access types
        if ($request->has('action')) {
            $action = $request->input('action');
            $actionMap = [
                'create' => 'create',
                'add' => 'create',
                'update' => 'edit',
                'edit' => 'edit',
                'delete' => 'delete',
                'remove' => 'delete',
                'read' => 'read',
                'view' => 'read',
                'print' => 'read',
                'export' => 'read',
            ];
            return $actionMap[strtolower($action)] ?? 'read';
        }

        // Default to read
        return 'read';
    }

    /**
     * Extract navigation access data inside the job
     * This is called WITHIN the job to do all heavy processing (DB queries, user lookups)
     *
     * @param int $navigationMenuID
     * @param int $companyID
     * @param string $accessType
     * @param int|null $userId
     * @param int|null $tokenId
     * @param array $requestData
     * @return array
     */
    public static function extractNavigationAccessDataInJob($navigationMenuID, $companyID, $accessType, $userId, $tokenId, $requestData)
    {
        try {
            if (!$userId) {
                Log::error('Navigation access logging: No user ID provided');
                return [];
            }

            if (!$tokenId) {
                Log::error('Navigation access logging: No token ID provided');
                return [];
            }

            $user = \App\Models\User::find($userId);
            
            if (!$user || !$user->employee) {
                Log::error('Navigation access logging: User not found or no employee for user ID: ' . $userId);
                return [];
            }

            // Get session ID from the specific access token used for this request
            $accessToken = AccessTokens::find($tokenId);
                
            $sessionId = $accessToken && $accessToken->session_id ? $accessToken->session_id : null;

            if (!$sessionId) {
                Log::error('Navigation access logging: No session ID found for token ID: ' . $tokenId);
                return [];
            }

            // Get screen name and navigation path in both languages (DB queries happen here)
            $navigationData = self::getNavigationData($navigationMenuID, $companyID);

            $company = Company::find($companyID);


            return [
                'sessionId' => $sessionId,
                'user' => $user,
                'employee' => $user->employee,
                'screenAccessed' => $navigationData['screenName'],
                'navigationPath' => $navigationData['navigationPath'],
                'accessType' => $accessType,
                'company' => $company ? $company->CompanyID . ' - ' . $company->CompanyName : '-',
                'companyID' => $companyID,
                'request' => $requestData,
                'tenantUuid' => $requestData['tenant_uuid'] ?? 'local'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to extract navigation access data in job: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * Get navigation data (screen name and path) for all active languages
     *
     * @param int $navigationMenuID
     * @param int $companyID
     * @return array
     */
    private static function getNavigationData($navigationMenuID, $companyID)
    {
        try {
            $navigationMenu = DB::table('srp_erp_navigationmenus')
                ->where('navigationMenuID', $navigationMenuID)
                ->first();
            
            if (!$navigationMenu) {
                return self::getDefaultNavigationData();
            }

            $activeLanguages = self::getActiveLanguages();
            
            // Function to get menu description for all active languages
            $getMenuDescription = function($menuID) use ($companyID, $activeLanguages) {
                $menu = DB::table('srp_erp_navigationmenus')
                    ->where('navigationMenuID', $menuID)
                    ->first();
                
                $defaultDescription = $menu ? $menu->description : '-';
                
                $descriptions = [];
                foreach ($activeLanguages as $lang) {
                    if ($lang === 'en') {
                        $descriptions[$lang] = $defaultDescription;
                    } else {
                        // Get language-specific description
                        $langDescription = DB::table('srp_erp_navigationmenus_languages')
                            ->where('navigationMenuID', $menuID)
                            ->where('languageCode', $lang)
                            ->value('description');
                        
                        $descriptions[$lang] = $langDescription ?? $defaultDescription;
                    }
                }
                
                return $descriptions;
            };
            
            // Get screen name in all languages
            $screenNameTranslations = $getMenuDescription($navigationMenuID);
            
            // Build breadcrumb navigation path by traversing master hierarchy
            $breadcrumbsByLang = [];
            foreach ($activeLanguages as $lang) {
                $breadcrumbsByLang[$lang] = [];
            }
            
            $currentMenu = $navigationMenu;
            $maxDepth = 10; // Safety limit
            $depth = 0;
            
            // Add current screen first
            $currentDescriptions = $getMenuDescription($navigationMenuID);
            foreach ($activeLanguages as $lang) {
                $breadcrumbsByLang[$lang][] = $currentDescriptions[$lang];
            }
            
            // Loop upward through master hierarchy
            while ($currentMenu && !empty($currentMenu->masterID) && $depth < $maxDepth) {
                $masterMenu = DB::table('srp_erp_navigationmenus')
                    ->where('navigationMenuID', $currentMenu->masterID)
                    ->first();
                
                if ($masterMenu) {
                    $masterDescriptions = $getMenuDescription($currentMenu->masterID);
                    foreach ($activeLanguages as $lang) {
                        $breadcrumbsByLang[$lang][] = $masterDescriptions[$lang];
                    }
                    $currentMenu = $masterMenu;
                    $depth++;
                } else {
                    break;
                }
            }
            
            // Reverse to get path from master to child and join
            $navigationPaths = [];
            foreach ($activeLanguages as $lang) {
                $breadcrumbs = array_reverse($breadcrumbsByLang[$lang]);
                // Use appropriate arrow based on language direction (RTL or LTR)
                $arrow = self::isRTL($lang) ? ' ← ' : ' → ';
                $navigationPaths[$lang] = implode($arrow, $breadcrumbs);
            }
            
            return [
                'screenName' => $screenNameTranslations,
                'navigationPath' => $navigationPaths
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get navigation data: ' . $e->getMessage());
            return self::getDefaultNavigationData();
        }
    }

    /**
     * Get default navigation data when menu not found
     *
     * @return array
     */
    private static function getDefaultNavigationData()
    {
        $activeLanguages = self::getActiveLanguages();
        $screenName = [];
        $navigationPath = [];
        
        foreach ($activeLanguages as $lang) {
            $screenName[$lang] = trans('audit.unknown_screen', [], $lang);
            $navigationPath[$lang] = trans('audit.unknown', [], $lang);
        }
        
        return [
            'screenName' => $screenName,
            'navigationPath' => $navigationPath
        ];
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
            
            // Extract language-specific screen and path if available
            if (isset($baseData['screenAccessed']) && is_array($baseData['screenAccessed'])) {
                $eventData['screenAccessed'] = $baseData['screenAccessed'][$language] ?? $baseData['screenAccessed']['en'] ?? trans('audit.unknown_screen', [], $language);
            }
            
            if (isset($baseData['navigationPath']) && is_array($baseData['navigationPath'])) {
                $eventData['navigationPath'] = $baseData['navigationPath'][$language] ?? $baseData['navigationPath']['en'] ?? trans('audit.unknown', [], $language);
            }
            
            // Translate access type
            if (isset($baseData['accessType'])) {
                $accessTypeKey = strtolower($baseData['accessType']);
                $eventData['accessType'] = trans("audit.{$accessTypeKey}", [], $language);
                // If translation not found, keep original
                if ($eventData['accessType'] === "audit.{$accessTypeKey}") {
                    $eventData['accessType'] = $baseData['accessType'];
                }
            }
            
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
     * Check if a language is RTL (Right-to-Left)
     * 
     * @param string $languageCode
     * @return bool
     */
    private static function isRTL($languageCode)
    {
        // List of RTL language codes
        $rtlLanguages = ['ar', 'he', 'fa', 'ur']; // Arabic, Hebrew, Persian, Urdu
        
        return in_array(strtolower($languageCode), $rtlLanguages);
    }
}


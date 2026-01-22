<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * VictoriaLogs Service
 *
 * Service for querying audit logs from VictoriaLogs using LogsQL
 *
 * Features:
 * - LogsQL query building
 * - NDJSON response parsing
 * - Date range filtering
 * - Multi-channel support (audit, auth, navigation, third_party_api)
 */
class VictoriaLogsService
{
    protected $httpClient;
    protected $baseUrl;
    protected $queryUrl;
    protected $username;
    protected $password;
    protected $environment;
    protected $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = config('victorialogs');

        $this->baseUrl = rtrim(env('VICTORIA_LOG_URL', $this->config['api_url'] ?? ''), '/');
        $this->queryUrl = $this->baseUrl . ($this->config['endpoints']['query'] ?? '/select/logsql/query');
        $this->username = env('VICTORIA_LOG_USERNAME', $this->config['auth']['username'] ?? '');
        $this->password = env('VICTORIA_LOG_PASSWORD', $this->config['auth']['password'] ?? '');
        $this->environment = env('VICTORIALOGS_ENV', $this->config['environment'] ?? env('APP_ENV', 'production'));

        $timeout = $this->config['timeout'] ?? 60;

        $this->httpClient = new Client([
            'timeout' => $timeout,
            'connect_timeout' => 15,
            'auth' => [$this->username, $this->password],
        ]);
    }

    /**
     * Query logs using LogsQL
     *
     * @param string $logsQLQuery The LogsQL query string
     * @return array Array of log entries
     * @throws \Exception
     */
    public function queryLogsQL(string $logsQLQuery): array
    {
        try {
            $response = $this->httpClient->post($this->queryUrl, [
                'form_params' => [
                    'query' => $logsQLQuery,
                ],
            ]);

            $body = $response->getBody()->getContents();
            return $this->parseNDJSONResponse($body);

        } catch (RequestException $e) {
            Log::error('Failed to query VictoriaLogs', [
                'query' => $logsQLQuery,
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            throw new \Exception('Failed to query logs: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get audit logs (business transaction logs from AuditLogJob)
     * 
     * Modes:
     * 1. Transaction-specific: Get all logs for one transaction (for shared audit-logs component)
     * 2. Global tracking: Get all action tracking logs with filters (for action-tracking-logs component)
     *
     * @param array $params Query parameters
     * @return array ['data' => []]
     */
    public function getAuditLogs(array $params) : array
    {
        $tenantUuid = $params['tenant_uuid'] ?? '';
        $companyId = $params['companyId'] ?? null;
        
        if (!empty($params['id']) && !empty($params['module'])) {
            return $this->getTransactionAuditLogs($params);
        }
        
        if (!empty($params['isFromTracking'])) {
            return $this->getActionTrackingLogs($params);
        }
        
        return [
            'data' => [],
        ];
    }

    /**
     * Get transaction-specific audit logs
     *
     * @param array $params
     * @return array
     */
    protected function getTransactionAuditLogs(array $params) : array
    {
        $tenantUuid = $params['tenant_uuid'] ?? '';
        $transactionId = $params['id'];
        $module = $params['module'];
        $fromDate = $params['fromDate'] ?? null;
        $toDate = $params['toDate'] ?? null;
        $locale = $params['locale'] ?? 'en';
        
        $limit = $this->config['limit'] ?? 10000;
        
        $logsQL = $this->buildTransactionAuditQuery($tenantUuid, $transactionId, $module, $fromDate, $toDate, $locale, $limit);
        
        $logs = $this->queryLogsQL($logsQL);
        
        return [
            'data' => $logs,
        ];
    }

    /**
     * Get action tracking logs (global view with date filters)
     *
     * @param array $params
     * @return array
     */
    protected function getActionTrackingLogs(array $params): array
    {
        $tenantUuid = $params['tenant_uuid'] ?? '';
        $fromDate = $params['fromDate'] ?? null;
        $toDate = $params['toDate'] ?? null;
        $companyId = $params['companyId'] ?? null;
        $employeeId = $params['employeeId'] ?? null;
        $accessType = $params['accessType'] ?? null;
        $searchTerm = $params['search']['value'] ?? null;
        $locale = $params['locale'] ?? 'en';
        
        $limit = $this->config['limit'] ?? 10000;
        
        $logsQL = $this->buildActionTrackingQuery($tenantUuid, $fromDate, $toDate, $employeeId, $accessType, $locale, $limit, $companyId);
        
        $logs = $this->queryLogsQL($logsQL);
        
        if (!empty($searchTerm)) {
            $logs = $this->filterLogsBySearch($logs, $searchTerm);
        }
        
        return [
            'data' => $logs,
        ];
    }

    /**
     * Get user audit logs (authentication events)
     *
     * @param array $params
     * @return array
     */
    public function getUserAuditLogs(array $params) : array
    {
        $tenantUuid = $params['tenant_uuid'] ?? '';
        $fromDate = $params['fromDate'] ?? null;
        $toDate = $params['toDate'] ?? null;
        $employeeId = $params['employeeId'] ?? null;
        $event = $params['event'] ?? null;
        $searchTerm = $params['search']['value'] ?? null;
        $locale = $params['locale'] ?? 'en';
        
        $limit = $this->config['limit'] ?? 10000;
        
        $logsQL = $this->buildUserAuditQuery($tenantUuid, $fromDate, $toDate, $employeeId, $event, $locale, $limit);
        
        $logs = $this->queryLogsQL($logsQL);
        
        if (!empty($searchTerm)) {
            $logs = $this->filterLogsBySearch($logs, $searchTerm);
        }
        
        return [
            'data' => $logs,
        ];
    }

    /**
     * Get navigation access logs
     *
     * @param array $params
     * @return array
     */
    public function getNavigationAccessLogs(array $params): array
    {
        $tenantUuid = $params['tenant_uuid'] ?? '';
        $fromDate = $params['fromDate'] ?? null;
        $toDate = $params['toDate'] ?? null;
        $employeeId = $params['employeeId'] ?? null;
        $accessType = $params['accessType'] ?? null;
        $searchTerm = $params['search']['value'] ?? null;
        $locale = $params['locale'] ?? 'en';
        $companyId = $params['companyId'] ?? null;
        
        $limit = $this->config['limit'] ?? 10000;
        
        $logsQL = $this->buildNavigationAccessQuery($tenantUuid, $fromDate, $toDate, $employeeId, $accessType, $locale, $limit, $companyId);
        
        $logs = $this->queryLogsQL($logsQL);
        
        if (!empty($searchTerm)) {
            $logs = $this->filterLogsBySearch($logs, $searchTerm);
        }
        
        return [
            'data' => $logs,
        ];
    }

    /**
     * Get third-party API logs by external reference and log ID
     *
     * @param array $params
     * @return array
     */
    public function getThirdPartyApiLogs(array $params): array
    {
        $tenantUuid = $params['tenant_uuid'] ?? 'local';
        $externalReference = $params['external_reference'] ?? null;
        $logId = $params['logId'] ?? null;
        $isWebhook = $params['is_webhook'] ?? '0';
        $locale = $params['locale'] ?? 'en';
        
        if (!$externalReference) {
            return ['data' => []];
        }
        
        $limit = $this->config['limit'] ?? 10000;
        
        $logsQL = $this->buildThirdPartyApiQuery($tenantUuid, $externalReference, $logId, $isWebhook, $locale, $limit);
        
        $logs = $this->queryLogsQL($logsQL);
        
        return [
            'data' => $logs,
        ];
    }

    /**
     * Build LogsQL query for third-party API logs
     *
     * @param string $tenantUuid
     * @param string $externalReference
     * @param string|null $logId
     * @param string $isWebhook
     * @param string $locale
     * @param int $limit
     * @return string
     */
    protected function buildThirdPartyApiQuery(string $tenantUuid, string $externalReference, ?string $logId, string $isWebhook, string $locale = 'en', int $limit = 10000): string
    {
        $labels = [
            'tenant="' . $tenantUuid . '"',
            'app="erp"',
            'env="' . $this->environment . '"',
            'channel="third_party_api"',
            'locale="' . $locale . '"'
        ];
        
        $query = '{' . implode(',', $labels) . '}';
        $query .= ' | unpack_json';
        
        $filters = [];
        
        if ($externalReference) {
            $filters[] = 'external_reference:="' . addslashes($externalReference) . '"';
        }
        
        if ($logId) {
            $filters[] = 'log_id:="' . addslashes($logId) . '"';
        }
        
        if ($isWebhook !== null && $isWebhook !== '') {
            $filters[] = 'is_webhook:="' . addslashes($isWebhook) . '"';
        }
        
        if (!empty($filters)) {
            $query .= ' | filter ' . implode(' and ', $filters);
        }
        
        $query .= ' | sort by (_time) desc';
        $query .= ' | limit ' . $limit;
        
        return $query;
    }

    /**
     * Build LogsQL query for transaction-specific audit logs
     *
     * @param string $tenantUuid
     * @param string $transactionId
     * @param string $module
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string $locale
     * @param int $limit
     * @return string
     */
    protected function buildTransactionAuditQuery(string $tenantUuid, string $transactionId, string $module, ?string $fromDate, ?string $toDate, string $locale = 'en', int $limit = 10000): string
    {
        $query = '';
        if ($fromDate && $toDate) {
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
            $toCarbon = \Carbon\Carbon::parse($toDate);
            
            $from = \Carbon\Carbon::create($fromCarbon->year, $fromCarbon->month, $fromCarbon->day, 0, 0, 0, 'UTC')->toIso8601String();
            $to = \Carbon\Carbon::create($toCarbon->year, $toCarbon->month, $toCarbon->day, 23, 59, 59, 'UTC')->toIso8601String();
            $query = '_time:[' . $from . ',' . $to . '] ';
        }
        
        $labels = [
            'tenant="' . $tenantUuid . '"',
            'app="erp"',
            'env="' . $this->environment . '"',
            'channel="audit"',
            'locale="' . $locale . '"'
        ];
        
        $query .= '{' . implode(',', $labels) . '}';
        $query .= ' | unpack_json';
        $query .= ' | filter (transaction_id:"' . $transactionId . '" and table:"' . $module . '") or (parent_table:"' . $module . '" and parent_id:"' . $transactionId . '")';
        $query .= ' | sort by (_time) desc';
        $query .= ' | limit ' . $limit;
        
        return $query;
    }

    /**
     * Build LogsQL query for action tracking logs
     *
     * @param string $tenantUuid
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string|null $employeeId
     * @param int|null $accessType
     * @param string $locale
     * @param int $limit
     * @return string
     */
    protected function buildActionTrackingQuery(string $tenantUuid, ?string $fromDate, ?string $toDate, ?string $employeeId, ?int $accessType, string $locale = 'en', int $limit = 10000, ?int $companyId = null): string
    {
        $query = '';
        if ($fromDate && $toDate) {
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
            $toCarbon = \Carbon\Carbon::parse($toDate);
            
            $from = \Carbon\Carbon::create($fromCarbon->year, $fromCarbon->month, $fromCarbon->day, 0, 0, 0, 'UTC')->toIso8601String();
            $to = \Carbon\Carbon::create($toCarbon->year, $toCarbon->month, $toCarbon->day, 23, 59, 59, 'UTC')->toIso8601String();
            $query = '_time:[' . $from . ',' . $to . '] ';
        }
        
        $labels = [
            'tenant="' . $tenantUuid . '"',
            'app="erp"',
            'env="' . $this->environment . '"',
            'channel="audit"',
            'locale="' . $locale . '"'
        ];
        
        $query .= '{' . implode(',', $labels) . '}';
        $query .= ' | unpack_json';
        
        $filters = [];
        
        if ($employeeId) {
            $filters[] = 'employeeId:="' . $employeeId . '"';
        }
        
        if ($companyId) {
            $filters[] = '(company_system_id:="' . $companyId . '" or not company_system_id:* or company_system_id:="")';
        }
        
        if ($accessType !== null && $accessType !== '') {
            app()->setLocale($locale);
            
            $accessTypeMap = [
                '1' => "C",
                '2' => "U",
                '3' => "D",
            ];
            
            $accessTypeValue = $accessTypeMap[$accessType] ?? "C";
            $accessTypeValue = addslashes($accessTypeValue);
            $filters[] = 'crudType:="' . $accessTypeValue . '"';
        }
        
        if (!empty($filters)) {
            $query .= ' | filter ' . implode(' and ', $filters);
        }
        
        $query .= ' | sort by (_time) desc';
        $query .= ' | limit ' . $limit;
        
        return $query;
    }

    /**
     * Build LogsQL query for user audit logs
     *
     * @param string $tenantUuid
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string|null $employeeId
     * @param int|null $event
     * @param string $locale
     * @param int $limit
     * @return string
     */
    protected function buildUserAuditQuery(string $tenantUuid, ?string $fromDate, ?string $toDate, ?string $employeeId, ?int $event, string $locale = 'en', int $limit = 10000): string
    {
        $query = '';
        if ($fromDate && $toDate) {
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
            $toCarbon = \Carbon\Carbon::parse($toDate);
            
            $from = \Carbon\Carbon::create($fromCarbon->year, $fromCarbon->month, $fromCarbon->day, 0, 0, 0, 'UTC')->toIso8601String();
            $to = \Carbon\Carbon::create($toCarbon->year, $toCarbon->month, $toCarbon->day, 23, 59, 59, 'UTC')->toIso8601String();
            $query = '_time:[' . $from . ',' . $to . '] ';
        }
        
        $labels = [
            'tenant="' . $tenantUuid . '"',
            'app="erp"',
            'env="' . $this->environment . '"',
            'channel="auth"',
            'locale="' . $locale . '"'
        ];
        
        $query .= '{' . implode(',', $labels) . '}';
        $query .= ' | unpack_json';
        
        $filters = [];
        
        if ($employeeId) {
            $filters[] = 'employeeId:="' . $employeeId . '"';
        }
        
        if ($event !== null && $event !== '') {
            app()->setLocale($locale);
            
            $eventMap = [
                '1' => trans('audit.login'),
                '2' => trans('audit.logout'),
                '3' => trans('audit.login_failed'),
                '4' => trans('audit.session_expired'),
            ];
            
            $eventValue = $eventMap[$event] ?? $event;
            $eventValue = addslashes($eventValue);
            $filters[] = 'event:="' . $eventValue . '"';
        }
        
        if (!empty($filters)) {
            $query .= ' | filter ' . implode(' and ', $filters);
        }
        
        $query .= ' | sort by (_time) desc';
        $query .= ' | limit ' . $limit;
        
        return $query;
    }

    /**
     * Build LogsQL query for navigation access logs
     *
     * @param string $tenantUuid
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param string|null $employeeId
     * @param int|null $accessType
     * @param string $locale
     * @param int $limit
     * @return string
     */
    protected function buildNavigationAccessQuery(string $tenantUuid, ?string $fromDate, ?string $toDate, ?string $employeeId, ?int $accessType, string $locale = 'en', int $limit = 10000, ?int $companyId = null): string
    {
        $query = '';
        if ($fromDate && $toDate) {
            $fromCarbon = \Carbon\Carbon::parse($fromDate);
            $toCarbon = \Carbon\Carbon::parse($toDate);
            
            $from = \Carbon\Carbon::create($fromCarbon->year, $fromCarbon->month, $fromCarbon->day, 0, 0, 0, 'UTC')->toIso8601String();
            $to = \Carbon\Carbon::create($toCarbon->year, $toCarbon->month, $toCarbon->day, 23, 59, 59, 'UTC')->toIso8601String();
            $query = '_time:[' . $from . ',' . $to . '] ';
        }
        
        $labels = [
            'tenant="' . $tenantUuid . '"',
            'app="erp"',
            'env="' . $this->environment . '"',
            'channel="navigation"',
            'locale="' . $locale . '"'
        ];
        
        $query .= '{' . implode(',', $labels) . '}';
        $query .= ' | unpack_json';
        
        $filters = [];
        
        if ($employeeId) {
            $filters[] = 'employeeId:="' . $employeeId . '"';
        }
        
        if ($companyId) {
            // Filter for companyID = companyId OR null (missing field or empty string)
            $filters[] = '(companyID:="' . $companyId . '" or not companyID:* or companyID:="")';
        }
        
        if ($accessType !== null && $accessType !== '') {
            app()->setLocale($locale);
            
            $accessTypeMap = [
                '1' => trans('audit.read'),
                '2' => trans('audit.create'),
                '3' => trans('audit.edit'),
                '4' => trans('audit.delete'),
            ];
            
            $accessTypeValue = $accessTypeMap[$accessType] ?? trans('audit.read');
            $accessTypeValue = addslashes($accessTypeValue);
            $filters[] = 'accessType:="' . $accessTypeValue . '"';
        }
        
        if (!empty($filters)) {
            $query .= ' | filter ' . implode(' and ', $filters);
        }
        
        $query .= ' | sort by (_time) desc';
        $query .= ' | limit ' . $limit;
        
        return $query;
    }

    /**
     * Parse NDJSON response from VictoriaLogs
     *
     * @param string $body
     * @return array
     */
    protected function parseNDJSONResponse(string $body): array
    {
        $logs = [];
        $lines = explode("\n", trim($body));

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $data = json_decode($line, true);
            if ($data && json_last_error() === JSON_ERROR_NONE) {
                $logData = [];
                foreach ($data as $key => $value) {
                    if (strpos($key, '_') !== 0) {
                        $logData[$key] = $value;
                    } else if ($key === '_time') {
                        $logData['date_time'] = $value;
                    }
                }
                
                if (!empty($logData)) {
                    $logs[] = $logData;
                }
            }
        }

        return $logs;
    }

    /**
     * Get table name for module
     *
     * @param string $module
     * @return string|null
     */
    public function getAuditTables(string $module): ?string
    {
        return null;
    }

    /**
     * Get all audit table names
     *
     * @return array
     */
    public function getAllAuditTables(): array
    {
        return [
            'audit',
            'auth',
            'navigation',
            'third_party_api',
        ];
    }

    /**
     * Filter logs by search term (case-insensitive search across all fields)
     *
     * @param array $logs Array of log entries
     * @param string $searchTerm Search term
     * @return array Filtered logs with reindexed keys
     */
    protected function filterLogsBySearch(array $logs, string $searchTerm): array
    {
        $searchTerm = strtolower(trim($searchTerm));
        
        if (empty($searchTerm)) {
            return $logs;
        }
        
        $filtered = array_filter($logs, function($log) use ($searchTerm) {
            $searchableString = strtolower(json_encode($log));
            
            return strpos($searchableString, $searchTerm) !== false;
        });
        
        return array_values($filtered);
    }
}

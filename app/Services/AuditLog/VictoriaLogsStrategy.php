<?php

namespace App\Services\AuditLog;

use App\Contracts\LogStorageStrategyInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * VictoriaLogs Storage Strategy
 *
 * Implements log storage using VictoriaLogs HTTP API
 * Part of Victoria Logs Migration
 */
class VictoriaLogsStrategy implements LogStorageStrategyInterface
{
    protected $httpClient;
    protected $baseUrl;
    protected $pushUrl;
    protected $queryUrl;
    protected $username;
    protected $password;
    protected $environment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = config('victorialogs');

        $this->baseUrl = rtrim(env('VICTORIA_LOG_URL', $config['api_url'] ?? ''), '/');
        $this->pushUrl = $this->baseUrl . ($config['endpoints']['push'] ?? '/insert/jsonline');
        $this->queryUrl = $this->baseUrl . ($config['endpoints']['query'] ?? '/select/logsql/query');
        $this->username = env('VICTORIA_LOG_USERNAME', $config['auth']['username'] ?? '');
        $this->password = env('VICTORIA_LOG_PASSWORD', $config['auth']['password'] ?? '');
        $this->environment = env('VICTORIALOGS_ENV', $config['environment'] ?? env('APP_ENV', 'production'));

        $timeout = $config['timeout'] ?? 30;

        $this->httpClient = new Client([
            'timeout' => $timeout,
            'connect_timeout' => 10,
            'auth' => [$this->username, $this->password],
        ]);
    }

    /**
     * Store a log entry in VictoriaLogs
     * 
     * @param array $logData
     * @return void
     * @throws \Exception
     */
    public function store(array $logData): void
    {
        try {
            // Extract timestamp from event date_time
            $timestampNanoseconds = $this->extractTimestamp($logData);
            
            // Build stream labels
            $streamLabels = $this->buildStreamLabels($logData);
            
            // Format log data as JSON string for the value
            $logValue = json_encode($logData);
            
            $lokiPayload = [
                'streams' => [
                    [
                        'stream' => $streamLabels,
                        'values' => [
                            [$timestampNanoseconds, $logValue]
                        ]
                    ]
                ]
            ];

            // Send HTTP POST request to VictoriaLogs
            $response = $this->httpClient->post($this->pushUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $lokiPayload,
            ]);
            
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 200 || $statusCode === 204) {
               
            }
            
        } catch (RequestException $e) {
            throw new \Exception('Failed to send log to VictoriaLogs: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new \Exception('Unexpected error sending log to VictoriaLogs: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Query logs from VictoriaLogs using LogsQL
     * 
     * @param array $params Query parameters
     * @return array Log entries
     * @throws \Exception
     */
    public function query(array $params): array
    {
        try {
            // Build LogsQL query
            $logsQLQuery = $this->buildLogsQLQuery($params);
            
            // Execute query
            $response = $this->httpClient->get($this->queryUrl, [
                'query' => [
                    'query' => $logsQLQuery,
                    'limit' => $params['limit'] ?? 100,
                ],
            ]);
            
            // Parse NDJSON response
            $body = $response->getBody()->getContents();
            $logs = $this->parseNDJSONResponse($body);
            
            return $logs;
            
        } catch (RequestException $e) {
            Log::error('Failed to query VictoriaLogs', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to query logs: ' . $e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error querying VictoriaLogs', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unexpected error querying logs: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get table name for module (placeholder implementation)
     * 
     * @param string $module
     * @return string|null
     */
    public function getAuditTables(string $module): ?string
    {
        // Map module names to audit table names
        $tableMap = [
            'customermaster' => 'customer_audit',
            'suppliermaster' => 'supplier_audit',
            'chartofaccounts' => 'chartofaccount_audit',
            // Add more mappings as needed
        ];

        return $tableMap[strtolower($module)] ?? null;
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
            'auth_audit',
            'navigation_access',
            'third_party_api',
        ];
    }

    /**
     * Extract timestamp from log data
     * 
     * @param array $logData
     * @return string Timestamp in nanoseconds
     */
    protected function extractTimestamp(array $logData): string
    {
        $eventDateTime = $logData['date_time'] ?? null;
        
        if ($eventDateTime) {
            try {
                $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $eventDateTime, 'UTC');
                return (string) ($dateTime->timestamp * 1000000000);
            } catch (\Exception $e) {
                Log::warning('Failed to parse event date_time, using current time', [
                    'date_time' => $eventDateTime,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $timestamp = microtime(true);
        return (string) ((int) ($timestamp * 1000000000));
    }

    /**
     * Build stream labels for VictoriaLogs
     * 
     * @param array $logData
     * @return array
     */
    protected function buildStreamLabels(array $logData): array
    {
        $channel = $logData['channel'] ?? 'unknown';
        
        $action = 'UNKNOWN';
        $entity = 'unknown';
        
        switch ($channel) {
            case 'audit':
                $action = strtoupper($logData['crudType'] ?? 'UNKNOWN');
                $entity = $logData['table'] ?? 'unknown';
                break;
            case 'auth':
                $event = $logData['event'] ?? '';
                $action = strtoupper(str_replace('_', '', $event));
                $entity = 'users';
                break;
            case 'navigation':
                $accessType = $logData['access_type'] ?? '1';
                $accessMap = ['1' => 'READ', '2' => 'CREATE', '3' => 'EDIT'];
                $action = $accessMap[$accessType] ?? 'READ';
                $entity = 'navigation_menu';
                break;
            case 'third_party_api':
                $action = strtoupper($logData['method'] ?? 'GET');
                $entity = 'api_call';
                break;
        }
        
        return [
            'channel' => $channel,
            'tenant' => $logData['tenant_uuid'] ?? 'unknown',
            'app' => 'erp',
            'env' => $this->environment,
            'action' => $action,
            'entity' => $entity,
            'locale' => $logData['locale'] ?? 'en',
        ];
    }

    /**
     * Build LogsQL query from parameters
     * 
     * @param array $params
     * @return string
     */
    protected function buildLogsQLQuery(array $params): string
    {
        // Basic label selectors
        $selectors = [];
        $selectors[] = 'app="erp"';
        
        if (!empty($params['tenant'])) {
            $selectors[] = 'tenant="' . $params['tenant'] . '"';
        }
        
        if (!empty($params['channel'])) {
            $selectors[] = 'channel="' . $params['channel'] . '"';
        }
        
        if (!empty($params['entity'])) {
            $selectors[] = 'entity="' . $params['entity'] . '"';
        }
        
        if (!empty($params['action'])) {
            $selectors[] = 'action="' . $params['action'] . '"';
        }
        
        // Build query
        $query = '{' . implode(', ', $selectors) . '}';
        
        // Add filters
        $filters = [];
        
        if (!empty($params['transaction_id'])) {
            $filters[] = 'transaction_id = "' . $params['transaction_id'] . '"';
        }
        
        if (!empty($params['fromDate']) && !empty($params['toDate'])) {
            $filters[] = 'date_time >= "' . $params['fromDate'] . '" AND date_time <= "' . $params['toDate'] . '"';
        }
        
        if (!empty($params['employeeId'])) {
            $filters[] = 'employeeId = "' . $params['employeeId'] . '"';
        }
        
        if (!empty($filters)) {
            $query .= ' | WHERE ' . implode(' AND ', $filters);
        }
        
        // Add ordering
        $query .= ' | ORDER BY date_time DESC';
        
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
                $logs[] = $data;
            }
        }
        
        return $logs;
    }
}

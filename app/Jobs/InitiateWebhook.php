<?php

namespace App\Jobs;

use App\Jobs\AuditLog\ThirdPartyApiSummaryLogJob;
use App\helper\CommonJobService;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InitiateWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 500;
    public $db;
    public $apiExternalKey;
    public $apiExternalUrl;
    public $webhookEndpoint;
    public $webhookPayload;
    public $externalReference;
    public $tenantUuid;
    public $companyId;
    public $logId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $apiExternalKey, $apiExternalUrl, $webhookEndpoint, $webhookPayload, $externalReference = null, $tenantUuid = null, $companyId = null, $logId = null)
    {
        if (env('QUEUE_DRIVER_CHANGE', 'database') == 'database') {
            if (env('IS_MULTI_TENANCY', false)) {
                self::onConnection('database_main');
            } else {
                self::onConnection('database');
            }
        } else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE', 'database'));
        }

        $this->db = $db;
        $this->apiExternalKey = $apiExternalKey;
        $this->apiExternalUrl = $apiExternalUrl;
        $this->webhookEndpoint = $webhookEndpoint;
        $this->webhookPayload = $webhookPayload;
        $this->externalReference = $externalReference;
        $this->tenantUuid = $tenantUuid;
        $this->companyId = $companyId;
        $this->logId = $logId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->db);
        
        // Initialize webhook execution variables
        $startTime = microtime(true);
        $webhookUrl = null;
        $responsePayload = null;
        $statusCode = null;
        $success = false;
        $isFailed = 0;
        $executionTime = 0;
        $errorMessage = null;
        
        // Get webhook configuration from database
        $webhookConfig = $this->getWebhookConfiguration();
        
        // Get webhook URL from configuration
        $webhookUrl = $this->getWebhookUrl($webhookConfig);
        
        // Determine if webhook should be executed
        $webhookConditionsMet = ($webhookUrl != null);
        
        if ($webhookConditionsMet) {
            
            try {
                $client = new Client();
                $headers = $this->buildAuthorizationHeaders($webhookConfig);
                
                $res = $client->request('POST', $webhookUrl, [
                    'headers' => $headers,
                    'json' => $this->webhookPayload
                ]);
                
                $statusCode = $res->getStatusCode();
                $responsePayload = $res->getBody()->getContents();
                $success = true;
                
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $responsePayload = $response->getBody()->getContents();
                $isFailed = 1;
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $statusCode = $response->getStatusCode();
                    $responsePayload = $response->getBody()->getContents();
                } else {
                    $statusCode = 0;
                    $responsePayload = json_encode([
                        'error' => true,
                        'message' => 'Connection failed: ' . $e->getMessage()
                    ]);
                }
                $isFailed = 1;
            } catch (\Exception $e) {
                $statusCode = 0;
                $responsePayload = json_encode([
                    'error' => true,
                    'message' => 'Unexpected error: ' . $e->getMessage()
                ]);
                $isFailed = 1;
            }
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
        } else {
            // Webhook conditions not met
            $statusCode = null;
            $responsePayload = '';
            $success = false;
            $isFailed = 1;
            $errorMessage = 'Webhook not executed - missing webhook configuration or webhook_base_url not found';
        }
        
        // Single combined webhook log
        if ($this->externalReference) {
            // Redact sensitive headers for logging
            $logHeaders = ['content-type' => 'application/json'];
            if ($webhookConditionsMet && $webhookConfig) {
                $actualHeaders = $this->buildAuthorizationHeaders($webhookConfig);
                foreach ($actualHeaders as $key => $value) {
                    if (in_array(strtolower($key), ['authorization', 'x-signature', 'x-api-key'])) {
                        $logHeaders[$key] = '[REDACTED]';
                    } else {
                        $logHeaders[$key] = $value;
                    }
                }
            }
            
            $requestPayload = [
                'webhook' => true,  // Flag to indicate this is a webhook log entry
                'headers' => $logHeaders,
                'payload' => $this->webhookPayload
            ];
            
            // Add URL only if webhook was attempted
            if ($webhookConditionsMet && $webhookUrl) {
                $requestPayload['url'] = $webhookUrl;
            }
            
            $responseData = [
                'status_code' => $statusCode,
                'body' => $responsePayload,
                'success' => $success
            ];
            
            // Add message for failed conditions
            if (!$webhookConditionsMet) {
                $responseData['message'] = 'Webhook conditions not met';
            }
            
            ThirdPartyApiSummaryLogJob::dispatch(
                $this->db,
                null,
                $this->tenantUuid ?: env('TENANT_UUID', 'local'),
                $this->webhookEndpoint,
                'POST',
                $requestPayload,
                $responseData,
                $statusCode,
                'system',
                $executionTime,
                $this->externalReference,
                1,
                $isFailed,
                $errorMessage,
                $this->logId
            );
        }
    }

    /**
     * Get webhook configuration from database
     *
     * @return array|null
     */
    private function getWebhookConfiguration()
    {
        try {
            $config = DB::table('third_party_apis as api')
                ->join('third_party_integration_key_apis as key_api', 'api.slug', '=', 'key_api.api_slug')
                ->join('third_party_integration_keys as integration_key', 'key_api.integration_key_id', '=', 'integration_key.id')
                ->where('api.webhook_endpoint', $this->webhookEndpoint)
                ->where('key_api.is_active', 1)
                ->where('integration_key.status', 'Active')
                ->where('integration_key.company_id', $this->companyId)
                ->select([
                    'key_api.webhook_security_method',
                    'key_api.webhook_security',
                    'key_api.webhook_base_url',
                    'integration_key.api_key',
                    'integration_key.api_external_key'
                ])
                ->first();

            return $config ? (array) $config : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build authorization headers based on webhook security method
     *
     * @param array $webhookConfig
     * @return array
     */
    private function buildAuthorizationHeaders($webhookConfig)
    {
        $headers = [
            'content-type' => 'application/json'
        ];

        $securityMethod = $webhookConfig['webhook_security_method'] ?? 'Legacy';
        
        switch ($securityMethod) {
            case 'Legacy':
                $securityData = json_decode($webhookConfig['webhook_security'] ?? '{}', true);
                $token = $securityData['token'] ?? '';
                if ($token) {
                    $headers['Authorization'] = 'ERP ' . $token;
                }
                break;

            case 'HMAC Signature':
                $apiKey = $webhookConfig['api_key'] ?? '';
                if ($apiKey) {
                    $payload = json_encode($this->webhookPayload);
                    $signature = hash_hmac('sha256', $payload, $apiKey);
                    $headers['X-Signature'] = 'sha256=' . $signature;
                }
                break;

            case 'Custom Headers':
                $customHeaders = json_decode($webhookConfig['webhook_security'] ?? '{}', true);
                if (is_array($customHeaders)) {
                    foreach ($customHeaders as $key => $value) {
                        $headers[$key] = $value;
                    }
                }
                break;

            case 'Bearer':
                $securityData = json_decode($webhookConfig['webhook_security'] ?? '{}', true);
                $token = $securityData['token'] ?? '';
                if ($token) {
                    $headers['Authorization'] = 'Bearer ' . $token;
                }
                break;

            default:
                // Fallback to legacy method
                $apiKey = $webhookConfig['api_external_key'] ?? $this->apiExternalKey;
                if ($apiKey) {
                    $headers['Authorization'] = 'ERP ' . $apiKey;
                }
                break;
        }

        return $headers;
    }

    /**
     * Get webhook URL from configuration
     *
     * @param array|null $webhookConfig
     * @return string|null
     */
    private function getWebhookUrl($webhookConfig)
    {
        if (!$webhookConfig) {
            return null;
        }

        $baseUrl = $webhookConfig['webhook_base_url'] ?? $this->apiExternalUrl;
        
        if (!$baseUrl) {
            return null;
        }

        return $baseUrl . $this->webhookEndpoint;
    }
} 
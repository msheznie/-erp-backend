<?php

namespace App\Jobs\AuditLog;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\ThirdPartyApiLog;
use Illuminate\Support\Facades\Log;
use Exception;

class ThirdPartyApiSummaryLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $db;
    protected $thirdPartyIntegrationKeyId;
    protected $tenant_uuid;
    protected $endpoint;
    protected $method;
    protected $requestPayload;
    protected $responsePayload;
    protected $statusCode;
    protected $apiKey;
    protected $user;
    protected $executionTime;
    protected $externalReference;
    protected $isWebhook;
    protected $errorMessage;
    protected $isFailed;
    protected $logId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $dataBase, 
        $thirdPartyIntegrationKeyId = null, 
        $tenant_uuid, 
        $endpoint, 
        $method, 
        $requestPayload = [], 
        $responsePayload = [], 
        $statusCode = null, 
        $user = null,
        $executionTime = null,
        $externalReference = null,
        $isWebhook = 0,
        $isFailed = 0,
        $errorMessage = null,
        $logId = null
    ) {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                $this->onConnection('database_main');
            }else{
                $this->onConnection('database');
            }
        }else{
            $this->onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        
        $this->db = $dataBase;
        $this->thirdPartyIntegrationKeyId = $thirdPartyIntegrationKeyId;
        $this->tenant_uuid = $tenant_uuid;
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->requestPayload = $requestPayload;
        $this->responsePayload = $responsePayload;
        $this->statusCode = $statusCode;
        $this->user = $user;
        $this->executionTime = $executionTime;
        $this->externalReference = $externalReference;
        $this->isWebhook = $isWebhook;
        $this->errorMessage = $errorMessage;
        $this->isFailed = $isFailed;
        $this->logId = $logId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $db = $this->db;
            CommonJobService::db_switch($db);

            // Generate external reference if not provided
            $externalReference = $this->externalReference ?: 
                ThirdPartyApiLog::generateExternalReference();


            if ($this->isWebhook == 0) {
                // Store in database
                ThirdPartyApiLog::create([
                    'external_reference' => $externalReference,
                    'third_party_integration_key_id' => $this->thirdPartyIntegrationKeyId,
                    'tenant_uuid' => $this->tenant_uuid,
                    'endpoint' => $this->endpoint,
                    'method' => $this->method,
                    'log_id' => $this->logId,
                    'execution_time_ms' => $this->executionTime
                ]);
            }


            // Also continue with the existing file logging
            $this->logToFile($externalReference, $this->isWebhook);

        } catch (Exception $e) {
            // Log error but don't break the application
            Log::error('Error in ThirdPartyApiSummaryLogJob: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'endpoint' => $this->endpoint,
                'method' => $this->method
            ]);
        }
    }

    /**
     * Log to file (existing functionality)
     */
    private function logToFile($externalReference, $isWebhook)
    {
        $sanitizedRequestPayload = $this->sanitizePayload($this->requestPayload);

        $logData = [
            'external_reference' => $externalReference,
            'request_payload' => $sanitizedRequestPayload,
            'response_payload' => $this->responsePayload,
            'status_code' => $this->statusCode,
            'execution_time_ms' => $this->executionTime
        ];

        Log::useFiles(storage_path() . '/logs/audit.log');

        Log::info('third_party_api_log:', [
            'channel' => 'third_party_api',
            'external_reference' => $externalReference,
            'error_message' => $this->errorMessage,
            'is_failed' => (string) $this->isFailed,
            'is_webhook' => (string) $isWebhook,
            'endpoint' => $this->endpoint,
            'log_id' => $this->logId,
            'tenant_uuid' => $this->tenant_uuid,
            'method' => $this->method,
            'user_name' => $this->user,
            'date_time' => date('Y-m-d H:i:s'),
            'data' => json_encode($logData),
        ]);
    }

    /**
     * Sanitize payload by removing sensitive information
     * 
     * @param array $payload
     * @return array
     */
    private function sanitizePayload($payload)
    {
        if (!is_array($payload)) {
            return $payload;
        }

        $sensitiveKeys = [
            'db',
            'tenant_uuid',
            'company_id',
            'api_external_key',
            'api_external_url',
            'third_party_system_id'
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($payload[$key])) {
                unset($payload[$key]);
            }
        }

        // Also check nested arrays
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sanitizePayload($value);
            }
        }

        return $payload;
    }
} 
<?php

namespace App\Jobs\AuditLog;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class SendToVictoriaLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logData;

    /**
     * Create a new job instance.
     *
     * @param array $logData
     * @return void
     */
    public function __construct($logData)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        
        $this->logData = $logData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $lokiUrl = env('VICTORIA_LOG_URL', 'https://php-auditlogs-rnd.gears-int.com/insert/loki/api/v1/push');
            $lokiUsername = env('VICTORIA_LOG_USERNAME', 'vauth');
            $lokiPassword = env('VICTORIA_LOG_PASSWORD', 'wMDyerXqWG-rmYdIpFrKVG_d286MQwqsdfgh76ozCnU6MQ8');
            
            // Use the actual event date_time from log data for accurate timestamp
            // This ensures date range filtering works correctly
            $eventDateTime = $this->logData['date_time'] ?? null;
            if ($eventDateTime) {
                try {
                    // Parse the date_time string (format: Y-m-d H:i:s)
                    $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $eventDateTime, 'UTC');
                    // Convert to Unix timestamp in nanoseconds
                    $timestampNanoseconds = (string) ($dateTime->timestamp * 1000000000);
                } catch (\Exception $e) {
                    // Fallback to current time if date parsing fails
                    $timestamp = microtime(true);
                    $timestampNanoseconds = (string) ((int) ($timestamp * 1000000000));
                }
            } else {
                // Use current time if date_time is not provided
                $timestamp = microtime(true);
                $timestampNanoseconds = (string) ((int) ($timestamp * 1000000000));
            }
            
            // Format log data as JSON string for the value
            $logValue = json_encode($this->logData);
            
            // Build stream labels
            // Use VICTORIALOGS_ENV if set, otherwise APP_ENV (must match query environment)
            $env = env("VICTORIALOGS_ENV", env("APP_ENV", "production"));
            $streamLabels = [
                'channel' => $this->logData['channel'] ?? 'unknown',
                'tenant' => $this->logData['tenant_uuid'] ?? 'unknown',
                'app' => 'erp',
                'env' => $env,
                'action' => strtoupper($this->logData['crudType'] ?? 'UNKNOWN'),
                'entity' => $this->logData['table'] ?? 'unknown',
                'locale' => $this->logData['locale'] ?? 'en',
            ];
            
            // Build Loki payload
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
            
            // Send HTTP request to Loki
            $client = new Client([
                'timeout' => 10,
                'auth' => [$lokiUsername, $lokiPassword],
            ]);
            
            $response = $client->post($lokiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $lokiPayload,
            ]);
            
            $statusCode = $response->getStatusCode();
            
            // Log success to file
            Log::useFiles(storage_path() . '/logs/victoria-log-success.log');
            if ($statusCode === 200 || $statusCode === 204) {
                Log::info('Successfully sent audit log to Victoria Log', [
                    'transaction_id' => $this->logData['transaction_id'] ?? null,
                    'table' => $this->logData['table'] ?? null,
                    'crudType' => $this->logData['crudType'] ?? null,
                    'status_code' => $statusCode,
                    'log_uuid' => $this->logData['log_uuid'] ?? null,
                ]);
            } else {
                Log::warning('Victoria Log returned unexpected status code: ' . $statusCode);
            }
            
        } catch (RequestException $e) {
            // Log error but don't fail the job
            Log::error('Failed to send audit log to Victoria Log: ' . $e->getMessage(), [
                'log_data' => $this->logData,
                'exception' => $e->getTraceAsString()
            ]);
        } catch (\Exception $e) {
            // Log any other errors
            Log::error('Unexpected error sending audit log to Victoria Log: ' . $e->getMessage(), [
                'log_data' => $this->logData,
                'exception' => $e->getTraceAsString()
            ]);
        }
    }
}


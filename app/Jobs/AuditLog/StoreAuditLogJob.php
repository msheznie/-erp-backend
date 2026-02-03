<?php

namespace App\Jobs\AuditLog;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\FailedAuditLog;
use App\Contracts\LogStorageStrategyInterface;
use App\Services\AuditLog\VictoriaLogsStrategy;

/**
 * Store Audit Log Job
 * 
 * Generic job for storing audit logs using Strategy Pattern
 * Part of Victoria Logs Migration
 * 
 * Features:
 * - Strategy-based (VictoriaLogs, File, or custom strategies)
 * - Automatic retry (max 3 attempts via Laravel queue)
 * - Failed logs stored in database for later retry
 * - Strategy-agnostic implementation
 * 
 * Note: Strategy is instantiated in handle() to avoid serialization issues
 */
class StoreAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * The log data to store
     *
     * @var array
     */
    protected $logData;

    /**
     * The log storage strategy class name
     *
     * @var string
     */
    protected $strategyClass;

    /**
     * Create a new job instance.
     *
     * @param array $logData Complete log data array
     * @param string|null $strategyClass Optional strategy class name (defaults to VictoriaLogs)
     * @return void
     */
    public function __construct($logData, $strategyClass = null)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                $this->onConnection('database_main');
            }else{
                $this->onConnection('database');
            }
        }else{
            $this->onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        
        $this->logData = $logData;
        $this->strategyClass = $strategyClass ?? VictoriaLogsStrategy::class;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $logStrategy = new $this->strategyClass;
            
            $logStrategy->store($this->logData);
        } catch (\Exception $e) {
            Log::error('Failed to store audit log', [
                'channel' => $this->logData['channel'] ?? 'unknown',
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
            ]);
            
            if ($this->attempts() >= $this->tries) {
                $this->storeFailedLogIfEnabled($e->getMessage());
            }
            
            throw $e;
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Log::error('StoreAuditLogJob permanently failed', [
            'channel' => $this->logData['channel'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'log_data' => $this->logData,
        ]);
        
        $this->storeFailedLogIfEnabled($exception->getMessage());
    }

    /**
     * Store failed log in database for later retry (if enabled in config)
     *
     * @param string $errorMessage
     * @return void
     */
    protected function storeFailedLogIfEnabled($errorMessage)
    {
        $storeFailedLogs = config('victorialogs.store_failed_logs');
        
        if (!$storeFailedLogs) {
            return;
        }
        
        $this->storeFailedLog($errorMessage);
    }

    /**
     * Store failed log in database for later retry
     *
     * @param string $errorMessage
     * @return void
     */
    protected function storeFailedLog($errorMessage)
    {
        try {
            FailedAuditLog::create([
                'log_data' => $this->logData,
                'error_message' => $errorMessage,
                'retry_count' => 0,
                'last_retry_at' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store audit log failure in database', [
                'channel' => $this->logData['channel'] ?? 'unknown',
                'original_error' => $errorMessage,
                'database_error' => $e->getMessage(),
                'log_data' => $this->logData,
            ]);
        }
    }
}

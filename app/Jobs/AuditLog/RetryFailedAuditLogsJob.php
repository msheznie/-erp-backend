<?php

namespace App\Jobs\AuditLog;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\FailedAuditLog;
use App\Jobs\AuditLog\StoreAuditLogJob;
/**
 * Retry Failed Audit Logs Job
 * 
 * Scheduled job that processes failed audit logs from database
 * and attempts to resend them to VictoriaLogs
 * 
 * Part of Victoria Logs Migration
 * 
 * Schedule: Every 5 minutes
 * Batch size: 100 logs per run
 * Max retries per log: 5
 * Retention: 7 days
 */
class RetryFailedAuditLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * Batch size for processing failed logs
     *
     * @var int
     */
    protected $batchSize = 100;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Configure queue connection
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                $this->onConnection('database_main');
            }else{
                $this->onConnection('database');
            }
        }else{
            $this->onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting RetryFailedAuditLogsJob');
        
        try {
            // Get retryable failed logs
            $failedLogs = FailedAuditLog::retryable()
                ->limit($this->batchSize)
                ->get();
            
            if ($failedLogs->isEmpty()) {
                return;
            }
            
            $totalLogs = $failedLogs->count();
            $successCount = 0;
            $failureCount = 0;
            $exceededLimitCount = 0;
            
            foreach ($failedLogs as $failedLog) {
                try {
                    if ($failedLog->hasExceededRetryLimit()) {
                        $exceededLimitCount++;
                        continue;
                    }
                    
                    if ($failedLog->hasExpired()) {
                        $exceededLimitCount++;
                        continue;
                    }
                    
                    StoreAuditLogJob::dispatch($failedLog->log_data);
                    
                    $failedLog->delete();
                    $successCount++;
                } catch (\Exception $e) {
                    $failedLog->error_message = $e->getMessage();
                    $failedLog->incrementRetry();
                    $failureCount++;
                    
                    Log::error('Failed to retry audit log', [
                        'id' => $failedLog->id,
                        'channel' => $failedLog->log_data['channel'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'retry_count' => $failedLog->retry_count,
                    ]);
                }
            }
            
            $this->cleanupPermanentlyFailedLogs();
        } catch (\Exception $e) {
            Log::error('RetryFailedAuditLogsJob encountered error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Clean up permanently failed logs (exceeded retry limit or expired)
     * 
     * Note: These are kept for historical/debugging purposes
     * This method just logs statistics, doesn't delete
     *
     * @return void
     */
    protected function cleanupPermanentlyFailedLogs()
    {
        try {
            $permanentlyFailed = FailedAuditLog::permanentlyFailed()->count();
            
            if ($permanentlyFailed > 0) {
                Log::warning('Permanently failed audit logs exist', [
                    'count' => $permanentlyFailed,
                    'message' => 'These logs have either exceeded retry limit (5) or are older than 7 days',
                ]);
                
                // Get statistics by channel
                $stats = FailedAuditLog::permanentlyFailed()
                    ->get()
                    ->groupBy(function ($item) {
                        return $item->log_data['channel'] ?? 'unknown';
                    })
                    ->map(function ($group) {
                        return $group->count();
                    })
                    ->toArray();
                
                Log::info('Permanently failed logs by channel', $stats);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get permanently failed log statistics', [
                'error' => $e->getMessage(),
            ]);
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
        Log::critical('RetryFailedAuditLogsJob failed completely', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}

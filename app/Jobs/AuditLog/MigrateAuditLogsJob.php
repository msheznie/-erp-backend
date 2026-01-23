<?php

namespace App\Jobs\AuditLog;

use App\Services\LokiService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class MigrateAuditLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $table;
    protected $env;
    protected $diff;
    protected $jobId;
    protected $batchId;
    protected $tenantUuid;
    /**
     * Create a new job instance.
     *
     * @param string $table Single table to migrate
     * @param string $env Environment
     * @param int $diff Number of days to look back
     * @param string $jobId Unique job identifier for tracking
     * @param string $batchId Batch identifier for grouping multiple table jobs
     * @return void
     */
    public function __construct($table, $env, $diff, $jobId, $batchId, $tenantUuid)
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
        
        $this->table = $table;
        $this->env = $env;
        $this->diff = $diff;
        $this->jobId = $jobId;
        $this->batchId = $batchId;
        $this->tenantUuid = $tenantUuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lokiService = new LokiService();
        
        $migratedCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Batch configuration
        $batchSize = env('AUDIT_LOG_MIGRATION_BATCH_SIZE', 50); // Process 50 logs per batch
        $batchDelayMs = env('AUDIT_LOG_MIGRATION_BATCH_DELAY_MS', 500); // 500ms delay between batches

        try {
            $params = 'query?query=rate({env="'.$this->env.'"} |= `\"table\":\"'.$this->table.'\"` |= `\"tenant_uuid\":\"'.$this->tenantUuid.'\"` | json ['.$this->diff.'d])';
            $data = $lokiService->getAuditLogsForMigration($params);
            
            $totalLogs = count($data);

            // Split data into batches
            $batches = array_chunk($data, $batchSize);
            $totalBatches = count($batches);
            

            foreach ($batches as $batchIndex => $batch) {
                $batchNumber = $batchIndex + 1;
                
                foreach ($batch as $key => $value) {
                    try {
                        if (isset($value)) {
                            $log = $value;
                            
                            if (isset($log['table'])) {
                                // Old audit log format - re-log using Log facade
                                \Log::channel('audit')->info('data:', [
                                    'channel' => 'audit',
                                    'transaction_id' => $log['transaction_id'] ?? '',
                                    'table' => $log['table'] ?? '',
                                    'user_name' => $log['user_name'] ?? 'Unknown',
                                    'tenant_uuid' => $log['tenant_uuid'] ?? 'local',
                                    'crudType' => $log['crudType'] ?? '',
                                    'narration' => $log['narration'] ?? '',
                                    'date_time' => $log['date_time'] ?? date('Y-m-d H:i:s'),
                                    'parent_id' => $log['parent_id'] ?? '',
                                    'parent_table' => $log['parent_table'] ?? null,
                                    'module' => 'Finance',
                                    'session_id' => $log['session_id'] ?? '',
                                    'data' => $log['data'] ?? '[]',
                                ]);
                                
                                $migratedCount++;
                            }
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = [
                            'key' => $key,
                            'error' => $e->getMessage()
                        ];
                    }
                }
                
                // Sleep between batches to give Fluent Bit time to process
                if ($batchNumber < $totalBatches) {
                    usleep($batchDelayMs * 1000); // Convert milliseconds to microseconds
                }
            }
        } catch (\Exception $e) {
            Log::channel('audit')->error("MigrateAuditLogsJob: Failed for table: {$this->table}, tenant: {$this->tenantUuid}", [
                'job_id' => $this->jobId,
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'migrated_count' => $migratedCount,
                'error_count' => $errorCount
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        throw $exception;
    }
}


<?php

namespace App\Jobs;

use App\Models\User;
use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UpdateUsersLoginTypeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenantDb;
    protected $loginType;
    protected $tenantId;

    /**
     * Create a new job instance.
     *
     * @param string $tenantDb
     * @param string $loginType
     * @param int|null $tenantId
     * @return void
     */
    public function __construct($tenantDb, $loginType, $tenantId = null)
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
        
        $this->tenantDb = $tenantDb;
        $this->loginType = $loginType;
        $this->tenantId = $tenantId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Switch to tenant database
            CommonJobService::db_switch($this->tenantDb);
            
            $processedCount = 0;
            $errorCount = 0;
            $chunkSize = 50;
            
            // Use chunkById instead of chunk to avoid issues with orderBy during data modification
            // This ensures we don't miss records when they are updated during processing
            User::chunkById($chunkSize, function($users) use (&$processedCount, &$errorCount) {
                foreach ($users as $user) {
                    try {
                        // Only update if loginType is different to avoid unnecessary saves
                        if ($user->loginType !== $this->loginType) {
                            $user->loginType = $this->loginType;
                            $user->save();
                            $processedCount++;
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error("Error updating user login type - User ID: {$user->id}, Tenant DB: {$this->tenantDb} - " . $e->getMessage());
                    }
                }
            }, 'id'); // Specify the column for chunkById
            
            Log::info("Successfully updated login type for {$processedCount} users in tenant: {$this->tenantDb}" . ($errorCount > 0 ? " (Errors: {$errorCount})" : ""));
            
        } catch (\Exception $e) {
            Log::error("Error updating users login type for tenant: {$this->tenantDb} - " . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
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
        Log::error('UpdateUsersLoginTypeJob failed for tenant ' . $this->tenantDb . ': ' . $exception->getMessage());
    }
}


<?php

namespace App\Jobs;

use App\Models\User;
use App\helper\CommonJobService;
use App\Traits\AuditLogsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessExpiredTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, AuditLogsTrait;

    protected $tenantUuid;
    protected $tenantDb;

    /**
     * Create a new job instance.
     *
     * @param string $tenantUuid
     * @param string $tenantDb
     * @return void
     */
    public function __construct($tenantUuid, $tenantDb)
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
        
        $this->tenantUuid = $tenantUuid;
        $this->tenantDb = $tenantDb;
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
            
            // Get expired tokens
            $expiredTokens = DB::table('oauth_access_tokens')
                ->where('expires_at', '>', now()->subHour())
                ->where('expires_at', '<=', now())
                ->where('revoked', 0)
                ->get();
            
            $count = 0;
            foreach ($expiredTokens as $token) {
                try {
                    $user = User::find($token->user_id);
                    $employeeId = $user ? $user->employee_id : null;
                    
                    $this->log('auth', [
                        'event' => 'token_expired',
                        'sessionId' => $token->session_id,
                        'userId' => $token->user_id,
                        'employeeId' => $employeeId,
                        'authType' => 'passport',
                        'request' => (object)['db' => $this->tenantDb],
                        'tenantUuid' => $this->tenantUuid
                    ]);
                    $count++;
                } catch (\Exception $tokenException) {
                    Log::error('Error logging expired token: ' . $tokenException->getMessage());
                    Log::error('Token ID: ' . $token->id);
                }
            }
            
            Log::info("Processed {$count} expired tokens for tenant: {$this->tenantUuid}");
            
        } catch (\Exception $e) {
            Log::error('Error processing expired tokens for tenant ' . $this->tenantUuid . ': ' . $e->getMessage());
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
        Log::error('ProcessExpiredTokensJob failed for tenant ' . $this->tenantUuid . ': ' . $exception->getMessage());
    }
}


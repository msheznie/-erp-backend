<?php

namespace App\Console\Commands;

use App\Models\User;
use App\helper\CommonJobService;
use App\Traits\AuditLogsTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectExpiredTokensCommand extends Command
{
    use AuditLogsTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:detect-expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect expired tokens and fire expiration events';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            
            $tenants = CommonJobService::tenant_list();

            foreach ($tenants as $tenant) {
                $tenant_uuid = $tenant->uuid;
                $db = $tenant->database;
                CommonJobService::db_switch($db);
                
                $expiredTokens = DB::table('oauth_access_tokens')
                    // ->where('expires_at', '>', now()->subHour())
                    ->where('expires_at', '<=', now())
                    ->where('revoked', false)
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
                            'request' => ['db' => $db],
                            'tenantUuid' => $tenant_uuid
                        ]);
                        $count++;
                    } catch (\Exception $tokenException) {
                        \Log::error('Error logging expired token: ' . $tokenException->getMessage());
                        $this->error('Error logging token ' . $token->id . ': ' . $tokenException->getMessage());
                    }
                }
            }
            return 0;
        } catch (\Exception $e) {
            $this->error('Error detecting expired tokens: ' . $e->getMessage());
            return 1;
        }
    }
}


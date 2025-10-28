<?php

namespace App\Console\Commands;

use App\Models\User;
use App\helper\CommonJobService;
use App\Services\AuditLog\AuthAuditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DetectExpiredTokensCommand extends Command
{
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
                    ->where('expires_at', '>', now()->subHour())
                    ->where('expires_at', '<=', now())
                    ->where('revoked', false)
                    ->get();
    
                $count = 0;
                foreach ($expiredTokens as $token) {
                    $user = User::find($token->user_id);
                    $employeeId = $user ? $user->employee_id : null;
                    
                    AuthAuditService::logTokenExpired($token->session_id, $token->user_id, $employeeId, 'passport', $tenant_uuid);
                    $count++;
                }
            }
            return 0;
        } catch (\Exception $e) {
            $this->error('Error detecting expired tokens: ' . $e->getMessage());
            return 1;
        }
    }
}


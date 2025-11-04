<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Jobs\ProcessExpiredTokensJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
            $tenants = DB::table('tenant')
                         ->where('is_active', 1)
                         ->where('sub_domain', 'like', '%erp%')
                         ->whereNotNull('uuid')
                         ->whereNotNull('database')
                         ->get();

            foreach ($tenants as $tenant) {
                $tenant_uuid = $tenant->uuid;
                $db = $tenant->database;

                if (!is_null($tenant_uuid) && !is_null($db)) {
                    ProcessExpiredTokensJob::dispatch($tenant_uuid, $db);
                }
            }
            return 0;
        } catch (\Exception $e) {
            Log::error('Error dispatching expired token jobs: ' . $e->getMessage());
            return 1;
        }
    }
}


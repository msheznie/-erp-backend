<?php

namespace App\Console\Commands;

use App\Jobs\PurgeMfaSessionsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;

class PurgeMfaSessions extends Command
{
    protected $signature = 'mfa:purge-sessions';

    protected $description = 'Delete expired and verified MFA sessions for all tenants';

    public function handle()
    {
        try {
            $tenants = CommonJobService::tenant_list();
            if (count($tenants) == 0) {
                $this->info('No tenants found');
                return;
            }


            foreach ($tenants as $tenant) {
                if (is_null($tenant->database)) {
                    PurgeMfaSessionsJob::dispatch($tenant->database);
                }
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to dispatch MFA purge jobs: '.$e->getMessage());
            Log::error('mfa:purge-sessions dispatch failed', ['error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }
}

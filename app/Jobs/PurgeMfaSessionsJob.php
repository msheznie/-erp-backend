<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\UserMfaSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PurgeMfaSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $tenantDb
    ) {
        if (env('QUEUE_DRIVER_CHANGE', 'database') == 'database') {
            if (env('IS_MULTI_TENANCY', false)) {
                self::onConnection('database_main');
            } else {
                self::onConnection('database');
            }
        } else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE', 'database'));
        }
    }

    public function handle(): void
    {
        try {
            CommonJobService::db_switch($this->tenantDb);

            $deleted = UserMfaSession::purgeable()->delete();
        } catch (\Exception $e) {
            Log::error('PurgeMfaSessionsJob failed', [
                'tenant_db' => $this->tenantDb,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error('PurgeMfaSessionsJob permanently failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}

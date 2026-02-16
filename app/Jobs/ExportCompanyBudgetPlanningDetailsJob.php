<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Services\Budget\ExportCompanyBudgetPlanningDetailsExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ExportCompanyBudgetPlanningDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $dispatch_db;
    public $userId;

    /**
     * Create a new job instance.
     *
     * @param string $dispatch_db Database connection name for multi-tenancy (optional)
     * @param array $input Request input (budgetPlanningId, companySystemID, filters, etc.)
     * @param int $userId Employee system ID to notify when export is ready
     */
    public function __construct($dispatch_db, array $input, $userId)
    {
        if (env('QUEUE_DRIVER_CHANGE', 'database') == 'database') {
            if (env('IS_MULTI_TENANCY', false)) {
                self::onConnection('database_main');
            } else {
                self::onConnection('database');
            }
        } else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE', 'database'));
        }

        $this->data = $input;
        $this->dispatch_db = $dispatch_db;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        Log::useFiles(storage_path() . '/logs/budget_planning_export.log');
        if ($db) {
            CommonJobService::db_switch($db);
        }

        try {
            (new ExportCompanyBudgetPlanningDetailsExcel($this->data, $this->userId))->export();
        } catch (\Exception $e) {
            Log::error('Budget planning export job failed.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

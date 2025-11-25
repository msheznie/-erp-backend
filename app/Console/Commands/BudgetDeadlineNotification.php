<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BudgetDeadlineNotificationJob;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;

class BudgetDeadlineNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:budgetDeadlineNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Budget Deadline Notification Scheduler - Sends notifications for approaching budget deadlines';

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
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/budget-deadline-notification.log');

        // $tenants = CommonJobService::tenant_list();
        // if (count($tenants) == 0) {
        //     $this->info('No tenants found');
        //     return;
        // }

        // foreach ($tenants as $tenant) {
            // $tenant_database = $tenant->database;
            $tenant_database = 'gears_erp_gutech';
            BudgetDeadlineNotificationJob::dispatch($tenant_database);
        // }

        // $this->info('Budget deadline notification jobs dispatched for ' . count($tenants) . ' tenant(s)');
    }
}


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BudgetSubmissionDeadlineReachedNotificationJob;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;

class BudgetSubmissionDeadlineReachedNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:budgetSubmissionDeadlineReachedNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Budget Submission Deadline Reached Notification Scheduler - Sends notifications for budget submissions that have passed their deadline';

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
        Log::useFiles(storage_path() . '/logs/budget-submission-deadline-reached-notification.log');

        $tenants = CommonJobService::tenant_list();
        if (count($tenants) == 0) {
            $this->info('No tenants found');
            return;
        }

        foreach ($tenants as $tenant) {
            $tenant_database = $tenant->database;
            BudgetSubmissionDeadlineReachedNotificationJob::dispatch($tenant_database);
        }

        $this->info('Budget submission deadline reached notification jobs dispatched for ' . count($tenants) . ' tenant(s)');
    }
}

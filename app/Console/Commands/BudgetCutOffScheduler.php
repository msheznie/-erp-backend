<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BudgetCutOffNotificationJob;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;

class BudgetCutOffScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:budgetCutoff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Budget Cutoff Notification Scheduler';

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
        Log::useFiles(storage_path() . '/logs/budget-cutoff-po.log');     

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;


            BudgetCutOffNotificationJob::dispatch($tenant_database);
        } 
    }
}

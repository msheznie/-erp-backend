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
        Log::info('Butget cutoff po Shedular'.now());

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            Log::info("Tenant details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;

            Log::info("{$tenant_database} DB added to queue for re order pr . \t on file: " . __CLASS__ ." \tline no :".__LINE__);

            BudgetCutOffNotificationJob::dispatch($tenant_database);
        } 
    }
}

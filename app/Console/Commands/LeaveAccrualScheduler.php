<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Jobs\LeaveAccrualInitiate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LeaveAccrualScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave_accrual_schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Leave accrual scheduler';

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
        Log::useFiles( CommonJobService::get_specific_log_file('leave-accrual') );

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;


            LeaveAccrualInitiate::dispatch($tenant_database);
        }
    }
}

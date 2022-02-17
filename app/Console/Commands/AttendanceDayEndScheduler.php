<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use App\Jobs\AttendanceDayEndPullingInitiate;

class AttendanceDayEndScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull attendance in the day end';

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
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockOut') );
        
        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            Log::info("Tenant details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;

            $msg = "{$tenant_database} DB added to queue for attendance day end pulling initiate.";
            Log::info("{$msg} \t on file: " . __CLASS__ ." \tline no :".__LINE__);

            AttendanceDayEndPullingInitiate::dispatch($tenant_database, $this->signature);
        }
    }
}

<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Jobs\AbsentNotificationCrossDayTenant;
use Carbon\Carbon;

class AbsentNotificationCrossDayScheduler extends Command
{
    protected $signature = 'command:AbsentNotificationCrossDay';
    protected $description = 'Attendance employee forgot to punch-in and punch-out CrossDay scheduler';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('absent-notification') );                    
        $currentDate = Carbon::now()->timezone('Asia/Muscat');

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            $msg = "Tenant details not found ( {$currentDate} ).";
            Log::error("{$msg} \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return;
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;
            $msg = "{$tenantDb} DB added to the queue for non cross day absent notification";
            $msg .= " ( {$currentDate} ).";
            Log::error("{$msg} \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            AbsentNotificationCrossDayTenant::dispatch($tenantDb);            
        }
    }
}

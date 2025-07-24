<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\helper\NotificationService;
use Illuminate\Support\Facades\Log;
use App\Jobs\AbsentNotificationCrossDayCompany;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AbsentNotificationCrossDayTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;

    public function __construct($tenantDb)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
    }

    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('absent-notification') );

        CommonJobService::db_switch( $this->tenantDb );
                
        $setupData = NotificationService::getActiveCompanyByScenario(50);
        if(count( $setupData ) == 0){
            return;
        }

        foreach ($setupData as $setup) { 
            $companyId = $setup['companyID'];
            $companyName = $setup['company']['CompanyName']; 
            $companyScenarioId = $setup['id']; 

            AbsentNotificationCrossDayCompany::dispatch($this->tenantDb, $companyId, $companyName, $companyScenarioId);
        }

    }
}

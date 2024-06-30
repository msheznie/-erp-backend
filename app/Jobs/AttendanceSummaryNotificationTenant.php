<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\helper\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttendanceSummaryNotificationTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    public $isDailyBasis;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb, $isDailyBasis)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
        $this->isDailyBasis = $isDailyBasis;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-notification') );

        CommonJobService::db_switch( $this->tenantDb );
        
        $logSlug = ($this->isDailyBasis)? "Daily": "Weekly";

             . __CLASS__ ." \tline no :".__LINE__);
        
        $scenarioId = ($this->isDailyBasis)? 16: 17;
        $setupData = NotificationService::getActiveCompanyByScenario($scenarioId);
        if(count( $setupData ) == 0){
            return;
        }

        foreach ($setupData as $setup) { 
            $companyId = $setup['companyID'];
            $companyName = $setup['company']['CompanyName']; 

            AttendanceSummaryNotificationCompany::dispatch($this->tenantDb, $companyId, $companyName, $this->isDailyBasis);
        }
    }
}

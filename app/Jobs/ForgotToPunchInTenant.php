<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\helper\NotificationService;
use Illuminate\Support\Facades\Log;
use App\Jobs\ForgotToPunchInCompany;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForgotToPunchInTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    public $isPunchOut;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb, $isPunchOut=false)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
        $this->isPunchOut = $isPunchOut;
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
        
        $logSlug = ($this->isPunchOut)? "punch out": "";

        
        $setupData = NotificationService::getActiveCompanyByScenario(15);
        if(count( $setupData ) == 0){
            return;
        }

        foreach ($setupData as $setup) { 
            $companyId = $setup['companyID'];
            $companyName = $setup['company']['CompanyName']; 

            ForgotToPunchInCompany::dispatch($this->tenantDb, $companyId, $companyName, $this->isPunchOut);
        }

    }
}

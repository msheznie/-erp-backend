<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\LeaveAccrualService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Config;

class LeaveAccrualInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $path = CommonJobService::get_specific_log_file('leave-accrual');
        Log::useFiles($path);

        $db = $this->dispatch_db;

        CommonJobService::db_switch( $db );

        $company_list = CommonJobService::company_list();

        if($company_list->count() == 0){
            Log::error("Company details not found on $db ( DB ) \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }
        else{

            foreach ($company_list as $company){
                $company = $company->toArray();
                ['code'=> $company_code, 'name'=> $company_name] = $company;

                $msg = "Checking data for Annual accrual daily basis process on $company_code | $company_name ";
                Log::info("$msg \t on file: " . __CLASS__ ." \tline no :".__LINE__);

                $ser = new LeaveAccrualService($company, []);
                $groups = $ser->prepare_for_accrual();


                /*
                 $groups example

                $groups = [
                    [
                        "leaveGroupID": 2,
                        "description": "AL22",
                        "details": [
                            {
                              "leaveGroupDetailID": 3,
                              "leaveGroupID": 2,
                              "leaveTypeID": 2,
                              "policyMasterID": 1,
                              "isDailyBasisAccrual": true,
                              "noOfDays": 22
                            }
                        ]
                    ]
                ];
                */

                if($groups){
                    foreach ($groups as $group){
                        LeaveAccrualProcess::dispatch($db, $company, $group);
                            //->delay(now()->addSecond(2));
                    }
                }

            }
        }
    }
}

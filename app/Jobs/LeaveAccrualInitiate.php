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

class LeaveAccrualInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $debugDate;
    public $debug;
    public $groupId;
    protected $company_code = '';
    protected $company_name = '';
    protected $companyId = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $debugDate = null, $debug = false)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
        $this->debugDate = $debugDate;
        $this->debug = $debug;
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
            
            $service_types = CommonJobService::leave_accrual_service_types();
            $seconds = 0;
            foreach ($company_list as $company){
                $company = $company->toArray();
                ['code'=> $company_code, 'name'=> $company_name, 'id'=> $companyId] = $company;
                $this->company_code = $company_code;
                $this->company_name = $company_name;
                $this->companyId = $companyId;

                foreach ($service_types as $accrual_type_det){
                    $accType = $accrual_type_det['description'];
                    $logData = $accType." triggered : ".$company_code;

                    $ser = new LeaveAccrualService($company, $accrual_type_det, [], null, $this->debug);
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

                    if(count($groups) > 0){
                        $this->groupId = '';
                        foreach ($groups as $group){
                            $group = array_only($group, ['leaveGroupID', 'description']);
                            $this->groupId .= $group['leaveGroupID'].', ' ?? null;
                            $seconds += 30;
                           
                            LeaveAccrualProcess::dispatch($db, $company, $accrual_type_det, $group, $this->debugDate, $this->debug)
                                ->delay(now()->addSeconds($seconds));
                        }
                    }

                    LeaveAccrualService::insertToLogTb( $logData, 'info', $accType.' ~ Leave Group: '.$this->groupId , $this->companyId);
                }
            }
        }
    }

    function log_suffix($line_no) : string
    {
        return " $this->company_code | $this->company_name \t on file:  " . __CLASS__ ." \tline no : {$line_no}";
    }
}

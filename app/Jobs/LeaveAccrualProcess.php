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

class LeaveAccrualProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $company;
    public $group;
    public $accrual_type_det;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $company_det, $accrual_type_det, $group)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
        $this->company = $company_det;
        $this->accrual_type_det = $accrual_type_det;
        $this->group = $group;

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

        ['code'=> $company_code, 'name'=> $company_name] = $this->company;

        $accDes = $this->accrual_type_det['description'];
        $leaveGroup = $this->group['description'];

        $msg = "Processing the {$accDes} | {$leaveGroup} (leave group) for {$company_code} | {$company_name}";
        Log::info($msg . " on file: " . __CLASS__ ." \tline no :".__LINE__);

        CommonJobService::db_switch( $this->dispatch_db );
        $ser = new LeaveAccrualService($this->company, $this->accrual_type_det, $this->group);
        $ser->create_accrual();
    }

}

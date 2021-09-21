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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_det, $group)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->company = $company_det;
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

        Log::info("Processing the leave accrual for {$company_code} | {$company_name}");

        $ser = new LeaveAccrualService($this->company, $this->group);
        $ser->create_accrual();
    }
}

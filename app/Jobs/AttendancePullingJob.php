<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\Services\hrms\attendance\AttendanceDataPullingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AttendancePullingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tenantId;
    public $companyId;
    public $pullingDate;
    public $isClockInPulling;

    public function __construct($tenantId, $companyId, $pullingDate, $isClockInPulling)
    {
        $this->tenantId = $tenantId;
        $this->companyId = $companyId;
        $this->pullingDate = $pullingDate;
        $this->isClockInPulling = $isClockInPulling;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockIn') );

        $db_name = CommonJobService::get_tenant_db($this->tenantId);
        if(empty($db_name)){
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }else{

            CommonJobService::db_switch( $db_name );
            
            $obj = new AttendanceDataPullingService($this->companyId, $this->pullingDate, $this->isClockInPulling);
            $obj->execute();
        }
    }
}

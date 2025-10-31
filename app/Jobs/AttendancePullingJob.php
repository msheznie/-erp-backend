<?php

namespace App\Jobs;

use App\enums\modules\Modules;
use App\Services\hrms\attendance\SMAttendancePullingService;
use App\Services\hrms\modules\HrModuleAssignService;
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
    public $isClockOutPulling;

    public function __construct($tenantId, $companyId, $pullingDate, $isClockOutPulling)
    {
        $this->tenantId = $tenantId;
        $this->companyId = $companyId;
        $this->pullingDate = $pullingDate;
        $this->isClockOutPulling = $isClockOutPulling;
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
            return Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }

        CommonJobService::db_switch( $db_name );
        $isShiftModule = HrModuleAssignService::checkModuleAvailability($this->companyId, Modules::SHIFT);

        if($isShiftModule){
            $obj = new SMAttendancePullingService($this->companyId, $this->pullingDate, $this->isClockOutPulling);
            $obj->execute();
            return;
        }

        $obj = new AttendanceDataPullingService($this->companyId, $this->pullingDate, $this->isClockOutPulling);
        $obj->execute();
    }
}

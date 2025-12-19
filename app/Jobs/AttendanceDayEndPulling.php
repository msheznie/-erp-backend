<?php

namespace App\Jobs;

use App\enums\modules\Modules;
use App\Services\hrms\attendance\SMAttendancePullingService;
use App\Services\hrms\modules\HrModuleAssignService;
use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\hrms\attendance\AttendanceDataPullingService;

class AttendanceDayEndPulling implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatchDb;
    public $companyId;
    public $attDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatchDb, $companyId, $attDate)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatchDb = $dispatchDb;
        $this->companyId = $companyId;
        $this->attDate = $attDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockOut') );

        CommonJobService::db_switch( $this->dispatchDb );

        $msg = "Company id {$this->companyId} started to execute the day end pulling in {$this->dispatchDb} DB ( {$this->attDate} )";

        $isShiftModule = HrModuleAssignService::checkModuleAvailability($this->companyId, Modules::SHIFT);
        if($isShiftModule){
            $obj = new SMAttendancePullingService($this->companyId, $this->attDate, true);
        }else{
            $obj = new AttendanceDataPullingService($this->companyId, $this->attDate, true);
        }

        $obj->execute();
    }
}

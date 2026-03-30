<?php

namespace App\Jobs;
use App\enums\modules\Modules;
use App\helper\CommonJobService;
use App\Services\hrms\attendance\SMAttendanceCrossDayPullingService;
use App\Services\hrms\modules\HrModuleAssignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AttendanceCrossDayPulling implements ShouldQueue{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatchDb;
    public $companyId;
    public $attDate;

    public function __construct($dispatchDb, $companyId, $attDate)
    {
        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->dispatchDb = $dispatchDb;
        $this->companyId = $companyId;
        $this->attDate = $attDate;

    }

    public function handle()
    {



        CommonJobService::db_switch($this->dispatchDb);

        $msg = "Company id {$this->companyId} started to executed the cross day end pulling in at ";
        $msg .= "{$this->dispatchDb} DB ( {$this->attDate} )";
        
        Log::channel('attendance_cross_day_job_service')->info($msg);

        $isShiftModule = HrModuleAssignService::checkModuleAvailability($this->companyId, Modules::SHIFT);

        if(!$isShiftModule){
            return Log::channel('attendance_cross_day_job_service')->error("cannot proceed in old shift module at ". $this->dateTime);
        }

        $obj = new SMAttendanceCrossDayPullingService($this->companyId, $this->attDate);
        $obj->execute();
    }
}
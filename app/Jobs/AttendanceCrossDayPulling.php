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
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatchDb = $dispatchDb;
        $this->companyId = $companyId;
        $this->attDate = $attDate;

    }

    public function handle()
    {


        Log::useFiles( CommonJobService::get_specific_log_file('attendance-cross-day-clockOut') );

        CommonJobService::db_switch($this->dispatchDb);

        $msg = "Company id {$this->companyId} started to execute the cross day end pulling in";
        $msg .= "{$this->dispatchDb} DB ( {$this->attDate}111 )";

        Log::info("$msg \t on file: " . __CLASS__ . " \tline no :" . __LINE__);

        $isShiftModule = HrModuleAssignService::checkModuleAvailability($this->companyId, Modules::SHIFT);

        if(!$isShiftModule){
            return Log::error("cannot proceed in old shift module");
        }

        $obj = new SMAttendanceCrossDayPullingService($this->companyId, $this->attDate);
        $obj->execute();



    }
}
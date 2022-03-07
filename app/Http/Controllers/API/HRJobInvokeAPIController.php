<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\CompanyJobs;
use Illuminate\Http\Request;
use App\helper\CommonJobService;
use App\Jobs\AttendancePullingJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppBaseController;
use App\Services\hrms\attendance\AttendanceDataPullingService;

class HRJobInvokeAPIController extends AppBaseController
{
    
    public function attendanceClockIn(Request $request)
    {
        
        $tenantId = $request->input('tenantId'); 
        $companyId = $request->input('companyId'); 
        $pullingDate = $request->input('attendanceDate');
        $isClockOutPulling = false;
        
        AttendancePullingJob::dispatch($tenantId, $companyId, $pullingDate, $isClockOutPulling);
            //->delay(now()->addSeconds(10));

        $data = [
            'tenantId'=> $tenantId, 'companyId'=> $companyId, 'attendanceDate'=> $pullingDate,
        ];

        return $this->sendResponse($data, 'clock in pulling job added to queue');
    }

    function test2(Request $request){
        //$n = CommonJobService::get_active_companies('pull-attendance');
        $n = Carbon::now()->subDays(1);
        echo '<pre>'; print_r($n); echo '</pre>'; 
        $n = Carbon::now()->timezone('Asia/Muscat')->subDays(1);
        echo '<pre>'; print_r($n); echo '</pre>';
    }

    function test(Request $request){
        
        $tenantId = $request->input('tenantId'); 
        $companyId = $request->input('companyId'); 
        $pullingDate = $request->input('attendanceDate');
        $isClockOutPulling = true;

        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockIn') );

        $dbName = CommonJobService::get_tenant_db($tenantId);
        
        if(empty($dbName)){
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return $this->sendError('db details not found.');
        }

        CommonJobService::db_switch( $dbName );

        $obj = new AttendanceDataPullingService($companyId, $pullingDate, $isClockOutPulling);
        $resp = $obj->execute();
        
        $data = [
            'tenantId'=> $tenantId, 'companyId'=> $companyId, 'attendanceDate'=> $pullingDate,
        ];

        return $this->sendResponse($data, 'clock out pulling job added to queue');
        //dd($resp);
    }
}

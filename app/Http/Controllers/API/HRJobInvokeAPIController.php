<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\helper\CommonJobService;
use App\Http\Controllers\AppBaseController;
use App\Jobs\AttendancePullingJob;
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

    function test(){
        $db_name = 'asaas_gears_erp';
        $tenantId = 9; 
        $companyId = 1; 
        $pullingDate = '2022-01-17'; 
        $isClockOutPulling = true;

        CommonJobService::db_switch( $db_name );

        $obj = new AttendanceDataPullingService($companyId, $pullingDate, $isClockOutPulling);
        $resp = $obj->execute();
        
        $data = [
            'tenantId'=> $tenantId, 'companyId'=> $companyId, 'attendanceDate'=> $pullingDate,
        ];

        return $this->sendResponse($data, 'clock out pulling job added to queue');
        //dd($resp);
    }
}

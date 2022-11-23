<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Models\CompanyJobs;
use Illuminate\Http\Request;
use App\helper\CommonJobService;
use App\Jobs\AttendancePullingJob;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;
use App\Jobs\ForgotToPunchInTenant;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AppBaseController;
use App\Console\Commands\ForgotToPunchInScheduler;
use App\Services\hrms\attendance\ForgotToPunchInService;
use App\Services\hrms\attendance\ForgotToPunchOutService;
use App\Services\hrms\attendance\AttendanceDataPullingService;
use App\Services\hrms\attendance\AttendanceDailySummaryService;
use App\Services\hrms\attendance\AttendanceWeeklySummaryService;


class HRJobInvokeAPIController extends AppBaseController
{
    public function attendanceClockIn(Request $request)
    {        
        
        $tenantId = $request->input('tenantId'); 
        $companyId = $request->input('companyId'); 
        $pullingDate = $request->input('attendanceDate');
        $isClockOutPulling = false;
        
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockIn') );

        $data = [
            'tenantId'=> $tenantId, 'companyId'=> $companyId, 'attendanceDate'=> $pullingDate,
        ];

        Log::info('auto sync triggered', $data);

        AttendancePullingJob::dispatch($tenantId, $companyId, $pullingDate, $isClockOutPulling);
            //->delay(now()->addSeconds(10));
    
        return $this->sendResponse($data, 'clock in pulling job added to queue');
    }

    function attendance_notification_debug(Request $request){
        $data = [];

        $tenantId = $request->input('tenantId');
        $scenarioId = $request->input('scenarioId');
        $companyId = $request->input('companyId');
        $date = $request->input('date');
        $time = $request->input('time');

        $this->loadTenantDb($tenantId);

        $job = null;
        switch($scenarioId){
            case 15:                
                $job = new ForgotToPunchInService($companyId, $date, $time);
            break;

            case 15.1:
                $date = Carbon::parse($date)->subDay()->format('Y-m-d');
                $job = new ForgotToPunchOutService($companyId, $date);
            break;

            case 16:
                $date = Carbon::parse($date)->subDay()->format('Y-m-d');
                $job = new AttendanceDailySummaryService($companyId, $date);
            break;

            case 17:
                $job = new AttendanceWeeklySummaryService($companyId, $date);
            break;
                throw new Exception("scenario id is not valid");
            default:
                
        } 
       
        $job->run();

        $msg = "Service started";
        return $this->sendResponse($data, $msg);
    }
    
    function loadTenantDb($id){
        if(empty($id)){
            throw new Exception("tenant id is required");
        }

        $db = CommonJobService::get_tenant_db($id);
        
        if(empty($db)){
            throw new Exception("database not found");
        }

        CommonJobService::db_switch($db);
    }

    function test2(Request $request){
        //$n = CommonJobService::get_active_companies('pull-attendance');
        $n = Carbon::now()->subDays(1);
        echo '<pre>'; print_r($n); echo '</pre>'; 
        $n = Carbon::now()->timezone('Asia/Muscat')->subDays(1);
        echo '<pre>'; print_r($n); echo '</pre>';
    }

    function clockOutDebug(Request $request){
        
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

        $data = [
            'tenantId'=> $tenantId, 'companyId'=> $companyId, 'attendanceDate'=> $pullingDate,
        ];         

        CommonJobService::db_switch( $dbName );

        DB::table('job_logs')->insert([
            'company_id'=> $companyId,
            'module'=> 'HRMS',
            'description'=> 'attendance-clock-out-job',
            'scenario_id'=> 0,
            'processed_for'=> $pullingDate,
            'logged_at'=> Carbon::now()->format('Y-m-d'),
            'log_type'=> 'info',
            'log_data'=> json_encode(['manually triggered', $data]),
        ]);

        $obj = new AttendanceDataPullingService($companyId, $pullingDate, $isClockOutPulling);
        $resp = $obj->execute();

        return $this->sendResponse($data, 'clock out pulling job added to queue');
    }
}

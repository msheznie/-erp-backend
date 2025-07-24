<?php

namespace App\Http\Controllers\API;

use App\enums\modules\Modules;
use App\Jobs\AttendanceCrossDayPulling;
use App\Jobs\AttendanceDayEndPulling;
use App\Jobs\AttendanceDayEndPullingInitiate;
use App\Jobs\BirthdayWishInitiate;
use App\Jobs\LeaveAccrualInitiate;
use App\Services\hrms\attendance\SMAttendanceCrossDayPullingService;
use App\Services\hrms\attendance\SMAttendancePullingService;
use App\Services\hrms\modules\HrModuleAssignService;
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
use App\Services\hrms\attendance\AbsentNotificationNonCrossDayService;
use App\Services\hrms\attendance\AbsentNotificationCrossDayService;
use App\helper\BirthdayWishService;
use App\Jobs\DelegationActivation;
use App\Jobs\HrDocNotificationJob;
use App\Jobs\ReturnToWorkNotificationJob;
use App\Jobs\EmpProfileCreateNotificationJob;
use App\Jobs\TravelRequestNotificationJob;
use App\Models\Company;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\Artisan;
use App\Models\NotificationScenarios;

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
        $companyScenarioId = $request->input('companyScenarioId');

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

            case 50:
                $job = new AbsentNotificationNonCrossDayService($companyId, $date, $time, $companyScenarioId);
            break;

            case 50.1:
                $job = new AbsentNotificationCrossDayService($companyId, $date, $time, $companyScenarioId);
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

        $isShiftModule = HrModuleAssignService::checkModuleAvailability($companyId, Modules::SHIFT);

        if($isShiftModule){
            $obj = new SMAttendancePullingService($companyId, $pullingDate, $isClockOutPulling);
            $obj->execute();
            return $this->sendResponse($data, 'clock out pulling job added to queue');
        }

        $obj = new AttendanceDataPullingService($companyId, $pullingDate, $isClockOutPulling);
        $obj->execute();
        return $this->sendResponse($data, 'clock out pulling job added to queue');
    }

    function clockOutJobCall(Request $request)
    {
        $tenantId = $request->input('tenantId');
        $companyId = $request->input('companyId');
        $attDate = $request->input('attendanceDate');
        $dispatchDb = CommonJobService::get_tenant_db($tenantId);

        $validateRep = $this->validateClockOutJob($attDate, $tenantId, $dispatchDb, $companyId);
        if (!$validateRep['status']) {
            Log::error($validateRep['msg'] . " \t on file: " . __CLASS__ . " \tline no :" . __LINE__);
        }

        $msg = "{$dispatchDb} DB added to the queue for attendance day end pulling initiate ({$attDate}).";
        Log::info("$msg \t on file: " . __CLASS__ . " \tline no :" . __LINE__);

        AttendanceDayEndPulling::dispatch($dispatchDb, $companyId, $attDate);
        return $this->sendResponse(true, 'clock out pulling job added to queue');
    }

    function validateClockOutJob($attDate, $tenantId, $dispatchDb, $companyId){
        if (empty($tenantId)) {
            $msg = "Tenant details not found ({$attDate}).";

            return ['status'=> false, 'msg' => $msg ];
        }

        if (empty($companyId)) {
            $msg = "There is not a single company found for process the pull-attendance in {$dispatchDb} DB";

            return ['status'=> false, 'msg' => $msg ];
        }

        if (empty($attDate)) {
            $msg = "check the attendance date";

            return ['status'=> false, 'msg' => $msg ];
        }

        if(empty($dispatchDb)){
            $msg = "Cannot find database check the tenant id";
            return ['status'=> false, 'msg' => $msg ];
        }

        return ['status'=> true, 'msg' => 'success' ];
    }

    function birthdayWishesEmailDebug(){
        Artisan::call('command:birthday_wish_schedule');
    }

    function birthdayWishesEmailDebug2(Request $request){

        $companyId = $request->input('companyId');

        $company = Company::selectRaw('companySystemID AS id, CompanyID AS code, CompanyName AS name')
                   ->find($companyId);
        $company = $company->toArray();

        $job = new BirthdayWishService($company);

        $job->execute();

    }

    function sendTravelRequestNotifications(Request $request)
    { 
        $input = $request->all();  
        $tenantId = $input['tenantId'];
        $companyId = $input['companyId'];
        $id = $input['id'];
        $tripMaster = $input['tripMaster'];
        $tripRequestBookings = $input['tripRequestBookings'];
        $dbName = CommonJobService::get_tenant_db($tenantId);

        TravelRequestNotificationJob::dispatch($dbName, $companyId, $id,$tripMaster,$tripRequestBookings); 
        return $this->sendResponse([], 'Travel request notification scenario added to queue');
    }
 
    function maximumLeaveCarryForwardDebug(){  
        Artisan::call('command:leaveCarryForwardComputationSchedule');

        return $this->sendResponse(null, 'Job triggered successfully');
    }

    function sendHrDocNotifications(Request $request)
    { 
        $input = $request->all();  
      
        $tenantId = $input['tenantId'];
        $companyId = $input['companyId'];
        $id = $input['id'];
        $visibility = $input['visibility'];
        $employees = $input['employees'];
        $portalUrl = $input['portalUrl'];
        $dbName = CommonJobService::get_tenant_db($tenantId);
        
        HrDocNotificationJob::dispatch($dbName, $companyId, $id, $visibility, $employees, $portalUrl); 
        return $this->sendResponse([], 'HR document notification scenario added to queue');
    }

    function sendReturnToWorkNotifications(Request $request){
        $input = $request->all();  
        $tenantId = $input['tenantId'];
        $dbName = CommonJobService::get_tenant_db($tenantId);
        $companyId = $input['companyId'];
        $id = $input['id'];   
        $masterDetails = $input['masterDetails'];

        ReturnToWorkNotificationJob::dispatch($dbName, $companyId, $id, $masterDetails); 
        return $this->sendResponse([], 'Return to work notification scenario added to queue');
    }

    function sendEmpProfileCreateNotifications(Request $request){
        $input = $request->all();  
        $tenantId = $input['tenantId'];
        $dbName = CommonJobService::get_tenant_db($tenantId);
        $companyId = $input['companyId'];
        $id = $input['id'];   
        $masterDetails = $input['masterDetails'];

        EmpProfileCreateNotificationJob::dispatch($dbName, $companyId, $id, $masterDetails); 

        return $this->sendResponse([], 'Employee profile creation notification scenario added to queue');
    }

    function hrNotificationDebug(Request $request)
    {
        $input = $request->all();

        if(!isset($input['tenantId']) || !isset($input['scenarioId'])){
            return $this->sendError('Tenant ID and Scenario ID are required', 422);
        }

        $tenantId = $input['tenantId'];
        $scenarioId =$input['scenarioId'];
    
        $dbName = CommonJobService::get_tenant_db($tenantId);

        CommonJobService::db_switch($dbName);

        $scenario = NotificationScenarios::find($scenarioId);

        if(empty($scenario)){
            return $this->sendError('Scenario is not found', 422);
        }
        NotificationService::process($scenarioId);
        
        return $this->sendResponse([], $scenario->scenarioDescription. ' is processed');
        
    }

    function crossDayClockOutJobCall(Request $request)
    {
        $tenantId = $request->input('tenantId');
        $companyId = $request->input('companyId');
        $attDate = $request->input('attendanceDate');
        $dispatchDb = CommonJobService::get_tenant_db($tenantId);

        $validateRep = $this->validateClockOutJob($attDate, $tenantId, $dispatchDb, $companyId);
        if (!$validateRep['status']) {
            Log::error($validateRep['msg'] . " \t on file: " . __CLASS__ . " \tline no :" . __LINE__);
        }

        $msg = "{$dispatchDb} DB added to the queue for attendance cross day pulling initiate ({$attDate}).";
        Log::info("$msg \t on file: " . __CLASS__ . " \tline no :" . __LINE__);

        AttendanceCrossDayPulling::dispatch($dispatchDb, $companyId, $attDate);
        return $this->sendResponse(true, 'cross day clock out pulling job added to queue1');
    }

    function leaveAccrualJobCallDebug(Request $req){
        $tenantId = $req->input('tenantId');
        $debugDate = $req->input('debug_date');
        $debug = $req->input('is_debug');

        $tdb = CommonJobService::get_tenant_db($tenantId);
        if(empty($tdb)){
            Log::info("Tenant details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }

        Log::info("{$tdb} DB added to queue for leave accrual initiate 
        . \t on file: " . __CLASS__ ." \tline no :".__LINE__);

        LeaveAccrualInitiate::dispatch($tdb, $debugDate, $debug);
        return $this->sendResponse(true, 'Leave accrual schedule job added to queue');
    }

        function delegationJobCallDebug(Request $req){
        $tenantId = $req->input('tenantId');
       
        $tdb = CommonJobService::get_tenant_db($tenantId);
        if(empty($tdb)){
            Log::info("Tenant details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }

        Log::info("{$tdb} DB added to queue for delegation
        . \t on file: " . __CLASS__ ." \tline no :".__LINE__);

        DelegationActivation::dispatch($tdb);
        return $this->sendResponse(true, 'Delegation job added to queue');
    }
}

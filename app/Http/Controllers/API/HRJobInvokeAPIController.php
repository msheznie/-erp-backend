<?php

namespace App\Http\Controllers\API;

use App\Jobs\BirthdayWishInitiate;
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
use App\helper\BirthdayWishService;
use App\Jobs\TravelRequestNotificationJob;
use App\Models\Company;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\Artisan;


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
       // CommonJobService::db_switch('gears_erp_hrms_qa');
        $input = $request->all();  
        $tenantId = $input['tenantId'];
        $companyId = $input['companyId'];
        $id = $input['id'];
        $tripMaster = $input['tripMaster'];
        $tripRequestBookings = $input['tripRequestBookings'];
        TravelRequestNotificationJob::dispatch($tenantId, $companyId, $id,$tripMaster,$tripRequestBookings); 
        return $this->sendResponse([], 'Travel request notification scenario added to queue');
    }

    function test($withUser)
    {
        //CommonJobService::db_switch('gears_erp_hrms_qa');
        $test =  NotificationCompanyScenario::select('id')
            ->where('scenarioID', 20)
            ->where('companyID', 3)
            ->where('isActive', 1);

        if ($withUser) {
            $test = $test->with(['user' => function ($q) {
                $q->select('id','empID','companyScenarionID','isActive')
                ->where('isActive', '=', 1)
                ->with(['employee' => function ($q3){ 
                    $q3->select('employeeSystemID','empFullName','empEmail','empID');
                }])
                ->with(['notificationUserDayCheck' => function ($q2) {
                    $q2->select('id','notificationUserID','notificationDaySetupID','emailNotification')
                    ->where('emailNotification', 1);
                }]);
            }])
                ->whereHas('user', function ($query) {
                    $query->where('isActive', '=', 1);  
                });
        }

        return $test->first();
    }
}

<?php

namespace App\helper;

use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BirthdayWishService
{

    public $companyId;
    public $companyCode;
    public $image;
    public $switchDb;

    public function __construct($companyData, $db='')
    {
        $this->companyId = $companyData['id'];
        $this->companyCode = $companyData['code'];
        $this->switchDb = $db;
        $this->image = base64_encode(Storage::disk('local_public')->get('image/Birthday-ASAAS-01.jpg'));
    }

    function execute()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('birthday-wishes') );
        $this->insertToLogTb('execution started');
        if($this->switchDb){
            CommonJobService::db_switch($this->switchDb);
        }

        $birthdayWishesPolicy = SME::policy($this->companyId, 'BRN', 'All');

        if($birthdayWishesPolicy != 1){
            $this->insertToLogTb('policy not set');
            return;
        }

        $employeeDetails = $this->getEmployeeDetails();

        if($employeeDetails->count() == 0){

            $msg = "There is no employee found for send Birthday wish on {$this->companyCode} company";
            $this->insertToLogTb($msg,'error');
            return;
        }

        $this->sendEachEmail($employeeDetails);
    }

    function getEmployeeDetails()
    {
        return $employeeDetails = SrpEmployeeDetails::select('EIdNo','Ename2', 'EEmail', 'EDOB','Erp_companyID')
        ->whereRaw('DATE_FORMAT(EDOB, "%m %d") = DATE_FORMAT(curdate() , "%m %d")')
        ->where('Erp_companyID', $this->companyId)
        ->where('isDischarged',0)
        ->get();
    }

    function sendEachEmail($employeeDetails)
    {
        $empList = $employeeDetails->map(function($emp){
           return  $emp->EIdNo.'|'.$emp->Ename2;
        });

        $this->insertToLogTb([
            "Birthday wish read to send for following employees",
            $empList
        ]);

        foreach ($employeeDetails as $employee) {

            $emailData['empEmail'] = $employee->EEmail;
            $emailData['companySystemID'] = $employee->Erp_companyID;
            $temp = '<img src="data:image/jpg;base64,'.$this->image.'" style="width: 100% !important;height: auto !important;">';
            $emailData['alertMessage'] = "Happy Birthday $employee->Ename2.";
            $emailData['emailAlertMessage'] = $temp;
            $sendEmail = \Email::sendEmailErp($emailData);

            if(!$sendEmail["success"]){
                $msg = "Birthday wish email not send for {$employee->EIdNo} | {$employee->Ename2} ";
                $this->insertToLogTb($msg,'error');
            }

        }
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);
        $currentDate = Carbon::now()->format('Y-m-d H:i:s');
        $data = [
            'company_id'=> $this->companyId,
            'module'=> 'HRMS',
            'description'=> 'Birthday wish service',
            'scenario_id'=> 0,
            'processed_for'=> $currentDate,
            'logged_at'=> $currentDate,
            'log_type'=> $logType,
            'log_data'=> $logData,
        ];

        DB::table('job_logs')->insert($data);
    }

}
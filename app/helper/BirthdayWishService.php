<?php

namespace App\helper;

use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BirthdayWishService
{

    public $companyId;
    public $companyCode;
    public $image;
    public $image2;
    public $image3;
    public $switchDb;

    public function __construct($companyData, $db='')
    {
        $this->companyId = $companyData['id'];
        $this->companyCode = $companyData['code'];
        $this->switchDb = $db;
        $this->image = asset("image/Birthday-ASAAS-01.jpg");
        $this->image2 = "https://gearsentattachments-qa.s3.us-west-1.amazonaws.com/BG/logos/BG_logo.png?X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIAX7MLAY73AFHLFUGD%2F20230109%2Fus-west-1%2Fs3%2Faws4_request&X-Amz-Date=20230109T072408Z&X-Amz-SignedHeaders=host&X-Amz-Expires=3600&X-Amz-Signature=5663f03c89ffe4be54deef4473d334b1d1cad74fe33e2c339959548e7f974c36";
        $this->image3 = asset("logos/BIT_logo.png");

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

        if(empty($employeeDetails)){

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
            $temp = '<img src= '.$this->image.' />
                     <br><img src= '.$this->image2.' />
                     <br><img src= '.$this->image3.' /> ';
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
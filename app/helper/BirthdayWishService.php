<?php

namespace App\helper;

use App\Models\SrpEmployeeDetails;
use App\Models\BirthdayTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BirthdayWishService
{

    public $companyId;
    public $companyCode;
    public $companyName;
    public $switchDb;

    public function __construct($companyData, $db = '')
    {
        $this->companyId = $companyData['id'];
        $this->companyCode = $companyData['code'];
        $this->companyName = $companyData['name'];
        $this->switchDb = $db;
    }

    function execute()
    {
        Log::useFiles(CommonJobService::get_specific_log_file('birthday-wishes'));
        $this->insertToLogTb('execution started');
        if ($this->switchDb) {
            CommonJobService::db_switch($this->switchDb);
        }

        $birthdayWishesPolicy = SME::policy($this->companyId, 'BRN', 'All');

        if ($birthdayWishesPolicy != 1) {
            $this->insertToLogTb('policy not set');
            return;
        }

        $employeeDetails = $this->getEmployeeDetails();
        if ($employeeDetails->count() == 0) {
            $msg = "No employees found for birthday wishes in {$this->companyCode} company";
            $this->insertToLogTb($msg);
            return;
        }

        $empList = $employeeDetails->map(function ($emp) {
            return  $emp->EIdNo . '|' . $emp->Ename2;
        });
        $this->insertToLogTb([
            "Birthday wishes are ready to be sent to the following employees",
            $empList
        ]);

        $templateDet = $this->getBirthdayTemplateDet();
        if (!$templateDet) {
            $msg = "Birthday template not assigned to {$this->companyCode} company";
            $this->insertToLogTb($msg);
        }
               
        $this->sendEachEmail($employeeDetails,$templateDet);
    }
    
    function getBirthdayTemplateDet()
    {
        $templateDet = BirthdayTemplate::select('template', 'client_code', 'image_path')
        ->where('company_id', $this->companyId)
        ->first();
        
        return $templateDet ?: Helper::getDefaultBirthdayTemplate();      
    }

    function getEmployeeDetails()
    {
        $currentDate = Carbon::now('Asia/Muscat')->format('m-d');

        return SrpEmployeeDetails::select('EIdNo', 'Ename2', 'EEmail', 'EDOB', 'Erp_companyID')
            ->whereRaw("DATE_FORMAT(EDOB, '%m-%d') = '$currentDate'")
            ->where('Erp_companyID', $this->companyId)
            ->where('empConfirmedYN', 1)
            ->where('isDischarged', 0)
            ->get();
    }

    /**
     * @throws \Throwable
     */
    function sendEachEmail($employeeDetails, $templateDet)
    {
        
        $clientCode = !empty($templateDet->client_code) ? $templateDet->client_code : null;
        $template = !empty($templateDet->template) ? $templateDet->template : null;
        $imagePath = !empty($templateDet->image_path) ? $templateDet->image_path : 'images/birthday/Birthday-default.jpg';
        if($clientCode == 'ASAAS' && empty($templateDet->image_path)){
            $imagePath = 'images/birthday/Birthday-ASAAS-01.jpg';
        }
        $imageUrl = $this->getBirthdayImage($imagePath);
              
        foreach ($employeeDetails as $employee) {

            $emailData['empEmail'] = $employee->EEmail;
            $emailData['companySystemID'] = $employee->Erp_companyID;
            $emailData['emailAlertMessage'] = $this->getBirthdayEmailMessage($imageUrl, $employee, $clientCode, $template);
            $emailData['alertMessage'] = "Happy Birthday $employee->Ename2.";
            $sendEmail = \Email::sendEmailErp($emailData);

            if (!$sendEmail["success"]) {
                $msg = "Birthday wish email not send for {$employee->EIdNo} | {$employee->Ename2} ";
                $this->insertToLogTb($msg, 'error');
            }
        }
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $currentDate = Carbon::now()->format('Y-m-d H:i:s');
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Birthday wish service',
            'scenario_id' => 0,
            'processed_for' => $currentDate,
            'logged_at' => $currentDate,
            'log_type' => $logType,
            'log_data' => $logData,
        ];

        DB::table('job_logs')->insert($data);
    }

    public function getBirthdayImage($imagePath)
    {
        return Helper::getFileUrlFromS3($imagePath, '+10080 minutes');
    }

    /**
     * @throws \Throwable
     */
    public function getBirthdayEmailMessage($image, $employee, $clientCode, $template): string
    {
        switch ($clientCode) {
            case 'ASAAS':
                return '<img src="' . $image . '">';

            default:
                return view("email.birthday.{$template}", [
                    'employeeName' => $employee->Ename2,
                    'companyName' => $this->companyName,
                    'imageUrl' => $image
                ])->render();
        }
    }
}

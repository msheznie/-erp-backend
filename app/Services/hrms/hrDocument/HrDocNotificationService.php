<?php

namespace App\Services\hrms\hrDocument;

use Collator;
use Exception;
use Carbon\Carbon;
use App\helper\CommonJobService;
use App\Models\Company;
use App\Models\NotificationCompanyScenario;
use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HrDocNotificationService
{
    private $companyId;
    private $tenantId;
    private $id;
    private $date;
    private $visibility;
    private $employees;
    private $notifyList;
    private $documentCode;


    public function __construct($companyId, $tenantId, $id, $visibility, $employees)
    {
        $this->companyId = $companyId;
        $this->tenantId = $tenantId;
        $this->id = $id;
        $this->date = Carbon::now()->format('Y-m-d H:i:s');
        $this->visibility = $visibility;
        $this->employees = $employees;
        $this->notifyList = [];
        $this->documentCode = $id;
    }

    function execute()
    { 
        
        $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'one'],'error');  
        
        if ( $this->visibility == 2 && empty($this->employees)) {
            $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'Employees does not exists'],'error');  
            return false;
        }

        $this->validateNotifyEmpDataExists();
        $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'Employees data does not exists'],'error');  

        if (empty($this->notifyList)) {
            $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'Employees data does not exists'],'error');  
            return false;
        }
        $this->sendEmail();
        $this->insertToLogTb([ 'Document Code'=> $this->documentCode ,'Message'=> 'Execution successfully completed']);
    }

    public function sendEmail()
    { 
        $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'two'],'error');  

        $this->insertToLogTb([ 'Document Code'=> $this->documentCode ,'Message'=> 'Email Function Triggered']);
        $msg = '';
        $logType = 'info';
        // $this->generateTravelRequestPdf(); 
        // $dataEmail['attachmentFileName'] = $this->pdfName; 
    

        foreach ($this->notifyList as $val) {
            $dataEmail['empEmail'] = $val['EEmail'];
            $dataEmail['companySystemID'] = $this->companyId;
            $temp = '<p>Dear ' . $val['Ename2'] . ', <br /></p>';
            $temp .=  '<p> HR has uploaded a new document, please login to download it.</p>';
            $dataEmail['emailAlertMessage'] = $temp;
            $dataEmail['alertMessage'] = 'New HR Document';
            $sendEmail = \Email::sendEmailErp($dataEmail);
            if (!$sendEmail["success"]) {
                $msg = "HR Document notification not sent for {$val['EIdNo']} | {$val['Ename2']} "; 
                $logType = 'error';
            }else { 
                $msg = "HR Document notification sent for {$val['EIdNo']} | {$val['Ename2']} ";
            }
            $this->insertToLogTb(['Document Code'=> $this->documentCode ,'Message'=> $msg],$logType); 
        }
    }

    
    public function validateNotifyEmpDataExists()
    {
        $getNotifyEmployees = $this->getEmpDetails();
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees : []);
    }

    public function getEmpDetails()
    { 
        $notifyEmpData = SrpEmployeeDetails::select('EIdNo', 'Ename2', 'EEmail')
            ->whereIn('EIdNo', $this->employees)
            ->get()
            ->toArray();
        
        return $notifyEmpData;
    }

    public function getCompanyData(){ 
        return Company::where('companySystemID', $this->companyId)
        ->first();
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'HR Document Notification Scenario',
            'scenario_id' => 20,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

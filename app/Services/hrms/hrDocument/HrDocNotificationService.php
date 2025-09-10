<?php

namespace App\Services\hrms\hrDocument;

use Collator;
use Exception;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\DB;

class HrDocNotificationService
{
    private $companyId;
    private $id;
    private $date;
    private $visibility;
    private $employees;
    private $notifyList;
    private $portalUrl;

    public function __construct($companyId, $id, $visibility, $employees, $portalUrl='')
    {
        $this->companyId = $companyId;
        $this->id = $id;
        $this->date = Carbon::now()->format('Y-m-d H:i:s');
        $this->visibility = $visibility;
        $this->employees = $employees;
        $this->notifyList = [];   
        $this->portalUrl = $portalUrl;   
    }

    function execute()
    { 
        
        if ( $this->visibility == 2 && empty($this->employees)) {
            $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'Employees does not exists'],'error');  
            return false;
        }

        $this->validateNotifyEmpDataExists();
       
        if (empty($this->notifyList)) {
            $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> 'Employees data does not exists'],'error');  
            return false;
        }
        $this->sendEmail();
        $this->insertToLogTb([ 'Document Code'=> $this->id ,'Message'=> 'Execution successfully completed']);
    }

    public function sendEmail()
    { 
        $this->insertToLogTb([ 'Document Code'=> $this->id ,'Message'=> 'Email Function Triggered']);
        $msg = '';
        $logType = 'info';

        foreach ($this->notifyList as $val) {
            $dataEmail['empEmail'] = $val['EEmail'];
            $dataEmail['companySystemID'] = $this->companyId;
            
            $temp = '<p>Dear ' . $val['Ename2'] . ', <br /></p>';
            $temp .= '<p> HR has uploaded a new document, please login to download it.</p>';
            if($this->portalUrl){
               $temp .= '<br><a href="'.$this->portalUrl.'"> Click here to view. </a> ';
            }
            $dataEmail['emailAlertMessage'] = $temp;
            $dataEmail['alertMessage'] = trans('email.new_hr_document');
            $sendEmail = \Email::sendEmailErp($dataEmail);
            if (!$sendEmail["success"]) {
                $msg = "HR Document notification not sent for {$val['EIdNo']} | {$val['Ename2']} "; 
                $logType = 'error';
            }else { 
                $msg = "HR Document notification sent for {$val['EIdNo']} | {$val['Ename2']} ";
            }
            $this->insertToLogTb(['Document Code'=> $this->id ,'Message'=> $msg],$logType); 
        }
    }

    
    public function validateNotifyEmpDataExists()
    {
        $getNotifyEmployees = $this->getEmpDetails();
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees : []);
    }

    public function getEmpDetails()
    { 
        if($this->visibility == 2){
            return SrpEmployeeDetails::select('EIdNo', 'Ename2', 'EEmail')
            ->whereIn('EIdNo', $this->employees)
            ->get()
            ->toArray();
        }   

        return SrpEmployeeDetails::select('EIdNo', 'Ename2', 'EEmail')
            ->where('Erp_companyID', $this->companyId)
            ->where('empConfirmedYN', 1)
            ->where('isDischarged', 0)
            ->get()
            ->toArray();       
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
            'scenario_id' => 0,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

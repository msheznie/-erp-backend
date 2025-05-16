<?php


namespace App\helper;

use App\Models\SupplierMaster;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationUser;
use App\Models\SrpEmployeeDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\Company;

class CheckRegisterNotificationService
{

    private $comScenarioId;
    private $params;
    private $type;
    private $days;
    private $currentDate;
    private $mailSubject = "Cheque Register Switch Notification";
    private $companyId = null;
    private $details;
    private $sentMailCount = 0;
    private $companyName;

    public function __construct($params)
    {
        $this->params = $params;
        $this->currentDate = Carbon::now()->format('Y-m-d H:i:s');
    }

    public function proceed()
    {

        $scenarioID = 48;
        $this->companyName = '';
        $this->companyId = $this->params['companyId'];
        $this->details = $this->params['details'];
        if($this->companyId)
        {
            $company = Company::find($this->companyId);
            $this->companyName = $company->CompanyName;
        }
        
        $com_assign_scenarios = NotificationService::getCompanyScenarioConfigurationForCompany($scenarioID,$this->companyId);
        $usersSetup = NotificationUser::getUsers($com_assign_scenarios->id);
        if (count($usersSetup) == 0) {
            $userConfMessage = "User's not configured for department end date expiry. \t on file: " . __CLASS__ ;
            $userConfMessage .= " \tline no :" . __LINE__;

            $this->insertToLogTb($userConfMessage, 'error');
            return false;
        }

        foreach ($usersSetup as $row) {
            switch ($row->applicableCategoryID) {
                case 1: //Employee
                    $mailTo = $row->empID;
                    $this->toSpecificEmployee($mailTo,$this->type);
                    break;
                default:
                    $defErrMsg = "Unknown Applicable Category \t on file: " . __CLASS__ . " \tline no :" . __LINE__;
                    $this->insertToLogTb($defErrMsg, 'error');
            }
        }

        $expiredDepartmentMsg = " expired department document mails send \t on file: " . __CLASS__ ;
        $expiredDepartmentMsg .= " \tline no :". __LINE__;
        $mailMessage = $this->sentMailCount . $expiredDepartmentMsg;
        $this->insertToLogTb($mailMessage);
        return true;
    }


    public function expiryTable()
    {
        $body = '<b>Cheque Replacement Details: <b>';
        $body .= "<br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">Document Number</th>
                        <th style="text-align: center;border: 1px solid black;">Previous Value</th>
                        <th style="text-align: center;border: 1px solid black;">Current Value</th>
                        <th style="text-align: center;border: 1px solid black;">Amended By</th>
                        <th style="text-align: center;border: 1px solid black;">Amended Date & Time</th> 
                        <th style="text-align: center;border: 1px solid black;">Reason for Replacement</th>                                            
                                  
                    </tr>
                </thead>';
        $body .= '<tbody>';

        $x = 1;
        foreach ($this->details as $row) {

            $body .= '<tr>
                <td style="text-align:center;border: 1px solid black;">' . $row['document'] . '</td>  
                <td style="text-align:center;border: 1px solid black;">' . $row['previous'] . '</td>  
                <td style="text-align:center;border: 1px solid black;">' . $row['current'] . '</td>  
                <td style="text-align:center;border: 1px solid black;">' . $row['amendBy'] . '</td>  
                <td style="text-align:center;border: 1px solid black;">' . date('Y-m-d', strtotime($row['amenDate'])) . '</td> 
                <td style="text-align:center;border: 1px solid black;">' . $row['reason'] . '</td>                 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }


    public function toSpecificEmployee($mailToEmp,$type){
        $mailTo = Employee::selectRaw('empEmail,empName,empEmail')->where('isEmailVerified',1)->where('empActive',1)->where('discharegedYN',0)->find( $mailToEmp );

        if(empty($mailTo)){
            $mailError = "Employee Not found \t on file: " . __CLASS__ ." \tline no :".__LINE__;
            $this->insertToLogTb($mailError,'error');
            return false;
        }
        $empEmail = $mailTo->empEmail ?? null;
        if (!empty($empEmail)) {
            $mailBody = "Hi {$mailTo->empName},<br/>";
            $mailBody .= $this->emailBody( );
            $mailBody .= "<br/>";
            $mailBody .= $this->expiryTable();
            $mailBody .= "<br/>";
            $mailBody .= "Best Regards,<br/> System Administrator,<br/> {$this->companyName}.";
            $empEmail = $mailTo->empEmail;
            $subject = $this->mailSubject;
            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);

            $this->sentMailCount++;
        }
        else
        {
            $mailError = "Email is missing for employee  {$mailTo->empName} \t on file: " . __CLASS__ . " \tline no :" . __LINE__;
            $this->insertToLogTb($mailError, 'warning');
        }

        return true;
    }


    public function emailBody( ){

        $str = "<br/>";

        $str .= "This is to inform you that a cheque has been replaced in the system. Please find the details below. ";

        $str .= "<br/>";

        return $str;
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'ERP',
            'description' => 'Supplier registration expiry',
            'scenario_id' => 47,
            'processed_for' => $this->currentDate,
            'logged_at' => $this->currentDate,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }

}
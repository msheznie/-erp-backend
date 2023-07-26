<?php


namespace App\helper;

use App\Models\HrEmpDepartments;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationUser;
use App\Models\SrpEmployeeDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepartmentExpiryNotificationService
{

    private $comScenarioId;
    private $companyId;
    private $type;
    private $days;
    private $currentDate;
    private $mailSubject = "Department End date Expiry Notification";
    private $sentMailCount = 0;
    private $expiryDate = null;
    private $expiredDepartments = [];

    public function __construct($company, $setup)
    {
        ['companyScenarionID' => $comScenarioId, 'beforeAfter' => $type, 'days' => $days] = $setup;
        $this->companyId = $company;
        $this->comScenarioId = $comScenarioId;
        $this->type = $type;
        $this->days = $days;
        $this->currentDate = Carbon::now()->format('Y-m-d H:i:s');
    }

    public function proceed()
    {
        $this->expiryDate = NotificationService::get_filter_date($this->type, $this->days);

        $expiredDepartments = HrEmpDepartments::where('date_to',$this->expiryDate)
            ->where('isPrimary', 1)
            ->where('Erp_companyID', $this->companyId)
            ->whereHas('employees', function ($q1) {
                $q1->where('isDischarged', 0);
            })
            ->with([
                'departments' => function ($q2) {
                    $q2->select('DepartmentMasterID', 'DepartmentDes');
                },
                'employees' => function ($q3) {
                    $q3->select('EIdNo', 'ECode', 'Ename2', 'EEmail');
                }
            ])
            ->select('date_to', 'DepartmentMasterID', 'EmpID')
            ->get();

        if (count($expiredDepartments) == 0) {
            $log = "Expiry department does not exist for type: {$this->type} and days: {$this->days}";
            $log .= "\t on file: " . __CLASS__ . " \tline no :" . __LINE__;
            $this->insertToLogTb($log);
            return false;
        }

        $this->expiredDepartments = $expiredDepartments->toArray();
        $expireDepartmentMsg = " expired department found. \t on file: " . __CLASS__ . " \tline no :" . __LINE__;
        $expiredDepartmentMsg = count($this->expiredDepartments) . $expireDepartmentMsg ;
        $this->insertToLogTb($expiredDepartmentMsg);

        $usersSetup = NotificationUser::get_notification_users_setup($this->comScenarioId);
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
                    $this->toSpecificEmployee($mailTo);
                    break;
                case 7: //Reporting manager
                    $this->toReportingManager();
                    break;
                case 9: //Applicable Employee
                    $this->toDocumentOwner();
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

    public function toDocumentOwner(){
        $data = collect( $this->expiredDepartments )->groupBy('EmpID')->toArray();

        foreach ($data as $row){

            $mailTo = $row[0]['employees'];
            $mailBody = "Dear {$mailTo['Ename2']},<br/>";
            $mailBody .= $this->emailBody(9 );
            $mailBody .= $this->expiryTable($row, true);

            $empEmail = $mailTo['EEmail'];
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);

            $this->sentMailCount++;

        }

        return true;
    }

    public function toReportingManager(){
        $empList = array_column($this->expiredDepartments, 'EmpID');
        $empList = array_unique($empList);

        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->whereIn('empID', $empList)
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail')
            ->get();

        if(count($manager) == 0){
            $managerError = "Manager details not found for expiry department. \t on file: ". __CLASS__ ;
            $managerError .= " \tline no :".__LINE__;
            $this->insertToLogTb($managerError);
            return false;
        }

        $manager = collect( $manager->toArray() )->groupBy('managerID')->toArray();

        $empWiseDocs = collect( $this->expiredDepartments )->groupBy('EmpID')->toArray();

        foreach ($manager as $row){
            $managerInfo = $row[0]['info'];

            $myReportingData = collect([]);
            foreach ($row as $rpt){
                $thisEmp = $rpt['empID'];
                if( array_key_exists($thisEmp, $empWiseDocs)){
                    $myReportingData = $myReportingData->concat( $empWiseDocs[$thisEmp] );
                }
            }

            $myReportingData = $myReportingData->toArray();

            $mailBody = "Dear {$managerInfo['Ename2']},<br/>";
            $mailBody .= $this->emailBody(7 );
            $mailBody .= $this->expiryTable( $myReportingData );

            $empEmail = $managerInfo['EEmail'];
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);

            $this->sentMailCount++;

        }

        return true;
    }

    public function expiryTable($data, $isOwner=false)
    {
        $empColumn = (!$isOwner)? '<th style="text-align: center;border: 1px solid black;">Employee</th>': '';

        $body = '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        '.$empColumn.'
                        <th style="text-align: center;border: 1px solid black;">Department Name</th>
                        <th style="text-align: center;border: 1px solid black;">Expired Date</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';

        $x = 1;
        foreach ($data as $row) {
            $empName = '';

            if(!$isOwner){
                $empName = $row['employees']['ECode'] .' | '. $row['employees']['Ename2'];
                $empName = '<td style="text-align:left;border: 1px solid black;">' . $empName . '</td>';
            }

            $department = $row['departments'];
            $dateTo = Carbon::parse($row['date_to'])->toDateString();
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                '.$empName.'
                <td style="text-align:left;border: 1px solid black;">' . $department['DepartmentDes'] . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $dateTo . '</td>                 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }

    public function toSpecificEmployee($mailToEmp){
        $mailTo = SrpEmployeeDetails::selectRaw('Ename2, EEmail')->find( $mailToEmp );

        if(empty($mailTo)){
            $mailError = "Employee Not found \t on file: " . __CLASS__ ." \tline no :".__LINE__;
            $this->insertToLogTb($mailError,'error');
            return false;
        }

        $mailBody = "Dear {$mailTo->Ename2},<br/>";
        $mailBody .= $this->emailBody(1 );
        $mailBody .= $this->expiryTable($this->expiredDepartments);

        $empEmail = $mailTo->EEmail;
        $subject = $this->mailSubject;
        NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);

        $this->sentMailCount++;

        return true;
    }

    public function emailBody( $for ){

        $str = "<br/>";

        switch ($for){
            case 1: //Employee
                $str .= "Departments expiry details as follow";
                break;
            case 7: //Reporting manager
                $str .= "Departments of your reporting employees expiry details as follow";
                break;
            case 9: //Applicable Employee
                $str .= "Your departments expiry details as follow";
                break;
        }

        $str .= ".<br/><b> Expiry date </b> : " . $this->expiryDate;
        $str .= ' ( '. Carbon::parse( $this->expiryDate )->diffForHumans() . " ) <br/><br/><br/>";

        return $str;
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Department End date Expiry Notification',
            'scenario_id' => 39,
            'processed_for' => $this->currentDate,
            'logged_at' => $this->currentDate,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }

}
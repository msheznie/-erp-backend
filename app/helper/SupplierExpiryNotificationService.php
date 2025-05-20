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

class SupplierExpiryNotificationService
{

    private $comScenarioId;
    private $companyId;
    private $type;
    private $days;
    private $currentDate;
    private $mailSubject = "Supplier Registration Expiry Notification";
    private $sentMailCount = 0;
    private $expiryDate = null;
    private $expiredSuppliers = [];
    private $companyName;
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

        $company = Company::find($this->companyId);
        $this->companyName = $company->CompanyName;

        $expiredSuppliers = SupplierMaster::whereHas('assigned',function($query) use($company){
                $query->where('companySystemID',$company->companySystemID)->where('isAssigned',-1);
             })->whereDate('registrationExprity',$this->expiryDate)->where('approvedYN',1)->where('isActive',1)->where('isBlocked',0)
               ->get();

        if (count($expiredSuppliers) == 0) {
            $log = "Expiry suppliers does not exist for type: {$this->type} and days: {$this->days}";
            $log .= "\t on file: " . __CLASS__ . " \tline no :" . __LINE__;
            $this->insertToLogTb($log);
            return false;
        }
       
        $this->expiredSuppliers = $expiredSuppliers->toArray();
        $expireSupplierMsg = " expired suppliers found. \t on file: " . __CLASS__ . " \tline no :" . __LINE__;
        $expiredSupplierMsg = count($this->expiredSuppliers) . $expireSupplierMsg ;
        $this->insertToLogTb($expiredSupplierMsg);

        $usersSetup = NotificationUser::getUsers($this->comScenarioId);
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
                case 3: //Supplier
                    $this->toSupplier();
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


    public function expiryTable($data)
    {

        $body = '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">Supplier Name</th>
                        <th style="text-align: center;border: 1px solid black;">Expired Date</th>
                         <th style="text-align: center;border: 1px solid black;">Registration Number</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';

        $x = 1;
        foreach ($data as $row) {

            $body .= '<tr>
                <td style="text-align:center;border: 1px solid black;">' . $row['supplierName'] . '</td>  
                <td style="text-align:center;border: 1px solid black;">' . date('Y-m-d', strtotime($row['registrationExprity'])) . '</td> 
                <td style="text-align:center;border: 1px solid black;">' . $row['registrationNumber'] . '</td>                 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }


     public function expiryTableSuppliers($data)
    {

        $body = '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">Supplier Name</th>
                        <th style="text-align: center;border: 1px solid black;">Expired Date</th>
                         <th style="text-align: center;border: 1px solid black;">Registration Number</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';

  
        $body .= '<tr>
            <td style="text-align:center;border: 1px solid black;">' . $data['supplierName'] . '</td>  
            <td style="text-align:center;border: 1px solid black;">' . date('Y-m-d', strtotime($data['registrationExprity'])) . '</td> 
            <td style="text-align:center;border: 1px solid black;">' . $data['registrationNumber'] . '</td>                 
            </tr>';
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
            $mailBody .= $this->expiryTable($this->expiredSuppliers);
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

    public function toSupplier(){

        foreach ($this->expiredSuppliers as $row){

            $empEmail = $row['supEmail'] ?? null;
            if (!empty($empEmail)) {
                $supplierName = $row['supplierName'];
                $mailBody = "Hi {$supplierName},<br/>";

                $mailBody .= $this->emailBody( );
                $mailBody .= "<br/>";
                $mailBody .= $this->expiryTableSuppliers($row);
                $mailBody .= "<br/>";
                $mailBody .= "Best Regards,<br/> System Administrator,<br/> {$this->companyName}.";
                $empEmail = $row['supEmail'];
                $subject = $this->mailSubject;

                NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);

                $this->sentMailCount++;
            }
            else
            {
                $mailError = "Email is missing for supplier  {$row['supplierName']} \t on file: " . __CLASS__ . " \tline no :" . __LINE__;
                $this->insertToLogTb($mailError, 'warning');
            }


        }

        return true;
    }

    public function emailBody( ){

        $str = "<br/>";

        switch ($this->type){
            case 0: 
                $str .= "The Commercial Registration (CR) number has reached its expiration today. Please renew the CR immediately to avoid any disruption.";
                break;
            case 1: 
                $str .= "The Commercial Registration (CR) number is nearing its expiration. Please renew the CR before the expiration date to avoid any disruption.";
                break;
            case 2: 
                $str .= "The Commercial Registration (CR) number has expired. Please renew the CR as soon as possible to avoid any disruption.";
                break;
        }
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
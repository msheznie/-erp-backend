<?php

namespace App\helper;

use App\Models\HREmpContractHistory;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationUser;
use App\Models\SrpEmployeeDetails;
use App\Models\SrpErpPayShiftEmployees;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ShiftPeriodEndNotificationService
{
    private $company;
    private $comScenarioID;
    private $type;
    private $days;
    private $mail_subject = "Employee shift period end reminder";
    private $debug = false;
    private $sent_mail_count = 0;


    public function __construct($company, $setup){
        [ 'companyScenarionID' => $comScenarioID, 'beforeAfter' => $type, 'days' => $days ] = $setup;

        $this->company = $company;
        $this->comScenarioID = $comScenarioID;
        $this->type = $type;
        $this->days = $days;
    }

    public function ended_shift(){
        $this->expiry_date = NotificationService::get_filter_date($this->type, $this->days);

        $data = SrpErpPayShiftEmployees::where('companyID', $this->company);
        $data = $data->with(['employee'=> function($q){
            $q->where('isDischarged', 0);
        }]);
        $data = $data->with('shift_master');
        $data = $data->where('endDate', $this->expiry_date);
        $data = $data->get();

        if(count($data) == 0){
            return'no shift period';
            $log = "Employee shift period does not exist for type: {$this->type} and days: {$this->days}";
            $log .= "\t on file: " . __CLASS__ ." \tline no :".__LINE__;

            if($this->debug){ echo "<pre>$log</pre>";}

            Log::error($log);

            return false;
        }

        $users_setup = NotificationUser::get_notification_users_setup($this->comScenarioID);
        if(count($users_setup) == 0){
            Log::error("User's not configured for Employee shift period end reminder. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

            foreach ($users_setup as $row){
                $mail_to = $row->empID;
                switch ($row->applicableCategoryID) {
                    case 1: //Employee
                        $this->to_specific_employee($mail_to, $data);
                    break;

                    case 7: //Reporting manager
                        $this->to_reporting_manager($data);
                    break;

                    case 9: //Applicable Employee
                        $this->to_document_owner($data);
                    break;

                    default:
                        Log::error("Unknown Applicable Category \t on file: " . __CLASS__ ." \tline no :".__LINE__);
                }
            }
        

        Log::info( $this->sent_mail_count. " Employee shift period end reminder mails send \t on file: " . __CLASS__ ." \tline no :".__LINE__ );

        return true;
    }

    public function to_specific_employee($mail_to_emp , $data){

        $mail_to = SrpEmployeeDetails::selectRaw('Ename2, EEmail')->find( $mail_to_emp );

        if(empty($mail_to)){
            Log::error("Employee Not found \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

        $mail_body = "Dear {$mail_to->Ename2},<br/>";
        $mail_body .= $this->email_body(1 );
        $mail_body .= $this->expiry_table($data);

        $empEmail = $mail_to->EEmail;
        $subject = $this->mail_subject;

        NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

        $this->sent_mail_count++;

        if($this->debug){
            echo '<br/><h3> to_specific_employee line no '. __LINE__ .' </h3><br/>';
            echo $mail_body;
        }

        return true;
    }

    public function to_document_owner($data){
        $data = collect( $data )->groupBy('empID')->toArray();
        foreach ($data as $row){

            $mail_to = $row[0]['employee'];

            $mail_body = "Dear {$mail_to['Ename2']},<br/>";
            $mail_body .= $this->email_body(9 );


            $empEmail = $mail_to['EEmail'];
            $subject = $this->mail_subject;

            NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

            $this->sent_mail_count++;
        }

        if($this->debug){
            echo '<br/> <h3>to_document_owner line no '. __LINE__.'</h3> <br/>';
        }

        return true;
    }

    public function to_reporting_manager($data){
        $employeeIds = [];
        foreach($data as $a){
            $employeeIds[] = $a->empID;
        }

        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->whereIn('empID', $employeeIds)
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail')
            ->get();

        if(count($manager) == 0){
            Log::error("Manager details not found for shift period expiry. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

        if($this->debug){
            echo '<pre> <h3>Manager :</h3>'; print_r($manager->toArray()); echo '</pre>';
        }

        $manager = collect( $manager->toArray() )->groupBy('managerID')->toArray();

        $mail_body_str = '';

        foreach ($manager as $row){
            $employeeID = collect($row)->pluck('empID')->toArray();

            $data = SrpErpPayShiftEmployees::where('companyID', $this->company);
            $data = $data->with(['employee'=> function($q){
                $q->where('isDischarged', 0);
            }]);
            $data = $data->with('shift_master');
            $data = $data->where('endDate', $this->expiry_date);
            $data = $data->whereIn('empID', $employeeID);
            $data = $data->get();


            $manager_info = $row[0]['info'];
            $mail_body = "Dear {$manager_info['Ename2']},<br/>";
            $mail_body .= $this->email_body(7 );
            $mail_body .= $this->expiry_table($data);


            $empEmail = $manager_info['EEmail'];
            $subject = $this->mail_subject;

            NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

            $this->sent_mail_count++;

            if($this->debug) { $mail_body_str .= '<br/><br/>' . $mail_body; }
        }

        if($this->debug){
            echo '<br/> <h3>to_reporting_manager line no '. __LINE__.'</h3> <br/>';
            echo $mail_body_str;
        }

        return true;
    }

    public function email_body( $for ){

        $str = "<br/>";

        switch ($for){
            case 1: //Employee
                $str .= "Employee shift period details are as follows";
                break;

            case 7: //Reporting manager
                $str .= "Shift period of your reporting employees' details are as follows";
                break;

            case 9: //Applicable Employee
                $str .= "Your shift period details are as follows";
                break;
        }
        $str .= ".<br/><b> Expiry date </b> : " . $this->expiry_date;
        $str .= ' ( '. Carbon::parse( $this->expiry_date )->diffForHumans() . " ) <br/><br/><br/>";

        return $str;
    }

    public function expiry_table($data)
    {
        
        $body = '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        <th style="text-align: center;border: 1px solid black;">Employee Code</th>
                        <th style="text-align: center;border: 1px solid black;">Employee Name</th>                                            
                        <th style="text-align: center;border: 1px solid black;">Employee Email</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';

        $x = 1;
        foreach ($data as $row) {
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>
                <td style="text-align:left;border: 1px solid black;">' . $row['employee']['ECode'] . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $row['employee']['Ename2'] . '</td>                 
                <td style="text-align:left;border: 1px solid black;">' . $row['employee']['EEmail'] . '</td>                 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }

}

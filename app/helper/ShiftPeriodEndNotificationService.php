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
            Log::error("User's not configured for Expiry HR contract. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

            foreach ($users_setup as $row){

                $payShiftEmployee = SrpErpPayShiftEmployees::where('companyID', $this->company)
                                                                ->where('empID', $row->empID)
                                                                ->first();
                $endDate = $payShiftEmployee['endDate'];
                $currentDate = Carbon::now();
    
                $endDate = date('Y-m-d', strtotime($endDate));
                $currentDate = date('Y-m-d', strtotime($currentDate));
    
                if($endDate == $currentDate){
                    $mail_to = $row->empID;
                    switch ($row->applicableCategoryID) {
                        case 1: //Employee
                            $this->to_specific_employee($mail_to);
                        break;
    
                        case 7: //Reporting manager
                            $this->to_reporting_manager($mail_to);
                        break;
    
                        case 9: //Applicable Employee
                            $this->to_document_owner($mail_to);
                        break;
    
                        default:
                            Log::error("Unknown Applicable Category \t on file: " . __CLASS__ ." \tline no :".__LINE__);
                    }
                }

            }
        

        Log::info( $this->sent_mail_count. " Employee shift period end reminder mails send \t on file: " . __CLASS__ ." \tline no :".__LINE__ );

        return true;
    }

    public function to_specific_employee($mail_to_emp){
        $mail_to = SrpEmployeeDetails::selectRaw('Ename2, EEmail')->find( $mail_to_emp );

        if(empty($mail_to)){
            Log::error("Employee Not found \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

        $mail_body = "Dear {$mail_to->Ename2},<br/>";
        $mail_body .= $this->email_body(1 );
        // $mail_body .= $this->expiry_table($this->expired_docs);

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

    public function to_document_owner($mail_to_emp){
        $mail_to = SrpEmployeeDetails::selectRaw('Ename2, EEmail')->find( $mail_to_emp );

        if(empty($mail_to)){
            Log::error("Employee Not found \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

        $mail_body = "Dear {$mail_to->Ename2},<br/>";
        $mail_body .= $this->email_body(1 );
        // $mail_body .= $this->expiry_table($this->expired_docs);

        $empEmail = $mail_to->EEmail;
        $subject = $this->mail_subject;

        NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

        $this->sent_mail_count++;

        if($this->debug){
            echo '<br/> <h3>to_document_owner line no '. __LINE__.'</h3> <br/>';
            echo $mail_body;
        }

        return true;
    }

    public function to_reporting_manager($mail_to){

        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->whereIn('empID', $mail_to)
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail')
            ->get();

        if(count($manager) == 0){
            Log::error("Manager details not found for expiry employee contract. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

        if($this->debug){
            echo '<pre> <h3>Manager :</h3>'; print_r($manager->toArray()); echo '</pre>';
        }

        $manager = collect( $manager->toArray() )->groupBy('managerID')->toArray();
        $mail_body_str = '';

        foreach ($manager as $row){
            $manager_info = $row[0]['info'];

            $mail_body = "Dear {$manager_info['Ename2']},<br/>";
            $mail_body .= $this->email_body(7 );

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
                $str .= "your employee Shift expired today";
                break;

            case 7: //Reporting manager
                $str .= "Shift of your reporting manager expired today";
                break;

            case 9: //Applicable Employee
                $str .= "Your Shift expired today";
                break;
        }
        return $str;
    }

}

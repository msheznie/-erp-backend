<?php

namespace App\helper;

use App\Models\HREmpContractHistory;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationUser;
use App\Models\SrpEmployeeDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HRContractNotificationService
{
    private $company;
    private $comScenarioID;
    private $type;
    private $days;
    private $expiry_date;
    private $expired_docs = [];
    private $mail_subject = "Employee contract expiry remainder";
    private $debug = false;
    private $sent_mail_count = 0;


    public function __construct($company, $setup){
        [ 'companyScenarionID' => $comScenarioID, 'beforeAfter' => $type, 'days' => $days ] = $setup;

        $this->company = $company;
        $this->comScenarioID = $comScenarioID;
        $this->type = $type;
        $this->days = $days;
    }

    public function expired_doc(){
        $this->expiry_date = NotificationService::get_filter_date($this->type, $this->days);

        $data = HREmpContractHistory::selectRaw('empID, contactTypeID, contractEndDate, contractRefNo')
            ->where('companyID', $this->company)
            ->whereDate('contractEndDate', $this->expiry_date)
            ->where('isCurrent', 1);

        $data = $data->whereHas('contract_type', function ($q){
            $q->where('typeID', 2)
                ->where('Erp_CompanyID', $this->company);
        });

        $data = $data->with(['contract_type'=> function ($q){
            $q->selectRaw('EmpContractTypeID, Description')
                ->where('typeID', 2)
                ->where('Erp_CompanyID', $this->company);
        }]);

        $data = $data->whereHas('employee', function($q){
            $q->where('isDischarged', 0);
        });
        $data = $data->with(['employee'=> function($q){
            $q->selectRaw('EIdNo,ECode,Ename2,EEmail')
                ->where('isDischarged', 0);
        }]);

        $data = $data->get();

        if(count($data) == 0){
            $log = "Expiry HR contract does not exist for type: {$this->type} and days: {$this->days}";
            $log .= "\t on file: " . __CLASS__ ." \tline no :".__LINE__;

            if($this->debug){ echo "<pre>$log</pre>";}

            Log::error($log);

            return false;
        }

        $this->expired_docs = $data->toArray();

        Log::info( count($this->expired_docs)." expired contracts found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);

        $users_setup = NotificationUser::get_notification_users_setup($this->comScenarioID);
        if(count($users_setup) == 0){
            Log::error("User's not configured for Expiry HR contract. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }

        if($this->debug){
            echo "<pre> <h3>Expired Documents ( days: {$this->days} )</h3>";
            print_r($this->expired_docs); echo '</pre>';
        }

        foreach ($users_setup as $row){

            switch ($row->applicableCategoryID) {
                case 1: //Employee
                    $mail_to = $row->empID;
                    $this->to_specific_employee($mail_to);
                break;

                case 7: //Reporting manager
                    $this->to_reporting_manager();
                break;

                case 9: //Applicable Employee
                    $this->to_document_owner();
                break;

                default:
                    Log::error("Unknown Applicable Category \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            }

        }


        Log::info( $this->sent_mail_count. " expired employee contract mails send \t on file: " . __CLASS__ ." \tline no :".__LINE__ );

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
        $mail_body .= $this->expiry_table($this->expired_docs);

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

    public function to_document_owner(){
        $data = collect( $this->expired_docs )->groupBy('empID')->toArray();

        $mail_body_str = '';
        foreach ($data as $row){

            $mail_to = $row[0]['employee'];

            $mail_body = "Dear {$mail_to['Ename2']},<br/>";
            $mail_body .= $this->email_body(9 );
            $mail_body .= $this->expiry_table($row, true);


            $empEmail = $mail_to['EEmail'];
            $subject = $this->mail_subject;

            NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

            $this->sent_mail_count++;

            if($this->debug) { $mail_body_str .= '<br/><br/>' . $mail_body; }
        }

        if($this->debug){
            echo '<br/> <h3>to_document_owner line no '. __LINE__.'</h3> <br/>';
            echo $mail_body_str;
        }

        return true;
    }

    public function to_reporting_manager(){
        $emp_list = array_column($this->expired_docs, 'empID');
        $emp_list = array_unique($emp_list);

        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->whereIn('empID', $emp_list)
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

        $emp_wise_docs = collect( $this->expired_docs )->groupBy('empID')->toArray();

        $mail_body_str = '';

        foreach ($manager as $row){
            $manager_info = $row[0]['info'];

            $my_reporting_data = collect([]);
            foreach ($row as $rpt){
                $this_emp = $rpt['empID'];

                if( array_key_exists($this_emp, $emp_wise_docs)){
                    $my_reporting_data = $my_reporting_data->concat( $emp_wise_docs[$this_emp] );
                }
            }

            $my_reporting_data = $my_reporting_data->toArray();

            $mail_body = "Dear {$manager_info['Ename2']},<br/>";
            $mail_body .= $this->email_body(7 );
            $mail_body .= $this->expiry_table( $my_reporting_data );

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
                $str .= "Employee contract expiry details as follow";
                break;

            case 7: //Reporting manager
                $str .= "Contract of your reporting employees expiry details as follow";
                break;

            case 9: //Applicable Employee
                $str .= "Your contract expiry details as follow";
                break;
        }

        $str .= ".<br/><b> Expiry date </b> : " . $this->expiry_date;
        $str .= ' ( '. Carbon::parse( $this->expiry_date )->diffForHumans() . " ) <br/><br/><br/>";

        return $str;
    }

    public function expiry_table($data, $is_owner=false)
    {
        $emp_column = (!$is_owner)? '<th style="text-align: center;border: 1px solid black;">Employee</th>': '';

        $body = '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        '.$emp_column.'
                        <th style="text-align: center;border: 1px solid black;">Contract type</th>
                        <th style="text-align: center;border: 1px solid black;">Contract no</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';

        $x = 1;
        foreach ($data as $row) {
            $emp_name = '';

            if(!$is_owner){
                $emp_name = $row['employee']['ECode'] .' | '. $row['employee']['Ename2'];
                $emp_name = '<td style="text-align:left;border: 1px solid black;">' . $emp_name . '</td>';
            }

            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                '.$emp_name.'
                <td style="text-align:left;border: 1px solid black;">' . $row['contract_type']['Description'] . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $row['contractRefNo'] . '</td>                 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }
}

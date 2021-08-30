<?php

namespace App\helper;

use App\Models\HRDocumentDescriptionForms;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationUser;
use App\Models\SMEEmpContractType;
use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\Log;

class HRNotificationService
{
    private $company;
    private $comScenarioID;
    private $type;
    private $days;
    private $expired_docs = [];
    private $mail_subject = "HR document expiry remainder";


    public function __construct($company, $setup){
        [ 'companyScenarionID' => $comScenarioID, 'beforeAfter' => $type, 'days' => $days ] = $setup;

        $this->company = $company;
        $this->comScenarioID = $comScenarioID;
        $this->type = $type;
        $this->days = $days;
    }

    public function emp_expired_docs()
    {
        $expiry_date = NotificationService::get_filter_date($this->type, $this->days);

        $expired_docs = HRDocumentDescriptionForms::selectRaw('DocDesID, PersonID, documentNo, expireDate, Erp_companyID')
            ->where('Erp_companyID', $this->company)
            ->where('PersonType', 'E')
            ->where('isDeleted', 0)
            ->whereDate('expireDate', $expiry_date);

        $expired_docs = $expired_docs->whereHas('master');
        $expired_docs = $expired_docs->whereHas('employee');

        $expired_docs = $expired_docs->with('master:DocDesID,DocDescription');
        $expired_docs = $expired_docs->with('employee:EIdNo,ECode,Ename2,EEmail');


        $expired_docs = $expired_docs->get();

        if(empty($expired_docs)){
            $log = "Expiry HR documents does not exist for type: {$this->type} and days: {$this->days}";
            $log .= "\t on class: " . __CLASS__ ." \tline no :".__LINE__;
            Log::error($log);
            return [];
        }

        $users_setup = NotificationUser::get_notification_users_setup($this->comScenarioID);
        if(empty($users_setup)){
            Log::error("User's not configured for Expiry HR documents. \t on class: " . __CLASS__ ." \tline no :".__LINE__);
            return [];
        }

        $expired_docs = $expired_docs->toArray();

        $this->expired_docs = $expired_docs;

        //dd($expired_docs);
        //dd( $users_setup->toArray() );
        foreach ($users_setup as $row){
            switch ($row->applicableCategoryID){
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
                    Log::error("Unknown Applicable Category \t on class: " . __CLASS__ ." \tline no :".__LINE__);
            }
        }

        return true;
    }

    public function to_specific_employee($mail_to_emp){
        $mail_to = SrpEmployeeDetails::selectRaw('Ename2, EEmail')->find( $mail_to_emp );

        if(empty($mail_to)){
            Log::error("Employee Not found \t on class: " . __CLASS__ ." \tline no :".__LINE__);
            return true;
        }

        $mail_body = "Dear {$mail_to->Ename2},<br/>";
        $mail_body .= $this->email_body($this->expired_docs);

        $empEmail = $mail_to->EEmail;
        $subject = $this->mail_subject;

        NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

        //echo $mail_body; exit;

        return true;
    }

    public function to_document_owner(){
        $data = collect( $this->expired_docs )->groupBy('PersonID')->toArray();

        $mn = '';
        foreach ($data as $row){

            $mail_to = $row[0]['employee'];

            $mail_body = "Dear {$mail_to['Ename2']},<br/>";
            $mail_body .= $this->email_body($row, true);


            $empEmail = $mail_to['EEmail'];
            $subject = $this->mail_subject;

            NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

            $mn .= '<br/><br/>'. $mail_body;
        }

        return '';
    }

    public function to_reporting_manager(){
        $emp_list = array_column($this->expired_docs, 'PersonID');
        $emp_list = array_unique($emp_list);

        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->whereIn('empID', $emp_list)
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail')
            ->get();

        if(empty($manager)){
            Log::error("Manager details not found for Expiry HR documents. \t on class: " . __CLASS__ ." \tline no :".__LINE__);
            return [];
        }

        $manager = collect( $manager->toArray() )->groupBy('managerID')->toArray();

        $emp_wise_docs = collect( $this->expired_docs )->groupBy('PersonID')->toArray();

        $s = '';
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
            $mail_body .= $this->email_body( $my_reporting_data );

            $empEmail = $manager_info['EEmail'];
            $subject = $this->mail_subject;

            NotificationService::emailNotification($this->company, $subject, $empEmail, $mail_body);

            //$s .= '<br/><br/>' .$mail_body;
        }

        //echo $s; exit;
    }

    public function email_body($data, $is_owner=false)
    {
        $emp_column = (!$is_owner)? '<th style="text-align: center;border: 1px solid black;">Employee</th>': '';

        $body = $this->email_title();

        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        '.$emp_column.'
                        <th style="text-align: center;border: 1px solid black;">Document Type</th>
                        <th style="text-align: center;border: 1px solid black;">Document no</th>                        
                    </tr>
                </thead>';
        $body .= '<tbody>';

        $x = 1;
        foreach ($data as $row) {
            $emp_name = '';

            if(!$is_owner){
                $employee = $row['employee'];
                $emp_name = $employee['ECode'] .' | '. $employee['Ename2'];
                $emp_name = '<td style="text-align:left;border: 1px solid black;">' . $emp_name . '</td>';
            }

            $document = $row['master'];

            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                '.$emp_name.'
                <td style="text-align:left;border: 1px solid black;">' . $document['DocDescription'] . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $row['documentNo'] . '</td>                 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }

    public function email_title(){

        $str = "Following HR documents ";

        switch ($this->type){
            case 0: //same day
                $str .= " expire today.<br/><br/>";
                break;

            case 1: //before
                $str .= "will be expired in {$this->days} day/s.<br/><br/>";
                break;

            case 2: // after
                $str .= "had expired {$this->days} day/s ago.<br/><br/>";
                break;
        }

        return $str;
    }

    public static function emp_contract_docs($company, $type, $days){
        $expiry_date = NotificationService::get_filter_date($type, $days);

        $data = SMEEmpContractType::selectRaw('EmpContractTypeID, Description')
            ->where('typeID', 2)
            ->where('Erp_CompanyID', $company);

        $data = $data->whereHas('emp_contract', function ($q) use ($company, $expiry_date){
            $q->where('companyID', $company)
                ->whereDate('contractEndDate', $expiry_date)
                ->where('isCurrent', 1);
        });

        $data = $data->with(['emp_contract'=> function ($q) use ($company, $expiry_date){
            $q->where('companyID', $company)
                ->whereDate('contractEndDate', $expiry_date)
                ->where('isCurrent', 1);
        }]);

        return $data->get();
    }

}

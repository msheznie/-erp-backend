<?php

namespace App\helper;

use App\Models\HREmpContractHistory;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationUser;
use App\Models\SrpEmployeeDetails;
use App\Models\SrpErpPayShiftEmployees;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DepartmentEndDateExpiryNotificationService
{

    private $company;
    private $comScenarioId;
    private $type;
    private $days;
    private $mailSubject = "Department End date Expiry Notification";
    private $debug = false;
    private $sentMailCount = 0;
    private $expiryDate = null;
    private $expiredDocs = [];

    public function __construct($company, $setup){
        [ 'companyScenarionID' => $comScenarioId, 'beforeAfter' => $type, 'days' => $days ] = $setup;

        $this->company = $company;
        $this->comScenarioId = $comScenarioId;
        $this->type = $type;
        $this->days = $days;
    }

    public function proceed()
    {
        $this->expiryDate = NotificationService::get_filter_date($this->type, $this->days);

        /*$expiredDepartments = HRDocumentDescriptionForms::
        selectRaw('DocDesFormID,DocDesID, PersonID, documentNo, expireDate, Erp_companyID')
            ->where('Erp_companyID', $this->company)
            ->where('PersonType', 'E')
            ->where('isDeleted', 0)
            ->whereDate('expireDate', $this->expiryDate);

        $expiredDepartments = $expiredDepartments->whereHas('master');
        $expiredDepartments = $expiredDepartments->whereHas('employee', function($q){
            $q->where('isDischarged', 0);
        });

        $expiredDepartments = $expiredDepartments->with('master:DocDesID,DocDescription');
        $expiredDepartments = $expiredDepartments->with(['employee'=>  function($q){
            $q->selectRaw('EIdNo,ECode,Ename2,EEmail')
                ->where('isDischarged', 0);
        }]);


        $expiredDepartments = $expiredDepartments->get();*/

        $expiredDepartments[] = [];
        if(count($expiredDepartments) == 0){
            $log = "Expiry Department does not exist for type: {$this->type} and days: {$this->days}";
            $log .= "\t on file: " . __CLASS__ ." \tline no :".__LINE__;

            if($this->debug){ echo "<pre>$log</pre>";}

            Log::error($log);
            return false;
        }

       /* $expiredDepartments = $expiredDepartments->toArray();
        $this->expireDepartments = $expiredDepartments;*/

        Log::info( count($expiredDepartments)." expired depatment found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);

        $usersSetup = NotificationUser::get_notification_users_setup($this->comScenarioId);
        if(count($usersSetup) == 0){
            if($this->debug){
                echo "<pre>User's not configured for Expiry Department. \t on file: {__CLASS__} \tline no : {__LINE_} </pre>";
            }

            $ShortMessage = "User's not configured for Department End Date Expiry. \t on file: ";
            Log::error($ShortMessage . __CLASS__ ." \tline no :".__LINE__);
            return false;
        }


        if($this->debug){
            echo '<pre> <h3>Expired Documents</h3>'; print_r($expiredDepartments); echo '</pre>';
        }

        foreach ($usersSetup as $row){
            switch ($row->applicableCategoryID){
                case 1: //Employee
                    $mailTo = $row->empID;
                    //$this->toSpecificEmployee($mailTo);
                    break;

               /* case 7: //Reporting manager
                    $this->toReportingManager();
                    break;

                case 9: //Applicable Employee
                    $this->toDocumentOwner();
                    break;*/

                default:
                    Log::error("Unknown Applicable Category \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            }
        }

        $mailShortMessage = " expired Department document mails send \t on file: ";
        Log::info( $this->sentMailCount. $mailShortMessage . __CLASS__ ." \tline no :".__LINE__ );

        return true;
    }

}
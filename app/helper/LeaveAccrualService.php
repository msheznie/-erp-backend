<?php

namespace App\helper;

use App\Models\CompanyFinanceYear;
use App\Models\LeaveAccrualDetail;
use App\Models\LeaveAccrualMaster;
use App\Models\LeaveGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveAccrualService
{
    public $company_id;
    public $company_code;
    public $company_name;
    public $date;
    public $date_time;
    private $debug = false;

    public $leave_groups= [];
    public $emp_arr= [];
    public $pending_accruals = [];
    public $header_data = [];
    public $finance_year;
    public $accrualMasterID = null;

    public function __construct($company_data, $header_data)
    {
        $this->company_id = $company_data['id'];
        $this->company_code = $company_data['code'];
        $this->company_name = $company_data['name'];
        $this->date_time = Carbon::now();
        $this->date = $this->date_time->format('Y-m-d');


        if($header_data){
            $this->header_data = $header_data;
        }
    }

    function prepare_for_accrual(){
        $status = $this->get_leave_group_details();
        if(!$status){ return false; }

        foreach ($this->leave_groups as $group){
            $group_id = $group['leaveGroupID'];

            $status = $this->get_employee_list($group_id, true, 1);

            if($status){
                $this->pending_accruals[] = $group;
            }
        }

        Log::info( $this->pending_accruals );

        return $this->pending_accruals;
    }

    function get_leave_group_details(){
        $leave_groups = LeaveGroup::get_leave_group_details($this->company_id, 1, true);

        if($this->debug){
            echo '<pre>'; print_r($leave_groups->toArray()); echo '</pre>';
        }

        if($leave_groups->count() == 0){
            Log::error("Annual daily Basis leave types not available on ". $this->log_suffix());
            return false;
        }

        $this->leave_groups = $leave_groups->toArray();

        return true;
    }

    function get_employee_list($leaveGroupID, $getCount, $dailyAccrualYN): bool
    {

        $year_date_filter = ""; // "company_finance_year_id = {$financeYearID}";
        if($dailyAccrualYN == 1){
            $year_date_filter = "dailyAccrualDate = '{$this->date}'";
        }

        $str = "EIdNo, ECode, Ename2, emp.leaveGroupID, leaveTypeID, noOfDays";
        if($getCount){
            $str = "COUNT(EIdNo) AS emp_count";
        }

        //when preparing for the data no need to check with a master id,
        //but when creating the details we have to check this
        //condition
        $master_id_filter = ($this->accrualMasterID) ? " AND mas.leaveaccrualMasterID != $this->accrualMasterID": '';

        $sql = "SELECT {$str}
            FROM srp_employeesdetails AS emp
            JOIN (
                SELECT * FROM srp_erp_leavegroupdetails WHERE leaveGroupID = {$leaveGroupID} 
                AND policyMasterID = 1 AND isDailyBasisAccrual = {$dailyAccrualYN}
            ) AS grp ON grp.leaveGroupID = emp.leaveGroupID 
            WHERE NOT EXISTS ( 
                SELECT * FROM srp_erp_leaveaccrualdetail AS det
                JOIN srp_erp_leaveaccrualmaster AS mas ON mas.leaveaccrualMasterID = det.leaveaccrualMasterID
                WHERE emp.EIdNo = empID AND det.leaveGroupID = {$leaveGroupID} AND {$year_date_filter}
                {$master_id_filter}
                GROUP BY empID
            ) AND emp.leaveGroupID = {$leaveGroupID} AND isDischarged != 1 AND Erp_companyID={$this->company_id}";

        $emp_arr = DB::select($sql);

        if($getCount) {
            $count = $emp_arr[0]->emp_count;
            Log::info("{$count} employees found for the accrual ". $this->log_suffix());
            return ($count > 0);
        }

        if(empty($emp_arr)){
            return false;
        }

        $this->emp_arr = $emp_arr;

        return true;

    }

    function create_accrual(){

        DB::beginTransaction();
        try {

            $master_data = $this->create_header();
            if(!$master_data['status']){
                Log::error( $master_data['message']. $this->log_suffix() );
                return false;
            }

            $group_id = $this->header_data['leaveGroupID'];
            $status = $this->get_employee_list($group_id, false, 1);

            if(!$status){
                Log::error("Employees not found for the accrual ". $this->log_suffix());

                DB::rollBack();
                return false;
            }

            $this->create_details();

            DB::commit();

            Log::info("successfully created the accrual [ ".$master_data['doc_code']. " ]" . $this->log_suffix());

            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            Log::error($message. $this->log_suffix());
            return false;
        }
    }

    function create_header(){
        $finance_year = CompanyFinanceYear::active_finance_year($this->company_id, $this->date);

        if(empty($finance_year)){
            return ['status'=> false, 'message'=> 'Company finance year details not found'];
        }

        $this->finance_year = $finance_year;

        $doc_code = HrDocumentCodeService::generate($this->company_id, $this->company_code, 'LAM');

        $data = [
            'companyID' => $this->company_id,
            'leaveaccrualMasterCode' => $doc_code,
            'documentID' => 'LAM',
            'description' => "System automate daily accrual",
            'dailyAccrualYN' => 1,
            'dailyAccrualDate' => $this->date,
            'year' => Carbon::parse( $this->date )->format('Y'),
            'month' => Carbon::parse( $this->date )->format('m'),
            'leaveGroupID' => $this->header_data['leaveGroupID'],
            'createdpc' => gethostname(),
            'policyMasterID' => 1,
            'company_finance_year_id'=> $finance_year['companyFinanceYearID'],
            'createdUserGroup' => '',
            'createdUserID' => 11, //system admin
            'confirmedby' => 11, //system admin
            'confirmedYN' => 1,
            'confirmedDate' => $this->date_time,
        ];

        $master = LeaveAccrualMaster::create($data);

        $this->accrualMasterID = $master->leaveaccrualMasterID;

        return [
            'status'=> true,
            'doc_code'=> $doc_code,
        ];

    }

    function create_details($dailyAccrualYN=true){
        $group_id = $this->header_data['leaveGroupID'];
        $financeYear = Carbon::parse( $this->finance_year['startDate'] )->format('Y');
        $financeYearEnd = Carbon::parse( $this->finance_year['endDate'] )->format('Y');


        $detail = array();

        foreach ($this->emp_arr as $val) {
            $hoursEntitled = 0;
            $daysEntitled = $val->noOfDays;

            if($dailyAccrualYN == 1){
                $daysEntitled = ($daysEntitled / 365);
            }


            $detail[] = array(
                'leaveaccrualMasterID' => $this->accrualMasterID,
                'empID' => $val->EIdNo,
                'leaveGroupID' => $group_id,
                'leaveType' => $val->leaveTypeID,
                'daysEntitled' => $daysEntitled,
                'hoursEntitled' => $hoursEntitled,
                'description' => 'Leave Accrual ' . $financeYear,
                'initalDate' => $financeYear,
                'nextDate' => $financeYearEnd,
                'createDate' => $this->date_time,
                'createdPCid' => gethostname()
            );

        }

        if (!empty($detail)) {
            LeaveAccrualDetail::insert($detail);
        }

    }

    function log_suffix(){
        $debugTrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $line_no = $debugTrace[0]['line'];

        return " $this->company_code | $this->company_name \t on file:  " . __CLASS__ ." \tline no : {$line_no}";
    }

}

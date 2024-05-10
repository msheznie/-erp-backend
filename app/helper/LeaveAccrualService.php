<?php

namespace App\helper;

use App\Models\CompanyFinanceYear;
use App\Models\LeaveAccrualDetail;
use App\Models\LeaveAccrualMaster;
use App\Models\LeaveGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\SME;
use App\helper\LeaveBalanceValidationHelper;


class LeaveAccrualService
{
    public $company_id;
    public $company_code;
    public $company_name;
    public $policy;
    public $dailyBasis;
    public $date;
    public $date_time;
    private $debug = false;

    public $leave_groups= [];
    public $emp_arr= [];
    public $pending_accruals = [];
    public $header_data = [];
    public $finance_year;
    public $accrualMasterID = null;
    public $year_det;
    public $month_det;
    public $accrualType;

    public function __construct($company_data, $accrual_type_det, $header_data)
    {
        $this->company_id = $company_data['id'];
        $this->company_code = $company_data['code'];
        $this->company_name = $company_data['name'];
        $this->policy = $accrual_type_det['policy'];
        $this->dailyBasis = $accrual_type_det['dailyBasis'];
        $this->date_time = Carbon::now();
        $this->date = $this->date_time->format('Y-m-d');
        $this->accrualType = $accrual_type_det['description'];

        if($header_data){
            $this->header_data = $header_data;
        }
    }

    function prepare_for_accrual(){
        $status = $this->get_leave_group_details();
        if (!$status) {
            return [];
        }

        if(!$this->dailyBasis){
            $leaveBalanceBasedOn = LeaveBalanceValidationHelper::validate($this->company_id,$this->date);

            if(!$leaveBalanceBasedOn['status']){
                Log::error($leaveBalanceBasedOn['message']." ".$this->log_suffix());
                return [];
            }
            $this->year_det = $leaveBalanceBasedOn['details'];
        }

        foreach ($this->leave_groups as $group){


            $status = $this->get_employee_list($group['leaveGroupID'], $group['description'], true);
            if($status){
                $this->pending_accruals[] = $group;
            }
        }

        if($this->debug){
            Log::info( $this->pending_accruals );
        }

        return $this->pending_accruals;
    }

    function get_leave_group_details(){
        $leave_groups = LeaveGroup::get_leave_group_details($this->company_id, $this->policy, $this->dailyBasis);

        if($this->debug){
            echo '<pre>'; print_r($leave_groups->toArray()); echo '</pre>';
        }

        if($leave_groups->count() == 0){
            Log::error($this->accrualType ." leave types not available on ". $this->log_suffix());
            return false;
        }

        $this->leave_groups = $leave_groups->toArray();

        return true;
    }

    function get_employee_list($leaveGroupID, $leave_group, $getCount): bool
    {
        $str = "EIdNo, ECode, Ename2, emp.leaveGroupID, gd.leaveTypeID, noOfDays";
        if($getCount){
            $str = "COUNT(EIdNo) AS emp_count";
        }

        //when preparing for the data no need to check with a master id,
        //but when creating the details we have to check this condition
        $master_id_filter = ($this->accrualMasterID) ? " AND m.leaveaccrualMasterID != $this->accrualMasterID": '';

        if($this->policy == 1){
            $sql = $this->pending_sql_annual($str, $leaveGroupID, $master_id_filter);
        }
        else{ // $this->policy == 3 ( monthly )
            $sql = $this->pending_sql_monthly($str, $leaveGroupID, $master_id_filter);
        }

        $emp_arr = DB::select($sql);

        if($getCount) {
            $count = $emp_arr[0]->emp_count;
            if($this->debug) {
                Log::info("{$count} employees found for {$leave_group} (leave group) ". $this->log_suffix());
            }
            return ($count > 0);
        }

        if(empty($emp_arr)){
            return false;
        }

        $this->emp_arr = $emp_arr;

        return true;

    }

    function pending_sql_annual($str, $leaveGroupID, $master_id_filter): string
    {
        $dailyBasisYN = $this->dailyBasis ? 1 : 0;

        $yearDateFilter = "m.company_finance_year_id = ".$this->year_det['id'];
        if ($this->year_det['accrualPolicyValue'] == 2){
            $year = Carbon::parse( $this->date )->format('Y');
            $yearDateFilter = " m.year = '{$year}' ";
        }

        if ($dailyBasisYN == 1) {
            $yearDateFilter = "m.dailyAccrualDate = '{$this->date}'";
        }
        return "SELECT {$str}
            FROM srp_employeesdetails AS emp
            JOIN (
                SELECT * FROM srp_erp_leavegroupdetails WHERE leaveGroupID = {$leaveGroupID} 
                AND policyMasterID = 1 AND isDailyBasisAccrual = {$dailyBasisYN}
            ) AS gd ON gd.leaveGroupID = emp.leaveGroupID 
            WHERE NOT EXISTS ( 
                SELECT * FROM srp_erp_leaveaccrualdetail AS det
                JOIN srp_erp_leaveaccrualmaster AS m ON m.leaveaccrualMasterID = det.leaveaccrualMasterID
                WHERE emp.EIdNo = empID AND det.leaveGroupID = {$leaveGroupID} 
                AND m.dailyAccrualYN = {$dailyBasisYN}
                AND {$yearDateFilter} {$master_id_filter}
                AND m.policyMasterID = 1 AND m.manualYN = 0        
                GROUP BY det.empID
            ) AND emp.leaveGroupID = {$leaveGroupID} AND emp.isDischarged != 1 AND emp.Erp_companyID={$this->company_id}";
    }

    function pending_sql_monthly($str, $leaveGroupID, $master_id_filter): string
    {
        $this->month_det = LeaveBalanceValidationHelper::validate_month($this->company_id,$this->date)['details'];

        $year = Carbon::parse( $this->date )->format('Y');
        $month = Carbon::parse( $this->date )->format('m');
        $lastDate = $this->month_det['dateTo'];

        $month_date_filter = "AND `year` = {$year} AND `month` = {$month}";

        if ($this->year_det['accrualPolicyValue'] == 3){
            $month_date_filter = "AND company_finance_year_id = ".$this->month_det['id'];
            
        }
        return "SELECT {$str}
            FROM srp_employeesdetails AS emp
            JOIN srp_erp_leavegroupdetails AS gd ON gd.leaveGroupID = emp.leaveGroupID AND policyMasterID = 3             
            JOIN srp_erp_leavetype ON gd.leaveTypeID = srp_erp_leavetype.leaveTypeID 
            WHERE Erp_companyID = {$this->company_id} AND isDischarged != 1 AND DateAssumed <= '{$lastDate}'  
            AND emp.leaveGroupID IS NOT NULL AND emp.leaveGroupID = {$leaveGroupID} AND
            (EIdNo, srp_erp_leavetype.leaveTypeID) NOT IN (
                SELECT empID, leaveType FROM srp_erp_leaveaccrualmaster AS m
                JOIN srp_erp_leaveaccrualdetail AS d ON m.leaveaccrualMasterID = d.leaveaccrualMasterID 
                WHERE m.policyMasterID = 3 {$month_date_filter} AND m.manualYN = 0 {$master_id_filter}
                GROUP BY empID, leaveType
            )";
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
            $leave_group = $this->header_data['description'];
            $status = $this->get_employee_list($group_id, $leave_group, false);

            if(!$status){
                Log::error("Employees not found for the accrual ". $this->log_suffix());

                DB::rollBack();
                return false;
            }

            $this->create_details();

            DB::commit();

            Log::info("successfully created the accrual [ ".$master_data['doc_code']. " ]" . $this->log_suffix());

            return true;
        }
        catch (\Exception $exception) {
            DB::rollBack();
            $message = $exception->getMessage();
            Log::error($message. $this->log_suffix());
            return false;
        }
    }

    function create_header(){

        $leaveBalanceBasedOn = LeaveBalanceValidationHelper::validate($this->company_id,$this->date);

        if(!$leaveBalanceBasedOn['status']){
            return ['status'=> false, 'message'=> $leaveBalanceBasedOn['message']];

        }
        $this->year_det = $leaveBalanceBasedOn['details'];

        $doc_code = HrDocumentCodeService::generate($this->company_id, $this->company_code, 'LAM');

        $data = [
            'companyID' => $this->company_id,
            'leaveaccrualMasterCode' => $doc_code,
            'documentID' => 'LAM',
            'description' => "System automate accrual",
            'dailyAccrualYN' => ($this->dailyBasis)? 1 : 0,
            'dailyAccrualDate' => ($this->dailyBasis)? $this->date: null,
            'year' => Carbon::parse( $this->date )->format('Y'),
            'month' => Carbon::parse( $this->date )->format('m'),
            'leaveGroupID' => $this->header_data['leaveGroupID'],
            'createdpc' => gethostname(),
            'policyMasterID' => $this->policy,
            'createdUserGroup' => '',
            'createdUserID' => 11, //system admin
            'confirmedby' => 11, //system admin
            'confirmedYN' => 1,
            'confirmedDate' => $this->date_time,
        ];

        if($this->policy == 1){
            $data['company_finance_year_id'] = $this->year_det['id'];
        }elseif($this->policy == 3){
            $this->month_det = LeaveBalanceValidationHelper::validate_month($this->company_id,$this->date)['details'];
            $data['company_finance_year_id'] = $this->month_det['id'];

        }

        $master = LeaveAccrualMaster::create($data);

        $this->accrualMasterID = $master->leaveaccrualMasterID;

        return [ 'status'=> true, 'doc_code'=> $doc_code ];
    }

    function create_details(){
        $group_id = $this->header_data['leaveGroupID'];
        $year = Carbon::parse( $this->year_det['startDate'] )->format('Y');
        $yearEnd = Carbon::parse( $this->year_det['endDate'] )->format('Y');

        $detail = [];

        foreach ($this->emp_arr as $val) {
            $hoursEntitled = 0;
            $daysEntitled = $val->noOfDays;

            if($this->dailyBasis){
                $daysEntitled = ($daysEntitled / 365);
            }

            $detail[] = array(
                'leaveaccrualMasterID' => $this->accrualMasterID,
                'empID' => $val->EIdNo,
                'leaveGroupID' => $group_id,
                'leaveType' => $val->leaveTypeID,
                'daysEntitled' => $daysEntitled,
                'hoursEntitled' => $hoursEntitled,
                'description' => 'Leave Accrual ' . $year,
                'initalDate' => $year,
                'nextDate' => $yearEnd,
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

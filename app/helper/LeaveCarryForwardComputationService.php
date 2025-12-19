<?php

namespace App\helper;

use App\Models\CompanyFinanceYear;
use App\Models\HRDocumentApproved;
use App\Models\LeaveAccrualDetail;
use App\Models\LeaveAccrualMaster; 
use App\Models\LeaveGroupDetails;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveCarryForwardComputationService
{
    public $companyId;
    public $companyCode;
    public $leaveComputationBasedOn = null;
    public $currentDate;
    public $currentDateTime;
    public $periodEndDate = null;
    public $payRollBasedData = [];
    public $financialYearBasedData = [];
    public $leaveGroups = [];
    public $leaveToAdjust = [];
    public $periodData = [];
    public $monthDet = [];
    public $leaveDetails = [];
    public $accrualMasterID = null;
    public $documentId = 'LAM';
    public $documentCode = null;
    public $periodStatus = true;

    public function __construct($companyData)
    {
        $this->companyId = $companyData['id'];
        $this->companyCode = $companyData['code'];
        $this->currentDate  = Carbon::now()->format('Y-m-d');
        $this->currentDateTime = Carbon::now()->format('Y-m-d H:m:s');
    }

    function execute()
    {
        
        $leaveComputationBasedPolicy = SME::policy($this->companyId, 'LC', 'All');
        $this->leaveComputationBasedOn =  empty($leaveComputationBasedPolicy) ? 1 : $leaveComputationBasedPolicy;
        $this->getPeriodEndDatePolicyWise();


        if(!$this->periodStatus){
            return false;
        }


        $yearEndDate = Carbon::parse($this->periodData['endDate'])->format('Y-m-d');

        if($yearEndDate != $this->currentDate){
            $this->insertToLogTb('Service execution stopped due to date validation failed'); 
            return false;
        }

        $this->insertToLogTb('Period data function triggered for ' . $this->companyCode . '');
        
        $this->getMonthWiseDate();

        if (empty($this->monthDet)) {
            $this->insertToLogTb('Leave computation policy is not set with any value', 'error');
            return;
        }

        if (empty($this->periodData['endDate'])) {
            $msg = 'Period End date not exists!';
            $this->insertToLogTb($msg, 'error');
            return;
        }

        $this->getLeaveGroupDetails();

        if (empty($this->leaveGroups)) {
            $msg = 'No Records found for the leave groups.';
            $this->insertToLogTb($msg, 'error');
            return;
        }
        
        $this->getLeaveToAdjustData();

        if (empty($this->leaveToAdjust)) {
            $msg = 'No Records found for leave to adjust.';
            $this->insertToLogTb($msg, 'error');
            return;
        } 
        $this->proceed();
    }

    function getPeriodEndDatePolicyWise()
    {
        if ($this->leaveComputationBasedOn == 3) {
            $this->payRollYearBased();
        } else if ($this->leaveComputationBasedOn == 2) {
            $this->standardYearBased();
        } else {
            $this->financialYearBased();
        } 
    }

    function payRollYearBased()
    {
        $this->payRollBasedData = DB::table('srp_erp_hrperiodmaster')
            ->selectRaw('hrPeriodID as id, DATE( startDate ) as  startDate, DATE(endDate) as endDate  ')
            ->whereRaw("'{$this->currentDate}' BETWEEN DATE(startDate) AND  DATE(endDate)")
            ->where('companyID', $this->companyId)
            ->first();

        if (empty($this->payRollBasedData)) {
            $this->periodStatus = false;
            $msg = 'Can not find the payroll period for current date!';
            $this->insertToLogTb($msg, 'error');
            return;
        }

        $this->periodData = [
            'id' => $this->payRollBasedData->id,
            'startDate' => $this->payRollBasedData->startDate,
            'endDate' => $this->payRollBasedData->endDate,
        ];
    }

    function financialYearBased()
    {
        $this->financialYearBasedData = CompanyFinanceYear::selectRaw('companyFinanceYearID as id,
            DATE( bigginingDate ) as  startDate, DATE(endingDate) as endDate')
            ->whereRaw("'$this->currentDate' BETWEEN DATE(bigginingDate) AND  DATE(endingDate) ")
            ->where('isActive', -1)
            ->where('isDeleted', 0)
            ->where('companySystemID', $this->companyId)
            ->first();

        if (empty($this->financialYearBasedData)) {
            $this->periodStatus = false;
            $msg = 'Company finance year not found for the current year!';
            $this->insertToLogTb($msg, 'error');
            return;
        }

        $this->periodData = [
            'id' => $this->financialYearBasedData->id,
            'startDate' => $this->financialYearBasedData->startDate,
            'endDate' => $this->financialYearBasedData->endDate,
        ];
    }

    function standardYearBased()
    {
        $this->periodData = [
            'id' => null,
            'startDate' => Carbon::parse($this->currentDate)->startOfYear()->format('Y-m-d'),
            'endDate' =>  Carbon::parse($this->currentDate)->endOfYear()->format('Y-m-d'),
        ];
    }

    function getLeaveGroupDetails()
    {
        $companyId = $this->companyId;
        $this->leaveGroups = LeaveGroupDetails::select(
                'leaveGroupDetailID',
                'leaveGroupID',
                'leaveTypeID',
                'isCarryForward',
                'maxCarryForward',
                'policyMasterID'
            )
            ->with(['master' => function ($q) use ($companyId) {
                $q->select('leaveGroupID', 'description')
                    ->where('companyID', $companyId);
            }])
            ->whereHas('master', function ($q) use ($companyId) {
                $q->where('companyID', $companyId);
            })
            ->where('isCarryForward', 1)
            ->where('maxCarryForward', '>', 0) 
            ->groupBy(['policyMasterID', 'leaveGroupID', 'leaveTypeID'])
            ->get();
            
        $this->insertToLogTb('Leave group detail data function triggered for ' . $this->companyCode . '');
    }

    public function getLeaveToAdjustData()
    {
        foreach ($this->leaveGroups as $val) {
            $this->leaveDetails($val->leaveGroupID, $this->leaveComputationBasedOn, $val->leaveTypeID, $val->policyMasterID);
            if (!empty($this->leaveDetails)) {
                foreach ($this->leaveDetails as $val3) {
                    $balance = (($val3->entitle - $val3->taken));
                    if ($balance > $val->maxCarryForward) {
                        $this->leaveToAdjust[$val3->emp_leave_group_id . '-' . $val->policyMasterID][$val3->leaveTypeId][$val3->emp_id] = [
                            'empId' => $val3->emp_id,
                            'balance' => $balance,
                            'maxCarryForward' => $val->maxCarryForward,
                            'adustment' => ($val->maxCarryForward - ($balance)),
                            'leaveGroupDescription' => $val['master']['description'],
                            'leaveGroupId' => $val3->emp_leave_group_id,
                            'leaveTypeId' => $val3->leaveTypeId,
                            'available' => $val3->entitle,
                            'taken' => $val3->taken
                        ];
                    }
                }
            }
        }
        $this->insertToLogTb('Leave data function triggered for ' . $this->companyCode . '');
        return true;
    }


    public function getMonthWiseDate()
    {
        $this->insertToLogTb('Month wise data function triggered for ' . $this->companyCode . '');
        $this->monthDet = LeaveBalanceValidationHelper::validate_month($this->companyId, $this->currentDate)['details'];
    }

    function leaveDetails($leaveGroupID, $policyBasedOn, $typeID, $leaveTypePolicy)
    {
        $select = '' . $typeID . ' as leaveTypeId,' . $leaveTypePolicy . ' as leaveTypePolicy, emp.EIdNo as emp_id, ECode, 
        Ename2, emp.leaveGroupID as emp_leave_group_id ';
        $monthlyFirstDate =  $this->monthDet['dateFrom'];
        $yearFirstDate =  $this->periodData['startDate'];
        $yearEndDate =  $this->periodData['endDate'];

        $carryForwardLogic = "IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3), 
            IF( leavGroupDet.policyMasterID=1,  DATE(accrualDate) BETWEEN '{$yearFirstDate}' AND '{$yearEndDate}',
            accrualDate BETWEEN '{$monthlyFirstDate}' AND '{$yearEndDate}'), accrualDate <= '{$yearEndDate}') ";

        $carryForwardLogic2 = "AND IF( isCarryForward=0 AND (leavGroupDet.policyMasterID=1 OR leavGroupDet.policyMasterID=3),IF( leavGroupDet.policyMasterID=1, 
            startDate BETWEEN '{$yearFirstDate}' AND '{$yearEndDate}',startDate BETWEEN '{$monthlyFirstDate}' AND '{$yearEndDate}'), startDate <= '{$yearEndDate}')";

        $accrualMasterJoin = "JOIN (
                SELECT leaveaccrualMasterID, confirmedYN, 
                IF(policyMasterID = 1, DATE(bigginingDate) , 
                CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01')) as accrualDate,
                company_finance_year_id
                FROM srp_erp_leaveaccrualmaster 
                LEFT JOIN companyfinanceyear ON companyfinanceyear.companyFinanceYearID = 
                srp_erp_leaveaccrualmaster.company_finance_year_id and srp_erp_leaveaccrualmaster.policyMasterID = 1 
                and companyfinanceyear.companySystemID=srp_erp_leaveaccrualmaster.companyID
                WHERE confirmedYN = 1 AND srp_erp_leaveaccrualmaster.companyID={$this->companyId}
            ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID   ";

        if ($policyBasedOn == 3) {
            $accrualMasterJoin = "JOIN (
                    SELECT leaveaccrualMasterID, confirmedYN,  
                    IF(policyMasterID = 1, DATE_FORMAT(srp_erp_hrperiodmaster.startDate, '%Y-%m-%d'), 
                    DATE_FORMAT(srp_erp_hrperiod.dateFrom, '%Y-%m-%d'))  as accrualDate,
                    company_finance_year_id
                    FROM srp_erp_leaveaccrualmaster
                    LEFT JOIN srp_erp_hrperiodmaster ON srp_erp_hrperiodmaster.hrPeriodID = 
                    srp_erp_leaveaccrualmaster.company_finance_year_id and srp_erp_leaveaccrualmaster.policyMasterID = 1 
                    and srp_erp_hrperiodmaster.companyID=srp_erp_leaveaccrualmaster.companyID
                    LEFT JOIN srp_erp_hrperiod ON srp_erp_hrperiod.id = srp_erp_leaveaccrualmaster.company_finance_year_id 
                    AND srp_erp_leaveaccrualmaster.policyMasterID = 3 and srp_erp_hrperiod.companyID=srp_erp_leaveaccrualmaster.companyID
                    WHERE confirmedYN = 1 AND srp_erp_leaveaccrualmaster.companyID={$this->companyId} 
                ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID   ";
        } elseif ($policyBasedOn == 2) {
            $accrualMasterJoin = "JOIN (
                    SELECT leaveaccrualMasterID, confirmedYN, 
                    IF(policyMasterID = 1,  CONCAT(`year`,'-01','-01'), 
                    CONCAT(`year`,'-',LPAD(`month`,2,'00'),'-01'))  as accrualDate,
                    company_finance_year_id
                    FROM srp_erp_leaveaccrualmaster WHERE confirmedYN = 1 AND companyID={$this->companyId}                     
                ) AS accMaster ON detailTB.leaveaccrualMasterID = accMaster.leaveaccrualMasterID   ";
        }

        $select .= ",round((IFNULL(
                                    (SELECT SUM(daysEntitled) FROM srp_erp_leaveaccrualdetail AS detailTB
                                    {$accrualMasterJoin}
                                    JOIN srp_erp_leavegroupdetails AS leavGroupDet ON leavGroupDet.leaveGroupID = detailTB.leaveGroupID 
                                    AND leavGroupDet.leaveTypeID = '{$typeID}'
                                    WHERE {$carryForwardLogic} AND detailTB.leaveType = '{$typeID}' 
                                    AND leavGroupDet.policyMasterID IN (1,3)
                                    AND (detailTB.cancelledLeaveMasterID = 0 OR detailTB.cancelledLeaveMasterID IS NULL) 
                                    AND detailTB.empID = emp_id), 0
                                    )
                                ) , 2) AS entitle,
                                round(
                                IFNULL(
                                    (SELECT SUM(days) FROM srp_erp_leavemaster 
                                     JOIN srp_erp_leavegroupdetails AS leavGroupDet 
                                     ON leavGroupDet.leaveGroupID = srp_erp_leavemaster.leaveGroupID 
                                     AND leavGroupDet.leaveTypeID = '{$typeID}'
                                     WHERE srp_erp_leavemaster.leaveTypeID='{$typeID}' 
                                     AND leavGroupDet.policyMasterID IN (1,3) AND
                                     srp_erp_leavemaster.empID = emp_id AND approvedYN = 1 
                                     AND (cancelledYN = 0 OR cancelledYN IS NULL)
                                     {$carryForwardLogic2} ), 0
                                ) , 2) AS taken ";

        $this->leaveDetails = DB::table('srp_employeesdetails AS emp')
            ->selectRaw($select)
            ->where('Erp_companyID', $this->companyId)
            ->where('leaveGroupID', $leaveGroupID)
            ->where('isSystemAdmin', 0)
            ->where('isDischarged', '!=', 1)
            ->get();
    }

    function proceed()
    {
        $empData = [];
        DB::beginTransaction();
        try {
            $this->insertToLogTb('Execution started for ' . $this->companyCode . '');
            foreach ($this->leaveToAdjust as $key => $val) {
                $explodeLeaveGroupData = explode('-', $key);
                $leaveGroupId = $explodeLeaveGroupData[0];
                $policyId = $explodeLeaveGroupData[1];

                $masterData = $this->createHeader($leaveGroupId, $policyId);

                if (!$masterData['status']) {
                    $this->insertToLogTb('Something went wrong in master record creation in ' . $this->companyCode, 'error');
                    return false;
                }

                foreach ($val as $val2) {
                    foreach ($val2 as $val3) {
                        $this->createDetail($val3);
                        $data = [
                            'empId' => $val3['empId'],
                            'available' => $val3['available'],
                            'taken' => $val3['taken'],
                            'leaveTypeId' => $val3['leaveTypeId']
                        ];
                        array_push($empData, $data);
                    }
                }
                $this->insertToLogTb(['groupId' => $leaveGroupId, 'policyId' => $policyId, 'Document Code' => $this->documentCode, 'employee' => $empData]);
                $this->approveDocument();
                DB::commit();
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->insertToLogTb($exception->getMessage() . ' Company Code - ' . $this->companyCode, 'error');
            return;
        }
    }

    function createHeader($leaveGroupId, $policyId)
    {
        $documentCode = HrDocumentCodeService::generate($this->companyId, $this->companyCode, $this->documentId);
        $effective_date = $this->periodData['endDate'];
        $data = [
            'companyID' => $this->companyId,
            'leaveaccrualMasterCode' => $documentCode,
            'effective_date' => $effective_date,
            'documentID' => $this->documentId,
            'description' => "Maximum carry forward system automated adjustment (Year End)",
            'year' => Carbon::parse($effective_date)->format('Y'),
            'month' => Carbon::parse($effective_date)->format('m'),
            'leaveGroupID' => $leaveGroupId,
            'createdpc' => gethostname(),
            'policyMasterID' => $policyId,
            'adjustmentType' => 1,
            'company_finance_year_id' => $this->periodData['id'],
            'accrualPolicyValue' => $this->leaveComputationBasedOn,
            'manualYN' => 1
        ];

        $master = LeaveAccrualMaster::create($data);
        $this->accrualMasterID = $master->leaveaccrualMasterID;
        $this->documentCode = $documentCode;

        return ['status' => true];
    }

    function createDetail($data)
    {
        $data = [
            'leaveaccrualMasterID' => $this->accrualMasterID,
            'empID' => $data['empId'],
            'leaveGroupID' => $data['leaveGroupId'],
            'leaveType' => $data['leaveTypeId'],
            'daysEntitled' => $data['adustment'],
            'description' =>  '-',
            'createDate' => $this->currentDateTime,
            'createdPCid' => gethostname(),
        ];

        LeaveAccrualDetail::create($data);
    }

    function approveDocument()
    {

        $data = [
            'departmentID' => $this->documentId,
            'documentID' => $this->documentId,
            'documentSystemCode' => $this->accrualMasterID,
            'documentCode' => $this->documentCode,
            'isCancel' => 0,
            'documentDate' => $this->currentDate,
            'approvalLevelID' => 1,
            'isReverseApplicableYN' => 1,
            'approvalGroupID' => 0,
            'docConfirmedDate' => $this->currentDateTime,
            'docConfirmedByEmpID' => 11,
            'table_name' => 'srp_erp_leaveaccrualmaster',
            'table_unique_field_name' => 'leaveaccrualMasterID',
            'approvedEmpID' => 11,
            'approvedYN' => 1,
            'approvedDate' => $this->currentDateTime,
            'approvedComments' => 'Approved by Admin',
            'approvedPC' =>  gethostname(),
            'companyID' =>  $this->companyId,
            'companyCode' => $this->companyCode,
        ];

        HRDocumentApproved::create($data);

        $dataUpdate = [
            'submitYN' => 1,
            'submittedBy' => 11,
            'submittedDate' => $this->currentDateTime,
            'confirmedYN' => 1,
            'confirmedby' => 11,
            'confirmedDate' => $this->currentDateTime,
            'approvedYN' => 1,
            'approvedby' => 11,
            'approvedDate' => $this->currentDateTime
        ];

        LeaveAccrualMaster::find($this->accrualMasterID)
            ->update($dataUpdate);

        $this->insertToLogTb('Execution successfully completed for Document Code : '.$this->documentCode. ' Company Code ' . $this->companyCode . '');
    }

    function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);        

        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Leave Maximum Carry Forward Adjustment',
            'scenario_id' => 0,
            'processed_for' => $this->currentDate,
            'logged_at' => $this->currentDateTime,
            'log_type' => $logType,
            'log_data' => $logData
        ];

        DB::table('job_logs')->insert($data);
    }
}

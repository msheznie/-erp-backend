<?php
/**
 * =============================================
 * -- File Name : LeaveDataMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Leave Application
 * -- Author : Mohamed Rilwan
 * -- Create date : 29 - August 2019
 * -- Description : This file contains the all related functions for leave appliation
 * -- REVISION HISTORY
 * -- Date: 29- August 2019 By: Rilwan Description: Added new function getLeaveHistory()
 * -- Date: 01- September 2019 By: Rilwan Description: Added new function getLeaveDetailsForEmployee(),getLeaveAvailableForEmployee
 * -- Date: 02- September 2019 By: Rilwan Description: Added new function saveLeaveDetails()
 *      saveLeaveDetails() - No Analyzing, discuss with shafri,fayas. shafri discuss with zahlan and asked to translate whole code into Laravel and without analyzig
 * -- Date: 04- September 2019 By Rilwan saveDocumentAttachments(),saveAttachment() - to save attachments
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateLeaveDataMasterAPIRequest;
use App\Http\Requests\API\UpdateLeaveDataMasterAPIRequest;
use App\Models\Alert;
use App\Models\CalenderMaster;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\EmployeeManagers;
use App\Models\LeaveDataDetail;
use App\Models\LeaveDataMaster;
use App\Models\LeaveDocumentApproved;
use App\Models\LeaveMaster;
use App\Models\QryLeavePosted;
use App\Models\QryLeavesAccrued;
use App\Models\QryLeavesApplied;
use App\Repositories\LeaveDataMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class LeaveDataMasterController
 * @package App\Http\Controllers\API
 */
class LeaveDataMasterAPIController extends AppBaseController
{
    /** @var  LeaveDataMasterRepository */
    private $leaveDataMasterRepository;

    public function __construct(LeaveDataMasterRepository $leaveDataMasterRepo)
    {
        $this->leaveDataMasterRepository = $leaveDataMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataMasters",
     *      summary="Get a listing of the LeaveDataMasters.",
     *      tags={"LeaveDataMaster"},
     *      description="Get all LeaveDataMasters",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/LeaveDataMaster")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->leaveDataMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveDataMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveDataMasters = $this->leaveDataMasterRepository->all();

        return $this->sendResponse($leaveDataMasters->toArray(), 'Leave Data Masters retrieved successfully');
    }

    /**
     * @param CreateLeaveDataMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveDataMasters",
     *      summary="Store a newly created LeaveDataMaster in storage",
     *      tags={"LeaveDataMaster"},
     *      description="Store LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveDataMasterAPIRequest $request)
    {

    }

    private function saveLeaveDataMaster()
    {
        $employee = Helper::getEmployeeInfo();
        $empID = $employee->empID;
        $company_id = $employee->empCompanyID;
        $sn = LeaveDataMaster::where('CompanyID', $company_id)
            ->orderBy('leavedatamasterID', 'Desc')
            ->max('serialNo');
        $serial_no = $sn + 1;
        $code = str_pad($serial_no, 5, '0', STR_PAD_LEFT);
        $leaveDataMasterCode = $company_id . '/HR/' . 'LA' . $code;
        $input = array(
            'empID' => $empID,
            'EntryType' => 1,
            'managerAttached' => $employee->empManagerAttached,
            'SeniorManager' => isset($employee->manager->empManagerAttached) ? $employee->manager->empManagerAttached : 0,
            'designatiomID' => isset($employee->details->designationID) ? $employee->details->designationID : 0,
            'CompanyID' => $company_id,
            'location' => isset($employee->details->categoryID) ? $employee->details->categoryID : 0,
            'scheduleMasterID' => isset($employee->details->schedulemasterID) ? $employee->details->schedulemasterID : 0,
            'leaveDataMasterCode' => $leaveDataMasterCode,
            'documentID' => 'LA',
            'serialNo' => $serial_no,
            'createDate' => date('Y-m-d H:i:s'),
            'createduserGroup' => $empID,
            'createdpc' => strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))
        );

        return $this->leaveDataMasterRepository->create($input);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDataMasters/{id}",
     *      summary="Display the specified LeaveDataMaster",
     *      tags={"LeaveDataMaster"},
     *      description="Get LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        return $this->sendResponse($leaveDataMaster->toArray(), 'Leave Data Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateLeaveDataMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveDataMasters/{id}",
     *      summary="Update the specified LeaveDataMaster in storage",
     *      tags={"LeaveDataMaster"},
     *      description="Update LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDataMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDataMaster")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/LeaveDataMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveDataMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveDataMaster $leaveDataMaster */
        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        $leaveDataMaster = $this->leaveDataMasterRepository->update($input, $id);

        return $this->sendResponse($leaveDataMaster->toArray(), 'LeaveDataMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveDataMasters/{id}",
     *      summary="Remove the specified LeaveDataMaster from storage",
     *      tags={"LeaveDataMaster"},
     *      description="Delete LeaveDataMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDataMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var LeaveDataMaster $leaveDataMaster */

        $leaveDataMaster = $this->leaveDataMasterRepository->findWithoutFail($id);

        if (empty($leaveDataMaster)) {
            return $this->sendError('Leave Data Master not found');
        }

        // check is claim data
        if ($leaveDataMaster->claimedLeavedatamasterID != null && $leaveDataMaster->EntryType == 2) {
            $date_time = date('Y-m-d H:i:s');
            $this->leaveDataMasterRepository->update(['claimedYN' => 0, 'timestamp' => $date_time], $leaveDataMaster->claimedLeavedatamasterID);
        }

        if (!empty($leaveDataMaster->detail())) {
            $leaveDataMaster->detail()->delete();
        }
        $leaveDataMaster->delete();

        return $this->sendResponse($id, 'Leave Data Master deleted successfully');
    }

    public function getLeaveHistory()
    {
        $emp_id = Helper::getEmployeeID();

        $leaveHistory = LeaveDataMaster::selectRaw('hrms_leavedatamaster.leavedatamasterID AS leavedatamasterID,
	                                                hrms_leavedatamaster.empID AS empID,
	                                                hrms_leavedatamaster.hrapprovalYN AS hrapprovalYN,
                                                    employees.empName AS empName,
                                                    employees_1.empName AS Manager,
                                                    hrms_leavedatamaster.leaveDataMasterCode AS leaveDataMasterCode,
                                                    hrms_leavedatamaster.confirmedYN AS confirmedYN,
                                                    hrms_leavedatamaster.approvedYN AS approvedYN,
                                                    hrms_leavedatamaster.timestamp AS timestamp,
                                                    hrms_designation.designation AS designation,
                                                    hrms_leavedatamaster.CompanyID AS CompanyID,
                                                    hrms_leavedatamaster.createDate AS createDate,
                                                    hrms_leavedatamaster.EntryType AS LeaveApplicationTypeID,
                                                    hrms_category.category AS category,
                                                    hrms_leaveapplicationtype.Type AS Type,
                                                    hrms_leavemaster.leavetype AS leavetype,
                                                    hrms_leavedatamaster.createduserGroup AS createduserGroup,
                                                    hrms_leavedatamaster.RollLevForApp_curr AS approval_level ,
                                                    hrms_leavedatadetail.startDate AS startDate,
                                                    hrms_leavedatadetail.endDate AS endDate')
            ->join('employees', 'hrms_leavedatamaster.empID', '=', 'employees.empID')
            ->leftJoin('employees as employees_1', 'hrms_leavedatamaster.managerAttached', '=', 'employees_1.empID')
            ->leftJoin('hrms_designation', 'hrms_leavedatamaster.designatiomID', '=', 'hrms_designation.designationID')
            ->leftJoin('hrms_category', 'hrms_leavedatamaster.location', '=', 'hrms_category.categoryID')
            ->leftJoin('hrms_leaveapplicationtype', 'hrms_leavedatamaster.EntryType', '=', 'hrms_leaveapplicationtype.LeaveApplicationTypeID')
            ->leftJoin('hrms_leavemaster', 'hrms_leavedatamaster.leaveType', '=', 'hrms_leavemaster.leavemasterID')
            ->leftJoin('hrms_leavedatadetail', 'hrms_leavedatamaster.leavedatamasterID', '=', 'hrms_leavedatadetail.leavedatamasterID')
            ->where('hrms_leavedatamaster.empID', $emp_id)->get();


        return $this->sendResponse($leaveHistory->toArray(), 'Leave history details retrieved successfully');
    }

    public function getLeaveDetailsForEmployee(Request $request)
    {
        $fromDate = isset($request['fromDate']) ? $request['fromDate'] : null;
        $toDate = isset($request['toDate']) ? $request['toDate'] : null;
        $leaveMasterID = isset($request['leaveMasterID']) ? $request['leaveMasterID'] : null;

        if ($leaveMasterID == null) {
            return $this->sendResponse([], 'Leave details not found');
        }

        $employee = Helper::getEmployeeInfo();
        $empID = $employee->empID;
        $workingDays = 0;
        $nonWorkingDays = 0;
        $total_days_applied = 0;
        $balance = 0;
        $day_type = "Working Days";

        if ($leaveMasterID == 16 || $leaveMasterID == 2 || $leaveMasterID == 3 || $leaveMasterID == 4 || $leaveMasterID == 21) {
            $date = date('Y');
            $available = $this->getLeaveAvailableForEmployee($empID, $leaveMasterID, $date);
        } else {
            $available = $this->getLeaveAvailableForEmployee($empID, $leaveMasterID);
        }

        if ($fromDate != null && $toDate != null) {

            $workingDays = CalenderMaster::where('isWorkingDay', -1)->whereBetween('calDate', [$fromDate, $toDate])->get()->count();
            $nonWorkingDays = CalenderMaster::where('isWorkingDay', 0)->whereBetween('calDate', [$fromDate, $toDate])->get()->count();
            $total_days_applied = $workingDays + $nonWorkingDays;

            if ($leaveMasterID == 16 || $leaveMasterID == 18 || $leaveMasterID == 3 || $leaveMasterID == 13 || $leaveMasterID == 2 || $leaveMasterID == 3 || $leaveMasterID == 4 || $leaveMasterID == 21 || $leaveMasterID == 5 || $leaveMasterID == 11) {
                $balance = $available - ($workingDays + $nonWorkingDays);
                $day_type = "Total days applied";
            } else {
                $calculateCalendarDays = isset($employee->details->schedule->calculateCalendarDays) ? $employee->details->schedule->calculateCalendarDays : 0;
                if ($leaveMasterID == 1) {
                    if ($calculateCalendarDays == -1) {
                        $balance = $available - ($workingDays + $nonWorkingDays);
                    } else {
                        $balance = $available - $workingDays;
                    }
                } else {
                    $balance = $available - $workingDays;
                }

            }
        }

        $output = array(
            'leave_available' => $available,
            'working_days' => $workingDays,
            'non_working_days' => $nonWorkingDays,
            'total_days_applied' => $total_days_applied,
            'balance' => $balance,
            'day_type' => $day_type
        );
        return $this->sendResponse($output, 'Leave details retrieved successfully');
    }

    private function getLeaveAvailableForEmployee($empID, $leaveMasterID = null, $date = null)
    {
        if ($empID != null && $leaveMasterID != null) {

            $leave_accured = QryLeavesAccrued::selectRaw('SUM(SumOfDaysEntitled) as leaveBalanceaccrued')
                ->where('empID', $empID)
                ->where('leaveType', $leaveMasterID)
                ->where(function ($query) use ($date) {
                    if ($date != null) {
                        $query->where('periodYear', $date);
                    }
                })->groupBy('leaveType')
                ->first();
            $leave_accured = isset($leave_accured->leaveBalanceaccrued) ? $leave_accured->leaveBalanceaccrued : 0;

            $leave_applied = QryLeavesApplied::selectRaw('SUM(calculatedDays) as leaveBalanceapplied')
                ->where('empID', $empID)
                ->where('leavemasterID', $leaveMasterID)
                ->where(function ($query) use ($date) {
                    if ($date != null) {
                        $query->where('CYear', $date);
                    }
                })->groupBy('leavemasterID')
                ->first();

            $leave_applied = isset($leave_applied->leaveBalanceapplied) ? $leave_applied->leaveBalanceapplied : 0;

            $balance = $leave_accured - $leave_applied;
            return $balance;
        }
        return 0;
    }

    public function saveLeaveDetails(Request $request)
    {
        $input = $request->all();

        $messages = [
            'leavemasterID.required' => 'Leave Type field is required'
        ];

        $validator = \Validator::make($input, [
            'leavemasterID' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'comment' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $leaveMasterID = $input['leavemasterID'];
        $leaveType = isset($input['leaveType']) ? $input['leaveType'] : null;

        $startDate_o = Carbon::parse($input['startDate']);   // Carbon time object
        $endDate_o = Carbon::parse($input['endDate']);       // Carbon time object
        $dateDiff = $startDate_o->diffInDays($endDate_o);

        $startDate = Carbon::parse($input['startDate'])->format('Y-m-d');        //to date format
        $endDate = Carbon::parse($input['endDate'])->format('Y-m-d');            //to date format

        if ($leaveMasterID == 15) {
            $policy_validator = \Validato::make($input, [
                'policytype' => 'required'
            ]);
            if ($policy_validator->fails()) {
                return $this->sendError($policy_validator->messages(), 422);
            }
        }
        DB::beginTransaction();
        try {

            if (isset($input['empID'])) {
                $employee = Employee::where('empID',$input['empID'])->first();
            } else {
                $employee = Helper::getEmployeeInfo();
            }
            $empID = $employee->empID;

            $createdLeaveData = $this->saveLeaveDataMaster();
            $input['leavedatamasterID'] = $createdLeaveData->leavedatamasterID;
            $leaveDataMasterID = $input['leavedatamasterID'];

            $documentCode = "LA";
            $input['documentSystemID']=null;
            $input['companySystemID']=null;
            $input['empSystemID']=$employee->employeeSystemID;

            $documentMaster = DocumentMaster::where('documentID', $documentCode)->first();
            if ($documentMaster) {
                $input['documentSystemID'] = $documentMaster->documentSystemID;
            }

            $companyMaster = Company::where('companyID', $employee->empCompanyID)->first();
            if ($companyMaster) {
                $input['companySystemID'] = $companyMaster->companySystemID;
            }

            $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
            if (empty($leaveDataMasters)) {
                return $this->sendError("Leave Master Data not found", 500);
            }

            if ($leaveType == 2) {
                $type_validator = \Validator::make($input, [
                    'leavedatamasterIDDrop' => 'required',
                    'claimedDays' => 'required|date',
                    'claimedDays' => 'required|numeric|min:1'
                ]);

                if ($type_validator->fails()) {
                    return $this->sendError($type_validator->messages(), 422);
                }
            }

            $leaveMasters = LeaveMaster::find($leaveMasterID);
            if (empty($leaveMasters)) {
                return $this->sendError("Leave Master not found", 500);
            }
            $restrictDays = $leaveMasters->restrictDays;

            $dateAssumed = isset($employee->details->dateAssumed) ? $employee->details->dateAssumed : null;
            $diffInMonths = 0;
            if ($dateAssumed != null) {
                $date_assumed_o = Carbon::parse($dateAssumed);
                $diffInMonths = $startDate_o->diffInMonths($date_assumed_o);
            }

            if ($request->hasFile('file')) {
                $type_validator = \Validator::make($input, [
                    'attachmentDescription' => 'required'
                ]);

                if ($type_validator->fails()) {
                    return $this->sendError($type_validator->messages(), 422);
                }

                $files = $request->file('file');
                $attach=[
                    'companyID'=>$employee->empCompanyID,
                    'companySystemID'=>$input['companySystemID'],
                    'documentSystemCode'=>$leaveDataMasterID,
                    'documentID'=>$documentCode,
                    'documentSystemID'=>$input['documentSystemID']
                ];
                $this->saveDocumentAttachments($files, $input['attachmentDescription'],$attach);
            }

            $attachmentStatus = 0;
            $attachments = DocumentAttachments::where('documentSystemCode', $leaveDataMasterID)
                ->where('documentID', $documentCode)->count();
            if ($attachments) {
                $attachmentStatus = 1;
            }

            $workingDays = CalenderMaster::where('isWorkingDay', -1)->whereBetween('calDate', [$startDate, $endDate])->count();

            $status = isset($input['status']) ? $input['status'] : null;

            $isAlreadyApplied = LeaveDataMaster::join('hrms_leavedatadetail', 'hrms_leavedatamaster.leavedatamasterID', '=', 'hrms_leavedatadetail.leavedatamasterID')
                ->where('hrms_leavedatamaster.empID', $empID)
                ->where('hrms_leavedatamaster.claimedYN', 0)
                ->where('hrms_leavedatamaster.leavedatamasterID', $leaveDataMasterID)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereRaw("'$startDate' BETWEEN startDate AND endFinalDate");
                    $query->orWhereRaw("'$endDate' BETWEEN startDate AND endFinalDate");
                })
                ->count();

            if (($restrictDays != -1) && $startDate < date('Y-m-d') && ($status != 1) && ($leaveMasterID == 1) && ($leaveType == 1)) {
                return $this->sendError('You cannot apply leave for past days');
            } else if (($restrictDays != -1) && ($dateDiff < $restrictDays) && ($status != 1) && ($leaveMasterID == 1) && (($workingDays > 2)) && ($leaveType == 1)) {
                return $this->sendError('Please apply the leave before' . $restrictDays . ' days interval');
            } else if (($leaveMasters->isProbation == -1) && ($diffInMonths < 3)) {
                return $this->sendError('You cannot obtain any leave in your probation period');
            } else if (($diffInMonths < 12) && ($leaveMasterID == 13)) {
                return $this->sendError('You must complete 1 year of service with the company to be eligible for Hajj leave');
            } else if ($leaveMasters->isAttachmentMandatory == -1 && ($attachmentStatus == 0)) {
                return $this->sendError('Attachment is required');
            } else if (($workingDays > $leaveMasters->maxDays) && ($leaveMasters->maxDays != 0) && ($leaveType == 1)) {
                return $this->sendError('You cannot apply leave more than ' . $leaveMasters->maxDays . ' days');
            } else if ($isAlreadyApplied && $leaveType == 1) {
                return $this->sendError('You have already taken leave in this period');
            } else {

                $input['startDate'] = $startDate;
                $input['endDate'] = $endDate;
                $input['modifieduser'] = $empID;
                $input['modifiedpc'] = strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $input['entryType'] = $leaveType;
                $input['leaveType'] = $leaveMasterID;
                $input['endFinalDate'] = $endDate;

                if (isset($input['confirmedYN']) && $input['confirmedYN'] == 1) {
                    $input['confirmedby'] = $empID;
                    $input['confirmedDate'] = date('Y-m-d');
                }
                //  entryType = 2   => leave claim
                if (isset($input['entryType']) && $input['entryType'] == 2) {
                    $claimedDays = $input['claimedDays'] ? $input['claimedDays'] : 0;

                    $endFinalDate_o = $endDate_o->copy()->subDays($claimedDays);
                    $endFinalDate = $endFinalDate_o->format('Y-m-d');
                    $input['endFinalDate'] = $endFinalDate;
                    $input['claimedLeavedatamasterID'] = $input['leavedatamasterIDDrop'];

                    if (isset($input['confirmedYN']) && $input['confirmedYN'] == 1) {

                        $input['noOfWorkingDays'] = $claimedDays * (-1);
                        $input['totalDays'] = null;
                        $input['noOfNonWorkingDays'] = null;
                        $input['calculatedDays'] = $input['noOfWorkingDays'];

                    } else {
                        $input['noOfWorkingDays'] = 0;
                        $input['totalDays'] = 0;
                        $input['noOfNonWorkingDays'] = 0;
                        $input['calculatedDays'] = 0;
                    }
                }

                $multiple = $leaveMasters->allowMultipleLeave;
                $approved = LeaveDataMaster::where('empID', $empID)
                    ->where('leaveType', $leaveMasterID)
                    ->where('EntryType', $leaveType)
                    ->where('approvedYN', 0)
                    ->count();

                if ($multiple || $approved == 0) {

                    $leaveDataDetail = $leaveDataMasters->detail;

                    if (!empty($leaveDataDetail)) {

                        $isLeaveDetailUpdate = $this->leaveDataMasterRepository->updateLeaveDataDetails($input);
                        if ($isLeaveDetailUpdate) {

                            $isLeaveDataMasterUpdate = $this->leaveDataMasterRepository->updateLeaveDataMaster($input);

                            if (isset($input["confirmedYN"]) && $input["confirmedYN"] == 1) {

                                $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
                                $department = isset($employee->details->departmentMaster) ? $employee->details->departmentMaster : null;

                                if (!empty($department)) {
                                    $hrApprovalLevels = isset($department->hrLeaveApprovalLevels) ? $department->hrLeaveApprovalLevels : 0;

                                    if ($hrApprovalLevels > 0) {

                                        for ($i = 1; $i <= $hrApprovalLevels; $i++) {
                                            $doc["companyID"] = $employee->empCompanyID;
                                            $doc["employeeID"] = $empID;
                                            $doc["departmentID"] = 'HRMS';
                                            $doc["serviceLineCode"] = 'x';
                                            $doc["documentID"] = 'LA';
                                            $doc["documentSystemCode"] = $input["leavedatamasterID"];
                                            $doc["documentCode"] = isset($leaveDataMasters->leaveDataMasterCode) ? $leaveDataMasters->leaveDataMasterCode : null;
                                            $doc["docConfirmedByEmpID"] = $empID;
                                            $doc["requesterID"] = Helper::getEmployeeID();
                                            $doc["Approver"] = Helper::getEmployeeID();
                                            $doc["rollLevelOrder"] = $i;
                                            $doc["approvedYN"] = 0;
                                            $doc["rejectedYN"] = 0;
                                            $doc["docConfirmedDate"] = date('Y-m-d');
                                            LeaveDocumentApproved::create($doc);
                                        }

                                        if (isset($input["leaveType"]) && $input["leaveType"] == 1) {
                                            $myDocumentName = "Leave Application";
                                        } else {
                                            $myDocumentName = "Leave Claim";
                                        }

                                        $managers = EmployeeManagers::where('empID', $empID)->get();

                                        foreach ($managers as $manager) {

                                            $empManager = Employee::where('empID', $manager->managerID)->first();
                                            $alert["companyID"] = $empManager->empCompanyID;
                                            $alert["documentSystemID"] = $input['documentSystemID'];
                                            $alert["companySystemID"] = $input['companySystemID'];
                                            $alert["empSystemID"] = $input['empSystemID'];
                                            $alert["empID"] = $manager->managerID;
                                            $alert["docID"] = 'LA';
                                            $alert["docApprovedYN"] = 0;
                                            $alert["docSystemCode"] = $leaveDataMasterID;
                                            $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                                            $alert["alertMessage"] = "Pending " . $myDocumentName . " approval " . $leaveDataMasters->leaveDataMasterCode;
                                            $alert["alertDateTime"] = date('Y-m-d');
                                            $alert["alertViewedYN"] = 0;
                                            $alert["alertViewedDateTime"] = Null;
                                            $alert["empName"] = $empManager->empName;
                                            $alert["empEmail"] = $empManager->empEmail;
                                            $alert["emailAlertMessage"] = "Hi " . $empManager->empName . ",<p>" . $myDocumentName . " <b>" . $documentCode . "</b> is pending for your approval from <b>" . $employee->empName . "<b/>.<a href=http://gears.gulfenergy-int.com/portal/leave_approval.php>Click here to approve.</a>.<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";
                                            $alert["isEmailSend"] = 0;
                                            $alert["timeStamp"] = date('Y-m-d');
                                            Alert::create($alert);
                                        }

                                        $alert["companyID"] = $employee->empCompanyID;
                                        $alert["documentSystemID"] = $input['documentSystemID'];
                                        $alert["companySystemID"] = $input['companySystemID'];
                                        $alert["empSystemID"] = $input['empSystemID'];
                                        $alert["empID"] = $manager->managerID;
                                        $alert["docID"] = 'LA';
                                        $alert["docApprovedYN"] = 0;
                                        $alert["docSystemCode"] = $leaveDataMasterID;
                                        $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                                        $alert["alertMessage"] = "Leave Application (" . $leaveDataMasters->leaveDataMasterCode . ") Submitted";
                                        $alert["alertDateTime"] = date('Y-m-d');
                                        $alert["alertViewedYN"] = 0;
                                        $alert["alertViewedDateTime"] = Null;
                                        $alert["empName"] = $employee->empName;
                                        $alert["empEmail"] = $employee->empEmail;
                                        $alert["emailAlertMessage"] = "Hi " . $employee->empName . ",<p>Your Leave Application <b>" . $leaveDataMasters->leaveDataMasterCode . "<b/> has been submitted to <b>" . $empManager->empName . "<b/> for Approval.<br><p style='color:#FF0000'><b>Note : Employees are strictly instructed not to go on the applied leave until they receive an approval of the leave application from their Supervisor/Manager.<b/></p><br><font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";
                                        $alert["isEmailSend"] = 0;
                                        $alert["timeStamp"] = date('Y-m-d');
                                        Alert::create($alert);

                                        if (!empty($leaveDataMasters) && $isLeaveDataMasterUpdate) {
                                            DB::commit();
                                            return $this->sendResponse([], 'Successfully leave application applied');
                                        } else {

                                            $input["confirmedby"] = NULL;
                                            $input["confirmedDate"] = NULL;
                                            $input["confirmedYN"] = 0;
                                            $update_array = array(
                                                'confirmedby' => null,
                                                'confirmedDate' => null,
                                                'confirmedYN' => 0,
                                            );

                                            LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                            return $this->sendError('Error Occurred! Please contact the administration');
                                        }
                                    } else {


                                        $input["confirmedby"] = NULL;
                                        $input["confirmedDate"] = NULL;
                                        $input["confirmedYN"] = 0;
                                        $update_array = array(
                                            'confirmedby' => null,
                                            'confirmedDate' => null,
                                            'confirmedYN' => 0,
                                        );

                                        LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                        return $this->sendError('No Approval levels created,Please contact Administrator');

                                    }
                                } else {

                                    $input["confirmedby"] = NULL;
                                    $input["confirmedDate"] = NULL;
                                    $input["confirmedYN"] = 0;
                                    $update_array = array(
                                        'confirmedby' => null,
                                        'confirmedDate' => null,
                                        'confirmedYN' => 0,
                                    );

                                    LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                    return $this->sendError('Functional department not set. Please contact HR');
                                }

                            } else {


                                if (!empty($leaveDataMasters) && $isLeaveDataMasterUpdate) {
                                    DB::commit();
                                    return $this->sendResponse([], 'Successfully leave application applied');
                                } else {
                                    return $this->sendError('Error Occurred! Please contact the administration');
                                }
                            }
                        }
                    } else {

                        $isLeaveDetailInsert = $this->leaveDataMasterRepository->insertLeaveDataDetails($input);

                        if ($isLeaveDetailInsert) {

                            $isLeaveDataMasterUpdate = $this->leaveDataMasterRepository->updateLeaveDataMaster($input);

                            if ($input["confirmedYN"] == 1 ) {

                                $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
                                $department = isset($employee->details->departmentMaster) ? $employee->details->departmentMaster : null;

                                if ($department != null) {
                                    $hrLeaveApprovalLevels = isset($department->hrLeaveApprovalLevels) ? $department->hrLeaveApprovalLevels : 0;

                                    if ($hrLeaveApprovalLevels > 0) {

                                        for ($i = 1; $i <= $hrLeaveApprovalLevels; $i++) {
                                            $doc["companyID"] = $employee->empCompanyID;
                                            $doc["employeeID"] = $empID;
                                            $doc["departmentID"] = 'HRMS';
                                            $doc["serviceLineCode"] = 'x';
                                            $doc["documentID"] = 'LA';
                                            $doc["documentSystemCode"] = $input["leavedatamasterID"];
                                            $doc["documentCode"] = isset($leaveDataMasters->leaveDataMasterCode) ? $leaveDataMasters->leaveDataMasterCode : null;
                                            $doc["docConfirmedByEmpID"] = $empID;
                                            $doc["requesterID"] = Helper::getEmployeeID();
                                            $doc["Approver"] = Helper::getEmployeeID();
                                            $doc["rollLevelOrder"] = $i;
                                            $doc["approvedYN"] = 0;
                                            $doc["rejectedYN"] = 0;
                                            $doc["docConfirmedDate"] = date('Y-m-d');
                                            LeaveDocumentApproved::create($doc);
                                        }

                                        if (isset($input["leaveType"]) && $input["leaveType"] == 1) {
                                            $myDocumentName = "Leave Application";
                                        } else {
                                            $myDocumentName = "Leave Claim";
                                        }

                                        $managers = EmployeeManagers::where('empID', $empID)->get();

                                        foreach ($managers as $manager) {

                                            $empManager = Employee::where('empID', $manager->managerID)->first();
                                            $alert["companyID"] = $empManager->empCompanyID;
                                            $alert["docSystemID"] = $input['documentSystemID'];
                                            $alert["companySystemID"] = $input['companySystemID'];
                                            $alert["empSystemID"] = $input['empSystemID'];
                                            $alert["empID"] = $manager->managerID;
                                            $alert["docID"] = 'LA';
                                            $alert["docApprovedYN"] = 0;
                                            $alert["docSystemCode"] = $leaveDataMasterID;
                                            $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                                            $alert["alertMessage"] = "Pending " . $myDocumentName . " approval " . $leaveDataMasters->leaveDataMasterCode;
                                            $alert["alertDateTime"] = date('Y-m-d');
                                            $alert["alertViewedYN"] = 0;
                                            $alert["alertViewedDateTime"] = Null;
                                            $alert["empName"] = $empManager->empName;
                                            $alert["empEmail"] = $empManager->empEmail;
                                            $alert["emailAlertMessage"] = "Hi " . $empManager->empName . ",<p>" . $myDocumentName . " <b>" . $documentCode . "</b> is pending for your approval from <b>" . $employee->empName . "<b/>.<a href=http://gears.gulfenergy-int.com/portal/leave_approval.php>Click here to approve.</a>.<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";
                                            $alert["isEmailSend"] = 0;
                                            $alert["timeStamp"] = date('Y-m-d');
                                            Alert::create($alert);
                                        }

                                        $alert["companyID"] = $employee->empCompanyID;
                                        $alert["docSystemID"] = $input['documentSystemID'];
                                        $alert["companySystemID"] = $input['companySystemID'];
                                        $alert["empSystemID"] = $input['empSystemID'];
                                        $alert["empID"] = $manager->managerID;
                                        $alert["docID"] = 'LA';
                                        $alert["docApprovedYN"] = 0;
                                        $alert["docSystemCode"] = $leaveDataMasterID;
                                        $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                                        $alert["alertMessage"] = "Leave Application (" . $leaveDataMasters->leaveDataMasterCode . ") Submitted";
                                        $alert["alertDateTime"] = date('Y-m-d');
                                        $alert["alertViewedYN"] = 0;
                                        $alert["alertViewedDateTime"] = Null;
                                        $alert["empName"] = $employee->empName;
                                        $alert["empEmail"] = $employee->empEmail;
                                        $alert["emailAlertMessage"] = "Hi " . $employee->empName . ",<p>Your Leave Application <b>" . $leaveDataMasters->leaveDataMasterCode . "<b/> has been submitted to <b>" . $empManager->empName . "<b/> for Approval.<br><p style='color:#FF0000'><b>Note : Employees are strictly instructed not to go on the applied leave until they receive an approval of the leave application from their Supervisor/Manager.<b/></p><br><font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";
                                        $alert["isEmailSend"] = 0;
                                        $alert["timeStamp"] = date('Y-m-d');
                                        Alert::create($alert);

                                        if (!empty($leaveDataMasters) && $isLeaveDataMasterUpdate) {
                                            DB::commit();
                                            return $this->sendResponse([], 'Successfully leave application applied');
                                        } else {
                                            $input["confirmedby"] = NULL;
                                            $input["confirmedDate"] = NULL;
                                            $input["confirmedYN"] = 0;
                                            $update_array = array(
                                                'confirmedby' => null,
                                                'confirmedDate' => null,
                                                'confirmedYN' => 0,
                                            );

                                            LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                            return $this->sendError('Error Occurred! Please contact the administration');
                                        }

                                    } else {

                                        $input["confirmedby"] = NULL;
                                        $input["confirmedDate"] = NULL;
                                        $input["confirmedYN"] = 0;
                                        $update_array = array(
                                            'confirmedby' => null,
                                            'confirmedDate' => null,
                                            'confirmedYN' => 0,
                                        );

                                        LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                        return $this->sendError('No Approval levels created,Please contact Administrator');
                                    }

                                } else {

                                    $input["confirmedby"] = NULL;
                                    $input["confirmedDate"] = NULL;
                                    $input["confirmedYN"] = 0;
                                    $update_array = array(
                                        'confirmedby' => null,
                                        'confirmedDate' => null,
                                        'confirmedYN' => 0,
                                    );

                                    LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                    return $this->sendError('Functional department not set. Please contact HR');
                                }

                            } else {
                                if (!empty($leaveDataMasters) && $isLeaveDataMasterUpdate) {
                                    DB::commit();
                                    return $this->sendResponse([], 'Successfully leave application applied');
                                } else {
                                    return $this->sendError('Error Occurred! Please contact the administration', 500);
                                }
                            }
                        }
                    }

                } else if (isset($input["status"]) && $input["status"] == 1) {

                    $leaveDataDetail = $leaveDataMasters->detail;

                    if ($restrictDays != -1 && $startDate != $leaveDataDetail->startDate && $dateDiff < $restrictDays && $leaveMasterID == 1 &&
                        ($workingDays > 2) && $leaveType == 1) {
                        return $this->sendError('Please apply the leave before' . $restrictDays . ' days interval');

                    } else if (($restrictDays != -1) && $startDate != date("Y-m-d", strtotime($leaveDataDetail->startDate)) && (date("Y-m-d", strtotime($leaveDataDetail->startDate)) > $startDate) && ($leaveMasterID == 1) && ($leaveType == 1)) {
                        return $this->sendError('You cannot apply leave for past days');

                    } else {

                        if (!empty($leaveDataDetail)) {

                            $isLeaveDetailUpdate = $this->leaveDataMasterRepository->updateLeaveDataDetails($input);

                            if ($isLeaveDetailUpdate) {

                                $isLeaveDataMasterUpdate = $this->leaveDataMasterRepository->updateLeaveDataMaster($input);

                                if (isset($input["confirmedYN"]) && $input["confirmedYN"] == 1) {

                                    $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
                                    $department = isset($employee->details->departmentMaster) ? $employee->details->departmentMaster : null;

                                    if (!empty($department)) {
                                        $hrApprovalLevels = isset($department->hrLeaveApprovalLevels) ? $department->hrLeaveApprovalLevels : 0;

                                        if ($hrApprovalLevels > 0) {

                                            for ($i = 1; $i <= $hrApprovalLevels; $i++) {
                                                $doc["companyID"] = $employee->empCompanyID;
                                                $doc["employeeID"] = $empID;
                                                $doc["departmentID"] = 'HRMS';
                                                $doc["serviceLineCode"] = 'x';
                                                $doc["documentID"] = 'LA';
                                                $doc["documentSystemCode"] = $input["leavedatamasterID"];
                                                $doc["documentCode"] = isset($leaveDataMasters->leaveDataMasterCode) ? $leaveDataMasters->leaveDataMasterCode : null;
                                                $doc["docConfirmedByEmpID"] = $empID;
                                                $doc["requesterID"] = Helper::getEmployeeID();
                                                $doc["Approver"] = Helper::getEmployeeID();
                                                $doc["rollLevelOrder"] = $i;
                                                $doc["approvedYN"] = 0;
                                                $doc["rejectedYN"] = 0;
                                                $doc["docConfirmedDate"] = date('Y-m-d');
                                                LeaveDocumentApproved::create($doc);
                                            }

                                            if (isset($input["leaveType"]) && $input["leaveType"] == 1) {
                                                $myDocumentName = "Leave Application";
                                            } else {
                                                $myDocumentName = "Leave Claim";
                                            }

                                            $managers = EmployeeManagers::where('empID', $empID)->get();

                                            foreach ($managers as $manager) {

                                                $empManager = Employee::where('empID', $manager->managerID)->first();
                                                $alert["companyID"] = $empManager->empCompanyID;
                                                $alert["docSystemID"] = $input['documentSystemID'];
                                                $alert["companySystemID"] = $input['companySystemID'];
                                                $alert["empSystemID"] = $input['empSystemID'];
                                                $alert["empID"] = $manager->managerID;
                                                $alert["docID"] = 'LA';
                                                $alert["docApprovedYN"] = 0;
                                                $alert["docSystemCode"] = $leaveDataMasterID;
                                                $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                                                $alert["alertMessage"] = "Pending " . $myDocumentName . " approval " . $leaveDataMasters->leaveDataMasterCode;
                                                $alert["alertDateTime"] = date('Y-m-d');
                                                $alert["alertViewedYN"] = 0;
                                                $alert["alertViewedDateTime"] = Null;
                                                $alert["empName"] = $empManager->empName;
                                                $alert["empEmail"] = $empManager->empEmail;
                                                $alert["emailAlertMessage"] = "Hi " . $empManager->empName . ",<p>" . $myDocumentName . " <b>" . $documentCode . "</b> is pending for your approval from <b>" . $employee->empName . "<b/>.<a href=http://gears.gulfenergy-int.com/portal/leave_approval.php>Click here to approve.</a>.<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";
                                                $alert["isEmailSend"] = 0;
                                                $alert["timeStamp"] = date('Y-m-d');
                                                Alert::create($alert);
                                            }

                                            $alert["companyID"] = $employee->empCompanyID;
                                            $alert["docSystemID"] = $input['documentSystemID'];
                                            $alert["companySystemID"] = $input['companySystemID'];
                                            $alert["empSystemID"] = $input['empSystemID'];
                                            $alert["empID"] = $manager->managerID;
                                            $alert["docID"] = 'LA';
                                            $alert["docApprovedYN"] = 0;
                                            $alert["docSystemCode"] = $leaveDataMasterID;
                                            $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                                            $alert["alertMessage"] = "Leave Application (" . $leaveDataMasters->leaveDataMasterCode . ") Submitted";
                                            $alert["alertDateTime"] = date('Y-m-d');
                                            $alert["alertViewedYN"] = 0;
                                            $alert["alertViewedDateTime"] = Null;
                                            $alert["empName"] = $employee->empName;
                                            $alert["empEmail"] = $employee->empEmail;
                                            $alert["emailAlertMessage"] = "Hi " . $employee->empName . ",<p>Your Leave Application <b>" . $leaveDataMasters->leaveDataMasterCode . "<b/> has been submitted to <b>" . $empManager->empName . "<b/> for Approval.<br><p style='color:#FF0000'><b>Note : Employees are strictly instructed not to go on the applied leave until they receive an approval of the leave application from their Supervisor/Manager.<b/></p><br><font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";
                                            $alert["isEmailSend"] = 0;
                                            $alert["timeStamp"] = date('Y-m-d');
                                            Alert::create($alert);

                                            if (!empty($leaveDataMasters) && $isLeaveDataMasterUpdate) {
                                                DB::commit();
                                                return $this->sendResponse([], 'Successfully leave application applied');
                                            } else {

                                                $input["confirmedby"] = NULL;
                                                $input["confirmedDate"] = NULL;
                                                $input["confirmedYN"] = 0;
                                                $update_array = array(
                                                    'confirmedby' => null,
                                                    'confirmedDate' => null,
                                                    'confirmedYN' => 0,
                                                );

                                                LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                                return $this->sendError('Error Occurred! Please contact the administration');
                                            }
                                        } else {


                                            $input["confirmedby"] = NULL;
                                            $input["confirmedDate"] = NULL;
                                            $input["confirmedYN"] = 0;
                                            $update_array = array(
                                                'confirmedby' => null,
                                                'confirmedDate' => null,
                                                'confirmedYN' => 0,
                                            );

                                            LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                            return $this->sendError('No Approval levels created,Please contact Administrator');

                                        }
                                    } else {

                                        $input["confirmedby"] = NULL;
                                        $input["confirmedDate"] = NULL;
                                        $input["confirmedYN"] = 0;
                                        $update_array = array(
                                            'confirmedby' => null,
                                            'confirmedDate' => null,
                                            'confirmedYN' => 0,
                                        );

                                        LeaveDataMaster::where('leavedatamasterID', $leaveDataMasterID)->update($update_array);
                                        return $this->sendError('Functional department not set. Please contact HR');
                                    }

                                } else {


                                    if (!empty($leaveDataMasters) && $isLeaveDataMasterUpdate) {
                                        DB::commit();
                                        return $this->sendResponse([], 'Successfully leave application applied');
                                    } else {
                                        return $this->sendError('Error Occurred! Please contact the administration');
                                    }
                                }
                            }
                        }
                    }
                } else {
                    return $this->sendError('You cannot create  leave Application there are pending leave application to be approved');
                }

            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 500);
        }


    }

    public function saveDocumentAttachments($files = [], $attachmentDescription = [],$attach=[])
    {
        if (!empty($files) && !empty($attachmentDescription)) {
            $i=0;
            foreach ($files as $file) {
                $size = $file->getSize();
                $sizeInKbs = 0;
                if ($size) {
                    $sizeInKbs = $size / 1024;
                }
                $data = [
                    'companyID' => $attach['companyID'],
                    'companySystemID' => $attach['companySystemID'],
                    'documentSystemCode' => $attach['documentSystemCode'],
                    'documentSystemID' => $attach['documentSystemID'],
                    'documentID' => $attach['documentID'],
                    'fileType' => $file->getClientOriginalExtension(),
                    'originalFileName' => $file->getClientOriginalName(),
                    'sizeInKbs' => $sizeInKbs,
                    'attachmentDescription'=>$attachmentDescription[$i]
                ];

                $this->saveAttachments($data, $file);
                $i++;
            }

        }

    }

    private function saveAttachments($data, $file)
    {
        $extension = $data['fileType'];

        $blockExtensions = ['ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
            'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
            'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
            'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
            'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
            'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
            'xbap', 'xnk', 'php'];

        if (in_array($extension, $blockExtensions)) {
            return $this->sendError('This type of file not allow to upload.', 500);
        }

        if (isset($data['size'])) {
            if ($data['size'] > 31457280) {
                return $this->sendError("Maximum allowed file size is 30 MB. Please upload lesser than 30 MB.", 500);
            }
        }

        if (isset($data['docExpirtyDate'])) {
            if ($data['docExpirtyDate']) {
                $data['docExpirtyDate'] = new Carbon($data['docExpirtyDate']);
            }
        }

        $data = $this->convertArrayToValue($data);

        $documentAttachments = DocumentAttachments::create($data);

        $decodeFile = base64_decode($file);

        $data_update['myFileName'] = $documentAttachments->companyID . '_' . $documentAttachments->documentID . '_' . $documentAttachments->documentSystemCode . '_' . $documentAttachments->attachmentID . '.' . $extension;

        $path = $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $data_update['myFileName'];

        Storage::disk('public')->put($path, $decodeFile);

        $data_update['isUploaded'] = 1;
        $data_update['path'] = $path;

        DocumentAttachments::where('attachmentID', $documentAttachments->attachmentID)->update($data_update);
    }
}

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
 *      saveLeaveDetails(),updateLeaveDetails - No Analyzing, discuss with shafri,fayas. shafri discuss with zahlan and asked to translate whole code into Laravel without analyzig
 * -- Date: 04- September 2019 By Rilwan saveDocumentAttachments(),saveAttachment() - to save attachments
 * -- Date 05 - September 2019 By Rilwan getLeaveDetails()
 * -- Date 06- Spetember 2019 By Rilwan updateLeaveDetails()
 * -- Date 18- November 2019 By Rilwan getLeaveBalance() - get leave balance, old portal code get this from mysql views. it consumes too much time. so i used particular table for retrieving data
 * -- Date 19- November 2019 By Rilwan getLeaveTypeWithBalance() - combine leave balance array with leave type
 * -- Date 29- May 2020 By Nasik getLeaveTypeWithBalance() - update the function to match with New Portal implementaion
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateLeaveDataMasterAPIRequest;
use App\Http\Requests\API\UpdateLeaveDataMasterAPIRequest;
use App\Models\Alert;
use App\Models\CalenderMaster;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\EmployeeManagers;
use App\Models\HrmsDocumentAttachments;
use App\Models\HRMSLeaveAccrualDetail;
use App\Models\HRMSLeaveAccrualPolicyType;
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
use App\Jobs\PushNotification;

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
            return $this->sendError('Leave Data Master not found',200);
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
            return $this->sendError('Leave Data Master not found',200);
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
            return $this->sendError('Leave Data Master not found',200);
        }

        if($leaveDataMaster->confirmedYN == 1){
            return $this->sendError('You can not delete confirmed leave',500);
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
            ->join('hrms_leavedatadetail', 'hrms_leavedatamaster.leavedatamasterID', '=', 'hrms_leavedatadetail.leavedatamasterID')
            ->where('hrms_leavedatamaster.empID', $emp_id)
            ->orderBy('hrms_leavedatamaster.leavedatamasterID', 'DESC')
            ->get();


        return $this->sendResponse($leaveHistory->toArray(), 'Leave history details retrieved successfully');
    }

    public function getLeaveAvailability(Request $request)
    {
        $fromDate = isset($request['fromDate']) ? $request['fromDate'] : null;
        $toDate = isset($request['toDate']) ? $request['toDate'] : null;
        $leaveMasterID = isset($request['leaveMasterID']) ? $request['leaveMasterID'] : null;

        if ($leaveMasterID == null) {
            return $this->sendError('Leave details not found',200);
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

    private function getLeaveAvailableForEmployee($empID, $leaveMasterID = null, $date = null, $policy=null)
    {   $balance = 0;
        if($policy != null){
            $policyBalance = HRMSLeaveAccrualPolicyType::select('daysEntitled')
                                ->where('policyType',$policy)
                                ->first();

            $balance = $policyBalance->daysEntitled;
        }elseif ($empID != null && $leaveMasterID != null) {

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

        }
        return $balance;
    }

    public function saveDocumentAttachments($files = [],$attach = [])
    {

        if (!empty($files) && !empty($attach)) {

            foreach ($files as $file) {

                /*Validation*/
                $validator = \Validator::make($file, [
                    'fileType' => 'required',
                    'originalFileName' => 'required',
                    'attachmentDescription' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                /*Validation*/

                $data = [
                    'companyID' => $attach['companyID'],
                    'companySystemID' => $attach['companySystemID'],
                    'documentSystemCode' => $attach['documentSystemCode'],
                    'documentSystemID' => $attach['documentSystemID'],
                    'documentID' => $attach['documentID'],
                    'fileType' => $file['fileType'],
                    'attachmentDescription' =>$file['attachmentDescription'],
                    'file' =>$file['file']
                ];

                $this->saveAttachments($data);

            }

        }

    }

    private function saveAttachments($data)
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

        if (isset($data['docExpirtyDate'])) {
            if ($data['docExpirtyDate']) {
                $data['docExpirtyDate'] = new Carbon($data['docExpirtyDate']);
            }
        }

        $data = $this->convertArrayToValue($data);
        $documentAttachments = HrmsDocumentAttachments::create($data);

        $disk = Helper::policyWiseDisk($documentAttachments->companySystemID, 'public');
        $decodeFile = base64_decode($data['file']);
  
        $data_update['myFileName'] = $documentAttachments->companyID . '_' . $documentAttachments->documentID . '_' . $documentAttachments->documentSystemCode . '_' . $documentAttachments->attachmentID . '.' . $extension;

        Storage::disk($disk)->put($data_update['myFileName'], $decodeFile);

        HrmsDocumentAttachments::where('attachmentID', $documentAttachments->attachmentID)->update($data_update);

    }

    public function getLeaveDetailsBacks(Request $request)
    {
        $input = $request->all();

        if (isset($input['leavedatamasterID']) && $input['leavedatamasterID']) {

            $leaveDataMaster = LeaveDataMaster::with(['detail','application_type','approved','hrapproved'])->find($input['leavedatamasterID']);
            if (empty($leaveDataMaster)) {
                return $this->sendError('Leave Data Not Found');
            }

            if(empty($leaveDataMaster->detail)){
                return $this->sendError('Leave Details Not Found');
            }

            $leaveDataDetail = $leaveDataMaster->detail;
            $output = $leaveDataMaster->toArray();
//            $output['approver'] = $leaveDataMaster->approved;
//            $output['hr'] = $leaveDataMaster->hrapproved;
//            $output['application_type'] = $leaveDataMaster->application_type;

            $output['employee'] = Employee::select('empTitle', 'empFullName')->where('empID', $leaveDataMaster->empID)->first();

            //get availabilty array
            $output['availability'] = $this->leaveDataMasterRepository->getLeaveAvailabilityArray($leaveDataDetail['leavemasterID'],$leaveDataDetail['noOfWorkingDays'],$leaveDataDetail['noOfNonWorkingDays'],$leaveDataDetail['totalDays'],$leaveDataMaster['leaveAvailable'],$leaveDataMaster['empID']);

            //set leave master to output array
            $leave_master = null;
            if(isset($leaveDataDetail->leave_master) && !empty($leaveDataDetail->leave_master)){
                $output['leave_master'] = collect($leaveDataDetail->leave_master)->only(['leavemasterID','leavetype'])->toArray();
            }

            //set leave availability details to output array
            $output['attachments'] = $this->getAttachments($leaveDataMaster->CompanyID, $leaveDataMaster->documentID, $leaveDataMaster->leavedatamasterID);

            return $this->sendResponse($output, 'Leave details retrieved successfully');
        }
        return $this->sendError('leavedatamasterID Not Found', 200);
    }

    public function getLeaveDetails(Request $request)
    {

        $input = $request->all();

        if (isset($input['leavedatamasterID']) && $input['leavedatamasterID']) {

            $leaveDataMaster = LeaveDataMaster::find($input['leavedatamasterID']);
            if (collect($leaveDataMaster)->count() == 0) {
                return $this->sendError('Leave Details Not Found', 200);
            }

            $leaveArray = $leaveDataMaster->toArray();
            $leaveArray = array_except($leaveArray,['approvedYN','approvedby','approvedDate','hrapprovalYN','hrapprovedby','hrapprovedDate','leaveType',
                'modifieduser','modifiedpc','createduserGroup','createdpc','timestamp']);

            $employee = Employee::select('empTitle', 'empFullName')->where('empID', $leaveDataMaster->empID)->first();

            // approver details
            $approved_details = array(
                'approvedYN' => $leaveDataMaster->approvedYN,
                'approvedby' => $leaveDataMaster->approvedby,
                'approvedby' => $leaveDataMaster->approvedby,
                'approvedDate' => (isset($leaveDataMaster->approvedDate)&&$leaveDataMaster->approvedDate)?Carbon::parse($leaveDataMaster->approvedDate)->format('Y-m-d H:i:s'):null,
                'reportingMangerComment' => $leaveDataMaster->detail->reportingMangerComment,
                'empTitle'=>null,
                'empFullName'=>null,

            );

            //hr approve details
            $hrapproved_details = array(
                'hrapprovalYN' => $leaveDataMaster->hrapprovalYN,
                'hrapprovedby' => $leaveDataMaster->hrapprovedby,
                'hrapprovedDate' => (isset($leaveDataMaster->hrapprovedDate)&&$leaveDataMaster->hrapprovedDate)?Carbon::parse($leaveDataMaster->hrapprovedDate)->format('Y-m-d H:i:s'):null,
                'reportingMangerComment' => null,
                'empTitle'=>null,
                'empFullName'=>null
            );


            //get approve manager details
            $approvManager = Employee::select('empTitle', 'empFullName')->where('empID', $leaveDataMaster->approvedby)->first();

            //get hr manager details
            $hrManager = Employee::select('empTitle', 'empFullName')->where('empID', $leaveDataMaster->hrapprovedby)->first();

            //get leave details
            $leaveDataDetail = collect($leaveDataMaster->detail)
                ->only(['leavemasterID','startDate', 'endDate', 'noOfWorkingDays', 'noOfNonWorkingDays', 'totalDays', 'calculatedDays', 'comment'])->toArray();

            if(empty($leaveDataDetail)){
                return $this->sendError("Leave Details Not Found",200);
            }

            //get availabilty array
            $availability = $this->leaveDataMasterRepository->getLeaveAvailabilityArray($leaveDataDetail['leavemasterID'],$leaveDataDetail['noOfWorkingDays'],$leaveDataDetail['noOfNonWorkingDays'],$leaveDataDetail['totalDays'],$leaveDataMaster['leaveAvailable'],$leaveDataMaster['empID']);

            // remove array elements from details array, because those elemts set on availability array

            $leaveDataDetail = array_except($leaveDataDetail,['leavemasterID', 'noOfWorkingDays', 'noOfNonWorkingDays', 'totalDays']);

            //get application type (New leave application or Claim)
            $application_type = collect($leaveDataMaster->application_type)->only(['Type'])->toArray();

            //merge leavedata and leave detail array
            $output = array_merge($leaveArray, $leaveDataDetail);
            $output = array_merge($output, $application_type);

            //set leave master to output array
            $leave_master = null;
            if($leaveDataMaster->detail->leave_master){
                $output['leave_master'] = collect($leaveDataMaster->detail->leave_master)->only(['leavemasterID','leavetype'])->toArray();
            }
            //set employee master to output array
            $output['employee'] = $employee;

            //set approver details to output array
            if(!empty($approvManager)){
                $output['approver'] = array_merge($approved_details,$approvManager->toArray());
            }else{
                $output['approver'] = $approved_details;
            }

            //set hr approve details to output array
            if(!empty($hrManager)){
                $output['hr'] = array_merge($hrapproved_details,$hrManager->toArray());
            }else{
                $output['hr'] = $hrapproved_details;
            }

            //set leave availability details to output array
            $output['availability'] = $availability;

            //set leave availability details to output array
            $output['attachments'] = $this->getAttachments($leaveDataMaster->CompanyID, $leaveDataMaster->documentID, $leaveDataMaster->leavedatamasterID);
            if(isset($leaveDataMaster->policy->leaveaccrualpolicyTypeID) && isset($leaveDataMaster->policy->description)){
                $output['policy'] = array(
                    'leaveaccrualpolicyTypeID'=>isset($leaveDataMaster->policy->leaveaccrualpolicyTypeID)?$leaveDataMaster->policy->leaveaccrualpolicyTypeID:null,
                    'description'=>isset($leaveDataMaster->policy->description)?$leaveDataMaster->policy->description:null
                );
            }else{
                $output['policy'] = null;
            }

            return $this->sendResponse($output, 'Leave details retrieved successfully');
        }
        return $this->sendError('leavedatamasterID Not Found', 200);
    }

    private function getAttachments($company_id, $document_code, $documentSystemCode)
    {
        $res = HrmsDocumentAttachments::where('companyID', $company_id)->where('documentID', $document_code)
            ->where('documentSystemCode', $documentSystemCode)
            ->get();

        if (collect($res->count())) {
            return $res->toArray();
        }
        return [];

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

        if ($leaveMasterID == 15) { // compassionate leave
            $policy_validator = \Validator::make($input, [
                'policytype' => 'required'
            ]);
            if ($policy_validator->fails()) {
                return $this->sendError('Policy type is required', 200);
            }
        }

        if(!isset($input['policytype'])){
            $input['policytype'] = null;
        }

        DB::beginTransaction();
        try {

            if (isset($input['empID'])) {
                $employee = Employee::where('empID', $input['empID'])->first();
            } else {
                $employee = Helper::getEmployeeInfo();
            }
            $empID = $employee->empID;

            if (!isset($input['leavedatamasterID'])) {
                $createdLeaveData = $this->saveLeaveDataMaster();
                $input['leavedatamasterID'] = $createdLeaveData->leavedatamasterID;
            }

            $leaveDataMasterID = $input['leavedatamasterID'];
            $documentCode = "LA";
            $input['documentSystemID'] = Helper::getDocumentSystemIDByCode($documentCode);
            $input['companySystemID'] = Helper::getCompanySystemIDByCode($employee->empCompanyID);
            $input['empSystemID'] = $employee->employeeSystemID;

            $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
            if (empty($leaveDataMasters)) {
                return $this->sendError("Leave Master Data not found", 200);
            }

            $leaveMasters = LeaveMaster::find($leaveMasterID);
            if (empty($leaveMasters)) {
                return $this->sendError("Leave Master not found", 200);
            }
            $restrictDays = $leaveMasters->restrictDays;

            $dateAssumed = isset($employee->details->dateAssumed) ? $employee->details->dateAssumed : null;
            $diffInMonths = 0;
            if ($dateAssumed != null) {
                $date_assumed_o = Carbon::parse($dateAssumed);
                $diffInMonths = $startDate_o->diffInMonths($date_assumed_o);
            }

            //save attachemnts

            if (isset($input['files'])) {

                $attach = [
                    'companyID' => $employee->empCompanyID,
                    'companySystemID' => $input['companySystemID'],
                    'documentSystemCode' => $leaveDataMasterID,
                    'documentID' => $documentCode,
                    'documentSystemID' => $input['documentSystemID']
                ];
                $this->saveDocumentAttachments($input['files'], $attach);
            }

            //end of save attachemnts

            $attachmentStatus = 0;
            $attachments = HrmsDocumentAttachments::where('documentSystemCode', $leaveDataMasterID)
                            ->where('documentID', $documentCode)
                            ->count();
            if ($attachments) {
                $attachmentStatus = 1;
            }

            $workingDays = CalenderMaster::where('isWorkingDay', -1)->whereBetween('calDate', [$startDate, $endDate])->count();

            $isAlreadyApplied = LeaveDataMaster::with(['detail'])
                                ->where('empID',$empID)
                                ->where('claimedYN',0)
                                ->where('leavedatamasterID','!=',$leaveDataMasterID)
                                ->whereHas('detail', function ($query) use ($startDate, $endDate) {
                                    $query->whereRaw("'$startDate' BETWEEN startDate AND endFinalDate")
                                        ->orWhereRaw("'$endDate' BETWEEN startDate AND endFinalDate");
                                })
                                ->count();
            if ($startDate < date('Y-m-d') && (!in_array($leaveMasterID, [2,3,4,15,16,21])) && ($leaveType == 1)) {
                return $this->sendError('You cannot apply leave for past days',200);
            } else if (($restrictDays != -1) && ($dateDiff < $restrictDays) && ($leaveMasterID == 1) && (($workingDays > 2)) && ($leaveType == 1)) {
                return $this->sendError('Please apply the leave before' . $restrictDays . ' days interval',200);
            } else if (($leaveMasters->isProbation == -1) && ($diffInMonths < 3)) {
                return $this->sendError('You cannot obtain any leave in your probation period',200);
            } else if (($diffInMonths < 12) && ($leaveMasterID == 13)) {
                return $this->sendError('You must complete 1 year of service with the company to be eligible for Hajj leave',200);
            } else if ($leaveMasters->isAttachmentMandatory == -1 && ($attachmentStatus == 0)) {
                return $this->sendError('Attachment is required',200);
            } else if (($workingDays > $leaveMasters->maxDays) && ($leaveMasters->maxDays != 0) && ($leaveType == 1)) {
                return $this->sendError('You cannot apply leave more than ' . $leaveMasters->maxDays . ' days',200);
            } else if ($isAlreadyApplied && $leaveType == 1) {
                return $this->sendError('You have already taken leave in this period',200);
            }

            if(isset($input['totBalance']) && $input['totBalance']){
                if(!in_array($leaveMasterID, [1,10]) && $input['totBalance']<0){
                    return $this->sendError('You do not have leave balance to apply',200);
                }
            }

            $input['startDate'] = $startDate;
            $input['endDate'] = $endDate;
            $input['modifieduser'] = $empID;
            $input['modifiedpc'] = gethostname();
            $input['entryType'] = $leaveType;
            $input['leaveType'] = $leaveMasterID;
            $input['endFinalDate'] = $endDate;
            $input['confirmedby'] = null;
            $input['confirmedDate'] = null;

            if (isset($input["confirmedYN"]) && $input["confirmedYN"] == 1) {
                $input['confirmedby'] = $empID;
                $input['confirmedDate'] = date('Y-m-d');
            }

            $multiple = $leaveMasters->allowMultipleLeave;
            $pending = LeaveDataMaster::where('empID', $empID)
                ->where('leaveType', $leaveMasterID)
                ->where('EntryType', $leaveType)
                ->where('approvedYN', 0)
                ->count();

            if ($multiple || $pending == 0) {

                $input['calculatedDays'] = $this->getCalculatedDays($leaveMasterID, $input);

                if (!empty($leaveDataMasters->detail)) {
                    $is_update = $this->leaveDataMasterRepository->updateLeaveDataDetails($input);
                } else {
                    $is_update = $this->leaveDataMasterRepository->insertLeaveDataDetails($input);
                }

                if (!empty($is_update)) {

                    $input['hrapprovalYN'] = -1;
                    $input['RollLevForApp_curr'] = 2;
                    $updateArray = array_only($input,['policytype','leaveType','confirmedYN','confirmedby','confirmedDate','hrapprovalYN','RollLevForApp_curr']);

                    $this->leaveDataMasterRepository->update($updateArray,$leaveDataMasterID);

                    if (isset($input["confirmedYN"]) && $input["confirmedYN"] == 1) {

                        $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);

                        $department = isset($employee->details->departmentMaster) ? $employee->details->departmentMaster : null;

                        if ($department != null) {
                            $hrApprovalLevels = isset($department->hrLeaveApprovalLevels) ? $department->hrLeaveApprovalLevels : 0;
                            $this->approveAndSendMails($hrApprovalLevels, $employee, $input, $leaveDataMasters);
                            DB::commit();
                            return $this->sendResponse((object)[], 'Successfully leave application applied');
                        }

                    } else {
                        DB::commit();
                        return $this->sendResponse((object)[], 'Successfully leave application applied');
                    }
                }
            } else {
                return $this->sendError('You cannot create  leave Application there are pending leave application to be approved',200);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 500);
        }

    }

    private function approveAndSendMails($hrApprovalLevels, Employee $employee, $input, LeaveDataMaster $leaveDataMasters)
    {

        if (isset($input["leaveType"]) && $input["leaveType"] == 1) {
            $myDocumentName = "Leave Application";
        } else {
            $myDocumentName = "Leave Claim";
        }

        $pushNotificationUserIds = [];
        $pushNotificationArray = [];
        $apply_emp = Employee::where('empID',$input['empID'])->first();
        for ($i = 1; $i <= $hrApprovalLevels; $i++) {
            $doc["companyID"] = $employee->empCompanyID;
            $doc["companySystemID"] = Helper::getCompanySystemIDByCode($employee->empCompanyID);
            $doc["employeeID"] = $input['empID'];
            $doc["empSystemID"] = $apply_emp->employeeSystemID;
            $doc["departmentID"] = 'HRMS';
            $doc["serviceLineCode"] = 'x';
            $doc["documentID"] = 'LA';
            $doc["documentSystemID"] = Helper::getDocumentSystemIDByCode('LA');
            $doc["documentSystemCode"] = $input["leavedatamasterID"];
            $doc["documentCode"] = isset($leaveDataMasters->leaveDataMasterCode) ? $leaveDataMasters->leaveDataMasterCode : null;
            $doc["docConfirmedByEmpID"] = $input['empID'];
            $doc["requesterID"] = Helper::getEmployeeID();
            $doc["Approver"] = Helper::getEmployeeID();
            $doc["rollLevelOrder"] = $i;
            $doc["approvedYN"] = 0;
            $doc["rejectedYN"] = 0;
            $doc["docConfirmedDate"] = date('Y-m-d');
            LeaveDocumentApproved::create($doc);
        }

        $managers = EmployeeManagers::where('empID', $input['empID'])->get();

        foreach ($managers as $manager) {

            $empManager = Employee::where('empID', $manager->managerID)->first();
            if(($empManager->discharegedYN == 0) && ($empManager->ActivationFlag == -1) && ($empManager->empLoginActive == 1) && ($empManager->empActive == 1)){
                $alert["companyID"] = $empManager->empCompanyID;
                $alert["documentSystemID"] = $input['documentSystemID'];
                $alert["companySystemID"] = $input['companySystemID'];
                $alert["empSystemID"] = $input['empSystemID'];
                $alert["empID"] = $manager->managerID;
                $alert["docID"] = 'LA';
                $alert["docApprovedYN"] = 0;
                $alert["docSystemCode"] = $input['leavedatamasterID'];
                $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
                $alert["alertMessage"] = "Pending " . $myDocumentName . " approval " . $leaveDataMasters->leaveDataMasterCode;
                $alert["alertDateTime"] = date('Y-m-d');
                $alert["alertViewedYN"] = 0;
                $alert["alertViewedDateTime"] = Null;
                $alert["empName"] = $empManager->empName;
                $alert["empEmail"] = $empManager->empEmail;
                $alert["emailAlertMessage"] = "Hi " . $empManager->empName . ",<p>" . $myDocumentName . " <b> LA </b> is pending for your approval from <b>" . $employee->empName . "<b/>.<a href=http://gears.gulfenergy-int.com/portal/leave_approval.php>Click here to approve.</a>.<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox.</font>";
                $alert["isEmailSend"] = 0;
                $alert["timeStamp"] = date('Y-m-d');

                $sendEmail = \Email::sendEmailErp($alert);
            }

            $pushNotificationMessage = "Pending " . $myDocumentName . " approval " . $leaveDataMasters->leaveDataMasterCode;
            $pushNotificationUserIds[] = $input['empSystemID'];
            $pushNotificationArray['companySystemID'] = $input['companySystemID'];
            $pushNotificationArray['documentSystemID'] = $input['documentSystemID'];
            $pushNotificationArray['id'] = $input['leavedatamasterID'];
            $pushNotificationArray['type'] = 2;
            $pushNotificationArray['documentCode'] = $leaveDataMasters->leaveDataMasterCode;
            $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;
        }

        if (!empty($pushNotificationUserIds)) {
            $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 1);
        }

        $pushNotificationUserIds = [];
        $pushNotificationArray = [];

        if(($employee->discharegedYN == 0) && ($employee->ActivationFlag == -1) && ($employee->empLoginActive == 1) && ($employee->empActive == 1)){
            $alert["companyID"] = $employee->empCompanyID;
            $alert["documentSystemID"] = $input['documentSystemID'];
            $alert["companySystemID"] = $input['companySystemID'];
            $alert["empSystemID"] = $input['empSystemID'];
            $alert["empID"] = $manager->managerID;
            $alert["docID"] = 'LA';
            $alert["docApprovedYN"] = 0;
            $alert["docSystemCode"] = $input['leavedatamasterID'];
            $alert["docCode"] = $leaveDataMasters->leaveDataMasterCode;
            $alert["alertMessage"] = "Leave Application (" . $leaveDataMasters->leaveDataMasterCode . ") Submitted";
            $alert["alertDateTime"] = date('Y-m-d');
            $alert["alertViewedYN"] = 0;
            $alert["alertViewedDateTime"] = Null;
            $alert["empName"] = $employee->empName;
            $alert["empEmail"] = $employee->empEmail;
            $alert["emailAlertMessage"] = "Hi " . $employee->empName . ",<p>Your Leave Application <b>" . $leaveDataMasters->leaveDataMasterCode . "<b/> has been submitted to <b>" . $empManager->empName . "<b/> for Approval.<br><p style='color:#FF0000'><b>Note : Employees are strictly instructed not to go on the applied leave until they receive an approval of the leave application from their Supervisor/Manager.<b/></p><br><font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!<br>This is an auto generated email. Please do not reply to this email because we are not monitoring this inbox.</font>";
            $alert["isEmailSend"] = 0;
            $alert["timeStamp"] = date('Y-m-d');

            $sendEmail = \Email::sendEmailErp($alert);
        }

        $pushNotificationMessage = "Leave Application (" . $leaveDataMasters->leaveDataMasterCode . ") Submitted";
        $pushNotificationUserIds[] = $input['empSystemID'];
        $pushNotificationArray['companySystemID'] = $input['companySystemID'];
        $pushNotificationArray['documentSystemID'] = $input['documentSystemID'];
        $pushNotificationArray['id'] = $input['leavedatamasterID'];
        $pushNotificationArray['type'] = 2;
        $pushNotificationArray['documentCode'] = $leaveDataMasters->leaveDataMasterCode;
        $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

        $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 2);
    }

    private function getCalculatedDays($leaveMasterID, $data)
    {
        if ($leaveMasterID == 16 || $leaveMasterID == 13 || $leaveMasterID == 2 || $leaveMasterID == 3 || $leaveMasterID == 4 || $leaveMasterID == 21 || $leaveMasterID == 5) {
            return $data['noOfWorkingDays'] + $data['noOfNonWorkingDays'];
        } else {
            return $data['noOfWorkingDays'];
        }
    }

    public function updateLeaveDetails(Request $request){
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
            $policy_validator = \Validator::make($input, [
                'policytype' => 'required'
            ]);
            if ($policy_validator->fails()) {
                return $this->sendError('Policy type is required', 200);
            }
        }
        if(!isset($input['policytype'])){
            $input['policytype'] = null;
        }
        DB::beginTransaction();
        try {

            if (isset($input['empID'])) {
                $employee = Employee::where('empID', $input['empID'])->first();
            } else {
                $employee = Helper::getEmployeeInfo();
            }
            $empID = $employee->empID;

            if (!isset($input['leavedatamasterID'])) {
                return $this->sendError("Leave Master Data not found", 200);
            }

            $leaveDataMasterID = $input['leavedatamasterID'];
            $documentCode = "LA";
            $input['documentSystemID'] = Helper::getDocumentSystemIDByCode($documentCode);
            $input['companySystemID'] = Helper::getCompanySystemIDByCode($employee->empCompanyID);
            $input['empSystemID'] = $employee->employeeSystemID;

            $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
            if (empty($leaveDataMasters)) {
                return $this->sendError("Leave Master Data not found", 200);
            }

            $leaveMasters = LeaveMaster::find($leaveMasterID);
            if (empty($leaveMasters)) {
                return $this->sendError("Leave Master not found", 200);
            }
            $restrictDays = $leaveMasters->restrictDays;

            $dateAssumed = isset($employee->details->dateAssumed) ? $employee->details->dateAssumed : null;
            $diffInMonths = 0;
            if ($dateAssumed != null) {
                $date_assumed_o = Carbon::parse($dateAssumed);
                $diffInMonths = $startDate_o->diffInMonths($date_assumed_o);
            }

            //save attachemnts

            if (isset($input['files'])) {

                $attach = [
                    'companyID' => $employee->empCompanyID,
                    'companySystemID' => $input['companySystemID'],
                    'documentSystemCode' => $leaveDataMasterID,
                    'documentID' => $documentCode,
                    'documentSystemID' => $input['documentSystemID']
                ];
                $this->saveDocumentAttachments($input['files'], $attach);
            }

            //end of save attachemnts

            $attachmentStatus = 0;
            $attachments = HrmsDocumentAttachments::where('documentSystemCode', $leaveDataMasterID)
                ->where('documentID', $documentCode)
                ->count();
            if ($attachments) {
                $attachmentStatus = 1;
            }

            $workingDays = CalenderMaster::where('isWorkingDay', -1)->whereBetween('calDate', [$startDate, $endDate])->count();

            $isAlreadyApplied = LeaveDataMaster::join('hrms_leavedatadetail', 'hrms_leavedatamaster.leavedatamasterID', '=', 'hrms_leavedatadetail.leavedatamasterID')
                ->where('hrms_leavedatamaster.empID', $empID)
                ->where('hrms_leavedatamaster.claimedYN', 0)
                ->where('hrms_leavedatamaster.leavedatamasterID','!=', $leaveDataMasterID)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereRaw("'$startDate' BETWEEN startDate AND endFinalDate");
                    $query->orWhereRaw("'$endDate' BETWEEN startDate AND endFinalDate");
                })
                ->count();

            if ($startDate < date('Y-m-d') && (!in_array($leaveMasterID, [2,3,4,15,16])) && ($leaveType == 1)) {
                return $this->sendError('You cannot apply leave for past days',200);
            } else if (($restrictDays != -1) && ($dateDiff < $restrictDays) && ($leaveMasterID == 1) && (($workingDays > 2)) && ($leaveType == 1)) {
                return $this->sendError('Please apply the leave before' . $restrictDays . ' days interval',200);
            } else if (($leaveMasters->isProbation == -1) && ($diffInMonths < 3)) {
                return $this->sendError('You cannot obtain any leave in your probation period',200);
            } else if (($diffInMonths < 12) && ($leaveMasterID == 13)) {
                return $this->sendError('You must complete 1 year of service with the company to be eligible for Hajj leave',200);
            } else if ($leaveMasters->isAttachmentMandatory == -1 && ($attachmentStatus == 0)) {
                return $this->sendError('Attachment is required',200);
            } else if (($workingDays > $leaveMasters->maxDays) && ($leaveMasters->maxDays != 0) && ($leaveType == 1)) {
                return $this->sendError('You cannot apply leave more than ' . $leaveMasters->maxDays . ' days',200);
            } else if ($isAlreadyApplied && $leaveType == 1) {
                return $this->sendError('You have already taken leave in this period',200);
            }
            if(isset($input['totBalance']) && $input['totBalance']){
                if(!in_array($leaveMasterID, [1,10]) && $input['totBalance']<0){
                    return $this->sendError('You do not have leave balance to apply',200);
                }
            }

            $leaveDataDetail = $leaveDataMasters->detail;

            if (!empty($leaveDataDetail)) {
                if ($restrictDays != -1 && $startDate != $leaveDataDetail->startDate && $dateDiff < $restrictDays && $leaveMasterID == 1 &&
                    ($workingDays > 2) && $leaveType == 1) {
                    return $this->sendError('Please apply the leave before' . $restrictDays . ' days interval');

                } else if (($restrictDays != -1) && $startDate != date("Y-m-d", strtotime($leaveDataDetail->startDate)) && (date("Y-m-d", strtotime($leaveDataDetail->startDate)) > $startDate) && ($leaveMasterID == 1) && ($leaveType == 1)) {
                    return $this->sendError('You cannot apply leave for past days');

                }
                $input['startDate'] = $startDate;
                $input['endDate'] = $endDate;
                $input['modifieduser'] = $empID;
                $input['modifiedpc'] = strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']));
                $input['entryType'] = $leaveType;
                $input['leaveType'] = $leaveMasterID;
                $input['endFinalDate'] = $endDate;
                if (isset($input["confirmedYN"]) && $input["confirmedYN"] == 1) {
                    $input['confirmedby'] = $empID;
                    $input['confirmedDate'] = date('Y-m-d');
                }
                $input['calculatedDays'] = $this->getCalculatedDays($leaveMasterID, $input);
                $is_update = $this->leaveDataMasterRepository->updateLeaveDataDetails($input);
                if($is_update){
                    $this->leaveDataMasterRepository->updateLeaveDataMaster($input);

                    if (isset($input["confirmedYN"]) && $input["confirmedYN"] == 1) {

                        $leaveDataMasters = LeaveDataMaster::find($leaveDataMasterID);
                        $department = isset($employee->details->departmentMaster) ? $employee->details->departmentMaster : null;

                        if ($department != null) {
                            $hrApprovalLevels = isset($department->hrLeaveApprovalLevels) ? $department->hrLeaveApprovalLevels : 0;
                            $this->approveAndSendMails($hrApprovalLevels, $employee, $input, $leaveDataMasters);
                            DB::commit();
                            return $this->sendResponse((object)[], 'Successfully leave application applied');
                        }

                    } else {
                        DB::commit();
                        return $this->sendResponse((object)[], 'Successfully leave application applied');
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getLeaveTypeWithBalance(){
        $user_data = Helper::getEmployeeInfo();
        $emp_det = Employee::select('employeeSystemID','empName','religion','gender','empID')
                    ->with( ['details' => function ($q) {
                        $q->select('employeeSystemID','expatOrLocal');
                    }])->where('employeeSystemID', $user_data->employeeSystemID)->first();
        $emp_det = $emp_det->toArray();
        $emp_data = [
            'empID'=> $emp_det['empID'],
            'gender'=> $emp_det['gender'],
            'religion'=> $emp_det['religion'],
            'isLocal'=> $emp_det['details']['expatOrLocal']
        ];
        return $this->leaveType_dropDown($emp_data);

        $policyArray = $this->getPolicyArray();
        $employee = Helper::getEmployeeInfo();
        $leaveBalance = $this->getLeaveBalance();
        $leaveMasters = LeaveMaster::select('leavemasterID','leavetype')->get();
        $output = [];
        $i = 0;
        if(!empty($leaveMasters)){
            foreach ($leaveMasters as $type){

                $balanceLeave = 0;
                if(!empty($leaveBalance)){

                    foreach ($leaveBalance as $balance){
                        if($balance['leavemasterID'] == $type->leavemasterID){
                            $balanceLeave = $balance['balance'];
                        }
                    }
                }

                if(in_array($type->leavemasterID,[2,3,4,21])){

                    if($balanceLeave!=0){
                        $output[$i]['leavemasterID'] = $type->leavemasterID;
                        $output[$i]['leavetype'] = $type->leavetype;
                        $output[$i]['balance'] = $balanceLeave;
                        $output[$i]['policy'] = [];
                        $i++;
                    }

                }elseif ($type->leavemasterID == 11){ // meternity
                    if(isset($employee->details->gender) && $employee->details->gender==2){
                        $output[$i]['leavemasterID'] = $type->leavemasterID;
                        $output[$i]['leavetype'] = $type->leavetype;
                        $output[$i]['balance'] = $balanceLeave;
                        $output[$i]['policy'] = [];
                        $i++;
                    }

                }elseif ($type->leavemasterID == 13){   // Haj
                    if($employee->religion==1){
                        $output[$i]['leavemasterID'] = $type->leavemasterID;
                        $output[$i]['leavetype'] = $type->leavetype;
                        $output[$i]['balance'] = $balanceLeave;
                        $output[$i]['policy'] = [];
                        $i++;
                    }

                }else{
                    $output[$i]['leavemasterID'] = $type->leavemasterID;
                    $output[$i]['leavetype'] = $type->leavetype;
                    $output[$i]['balance'] = $balanceLeave;
                    $output[$i]['policy'] = [];
                    if($type->leavemasterID==15){
                        $output[$i]['policy'] = $policyArray;
                    }
                    $i++;
                }

            }
        }


        return $this->sendResponse($output, 'Leave Type with balance retrieved successfully');
    }

    function leaveType_dropDown($emp_data){
        $empID = $emp_data['empID'];

        $types = DB::select("SELECT typMas.leavemasterID, typMas.leavetype
                              #SELECT typMas.leavemasterID AS `value`, typMas.leavetype AS label
                              FROM hrms_leaveaccrualmaster lMaster
                              JOIN hrms_leaveaccrualdetail lDet ON lDet.leaveaccrualMasterID = lMaster.leaveaccrualMasterID   
                              LEFT JOIN hrms_periodmaster hrPeriod ON hrPeriod.periodMasterID = lDet.leavePeriod
                              JOIN hrms_leavemaster AS typMas ON typMas.leavemasterID =  lDet.leaveType
                              WHERE empID = '{$empID}' AND approvedYN = -1 
                              GROUP BY typMas.leavemasterID, typMas.leavetype");

        $drop_down = []; $track = 0;
        foreach ($types as $val) {
            $id = $val->leavemasterID;
            $balance_data = $this->employee_leave_acc($emp_data['empID'], $id, 1);
            $leaveBalance = $balance_data['balance'];
            $is_in_list = $balance_data['in_list'];

            if ($id == 2 || $id == 3 || $id == 4 || $id == 21) {
                $isCan = false;
                if ($id == 2) {
                    if ($leaveBalance == 0) {
                        $track = 1;
                    } else {
                        $isCan = true;
                    }
                }
                if ($id == 3 && $track == 1) {
                    if ($leaveBalance == 0) {
                        $track = 2;
                    } else {
                        $isCan = true;
                    }
                }
                if ($id == 4 && $track == 2) {
                    if ($leaveBalance == 0) {
                        $track = 3;
                    } else {
                        $isCan = true;
                    }
                }
                if ($id == 21 && $track == 3) {
                    if ($leaveBalance == 0) {
                        $track = 4;
                    } else {
                        $isCan = true;
                    }
                }

                if($isCan){
                    $drop_down[] = ['leavemasterID'=> $id, 'leavetype'=> $val->leavetype,
                        'balance'=> $leaveBalance, 'policy'=> []];
                }
            }
            else if ($id == 11) {
                if ($emp_data['gender'] == 2 && $is_in_list) {
                    $drop_down[] = [ 'leavemasterID'=> $id, 'leavetype'=> $val->leavetype,
                        'balance'=> $leaveBalance, 'policy'=> []];
                }
            }
            else if ($id == 13) {
                if ($emp_data['religion'] == 1 && $is_in_list) {
                    $drop_down[] = [ 'leavemasterID'=> $id, 'leavetype'=> $val->leavetype,
                        'balance'=> $leaveBalance, 'policy'=> []];
                }
            }
            else {
                if($is_in_list){
                    $drop_down[] = [ 'leavemasterID'=> $id, 'leavetype'=> $val->leavetype,
                        'balance'=> $leaveBalance, 'policy'=> []];
                }
            }
        }

        $lType = LeaveMaster::where('leavemasterID', 15)->value('leavetype');
        $policy = $this->policyType($emp_data);
        $drop_down[] = [ 'leavemasterID'=> 15, 'leavetype'=> $lType, 'balance'=> 0, 'policy'=> $policy];

        return $this->sendResponse($drop_down, 'Leave Type with balance retrieved successfully');
    }

    function employee_leave_acc($empID, $leaveType, $makeDecision=0){
        $asOfDate = date('Y-12-31');
        $yearFirst = date('Y-01-01', strtotime($asOfDate));

        $isCarryForward = LeaveMaster::where('leavemasterID', $leaveType)->value('isCarryForward');

        $periodYear = " WHERE lStartDate BETWEEN '{$yearFirst}' AND '{$asOfDate}'";
        $periodYear2 = " AND DATE(lMaster.approvedDate) BETWEEN '{$yearFirst}' AND '{$asOfDate}'";

        if($isCarryForward == -1){
            $periodYear = " WHERE lStartDate <= '{$asOfDate}' ";
            $periodYear2 = " AND DATE(lMaster.approvedDate) <= '{$asOfDate}' ";
        }


        $accrued = DB::select("SELECT SUM( daysEntitled ) AS daysEntitled FROM (                               
                          SELECT leaveaccrualMasterCode, lMaster.Description, daysEntitled,
                          DATE_FORMAT(lDet.startDate ,'%d/%m/%y') startDate, DATE_FORMAT(lDet.endDate ,'%d/%m/%y') endDate,
                          IF ( ( lDet.leavePeriod IS NOT NULL ), hrPeriod.periodYear, YEAR ( lDet.startDate) ) AS leavePeriod,
                          IF ( ( lDet.leavePeriod IS NOT NULL ), hrPeriod.startDate, lDet.startDate ) AS lStartDate
                          FROM hrms_leaveaccrualmaster lMaster
                          JOIN hrms_leaveaccrualdetail lDet ON lDet.leaveaccrualMasterID = lMaster.leaveaccrualMasterID   
                          LEFT JOIN hrms_periodmaster hrPeriod ON hrPeriod.periodMasterID = lDet.leavePeriod
                          WHERE empID = '{$empID}' AND lDet.leaveType = {$leaveType} AND approvedYN = -1 
                          ORDER BY lMaster.leaveaccrualMasterID DESC
                          ) t1 {$periodYear}");
        $accrued = $accrued[0]->daysEntitled;

        $applied = DB::select("SELECT SUM(calculatedDays) AS calculatedDays 
                              FROM hrms_leavedatamaster lMaster
                              JOIN hrms_leavedatadetail lDet ON lDet.leavedatamasterID = lMaster.leavedatamasterID
                              WHERE empID = '{$empID}' AND lDet.leavemasterID = {$leaveType} AND approvedYN = -1 {$periodYear2}
                              ORDER BY lMaster.leavedatamasterID DESC");
        $applied = $applied[0]->calculatedDays;

        if($makeDecision == 0){
            return $accrued - $applied;
        }

        $accrued = (is_numeric($accrued))? $accrued: 0;

        return [
            'in_list'=> ($accrued > 0),
            'balance'=> ($accrued - $applied)
        ];
    }

    function policyType($emp_data){
        $drop = [];
        $emp_data['isLocal'] = ($emp_data['isLocal'] == 2)? -1: $emp_data['isLocal'];
        if($emp_data['isLocal'] == 1){
            $policy = DB::table('hrms_leaveaccrualpolicytype')->where('isExpat', 0)->get();

            if(!empty($policy)){
                foreach($policy as $val) {
                    $is_can = true;
                    if($val->isOnlyMuslim == -1){
                        if($emp_data['religion'] != 1){
                            $is_can = false;
                        }
                    }

                    if($val->isOnlyFemale == -1){
                        if($emp_data['gender'] != 2){
                            $is_can = false;
                        }
                    }

                    if($is_can){
                        $drop[] = ['leaveaccrualpolicyTypeID'=> $val->leaveaccrualpolicyTypeID, 'description'=> $val->description];
                    }
                }
            }
        }
        else{
            $policy = DB::table('hrms_leaveaccrualpolicytype')->select('leaveaccrualpolicyTypeID','description')
                ->where('isExpat', -1)->get();

            if(!empty($policy)){
                foreach($policy as $val) {
                    $drop[] = ['value'=> $val->leaveaccrualpolicyTypeID, 'label'=> $val->description];
                }
            }
        }

        return $drop;
    }

    public function getLeaveBalanceTest(){
        /*
         * getting from my sql view.
         *
         * */
        $employee = $employee = \Helper::getEmployeeInfo();
        $i = 0;
        $calculated  = 0;
        $track=0;
        $leaveBalance = [];
        $leaveAcc = $this->getLeaveAccured($employee->empID);
        //        return QryLeavesAccrued::with(['leaveMaster'])
//            ->selectRaw('SUM(IFNULL(SumOfdaysEntitled,0)) as SumOfdaysEntitled,leaveType')
//            ->where('empID',$empID)
//            ->groupBy('leaveType')
//            ->get();

        $leaveApplied = $this->getLeaveApplied($employee->empID);
        //        return QryLeavesApplied::with(['leaveMaster'])
//            ->selectRaw('SUM(IFNULL(calculatedDays,0)) as calculatedDays,SUM(totalDays) as totalDays,leavemasterID')
//            ->where('empID',$empID)
//            ->groupBy('leavemasterID')
//            ->get();
        
        if(!empty($leaveAcc)){
            foreach ($leaveAcc as $val){

                if($val->leaveType == 16 || $val->leaveType == 2 || $val->leaveType == 3 || $val->leaveType == 4 || $val->leaveType == 21){
                    $leaveBal = $this->getLeaveAccuredYearBalance($employee->empID,$val->leaveType,date('Y'));
                    //        return QryLeavesAccrued::with(['leaveMaster'])
//            ->selectRaw('SUM(IFNULL(SumOfdaysEntitled,0)) as SumOfdaysEntitled,leaveType')
//            ->where('empID',$empID)
//            ->where('leaveType',$type)
//            ->where('periodYear',$year)
//            ->groupBy('leaveType')
//            ->first();
                    $calculated = isset($leaveBal->SumOfdaysEntitled)?$leaveBal->SumOfdaysEntitled:0;

                }else{
                    $calculated	=	$val->SumOfdaysEntitled;
                }

                if(!empty($leaveApplied)){

                    foreach ($leaveApplied as $value){

                        if( $val->leaveType == $value->leavemasterID ){

                            if($value->leavemasterID == 16 || $value->leavemasterID == 2 || $value->leavemasterID == 3 || $value->leavemasterID == 4 || $value->leavemasterID == 21){
                                $leaveBal = $this->getLeaveAppliedYearBalance($employee->empID,$value->leavemasterID,date('Y'));
                                //        return QryLeavesApplied::with(['leaveMaster'])
//            ->selectRaw('SUM(IFNULL(calculatedDays,0)) as calculatedDays,leavemasterID')
//            ->where('empID',$empID)
//            ->where('leavemasterID',$type)
//            ->where('CYear',$year)
//            ->groupBy('leavemasterID')
//            ->first();
                                $leaveBalanceapplied = isset($leaveBal->calculatedDays)?$leaveBal->calculatedDays:0;
                                $calculated = $calculated - $leaveBalanceapplied;

                            }else{
                                $calculated = $calculated - $value->calculatedDays;
                            }

                        }

                    }

                }

                if($val->leaveType == 2 || $val->leaveType == 3 || $val->leaveType == 4 || $val->leaveType == 21 ) {

                    if($val->leaveType == 2){
                        if($calculated == 0){
                            $track = 1;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $val->leaveMaster->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }
                    }

                    if($val->leaveType == 3 && $track == 1){
                        if($calculated == 0){
                            $track = 2;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $val->leaveMaster->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }
                    }

                    if($val->leaveType == 4 && $track == 2){
                        if($calculated == 0){
                            $track = 3;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $val->leaveMaster->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }
                    }

                }else if($val->leaveType == 13 || $val->leaveType == 20){

                    if($calculated < 0){
                        $leaveBalance [$i]['leaveType'] = $val->leaveMaster->leavetype;
                        $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                        $leaveBalance [$i++]['balance'] = 0;
                    }else{
                        $leaveBalance [$i]['leaveType'] = $val->leaveMaster->leavetype;
                        $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                        $leaveBalance [$i++]['balance'] = $calculated;
                    }

                }else{

                    $leaveBalance [$i]['leaveType'] = $val->leaveMaster->leavetype;
                    $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                    $leaveBalance [$i++]['balance'] = $calculated;

                }
            }
        }
        return $leaveBalance;
    }

    private function getLeaveAccured($empID) {

        return HRMSLeaveAccrualDetail::selectRaw('empID,leaveType,SUM(IFNULL(daysEntitled,0)) as SumOfdaysEntitled, leaveType, YEAR(endDate) as CYear')
            ->groupBy('leaveType')
            ->whereHas('master', function ($q) {
                $q->where('approvedYN',-1);
            })
            ->where('empID',$empID)
            ->with(['master','leave_master'])
            ->get();

    }

    private function getLeaveAccuredYearBalance($empID, $type, $year) {

        return HRMSLeaveAccrualDetail::selectRaw('empID,leaveType,SUM(IFNULL(daysEntitled,0)) as SumOfdaysEntitled, leaveType, YEAR(startDate) as CYear')
            ->groupBy('leaveType')
            ->whereHas('master', function ($q) {
                $q->where('approvedYN',-1);
            })
            ->whereHas('period', function($q) use($year){
                $q->where('periodYear',$year);
            })
            ->where('empID',$empID)
            ->where('leaveType',$type)
            ->with(['master','leave_master'])
            ->first();

    }

    private function getLeaveApplied($empID) {

        return LeaveDataDetail::selectRaw('SUM(IFNULL(noOfWorkingDays,0)) as SumOfnoOfWorkingDays,SUM(IFNULL(totalDays,0)) as totalDays,SUM(IFNULL(calculatedDays,0)) as calculatedDays, leavemasterID, YEAR(endDate) as CYear')
            ->groupBy(DB::raw('leavemasterID,YEAR (endDate)'))
            ->whereHas('master', function ($q) use($empID){
                $q->where('approvedYN',-1);
                $q->where('empID',$empID);
            })
            ->with(['master','leave_master'])
            ->get();

    }

    private function getLeaveAppliedYearBalance($empID, $type, $year) {

        return LeaveDataDetail::selectRaw('SUM(IFNULL(noOfWorkingDays,0)) as SumOfnoOfWorkingDays,SUM(IFNULL(totalDays,0)) as totalDays,SUM(IFNULL(calculatedDays,0)) as calculatedDays, leavemasterID, YEAR(endDate) as CYear')
            ->groupBy(DB::raw('leavemasterID,YEAR (endDate)'))
            ->whereHas('master', function ($q) use($empID){
                $q->where('approvedYN',-1);
                $q->where('empID',$empID);
            })
            ->where('leavemasterID',$type)
            ->whereRaw('YEAR(endDate)='.$year)
            ->with(['master','leave_master'])
            ->first();

    }

    public function getLeaveBalance(){

        $employee = $employee = \Helper::getEmployeeInfo();
        $i = 0;
        $calculated  = 0;
        $track=0;
        $leaveBalance = [];
        $leaveAcc = $this->getLeaveAccured($employee->empID);
        $leaveApplied = $this->getLeaveApplied($employee->empID);

        if(count($leaveAcc)){

            foreach ($leaveAcc as $val){

                if($val->leaveType == 16 || $val->leaveType == 2 || $val->leaveType == 3 || $val->leaveType == 4 || $val->leaveType == 21){
                    $leaveBal = $this->getLeaveAccuredYearBalance($employee->empID,$val->leaveType,date('Y'));
                    $calculated = isset($leaveBal->SumOfdaysEntitled)?$leaveBal->SumOfdaysEntitled:0;
                }else{
                    $calculated	=	$val->SumOfdaysEntitled;
                }

                if(!empty($leaveApplied)){

                    foreach ($leaveApplied as $value){

                        if( $val->leaveType == $value->leavemasterID ){

                            if($value->leavemasterID == 16 || $value->leavemasterID == 2 || $value->leavemasterID == 3 || $value->leavemasterID == 4 || $value->leavemasterID == 21){

                                if(isset($value->CYear) &&$value->CYear == date('Y')){
                                    $leaveBal = $this->getLeaveAppliedYearBalance($employee->empID,$val->leaveType,date('Y'));
                                    $leaveBalanceapplied = isset($leaveBal->calculatedDays)?$leaveBal->calculatedDays:0;
                                    $calculated = $calculated - $leaveBalanceapplied;
                                }
                            }else{
                                $calculated = $calculated - $value->calculatedDays;
                            }

                        }

                    }

                }

                if($val->leaveType == 2 || $val->leaveType == 3 || $val->leaveType == 4 || $val->leaveType == 21 ) {

                    if($val->leaveType == 2){
                        if($calculated == 0){
                            $track = 1;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $val->leave_master->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }
                    }

                    if($val->leaveType == 3 && $track == 1){
                        if($calculated == 0){
                            $track = 2;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $val->leave_master->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }
                    }

                    if($val->leaveType == 4 && $track == 2){
                        if($calculated == 0){
                            $track = 3;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $val->leave_master->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }
                    }

                }else if($val->leaveType == 13 || $val->leaveType == 20){

                    if($calculated < 0){
                        $leaveBalance [$i]['leaveType'] = $val->leave_master->leavetype;
                        $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                        $leaveBalance [$i++]['balance'] = 0;
                    }else{
                        $leaveBalance [$i]['leaveType'] = $val->leave_master->leavetype;
                        $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                        $leaveBalance [$i++]['balance'] = $calculated;
                    }

                }else{
                    $leaveBalance [$i]['leaveType'] = $val->leave_master->leavetype;
                    $leaveBalance [$i]['leavemasterID'] = $val->leaveType;
                    $leaveBalance [$i++]['balance'] = $calculated;

                }
            }
        }else{

            if(!empty($leaveApplied)){

                foreach ($leaveApplied as $value){

                        if($value->leavemasterID == 16 || $value->leavemasterID == 2 || $value->leavemasterID == 3 || $value->leavemasterID == 4 || $value->leavemasterID == 21){
                            $leaveBal = $this->getLeaveAppliedYearBalance($employee->empID,$value->leavemasterID,date('Y'));
                            $leaveBalanceapplied = isset($leaveBal->calculatedDays)?$leaveBal->calculatedDays:0;
                            $calculated = $calculated - $leaveBalanceapplied;
                        }else{
                            $calculated = $calculated - $value->calculatedDays;
                        }

                    if($value->leavemasterID == 2 || $value->leavemasterID == 3 || $value->leavemasterID == 4 || $value->leavemasterID == 21 ) {

                        if($value->leavemasterID == 2){
                            if($calculated == 0){
                                $track = 1;
                            }else{
                                $leaveBalance [$i]['leaveType'] = $value->leave_master->leavetype;
                                $leaveBalance [$i]['leavemasterID'] = $value->leavemasterID;
                                $leaveBalance [$i++]['balance'] = $calculated;
                            }
                        }

                        if($value->leavemasterID == 3 && $track == 1){
                            if($calculated == 0){
                                $track = 2;
                            }else{
                                $leaveBalance [$i]['leaveType'] = $value->leave_master->leavetype;
                                $leaveBalance [$i]['leavemasterID'] = $value->leavemasterID;
                                $leaveBalance [$i++]['balance'] = $calculated;
                            }
                        }

                        if($value->leavemasterID == 4 && $track == 2){
                            if($calculated == 0){
                                $track = 3;
                            }else{
                                $leaveBalance [$i]['leaveType'] = $value->leave_master->leavetype;
                                $leaveBalance [$i]['leavemasterID'] = $value->leavemasterID;
                                $leaveBalance [$i++]['balance'] = $calculated;
                            }
                        }

                    }else if($value->leavemasterID == 13 || $value->leavemasterID == 20){

                        if($calculated < 0){
                            $leaveBalance [$i]['leaveType'] = $value->leave_master->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $value->leavemasterID;
                            $leaveBalance [$i++]['balance'] = 0;
                        }else{
                            $leaveBalance [$i]['leaveType'] = $value->leave_master->leavetype;
                            $leaveBalance [$i]['leavemasterID'] = $value->leavemasterID;
                            $leaveBalance [$i++]['balance'] = $calculated;
                        }

                    }else{
                        $leaveBalance [$i]['leaveType'] = $value->leave_master->leavetype;
                        $leaveBalance [$i]['leavemasterID'] = $value->leavemasterID;
                        $leaveBalance [$i++]['balance'] = $calculated;

                    }
                }

            }

        }

//         check for nopay
        if(!empty($leaveApplied)){
            $typeName ='';
            $masterID = 0;
            $calculated = 0;

            foreach ($leaveApplied as $value) {
                if ($value->leavemasterID == 10 ) {
                    $leaveBalanceapplied = isset($value->calculatedDays) ? $value->calculatedDays : 0;
                    $calculated = $calculated - $leaveBalanceapplied;
                    $typeName = $value->leave_master->leavetype;
                    $masterID= $value->leavemasterID;
                    $calculated = $calculated;
                }
            }

            if($masterID>0){
                $leaveBalance [$i]['leaveType'] = $typeName;
                $leaveBalance [$i]['leavemasterID'] = $masterID;
                $leaveBalance [$i++]['balance'] = $calculated;
            }
        }
        //end of nopay

        return $leaveBalance;
    }

    private function getPolicyArray(){

        $employee = Helper::getEmployeeInfo();
         $checkEmployee = Employee::where('employeeSystemID',$employee->employeeSystemID)
                                ->with(['details'=> function($q) {
                                    $q->with(['country']);
                                }])->first();
        $religion = ($employee->religion==1)?-1:0;
        $gender = ((isset($checkEmployee->details->gender)) && ($checkEmployee->details->gender==2))?-1:0;
        $isLocal = ((isset($checkEmployee->details->country->isLocal)) && ($checkEmployee->details->country->isLocal==1))?1:0;

        $policy = HRMSLeaveAccrualPolicyType::select('leaveaccrualpolicyTypeID','description')
            ->where(function ($q) use($isLocal,$religion,$gender){
            if($isLocal){
                $q->where(function($q) use($religion,$gender){
                    $q->where('isExpat',0)
                        ->orWhere(function($q) use($religion,$gender){
                            $q->where('isOnlyFemale',$gender)
                                ->where('isOnlyMuslim',$religion);
                        });
                });
            }else{
                $q->where('isExpat',-1);
            }
        })->get();

        return $policy->toArray();
    }

}

<?php
/**
 * =============================================
 * -- File Name : LeaveDocumentApprovedAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Leave Application
 * -- Author :
 * -- Create date :
 * -- Description : This file contains the all CRUD for Leave Document Approved
 * -- REVISION HISTORY
 * -- Date: 20 -November 2019 By: Rilwan Description: Added new functions named as getLeaveApproval()
 * -- Date: 21 -November 2019 By: Rilwan Description: Added new functions named as referBack()
 *
 */
namespace App\Http\Controllers\API;

use App\helper\email;
use App\helper\Helper;
use App\Http\Requests\API\CreateLeaveDocumentApprovedAPIRequest;
use App\Http\Requests\API\UpdateLeaveDocumentApprovedAPIRequest;
use App\Models\Company;
use App\Models\DepartmentMaster;
use App\Models\Employee;
use App\Models\employeeDepartmentDelegation;
use App\Models\EmployeeManagers;
use App\Models\ExpenseClaim;
use App\Models\LeaveDataDetail;
use App\Models\LeaveDataMaster;
use App\Models\LeaveDocumentApproved;
use App\Models\LeaveMaster;
use App\Repositories\LeaveDocumentApprovedRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Jobs\PushNotification;

/**
 * Class LeaveDocumentApprovedController
 * @package App\Http\Controllers\API
 */

class LeaveDocumentApprovedAPIController extends AppBaseController
{
    /** @var  LeaveDocumentApprovedRepository */
    private $leaveDocumentApprovedRepository;

    public function __construct(LeaveDocumentApprovedRepository $leaveDocumentApprovedRepo)
    {
        $this->leaveDocumentApprovedRepository = $leaveDocumentApprovedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDocumentApproveds",
     *      summary="Get a listing of the LeaveDocumentApproveds.",
     *      tags={"LeaveDocumentApproved"},
     *      description="Get all LeaveDocumentApproveds",
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
     *                  @SWG\Items(ref="#/definitions/LeaveDocumentApproved")
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
        $this->leaveDocumentApprovedRepository->pushCriteria(new RequestCriteria($request));
        $this->leaveDocumentApprovedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $leaveDocumentApproveds = $this->leaveDocumentApprovedRepository->all();

        return $this->sendResponse($leaveDocumentApproveds->toArray(), trans('custom.leave_document_approveds_retrieved_successfully'));
    }

    /**
     * @param CreateLeaveDocumentApprovedAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/leaveDocumentApproveds",
     *      summary="Store a newly created LeaveDocumentApproved in storage",
     *      tags={"LeaveDocumentApproved"},
     *      description="Store LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDocumentApproved that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDocumentApproved")
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
     *                  ref="#/definitions/LeaveDocumentApproved"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateLeaveDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->create($input);

        return $this->sendResponse($leaveDocumentApproved->toArray(), trans('custom.leave_document_approved_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/leaveDocumentApproveds/{id}",
     *      summary="Display the specified LeaveDocumentApproved",
     *      tags={"LeaveDocumentApproved"},
     *      description="Get LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDocumentApproved",
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
     *                  ref="#/definitions/LeaveDocumentApproved"
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
        /** @var LeaveDocumentApproved $leaveDocumentApproved */
        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->findWithoutFail($id);

        if (empty($leaveDocumentApproved)) {
            return $this->sendError(trans('custom.leave_document_approved_not_found'));
        }

        return $this->sendResponse($leaveDocumentApproved->toArray(), trans('custom.leave_document_approved_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateLeaveDocumentApprovedAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/leaveDocumentApproveds/{id}",
     *      summary="Update the specified LeaveDocumentApproved in storage",
     *      tags={"LeaveDocumentApproved"},
     *      description="Update LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDocumentApproved",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="LeaveDocumentApproved that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/LeaveDocumentApproved")
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
     *                  ref="#/definitions/LeaveDocumentApproved"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateLeaveDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        /** @var LeaveDocumentApproved $leaveDocumentApproved */
        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->findWithoutFail($id);

        if (empty($leaveDocumentApproved)) {
            return $this->sendError(trans('custom.leave_document_approved_not_found'));
        }

        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->update($input, $id);

        return $this->sendResponse($leaveDocumentApproved->toArray(), trans('custom.leavedocumentapproved_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/leaveDocumentApproveds/{id}",
     *      summary="Remove the specified LeaveDocumentApproved from storage",
     *      tags={"LeaveDocumentApproved"},
     *      description="Delete LeaveDocumentApproved",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of LeaveDocumentApproved",
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
        /** @var LeaveDocumentApproved $leaveDocumentApproved */
        $leaveDocumentApproved = $this->leaveDocumentApprovedRepository->findWithoutFail($id);

        if (empty($leaveDocumentApproved)) {
            return $this->sendError(trans('custom.leave_document_approved_not_found'));
        }

        $leaveDocumentApproved->delete();

        return $this->sendResponse($id, trans('custom.leave_document_approved_deleted_successfully'));
    }

    public function getLeaveApproval(Request $request){

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $user = Helper::getEmployeeInfo();

        $leave = LeaveDocumentApproved::
            with(['leave.employee','leave','leave.leave_type'])
            ->where('approvedYN',0)
            ->where('rejectedYN',0)
            ->where('hrApproval',0)
            ->where('rollLevelOrder',1)
            ->where('documentID','LA')
            ->whereHas('leave', function ($q) use ($user){
                $q->whereHas('employee', function ($q) use ($user){
                    $q->whereHas('employee_managers' , function ($query) use ($user){
                        $query->where('managerID', $user->empID)
                              ->where('isFunctionalManager', -1);
                    });
                });
            });

//        $search = $request->input('search.value');
//        if($search){
//            $leave =   $leave->where(function ($query) use($search){
//                $query->where('leaveDataMasterCode','LIKE',"%{$search}%");
//            });
//        }

        return \DataTables::eloquent($leave)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function leaveReferBack(Request $request){

        $input = $request->all();
        $user = Helper::getEmployeeInfo();
        $validator = \Validator::make($input, [
            'rejectedComments' => 'required',
            'documentApprovedID' => 'required|numeric|min:1'

        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $input = $this->convertArrayToSelectedValue($input, array('rejectedComments', 'documentApprovedID'));

        $leaveDocumentApproved = LeaveDocumentApproved::find($input['documentApprovedID']);

        if(empty($leaveDocumentApproved)){
            return $this->sendError(trans('custom.leave_document_approved_details_not_found'));
        }

        $input['companySystemID'] = $leaveDocumentApproved->companySystemID;
        $input['documentSystemID'] = $leaveDocumentApproved->documentSystemID;
        $input['empSystemID'] = $leaveDocumentApproved->empSystemID;
        $documentSystemCode = $leaveDocumentApproved->documentSystemCode;

        $company= Company::find($input['companySystemID']);
        $companyName = $company->CompanyName;

        $leaveDetails = LeaveDataMaster::with(['detail'])
                                    ->where('leavedatamasterID',$documentSystemCode)
                                    ->whereHas('detail')
                                    ->first();
        if(empty($leaveDetails)){
            return $this->sendError(trans('custom.leave_details_not_found_1'));
        }
        DB::beginTransaction();

        try {

            $isDelete = LeaveDocumentApproved::where('documentApprovedID',$input['documentApprovedID'])->delete();
            if($isDelete){
                $this->updateLeaveMaster($documentSystemCode);
                $this->updateLeaveDetail($documentSystemCode,$input['rejectedComments']);

                $documentName = ($leaveDetails->EntryType == 1)? "Leave Application":"Leave Claim";

                $originator = Employee::where('empID',$leaveDetails->confirmedby)->first();

                $emails[] = array('empSystemID' => $input['empSystemID'],
                    'companySystemID' => $input['companySystemID'],
                    'docSystemID' => $input['documentSystemID'],
                    'alertMessage' => "Referred Back ".$documentName." ".$leaveDetails->leaveDataMasterCode,
                    'emailAlertMessage' => trans('email.hi') . " ".$originator->empName.",<p> The ".$documentName."<b> " .$leaveDetails->leaveDataMasterCode."</b> is referred back by ". $user->empName." from ".$companyName.". Please Check it.<p>Comment: ".$input["rejectedComments"],
                    'docSystemCode' => $documentSystemCode);

                $pushNotificationMessage = "The ".$documentName." " .$leaveDetails->leaveDataMasterCode." is referred back by ". $user->empName." from ".$companyName;
                $pushNotificationUserIds[] = $input['empSystemID'];
                $pushNotificationArray['companySystemID'] = $input['companySystemID'];
                $pushNotificationArray['documentSystemID'] = $input['documentSystemID'];
                $pushNotificationArray['id'] = $documentSystemCode;
                $pushNotificationArray['type'] = 2;
                $pushNotificationArray['documentCode'] = $leaveDetails->leaveDataMasterCode;
                $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

                $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 2);

                $isSendMail = email::sendEmail($emails);
                if(isset($isSendMail['success']) && $isSendMail['success']){
                    DB::commit();
                    return $this->sendResponse([],trans('custom.successfully_referred_back'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    private function updateLeaveMaster($leavedatamasterID){
        $updateArray = [
            'confirmedYN' =>0,
            'hrapprovalYN' =>0,
            'approvedYN' =>0,
        ];
        return LeaveDataMaster::where('leavedatamasterID',$leavedatamasterID)->update($updateArray);
    }

    private function updateLeaveDetail($leavedatamasterID,$reportingMangerComment){
        $updateArray = [
            'reportingMangerComment' => $reportingMangerComment
        ];
        return LeaveDataDetail::where('leavedatamasterID',$leavedatamasterID)->update($updateArray);
    }

    public function approveLeave(Request $request) {

        $input = $request->all();
        $validator = \Validator::make($input, [
            'documentApprovedID' => 'required|numeric|min:1'

        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $leaveDocumentApproved = LeaveDocumentApproved::find($input['documentApprovedID']);
        if(empty($leaveDocumentApproved)){
            return $this->sendError(trans('custom.leave_document_approved_details_not_found'));
        }
        $documentSystemCode = $leaveDocumentApproved->documentSystemCode;

        $leaveDetails = LeaveDataMaster::with(['detail'])
            ->where('leavedatamasterID',$documentSystemCode)
            ->whereHas('detail')
            ->first();

        if(empty($leaveDetails)){
            return $this->sendError(trans('custom.leave_details_not_found_1'));
        }

        $user = Helper::getEmployeeInfo();

        $isManagerMatch = EmployeeManagers::where('empID',$leaveDetails->empID)
                            ->where('managerID',$user->empID)
                            ->first();

        if(empty($isManagerMatch)){
            return $this->sendError('Not Allowed, Only Reporting Manager can approve');
        }

        //update document approved
        DB::beginTransaction();
        try{
            $updateApproveArray = [
                'approvedYN'=>-1,
                'employeeID'=>$user->empID,
                'empSystemID'=>$user->employeeSystemID,
                'approvedDate'=>date('Y-m-d H:i:s')
            ];
            LeaveDocumentApproved::where('documentApprovedID',$input['documentApprovedID'])->update($updateApproveArray);
            // To do - if leave claim pending mail should go - claim process

            $updateArray = [
                'approvedYN'=>-1,
                'approvedby'=>$user->empID,
                'approvedByUserSystemID'=>$user->employeeSystemID,
                'approvedDate'=>date('Y-m-d H:i:s')
            ];
            LeaveDataMaster::where('leavedatamasterID',$leaveDetails->leavedatamasterID)->update($updateArray);

            $empData = Employee::where('empID',$leaveDetails->confirmedby)->first();

            $documentName = ($leaveDetails->EntryType == 1)? "Leave Application":"Leave Claim";
            $pushNotificationUserIds = [];
            $pushNotificationArray = [];
            $emails[] = array(
                'empSystemID' => $empData->employeeSystemID,
                'companySystemID' => $empData->empCompanySystemID,
                'docSystemID' => 37,
                'alertMessage' => "Approved " .$leaveDetails->leaveDataMasterCode,
                'emailAlertMessage' => $documentName ." <b>".$leaveDetails->leaveDataMasterCode."</b> has been approved.",
                'docSystemCode' => $documentSystemCode);

            $pushNotificationMessage = $documentName ." ".$leaveDetails->leaveDataMasterCode." has been approved.";
            $pushNotificationUserIds[] = $empData->employeeSystemID;
            $pushNotificationArray['companySystemID'] = $empData->empCompanySystemID;
            $pushNotificationArray['documentSystemID'] = 37;
            $pushNotificationArray['id'] = $documentSystemCode;
            $pushNotificationArray['type'] = 2;
            $pushNotificationArray['documentCode'] = $leaveDetails->leaveDataMasterCode;
            $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

            $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 2);
            if($leaveDetails->hrapprovedby){
                $pushNotificationUserIds = [];
                $pushNotificationArray = [];
                $hr = Employee::where('empID',$leaveDetails->hrapprovedby)->first();

                if(!empty($hr)) {
                    $emails[] = array(
                        'empSystemID' => $hr->employeeSystemID,
                        'companySystemID' => $hr->empCompanySystemID,
                        'docSystemID' => 37,
                        'alertMessage' => "Approved " .$leaveDetails->leaveDataMasterCode,
                        'emailAlertMessage' => $documentName ." <b>".$leaveDetails->leaveDataMasterCode."</b> has been approved.",
                        'docSystemCode' => $documentSystemCode);

                    $pushNotificationMessage = $documentName ." ".$leaveDetails->leaveDataMasterCode." has been approved.";
                    $pushNotificationUserIds[] = $hr->employeeSystemID;
                    $pushNotificationArray['companySystemID'] = $hr->empCompanySystemID;
                    $pushNotificationArray['documentSystemID'] = 37;
                    $pushNotificationArray['id'] = $documentSystemCode;
                    $pushNotificationArray['type'] = 2;
                    $pushNotificationArray['documentCode'] = $leaveDetails->leaveDataMasterCode;
                    $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

                    $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 2);
                }
            }

            $isSendMail = email::sendEmail($emails);
            if(isset($isSendMail['success']) && $isSendMail['success']){
                DB::commit();
                return $this->sendResponse([],trans('custom.successfully_approved'));
            }

        }catch(\Exception $exception){
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }


    /*
     * documentID
     *important. all response data will cast to string because of yajra datatable issue. if it fixed by library,
     * mobile developer should warn. other wise mobile app will crack. library issue hasnot fix until V9.7.2
     * */
    public function getHRMSApprovals(Request $request){

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $user = Helper::getEmployeeInfo();

        $leave = LeaveDocumentApproved::with(['company'=>function($q){
                $q->select('companySystemID','CompanyID','CompanyName');
            }])
            ->where('approvedYN',0)
            ->where('rejectedYN',0)
            ->where(function($query) use($user) {

                // for Leave Application
                $query->where(function($q) use($user){
                    $q->where('documentSystemID',37)
                        ->where('hrApproval',0)
                        ->where('rollLevelOrder',1)
                        ->whereHas('leave', function ($q) use ($user){
                            $q->whereHas('employee', function ($q) use ($user){
                                $q->whereHas('employee_managers' , function ($query) use ($user){
                                    $query->where('managerID', $user->empID)
                                        ->where('isFunctionalManager', -1);
                                });
                            });
                        });
                });

                // for Expense Claim
                $query->orWhere(function($q) use($user){
                    $q->where('documentSystemID',6)
                        ->whereHas('expenseClaim', function ($q) use ($user){
                            $q->whereHas('created_by', function ($q) use ($user){
                                $q->whereHas('employee_managers' , function ($query) use ($user){
                                    $query->where('managerID', $user->empID)
                                          ->where('isFunctionalManager', -1);
                                });
                            });
                        });
                });

                // To do for Travel Claim

            });

        $search = $request->input('search.value');
        if($search){
            $leave =   $leave->where(function ($query) use($search){
                $query->where('documentCode','LIKE',"%{$search}%");
            });
        }

        return \DataTables::eloquent($leave)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }else{
                    $query->orderBy('documentApprovedID', 'DESC');
                }
            })
            ->addColumn('documentDescription', function ($row) {
                if($row->documentSystemID == 37){
                    return "Leave";
                }elseif($row->documentSystemID == 6){
                    return "Expense Claim";
                }else{
                    return "";
                }

            })
            ->addColumn('details', function ($row) {

                if($row->documentSystemID == 6){    // for expense claim
                    return array(
                       'requester' => $row->expenseClaim->created_by->empFullName,
                       'requesterSystemID' => $row->expenseClaim->created_by->employeeSystemID,
                       'typeID' => $row->expenseClaim->expense_claim_type->expenseClaimTypeID,
                       'typeDescription' => $row->expenseClaim->expense_claim_type->expenseClaimTypeDescription,
                       'tableMasterID' => $row->expenseClaim->expenseClaimMasterAutoID,
                       'total_amount' => $row->expenseClaim->details->sum('amount'),
                       'comment' => $row->expenseClaim->comment,
                       'currency' => $row->expenseClaim->details[0]->currency->only('currencyID','CurrencyName','CurrencyCode','DecimalPlaces'),
                    );
                }else if($row->documentSystemID == 37){ // for leave
                    return array(
                        'requester' => $row->leave->employee->empFullName,
                        'requesterSystemID' => $row->leave->employee->employeeSystemID,
                        'typeID' => $row->leave->leave_type->leavemasterID,
                        'typeDescription' => $row->leave->leave_type->leavetype,
                        'tableMasterID' => $row->leave->leavedatamasterID,
                        'startDate' => Carbon::parse($row->leave->detail->startDate)->format('Y-m-d'),
                        'endDate' => Carbon::parse($row->leave->detail->endDate)->format('Y-m-d'),
                        'comment' => $row->leave->detail->comment,
                    );
                }
                return [];
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    /**
     * function for approve HRMS documents
     * @param $input - documentApprovedID
     * @return mixed
     */
    public function approveHRMSDocument(Request $request) {

        $docInforArr =  array(
            'tableName' => 'erp_expenseclaimmaster',
            'modelName' => 'ExpenseClaim',
            'primarykey' => 'expenseClaimMasterAutoID',
            'child' => ''
        );
        $input = $request->all();
        $validator = \Validator::make($input, [
            'documentApprovedID' => 'required|numeric|min:1'

        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $leaveDocumentApproved = LeaveDocumentApproved::find($input['documentApprovedID']);
        if(empty($leaveDocumentApproved)){
            return $this->sendError(trans('custom.leave_document_approved_details_not_found'));
        }

        // check already approved
        if($leaveDocumentApproved->approvedYN == -1){
            return $this->sendError(trans('custom.document_already_approved'));
        }


        $documentSystemID = $leaveDocumentApproved->documentSystemID;
        switch ($documentSystemID){

            case 6:
                $docInforArr =  array(
                    'tableName' => 'erp_expenseclaimmaster',
                    'modelName' => 'ExpenseClaim',
                    'primarykey' => 'expenseClaimMasterAutoID',
                    'child' => 'details',
                    'documentName'=> 'Expense Claim'
                );
                break;
            case 37:
                $docInforArr =  array(
                    'tableName' => 'hrms_leavedatamaster',
                    'modelName' => 'LeaveDataMaster',
                    'primarykey' => 'leavedatamasterID',
                    'child' => 'detail',
                    'documentName'=> 'Leave Application'
                );
                break;
            default:
                return $this->sendError(trans('custom.document_id_not_found'));
        }

        $documentSystemCode = $leaveDocumentApproved->documentSystemCode;
        $email_message = $docInforArr['documentName'] ." <b>".$leaveDocumentApproved->documentCode."</b> has been approved.";
        $pushNotificationMessage = $docInforArr['documentName'] ." ".$leaveDocumentApproved->documentCode." has been approved.";
        $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $namespacedModelChild = $docInforArr["child"]; // Model name
        $modelDetails = $namespacedModel::with([$namespacedModelChild])
            ->where($docInforArr["primarykey"],$documentSystemCode)
            ->whereHas($namespacedModelChild)
            ->first();

        if(empty($modelDetails)){
            return $this->sendError(trans('custom.leave_details_not_found_1'));
        }

        $user = Helper::getEmployeeInfo();

        $emailEmployeeList = [];
        $empData = [];
        $notificationType = "";
        if($documentSystemID==37){

            $empData = Employee::where('empID',$modelDetails->confirmedby)->first();

            $isManagerMatch = EmployeeManagers::where('empID',$modelDetails->empID)
                ->where('managerID',$user->empID)
                ->first();

            if(empty($isManagerMatch)){
                return $this->sendError('Not Allowed, Only Reporting Manager can approve');
            }
            $notificationType = 2;
        }elseif ($documentSystemID==6){
            $notificationType = 3;
            $empData = Employee::where('employeeSystemID',$modelDetails->confirmedByEmpSystemID)->first();

            if($modelDetails->departmentSystemID){
                $emailEmployeeList = employeeDepartmentDelegation::with(['employee','company'])->where('companySystemID',$modelDetails->companySystemID)
                                    ->where('departmentSystemID',$modelDetails->departmentSystemID)
                                    ->where('documentSystemID',$documentSystemID)
                                    ->get();

                if(!empty($emailEmployeeList)){
                    $del_emp_name = [];
                    foreach ($emailEmployeeList as $value){
                        $del_emp_name[] = $value->employee->empName;
                    }
                    $email_message .= " It is being processed by ".join($del_emp_name,',');
                    $pushNotificationMessage .= " It is being processed by ".join($del_emp_name,',');
                }
            }
        }

        //update document approved
        DB::beginTransaction();
        try{

            $updateApproveArray = [
                'approvedYN'=>-1,
                'employeeID'=>$user->empID,
                'empSystemID'=>$user->employeeSystemID,
                'approvedDate'=>date('Y-m-d H:i:s')
            ];
            LeaveDocumentApproved::where('documentApprovedID',$input['documentApprovedID'])->update($updateApproveArray);

            // because different column names in tables

            if($documentSystemID==37){
                $updateArray = [
                    'approvedYN'=>-1,
                    'approvedby'=>$user->empID,
                    'approvedByUserSystemID'=>$user->employeeSystemID,
                    'approvedDate'=>date('Y-m-d H:i:s')
                ];
                LeaveDataMaster::where('leavedatamasterID',$documentSystemCode)->where('CompanyID',$modelDetails->CompanyID)->update($updateArray);
            }elseif($documentSystemID == 6){
                $updateArray = [
                    'approved'=>-1,
                    'approvedByUserSystemID'=>$user->employeeSystemID,
                    'approvedDate'=>date('Y-m-d H:i:s')
                ];
                ExpenseClaim::where('expenseClaimMasterAutoID',$documentSystemCode)->where('companySystemID',$modelDetails->companySystemID)->update($updateArray);
            }

            $emails[] = array(
                'empSystemID' => $empData->employeeSystemID,
                'companySystemID' => $empData->empCompanySystemID,
                'docSystemID' => $documentSystemID,
                'alertMessage' => "Approved " .$leaveDocumentApproved->documentCode,
                'emailAlertMessage' => $email_message,
                'docSystemCode' => $documentSystemCode);

            $pushNotificationUserIds[] = $empData->employeeSystemID;
            $pushNotificationArray['companySystemID'] = $empData->empCompanySystemID;
            $pushNotificationArray['documentSystemID'] = $documentSystemID;
            $pushNotificationArray['id'] = $documentSystemCode;
            $pushNotificationArray['type'] = 2;
            $pushNotificationArray['documentCode'] = $leaveDocumentApproved->documentCode;
            $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

            $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, $notificationType);


            $pushNotificationUserIds = [];
            $pushNotificationArray = [];
            if($documentSystemID==6 ){
                if($modelDetails->departmentSystemID){

                    $emailEmployeeList = employeeDepartmentDelegation::with(['employee','company'])->where('companySystemID',$modelDetails->companySystemID)
                        ->where('departmentSystemID',$modelDetails->departmentSystemID)
                        ->where('documentSystemID',$documentSystemID)
                        ->get();

                    if(!empty($emailEmployeeList)) {
                        foreach ($emailEmployeeList as $value){
                            if(!empty($value->employee)){
                                $empCompany = $value->company->CompanyName;
                                $emails[] = array(
                                    'empSystemID' => $value->employee->employeeSystemID,
                                    'companySystemID' => $value->employee->empCompanySystemID,
                                    'docSystemID' => $documentSystemID,
                                    'alertMessage' => $docInforArr['documentName']." Approved Mail to Account Payable Department",
                                    'emailAlertMessage' => "Dear " .$value->employee->empName. ",<p>Expense Claim <strong>". $leaveDocumentApproved->documentCode ."</strong> is approved in <strong>". $empCompany ."<strong/> Please process the payment.<br><br>Regards,<br>Team Gears<br>",
                                    'docSystemCode' => $documentSystemCode);

                                $pushNotificationMessage = "Expense Claim ". $leaveDocumentApproved->documentCode ." is approved in ". $empCompany ." Please process the payment";
                                $pushNotificationUserIds[] = $value->employee->employeeSystemID;
                                $pushNotificationArray['companySystemID'] = $value->employee->empCompanySystemID;
                                $pushNotificationArray['documentSystemID'] = $documentSystemID;
                                $pushNotificationArray['id'] = $documentSystemCode;
                                $pushNotificationArray['type'] = 2;
                                $pushNotificationArray['documentCode'] = $leaveDocumentApproved->documentCode;
                                $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;
                            }

                        }
                        $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, $notificationType);
                    }
                }

            }elseif ($documentSystemID==37) {
                $pushNotificationUserIds = [];
                $pushNotificationArray = [];
                if($modelDetails->hrapprovedby){
                    $hr = Employee::where('empID',$modelDetails->hrapprovedby)->first();

                    if(!empty($hr)) {
                        $emails[] = array(
                            'empSystemID' => $hr->employeeSystemID,
                            'companySystemID' => $hr->empCompanySystemID,
                            'docSystemID' => $documentSystemID,
                            'alertMessage' => "Approved " .$modelDetails->leaveDataMasterCode,
                            'emailAlertMessage' => $docInforArr['documentName'] ." <b>".$modelDetails->leaveDataMasterCode."</b> has been approved.",
                            'docSystemCode' => $documentSystemCode);

                        $pushNotificationMessage = $docInforArr['documentName'] ." ".$modelDetails->leaveDataMasterCode." has been approved.";
                        $pushNotificationUserIds[] = $hr->employeeSystemID;
                        $pushNotificationArray['companySystemID'] = $hr->empCompanySystemID;
                        $pushNotificationArray['documentSystemID'] = $documentSystemID;
                        $pushNotificationArray['id'] = $documentSystemCode;
                        $pushNotificationArray['type'] = 2;
                        $pushNotificationArray['documentCode'] = $modelDetails->leaveDataMasterCode;
                        $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

                        $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, $notificationType);
                    }
                }

            }

            $isSendMail = email::sendEmail($emails);
            if(isset($isSendMail['success']) && $isSendMail['success']){
                DB::commit();
                return $this->sendResponse([],trans('custom.successfully_approved'));
            }

        }catch(\Exception $exception){
            DB::rollBack();
            return $this->sendError($exception->getLine().$exception->getMessage());
        }

    }

    public function referBackHRMSDocument(Request $request){

        $input = $request->all();
        $user = Helper::getEmployeeInfo();
        $validator = \Validator::make($input, [
            'rejectedComments' => 'required',
            'documentApprovedID' => 'required|numeric|min:1'

        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $input = $this->convertArrayToSelectedValue($input, array('rejectedComments', 'documentApprovedID'));

        $leaveDocumentApproved = LeaveDocumentApproved::find($input['documentApprovedID']);

        if(empty($leaveDocumentApproved)){
            return $this->sendError(trans('custom.leave_document_approved_details_not_found'));
        }

        if(!$leaveDocumentApproved->companySystemID){
            return $this->sendError(trans('custom.company_system_id_not_found_on_document_approved_t'));
        }

        if(!$leaveDocumentApproved->documentSystemID){
            return $this->sendError(trans('custom.document_system_id_not_found_on_document_approved_'));
        }

        $input['companySystemID'] = $leaveDocumentApproved->companySystemID;
        $input['documentSystemID'] = $leaveDocumentApproved->documentSystemID;
        $documentSystemCode = $leaveDocumentApproved->documentSystemCode;

        $company= Company::find($input['companySystemID']);
        $companyName = $company->CompanyName;

        DB::beginTransaction();

        try {

            $isDelete = LeaveDocumentApproved::where('documentApprovedID',$input['documentApprovedID'])->delete();
            if($isDelete){
                $notificationType = "";
                $pushNotificationUserIds = [];
                $pushNotificationArray = [];
                if($input['documentSystemID'] == 6){
                    $notificationType = 3;
                    $documentName = "Expense Claim";

                    $entityDetail = ExpenseClaim::with(['details'])
                        ->where('expenseClaimMasterAutoID',$documentSystemCode)
                        ->whereHas('details')
                        ->first();

                    if(empty($entityDetail)){
                        return $this->sendError(trans('custom.expense_claim_details_not_found'));
                    }

                    $confirmEmployee = $entityDetail->confirmedByEmpID;

                    $updateArray = [
                        'rejectedComment' => $input['rejectedComments'],
                        'rejectedYN' => -1,
                        'confirmedYN' => 0
                    ];
                    ExpenseClaim::where('expenseClaimMasterAutoID',$documentSystemCode)->update($updateArray);

                }else if($input['documentSystemID'] == 37){
                    $notificationType = 2;
                    $documentName = "Leave Application";

                    $entityDetail = LeaveDataMaster::with(['detail'])
                        ->where('leavedatamasterID',$documentSystemCode)
                        ->whereHas('detail')
                        ->first();
                    if(empty($entityDetail)){
                        return $this->sendError(trans('custom.leave_details_not_found_1'));
                    }

                    $confirmEmployee = $entityDetail->confirmedby;

                    $this->updateLeaveMaster($documentSystemCode);
                    $this->updateLeaveDetail($documentSystemCode,$input['rejectedComments']);
                }

                $originator = Employee::where('empID',$confirmEmployee)->first();

                 $emails[] = array(
                    'empSystemID' => $originator->employeeSystemID,
                    'companySystemID' => $input['companySystemID'],
                    'docSystemID' => $input['documentSystemID'],
                    'alertMessage' => "Referred Back ".$documentName." ".$leaveDocumentApproved->documentCode,
                    'emailAlertMessage' => trans('email.hi') . " ".$originator->empName.",<p> The ".$documentName."<b> " .$leaveDocumentApproved->documentCode."</b> is referred back by ". $user->empName." from ".$companyName.". Please Check it.<p>Comment: ".$input["rejectedComments"],
                    'docSystemCode' => $documentSystemCode);

                $pushNotificationMessage = "The ".$documentName." " .$leaveDocumentApproved->documentCode." is referred back by ". $user->empName." from ".$companyName;
                $pushNotificationUserIds[] = $originator->employeeSystemID;
                $pushNotificationArray['companySystemID'] = $input['companySystemID'];
                $pushNotificationArray['documentSystemID'] = $input['documentSystemID'];
                $pushNotificationArray['id'] = $documentSystemCode;
                $pushNotificationArray['type'] = 2;
                $pushNotificationArray['documentCode'] = $leaveDocumentApproved->documentCode;
                $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;

                $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, $notificationType);

                $isSendMail = email::sendEmail($emails);
                if(isset($isSendMail['success']) && $isSendMail['success']){
                    DB::commit();
                    return $this->sendResponse([],trans('custom.successfully_referred_back'));
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}

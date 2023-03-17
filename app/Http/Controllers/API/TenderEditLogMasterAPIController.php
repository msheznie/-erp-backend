<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderEditLogMasterAPIRequest;
use App\Http\Requests\API\UpdateTenderEditLogMasterAPIRequest;
use App\Models\TenderEditLogMaster;
use App\Repositories\TenderEditLogMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\TenderMaster;
use App\Models\ApprovalLevel;
use App\Models\DocumentApproved;
use Illuminate\Support\Facades\Log;
use App\Models\EmployeesDepartment;
use App\Models\DocumentMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class TenderEditLogMasterController
 * @package App\Http\Controllers\API
 */

class TenderEditLogMasterAPIController extends AppBaseController
{
    /** @var  TenderEditLogMasterRepository */
    private $tenderEditLogMasterRepository;

    public function __construct(TenderEditLogMasterRepository $tenderEditLogMasterRepo)
    {
        $this->tenderEditLogMasterRepository = $tenderEditLogMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderEditLogMasters",
     *      summary="getTenderEditLogMasterList",
     *      tags={"TenderEditLogMaster"},
     *      description="Get all TenderEditLogMasters",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/TenderEditLogMaster")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->tenderEditLogMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderEditLogMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderEditLogMasters = $this->tenderEditLogMasterRepository->all();

        return $this->sendResponse($tenderEditLogMasters->toArray(), 'Tender Edit Log Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderEditLogMasters",
     *      summary="createTenderEditLogMaster",
     *      tags={"TenderEditLogMaster"},
     *      description="Create TenderEditLogMaster",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderEditLogMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderEditLogMasterAPIRequest $request)
    {
        $input = $request->all();

        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->create($input);

        return $this->sendResponse($tenderEditLogMaster->toArray(), 'Tender Edit Log Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderEditLogMasters/{id}",
     *      summary="getTenderEditLogMasterItem",
     *      tags={"TenderEditLogMaster"},
     *      description="Get TenderEditLogMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderEditLogMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderEditLogMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var TenderEditLogMaster $tenderEditLogMaster */
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            return $this->sendError('Tender Edit Log Master not found');
        }

        return $this->sendResponse($tenderEditLogMaster->toArray(), 'Tender Edit Log Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderEditLogMasters/{id}",
     *      summary="updateTenderEditLogMaster",
     *      tags={"TenderEditLogMaster"},
     *      description="Update TenderEditLogMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderEditLogMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderEditLogMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderEditLogMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderEditLogMaster $tenderEditLogMaster */
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            return $this->sendError('Tender Edit Log Master not found');
        }

        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->update($input, $id);

        return $this->sendResponse($tenderEditLogMaster->toArray(), 'TenderEditLogMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderEditLogMasters/{id}",
     *      summary="deleteTenderEditLogMaster",
     *      tags={"TenderEditLogMaster"},
     *      description="Delete TenderEditLogMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderEditLogMaster",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var TenderEditLogMaster $tenderEditLogMaster */
        $tenderEditLogMaster = $this->tenderEditLogMasterRepository->findWithoutFail($id);

        if (empty($tenderEditLogMaster)) {
            return $this->sendError('Tender Edit Log Master not found');
        }

        $tenderEditLogMaster->delete();

        return $this->sendSuccess('Tender Edit Log Master deleted successfully');
    }

    public function createTenderEditRequest(Request $request)
    {
        DB::beginTransaction();
        try 
            {
                $input = $request->all();
                    
                $TenderMaster = TenderMaster::where('id',$input["tenderid"])->first();

                $company = Company::where('companySystemID', $input['companySystemID'])->first();
                if ($company) {
                    $params['companyID'] = $company->CompanyID;
                }

                $params['companySystemID'] = $input['companySystemID'];
                $params['documentSystemCode'] = $input['tenderid'];
                $params['employeeSystemID'] = \Helper::getEmployeeSystemID();
                $params['employeeID'] = \Helper::getEmployeeID();
                $params['requestcurrellevelNo'] = 1;
                $params['currency_id'] = $TenderMaster->currency_id;
                $params['status'] = 1;
                $params['type'] = $input['type'];

                if($input['type'] == 1)
                {
                    $type = 'Edit'; 
                }
                else
                {
                    $type = 'Amend'; 
                }

                $result = TenderEditLogMaster::create($params);

    
                $TenderMaster->tender_edit_version_id =  $result['id'];
                $TenderMaster->save();

                $output = ApprovalLevel::with('approvalrole')->where('companySystemID', $input["companySystemID"])->where('documentSystemID', 108)->where('isActive', -1)->first();


                $documentApproved = [];
                if ($output) {
                    if ($output->approvalrole) {
                        foreach ($output->approvalrole as $val) {
                            if ($val->approvalGroupID) {
                                $group_id = $val->approvalGroupID;
                                $documentApproved[] = array('companySystemID' => $val->companySystemID, 'companyID' => $val->companyID, 'departmentSystemID' => $val->departmentSystemID, 'departmentID' => $val->departmentID, 'serviceLineSystemID' => $val->serviceLineSystemID, 'serviceLineCode' => $val->serviceLineID, 'documentSystemID' => $val->documentSystemID, 'documentID' => $val->documentID, 'documentSystemCode' => $input['tenderid'], 'documentCode' => $TenderMaster->tender_code, 'approvalLevelID' => $val->approvalLevelID, 'rollID' => $val->rollMasterID, 'approvalGroupID' => $val->approvalGroupID, 'rollLevelOrder' => $val->rollLevel, 'docConfirmedDate' => now(), 'docConfirmedByEmpSystemID' => \Helper::getEmployeeSystemID(), 'docConfirmedByEmpID' => \Helper::getEmployeeID(), 'timeStamp' => NOW(), 'reference_email' => null);
                            } else {
                                return ['success' => false, 'message' => 'Please set the approval group'];
                            }
                        }
                    } else {
                        return ['success' => false, 'message' => 'No approval setup created for this document'];
                    }
                }

                $doc = DocumentApproved::insert($documentApproved);

                $approvalList = EmployeesDepartment::where('employeeGroupID', 111)
                            ->whereHas('employee', function ($q) {
                                $q->where('discharegedYN', 0);
                            })
                            ->where('companySystemID', $input["companySystemID"])
                            ->where('documentSystemID', 108)
                            ->where('isActive', 1)
                            ->where('removedYN', 0);

        

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                $document = DocumentMaster::where('documentSystemID', $output->documentSystemID)->first();
                $approvedDocNameBody = $document->documentDescription . ' <b>' . $TenderMaster->tender_code . '</b>';
                $redirectUrl =  self::checkDomai();
                $body = '<p>' . $approvedDocNameBody . ' is pending for your approval for '.$type.'. <br><br><a href="' . $redirectUrl . '">Click here to approve</a></p>';
            

                $subject = "Pending " . $document->documentDescription ." ".$type. " approval " . $TenderMaster->tender_code;

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array(
                            'empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $input['companySystemID'],
                            'docSystemID' => $output->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $input['tenderid']
                        );

                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                // if (!$sendEmail["success"]) {
                //     return ['success' => false, 'message' => $sendEmail["message"]];
                // }

                DB::commit();
                return ['success' => true, 'message' => ' Successfully updated', 'data' => $emails];
            
            } 
        catch (\Exception $e) 
            {
                DB::rollback();
                    Log::error($this->failed($e));
                    return ['success' => false, 'message' => $e];
            }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }

    public static function checkDomai()
    {

        $redirectUrl =  env("ERP_APPROVE_URL"); //ex: change url to https://*.pl.uat-gears-int.com/#/approval/erp

        if (env('IS_MULTI_TENANCY') == true) {

            $url = $_SERVER['HTTP_HOST'];
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];

            $tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

            $search = '*';
            $redirectUrl = str_replace($search, $tenantDomain, $redirectUrl);
        }

        return $redirectUrl;
    }

    public function approveTenderEdit(Request $request)
    {
        DB::beginTransaction();
        try 
            {
                $input = $request->all();
                $docApproved = DocumentApproved::find($input["documentApprovedID"]);

                if ($docApproved) {

                    $empInfo = self::getEmployeeInfo();
                    $isConfirmed = TenderMaster::find($input["documentSystemCode"]);

                    if (!$isConfirmed["confirmed_yn"]) { // check document is confirmed or not
                        return ['success' => false, 'message' => 'Document is not confirmed'];
                    }
    

                    $policyConfirmedUserToApprove = '';
                    $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 31)
                            ->where('companySystemID', $isConfirmed['companySystemID'])
                            ->first();

                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $docApproved->companySystemID)
                        ->where('documentSystemID', $input["documentSystemID"])
                        ->first();
                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => 'Policy not found.'];
                    }

                    $checkUserHasApprovalAccess = EmployeesDepartment::where('employeeGroupID', $docApproved->approvalGroupID)
                        ->where('companySystemID', $docApproved->companySystemID)
                        ->where('employeeSystemID', $empInfo->employeeSystemID)
                        ->where('documentSystemID', $input["documentSystemID"])
                        ->where('isActive', 1)
                        ->where('removedYN', 0);
    
                    if ($companyDocument['isServiceLineApproval'] == -1) {
                        $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->where('ServiceLineSystemID', $docApproved->serviceLineSystemID);
                    }
    

                    $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->whereHas('employee', function ($q) {
                        $q->where('discharegedYN', 0);
                    })
                        ->groupBy('employeeSystemID')
                        ->exists();
    
                    if (!$checkUserHasApprovalAccess) {
                        if (($input["documentSystemID"] == 9 && ($isConfirmed && $isConfirmed->isFromPortal == 0)) || $input["documentSystemID"] != 9) {
                            return ['success' => false, 'message' => 'You do not have access to approve this document.'];
                        } 
                    }
    
                    if ($policyConfirmedUserToApprove && $policyConfirmedUserToApprove['isYesNO'] == 0) {
                        if ($isConfirmed[$docInforArr["confirmedEmpSystemID"]] == $empInfo->employeeSystemID) {
                            return ['success' => false, 'message' => 'Not authorized. Confirmed person cannot approve!'];
                        }
                    }
    
                    if (["documentSystemID"] == 46) {
                        if ($isConfirmed['year'] != date("Y")) {
                            return ['success' => false, 'message' => 'Budget transfer you are trying to approve is not for the current year. You cannot approve a budget transfer which is not for current year.'];
                        }
                    }
    
                    if ($docApproved->rejectedYN == -1) {
                        return ['success' => false, 'message' => 'Level is already rejected'];
                    }

                    //check document is already approved
                    $isApproved = DocumentApproved::where('documentApprovedID', $input["documentApprovedID"])->where('approvedYN', -1)->first();

                    if (!$isApproved) {
                        $approvalLevel = ApprovalLevel::find($input["approvalLevelID"]);
                        
                        if ($approvalLevel) {
                            //Budget check on the 1st level approval for PR/DR/WR
                            if ($input["rollLevelOrder"] == 1) {
                                if (BudgetConsumptionService::budgetCheckDocumentList($input["documentSystemID"])) {
                                    $budgetCheck = BudgetConsumptionService::checkBudget($input["documentSystemID"], $input["documentSystemCode"]);
                                    if ($budgetCheck['status'] && $budgetCheck['message'] != "") {
                                        if (BudgetConsumptionService::budgetBlockUpdateDocumentList($input["documentSystemID"])) {
                                            $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => -1]);
                                        }
                                        DB::commit();
                                        return ['success' => false, 'message' => $budgetCheck['message']];
                                    } else {
                                        if (BudgetConsumptionService::budgetBlockUpdateDocumentList($input["documentSystemID"])) {
                                            // update PR master table
                                            $prMasterUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['budgetBlockYN' => 0]);
                                        }
                                    }
                                }
                            }
    
                            if ($input['documentSystemID'] == 107) {
                                // pass below data for taking action in controller
                                $more_data = [
                                    'numberOfLevels' => $approvalLevel->noOfLevels,
                                    'currentLevel' => $input["rollLevelOrder"]
                                ];
                            }
    
                            if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // update the document after the final approval
    
                                $validatePostedDate = GlPostedDateService::validatePostedDate($input["documentSystemCode"], $input["documentSystemID"]);
    
                                if (!$validatePostedDate['status']) {
                                    DB::rollback();
                                    return ['success' => false, 'message' => $validatePostedDate['message']];
                                }
    
                                if($input["documentSystemID"] == 2){
                                    $purchaseOrderMaster  = ProcumentOrder::find($input["documentSystemCode"]);
                                    if ($purchaseOrderMaster && $purchaseOrderMaster->supplierID > 0) {
    
                                        $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $purchaseOrderMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $purchaseOrderMaster->purchaseOrderCode, 'documentDate' => $purchaseOrderMaster->createdDateTime, 'documentNarration' => $purchaseOrderMaster->narration, 'supplierID' => $purchaseOrderMaster->supplierID, 'supplierCode' => $purchaseOrderMaster->supplierPrimaryCode, 'supplierName' => $purchaseOrderMaster->supplierName, 'confirmedDate' => $purchaseOrderMaster->poConfirmedDate, 'confirmedBy' => $purchaseOrderMaster->poConfirmedByEmpSystemID, 'approvedDate' => $purchaseOrderMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $purchaseOrderMaster->supplierTransactionCurrencyID, 'amount' => $purchaseOrderMaster->poTotalSupplierTransactionCurrency];
                                        CreateSupplierTransactions::dispatch($masterModel);
                                    }
                                }
    
                                if($input["documentSystemID"] == 3){
    
                                    $grvMaster  = GRVMaster::find($input["documentSystemCode"]);
                                    if ($grvMaster && $grvMaster->supplierID > 0) {
    
                                        $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $grvMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $grvMaster->grvPrimaryCode, 'documentDate' => $grvMaster->createdDateTime, 'documentNarration' => $grvMaster->grvNarration, 'supplierID' => $grvMaster->supplierID, 'supplierCode' => $grvMaster->supplierPrimaryCode, 'supplierName' => $grvMaster->supplierName, 'confirmedDate' => $grvMaster->grvConfirmedDate, 'confirmedBy' => $grvMaster->grvConfirmedByEmpSystemID, 'approvedDate' => $grvMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $grvMaster->supplierTransactionCurrencyID, 'amount' => $grvMaster->grvTotalSupplierTransactionCurrency];
                                        CreateSupplierTransactions::dispatch($masterModel);
                                    }
                                }
    
    
                                if($input["documentSystemID"] == 11){
    
                                    $supplierInvMaster  = BookInvSuppMaster::find($input["documentSystemCode"]);
    
                                    if ($supplierInvMaster && $supplierInvMaster->supplierID > 0) {
    
                                        $supplierMaster = SupplierMaster::find($supplierInvMaster->supplierID);
                                        $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $supplierInvMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $supplierInvMaster->bookingInvCode, 'documentDate' => $supplierInvMaster->createdDateAndTime, 'documentNarration' => $supplierInvMaster->comments, 'supplierID' => $supplierInvMaster->supplierID, 'supplierCode' => $supplierMaster->primarySupplierCode, 'supplierName' => $supplierMaster->supplierName, 'confirmedDate' => $supplierInvMaster->confirmedDate, 'confirmedBy' => $supplierInvMaster->confirmedByEmpSystemID, 'approvedDate' => $supplierInvMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $supplierInvMaster->supplierTransactionCurrencyID, 'amount' => $supplierInvMaster->bookingAmountTrans];
                                        CreateSupplierTransactions::dispatch($masterModel);
    
                                    }
                                }
    
    
                                if($input["documentSystemID"] == 15){
    
                                    $debitNoteMaster  = DebitNote::find($input["documentSystemCode"]);
                                    if ($debitNoteMaster && $debitNoteMaster->supplierID > 0) {
    
                                        $supplierMaster = SupplierMaster::find($debitNoteMaster->supplierID);
                                        $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $debitNoteMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $debitNoteMaster->debitNoteCode, 'documentDate' => $debitNoteMaster->createdDateAndTime, 'documentNarration' => $debitNoteMaster->comments, 'supplierID' => $debitNoteMaster->supplierID, 'supplierCode' => $supplierMaster->primarySupplierCode, 'supplierName' => $supplierMaster->supplierName, 'confirmedDate' => $debitNoteMaster->confirmedDate, 'confirmedBy' => $debitNoteMaster->confirmedByEmpSystemID, 'approvedDate' => $debitNoteMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $debitNoteMaster->supplierTransactionCurrencyID, 'amount' => $debitNoteMaster->debitAmountTrans];
                                        CreateSupplierTransactions::dispatch($masterModel);
                                    }
                                }
    
                                if($input["documentSystemID"] == 4){
    
                                    $paySupplierMaster  = PaySupplierInvoiceMaster::find($input["documentSystemCode"]);
                                    if ($paySupplierMaster && $paySupplierMaster->BPVsupplierID > 0) {
    
                                        $supplierMaster = SupplierMaster::find($paySupplierMaster->BPVsupplierID);
                                        $masterModel = ['supplierPrimaryCode' => $input["supplierPrimaryCode"], 'documentSystemID' => $input["documentSystemID"], 'documentID' => $paySupplierMaster->documentID, 'documentSystemCode' => $input["documentSystemCode"], 'documentCode' => $paySupplierMaster->BPVcode, 'documentDate' => $paySupplierMaster->createdDateTime, 'documentNarration' => $paySupplierMaster->BPVNarration, 'supplierID' => $paySupplierMaster->BPVsupplierID, 'supplierCode' => $supplierMaster->primarySupplierCode, 'supplierName' => $supplierMaster->supplierName, 'confirmedDate' => $paySupplierMaster->confirmedDate, 'confirmedBy' => $paySupplierMaster->confirmedByEmpSystemID, 'approvedDate' => $paySupplierMaster->approvedDate, 'lastApprovedBy' => $empInfo->employeeSystemID, 'transactionCurrency' => $paySupplierMaster->supplierTransCurrencyID, 'amount' => $paySupplierMaster->suppAmountDocTotal];
                                        CreateSupplierTransactions::dispatch($masterModel);
                                    }
                                }
    
                                // create monthly deduction
                                if (
                                    $input["documentSystemID"] == 4 &&
                                    $input['createMonthlyDeduction'] == 1 &&
                                    Helper::checkHrmsIntergrated($input['companySystemID'])
                                ) {
    
                                    $monthly_ded = new HrMonthlyDeductionService($input['documentSystemCode']);
                                    $message = $monthly_ded->create_monthly_deduction();
    
                                    $more_data = ($message != '') ? ['custom_message' => $message] : [];
                                }
    
                                if ($input["documentSystemID"] == 99) { // asset verification
                                    $verified_date = $isConfirmed['documentDate'];
                                    AssetVerificationDetail::where('verification_id', $isConfirmed['id'])->get()->each(function ($asset) use ($verified_date) {
                                        FixedAssetMaster::where('faID', $asset['faID'])->update(['lastVerifiedDate' => $verified_date]);
                                    });
                                }
    
                                if ($input["documentSystemID"] == 97) { //stock count negative validation
                                    // $stockCountRes = StockCountService::updateStockCountAdjustmentDetail($input);
                                    // if (!$stockCountRes['status']) {
                                    //     DB::rollback();
                                    //     return ['success' => false, 'message' => $stockCountRes['message']];
                                    // }
                                }
    
                                $sourceModel = $namespacedModel::find($input["documentSystemCode"]);
    
                                if ($input["documentSystemID"] == 46) { //Budget transfer for review notfifications
                                    $budgetBlockNotifyRes = BudgetReviewService::notfifyBudgetBlockRemoval($input['documentSystemID'], $input['documentSystemCode']);
                                    if (!$budgetBlockNotifyRes['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $budgetBlockNotifyRes['message']];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 65) { //write budget to history table
                                    $budgetHistoryRes = BudgetHistoryService::updateHistory($input['documentSystemCode']);
                                    if (!$budgetHistoryRes['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $budgetHistoryRes['message']];
                                    }
                                }
    
                                if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41, 71, 87, 97])) { // already GL entry passed Check
                                    $outputGL = Models\GeneralLedger::where('documentSystemCode', $input["documentSystemCode"])->where('documentSystemID', $input["documentSystemID"])->first();
                                    if ($outputGL) {
                                        return ['success' => false, 'message' => 'GL entries are already passed for this document'];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 103) { // Asset Transfer
                                    $generatePR = AssetTransferService::generatePRForAssetTransfer($input);
                                    if (!$generatePR['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $generatePR['message']];
                                    }
                                }
    
                                $finalupdate = $namespacedModel::find($input["documentSystemCode"])->update([$docInforArr["approvedColumnName"] => $docInforArr["approveValue"], $docInforArr["approvedBy"] => $empInfo->empID, $docInforArr["approvedBySystemID"] => $empInfo->employeeSystemID, $docInforArr["approvedDate"] => now()]);
    
                                $masterData = ['documentSystemID' => $docApproved->documentSystemID, 'autoID' => $docApproved->documentSystemCode, 'companySystemID' => $docApproved->companySystemID, 'employeeSystemID' => $empInfo->employeeSystemID];
    
                                $masterDataDEO = ['documentSystemID' => $docApproved->documentSystemID, 'id' => $docApproved->id, 'companySystemID' => $docApproved->companySystemID, 'employeeSystemID' => $empInfo->employeeSystemID];
    
                                if ($input["documentSystemID"] == 57) { //Auto assign item to itemassign table
                                    $itemMaster = DB::table('itemmaster')->selectRaw('itemCodeSystem,primaryCode as itemPrimaryCode,secondaryItemCode,barcode,itemDescription,unit as itemUnitOfMeasure,itemUrl,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,financeCategoryMaster,financeCategorySub, -1 as isAssigned,companymaster.localCurrencyID as wacValueLocalCurrencyID,companymaster.reportingCurrency as wacValueReportingCurrencyID,NOW() as timeStamp, faFinanceCatID')->join('companymaster', 'companySystemID', '=', 'primaryCompanySystemID')->where('itemCodeSystem', $input["documentSystemCode"])->first();
                                    $itemAssign = Models\ItemAssigned::insert(collect($itemMaster)->toArray());
                                }
    
                                if ($input["documentSystemID"] == 56) { //Auto assign item to supplier table
                                    $supplierAssignRes = SupplierAssignService::assignSupplier($input["documentSystemCode"], $docApproved->companySystemID);
                                    if (!$supplierAssignRes['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => "Error occured while assign supplier"];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 58) { //Auto assign customer
                                    $supplierAssignRes = CustomerAssignService::assignCustomer($input["documentSystemCode"], $docApproved->companySystemID);
                                    if (!$supplierAssignRes['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => "Error occured while assign customer"];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 86) { //insert data to supplier table
                                    $resSupplierRegister = SupplierRegister::registerSupplier($input);
                                    if (!$resSupplierRegister['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $resSupplierRegister['message']];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 96) { //insert data to conversion table
                                    $conversionRes = CurrencyConversionService::setConversion($input);
                                    if (!$conversionRes['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $conversionRes['message']];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 59) { //Auto assign item to Chart Of Account
                                    $chartOfAccount = $namespacedModel::selectRaw('primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,chartOfAccountSystemID,AccountCode,AccountDescription,masterAccount,catogaryBLorPLID,catogaryBLorPL,controllAccountYN,controlAccountsSystemID,controlAccounts,isActive,isBank,AllocationID,relatedPartyYN,-1 as isAssigned,NOW() as timeStamp')->find($input["documentSystemCode"]);
                                    $chartOfAccountAssign = Models\ChartOfAccountsAssigned::insert($chartOfAccount->toArray());
                                    $assignResp = ChartOfAccountDependency::assignToReports($input["documentSystemCode"]);
                                    if (!$assignResp['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $assignResp['message']];
                                    }
    
                                    $templateAssignRes = ChartOfAccountDependency::assignToTemplateCategory($input["documentSystemCode"], $docApproved->companySystemID);
                                    if (!$templateAssignRes['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $templateAssignRes['message']];
                                    }
    
                                    $checkAndAssignRelatedParty = ChartOfAccountDependency::checkAndAssignToRelatedParty($input["documentSystemCode"], $docApproved->companySystemID);
                                    if (!$checkAndAssignRelatedParty['status']) {
                                        DB::rollback();
                                        return ['success' => false, 'message' => $checkAndAssignRelatedParty['message']];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 63) { //Create Asset Disposal
                                    $assetDisposal = self::generateAssetDisposal($masterData);
                                }
    
                                if ($input["documentSystemID"] == 17) { //Create Accrual JV Reversal
    
                                    $jvMasterData = $namespacedModel::find($input["documentSystemCode"]);
    
                                    if ($jvMasterData->jvType == 1 && $jvMasterData->isReverseAccYN == 0) {
                                        $accrualJournalVoucher = self::generateAccrualJournalVoucher($input["documentSystemCode"]);
                                    } else if ($jvMasterData->jvType == 5 && $jvMasterData->isReverseAccYN == 0) {
                                        $POAccrualJournalVoucher = self::generatePOAccrualJournalVoucher($input["documentSystemCode"]);
                                    }
                                }
    
                                // insert the record to item ledger
    
                                if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 61, 24, 7, 20, 71, 87, 97, 11])) {
    
                                    if ($input['documentSystemID'] == 71) {
                                        if ($sourceModel->isFrom != 5) {
                                            $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                                        }
                                    } else if ($input['documentSystemID'] == 11) {
                                        if ($sourceModel->documentType == 3) {
                                            $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                                        }
                                    } else {
                                        $jobIL = ItemLedgerInsert::dispatch($masterData, $dataBase);
                                    }
                                }
    
                                if ($input["documentSystemID"] == 11) {
                                    if ($sourceModel->documentType == 1 && $sourceModel->createMonthlyDeduction) {
                                        $monthlyDedRes = HrMonthlyDeductionService::createMonthlyDeductionForSupplierInvoice($masterData);
    
                                        if (!$monthlyDedRes['status']) {
                                            return ['success' => false, 'message' => $monthlyDedRes['message']];
                                        }
                                    }
                                }
    
    
    
                                if ($input["documentSystemID"] == 69) {
                                    $outputEL = Models\EliminationLedger::where('documentSystemCode', $input["documentSystemCode"])->where('documentSystemID', $input["documentSystemID"])->first();
                                    if ($outputEL) {
                                        return ['success' => false, 'message' => 'Elimination Ledger entries are already passed for this document'];
                                    }
    
                                    $jobGL = EliminationLedgerInsert::dispatch($masterData);
                                }
    
                                if ($input["documentSystemID"] == 24) {
                                    $updateReturnQty = self::updateReturnQtyInGrvDetails($masterData);
                                    if (!$updateReturnQty["success"]) {
                                        return ['success' => false, 'message' => $updateReturnQty["message"]];
                                    }
    
                                    $updateReturnQtyInPo = self::updateReturnQtyInPoDetails($masterData);
                                    if (!$updateReturnQtyInPo["success"]) {
                                        return ['success' => false, 'message' => $updateReturnQty["message"]];
                                    }
                                }
    
                                if ($input["documentSystemID"] == 87) {
    
                                    $updateReturnQtyInPo = self::updateReturnQtyInDeliveryOrderDetails($input["documentSystemCode"]);
                                    if (!$updateReturnQtyInPo["success"]) {
                                        return ['success' => false, 'message' => "Success"];
                                    }
                                }
    
    
    
                                if ($input["documentSystemID"] == 21) {
                                    //$bankLedgerInsert = \App\Jobs\BankLedgerInsert::dispatch($masterData);
                                    if ($sourceModel->pdcChequeYN == 0) {
                                        $bankLedgerInsert = self::appendToBankLedger($input["documentSystemCode"]);
                                    }
                                }
                                if ($input["documentSystemID"] == 13 && !empty($sourceModel)) {
                                    $jobCI = CreateStockReceive::dispatch($sourceModel, $dataBase);
                                }
                                if ($input["documentSystemID"] == 10 && !empty($sourceModel)) {
                                    $jobSI = CreateSupplierInvoice::dispatch($sourceModel);
                                }
                                if ($input["documentSystemID"] == 4 && !empty($sourceModel)) {
                                    //$jobPV = CreateReceiptVoucher::dispatch($sourceModel);
                                    if ($sourceModel->invoiceType == 3) {
                                        $jobPV = self::generateCustomerReceiptVoucher($sourceModel);
                                        if (!$jobPV["success"]) {
                                            return ['success' => false, 'message' => $jobPV["message"]];
                                        }
                                    } else if($sourceModel->invoiceType == 2){
                                        $jobPV = self::generatePaymentVoucher($sourceModel);
                                        if (!$jobPV["success"]) {
                                            return ['success' => false, 'message' => $jobPV["message"]];
                                        }
                                    }
                                    else {
                                        if ($sourceModel->pdcChequeYN == 0) {
                                            $bankLedger = BankLedgerInsert::dispatch($masterData);
                                        }
                                    }
                                }
    
                                if ($input["documentSystemID"] == 46 && !empty($sourceModel)) {
                                    $jobBTN = BudgetAdjustment::dispatch($sourceModel);
                                }
    
                                if ($input["documentSystemID"] == 102 && !empty($sourceModel)) { //Budget Addition Note Job
                                    $jobBDA = BudgetAdditionAdjustment::dispatch($sourceModel);
                                }
    
                                if ($input["documentSystemID"] == 61) { //create fixed asset
                                    $fixeAssetDetail = Models\InventoryReclassificationDetail::with(['master'])->where('inventoryreclassificationID', $input["documentSystemCode"])->get();
                                    $qtyRangeArr = [];
                                    if ($fixeAssetDetail) {
                                        $lastSerialNumber = 1;
                                        $lastSerial = Models\FixedAssetMaster::selectRaw('MAX(serialNo) as serialNo')->first();
                                        if ($lastSerial) {
                                            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                                        }
                                        foreach ($fixeAssetDetail as $val) {
                                            if ($val["currentStockQty"]) {
                                                $qtyRange = range(1, $val["currentStockQty"]);
                                                if ($qtyRange) {
                                                    foreach ($qtyRange as $qty) {
                                                        $documentCode = ($val["master"]["companyID"] . '\\FA' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));
                                                        $data["departmentID"] = 'AM';
                                                        $data["departmentSystemID"] = null;
                                                        $data["serviceLineSystemID"] = $val["master"]["serviceLineSystemID"];
                                                        $data["serviceLineCode"] = $val["master"]["serviceLineCode"];
                                                        $data["docOriginSystemCode"] = $val["inventoryreclassificationID"];
                                                        $data["docOrigin"] = $val["master"]["documentCode"];
                                                        $data["docOriginDetailID"] = $val["inventoryReclassificationDetailID"];
                                                        $data["companySystemID"] = $val["master"]["companySystemID"];
                                                        $data["companyID"] = $val["master"]["companyID"];
                                                        $data["documentSystemID"] = 22;
                                                        $data["documentID"] = 'FA';
                                                        $data["serialNo"] = $lastSerialNumber;
                                                        $data["itemCode"] = $val["itemSystemCode"];
                                                        $data["faCode"] = $documentCode;
                                                        $data["assetDescription"] = $val["itemDescription"];
                                                        $data["COSTUNIT"] = $val["unitCostLocal"];
                                                        $data["costUnitRpt"] = $val["unitCostRpt"];
                                                        $data["assetType"] = 1;
                                                        $data['createdPcID'] = gethostname();
                                                        $data['createdUserID'] = \Helper::getEmployeeID();
                                                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                                                        $data["timestamp"] = date('Y-m-d H:i:s');
                                                        $qtyRangeArr[] = $data;
                                                        $lastSerialNumber++;
                                                    }
                                                }
                                            }
                                        }
                                        $fixedAsset = Models\FixedAssetMaster::insert($qtyRangeArr);
                                    }
                                }
    
                                //generate customer invoice or Direct GRV
                                if ($input["documentSystemID"] == 41 && !empty($sourceModel)) {
                                    if ($sourceModel->disposalType == 1) {
                                        $jobCI = CreateCustomerInvoice::dispatch($sourceModel, $dataBase);
                                    }
                                    $updateDisposed = Models\AssetDisposalDetail::ofMaster($input["documentSystemCode"])->get();
                                    if (count($updateDisposed) > 0) {
                                        foreach ($updateDisposed as $val) {
                                            $faMaster = Models\FixedAssetMaster::find($val->faID)->update(['DIPOSED' => -1, 'disposedDate' => $sourceModel->disposalDocumentDate, 'assetdisposalMasterAutoID' => $input["documentSystemCode"]]);
                                        }
                                    }
                                }
    
    
                                // generate asset costing
                                if ($input["documentSystemID"] == 22) {
                                    $assetCosting = self::generateAssetCosting($sourceModel);
                                }
    
                                // insert the record to budget consumed data
                                if (BudgetConsumptionService::budgetConsumedDocumentList($input["documentSystemID"])) {
    
                                    $budgetConsumedRes = BudgetConsumptionService::insertBudgetConsumedData($input["documentSystemID"], $input["documentSystemCode"]);
                                    if (!$budgetConsumedRes['status']) {
                                        return ['success' => false, 'message' => $budgetConsumedRes['message']];
                                    }
                                }
    
                                // adding records to budget consumption data
                                if ($input["documentSystemID"] == 11 || $input["documentSystemID"] == 4 || $input["documentSystemID"] == 15 || $input["documentSystemID"] == 19) {
                                    $storingBudget = self::storeBudgetConsumption($masterData);
                                }
    
                                //sending email based on policy
                                if ($input["documentSystemID"] == 1 || $input["documentSystemID"] == 50 || $input["documentSystemID"] == 51 || $input["documentSystemID"] == 2 || $input["documentSystemID"] == 5 || $input["documentSystemID"] == 52 || $input["documentSystemID"] == 4) {
                                    $sendingEmail = self::sendingEmailNotificationPolicy($masterData);
                                }
    
                                if ($input["documentSystemID"] == 107) {
    
                                    $suppiler_info = SupplierRegistrationLink::where('id', '=', $docApproved->documentSystemCode)->first();
                                    if (isset($suppiler_info) && isset($docApproved->reference_email) && !empty($docApproved->reference_email)) {
    
                                        $dataEmail['empEmail'] = $docApproved->reference_email;
                                        $dataEmail['companySystemID'] = $docApproved->companySystemID;
                                        $temp = '<p>Dear Supplier, <br /></p><p>Please be informed that your KYC has been approved. <br><br> Thank You. </p>';
                                        $dataEmail['alertMessage'] = "Registration Approved";
                                        $dataEmail['emailAlertMessage'] = $temp;
                                        $sendEmail = \Email::sendEmailErp($dataEmail);
                                    }
                                }
    
                                if ($input["documentSystemID"] == 106) {
    
                                    $suppiler_info = SupplierRegistrationLink::where('id', '=', $docApproved->documentSystemCode)->first();
                                    if (isset($docApproved->reference_email) && !empty($docApproved->reference_email)) {
                                        $dataEmail['empEmail'] = $docApproved->reference_email;
                                        $dataEmail['companySystemID'] = $docApproved->companySystemID;
                                        $temp = '<p>Dear Supplier, <br /></p><p>Please be informed that your appointment has been approved. <br><br> Thank You. </p>';
                                        $dataEmail['alertMessage'] = "Appoinment Approved";
                                        $dataEmail['emailAlertMessage'] = $temp;
                                        $sendEmail = \Email::sendEmailErp($dataEmail);
                                    }
    
                                }
    
                                if ($input["documentSystemID"] == 22) {
    
                                   
                                    $acc_d = CreateAccumulatedDepreciation::dispatch($input["faID"]);
                                }
                                //
    
                                // insert the record to general ledger
                                if (in_array($input["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41, 71, 87, 97])) {
                                    if ($input['documentSystemID'] == 71) {
                                        if ($sourceModel->isFrom != 5) {
                                            $jobGL = GeneralLedgerInsert::dispatch($masterData, $dataBase);
                                        }
                                    } else if ($input['documentSystemID'] == 17) {
                                        if ($sourceModel->jvType != 9) {
                                            $jobGL = GeneralLedgerInsert::dispatch($masterData, $dataBase);
                                        }
                                    } else {
                                        $jobGL = GeneralLedgerInsert::dispatch($masterData, $dataBase);
                                    }
                                    
                                    if ($input["documentSystemID"] == 3) {
                                        $sourceData = $namespacedModel::find($input["documentSystemCode"]);
                                        $masterData['supplierID'] = $sourceData->supplierID;
                                        $jobUGRV = UnbilledGRVInsert::dispatch($masterData, $dataBase);
                                        $jobSI = CreateGRVSupplierInvoice::dispatch($input["documentSystemCode"], $dataBase);
                                        WarehouseItemUpdate::dispatch($input["documentSystemCode"]);
    
                                        if ($sourceData->interCompanyTransferYN == -1) {
                                            $consoleJVData = [
                                                'data' => InterCompanyAssetDisposal::where('grvID', $sourceData->grvAutoID)->first(),
                                                'type' => "INTER_ASSET_DISPOSAL"
                                            ];
    
                                            CreateConsoleJV::dispatch($consoleJVData);
                                        }
                                    }
    
                                    if ($input["documentSystemID"] == 21) {
                                        $sourceData = $namespacedModel::find($input["documentSystemCode"]);
                                        if ($sourceData->intercompanyPaymentID > 0) {
                                            $receiptData = [
                                                'data' => $sourceData,
                                                'type' => "FUND_TRANSFER"
                                            ];
    
                                            CreateConsoleJV::dispatch($receiptData);
                                        }
                                    }
                                }
    
                            } else {
                                // update roll level in master table
                                $rollLevelUpdate = $namespacedModel::find($input["documentSystemCode"])->update(['RollLevForApp_curr' => $input["rollLevelOrder"] + 1]);
                            }
    
                            // update record in document approved table
                            $approvedeDoc = $docApproved::find($input["documentApprovedID"])->update(['approvedYN' => -1, 'approvedDate' => now(), 'approvedComments' => $input["approvedComments"], 'employeeID' => $empInfo->empID, 'employeeSystemID' => $empInfo->employeeSystemID]);
    
                            $sourceModel = $namespacedModel::find($input["documentSystemCode"]);
                            $currentApproved = Models\DocumentApproved::find($input["documentApprovedID"]);
                            $emails = array();
                            $pushNotificationUserIds = [];
                            $pushNotificationArray = [];
                            if (!empty($sourceModel)) {
                                $document = Models\DocumentMaster::where('documentSystemID', $currentApproved->documentSystemID)->first();
                                $subjectName = $document->documentDescription . ' ' . $currentApproved->documentCode;
                                $bodyName = $document->documentDescription . ' ' . '<b>' . $currentApproved->documentCode . '</b>';
    
                                if ($sourceModel[$docInforArr["confirmedYN"]] == 1 || $sourceModel[$docInforArr["confirmedYN"]] == -1) {
    
                                    if ($approvalLevel->noOfLevels == $input["rollLevelOrder"]) { // if fully approved
                                        $subject = $subjectName . " is fully approved";
                                        $body = "<p>". $bodyName . " is fully approved . ";
                                        $pushNotificationMessage = $subject;
                                        $pushNotificationUserIds[] = $sourceModel[$docInforArr["confirmedEmpSystemID"]];
                                    } else {
    
                                        $companyDocument = Models\CompanyDocumentAttachment::where('companySystemID', $currentApproved->companySystemID)
                                            ->where('documentSystemID', $currentApproved->documentSystemID)
                                            ->first();
    
                                        if (empty($companyDocument)) {
                                            return ['success' => false, 'message' => 'Policy not found for this document'];
                                        }
    
                                        $nextLevel = $currentApproved->rollLevelOrder + 1;
    
                                        $nextApproval = Models\DocumentApproved::where('companySystemID', $currentApproved->companySystemID)
                                            ->where('documentSystemID', $currentApproved->documentSystemID)
                                            ->where('documentSystemCode', $currentApproved->documentSystemCode)
                                            ->where('rollLevelOrder', $nextLevel)
                                            ->first();
    
                                        $approvalList = Models\EmployeesDepartment::where('employeeGroupID', $nextApproval->approvalGroupID)
                                            ->whereHas('employee', function ($q) {
                                                $q->where('discharegedYN', 0);
                                            })
                                            ->where('companySystemID', $currentApproved->companySystemID)
                                            ->where('documentSystemID', $currentApproved->documentSystemID)
                                            ->where('isActive', 1)
                                            ->where('removedYN', 0);
    
    
                                        if ($companyDocument['isServiceLineApproval'] == -1) {
                                            $approvalList = $approvalList->where('ServiceLineSystemID', $currentApproved->serviceLineSystemID);
                                        }
    
                                        $approvalList = $approvalList
                                            ->with(['employee'])
                                            ->groupBy('employeeSystemID')
                                            ->get();
    
                                        $pushNotificationMessage = $subjectName . " is pending for your approval.";
    
                                        // if (in_array($input["documentSystemID"], self::documentListForClickHere())) {
                                        //     if (in_array($input["documentSystemID"], [1, 50, 51])) {
                                        //         $redirectUrl =  env("PR_APPROVE_URL");
                                        //     } else {
                                        //         $redirectUrl =  env("APPROVE_URL");
                                        //     }
                                        //     $nextApprovalBody = '<p>' . $bodyName . ' Level ' . $currentApproved->rollLevelOrder . ' is approved and pending for your approval. <br><br><a href="' . $redirectUrl . '">Click here to approve</a></p>';
                                        // } else {
                                        //     $redirectUrl =  env("ERP_APPROVE_URL");
                                        //     $nextApprovalBody = '<p>' . $bodyName . ' Level ' . $currentApproved->rollLevelOrder . ' is approved and pending for your approval. <br><br><a href="' . $redirectUrl . '">Click here to approve</a></p>';
                                        // }
    
    
    
    
                                        $redirectUrl =  self::checkDomai();
                                        //$body = '<p>' . $approvedDocNameBody . ' is pending for your approval. <br><br><a href="' . $redirectUrl . '">Click here to approve</a></p>';   
                                        $nextApprovalBody = '<p>' . $bodyName . ' Level ' . $currentApproved->rollLevelOrder . ' is approved and pending for your approval. <br><br><a href="' . $redirectUrl . '">Click here to approve</a></p>';
    
                                        $nextApprovalSubject = $subjectName . " Level " . $currentApproved->rollLevelOrder . " is approved and pending for your approval";
                                        $nextApproveNameList = "";
                                        foreach ($approvalList as $da) {
                                            if ($da->employee) {
    
                                                $nextApproveNameList = $nextApproveNameList . '<br>' . $da->employee->empName;
    
                                                $emails[] = array(
                                                    'empSystemID' => $da->employee->employeeSystemID,
                                                    'companySystemID' => $nextApproval->companySystemID,
                                                    'docSystemID' => $nextApproval->documentSystemID,
                                                    'alertMessage' => $nextApprovalSubject,
                                                    'emailAlertMessage' => $nextApprovalBody,
                                                    'docSystemCode' => $nextApproval->documentSystemCode
                                                );
    
                                                $pushNotificationUserIds[] = $da->employee->employeeSystemID;
                                            }
                                        }
    
                                        $subject = $subjectName . " Level " . $currentApproved->rollLevelOrder . " is approved and sent to next level approval";
                                        $body = '<p>'.$bodyName . " Level " . $currentApproved->rollLevelOrder . " is approved and sent to next level approval to below employees <br>" . $nextApproveNameList;
                                    }
    
    
                                    $emails[] = array(
                                        'empSystemID' => $sourceModel[$docInforArr["confirmedEmpSystemID"]],
                                        'companySystemID' => $currentApproved->companySystemID,
                                        'docSystemID' => $currentApproved->documentSystemID,
                                        'alertMessage' => $subject,
                                        'emailAlertMessage' => $body,
                                        'docSystemCode' => $input["documentSystemCode"]
                                    );
    
                                    $pushNotificationArray['companySystemID'] = $currentApproved->companySystemID;
                                    $pushNotificationArray['documentSystemID'] = $currentApproved->documentSystemID;
                                    $pushNotificationArray['id'] = $currentApproved->documentSystemCode;
                                    $pushNotificationArray['type'] = 1;
                                    $pushNotificationArray['documentCode'] = $currentApproved->documentCode;
                                    $pushNotificationArray['pushNotificationMessage'] = $pushNotificationMessage;
                                }
                            }
    
                            if ($input['documentSystemID'] == 2) {
                                 Log::info('approvedDocument function called in side general helper');
                                SendEmailForDocument::approvedDocument($input);
                            }
                            
                            $sendEmail = \Email::sendEmail($emails);
    
    
                            if (!$sendEmail["success"]) {
                                return ['success' => false, 'message' => $sendEmail["message"]];
                            }
    
                            $jobPushNotification = PushNotification::dispatch($pushNotificationArray, $pushNotificationUserIds, 1);
    
                            $webPushData = [
                                'title' => $pushNotificationMessage,
                                'body' => '',
                                'url' => isset($redirectUrl) ? $redirectUrl : "",
                            ];
    
                            // WebPushNotificationService::sendNotification($webPushData, 2, $pushNotificationUserIds, $dataBase);
    
                        } else {
                            return ['success' => false, 'message' => 'Approval level not found'];
                        }
                        DB::commit();
                        return ['success' => true, 'message' => $userMessage, 'data' => $more_data];
                    } else {
                        return ['success' => false, 'message' => 'Level is already approved'];
                    }
                } else {
                    return ['success' => false, 'message' => 'No records found'];
                }

                DB::commit();
                return ['success' => true, 'message' => ' Successfully updated', 'data' => $docApproved];
          
            } 
        catch (\Exception $e) 
            {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
    }

    public static function getEmployeeInfo()
    {
        $user = User::find(Auth::id());
        $employee = Employee::with(['profilepic', 'user_data' => function($query) {
            $query->select('uuid', 'employee_id');
        }])->find($user->employee_id);
        return $employee;
    }
}

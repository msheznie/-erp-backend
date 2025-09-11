<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Mohamed Nazir
 * -- Create date : 10 - September 2018
 * -- Description : This file contains the all CRUD for Expense Claim
 * -- REVISION HISTORY
 * -- Date: 10- September 2018 By: Fayas Description: Added new function getExpenseClaimByCompany(),getExpenseClaimFormData()
 * -- Date: 11- September 2018 By: Fayas Description: Added new function getExpenseClaimAudit(),printExpenseClaim(),getPaymentStatusHistory()
 * -- Date: 29- August 2019 By: Rilwan Description: Added new function getExpenseClaim(),getExpenseClaimHistory(),getExpenseClaimDepartment(),deleteExpenseClaim()
 * -- Date 06 -September 2019 B Rilwan Description: Added new functions - getExpenseDropDownData()
 * -- Date 09 September  2019 By Rilwan Description: Modified Destroy functions with detail table removals
 */

namespace App\Http\Controllers\API;

use App\helper\email;
use App\helper\Helper;
use App\Http\Requests\API\CreateExpenseClaimAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\DepartmentMaster;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\DocumentManagement;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\ExpenseClaim;
use App\Models\ExpenseClaimCategories;
use App\Models\ExpenseClaimDetails;
use App\Models\ExpenseClaimType;
use App\Models\LeaveDocumentApproved;
use App\Models\QryExpenseClaimDepViewClaim2;
use App\Models\QryExpenseClaimUserViewHsitory;
use App\Models\QryExpenseClaimUserViewNewClaim;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ExpenseClaimRepository;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Validator;

use Response;

/**
 * Class ExpenseClaimController
 * @package App\Http\Controllers\API
 */
class ExpenseClaimAPIController extends AppBaseController
{
    /** @var  ExpenseClaimRepository */
    private $expenseClaimRepository;

    public function __construct(ExpenseClaimRepository $expenseClaimRepo)
    {
        $this->expenseClaimRepository = $expenseClaimRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @SWG\Get(
     *      path="/expenseClaims",
     *      summary="Get a listing of the ExpenseClaims.",
     *      tags={"ExpenseClaim"},
     *      description="Get all ExpenseClaims",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaim")
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
        $this->expenseClaimRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaims = $this->expenseClaimRepository->all();

        return $this->sendResponse($expenseClaims->toArray(), trans('custom.expense_claims_retrieved_successfully'));
    }

    /**
     * @param CreateExpenseClaimAPIRequest $request
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @SWG\Post(
     *      path="/expenseClaims",
     *      summary="Store a newly created ExpenseClaim in storage",
     *      tags={"ExpenseClaim"},
     *      description="Store ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaim that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaim")
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
     *                  ref="#/definitions/ExpenseClaim"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimAPIRequest $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            //'companyID' => 'required',
            //'departmentID' => 'required',
            'pettyCashYN' => 'required',
            'comments' => 'required',
            'expenseClaimDate' => 'required'
        ]);
        if ($validator->fails()) {
            //return $result = $validator->messages();
            return $this->sendError(trans('custom.some_required_fields_are_missing'), 200);
        }

        $employeeInfo = Helper::getEmployeeInfo();

        if (isset($input['expenseClaimMasterAutoID']) && $input['expenseClaimMasterAutoID'] > 0) {
            /** update */
            $document = ExpenseClaim::find($input['expenseClaimMasterAutoID']);
            if(empty($document)){
               return $this->sendError("Master Details Not Found",200);
            }

            /**/
            if(isset($input['confirmedYN']) && $input['confirmedYN']==1){
                // check document approved table data
                $is_approved_data = LeaveDocumentApproved::where('documentSystemCode',$document->expenseClaimMasterAutoID)
                    ->where('companySystemID',$document->companySystemID)
                    ->where('documentSystemID',6)
                    ->first();

                if(empty($is_approved_data)){

                    $employee = Helper::getEmployeeInfo();

                    $insert_array = [
                        'companySystemID' =>$document->companySystemID,
                        'companyID' => $document->companyID,
                        'departmentID' => $document->departmentID,
                        'serviceLineCode'=>'x',
                        'documentSystemID'=>$document->documentSystemID,
                        'documentID'=>$document->documentID,
                        'documentSystemCode'=>$document->expenseClaimMasterAutoID,
                        'documentCode'=>$document->expenseClaimCode,
                        'rollLevelOrder'=>1,
                        'Approver'=>$employee->empManagerAttached,
                        'docConfirmedDate'=>date('Y-m-d'),
                        'docConfirmedByEmpID'=>$employee->empID,
                        'requesterID'=>$employee->empID,
                        'approvedYN'=>0,
                        'rejectedYN'=>0
                    ];
                    LeaveDocumentApproved::create($insert_array);
                }

            }

        } else {
            /** insert */
            $document = new ExpenseClaim();
            $SL = ExpenseClaim::select('serialNo')->orderBy('expenseClaimMasterAutoID', 'DESC')->first();

            $SL_b = DocumentManagement::select('bigginingSerialNumber as serialNo', 'numberOfSerialNoDigits')->where('documentID', 'EX')
                ->where('companyID', $employeeInfo->empCompanyID)
                ->first();

            if (!empty($SL)) {
                $serialNo = $SL->serialNo;
            } else {
                $serialNo = $SL_b->serialNo;
            }
            $serialNo = ($serialNo + 1);
            $document->serialNo = $serialNo;
            $tmpCode = str_pad($serialNo, $SL_b->numberOfSerialNoDigits, '0', STR_PAD_LEFT);
            $emp = Employee::where('empID', Helper::getEmployeeID())->first();
            $Code = 'EX' . $tmpCode;
            $document->expenseClaimCode = $Code;
            $document->createdUserID = $emp->empID;
            $document->createdUserSystemID = $emp->employeeSystemID;
            $document->createdPcID = strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $document->documentID = 'EX';
            $documentMaster = DocumentMaster::where('documentID', $document->documentID)->first();
            $document->documentSystemID = $documentMaster->documentSystemID;
            $document->departmentSystemID = $employeeInfo->details->departmentMaster->departmentSystemID;
            $document->departmentID = $employeeInfo->details->departmentMaster->DepartmentID;
            $document->clamiedByName = $emp->empName;
            $document->clamiedByNameSystemID = $emp->employeeSystemID;
            $document->seniorManager = isset($employeeInfo->manager->empManagerAttached) ? $employeeInfo->manager->empManagerAttached : null;
            $document->companySystemID = $employeeInfo->empCompanySystemID;
            $document->companyID = $employeeInfo->empCompanyID;

        }

        $document->expenseClaimDate = Carbon::parse($input['expenseClaimDate'] ." ". date('H:i:s'))->format('Y-m-d H:i:s');
        $document->comments = $input['comments'];
        $document->pettyCashYN = $input['pettyCashYN'];

        if(isset($input['confirmedYN']) && $input['confirmedYN']==1){
            $document->confirmedYN = $input['confirmedYN'];
            $document->confirmedByEmpSystemID = $employeeInfo->employeeSystemID;
            $document->confirmedByEmpID = $employeeInfo->empID;
            $document->confirmedByName = $employeeInfo->empFullName;
            $document->confirmedDate = Carbon::now()->toDateTimeString();
        }

        $document->save();
        return $this->sendResponse($document, trans('custom.expense_claim_header_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaims/{id}",
     *      summary="Display the specified ExpenseClaim",
     *      tags={"ExpenseClaim"},
     *      description="Get ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaim",
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
     *                  ref="#/definitions/ExpenseClaim"
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
        /** @var ExpenseClaim $expenseClaim */
        $expenseClaim = $this->expenseClaimRepository->with(['confirmed_by'])->findWithoutFail($id);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }

        return $this->sendResponse($expenseClaim->toArray(), trans('custom.expense_claim_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimAPIRequest $request
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @SWG\Put(
     *      path="/expenseClaims/{id}",
     *      summary="Update the specified ExpenseClaim in storage",
     *      tags={"ExpenseClaim"},
     *      description="Update ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaim",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaim that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaim")
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
     *                  ref="#/definitions/ExpenseClaim"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaim $expenseClaim */
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }

        $expenseClaim = $this->expenseClaimRepository->update($input, $id);

        return $this->sendResponse($expenseClaim->toArray(), trans('custom.expenseclaim_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaims/{id}",
     *      summary="Remove the specified ExpenseClaim from storage",
     *      tags={"ExpenseClaim"},
     *      description="Delete ExpenseClaim",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaim",
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
        /** @var ExpenseClaim $expenseClaim */
        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($id);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }
        if (!empty($expenseClaim->details())) {
            $expenseClaim->details()->delete();
        }
        $expenseClaim->delete();

        return $this->sendResponse($id, trans('custom.expense_claim_deleted_successfully'));
    }

    public function getExpenseClaimByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'glCodeAssignedYN', 'approved', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $expenseClaims = $this->expenseClaimRepository->expenseClaimListQuery($request, $input, $search);

        return \DataTables::of($expenseClaims)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('expenseClaimMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getExpenseClaimFormData(Request $request)
    {
        $companyId = $request['companyId'];
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $types = ExpenseClaimType::all();

        $segments = SegmentMaster::where("companySystemID", $companyId)
            ->where('isActive', 1)->get();

        $categories = ExpenseClaimCategories::all();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'types' => $types,
            'segments' => $segments,
            'categories' => $categories
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    /**
     * Display the specified Expense Claim Audit.
     * GET|HEAD /getExpenseClaimAudit
     *
     * @param int $id
     *
     * @return Response
     */
    public function getExpenseClaimAudit(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }

        $expenseClaim->docRefNo = \Helper::getCompanyDocRefNo($expenseClaim->companySystemID, $expenseClaim->documentSystemID);

        return $this->sendResponse($expenseClaim->toArray(), trans('custom.expense_claim_retrieved_successfully'));
    }

    public function printExpenseClaim(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }

        $expenseClaim->docRefNo = \Helper::getCompanyDocRefNo($expenseClaim->companySystemID, $expenseClaim->documentSystemID);
        $expenseClaim->localDecimal = 3;
        $expenseClaim->localDecimal = 'OMR';
        $expenseClaim->total = 0;


        $grandTotal = collect($expenseClaim->details)->pluck('localAmount')->toArray();
        $expenseClaim->total = array_sum($grandTotal);

        foreach ($expenseClaim->details as $item) {
            $item->currencyDecimal = 2;
            $item->localDecimal = 3;

            if ($item->currency) {
                $item->currencyDecimal = $item->currency->DecimalPlaces;
            }
            if ($item->local_currency) {
                $item->localDecimal = $item->local_currency->DecimalPlaces;
            }
        }

        if ($expenseClaim->company) {
            if ($expenseClaim->company->localcurrency) {
                $expenseClaim->localDecimal = $expenseClaim->company->localcurrency->DecimalPlaces;
                $expenseClaim->localCurrencyCode = $expenseClaim->company->localcurrency->CurrencyCode;
            }
        }

        $array = array('entity' => $expenseClaim);
        $time = strtotime("now");
        $fileName = 'expense_claim' . $id . '_' . $time . '.pdf';
        $html = view('print.expense_claim', $array);
        $htmlFooter = view('print.expense_claim_footer', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function getPaymentStatusHistory(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }

        $detail = \DB::select('SELECT
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.BPVcode,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.companyID,
                                erp_paysupplierinvoicemaster.BPVdate,
                                erp_paysupplierinvoicemaster.BPVNarration,
                                erp_paysupplierinvoicemaster.createdUserID,
                                employees.empName,
                                erp_directpaymentdetails.expenseClaimMasterAutoID 
                            FROM
                                ( erp_directpaymentdetails INNER JOIN erp_paysupplierinvoicemaster ON erp_directpaymentdetails.directPaymentAutoID = erp_paysupplierinvoicemaster.PayMasterAutoId )
                                LEFT JOIN employees ON erp_paysupplierinvoicemaster.createdUserID = employees.empID 
                            WHERE
                                erp_directpaymentdetails.expenseClaimMasterAutoID = ' . $id . '
                            GROUP BY
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.companySystemID,
                                erp_directpaymentdetails.expenseClaimMasterAutoID 
                            HAVING
                                ( ( ( erp_directpaymentdetails.expenseClaimMasterAutoID ) != 0 ) ) UNION ALL
                            SELECT
                                hrms_monthlyadditionsmaster.monthlyAdditionsMasterID,
                                hrms_monthlyadditionsmaster.monthlyAdditionsCode,
                                hrms_monthlyadditionsmaster.documentID,
                                hrms_monthlyadditionsmaster.CompanyID,
                                hrms_monthlyadditionsmaster.dateMA,
                                hrms_monthlyadditionsmaster.description,
                                hrms_monthlyadditionsmaster.modifieduser,
                                employees.empName,
                                hrms_monthlyadditiondetail.expenseClaimMasterAutoID 
                            FROM
                                ( hrms_monthlyadditionsmaster INNER JOIN hrms_monthlyadditiondetail ON hrms_monthlyadditionsmaster.monthlyAdditionsMasterID = hrms_monthlyadditiondetail.monthlyAdditionsMasterID )
                                LEFT JOIN employees ON hrms_monthlyadditionsmaster.modifieduser = employees.empID 
                            WHERE
                                hrms_monthlyadditiondetail.expenseClaimMasterAutoID = ' . $id . '
                            GROUP BY
                                hrms_monthlyadditionsmaster.monthlyAdditionsMasterID,
                                hrms_monthlyadditionsmaster.monthlyAdditionsCode,
                                hrms_monthlyadditionsmaster.documentSystemID,
                                hrms_monthlyadditionsmaster.companySystemID,
                                hrms_monthlyadditiondetail.expenseClaimMasterAutoID 
                            HAVING
                                ( ( ( hrms_monthlyadditiondetail.expenseClaimMasterAutoID ) <> 0 ) );
                            ;');


        return $this->sendResponse($detail, trans('custom.payment_status_retrieved_successfully'));
    }

    public function amendExpenseClaimReview(Request $request)
    {

        $input = $request->all();

        $id = $input['expenseClaimMasterAutoID'];
        $employee = Helper::getEmployeeInfo();
        $emails = array();
        $masterData = ExpenseClaim::find($id);
        $documentName = trans('email.expense_claim');

        if (empty($masterData)) {
            return $this->sendError($documentName . ' not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this') . $documentName . ', it is not confirmed');
        }

        $emailBody = '<p>' . $masterData->expenseClaimCode . ' ' . trans('email.has_been_returned_back_to_amend_by', ['empName' => $employee->empName]) . ' ' . trans('email.due_to_below_reason') . '.</p><p>' . trans('email.comment') . ' : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->expenseClaimCode . ' ' . trans('email.has_been_returned_back_to_amend');

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $masterData->expenseClaimMasterAutoID
                );
            }

            $documentApproval = LeaveDocumentApproved::where('documentSystemCode', $id)
                                                    ->where('companySystemID', $masterData->companySystemID)
                                                    ->where('documentSystemID', $masterData->documentSystemID)
                                                    ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->empSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $masterData->expenseClaimMasterAutoID);
                }
            }

            $sendEmail = email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            $deleteApproval = LeaveDocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;

            $masterData->approved = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedDate = null;

            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), $documentName . ' amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getExpenseClaim()
    {

        $expenseClaim = $this->expenseClaimRepository->getClaimFullHistory();
        $paginate = $expenseClaim;
        if($expenseClaim->count()){
            $data = [];
            $paginate = array_only($expenseClaim->toArray(),['current_page','first_page_url','from','last_page','last_page_url','next_page_url','path','per_page','prev_page_url','to','total']);
            foreach ($expenseClaim as $key=> $claim){
                $currency = null;
                if(isset($claim->details[0]->currencyID)) {
                    $currency = CurrencyMaster::select('currencyID','CurrencyName','CurrencyCode','DecimalPlaces')->find($claim->details[0]->currencyID);
                }
                $data[$key] = $claim;
                $data[$key]['total_amount']=$claim->details->sum('amount');
                $data[$key]['currency']=$currency;
                $data[$key] = array_except($data[$key] ,['details']);
            }
            $paginate['data'] = $data;
        }

        return $this->sendResponse($paginate, trans('custom.expense_claim_details_retrieved_successfully'));
    }

    public function getExpenseClaimHistory()
    {
        $emp_id = Helper::getEmployeeID();
        $expenseClaim = QryExpenseClaimUserViewHsitory::select('CompanyName', 'expenseClaimDate', 'expenseClaimCode', 'comments',
            'expenseClaimTypeDescription', 'paymentProcessingInProgress', 'paymentConfirmed', 'paymentApproved', 'expenseClaimMasterAutoID')
            ->where('createdUserID', $emp_id)
            ->get();
        return $this->sendResponse($expenseClaim->toArray(), trans('custom.expense_claim_history_retrieved_successfully'));
    }

    public function getExpenseClaimDepartment()
    {
        $emp_id = Helper::getEmployeeID();

        $expenseClaim = QryExpenseClaimDepViewClaim2::select('CompanyName', 'expenseClaimDate', 'expenseClaimCode', 'erp_qry_expenseclaimdepview_claim2.comments',
            'expenseClaimTypeDescription', 'clamiedByName', 'confirmedYN', 'approved', 'addedForPayment', 'erp_qry_expenseclaimdepview_claim2.expenseClaimMasterAutoID')
            ->selectRaw('null as myConfirmed,null as paymentConfirmed,null as paymentApproved, sum(amount) as total_amount,currencyID')// required by dilan
            ->leftJoin('erp_expenseclaimdetails','erp_qry_expenseclaimdepview_claim2.expenseClaimMasterAutoID','=','erp_expenseclaimdetails.expenseClaimMasterAutoID')
            ->where('managerID', $emp_id)
            ->orWhere('seniormanagerID', $emp_id)
            ->groupBy('erp_qry_expenseclaimdepview_claim2.expenseClaimMasterAutoID')
            ->orderBy('erp_qry_expenseclaimdepview_claim2.expenseClaimMasterAutoID','DESC')
            ->paginate(50);
        $paginate = $expenseClaim;
        if($expenseClaim->count()){
            $paginate = array_only($expenseClaim->toArray(),['current_page','first_page_url','from','last_page','last_page_url','next_page_url','path','per_page','prev_page_url','to','total']);
            $data = [];
            foreach ($expenseClaim as $key => $claim){
                $currency = null;
                if($claim->currencyID) {
                    $currency = CurrencyMaster::select('currencyID','CurrencyName','CurrencyCode','DecimalPlaces')->find($claim->currencyID);
                }
                $data[$key] = $claim;
                $data[$key]['currency'] = $currency;

            }
            $paginate['data'] = $data;
        }

        return $this->sendResponse($paginate, trans('custom.expense_claim_department_details_retrieved_success'));
    }

    public function getExpenseDropDownData(Request $request)
    {
        $input = $request->all();
        $employee = Helper::getEmployeeInfo();
        $company_id = $employee->empCompanyID;

        $output['currency'] = CurrencyMaster::select('currencyID', 'CurrencyName', 'CurrencyCode', 'DecimalPlaces')->get();

        $output['claim_category'] = ExpenseClaimCategories::select('expenseClaimCategoriesAutoID', 'claimCategoriesDescription')
            ->orderBy('claimCategoriesDescription')
            ->get();

        $output['expense_claim_type'] = ExpenseClaimType::all();

        $output['department'] = [];
        if ($company_id) {
            $output['department'] = SegmentMaster::select('serviceLineSystemID', 'ServiceLineCode', 'serviceLineMasterCode', 'ServiceLineDes')
                                                ->where('companyID', $company_id)
                                                ->where('isActive', 1)
                                                ->get();
        }

        return $this->sendResponse($output, trans('custom.expense_claim_department_details_retrieved_success'));
    }

    public function getExpenseClaimDetails(Request $request)
    {

        $input = $request->all();

        if (!isset($input['expenseClaimMasterAutoID']) || $input['expenseClaimMasterAutoID'] == 0) {
            $this->sendError(trans('custom.master_id_not_found'), 422);
        }

        /*set Claim Array*/
        $expenseClaim = ExpenseClaim::find($input['expenseClaimMasterAutoID']);
        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_details_not_found'), 200);
        }
        $claimType = [];
        if(!empty($expenseClaim->expense_claim_type)){
            $claimType = $expenseClaim->expense_claim_type;
        }
        $output['claim'] = array_only($expenseClaim->toArray(), ['expenseClaimMasterAutoID', 'expenseClaimCode', 'expenseClaimDate', 'pettyCashYN', 'comments','confirmedYN','addedForPayment','approved']);
        $output['claim']['CompanyName'] = isset($expenseClaim->company->CompanyName) ? $expenseClaim->company->CompanyName : '';
        $output['claim']['claim_type'] = $claimType;
        $output['claim']['currency'] = isset($expenseClaim->details[0]->currency) ? array_only($expenseClaim->details[0]->currency->toArray(),['currencyID','CurrencyName','CurrencyCode','DecimalPlaces']) : NULL;


        /*set Detail Array*/
        $expenseClaimDetails = ExpenseClaimDetails::
        with(
            array(
                'currency'=>function($query){
                    $query->select('currencyID','CurrencyName','CurrencyCode','DecimalPlaces');
                },
                'segment'=>function($query){
                    $query->select('serviceLineSystemID','ServiceLineCode','serviceLineMasterCode','ServiceLineDes');
                },
                'category'=>function($query){
                    $query->select('expenseClaimCategoriesAutoID','claimcategoriesDescription');
                },))
            ->select('expenseClaimDetailsID', 'expenseClaimMasterAutoID', 'serviceLineCode', 'serviceLineSystemID', 'expenseClaimCategoriesAutoID', 'description', 'docRef', 'currencyID', 'amount')
            ->where('expenseClaimMasterAutoID', $input['expenseClaimMasterAutoID'])
            ->get();
        $output['claim']['totalAmount'] = (double)$expenseClaimDetails->sum('amount');
        $output['details'] = $expenseClaimDetails->toArray();

        /*set attachemnt Array*/
        $output['attachements'] = DocumentAttachments::where('companyID', $expenseClaim->companyID)
            ->where('documentID', $expenseClaim->documentID)
            ->where('documentSystemCode', $expenseClaim->expenseClaimMasterAutoID)
            ->get();

        return $this->sendResponse($output, trans('custom.expense_claim_details_retrieved_successfully'));
    }
}

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
 */
namespace App\Http\Controllers\API;

use App\helper\email;
use App\helper\Helper;
use App\Http\Requests\API\CreateExpenseClaimAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimAPIRequest;
use App\Models\DocumentApproved;
use App\Models\DocumentAttachments;
use App\Models\ExpenseClaim;
use App\Models\ExpenseClaimCategories;
use App\Models\ExpenseClaimDetails;
use App\Models\ExpenseClaimType;
use App\Models\QryExpenseClaimDepViewClaim2;
use App\Models\QryExpenseClaimUserViewHsitory;
use App\Models\QryExpenseClaimUserViewNewClaim;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ExpenseClaimRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
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

        return $this->sendResponse($expenseClaims->toArray(), 'Expense Claims retrieved successfully');
    }

    /**
     * @param CreateExpenseClaimAPIRequest $request
     * @return Response
     *
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

        $expenseClaims = $this->expenseClaimRepository->create($input);

        return $this->sendResponse($expenseClaims->toArray(), 'Expense Claim saved successfully');
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
            return $this->sendError('Expense Claim not found');
        }

        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimAPIRequest $request
     * @return Response
     *
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
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim = $this->expenseClaimRepository->update($input, $id);

        return $this->sendResponse($expenseClaim->toArray(), 'ExpenseClaim updated successfully');
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
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim->delete();

        return $this->sendResponse($id, 'Expense Claim deleted successfully');
    }

    public function getExpenseClaimByCompany(Request $request){

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'glCodeAssignedYN', 'approved', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $expenseClaims = ExpenseClaim::whereIn('companySystemID', $subCompanies)
            ->with('created_by')
            ->where('documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $expenseClaims = $expenseClaims->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $expenseClaims = $expenseClaims->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('glCodeAssignedYN', $input)) {
            if (($input['glCodeAssignedYN'] == 0 || $input['glCodeAssignedYN'] == -1) && !is_null($input['glCodeAssignedYN'])) {
                $expenseClaims = $expenseClaims->where('glCodeAssignedYN', '=', $input['glCodeAssignedYN']);
            }
        }
        
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $expenseClaims = $expenseClaims->where(function ($query) use ($search) {
                $query->where('expenseClaimCode', 'LIKE', "%{$search}%");
            });
        }

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

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    /**
     * Display the specified Expense Claim Audit.
     * GET|HEAD /getExpenseClaimAudit
     *
     * @param  int $id
     *
     * @return Response
     */
    public function getExpenseClaimAudit(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim->docRefNo = \Helper::getCompanyDocRefNo($expenseClaim->companySystemID, $expenseClaim->documentSystemID);

        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim retrieved successfully');
    }

    public function printExpenseClaim(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim->docRefNo = \Helper::getCompanyDocRefNo($expenseClaim->companySystemID, $expenseClaim->documentSystemID);
        $expenseClaim->localDecimal = 3;
        $expenseClaim->localDecimal = 'OMR';
        $expenseClaim->total = 0;


        $grandTotal = collect($expenseClaim->details)->pluck('localAmount')->toArray();
        $expenseClaim->total = array_sum($grandTotal);

        foreach ($expenseClaim->details as $item){
            $item->currencyDecimal = 2;
            $item->localDecimal = 3;

            if($item->currency){
                $item->currencyDecimal = $item->currency->DecimalPlaces;
            }
            if($item->local_currency){
                $item->localDecimal = $item->local_currency->DecimalPlaces;
            }
        }

        if($expenseClaim->company){
            if($expenseClaim->company->localcurrency){
                $expenseClaim->localDecimal = $expenseClaim->company->localcurrency->DecimalPlaces;
                $expenseClaim->localCurrencyCode = $expenseClaim->company->localcurrency->CurrencyCode;
            }
        }

        $array = array('entity' => $expenseClaim);
        $time = strtotime("now");
        $fileName = 'expense_claim' . $id . '_' . $time . '.pdf';
        $html = view('print.expense_claim', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);
    }

    public function getPaymentStatusHistory(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
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
                                erp_directpaymentdetails.expenseClaimMasterAutoID = '.$id.'
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
                                hrms_monthlyadditiondetail.expenseClaimMasterAutoID = '.$id.'
                            GROUP BY
                                hrms_monthlyadditionsmaster.monthlyAdditionsMasterID,
                                hrms_monthlyadditionsmaster.monthlyAdditionsCode,
                                hrms_monthlyadditionsmaster.documentSystemID,
                                hrms_monthlyadditionsmaster.companySystemID,
                                hrms_monthlyadditiondetail.expenseClaimMasterAutoID 
                            HAVING
                                ( ( ( hrms_monthlyadditiondetail.expenseClaimMasterAutoID ) <> 0 ) );
                            ;');


        return $this->sendResponse($detail, 'payment status retrieved successfully');
    }

    public function amendExpenseClaimReview(Request $request){

        $input = $request->all();

        $id = $input['expenseClaimMasterAutoID'];
        $employee = Helper::getEmployeeInfo();
        $emails = array();
        $masterData = ExpenseClaim::find($id);
        $documentName = "Expense Claim";

        if (empty($masterData)) {
            return $this->sendError($documentName.' not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this '.$documentName.', it is not confirmed');
        }

        $emailBody = '<p>' . $masterData->expenseClaimCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->expenseClaimCode . ' has been return back to amend';

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

            $documentApproval =  DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
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

            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            // updating fields
            $masterData->confirmedYN = 0 ;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;

            $masterData->approved = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedDate = null;

            $masterData->save();

            DB::commit();
            return $this->sendResponse($masterData->toArray(), $documentName.' amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getExpenseClaim()
    {
        $emp_id = Helper::getEmployeeID();
        $expenseClaim = QryExpenseClaimUserViewNewClaim::select('CompanyName','expenseClaimDate','expenseClaimTypeDescription',
            'comments','confirmedYN','approved','addedForPayment','expenseClaimMasterAutoID','myConfirmed','expenseClaimCode')
            ->where('createdUserID',$emp_id)
            ->get();
        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim Details retrieved successfully');
    }

    public function getExpenseClaimHistory()
    {
        $emp_id = Helper::getEmployeeID();
        $expenseClaim = QryExpenseClaimUserViewHsitory::select('CompanyName','expenseClaimDate','expenseClaimCode','comments',
            'expenseClaimTypeDescription','paymentProcessingInProgress','paymentConfirmed','paymentApproved','expenseClaimMasterAutoID')
            ->where('createdUserID',$emp_id)
            ->get();
        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim history retrieved successfully');
    }

    public function getExpenseClaimDepartment()
    {
        $emp_id = Helper::getEmployeeID();
        $expenseClaim = QryExpenseClaimDepViewClaim2::select('CompanyName','expenseClaimDate','expenseClaimCode','comments',
            'expenseClaimTypeDescription','clamiedByName','confirmedYN','approved','addedForPayment','expenseClaimMasterAutoID')
            ->where('managerID',$emp_id)
            ->orWhere('seniormanagerID',$emp_id)
            ->groupBy('expenseClaimMasterAutoID')
            ->get();
        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim Department details retrieved successfully');
    }
}

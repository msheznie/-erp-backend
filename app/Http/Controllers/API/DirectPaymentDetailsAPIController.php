<?php
/**
 * =============================================
 * -- File Name : DirectPaymentDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Payment Voucher
 * -- Author : Mohamed Mubashir
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Direct payment detail
 * -- REVISION HISTORY
 * -- Date: 18 September 2018 By: Mubashir Description: Added new function updateDirectPaymentAccount(),deleteAllDirectPayment(),getDirectPaymentDetails()
 * -- Date: 15 November 2018 By: Fayas Description: Added new function addDetailsFromExpenseClaim()
 */

namespace App\Http\Controllers\API;

use App\helper\PaySupplier;
use App\helper\SupplierInvoice;
use App\helper\TaxService;
use App\Http\Requests\API\CreateDirectPaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateDirectPaymentDetailsAPIRequest;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\ChartOfAccount;
use App\Models\CompanyFinanceYear;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyConversion;
use App\Models\DirectPaymentDetails;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Employee;
use App\Models\ExpenseClaimDetails;
use App\Models\ExpenseClaimDetailsMaster;
use App\Models\ExpenseClaimMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SegmentMaster;
use App\Models\Taxdetail;
use App\Repositories\DirectPaymentDetailsRepository;
use App\Repositories\ExpenseAssetAllocationRepository;
use App\Repositories\ExpenseClaimRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Services\PaymentVoucherServices;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseAssetAllocation;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\ServiceLine;
use App\Models\SrpEmployeeDetails;
use App\Models\SMECompany;

/**
 * Class DirectPaymentDetailsController
 * @package App\Http\Controllers\API
 */
class DirectPaymentDetailsAPIController extends AppBaseController
{
    /** @var  DirectPaymentDetailsRepository */
    private $directPaymentDetailsRepository;
    private $expenseClaimRepository;
    private $paySupplierInvoiceMasterRepository;
    private $expenseAssetAllocationRepository;

    public function __construct(
        DirectPaymentDetailsRepository $directPaymentDetailsRepo,
        ExpenseClaimRepository $expenseClaimRepo,
        PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo,
        ExpenseAssetAllocationRepository $expenseAssetAllocationRepo
    )
    {
        $this->directPaymentDetailsRepository = $directPaymentDetailsRepo;
        $this->expenseClaimRepository = $expenseClaimRepo;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
        $this->expenseAssetAllocationRepository = $expenseAssetAllocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentDetails",
     *      summary="Get a listing of the DirectPaymentDetails.",
     *      tags={"DirectPaymentDetails"},
     *      description="Get all DirectPaymentDetails",
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
     *                  @SWG\Items(ref="#/definitions/DirectPaymentDetails")
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
        $this->directPaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->directPaymentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directPaymentDetails = $this->directPaymentDetailsRepository->all();

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details retrieved successfully');
    }

    /**
     * @param CreateDirectPaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directPaymentDetails",
     *      summary="Store a newly created DirectPaymentDetails in storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Store DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentDetails")
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
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectPaymentDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $createDocument = PaymentVoucherServices::storeDirectPaymentDetails($input);

        if ($createDocument['status']) {
            return $this->sendResponse($createDocument['data'], $createDocument['message']);
        }
        else {
            return $this->sendError(
                $createDocument['message'],
                isset($createDocument['code']) && ($createDocument['code'] != 404) ? $createDocument['code'] : 404
            );
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentDetails/{id}",
     *      summary="Display the specified DirectPaymentDetails",
     *      tags={"DirectPaymentDetails"},
     *      description="Get DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
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
     *                  ref="#/definitions/DirectPaymentDetails"
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
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectPaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directPaymentDetails/{id}",
     *      summary="Update the specified DirectPaymentDetails in storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Update DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentDetails")
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
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectPaymentDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'chartofaccount','to_bank']);
        $input = $this->convertArrayToValue($input);

        $updateDocument = PaymentVoucherServices::updateDirectPaymentDetails($id, $input);

        if ($updateDocument['status']) {
            return $this->sendResponse($updateDocument['data'], $updateDocument['message']);
        }
        else {
            return $this->sendError(
                $updateDocument['message'],
                isset($updateDocument['code']) && ($updateDocument['code'] != 404) ? $updateDocument['code'] : 404,
                $updateDocument['type'] ?? array('type' => '')
            );
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directPaymentDetails/{id}",
     *      summary="Remove the specified DirectPaymentDetails from storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Delete DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
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
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        if($directPaymentDetails->master && $directPaymentDetails->master->confirmedYN){
            return $this->sendError('You cannot delete Direct Payment Detail, this document already confirmed',500);
        }

        $this->expenseAssetAllocationRepository->deleteExpenseAssetAllocation(
            $directPaymentDetails->master->PayMasterAutoId,
            $directPaymentDetails->master->documentSystemID,
            $id
        );

        $directPaymentDetails->delete();

        // update master table  
        PaySupplier::updateMaster($directPaymentDetails->directPaymentAutoID);

        return $this->sendResponse($id, 'Direct Payment Details deleted successfully');
    }


    public function getDirectPaymentDetails(Request $request)
    {
        $id = $request->PayMasterAutoId;

        $directPaymentDetails = $this->directPaymentDetailsRepository->with(['segment', 'chartofaccount','to_bank'=>function($query){
            $query->with(['currency' => function($query){
                $query->select('currencyID','CurrencyCode');
            }]);

        }])->findWhere(['directPaymentAutoID' => $id]);

        return $this->sendResponse($directPaymentDetails, 'Details retrieved successfully');
    }

    public function deleteAllDirectPayment(Request $request)
    {

        $id = $request->directPaymentAutoID;

        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        if($paySupplierInvoiceMaster->confirmedYN){
            return $this->sendError('You cannot delete Direct Payment Detail, this document already confirmed',500);
        }

        $expenseClaimDetails = DirectPaymentDetails::where('directPaymentAutoID', $id)->get();

        foreach ($expenseClaimDetails as $detail){
            if($detail['expenseClaimMasterAutoID']){
                $expenseClaim = ExpenseClaimMaster::find($detail['expenseClaimMasterAutoID']);
                if (!empty($expenseClaim)) {
                    ExpenseClaimMaster::find($detail['expenseClaimMasterAutoID'])->update(['addedForPayment' => 0, 'addedToSalary' => 0]);
                }
            }
        }

        $this->expenseAssetAllocationRepository->deleteExpenseAssetAllocation($id, $paySupplierInvoiceMaster->documentSystemID);

        $directPaymentDetails = DirectPaymentDetails::where('directPaymentAutoID', $id)->delete();

        PaySupplier::updateMaster($id);
        return $this->sendResponse($directPaymentDetails, 'Successfully delete');
    }

    public function updateDirectPaymentAccount(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $messages = [
            'toBankID.not_in' => 'To Bank field is required.',
            'toBankAccountID.not_in' => 'To Bank account field is required.',
            'toBankCurrencyID.not_in' => 'To Bank currency field is required.',
            'toBankID.required' => 'To Bank field is required.',
            'toBankAmount.required' => 'To Bank amount field is required.',
            'toBankAccountID.required' => 'To Bank account field is required.',
            'toBankCurrencyID.required' => 'To Bank currency field is required.',
        ];

        $validator = \Validator::make($input, [
            'toBankID' => 'required|not_in:0',
            'toBankAccountID' => 'required|not_in:0',
            'toBankCurrencyID' => 'required|not_in:0',
            'companySystemID' => 'required|not_in:0',
            'toBankAmount' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($input['directPaymentDetailsID']);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['toBankCurrencyID'], $input['toBankCurrencyID'], $input['toBankAmount']);

        $company = Company::find($input['companySystemID']);
        if(empty($company)){
            return $this->sendError('Company not found');
        }
        $bankAccount = BankAccount::find($input['toBankAccountID']);

        if(empty($bankAccount)){
            return $this->sendError('Bank not found');
        }

        $chartofaccount = ChartOfAccount::find($bankAccount->chartOfAccountSystemID);

        if(empty($chartofaccount)){
            return $this->sendError('Bank account GL code not found');
        }

        $input['toCompanyLocalCurrencyID'] = $company->localCurrencyID;
        $input['toCompanyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        $input['toCompanyLocalCurrencyAmount'] = \Helper::roundValue($companyCurrencyConversion['localAmount']);
        $input['toCompanyRptCurrencyID'] = $company->reportingCurrency;
        $input['toCompanyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        $input['toCompanyRptCurrencyAmount'] = \Helper::roundValue($companyCurrencyConversion['reportingAmount']);
        $input['toBankGlCodeSystemID'] = $bankAccount->chartOfAccountSystemID;
        $input['toBankGlCode'] = $chartofaccount->AccountCode;
        $input['toBankGLDescription'] = $chartofaccount->AccountDescription;
        $input['toBankAmount'] = \Helper::roundValue($input['toBankAmount']);
        unset($input['companySystemID']);

        $directPaymentDetails = $this->directPaymentDetailsRepository->update($input, $input['directPaymentDetailsID']);

        return $this->sendResponse($directPaymentDetails->toArray(), 'DirectPaymentDetails updated successfully');

    }

    public function getDPExchangeRateAmount(Request $request)
    {
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($request->directPaymentDetailsID);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        if ($request->toBankCurrencyID) {

            $conversion = CurrencyConversion::where('masterCurrencyID', $directPaymentDetails->supplierTransCurrencyID)
                ->where('subCurrencyID', $request->toBankCurrencyID)
                ->first();
            $conversion = $conversion->conversion;

            $bankAmount = 0;
            if ($request->toBankCurrencyID == $directPaymentDetails->localCurrency) {
                $bankAmount = $directPaymentDetails->localAmount;
            } else if($request->toBankCurrencyID == $directPaymentDetails->comRptCurrency){
                $bankAmount = $directPaymentDetails->comRptAmount;
            } else {
                if ($conversion > $directPaymentDetails->DPAmountCurrencyER) {
                    if ($conversion > 1) {
                        $bankAmount = $directPaymentDetails->DPAmount / $conversion;
                    } else {
                        $bankAmount = $directPaymentDetails->DPAmount * $conversion;
                    }
                } else {
                    If ($conversion > 1) {
                        $bankAmount = $directPaymentDetails->DPAmount * $conversion;
                    } else {
                        $bankAmount = $directPaymentDetails->DPAmount / $conversion;
                    }
                }
            }

            $output = ['toBankCurrencyER' => $conversion, 'toBankAmount' => \Helper::roundValue($bankAmount)];
            return $this->sendResponse($output, 'Successfully data retrieved');
        } else {
            $output = ['toBankCurrencyER' => 0, 'toBankAmount' => 0];
            return $this->sendResponse($output, 'Successfully data retrieved');
        }
    }


    public function addDetailsFromExpenseClaim(Request $request)
    {
        $input = $request->all();
        $id = $input['expenseClaimId'];
        $payMasterAutoId = $input['PayMasterAutoId'];

        $expenseClaim = ExpenseClaimMaster::find($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }



        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($payMasterAutoId);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice not found');
        }
        // check policy 16 is on for ec
//        if($expenseClaim->pettyCashYN == 1){
//            $UPECSLPolicy = CompanyPolicyMaster::where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
//                ->where('companyPolicyCategoryID', 16)
//                ->where('isYesNO', 1)
//                ->first();
//            if (isset($UPECSLPolicy->isYesNO) && $UPECSLPolicy->isYesNO==1 ) {
//                return $this->sendError('You can not add detail. UPECS policy is on for the company');
//            }
//        }

        if ($paySupplierInvoiceMaster->BPVdate) {
            $finYearExp = explode('-', $paySupplierInvoiceMaster->BPVdate);
            $budgetYear = $finYearExp[0];
        } else {
            $budgetYear = date("Y");
        }

        $expenseClaimDetails = ExpenseClaimDetailsMaster::where('companyID', $expenseClaim->companyID)
            ->where('expenseClaimMasterAutoID', $id)
            ->with(['currency','segment','category','local_currency'])
            ->get();

        $CompanyCurrency = SMECompany::where('company_id', $expenseClaim->companyID)->select('company_default_decimal')->first();

        foreach ($expenseClaimDetails as $detail) {

            $emp = Employee::with(['details'])->find($expenseClaim->clamiedByNameSystemID);

            $empID = '';
            $empDepartment = 0;

            if(!empty($emp)){
                $empID = $emp->empID;
                if($emp->details){
                    $empDepartment = $emp->details->departmentID;
                }
            }


            $currencyConvert = \Helper::currencyConversion($paySupplierInvoiceMaster->companySystemID,
                $detail->transactionCurrencyID, $detail->companyLocalCurrencyID, $detail->companyLocalAmount,
                $paySupplierInvoiceMaster->BPVAccount);

            $currencyConversion = \Helper::currencyConversion($paySupplierInvoiceMaster->companySystemID, $detail->transactionCurrencyID, $paySupplierInvoiceMaster->supplierTransCurrencyID, $detail->transactionAmount);
            $expenceClaimAmount = round($currencyConversion['documentAmount'],$detail->transactionCurrencyDecimalPlaces);

            $temData = array(
                'directPaymentAutoID' => $paySupplierInvoiceMaster->PayMasterAutoId,
                'companySystemID' => $detail['companyID'],
                'companyID' => $detail['companyCode'],
                'serviceLineSystemID' => ($detail->segment) ? $detail->segment->serviceLineSystemID : null,
                'serviceLineCode'=> ($detail->segment) ? $detail->segment->ServiceLineCode : null,
                'comments'=> $detail['description'],
                'expenseClaimMasterAutoID' => $expenseClaim->expenseClaimMasterAutoID,
                'pettyCashYN' => $expenseClaim->pettyCashYN,
                'chartOfAccountSystemID'=> $detail->category->glAutoID,
                'glCode' => $detail->category->glCode,
                'glCodeDes' => $detail->category->glCodeDescription,
                'glCodeIsBank' => 0,
                'budgetYear' => $budgetYear,
                'supplierTransCurrencyID' => $detail->transactionCurrencyID,
                'supplierTransER' => 1,
                'DPAmountCurrency' => $detail->companyLocalCurrencyID,
                'DPAmountCurrencyER' => 1,
                'DPAmount' => $expenceClaimAmount,
                'netAmount' => $expenceClaimAmount,
                'bankAmount' => \Helper::roundValue($currencyConvert['bankAmount']),
                'bankCurrencyID' => $paySupplierInvoiceMaster->supplierTransCurrencyID,
                'bankCurrencyER' =>  $currencyConvert['transToBankER'],
                'localCurrency' => $detail['companyLocalCurrencyID'],
                'localCurrencyER' => 1,
                'localAmount' => $detail->companyLocalAmount,
                'comRptCurrency' => $detail->companyReportingCurrencyID,
                'comRptCurrencyER' => $currencyConvert['trasToRptER'],
                'comRptAmount' => \Helper::roundValue($currencyConvert['reportingAmount']),
                'expense_claim_er' => $paySupplierInvoiceMaster->BPVbankCurrencyER
                );

            $this->directPaymentDetailsRepository->create($temData);
        }

        if (count($expenseClaimDetails) > 0) {
            ExpenseClaimMaster::find($id)->update(['addedForPayment' => -1, 'addedToSalary' => -1]);
        }

        return $this->sendResponse($expenseClaimDetails, 'Monthly Addition Details added successfully');
    }

    public function addPVDetailsByInterCompany(Request $request)
    {
        $input = $request->all();

        if (!isset($input['interCompanyToSystemID'])) {
            return $this->sendError("Inter company ID not found", 500);
        }

        $checkCompany = Company::find($input['interCompanyToSystemID']);

        if (!$checkCompany) {
            return $this->sendError("Inter company ID not found", 500);
        }

        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($input['PayMasterAutoId']);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice not found', 500);
        }

        if($paySupplierInvoiceMaster->confirmedYN){
            return $this->sendError('You cannot add Direct Payment Detail, this document already confirmed', 500);
        }


        $bankMaster = BankAssign::ofCompany($paySupplierInvoiceMaster->companySystemID)->isActive()->where('bankmasterAutoID', $paySupplierInvoiceMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return $this->sendError('Selected Bank is not active', 500);
        }

        $bankAccount = BankAccount::isActive()->find($paySupplierInvoiceMaster->BPVAccount);

        if (empty($bankAccount)) {
            return $this->sendError('Selected Bank Account is not active', 500);
        }


         if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {

            if(is_null($paySupplierInvoiceMaster->interCompanyToSystemID)){
                return $this->sendError('Please select a company to');
            }

            $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['PayMasterAutoId'], 'relatedPartyYN' => 1]);
            if (count($directPaymentDetails) > 0) {
                return $this->sendError('Cannot add GL code as there is a related party GL code added.', 500);
            }
        }

        $directPaymentDetails = $this->directPaymentDetailsRepository->findWhere(['directPaymentAutoID' => $input['PayMasterAutoId'], 'glCodeIsBank' => 1]);
        if (count($directPaymentDetails) > 0) {
            return $this->sendError('Cannot add GL code as there is a bank GL code added.', 500);
        }


        $items = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) use ($input){
                                                $q->where('isApproved', 1)
                                                  ->where('interCompanySystemID', $input['interCompanyToSystemID']);
                                            })
                                            ->where('isAssigned', -1)
                                            ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                                            ->where('controllAccountYN', 0)
                                            ->where('controlAccountsSystemID', '<>', 1)
                                            ->where('isActive', 1)
                                            ->get();

        $warningMessage = '';
        if (count($items) == 0) {
            $warningMessage = "Account code is not assigned for the selected inter company";
        }

        DB::beginTransaction();
        try {
            $errorMessageArray = [];
            foreach ($items as $key => $value) {
                $res = $this->directPaymentDetailsRepository->storeDirectDetail($input['PayMasterAutoId'],$value->chartOfAccountSystemID);
                if (!$res['status']) {
                    $errorMessageArray[] = $res['message'];
                }
            }


            if ((count($errorMessageArray) == count($items)) && count($items) > 0 && count($errorMessageArray) > 0) {
                DB::rollBack();
                return $this->sendError($errorMessageArray, 422);
            }


            $message = [['status' => 'success', 'message' => 'PaySupplierInvoiceMaster updated successfully'], ['status' => 'warning', 'message' => $warningMessage], ['status' => 'error', 'message' => $errorMessageArray]];
            DB::commit();
            return $this->sendResponse([], $message);
        } catch
        (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 500);
        }
    }

    function updat_monthly_deduction(UpdateDirectPaymentDetailsAPIRequest $request){
        $validator = \Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $id = $request->input('id');

        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $input['deductionType'] = $request->input('deduction_type');
        $input = $this->convertArrayToValue( $input );

        $directPaymentDetails = $this->directPaymentDetailsRepository->update($input, $id);
        return $this->sendResponse($directPaymentDetails->toArray(), 'Monthly deduction type updated successfully');

    }

    public function getBankAccountDetails(request $request)
    {     
        $input = $request->all();

        if (!isset($input['type'])) {
            return $this->sendError('Type parameter required');
        }

        if (!isset($input['invoice'])) {
            return $this->sendError('Invoice parameter required.');
        }

        
        $type = $input['type'];
        $invoice = $input['invoice'];

        $account = BankAccount::where('chartOfAccountSystemID', $input['bankGLId'])->where('companySystemID', $input['company_id'])->first();
        if (empty($account) && $type == 15 && $invoice == 3) {
            return $this->sendError('The selected GL is not linked to a bank account.');
        } else {
            return $this->sendResponse($account, 'Account details retrieved successfully.');
        }

    }
}

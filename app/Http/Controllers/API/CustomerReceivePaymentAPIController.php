<?php
/**
 * =============================================
 * -- File Name : CustomerReceivePaymentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  CustomerReceivePayment
 * -- Author : Mohamed Nazir
 * -- Create date : 29 - October 2018
 * -- Description : This file contains the all CRUD for Receipt Voucher
 * -- REVISION HISTORY
 * -- Date: 29-October 2018 By: Nazir Description: Added new function getReceiptVoucherMasterRecord(),
 * -- Date: 29-October 2018 By: Nazir Description: Added new function receiptVoucherReopen(),
 * -- Date: 08-November 2018 By: Nazir Description: Added new function printReceiptVoucher(),
 * -- Date: 19-November 2018 By: Nazir Description: Added new function getReceiptVoucherApproval(),
 * -- Date: 19-November 2018 By: Nazir Description: Added new function getApprovedRVForCurrentUser(),
 * -- Date: 21-November 2018 By: Nazir Description: Added new function amendReceiptVoucher(),
 * -- Date: 31-December 2018 By: Nazir Description: Added new function receiptVoucherCancel(),
 * -- Date: 11-January 2019 By: Mubashir Description: Added new function approvalPreCheckReceiptVoucher()
 * -- Date: 13-June 2019 By: Fayas Description: Added new function amendReceiptVoucherReview()
 */

namespace App\Http\Controllers\API;

use App\helper\CustomValidation;
use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Requests\API\CreateCustomerReceivePaymentAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentAPIRequest;
use App\Models\AccountsReceivableLedger;
use App\Models\AdvanceReceiptDetails;
use App\Models\BankLedger;
use App\Models\ChartOfAccount;
use App\Models\ChequeRegisterDetail;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\Employee;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerAssigned;
use App\Models\CurrencyMaster;
use App\Models\CustomerCurrency;
use App\Models\Company;
use App\Models\CustomerMaster;
use App\Models\BankAccount;
use App\Models\PdcLog;
use App\Models\CustomerReceivePaymentRefferedHistory;
use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Models\DirectReceiptDetailsRefferedHistory;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ExpenseClaimType;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\ErpProjectMaster;
use App\Models\SegmentMaster;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DirectReceiptDetail;
use App\Models\BankAssign;
use App\Models\CompanyFinancePeriod;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\YesNoSelectionForMinus;
use App\Models\YesNoSelection;
use App\Models\Months;
use App\Repositories\CustomerReceivePaymentRepository;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Services\API\ApiPermissionServices;
use App\Services\ChartOfAccountValidationService;
use App\Services\CustomerReceivePaymentService;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\PaymentType;
use App\Services\GeneralLedgerService;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Services\ValidateDocumentAmend;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;

/**
 * Class CustomerReceivePaymentController
 * @package App\Http\Controllers\API
 */
class CustomerReceivePaymentAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentRepository */
    private $customerReceivePaymentRepository;
    private $vatReturnFillingMasterRepo;

    public function __construct(CustomerReceivePaymentRepository $customerReceivePaymentRepo, VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePayments",
     *      summary="Get a listing of the CustomerReceivePayments.",
     *      tags={"CustomerReceivePayment"},
     *      description="Get all CustomerReceivePayments",
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
     *                  @SWG\Items(ref="#/definitions/CustomerReceivePayment")
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
        $this->customerReceivePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->customerReceivePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerReceivePayments = $this->customerReceivePaymentRepository->all();

        return $this->sendResponse($customerReceivePayments->toArray(), 'Customer Receive Payments retrieved successfully');
    }

    /**
     * @param CreateCustomerReceivePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerReceivePayments",
     *      summary="Store a newly created CustomerReceivePayment in storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Store CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePayment")
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
     *                  ref="#/definitions/CustomerReceivePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerReceivePaymentAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'documentType', 'companyFinanceYearID', 'custTransactionCurrencyID', 'customerID', 'employeeID'));

            if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['custTransactionCurrencyID'])) {
                return $this->sendError(
                    'Currency exchange rate to local and reporting currency must be greater than zero.',
                    500
                );
            }

            $resultData = CustomerReceivePaymentService::createCustomerReceivePayment($input);
            if($resultData['status']){
                DB::commit();
                return $this->sendResponse($resultData['data'], $resultData['message']);
            }
            else{
                DB::rollBack();
                return $this->sendError($resultData['message'], 500);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePayments/{id}",
     *      summary="Display the specified CustomerReceivePayment",
     *      tags={"CustomerReceivePayment"},
     *      description="Get CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
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
     *                  ref="#/definitions/CustomerReceivePayment"
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
        /** @var CustomerReceivePayment $customerReceivePayment */
        //  $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        $customerReceivePayment = $this->customerReceivePaymentRepository->with(['currency', 'localCurrency', 'rptCurrency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        },'customer', 'employee', 'bank' =>function ($q) {
            $q->with(['currency']);
        },'bank_info'])->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        return $this->sendResponse($customerReceivePayment->toArray(), 'Customer Receive Payment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerReceivePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerReceivePayments/{id}",
     *      summary="Update the specified CustomerReceivePayment in storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Update CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePayment")
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
     *                  ref="#/definitions/CustomerReceivePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerReceivePaymentAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companyFinanceYearID', 'customerID', 'employeeID','companyFinancePeriodID', 'custTransactionCurrencyID', 'bankID', 'bankAccount', 'bankCurrency', 'confirmedYN', 'expenseClaimOrPettyCash', 'projectID'));

        $input = array_except($input, ['currency', 'finance_year_by', 'finance_period_by', 'localCurrency', 'rptCurrency','customer','bank', 'employee','bank_info']);

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['custTransactionCurrencyID'])) {
            return $this->sendError(
                'Currency exchange rate to local and reporting currency must be greater than zero.',
                500
            );
        }

        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);


        if (empty($customerReceivePayment)) {
            return $this->sendError('Receipt Voucher not found');
        }

        if(empty($input['projectID'])){
            $input['projectID'] = null;
        }

        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($customerReceivePayment->custTransactionCurrencyID);

        $input['payment_type_id'] = isset($input['paymentType'][0]) ?  $input['paymentType'][0]: $input['paymentType'];

        $input['custPaymentReceiveDate'] = ($input['custPaymentReceiveDate'] != '' ? Carbon::parse($input['custPaymentReceiveDate'])->format('Y-m-d') . ' 00:00:00' : NULL);

        $input['custChequeDate'] = ($input['custChequeDate'] != '' ? Carbon::parse($input['custChequeDate'])->format('Y-m-d') . ' 00:00:00' : NULL);

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
            $input['custChequeNo'] = null;            
        }

        $customValidation = CustomValidation::validation($customerReceivePayment->documentSystemID, $customerReceivePayment, 2, $input);
        if (!$customValidation["success"]) {
            return $this->sendError($customValidation["message"], 500, array('type' => 'already_confirmed'));
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 4;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }

        $documentDate = $input['custPaymentReceiveDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the financial period!', 500);
        }


        if(empty($input['custTransactionCurrencyID'])){
            $message = 'Currency field is required.';
            return $this->sendError($message, 500);
        }

        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'custPaymentReceiveDate' => 'required',
            'custTransactionCurrencyID' => 'required|numeric|min:1',
            'narration' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }



        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $input['custTransactionCurrencyID'], 0);
        if ($company) {
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyRptCurrencyID'] = $company->reportingCurrency;
            $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyPolicyCategoryID', 67)
                ->where('isYesNO', 1)
                ->first();
            $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

            if($policy == false || $input['documentType'] != 14) {
                $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            }
        }


        if($input['documentType'] == 14){
            if(isset($input['payeeTypeID'])){
                if($input['payeeTypeID'] == 1){
                    if(!$input['customerID'] > 0){
                        return $this->sendError('Customer field is required', 500);
                    }
                }
                if($input['payeeTypeID'] == 2){
                    if(!$input['employeeID'] > 0){
                        return $this->sendError('Employee field is required', 500);
                    }
                }
                if($input['payeeTypeID'] == 3){
                    if($input['PayeeName'] == null){
                        return $this->sendError('Other field is required', 500);
                    }
                }

            }
        }

        if ($input['documentType'] == 13 || $input['documentType'] == 15) {
            /*customer reciept*/
            $detail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)->get();

            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            if(!empty($customer)) {
                $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
                $input['customerGLCode'] = $customer->custGLaccount;
                $input['custAdvanceAccountSystemID'] = $customer->custAdvanceAccountSystemID;
                $input['custAdvanceAccount'] = $customer->custAdvanceAccount;
            }
            if ($input['customerID'] != $customerReceivePayment->customerID) {

                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the customer.', 500);
                }

                /*if customer change*/
                $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
                if (empty($customer)) {
                    return $this->sendError('Customer not found.', 500);
                }
                $input['customerGLCode'] = $customer->custGLaccount;
                $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
                $currency = CustomerCurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
                if ($currency) {
                    $input['custTransactionCurrencyID'] = $currency->currencyID;
                    $myCurr = $currency->currencyID;

                    $companyCurrency = \Helper::companyCurrency($customerReceivePayment->companySystemID);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrencyER'] = 0;
                    $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $customerReceivePayment->companySystemID)
                        ->where('isDefault', -1)
                        ->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                        $bankAccount = BankAccount::where('companySystemID', $customerReceivePayment->companySystemID)
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->where('isAccountActive', 1)
                            ->where('approvedYN', 1)
                            ->where('accountCurrencyID', $myCurr)
                            ->first();
                        if ($bankAccount) {
                            $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                            $input['bankCurrency'] = $myCurr;
                            $input['bankCurrencyER'] = 1;
                        }
                    }
                }
            }


            if ($input['bankAccount'] != $customerReceivePayment->bankAccount) {

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMaster) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                    }
                }
            }

            if ($input['custTransactionCurrencyID'] != $customerReceivePayment->custTransactionCurrencyID) {
                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the currency.', 500);
                } else {
                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($customerReceivePayment->companySystemID);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrency'] = null;
                    $input['bankCurrencyER'] = 0;


                    $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $customerReceivePayment->companySystemID)
                        ->where('isDefault', -1)
                        ->first();

                    if ($bank) {
                        $bankAccount = BankAccount::where('companySystemID', $customerReceivePayment->companySystemID)
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->where('accountCurrencyID', $myCurr)
                            ->first();

                        $input['bankID'] = $bank->bankmasterAutoID;

                        if ($bankAccount) {
                            $input['bankAccount'] = $bankAccount->bankAccountAutoID;

                            $input['bankCurrency'] = $myCurr;
                            $input['bankCurrencyER'] = 1;
                        }
                    }
                }
            }

            if ($input['bankID'] != $customerReceivePayment->bankID) {
                $bankAccount = BankAccount::where('companySystemID', $customerReceivePayment->companySystemID)
                    ->where('bankmasterAutoID', $input['bankID'])
                    ->where('isDefault', 1)
                    ->where('isAccountActive', 1)
                    ->where('approvedYN', 1)
                    ->where('accountCurrencyID', $input['custTransactionCurrencyID'])
                    ->first();

                $input['bankAccount'] = null;
                $input['bankCurrencyER'] = 0;
                $input['bankCurrency'] = null;
                if ($bankAccount) {
                    $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                    $input['bankCurrencyER'] = 1;
                    $input['bankCurrency'] = $input['custTransactionCurrencyID'];
                }
            }


        }

        if ($input['documentType'] == 14 || $input['documentType'] == 15) {
            /*direct receipt*/
            $detail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

            if ($input['bankID'] != $customerReceivePayment->bankID) {
                $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)
                    ->where('bankmasterAutoID', $input['bankID'])
                    ->where('isAccountActive', 1)
                    ->where('approvedYN', 1)
                    ->where('isDefault', 1)
                    ->first();

                if ($bankAccount) {
                    $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMasterBank = \Helper::currencyConversion($customerReceivePayment->companySystemID, $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMasterBank) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMasterBank['transToDocER'];
                    }                
                }
            }

            if ($input['bankAccount'] != $customerReceivePayment->bankAccount) {

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMasterBank = \Helper::currencyConversion($customerReceivePayment->companySystemID, $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMasterBank) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMasterBank['transToDocER'];
                    }
                }
            }
        }

        // calculating header total
        $checkPreDirectSumTrans = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('DRAmount');

        $checkPreDirectSumLocal = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('localAmount');

        $checkPreDirectSumReport = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('comRptAmount');

        $masterHeaderSumTrans = 0;
        $masterHeaderSumLocal = 0;
        $masterHeaderSumReport = 0;
        if ($input['documentType'] == 13) {

            $customerReceiveAmountTrans = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                ->sum('receiveAmountTrans');

            $customerReceiveAmountLocal = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                ->sum('receiveAmountLocal');

            $customerReceiveAmountReport = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                ->sum('receiveAmountRpt');

            $masterHeaderSumTrans = $checkPreDirectSumTrans + $customerReceiveAmountTrans;
            $masterHeaderSumLocal = $checkPreDirectSumLocal + $customerReceiveAmountLocal;
            $masterHeaderSumReport = $checkPreDirectSumReport + $customerReceiveAmountReport;

            $masterHeaderSumTrans = abs($masterHeaderSumTrans);
            $masterHeaderSumLocal = abs($masterHeaderSumLocal);
            $masterHeaderSumReport = abs($masterHeaderSumReport);

            $input['receivedAmount'] = (\Helper::roundValue($masterHeaderSumTrans) * -1);
            $input['localAmount'] = (\Helper::roundValue($masterHeaderSumLocal) * -1);
            $input['companyRptAmount'] = (\Helper::roundValue($masterHeaderSumReport) * -1);

        }
        else if ($input['documentType'] == 14 || $input['documentType'] == 15) {

            $masterHeaderSumTrans = $checkPreDirectSumTrans;
            $masterHeaderSumLocal = $checkPreDirectSumLocal;
            $masterHeaderSumReport = $checkPreDirectSumReport;

            if($input['documentType'] == 15){

                $detailsTotal = AdvanceReceiptDetails::select(
                    DB::raw("IFNULL(SUM(paymentAmount),0) as netAmount"),
                    DB::raw("IFNULL(SUM(localAmount),0) as netAmountLocal"),
                    DB::raw("IFNULL(SUM(comRptAmount),0) as netAmountRpt"))
                    ->where('custReceivePaymentAutoID', $id)
                    ->first();

                if(!empty($detailsTotal)){
                    $masterHeaderSumTrans  = $masterHeaderSumTrans + $detailsTotal->netAmount;
                    $masterHeaderSumLocal  = $masterHeaderSumLocal + $detailsTotal->netAmountLocal;
                    $masterHeaderSumReport = $masterHeaderSumReport + $detailsTotal->netAmountRpt;
                }

            }

            $masterHeaderSumTrans = abs($masterHeaderSumTrans);
            $masterHeaderSumLocal = abs($masterHeaderSumLocal);
            $masterHeaderSumReport = abs($masterHeaderSumReport);

            $input['receivedAmount'] = (\Helper::roundValue($masterHeaderSumTrans) * -1);
            $input['localAmount'] = (\Helper::roundValue($masterHeaderSumLocal) * -1);
            $input['companyRptAmount'] = (\Helper::roundValue($masterHeaderSumReport) * -1);
        }

        // calculating bank amount

        if ($input['bankCurrency'] == $input['localCurrencyID']) {
            $input['bankAmount'] = $input['localAmount'];
            $input['bankCurrencyER'] = $input['localCurrencyER'];
        } else if ($input['bankCurrency'] == $input['companyRptCurrencyID']) {
            $input['bankAmount'] = $input['companyRptAmount'];
            $input['bankCurrencyER'] = $input['companyRptCurrencyER'];
        } else {
            $bankCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $input['bankCurrency'], $masterHeaderSumTrans);

            if ($bankCurrencyConversion) {
                $input['bankAmount'] = (\Helper::roundValue($bankCurrencyConversion['documentAmount']) * -1);
                $input['bankCurrencyER'] = $bankCurrencyConversion['transToDocER'];
            }
        }

        if ($input['documentType'] == 13) {
            if($masterHeaderSumTrans == abs($customerReceivePayment->receivedAmount)) {
                $input['bankCurrencyER'] = $customerReceivePayment->bankCurrencyER;
                $input['localCurrencyER'] = $customerReceivePayment->localCurrencyER;
                $input['companyRptCurrencyER'] = $customerReceivePayment->companyRptCurrencyER;
            }
        }

        $itemExistArray = array();
        $error_count = 0;

        if ($customerReceivePayment->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'custPaymentReceiveDate' => 'required',
                'bankID' => 'required|numeric|min:1',
                'bankCurrency' => 'required|numeric|min:1',
                'bankAccount' => 'required|numeric|min:1',
                'custTransactionCurrencyID' => 'required|numeric|min:1',
                'narration' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if ($input['documentType'] == 13) {

                $customerReceivePaymentDetailCount = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->count();
                if ($customerReceivePaymentDetailCount == 0) {
                    return $this->sendError('Every receipt voucher should have at least one item', 500);
                }
            }
            else if ($input['documentType'] == 14 || $input['documentType'] == 15) {
                $checkDirectItemsCount = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->count();

                if($input['documentType'] == 14 && $checkDirectItemsCount == 0){
                    return $this->sendError('Every receipt voucher should have at least one item', 500);
                }

                $checkAdvReceiptDetails = AdvanceReceiptDetails::where('custReceivePaymentAutoID',$id)->count();

                if ($checkAdvReceiptDetails == 0 && $checkDirectItemsCount == 0) {
                    return $this->sendError('Every receipt voucher should have at least one item', 500);
                }
            }
            // checking minus value
            if ($input['documentType'] == 13) {

                $checkDirectMinusTotal = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->sum('DRAmount');

                $checkReciveDetailMinusTotal = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->sum('receiveAmountTrans');

                $netMinustot = $checkReciveDetailMinusTotal + $checkDirectMinusTotal;

                if ($netMinustot < 0) {
                    return $this->sendError('Net amount cannot be minus total', 500);
                }
            }



            if ($input['documentType'] == 13) {

                $detailAllRecords = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->get();

                if ($detailAllRecords) {
                    foreach ($detailAllRecords as $row) {
                        if ($row['addedDocumentSystemID'] == 20) {
                            $checkAmount = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                                ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                                ->where('receiveAmountTrans', '<=', 0)
                                ->where(function ($q) {
                                    $q->where('receiveAmountTrans', '<=', 0)
                                        ->orWhereNull('receiveAmountLocal', '<=', 0)
                                        ->orWhereNull('receiveAmountRpt', '<=', 0)
                                        ->orWhereNull('receiveAmountTrans')
                                        ->orWhereNull('receiveAmountLocal')
                                        ->orWhereNull('receiveAmountRpt');
                                })
                                ->count();

                            if ($checkAmount > 0) {
                                return $this->sendError('Amount should be greater than 0 for every items', 500);
                            }
                        } elseif ($row['addedDocumentSystemID'] == 19) {
                            $checkAmount = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                                ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                                ->where(function ($q) {
                                    $q->where('receiveAmountTrans', '=', 0)
                                        ->orWhereNull('receiveAmountLocal', '=', 0)
                                        ->orWhereNull('receiveAmountRpt', '=', 0)
                                        ->orWhereNull('receiveAmountTrans')
                                        ->orWhereNull('receiveAmountLocal')
                                        ->orWhereNull('receiveAmountRpt');
                                })
                                ->count();

                            if ($checkAmount > 0) {
                                return $this->sendError('Amount should be greater than 0 for every items', 500);
                            }
                        }
                    }
                }

                $checkQuantity = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->where(function ($q) {
                        $q->where('DRAmount', '=', 0)
                            ->orWhereNull('localAmount', '=', 0)
                            ->orWhereNull('comRptAmount', '=', 0)
                            ->orWhereNull('DRAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();
                if ($checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            if ($input['documentType'] == 14 ||  $input['documentType'] == 15) {
                $checkQuantity = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->where(function ($q) {
                        $q->where('DRAmount', '<=', 0)
                            ->orWhereNull('localAmount', '<=', 0)
                            ->orWhereNull('comRptAmount', '<=', 0)
                            ->orWhereNull('DRAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();
                if ($input['documentType'] == 14 && $checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }

                $checkAdvReceiptDetailsAmount = AdvanceReceiptDetails::where('custReceivePaymentAutoID',$id)
                                                                        ->where(function ($q) {
                                                                            $q->where('paymentAmount', '<=', 0)
                                                                                ->orWhereNull('localAmount', '<=', 0)
                                                                                ->orWhereNull('comRptAmount', '<=', 0)
                                                                                ->orWhereNull('paymentAmount')
                                                                                ->orWhereNull('localAmount')
                                                                                ->orWhereNull('comRptAmount');
                                                                        })
                                                                        ->count();

                if ($checkAdvReceiptDetailsAmount > 0 || $checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if ($input['documentType'] == 14 || $input['documentType'] == 15) {
                $directReceiptDetail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

                $finalError = array('amount_zero' => array(),
                    'amount_neg' => array(),
                    'required_serviceLine' => array(),
                    'active_serviceLine' => array(),
                    'contract_check' => array()
                );
                foreach ($directReceiptDetail as $item) {

                    $updateItem = DirectReceiptDetail::find($item['directReceiptDetailsID']);

                    if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glCode);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['required_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }

                    $updateItem->save();

                    if ($updateItem->DRAmount == 0 || $updateItem->localAmount == 0 || $updateItem->comRptAmount == 0) {
                        array_push($finalError['amount_zero'], $updateItem->itemPrimaryCode);
                        $error_count++;
                    }
                }

                if ($policyConfirmedUserToApprove->isYesNO == 0) {

                    foreach ($directReceiptDetail as $item) {

                        $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')
                                                                 ->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)
                                                                 ->first();

                        if (isset($chartOfAccount) && $chartOfAccount->controlAccountsSystemID == 1) {
                            if ($item['contractUID'] == '' || $item['contractUID'] == 0) {
                                array_push($finalError['contract_check'], $item->glCode);
                                $error_count++;
                            }
                        }
                    }
                }

                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                }
            }

            if ($input['documentType'] == 13) {
                $directReceiptDetail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

                $finalError = array('amount_zero' => array(),
                    'amount_neg' => array(),
                    'required_serviceLine' => array(),
                    'active_serviceLine' => array()
                );

                foreach ($directReceiptDetail as $item) {

                    $updateItem = DirectReceiptDetail::find($item['directReceiptDetailsID']);

                    if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glCode);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['required_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }

                    $updateItem->save();

                }

                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                }
            }
            // updating accounts receivable ledger table
            if ($input['documentType'] == 13) {

                $customerReceivePaymentDetailRec = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->where('addedDocumentSystemID', 20)
                    ->get();

                foreach ($customerReceivePaymentDetailRec as $item) {

                    $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $item['arAutoID'])
                        ->sum('receiveAmountTrans');

                    $customerInvoiceMaster = CustomerInvoiceDirect::find($item['bookingInvCodeSystem']);
                    if($customerInvoiceMaster['isPerforma'] == 2) {
                        $_documentTransAmount = 0; 
                        $_documentLocalAmount = 0;
                        $_documentRptAmount = 0;

                        $_customerInvoiceDirectDetails = CustomerInvoiceDirectDetail::with(['chart_Of_account'])->where('custInvoiceDirectID', $customerInvoiceMaster["custInvoiceDirectAutoID"])->get();
                        foreach ($_customerInvoiceDirectDetails as $item) {
                            if($item->chart_Of_account->controlAccountsSystemID == 2 || $item->chart_Of_account->controlAccountsSystemID == 5 || $item->chart_Of_account->controlAccountsSystemID == 3) {
                                $_documentTransAmount -= ($item->invoiceAmount + $item->VATAmountTotal);
                                $_documentLocalAmount -= ($item->localAmount + $item->VATAmountLocalTotal);
                                $_documentRptAmount -= ($item->comRptAmount + $item->VATAmountRptTotal);
                                
                            }else if($item->chart_Of_account->controlAccountsSystemID == 4) {
                                $_documentTransAmount += $item->invoiceAmount + $item->VATAmountTotal;
                                $_documentLocalAmount += $item->localAmount + $item->VATAmountLocalTotal;
                                $_documentRptAmount += $item->comRptAmount + $item->VATAmountRptTotal;  
                            }else{
                                $_documentTransAmount += $item->invoiceAmount + $item->VATAmountTotal;
                                $_documentLocalAmount += $item->localAmount + $item->VATAmountLocalTotal;
                                $_documentRptAmount += $item->comRptAmount + $item->VATAmountRptTotal;  
                            }
                        }


                        if (round($totalReceiveAmountTrans, $documentCurrencyDecimalPlace) > round(($customerInvoiceMaster['bookingAmountTrans'] + $customerInvoiceMaster['VATAmount'] + $_documentTransAmount), $documentCurrencyDecimalPlace)) {

                            $itemDrt = "Selected invoice " . $item['bookingInvCode'] . " booked more than the invoice amount.";
                            $itemExistArray[] = [$itemDrt];
    
                        }
                    }else {
                        if (round($totalReceiveAmountTrans, $documentCurrencyDecimalPlace) > round(($customerInvoiceMaster['bookingAmountTrans'] + $customerInvoiceMaster['VATAmount']), $documentCurrencyDecimalPlace)) {

                            $itemDrt = "Selected invoice " . $item['bookingInvCode'] . " booked more than the invoice amount.";
                            $itemExistArray[] = [$itemDrt];
    
                        }
                    }

                }
            }


            if (!empty($itemExistArray)) {
                return $this->sendError($itemExistArray, 422);
            }

            // updating accounts receivable ledger table
            if ($input['documentType'] == 13) {

                $customerReceivePaymentDetailRec = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->where('addedDocumentSystemID', '<>', '')
                    ->get();

                foreach ($customerReceivePaymentDetailRec as $row) {

                    $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $row['arAutoID'])
                        ->sum('receiveAmountTrans');

                    $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
                        ->where('companySystemID', $row["companySystemID"])
                        ->where('PayMasterAutoId', $row["bookingInvCodeSystem"])
                        ->where('documentSystemID', $row["addedDocumentSystemID"])
                        ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')->first();

                    if (!$matchedAmount) {
                        $matchedAmount['SumOfmatchedAmount'] = 0;
                    }

                    $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];

                    $arLedgerUpdate = AccountsReceivableLedger::find($row['arAutoID']);

                    if ($row['addedDocumentSystemID'] == 20) {
                        if ($totReceiveAmount == 0) {
                            $arLedgerUpdate->fullyInvoiced = 0;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        } else if ($row->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount > $row->bookingAmountTrans) {
                            $arLedgerUpdate->fullyInvoiced = 2;
                            $arLedgerUpdate->selectedToPaymentInv = -1;
                        } else if (($row->bookingAmountTrans > $totReceiveAmount) && ($totReceiveAmount > 0)) {
                            $arLedgerUpdate->fullyInvoiced = 1;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        }
                    } else if ($row['addedDocumentSystemID'] == 19) {
                        if ($totReceiveAmount == 0) {
                            $arLedgerUpdate->fullyInvoiced = 0;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        } else if ($row->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount < $row->bookingAmountTrans) {
                            $arLedgerUpdate->fullyInvoiced = 2;
                            $arLedgerUpdate->selectedToPaymentInv = -1;
                        } else if (($row->bookingAmountTrans < $totReceiveAmount) && ($totReceiveAmount < 0)) {
                            $arLedgerUpdate->fullyInvoiced = 1;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        }
                    }
                    $arLedgerUpdate->save();
                }

            }

            if($input['documentType'] == 14 || $input['documentType'] == 15){
                $details = DirectReceiptDetail::select(DB::raw("IFNULL(SUM(DRAmount),0) as receivedAmount"),
                    DB::raw("IFNULL(SUM(localAmount),0) as localAmount"),
                    DB::raw("IFNULL(SUM(DRAmount),0) as bankAmount"),
                    DB::raw("IFNULL(SUM(comRptAmount),0) as companyRptAmount"),
                    DB::raw("IFNULL(SUM(VATAmount),0) as VATAmount"),
                    DB::raw("IFNULL(SUM(VATAmountLocal),0) as VATAmountLocal"),
                    DB::raw("IFNULL(SUM(VATAmountRpt),0) as VATAmountRpt"),
                    DB::raw("IFNULL(SUM(netAmount),0) as netAmount"),
                    DB::raw("IFNULL(SUM(netAmountLocal),0) as netAmountLocal"),
                    DB::raw("IFNULL(SUM(netAmountRpt),0) as netAmountRpt"))
                    ->where('directReceiptAutoID', $id)
                    ->first();

                if(!empty($details)) {
                    $input['VATAmount'] = $details->VATAmount;
                    $input['VATAmountLocal'] = $details->VATAmountLocal;
                    $input['VATAmountRpt'] = $details->VATAmountRpt;
                    $input['netAmount'] = $details->netAmount;
                    $input['netAmountLocal'] = $details->netAmountLocal;
                    $input['netAmountRpt'] = $details->netAmountRpt;
                }

                if($input['documentType'] == 15){
                    $details = AdvanceReceiptDetails::select(DB::raw("IFNULL(SUM(VATAmount),0) as VATAmount"),
                        DB::raw("IFNULL(SUM(VATAmountLocal),0) as VATAmountLocal"),
                        DB::raw("IFNULL(SUM(VATAmountRpt),0) as VATAmountRpt"),
                        DB::raw("IFNULL(SUM(paymentAmount),0) as netAmount"),
                        DB::raw("IFNULL(SUM(localAmount),0) as netAmountLocal"),
                        DB::raw("IFNULL(SUM(comRptAmount),0) as netAmountRpt"))
                        ->where('custReceivePaymentAutoID', $id)
                        ->first();

                    if(!empty($details) && $details->VATAmount != 0) {
                        $input['VATAmount'] = $details->VATAmount;
                        $input['VATAmountLocal'] = $details->VATAmountLocal;
                        $input['VATAmountRpt'] = $details->VATAmountRpt;
                        $input['netAmount'] = $details->netAmount;
                        $input['netAmountLocal'] = $details->netAmountLocal;
                        $input['netAmountRpt'] = $details->netAmountRpt;
                    }
                }
            }

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemID"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && isset($input['VATAmount']) && $input['VATAmount'] > 0){

                if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))) {
                    return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
                }

                if($input['documentType'] == 15 && empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                    return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemID'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $customerReceivePayment->custPaymentReceiveCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];


                if($input['documentType'] == 15){
                    $taxDetail['payeeSystemCode'] = $input['customerID'];
                    $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();

                    if(!empty($customer)) {
                        $taxDetail['payeeCode'] = $customer->CutomerCode;
                        $taxDetail['payeeName'] = $customer->CustomerName;
                    }else{
                        return $this->sendError('Customer not found', 500);
                    }
                }else {
                    $taxDetail['payeeSystemCode'] = 0;
                    $taxDetail['payeeCode'] = '';
                    $taxDetail['payeeName'] = '';
                }



                $taxDetail['amount'] = $input['VATAmount'];
                $taxDetail['localCurrencyER']  = $input['localCurrencyER'];
                $taxDetail['rptCurrencyER'] = $input['companyRptCurrencyER'];
                $taxDetail['localAmount'] = $input['VATAmountLocal'];
                $taxDetail['rptAmount'] = $input['VATAmountRpt'];
                $taxDetail['currency'] =  $input['custTransactionCurrencyID'];
                $taxDetail['currencyER'] =  1;

                $taxDetail['localCurrencyID'] =  $customerReceivePayment->localCurrencyID;
                $taxDetail['rptCurrencyID'] =  $customerReceivePayment->companyRptCurrencyID;
                $taxDetail['payeeDefaultCurrencyID'] =  $input['custTransactionCurrencyID'];
                $taxDetail['payeeDefaultCurrencyER'] =  1;
                $taxDetail['payeeDefaultAmount'] =  $input['VATAmount'];

                Taxdetail::create($taxDetail);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);


            if ($input['pdcChequeYN']) {
                $pdcLogValidation = PdcLog::where('documentSystemID', $input['documentSystemID'])
                                      ->where('documentmasterAutoID', $id)
                                      ->whereNull('chequeDate')
                                      ->first();

                if ($pdcLogValidation) {
                    return $this->sendError('PDC Cheque date cannot be empty', 500); 
                }

                $pdcLogValidationChequeNo = PdcLog::where('documentSystemID', $input['documentSystemID'])
                                      ->where('documentmasterAutoID', $id)
                                      ->whereNull('chequeNo')
                                      ->first();

                if ($pdcLogValidationChequeNo) {
                    return $this->sendError('PDC Cheque no cannot be empty', 500); 
                }


                $totalAmountForPDC = 0;
                if ($input['documentType'] == 13) {
                    $detailAllRecordsSum = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                                                                    ->sum('receiveAmountTrans');
                    $bankTotal = DirectReceiptDetail::where('directReceiptAutoID', $id)->sum('netAmount');

                    $totalAmountForPDC = $detailAllRecordsSum + $bankTotal;
                } else if ($input['documentType'] == 14) {
                    $totalAmountForPDC = DirectReceiptDetail::where('directReceiptAutoID', $id)->sum('netAmount');
                } else if ($input['documentType'] == 15) {
                    $totalAmountForPDC = DirectReceiptDetail::where('directReceiptAutoID', $id)->sum('netAmount');
                }

                $pdcLog = PdcLog::where('documentSystemID', $customerReceivePayment->documentSystemID)
                                      ->where('documentmasterAutoID', $id)
                                      ->get();

                if (count($pdcLog) == 0) {
                    return $this->sendError('PDC Cheques not created, Please create atleast one cheque', 500);
                } 

                $pdcLogAmount = PdcLog::where('documentSystemID', $customerReceivePayment->documentSystemID)
                                      ->where('documentmasterAutoID', $id)
                                      ->sum('amount');

                $checkingAmount = round($totalAmountForPDC, 3) - round($pdcLogAmount, 3);

                if ($checkingAmount > 0.001 || $checkingAmount < 0) {
                    return $this->sendError('PDC Cheque amount should equal to PV total amount', 500); 
                }

                $checkPlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "pdc-receivable-account");

                if (is_null($checkPlAccount)) {
                    return $this->sendError('Please configure PDC Payable account for payment voucher', 500);
                } 
            }

            if ($input['documentType'] == 14) {
                $object = new ChartOfAccountValidationService();
                $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);


                if (isset($result) && !empty($result["accountCodes"])) {
                    return $this->sendError($result["errorMsg"]);
                }
            }

            $params = array('autoID' => $id,
                'company' => $customerReceivePayment->companySystemID,
                'document' => $customerReceivePayment->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => $input['receivedAmount']
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
            $input['custChequeDate'] = null;
            $input['custChequeNo'] = null;
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

       

        if(isset($input['bankAccount']))
        {
            if(!empty($input['bankAccount']) )
            {
                $bank_currency = $input['bankCurrency'];
                $document_currency = $input['custTransactionCurrencyID'];

                $cur_det['companySystemID'] = $input['companySystemID'];
                $cur_det['bankmasterAutoID'] = $input['bankID'];
                $cur_det['bankAccountAutoID'] = $input['bankAccount'];
                $cur_det_info =  (object)$cur_det;

                $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);

                $amount = $bankBalance['netBankBalance'];
                $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();

                $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');

            
                $input['bankAccountBalance'] = $rounded_amount;

            }
        }

        $input['PayeeEmpID'] = $input['employeeID'];


        if ($input['payeeTypeID'] == 1 && $input['documentType'] == 14) {
            $input['PayeeName'] = null;
            $input['PayeeEmpID'] = null;
        } else if ($input['payeeTypeID'] == 2 && $input['documentType'] == 14) {
            $input['PayeeName'] = null;
            $input['customerID'] = null;

        } else if ($input['payeeTypeID'] == 3 && $input['documentType'] == 14) {
            $input['PayeeEmpID'] = null;
            $input['customerID'] = null;
        }

        $customerReceivePayment = $this->customerReceivePaymentRepository->update($input, $id);

        return $this->sendReponseWithDetails($input, 'Receipt Voucher updated successfully',1,$confirm['data'] ?? null);
    }

    public function updateCurrency($id, UpdateCustomerReceivePaymentAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companyFinanceYearID', 'customerID', 'companyFinancePeriodID', 'custTransactionCurrencyID', 'bankID', 'bankAccount', 'bankCurrency', 'confirmedYN', 'expenseClaimOrPettyCash', 'projectID'));

        $input = array_except($input, ['currency', 'finance_year_by', 'finance_period_by', 'localCurrency', 'rptCurrency','customer','bank','bank_info']);

        if (!\Helper::validateCurrencyRate($input['companySystemID'], $input['custTransactionCurrencyID'])) {
            return $this->sendError(
                'Currency exchange rate to local and reporting currency must be greater than zero.',
                500
            );
        }

        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);


        if (empty($customerReceivePayment)) {
            return $this->sendError('Receipt Voucher not found');
        }

        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($customerReceivePayment->custTransactionCurrencyID);

        $input['payment_type_id'] = isset($input['paymentType'][0]) ?  $input['paymentType'][0]: $input['paymentType'];

        $input['custPaymentReceiveDate'] = ($input['custPaymentReceiveDate'] != '' ? Carbon::parse($input['custPaymentReceiveDate'])->format('Y-m-d') . ' 00:00:00' : NULL);

        $input['custChequeDate'] = ($input['custChequeDate'] != '' ? Carbon::parse($input['custChequeDate'])->format('Y-m-d') . ' 00:00:00' : NULL);

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
            $input['custChequeNo'] = null;
        }

        $customValidation = CustomValidation::validation($customerReceivePayment->documentSystemID, $customerReceivePayment, 2, $input);
        if (!$customValidation["success"]) {
            return $this->sendError($customValidation["message"], 500, array('type' => 'already_confirmed'));
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystemID"] = 4;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else {
            $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
        }

        $documentDate = $input['custPaymentReceiveDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the financial period!', 500);
        }

        $validator = \Validator::make($input, [
            'companyFinancePeriodID' => 'required|numeric|min:1',
            'companyFinanceYearID' => 'required|numeric|min:1',
            'custPaymentReceiveDate' => 'required',
            'custTransactionCurrencyID' => 'required|numeric|min:1',
            'narration' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $input['custTransactionCurrencyID'], 0);
        if ($company) {
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyRptCurrencyID'] = $company->reportingCurrency;
            $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }


        if ($input['documentType'] == 13 || $input['documentType'] == 15) {
            /*customer reciept*/
            $detail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)->get();

            if ($input['customerID'] != $customerReceivePayment->customerID) {

                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the customer.', 500);
                }

                /*if customer change*/
                $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
                if (empty($customer)) {
                    return $this->sendError('Customer not found.', 500);
                }
                $input['customerGLCode'] = $customer->custGLaccount;
                $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
                $currency = CustomerCurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
                if ($currency) {
                    $input['custTransactionCurrencyID'] = $currency->currencyID;
                    $myCurr = $currency->currencyID;

                    $companyCurrency = \Helper::companyCurrency($customerReceivePayment->companySystemID);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrencyER'] = 0;
                    $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $customerReceivePayment->companySystemID)
                        ->where('isDefault', -1)
                        ->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                        $bankAccount = BankAccount::where('companySystemID', $customerReceivePayment->companySystemID)
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->where('isAccountActive', 1)
                            ->where('approvedYN', 1)
                            ->where('accountCurrencyID', $myCurr)
                            ->first();
                        if ($bankAccount) {
                            $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                            $input['bankCurrency'] = $myCurr;
                            $input['bankCurrencyER'] = 1;
                        }
                    }
                }
            }


            if ($input['bankAccount'] != $customerReceivePayment->bankAccount) {

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMaster) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                    }
                }
            }

            if ($input['custTransactionCurrencyID'] != $customerReceivePayment->custTransactionCurrencyID) {
                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the currency.', 500);
                } else {
                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($customerReceivePayment->companySystemID);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrency'] = null;
                    $input['bankCurrencyER'] = 0;


                    $bank = BankAssign::select('bankmasterAutoID')
                        ->where('companySystemID', $customerReceivePayment->companySystemID)
                        ->where('isDefault', -1)
                        ->first();

                    if ($bank) {
                        $bankAccount = BankAccount::where('companySystemID', $customerReceivePayment->companySystemID)
                            ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                            ->where('isDefault', 1)
                            ->where('accountCurrencyID', $myCurr)
                            ->first();

                        $input['bankID'] = $bank->bankmasterAutoID;

                        if ($bankAccount) {
                            $input['bankAccount'] = $bankAccount->bankAccountAutoID;

                            $input['bankCurrency'] = $myCurr;
                            $input['bankCurrencyER'] = 1;
                        }
                    }
                }
            }

            if ($input['bankID'] != $customerReceivePayment->bankID) {
                $bankAccount = BankAccount::where('companySystemID', $customerReceivePayment->companySystemID)
                    ->where('bankmasterAutoID', $input['bankID'])
                    ->where('isDefault', 1)
                    ->where('isAccountActive', 1)
                    ->where('approvedYN', 1)
                    ->where('accountCurrencyID', $input['custTransactionCurrencyID'])
                    ->first();

                $input['bankAccount'] = null;
                $input['bankCurrencyER'] = 0;
                $input['bankCurrency'] = null;
                if ($bankAccount) {
                    $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                    $input['bankCurrencyER'] = 1;
                    $input['bankCurrency'] = $input['custTransactionCurrencyID'];
                }
            }


        }

        if ($input['documentType'] == 14 || $input['documentType'] == 15) {
            /*direct receipt*/
            $detail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

            if ($input['bankID'] != $customerReceivePayment->bankID) {
                $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)
                    ->where('bankmasterAutoID', $input['bankID'])
                    ->where('isAccountActive', 1)
                    ->where('approvedYN', 1)
                    ->where('isDefault', 1)
                    ->first();

                if ($bankAccount) {
                    $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMasterBank = \Helper::currencyConversion($customerReceivePayment->companySystemID, $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMasterBank) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMasterBank['transToDocER'];
                    }
                }
            }

            if ($input['bankAccount'] != $customerReceivePayment->bankAccount) {

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $currencyConversionDefaultMasterBank = \Helper::currencyConversion($customerReceivePayment->companySystemID, $input['custTransactionCurrencyID'], $bankAccount->accountCurrencyID, 0);
                    if ($currencyConversionDefaultMasterBank) {
                        $input['bankCurrencyER'] = $currencyConversionDefaultMasterBank['transToDocER'];
                    }
                }
            }
        }

        // calculating header total
        $checkPreDirectSumTrans = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('DRAmount');

        $checkPreDirectSumLocal = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('localAmount');

        $checkPreDirectSumReport = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('comRptAmount');

        $masterHeaderSumTrans = 0;
        $masterHeaderSumLocal = 0;
        $masterHeaderSumReport = 0;
        if ($input['documentType'] == 13) {

            $customerReceiveAmountTrans = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                ->sum('receiveAmountTrans');

            $customerReceiveAmountLocal = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                ->sum('receiveAmountLocal');

            $customerReceiveAmountReport = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                ->sum('receiveAmountRpt');

            $masterHeaderSumTrans = $checkPreDirectSumTrans + $customerReceiveAmountTrans;
            $masterHeaderSumLocal = $checkPreDirectSumLocal + $customerReceiveAmountLocal;
            $masterHeaderSumReport = $checkPreDirectSumReport + $customerReceiveAmountReport;

            $masterHeaderSumTrans = abs($masterHeaderSumTrans);
            $masterHeaderSumLocal = abs($masterHeaderSumLocal);
            $masterHeaderSumReport = abs($masterHeaderSumReport);

            $input['receivedAmount'] = (\Helper::roundValue($masterHeaderSumTrans) * -1);
            $input['localAmount'] = (\Helper::roundValue($masterHeaderSumLocal) * -1);
            $input['companyRptAmount'] = (\Helper::roundValue($masterHeaderSumReport) * -1);

        }
        else if ($input['documentType'] == 14 || $input['documentType'] == 15) {

            $masterHeaderSumTrans = $checkPreDirectSumTrans;
            $masterHeaderSumLocal = $checkPreDirectSumLocal;
            $masterHeaderSumReport = $checkPreDirectSumReport;

            if($input['documentType'] == 15){

                $detailsTotal = AdvanceReceiptDetails::select(
                    DB::raw("IFNULL(SUM(paymentAmount),0) as netAmount"),
                    DB::raw("IFNULL(SUM(localAmount),0) as netAmountLocal"),
                    DB::raw("IFNULL(SUM(comRptAmount),0) as netAmountRpt"))
                    ->where('custReceivePaymentAutoID', $id)
                    ->first();

                if(!empty($detailsTotal)){
                    $masterHeaderSumTrans  = $masterHeaderSumTrans + $detailsTotal->netAmount;
                    $masterHeaderSumLocal  = $masterHeaderSumLocal + $detailsTotal->netAmountLocal;
                    $masterHeaderSumReport = $masterHeaderSumReport + $detailsTotal->netAmountRpt;
                }

            }

            $masterHeaderSumTrans = abs($masterHeaderSumTrans);
            $masterHeaderSumLocal = abs($masterHeaderSumLocal);
            $masterHeaderSumReport = abs($masterHeaderSumReport);

            $input['receivedAmount'] = (\Helper::roundValue($masterHeaderSumTrans) * -1);
            $input['localAmount'] = (\Helper::roundValue($masterHeaderSumLocal) * -1);
            $input['companyRptAmount'] = (\Helper::roundValue($masterHeaderSumReport) * -1);
        }

        // calculating bank amount

        if ($input['bankCurrency'] == $input['localCurrencyID']) {
            $input['bankAmount'] = $input['localAmount'];
            $input['bankCurrencyER'] = $input['localCurrencyER'];
        } else if ($input['bankCurrency'] == $input['companyRptCurrencyID']) {
            $input['bankAmount'] = $input['companyRptAmount'];
            $input['bankCurrencyER'] = $input['companyRptCurrencyER'];
        } else {
            $bankCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['custTransactionCurrencyID'], $input['bankCurrency'], $masterHeaderSumTrans);

            if ($bankCurrencyConversion) {
                $input['bankAmount'] = (\Helper::roundValue($bankCurrencyConversion['documentAmount']) * -1);
                $input['bankCurrencyER'] = $bankCurrencyConversion['transToDocER'];
            }
        }

        $itemExistArray = array();
        $error_count = 0;

        if ($customerReceivePayment->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'companyFinancePeriodID' => 'required|numeric|min:1',
                'companyFinanceYearID' => 'required|numeric|min:1',
                'custPaymentReceiveDate' => 'required',
                'bankID' => 'required|numeric|min:1',
                'bankCurrency' => 'required|numeric|min:1',
                'bankAccount' => 'required|numeric|min:1',
                'custTransactionCurrencyID' => 'required|numeric|min:1',
                'narration' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            if ($input['documentType'] == 13) {

                $customerReceivePaymentDetailCount = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->count();
                if ($customerReceivePaymentDetailCount == 0) {
                    return $this->sendError('Every receipt voucher should have at least one item', 500);
                }
            }
            else if ($input['documentType'] == 14 || $input['documentType'] == 15) {
                $checkDirectItemsCount = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->count();

                if($input['documentType'] == 14 && $checkDirectItemsCount == 0){
                    return $this->sendError('Every receipt voucher should have at least one item', 500);
                }

                $checkAdvReceiptDetails = AdvanceReceiptDetails::where('custReceivePaymentAutoID',$id)->count();

                if ($checkAdvReceiptDetails == 0 && $checkDirectItemsCount == 0) {
                    return $this->sendError('Every receipt voucher should have at least one item', 500);
                }
            }
            // checking minus value
            if ($input['documentType'] == 13) {

                $checkDirectMinusTotal = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->sum('DRAmount');

                $checkReciveDetailMinusTotal = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->sum('receiveAmountTrans');

                $netMinustot = $checkReciveDetailMinusTotal + $checkDirectMinusTotal;

                if ($netMinustot < 0) {
                    return $this->sendError('Net amount cannot be minus total', 500);
                }
            }

            if ($input['documentType'] == 13) {

                $detailAllRecords = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->get();

                if ($detailAllRecords) {
                    foreach ($detailAllRecords as $row) {
                        if ($row['addedDocumentSystemID'] == 20) {
                            $checkAmount = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                                ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                                ->where('receiveAmountTrans', '<=', 0)
                                ->where(function ($q) {
                                    $q->where('receiveAmountTrans', '<=', 0)
                                        ->orWhereNull('receiveAmountLocal', '<=', 0)
                                        ->orWhereNull('receiveAmountRpt', '<=', 0)
                                        ->orWhereNull('receiveAmountTrans')
                                        ->orWhereNull('receiveAmountLocal')
                                        ->orWhereNull('receiveAmountRpt');
                                })
                                ->count();

                            if ($checkAmount > 0) {
                                return $this->sendError('Amount should be greater than 0 for every items', 500);
                            }
                        } elseif ($row['addedDocumentSystemID'] == 19) {
                            $checkAmount = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                                ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                                ->where(function ($q) {
                                    $q->where('receiveAmountTrans', '=', 0)
                                        ->orWhereNull('receiveAmountLocal', '=', 0)
                                        ->orWhereNull('receiveAmountRpt', '=', 0)
                                        ->orWhereNull('receiveAmountTrans')
                                        ->orWhereNull('receiveAmountLocal')
                                        ->orWhereNull('receiveAmountRpt');
                                })
                                ->count();

                            if ($checkAmount > 0) {
                                return $this->sendError('Amount should be greater than 0 for every items', 500);
                            }
                        }
                    }
                }

                $checkQuantity = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->where(function ($q) {
                        $q->where('DRAmount', '=', 0)
                            ->orWhereNull('localAmount', '=', 0)
                            ->orWhereNull('comRptAmount', '=', 0)
                            ->orWhereNull('DRAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();
                if ($checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            if ($input['documentType'] == 14 ||  $input['documentType'] == 15) {
                $checkQuantity = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->where(function ($q) {
                        $q->where('DRAmount', '<=', 0)
                            ->orWhereNull('localAmount', '<=', 0)
                            ->orWhereNull('comRptAmount', '<=', 0)
                            ->orWhereNull('DRAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();
                if ($input['documentType'] == 14 && $checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }

                $checkAdvReceiptDetailsAmount = AdvanceReceiptDetails::where('custReceivePaymentAutoID',$id)
                    ->where(function ($q) {
                        $q->where('paymentAmount', '<=', 0)
                            ->orWhereNull('localAmount', '<=', 0)
                            ->orWhereNull('comRptAmount', '<=', 0)
                            ->orWhereNull('paymentAmount')
                            ->orWhereNull('localAmount')
                            ->orWhereNull('comRptAmount');
                    })
                    ->count();

                if ($checkAdvReceiptDetailsAmount > 0 || $checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if ($input['documentType'] == 14 || $input['documentType'] == 15) {
                $directReceiptDetail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

                $finalError = array('amount_zero' => array(),
                    'amount_neg' => array(),
                    'required_serviceLine' => array(),
                    'active_serviceLine' => array(),
                    'contract_check' => array()
                );

                foreach ($directReceiptDetail as $item) {

                    $updateItem = DirectReceiptDetail::find($item['directReceiptDetailsID']);

                    if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glCode);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['required_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }

                    $updateItem->save();

                    if ($updateItem->DRAmount == 0 || $updateItem->localAmount == 0 || $updateItem->comRptAmount == 0) {
                        array_push($finalError['amount_zero'], $updateItem->itemPrimaryCode);
                        $error_count++;
                    }
                }

                if ($policyConfirmedUserToApprove->isYesNO == 0) {

                    foreach ($directReceiptDetail as $item) {

                        $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')
                            ->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)
                            ->first();

                        if ($chartOfAccount->controlAccountsSystemID == 1) {
                            if ($item['contractUID'] == '' || $item['contractUID'] == 0) {
                                array_push($finalError['contract_check'], $item->glCode);
                                $error_count++;
                            }
                        }
                    }
                }

                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                }
            }

            if ($input['documentType'] == 13) {
                $directReceiptDetail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

                $finalError = array('amount_zero' => array(),
                    'amount_neg' => array(),
                    'required_serviceLine' => array(),
                    'active_serviceLine' => array()
                );

                foreach ($directReceiptDetail as $item) {

                    $updateItem = DirectReceiptDetail::find($item['directReceiptDetailsID']);

                    if ($updateItem->serviceLineSystemID && !is_null($updateItem->serviceLineSystemID)) {

                        $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $updateItem->serviceLineSystemID)
                            ->where('isActive', 1)
                            ->first();
                        if (empty($checkDepartmentActive)) {
                            $updateItem->serviceLineSystemID = null;
                            $updateItem->serviceLineCode = null;
                            array_push($finalError['active_serviceLine'], $updateItem->glCode);
                            $error_count++;
                        }
                    } else {
                        array_push($finalError['required_serviceLine'], $updateItem->glCode);
                        $error_count++;
                    }

                    $updateItem->save();

                }

                $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                if ($error_count > 0) {
                    return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                }
            }
            // updating accounts receivable ledger table
            if ($input['documentType'] == 13) {

                $customerReceivePaymentDetailRec = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->where('addedDocumentSystemID', 20)
                    ->get();

                foreach ($customerReceivePaymentDetailRec as $item) {

                    $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $item['arAutoID'])
                        ->sum('receiveAmountTrans');

                    $customerInvoiceMaster = CustomerInvoiceDirect::find($item['bookingInvCodeSystem']);
                    if (round($totalReceiveAmountTrans, $documentCurrencyDecimalPlace) > round(($customerInvoiceMaster['bookingAmountTrans'] + $customerInvoiceMaster['VATAmount']), $documentCurrencyDecimalPlace)) {

                        $itemDrt = "Selected invoice " . $item['bookingInvCode'] . " booked more than the invoice amount.";
                        $itemExistArray[] = [$itemDrt];

                    }
                }
            }


            if (!empty($itemExistArray)) {
                return $this->sendError($itemExistArray, 422);
            }

            // updating accounts receivable ledger table
            if ($input['documentType'] == 13) {

                $customerReceivePaymentDetailRec = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                    ->where('addedDocumentSystemID', '<>', '')
                    ->get();

                foreach ($customerReceivePaymentDetailRec as $row) {

                    $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $row['arAutoID'])
                        ->sum('receiveAmountTrans');

                    $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
                        ->where('companySystemID', $row["companySystemID"])
                        ->where('PayMasterAutoId', $row["bookingInvCodeSystem"])
                        ->where('documentSystemID', $row["addedDocumentSystemID"])
                        ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')->first();

                    if (!$matchedAmount) {
                        $matchedAmount['SumOfmatchedAmount'] = 0;
                    }

                    $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];

                    $arLedgerUpdate = AccountsReceivableLedger::find($row['arAutoID']);

                    if ($row['addedDocumentSystemID'] == 20) {
                        if ($totReceiveAmount == 0) {
                            $arLedgerUpdate->fullyInvoiced = 0;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        } else if ($row->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount > $row->bookingAmountTrans) {
                            $arLedgerUpdate->fullyInvoiced = 2;
                            $arLedgerUpdate->selectedToPaymentInv = -1;
                        } else if (($row->bookingAmountTrans > $totReceiveAmount) && ($totReceiveAmount > 0)) {
                            $arLedgerUpdate->fullyInvoiced = 1;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        }
                    } else if ($row['addedDocumentSystemID'] == 19) {
                        if ($totReceiveAmount == 0) {
                            $arLedgerUpdate->fullyInvoiced = 0;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        } else if ($row->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount < $row->bookingAmountTrans) {
                            $arLedgerUpdate->fullyInvoiced = 2;
                            $arLedgerUpdate->selectedToPaymentInv = -1;
                        } else if (($row->bookingAmountTrans < $totReceiveAmount) && ($totReceiveAmount < 0)) {
                            $arLedgerUpdate->fullyInvoiced = 1;
                            $arLedgerUpdate->selectedToPaymentInv = 0;
                        }
                    }
                    $arLedgerUpdate->save();
                }

            }

            if($input['documentType'] == 14 || $input['documentType'] == 15){
                $details = DirectReceiptDetail::select(DB::raw("IFNULL(SUM(DRAmount),0) as receivedAmount"),
                    DB::raw("IFNULL(SUM(localAmount),0) as localAmount"),
                    DB::raw("IFNULL(SUM(DRAmount),0) as bankAmount"),
                    DB::raw("IFNULL(SUM(comRptAmount),0) as companyRptAmount"),
                    DB::raw("IFNULL(SUM(VATAmount),0) as VATAmount"),
                    DB::raw("IFNULL(SUM(VATAmountLocal),0) as VATAmountLocal"),
                    DB::raw("IFNULL(SUM(VATAmountRpt),0) as VATAmountRpt"),
                    DB::raw("IFNULL(SUM(netAmount),0) as netAmount"),
                    DB::raw("IFNULL(SUM(netAmountLocal),0) as netAmountLocal"),
                    DB::raw("IFNULL(SUM(netAmountRpt),0) as netAmountRpt"))
                    ->where('directReceiptAutoID', $id)
                    ->first();

                if(!empty($details)) {
                    $input['VATAmount'] = $details->VATAmount;
                    $input['VATAmountLocal'] = $details->VATAmountLocal;
                    $input['VATAmountRpt'] = $details->VATAmountRpt;
                    $input['netAmount'] = $details->netAmount;
                    $input['netAmountLocal'] = $details->netAmountLocal;
                    $input['netAmountRpt'] = $details->netAmountRpt;
                }

                if($input['documentType'] == 15){
                    $details = AdvanceReceiptDetails::select(DB::raw("IFNULL(SUM(VATAmount),0) as VATAmount"),
                        DB::raw("IFNULL(SUM(VATAmountLocal),0) as VATAmountLocal"),
                        DB::raw("IFNULL(SUM(VATAmountRpt),0) as VATAmountRpt"),
                        DB::raw("IFNULL(SUM(paymentAmount),0) as netAmount"),
                        DB::raw("IFNULL(SUM(localAmount),0) as netAmountLocal"),
                        DB::raw("IFNULL(SUM(comRptAmount),0) as netAmountRpt"))
                        ->where('custReceivePaymentAutoID', $id)
                        ->first();

                    if(!empty($details)) {
                        $input['VATAmount'] = $details->VATAmount;
                        $input['VATAmountLocal'] = $details->VATAmountLocal;
                        $input['VATAmountRpt'] = $details->VATAmountRpt;
                        $input['netAmount'] = $details->netAmount;
                        $input['netAmountLocal'] = $details->netAmountLocal;
                        $input['netAmountRpt'] = $details->netAmountRpt;
                    }
                }
            }

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', $input["documentSystemID"])
                ->delete();

            // if VAT Applicable
            if(isset($input['isVATApplicable']) && $input['isVATApplicable'] && isset($input['VATAmount']) && $input['VATAmount'] > 0){

                if(empty(TaxService::getOutputVATGLAccount($input["companySystemID"]))) {
                    return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
                }

                if($input['documentType'] == 15 && empty(TaxService::getOutputVATTransferGLAccount($input["companySystemID"]))){
                    return $this->sendError('Cannot confirm. Output VAT Transfer GL Account not configured.', 500);
                }

                $taxDetail['companyID'] = $input['companyID'];
                $taxDetail['companySystemID'] = $input['companySystemID'];
                $taxDetail['documentID'] = $input['documentID'];
                $taxDetail['documentSystemID'] = $input['documentSystemID'];
                $taxDetail['documentSystemCode'] = $id;
                $taxDetail['documentCode'] = $customerReceivePayment->custPaymentReceiveCode;
                $taxDetail['taxShortCode'] = '';
                $taxDetail['taxDescription'] = '';
                $taxDetail['taxPercent'] = $input['VATPercentage'];


                if($input['documentType'] == 15){
                    $taxDetail['payeeSystemCode'] = $input['customerID'];
                    $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();

                    if(!empty($customer)) {
                        $taxDetail['payeeCode'] = $customer->CutomerCode;
                        $taxDetail['payeeName'] = $customer->CustomerName;
                    }else{
                        return $this->sendError('Customer not found', 500);
                    }
                }else {
                    $taxDetail['payeeSystemCode'] = 0;
                    $taxDetail['payeeCode'] = '';
                    $taxDetail['payeeName'] = '';
                }

                $taxDetail['amount'] = $input['VATAmount'];
                $taxDetail['localCurrencyER']  = $input['localCurrencyER'];
                $taxDetail['rptCurrencyER'] = $input['companyRptCurrencyER'];
                $taxDetail['localAmount'] = $input['VATAmountLocal'];
                $taxDetail['rptAmount'] = $input['VATAmountRpt'];
                $taxDetail['currency'] =  $input['custTransactionCurrencyID'];
                $taxDetail['currencyER'] =  1;

                $taxDetail['localCurrencyID'] =  $customerReceivePayment->localCurrencyID;
                $taxDetail['rptCurrencyID'] =  $customerReceivePayment->companyRptCurrencyID;
                $taxDetail['payeeDefaultCurrencyID'] =  $input['custTransactionCurrencyID'];
                $taxDetail['payeeDefaultCurrencyER'] =  1;
                $taxDetail['payeeDefaultAmount'] =  $input['VATAmount'];

                Taxdetail::create($taxDetail);
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);


            if ($input['pdcChequeYN']) {
                $pdcLogValidation = PdcLog::where('documentSystemID', $input['documentSystemID'])
                    ->where('documentmasterAutoID', $id)
                    ->whereNull('chequeDate')
                    ->first();

                if ($pdcLogValidation) {
                    return $this->sendError('PDC Cheque date cannot be empty', 500);
                }

                $pdcLogValidationChequeNo = PdcLog::where('documentSystemID', $input['documentSystemID'])
                    ->where('documentmasterAutoID', $id)
                    ->whereNull('chequeNo')
                    ->first();

                if ($pdcLogValidationChequeNo) {
                    return $this->sendError('PDC Cheque no cannot be empty', 500);
                }


                $totalAmountForPDC = 0;
                if ($input['documentType'] == 13) {
                    $detailAllRecordsSum = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                        ->sum('receiveAmountTrans');
                    $bankTotal = DirectReceiptDetail::where('directReceiptAutoID', $id)->sum('netAmount');

                    $totalAmountForPDC = $detailAllRecordsSum + $bankTotal;
                } else if ($input['documentType'] == 14) {
                    $totalAmountForPDC = DirectReceiptDetail::where('directReceiptAutoID', $id)->sum('netAmount');
                } else if ($input['documentType'] == 15) {
                    $totalAmountForPDC = DirectReceiptDetail::where('directReceiptAutoID', $id)->sum('netAmount');
                }

                $pdcLog = PdcLog::where('documentSystemID', $customerReceivePayment->documentSystemID)
                    ->where('documentmasterAutoID', $id)
                    ->get();

                if (count($pdcLog) == 0) {
                    return $this->sendError('PDC Cheques not created, Please create atleast one cheque', 500);
                }

                $pdcLogAmount = PdcLog::where('documentSystemID', $customerReceivePayment->documentSystemID)
                    ->where('documentmasterAutoID', $id)
                    ->sum('amount');

                $checkingAmount = round($totalAmountForPDC, 3) - round($pdcLogAmount, 3);

                if ($checkingAmount > 0.001 || $checkingAmount < 0) {
                    return $this->sendError('PDC Cheque amount should equal to PV total amount', 500);
                }

                $checkPlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "pdc-receivable-account");

                if (is_null($checkPlAccount)) {
                    return $this->sendError('Please configure PDC Payable account for payment voucher', 500);
                }
            }


            $params = array('autoID' => $id,
                'company' => $customerReceivePayment->companySystemID,
                'document' => $customerReceivePayment->documentSystemID,
                'segment' => 0,
                'category' => 0,
                'amount' => $input['receivedAmount']
            );

            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            }
        }

        if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
            $input['custChequeDate'] = null;
            $input['custChequeNo'] = null;
        }

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;



        if(isset($input['bankAccount']))
        {
            if(!empty($input['bankAccount']) )
            {
                $bank_currency = $input['bankCurrency'];
                $document_currency = $input['custTransactionCurrencyID'];

                $cur_det['companySystemID'] = $input['companySystemID'];
                $cur_det['bankmasterAutoID'] = $input['bankID'];
                $cur_det['bankAccountAutoID'] = $input['bankAccount'];
                $cur_det_info =  (object)$cur_det;

                $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);

                $amount = $bankBalance['netBankBalance'];
                $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();

                $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');


                $input['bankAccountBalance'] = $rounded_amount;

            }
        }
        $customerReceivePayment = $this->customerReceivePaymentRepository->update($input, $id);

        return $this->sendReponseWithDetails($input, 'Receipt Voucher updated successfully',1,$confirm['data'] ?? null);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerReceivePayments/{id}",
     *      summary="Remove the specified CustomerReceivePayment from storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Delete CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
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
        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $customerReceivePayment->delete();

        return $this->sendResponse($id, 'Customer Receive Payment deleted successfully');
    }

    public function recieptVoucherLocalUpdate($id, Request $request){

        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = DirectReceiptDetail::where('directReceiptAutoID',$id)->get();

        $masterINVID = CustomerReceivePayment::findOrFail($id);
            $VATAmountLocal = \Helper::roundValue($masterINVID->VATAmount/$value);
            $netAmountLocal = \Helper::roundValue($masterINVID->netAmount/$value);
            $localAmount = \Helper::roundValue($masterINVID->receivedAmount/$value);

            $masterInvoiceArray = array('localCurrencyER'=>$value, 'VATAmountLocal'=>$VATAmountLocal, 'netAmountLocal'=>$netAmountLocal, 'localAmount'=>$localAmount);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $localAmount = \Helper::roundValue($item->DRAmount / $value);
            $itemVATAmountLocal = \Helper::roundValue($item->VATAmount / $value);
            $itemNetAmountLocal = \Helper::roundValue($item->netAmount / $value);
            $directInvoiceDetailsArray = array('localCurrencyER'=>$value, 'localAmount'=>$localAmount,'VATAmountLocal'=>$itemVATAmountLocal, 'netAmountLocal'=>$itemNetAmountLocal);
            $updatedLocalER = DirectReceiptDetail::findOrFail($item->directReceiptDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], 'Update Local ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function recieptVoucherReportingUpdate($id, Request $request){

        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {
        $details = DirectReceiptDetail::where('directReceiptAutoID',$id)->get();

        $masterINVID = CustomerReceivePayment::findOrFail($id);
        $VATAmountRpt = \Helper::roundValue($masterINVID->VATAmount/$value);
        $netAmountRpt = \Helper::roundValue($masterINVID->netAmount/$value);
        $rptAmount = \Helper::roundValue($masterINVID->receivedAmount/$value);


            $masterInvoiceArray = array('companyRptCurrencyER'=>$value, 'VATAmountRpt'=>$VATAmountRpt, 'netAmountRpt'=>$netAmountRpt, 'companyRptAmount'=>$rptAmount);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $reportingAmount = \Helper::roundValue($item->DRAmount / $value);
            $itemVATAmountRpt = \Helper::roundValue($item->VATAmount / $value);
            $itemNetAmountRpt = \Helper::roundValue($item->netAmount / $value);
            $directInvoiceDetailsArray = array('comRptCurrencyER'=>$value, 'comRptAmount'=>$reportingAmount,'VATAmountRpt'=>$itemVATAmountRpt, 'netAmountRpt'=>$itemNetAmountRpt);
            $updatedLocalER = DirectReceiptDetail::findOrFail($item->directReceiptDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], 'Update Reporting ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function getRecieptVoucherFormData(Request $request)
    {
        $input = $request->all();
        /*companySystemID*/
        $companySystemID = isset($input['companyId']) ? $input['companyId'] : 0;
        $type = $input['type']; /*value ['filter','create','getCurrency']*/
        $advaceReceipt  = array('value' => 15, 'label' => 'Advance Receipt');

        switch ($type) {
            case 'filter':
                $output['yesNoSelectionForMinus'] = YesNoSelectionForMinus::all();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['month'] = Months::all();
                $output['years'] = CustomerReceivePayment::select(DB::raw("YEAR(custPaymentReceiveDate) as year"))
                    ->whereNotNull('custPaymentReceiveDate')
                    ->where('companySystemID', $companySystemID)
                    ->groupby('year')
                    ->orderby('year', 'desc')
                    ->get();
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'),
                                               array('value' => 14, 'label' => 'Direct Receipt'));

                if(Helper::checkPolicy($companySystemID,49)){
                    array_push($output['invoiceType'], $advaceReceipt);
                }

                $output['paymentType'] = PaymentType::all();
                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                    ->get();
                $output['payee'] = Employee::select(DB::raw("employeeSystemID,CONCAT(empID, ' | ' ,empName) as employeeName"))->where('empCompanySystemID', $companySystemID)->where('discharegedYN', '<>', 2)->get();
                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName,vatEligible,vatPercentage"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })     
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                break;

            case 'create':

                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName,vatEligible,vatPercentage"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })       
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID, 1);
                $output['company'] = Company::select('CompanyName', 'CompanyID','vatRegisteredYN')->where('companySystemID', $companySystemID)->first();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'),
                                               array('value' => 14, 'label' => 'Direct Receipt'));
                $output['paymentType'] = PaymentType::all();
                
                if(Helper::checkPolicy($companySystemID,49)){
                    array_push($output['invoiceType'], $advaceReceipt);
                }
                $output['payee'] = Employee::select(DB::raw("employeeSystemID,CONCAT(empID, ' | ' ,empName) as employeeName"))->where('empCompanySystemID', $companySystemID)->where('discharegedYN', '<>', 2)->get();

                $output['isProjectBase'] = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $companySystemID)
                ->where('isYesNO', 1)
                ->exists();

                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                    ->get();

                break;
            case 'getCurrency':
                $customerID = $input['customerID'];
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                break;

            case 'edit':
                $id = $input['id'];
                $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID','vatRegisteredYN')->where('companySystemID', $companySystemID)->first();
                $output['expenseClaimType'] = ExpenseClaimType::all();
                $output['payee'] = Employee::select(DB::raw("employeeSystemID,CONCAT(empID, ' | ' ,empName) as employeeName"))->where('empCompanySystemID', $companySystemID)->where('discharegedYN', '<>', 2)->get();


                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                }
                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName,vatEligible,vatPercentage"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })      
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->approved()->withAssigned($companySystemID)->get();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['docType'] = $master->documentType;
                $output['payeeTypeID'] = $master->payeeTypeID;
                $output['bankDropdown'] = BankAssign::where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->where('companySystemID', $companySystemID)
                    ->get();
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'),
                    array('value' => 14, 'label' => 'Direct Receipt'),array('value' => 15, 'label' => 'Advance Receipt'));


                $output['bankAccount'] = [];
                $output['bankCurrencies'] = [];
                if ($master->bankID != '') {
                    $output['bankAccount'] = BankAccount::where('companySystemID', $companySystemID)
                        ->where('bankmasterAutoID', $master->bankID)
                        ->where('isAccountActive', 1)
                        ->where('approvedYN', 1)
                        ->get();
                }
                if ($master->bankAccount != '') {
                    $output['bankCurrencies'] = DB::table('erp_bankaccount')
                        ->join('currencymaster', 'accountCurrencyID', '=', 'currencymaster.currencyID')
                        ->where('companySystemID', $companySystemID)
                        ->where('bankmasterAutoID', $master->bankID)
                        ->where('bankAccountAutoID', $master->bankAccount)
                        ->where('isAccountActive', 1)
                        ->select('currencymaster.currencyID', 'currencymaster.CurrencyCode')
                        ->get();
                }
                $output['paymentType'] = PaymentType::all();

                $output['isProjectBase'] = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $companySystemID)
                ->where('isYesNO', 1)
                ->exists();

                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                    ->get();

                break;
            case 'amendEdit':
                $id = $input['id'];
                $master = CustomerReceivePaymentRefferedHistory::where('custReceivePaymentRefferedID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID','vatRegisteredYN')->where('companySystemID', $companySystemID)->first();
                $output['expenseClaimType'] = ExpenseClaimType::all();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                }

                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName,vatEligible,vatPercentage"))
                    ->whereHas('customer_master',function($q){
                        $q->where('isCustomerActive',1);
                    })       
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->approved()->withAssigned($companySystemID)->get();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['docType'] = $master->documentType;
                $output['bankDropdown'] = BankAssign::where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->where('companySystemID', $companySystemID)
                    ->get();

                $output['bankAccount'] = [];
                $output['bankCurrencies'] = [];
                if ($master->bankID != '') {
                    $output['bankAccount'] = BankAccount::where('companySystemID', $companySystemID)
                        ->where('bankmasterAutoID', $master->bankID)
                        ->where('isAccountActive', 1)
                        ->where('approvedYN', 1)
                        ->get();
                }
                if ($master->bankAccount != '') {
                    $output['bankCurrencies'] = DB::table('erp_bankaccount')
                        ->join('currencymaster', 'accountCurrencyID', '=', 'currencymaster.currencyID')
                        ->where('companySystemID', $companySystemID)
                        ->where('bankmasterAutoID', $master->bankID)
                        ->where('bankAccountAutoID', $master->bankAccount)
                        ->where('isAccountActive', 1)
                        ->select('currencymaster.currencyID', 'currencymaster.CurrencyCode')
                        ->get();
                }

                $output['isProjectBase'] = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $companySystemID)
                ->where('isYesNO', 1)
                ->exists();

                $output['projects'] = ErpProjectMaster::where('companySystemID', $companySystemID)
                    ->get();

                break;
            default:
                $output = [];
        }
        return $this->sendResponse($output, 'Form data');

    }

    public function recieptVoucherDataTable(Request $request)
    {
        $input = $this->convertArrayToSelectedValue($request->all(), ['confirmedYN', 'month', 'approved', 'year', 'documentType', 'trsClearedYN', 'paymentType', 'projectID', 'payeeTypeID']);
        $search = $request->input('search.value');
        $master = $this->customerReceivePaymentRepository->customerReceiveListQuery($request, $input, $search);

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        
        return \DataTables::of($master)
            ->order(function ($query) use ($input) {
                if (request()->has('order') && $input['order'][0]['column'] == 0) {
                    $query->orderBy('custReceivePaymentAutoID', $input['order'][0]['dir']);
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getReceiptVoucherMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = CustomerReceivePayment::where('custReceivePaymentAutoID', $input['custReceivePaymentAutoID'])->with(['project','payment_type','confirmed_by', 'created_by', 'modified_by', 'cancelled_by', 'company', 'bank', 'currency','bank_currency', 'localCurrency', 'rptCurrency', 'customer', 'employee', 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 21);
        }, 'directdetails' => function ($query) {
            $query->with('segment','project');
        }, 'details', 'bankledger_by' => function ($query) {
            $query->with('bankrec_by');
            $query->where('documentSystemID', 21);
        },'audit_trial.modified_by','advance_receipt_details','pdc_cheque'=> function ($query) {
            $query->where('documentSystemID', 21);
        }])->first();

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $output->companySystemID)
        ->where('isYesNO', 1)
        ->exists();

        $output['isProjectBase'] = $isProjectBase;

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function receiptVoucherReopen(Request $request)
    {
        $input = $request->all();

        $custReceivePaymentAutoID = $input['custReceivePaymentAutoID'];

        $custReceivePaymentMaster = CustomerReceivePayment::find($custReceivePaymentAutoID);

        $emails = array();
        if (empty($custReceivePaymentMaster)) {
            return $this->sendError('Customer receive payment not found');
        }

        if ($custReceivePaymentMaster->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this receipt voucher it is already partially approved');
        }

        if ($custReceivePaymentMaster->approved == -1) {
            return $this->sendError('You cannot reopen this receipt voucher it is already fully approved');
        }

        if ($custReceivePaymentMaster->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this receipt voucher, it is not confirmed');
        }

        // updating fields
        $custReceivePaymentMaster->confirmedYN = 0;
        $custReceivePaymentMaster->confirmedByEmpSystemID = null;
        $custReceivePaymentMaster->confirmedByEmpID = null;
        $custReceivePaymentMaster->confirmedByName = null;
        $custReceivePaymentMaster->confirmedDate = null;
        $custReceivePaymentMaster->RollLevForApp_curr = 1;
        $custReceivePaymentMaster->save();


        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $custReceivePaymentMaster->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $custReceivePaymentMaster->bookingInvCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $custReceivePaymentMaster->bookingInvCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $custReceivePaymentMaster->companySystemID)
            ->where('documentSystemCode', $custReceivePaymentMaster->bookingSuppMasInvAutoID)
            ->where('documentSystemID', $custReceivePaymentMaster->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $custReceivePaymentMaster->companySystemID)
                    ->where('documentSystemID', $custReceivePaymentMaster->documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }


        DocumentApproved::where('documentSystemCode', $custReceivePaymentAutoID)
            ->where('companySystemID', $custReceivePaymentMaster->companySystemID)
            ->where('documentSystemID', $custReceivePaymentMaster->documentSystemID)
            ->delete();

        AuditTrial::insertAuditTrial('CustomerReceivePayment', $custReceivePaymentAutoID,$input['reopenComments'],'Reopened');

        return $this->sendResponse($custReceivePaymentMaster->toArray(), 'Supplier Invoice reopened successfully');
    }

    public function printReceiptVoucher(Request $request)
    {

        $id = $request->get('custRecivePayDetAutoID');

        $customerReceivePaymentData = CustomerReceivePayment::find($id);

        if (empty($customerReceivePaymentData)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $customerReceivePaymentRecord = CustomerReceivePayment::where('custReceivePaymentAutoID', $id)->with(['project','payment_type','confirmed_by', 'created_by', 'modified_by', 'company', 'bank', 'currency','bank_currency', 'localCurrency', 'rptCurrency', 'customer', 'employee', 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 21);
        }, 'directdetails' => function ($query) {
            $query->with('project','segment');
        }, 'details','advance_receipt_details','pdc_cheque'=> function ($query) {
            $query->where('documentSystemID', 21);
        }])->first();

        if (empty($customerReceivePaymentRecord)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $refernaceDoc = \Helper::getCompanyDocRefNo($customerReceivePaymentRecord->companySystemID, $customerReceivePaymentRecord->documentSystemID);

        $transDecimal = 2;
        $localDecimal = 3;
        $rptDecimal = 2;
        $advanceDetailsTotalNet = 0;

        if ($customerReceivePaymentRecord->currency) {
            $transDecimal = $customerReceivePaymentRecord->currency->DecimalPlaces;
        }

        if ($customerReceivePaymentRecord->localCurrency) {
            $localDecimal = $customerReceivePaymentRecord->localCurrency->DecimalPlaces;
        }

        if ($customerReceivePaymentRecord->rptCurrency) {
            $rptDecimal = $customerReceivePaymentRecord->rptCurrency->DecimalPlaces;
        }

        $directTotTra = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('DRAmount');

        $directTotalVAT = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('VATAmount');

        $directTotalNet = DirectReceiptDetail::where('directReceiptAutoID', $id)
            ->sum('netAmount');

        $ciDetailTotTra = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
                                                      ->where('matchingDocID', 0)
                                                      ->sum('receiveAmountTrans');

        $advanceDetailsTotalNet =  AdvanceReceiptDetails::where('custReceivePaymentAutoID',$id)->sum('paymentAmount');

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $customerReceivePaymentRecord->companySystemID)
        ->where('isYesNO', 1)
        ->exists();

        $order = array(
            'masterdata' => $customerReceivePaymentRecord,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'localDecimal' => $localDecimal,
            'rptDecimal' => $rptDecimal,
            'directTotTra' => $directTotTra,
            'directTotalVAT' => $directTotalVAT,
            'directTotalNet' => $directTotalNet,
            'ciDetailTotTra' => $ciDetailTotTra,
            'isProjectBase' => $isProjectBase,
            'advanceDetailsTotalNet' => $advanceDetailsTotalNet
        );

        $time = strtotime("now");
        $fileName = 'receipt_voucher_' . $id . '_' . $time . '.pdf';
        $html = view('print.receipt_voucher', $order);
        $htmlFooter = view('print.receipt_voucher_footer', $order);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function getReceiptVoucherApproval(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyID)
            ->where('documentSystemID', 21)
            ->first();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_customerreceivepayment.custReceivePaymentAutoID',
            'erp_customerreceivepayment.custPaymentReceiveCode',
            'erp_customerreceivepayment.documentSystemID',
            'erp_customerreceivepayment.custPaymentReceiveDate',
            'erp_customerreceivepayment.narration',
            'erp_customerreceivepayment.createdDateTime',
            'erp_customerreceivepayment.confirmedDate',
            'erp_customerreceivepayment.receivedAmount',
            'erp_customerreceivepayment.bankAmount',
            'erp_customerreceivepayment.documentType',
            'erp_customerreceivepayment.payeeTypeID',
            'payee.empID',
            'payee.empName as employeeName',
            'erp_customerreceivepayment.PayeeName',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'customerDocCurrency.DecimalPlaces As customerDocDecimalPlaces',
            'customerDocCurrency.CurrencyCode As customerDocCurrencyCode',
            'bankDocCurrency.DecimalPlaces As bankDocDecimalPlaces',
            'bankDocCurrency.CurrencyCode As bankDocCurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $serviceLinePolicy) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
            }
            $query->where('employeesdepartments.documentSystemID', 21)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('erp_customerreceivepayment', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'custReceivePaymentAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_customerreceivepayment.companySystemID', $companyID)
                ->where('erp_customerreceivepayment.approved', 0)
                ->where('erp_customerreceivepayment.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftjoin('employees AS payee', 'erp_customerreceivepayment.PayeeEmpID', '=', 'payee.employeeSystemID')
            ->leftJoin('currencymaster as customerDocCurrency', 'custTransactionCurrencyID', 'customerDocCurrency.currencyID')
            ->leftJoin('currencymaster as bankDocCurrency', 'bankCurrency', 'bankDocCurrency.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 21)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('custPaymentReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $grvMasters = [];
        }

        return \DataTables::of($grvMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function getApprovedRVForCurrentUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_customerreceivepayment.custReceivePaymentAutoID',
            'erp_customerreceivepayment.custPaymentReceiveCode',
            'erp_customerreceivepayment.documentSystemID',
            'erp_customerreceivepayment.custPaymentReceiveDate',
            'erp_customerreceivepayment.narration',
            'erp_customerreceivepayment.createdDateTime',
            'erp_customerreceivepayment.confirmedDate',
            'erp_customerreceivepayment.receivedAmount',
            'erp_customerreceivepayment.bankAmount',
            'erp_customerreceivepayment.documentType',
            'erp_customerreceivepayment.approvedDate',
            'erp_customerreceivepayment.payeeTypeID',
            'payee.empID',
            'payee.empName as employeeName',
            'erp_customerreceivepayment.PayeeName',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'customerDocCurrency.DecimalPlaces As customerDocDecimalPlaces',
            'customerDocCurrency.CurrencyCode As customerDocCurrencyCode',
            'bankDocCurrency.DecimalPlaces As bankDocDecimalPlaces',
            'bankDocCurrency.CurrencyCode As bankDocCurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_customerreceivepayment', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'custReceivePaymentAutoID')
                ->where('erp_customerreceivepayment.companySystemID', $companyID)
                ->where('erp_customerreceivepayment.approved', -1)
                ->where('erp_customerreceivepayment.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftjoin('employees AS payee', 'erp_customerreceivepayment.PayeeEmpID', '=', 'payee.employeeSystemID')
            ->leftJoin('currencymaster as customerDocCurrency', 'custTransactionCurrencyID', 'customerDocCurrency.currencyID')
            ->leftJoin('currencymaster as bankDocCurrency', 'bankCurrency', 'bankDocCurrency.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->where('erp_documentapproved.documentSystemID', 21)
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('custPaymentReceiveCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($grvMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function approveReceiptVoucher(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectReceiptVoucher(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }

    }

    public function amendReceiptVoucher(Request $request)
    {
        $input = $request->all();

        $custReceivePaymentAutoID = $input['custReceivePaymentAutoID'];

        $customerReceivePaymentData = CustomerReceivePayment::find($custReceivePaymentAutoID);

        if (empty($customerReceivePaymentData)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        if ($customerReceivePaymentData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this Receipt Voucher');
        }

        $receivePaymentArray = $customerReceivePaymentData->toArray();

        if(isset($receivePaymentArray['isFromApi'])){
            unset($receivePaymentArray['isFromApi']);
        }


        $storeReceiptVoucherHistory = CustomerReceivePaymentRefferedHistory::insert($receivePaymentArray);

        $customerReceivePaymentDetailRec = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $custReceivePaymentAutoID)->get();

        if (!empty($customerReceivePaymentDetailRec)) {
            foreach ($customerReceivePaymentDetailRec as $bookDetail) {
                $bookDetail['timesReferred'] = $customerReceivePaymentData->timesReferred;
            }
        }

        $customerReceiveDetailArray = $customerReceivePaymentDetailRec->toArray();

        if(isset($customerReceiveDetailArray['isFromApi'])){
            unset($customerReceiveDetailArray['isFromApi']);
        }

        $storeSupplierInvoiceBookDetailHistory = CustReceivePaymentDetRefferedHistory::insert($customerReceiveDetailArray);

        $customerReceivePaymentDirectDetailRec = DirectReceiptDetail::where('directReceiptAutoID', $custReceivePaymentAutoID)->get();

        if (!empty($customerReceivePaymentDirectDetailRec)) {
            foreach ($customerReceivePaymentDirectDetailRec as $bookDirectDetail) {
                $bookDirectDetail['timesReferred'] = $customerReceivePaymentData->timesReferred;
            }
        }

        $ReceivePaymentDirectDetailArray = $customerReceivePaymentDirectDetailRec->toArray();

        if(isset($ReceivePaymentDirectDetailArray['isFromApi'])){
            unset($ReceivePaymentDirectDetailArray['isFromApi']);
        }

        $storeSupplierInvoiceBookDirectDetailHistory = DirectReceiptDetailsRefferedHistory::insert($ReceivePaymentDirectDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $custReceivePaymentAutoID)
            ->where('companySystemID', $customerReceivePaymentData->companySystemID)
            ->where('documentSystemID', $customerReceivePaymentData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $customerReceivePaymentData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $custReceivePaymentAutoID)
            ->where('companySystemID', $customerReceivePaymentData->companySystemID)
            ->where('documentSystemID', $customerReceivePaymentData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $customerReceivePaymentData->refferedBackYN = 0;
            $customerReceivePaymentData->confirmedYN = 0;
            $customerReceivePaymentData->confirmedByEmpSystemID = null;
            $customerReceivePaymentData->confirmedByEmpID = null;
            $customerReceivePaymentData->confirmedByName = null;
            $customerReceivePaymentData->confirmedDate = null;
            $customerReceivePaymentData->RollLevForApp_curr = 1;
            $customerReceivePaymentData->save();
        }


        return $this->sendResponse($customerReceivePaymentData->toArray(), 'Receipt voucher amend successfully');
    }

    public function receiptVoucherCancel(Request $request)
    {
        $input = $request->all();

        $custReceivePaymentAutoID = $input['custReceivePaymentAutoID'];

        $customerReceivePaymentData = CustomerReceivePayment::find($custReceivePaymentAutoID);

        if (empty($customerReceivePaymentData)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        if ($customerReceivePaymentData->confirmedYN == 1) {
            return $this->sendError('You cannot cancel this receipt voucher, this is already confirmed');
        }

        if ($customerReceivePaymentData->approved == -1) {
            return $this->sendError('You cannot cancel this receipt voucher, this is already approved');
        }

        if ($customerReceivePaymentData->cancelYN == -1) {
            return $this->sendError('You cannot cancel this receipt voucher, this is already cancelled');
        }

        $directDetail = DirectReceiptDetail::where('directReceiptAutoID', $custReceivePaymentAutoID)->get();

        if (count($directDetail) > 0) {
            return $this->sendError('You cannot cancel this receipt voucher, invoice details are exist');
        }

        $customerReceiptDetail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $custReceivePaymentAutoID)->get();

        if (count($customerReceiptDetail) > 0) {
            return $this->sendError('You cannot cancel this receipt voucher, invoice details are exist');
        }

        $employee = \Helper::getEmployeeInfo();

        $customerReceivePaymentData->cancelYN = -1;
        $customerReceivePaymentData->cancelComment = $request['cancelComments'];
        $customerReceivePaymentData->cancelDate = NOW();
        $customerReceivePaymentData->cancelledByEmpSystemID = \Helper::getEmployeeSystemID();
        $customerReceivePaymentData->canceledByEmpID = $employee->empID;
        $customerReceivePaymentData->canceledByEmpName = $employee->empFullName;
        $customerReceivePaymentData->save();

        /*Audit entry*/
        AuditTrial::insertAuditTrial('CustomerReceivePayment', $custReceivePaymentAutoID,$input['cancelComments'],'Cancelled');

        return $this->sendResponse($customerReceivePaymentData->toArray(), 'Receipt voucher cancelled successfully');
    }

    public function approvalPreCheckReceiptVoucher(Request $request)
    {
        $approve = \Helper::postedDatePromptInFinalApproval($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"], 500, ['type' => $approve["type"]]);
        } else {
            return $this->sendResponse(array('type' => $approve["type"]), $approve["message"]);
        }

    }

    public function amendReceiptVoucherReview(Request $request)
    {
        $input = $request->all();

        $id = $input['custReceivePaymentAutoID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = $this->customerReceivePaymentRepository->findWithoutFail($id);
        if (empty($masterData)) {
            return $this->sendError('Receipt Voucher Master not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend, this Receipt Voucher, it is not confirmed');
        }


        if(!ApiPermissionServices::checkAmendPermission($masterData->custReceivePaymentAutoID,21))
        {
            return $this->sendError('This is an autogenerated document. This cannot be returned back to amend');
        }



        $documentAutoId = $id;
        $documentSystemID = $masterData->documentSystemID;

        $checkBalance = GeneralLedgerService::validateDebitCredit($documentSystemID, $documentAutoId);
        if (!$checkBalance['status']) {
            $allowValidateDocumentAmend = false;
        } else {
            $allowValidateDocumentAmend = true;
        }

        if($masterData->approved == -1){
            $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID);
            if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                    return $this->sendError($validateFinanceYear['message']);
                }
            }
            
            $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID);
            if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                    return $this->sendError($validateFinancePeriod['message']);
                }
            }
    
            if($allowValidateDocumentAmend){
                $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId,$documentSystemID);
                if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                    if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                        return $this->sendError($validatePendingGlPost['message']);
                    }
                }
            }

            $validateVatReturnFilling = ValidateDocumentAmend::validateVatReturnFilling($documentAutoId,$documentSystemID,$masterData->companySystemID);
            if(isset($validateVatReturnFilling['status']) && $validateVatReturnFilling['status'] == false){
                $errorMessage = "Receipt Voucher " . $validateVatReturnFilling['message'];
                return $this->sendError($errorMessage);
            }
        }


        // checking document matched in matchmaster
        $checkDetailExistMatch = MatchDocumentMaster::where('PayMasterAutoId', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemID)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError('You cannot return back to amend. this Receipt Voucher is added to matching');
        }

        $checkBLDataExist = BankLedger::where('documentSystemCode', $id)
            ->where('companySystemID', $masterData->companySystemID)
            ->where('documentSystemID', $masterData->documentSystemID)
            ->first();

        if ($checkBLDataExist) {
            if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == 0 && $checkBLDataExist->pulledToBankTransferYN == 0) {
                return $this->sendError('Treasury cleared, You cannot return back to amend.');
            } else if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == -1 && $checkBLDataExist->pulledToBankTransferYN == 0) {
                return $this->sendError('Bank cleared. You cannot return back to amend.');
            } else if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == 0 && $checkBLDataExist->pulledToBankTransferYN == -1) {
                return $this->sendError('Added to bank transfer. You cannot return back to amend.');
            } else if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == -1 && $checkBLDataExist->pulledToBankTransferYN == -1) {
                return $this->sendError('Added to bank transfer and bank cleared. You cannot return back to amend.');
            } else if ($checkBLDataExist->trsClearedYN == 0 && $checkBLDataExist->bankClearedYN == 0 && $checkBLDataExist->pulledToBankTransferYN == -1) {
                return $this->sendError('Added to bank transfer. You cannot return back to amend.');
            }
        }

        $emailBody = '<p>' . $masterData->custPaymentReceiveCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';

        $emailSubject = $masterData->custPaymentReceiveCode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($masterData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $masterData->confirmedByEmpSystemID,
                    'companySystemID' => $masterData->companySystemID,
                    'docSystemID' => $masterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $id,
                    'docCode' => $masterData->custPaymentReceiveCode
                );
            }

            $documentApproval = DocumentApproved::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $id)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $masterData->companySystemID,
                        'docSystemID' => $masterData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $id,
                        'docCode' => $masterData->custPaymentReceiveCode
                    );
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            //deleting records from accounts payable
            $deleteAPData = AccountsReceivableLedger::where('documentCodeSystem', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            //deleting records from bank ledger
            $deleteBLData = BankLedger::where('documentSystemCode', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            //deleting records from tax ledger
            TaxLedger::where('documentMasterAutoID', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->delete();

            $taxLedgerDetails = TaxLedgerDetail::where('documentMasterAutoID', $id)
                ->where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemID', $masterData->documentSystemID)
                ->get();

            $returnFilledDetailID = null;
            foreach ($taxLedgerDetails as $taxLedgerDetail) {
                if($taxLedgerDetail->returnFilledDetailID != null){
                    $returnFilledDetailID = $taxLedgerDetail->returnFilledDetailID;
                }
                $taxLedgerDetail->delete();
            }

            if($returnFilledDetailID != null){
                $this->vatReturnFillingMasterRepo->updateVatReturnFillingDetails($returnFilledDetailID);
            }

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approved = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->postedDate = null;
            $masterData->save();

            AuditTrial::insertAuditTrial('CustomerReceivePayment', $id, $input['returnComment'], 'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), 'Receipt Voucher return back to amend successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function checkBRVDocumentActive(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $input["custReceivePaymentAutoID"] = isset($input["custReceivePaymentAutoID"]) ? $input["custReceivePaymentAutoID"] : 0;

        /** @ PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $masterData = $this->customerReceivePaymentRepository->findWithoutFail($input["custReceivePaymentAutoID"]);

        if (empty($masterData)) {
            return $this->sendError('Receipt Voucher not found');
        }

        $bankMaster = BankAssign::ofCompany($masterData->companySystemID)
            ->isActive()
            ->where('bankmasterAutoID', $masterData->bankID)
            ->first();

        if (empty($bankMaster)) {
            return $this->sendError('Selected Bank is not active', 500);
        }

        $bankAccount = BankAccount::isActive()->find($masterData->bankAccount);

        if (empty($bankAccount)) {
            return $this->sendError('Selected Bank Account is not active', 500);
        }

        return $this->sendResponse($bankAccount, 'Record retrieved successfully');
    }

    public function getADVPaymentForBRV(Request $request)
    {
        $input = $request->all();
        $id = isset($input["custReceivePaymentAutoID"]) ? $input["custReceivePaymentAutoID"] : 0;

        /** @ PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $masterData = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($masterData)) {
            return $this->sendError('Receipt Voucher not found');
        }

        $output = DB::select('SELECT
                                erp_salesorderadvpayment.soAdvPaymentID,
                                erp_salesorderadvpayment.companyID,
                                erp_salesorderadvpayment.companySystemID,
                                erp_salesorderadvpayment.soID AS salesOrderID,
                                erp_salesorderadvpayment.soCode AS salesOrderCode,
                                erp_salesorderadvpayment.serviceLineID AS segment,
                                erp_salesorderadvpayment.customerId,
                                erp_salesorderadvpayment.narration AS comments,
                                erp_salesorderadvpayment.currencyID,
                                erp_salesorderadvpayment.VATAmount,
                                erp_salesorderadvpayment.VATAmountLocal,
                                erp_salesorderadvpayment.VATAmountRpt,
                                currencymaster.CurrencyCode,
                                currencymaster.DecimalPlaces,
                                IFNULL( erp_salesorderadvpayment.reqAmount, 0 ) AS reqAmount,
                                ( IFNULL( erp_salesorderadvpayment.reqAmount, 0 ) - IFNULL( advd.SumOfpaymentAmount, 0 ) ) AS BalanceAmount,
                                erp_quotationmaster.transactionCurrencyID AS transactionCurrencyID,
                                erp_quotationmaster.transactionExchangeRate AS transactionExchangeRate,
                                -- erp_quotationmaster.supplierDefaultCurrencyID,
                                -- erp_quotationmaster.supplierDefaultER AS supplierDefaultCurrencyER,
                                erp_quotationmaster.companyLocalCurrency,
                                erp_quotationmaster.companyLocalExchangeRate AS localER,
                                erp_quotationmaster.companyReportingCurrency AS comRptCurrencyID,
                                erp_quotationmaster.companyReportingExchangeRate AS comRptER,
                                erp_quotationmaster.transactionAmount AS totalTransactionAmount,
                                FALSE AS isChecked 
                            FROM
                                ( ( erp_salesorderadvpayment LEFT JOIN currencymaster ON erp_salesorderadvpayment.currencyID = currencymaster.currencyID ) INNER JOIN erp_quotationmaster ON erp_salesorderadvpayment.soID = erp_quotationmaster.quotationMasterID )
                                LEFT JOIN (
                            SELECT
                                erp_advancereceiptdetails.soAdvPaymentID,
                                erp_advancereceiptdetails.companyID,
                                erp_advancereceiptdetails.companySystemID,
                                erp_advancereceiptdetails.salesOrderID,
                                IFNULL( Sum( erp_advancereceiptdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount 
                            FROM
                                erp_advancereceiptdetails 
                            GROUP BY
                                erp_advancereceiptdetails.soAdvPaymentID,
                                erp_advancereceiptdetails.companySystemID,
                                erp_advancereceiptdetails.salesOrderID 
                            HAVING
                                ( ( ( erp_advancereceiptdetails.salesOrderID ) IS NOT NULL ) ) 
                                ) AS advd ON ( erp_salesorderadvpayment.soID = advd.salesOrderID ) 
                                AND ( erp_salesorderadvpayment.soAdvPaymentID = advd.soAdvPaymentID ) 
                                AND ( erp_salesorderadvpayment.companySystemID = advd.companySystemID ) 
                            WHERE
                                (
                                ( ( erp_salesorderadvpayment.companySystemID ) = ' . $masterData->companySystemID . ' ) 
                                AND ( ( erp_salesorderadvpayment.customerId ) = ' . $masterData->customerID . ' ) 
                                AND ( ( erp_salesorderadvpayment.currencyID ) = ' . $masterData->custTransactionCurrencyID . ' ) 
                                AND ( ( erp_quotationmaster.documentDate ) <= "' . $masterData->custPaymentReceiveDate . '" ) 
                                AND ( ( erp_salesorderadvpayment.selectedToPayment ) = 0 ) 
                                AND ( ( erp_quotationmaster.confirmedYN ) = 1 ) 
                                AND ( ( erp_quotationmaster.approvedYN ) = -1 ) 
                                AND ( ( erp_salesorderadvpayment.fullyPaid ) <> 2 ) 
                                );');
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function generatePdcForReceiptVoucher(Request $request)
    {
        $input = $request->all();

        $receipt = CustomerReceivePayment::find($input['custReceivePaymentAutoID']);

        if (empty($receipt)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        DB::beginTransaction();
        try {

            $deleteAllPDC = $this->deleteAllPDC($receipt->documentSystemID, $input['custReceivePaymentAutoID']);
            $bankAccount = BankAccount::find($receipt->bankAccount);

            if (!$bankAccount) {
                return $this->sendError('Bank Account not selected');
            }
    
            $amount = floatval($input['totalAmount']) / floatval($input['noOfCheques']);

            for ($i=0; $i < floatval($input['noOfCheques']); $i++) { 
                $pdcLogData = [
                    'documentSystemID' => $receipt->documentSystemID,
                    'documentmasterAutoID' => $input['custReceivePaymentAutoID'],
                    'paymentBankID' => $bankAccount->bankmasterAutoID,
                    'companySystemID' => $receipt->companySystemID,
                    'currencyID' => $receipt->custTransactionCurrencyID,
                    'chequeRegisterAutoID' => null,
                    'chequeNo' => null,
                    'chequeStatus' => 0,
                    'amount' => $amount,
                ];

                $resPdc = PdcLog::create($pdcLogData);
            }

            DB::commit();
            return $this->sendResponse([], "PDC cheques generated successfully");
        } catch
        (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

    public function deleteAllPDC($documentSystemID, $documentAutoID)
    {
        $cheques = PdcLog::where('documentSystemID', $documentSystemID)
                         ->where('documentmasterAutoID', $documentAutoID)
                         ->get();

        if (count($cheques) > 0) {
            $chequeRegisterAutoIDs = collect($cheques)->pluck('chequeRegisterAutoID')->toArray();


            if (count($chequeRegisterAutoIDs) > 0) {
                $update_array = [
                    'document_id' => null,
                    'document_master_id' => null,
                    'status' => 0,
                ];

                ChequeRegisterDetail::whereIn('id', $chequeRegisterAutoIDs)->update($update_array);
            }

            $chequesDelete = PdcLog::where('documentSystemID', $documentSystemID)
                             ->where('documentmasterAutoID', $documentAutoID)
                             ->delete();

        }
        
        return ['status' => true];
    }

    public function checkConversionRateIsUpdate(Request $request) {

        $request->validate([
            'documentID' => 'required',
            'companyID' => 'required'
        ]);

        $input = $request->all();

        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($input['documentID']);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Receipt Voucher not found');
        }

        $currencyConversion = Helper::currencyConversion($input['companyID'], $customerReceivePayment->custTransactionCurrencyID, $customerReceivePayment->bankCurrency, 0);
        if(($customerReceivePayment->bankCurrencyER != $currencyConversion['transToDocER']) || ($customerReceivePayment->localCurrencyER != $currencyConversion['trasToLocER']) || ($customerReceivePayment->companyRptCurrencyER != $currencyConversion['trasToRptER'])) {
            $data['isShowWarning'] = 1;
            $data['message'] = "The exchange rates are updated as follows,<br><br>".
                "Previous rates Bank ER ".$customerReceivePayment->bankCurrencyER." | Local ER ".$customerReceivePayment->localCurrencyER." | Reporting ER ".$customerReceivePayment->companyRptCurrencyER."<br><br>".
                "Current rates Bank ER ".$currencyConversion['transToDocER']." | Local ER ".$currencyConversion['trasToLocER']." | Reporting ER ".$currencyConversion['trasToRptER']."<br><br>".
                "Are you sure you want to proceed?";
            return $this->sendResponse($data, "Conversion rates changed");
        }
        else {
            $data['isShowWarning'] = 0;
            return $this->sendResponse($data, "Conversion rates not change");
        }
    }

}

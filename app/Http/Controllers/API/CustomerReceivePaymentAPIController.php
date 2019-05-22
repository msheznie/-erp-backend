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
 * -- Date: 11-January 2019 By: Mubashir Description: Added new function approvalPreCheckReceiptVoucher(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentAPIRequest;
use App\Models\AccountsReceivableLedger;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerAssigned;
use App\Models\CurrencyMaster;
use App\Models\customercurrency;
use App\Models\Company;
use App\Models\CustomerMaster;
use App\Models\BankAccount;
use App\Models\CustomerReceivePaymentRefferedHistory;
use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Models\DirectReceiptDetailsRefferedHistory;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ExpenseClaimType;
use App\Models\MatchDocumentMaster;
use App\Models\SegmentMaster;
use App\Models\CompanyFinanceYear;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DirectReceiptDetail;
use App\Models\BankAssign;
use App\Models\CompanyFinancePeriod;
use App\Models\YesNoSelectionForMinus;
use App\Models\YesNoSelection;
use App\Models\Months;
use App\Repositories\CustomerReceivePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
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

    public function __construct(CustomerReceivePaymentRepository $customerReceivePaymentRepo)
    {
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
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
        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'documentType', 'companyFinanceYearID', 'custTransactionCurrencyID', 'customerID'));

        if ($input['documentType'] == 13 && $input['customerID'] == '') {
            return $this->sendError("Customer is required", 500);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();

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

        unset($inputParam);

        if (isset($input['custPaymentReceiveDate'])) {
            if ($input['custPaymentReceiveDate']) {
                $input['custPaymentReceiveDate'] = new Carbon($input['custPaymentReceiveDate']);
            }
        }

        $documentDate = $input['custPaymentReceiveDate'];
        $monthBegin = $input['FYPeriodDateFrom'];
        $monthEnd = $input['FYPeriodDateTo'];

        if (($documentDate >= $monthBegin) && ($documentDate <= $monthEnd)) {
        } else {
            return $this->sendError('Document date is not within the financial period!', 500);
        }

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();

        $serialNo = CustomerReceivePayment::where('documentSystemID', 21)
            ->where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($serialNo) {
            $lastSerialNumber = intval($serialNo->serialNo) + 1;
        }

        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));

        $custPaymentReceiveCode = ($company->CompanyID . '\\' . $y . '\\BRV' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

        $input['documentSystemID'] = 21;
        $input['documentID'] = 'BRV';
        $input['serialNo'] = $lastSerialNumber;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['custPaymentReceiveCode'] = $custPaymentReceiveCode;
        $input['custChequeDate'] = Carbon::now();

        /*currency*/
        $myCurr = $input['custTransactionCurrencyID'];

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $myCurr, $myCurr, 0);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyRptCurrencyID'] = $company->reportingCurrency;
            $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $input['custTransactionCurrencyER'] = 1;

        $bank = BankAssign::select('bankmasterAutoID')
            ->where('companySystemID', $company['companySystemID'])
            ->where('isDefault', -1)
            ->first();

        if ($bank) {
            $input['bankID'] = $bank->bankmasterAutoID;

            $bankAccount = BankAccount::where('companySystemID', $company['companySystemID'])
                ->where('bankmasterAutoID', $bank->bankmasterAutoID)
                ->where('isDefault', 1)
                ->where('accountCurrencyID', $myCurr)
                ->first();

            if ($bankAccount) {
                $input['bankAccount'] = $bankAccount->bankAccountAutoID;

                $input['bankCurrency'] = $myCurr;
                $input['bankCurrencyER'] = 1;
            }
        }

        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        if ($input['documentType'] == 13) {
            /* Customer Invoice Receipt*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
            $input['customerGLCode'] = $customer->custGLaccount;
        }

        if ($input['documentType'] == 14) {
            $input = array_except($input, 'customerID');
            /* Direct Invoice*/
        }

        if (($input['custPaymentReceiveDate'] >= $companyfinanceperiod->dateFrom) && ($input['custPaymentReceiveDate'] <= $companyfinanceperiod->dateTo)) {
            $customerReceivePayments = $this->customerReceivePaymentRepository->create($input);
            return $this->sendResponse($customerReceivePayments->toArray(), 'Receipt voucher created successfully');
        } else {
            return $this->sendError('Receipt voucher document date should be between financial period start and end date', 500);
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
        }])->findWithoutFail($id);

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

        $input = $this->convertArrayToSelectedValue($input, array('companyFinanceYearID', 'customerID', 'companyFinancePeriodID', 'custTransactionCurrencyID', 'bankID', 'bankAccount', 'bankCurrency', 'confirmedYN', 'expenseClaimOrPettyCash'));

        $input = array_except($input, ['currency', 'finance_year_by', 'finance_period_by', 'localCurrency', 'rptCurrency']);
        $bankcurrencyID = $input['bankCurrency'];
        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);


        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($customerReceivePayment->custTransactionCurrencyID);

        $input['custPaymentReceiveDate'] = ($input['custPaymentReceiveDate'] != '' ? Carbon::parse($input['custPaymentReceiveDate'])->format('Y-m-d') . ' 00:00:00' : NULL);

        $input['custChequeDate'] = ($input['custChequeDate'] != '' ? Carbon::parse($input['custChequeDate'])->format('Y-m-d') . ' 00:00:00' : NULL);

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


        if ($input['documentType'] == 13) {
            /*customer reciept*/
            $detail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)->get();

            if ($input['customerID'] != $customerReceivePayment->customerID) {

                if (count($detail) > 0) {
                    return $this->sendError('Invoice details exist. You can not change the customer.', 500);
                }
                $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();

                /*if customer change*/
                $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
                $input['customerGLCode'] = $customer->custGLaccount;
                $input['customerGLSystemID'] = $customer->custGLAccountSystemID;
                $currency = customercurrency::where('customerCodeSystem', $customer->customerCodeSystem)->where('isDefault', -1)->first();
                if ($currency) {
                    $input['custTransactionCurrencyID'] = $currency->currencyID;
                    $myCurr = $currency->currencyID;

                    $companyCurrency = \Helper::companyCurrency($currency->currencyID);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrencyER'] = 0;
                    $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $customerReceivePayment->companyID)->where('isDefault', -1)->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                        $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $myCurr)->first();
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
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['custTransactionCurrencyER'] = 1;
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['bankID'] = null;
                    $input['bankAccount'] = null;
                    $input['bankCurrency'] = null;
                    $input['bankCurrencyER'] = 0;


                    $bank = BankAssign::select('bankmasterAutoID')->where('companyID', $customerReceivePayment->companyID)->where('isDefault', -1)->first();
                    $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $bank->bankmasterAutoID)->where('isDefault', 1)->where('accountCurrencyID', $myCurr)->first();
                    if ($bank) {
                        $input['bankID'] = $bank->bankmasterAutoID;
                    }
                    if ($bankAccount) {
                        $input['bankAccount'] = $bankAccount->bankAccountAutoID;

                        $input['bankCurrency'] = $myCurr;
                        $input['bankCurrencyER'] = 1;
                    }

                }
            }

            if ($input['bankID'] != $customerReceivePayment->bankID) {
                $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $input['bankID'])->where('isDefault', 1)->where('accountCurrencyID', $input['custTransactionCurrencyID'])->first();
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

        if ($input['documentType'] == 14) {
            /*direct receipt*/
            $detail = DirectReceiptDetail::where('directReceiptAutoID', $id)->get();

            if ($input['bankID'] != $customerReceivePayment->bankID) {
                $bankAccount = BankAccount::where('companyID', $customerReceivePayment->companyID)->where('bankmasterAutoID', $input['bankID'])->where('isDefault', 1)->first();

                $input['custTransactionCurrencyER'] = 0;
                $input['companyRptCurrencyID'] = 0;
                $input['companyRptCurrencyER'] = 0;
                $input['localCurrencyID'] = 0;
                $input['localCurrencyER'] = 0;

                if ($bankAccount) {
                    $input['bankAccount'] = $bankAccount->bankAccountAutoID;
                    $input['bankCurrencyER'] = 1;
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyID'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyER'] = 1;

                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                }
            }

            if ($input['bankAccount'] != $customerReceivePayment->bankAccount) {

                $bankAccount = BankAccount::find($input['bankAccount']);
                if ($bankAccount) {
                    $input['bankCurrencyER'] = 1;
                    $input['bankCurrency'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyID'] = $bankAccount->accountCurrencyID;
                    $input['custTransactionCurrencyER'] = 1;

                    $myCurr = $input['custTransactionCurrencyID'];
                    $companyCurrency = \Helper::companyCurrency($myCurr);
                    $companyCurrencyConversion = \Helper::currencyConversion($customerReceivePayment->companySystemID, $myCurr, $myCurr, 0);
                    /*exchange added*/
                    $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
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

        } else if ($input['documentType'] == 14) {

            $masterHeaderSumTrans = $checkPreDirectSumTrans;
            $masterHeaderSumLocal = $checkPreDirectSumLocal;
            $masterHeaderSumReport = $checkPreDirectSumReport;

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
            } else if ($input['documentType'] == 14) {
                $checkDirectItemsCount = DirectReceiptDetail::where('directReceiptAutoID', $id)
                    ->count();
                if ($checkDirectItemsCount == 0) {
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

            if ($input['documentType'] == 14) {
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
                if ($checkQuantity > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            $policyConfirmedUserToApprove = CompanyPolicyMaster::where('companyPolicyCategoryID', 15)
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if ($input['documentType'] == 14) {
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

                        $chartOfAccount = ChartOfAccountsAssigned::select('controlAccountsSystemID')->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)->first();

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

                    if (round($totalReceiveAmountTrans, $documentCurrencyDecimalPlace) > round($customerInvoiceMaster['bookingAmountTrans'], $documentCurrencyDecimalPlace)) {

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

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);


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

        $employee = \Helper::getEmployeeInfo();

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $customerReceivePayment = $this->customerReceivePaymentRepository->update($input, $id);

        return $this->sendResponse($customerReceivePayment->toArray(), 'Customer Receive Payment updated successfully');
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

    public function getRecieptVoucherFormData(Request $request)
    {
        $input = $request->all();
        /*companySystemID*/
        $companySystemID = $input['companyId'];
        $type = $input['type']; /*value ['filter','create','getCurrency']*/

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
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'), array('value' => 14, 'label' => 'Direct Receipt'));
                break;

            case 'create':

                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID, 1);
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['invoiceType'] = array(array('value' => 13, 'label' => 'Customer Invoice Receipt'), array('value' => 14, 'label' => 'Direct Receipt'));
                break;
            case 'getCurrency':
                $customerID = $input['customerID'];
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                break;

            case 'edit':
                $id = $input['id'];
                $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();
                $output['expenseClaimType'] = ExpenseClaimType::all();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                }
                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->get();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['docType'] = $master->documentType;
                $output['bankDropdown'] = BankAssign::where('isActive', 1)->where('isAssigned', -1)->where('companyID', $output['company']['CompanyID'])->get();

                $output['bankAccount'] = [];
                $output['bankCurrencies'] = [];
                if ($master->bankID != '') {
                    $output['bankAccount'] = BankAccount::where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $master->bankID)->where('isAccountActive', 1)->get();
                }
                if ($master->bankAccount != '') {
                    $output['bankCurrencies'] = DB::table('erp_bankaccount')->join('currencymaster', 'accountCurrencyID', '=', 'currencymaster.currencyID')->where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $master->bankID)->where('bankAccountAutoID', $master->bankAccount)->where('isAccountActive', 1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode')->get();
                }
                break;
            case 'amendEdit':
                $id = $input['id'];
                $master = CustomerReceivePaymentRefferedHistory::where('custReceivePaymentRefferedID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();
                $output['expenseClaimType'] = ExpenseClaimType::all();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                }

                $output['customer'] = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
                    ->where('companySystemID', $companySystemID)
                    ->where('isActive', 1)
                    ->where('isAssigned', -1)
                    ->get();

                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->get();
                $output['currencymaster'] = CurrencyMaster::select('currencyID', 'CurrencyCode')->get();
                $output['docType'] = $master->documentType;
                $output['bankDropdown'] = BankAssign::where('isActive', 1)->where('isAssigned', -1)->where('companyID', $output['company']['CompanyID'])->get();

                $output['bankAccount'] = [];
                $output['bankCurrencies'] = [];
                if ($master->bankID != '') {
                    $output['bankAccount'] = BankAccount::where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $master->bankID)->where('isAccountActive', 1)->get();
                }
                if ($master->bankAccount != '') {
                    $output['bankCurrencies'] = DB::table('erp_bankaccount')->join('currencymaster', 'accountCurrencyID', '=', 'currencymaster.currencyID')->where('companyID', $output['company']['CompanyID'])->where('bankmasterAutoID', $master->bankID)->where('bankAccountAutoID', $master->bankAccount)->where('isAccountActive', 1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode')->get();
                }
                break;
            default:
                $output = [];
        }
        return $this->sendResponse($output, 'Form data');

    }

    public function recieptVoucherDataTable(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year', 'documentType', 'trsClearedYN'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $master = CustomerReceivePayment::where('erp_customerreceivepayment.companySystemID', $input['companyId'])
            ->leftjoin('currencymaster as transCurr', 'custTransactionCurrencyID', '=', 'transCurr.currencyID')
            ->leftjoin('currencymaster as bankCurr', 'bankCurrency', '=', 'bankCurr.currencyID')
            ->leftjoin('employees', 'erp_customerreceivepayment.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_customerreceivepayment.customerID')
            ->leftJoin('erp_bankledger', function ($join) {
                $join->on('erp_bankledger.documentSystemCode', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID');
                $join->on('erp_bankledger.companySystemID', '=', 'erp_customerreceivepayment.companySystemID');
                $join->on('erp_bankledger.documentSystemID', '=', 'erp_customerreceivepayment.documentSystemID');
            })
            ->where('erp_customerreceivepayment.documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $master->where('erp_customerreceivepayment.confirmedYN', $input['confirmedYN']);
            }
        }
        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $master->where('erp_customerreceivepayment.approved', $input['approved']);
            }
        }

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $master->where('erp_customerreceivepayment.cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $master->whereMonth('custPaymentReceiveDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $master->whereYear('custPaymentReceiveDate', '=', $input['year']);
            }
        }
        if (array_key_exists('documentType', $input)) {
            if ($input['documentType'] && !is_null($input['documentType'])) {
                $master->where('documentType', '=', $input['documentType']);
            }
        }
        if (array_key_exists('trsClearedYN', $input)) {
            if ($input['trsClearedYN'] && !is_null($input['trsClearedYN'])) {
                $master->where('erp_bankledger.trsClearedYN', '=', $input['trsClearedYN']);
            }
        }

        $master = $master->select([
            'custPaymentReceiveCode',
            'transCurr.CurrencyCode as transCurrencyCode',
            'bankCurr.CurrencyCode as bankCurrencyCode',
            'documentType',
            'erp_customerreceivepayment.approvedDate',
            'erp_customerreceivepayment.confirmedDate',
            'erp_customerreceivepayment.createdDateTime',
            'custPaymentReceiveDate',
            'erp_customerreceivepayment.narration',
            'empName',
            'transCurr.DecimalPlaces as transDecimal',
            'bankCurr.DecimalPlaces as bankDecimal',
            'erp_customerreceivepayment.refferedBackYN',
            'erp_customerreceivepayment.confirmedYN',
            'erp_customerreceivepayment.approved',
            'erp_customerreceivepayment.cancelYN',
            'custReceivePaymentAutoID',
            'customermaster.CutomerCode',
            'customermaster.CustomerName',
            'receivedAmount as receivedAmount',
            'bankAmount as bankAmount',
            'erp_bankledger.trsClearedYN as trsClearedYN'
        ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $search_without_comma = str_replace(",", "", $search);
            $master = $master->where(function ($query) use ($search, $search_without_comma) {
                $query->Where('custPaymentReceiveCode', 'LIKE', "%{$search}%")
                    ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CutomerCode', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                    ->orWhere('erp_customerreceivepayment.narration', 'LIKE', "%{$search}%")
                    ->orWhere('erp_customerreceivepayment.receivedAmount', 'LIKE', "%{$search_without_comma}%")
                    ->orWhere('erp_customerreceivepayment.bankAmount', 'LIKE', "%{$search_without_comma}%");
            });
        }

        return \DataTables::of($master)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('custReceivePaymentAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getReceiptVoucherMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = CustomerReceivePayment::where('custReceivePaymentAutoID', $input['custReceivePaymentAutoID'])->with(['confirmed_by', 'created_by', 'modified_by', 'cancelled_by', 'company', 'bank', 'currency', 'localCurrency', 'rptCurrency', 'customer', 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 21);
        }, 'directdetails' => function ($query) {
            $query->with('segment');
        }, 'details', 'bankledger_by' => function ($query) {
            $query->with('bankrec_by');
            $query->where('documentSystemID', 21);
        }])->first();

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


        $deleteApproval = DocumentApproved::where('documentSystemCode', $custReceivePaymentAutoID)
            ->where('companySystemID', $custReceivePaymentMaster->companySystemID)
            ->where('documentSystemID', $custReceivePaymentMaster->documentSystemID)
            ->delete();

        return $this->sendResponse($custReceivePaymentMaster->toArray(), 'Supplier Invoice reopened successfully');
    }

    public function printReceiptVoucher(Request $request)
    {

        $id = $request->get('custRecivePayDetAutoID');

        $customerReceivePaymentData = CustomerReceivePayment::find($id);

        if (empty($customerReceivePaymentData)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $customerReceivePaymentRecord = CustomerReceivePayment::where('custReceivePaymentAutoID', $id)->with(['confirmed_by', 'created_by', 'modified_by', 'company', 'bank', 'currency', 'localCurrency', 'rptCurrency', 'customer', 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 21);
        }, 'directdetails' => function ($query) {
            $query->with('segment');
        }, 'details'])->first();

        if (empty($customerReceivePaymentRecord)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $refernaceDoc = \Helper::getCompanyDocRefNo($customerReceivePaymentRecord->companySystemID, $customerReceivePaymentRecord->documentSystemID);

        $transDecimal = 2;
        $localDecimal = 3;
        $rptDecimal = 2;

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

        $ciDetailTotTra = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $id)
            ->sum('receiveAmountTrans');

        $order = array(
            'masterdata' => $customerReceivePaymentRecord,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'localDecimal' => $localDecimal,
            'rptDecimal' => $rptDecimal,
            'directTotTra' => $directTotTra,
            'ciDetailTotTra' => $ciDetailTotTra
        );

        $time = strtotime("now");
        $fileName = 'receipt_voucher_' . $id . '_' . $time . '.pdf';
        $html = view('print.receipt_voucher', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
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
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_customerreceivepayment', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'custReceivePaymentAutoID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_customerreceivepayment.companySystemID', $companyID)
                ->where('erp_customerreceivepayment.approved', 0)
                ->where('erp_customerreceivepayment.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
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

        $storeReceiptVoucherHistory = CustomerReceivePaymentRefferedHistory::insert($receivePaymentArray);

        $customerReceivePaymentDetailRec = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $custReceivePaymentAutoID)->get();

        if (!empty($customerReceivePaymentDetailRec)) {
            foreach ($customerReceivePaymentDetailRec as $bookDetail) {
                $bookDetail['timesReferred'] = $customerReceivePaymentData->timesReferred;
            }
        }

        $customerReceiveDetailArray = $customerReceivePaymentDetailRec->toArray();

        $storeSupplierInvoiceBookDetailHistory = CustReceivePaymentDetRefferedHistory::insert($customerReceiveDetailArray);

        $customerReceivePaymentDirectDetailRec = DirectReceiptDetail::where('directReceiptAutoID', $custReceivePaymentAutoID)->get();

        if (!empty($customerReceivePaymentDirectDetailRec)) {
            foreach ($customerReceivePaymentDirectDetailRec as $bookDirectDetail) {
                $bookDirectDetail['timesReferred'] = $customerReceivePaymentData->timesReferred;
            }
        }

        $ReceivePaymentDirectDetailArray = $customerReceivePaymentDirectDetailRec->toArray();

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
}

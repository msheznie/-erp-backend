<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file contains the all CRUD for Pay Supplier Invoice Master
 * -- REVISION HISTORY
 * -- Date: 03-September 2018 By:Mubashir Description: Added new functions named as getPaymentVoucherFormData(),getAllPaymentVoucherByCompany()
 * -- Date: 14-September 2018 By:Mubashir Description: Added new functions named as getPaymentVoucherMatchItems()
 * -- Date: 12-November 2018 By:Nazir Description: Added new functions named as updateSentToTreasuryDetail()
 * -- Date: 13-November 2018 By:Nazir Description: Added new functions named as printPaymentVoucher()
 * -- Date: 26-December 2018 By:Nazir Description: Added new functions named as amendPaymentVoucherReview()
 * -- Date: 11-February 2019 By:Nazir Description: Added new functions named as amendPaymentVoucherPreCheck()
 */

namespace App\Http\Controllers\API;

use App\helper\CustomValidation;
use App\Models\PaymentVoucherBankChargeDetails;
use App\Services\PaymentVoucherServices;
use ExchangeSetupConfig;
use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Requests\API\CreatePaySupplierInvoiceMasterAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceMasterAPIRequest;
use App\Models\BudgetConsumedData;
use App\Models\EmployeeLedger;
use App\Models\AccountsPayableLedger;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvancePaymentReferback;
use App\Models\BankAccount;
use App\Models\PdcLogPrintedHistory;
use App\Models\BankAssign;
use App\Models\BookInvSuppMaster;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\PdcLog;
use App\Models\BankLedger;
use App\Models\ChartOfAccountsAssigned;
use App\Models\BankMemoPayee;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\ChartOfAccount;
use App\Models\ChequeRegister;
use App\Models\ErpProjectMaster;
use App\Models\ChequeRegisterDetail;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DirectPaymentDetails;
use App\Models\DirectPaymentReferback;
use App\Models\DirectReceiptDetail;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\ExpenseClaimType;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\MonthlyDeclarationsTypes;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceDetailReferback;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PaySupplierInvoiceMasterReferback;
use App\Models\PoAdvancePayment;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Repositories\MatchDocumentMasterRepository;
use App\Repositories\ExpenseAssetAllocationRepository;
use App\Repositories\VatReturnFillingMasterRepository;
use App\Services\ChartOfAccountValidationService;
use App\Traits\AuditTrial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\PaymentType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\SupplierBlock;
use App\Services\ValidateDocumentAmend;
use App\Services\GeneralLedgerService;

/**
 * Class PaySupplierInvoiceMasterController
 * @package App\Http\Controllers\API
 */
class PaySupplierInvoiceMasterAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterRepository */
    private $paySupplierInvoiceMasterRepository;
    private $matchDocumentMasterRepository;
    private $expenseAssetAllocationRepository;
    private $vatReturnFillingMasterRepo;


    public function __construct(PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo, ExpenseAssetAllocationRepository $expenseAssetAllocationRepo, MatchDocumentMasterRepository $matchDocumentMasterRepository,VatReturnFillingMasterRepository $vatReturnFillingMasterRepo)
    {
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepository;
        $this->expenseAssetAllocationRepository = $expenseAssetAllocationRepo;
        $this->vatReturnFillingMasterRepo = $vatReturnFillingMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasters",
     *      summary="Get a listing of the PaySupplierInvoiceMasters.",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Get all PaySupplierInvoiceMasters",
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
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceMaster")
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
        $this->paySupplierInvoiceMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceMasters = $this->paySupplierInvoiceMasterRepository->all();

        return $this->sendResponse($paySupplierInvoiceMasters->toArray(), 'Pay Supplier Invoice Masters retrieved successfully');
    }

    /**
     * @param CreatePaySupplierInvoiceMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceMasters",
     *      summary="Store a newly created PaySupplierInvoiceMaster in storage",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Store PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMaster")
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
     *                  ref="#/definitions/PaySupplierInvoiceMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);

            $conditions = [
                'invoiceType' => 'required',
                'paymentMode' => 'required',
                'supplierTransCurrencyID' => 'required',
                'BPVNarration' => 'required',
                'BPVbank' => 'required',
                'BPVAccount' => 'required',
                'BPVdate' => 'required|date',
            ];

            if (isset($input['pdcChequeYN']) && !$input['pdcChequeYN']) {
                $conditions['BPVchequeDate'] = 'required|date';
            }

            $validator = \Validator::make($request->all(), $conditions);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $resultData = PaymentVoucherServices::createPaymentVoucher($input);
            if($resultData['status']){
                DB::commit();
                return $this->sendResponse($resultData['data'], $resultData['message']);
            }
            else{
                DB::rollBack();
                return $this->sendError($resultData['message'], 500, $resultData['type']);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function paymentVoucherProjectUpdate(Request $request){
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);
            $PayMasterAutoId = $input['PayMasterAutoId'];

            if(!empty($input['projectID'])){
                $projectID = $input['projectID'];
            } else {
                $projectID = null;
            }

            $update_array = [
                'projectID' => $projectID
            ];

            $paymentVoucherProjectUpdate = PaySupplierInvoiceMaster::where('PayMasterAutoId', $PayMasterAutoId)->update($update_array);
            $paymentVoucherProject = PaySupplierInvoiceMaster::where('PayMasterAutoId', $PayMasterAutoId)->first();

            DB::commit();
            return $this->sendResponse($paymentVoucherProject, 'Project updated successfully');
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
     *      path="/paySupplierInvoiceMasters/{id}",
     *      summary="Display the specified PaySupplierInvoiceMaster",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Get PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMaster",
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
     *                  ref="#/definitions/PaySupplierInvoiceMaster"
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
        /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->with(['transactioncurrency', 'confirmed_by', 'bankaccount', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'supplier' => function ($query) {
            $query->selectRaw('CONCAT(primarySupplierCode," | ",supplierName) as supplierName,supplierCodeSystem');
        }, 'suppliercurrency' => function ($query) {
            $query->selectRaw('CONCAT(CurrencyCode," | ",CurrencyName) as CurrencyName,currencyID');
        },'bank','bankaccount.currency','payee','company_to'])->withCount(['approved_by as approvalLevels' => function ($q) {
            $q->where('documentSystemID', 4);
        }])->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $paySupplierInvoiceMaster['supplierTransCurrencyCode'] = CurrencyMaster::where('currencyID',$paySupplierInvoiceMaster['supplierTransCurrencyID'])->first()->CurrencyCode;
        $paySupplierInvoiceMaster['BPVbankCurrencyCode'] = CurrencyMaster::where('currencyID',$paySupplierInvoiceMaster['BPVbankCurrency'])->first()->CurrencyCode;
        $paySupplierInvoiceMaster['companyRptCurrencyCode'] = CurrencyMaster::where('currencyID',$paySupplierInvoiceMaster['companyRptCurrencyID'])->first()->CurrencyCode;
        $paySupplierInvoiceMaster['localCurrencyCode'] = CurrencyMaster::where('currencyID',$paySupplierInvoiceMaster['localCurrencyID'])->first()->CurrencyCode;
        $paySupplierInvoiceMaster['BPVchequeNoID'] = null;

        if($paySupplierInvoiceMaster['payment_mode'] == 2 && $paySupplierInvoiceMaster['pdcChequeYN'] == 0 && !empty($paySupplierInvoiceMaster['BPVchequeNo'])) {
            $chequeRegisterData = ChequeRegister::where('bank_id',$paySupplierInvoiceMaster['BPVbank'])
                ->where('bank_account_id',$paySupplierInvoiceMaster['BPVAccount'])
                ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                ->where('started_cheque_no', '<=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                ->where('ended_cheque_no', '>=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                ->first();

            $checkRegisterDetails = ChequeRegisterDetail::where('cheque_register_master_id',$chequeRegisterData->id)
                ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                ->where('cheque_no',$paySupplierInvoiceMaster['BPVchequeNo'])
                ->first();

            $paySupplierInvoiceMaster['BPVchequeNoID'] = $checkRegisterDetails->id;
        }

        return $this->sendResponse($paySupplierInvoiceMaster->toArray(), 'Pay Supplier Invoice Master retrieved successfully');
    }



    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceMasters/{id}",
     *      summary="Update the specified PaySupplierInvoiceMaster in storage",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Update PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMaster")
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
     *                  ref="#/definitions/PaySupplierInvoiceMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function updateCurrency($id, UpdatePaySupplierInvoiceMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);

            /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

            if (empty($paySupplierInvoiceMaster)) {
                return $this->sendError('Pay Supplier Invoice Master not found');
            }

            $customValidation = CustomValidation::validation(4, $paySupplierInvoiceMaster, 2, $input);
            if (!$customValidation["success"]) {
                return $this->sendError($customValidation["message"], 500, array('type' => 'already_confirmed'));
            }

            $companySystemID = $paySupplierInvoiceMaster->companySystemID;
            $documentSystemID = $paySupplierInvoiceMaster->documentSystemID;
            $input['companySystemID'] = $companySystemID;


            if ($input['payeeType'] == 1) {
                if (isset($input['BPVsupplierID']) && !empty($input['BPVsupplierID'])) {
                    $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $companySystemID)->first();

                    $supCurrency = SupplierCurrency::where('supplierCodeSystem', $input['BPVsupplierID'])->where('isAssigned', -1)->where('isDefault', -1)->first();

                    if ($supDetail) {
                        $input['supplierGLCode'] = $supDetail->liabilityAccount;
                        $input['supplierGLCodeSystemID'] = $supDetail->liabilityAccountSysemID;

                    }
                    $input['supplierTransCurrencyER'] = 1;
                    if ($supCurrency) {
                        $input['supplierDefCurrencyID'] = $supCurrency->currencyID;
                        $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $supCurrency->currencyID, 0);
                        if ($currencyConversionDefaultMaster) {
                            $input['supplierDefCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                        }
                    }
                    $supplier = SupplierMaster::find($input['BPVsupplierID']);
                    $input['directPaymentPayee'] = $supplier->supplierName;
                } else {
                    $input['supplierTransCurrencyER'] = 1;
                    $input['supplierDefCurrencyID'] = $input['supplierTransCurrencyID'];
                    $input['supplierDefCurrencyER'] = 1;
                }
            } else {
                $input['supplierTransCurrencyER'] = 1;
                $input['supplierDefCurrencyID'] = $input['supplierTransCurrencyID'];
                $input['supplierDefCurrencyER'] = 1;
            }


            if ($input['invoiceType'] == 6) {
                $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

                if (is_null($checkEmployeeControlAccount)) {
                    return $this->sendError('Please configure Employee control account for this company', 500);
                }

                $input['supplierGLCodeSystemID'] = $checkEmployeeControlAccount;
                $input['supplierGLCode'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
                $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                $input['directPaymentPayee'] = $emp->empFullName;
            }


            if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {
                if (isset($input['interCompanyToSystemID'])) {
                    if ($input['interCompanyToSystemID']) {
                        $interCompany = Company::find($input['interCompanyToSystemID']);
                        if ($interCompany) {
                            $input['interCompanyToID'] = $interCompany->CompanyID;
                        }
                    } else {
                        $input['interCompanyToSystemID'] = null;
                        $input['interCompanyToID'] = null;
                    }
                } else {
                    $input['interCompanyToSystemID'] = null;
                    $input['interCompanyToID'] = null;
                }
            }
            else {
                $input['interCompanyToSystemID'] = null;
                $input['interCompanyToID'] = null;
            }

            if (!isset($input['expenseClaimOrPettyCash'])) {
                $input['expenseClaimOrPettyCash'] = null;
            }

            $bankAccount = BankAccount::find($input['BPVAccount']);
            if ($bankAccount) {
                $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;
                $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $bankAccount->accountCurrencyID, 0);
                if ($currencyConversionDefaultMaster) {
                    $input['BPVbankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                }
            }else{
                $input['BPVbankCurrency'] = 0;
                $input['BPVbankCurrencyER'] = 0;
            }

            $companyCurrency = \Helper::companyCurrency($companySystemID);
            if ($companyCurrency) {
                $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $companyCurrencyConversion = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $input['supplierTransCurrencyID'], 0);
                if ($companyCurrencyConversion) {
                        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                        $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];

                }
            }


            if ($paySupplierInvoiceMaster->invoiceType == 3) {
                if ($input['payeeType'] == 3) {
                    $input['directPaymentpayeeYN'] = -1;
                    $input['directPaymentPayeeSelectEmp'] = 0;
                    $input['directPaymentPayeeEmpID'] = null;
                    $input['supplierGLCode'] = null;
                    $input['supplierGLCodeSystemID'] = null;
                    $input['supplierDefCurrencyID'] = null;
                    $input['supplierDefCurrencyER'] = null;
                    $input['BPVsupplierID'] = null;
                }
                if ($input['payeeType'] == 2) {
                    $input['directPaymentPayeeSelectEmp'] = -1;
                    $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                    if (!empty($emp)) {
                        $input['directPaymentPayee'] = $emp->empFullName;
                    } else {
                        $input['directPaymentPayee'] = null;
                    }
                    $input['directPaymentpayeeYN'] = 0;
                    $input['supplierGLCode'] = null;
                    $input['supplierGLCodeSystemID'] = null;
                    $input['supplierDefCurrencyID'] = null;
                    $input['supplierDefCurrencyER'] = null;
                    $input['BPVsupplierID'] = null;
                }
                if ($input['payeeType'] == 1) {
                    $input['directPaymentpayeeYN'] = 0;
                    $input['directPaymentPayeeSelectEmp'] = 0;
                    $input['directPaymentPayeeEmpID'] = null;
                }
            }

            $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];

            if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
                $input['BPVchequeDate'] = null;
                $input['BPVchequeNo'] = null;
                $input['expenseClaimOrPettyCash'] = null;
            } else {
                $input['pdcChequeYN'] = 0;
            }

            $warningMessage = '';

            if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {

            } else {
                if (isset($input['pdcChequeYN']) && $input['pdcChequeYN'] == 0 && $input['paymentMode'] == 2) {
                    $warningMessage = "Cheque number won't be generated. The bank currency and the local currency is not equal.";
                }
            }

            $input['BPVdate'] = new Carbon($input['BPVdate']);
            $input['BPVchequeDate'] = new Carbon($input['BPVchequeDate']);
            Log::useFiles(storage_path() . '/logs/pv_cheque_no_jobs.log');
            if ($paySupplierInvoiceMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

                if ($input['pdcChequeYN']) {


                    $pdcLogValidation = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                        ->where('documentmasterAutoID', $id)
                        ->whereNull('chequeDate')
                        ->first();

                    if ($pdcLogValidation) {
                        return $this->sendError('PDC Cheque date cannot be empty', 500);
                    }


                    $totalAmountForPDC = 0;
                    if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                        $totalAmountForPDC = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                            ->sum('supplierPaymentAmount');

                    } else if ($paySupplierInvoiceMaster->invoiceType == 5) {
                        $totalAmountForPDC = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                            ->sum('paymentAmount');
                    } else if ($paySupplierInvoiceMaster->invoiceType == 3) {
                        $totalAmountForPDC = DirectPaymentDetails::where('directPaymentAutoID', $id)->sum('DPAmount');
                    }

                    $pdcLog = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                        ->where('documentmasterAutoID', $id)
                        ->get();

                    if (count($pdcLog) == 0) {
                        return $this->sendError('PDC Cheques not created, Please create atleast one cheque', 500);
                    }

                    $pdcLogAmount = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                        ->where('documentmasterAutoID', $id)
                        ->sum('amount');

                    $checkingAmount = round($totalAmountForPDC, 3) - round($pdcLogAmount, 3);

                    if ($checkingAmount > 0.001 || $checkingAmount < 0) {
                        return $this->sendError('PDC Cheque amount should equal to PV total amount', 500);
                    }

                    $checkPlAccount = SystemGlCodeScenarioDetail::getGlByScenario($companySystemID, $paySupplierInvoiceMaster->documentSystemID, "pdc-payable-account");

                    if (is_null($checkPlAccount)) {
                        return $this->sendError('Please configure PDC Payable account for payment voucher', 500);
                    }
                }

                $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
                if (!$companyFinanceYear["success"]) {
                    return $this->sendError($companyFinanceYear["message"], 500, ['type' => 'confirm']);
                } else {
                    $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                    $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
                }

                $inputParam = $input;
                $inputParam["departmentSystemID"] = 1;
                $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
                if (!$companyFinancePeriod["success"]) {
                    return $this->sendError($companyFinancePeriod["message"], 500, ['type' => 'confirm']);
                } else {
                    $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                    $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
                }

                unset($inputParam);
                $validator = \Validator::make($input, [
                    'companyFinancePeriodID' => 'required|numeric|min:1',
                    'companyFinanceYearID' => 'required|numeric|min:1',
                    'BPVdate' => 'required|date',
                    'BPVchequeDate' => 'required|date',
                    'invoiceType' => 'required|numeric|min:1',
                    'paymentMode' => 'required',
                    'BPVbank' => 'required|numeric|min:1',
                    'BPVAccount' => 'required|numeric|min:1',
                    'supplierTransCurrencyID' => 'required|numeric|min:1',
                    'BPVNarration' => 'required'
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422, ['type' => 'confirm']);
                }

                if(isset($input['payeeType'])){
                    if($input['payeeType'] == 1){
                        $validator = \Validator::make($input, [
                            'BPVsupplierID' => 'required|numeric|min:1'
                        ]);
                    }else if($input['payeeType'] == 2){
                        $validator = \Validator::make($input, [
                            'directPaymentPayeeEmpID' => 'required|numeric|min:1'
                        ]);
                    }else if($input['payeeType'] == 3){
                        $validator = \Validator::make($input, [
                            'directPaymentPayee' => 'required'
                        ]);
                    }
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422, ['type' => 'confirm']);
                }

                $monthBegin = $input['FYPeriodDateFrom'];
                $monthEnd = $input['FYPeriodDateTo'];

                if (($input['BPVdate'] >= $monthBegin) && ($input['BPVdate'] <= $monthEnd)) {
                } else {
                    return $this->sendError('Payment voucher date is not within financial period!', 500, ['type' => 'confirm']);
                }

                $bank = BankAccount::find($input['BPVAccount']);
                if (empty($bank)) {
                    return $this->sendError('Bank account not found', 500, ['type' => 'confirm']);
                }

                if (!$bank->chartOfAccountSystemID) {
                    return $this->sendError('Bank account is not linked to gl account', 500, ['type' => 'confirm']);
                }

                $overPaymentErrorMessage = [];
                // po payment
                if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                    $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $checkAmountGreater = PaySupplierInvoiceDetail::selectRaw('SUM(supplierPaymentAmount) as supplierPaymentAmount')
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (round($checkAmountGreater['supplierPaymentAmount'], 3) < 0) {
                        return $this->sendError('Total Amount should be equal or greater than zero', 500, ['type' => 'confirm']);
                    }

                    $checkAmount = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->where('supplierPaymentAmount', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }


                    $finalError = array(
                        'more_booked' => array(),
                    );

                    $error_count = 0;

                    $pvDetailExist = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->get();

                    foreach ($pvDetailExist as $val) {
                        $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                                                                   ->when(($paySupplierInvoiceMaster->invoiceType == 6 || $paySupplierInvoiceMaster->invoiceType == 7), function($query) {
                                                                        $query->whereHas('payment_master', function($query) {
                                                                            $query->whereIn('invoiceType',[6,7]);
                                                                        });
                                                                    })
                                                                    ->when(($paySupplierInvoiceMaster->invoiceType != 6 && $paySupplierInvoiceMaster->invoiceType != 7), function($query) {
                                                                        $query->whereHas('payment_master', function($query) {
                                                                            $query->where(function($query) {
                                                                                $query->where('invoiceType', '!=', 6)
                                                                                      ->where('invoiceType', '!=', 7);
                                                                            });
                                                                        });
                                                                    })
                                                                    ->where('apAutoID', $val->apAutoID)
                                                                    ->first();

                        $a = $payDetailMoreBooked->supplierPaymentAmount;
                        $b = $val->supplierInvoiceAmount;
                        $epsilon = 0.0001;
                        if ($val->addedDocumentSystemID == 11) {
                            //supplier invoice
                            if (($a-$b) > $epsilon) {
                                array_push($finalError['more_booked'], $val->addedDocumentID . ' | ' . $val->bookingInvDocCode);
                                $error_count++;
                            }
                        } else if ($val->addedDocumentSystemID == 15) {
                            //debit note
                            if (($a-$b) < $epsilon) {
                                array_push($finalError['more_booked'], $val->addedDocumentID . ' | ' . $val->bookingInvDocCode);
                                $error_count++;
                            }
                        }


                    }


                    $poIds = array_unique(collect($pvDetailExist)->pluck('purchaseOrderID')->toArray());

                    foreach ($poIds as $keyPO => $valuePO) {
                        if (!is_null($valuePO)) {
                            $resValidate = $this->paySupplierInvoiceMasterRepository->validatePoPayment($valuePO, $id);

                            if (!$resValidate['status']) {
                                $overPaymentErrorMessage[] = $resValidate['message'];
                            }
                        }
                    }


                    $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                    }

                    foreach ($pvDetailExist as $val) {
                        if ($paySupplierInvoiceMaster->invoiceType == 2) {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID);
                            if ($updatePayment) {

                                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                    ->when(($paySupplierInvoiceMaster->invoiceType == 6 || $paySupplierInvoiceMaster->invoiceType == 7), function($query) {
                                        $query->whereHas('payment_master', function($query) {
                                            $query->whereIn('invoiceType',[6,7]);
                                        });
                                    })
                                    ->when(($paySupplierInvoiceMaster->invoiceType != 6 && $paySupplierInvoiceMaster->invoiceType != 7), function($query) {
                                        $query->whereHas('payment_master', function($query) {
                                            $query->where(function($query) {
                                                $query->where('invoiceType', '!=', 6)
                                                      ->where('invoiceType', '!=', 7);
                                            });
                                        });
                                    })
                                    ->where('apAutoID', $val->apAutoID)->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

                                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $val->bookingInvSystemCode)->where('documentSystemID', $val->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                                $machAmount = 0;
                                if ($matchedAmount) {
                                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                                }

                                $totalPaidAmount = ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));

                                if ($val->addedDocumentSystemID == 11) {
                                    if ($totalPaidAmount == 0) {
                                        $updatePayment->selectedToPaymentInv = 0;
                                        $updatePayment->fullyInvoice = 0;
                                        $updatePayment->save();
                                    } else if ($val->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $val->supplierInvoiceAmount) {
                                        $updatePayment->selectedToPaymentInv = -1;
                                        $updatePayment->fullyInvoice = 2;
                                        $updatePayment->save();
                                    } else if (($val->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                                        $updatePayment->selectedToPaymentInv = 0;
                                        $updatePayment->fullyInvoice = 1;
                                        $updatePayment->save();
                                    }
                                } else if ($val->addedDocumentSystemID == 15 || $val->addedDocumentSystemID == 24) {

                                    if ($totalPaidAmount == 0) {
                                        $updatePayment->selectedToPaymentInv = 0;
                                        $updatePayment->fullyInvoice = 0;
                                        $updatePayment->save();
                                    } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                                        $updatePayment->selectedToPaymentInv = -1;
                                        $updatePayment->fullyInvoice = 2;
                                        $updatePayment->save();
                                    } else if ($val->supplierInvoiceAmount < $totalPaidAmount) {
                                        $updatePayment->selectedToPaymentInv = 0;
                                        $updatePayment->fullyInvoice = 1;
                                        $updatePayment->save();
                                    } else if ($val->supplierInvoiceAmount > $totalPaidAmount) {
                                        $updatePayment->selectedToPaymentInv = -1;
                                        $updatePayment->fullyInvoice = 2;
                                        $updatePayment->save();
                                    }
                                }
                            }
                        }
                    }
                }

                // Advance payment
                if ($paySupplierInvoiceMaster->invoiceType == 5) {
                    $pvDetailExist = AdvancePaymentDetails::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $checkAmountGreater = AdvancePaymentDetails::selectRaw('PayMasterAutoId,SUM(paymentAmount) as supplierPaymentAmount')
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (round($checkAmountGreater['paymentAmount'], 3) < 0) {
                        return $this->sendError('Total Amount should be equal or greater than zero', 500, ['type' => 'confirm']);
                    }

                    $checkAmount = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                        ->where('paymentAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }

                    $advancePaymentDetails = AdvancePaymentDetails::where('PayMasterAutoId', $id)->get();
                    foreach ($advancePaymentDetails as $val) {
                        $advancePayment = PoAdvancePayment::find($val->poAdvPaymentID);

                        $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                            ->where('companySystemID', $advancePayment->companySystemID)
                            ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                            ->where('purchaseOrderID', $advancePayment->poID)
                            ->first();

                        if (($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) || $advancePayment->reqAmount < $advancePaymentDetailsSum->SumOfpaymentAmount) {
                            $advancePayment->selectedToPayment = -1;
                            $advancePayment->fullyPaid = 2;
                            $advancePayment->save();
                        } else {
                            $advancePayment->selectedToPayment = 0;
                            $advancePayment->fullyPaid = 1;
                            $advancePayment->save();
                        }

                        $resValidate = $this->paySupplierInvoiceMasterRepository->validatePoPayment($val->purchaseOrderID, $id);

                        if (!$resValidate['status']) {
                            $overPaymentErrorMessage[] = $resValidate['message'];
                        }
                    }

                }

                if (count($overPaymentErrorMessage) > 0) {
                    $confirmErrorOverPay = array('type' => 'confirm_error_over_payment', 'data' => $overPaymentErrorMessage);
                    return $this->sendError("You cannot confirm this document.", 500, $confirmErrorOverPay);
                }

                // Direct payment
                if ($paySupplierInvoiceMaster->invoiceType == 3) {
                    $pvDetailExist = DirectPaymentDetails::where('directPaymentAutoID', $id)->get();

                    if (count($pvDetailExist) == 0) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $finalError = array(
                        'required_serviceLine' => array(),
                        'active_serviceLine' => array(),
                        'bank_not_updated' => array(),
                        'bank_account_not_updated' => array(),
                        'bank_account_currency_not_updated' => array(),
                        'bank_account_currency_er_not_updated' => array(),
                        'bank_amount_not_updated' => array(),
                        'bank_account_gl__account_not_updated' => array(),
                        'bank_account_local_currency_not_updated' => array(),
                        'bank_account_local_currency_er_not_updated' => array(),
                        'bank_account_local_currency_amount_not_updated' => array(),
                        'bank_account_reporting_currency_not_updated' => array(),
                        'bank_account_reporting_currency_er_not_updated' => array(),
                        'bank_account_reporting_currency_amount_not_updated' => array(),
                        'inter_company_gl_code_not_created' => array(),
                        'from_comany_not_configured_in_to_company' => array(),
                        'monthly_deduction_not_updated' => [],
                    );

                    $error_count = 0;

                    foreach ($pvDetailExist as $item) {
                        if ($item->serviceLineSystemID && !is_null($item->serviceLineSystemID)) {
                            $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $item->serviceLineSystemID)
                                ->where('isActive', 1)
                                ->first();
                            if (empty($checkDepartmentActive)) {
                                $item->serviceLineSystemID = null;
                                $item->serviceLineCode = null;
                                array_push($finalError['active_serviceLine'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                        } else {
                            array_push($finalError['required_serviceLine'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }

                        if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {

                            $toRelatedAccounts = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) use ($paySupplierInvoiceMaster){
                                $q->where('isApproved', 1)
                                    ->where('interCompanySystemID', $paySupplierInvoiceMaster->companySystemID);
                            })
                                ->where('isAssigned', -1)
                                ->where('companySystemID', $paySupplierInvoiceMaster->interCompanyToSystemID)
                                ->where('controllAccountYN', 0)
                                ->where('controlAccountsSystemID', '<>', 1)
                                ->where('isActive', 1)
                                ->first();

                            $fromCompanyData = Company::find($paySupplierInvoiceMaster->companySystemID);
                            $toCompanyData = Company::find($paySupplierInvoiceMaster->interCompanyToSystemID);

                            $fromCompanyName = isset($fromCompanyData->CompanyName) ? $fromCompanyData->CompanyName : "";
                            $toCompanyName = isset($toCompanyData->CompanyName) ? $toCompanyData->CompanyName : "";

                            if (!$toRelatedAccounts) {
                                array_push($finalError['from_comany_not_configured_in_to_company'], $fromCompanyName . ' to ' . $toCompanyName);
                                $error_count++;
                            }

                            if (!$item->toBankID) {
                                array_push($finalError['bank_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankAccountID) {
                                array_push($finalError['bank_account_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankCurrencyID) {
                                array_push($finalError['bank_account_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankCurrencyER) {
                                array_push($finalError['bank_account_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankAmount) {
                                array_push($finalError['bank_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankGlCodeSystemID) {
                                array_push($finalError['bank_account_gl__account_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyLocalCurrencyID) {
                                array_push($finalError['bank_account_local_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyLocalCurrencyER) {
                                array_push($finalError['bank_account_local_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyLocalCurrencyAmount) {
                                array_push($finalError['bank_account_local_currency_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyRptCurrencyID) {
                                array_push($finalError['bank_account_reporting_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyRptCurrencyER) {
                                array_push($finalError['bank_account_reporting_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyRptCurrencyAmount) {
                                array_push($finalError['bank_account_reporting_currency_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }

                            $chartofAccount = ChartOfAccount::where('interCompanySystemID', $paySupplierInvoiceMaster->companySystemID)->get();
                            if (count($chartofAccount) == 0) {
                                array_push($finalError['inter_company_gl_code_not_created'], $item->glCode . ' | ' . $item->glCodeDes);
                            }

                        }

                        if($paySupplierInvoiceMaster->createMonthlyDeduction){
                            if (empty($item->deductionType)) {
                                $finalError['monthly_deduction_not_updated'][] = $item->glCode . ' | ' . $item->glCodeDes;
                                $error_count++;
                            }
                        }
                    }
                    $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                    }


                    $checkAmount = DirectPaymentDetails::where('directPaymentAutoID', $id)
                        ->where('DPAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }

                }

                $params = array('autoID' => $id, 'company' => $companySystemID, 'document' => $documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
                }

                $paySupplierInvoice = PaySupplierInvoiceMaster::find($id);
                if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {
                    if ($input['chequePaymentYN'] == -1) {
                        $bankAccount = BankAccount::find($input['BPVAccount']);
                        /*
                         * check 'Get cheque number from cheque register' policy exist
                         * if policy exist - cheque no should get from erp_cheque register details - Get cheque number from cheque register
                         * else - usual method
                         *
                         * */
                        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $companySystemID)
                            ->where('companyPolicyCategoryID', 35)
                            ->where('isYesNO', 1)
                            ->first();
                        if (!empty($is_exist_policy_GCNFCR)) {

                            $usedCheckID = $this->paySupplierInvoiceMasterRepository->getLastUsedChequeID($companySystemID, $bankAccount->bankAccountAutoID);

                            $unUsedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use ($companySystemID, $bankAccount) {
                                $q->where('bank_account_id', $bankAccount->bankAccountAutoID)
                                    ->where('company_id', $companySystemID)
                                    ->where('isActive', 1);
                            })
                                ->where('status', 0)
                                ->where(function ($q) use ($usedCheckID) {
                                    if ($usedCheckID) {
                                        $q->where('id', '>', $usedCheckID);
                                    }
                                })
                                ->orderBy('id', 'ASC')
                                ->first();

                            if (!empty($unUsedCheque)) {
                                $nextChequeNo = $unUsedCheque->cheque_no;
                                $input['BPVchequeNo'] = $nextChequeNo;
                                /*update cheque detail table */
                                $update_array = [
                                    'document_id' => $id,
                                    'document_master_id' => $documentSystemID,
                                    'status' => 1,
                                ];
                                ChequeRegisterDetail::where('id', $unUsedCheque->id)->update($update_array);

                            } else {
                                return $this->sendError('Could not found any unassigned cheques. Please add cheques to cheque registry', 500);
                            }

                        } else {
                            $nextChequeNo = $bankAccount->chquePrintedStartingNo + 1;
                        }
                        /*code ended here*/

                        $checkChequeNoDuplicate = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('BPVbank', $input['BPVbank'])->where('BPVAccount', $input['BPVAccount'])->where('BPVchequeNo', $nextChequeNo)->first();

                        if ($checkChequeNoDuplicate) {
                            //return $this->sendError('The cheque no ' . $nextChequeNo . ' is already taken in ' . $checkChequeNoDuplicate['BPVcode'] . ' Please check again.', 500, ['type' => 'confirm']);
                        }

                        if ($bankAccount->isPrintedActive == 1 && empty($is_exist_policy_GCNFCR)) {
                            $input['BPVchequeNo'] = $nextChequeNo;
                            $bankAccount->chquePrintedStartingNo = $nextChequeNo;
                            $bankAccount->save();

                            Log::info('Cheque No:' . $input['BPVchequeNo']);
                            Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
                            Log::info('-------------------------------------------------------');
                        }
                    } else {
                        $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
                        if ($chkCheque) {
                            $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                        } else {
                            $input['BPVchequeNo'] = 1;
                        }
                    }
                } else {
                    $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
                    if ($chkCheque) {
                        $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                    } else {
                        $input['BPVchequeNo'] = 1;
                    }
                }

                if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
                    $input['chequePaymentYN'] = 0;
                    $input['BPVchequeDate'] = null;
                    $input['BPVchequeNo'] = null;
                    $input['expenseClaimOrPettyCash'] = null;
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                $totalAmount = PaySupplierInvoiceDetail::selectRaw("SUM(supplierInvoiceAmount) as supplierInvoiceAmount,SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierPaymentAmount) as supplierPaymentAmount, SUM(paymentBalancedAmount) as paymentBalancedAmount, SUM(paymentSupplierDefaultAmount) as paymentSupplierDefaultAmount, SUM(paymentLocalAmount) as paymentLocalAmount, SUM(paymentComRptAmount) as paymentComRptAmount")
                    ->where('PayMasterAutoId', $id)
                    ->where('matchingDocID', 0)
                    ->first();

                if (!empty($totalAmount->supplierPaymentAmount)) {
                    if ($paySupplierInvoiceMaster->BPVbankCurrency == $paySupplierInvoiceMaster->supplierTransCurrencyID) {
                        $input['payAmountBank'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                        $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                        $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                        $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->paymentLocalAmount);
                        $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->paymentComRptAmount);
                        $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                    } else {
                        $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierPaymentAmount);
                        $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
                        $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                        $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                        $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                        $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                        $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                    }
                } else {
                    $input['payAmountBank'] = 0;
                    $input['payAmountSuppTrans'] = 0;
                    $input['payAmountSuppDef'] = 0;
                    $input['payAmountCompLocal'] = 0;
                    $input['payAmountCompRpt'] = 0;
                    $input['suppAmountDocTotal'] = 0;
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 5) {


                $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $companySystemID)->first();

                if($supDetail)
                {
                    $input['AdvanceAccount'] = $supDetail->AdvanceAccount;
                    $input['advanceAccountSystemID'] = $supDetail->advanceAccountSystemID;
                }


                $totalAmount = AdvancePaymentDetails::selectRaw("SUM(paymentAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(supplierTransAmount) as supplierTransAmount")->where('PayMasterAutoId', $id)->first();

                if (!empty($totalAmount->supplierTransAmount)) {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierTransAmount);
                    $input['payAmountBank'] = $bankAmount["defaultAmount"];
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierTransAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierDefaultAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                    $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierTransAmount);
                } else {
                    $input['payAmountBank'] = 0;
                    $input['payAmountSuppTrans'] = 0;
                    $input['payAmountSuppDef'] = 0;
                    $input['payAmountCompLocal'] = 0;
                    $input['payAmountCompRpt'] = 0;
                    $input['suppAmountDocTotal'] = 0;
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 3) {
                $totalAmount = DirectPaymentDetails::selectRaw("SUM(DPAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount")->where('directPaymentAutoID', $id)->first();

                if (!empty($totalAmount->paymentAmount)) {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->paymentAmount);
                    $input['payAmountBank'] = $bankAmount["defaultAmount"];
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->paymentAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->paymentAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                    $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->paymentAmount);
                } else {
                    $input['payAmountBank'] = 0;
                    $input['payAmountSuppTrans'] = 0;
                    $input['payAmountSuppDef'] = 0;
                    $input['payAmountCompLocal'] = 0;
                    $input['payAmountCompRpt'] = 0;
                    $input['suppAmountDocTotal'] = 0;
                }
            }

            $input['createMonthlyDeduction'] = ($input['createMonthlyDeduction'] == 1)? 1: 0;
            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

            Log::info('Cheque No:' . $input['BPVchequeNo']);
            Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
            Log::info('beforeUpdate______________________________________________________');


            if(isset($input['BPVAccount']))
            {
                if(!empty($input['BPVAccount']) )
                {
                    $bank_currency = $input['BPVbankCurrency'];
                    $document_currency = $input['supplierTransCurrencyID'];

                    $cur_det['companySystemID'] = $input['companySystemID'];
                    $cur_det['bankmasterAutoID'] = $input['BPVbank'];
                    $cur_det['bankAccountAutoID'] = $input['BPVAccount'];
                    $cur_det_info =  (object)$cur_det;

                    $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);

                    $amount = $bankBalance['netBankBalance'];
                    $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();

                    $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');


                    $input['bankAccountBalance'] = $rounded_amount;

                }
            }

            $input['payment_mode'] = $input['paymentMode'];
            unset($input['paymentMode']);
            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $id);

            Log::info('Cheque No:' . $input['BPVchequeNo']);
            Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
            Log::info($paySupplierInvoiceMaster);
            Log::info('afterUpdate______________________________________________________');

            if ($input['payeeType'] == 1) {
                $bankMemoSupplier = BankMemoPayee::where('documentSystemCode', $id)
                    ->delete();
            }

            $message = [['status' => 'success', 'message' => 'PaySupplierInvoiceMaster updated successfully'], ['status' => 'warning', 'message' => $warningMessage]];
            DB::commit();
            return $this->sendReponseWithDetails($paySupplierInvoiceMaster->toArray(), $message,1,$confirm['data'] ?? null);
        } catch
        (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function getRetentionValues(Request $request){
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        try{
            $BPVdate = new Carbon($input['BPVdate']);
        }
        catch (\Exception $e){
            return $this->sendError('Invalid Pay Invoice Date format');
        }
        $details = PaySupplierInvoiceDetail::where('PayMasterAutoId', $input['PayMasterAutoId'])->where('isRetention', 1)->where('supplierPaymentAmount', '!=', 0)->get();
        if($details) {
            $bookinvDetailsArray = [];
            $details = collect($details)->pluck('bookingInvSystemCode');
            $bookinvDetails = BookInvSuppMaster::whereIn('bookingSuppMasInvAutoID', $details)->get();
            foreach ($bookinvDetails as $key => $objects){
                if($BPVdate < $objects->retentionDueDate) {
                    $bookinvDetailsArray[$key]['bookingInvCode'] = $objects->bookingInvCode;
                    $bookinvDetailsArray[$key]['retentionDueDate'] = $objects->retentionDueDate;
                    $bookinvDetailsArray[$key]['retentionAmount'] = $objects->retentionAmount;
                }
            }

            return $bookinvDetailsArray;
        }
    }


    public function update($id, UpdatePaySupplierInvoiceMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = $this->convertArrayToValue($input);

            /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

            if (empty($paySupplierInvoiceMaster)) {
                return $this->sendError('Pay Supplier Invoice Master not found');
            }

            $customValidation = CustomValidation::validation(4, $paySupplierInvoiceMaster, 2, $input);
            if (!$customValidation["success"]) {
                return $this->sendError($customValidation["message"], 500, array('type' => 'already_confirmed'));
            }

            $supplier_id = $input['BPVsupplierID'];
            $supplierMaster = SupplierMaster::where('supplierCodeSystem',$supplier_id)->first();

            $companySystemID = $paySupplierInvoiceMaster->companySystemID;
            $documentSystemID = $paySupplierInvoiceMaster->documentSystemID;
            $input['companySystemID'] = $companySystemID;

         
            if ($input['payeeType'] == 1) {
                if (isset($input['BPVsupplierID']) && !empty($input['BPVsupplierID'])) {
                    $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $companySystemID)->first();

                    $supCurrency = SupplierCurrency::where('supplierCodeSystem', $input['BPVsupplierID'])->where('isAssigned', -1)->where('isDefault', -1)->first();
                    $input['directPaymentPayeeEmpID'] = 0;
                    if ($supDetail) {
                        $input['supplierGLCode'] = $supDetail->liabilityAccount;
                        $input['supplierGLCodeSystemID'] = $supDetail->liabilityAccountSysemID;

                    }
                    $input['supplierTransCurrencyER'] = 1;
                    if ($supCurrency) {
                        $input['supplierDefCurrencyID'] = $supCurrency->currencyID;
                        $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $supCurrency->currencyID, 0);
                        if ($currencyConversionDefaultMaster) {
                            $input['supplierDefCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                        }
                    }
                    $supplier = SupplierMaster::find($input['BPVsupplierID']);
                    $input['directPaymentPayee'] = $supplier->supplierName;
                } else {
                    $input['supplierTransCurrencyER'] = 1;
                    $input['supplierDefCurrencyID'] = $input['supplierTransCurrencyID'];
                    $input['supplierDefCurrencyER'] = 1;
                }
            } else {
                $input['supplierTransCurrencyER'] = 1;
                $input['supplierDefCurrencyID'] = $input['supplierTransCurrencyID'];
                $input['supplierDefCurrencyER'] = 1;
            }

            if ($input['invoiceType'] == 6 || $input['invoiceType'] == 7) {
                $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

                if (is_null($checkEmployeeControlAccount)) {
                    return $this->sendError('Please configure Employee control account for this company', 500);
                }


                $input['BPVsupplierID'] = 0;
                $input['supplierGLCodeSystemID'] = $checkEmployeeControlAccount;
                $input['supplierGLCode'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
                $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                if(isset($emp) && $emp != null)
                {
                    $input['directPaymentPayee'] = $emp->empFullName;
                }
                
            }

            if ($input['invoiceType'] == 7) {
                $isEmpAdvConfigured = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-advance-account");

                if (is_null($isEmpAdvConfigured)) {
                    return $this->sendError('Please configure employee advance account for this company', 500, array('type' => 'create'));
                }

                $input['employeeAdvanceAccount'] = ChartOfAccount::getAccountCode($isEmpAdvConfigured);
                $input['employeeAdvanceAccountSystemID'] = $isEmpAdvConfigured;
            } else {
                $input['employeeAdvanceAccount'] = null;
                $input['employeeAdvanceAccountSystemID'] = null;
            }
            

            if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {
                if (isset($input['interCompanyToSystemID'])) {
                    if ($input['interCompanyToSystemID']) {
                        $interCompany = Company::find($input['interCompanyToSystemID']);
                        if ($interCompany) {
                            $input['interCompanyToID'] = $interCompany->CompanyID;
                        }
                    } else {
                        $input['interCompanyToSystemID'] = null;
                        $input['interCompanyToID'] = null;
                    }
                } else {
                    $input['interCompanyToSystemID'] = null;
                    $input['interCompanyToID'] = null;
                }
            }
            else {
                $input['interCompanyToSystemID'] = null;
                $input['interCompanyToID'] = null;
            }

            if (!isset($input['expenseClaimOrPettyCash'])) {
                $input['expenseClaimOrPettyCash'] = null;
            }

            $bankAccount = BankAccount::find($input['BPVAccount']);
            if ($bankAccount) {
                $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;
                $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $bankAccount->accountCurrencyID, 0);
                if (!isset($paySupplierInvoiceMaster->BPVbankCurrencyER)) {
                    if($currencyConversionDefaultMaster){
                        $input['BPVbankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                    } else {
                        $input['BPVbankCurrencyER'] = 0;
                    }
                }else {
                    $input['BPVbankCurrencyER'] = $paySupplierInvoiceMaster->BPVbankCurrencyER;
                }

            }else{
                $input['BPVbankCurrency'] = 0;
                $input['BPVbankCurrencyER'] = 0;
            }

            $companyCurrency = \Helper::companyCurrency($companySystemID);
            if ($companyCurrency) {
                $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $companyCurrencyConversion = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $input['supplierTransCurrencyID'], 0);
                if ($companyCurrencyConversion) {
                    $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
                        ->where('companyPolicyCategoryID', 67)
                        ->where('isYesNO', 1)
                        ->first();
                    $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

                    // if($policy == false || $paySupplierInvoiceMaster->invoiceType != 3) {
                        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                        $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                    // }
                }
            }


            $checkErChange = isset($input['checkErChange']) ? $input['checkErChange'] : true;

            if ((($paySupplierInvoiceMaster->BPVbankCurrencyER != $input['BPVbankCurrencyER'] && $input['BPVbankCurrency'] == $paySupplierInvoiceMaster->BPVbankCurrency) || $paySupplierInvoiceMaster->localCurrencyER != $input['localCurrencyER'] && $input['localCurrencyID'] == $paySupplierInvoiceMaster->localCurrencyID || $paySupplierInvoiceMaster->companyRptCurrencyER != $input['companyRptCurrencyER'] && $input['companyRptCurrencyID'] == $paySupplierInvoiceMaster->companyRptCurrencyID)) {

                if ($checkErChange && $input['confirmedYN'] == 1) {
                    if(($input['BPVbankCurrencyEROld'] != $paySupplierInvoiceMaster->BPVbankCurrencyER) || ($input['localCurrencyEROld'] != $paySupplierInvoiceMaster->localCurrencyER) || ($input['companyRptCurrencyEROld'] != $paySupplierInvoiceMaster->companyRptCurrencyER))
                    {
                        $erMessage = "<p>The exchange rates are updated as follows,</p><p style='font-size: medium;'>Previous rates Bank ER ".$input['BPVbankCurrencyEROld']." | Local ER ".$input['localCurrencyEROld']." | Reporting ER ".$input['companyRptCurrencyEROld']."</p><p style='font-size: medium;'>Current rates Bank ER ".$paySupplierInvoiceMaster->BPVbankCurrencyER." | Local ER ".$paySupplierInvoiceMaster->localCurrencyER." | Reporting ER ".$paySupplierInvoiceMaster->companyRptCurrencyER."</p><p>Are you sure you want to proceed ?</p>";
                    }else {
                        $erMessage = "<p>The exchange rates are updated as follows,</p><p style='font-size: medium;'>Previous rates Bank ER ".$paySupplierInvoiceMaster->BPVbankCurrencyER." | Local ER ".$paySupplierInvoiceMaster->localCurrencyER." | Reporting ER ".$paySupplierInvoiceMaster->companyRptCurrencyER."</p><p style='font-size: medium;'>Current rates Bank ER ".$input['BPVbankCurrencyER']." | Local ER ".$input['localCurrencyER']." | Reporting ER ".$input['companyRptCurrencyER']."</p><p>Are you sure you want to proceed ?</p>";
                    }

                    return $this->sendError($erMessage, 500, ['type' => 'erChange']);
                } else {
                    unset($input['localCurrencyER']);
                    unset($input['companyRptCurrencyER']);
                    //PaySupplierInvoiceMaster::where('PayMasterAutoId', $paySupplierInvoiceMaster->PayMasterAutoId)->update(['BPVbankCurrencyER' => $input['BPVbankCurrencyER'], 'localCurrencyER' => $input['localCurrencyER'], 'companyRptCurrencyER' => $input['companyRptCurrencyER']]);
                }
            }
      
            if ($paySupplierInvoiceMaster->invoiceType == 3) {
                if ($input['payeeType'] == 3) {
                    $input['directPaymentpayeeYN'] = -1;
                    $input['directPaymentPayeeSelectEmp'] = 0;
                    $input['directPaymentPayeeEmpID'] = null;
                    $input['supplierGLCode'] = null;
                    $input['supplierGLCodeSystemID'] = null;
                    $input['supplierDefCurrencyID'] = null;
                    $input['supplierDefCurrencyER'] = null;
                    $input['BPVsupplierID'] = null;
                }
                if ($input['payeeType'] == 2) {
                    $input['directPaymentPayeeSelectEmp'] = -1;
                    $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                    if (!empty($emp)) {
                        $input['directPaymentPayee'] = $emp->empFullName;
                    } else {
                        $input['directPaymentPayee'] = null;
                    }
                    $input['directPaymentpayeeYN'] = 0;
                    $input['supplierGLCode'] = null;
                    $input['supplierGLCodeSystemID'] = null;
                    $input['supplierDefCurrencyID'] = null;
                    $input['supplierDefCurrencyER'] = null;
                    $input['BPVsupplierID'] = null;
                }
                if ($input['payeeType'] == 1) {
                    $input['directPaymentpayeeYN'] = 0;
                    $input['directPaymentPayeeSelectEmp'] = 0;
                    $input['directPaymentPayeeEmpID'] = null;
                }
            }
            
            $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];

            if (isset($input['chequePaymentYN'])) {
                if ($input['chequePaymentYN'] && $input['paymentMode'] == 2) {
                    $input['chequePaymentYN'] = -1;
                } else {
                    $input['chequePaymentYN'] = 0;
                }
            } else {
                $input['chequePaymentYN'] = 0;
            }

            if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
                $input['BPVchequeDate'] = null;
                $input['BPVchequeNo'] = null;
                $input['expenseClaimOrPettyCash'] = null;

                if(!is_null($paySupplierInvoiceMaster->BPVchequeNo) && ($paySupplierInvoiceMaster->BPVchequeNo != 0)) {
                    ChequeRegisterDetail::where('document_id', $input['PayMasterAutoId'])
                        ->where('document_master_id', $input['documentSystemID'])
                        ->where('company_id', $companySystemID)
                        ->where('cheque_no', $paySupplierInvoiceMaster->BPVchequeNo)
                        ->update(['status' => 0, 'document_master_id' => null, 'document_id' => null]);
                }

            } else {
                $input['pdcChequeYN'] = 0;
            }

            if (isset($input['pdcChequeYN']) && $input['pdcChequeYN'] == false) {

                $isPdcLog = PdcLog::where('documentSystemID', $input['documentSystemID'])
                    ->where('documentmasterAutoID', $input['PayMasterAutoId'])
                    ->first();

                if(!empty($isPdcLog)) {
                    ChequeRegisterDetail::where('document_id', $input['PayMasterAutoId'])->where('document_master_id', $input['documentSystemID'])->update(['status' => 0, 'document_master_id' => null, 'document_id' => null]);

                    PdcLog::where('documentSystemID', $input['documentSystemID'])
                        ->where('documentmasterAutoID', $input['PayMasterAutoId'])
                        ->delete();
                }

            }


            $warningMessage = '';

            if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {

            } else {
                if (isset($input['pdcChequeYN']) && $input['pdcChequeYN'] == 0 && $input['paymentMode'] == 2) {
                    $warningMessage = "Cheque number won't be generated. The bank currency and the local currency is not equal.";
                }
            }
            
            $input['BPVdate'] = new Carbon($input['BPVdate']);
            $input['BPVchequeDate'] = new Carbon($input['BPVchequeDate']);
            Log::useFiles(storage_path() . '/logs/pv_cheque_no_jobs.log');

            $changeChequeNoBaseOnPolicy = false;
            $is_exist_policy_GCNFCR = Helper::checkPolicy($companySystemID, 35);

            if($input['paymentMode'] == 2 && !$input['pdcChequeYN'] && $is_exist_policy_GCNFCR) {
                $checkRegisterDetails = ChequeRegisterDetail::where('id',$input['BPVchequeNoDropdown'])
                    ->where('company_id',$companySystemID)
                    ->first();

                if($checkRegisterDetails) {
                    $input['BPVchequeNo'] = $checkRegisterDetails->cheque_no;
                    $changeChequeNoBaseOnPolicy = true;

                    /*update cheque detail table */
                    $checkRegisterDetails->document_id = $id;
                    $checkRegisterDetails->document_master_id = $documentSystemID;
                    $checkRegisterDetails->status = 1;
                    $checkRegisterDetails->save();

                    if((!is_null($paySupplierInvoiceMaster->BPVchequeNo) && $paySupplierInvoiceMaster->BPVchequeNo != 0) && ($paySupplierInvoiceMaster->BPVchequeNo != $checkRegisterDetails->cheque_no)) {
                        $chequeRegisterData = ChequeRegister::where('bank_id',$paySupplierInvoiceMaster['BPVbank'])
                            ->where('bank_account_id',$paySupplierInvoiceMaster['BPVAccount'])
                            ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                            ->where('started_cheque_no', '<=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                            ->where('ended_cheque_no', '>=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                            ->first();

                        $checkRegisterDetails = ChequeRegisterDetail::where('cheque_register_master_id',$chequeRegisterData->id)
                            ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                            ->where('cheque_no',$paySupplierInvoiceMaster['BPVchequeNo'])
                            ->first();

                        $checkRegisterDetails->document_id = null;
                        $checkRegisterDetails->document_master_id = null;
                        $checkRegisterDetails->status = 0;
                        $checkRegisterDetails->save();
                    }
                }
                unset($checkRegisterDetails);
            }

            if ($paySupplierInvoiceMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

                // checking minus value
                if ($input['invoiceType'] == 2) {

                    $checkBankChargeTotal = PaymentVoucherBankChargeDetails::where('payMasterAutoID', $input['PayMasterAutoId'])->sum('dpAmount');

                    $checkInvoiceDetailTotal = PaySupplierInvoiceDetail::where('PayMasterAutoId', $input['PayMasterAutoId'])->sum('supplierPaymentAmount');

                    $netMinustot = $checkBankChargeTotal + $checkInvoiceDetailTotal;

                    if ($netMinustot < 0) {
                        return $this->sendError('Net amount cannot be negative value', 500);
                    }

                    $checkQuantity = PaymentVoucherBankChargeDetails::where('payMasterAutoID', $input['PayMasterAutoId'])
                        ->where(function ($q) {
                            $q->where('dpAmount', '=', 0)
                                ->orWhere('localAmount', '=', 0)
                                ->orWhere('comRptAmount', '=', 0)
                                ->orWhereNull('dpAmount')
                                ->orWhereNull('localAmount')
                                ->orWhereNull('comRptAmount');
                        })->count();

                    if ($checkQuantity > 0) {
                        return $this->sendError('Amount should be have value', 500);
                    }

                    $pvBankChargeDetail = PaymentVoucherBankChargeDetails::where('payMasterAutoID', $input['PayMasterAutoId'])->get();

                    $finalError = array(
                        'amount_zero' => array(),
                        'amount_neg' => array(),
                        'required_serviceLine' => array(),
                        'active_serviceLine' => array()
                    );

                    $error_count = 0;

                    foreach ($pvBankChargeDetail as $item) {

                        $updateItem = PaymentVoucherBankChargeDetails::find($item['id']);

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
                
                if(($input['isSupplierBlocked']) && ($paySupplierInvoiceMaster->invoiceType == 2))
                {

                    $validatorResult = \Helper::checkBlockSuppliers($input['BPVdate'],$supplier_id);
                    if (!$validatorResult['success']) {              
                        return $this->sendError('The selected supplier has been blocked. Are you sure you want to proceed ?', 500,['type' => 'blockSupplier']);
        
                    }
                }

                if ($input['pdcChequeYN']) {
                    

                    $pdcLogValidation = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                                          ->where('documentmasterAutoID', $id)
                                          ->whereNull('chequeDate')
                                          ->first();

                    if ($pdcLogValidation) {
                        return $this->sendError('PDC Cheque date cannot be empty', 500); 
                    }


                    $totalAmountForPDC = 0;
                    if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                        $totalAmountForPDCData = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                                                                        ->selectRaw('SUM(supplierPaymentAmount + retentionVatAmount) as total')
                                                                        ->first();

                        $totalAmountForPDC = $totalAmountForPDCData ? $totalAmountForPDCData->total : 0;

                    } else if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {
                        $totalAmountForPDC = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                                                                    ->sum('paymentAmount');

                    } else if ($paySupplierInvoiceMaster->invoiceType == 3) {
                        $totalAmountForPDCData = DirectPaymentDetails::where('directPaymentAutoID', $id)
                                                                        ->selectRaw('SUM(DPAmount + vatAmount) as total')
                                                                        ->first();
                                                                        
                        $totalAmountForPDC = $totalAmountForPDCData ? $totalAmountForPDCData->total : 0;
                    }

                    $pdcLog = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                                          ->where('documentmasterAutoID', $id)
                                          ->get();

                    if (count($pdcLog) == 0) {
                        return $this->sendError('PDC Cheques not created, Please create atleast one cheque', 500);
                    } 

                    $pdcLogAmount = PdcLog::where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                                          ->where('documentmasterAutoID', $id)
                                          ->sum('amount');

                    $checkingAmount = round($totalAmountForPDC, 3) - round($pdcLogAmount, 3);

                    if ($checkingAmount > 0.001 || $checkingAmount < 0) {
                        return $this->sendError('PDC Cheque amount should equal to PV total amount', 500); 
                    }

                    $checkPlAccount = SystemGlCodeScenarioDetail::getGlByScenario($companySystemID, $paySupplierInvoiceMaster->documentSystemID, "pdc-payable-account");

                    if (is_null($checkPlAccount)) {
                        return $this->sendError('Please configure PDC Payable account for payment voucher', 500);
                    } 
                }

                if ($input['invoiceType'] == 2 || $input['invoiceType'] == 6) {
                    $bankCharge = PaymentVoucherBankChargeDetails::selectRaw("SUM(dpAmount) as dpAmount, SUM(localAmount) as localAmount,SUM(comRptAmount) as comRptAmount")->WHERE('payMasterAutoID', $paySupplierInvoiceMaster->PayMasterAutoId)->first();
                    $si = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $paySupplierInvoiceMaster->PayMasterAutoId)->WHERE('matchingDocID', 0)->first();

                    $masterTransAmountTotal = $si->transAmount + $bankCharge->dpAmount;
                    $masterLocalAmountTotal = $si->localAmount + $bankCharge->localAmount;
                    $masterRptAmountTotal = $si->rptAmount + $bankCharge->comRptAmount;

                    $convertAmount = \Helper::convertAmountToLocalRpt(203, $paySupplierInvoiceMaster->PayMasterAutoId, $masterTransAmountTotal);

                    $transAmountTotal = $masterTransAmountTotal;
                    $localAmountTotal = $convertAmount["localAmount"];
                    $rptAmountTotal = $convertAmount["reportingAmount"];

                    $diffTrans = $transAmountTotal - $masterTransAmountTotal;
                    $diffLocal = $localAmountTotal - $masterLocalAmountTotal;
                    $diffRpt = $rptAmountTotal - $masterRptAmountTotal;


                    $masterData = PaySupplierInvoiceMaster::with(['localcurrency', 'rptcurrency'])->find($paySupplierInvoiceMaster->PayMasterAutoId);

                    if (ABS(round($diffTrans)) != 0 || ABS(round($diffLocal, $masterData->localcurrency->DecimalPlaces)) != 0 || ABS(round($diffRpt, $masterData->rptcurrency->DecimalPlaces)) != 0) {

                        $checkExchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($companySystemID, $documentSystemID, "exchange-gainloss-gl");
                        if (is_null($checkExchangeGainLossAccount)) {
                            $checkExchangeGainLossAccountCode = SystemGlCodeScenarioDetail::getGlCodeByScenario($companySystemID, $documentSystemID, "exchange-gainloss-gl");

                            if ($checkExchangeGainLossAccountCode) {
                                return $this->sendError('Please assign Exchange Gain/Loss account for this company', 500);
                            }
                            return $this->sendError('Please configure Exchange Gain/Loss account for this company', 500);
                        }
                    }
                }



                $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
                if (!$companyFinanceYear["success"]) {
                    return $this->sendError($companyFinanceYear["message"], 500, ['type' => 'confirm']);
                } else {
                    $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                    $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
                }

                $inputParam = $input;
                $inputParam["departmentSystemID"] = 1;
                $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
                if (!$companyFinancePeriod["success"]) {
                    return $this->sendError($companyFinancePeriod["message"], 500, ['type' => 'confirm']);
                } else {
                    $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                    $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
                }
                
                unset($inputParam);
                $validator = \Validator::make($input, [
                    'companyFinancePeriodID' => 'required|numeric|min:1',
                    'companyFinanceYearID' => 'required|numeric|min:1',
                    'BPVdate' => 'required|date',
                    'BPVchequeDate' => 'required|date',
                    'invoiceType' => 'required|numeric|min:1',
                    'paymentMode' => 'required',
                    'BPVbank' => 'required|numeric|min:1',
                    'BPVAccount' => 'required|numeric|min:1',
                    'supplierTransCurrencyID' => 'required|numeric|min:1',
                    'BPVNarration' => 'required'
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422, ['type' => 'confirm']);
                }

                if(isset($input['payeeType'])){
                    if($input['payeeType'] == 1 && $input['invoiceType'] != 6 && $input['invoiceType'] != 7){
                        $validator = \Validator::make($input, [
                            'BPVsupplierID' => 'required|numeric|min:1'
                        ]);
                    }else if($input['payeeType'] == 2){
                        $validator = \Validator::make($input, [
                            'directPaymentPayeeEmpID' => 'required|numeric|min:1'
                        ]);
                    }else if($input['payeeType'] == 3){
                        $validator = \Validator::make($input, [
                            'directPaymentPayee' => 'required'
                        ]);
                    }
                }

                if ($input['invoiceType'] == 6 || $input['invoiceType'] == 7) {
                    $validator = \Validator::make($input, [
                        'directPaymentPayeeEmpID' => 'required|numeric|min:1'
                    ]);
                }
                
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422, ['type' => 'confirm']);
                }

                $monthBegin = $input['FYPeriodDateFrom'];
                $monthEnd = $input['FYPeriodDateTo'];

                if (($input['BPVdate'] >= $monthBegin) && ($input['BPVdate'] <= $monthEnd)) {
                } else {
                    return $this->sendError('Payment voucher date is not within financial period!', 500, ['type' => 'confirm']);
                }

                $bank = BankAccount::find($input['BPVAccount']);
                if (empty($bank)) {
                    return $this->sendError('Bank account not found', 500, ['type' => 'confirm']);
                }

                if (!$bank->chartOfAccountSystemID) {
                    return $this->sendError('Bank account is not linked to gl account', 500, ['type' => 'confirm']);
                }

              
                $overPaymentErrorMessage = [];
                // po payment
                if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                    $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }
                    
                    $checkAmountGreater = PaySupplierInvoiceDetail::selectRaw('SUM(supplierPaymentAmount) as supplierPaymentAmount')
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (round($checkAmountGreater['supplierPaymentAmount'], 3) < 0) {
                        return $this->sendError('Total Amount should be equal or greater than zero', 500, ['type' => 'confirm']);
                    }

                    $checkAmount = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->where('supplierPaymentAmount', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }


                    $finalError = array(
                        'more_booked' => array(),
                    );

                    $error_count = 0;

                    $pvDetailExist = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->get();

                    foreach ($pvDetailExist as $val) {
                        $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                            ->when(($paySupplierInvoiceMaster->invoiceType == 6 || $paySupplierInvoiceMaster->invoiceType == 7), function($query) {
                                $query->whereHas('payment_master', function($query) {
                                    $query->whereIn('invoiceType',[6,7]);
                                });
                            })
                            ->when(($paySupplierInvoiceMaster->invoiceType != 6 && $paySupplierInvoiceMaster->invoiceType != 7), function($query) {
                                $query->whereHas('payment_master', function($query) {
                                    $query->where(function($query) {
                                        $query->where('invoiceType', '!=', 6)
                                              ->where('invoiceType', '!=', 7);
                                    });
                                });
                            })
                            ->where('apAutoID', $val->apAutoID)
                            ->where('matchingDocID', 0)
                            ->first();

                        $a = ($val->addedDocumentSystemID == 11) ? $payDetailMoreBooked->supplierPaymentAmount : abs($payDetailMoreBooked->supplierPaymentAmount);
                        $b = ($val->addedDocumentSystemID == 11) ? $val->supplierInvoiceAmount : abs($val->supplierInvoiceAmount);
                        $epsilon = 0.0001;
                        //supplier invoice
                        if (($a-$b) > $epsilon) {
                            array_push($finalError['more_booked'], $val->addedDocumentID . ' | ' . $val->bookingInvDocCode);
                            $error_count++;
                        }

                        
                    }


                    $poIds = array_unique(collect($pvDetailExist)->pluck('purchaseOrderID')->toArray());

                    foreach ($poIds as $keyPO => $valuePO) {
                        if (!is_null($valuePO)) {
                            $resValidate = $this->paySupplierInvoiceMasterRepository->validatePoPayment($valuePO, $id);

                            if (!$resValidate['status']) {
                                $overPaymentErrorMessage[] = $resValidate['message'];
                            }
                        }
                    }


                    $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                    }

                    foreach ($pvDetailExist as $val) {
                        if ($paySupplierInvoiceMaster->invoiceType == 6) {
                            $updatePayment = EmployeeLedger::find($val->apAutoID);
                        } else {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID);
                        }
                        if ($updatePayment) {

                            $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                                                            ->when(($paySupplierInvoiceMaster->invoiceType == 6 || $paySupplierInvoiceMaster->invoiceType == 7), function($query) {
                                                                                $query->whereHas('payment_master', function($query) {
                                                                                    $query->whereIn('invoiceType',[6,7]);
                                                                                });
                                                                            })
                                                                            ->when(($paySupplierInvoiceMaster->invoiceType != 6 && $paySupplierInvoiceMaster->invoiceType != 7), function($query) {
                                                                                $query->whereHas('payment_master', function($query) {
                                                                                    $query->where(function($query) {
                                                                                        $query->where('invoiceType', '!=', 6)
                                                                                              ->where('invoiceType', '!=', 7);
                                                                                    });
                                                                                });
                                                                            })
                                                                            ->where('apAutoID', $val->apAutoID)
                                                                            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

                            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $val->bookingInvSystemCode)->where('documentSystemID', $val->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                            $machAmount = 0;
                            if ($matchedAmount) {
                                $machAmount = $matchedAmount["SumOfmatchedAmount"];
                            }

                            $totalPaidAmount = ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1));

                            if ($val->addedDocumentSystemID == 11) {
                                if ($totalPaidAmount == 0) {
                                    $updatePayment->selectedToPaymentInv = 0;
                                    $updatePayment->fullyInvoice = 0;
                                    $updatePayment->save();
                                } else if ($val->supplierInvoiceAmount == $totalPaidAmount || $totalPaidAmount > $val->supplierInvoiceAmount) {
                                    $updatePayment->selectedToPaymentInv = -1;
                                    $updatePayment->fullyInvoice = 2;
                                    $updatePayment->save();
                                } else if (($val->supplierInvoiceAmount > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                                    $updatePayment->selectedToPaymentInv = 0;
                                    $updatePayment->fullyInvoice = 1;
                                    $updatePayment->save();
                                }
                            } else if ($val->addedDocumentSystemID == 15 || $val->addedDocumentSystemID == 24) {

                                if ($totalPaidAmount == 0) {
                                    $updatePayment->selectedToPaymentInv = 0;
                                    $updatePayment->fullyInvoice = 0;
                                    $updatePayment->save();
                                } else if ($val->supplierInvoiceAmount == $totalPaidAmount) {
                                    $updatePayment->selectedToPaymentInv = -1;
                                    $updatePayment->fullyInvoice = 2;
                                    $updatePayment->save();
                                } else if ($val->supplierInvoiceAmount < $totalPaidAmount) {
                                    $updatePayment->selectedToPaymentInv = 0;
                                    $updatePayment->fullyInvoice = 1;
                                    $updatePayment->save();
                                } else if ($val->supplierInvoiceAmount > $totalPaidAmount) {
                                    $updatePayment->selectedToPaymentInv = -1;
                                    $updatePayment->fullyInvoice = 2;
                                    $updatePayment->save();
                                }
                            }
                        }
                    }
                }

                // Advance payment
                if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {
                    $pvDetailExist = AdvancePaymentDetails::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $checkAmountGreater = AdvancePaymentDetails::selectRaw('PayMasterAutoId,SUM(paymentAmount) as supplierPaymentAmount')
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (round($checkAmountGreater['paymentAmount'], 3) < 0) {
                        return $this->sendError('Total Amount should be equal or greater than zero', 500, ['type' => 'confirm']);
                    }

                    $checkAmount = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                        ->where('paymentAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }


                    $checkAdvVATAmount = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                                                               ->sum('VATAmount');

                    if ($paySupplierInvoiceMaster->invoiceType == 5 && $paySupplierInvoiceMaster->applyVAT == 1 && $checkAdvVATAmount > 0) {
                        if(empty(TaxService::getInputVATTransferGLAccount($paySupplierInvoiceMaster->companySystemID))){
                            return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not configured.', 500);
                        }

                        $inputVATTransferGL = TaxService::getInputVATTransferGLAccount($paySupplierInvoiceMaster->companySystemID);

                        $checkAssignedStatusInputTrans = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATTransferGL->inputVatTransferGLAccountAutoID, $paySupplierInvoiceMaster->companySystemID);

                        if (!$checkAssignedStatusInputTrans) {
                            return $this->sendError('Cannot confirm. Input VAT Transfer GL Account not assigned to company.', 500);
                        }

                        if(empty(TaxService::getInputVATGLAccount($paySupplierInvoiceMaster->companySystemID))){
                            return $this->sendError('Cannot confirm. Input VAT GL Account not configured.', 500);
                        }

                        $inputVATGL = TaxService::getInputVATGLAccount($paySupplierInvoiceMaster->companySystemID);

                        $checkAssignedStatus = ChartOfAccountsAssigned::checkCOAAssignedStatus($inputVATGL->inputVatTransferGLAccountAutoID, $paySupplierInvoiceMaster->companySystemID);

                        if (!$checkAssignedStatus) {
                            return $this->sendError('Cannot confirm. Input VAT GL Account not assigned to company.', 500);
                        }
                    }

                    $advancePaymentDetails = AdvancePaymentDetails::where('PayMasterAutoId', $id)->get();
                    foreach ($advancePaymentDetails as $val) {
                        $advancePayment = PoAdvancePayment::find($val->poAdvPaymentID);

                        if(isset($advancePayment))
                        {
                            $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                            ->where('companySystemID', $advancePayment->companySystemID)
                            ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                            ->where('purchaseOrderID', $advancePayment->poID)
                            ->first();

                            if (($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) || $advancePayment->reqAmount < $advancePaymentDetailsSum->SumOfpaymentAmount) {
                                $advancePayment->selectedToPayment = -1;
                                $advancePayment->fullyPaid = 2;
                                $advancePayment->save();
                            } else {
                                $advancePayment->selectedToPayment = 0;
                                $advancePayment->fullyPaid = 1;
                                $advancePayment->save();
                            }

                        }

                  
                        $resValidate = $this->paySupplierInvoiceMasterRepository->validatePoPayment($val->purchaseOrderID, $id);

                        if (!$resValidate['status']) {
                            $overPaymentErrorMessage[] = $resValidate['message'];
                        }
                    }

                }
             
                if (count($overPaymentErrorMessage) > 0) {
                    $confirmErrorOverPay = array('type' => 'confirm_error_over_payment', 'data' => $overPaymentErrorMessage);
                    return $this->sendError("You cannot confirm this document.", 500, $confirmErrorOverPay);
                }

                // Direct payment
                if ($paySupplierInvoiceMaster->invoiceType == 3) {
                    $pvDetailExist = DirectPaymentDetails::where('directPaymentAutoID', $id)->get();

                    if (count($pvDetailExist) == 0) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $finalError = array(
                        'required_serviceLine' => array(),
                        'active_serviceLine' => array(),
                        'bank_not_updated' => array(),
                        'bank_account_not_updated' => array(),
                        'bank_account_currency_not_updated' => array(),
                        'bank_account_currency_er_not_updated' => array(),
                        'bank_amount_not_updated' => array(),
                        'bank_account_gl__account_not_updated' => array(),
                        'bank_account_local_currency_not_updated' => array(),
                        'bank_account_local_currency_er_not_updated' => array(),
                        'bank_account_local_currency_amount_not_updated' => array(),
                        'bank_account_reporting_currency_not_updated' => array(),
                        'bank_account_reporting_currency_er_not_updated' => array(),
                        'bank_account_reporting_currency_amount_not_updated' => array(),
                        'inter_company_gl_code_not_created' => array(),
                        'from_comany_not_configured_in_to_company' => array(),
                        'monthly_deduction_not_updated' => [],
                    );

                    $error_count = 0;

                    DirectPaymentDetails::where('directPaymentAutoID', $id)->update(['bankCurrencyER' => $input['BPVbankCurrencyER']]);

                    $employeeInvoice = CompanyPolicyMaster::where('companyPolicyCategoryID', 68)
                                    ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                                    ->first();

                    $employeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($paySupplierInvoiceMaster->companySystemID, null, "employee-control-account");

                    $companyData = Company::find($paySupplierInvoiceMaster->companySystemID);

                    if ($employeeInvoice && $employeeInvoice->isYesNO == 1 && $companyData && $companyData->isHrmsIntergrated && ($employeeControlAccount > 0)) {
                        $employeeControlRelatedAc = DirectPaymentDetails::where('directPaymentAutoID', $id)
                                                                       ->where('chartOfAccountSystemID', $employeeControlAccount)
                                                                       ->get();


                        foreach ($employeeControlRelatedAc as $key => $value) {
                            $detailTotalOfLine = $value->DPAmount;

                            $allocatedSum = ExpenseEmployeeAllocation::where('documentDetailID', $value->directPaymentDetailsID)
                                                                              ->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                                                                              ->sum('amount');

                            if ($allocatedSum != $detailTotalOfLine) {
                                return $this->sendError("Please allocate the full amount of ".$value->glCode." - ".$value->glCodeDes, 500);
                            }
                        }
                    }

                    foreach ($pvDetailExist as $item) {
                        if ($item->serviceLineSystemID && !is_null($item->serviceLineSystemID)) {
                            $checkDepartmentActive = SegmentMaster::where('serviceLineSystemID', $item->serviceLineSystemID)
                                ->where('isActive', 1)
                                ->first();
                            if (empty($checkDepartmentActive)) {
                                $item->serviceLineSystemID = null;
                                $item->serviceLineCode = null;
                                array_push($finalError['active_serviceLine'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                        } else {
                            array_push($finalError['required_serviceLine'], $item->glCode . ' | ' . $item->glCodeDes);
                            $error_count++;
                        }

                        if ($paySupplierInvoiceMaster->expenseClaimOrPettyCash == 6 || $paySupplierInvoiceMaster->expenseClaimOrPettyCash == 7) {

                            $toRelatedAccounts = ChartOfAccountsAssigned::whereHas('chartofaccount', function ($q) use ($paySupplierInvoiceMaster){
                                                                            $q->where('isApproved', 1)
                                                                              ->where('interCompanySystemID', $paySupplierInvoiceMaster->companySystemID);
                                                                        })
                                                                        ->where('isAssigned', -1)
                                                                        ->where('companySystemID', $paySupplierInvoiceMaster->interCompanyToSystemID)
                                                                        ->where('controllAccountYN', 0)
                                                                        ->where('controlAccountsSystemID', '<>', 1)
                                                                        ->where('isActive', 1)
                                                                        ->first();

                            $fromCompanyData = Company::find($paySupplierInvoiceMaster->companySystemID);
                            $toCompanyData = Company::find($paySupplierInvoiceMaster->interCompanyToSystemID);

                            $fromCompanyName = isset($fromCompanyData->CompanyName) ? $fromCompanyData->CompanyName : "";
                            $toCompanyName = isset($toCompanyData->CompanyName) ? $toCompanyData->CompanyName : "";

                            if (!$toRelatedAccounts) {
                                array_push($finalError['from_comany_not_configured_in_to_company'], $fromCompanyName . ' to ' . $toCompanyName);
                                $error_count++;
                            }

                            if (!$item->toBankID) {
                                array_push($finalError['bank_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankAccountID) {
                                array_push($finalError['bank_account_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankCurrencyID) {
                                array_push($finalError['bank_account_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankCurrencyER) {
                                array_push($finalError['bank_account_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankAmount) {
                                array_push($finalError['bank_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toBankGlCodeSystemID) {
                                array_push($finalError['bank_account_gl__account_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyLocalCurrencyID) {
                                array_push($finalError['bank_account_local_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyLocalCurrencyER) {
                                array_push($finalError['bank_account_local_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyLocalCurrencyAmount) {
                                array_push($finalError['bank_account_local_currency_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyRptCurrencyID) {
                                array_push($finalError['bank_account_reporting_currency_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyRptCurrencyER) {
                                array_push($finalError['bank_account_reporting_currency_er_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }
                            if (!$item->toCompanyRptCurrencyAmount) {
                                array_push($finalError['bank_account_reporting_currency_amount_not_updated'], $item->glCode . ' | ' . $item->glCodeDes);
                                $error_count++;
                            }

                            $chartofAccount = ChartOfAccount::where('interCompanySystemID', $paySupplierInvoiceMaster->companySystemID)->get();
                            if (count($chartofAccount) == 0) {
                                array_push($finalError['inter_company_gl_code_not_created'], $item->glCode . ' | ' . $item->glCodeDes);
                            }

                        }

                        if($paySupplierInvoiceMaster->createMonthlyDeduction){
                            if (empty($item->deductionType)) {
                                $finalError['monthly_deduction_not_updated'][] = $item->glCode . ' | ' . $item->glCodeDes;
                                $error_count++;
                            }
                        }
                    }
                    $confirm_error = array('type' => 'confirm_error', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError("You cannot confirm this document.", 500, $confirm_error);
                    }


                    $checkAmount = DirectPaymentDetails::where('directPaymentAutoID', $id)
                        ->where('DPAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }

                    $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                                    ->WHERE('documentSystemCode', $id)
                                    ->WHERE('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)
                                    ->groupBy('documentSystemCode')
                                    ->first();

                    $isVATEligible = TaxService::checkCompanyVATEligible($paySupplierInvoiceMaster->companySystemID);

                    if ($isVATEligible == 1) {
                        if($tax){
                            $taxInputVATControl = TaxService::getInputVATGLAccount($paySupplierInvoiceMaster->companySystemID);

                            if (!$taxInputVATControl) {
                                return $this->sendError('Input VAT GL Account is not configured for this company', 500, ['type' => 'confirm']);
                            }

                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxInputVATControl->inputVatGLAccountAutoID)
                                ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                                ->where('isAssigned', -1)
                                ->first();

                            if (!$chartOfAccountData) {
                                return $this->sendError('Input VAT GL Account is not assigned to this company', 500, ['type' => 'confirm']);
                            }

                            if($paySupplierInvoiceMaster->rcmActivated == 1) {
                                $taxOutputVATControl = TaxService::getOutputVATGLAccount($paySupplierInvoiceMaster->companySystemID);

                                if (!$taxOutputVATControl) {
                                    return $this->sendError('Output VAT GL Account is not configured for this company', 500, ['type' => 'confirm']);
                                }

                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATControl->outputVatGLAccountAutoID)
                                    ->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)
                                    ->where('isAssigned', -1)
                                    ->first();

                                if (!$chartOfAccountData) {
                                    return $this->sendError('Output VAT GL Account is not assigned to this company', 500, ['type' => 'confirm']);
                                }
                            } 
                        }
                    }

                }


                $amountForApproval = 0;
                if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                    $bankCharge = PaymentVoucherBankChargeDetails::where('payMasterAutoID',$id)->selectRaw('SUM(localAmount) as total')->first();
                    $totalAmountForApprovalData = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                                                                    ->selectRaw('SUM(paymentLocalAmount) as total, SUM(retentionVatAmount) as retentionVatAmount, supplierTransCurrencyID, localCurrencyID')
                                                                    ->first();

                    if ($totalAmountForApprovalData) {
                        $currencyConversionRetAmount = \Helper::currencyConversion($paySupplierInvoiceMaster->companySystemID, $totalAmountForApprovalData->supplierTransCurrencyID, $totalAmountForApprovalData->supplierTransCurrencyID, $totalAmountForApprovalData->retentionVatAmount);

                        $retLocal = $currencyConversionRetAmount['localAmount'];
                        

                        $amountForApproval = $totalAmountForApprovalData->total + $bankCharge->total + $retLocal;
                    }


                } else if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {
                    $amountForApproval = AdvancePaymentDetails::where('PayMasterAutoId', $id)
                                                                ->sum('localAmount');

                } else if ($paySupplierInvoiceMaster->invoiceType == 3) {
                    $totalAmountForApprovalData = DirectPaymentDetails::where('directPaymentAutoID', $id)
                                                                    ->selectRaw('SUM(localAmount + VATAmountLocal) as total')
                                                                    ->first();
                                                                    
                    $amountForApproval = $totalAmountForApprovalData ? $totalAmountForApprovalData->total : 0;
                }
                if ($paySupplierInvoiceMaster->invoiceType == 3) {
                    $object = new ChartOfAccountValidationService();
                    $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);

                    if (isset($result) && !empty($result["accountCodes"])) {
                        return $this->sendError($result["errorMsg"],500, ['type' => 'confirm']);
                    }
                }
                $params = array('autoID' => $id, 'company' => $companySystemID, 'document' => $documentSystemID, 'segment' => '', 'category' => '', 'amount' => $amountForApproval);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500, ['type' => 'confirm']);
                }

                $paySupplierInvoice = PaySupplierInvoiceMaster::find($id);
                if(!$changeChequeNoBaseOnPolicy) {
                    if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {
                        if ($input['chequePaymentYN'] == -1 &&  $input['pdcChequeYN'] == 0) {
                            $bankAccount = BankAccount::find($input['BPVAccount']);
                            /*
                             * check 'Get cheque number from cheque register' policy exist
                             * if policy exist - cheque no should get from erp_cheque register details - Get cheque number from cheque register
                             * else - usual method
                             *
                             * */
                            $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $companySystemID)
                                ->where('companyPolicyCategoryID', 35)
                                ->where('isYesNO', 1)
                                ->first();
                            if (!empty($is_exist_policy_GCNFCR)) {

                                $usedCheckID = $this->paySupplierInvoiceMasterRepository->getLastUsedChequeID($companySystemID, $bankAccount->bankAccountAutoID);

                                $unUsedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use ($companySystemID, $bankAccount) {
                                    $q->where('bank_account_id', $bankAccount->bankAccountAutoID)
                                        ->where('company_id', $companySystemID)
                                        ->where('isActive', 1);
                                })
                                    ->where('status', 0)
                                    ->where(function ($q) use ($usedCheckID) {
                                        if ($usedCheckID) {
                                            $q->where('id', '>', $usedCheckID);
                                        }
                                    })
                                    ->orderBy('id', 'ASC')
                                    ->first();

                                if (!empty($unUsedCheque)) {
                                    $nextChequeNo = $unUsedCheque->cheque_no;
                                    $input['BPVchequeNo'] = $nextChequeNo;
                                    /*update cheque detail table */
                                    $update_array = [
                                        'document_id' => $id,
                                        'document_master_id' => $documentSystemID,
                                        'status' => 1,
                                    ];
                                    ChequeRegisterDetail::where('id', $unUsedCheque->id)->update($update_array);

                                } else {
                                    return $this->sendError('Could not found any unassigned cheques. Please add cheques to cheque registry', 500, ['type' => 'confirm']);
                                }

                            } else {
                                $nextChequeNo = $bankAccount->chquePrintedStartingNo + 1;
                            }
                            /*code ended here*/

                            $checkChequeNoDuplicate = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('BPVbank', $input['BPVbank'])->where('BPVAccount', $input['BPVAccount'])->where('BPVchequeNo', $nextChequeNo)->first();

                            if ($checkChequeNoDuplicate) {
                                //return $this->sendError('The cheque no ' . $nextChequeNo . ' is already taken in ' . $checkChequeNoDuplicate['BPVcode'] . ' Please check again.', 500, ['type' => 'confirm']);
                            }

                            if ($bankAccount->isPrintedActive == 1 && empty($is_exist_policy_GCNFCR)) {
                                $input['BPVchequeNo'] = $nextChequeNo;
                                $bankAccount->chquePrintedStartingNo = $nextChequeNo;
                                $bankAccount->save();

                                Log::info('Cheque No:' . $input['BPVchequeNo']);
                                Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
                                Log::info('-------------------------------------------------------');
                            }
                        } else {
                            $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
                            if ($chkCheque) {
                                $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                            } else {
                                $input['BPVchequeNo'] = 1;
                            }
                        }
                    } else {
                        $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('BPVchequeNo', 'DESC')->first();
                        if ($chkCheque) {
                            $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                        } else {
                            $input['BPVchequeNo'] = 1;
                        }
                    }
                }

                if (isset($input['pdcChequeYN']) && $input['pdcChequeYN']) {
                    $input['chequePaymentYN'] = 0;
                    $input['BPVchequeDate'] = null;
                    $input['BPVchequeNo'] = null;
                    $input['expenseClaimOrPettyCash'] = null;
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 2 || $paySupplierInvoiceMaster->invoiceType == 6) {
                $bankChargeTotal = PaymentVoucherBankChargeDetails::selectRaw("SUM(dpAmount) as dpAmount, SUM(localAmount) as localAmount,SUM(comRptAmount) as comRptAmount")->WHERE('payMasterAutoID', $id)->first();

                $totalAmount = PaySupplierInvoiceDetail::selectRaw("SUM(supplierInvoiceAmount) as supplierInvoiceAmount,SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(retentionVatAmount) as retentionVatAmount, SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierPaymentAmount) as supplierPaymentAmount, SUM(paymentBalancedAmount) as paymentBalancedAmount, SUM(paymentSupplierDefaultAmount) as paymentSupplierDefaultAmount, SUM(paymentLocalAmount) as paymentLocalAmount, SUM(paymentComRptAmount) as paymentComRptAmount")
                    ->where('PayMasterAutoId', $id)
                    ->where('matchingDocID', 0)
                    ->first();
                $supplierPaymentAmount = $totalAmount->supplierPaymentAmount + $bankChargeTotal->dpAmount;
                if (!empty($supplierPaymentAmount)) {
                    if ($paySupplierInvoiceMaster->BPVbankCurrency == $paySupplierInvoiceMaster->supplierTransCurrencyID) {
                        $input['payAmountBank'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['payAmountSuppTrans'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['payAmountSuppDef'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->paymentLocalAmount + $bankChargeTotal->localAmount);
                        $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->paymentComRptAmount + $bankChargeTotal->comRptAmount);
                        $input['suppAmountDocTotal'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['retentionVatAmount'] = \Helper::roundValue($totalAmount->retentionVatAmount);
                    } else {
                        $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $supplierPaymentAmount);
                        $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
                        $input['payAmountSuppTrans'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['payAmountSuppDef'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                        $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                        $input['suppAmountDocTotal'] = \Helper::roundValue($supplierPaymentAmount);
                        $input['retentionVatAmount'] = \Helper::roundValue($totalAmount->retentionVatAmount);

                    }
                    $exchangeAmount =\Helper::convertAmountToLocalRpt(203, $id, $supplierPaymentAmount);
                    $input['payAmountBank'] = $exchangeAmount["defaultAmount"];
                    $input['payAmountCompLocal'] = \Helper::roundValue($exchangeAmount["localAmount"]);
                    $input['payAmountCompRpt'] = \Helper::roundValue($exchangeAmount["reportingAmount"]);
                } else {
                    $input['payAmountBank'] = 0;
                    $input['payAmountSuppTrans'] = 0;
                    $input['payAmountSuppDef'] = 0;
                    $input['payAmountCompLocal'] = 0;
                    $input['payAmountCompRpt'] = 0;
                    $input['suppAmountDocTotal'] = 0;
                    $input['retentionVatAmount'] = 0;
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 5 || $paySupplierInvoiceMaster->invoiceType == 7) {


                if ($paySupplierInvoiceMaster->invoiceType == 5) {
                    $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $companySystemID)->first();

                    if($supDetail)
                    {
                        $input['AdvanceAccount'] = $supDetail->AdvanceAccount;
                        $input['advanceAccountSystemID'] = $supDetail->advanceAccountSystemID;
                    }
                } else {
                    $checkEmployeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-control-account");

                    if (is_null($checkEmployeeControlAccount)) {
                        return $this->sendError('Please configure Employee control account for this company', 500);
                    }

                    $input['AdvanceAccount'] = ChartOfAccount::getAccountCode($checkEmployeeControlAccount);
                    $input['advanceAccountSystemID'] = $checkEmployeeControlAccount;
                }

                $totalAmount = AdvancePaymentDetails::selectRaw("SUM(paymentAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(supplierTransAmount) as supplierTransAmount")->where('PayMasterAutoId', $id)->first();

                if (!empty($totalAmount->supplierTransAmount)) {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierTransAmount);
                    $input['payAmountBank'] = $bankAmount["defaultAmount"];
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierTransAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierDefaultAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                    $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierTransAmount);
                } else {
                    $input['payAmountBank'] = 0;
                    $input['payAmountSuppTrans'] = 0;
                    $input['payAmountSuppDef'] = 0;
                    $input['payAmountCompLocal'] = 0;
                    $input['payAmountCompRpt'] = 0;
                    $input['suppAmountDocTotal'] = 0;
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 3) {
                $totalAmount = DirectPaymentDetails::selectRaw("SUM(DPAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount")->where('directPaymentAutoID', $id)->first();

                if (!empty($totalAmount->paymentAmount)) {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->paymentAmount);
                    $input['payAmountBank'] = $bankAmount["defaultAmount"];
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->paymentAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->paymentAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($bankAmount["localAmount"]);
                    $input['payAmountCompRpt'] = \Helper::roundValue($bankAmount["reportingAmount"]);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->paymentAmount);
                } else {
                    $input['payAmountBank'] = 0;
                    $input['payAmountSuppTrans'] = 0;
                    $input['payAmountSuppDef'] = 0;
                    $input['payAmountCompLocal'] = 0;
                    $input['payAmountCompRpt'] = 0;
                    $input['suppAmountDocTotal'] = 0;
                }
            }

            $input['createMonthlyDeduction'] = ($input['createMonthlyDeduction'] == 1)? 1: 0;
            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

            Log::info('Cheque No:' . $input['BPVchequeNo']);
            Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
            Log::info('beforeUpdate______________________________________________________');
         

            if(isset($input['BPVAccount']))
            {
                if(!empty($input['BPVAccount']) )
                {
                    $bank_currency = $input['BPVbankCurrency'];
                    $document_currency = $input['supplierTransCurrencyID'];
    
                    $cur_det['companySystemID'] = $input['companySystemID'];
                    $cur_det['bankmasterAutoID'] = $input['BPVbank'];
                    $cur_det['bankAccountAutoID'] = $input['BPVAccount'];
                    $cur_det_info =  (object)$cur_det;
    
                    $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);
    
                    $amount = $bankBalance['netBankBalance'];
                    $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();
    
                    $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');
               
          
                    $input['bankAccountBalance'] = $rounded_amount;
    
                }
            }
            
            $input['payment_mode'] = $input['paymentMode'];
            unset($input['paymentMode']);

            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $id);

            Log::info('Cheque No:' . $input['BPVchequeNo']);
            Log::info('PV Code:' . $paySupplierInvoiceMaster->BPVcode);
            Log::info($paySupplierInvoiceMaster);
            Log::info('afterUpdate______________________________________________________');

            if ($input['payeeType'] == 1) {
                $bankMemoSupplier = BankMemoPayee::where('documentSystemCode', $id)
                    ->delete();
            }

            $message = [['status' => 'success', 'message' => 'PaySupplierInvoiceMaster updated successfully'], ['status' => 'warning', 'message' => $warningMessage]];
            DB::commit();
            return $this->sendReponseWithDetails($paySupplierInvoiceMaster->toArray(), $message,1,$confirm['data'] ?? null);
        } catch
        (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function paymentVoucherLocalUpdate($id,Request $request){


        $value = $request->data;
        $companyId = $request->companyId;
        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();

        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = DirectPaymentDetails::where('directPaymentAutoID',$id)->get();

        $masterINVID = PaySupplierInvoiceMaster::findOrFail($id);
        $AmountLocal = \Helper::roundValue($masterINVID->payAmountSuppTrans/$value);

            $masterInvoiceArray = array('localCurrencyER'=>$value, 'payAmountCompLocal'=>$AmountLocal);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $localAmount = \Helper::roundValue($item->DPAmount / $value);
            $directInvoiceDetailsArray = array('localCurrencyER'=>$value, 'localAmount'=>$localAmount);
            $updatedLocalER = DirectPaymentDetails::findOrFail($item->directPaymentDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }

        return $this->sendResponse([$id,$value], 'Update Local ER');
        }
        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function paymentVoucherReportingUpdate($id,Request $request){
        $value = $request->data;
        $companyId = $request->companyId;

        $policy = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        if (isset($policy->isYesNO) && $policy->isYesNO == 1) {

        $details = DirectPaymentDetails::where('directPaymentAutoID',$id)->get();

        $masterINVID = PaySupplierInvoiceMaster::findOrFail($id);
            $AmountRpt = \Helper::roundValue($masterINVID->payAmountSuppTrans/$value);

            $masterInvoiceArray = array('companyRptCurrencyER'=>$value, 'payAmountCompRpt'=>$AmountRpt);
        $masterINVID->update($masterInvoiceArray);

        foreach($details as $item){
            $reportingAmount = \Helper::roundValue($item->DPAmount / $value);
            $directInvoiceDetailsArray = array('comRptCurrencyER'=>$value, 'comRptAmount'=>$reportingAmount);
            $updatedLocalER = DirectPaymentDetails::findOrFail($item->directPaymentDetailsID);
            $updatedLocalER->update($directInvoiceDetailsArray);
        }


        return $this->sendResponse($id, 'Update Reporting ER');
        }

        else{
            return $this->sendError('Policy not enabled', 400);
        }
    }

    public function validationsForPDC(Request $request){
        $bankAccountID = $request->get('bankAccountID');
        $companySystemID = $request->get('companyID');


        $bankAccount = BankAccount::find($bankAccountID);

        if(!empty($bankAccount)) {

            $chequeRegister = ChequeRegister::where('bank_id', $bankAccount->bankmasterAutoID)->where('bank_account_id', $bankAccount->bankAccountAutoID)->where('isActive', 1)->first();

            if (empty($chequeRegister)) {
                return $this->sendError('No Active cheque register found for the selected bank account');
            }

            $usedCheckID = $this->getLastUsedChequeID($companySystemID, $bankAccount->bankAccountAutoID);

            $unUsedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use ($companySystemID, $bankAccount) {
                $q->where('bank_account_id', $bankAccount->bankAccountAutoID)
                    ->where('company_id', $companySystemID)
                    ->where('isActive', 1);
            })
                ->where('status', 0)
                ->where(function ($q) use ($usedCheckID) {
                    if ($usedCheckID) {
                        $q->where('id', '>', $usedCheckID);
                    }
                })
                ->orderBy('id', 'ASC')
                ->first();

            if (empty($unUsedCheque)) {
                return $this->sendError('There are no unused cheques in the cheque register ' . $chequeRegister->description . ' Define a new cheque register for the selected bank account');

            }
        }

        return $this->sendResponse([], "PDC cheques validated successfully");

        }

    public function getLastUsedChequeID($company_system_id, $bank_account_id) {
        $usedCheque = ChequeRegisterDetail::whereHas('master', function ($q) use($company_system_id,$bank_account_id) {
            $q->where('bank_account_id', $bank_account_id)
                ->where('company_id', $company_system_id)
                ->where('isActive', 1);
        })
            ->where(function ($q) {
                $q->where('status', 1)  // status = 1 => used
                ->orWhere('status', 2); // // status = 2 => cancelled
            })
            ->orderBy('id', 'DESC')
            ->first();

        if(!empty($usedCheque)){
            return $usedCheque->id;
        }
        return null;
    }

    public function generatePdcForPv(Request $request)
    {
        $input = $request->all();

        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($input['PayMasterAutoId']);

        if (empty($paySupplierInvoiceMaster)) {
                return $this->sendError('Pay Supplier Invoice Master not found');
        }

        DB::beginTransaction();
        try {

            $deleteAllPDC = $this->deleteAllPDC($paySupplierInvoiceMaster->documentSystemID, $input['PayMasterAutoId']);

            $bankAccount = BankAccount::find($paySupplierInvoiceMaster->BPVAccount);

            if (!$bankAccount) {
                return $this->sendError('Bank Account not selected');
            }
    
            $amount = floatval($input['totalAmount']) / floatval($input['noOfCheques']);

            for ($i=0; $i < floatval($input['noOfCheques']); $i++) { 
                $chequeRegisterAutoID = null;
                $nextChequeNo = null;
                $chequeGenrated = false;
                if ($paySupplierInvoiceMaster->BPVbankCurrency == $paySupplierInvoiceMaster->localCurrencyID && $paySupplierInvoiceMaster->supplierTransCurrencyID == $paySupplierInvoiceMaster->localCurrencyID) {
                    $res =  $this->paySupplierInvoiceMasterRepository->getChequeNoForPDC($paySupplierInvoiceMaster->companySystemID, $bankAccount, $input['PayMasterAutoId'], $paySupplierInvoiceMaster->documentSystemID);

                    if (!$res['status']) {
                        return $this->sendError($res['message'], 500);
                    }

                    $chequeRegisterAutoID = $res['chequeRegisterAutoID'];
                    $nextChequeNo = $res['nextChequeNo'];
                    $chequeGenrated = $res['chequeGenrated'];
                } 

                $pdcLogData = [
                    'documentSystemID' => $paySupplierInvoiceMaster->documentSystemID,
                    'documentmasterAutoID' => $input['PayMasterAutoId'],
                    'paymentBankID' => $bankAccount->bankmasterAutoID,
                    'companySystemID' => $paySupplierInvoiceMaster->companySystemID,
                    'currencyID' => $paySupplierInvoiceMaster->supplierTransCurrencyID,
                    'chequeRegisterAutoID' => $chequeRegisterAutoID,
                    'chequeNo' => $nextChequeNo,
                    'chequeStatus' => 0,
                    'amount' => $amount,
                ];

                $resPdc = PdcLog::create($pdcLogData);
            }

            DB::commit();
            return $this->sendResponse(['chequeGenrated' => $chequeGenrated], "PDC cheques generated successfully");
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

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceMasters/{id}",
     *      summary="Remove the specified PaySupplierInvoiceMaster from storage",
     *      tags={"PaySupplierInvoiceMaster"},
     *      description="Delete PaySupplierInvoiceMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMaster",
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
    public
    function destroy($id)
    {
        /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $paySupplierInvoiceMaster->delete();

        return $this->sendResponse($id, 'Pay Supplier Invoice Master deleted successfully');
    }

    public function getPaymentVoucherMaster(Request $request)
    {
        $input = $request->all();

        $output = PaySupplierInvoiceMaster::where('PayMasterAutoId', $input['PayMasterAutoId'])
            ->with(['project','supplier', 'bankaccount'=> function($query){
                $query->with('currency');
            }, 'transactioncurrency', 'paymentmode',
                'supplierdetail' => function ($query) {
                    $query->with(['pomaster']);
                },
                'company', 'localcurrency', 'rptcurrency', 'advancedetail', 'confirmed_by',
                'modified_by', 'cheque_treasury_by', 'directdetail' => function ($query) {
                    $query->with('project','segment');
                }, 'approved_by' => function ($query) {
                    $query->with('employee');
                    $query->where('documentSystemID', 4);
                }, 'created_by', 'cancelled_by', 'bankledgers' => function ($query) {
                    $query->where('documentSystemID', 4);
                    $query->with(['bankrec_by']);
                },
                'bankledger_by' => function ($query) {
                    $query->where('documentSystemID', 4);
                    $query->with(['bankrec_by', 'bank_transfer']);
                },'audit_trial.modified_by','pdc_cheque' => function ($q) {
                    $q->where('documentSystemID', 4);
                } ])->first();

        $output['isProjectBase'] = false;
        if ($output) {
            $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
                ->where('companySystemID', $output->companySystemID)
                ->where('isYesNO', 1)
                ->exists();    
        }

        $output['isProjectBase'] = $isProjectBase;

        return $this->sendResponse($output, 'Data retrieved successfully');

    }


    public function getAllPaymentVoucherByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'cancelYN', 'confirmedYN', 'approved', 'invoiceType', 'supplierID', 'chequePaymentYN', 'BPVbank', 'BPVAccount', 'chequeSentToTreasury', 'payment_mode', 'projectID','payeeTypeID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        $employeeID = $request['employeeID'];
        $employeeID = (array)$employeeID;
        $employeeID = collect($employeeID)->pluck('id');

        $projectID = $request['projectID'];
        $projectID = (array)$projectID;
        $projectID = collect($projectID)->pluck('id');

        $createdBy = $request['createdBy'];
        $createdBy = (array)$createdBy;
        $createdBy = collect($createdBy)->pluck('id');

        $search = $request->input('search.value');
        
        if(empty($input['BPVAccount'])){
            unset($input['BPVAccount']);
        }
        if(empty($input['BPVbank'])){
            unset($input['BPVbank']);
        }

        if(empty($input['chequeSentToTreasury'])){
            unset($input['chequeSentToTreasury']);
        }

        if(empty($input['invoiceType'])){
            unset($input['invoiceType']);
        }

        if(empty($input['payeeTypeID'])){
            unset($input['payeeTypeID']);
        }
        if(empty($input['month'])){
            unset($input['month']);
        }
        if(empty($input['supplierID'])){
            unset($input['supplierID']);
        }
        if(empty($input['employeeID'])){
            unset($input['employeeID']);
        }
        if(empty($input['year'])){
            unset($input['year']);
        }
        if(empty($input['payment_mode'])){
            unset($input['payment_mode']);
        }
        if(empty($input['cancelYN'])){
            unset($input['cancelYN']);
        }
        if(empty($input['chequePaymentYN'])){
            unset($input['chequePaymentYN']);
        }

        $paymentVoucher = $this->paySupplierInvoiceMasterRepository->paySupplierInvoiceListQuery($request, $input, $search, $supplierID, $projectID, $employeeID,$createdBy);

        return \DataTables::eloquent($paymentVoucher)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('PayMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getPaymentVoucherFormData(Request $request)
    {
        $companyId = isset($request['companyId']) ? $request['companyId'] : 0;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $currency = CurrencyMaster::all();
        $bank = BankAssign::where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        if (isset($request['type']) && $request['type'] == 'account_details') {
            $output = array( 'bank' => $bank, 'currency' => $currency);
        } else {
            $supplier = SupplierAssigned::whereIn("companySystemID", $subCompanies);
            if (isset($request['type']) && $request['type'] != 'filter') {
                $supplier = $supplier->where('isActive', 1);
            }
            $supplier = $supplier->get();

            $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

            $companyFinanceYear = \Helper::companyFinanceYear($companyId, 1);
            /** Yes and No Selection */
            $yesNoSelection = YesNoSelection::all();

            $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

            $month = Months::all();

            $years = PaySupplierInvoiceMaster::select(DB::raw("YEAR(createdDateTime) as year"))
                ->whereNotNull('createdDateTime')
                ->groupby('year')
                ->orderby('year', 'desc')
                ->get();


            $payee = Employee::whereHas('invoice' , function($q) use ($companyId) {
                $q->where('companySystemID',$companyId);
            })->where('discharegedYN','<>', 2);
            if(Helper::checkHrmsIntergrated($companyId)){
                $payee = $payee->whereHas('hr_emp', function($q){
                    $q->where('isDischarged', 0)->where('empConfirmedYN', 1);
                });
            }
            $payee = $payee->get();

            $payeeAll = Employee::where('discharegedYN','<>', 2);
            if(Helper::checkHrmsIntergrated($companyId)){
                $payeeAll = $payeeAll->whereHas('hr_emp', function($q){
                    $q->where('isDischarged', 0)->where('empConfirmedYN', 1);
                });
            }
            $payeeAll = $payeeAll->get();

            $segment = SegmentMaster::ofCompany($subCompanies)->IsActive()->get();

            $expenseClaimType = ExpenseClaimType::all();

            $interCompanyTo = Company::where('isGroup', 0)->get();

            $companyCurrency = \Helper::companyCurrency($companyId);

            // check policy
            $policyOn = 0;
            $UPECSLPolicy = CompanyPolicyMaster::where('companySystemID', $companyId)
                ->where('companyPolicyCategoryID', 16)
                ->where('isYesNO', 1)
                ->first();
            if (isset($UPECSLPolicy->isYesNO) && $UPECSLPolicy->isYesNO == 1) {
                $policyOn = 1;
            }

            $monthly_declarations_drop = MonthlyDeclarationsTypes::selectRaw("monthlyDeclarationID, monthlyDeclaration")
                    ->where('companyID', $companyId)->where('monthlyDeclarationType', 'D')->where('isPayrollCategory', 1)
                    ->get();

            $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $companyId)
                ->where('companyPolicyCategoryID', 35)
                ->where('isYesNO', 1)
                ->first();

            $assetAllocatePolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 61)
                                    ->where('companySystemID', $companyId)
                                    ->where('isYesNO', 1)
                                    ->first();

            $paymentMode = PaymentType::all();

             $employeeInvoice = CompanyPolicyMaster::where('companyPolicyCategoryID', 68)
                                    ->where('companySystemID', $companyId)
                                    ->first();

            $employeeControlAccount = SystemGlCodeScenarioDetail::getGlByScenario($companyId, null, "employee-control-account");

            $companyData = Company::find($companyId);

            $isProject_base = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
            ->where('companySystemID', $companyId)
            ->where('isYesNO', 1)
            ->exists();

            $projects = ErpProjectMaster::where('companySystemID', $companyId)->get();

            $isVATEligible = TaxService::checkCompanyVATEligible($companyId);

            $contractEnablePolicy = Helper::checkPolicy($companyId, 93);
            $sendToTreasuryPolicy = Helper::checkPolicy($companyId, 96);

            $output = array(
                'financialYears' => $financialYears,
                'companyFinanceYear' => $companyFinanceYear,
                'yesNoSelection' => $yesNoSelection,
                'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
                'month' => $month,
                'years' => $years,
                'supplier' => $supplier,
                'chequeRegistryPolicy' => $is_exist_policy_GCNFCR ? true : false,
                'assetAllocatePolicy' => $assetAllocatePolicy ? true : false,
                'payee' => $payee,
                'employeeInvoicePolicy' => ($employeeInvoice && $employeeInvoice->isYesNO == 1) ? true : false,
                'bank' => $bank,
                'employeeControlAccount' => $employeeControlAccount,
                'isHrmsIntergrated' => ($companyData) ? $companyData->isHrmsIntergrated : false,
                'currency' => $currency,
                'segments' => $segment,
                'expenseClaimType' => $expenseClaimType,
                'interCompany' => $interCompanyTo,
                'companyCurrency' => $companyCurrency,
                'isPolicyOn' => $policyOn,
                'deduction_type_drop' => $monthly_declarations_drop,
                'paymentMode' => $paymentMode,
                'isProjectBase' => $isProject_base,
                'isVATEligible' => $isVATEligible,
                'projects' => $projects,
                'payeeAll' => $payeeAll,
                'contractEnablePolicy' => $contractEnablePolicy,
                'sendToTreasuryPolicy' => $sendToTreasuryPolicy
            );
        }

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public
    function getBankAccount(Request $request)
    {
        $bankAccount = DB::table('erp_bankaccount')->leftjoin('currencymaster', 'currencyID', 'accountCurrencyID')->where('bankmasterAutoID', $request["bankmasterAutoID"])->where('erp_bankaccount.companySystemID', $request["companyID"])->where('isAccountActive', 1)->where('approvedYN', 1)->get();
        return $this->sendResponse($bankAccount, 'Record retrieved successfully');
    }

    public function getMultipleAccountsByBank(Request $request)
    {
        $bankmasterAutoID = $request['bank_id'];
        $bankmasterAutoID = (array)$bankmasterAutoID;
        $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

        $bankAccount = DB::table('erp_bankaccount')->leftjoin('currencymaster', 'currencyID', 'accountCurrencyID')->whereIn('bankmasterAutoID', $bankmasterAutoID)->where('erp_bankaccount.companySystemID', $request["companyID"])->where('isAccountActive', 1)->where('approvedYN', 1)->get();
        return $this->sendResponse($bankAccount, 'Record retrieved successfully');
    }

    public function checkPVDocumentActive(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @ PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($input["PayMasterAutoId"]);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $companySystemID = $paySupplierInvoiceMaster->companySystemID;
        $documentSystemID = $paySupplierInvoiceMaster->documentSystemID;

        $bankMaster = BankAssign::ofCompany($paySupplierInvoiceMaster->companySystemID)->isActive()->where('bankmasterAutoID', $paySupplierInvoiceMaster->BPVbank)->first();

        if (empty($bankMaster)) {
            return $this->sendError('Selected Bank is not active', 500);
        }

        $bankAccount = BankAccount::isActive()->find($paySupplierInvoiceMaster->BPVAccount);

        if (empty($bankAccount)) {
            return $this->sendError('Selected Bank Account is not active', 500);
        }

        return $this->sendResponse($bankAccount, 'Record retrieved successfully');
    }

    public function getPaymentVoucherPendingAmountDetails(Request $request) {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request["id"]);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Payment voucher not found');
        }

        if ($paySupplierInvoiceMaster->invoiceType == 6) {
            $sql = 'SELECT * FROM erp_accountspayableledger WHERE documentSystemCode ='.$request["id"].' ORDER BY apAutoID DESC';
            $output1 = DB::select($sql);
            return $this->sendResponse($output1, 'Record retrieved successfully');
        }

        $decimalPlaces  = Helper::getCurrencyDecimalPlace($paySupplierInvoiceMaster->supplierTransCurrencyID);

        $BPVdate = Carbon::parse($paySupplierInvoiceMaster->BPVdate)->format('Y-m-d');


        $sql = 'SELECT
        erp_accountspayableledger.apAutoID,
        erp_accountspayableledger.documentSystemCode as bookingInvSystemCode,
        erp_accountspayableledger.supplierTransCurrencyID,
        erp_accountspayableledger.supplierTransER,
        erp_accountspayableledger.localCurrencyID,
        erp_accountspayableledger.localER,
        erp_accountspayableledger.localAmount,
        erp_accountspayableledger.comRptCurrencyID,
        erp_accountspayableledger.comRptER,
        erp_accountspayableledger.comRptAmount,
        erp_accountspayableledger.companySystemID,
        erp_accountspayableledger.companyID,
        erp_accountspayableledger.documentSystemID as addedDocumentSystemID,
        erp_accountspayableledger.documentID as addedDocumentID,
        erp_accountspayableledger.documentCode as bookingInvDocCode,
        erp_accountspayableledger.documentDate as bookingInvoiceDate,
        erp_accountspayableledger.invoiceType as addedDocumentType,
        erp_accountspayableledger.supplierCodeSystem,
        erp_accountspayableledger.supplierInvoiceNo,
        erp_accountspayableledger.supplierInvoiceDate,
        erp_accountspayableledger.supplierDefaultCurrencyID,
        erp_accountspayableledger.supplierDefaultCurrencyER,
        erp_accountspayableledger.supplierDefaultAmount,
        erp_accountspayableledger.purchaseOrderID,
        erp_accountspayableledger.isRetention,
        poid.purchaseOrderCode,
        CurrencyCode,
        DecimalPlaces,
        IFNULL(supplierInvoiceAmount,0) as supplierInvoiceAmount,
        IFNULL(supplierInvoiceAmount,0) - IFNULL(sid.SumOfsupplierPaymentAmount,0)- IFNULL(md.matchedAmount *- 1,0) as paymentBalancedAmount,
        IFNULL(ABS(sid.SumOfsupplierPaymentAmount),0) + IFNULL(md.matchedAmount,0) as matchedAmount,
        false as isChecked 
    FROM
        erp_accountspayableledger
        LEFT JOIN (
          SELECT
                erp_purchaseordermaster.purchaseOrderCode,
                erp_purchaseordermaster.purchaseOrderID
            FROM
                erp_purchaseordermaster
            ) poid ON poid.purchaseOrderID = erp_accountspayableledger.purchaseOrderID
        LEFT JOIN (
            SELECT
                erp_paysupplierinvoicedetail.apAutoID,
                IFNULL(Sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ),0) AS SumOfsupplierPaymentAmount,
                IFNULL(Sum( erp_paysupplierinvoicedetail.paymentBalancedAmount ),0) AS SumOfpaymentBalancedAmount 
            FROM
                erp_paysupplierinvoicedetail 
            GROUP BY
                erp_paysupplierinvoicedetail.apAutoID 
                ) sid ON sid.apAutoID = erp_accountspayableledger.apAutoID
                LEFT JOIN (
            SELECT
                erp_matchdocumentmaster.PayMasterAutoId,
                erp_matchdocumentmaster.companyID,
                erp_matchdocumentmaster.companySystemID,
                erp_matchdocumentmaster.documentSystemID,
                erp_matchdocumentmaster.BPVcode,
                erp_matchdocumentmaster.BPVsupplierID,
                erp_matchdocumentmaster.supplierTransCurrencyID,
                SUM(erp_matchdocumentmaster.matchedAmount) as matchedAmount,
                SUM(erp_matchdocumentmaster.matchLocalAmount) as matchLocalAmount,
                SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount
            FROM
                erp_matchdocumentmaster 
            WHERE
                erp_matchdocumentmaster.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                AND erp_matchdocumentmaster.documentSystemID = 15
                GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                ) md ON md.documentSystemID = erp_accountspayableledger.documentSystemID 
        AND md.PayMasterAutoId = erp_accountspayableledger.documentSystemCode 
        AND md.BPVsupplierID = erp_accountspayableledger.supplierCodeSystem 
        AND md.supplierTransCurrencyID = erp_accountspayableledger.supplierTransCurrencyID 
        AND md.companySystemID = erp_accountspayableledger.companySystemID 
        LEFT JOIN currencymaster ON erp_accountspayableledger.supplierTransCurrencyID = currencymaster.currencyID 
    WHERE
        erp_accountspayableledger.invoiceType IN ( 0, 1, 4, 7, 2,3 ) 
        AND erp_accountspayableledger.documentSystemID != 4
        AND erp_accountspayableledger.selectedToPaymentInv = 0 
        AND erp_accountspayableledger.fullyInvoice <> 2 
        AND erp_accountspayableledger.documentSystemCode ='.$request["id"].'
        ORDER BY erp_accountspayableledger.apAutoID DESC';

        $output = DB::select($sql);

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    public
    function getPOPaymentForPV(Request $request)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request["PayMasterAutoId"]);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Payment voucher not found');
        }

        if ($paySupplierInvoiceMaster->invoiceType == 6) {
            $output1 = $this->getEmployeePaymentForPV($request, $paySupplierInvoiceMaster);
            return $this->sendResponse($output1, 'Record retrieved successfully');
        }

        $decimalPlaces  = Helper::getCurrencyDecimalPlace($paySupplierInvoiceMaster->supplierTransCurrencyID);

        $BPVdate = Carbon::parse($paySupplierInvoiceMaster->BPVdate)->format('Y-m-d');

        if($paySupplierInvoiceMaster->invoiceType == 2) {
            $sql = 'SELECT
                	erp_accountspayableledger.apAutoID,
                	erp_accountspayableledger.documentSystemCode as bookingInvSystemCode,
                	erp_accountspayableledger.supplierTransCurrencyID,
                	erp_accountspayableledger.supplierTransER,
                	erp_accountspayableledger.localCurrencyID,
                	erp_accountspayableledger.localER,
                	erp_accountspayableledger.localAmount,
                	erp_accountspayableledger.comRptCurrencyID,
                	erp_accountspayableledger.comRptER,
                	erp_accountspayableledger.comRptAmount,
                	erp_accountspayableledger.companySystemID,
                	erp_accountspayableledger.companyID,
                	erp_accountspayableledger.documentSystemID as addedDocumentSystemID,
                	erp_accountspayableledger.documentID as addedDocumentID,
                	erp_accountspayableledger.documentCode as bookingInvDocCode,
                	erp_accountspayableledger.documentDate as bookingInvoiceDate,
                	erp_accountspayableledger.invoiceType as addedDocumentType,
                	erp_accountspayableledger.supplierCodeSystem,
                	erp_accountspayableledger.supplierInvoiceNo,
                	erp_accountspayableledger.supplierInvoiceDate,
                	erp_accountspayableledger.supplierDefaultCurrencyID,
                	erp_accountspayableledger.supplierDefaultCurrencyER,
                	erp_accountspayableledger.supplierDefaultAmount,
                    erp_accountspayableledger.purchaseOrderID,
                    erp_accountspayableledger.isRetention,
                    poid.purchaseOrderCode,
                	CurrencyCode,
                	DecimalPlaces,
                	IFNULL(supplierInvoiceAmount,0) as supplierInvoiceAmount,
                	IFNULL(supplierInvoiceAmount,0) - IFNULL(sid.SumOfsupplierPaymentAmount,0)- IFNULL(md.matchedAmount *- 1,0) as paymentBalancedAmount,
                	IFNULL(ABS(sid.SumOfsupplierPaymentAmount),0) + IFNULL(md.matchedAmount,0) as matchedAmount,
                	false as isChecked 
                FROM
                	erp_accountspayableledger
                    LEFT JOIN (
                      SELECT
                            erp_purchaseordermaster.purchaseOrderCode,
                            erp_purchaseordermaster.purchaseOrderID
                        FROM
                            erp_purchaseordermaster
                        ) poid ON poid.purchaseOrderID = erp_accountspayableledger.purchaseOrderID
                	LEFT JOIN (
                        SELECT
                        	erp_paysupplierinvoicedetail.apAutoID,
                        	IFNULL(Sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ),0) AS SumOfsupplierPaymentAmount,
                        	IFNULL(Sum( erp_paysupplierinvoicedetail.paymentBalancedAmount ),0) AS SumOfpaymentBalancedAmount 
                        FROM
                        	erp_paysupplierinvoicedetail 
                        INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId
                        LEFT JOIN erp_debitnote ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_debitnote.debitNoteAutoID
                        WHERE erp_paysupplierinvoicemaster.invoiceType != 6 AND erp_paysupplierinvoicemaster.invoiceType != 7 AND (erp_debitnote.type = 1 OR erp_debitnote.debitNoteAutoID IS NULL)
                        GROUP BY
                        	erp_paysupplierinvoicedetail.apAutoID 
                        	) sid ON sid.apAutoID = erp_accountspayableledger.apAutoID
                        	LEFT JOIN (
                        SELECT
                        	erp_matchdocumentmaster.PayMasterAutoId,
                        	erp_matchdocumentmaster.companyID,
                        	erp_matchdocumentmaster.companySystemID,
                        	erp_matchdocumentmaster.documentSystemID,
                        	erp_matchdocumentmaster.BPVcode,
                        	erp_matchdocumentmaster.BPVsupplierID,
                        	erp_matchdocumentmaster.supplierTransCurrencyID,
                        	SUM(erp_matchdocumentmaster.matchedAmount) as matchedAmount,
                        	SUM(erp_matchdocumentmaster.matchLocalAmount) as matchLocalAmount,
                        	SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount
                        FROM
                        	erp_matchdocumentmaster 
                        WHERE
                        	erp_matchdocumentmaster.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                        	AND erp_matchdocumentmaster.documentSystemID = 15
                        	GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                        	) md ON md.documentSystemID = erp_accountspayableledger.documentSystemID 
                	AND md.PayMasterAutoId = erp_accountspayableledger.documentSystemCode 
                	AND md.BPVsupplierID = erp_accountspayableledger.supplierCodeSystem 
                	AND md.supplierTransCurrencyID = erp_accountspayableledger.supplierTransCurrencyID 
                	AND md.companySystemID = erp_accountspayableledger.companySystemID 
                	LEFT JOIN currencymaster ON erp_accountspayableledger.supplierTransCurrencyID = currencymaster.currencyID 
                WHERE
                	erp_accountspayableledger.invoiceType IN ( 0, 1, 4, 7, 2,3 ) 
                	AND erp_accountspayableledger.documentSystemID != 4
                	AND DATE_FORMAT(erp_accountspayableledger.documentDate,"%Y-%m-%d") <= "' . $BPVdate . '" 
                	AND erp_accountspayableledger.selectedToPaymentInv = 0 
                	AND erp_accountspayableledger.fullyInvoice <> 2 
                	AND erp_accountspayableledger.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                	AND erp_accountspayableledger.supplierCodeSystem = ' . $paySupplierInvoiceMaster->BPVsupplierID . ' 
                	AND erp_accountspayableledger.supplierTransCurrencyID = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount, ' . $decimalPlaces . ') != 0 ORDER BY erp_accountspayableledger.apAutoID DESC';
        }
        else {
            $sql = 'SELECT
                	erp_accountspayableledger.apAutoID,
                	erp_accountspayableledger.documentSystemCode as bookingInvSystemCode,
                	erp_accountspayableledger.supplierTransCurrencyID,
                	erp_accountspayableledger.supplierTransER,
                	erp_accountspayableledger.localCurrencyID,
                	erp_accountspayableledger.localER,
                	erp_accountspayableledger.localAmount,
                	erp_accountspayableledger.comRptCurrencyID,
                	erp_accountspayableledger.comRptER,
                	erp_accountspayableledger.comRptAmount,
                	erp_accountspayableledger.companySystemID,
                	erp_accountspayableledger.companyID,
                	erp_accountspayableledger.documentSystemID as addedDocumentSystemID,
                	erp_accountspayableledger.documentID as addedDocumentID,
                	erp_accountspayableledger.documentCode as bookingInvDocCode,
                	erp_accountspayableledger.documentDate as bookingInvoiceDate,
                	erp_accountspayableledger.invoiceType as addedDocumentType,
                	erp_accountspayableledger.supplierCodeSystem,
                	erp_accountspayableledger.supplierInvoiceNo,
                	erp_accountspayableledger.supplierInvoiceDate,
                	erp_accountspayableledger.supplierDefaultCurrencyID,
                	erp_accountspayableledger.supplierDefaultCurrencyER,
                	erp_accountspayableledger.supplierDefaultAmount,
                    erp_accountspayableledger.purchaseOrderID,
                    erp_accountspayableledger.isRetention,
                    poid.purchaseOrderCode,
                	CurrencyCode,
                	DecimalPlaces,
                	IFNULL(supplierInvoiceAmount,0) as supplierInvoiceAmount,
                	IFNULL(supplierInvoiceAmount,0) - IFNULL(sid.SumOfsupplierPaymentAmount,0)- IFNULL(md.matchedAmount *- 1,0) as paymentBalancedAmount,
                	IFNULL(ABS(sid.SumOfsupplierPaymentAmount),0) + IFNULL(md.matchedAmount,0) as matchedAmount,
                	false as isChecked 
                FROM
                	erp_accountspayableledger
                    LEFT JOIN (
                      SELECT
                            erp_purchaseordermaster.purchaseOrderCode,
                            erp_purchaseordermaster.purchaseOrderID
                        FROM
                            erp_purchaseordermaster
                        ) poid ON poid.purchaseOrderID = erp_accountspayableledger.purchaseOrderID
                	LEFT JOIN (
                        SELECT
                        	erp_paysupplierinvoicedetail.apAutoID,
                        	IFNULL(Sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ),0) AS SumOfsupplierPaymentAmount,
                        	IFNULL(Sum( erp_paysupplierinvoicedetail.paymentBalancedAmount ),0) AS SumOfpaymentBalancedAmount 
                        FROM
                        	erp_paysupplierinvoicedetail 
                        INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId
                        WHERE erp_paysupplierinvoicemaster.invoiceType != 6 AND erp_paysupplierinvoicemaster.invoiceType != 7
                        GROUP BY
                        	erp_paysupplierinvoicedetail.apAutoID 
                        	) sid ON sid.apAutoID = erp_accountspayableledger.apAutoID
                        	LEFT JOIN (
                        SELECT
                        	erp_matchdocumentmaster.PayMasterAutoId,
                        	erp_matchdocumentmaster.companyID,
                        	erp_matchdocumentmaster.companySystemID,
                        	erp_matchdocumentmaster.documentSystemID,
                        	erp_matchdocumentmaster.BPVcode,
                        	erp_matchdocumentmaster.BPVsupplierID,
                        	erp_matchdocumentmaster.supplierTransCurrencyID,
                        	SUM(erp_matchdocumentmaster.matchedAmount) as matchedAmount,
                        	SUM(erp_matchdocumentmaster.matchLocalAmount) as matchLocalAmount,
                        	SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount
                        FROM
                        	erp_matchdocumentmaster 
                        WHERE
                        	erp_matchdocumentmaster.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                        	AND erp_matchdocumentmaster.documentSystemID = 15
                        	GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                        	) md ON md.documentSystemID = erp_accountspayableledger.documentSystemID 
                	AND md.PayMasterAutoId = erp_accountspayableledger.documentSystemCode 
                	AND md.BPVsupplierID = erp_accountspayableledger.supplierCodeSystem 
                	AND md.supplierTransCurrencyID = erp_accountspayableledger.supplierTransCurrencyID 
                	AND md.companySystemID = erp_accountspayableledger.companySystemID 
                	LEFT JOIN currencymaster ON erp_accountspayableledger.supplierTransCurrencyID = currencymaster.currencyID 
                WHERE
                	erp_accountspayableledger.invoiceType IN ( 0, 1, 4, 7, 2,3 ) 
                	AND erp_accountspayableledger.documentSystemID != 4
                	AND DATE_FORMAT(erp_accountspayableledger.documentDate,"%Y-%m-%d") <= "' . $BPVdate . '" 
                	AND erp_accountspayableledger.selectedToPaymentInv = 0 
                	AND erp_accountspayableledger.fullyInvoice <> 2 
                	AND erp_accountspayableledger.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                	AND erp_accountspayableledger.supplierCodeSystem = ' . $paySupplierInvoiceMaster->BPVsupplierID . ' 
                	AND erp_accountspayableledger.supplierTransCurrencyID = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount, '.$decimalPlaces.') != 0 ORDER BY erp_accountspayableledger.apAutoID DESC';
        }
        $output = DB::select($sql);
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public static function getEmployeePaymentForPV($request, $paySupplierInvoiceMaster)
    {
        $decimalPlaces  = Helper::getCurrencyDecimalPlace($paySupplierInvoiceMaster->supplierTransCurrencyID);

        $BPVdate = Carbon::parse($paySupplierInvoiceMaster->BPVdate)->format('Y-m-d');
        $sql = 'SELECT
                employee_ledger.id,
                employee_ledger.documentSystemCode as bookingInvSystemCode,
                employee_ledger.supplierTransCurrencyID,
                employee_ledger.supplierTransER,
                employee_ledger.localCurrencyID,
                employee_ledger.localER,
                employee_ledger.localAmount,
                employee_ledger.comRptCurrencyID,
                employee_ledger.comRptER,
                employee_ledger.comRptAmount,
                employee_ledger.companySystemID,
                employee_ledger.companyID,
                employee_ledger.documentSystemID as addedDocumentSystemID,
                employee_ledger.documentID as addedDocumentID,
                employee_ledger.documentCode as bookingInvDocCode,
                employee_ledger.documentDate as bookingInvoiceDate,
                employee_ledger.invoiceType as addedDocumentType,
                employee_ledger.employeeSystemID,
                employee_ledger.supplierInvoiceNo,
                employee_ledger.supplierInvoiceDate,
                employee_ledger.supplierDefaultCurrencyID,
                employee_ledger.supplierDefaultCurrencyER,
                employee_ledger.supplierDefaultAmount,
                CurrencyCode,
                DecimalPlaces,
                IFNULL(supplierInvoiceAmount,0) as supplierInvoiceAmount,
                IFNULL(supplierInvoiceAmount,0) - IFNULL(sid.SumOfsupplierPaymentAmount,0)- IFNULL(md.matchedAmount *- 1,0) as paymentBalancedAmount,
                IFNULL(ABS(sid.SumOfsupplierPaymentAmount),0) + IFNULL(md.matchedAmount,0) as matchedAmount,
                false as isChecked 
            FROM
                employee_ledger
                LEFT JOIN (
            SELECT
                erp_paysupplierinvoicedetail.apAutoID,
                IFNULL(Sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ),0) AS SumOfsupplierPaymentAmount,
                IFNULL(Sum( erp_paysupplierinvoicedetail.paymentBalancedAmount ),0) AS SumOfpaymentBalancedAmount 
            FROM
                erp_paysupplierinvoicedetail
                JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_paysupplierinvoicemaster.PayMasterAutoId 
            WHERE 
                erp_paysupplierinvoicemaster.invoiceType = 6 OR erp_paysupplierinvoicemaster.invoiceType = 7
            GROUP BY
                erp_paysupplierinvoicedetail.apAutoID 
                ) sid ON sid.apAutoID = employee_ledger.id
                LEFT JOIN (
            SELECT
                erp_matchdocumentmaster.PayMasterAutoId,
                erp_matchdocumentmaster.companyID,
                erp_matchdocumentmaster.companySystemID,
                erp_matchdocumentmaster.documentSystemID,
                erp_matchdocumentmaster.BPVcode,
                erp_matchdocumentmaster.BPVsupplierID,
                erp_matchdocumentmaster.supplierTransCurrencyID,
                SUM(erp_matchdocumentmaster.matchedAmount) as matchedAmount,
                SUM(erp_matchdocumentmaster.matchLocalAmount) as matchLocalAmount,
                SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount
            FROM
                erp_matchdocumentmaster 
            WHERE
                erp_matchdocumentmaster.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                AND erp_matchdocumentmaster.documentSystemID = 15
                GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                ) md ON md.documentSystemID = employee_ledger.documentSystemID 
                AND md.PayMasterAutoId = employee_ledger.documentSystemCode 
                AND md.BPVsupplierID = employee_ledger.employeeSystemID 
                AND md.supplierTransCurrencyID = employee_ledger.supplierTransCurrencyID 
                AND md.companySystemID = employee_ledger.companySystemID 
                LEFT JOIN currencymaster ON employee_ledger.supplierTransCurrencyID = currencymaster.currencyID 
            WHERE
                employee_ledger.invoiceType IN ( 0, 1, 4) 
                AND DATE_FORMAT(employee_ledger.documentDate,"%Y-%m-%d") <= "' . $BPVdate . '" 
                AND employee_ledger.selectedToPaymentInv = 0 
                AND employee_ledger.fullyInvoice <> 2 
                AND employee_ledger.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                AND employee_ledger.employeeSystemID = ' . $paySupplierInvoiceMaster->directPaymentPayeeEmpID . ' 
                AND employee_ledger.supplierTransCurrencyID = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount, '.$decimalPlaces.') != 0 ORDER BY employee_ledger.id DESC';


        $sql1 = 'SELECT
                employee_ledger.id,
                employee_ledger.documentSystemCode as bookingInvSystemCode,
                employee_ledger.supplierTransCurrencyID,
                employee_ledger.supplierTransER,
                employee_ledger.localCurrencyID,
                employee_ledger.localER,
                employee_ledger.localAmount,
                employee_ledger.comRptCurrencyID,
                employee_ledger.comRptER,
                employee_ledger.comRptAmount,
                employee_ledger.companySystemID,
                employee_ledger.companyID,
                employee_ledger.documentSystemID as addedDocumentSystemID,
                employee_ledger.documentID as addedDocumentID,
                employee_ledger.documentCode as bookingInvDocCode,
                employee_ledger.documentDate as bookingInvoiceDate,
                employee_ledger.invoiceType as addedDocumentType,
                employee_ledger.employeeSystemID,
                employee_ledger.supplierInvoiceNo,
                employee_ledger.supplierInvoiceDate,
                employee_ledger.supplierDefaultCurrencyID,
                employee_ledger.supplierDefaultCurrencyER,
                employee_ledger.supplierDefaultAmount,
                CurrencyCode,
                DecimalPlaces,
                IFNULL(supplierInvoiceAmount,0) as supplierInvoiceAmount,
                IFNULL(supplierInvoiceAmount,0) - IFNULL(sid.SumOfsupplierPaymentAmount,0)- IFNULL(md.matchedAmount *- 1,0) as paymentBalancedAmount,
                IFNULL(ABS(sid.SumOfsupplierPaymentAmount),0) + IFNULL(md.matchedAmount,0) as matchedAmount,
                false as isChecked 
            FROM
                employee_ledger
                LEFT JOIN (
            SELECT
                erp_paysupplierinvoicedetail.apAutoID,
                IFNULL(Sum( erp_paysupplierinvoicedetail.supplierPaymentAmount ),0) AS SumOfsupplierPaymentAmount,
                IFNULL(Sum( erp_paysupplierinvoicedetail.paymentBalancedAmount ),0) AS SumOfpaymentBalancedAmount 
            FROM
                erp_paysupplierinvoicedetail
                JOIN erp_debitnote ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_debitnote.debitNoteAutoID 
            WHERE 
                    erp_debitnote.type = 2 AND erp_paysupplierinvoicedetail.documentSystemID = 15
            GROUP BY
                erp_paysupplierinvoicedetail.apAutoID 
                ) sid ON sid.apAutoID = employee_ledger.id
                LEFT JOIN (
            SELECT
                erp_matchdocumentmaster.PayMasterAutoId,
                erp_matchdocumentmaster.companyID,
                erp_matchdocumentmaster.companySystemID,
                erp_matchdocumentmaster.documentSystemID,
                erp_matchdocumentmaster.BPVcode,
                erp_matchdocumentmaster.BPVsupplierID,
                erp_matchdocumentmaster.supplierTransCurrencyID,
                SUM(erp_matchdocumentmaster.matchedAmount) as matchedAmount,
                SUM(erp_matchdocumentmaster.matchLocalAmount) as matchLocalAmount,
                SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount
            FROM
                erp_matchdocumentmaster 
            WHERE
                erp_matchdocumentmaster.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                AND erp_matchdocumentmaster.documentSystemID = 15
                GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                ) md ON md.documentSystemID = employee_ledger.documentSystemID 
                AND md.PayMasterAutoId = employee_ledger.documentSystemCode 
                AND md.BPVsupplierID = employee_ledger.employeeSystemID 
                AND md.supplierTransCurrencyID = employee_ledger.supplierTransCurrencyID 
                AND md.companySystemID = employee_ledger.companySystemID 
                LEFT JOIN currencymaster ON employee_ledger.supplierTransCurrencyID = currencymaster.currencyID 
            WHERE
                employee_ledger.invoiceType IN ( 0, 1, 4) 
                AND DATE_FORMAT(employee_ledger.documentDate,"%Y-%m-%d") <= "' . $BPVdate . '" 
                AND employee_ledger.selectedToPaymentInv = 0 
                AND employee_ledger.fullyInvoice <> 2 
                AND employee_ledger.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
                AND employee_ledger.employeeSystemID = ' . $paySupplierInvoiceMaster->directPaymentPayeeEmpID . ' 
                AND employee_ledger.supplierTransCurrencyID = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount, '.$decimalPlaces.') != 0 ORDER BY employee_ledger.id DESC';


        $output = DB::select($sql);
        $output1 = (array)(DB::select($sql1));

        foreach($output1 as $key=>$val)
        {
            foreach($output as $out)
            {
                if($val->bookingInvDocCode == $out->bookingInvDocCode)
                {
                    $output1[$key]->matchedAmount = $val->matchedAmount + $out->matchedAmount;
                    $output1[$key]->paymentBalancedAmount = $val->supplierInvoiceAmount - $output1[$key]->matchedAmount;
                    break;
                }
                
            }
   
          
          
        }
        return $output1;
        
    }

    public function getADVPaymentForPV(Request $request)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request["PayMasterAutoId"]);
        $output = DB::select('SELECT
	erp_purchaseorderadvpayment.poAdvPaymentID,
	erp_purchaseorderadvpayment.companyID,
	erp_purchaseorderadvpayment.companySystemID,
	erp_purchaseorderadvpayment.poID as purchaseOrderID,
	erp_purchaseorderadvpayment.poCode as purchaseOrderCode,
	erp_purchaseorderadvpayment.supplierID,
	erp_purchaseorderadvpayment.narration as comments,
	erp_purchaseorderadvpayment.currencyID,
	currencymaster.CurrencyCode,
	currencymaster.DecimalPlaces,
	IFNULL( erp_purchaseorderadvpayment.reqAmount, 0 ) AS reqAmount,
	( IFNULL( erp_purchaseorderadvpayment.reqAmount, 0 ) - IFNULL( advd.SumOfpaymentAmount, 0 ) ) AS BalanceAmount,
	erp_purchaseordermaster.supplierTransactionCurrencyID as supplierTransCurrencyID,
	erp_purchaseordermaster.supplierTransactionER as supplierTransER,
	erp_purchaseordermaster.supplierDefaultCurrencyID,
	erp_purchaseordermaster.supplierDefaultER as supplierDefaultCurrencyER, 
	erp_purchaseordermaster.localCurrencyID,
	erp_purchaseordermaster.localCurrencyER as localER,
	erp_purchaseordermaster.companyReportingCurrencyID as comRptCurrencyID,
	erp_purchaseordermaster.companyReportingER as comRptER,
	erp_purchaseordermaster.poTotalSupplierTransactionCurrency as poTotalSupplierTransactionCurrency,
	false as isChecked  
FROM
	( ( erp_purchaseorderadvpayment LEFT JOIN currencymaster ON erp_purchaseorderadvpayment.currencyID = currencymaster.currencyID ) INNER JOIN erp_purchaseordermaster ON erp_purchaseorderadvpayment.poID = erp_purchaseordermaster.purchaseOrderID )
	LEFT JOIN (
SELECT
	erp_advancepaymentdetails.poAdvPaymentID,
	erp_advancepaymentdetails.companyID,
	erp_advancepaymentdetails.companySystemID,
	erp_advancepaymentdetails.purchaseOrderID,
	IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount 
FROM
	erp_advancepaymentdetails 
GROUP BY
	erp_advancepaymentdetails.poAdvPaymentID,
	erp_advancepaymentdetails.companySystemID,
	erp_advancepaymentdetails.purchaseOrderID 
HAVING
	( ( ( erp_advancepaymentdetails.purchaseOrderID ) IS NOT NULL ) ) 
	) AS advd ON ( erp_purchaseorderadvpayment.poID = advd.purchaseOrderID ) 
	AND ( erp_purchaseorderadvpayment.poAdvPaymentID = advd.poAdvPaymentID ) 
	AND ( erp_purchaseorderadvpayment.companySystemID = advd.companySystemID ) 
WHERE
	(
	( ( erp_purchaseorderadvpayment.companySystemID ) = ' . $paySupplierInvoiceMaster->companySystemID . ' ) 
	AND ( ( erp_purchaseorderadvpayment.supplierID ) = ' . $paySupplierInvoiceMaster->BPVsupplierID . ' ) 
	AND ( ( erp_purchaseorderadvpayment.currencyID ) = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' )
	AND ( ( erp_purchaseorderadvpayment.selectedToPayment ) = 0 ) 
    AND ( ( erp_purchaseorderadvpayment.cancelledYN ) = 0 ) 
	AND ( ( erp_purchaseordermaster.poCancelledYN ) = 0 ) 
	AND ( ( erp_purchaseordermaster.poConfirmedYN ) = 1 ) 
	AND ( ( erp_purchaseordermaster.approved ) =- 1 ) 
	AND ( ( erp_purchaseordermaster.WO_confirmedYN ) = 1 ) 
	AND ( ( erp_purchaseorderadvpayment.fullyPaid ) <> 2 )
	);');
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getADVPaymentForMatchingDocument(Request $request)
    {
        $documentMaster = $this->matchDocumentMasterRepository->findWithoutFail($request["matchDocumentMasterAutoID"]);

        $output = DB::select('SELECT
	erp_purchaseorderadvpayment.poAdvPaymentID,
	erp_purchaseorderadvpayment.companyID,
	erp_purchaseorderadvpayment.companySystemID,
	erp_purchaseorderadvpayment.poID as purchaseOrderID,
	erp_purchaseorderadvpayment.poCode as purchaseOrderCode,
	erp_purchaseorderadvpayment.supplierID,
	erp_purchaseorderadvpayment.narration as comments,
	erp_purchaseorderadvpayment.currencyID,
	currencymaster.CurrencyCode,
	currencymaster.DecimalPlaces,
	IFNULL( erp_purchaseorderadvpayment.reqAmount, 0 ) AS reqAmount,
	( IFNULL( erp_purchaseorderadvpayment.reqAmount, 0 ) - IFNULL( advd.SumOfpaymentAmount, 0 ) ) AS BalanceAmount,
	erp_purchaseordermaster.supplierTransactionCurrencyID as supplierTransCurrencyID,
	erp_purchaseordermaster.supplierTransactionER as supplierTransER,
	erp_purchaseordermaster.supplierDefaultCurrencyID,
	erp_purchaseordermaster.supplierDefaultER as supplierDefaultCurrencyER, 
	erp_purchaseordermaster.localCurrencyID,
	erp_purchaseordermaster.localCurrencyER as localER,
	erp_purchaseordermaster.companyReportingCurrencyID as comRptCurrencyID,
	erp_purchaseordermaster.companyReportingER as comRptER,
	erp_purchaseordermaster.poTotalSupplierTransactionCurrency as poTotalSupplierTransactionCurrency,
	false as isChecked  
FROM
	( ( erp_purchaseorderadvpayment LEFT JOIN currencymaster ON erp_purchaseorderadvpayment.currencyID = currencymaster.currencyID ) INNER JOIN erp_purchaseordermaster ON erp_purchaseorderadvpayment.poID = erp_purchaseordermaster.purchaseOrderID )
	LEFT JOIN (
SELECT
	erp_advancepaymentdetails.poAdvPaymentID,
	erp_advancepaymentdetails.companyID,
	erp_advancepaymentdetails.companySystemID,
	erp_advancepaymentdetails.purchaseOrderID,
	IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount 
FROM
	erp_advancepaymentdetails 
GROUP BY
	erp_advancepaymentdetails.poAdvPaymentID,
	erp_advancepaymentdetails.companySystemID,
	erp_advancepaymentdetails.purchaseOrderID 
HAVING
	( ( ( erp_advancepaymentdetails.purchaseOrderID ) IS NOT NULL ) ) 
	) AS advd ON ( erp_purchaseorderadvpayment.poID = advd.purchaseOrderID ) 
	AND ( erp_purchaseorderadvpayment.poAdvPaymentID = advd.poAdvPaymentID ) 
	AND ( erp_purchaseorderadvpayment.companySystemID = advd.companySystemID ) 
WHERE
	(
	( ( erp_purchaseorderadvpayment.companySystemID ) = ' . $documentMaster->companySystemID . ' ) 
	AND ( ( erp_purchaseorderadvpayment.supplierID ) = ' . $documentMaster->BPVsupplierID . ' ) 
	AND ( ( erp_purchaseorderadvpayment.currencyID ) = ' . $documentMaster->supplierTransCurrencyID . ' )
	AND ( ( erp_purchaseorderadvpayment.selectedToPayment ) = 0 ) 
    AND ( ( erp_purchaseorderadvpayment.cancelledYN ) = 0 ) 
	AND ( ( erp_purchaseordermaster.poCancelledYN ) = 0 ) 
	AND ( ( erp_purchaseordermaster.poConfirmedYN ) = 1 ) 
	AND ( ( erp_purchaseordermaster.approved ) =- 1 ) 
	AND ( ( erp_purchaseordermaster.WO_confirmedYN ) = 1 ) 
	AND ( ( erp_purchaseorderadvpayment.fullyPaid ) <> 2 )
	);');
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getPaymentVoucherMatchItems(Request $request)
    {
        $input = $request->all();

        if ($input['matchType'] == 2 || $input['matchType'] == 1) {
            $user_type =  $input['userType'];

            if($user_type == 1)
            {
                if (!isset($input['BPVsupplierID']) || $input['BPVsupplierID'] == 0) {
                    return $this->sendError('Please select a supplier');
                }
            }
            else{
                if (!isset($input['employeeID']) || $input['employeeID'] == 0) {
                    return $this->sendError('Please select a employee');
                }
            }

        }
        else
        {
            if (!isset($input['BPVsupplierID']) || $input['BPVsupplierID'] == 0) {
                return $this->sendError('Please select a supplier');
            }
        }

     

        if ($input['matchType'] == 1) {

            $user_type =  $input['userType'];

            if($user_type == 1)
            {
                $col = 'BPVsupplierID';
                $val = $input['BPVsupplierID'];
                $invoiceType = 5;
            }
            else{
                $col = 'directPaymentPayeeEmpID';
                $val = $input['employeeID'];
                $invoiceType = 7;
            }

            $invoiceMaster = DB::select('SELECT
                                	MASTER.PayMasterAutoId as masterAutoID,
                                	MASTER.BPVcode as documentCode,
                                	MASTER.BPVdate as docDate,
                                	MASTER.payAmountSuppTrans as transAmount,
                                	MASTER.BPVsupplierID,
                                	currency.CurrencyCode,
                                	currency.DecimalPlaces,
                                	IFNULL(advd.SumOfmatchingAmount, 0) as SumOfmatchingAmount,
                                	(
                                		MASTER .payAmountSuppTrans - IFNULL(advd.SumOfmatchingAmount, 0)
                                	) AS BalanceAmt
                                FROM
                                	erp_paysupplierinvoicemaster AS MASTER
                                INNER JOIN currencymaster AS currency ON currency.currencyID = MASTER .supplierTransCurrencyID
                                LEFT JOIN (
                                	SELECT
                                		erp_matchdocumentmaster.PayMasterAutoId,
                                		erp_matchdocumentmaster.documentSystemID,
                                		erp_matchdocumentmaster.companySystemID,
                                		erp_matchdocumentmaster.BPVcode,
                                        erp_matchdocumentmaster.matchingOption,
                                		COALESCE (
                                			SUM(
                                				erp_matchdocumentmaster.matchingAmount
                                			),
                                			0
                                		) AS SumOfmatchingAmount
                                	FROM
                                		erp_matchdocumentmaster
                                	GROUP BY
                                		erp_matchdocumentmaster.PayMasterAutoId,
                                		erp_matchdocumentmaster.documentSystemID,
                                        erp_matchdocumentmaster.matchingOption
                                ) AS advd ON (
                                	MASTER .PayMasterAutoId = advd.PayMasterAutoId AND MASTER.documentSystemID = advd.documentSystemID AND MASTER.companySystemID = advd.companySystemID AND advd.matchingOption IS NULL
                                )
                                WHERE
                                	approved = - 1
                                AND invoiceType = '.$invoiceType.'    
                                AND matchInvoice <> 2
                                AND MASTER.companySystemID = ' . $input['companySystemID'] . ' AND '.$col.' = ' . $val . ' HAVING (ROUND(BalanceAmt, currency.DecimalPlaces) > 0)');
        } elseif ($input['matchType'] == 2) {

            $user_type =  $input['userType'];

            if($user_type == 1)
            {
                $col = 'supplierID';
                $val = $input['BPVsupplierID'];
            }
            else{
                $col = 'empID';
                $val = $input['employeeID'];
            }
           


            $invoiceMaster = DB::select('SELECT
                MASTER.debitNoteAutoID as masterAutoID,
                MASTER.debitNoteCode as documentCode,
                MASTER.debitNoteDate as docDate,
                MASTER.debitAmountTrans as transAmount,
                MASTER.supplierID,
                currency.CurrencyCode,
                currency.DecimalPlaces,
                IFNULL(advd.SumOfmatchingAmount, 0) AS SumOfmatchingAmount,
                IFNULL(payInvoice.SumOfsupplierPaymentAmount, 0) AS SumOfsupplierPaymentAmount,
                (MASTER .debitAmountTrans - IFNULL(advd.SumOfmatchingAmount, 0) - (IFNULL(payInvoice.SumOfsupplierPaymentAmount, 0) * -1)
                ) AS BalanceAmt
            FROM
                erp_debitnote AS MASTER
            INNER JOIN currencymaster AS currency ON currency.currencyID = MASTER .supplierTransactionCurrencyID
            LEFT JOIN (
                SELECT
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.documentSystemID,
                    erp_matchdocumentmaster.companySystemID,
                    erp_matchdocumentmaster.BPVcode,
                    erp_matchdocumentmaster.matchingOption,
                    erp_matchdocumentmaster.user_type,
                    COALESCE (
                        SUM(
                            erp_matchdocumentmaster.matchingAmount
                        ),
                        0
                    ) AS SumOfmatchingAmount
                FROM
                    erp_matchdocumentmaster
                GROUP BY
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.documentSystemID,
                    erp_matchdocumentmaster.matchingOption
            ) AS advd ON (
                MASTER .debitNoteAutoID = advd.PayMasterAutoId
                AND MASTER .documentSystemID = advd.documentSystemID
                AND MASTER .companySystemID = advd.companySystemID
                AND advd.matchingOption IS NULL
                AND advd.user_type = ' . $user_type . '
            )
            LEFT JOIN (
                SELECT
                    erp_paysupplierinvoicedetail.PayMasterAutoId,
                    erp_paysupplierinvoicedetail.addedDocumentSystemID,
                    erp_paysupplierinvoicedetail.bookingInvSystemCode,
                    erp_paysupplierinvoicedetail.bookingInvDocCode,
                    erp_paysupplierinvoicedetail.companySystemID,
                    Sum(
                        erp_paysupplierinvoicedetail.supplierPaymentAmount
                    ) AS SumOfsupplierPaymentAmount
                FROM
                    erp_paysupplierinvoicedetail
                GROUP BY
                    erp_paysupplierinvoicedetail.addedDocumentSystemID,
                    erp_paysupplierinvoicedetail.bookingInvSystemCode
            ) AS payInvoice ON (
                MASTER.debitNoteAutoID = payInvoice.bookingInvSystemCode
                AND MASTER.documentSystemID = payInvoice.addedDocumentSystemID
                AND MASTER.companySystemID = payInvoice.companySystemID
            )
            WHERE
                approved = - 1
            AND matchInvoice <> 2
            AND type = '.$user_type.'
            AND MASTER.companySystemID = ' . $input['companySystemID'] . '
            AND '.$col.' = ' . $val . '
            HAVING
                (
                    ROUND(
                        BalanceAmt,
                        currency.DecimalPlaces
                    ) > 0
                )');
        }
        elseif ($input['matchType'] == 4) {
            $invoiceMaster = DB::select('SELECT
                MASTER.PayMasterAutoId as masterAutoID,
                MASTER.BPVcode as documentCode,
                MASTER.BPVdate as docDate,
                MASTER.payAmountSuppTrans as transAmount,
                MASTER.BPVsupplierID,
                currency.CurrencyCode,
                currency.DecimalPlaces,
                IFNULL(advd.SumOfmatchingAmount, 0) as SumOfmatchingAmount,
                (
                    MASTER .payAmountSuppTrans - IFNULL(advd.SumOfmatchingAmount, 0)
                ) AS BalanceAmt
            FROM
                erp_paysupplierinvoicemaster AS MASTER
            INNER JOIN currencymaster AS currency ON currency.currencyID = MASTER .supplierTransCurrencyID
            LEFT JOIN (
                SELECT
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.documentSystemID,
                    erp_matchdocumentmaster.companySystemID,
                    erp_matchdocumentmaster.BPVcode,
                    COALESCE (
                        SUM(
                            erp_matchdocumentmaster.matchingAmount
                        ),
                        0
                    ) AS SumOfmatchingAmount
                FROM
                    erp_matchdocumentmaster
                GROUP BY
                    erp_matchdocumentmaster.PayMasterAutoId,
                    erp_matchdocumentmaster.documentSystemID
            ) AS advd ON (
                MASTER .PayMasterAutoId = advd.PayMasterAutoId AND MASTER.documentSystemID = advd.documentSystemID AND MASTER.companySystemID = advd.companySystemID
            )
            WHERE
                approved = - 1
            AND invoiceType = 3    
            AND matchInvoice <> 2
            AND MASTER.companySystemID = ' . $input['companySystemID'] . ' AND BPVsupplierID = ' . $input['BPVsupplierID'] . ' HAVING (ROUND(BalanceAmt, currency.DecimalPlaces) > 0)');
        }
        elseif ($input['matchType'] == 3) {
            $invoiceMaster = DB::select('SELECT
	MASTER.PayMasterAutoId as masterAutoID,
	MASTER.BPVcode as documentCode,
	MASTER.BPVdate as docDate,
	MASTER.payAmountSuppTrans as transAmount,
	MASTER.BPVsupplierID,
	currency.CurrencyCode,
	currency.DecimalPlaces,
	IFNULL(advd.SumOfmatchingAmount, 0) as SumOfmatchingAmount,
	(
		MASTER .payAmountSuppTrans - IFNULL(advd.SumOfmatchingAmount, 0)
	) AS BalanceAmt
FROM
	erp_paysupplierinvoicemaster AS MASTER
INNER JOIN currencymaster AS currency ON currency.currencyID = MASTER .supplierTransCurrencyID
LEFT JOIN (
	SELECT
		erp_matchdocumentmaster.PayMasterAutoId,
		erp_matchdocumentmaster.documentSystemID,
		erp_matchdocumentmaster.companySystemID,
		erp_matchdocumentmaster.BPVcode,
        erp_matchdocumentmaster.matchingOption,
		COALESCE (
			SUM(
				erp_matchdocumentmaster.matchingAmount
			),
			0
		) AS SumOfmatchingAmount
	FROM
		erp_matchdocumentmaster
	GROUP BY
		erp_matchdocumentmaster.PayMasterAutoId,
		erp_matchdocumentmaster.documentSystemID,
        erp_matchdocumentmaster.matchingOption
) AS advd ON (
	MASTER .PayMasterAutoId = advd.PayMasterAutoId AND MASTER.documentSystemID = advd.documentSystemID AND MASTER.companySystemID = advd.companySystemID AND advd.matchingOption = 1
)
WHERE
	approved = - 1
AND invoiceType = 5   
AND advancePaymentTypeID = 0    
AND matchInvoice <> 2
AND MASTER.companySystemID = ' . $input['companySystemID'] . ' AND BPVsupplierID = ' . $input['BPVsupplierID'] . ' HAVING (ROUND(BalanceAmt, currency.DecimalPlaces) > 0)');
        }

        return $this->sendResponse($invoiceMaster, 'Data retrived successfully');
    }

    public
    function paymentVoucherReopen(Request $request)
    {

        DB::beginTransaction();
        try {
            $input = $request->all();

            $id = $input['PayMasterAutoId'];
            $payInvoice = $this->paySupplierInvoiceMasterRepository->findWithoutFail($id);
            $emails = array();
            if (empty($payInvoice)) {
                return $this->sendError('Payment Voucher not found');
            }

            if ($payInvoice->approved == -1) {
                return $this->sendError('You cannot reopen this Payment Voucher it is already fully approved');
            }

            if ($payInvoice->RollLevForApp_curr > 1) {
                return $this->sendError('You cannot reopen this Payment Voucher it is already partially approved');
            }

            if ($payInvoice->confirmedYN == 0) {
                return $this->sendError('You cannot reopen this Payment Voucher, it is not confirmed');
            }

            /*
             * Updating cheque details when reopen the document
             * */
            if ($payInvoice->chequePaymentYN == -1) {
                $this->paySupplierInvoiceMasterRepository->releaseChequeDetails($payInvoice->companySystemID, $payInvoice->BPVAccount, $payInvoice->BPVchequeNo);
            }

            $updateInput = ['confirmedYN' => 0, 'confirmedByEmpSystemID' => null, 'confirmedByEmpID' => null,
                'confirmedByName' => null, 'confirmedDate' => null, 'RollLevForApp_curr' => 1, 'BPVchequeNo' => 0];

            $this->paySupplierInvoiceMasterRepository->update($updateInput, $id);

            $employee = \Helper::getEmployeeInfo();

            $document = DocumentMaster::where('documentSystemID', $payInvoice->documentSystemID)->first();

            $cancelDocNameBody = $document->documentDescription . ' <b>' . $payInvoice->BPVcode . '</b>';
            $cancelDocNameSubject = $document->documentDescription . ' ' . $payInvoice->BPVcode;

            $subject = $cancelDocNameSubject . ' is reopened';

            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

            $documentApproval = DocumentApproved::where('companySystemID', $payInvoice->companySystemID)
                ->where('documentSystemCode', $payInvoice->PayMasterAutoId)
                ->where('documentSystemID', $payInvoice->documentSystemID)
                ->where('rollLevelOrder', 1)
                ->first();

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $companyDocument = CompanyDocumentAttachment::where('companySystemID', $payInvoice->companySystemID)
                        ->where('documentSystemID', $payInvoice->documentSystemID)
                        ->first();

                    if (empty($companyDocument)) {
                        return ['success' => false, 'message' => 'Policy not found for this document'];
                    }

                    $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                        ->where('companySystemID', $documentApproval->companySystemID)
                        ->where('documentSystemID', $documentApproval->documentSystemID);

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

            DocumentApproved::where('documentSystemCode', $id)
                ->where('companySystemID', $payInvoice->companySystemID)
                ->where('documentSystemID', $payInvoice->documentSystemID)
                ->delete();

            $paySupplierInvoice = PaySupplierInvoiceMaster::find($id);
            if ($paySupplierInvoice->BPVbankCurrency == $paySupplierInvoice->localCurrencyID && $paySupplierInvoice->supplierTransCurrencyID == $paySupplierInvoice->localCurrencyID) {
                if ($paySupplierInvoice->chequePaymentYN == -1) {
                    $bankAccount = BankAccount::find($paySupplierInvoice->BPVAccount);
                    if ($bankAccount->isPrintedActive == 1) {
                        $paySupplierInvoice->BPVchequeNo = 0;
                        $paySupplierInvoice->save();
                    }
                } else {
                    $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('PayMasterAutoId', 'ASC')->first();
                    if ($chkCheque) {
                        $paySupplierInvoice->BPVchequeNo = 0;
                        $paySupplierInvoice->save();
                    } else {
                        $paySupplierInvoice->BPVchequeNo = 0;
                        $paySupplierInvoice->save();
                    }
                }

            } else {
                /*return $this->sendError("Cheque number won\'t be generated. The bank currency and the local currency is not equal", 500);*/
            }

            if ($payInvoice->invoiceType == 2 || $payInvoice->invoiceType == 6) {
                $pvDetailExist = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                    ->get();
                foreach ($pvDetailExist as $val) {
                    $updatePayment = ($payInvoice->invoiceType == 2) ? AccountsPayableLedger::find($val->apAutoID) : EmployeeLedger::find($val->apAutoID);
                    if ($updatePayment) {
                        $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                            ->where('apAutoID', $val->apAutoID)
                            ->when(($payInvoice->invoiceType == 6 || $payInvoice->invoiceType == 7), function($query) {
                                $query->whereHas('payment_master', function($query) {
                                    $query->whereIn('invoiceType',[6,7]);
                                });
                            })
                            ->when(($payInvoice->invoiceType != 6 && $payInvoice->invoiceType != 7), function($query) {
                                $query->whereHas('payment_master', function($query) {
                                    $query->where(function($query) {
                                        $query->where('invoiceType', '!=', 6)
                                              ->where('invoiceType', '!=', 7);
                                    });
                                });
                            })
                            ->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

                        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $val->bookingInvSystemCode)->where('documentSystemID', $val->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                        $machAmount = 0;
                        if ($matchedAmount) {
                            $machAmount = $matchedAmount["SumOfmatchedAmount"];
                        }

                        $paymentBalancedAmount = \Helper::roundValue($val->supplierInvoiceAmount - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1)));

                        if ($val->supplierInvoiceAmount == $paymentBalancedAmount) {
                            $updatePayment->selectedToPaymentInv = 1;
                            $updatePayment->save();
                        } else if (($val->supplierInvoiceAmount > $paymentBalancedAmount) && ($val->paymentBalancedAmount > 0)) {
                            $updatePayment->selectedToPaymentInv = 1;
                            $updatePayment->save();
                        }
                    }
                }
            }

            if ($payInvoice->invoiceType == 5) {

                $advancePaymentDetails = AdvancePaymentDetails::where('PayMasterAutoId', $id)->get();
                foreach ($advancePaymentDetails as $val) {
                    $advancePayment = PoAdvancePayment::find($val->poAdvPaymentID);
                    if(isset($advancePayment))
                    {
                        $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                        ->where('companySystemID', $advancePayment->companySystemID)
                        ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                        ->where('purchaseOrderID', $advancePayment->poID)
                        ->first();

                        if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                            $advancePayment->selectedToPayment = 1;
                            $advancePayment->save();
                        }
                    }

          
                }
            }

            /*Audit entry*/
            AuditTrial::createAuditTrial($payInvoice->documentSystemID,$id,$input['reopenComments'],'Reopened');

            DB::commit();
            return $this->sendResponse($payInvoice->toArray(), 'Payment Voucher reopened successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public
    function paymentVoucherCancel(Request $request)
    {
        $payInvoice = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request['PayMasterAutoId']);
        if (empty($payInvoice)) {
            return $this->sendError('Payment Voucher not found');
        }
        $payInvoice->cancelYN = -1;
        $payInvoice->cancelComment = $request['cancelComments'];
        $payInvoice->cancelDate = NOW();
        $payInvoice->cancelledByEmpSystemID = \Helper::getEmployeeSystemID();
        $payInvoice->canceledByEmpID = \Helper::getEmployeeID();
        $payInvoice->save();

        /*Audit entry*/
        AuditTrial::createAuditTrial($payInvoice->documentSystemID,$request['PayMasterAutoId'],$request['cancelComments'],'Cancelled');

        return $this->sendResponse($payInvoice->toArray(), 'Payment Voucher cancelled successfully');

    }

    public
    function updateSentToTreasuryDetail(Request $request)
    {
        $payInvoice = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request['PayMasterAutoId']);

        if (empty($payInvoice)) {
            return $this->sendError('Payment Voucher not found');
        }

        if ($payInvoice->confirmedYN == 0) {
            return $this->sendError('You cannot send to treasury this PV, this is not confirmed');
        }

        if ($payInvoice->approved == -1) {
            return $this->sendError('You cannot send to treasury this PV, this is already approved');
        }

        $employee = \Helper::getEmployeeInfo();

        $payInvoice->chequeSentToTreasury = -1;
        $payInvoice->chequeSentToTreasuryByEmpSystemID = $employee->employeeSystemID;
        $payInvoice->chequeSentToTreasuryByEmpID = $employee->empID;
        $payInvoice->chequeSentToTreasuryByEmpName = $employee->empFullName;
        $payInvoice->chequeSentToTreasuryDate = NOW();
        $payInvoice->save();

        return $this->sendResponse($payInvoice->toArray(), 'Payment Voucher updated successfully');

    }


    public
    function printPaymentVoucher(Request $request)
    {

        $id = $request->get('PayMasterAutoId');

        $PaySupplierInvoiceMasterData = PaySupplierInvoiceMaster::find($id);

        if (empty($PaySupplierInvoiceMasterData)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $output = PaySupplierInvoiceMaster::where('PayMasterAutoId', $id)
            ->with(['project','supplier', 'bankaccount', 'transactioncurrency', 'paymentmode',
                'supplierdetail' => function ($query) {
                    $query->with(['pomaster']);
                }, 'company', 'localcurrency', 'rptcurrency', 'advancedetail', 'confirmed_by', 'directdetail' => function ($query) {
                    $query->with('project','segment');
                }, 'approved_by' => function ($query) {
                    $query->with('employee');
                    $query->where('documentSystemID', 4);
                }, 'created_by', 'cancelled_by','pdc_cheque' => function ($q) {
                    $q->where('documentSystemID', 4);
                }])->first();

        if (empty($output)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $refernaceDoc = \Helper::getCompanyDocRefNo($output->companySystemID, $output->documentSystemID);

        $transDecimal = 2;
        $localDecimal = 3;
        $rptDecimal = 2;

        if ($output->transactioncurrency) {
            $transDecimal = $output->transactioncurrency->DecimalPlaces;
        }

        if ($output->localcurrency) {
            $localDecimal = $output->localcurrency->DecimalPlaces;
        }

        if ($output->rptcurrency) {
            $rptDecimal = $output->rptcurrency->DecimalPlaces;
        }

        $supplierdetailTotTra = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
            ->sum('supplierPaymentAmount');

        $directDetailTotTra = DirectPaymentDetails::where('directPaymentAutoID', $id)
            ->sum('DPAmount');

        $advancePayDetailTotTra = AdvancePaymentDetails::where('PayMasterAutoId', $id)
            ->sum('paymentAmount');

        $isProjectBase = CompanyPolicyMaster::where('companyPolicyCategoryID', 56)
        ->where('companySystemID', $output->companySystemID)
        ->where('isYesNO', 1)
        ->exists();

        $order = array(
            'masterdata' => $output,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'localDecimal' => $localDecimal,
            'rptDecimal' => $rptDecimal,
            'supplierdetailTotTra' => $supplierdetailTotTra,
            'directDetailTotTra' => $directDetailTotTra,
            'isProjectBase' => $isProjectBase,
            'advancePayDetailTotTra' => $advancePayDetailTotTra
        );

        $time = strtotime("now");

        $fileName = 'payment_voucher_' . $id . '_' . $time . '.pdf';
        $html = view('print.payment_voucher', $order);
        $htmlFooter = view('print.payment_voucher_footer', $order);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';

        $mpdf->SetHTMLFooter($htmlFooter);

        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public
    function getPaymentApprovalByUser(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
            ->where('documentSystemID', 4)
            ->first();

        $paymentVoucher = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'erp_paysupplierinvoicemaster.*',
                'employees.empName As created_emp',
                'suppliermaster.supplierName',
                'suppliercurrency.CurrencyCode as supplierCurrencyCode',
                'suppliercurrency.DecimalPlaces as supplierCurrencyDecimalPlaces',
                'bankcurrency.CurrencyCode as bankCurrencyCode',
                'bankcurrency.DecimalPlaces as bankCurrencyCodeDecimalPlaces',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'erp_expenseclaimtype.expenseClaimTypeDescription',
                'erp_documentapproved.documentSystemCode',
                'erp_bankmemosupplier.memoHeaderSupplier',
                'erp_bankmemosupplier.memoDetailSupplier',
                'erp_bankmemopayee.memoHeaderPayee',
                'erp_bankmemopayee.memoDetailPayee',
                'payment_type.description as paymentMode',
                'erp_pdc_logs.chequeNo as pdcChequeNo'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID, $serviceLinePolicy) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    $query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }
                $query->whereIn('employeesdepartments.documentSystemID', [4])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('erp_paysupplierinvoicemaster', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'PayMasterAutoId')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_paysupplierinvoicemaster.companySystemID', $companyId)
                    ->where('erp_paysupplierinvoicemaster.approved', 0)
                    ->where('erp_paysupplierinvoicemaster.confirmedYN', 1);
            })
            ->leftJoin('payment_type', 'erp_paysupplierinvoicemaster.payment_mode', '=', 'payment_type.id')
            ->leftJoin('erp_pdc_logs', function ($join) {
                $join->on('erp_pdc_logs.documentmasterAutoID', '=', 'erp_paysupplierinvoicemaster.PayMasterAutoId')
                    ->where('erp_pdc_logs.id', '=', function ($query) {
                        $query->select('id')
                            ->from('erp_pdc_logs')
                            ->whereColumn('erp_pdc_logs.documentmasterAutoID', '=', 'erp_paysupplierinvoicemaster.PayMasterAutoId')
                            ->limit(1);
                    });
            })
            ->leftjoin('suppliercurrency as suppCurrency', function ($query) use ($companyId) {
                $query->on('erp_paysupplierinvoicemaster.BPVsupplierID', '=', 'suppCurrency.supplierCodeSystem')
                    ->on('erp_paysupplierinvoicemaster.supplierTransCurrencyID', '=', 'suppCurrency.currencyID');
            })
            ->leftJoin(DB::raw('(SELECT group_concat(bankMemoHeader SEPARATOR "|") as memoHeaderSupplier,group_concat(memoDetail SEPARATOR "|") as memoDetailSupplier, supplierCodeSystem, supplierCurrencyID FROM erp_bankmemosupplier INNER JOIN erp_bankmemotypes ON erp_bankmemotypes.bankMemoTypeID = erp_bankmemosupplier.bankMemoTypeID WHERE erp_bankmemosupplier.bankMemoTypeID IN (1,2,4) GROUP BY supplierCodeSystem, supplierCurrencyID ORDER BY sortOrder asc) as erp_bankmemosupplier'), function ($query) use ($companyId) {
                $query->on('erp_paysupplierinvoicemaster.BPVsupplierID', '=', 'erp_bankmemosupplier.supplierCodeSystem')
                    ->on('suppCurrency.supplierCurrencyID', '=', 'erp_bankmemosupplier.supplierCurrencyID');
            })
            ->leftJoin(DB::raw('(SELECT group_concat(bankMemoHeader SEPARATOR "|") as memoHeaderPayee,group_concat(memoDetail SEPARATOR "|") as memoDetailPayee, documentSystemCode, documentSystemID FROM erp_bankmemopayee INNER JOIN erp_bankmemotypes ON erp_bankmemotypes.bankMemoTypeID = erp_bankmemopayee.bankMemoTypeID WHERE erp_bankmemopayee.bankMemoTypeID IN (1,2,4) GROUP BY documentSystemCode, documentSystemID ORDER BY sortOrder asc) as erp_bankmemopayee'), function ($query) use ($companyId) {
                $query->on('erp_paysupplierinvoicemaster.PayMasterAutoId', '=', 'erp_bankmemopayee.documentSystemCode')
                    ->on('erp_paysupplierinvoicemaster.documentSystemID', '=', 'erp_bankmemopayee.documentSystemID');
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'erp_paysupplierinvoicemaster.createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('erp_expenseclaimtype', 'expenseClaimOrPettyCash', 'erp_expenseclaimtype.expenseClaimTypeID')
            ->leftJoin('suppliermaster', 'suppliermaster.supplierCodeSystem', 'erp_paysupplierinvoicemaster.BPVsupplierID')
            ->leftJoin('currencymaster as suppliercurrency', 'suppliercurrency.currencyID', 'supplierTransCurrencyID')
            ->leftJoin('currencymaster as bankcurrency', 'bankcurrency.currencyID', 'BPVbankCurrency')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [4])
            ->where('erp_documentapproved.companySystemID', $companyId);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $paymentVoucher = $paymentVoucher->where(function ($query) use ($search) {
                $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%");
            });
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $paymentVoucher = [];
        }

        return \DataTables::of($paymentVoucher)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('PayMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPaymentApprovedByUser(Request $request)
    {

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $paymentVoucher = DB::table('erp_documentapproved')
            ->select(
                'erp_paysupplierinvoicemaster.*',
                'employees.empName As created_emp',
                'erp_documentapproved.documentApprovedID',
                'suppliermaster.supplierName',
                'suppliercurrency.CurrencyCode as supplierCurrencyCode',
                'suppliercurrency.DecimalPlaces as supplierCurrencyDecimalPlaces',
                'bankcurrency.CurrencyCode as bankCurrencyCode',
                'bankcurrency.DecimalPlaces as bankCurrencyCodeDecimalPlaces',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode',
                'payment_type.description as paymentMode',
                'erp_pdc_logs.chequeNo as pdcChequeNo'
            )->join('erp_paysupplierinvoicemaster', function ($query) use ($companyId) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'PayMasterAutoId')
                    ->where('erp_paysupplierinvoicemaster.companySystemID', $companyId)
                    ->where('erp_paysupplierinvoicemaster.confirmedYN', 1);
            })
            ->leftJoin('payment_type', 'erp_paysupplierinvoicemaster.payment_mode', '=', 'payment_type.id')
            ->leftJoin('erp_pdc_logs', function ($join) {
                $join->on('erp_pdc_logs.documentmasterAutoID', '=', 'erp_paysupplierinvoicemaster.PayMasterAutoId')
                    ->where('erp_pdc_logs.id', '=', function ($query) {
                        $query->select('id')
                            ->from('erp_pdc_logs')
                            ->whereColumn('erp_pdc_logs.documentmasterAutoID', '=', 'erp_paysupplierinvoicemaster.PayMasterAutoId')
                            ->limit(1);
                    });
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('suppliermaster', 'suppliermaster.supplierCodeSystem', 'erp_paysupplierinvoicemaster.BPVsupplierID')
            ->leftJoin('currencymaster as suppliercurrency', 'suppliercurrency.currencyID', 'supplierTransCurrencyID')
            ->leftJoin('currencymaster as bankcurrency', 'bankcurrency.currencyID', 'BPVbankCurrency')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [4])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $paymentVoucher = $paymentVoucher->where(function ($query) use ($search) {
                $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($paymentVoucher)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('PayMasterAutoId', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public
    function referBackPaymentVoucher(Request $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $PayMasterAutoId = $input['PayMasterAutoId'];

            $paymentVoucher = $this->paySupplierInvoiceMasterRepository->findWithoutFail($PayMasterAutoId);
            if (empty($paymentVoucher)) {
                return $this->sendError('Payment Voucher Master not found');
            }

            if ($paymentVoucher->refferedBackYN != -1) {
                return $this->sendError('You cannot amend this document');
            }

            $paymentVoucherArray = $paymentVoucher->toArray();

            $storePVHistory = PaySupplierInvoiceMasterReferback::create($paymentVoucherArray);

            if ($paymentVoucher->invoiceType == 2 || $paymentVoucher->invoiceType == 6) {
                $fetchPVDetails = PaySupplierInvoiceDetail::where('PayMasterAutoId', $PayMasterAutoId)
                    ->get();

                if (!empty($fetchPVDetails)) {
                    foreach ($fetchPVDetails as $pvDetail) {
                        $pvDetail['timesReferred'] = $paymentVoucher->timesReferred;
                    }
                }

                $pvDetailArray = $fetchPVDetails->toArray();

                $storePVDetailHistory = PaySupplierInvoiceDetailReferback::insert($pvDetailArray);
            } else if ($paymentVoucher->invoiceType == 3) {
                $fetchPVDetails = DirectPaymentDetails::where('directPaymentAutoID', $PayMasterAutoId)
                    ->get();

                if (!empty($fetchPVDetails)) {
                    foreach ($fetchPVDetails as $pvDetail) {
                        $pvDetail['timesReferred'] = $paymentVoucher->timesReferred;
                    }
                }

                $pvDetailArray = $fetchPVDetails->toArray();

                $storePVDetailHistory = DirectPaymentReferback::insert($pvDetailArray);
            } else if ($paymentVoucher->invoiceType == 5) {
                $fetchPVDetails = AdvancePaymentDetails::where('PayMasterAutoId', $PayMasterAutoId)
                    ->get();

                if (!empty($fetchPVDetails)) {
                    foreach ($fetchPVDetails as $pvDetail) {
                        $pvDetail['timesReferred'] = $paymentVoucher->timesReferred;
                    }
                }

                $pvDetailArray = $fetchPVDetails->toArray();

                $storePVDetailHistory = AdvancePaymentReferback::insert($pvDetailArray);
            }


            $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucher->companySystemID)
                ->where('documentSystemID', $paymentVoucher->documentSystemID)
                ->get();


            if (!empty($fetchDocumentApproved)) {
                foreach ($fetchDocumentApproved as $DocumentApproved) {
                    $DocumentApproved['refTimes'] = $paymentVoucher->timesReferred;
                }
            }

            $DocumentApprovedArray = $fetchDocumentApproved->toArray();

            $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

            $deleteApproval = DocumentApproved::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucher->companySystemID)
                ->where('documentSystemID', $paymentVoucher->documentSystemID)
                ->delete();

            if ($deleteApproval) {
                $paymentVoucher->refferedBackYN = 0;
                $paymentVoucher->confirmedYN = 0;
                $paymentVoucher->confirmedByEmpSystemID = null;
                $paymentVoucher->confirmedByEmpID = null;
                $paymentVoucher->confirmedDate = null;
                $paymentVoucher->RollLevForApp_curr = 1;
                $paymentVoucher->BPVchequeNo = 0;
                $paymentVoucher->chequePrintedYN = 0;
                $paymentVoucher->save();
            }

            DB::commit();
            return $this->sendResponse($paymentVoucher->toArray(), 'Payment Voucher amended successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function amendPaymentVoucherReview(Request $request)
    {
        $input = $request->all();

        $PayMasterAutoId = $input['PayMasterAutoId'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $paymentVoucherData = $this->paySupplierInvoiceMasterRepository->findWithoutFail($PayMasterAutoId);
        if (empty($paymentVoucherData)) {
            return $this->sendError('Payment Voucher Master not found');
        }

        $documentAutoId = $PayMasterAutoId;
        $documentSystemID = $paymentVoucherData->documentSystemID;

        $checkBalance = GeneralLedgerService::validateDebitCredit($documentSystemID, $documentAutoId);
        if (!$checkBalance['status']) {
            $allowValidateDocumentAmend = false;
        } else {
            $allowValidateDocumentAmend = true;
        }

        if($paymentVoucherData->approved == -1){
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

            $validateVatReturnFilling = ValidateDocumentAmend::validateVatReturnFilling($documentAutoId,$documentSystemID,$paymentVoucherData->companySystemID);
            if(isset($validateVatReturnFilling['status']) && $validateVatReturnFilling['status'] == false){
                $errorMessage = "Payment Voucher " . $validateVatReturnFilling['message'];
                return $this->sendError($errorMessage);
            }
        }


        if ($paymentVoucherData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend, this payment voucher, it is not confirmed');
        }

        /*       // checking document matched in matchmaster
               $checkDetailExistMatch = PaySupplierInvoiceDetail::where('bookingInvSystemCode', $PayMasterAutoId)
                   ->where('companySystemID', $paymentVoucherData->companySystemID)
                   ->where('addedDocumentSystemID', $paymentVoucherData->documentSystemID)
                   ->first();

               if ($checkDetailExistMatch) {
                   return $this->sendError('Cannot return back to amend. payment voucher is added to matching');
               }*/

        // checking document matched in matchmaster
        $checkDetailExistMatch = MatchDocumentMaster::where('PayMasterAutoId', $PayMasterAutoId)
            ->where('companySystemID', $paymentVoucherData->companySystemID)
            ->where('documentSystemID', $paymentVoucherData->documentSystemID)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError('You cannot return back to amend. this payment voucher is added to matching');
        }

        $checkBLDataExist = BankLedger::where('documentSystemCode', $PayMasterAutoId)
            ->where('companySystemID', $paymentVoucherData->companySystemID)
            ->where('documentSystemID', $paymentVoucherData->documentSystemID)
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

        $emailBody = '<p>' . $paymentVoucherData->BPVcode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';

        $emailSubject = $paymentVoucherData->BPVcode . ' has been return back to amend';

        DB::beginTransaction();
        try {

            //sending email to relevant party
            if ($paymentVoucherData->confirmedYN == 1) {
                $emails[] = array('empSystemID' => $paymentVoucherData->confirmedByEmpSystemID,
                    'companySystemID' => $paymentVoucherData->companySystemID,
                    'docSystemID' => $paymentVoucherData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $paymentVoucherData->BPVcode);
            }

            $documentApproval = DocumentApproved::where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemCode', $PayMasterAutoId)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->get();

            foreach ($documentApproval as $da) {
                if ($da->approvedYN == -1) {
                    $emails[] = array('empSystemID' => $da->employeeSystemID,
                        'companySystemID' => $paymentVoucherData->companySystemID,
                        'docSystemID' => $paymentVoucherData->documentSystemID,
                        'alertMessage' => $emailSubject,
                        'emailAlertMessage' => $emailBody,
                        'docSystemCode' => $paymentVoucherData->BPVcode);
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            $deleteApproval = DocumentApproved::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->delete();

            //deleting from general ledger table
            $deleteGLData = GeneralLedger::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->delete();

            //deleting records from accounts payable
            $deleteAPData = AccountsPayableLedger::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->delete();

            //deleting records from employee ledger
            $deleteELData = EmployeeLedger::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->delete();


            //deleting records from tax ledger
            $deleteTaxLedgerData = TaxLedger::where('documentMasterAutoID', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->whereNull('matchDocumentMasterAutoID')
                ->delete();

            $taxLedgerDetails = TaxLedgerDetail::where('documentMasterAutoID', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->whereNull('matchDocumentMasterAutoID')
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

            BudgetConsumedData::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->delete();

            if ($paymentVoucherData->invoiceType == 3) {
                if ($paymentVoucherData->expenseClaimOrPettyCash == 6 || $paymentVoucherData->expenseClaimOrPettyCash == 7) {

                    //deleting records from customer receive voucher master
                    $deleteCRVData = CustomerReceivePayment::where('custReceivePaymentAutoID', $PayMasterAutoId)
                        ->where('companySystemID', $paymentVoucherData->interCompanyToSystemID)
                        ->where('documentSystemID', 21)
                        ->delete();

                    //deleting records from customer receive voucher detail
                    $deleteCRVDetailData = DirectReceiptDetail::where('directReceiptAutoID', $PayMasterAutoId)
                        ->where('companySystemID', $paymentVoucherData->interCompanyToSystemID)
                        ->delete();
                } else {
                    //deleting records from customer receive voucher master
                    $deleteCRVData = CustomerReceivePayment::where('PayMasterAutoId', $PayMasterAutoId)
                        ->where('companySystemID', $paymentVoucherData->companySystemID)
                        ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                        ->delete();
                }
            }

            //deleting records from bank ledger
            $deleteBLData = BankLedger::where('documentSystemCode', $PayMasterAutoId)
                ->where('companySystemID', $paymentVoucherData->companySystemID)
                ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                ->delete();

            /*
             * Updating cheque details when amend the document
             * */
            if ($paymentVoucherData->chequePaymentYN == -1) {
                $this->paySupplierInvoiceMasterRepository->releaseChequeDetails($paymentVoucherData->companySystemID, $paymentVoucherData->BPVAccount, $paymentVoucherData->BPVchequeNo);
            }
            // updating fields
            $paymentVoucherData->confirmedYN = 0;
            $paymentVoucherData->confirmedByEmpSystemID = null;
            $paymentVoucherData->confirmedByEmpID = null;
            $paymentVoucherData->confirmedByName = null;
            $paymentVoucherData->confirmedDate = null;
            $paymentVoucherData->RollLevForApp_curr = 1;

            $paymentVoucherData->approved = 0;
            $paymentVoucherData->approvedByUserSystemID = null;
            $paymentVoucherData->approvedByUserID = null;
            $paymentVoucherData->approvedDate = null;
            $paymentVoucherData->postedDate = null;
            $paymentVoucherData->BPVchequeNo = 0;
            $paymentVoucherData->chequePrintedYN = 0;
            $paymentVoucherData->save();

            $cheqkPrintedPdcs = PdcLog::where('documentmasterAutoID', $PayMasterAutoId)
                                      ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                                      ->where('chequePrinted', 1)
                                      ->get();

            if (count($cheqkPrintedPdcs) > 0) {
                foreach ($cheqkPrintedPdcs as $key => $value) {
                    $printHistory = [
                        'pdcLogID' => $value->id,
                        'chequePrintedBy' => $value->chequePrintedBy,
                        'chequePrintedDate' => $value->chequePrintedDate,
                        'changedBy' => $employee->employeeSystemID,
                        'documentSystemID' => $value->documentSystemID,
                        'documentmasterAutoID' => $value->documentmasterAutoID,
                        'amount' => $value->amount,
                        'currencyID' => $value->currencyID,
                        'chequeNo' => $value->chequeNo,
                    ];

                    $res = PdcLogPrintedHistory::create($printHistory);

                    PdcLog::where('id', $value->id)->update(['chequePrintedBy' => null, 'chequePrintedDate' => null, 'chequePrinted' => 0]);

                    $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $value->companySystemID)
                                                                ->where('companyPolicyCategoryID', 35)
                                                                ->where('isYesNO', 1)
                                                                ->first();
                    if (!empty($is_exist_policy_GCNFCR)) {
                        $check_registry = [
                            'isPrinted' => 0,
                            'cheque_printed_at' => null,
                            'cheque_print_by' => null
                        ];
                        ChequeRegisterDetail::where('cheque_no', $value->chequeNo)
                            ->where('company_id', $value->companySystemID)
                            ->where('document_id', $value->documentmasterAutoID)
                            ->update($check_registry);
                    }
                }
            }

            AuditTrial::createAuditTrial($paymentVoucherData->documentSystemID,$PayMasterAutoId,$input['returnComment'],'returned back to amend');

            $this->expenseAssetAllocationRepository->deleteExpenseAssetAllocation($PayMasterAutoId, $paymentVoucherData->documentSystemID);

            DB::commit();
            return $this->sendResponse($paymentVoucherData->toArray(), 'Payment voucher return back to amend successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public
    function amendPaymentVoucherPreCheck(Request $request)
    {
        $input = $request->all();

        $PayMasterAutoId = $input['PayMasterAutoId'];

        $paymentVoucherData = $this->paySupplierInvoiceMasterRepository->findWithoutFail($PayMasterAutoId);
        if (empty($paymentVoucherData)) {
            return $this->sendError('Payment Voucher Master not found');
        }


        if ($paymentVoucherData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend, this payment voucher, it is not confirmed');
        }

        // checking document matched in matchmaster
        $checkDetailExistMatch = MatchDocumentMaster::where('PayMasterAutoId', $PayMasterAutoId)
            ->where('companySystemID', $paymentVoucherData->companySystemID)
            ->where('documentSystemID', $paymentVoucherData->documentSystemID)
            ->first();

        if ($checkDetailExistMatch) {
            return $this->sendError('You cannot return back to amend. this payment voucher is added to matching');
        }

        $checkBLDataExist = BankLedger::where('documentSystemCode', $PayMasterAutoId)
            ->where('companySystemID', $paymentVoucherData->companySystemID)
            ->where('documentSystemID', $paymentVoucherData->documentSystemID)
            ->first();


        if ($checkBLDataExist) {
            if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == 0 && $checkBLDataExist->pulledToBankTransferYN == 0) {
                return $this->sendError('Treasury cleared, You cannot return back to amend.', 404,['type' => 'error']);
            } else if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == -1 && $checkBLDataExist->pulledToBankTransferYN == 0) {
                return $this->sendError('Bank cleared. You cannot return back to amend.', 404,['type' => 'error']);
            } else if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == 0 && $checkBLDataExist->pulledToBankTransferYN == -1) {
                return $this->sendError('Added to bank transfer. You cannot return back to amend.', 404,['type' => 'error']);
            } else if ($checkBLDataExist->trsClearedYN == -1 && $checkBLDataExist->bankClearedYN == -1 && $checkBLDataExist->pulledToBankTransferYN == -1) {
                return $this->sendError('Added to bank transfer and bank cleared. You cannot return back to amend.', 404, ['type' => 'error']);
            } else if ($checkBLDataExist->trsClearedYN == 0 && $checkBLDataExist->bankClearedYN == 0 && $checkBLDataExist->pulledToBankTransferYN == -1) {
                return $this->sendError('Added to bank transfer. You cannot return back to amend.', 404, ['type' => 'error']);
            }
        }

        if ($paymentVoucherData->pdcChequeYN) {
            $cheqkPrintedPdcs = PdcLog::where('documentmasterAutoID', $PayMasterAutoId)
                                      ->where('documentSystemID', $paymentVoucherData->documentSystemID)
                                      ->where('chequePrinted', 1)
                                      ->count();

            if ($cheqkPrintedPdcs == 1) {
                return $this->sendError('The PDC cheque is already printed.', 404, ['type' => 'warning']);
            } else if ($cheqkPrintedPdcs > 1) {
                return $this->sendError('The PDC cheques are already printed.', 404, ['type' => 'warning']);
            }
        }

        return $this->sendResponse($paymentVoucherData, 'Payment voucher pre checked successfully');
    }

    public function updateBankBalance(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {

        $input = $this->convertArrayToValue($input);
        $id = $input['PayMasterAutoId'];
        if(isset($input['BPVAccount']))
        {
            if(!empty($input['BPVAccount']) )
            {

                $bankAccount = BankAccount::find($input['BPVAccount']);
                if ($bankAccount) {
                    $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;

                }
                else
                {
                    return $this->sendError('Bank currency not found');
                }


                $bank_currency = $input['BPVbankCurrency'];
                $document_currency = $input['supplierTransCurrencyID'];

                $cur_det['companySystemID'] = $input['companySystemID'];
                $cur_det['bankmasterAutoID'] = $input['BPVbank'];
                $cur_det['bankAccountAutoID'] = $input['BPVAccount'];
                $cur_det_info =  (object)$cur_det;

                $bankBalance = app('App\Http\Controllers\API\BankAccountAPIController')->getBankAccountBalanceSummery($cur_det_info);

                $amount = $bankBalance['netBankBalance'];
                $currencies = CurrencyMaster::where('currencyID','=',$document_currency)->select('DecimalPlaces')->first();

                $rounded_amount =  number_format($amount,$currencies->DecimalPlaces,'.', '');
                
                $details['bankAccountBalance'] = $rounded_amount;

                $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($details, $id);
                DB::commit();
                return $this->sendResponse($paySupplierInvoiceMaster, 'successfully updated');

            }
        }
      
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

  
       
       
    }

    public function getAvailableChequeNumbers(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $chequeRegisterData = ChequeRegister::where('bank_id',$input['bankID'])
            ->where('bank_account_id',$input['accountID'])
            ->where('company_id',$input['company_id'])
            ->where('isActive',1)
            ->first();

        $checkRegisterDetails = ChequeRegisterDetail::where('cheque_register_master_id',$chequeRegisterData['id'])
            ->where('company_id',$input['company_id'])
            ->where('status',0)
            ->get();

        if(isset($input['documentAutoID'])) {
            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($input['documentAutoID']);

            if(!empty($paySupplierInvoiceMaster['BPVchequeNo'])) {
                $chequeRegisterData = ChequeRegister::where('bank_id',$paySupplierInvoiceMaster['BPVbank'])
                    ->where('bank_account_id',$paySupplierInvoiceMaster['BPVAccount'])
                    ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                    ->where('started_cheque_no', '<=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                    ->where('ended_cheque_no', '>=' ,$paySupplierInvoiceMaster['BPVchequeNo'])
                    ->first();

                $checkRegisterDetailsOldRecord = ChequeRegisterDetail::where('cheque_register_master_id',$chequeRegisterData->id)
                    ->where('company_id',$paySupplierInvoiceMaster['companySystemID'])
                    ->where('cheque_no',$paySupplierInvoiceMaster['BPVchequeNo'])
                    ->first();

                $checkRegisterDetails->push($checkRegisterDetailsOldRecord);

                $sorted = $checkRegisterDetails->sortBy('cheque_no');
                $checkRegisterDetails = $sorted->values()->all();
            }
        }

        return $this->sendResponse($checkRegisterDetails, 'Data fetched successfully');
    }


}

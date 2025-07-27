<?php
/**
 * =============================================
 * -- File Name : BankLedgerAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Ledger
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - September 2018
 * -- Description : This file contains the all CRUD for Bank Ledger
 * -- REVISION HISTORY
 * -- Date: 19-September 2018 By: Fayas Description: Added new functions named as getBankReconciliationsByType()
 * -- Date: 27-September 2018 By: Fayas Description: Added new functions named as getBankAccountPaymentReceiptByType()
 * -- Date: 03-October 2018 By: Fayas Description: Added new functions named as getPaymentsByBankTransfer()
 * -- Date: 30-October 2018 By: Fayas Description: Added new functions named as getChequePrintingItems()
 * -- Date: 31-October 2018 By: Fayas Description: Added new functions named as getChequePrintingFormData(),printChequeItems()
 * -- Date: 16-November 2018 By: Fayas Description: Added new functions named as updateTreasuryCollection()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankLedgerAPIRequest;
use App\Http\Requests\API\UpdateBankLedgerAPIRequest;
use App\Models\AdvancePaymentDetails;
use App\Models\Alert;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\Models\CurrencyMaster;
use App\Models\BankMaster;
use App\Models\BankMemoSupplier;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationRefferedBack;
use App\Models\ChequeRegisterDetail;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerReceivePayment;
use App\Models\DocumentApproved;
use App\Models\DirectPaymentDetails;
use App\Models\GeneralLedger;
use App\Models\PaymentBankTransfer;
use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SupplierContactDetails;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Repositories\BankLedgerRepository;
use App\Repositories\BankReconciliationRepository;
use App\Repositories\CustomerReceivePaymentRepository;
use App\Repositories\PaymentBankTransferRefferedBackRepository;
use App\Repositories\PaymentBankTransferRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;
use App\Models\ChequeTemplateBank;
use App\Jobs\DocumentAttachments\PaymentReleasedToSupplierJob;
use App\helper\CreateExcel;
use App\Services\BankLedger\BankLedgerService;
use App\Jobs\Report\BankLedgerPdfJob;
use App\Models\BankStatementDetail;

/**
 * Class BankLedgerController
 * @package App\Http\Controllers\API
 */
class BankLedgerAPIController extends AppBaseController
{
    /** @var  BankLedgerRepository */
    private $bankLedgerRepository;
    private $bankReconciliationRepository;
    private $paymentBankTransferRepository;
    private $paySupplierInvoiceMasterRepository;
    private $customerReceivePaymentRepository;
    private $paymentBankTransferRefferedBackRepository;

    public function __construct(BankLedgerRepository $bankLedgerRepo, BankReconciliationRepository $bankReconciliationRepo,
                                PaymentBankTransferRepository $paymentBankTransferRepo,
                                PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo,
                                CustomerReceivePaymentRepository $customerReceivePaymentRepo,
                                PaymentBankTransferRefferedBackRepository $paymentBankTransferRefferedBackRepo)
    {
        $this->bankLedgerRepository = $bankLedgerRepo;
        $this->bankReconciliationRepository = $bankReconciliationRepo;
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
        $this->paymentBankTransferRefferedBackRepository = $paymentBankTransferRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankLedgers",
     *      summary="Get a listing of the BankLedgers.",
     *      tags={"BankLedger"},
     *      description="Get all BankLedgers",
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
     *                  @SWG\Items(ref="#/definitions/BankLedger")
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
        $this->bankLedgerRepository->pushCriteria(new RequestCriteria($request));
        $this->bankLedgerRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankLedgers = $this->bankLedgerRepository->all();

        return $this->sendResponse($bankLedgers->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_ledgers')]));
    }

    /**
     * @param CreateBankLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankLedgers",
     *      summary="Store a newly created BankLedger in storage",
     *      tags={"BankLedger"},
     *      description="Store BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankLedger that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankLedger")
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
     *                  ref="#/definitions/BankLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankLedgerAPIRequest $request)
    {
        $input = $request->all();

        $bankLedgers = $this->bankLedgerRepository->create($input);

        return $this->sendResponse($bankLedgers->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_ledgers')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankLedgers/{id}",
     *      summary="Display the specified BankLedger",
     *      tags={"BankLedger"},
     *      description="Get BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
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
     *                  ref="#/definitions/BankLedger"
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
        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_ledgers')]));
        }

        return $this->sendResponse($bankLedger->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_ledgers')]));
    }

    /**
     * @param int $id
     * @param UpdateBankLedgerAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankLedgers/{id}",
     *      summary="Update the specified BankLedger in storage",
     *      tags={"BankLedger"},
     *      description="Update BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankLedger that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankLedger")
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
     *                  ref="#/definitions/BankLedger"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->with(['bank_account', 'reporting_currency'])->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_ledgers')]));
        }

        /** Validate bank reconciliation auto match */
        $ExsistsInMatching = BankStatementDetail::where('bankLedgerAutoID', $input['bankLedgerAutoID'])->first();
        if ($ExsistsInMatching) {
            return $this->sendError('Selected document in matching process', 500);
        }

        $employee = \Helper::getEmployeeInfo();
        $updateArray = array();

        if (array_key_exists('editType', $input)) {

            if ($input['editType'] == 1) {

                $bankReconciliation = $this->bankReconciliationRepository->findWithoutFail($input['bankRecAutoID']);

                if (empty($bankReconciliation)) {
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_reconciliation')]));
                }

                if ($bankReconciliation->confirmedYN == 1) {
                    return $this->sendError(trans('custom.you_cannot_edit_this_document_already_confirmed'), 500);
                }

                $checkGLAmount = GeneralLedger::selectRaw('SUM(documentRptAmount) as documentRptAmount, reportingCurrency.DecimalPlaces as DecimalPlaces')
                    ->join('currencymaster as reportingCurrency', 'reportingCurrency.currencyID', '=', 'documentRptCurrencyID')
                    ->where('companySystemID', $bankLedger->companySystemID)
                    ->where('documentSystemID', $bankLedger->documentSystemID)
                    ->where('documentSystemCode', $bankLedger->documentSystemCode)
                    ->when($bankLedger->pdcID > 0, function($query) use ($bankLedger) {
                        $query->where('pdcID', $bankLedger->pdcID);
                    })
                    ->where('chartOfAccountSystemID', $bankLedger->bank_account->chartOfAccountSystemID)
                    ->first();

                if (!empty($checkGLAmount)) {
                    $glAmount = 0;
                    $conditionChecking = true;
                    $glAmount = $checkGLAmount->documentRptAmount;
                    $a = abs(round($bankLedger->payAmountCompRpt, $bankLedger->reporting_currency->DecimalPlaces));
                    $b = abs(round($glAmount,$checkGLAmount->DecimalPlaces));
                    $epsilon = 0.00001;
                    if ((abs($a-$b) > $epsilon)) {
                        return $this->sendError(trans('custom.bank_amount_is_not_matching_with_gl_amount'), 500);
                    }
                } else {
                    return $this->sendError(trans('custom.gl_data_cannot_be_found_for_this_document'), 500);
                }

                $updateArray['bankClearedYN'] = ($input['bankClearedYN'])
                    ? ($bankLedger->trsClearedYN == -1 ? -1 : 0)
                    : 0;

                if(!$bankLedger->trsClearedYN) {
                    return $this->sendError('Treasury not cleared for this document '.$bankLedger->documentCode, 500);
                }

                if ($updateArray['bankClearedYN']) {
                    $updateArray['bankClearedAmount'] = $bankLedger->payAmountBank;
                    $updateArray['bankClearedByEmpName'] = $employee->empName;
                    $updateArray['bankClearedByEmpID'] = $employee->empID;
                    $updateArray['bankClearedByEmpSystemID'] = $employee->employeeSystemID;
                    $updateArray['bankClearedDate'] = now();
                    $updateArray['bankRecAutoID'] = $input['bankRecAutoID'];
                    $updateArray['bankreconciliationDate'] = $bankReconciliation->bankRecAsOf;
                    $updateArray['bankRecYear'] = $bankReconciliation->year;
                    $updateArray['bankrecMonth'] = $bankReconciliation->month;
                } else {
                    $updateArray['bankClearedAmount'] = 0;
                    $updateArray['bankClearedByEmpName'] = null;
                    $updateArray['bankClearedByEmpID'] = null;
                    $updateArray['bankClearedByEmpSystemID'] = null;
                    $updateArray['bankClearedDate'] = null;
                    $updateArray['bankRecAutoID'] = null;
                    $updateArray['bankreconciliationDate'] = null;
                    $updateArray['bankRecYear'] = null;
                    $updateArray['bankrecMonth'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);

                $bankRecReceiptAmount = BankLedger::where('bankRecAutoID', $input['bankRecAutoID'])
                    ->where('bankClearedYN', -1)
                    ->where('payAmountBank', '<', 0)
                    ->sum('bankClearedAmount');

                $bankRecPaymentAmount = BankLedger::where('bankRecAutoID', $input['bankRecAutoID'])
                    ->where('bankClearedYN', -1)
                    ->where('payAmountBank', '>', 0)
                    ->sum('bankClearedAmount');

                $closingAmount = $bankReconciliation->openingBalance + ($bankRecReceiptAmount * -1) - $bankRecPaymentAmount;

                $inputNew = array('closingBalance' => $closingAmount);
                $this->bankReconciliationRepository->update($inputNew, $input['bankRecAutoID']);
            } else if ($input['editType'] == 2) {

                if ($bankLedger->bankClearedYN == -1) {
                    return $this->sendError(trans('custom.you_cannot_edit_this_item_already_added_to_bank_reconciliation'), 500);
                }

                if ($input['trsCollectedYN']) {
                    $updateArray['trsCollectedYN'] = -1;
                } else {
                    $updateArray['trsCollectedYN'] = 0;
                }

                if ($updateArray['trsCollectedYN']) {
                    $updateArray['trsCollectedByEmpName'] = $employee->empName;
                    $updateArray['trsCollectedByEmpID'] = $employee->empID;
                    $updateArray['trsCollectedByEmpSystemID'] = $employee->employeeSystemID;
                    $updateArray['trsCollectedDate'] = now();
                } else {
                    $updateArray['trsCollectedByEmpName'] = null;
                    $updateArray['trsCollectedByEmpID'] = null;
                    $updateArray['trsCollectedByEmpSystemID'] = null;
                    $updateArray['trsCollectedDate'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);
            } else if ($input['editType'] == 3) {

                if ($bankLedger->pulledToBankTransferYN == -1) {
                    return $this->sendError(trans('custom.you_cannot_edit_this_payment_already_added_to_bank_transfer'), 500);
                }

                if ($bankLedger->bankClearedYN == -1) {
                    return $this->sendError(trans('custom.you_cannot_edit_this_document_is_already_added_to_bank_reconciliation'), 500);
                }

                if ($input['trsClearedYN']) {
                    $updateArray['trsClearedYN'] = -1;
                } else {
                    $updateArray['trsClearedYN'] = 0;
                }

                if ($updateArray['trsClearedYN'] == -1) {

                    $bankGLCode = 000;

                    if ($bankLedger->bank_account) {
                        $bankGLCode = $bankLedger->bank_account->chartOfAccountSystemID;
                    }
                    

                    $checkGLAmount = GeneralLedger::selectRaw('SUM(documentRptAmount) as documentRptAmount,reportingCurrency.DecimalPlaces as DecimalPlaces')
                        ->join('currencymaster as reportingCurrency', 'reportingCurrency.currencyID', '=', 'documentRptCurrencyID')
                        ->where('companySystemID', $bankLedger->companySystemID)
                        ->where('documentSystemID', $bankLedger->documentSystemID)
                        ->where('documentSystemCode', $bankLedger->documentSystemCode)
                        ->when($bankLedger->pdcID > 0, function($query) use ($bankLedger) {
                            $query->where('pdcID', $bankLedger->pdcID);
                        })
                        ->where('chartOfAccountSystemID', $bankGLCode)
                        ->first();

                    if (!empty($checkGLAmount)) {
                        $glAmount = 0;
                        $conditionChecking = true;
                        $glAmount = $checkGLAmount->documentRptAmount;
                        $a = abs(round($bankLedger->payAmountCompRpt, $bankLedger->reporting_currency->DecimalPlaces));
                        $b = abs(round($glAmount,$checkGLAmount->DecimalPlaces));
                        $epsilon = 0.00001;
                        if ((abs($a-$b) > $epsilon)) {
                            return $this->sendError(trans('custom.bank_amount_is_not_matching_with_gl_amount'), 500);
                        }
                    } else {
                        return $this->sendError(trans('custom.gl_data_cannot_be_found_for_this_document'), 500);
                    }
                }



                if (isset($input['dateChange']) && $input['dateChange']) {
                    if (Carbon::parse($input['trsClearedDate']) > Carbon::now()) {
                        return $this->sendError("Clear date cannot be a future date", 500);
                    }

                    $bankReconciliation = BankReconciliation::where('companySystemID', $bankLedger->companySystemID)
                                                           ->where('bankAccountAutoID', $bankLedger->bankAccountID)
                                                           ->orderBy('bankRecAutoID', 'desc')
                                                           ->first();

                    if ($bankReconciliation && Carbon::parse($bankReconciliation->bankRecAsOf) > Carbon::parse($input['trsClearedDate'])) {
                        return $this->sendError("Clear date cannot be a less than last reconciliation date. Last reconciliation date - ".Carbon::parse($bankReconciliation->bankRecAsOf)->format('d/m/Y'), 500);
                    }


                    $updateArray['trsClearedDate'] = Carbon::parse($input['trsClearedDate']);
                } else {
                    if ($updateArray['trsClearedYN']) {
                        $updateArray['trsClearedAmount'] = $bankLedger->payAmountBank;
                        $updateArray['trsClearedByEmpName'] = $employee->empName;
                        $updateArray['trsClearedByEmpID'] = $employee->empID;
                        $updateArray['trsClearedByEmpSystemID'] = $employee->employeeSystemID;
                        $updateArray['trsClearedDate'] = now();


                        // email sent to supplier
                        if ($bankLedger->payAmountBank > 0 && ($bankLedger->invoiceType == 2 || $bankLedger->invoiceType == 5)) {

                            $supplierDefaultDetail = SupplierContactDetails::where('supplierID', $bankLedger->payeeID)
                                ->where('isDefault', -1)
                                ->first();

                            $supplierMaster = SupplierMaster::find($bankLedger->payeeID);
                            $supplierEmail = "";

                            $company = Company::where('companySystemID', $bankLedger->companySystemID)->first();

                            if (!empty($supplierDefaultDetail)) {
                                $supplierEmail = $supplierDefaultDetail->contactPersonEmail;
                            } else {
                                if (!empty($supplierMaster)) {
                                    $supplierEmail = $supplierMaster->supEmail;
                                }
                            }

                            $paySupplierInvoice = PaySupplierInvoiceMaster::with(['supplier', 'transactioncurrency',
                                                        'supplierdetail','company', 'localcurrency', 'rptcurrency', 'advancedetail', 'confirmed_by'])
                                                        ->find($bankLedger->documentSystemCode);

                            $confirmedPersonEmail = "";
                            $invoiceNumbers = "";

                            if (!empty($paySupplierInvoice)) {

                                if ($paySupplierInvoice->confirmed_by) {
                                    $confirmedPersonEmail = $paySupplierInvoice->confirmed_by->empEmail;
                                    if ($supplierEmail == "") {
                                        $supplierEmail = $confirmedPersonEmail;
                                    }
                                }

                                if ($bankLedger->invoiceType == 2) {
                                    $details = $paySupplierInvoice->supplierdetail;
                                    foreach ($details as $detail) {
                                        $invoiceNumbers = $invoiceNumbers . '<br>' . $detail['supplierInvoiceNo'];
                                    }
                                } else if ($bankLedger->invoiceType == 5) {
                                    $details = $paySupplierInvoice->advancedetail;
                                    foreach ($details as $detail) {
                                        $invoiceNumbers = $invoiceNumbers . '<br>' . $detail['purchaseOrderCode'];
                                    }
                                }

                            }


                            if ($supplierEmail && $confirmedPersonEmail) {

                                $pdfName = '/PV_REMIT_'.$bankLedger->companyID.'_'.$bankLedger->documentSystemCode.'.pdf';

                                $refernaceDoc = \Helper::getCompanyDocRefNo($paySupplierInvoice->companySystemID, $paySupplierInvoice->documentSystemID);

                                $transDecimal = 2;
                                $localDecimal = 3;
                                $rptDecimal = 2;

                                if ($paySupplierInvoice->transactioncurrency) {
                                    $transDecimal = $paySupplierInvoice->transactioncurrency->DecimalPlaces;
                                }

                                if ($paySupplierInvoice->localcurrency) {
                                    $localDecimal = $paySupplierInvoice->localcurrency->DecimalPlaces;
                                }

                                if ($paySupplierInvoice->rptcurrency) {
                                    $rptDecimal = $paySupplierInvoice->rptcurrency->DecimalPlaces;
                                }

                                $supplierdetailTotTra = PaySupplierInvoiceDetail::where('PayMasterAutoId', $bankLedger->documentSystemCode)
                                    ->sum('supplierPaymentAmount');

                                $directDetailTotTra = DirectPaymentDetails::where('directPaymentAutoID', $bankLedger->documentSystemCode)
                                    ->sum('DPAmount');

                                $advancePayDetailTotTra = AdvancePaymentDetails::where('PayMasterAutoId', $bankLedger->documentSystemCode)
                                    ->sum('paymentAmount');


                                $order = array(
                                    'masterdata' => $paySupplierInvoice,
                                    'docRef' => $refernaceDoc,
                                    'transDecimal' => $transDecimal,
                                    'localDecimal' => $localDecimal,
                                    'rptDecimal' => $rptDecimal,
                                    'supplierdetailTotTra' => $supplierdetailTotTra,
                                    'directDetailTotTra' => $directDetailTotTra,
                                    'advancePayDetailTotTra' => $advancePayDetailTotTra
                                );

                                $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" . "<br>This is an auto generated email. Please do not reply to this email because we are not" . "monitoring this inbox.</font>";

                                $dataEmail = array();
                                $dataEmail['empName'] = $supplierMaster->nameOnPaymentCheque;
                                $dataEmail['empEmail'] = $supplierEmail;


                                $dataEmail['empSystemID'] = $employee->employeeSystemID;
                                $dataEmail['empID']       = $employee->empID;

                                $dataEmail['companySystemID'] = $bankLedger->companySystemID;
                                $dataEmail['companyID'] = $bankLedger->companyID;

                                $dataEmail['docID'] = 'SUPPLIEREMAIL';
                                $dataEmail['docSystemID'] = null;
                                $dataEmail['docSystemCode'] = $bankLedger->documentSystemCode;

                                $dataEmail['docApprovedYN'] = $paySupplierInvoice->approved;
                                $dataEmail['docCode'] = $bankLedger->documentCode;
                                $dataEmail['ccEmailID'] = $confirmedPersonEmail;

                                $emailTitle = "";
                                if ($bankLedger->invoiceType == 2) {
                                    $emailTitle = 'Payment has been released for the below invoices';
                                } else if ($bankLedger->invoiceType == 5) {
                                    $emailTitle = 'Advance Payment has been released for the below orders';
                                }

                                $temp = "Dear " . $supplierMaster->nameOnPaymentCheque . ",<p> " .$emailTitle. " from <b>" . $company->CompanyName . "<b/>.<p>" . $invoiceNumbers . "<p><p>" . $footer;

                                //$location = \DB::table('systemmanualfolder')->first();
                                $dataEmail['isEmailSend'] = 0;
                                
                                $dataEmail['alertMessage'] = "Payment Released";
                                $dataEmail['emailAlertMessage'] = $temp;

                               PaymentReleasedToSupplierJob::dispatch($request->db, $order, $dataEmail, $pdfName);
                            }
                        }

                    } else {
                        $updateArray['trsClearedAmount'] = 0;
                        $updateArray['trsClearedByEmpName'] = null;
                        $updateArray['trsClearedByEmpID'] = null;
                        $updateArray['trsClearedByEmpSystemID'] = null;
                        $updateArray['trsClearedDate'] = null;
                    }
                }


                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);
            } else if ($input['editType'] == 4) {

                $bankTransfer = $this->paymentBankTransferRepository->with(['bank_account'])->findWithoutFail($input['paymentBankTransferID']);

                if (empty($bankTransfer)) {
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_transfer')]));
                }

                if ($bankTransfer->confirmedYN == 1) {
                    return $this->sendError(trans('custom.you_cannot_edit_this_document_already_confirmed'), 500);
                }

                $bankId = 0;
                if ($bankTransfer->bank_account) {
                    $bankId = $bankTransfer->bank_account->accountCurrencyID;
                }

                $bankLedger = BankLedger::where('bankLedgerAutoID', $id)->first();

                if(empty($bankLedger)){
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.payment')]), 500);
                }

                $checkBankAccount = BankLedger::where('bankLedgerAutoID', $id);


                if($bankLedger->payeeID){
                    $checkBankAccount =  $checkBankAccount->whereHas('supplier_by', function ($q3) use ($bankLedger) {
                        $q3->whereHas('supplierCurrency', function ($q4) use ($bankLedger) {
                            $q4->where('currencyID', $bankLedger->supplierTransCurrencyID)
                                ->whereHas('bankMemo_by', function ($q) {
                                    $q->where('bankMemoTypeID', 4);
                                });
                        });
                    })->first();
                }else{
                    $checkBankAccount =  $checkBankAccount->whereHas('payee_bank_memos',function ($q) {
                        $q->where('bankMemoTypeID', 4);
                    })->first();
                }

                if (empty($checkBankAccount) && $input['pulledToBankTransferYN']  && $bankTransfer->fileType != 1) {
                    return $this->sendError(trans('custom.supplier_account_is_not_updated_you_cannot_add_this_payment_to_the_transfer'), 500);
                }

                if ($input['pulledToBankTransferYN']) {
                    $updateArray['pulledToBankTransferYN'] = -1;
                } else {
                    $updateArray['pulledToBankTransferYN'] = 0;
                }

                if ($updateArray['pulledToBankTransferYN']) {
                    $updateArray['paymentBankTransferID'] = $input['paymentBankTransferID'];
                } else {
                    $updateArray['paymentBankTransferID'] = null;
                }

                $bankLedger = $this->bankLedgerRepository->update($updateArray, $id);
            }
        }
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);
        return $this->sendResponse($bankLedger->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_ledgers')]));
    }

    public function pvSupplierPrint(Request $request)
    {

        $id = 76605;

        $PaySupplierInvoiceMasterData = PaySupplierInvoiceMaster::find($id);

        if (empty($PaySupplierInvoiceMasterData)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
        }

        $output = PaySupplierInvoiceMaster::where('PayMasterAutoId', $id)
            ->with(['supplier', 'transactioncurrency', 'supplierdetail',
                'company', 'localcurrency', 'rptcurrency', 'advancedetail', 'confirmed_by'])->first();

        if (empty($output)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_receive_payment')]));
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


        $order = array(
            'masterdata' => $output,
            'docRef' => $refernaceDoc,
            'transDecimal' => $transDecimal,
            'localDecimal' => $localDecimal,
            'rptDecimal' => $rptDecimal,
            'supplierdetailTotTra' => $supplierdetailTotTra,
            'directDetailTotTra' => $directDetailTotTra,
            'advancePayDetailTotTra' => $advancePayDetailTotTra
        );

        $time = strtotime("now");
        $fileName = 'payment_voucher_' . $id . '_' . $time . '.pdf';
        $html = view('print.payment_remittance_report_treasury_email', $order);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream($fileName);

    }



    public function updateTreasuryCollection(Request $request)
    {

        $input = $request->all();
        $id = 0;

        if (array_key_exists('editType', $input)) {

            $entity = null;
            $bankAccountAutoID = 0;
            if ($input['editType'] == 1) {
                $id = $input['custReceivePaymentAutoID'];
                $entity = $this->customerReceivePaymentRepository->find($id);

                if (empty($entity)) {
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.payment')]));
                }
                $bankAccountAutoID = $entity->bankAccount;
            } else if ($input['editType'] == 2) {
                $id = $input['PayMasterAutoId'];
                $entity = $this->paySupplierInvoiceMasterRepository->find($id);

                if (empty($entity)) {
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.receipt')]));
                }

                $bankAccountAutoID = $entity->BPVAccount;
            } else {
                return $this->sendError('Error', 500);
            }


            $employee = \Helper::getEmployeeInfo();

            if ($entity->confirmedYN != 1) {
                return $this->sendError(trans('custom.you_cannot_edit_it_is_not_confirmed'), 500);
            }

            if ($entity->approved == -1) {
                return $this->sendError(trans('custom.you_cannot_edit_it_is_already_approved'), 500);
            }

            if ($input['trsCollectedYN']) {
                $updateArray['trsCollectedYN'] = -1;
            } else {
                $updateArray['trsCollectedYN'] = 0;
            }

             if (isset($input['dateChange']) && $input['dateChange']) {
                if (Carbon::parse($input['trsCollectedDate']) > Carbon::now()) {
                    return $this->sendError("Collected date cannot be a future date", 500);
                }


                $bankReconciliation = BankReconciliation::where('companySystemID', $entity->companySystemID)
                                                       ->where('bankAccountAutoID', $bankAccountAutoID)
                                                       ->orderBy('bankRecAutoID', 'desc')
                                                       ->first();

                if ($bankReconciliation && Carbon::parse($bankReconciliation->bankRecAsOf) > Carbon::parse($input['trsCollectedDate'])) {
                    return $this->sendError("Collected date cannot be a less than last reconciliation date. Last reconciliation date - ".Carbon::parse($bankReconciliation->bankRecAsOf)->format('d/m/Y'), 500);
                }


                $updateArray['trsCollectedDate'] = Carbon::parse($input['trsCollectedDate']);
            } else {
                if ($updateArray['trsCollectedYN']) {
                    $updateArray['trsCollectedByEmpName'] = $employee->empName;
                    $updateArray['trsCollectedByEmpID'] = $employee->empID;
                    $updateArray['trsCollectedByEmpSystemID'] = $employee->employeeSystemID;
                    $updateArray['trsCollectedDate'] = now();
                } else {
                    $updateArray['trsCollectedByEmpName'] = null;
                    $updateArray['trsCollectedByEmpID'] = null;
                    $updateArray['trsCollectedByEmpSystemID'] = null;
                    $updateArray['trsCollectedDate'] = null;
                }
            }


            if ($input['editType'] == 1) {
                $id = $input['custReceivePaymentAutoID'];
                $this->customerReceivePaymentRepository->update($updateArray, $id);
                $entity = $this->customerReceivePaymentRepository->find($id);
            } else if ($input['editType'] == 2) {
                $id = $input['PayMasterAutoId'];
                $this->paySupplierInvoiceMasterRepository->update($updateArray, $id);
                $entity = $this->paySupplierInvoiceMasterRepository->find($id);
            }
            return $this->sendResponse($entity->toArray(), 'Successfully updated');
        } else {
            return $this->sendError(trans('custom.error'), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankLedgers/{id}",
     *      summary="Remove the specified BankLedger from storage",
     *      tags={"BankLedger"},
     *      description="Delete BankLedger",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankLedger",
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
        /** @var BankLedger $bankLedger */
        $bankLedger = $this->bankLedgerRepository->findWithoutFail($id);

        if (empty($bankLedger)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_ledgers')]));
        }

        $bankLedger->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_ledgers')]));
    }

    public function getBankReconciliationsByType(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

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

        $type = '<';
        $orderBy = 'bankLedgerAutoID';
        if (array_key_exists('type', $input) && ($input['type'] == 1 || $input['type'] == 2)) {

            if ($input['type'] == 1) {
                $type = '<';
            } else if ($input['type'] == 2) {
                $type = '>';
            }
        }

        if($input['isFromHistory'] == 0){
            $bankReconciliation = BankReconciliation::find($input['bankRecAutoID']);

            $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies);
        }else if($input['isFromHistory'] == 1) {
            $orderBy = 'refferedbackAutoID';
            $bankReconciliation = BankReconciliationRefferedBack::find($input['bankRecAutoID']);

            $bankLedger = PaymentBankTransferDetailRefferedBack::whereIn('companySystemID', $subCompanies)
                                    ->where('timesReferred',$input['timesReferred']);

            $input['bankRecAutoID'] = $bankReconciliation->bankRecAutoID;
        }


        $confirmed = 0;
        if (!empty($bankReconciliation)) {
            $confirmed = $bankReconciliation->confirmedYN;
        }

        $bankLedger = $bankLedger->where('payAmountBank', $type, 0)
                                    ->where("bankAccountID", $input['bankAccountAutoID'])
                                    ->where("trsClearedYN", -1)
                                    ->whereDate("postedDate", '<=', $bankReconciliation->bankRecAsOf)
                                    ->where(function ($q) use ($input, $confirmed) {
                                        $q->where(function ($q1) use ($input) {
                                            $q1->where('bankRecAutoID', $input['bankRecAutoID'])
                                                ->where("bankClearedYN", -1);
                                        })->when($confirmed == 0, function ($q2) {
                                            $q2->orWhere("bankClearedYN", 0);
                                        });

                                    });

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%")
                    ->orWhere('documentChequeNo', 'LIKE', "%{$search}%")
                    ->orWhere('payAmountBank', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankLedger)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input,$orderBy) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy($orderBy, $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBankAccountPaymentReceiptByType(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

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

        $type = '<';
        $documentCode = "documentCode";
        $documentNarration = "documentNarration";
        $autoId = "bankLedgerAutoID";
        $documentChequeNo = "documentChequeNo";
        $payAmountBank = "payAmountBank";
        $data = array();

        if (array_key_exists('type', $input) && ($input['type'] == 1 || $input['type'] == 2)) {
            if ($input['type'] == 1) {
                $type = '<';
                $documentCode = "custPaymentReceiveCode";
                $documentNarration = "narration";
                $autoId = "custReceivePaymentAutoID";
                $documentChequeNo = "custChequeNo";
                $payAmountBank = "bankAmount";
            } else if ($input['type'] == 2) {
                $type = '>';
                $documentCode = "BPVcode";
                $documentNarration = "BPVNarration";
                $autoId = "PayMasterAutoId";
                $documentChequeNo = "BPVchequeNo";
            }
        }

        if (array_key_exists('isClear', $input) && $input['isClear']) {
            $documentCode = "documentCode";
            $documentNarration = "documentNarration";
            $autoId = "bankLedgerAutoID";
            $documentChequeNo = "documentChequeNo";
            $payAmountBank = "payAmountBank";
            $data = BankLedger::whereIn('companySystemID', $subCompanies)
                ->where('payAmountBank', $type, 0)
                ->where("bankAccountID", $input['bankAccountAutoID'])
                ->where("bankClearedYN", 0);
        } else {
            if ($input['type'] == 1) {
                $data = CustomerReceivePayment::whereIn('companySystemID', $subCompanies)
                    ->where("bankAccount", $input['bankAccountAutoID'])
                    ->where("bankClearedYN", 0)
                    ->where("confirmedYN", 1)
                    ->where("approved", 0);

            } else if ($input['type'] == 2) {
                $data = PaySupplierInvoiceMaster::whereIn('companySystemID', $subCompanies)
                    ->where("BPVAccount", $input['bankAccountAutoID'])
                    ->where("bankClearedYN", 0)
                    ->where("confirmedYN", 1)
                    ->where("approved", 0);
            }
        }

        $search = $request->input('search.value');

        if ($search && $documentCode && $documentNarration) {
            $search = str_replace("\\", "\\\\", $search);
            $data = $data->where(function ($query) use ($search, $documentCode, $documentNarration, $documentChequeNo, $payAmountBank) {
                $query->where($documentCode, 'LIKE', "%{$search}%")
                    ->orWhere($documentNarration, 'LIKE', "%{$search}%")
                    ->orWhere($documentChequeNo, 'LIKE', "%{$search}%")
                    ->orWhere($payAmountBank, 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($data)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input, $autoId) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0 && $autoId) {
                        $query->orderBy($autoId, $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPaymentsByBankTransfer(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

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

        $orderBy = 'bankLedgerAutoID';

        if($input['isFromHistory'] == 0){
            $paymentBankTransfer = PaymentBankTransfer::find($input['paymentBankTransferID']);
        }else if($input['isFromHistory'] == 1) {
            $paymentBankTransfer =  $this->paymentBankTransferRefferedBackRepository->find($input['paymentBankTransferID']);
        }

        $confirmed = 0;
        if (!empty($paymentBankTransfer)) {
            $confirmed = $paymentBankTransfer->confirmedYN;
        }
        $bankId = 0;
        if ($paymentBankTransfer->bank_account) {
            $bankId = $paymentBankTransfer->bank_account->accountCurrencyID;
        }

        if($input['isFromHistory'] == 0){
            $bankLedger = BankLedger::whereIn('companySystemID', $subCompanies);
        }else if($input['isFromHistory'] == 1) {
            $orderBy = 'refferedbackAutoID';
            $bankLedger = PaymentBankTransferDetailRefferedBack::whereIn('companySystemID', $subCompanies)
                ->where('timesReferred',$input['timesReferred']);

            $input['paymentBankTransferID'] = $paymentBankTransfer->paymentBankTransferID;
        }

        $bankLedger = $bankLedger->where('payAmountBank', '>', 0)
            ->where("bankAccountID", $input['bankAccountAutoID'])
            ->where("trsClearedYN", -1)
            ->where("bankClearedYN", 0)
            ->where('documentSystemID',4)
            ->where("payAmountBank",'>',0)
            ->where("bankCurrency", $bankId)
            ->with(['payee_bank_memos' => function ($q) {
                $q->where('bankMemoTypeID', 4);
            },'paymentVoucher'=> function($q) {
                $q->with(['supplier','payee']);
            }]);


        if($paymentBankTransfer->fileType == 0)
        {
            $bankLedger->whereIn('invoiceType', [2, 3, 5])->whereHas('paymentVoucher', function ($q) {
                $q->where('payment_mode',3);
                $q->whereHas('supplier');
            });
        }else {

            $bankLedger->whereIn('invoiceType', [3])
                ->whereHas('paymentVoucher', function ($q) {
                    $q->where('payment_mode', 3)
                        ->whereIn('finalSettlementYN', [1]);
                })
                ->orWhereHas('paymentVoucher', function ($q) {
                    $q->where('payment_mode', 3)
                        ->where('finalSettlementYN', [-1])
                        ->whereHas('payee');
                });
        }

        $bankLedger->where(function ($q) use ($input, $confirmed) {
            $q->where(function ($q1) use ($input) {
                $q1->where('paymentBankTransferID', $input['paymentBankTransferID'])
                    ->where("pulledToBankTransferYN", -1);
            })->when($confirmed == 0, function ($q2) {
                $q2->orWhere("pulledToBankTransferYN", 0);
            });
        });

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentNarration', 'LIKE', "%{$search}%")
                    ->orWhere('payeeName', 'LIKE', "%{$search}%")
                    ->orWhere('documentChequeNo', 'LIKE', "%{$search}%")
                    ->orWhere('payAmountBank', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankLedger)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input,$orderBy) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy($orderBy, $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('supplier_by', function ($row) {
                return $this->getSupplierBankMemoByCurrency($row);
            })
            ->with('orderCondition', $sort)
            ->make(true);
    }


    function getSupplierBankMemoByCurrency ($row){

        /*'supplier_by' => function ($q3) use ($bankId) {
            $q3->with(['supplierCurrency' => function ($q4) use ($bankId) {
                $q4->where('currencyID', $bankId)
                    ->with(['bankMemo_by' => function ($q) {
                        $q->where('bankMemoTypeID', 4);
                    }]);
            }]);
        },*/
        $bankMemo = SupplierMaster::where('supplierCodeSystem',$row->payeeID)
                                    ->with(['supplierCurrency' => function ($q4) use ($row) {
                                        $q4->where('currencyID', $row->supplierTransCurrencyID)
                                            ->with(['bankMemo_by' => function ($q) {
                                                $q->where('bankMemoTypeID', 4);
                                            }]);
                                    }])->first();
        if(!empty($bankMemo)){
            $bankMemo = $bankMemo->toArray();
        }else{
            $bankMemo = array();
        }

        return $bankMemo;

    }

    public function getChequePrintingItems(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        //$bankLedger = $this->chequeListQrt($input, $search, 0);

        $input = $this->convertArrayToSelectedValue($input, array('bankID', 'bankAccountID', 'invoiceType','option'));

        $selectedCompanyId = $input['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $bankAccount = null;
        if($input['bankID'] && $input['bankAccountID']){
            $bankAccount = BankAccount::where('bankmasterAutoID', $input['bankID'])
                ->where('bankAccountAutoID', $input['bankAccountID'])
                ->with(['currency'])
                ->first();
        }

        $bank_currency_id = 2;
        if ($bankAccount && $bankAccount->currency) {
            $bank_currency_id = $bankAccount->currency->currencyID;
        }

        $bankLedger = PaySupplierInvoiceMaster::whereIn('companySystemID', $subCompanies)
            ->where("confirmedYN", 1)
            ->where("approved", 0)
            ->whereIn('payment_mode',[2,3])
            // ->where('RollLevForApp_curr','>',1)
            ->where("refferedBackYN", 0)
            ->where("cancelYN", 0)
            ->where("BPVchequeNo",'!=',0)
//            ->where("BPVsupplierID",'!=', null) // check supplier id only for direct. not for cheque
            ->where(function ($q) use($input) {
                $q->where(function ($q){
                    $q->where('invoiceType', '!=',3)->where("BPVsupplierID", '!=', null);
                })->orWhere(function($q){
                    $q->where('invoiceType', 3);

                });
            })
            ->when(!empty($input['invoiceType']) && $input['invoiceType'] >0, function ($q) use ($input) {
                if($input['invoiceType'] == 3){
                    return $q->where('invoiceType', $input['invoiceType']);
                }else{
                    return $q->where('invoiceType', $input['invoiceType'])->where("BPVsupplierID", '!=', null);
                }
            })
            ->when(isset($input['option']) && !empty($input['option']), function ($q) use ($input) {
                if($input['option']==-99){
                    return $q->where('chequePaymentYN', 0);
                }else{
                    return $q->where('chequePaymentYN', $input['option']);
                }
            })
            ->when(!empty($input['bankID']) && $input['bankID'] >0, function ($q) use ($input) {
                return $q->where('BPVbank', $input['bankID']);
            })
            ->when(!empty($input['bankAccountID']) && $input['bankAccountID']>0, function ($q) use ($input) {
                return $q->where('BPVAccount', $input['bankAccountID']);
            })
            ->with(['bankcurrency', 'company', 'bankaccount', 'supplier' => function ($q3) use ($bank_currency_id) {
                $q3->with(['supplierCurrency' => function ($q4) use ($bank_currency_id) {
                    $q4->where('currencyID', $bank_currency_id)
                        ->with(['bankMemo_by']);
                }]);
            }]);
            if(isset($input['isPrinted']) && $input['isPrinted']==1){
                $bankLedger->where("chequePrintedYN", -1);
            }else{
                $bankLedger->where("chequePrintedYN", 0);
            }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                return $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")
                    ->orWhere('directPaymentPayee', 'LIKE', "%{$search}%")
                    ->orWhere('BPVchequeNo', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankLedger)
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

    public function chequeListQrt($request, $search, $chequePrintedYN)
    {
        $input = $request;
        $input = $this->convertArrayToSelectedValue($input, array('bankID', 'bankAccountID', 'invoiceType'));
        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }


        $validator = \Validator::make($input, [
            'bankID' => 'required',
            'bankAccountID' => 'required',
            'invoiceType' => 'required',
            'option' => 'required'
        ]);

        if (!$validator->fails() && $input['bankID'] > 0 && $input['bankAccountID'] > 0 &&
            in_array($input['invoiceType'], [2, 3, 5])
            && in_array($input['option'], [0, -1])
        ) {
        } else {
            $input['chequePaymentYN'] = -99;
            $input['invoiceType'] = -99;
            $input['bankID'] = -99;
            $input['bankAccountID'] = -99;
            $input['option'] = -99;
        }

        $bankAccount = BankAccount::where('bankmasterAutoID', $input['bankID'])
            ->where('bankAccountAutoID', $input['bankAccountID'])
            ->with(['currency'])
            ->first();

        $bank_currency_id = 2;
        if ($bankAccount && $bankAccount->currency) {
            $bank_currency_id = $bankAccount->currency->currencyID;
        }

        $bankLedger = PaySupplierInvoiceMaster::whereIn('companySystemID', $subCompanies)
            ->where("confirmedYN", 1)
            //->where("approved", 0)
            ->whereRaw('RollLevForApp_curr <=> noOfApprovalLevels')
            ->where("refferedBackYN", 0)
            ->where("cancelYN", 0)
            ->where("BPVchequeNo",'!=',0)
            ->where("chequePrintedYN", $chequePrintedYN)
            ->where("chequePaymentYN", $input['option'])
            ->when(request('invoiceType') && in_array($input['invoiceType'], $input), function ($q) use ($input) {
                return $q->where('invoiceType', $input['invoiceType']);
            })
            ->when(request('bankID'), function ($q) use ($input) {
                return $q->where('BPVbank', $input['bankID']);
            })
            ->when(request('bankAccountID'), function ($q) use ($input) {
                return $q->where('BPVAccount', $input['bankAccountID']);
            })
            ->with(['bankcurrency', 'company', 'bankaccount', 'supplier' => function ($q3) use ($bank_currency_id) {
                $q3->with(['supplierCurrency' => function ($q4) use ($bank_currency_id) {
                    $q4->where('currencyID', $bank_currency_id)
                        ->with(['bankMemo_by']);
                }]);
            }]);


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankLedger = $bankLedger->where(function ($query) use ($search) {
                return $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")
                    ->orWhere('directPaymentPayee', 'LIKE', "%{$search}%")
                    ->orWhere('BPVchequeNo', 'LIKE', "%{$search}%");
            });
        }

        return $bankLedger;
    }

    public function updatePrintChequeItems(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        $htmlName = '';
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $employee = \Helper::getEmployeeInfo();

        /*validation for cheque print*/
        if(isset($input['selectedForPrint']) && is_array($input['selectedForPrint']) && count($input['selectedForPrint'])>0){
            $chequeCount = 0;
            $transferCount = 0;
            $supplierTransCurrencyID = 0;
            $pv = PaySupplierInvoiceMaster::whereIn('PayMasterAutoId',$input['selectedForPrint'])->get();
            if(!empty($pv)){

                foreach ($pv as $value){
                    if ($value->chequePaymentYN == -1){
                        $chequeCount++;
                    }elseif($value->chequePaymentYN == 0){
                        $transferCount++;
                    }
                    $supplierTransCurrencyID = $value->supplierTransCurrencyID;
                }

                /*
                 * User Should able select multiple Bank transfer and print for same supplier
                 * ( If Supplier, Bank, account and Transaction currency are same)
                 * */
                $supplierCountArray = collect($pv)->groupBy('BPVsupplierID');
                $supplierArrayKeys = array_keys($supplierCountArray->toArray());
                if(count($supplierCountArray->toArray())>1){
                    return $this->sendError(trans('custom.different_suppliers_found_you_can_not_select_different_suppliers_vouchers'),500);
                }
//                if(in_array('',$supplierArrayKeys) || in_array(null,$supplierArrayKeys)){
//                    return $this->sendError('supplier ID field is can not be empty on db',500);
//                }

                $accountCountArray = collect($pv)->groupBy('BPVAccount');
                $accountArrayKeys = array_keys($accountCountArray->toArray());
                if(count($accountCountArray->toArray())>1){
                    return $this->sendError(trans('custom.different_accounts_found_you_can_not_select_different_suppliers_vouchers'),500);
                }
                if(in_array(null,$accountArrayKeys) || in_array('',$accountArrayKeys)){
                    return $this->sendError(trans('custom.account_field_is_can_not_be_empty_on_db'),500);
                }

                $currencyCountArray = collect($pv)->groupBy('supplierTransCurrencyID');
                $currencyArrayKeys = array_keys($currencyCountArray->toArray());
                if(count($currencyCountArray->toArray())>1){
                    return $this->sendError(trans('custom.different_currencies_found_you_can_not_select_different_accounts_vouchers'),500);
                }
                if(in_array(null,$currencyArrayKeys) || in_array('',$currencyArrayKeys)){
                    return $this->sendError(trans('custom.null_currency_field_is_found'),500);
                }
                ////////////

                /*
                 * Don't allow user to Select different Transaction type (Cheques and Transfer) and Print
                 * Don't allow user to select multiple Cheques and Print
                 *
                 * */
                if($chequeCount ==0 && $transferCount ==0){
                    return $this->sendError(trans('custom.please_select_an_item_to_print'),500);
                }elseif ($chequeCount >0 && $transferCount >0){
                    return $this->sendError(trans('custom.you_can_not_print_cheque_payment_and_bank_transfer_at_a_time'),500);
                }elseif ($chequeCount >1){
                    return $this->sendError(trans('custom.you_can_print_only_one_cheque_payment_at_a_time'),500);
                }
                //////////////
                if($chequeCount>0){
                    $htmlName='cheque';
                }elseif ($transferCount>0){
                    $htmlName='bank_transfer';
                }
            }

            $input = $this->convertArrayToSelectedValue($input, array('bankID', 'bankAccountID', 'invoiceType','option'));

            $selectedCompanyId = $input['companySystemID'];
            $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

            if ($isGroup) {
                $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            } else {
                $subCompanies = [$selectedCompanyId];
            }
            $bankAccount = null;
            if($input['bankID'] && $input['bankAccountID']){
                $bankAccount = BankAccount::where('bankmasterAutoID', $input['bankID'])
                    ->where('bankAccountAutoID', $input['bankAccountID'])
                    ->with(['currency'])
                    ->first();
            }

            $bank_currency_id = 2;
            if ($bankAccount && $bankAccount->currency) {
                $bank_currency_id = $bankAccount->currency->currencyID;
            }


            $bankLedger = PaySupplierInvoiceMaster::whereIn('companySystemID', $subCompanies)
                ->where("confirmedYN", 1)
                ->where("approved", 0)
                // ->where('RollLevForApp_curr','>',1)
                ->where("refferedBackYN", 0)
                ->where("cancelYN", 0)
                ->where("BPVchequeNo",'!=',0)
                ->where("chequePrintedYN", 0)

                ->when($input['selectedForPrint'], function ($q) use ($input) {
                    $q->whereIn('PayMasterAutoId', $input['selectedForPrint']);
                })
                ->with(['bankcurrency', 'company', 'bankaccount', 'supplier' => function ($q3) use ($bank_currency_id,$supplierTransCurrencyID) {
                    $q3->with(['supplierCurrency' => function ($q4) use ($bank_currency_id,$supplierTransCurrencyID) {
//                        $q4->where('currencyID', $bank_currency_id)
                        $q4->where('currencyID', $supplierTransCurrencyID)
                            ->where('isAssigned', -1)
                            ->with(['bankMemo_by']);
                    }]);
                }, 'payee_memo' => function($q) use($subCompanies){
                    $q->where('documentSystemID', 4)
                    ->whereIn('companySystemID',$subCompanies);
                }])
                ->orderBy('PayMasterAutoId', $sort)
                ->get();

            if (count($bankLedger) == 0) {
                return $this->sendError(trans('custom.no_items_found_for_print'), 500);
            }

            DB::beginTransaction();
            try {
                $time = strtotime("now");
                $fileName = 'cheque' . $time . '.pdf';
                $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                $totalAmount = 0;
                foreach ($bankLedger as $item) {
                    $temArray = array();
                    $temArray['chequePrintedYN'] = -1;
                    $temArray['chequePrintedDateTime'] = now();
                    $temArray['chequePrintedByEmpSystemID'] = $employee->employeeSystemID;
                    $temArray['chequePrintedByEmpID'] = $employee->empID;
                    $temArray['chequePrintedByEmpName'] = $employee->empName;
                    if(isset($input['isPrint']) && $input['isPrint']) {
                        $this->paySupplierInvoiceMasterRepository->update($temArray, $item->PayMasterAutoId);

                        /*
                         * update cheque registry table print status if GCNFCR policy is on
                         * */
                        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $item->companySystemID)
                            ->where('companyPolicyCategoryID', 35)
                            ->where('isYesNO', 1)
                            ->first();
                        if (!empty($is_exist_policy_GCNFCR)) {
                            $check_registry = [
                                'isPrinted' => -1,
                                'cheque_printed_at' => now(),
                                'cheque_print_by' => $employee->employeeSystemID
                            ];
                            ChequeRegisterDetail::where('cheque_no', $item->BPVchequeNo)
                                ->where('company_id', $item->companySystemID)
                                ->where('document_id', $item->PayMasterAutoId)
                                ->update($check_registry);
                        }
                    }
                    $item['decimalPlaces'] = 2;
                    if ($item['bankcurrency']) {
                        $item['decimalPlaces'] = $item['bankcurrency']['DecimalPlaces'];
                    }

                    $item->memos = isset($item['supplier']['supplierCurrency'][0]['bankMemo_by'])?$item['supplier']['supplierCurrency'][0]['bankMemo_by']:null;
                    $temDetails = PaySupplierInvoiceMaster::where('PayMasterAutoId', $item['PayMasterAutoId'])
                        ->first();

                    if (!empty($temDetails)) {
                        if ($temDetails->invoiceType == 2) {
                            $item['details'] = $temDetails->supplierdetail;
                        } else if ($temDetails->invoiceType == 3) {
                            $item['details'] = $temDetails->directdetail;
                        } else if ($temDetails->invoiceType == 5) {
                            $item['details'] = $temDetails->advancedetail;
                        } else {
                            $item['details'] = [];
                        }
                    } else {
                        $item['details'] = [];
                    }
                    $totalAmount = $totalAmount+$item->payAmountSuppTrans;
                }
                $entities = $bankLedger;
                if(count($entities) && isset($entities[0])){
                    $entity = $entities[0];
                    $entity->totalAmount = $totalAmount;
                    $totalAmount = round($totalAmount, $entity->decimalPlaces);
                    $amountSplit = explode(".", $totalAmount);
                    $intAmt = 0;
                    $floatAmt = 00;

                    if (count($amountSplit) == 1) {
                        $intAmt = $amountSplit[0];
                        $floatAmt = 00;
                    } else if (count($amountSplit) == 2) {
                        $intAmt = $amountSplit[0];
                        $floatAmt = $amountSplit[1];
                    }

                    $entity->floatAmt = (string)$floatAmt;

                    //add zeros to decimal point
                    if($entity->floatAmt != 00){
                        $length = strlen($entity->floatAmt);
                        if($length<$entity->decimalPlaces){
                            $count = $entity->decimalPlaces-$length;
                            for ($i=0; $i<$count; $i++){
                                $entity->floatAmt .= '0';
                            }
                        }
                    }

                    // get supplier transaction currency
                    $entity->instruction = '';
                    $entity->supplierTransactionCurrencyDetails = [];
                    if(isset($entity->supplier->supplierCurrency[0]->currencyMaster) && $entity->supplier->supplierCurrency[0]->currencyMaster){
                        $entity->supplierTransactionCurrencyDetails = $entity->supplier->supplierCurrency[0]->currencyMaster;
                        if($supplierTransCurrencyID != $bank_currency_id){
                            $entity->instruction = 'The exchange rate agreed with treasury department is '.$entity->supplierTransactionCurrencyDetails->CurrencyCode.' '.$entity->supplierTransCurrencyER.' = '.$entity->bankcurrency->CurrencyCode.' '.number_format($entity->companyRptCurrencyER,4);
                        }
                    }else{
                        $entity->supplierTransactionCurrencyDetails = $entity->bankcurrency;
                    }





                    $entity->amount_word = ucfirst($f->format($intAmt));
                    $entity->amount_word = str_replace('-', ' ', $entity->amount_word);
                    $entity->chequePrintedByEmpName = $employee->empName;
                    if($entity->supplier){
                        $entity->nameOnCheque = isset($entity->supplier->nameOnPaymentCheque)?$entity->supplier->nameOnPaymentCheque:'';
                    }else{
                        $entity->nameOnCheque = $entity->directPaymentPayee;
                    }

                }else{
                    $entity = null;
                }

                $array = array('entity' => $entity, 'date' => now(),'type'=>$htmlName);
                if ($htmlName) {
                    $html = view('print.' . $htmlName, $array)->render();
                    DB::commit();
                    if(isset($input['isPrint']) && $input['isPrint']) {
                        return $this->sendResponse($html, trans('custom.print_successfully'));
                    }else{
                        return $this->sendResponse($array, trans('custom.retrieved_successfully'));
                    }

                } else {
                    return $this->sendError(trans('custom.error'), 500);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return ['success' => false, 'message' => $e . trans('custom.error')];
            }

        }else{
            return $this->sendError(trans('custom.please_select_an_item_to_print'),500);
        }

    }


    public function updatePrintAhliChequeItems(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        $htmlName = '';
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $employee = \Helper::getEmployeeInfo();
        
        /*validation for cheque print*/
        if(isset($input['selectedForPrint']) && is_array($input['selectedForPrint']) && count($input['selectedForPrint'])>0){
            $chequeCount = 0;
            $transferCount = 0;
            $supplierTransCurrencyID = 0;
            $pv = PaySupplierInvoiceMaster::whereIn('PayMasterAutoId',$input['selectedForPrint'])->get();
            if(!empty($pv)){

                foreach ($pv as $value){
                    if ($value->chequePaymentYN == -1){
                        $chequeCount++;
                    }elseif($value->chequePaymentYN == 0){
                        $transferCount++;
                    }
                    $supplierTransCurrencyID = $value->supplierTransCurrencyID;
                }

                /*
                 * User Should able select multiple Bank transfer and print for same supplier
                 * ( If Supplier, Bank, account and Transaction currency are same)
                 * */
                $supplierCountArray = collect($pv)->groupBy('BPVsupplierID');
                $supplierArrayKeys = array_keys($supplierCountArray->toArray());
                if(count($supplierCountArray->toArray())>1){
                    return $this->sendError(trans('custom.different_suppliers_found_you_can_not_select_different_suppliers_vouchers'),500);
                }
//                if(in_array('',$supplierArrayKeys) || in_array(null,$supplierArrayKeys)){
//                    return $this->sendError('supplier ID field is can not be empty on db',500);
//                }

                $accountCountArray = collect($pv)->groupBy('BPVAccount');
                $accountArrayKeys = array_keys($accountCountArray->toArray());
                if(count($accountCountArray->toArray())>1){
                    return $this->sendError(trans('custom.different_accounts_found_you_can_not_select_different_suppliers_vouchers'),500);
                }
                if(in_array(null,$accountArrayKeys) || in_array('',$accountArrayKeys)){
                    return $this->sendError(trans('custom.account_field_is_can_not_be_empty_on_db'),500);
                }

                $currencyCountArray = collect($pv)->groupBy('supplierTransCurrencyID');
                $currencyArrayKeys = array_keys($currencyCountArray->toArray());
                if(count($currencyCountArray->toArray())>1){
                    return $this->sendError(trans('custom.different_currencies_found_you_can_not_select_different_accounts_vouchers'),500);
                }
                if(in_array(null,$currencyArrayKeys) || in_array('',$currencyArrayKeys)){
                    return $this->sendError(trans('custom.null_currency_field_is_found'),500);
                }
                ////////////
                
                /*
                 * Don't allow user to Select different Transaction type (Cheques and Transfer) and Print
                 * Don't allow user to select multiple Cheques and Print
                 *
                 * */
                if($chequeCount ==0 && $transferCount ==0){
                    return $this->sendError(trans('custom.please_select_an_item_to_print'),500);
                }elseif ($chequeCount >0 && $transferCount >0){
                    return $this->sendError(trans('custom.you_can_not_print_cheque_payment_and_bank_transfer_at_a_time'),500);
                }elseif ($chequeCount >1){
                    return $this->sendError(trans('custom.you_can_print_only_one_cheque_payment_at_a_time'),500);
                }
                //////////////

               
                if($chequeCount>0){

                   
                


                    if($input['type'] == 2 && $input['name'] != '')
                    {
                        
                        $htmlName=$input['name'];
                    }
                    else if($input['type'] == 1)
                    {   

                  
                  
                        if(isset($input['bank_master_id']) && ($input['bank_master_id'] != null) && (!empty($input['bank_master_id']) ) )
                        {
                        
                            $templates_bank = ChequeTemplateBank::where('bank_id',$input['bank_master_id'][0])->with('template')->get();

                     
                            if(count($templates_bank) == 0)
                            {
                                return $this->sendError(trans('custom.no_templates'),500);
                            }
                            else if(count($templates_bank) == 1)
                            {
                                $htmlName=$templates_bank[0]['template']['view_name'];
                            
                            }
                            else if(count($templates_bank) > 1)
                            {
                                $details['is_modal'] = true;
                                $details['data'] = $templates_bank;
                                return $this->sendResponse($details, trans('custom.retrieved_successfully'));
                            }
                        }
                        else
                        {
                            return $this->sendError(trans('custom.no_bank'),500);
                        }
        
                    
                    }
                
                   
                }elseif ($transferCount>0){
                    $htmlName='bank_transfer';
                }
            }

            

            $input = $this->convertArrayToSelectedValue($input, array('bankID', 'bankAccountID', 'invoiceType','option'));

            $selectedCompanyId = $input['companySystemID'];
            $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

            if ($isGroup) {
                $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
            } else {
                $subCompanies = [$selectedCompanyId];
            }
            $bankAccount = null;
            if($input['bankID'] && $input['bankAccountID']){
                $bankAccount = BankAccount::where('bankmasterAutoID', $input['bankID'])
                    ->where('bankAccountAutoID', $input['bankAccountID'])
                    ->with(['currency'])
                    ->first();
            }

            $bank_currency_id = 2;
            if ($bankAccount && $bankAccount->currency) {
                $bank_currency_id = $bankAccount->currency->currencyID;
            }


            $bankLedger = PaySupplierInvoiceMaster::whereIn('companySystemID', $subCompanies)
                ->where("confirmedYN", 1)
                ->where("approved", 0)
                // ->where('RollLevForApp_curr','>',1)
                ->where("refferedBackYN", 0)
                ->where("cancelYN", 0)
                ->where("BPVchequeNo",'!=',0)
                ->where("chequePrintedYN", 0)

                ->when($input['selectedForPrint'], function ($q) use ($input) {
                    $q->whereIn('PayMasterAutoId', $input['selectedForPrint']);
                })
                ->with(['bankcurrency', 'company', 'bankaccount', 'supplier' => function ($q3) use ($bank_currency_id,$supplierTransCurrencyID) {
                    $q3->with(['supplierCurrency' => function ($q4) use ($bank_currency_id,$supplierTransCurrencyID) {
//                        $q4->where('currencyID', $bank_currency_id)
                        $q4->where('currencyID', $supplierTransCurrencyID)
                            ->where('isAssigned', -1)
                            ->with(['bankMemo_by']);
                    }]);
                }, 'payee_memo' => function($q) use($subCompanies){
                    $q->where('documentSystemID', 4)
                    ->whereIn('companySystemID',$subCompanies);
                }])
                ->orderBy('PayMasterAutoId', $sort)
                ->get();

            if (count($bankLedger) == 0) {
                return $this->sendError(trans('custom.no_items_found_for_print'), 500);
            }
                
            DB::beginTransaction();
            try {
                $time = strtotime("now");
                $fileName = 'cheque_ahli' . $time . '.pdf';
                $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                $totalAmount = 0;
                foreach ($bankLedger as $item) {
                    $temArray = array();
                    $temArray['chequePrintedYN'] = -1;
                    $temArray['chequePrintedDateTime'] = now();
                    $temArray['chequePrintedByEmpSystemID'] = $employee->employeeSystemID;
                    $temArray['chequePrintedByEmpID'] = $employee->empID;
                    $temArray['chequePrintedByEmpName'] = $employee->empName;
                    if(isset($input['isPrint']) && $input['isPrint']) {
                        $this->paySupplierInvoiceMasterRepository->update($temArray, $item->PayMasterAutoId);

                        /*
                         * update cheque registry table print status if GCNFCR policy is on
                         * */
                        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID', $item->companySystemID)
                            ->where('companyPolicyCategoryID', 35)
                            ->where('isYesNO', 1)
                            ->first();
                        if (!empty($is_exist_policy_GCNFCR)) {
                            $check_registry = [
                                'isPrinted' => -1,
                                'cheque_printed_at' => now(),
                                'cheque_print_by' => $employee->employeeSystemID
                            ];
                            ChequeRegisterDetail::where('cheque_no', $item->BPVchequeNo)
                                ->where('company_id', $item->companySystemID)
                                ->where('document_id', $item->PayMasterAutoId)
                                ->update($check_registry);
                        }
                    }
                    $item['decimalPlaces'] = 2;
                    if ($item['bankcurrency']) {
                        $item['decimalPlaces'] = $item['bankcurrency']['DecimalPlaces'];
                    }

                    $item->memos = isset($item['supplier']['supplierCurrency'][0]['bankMemo_by'])?$item['supplier']['supplierCurrency'][0]['bankMemo_by']:null;
                    $temDetails = PaySupplierInvoiceMaster::where('PayMasterAutoId', $item['PayMasterAutoId'])
                        ->first();

                    if (!empty($temDetails)) {
                        if ($temDetails->invoiceType == 2) {
                            $item['details'] = $temDetails->supplierdetail;
                        } else if ($temDetails->invoiceType == 3) {
                            $item['details'] = $temDetails->directdetail;
                        } else if ($temDetails->invoiceType == 5) {
                            $item['details'] = $temDetails->advancedetail;
                        } else {
                            $item['details'] = [];
                        }
                    } else {
                        $item['details'] = [];
                    }
                    $totalAmount = $totalAmount+$item->payAmountSuppTrans + $item->VATAmount;
                }
                $entities = $bankLedger;
                if(count($entities) && isset($entities[0])){
                    $entity = $entities[0];
                    $entity->totalAmount = $totalAmount;
                    $totalAmount = round($totalAmount, $entity->decimalPlaces);
                    $amountSplit = explode(".", $totalAmount);
                    $intAmt = 0;
                    $floatAmt = 00;

                    if (count($amountSplit) == 1) {
                        $intAmt = $amountSplit[0];
                        $floatAmt = 00;
                    } else if (count($amountSplit) == 2) {
                        $intAmt = $amountSplit[0];
                        $floatAmt = $amountSplit[1];
                    }

                    $entity->floatAmt = (string)$floatAmt;

                    //add zeros to decimal point
                    if($entity->floatAmt != 00){
                        $length = strlen($entity->floatAmt);
                        if($length<$entity->decimalPlaces){
                            $count = $entity->decimalPlaces-$length;
                            for ($i=0; $i<$count; $i++){
                                $entity->floatAmt .= '0';
                            }
                        }
                    }

                    // get supplier transaction currency
                    $entity->instruction = '';
                    $entity->supplierTransactionCurrencyDetails = [];
                    if(isset($entity->supplier->supplierCurrency[0]->currencyMaster) && $entity->supplier->supplierCurrency[0]->currencyMaster){
                        $entity->supplierTransactionCurrencyDetails = $entity->supplier->supplierCurrency[0]->currencyMaster;
                        if($supplierTransCurrencyID != $bank_currency_id){
                            $entity->instruction = 'The exchange rate agreed with treasury department is '.$entity->supplierTransactionCurrencyDetails->CurrencyCode.' '.$entity->supplierTransCurrencyER.' = '.$entity->bankcurrency->CurrencyCode.' '.number_format($entity->companyRptCurrencyER,4);
                        }
                    }else{
                        $entity->supplierTransactionCurrencyDetails = $entity->bankcurrency;
                    }





                    $entity->amount_word = ucfirst($f->format($intAmt));
                    $entity->amount_word = str_replace('-', ' ', $entity->amount_word);
                    $entity->chequePrintedByEmpName = $employee->empName;
                    if($entity->supplier){
                        $entity->nameOnCheque = isset($entity->supplier->nameOnPaymentCheque)?$entity->supplier->nameOnPaymentCheque:'';
                    }else{
                        $entity->nameOnCheque = $entity->directPaymentPayee;
                    }

                }else{
                    $entity = null;
                }
            
                $array = array('entity' => $entity, 'date' => now(),'type'=>$htmlName);
                if ($htmlName) {
                    $html = view('print.' . $htmlName, $array)->render();
                    DB::commit();
                    if(isset($input['isPrint']) && $input['isPrint']) {
                        return $this->sendResponse($html, trans('custom.print_successfully'));
                    }else{
                        $details['is_modal'] = false;
                        $details['data'] = $array;
                        return $this->sendResponse($details, trans('custom.retrieved_successfully'));
                    }

                } else {
                    return $this->sendError(trans('custom.error'), 500);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return ['success' => false, 'message' => $e . trans('custom.error')];
            }

        }else{
            return $this->sendError(trans('custom.please_select_an_item_to_print'),500);
        }

    }

    public function revertChequePrint(Request $request){
        $input = $request->all();

        $validator = \Validator::make($input, [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::where('PayMasterAutoId',$input['id'])->first();
        if(empty($paySupplierInvoiceMaster)){
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.pay_supplier_invoice_master')]),500);
        }

        $employee = \Helper::getEmployeeInfo();

        $temArray['chequePrintedYN'] = 0;
        $temArray['chequePrintedDateTime'] = '';
        $temArray['chequePrintedByEmpSystemID'] = '';
        $temArray['chequePrintedByEmpID'] = '';
        $temArray['chequePrintedByEmpName'] = '';
        $temArray['modifiedUserSystemID'] = $employee->employeeSystemID;
        $temArray['modifiedUser'] = $employee->empID;
        $temArray['modifiedPc'] = gethostname();
        $this->paySupplierInvoiceMasterRepository->update($temArray, $paySupplierInvoiceMaster->PayMasterAutoId);

        /*
         * update cheque registry table print status if GCNFCR policy is on
         * */
        $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID',$paySupplierInvoiceMaster->companySystemID)
            ->where('companyPolicyCategoryID',35)
            ->where('isYesNO',1)
            ->first();
        if(!empty($is_exist_policy_GCNFCR)) {
            $check_registry = [
                'isPrinted' => 0,
                'cheque_printed_at' => '',
                'cheque_print_by' => '',
                'updated_at' => now(),
                'updated_by' => $employee->employeeSystemID,
                'updated_pc' => gethostname()
            ];
            ChequeRegisterDetail::where('cheque_no',$paySupplierInvoiceMaster->BPVchequeNo)
                ->where('company_id',$paySupplierInvoiceMaster->companySystemID)
                ->where('document_id',$paySupplierInvoiceMaster->PayMasterAutoId)
                ->update($check_registry);
        }

        return $this->sendResponse([],trans('custom.print_reverted_successfully'));
    }

    public function updatePrintChequeItemsBacksUp(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $employee = \Helper::getEmployeeInfo();

        if(!isset($input['chequeNumberRangeFrom']) && !$input['chequeNumberRangeFrom']){
            $input['chequeNumberRangeFrom'] = 0;
        }

        if(!isset($input['chequeNumberRangeTo']) && !$input['chequeNumberRangeTo']){
            $input['chequeNumberRangeTo'] = 0;
        }

        $bankLedger = $this->chequeListQrt($input, $search, 0)
            ->when($input['chequeNumberRange'], function ($q) use ($input) {
                $q->where('BPVchequeNo', '>=', $input['chequeNumberRangeFrom'])
                    ->where('BPVchequeNo', '<=', $input['chequeNumberRangeTo']);
            })
            ->orderBy('PayMasterAutoId', $sort)
            ->get();

        if (count($bankLedger) == 0) {
            return $this->sendError(trans('custom.no_items_found_for_print'), 500);
        }
        DB::beginTransaction();
        try {
            $time = strtotime("now");
            $fileName = 'cheque' . $time . '.pdf';
            $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

            foreach ($bankLedger as $item) {
                $temArray = array();
                $temArray['chequePrintedYN'] = -1;
                $temArray['chequePrintedDateTime'] = now();
                $temArray['chequePrintedByEmpSystemID'] = $employee->employeeSystemID;
                $temArray['chequePrintedByEmpID'] = $employee->empID;
                $temArray['chequePrintedByEmpName'] = $employee->empName;
                $this->paySupplierInvoiceMasterRepository->update($temArray, $item->PayMasterAutoId);

                /*
                 * update check registry table print status
                 * */
//            $is_exist_policy_GCNFCR = CompanyPolicyMaster::where('companySystemID',$item->companySystemID)
//                ->where('companyPolicyCategoryID',35)
//                ->where('isYesNO',1)
//                ->first();
//            if(!empty($is_exist_policy_GCNFCR)) {
                $check_registry = [
                    'isPrinted' => -1,
                    'cheque_printed_at' => now(),
                    'cheque_print_by' => $employee->employeeSystemID
                ];
                ChequeRegisterDetail::where('cheque_no',$item->BPVchequeNo)
                    ->where('company_id',$item->companySystemID)
                    ->update($check_registry);
//            }
                /**/

                $temArray['chequePrintedYN'] = -1;
                $this->paySupplierInvoiceMasterRepository->update($temArray, $item->PayMasterAutoId);

                $amountSplit = explode(".", $item->payAmountBank);
                $intAmt = 0;
                $floatAmt = 00;

                if (count($amountSplit) == 1) {
                    $intAmt = $amountSplit[0];
                    $floatAmt = 00;
                } else if (count($amountSplit) == 2) {
                    $intAmt = $amountSplit[0];
                    $floatAmt = $amountSplit[1];
                }

                $item['amount_word'] = ucwords($f->format($intAmt));
                if ($item['supplier']) {
                    if ($item['supplier']['supplierCurrency']) {
                        if ($item['supplier']['supplierCurrency'][0]['bankMemo_by']) {
                            $memos = $item['supplier']['supplierCurrency'][0]['bankMemo_by'];
                            $item->memos = $memos;
                        }
                    }
                }
                $item['decimalPlaces'] = 2;
                $item['floatAmt'] = (string)$floatAmt;
                if ($item['bankcurrency']) {
                    $item['decimalPlaces'] = $item['bankcurrency']['DecimalPlaces'];
                }

                $temDetails = PaySupplierInvoiceMaster::where('PayMasterAutoId', $item['PayMasterAutoId'])
                    ->first();

                if (!empty($temDetails)) {
                    if ($input['invoiceType'] == 2) {
                        $item['details'] = $temDetails->supplierdetail;
                    } else if ($input['invoiceType'] == 3) {
                        $item['details'] = $temDetails->directdetail;
                    } else if ($input['invoiceType'] == 5) {
                        $item['details'] = $temDetails->advancedetail;
                    } else {
                        $item['details'] = [];
                    }
                } else {
                    $item['details'] = [];
                }
            }

            $htmlName = '';
            if ($input['option'] == -1) {
                $htmlName = 'cheque';
            } else if ($input['option'] == 0) {
                $htmlName = 'bank_transfer';
            }
            $array = array('entities' => $bankLedger, 'date' => now());
            if ($htmlName) {
                $html = view('print.' . $htmlName, $array)->render();
                //$pdf = \App::make('dompdf.wrapper');
                //$pdf->loadHTML($html);
                //$materielIssue->docRefNo = \Helper::getCompanyDocRefNo($input['companySystemID'], $materielIssue->documentSystemID);
                //'landscape'
                DB::commit();
                // return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
                return $this->sendResponse($html, trans('custom.print_successfully'));
            } else {
                return $this->sendError(trans('custom.error'), 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . trans('custom.error')];
        }
        // return $this->sendResponse($bankLedger->toArray(), 'updated successfully');
    }

    public function printChequeItems(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankLedger = $this->chequeListQrt($input, $search, 1)
            ->when($input['chequeNumberRange'], function ($q) use ($input) {
                $q->where('BPVchequeNo', '>=', $input['chequeNumberRangeFrom'])
                    ->where('BPVchequeNo', '<=', $input['chequeNumberRangeTo']);
            })
            ->orderBy('PayMasterAutoId', $sort)
            ->get();

        if (count($bankLedger) == 0) {
            return $this->sendError(trans('custom.error_not_found'), 500);
        }
        $time = strtotime("now");
        $fileName = 'cheque' . $time . '.pdf';
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        foreach ($bankLedger as $item) {

            $temArray['chequePrintedYN'] = -1;
            $this->paySupplierInvoiceMasterRepository->update($temArray, $item->PayMasterAutoId);

            $amountSplit = explode(".", $item->payAmountBank);
            $intAmt = 0;
            $floatAmt = 00;

            if (count($amountSplit) == 1) {
                $intAmt = $amountSplit[0];
                $floatAmt = 00;
            } else if (count($amountSplit) == 2) {
                $intAmt = $amountSplit[0];
                $floatAmt = $amountSplit[1];
            }

            $item['amount_word'] = ucwords($f->format($intAmt));
            if ($item['supplier']) {
                if ($item['supplier']['supplierCurrency']) {
                    if ($item['supplier']['supplierCurrency'][0]['bankMemo_by']) {
                        $memos = $item['supplier']['supplierCurrency'][0]['bankMemo_by'];
                        $item->memos = $memos;
                    }
                }
            }
            $item['decimalPlaces'] = 2;
            $item['floatAmt'] = (string)$floatAmt;
            if ($item['bankcurrency']) {
                $item['decimalPlaces'] = $item['bankcurrency']['DecimalPlaces'];
            }

            $temDetails = PaySupplierInvoiceMaster::where('PayMasterAutoId', $item['PayMasterAutoId'])
                ->first();

            if (!empty($temDetails)) {
                if ($input['invoiceType'] == 2) {
                    $item['details'] = $temDetails->supplierdetail;
                } else if ($input['invoiceType'] == 3) {
                    $item['details'] = $temDetails->directdetail;
                } else if ($input['invoiceType'] == 5) {
                    $item['details'] = $temDetails->advancedetail;
                } else {
                    $item['details'] = [];
                }
            } else {
                $item['details'] = [];
            }
        }

        //return $bankLedger;
        $htmlName = '';
        if ($input['option'] == -1) {
            $htmlName = 'cheque';
        } else if ($input['option'] == 0) {
            $htmlName = 'bank_transfer';
        }
        $array = array('entities' => $bankLedger, 'date' => now());
        if ($htmlName) {
            $html = view('print.' . $htmlName, $array);
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($html);
            //$materielIssue->docRefNo = \Helper::getCompanyDocRefNo($input['companySystemID'], $materielIssue->documentSystemID);
            //'landscape'
            return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream($fileName);
        } else {
            return $this->sendError(trans('custom.error'), 500);
        }
    }


    public function getChequePrintingFormData(Request $request)
    {
        $selectedCompanyId = $request['companyId'];
        $subCompaniesByGroup = [];
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $subCompaniesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompaniesByGroup = (array)$selectedCompanyId;
        }

        $banks = BankMaster::all();
        $bankIds = $banks->pluck('bankmasterAutoID');
        $accounts = BankAccount::whereIn('companySystemID', $subCompaniesByGroup)
            ->whereIN('bankmasterAutoID', $bankIds)
            ->where('isAccountActive', 1)
            ->with(['currency'])
            ->get(['bankAccountAutoID', 'AccountNo','accountCurrencyID','bankmasterAutoID']);
        $output = array(
            'banks' => $banks,
            'accounts' => $accounts
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }


    public function amendBankTransferReview(Request $request)
    {
        $input = $request->all();

        $id = $input['paymentBankTransferID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = PaymentBankTransfer::find($id);

        if (empty($masterData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_transfer')]));
        }


        $checkBankTransferGenerated = PaymentBankTransfer::where('bankAccountAutoID', $masterData->bankAccountAutoID)
                                                         ->whereDate('documentDate', '>=', Carbon::parse($masterData->documentDate))
                                                         ->where('companySystemID', $masterData->companySystemID)
                                                         ->where('paymentBankTransferID', '!=',$id)
                                                         ->first();

        if ($checkBankTransferGenerated) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_bank_transfer_upcoming_months_bank_transfer_is_already_created'));
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this_bank_transfer_it_is_not_confirmed'));
        }


        $emailBody = '<p>' . $masterData->bankTransferDocumentCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->bankTransferDocumentCode . ' has been return back to amend';

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
                    'docCode' => $masterData->bankTransferDocumentCode
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
                        'docCode' => $masterData->bankTransferDocumentCode
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

            // updating fields
            $masterData->confirmedYN = 0;
            $masterData->confirmedByEmpSystemID = null;
            $masterData->confirmedByEmpID = null;
            $masterData->confirmedByName = null;
            $masterData->confirmedDate = null;
            $masterData->RollLevForApp_curr = 1;

            $masterData->approvedYN = 0;
            $masterData->exportedYN = 0;
            $masterData->approvedByUserSystemID = null;
            $masterData->approvedByUserID = null;
            $masterData->approvedDate = null;
            $masterData->save();

            DB::commit();
            return $this->sendResponse($masterData->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_transfer_amend')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function clearExportBlockConfirm(Request $request)
    {
        $input = $request->all();

        $id = $input['paymentBankTransferID'];
        $masterData = PaymentBankTransfer::find($id);

        if (empty($masterData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_transfer')]));
        }

        $masterData->exportedYN = 0;
        $masterData->save();

        return $this->sendResponse($masterData->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_transfer')]));
    }

    public function getBankLedgerFilterFormData(Request $request)
    {
        $input = $request->all();

        $bankAccounts = DB::table('erp_bankaccount')
                          ->selectRaw('AccountNo, erp_bankmaster.bankName, AccountCode, AccountDescription, bankAccountAutoID')
                          ->join('erp_bankmaster', 'erp_bankaccount.bankmasterAutoID', '=', 'erp_bankmaster.bankmasterAutoID')
                          ->join('chartofaccounts', 'erp_bankaccount.chartOfAccountSystemID', '=', 'chartofaccounts.chartOfAccountSystemID')
                          ->where('companySystemID', $input['selectedCompanyId'])
                          ->get();

        return $this->sendResponse(['accounts' => $bankAccounts], "bank ledger form data retrived successfully");
    } 

    public function validateBankLedgerReport(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'fromDate' => 'required',
            'toDate' => 'required|date|after_or_equal:fromDate',
            'accounts' => 'required',
            'currencyID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        return $this->sendResponse([], "bank ledger report filter validated successfully");
    } 

    public function generateBankLedgerReport(Request $request)
    {
        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
        $checkIsGroup = Company::find($request->companySystemID);

        $output = BankLedgerService::getBankLedgerData($request);

        $requestCurrencyRpt = CurrencyMaster::where('currencyID', $checkIsGroup->reportingCurrency)->first();
        $requestCurrencyLocal = CurrencyMaster::where('currencyID', $checkIsGroup->localCurrencyID)->first();

        if ($request->currencyID == 2) {
            $decimalPlace = $requestCurrencyRpt ? $requestCurrencyRpt->DecimalPlaces : 2;
            $currencyCode = $requestCurrencyRpt ? $requestCurrencyRpt->CurrencyCode : "";
        } else if ($request->currencyID == 3) {
            $decimalPlace = $requestCurrencyLocal ? $requestCurrencyLocal->DecimalPlaces : 2;
            $currencyCode = $requestCurrencyLocal ? $requestCurrencyLocal->CurrencyCode : "";
        } else {
            $decimalPlace = 2;
            $currencyCode = "";
        }

        if (count($request->accounts) == 1) {
            foreach ($output as $key => $value) {
                $value->accountBalance = $this->calculateAccountBalance($output, $key, $request->currencyID);
            }
        }
        
        $total = array();
        $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
        $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
        $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
        $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
        $total['documentBankAmountDebit'] = array_sum(collect($output)->pluck('bankDebit')->toArray());
        $total['documentBankAmountCredit'] = array_sum(collect($output)->pluck('bankCredit')->toArray());


        return \DataTables::of($output)
                        ->addIndexColumn()
                        ->with('companyName', $checkIsGroup->CompanyName)
                        ->with('isGroup', $checkIsGroup->isGroup)
                        ->with('currencyID', $request->currencyID)
                        ->with('total', $total)
                        ->with('decimalPlace', $decimalPlace)
                        ->with('currencyCode', $currencyCode)
                        ->addIndexColumn()
                        ->make(true);
    }

    public function calculateAccountBalance($data, $index, $currencyID)
    {
        $balance = 0;

        foreach ($data as $key => $value) {
            if ($key <= $index) {
                if ($currencyID == 1) {
                    $balance += $value->bankDebit - $value->bankCredit;
                } else if ($currencyID == 2) {
                    $balance += $value->rptDebit - $value->rptCredit;
                } else if ($currencyID == 3) {
                    $balance += $value->localDebit - $value->localCredit;
                }
            }
        }

        return $balance;

    }

    public function exportBankLedgerReport(Request $request)
    {
        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
        $checkIsGroup = Company::find($request->companySystemID);

        $output = BankLedgerService::getBankLedgerData($request);

        if (count($request->accounts) == 1) {
            foreach ($output as $key => $value) {
                $value->accountBalance = $this->calculateAccountBalance($output, $key, $request->currencyID);
            }
        }

        $requestCurrencyRpt = CurrencyMaster::where('currencyID', $checkIsGroup->reportingCurrency)->first();
        $requestCurrencyLocal = CurrencyMaster::where('currencyID', $checkIsGroup->localCurrencyID)->first();

        if ($request->currencyID == 2) {
            $decimalPlace = $requestCurrencyRpt ? $requestCurrencyRpt->DecimalPlaces : 2;
            $currencyCode = $requestCurrencyRpt ? $requestCurrencyRpt->CurrencyCode : "";
        } else if ($request->currencyID == 3) {
            $decimalPlace = $requestCurrencyLocal ? $requestCurrencyLocal->DecimalPlaces : 2;
            $currencyCode = $requestCurrencyLocal ? $requestCurrencyLocal->CurrencyCode : "";
        } else {
            $decimalPlace = 2;
            $currencyCode = "";
        }

        $extraColumns = [];
        if (isset($request->extraColoumns) && count($request->extraColoumns) > 0) {
            $extraColumns = collect($request->extraColoumns)->pluck('id')->toArray();
        }

        
        $total = array();
        $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
        $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
        $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
        $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
        $total['documentBankAmountDebit'] = array_sum(collect($output)->pluck('bankDebit')->toArray());
        $total['documentBankAmountCredit'] = array_sum(collect($output)->pluck('bankCredit')->toArray());


        if ($output) {
            $x = 0;
            $subTotalDebitRpt = 0;
            $subTotalCreditRpt = 0;
            $subTotalDebitLocal = 0;
            $subTotalCreditRptLocal = 0;
            $subTotalDebitBank = 0;
            $subTotalCreditBank = 0;

            $dataArrayNew = array();

            foreach ($output as $val) {
                $data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Company Name'] = $val->CompanyName;
                $data[$x]['Bank'] = $val->bankName;
                $data[$x]['Account No'] = $val->AccountNo;
                $data[$x]['Account Description'] = $val->AccountDescription;
                $data[$x]['Document Number'] = $val->documentCode;
                $data[$x]['Document Type'] = $val->documentID;
                $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                $data[$x]['Document Narration'] = $val->documentNarration;
                $data[$x]['Supplier/Customer'] = $val->partyName;
                if (in_array('confi_name', $extraColumns)) {
                    $data[$x]['Confirmed By'] = $val->confirmBy;
                }

                if (in_array('confi_date', $extraColumns)) {
                    $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->confirmDate);
                }

                if (in_array('app_name', $extraColumns)) {
                    $data[$x]['Approved By'] = $val->approvedBy;
                }

                if (in_array('app_date', $extraColumns)) {
                    $data[$x]['Approved Date'] = \Helper::dateFormat($val->approvedDate);
                }

                if ($request->currencyID == 1) {
                    $data[$x]['Currency'] = $val->bankCurrency;
                    $data[$x]['Debit (Bank Currency)'] = round($val->bankDebit, $val->bankCurrencyDecimal);
                    $data[$x]['Credit (Bank Currency)'] = round($val->bankCredit, $val->bankCurrencyDecimal);
                    
                    if (count($request->accounts) == 1) {
                        $data[$x]['Account Balance'] = isset($val->accountBalance) ? round($val->accountBalance, $val->bankCurrencyDecimal): "";
                    }
                }

                if ($checkIsGroup->isGroup == 0 && $request->currencyID == 3) {
                    $data[$x]['Debit (Local Currency - ' . $currencyCode . ')'] = round($val->localDebit, $decimalPlace);
                    $data[$x]['Credit (Local Currency - ' . $currencyCode . ')'] = round($val->localCredit, $decimalPlace);

                    if (count($request->accounts) == 1) {
                        $data[$x]['Account Balance'] = isset($val->accountBalance) ? round($val->accountBalance, $decimalPlace): "";
                    }
                }

                if($request->currencyID == 2) {
                    $data[$x]['Debit (Reporting Currency - ' . $currencyCode . ')'] = round($val->rptDebit, $decimalPlace);
                    $data[$x]['Credit (Reporting Currency - ' . $currencyCode . ')'] = round($val->rptCredit, $decimalPlace);

                    if (count($request->accounts) == 1) {
                        $data[$x]['Account Balance'] = isset($val->accountBalance) ? round($val->accountBalance, $decimalPlace): "";
                    }
                }




                $subTotalDebitRpt += round($val->rptDebit, $decimalPlace);
                $subTotalCreditRpt += round($val->rptCredit, $decimalPlace);

                $subTotalDebitLocal += round($val->localDebit, $decimalPlace);
                $subTotalCreditRptLocal += round($val->localCredit, $decimalPlace);

                $subTotalDebitBank += round($val->bankDebit, $val->bankCurrencyDecimal);
                $subTotalCreditBank += round($val->bankCredit, $val->bankCurrencyDecimal);
                $x++;
            }
        }
        $data[$x]['Company ID'] = "";
        $data[$x]['Company Name'] = "";
        $data[$x]['Bank'] = "";
        $data[$x]['Account No'] = "";
        $data[$x]['Account Description'] = "";
        $data[$x]['Document Number'] = "";
        $data[$x]['Document Type'] = "";
        $data[$x]['Date'] = "";
        $data[$x]['Document Narration'] = "";

        if (in_array('confi_name', $extraColumns)) {
            $data[$x]['Confirmed By'] = "";
        }

        if (in_array('confi_date', $extraColumns)) {
            $data[$x]['Confirmed Date'] = "";
        }

        if (in_array('app_name', $extraColumns)) {
            $data[$x]['Approved By'] = "";
        }

        if (in_array('app_date', $extraColumns)) {
            $data[$x]['Approved Date'] = "";
        }

        if ($request->currencyID != 1) {
            $data[$x]['Supplier/Customer'] = "Total Amount";
        }

        if ($request->currencyID == 1 && count($request->accounts) == 1) {
            $data[$x]['Supplier/Customer'] = "";
            $data[$x]['Currency'] = "Total Amount";;
            $data[$x]['Debit (Bank Currency)'] = $subTotalDebitBank;
            $data[$x]['Credit (Bank Currency)'] = $subTotalCreditBank;
        }

        if ($checkIsGroup->isGroup == 0 && $request->currencyID == 3) {
            $data[$x]['Debit (Local Currency - ' . $currencyCode . ')'] = round($subTotalDebitLocal, $decimalPlace);
            $data[$x]['Credit (Local Currency - ' . $currencyCode . ')'] = round($subTotalCreditRptLocal, $decimalPlace);
        }

        if($request->currencyID == 2) {
            $data[$x]['Debit (Reporting Currency - ' . $currencyCode . ')'] = round($subTotalDebitRpt, $decimalPlace);
            $data[$x]['Credit (Reporting Currency - ' . $currencyCode . ')'] = round($subTotalCreditRpt, $decimalPlace);
        }

        $x++;
        $data[$x]['Company ID'] = "";
        $data[$x]['Company Name'] = "";
        $data[$x]['Bank'] = "";
        $data[$x]['Account No'] = "";
        $data[$x]['Account Description'] = "";
        $data[$x]['Document Number'] = "";
        $data[$x]['Document Type'] = "";
        $data[$x]['Date'] = "";
        $data[$x]['Document Narration'] = "";

        if (in_array('confi_name', $extraColumns)) {
            $data[$x]['Confirmed By'] = "";
        }

        if (in_array('confi_date', $extraColumns)) {
            $data[$x]['Confirmed Date'] = "";
        }

        if (in_array('app_name', $extraColumns)) {
            $data[$x]['Approved By'] = "";
        }

        if (in_array('app_date', $extraColumns)) {
            $data[$x]['Approved Date'] = "";
        }

        if ($request->currencyID != 1) {
            $data[$x]['Supplier/Customer'] = "Net Amount";
        }

        if ($request->currencyID == 1 && count($request->accounts) == 1) {
            $data[$x]['Supplier/Customer'] = "";
            $data[$x]['Currency'] = "Net Amount";;
            $data[$x]['Debit (Bank Currency)'] = ($subTotalDebitBank - $subTotalCreditBank) > 0 ? ($subTotalDebitBank - $subTotalCreditBank) : "";;
            $data[$x]['Credit (Bank Currency)'] = ($subTotalDebitBank - $subTotalCreditBank) < 0 ? ($subTotalDebitBank - $subTotalCreditBank) * -1 : "";;
        }


        if ($checkIsGroup->isGroup == 0 && $request->currencyID == 3) {
            $data[$x]['Debit (Local Currency - ' . $currencyCode . ')'] = ($subTotalDebitLocal - $subTotalCreditRptLocal) > 0 ? round($subTotalDebitLocal - $subTotalCreditRptLocal, $decimalPlace) : "";
            $data[$x]['Credit (Local Currency - ' . $currencyCode . ')'] = ($subTotalDebitLocal - $subTotalCreditRptLocal) < 0 ? round(($subTotalDebitLocal - $subTotalCreditRptLocal) * -1, $decimalPlace) : "";
        }

        if($request->currencyID == 2) {
            $data[$x]['Debit (Reporting Currency - ' . $currencyCode . ')'] = ($subTotalDebitRpt - $subTotalCreditRpt) > 0 ? round($subTotalDebitRpt - $subTotalCreditRpt, $decimalPlace) : "";
            $data[$x]['Credit (Reporting Currency - ' . $currencyCode . ')'] = ($subTotalDebitRpt - $subTotalCreditRpt) < 0 ? round(($subTotalDebitRpt - $subTotalCreditRpt) * -1, $decimalPlace) : "";
        }

        $type = $request->type;
        $company_name = $checkIsGroup->CompanyName;
        $companyCode = isset($checkIsGroup->CompanyID)?$checkIsGroup->CompanyID:'common';

        $to_date = \Helper::dateFormat($request->toDate);
        $from_date = \Helper::dateFormat($request->fromDate);
        $cur = null;
        $title = "Bank Ledeger Details";
        $detail_array = array(  'type' => 1,
                                'from_date'=>$from_date,
                                'to_date'=>$to_date,
                                'company_name'=>$company_name,
                                'cur'=>$cur,
                                'title'=>$title,'company_code'=>$companyCode);

        $fileName = 'bank_ledger';
        $path = 'bank-ledger/report/bank_ledger/excel/';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);


        if($basePath == '') {
             return $this->sendError('Unable to export excel');
        } else {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

    public function generateBankLedgerReportPDF(Request $request)
    {
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', -1);
        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

        $db = isset($request->db) ? $request->db : ""; 

        $employeeID = \Helper::getEmployeeSystemID();
        BankLedgerPdfJob::dispatch($db, $request, [$employeeID])->onQueue('reporting');

        return $this->sendResponse([], "Bank Ledger PDF report has been sent to queue");
    }
}

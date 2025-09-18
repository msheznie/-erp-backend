<?php
/**
 * =============================================
 * -- File Name : MatchDocumentMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  MatchDocumentMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 13 - September 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 13-September 2018 By: Nazir Description: Added new functions named as getMatchDocumentMasterFormData() For load Master View
 * -- Date: 13-September 2018 By: Nazir Description: Added new functions named as getMatchDocumentMasterView()
 * -- Date: 18-September 2018 By: Nazir Description: Added new functions named as getPaymentVoucherMatchPullingDetail()
 * -- Date: 24-September 2018 By: Nazir Description: Added new functions named as getMatchDocumentMasterRecord()
 * -- Date: 02-October 2018 By: Nazir Description: Added new functions named as PaymentVoucherMatchingCancel()
 * -- Date: 16-October 2018 By: Nazir Description: Added new functions named as getRVMatchDocumentMasterView()
 * -- Date: 17-October 2018 By: Nazir Description: Added new functions named as getReceiptVoucherMatchItems()
 * -- Date: 22-October 2018 By: Nazir Description: Added new functions named as getReceiptVoucherPullingDetail()
 * -- Date: 25-October 2018 By: Nazir Description: Added new functions named as receiptVoucherMatchingCancel()
 * -- Date: 25-October 2018 By: Nazir Description: Added new functions named as updateReceiptVoucherMatching()
 * -- Date: 10-January 2019 By: Nazir Description: Added new functions named as printPaymentMatching()
 * -- Date: 17-January 2019 By: Nazir Description: Added new functions named as deleteAllPVMDetails()
 * -- Date: 09-July 2019 By: Fayas Description: Added new functions named as amendReceiptMatchingReview()
 */
namespace App\Http\Controllers\API;

use App\helper\CustomValidation;
use App\Services\TaxLedger\PaymentVoucherTaxLedgerService;
use App\helper\Helper;
use App\Http\Requests\API\CreateMatchDocumentMasterAPIRequest;
use App\Http\Requests\API\UpdateMatchDocumentMasterAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Models\AccountsReceivableLedger;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\BookInvSuppMaster;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CurrencyMaster;
use App\Models\TaxLedgerDetail;
use App\Models\CustomerAssigned;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DirectReceiptDetail;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\TaxLedger;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeLedger;
use App\Repositories\MatchDocumentMasterRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;
use App\helper\CurrencyValidation;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SystemGlCodeScenarioDetail;
use App\helper\TaxService;
use App\Models\DocumentSystemMapping;
use App\Services\GeneralLedger\GlPostedDateService;
use App\Models\Taxdetail;
use App\Services\GeneralLedgerService;
use App\Services\TaxLedger\RecieptVoucherTaxLedgerService;
use App\Services\ValidateDocumentAmend;

/**
 * Class MatchDocumentMasterController
 * @package App\Http\Controllers\API
 */
class MatchDocumentMasterAPIController extends AppBaseController
{
    /** @var  MatchDocumentMasterRepository */
    private $matchDocumentMasterRepository;

    public function __construct(MatchDocumentMasterRepository $matchDocumentMasterRepo)
    {
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/matchDocumentMasters",
     *      summary="Get a listing of the MatchDocumentMasters.",
     *      tags={"MatchDocumentMaster"},
     *      description="Get all MatchDocumentMasters",
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
     *                  @SWG\Items(ref="#/definitions/MatchDocumentMaster")
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
        $this->matchDocumentMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->matchDocumentMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $matchDocumentMasters = $this->matchDocumentMasterRepository->all();

        return $this->sendResponse($matchDocumentMasters->toArray(), trans('custom.match_document_masters_retrieved_successfully'));
    }

    /**
     * @param CreateMatchDocumentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/matchDocumentMasters",
     *      summary="Store a newly created MatchDocumentMaster in storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Store MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MatchDocumentMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MatchDocumentMaster")
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
     *                  ref="#/definitions/MatchDocumentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMatchDocumentMasterAPIRequest $request)
    {
        $input = $request->all();

        if ($input['tempType'] == 'PVM') {

            $input = $this->convertArrayToValue($input);
            if (!isset($input['paymentAutoID'])) {

                if($input['matchType'] == 1 || $input['matchType'] == 3)
                {
                    return $this->sendError(trans('custom.please_select_payment_voucher'), 500);
                }
                else if($input['matchType'] == 2)
                {
                    return $this->sendError(trans('custom.please_select_debit_note'), 500);
                }
               
            }
            if(isset($input['matchType']) && $input['matchType'] == 1 && isset($input['user_type']) && $input['user_type'] == 2) {
                $isEmpAdvConfigured = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], 4, "employee-advance-account");

                if (is_null($isEmpAdvConfigured)) {
                    return $this->sendError('Please configure employee advance account for this company', 500);
                }
            }
            
            $validator = \Validator::make($input, [
                'companySystemID' => 'required',
                'matchType' => 'required',
                'paymentAutoID' => 'required',
                'employeeID' => ['required_if:user_type,2'],
                'tempType' => 'required',
                'supplierID' => ['required_if:user_type,1'],
            ],
            [
                'employeeID.required_if' => 'Please select an employee',
                'supplierID.required_if' => 'Please select a supplier',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            
            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            if ($input['matchType'] == 1) {
                $user_type = ($input['user_type']);
                $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($input['paymentAutoID']);

                if (empty($paySupplierInvoiceMaster)) {
                    return $this->sendError(trans('custom.pay_supplier_invoice_master_not_found'));
                }

                $existCheck = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                    ->where('PayMasterAutoId', $input['paymentAutoID'])
                    ->where('matchingConfirmedYN', 0)
                    ->whereNull('matchingOption')
                    ->where('documentSystemID',$paySupplierInvoiceMaster->documentSystemID)
                    ->first();

                if($existCheck){
                    return $this->sendError(trans('custom.a_matching_document_for_the_selected_advanced_paym'), 500);
                }

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)->where('documentSystemCode', $input['paymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                        return $this->sendError(trans('custom.selected_payment_voucher_is_not_updated_in_general'), 500);
                    }
                } else {
                    return $this->sendError(trans('custom.selected_payment_voucher_is_not_updated_in_general'), 500);
                }

                //when adding a new matching, checking whether advance payment more than the document value
                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input['paymentAutoID'])->whereNull('matchingOption')->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }

                if ($paySupplierInvoiceMaster->payAmountSuppTrans == $machAmount || $machAmount > $paySupplierInvoiceMaster->payAmountSuppTrans) {
                    return $this->sendError('Advance payment amount is more than document value, please check again', 500);
                }

                $input['matchingType'] = 'AP';
                $input['PayMasterAutoId'] = $input['paymentAutoID'];
                $input['documentSystemID'] = $paySupplierInvoiceMaster->documentSystemID;
                $input['documentID'] = $paySupplierInvoiceMaster->documentID;
                $input['BPVcode'] = $paySupplierInvoiceMaster->BPVcode;
                $input['BPVdate'] = $paySupplierInvoiceMaster->BPVdate;
                $input['BPVNarration'] = $paySupplierInvoiceMaster->BPVNarration;
                $input['directPaymentPayeeSelectEmp'] = $paySupplierInvoiceMaster->directPaymentPayeeSelectEmp;
                $input['directPaymentPayee'] = $paySupplierInvoiceMaster->directPaymentPayee;
                $input['directPayeeCurrency'] = $paySupplierInvoiceMaster->directPayeeCurrency;
                
                
                if($user_type == 1)
                {
                    $input['BPVsupplierID'] = $paySupplierInvoiceMaster->BPVsupplierID;
                    $input['supplierGLCodeSystemID'] = $paySupplierInvoiceMaster->supplierGLCodeSystemID;
                    $input['supplierGLCode'] = $paySupplierInvoiceMaster->supplierGLCode;
                }
                else if($user_type == 2)
                {
                    $input['employee_id'] = $paySupplierInvoiceMaster->directPaymentPayeeEmpID;
                    $input['employeeGLCodeSystemID'] = $paySupplierInvoiceMaster->AdvanceAccount;
                    $input['employeeGLCode'] = $paySupplierInvoiceMaster->advanceAccountSystemID;
                }

                $input['supplierTransCurrencyID'] = $paySupplierInvoiceMaster->supplierTransCurrencyID;
                $input['supplierTransCurrencyER'] = $paySupplierInvoiceMaster->supplierTransCurrencyER;
                $input['supplierDefCurrencyID'] = $paySupplierInvoiceMaster->supplierDefCurrencyID;
                $input['supplierDefCurrencyER'] = $paySupplierInvoiceMaster->supplierDefCurrencyER;
                $input['localCurrencyID'] = $paySupplierInvoiceMaster->localCurrencyID;
                $input['localCurrencyER'] = $paySupplierInvoiceMaster->localCurrencyER;
                $input['companyRptCurrencyID'] = $paySupplierInvoiceMaster->companyRptCurrencyID;
                $input['companyRptCurrencyER'] = $paySupplierInvoiceMaster->companyRptCurrencyER;
                $input['payAmountBank'] = $paySupplierInvoiceMaster->payAmountBank;
                $input['payAmountSuppTrans'] = $paySupplierInvoiceMaster->payAmountSuppTrans;
                $input['payAmountSuppDef'] = $paySupplierInvoiceMaster->payAmountSuppDef;
                $input['suppAmountDocTotal'] = $paySupplierInvoiceMaster->suppAmountDocTotal;
                $input['payAmountCompLocal'] = $paySupplierInvoiceMaster->payAmountCompLocal;
                $input['payAmountCompRpt'] = $paySupplierInvoiceMaster->payAmountCompRpt;
                $input['invoiceType'] = $paySupplierInvoiceMaster->invoiceType;
                $input['matchInvoice'] = $paySupplierInvoiceMaster->matchInvoice;
                $input['matchingAmount'] = 0;

                $input['confirmedYN'] = $paySupplierInvoiceMaster->confirmedYN;
                $input['confirmedByEmpID'] = $paySupplierInvoiceMaster->confirmedByEmpID;
                $input['confirmedByEmpSystemID'] = $paySupplierInvoiceMaster->confirmedByEmpSystemID;
                $input['confirmedByName'] = $paySupplierInvoiceMaster->confirmedByName;
                $input['confirmedDate'] = $paySupplierInvoiceMaster->confirmedDate;
                $input['approved'] = $paySupplierInvoiceMaster->approved;
                $input['approvedDate'] = $paySupplierInvoiceMaster->approvedDate;

            } else if ($input['matchType'] == 2) {
                $debitNoteMaster = DebitNote::find($input['paymentAutoID']);
                $user_type = ($input['user_type']);
                if (empty($debitNoteMaster)) {
                    return $this->sendError(trans('custom.debit_note_not_found'));
                }
                
                $existCheck = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                    ->where('PayMasterAutoId', $input['paymentAutoID'])
                    ->where('matchingConfirmedYN', 0)
                    ->where('documentSystemID', 15)
                    ->whereNull('matchingOption')
                    ->first();

                if($existCheck){
                    return $this->sendError(trans('custom.a_matching_document_for_the_selected_debit_note_is'), 500);
                }

                //when adding a new matching, checking whether debit note added in general ledger
                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $debitNoteMaster->documentSystemID)->where('companySystemID', $debitNoteMaster->companySystemID)->where('documentSystemCode', $input['paymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                        return $this->sendError(trans('custom.selected_debit_note_is_not_updated_in_general_ledg'), 500);
                    }
                } else {
                    return $this->sendError(trans('custom.selected_debit_note_is_not_updated_in_general_ledg'), 500);
                }

                //when adding a new matching, checking whether debit amount more than the document value
                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, addedDocumentSystemID, bookingInvSystemCode, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                    ->where('addedDocumentSystemID', $debitNoteMaster->documentSystemID)
                    ->where('bookingInvSystemCode', $debitNoteMaster->debitNoteAutoID)
                    ->groupBy('addedDocumentSystemID', 'bookingInvSystemCode')
                    ->first();

                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')
                    ->where('PayMasterAutoId', $input['paymentAutoID'])
                    ->where('documentSystemID', $debitNoteMaster->documentSystemID)
                    ->whereNull('matchingOption')
                    ->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')
                    ->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }

                $totalPaidAmount = (($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] * -1) + $machAmount);

                if ($debitNoteMaster->debitAmountTrans == $totalPaidAmount || $totalPaidAmount > $debitNoteMaster->debitAmountTrans) {
                    return $this->sendError('Debit note amount is more than document value, please check again', 500);
                }

                $input['matchingType'] = 'AP';
                $input['PayMasterAutoId'] = $input['paymentAutoID'];
                $input['documentSystemID'] = $debitNoteMaster->documentSystemID;
                $input['documentID'] = $debitNoteMaster->documentID;
                $input['BPVcode'] = $debitNoteMaster->debitNoteCode;
                $input['BPVdate'] = $debitNoteMaster->debitNoteDate;
                $input['BPVNarration'] = $debitNoteMaster->comments;
                $input['directPaymentPayeeSelectEmp'] = $debitNoteMaster->directPaymentPayeeSelectEmp;
                //$input['directPaymentPayee'] = $debitNoteMaster->directPaymentPayee;
                $input['directPayeeCurrency'] = $debitNoteMaster->supplierTransactionCurrencyID;
                if($user_type == 1)
                {
                    $input['BPVsupplierID'] = $debitNoteMaster->supplierID;
                    $input['supplierGLCodeSystemID'] = $debitNoteMaster->supplierGLCodeSystemID;
                    $input['supplierGLCode'] = $debitNoteMaster->supplierGLCode;
               
                }
                else if($user_type == 2)
                {
                    $input['employee_id'] = $debitNoteMaster->empID;
                    $input['employeeGLCodeSystemID'] = $debitNoteMaster->empControlAccount;
                    $input['employeeGLCode'] = ChartOfAccount::getGlAccountCode($input['employeeGLCodeSystemID']);
                }

                $input['supplierTransCurrencyID'] = $debitNoteMaster->supplierTransactionCurrencyID;
                $input['supplierTransCurrencyER'] = $debitNoteMaster->supplierTransactionCurrencyER;
                $input['supplierDefCurrencyID'] = $debitNoteMaster->supplierTransactionCurrencyID;
                $input['supplierDefCurrencyER'] = $debitNoteMaster->supplierTransactionCurrencyER;
                $input['localCurrencyID'] = $debitNoteMaster->localCurrencyID;
                $input['localCurrencyER'] = $debitNoteMaster->localCurrencyER;
                $input['companyRptCurrencyID'] = $debitNoteMaster->companyReportingCurrencyID;
                $input['companyRptCurrencyER'] = $debitNoteMaster->companyReportingER;
                //$input['payAmountBank'] = $debitNoteMaster->payAmountBank;
                $input['payAmountSuppTrans'] = $debitNoteMaster->debitAmountTrans;
                $input['payAmountSuppDef'] = $debitNoteMaster->debitAmountTrans;
                //$input['suppAmountDocTotal'] = $debitNoteMaster->suppAmountDocTotal;
                $input['payAmountCompLocal'] = $debitNoteMaster->debitAmountLocal;
                $input['payAmountCompRpt'] = $debitNoteMaster->debitAmountRpt;
                $input['invoiceType'] = $debitNoteMaster->documentType;
                $input['matchingAmount'] = 0;
                $input['confirmedYN'] = $debitNoteMaster->confirmedYN;
                $input['confirmedByEmpID'] = $debitNoteMaster->confirmedByEmpID;
                $input['confirmedByEmpSystemID'] = $debitNoteMaster->confirmedByEmpSystemID;
                $input['confirmedByName'] = $debitNoteMaster->confirmedByName;
                $input['confirmedDate'] = $debitNoteMaster->confirmedDate;
                $input['approved'] = $debitNoteMaster->approved;
                $input['approvedDate'] = $debitNoteMaster->approvedDate;
            }
            if ($input['matchType'] == 3) {
                
                $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($input['paymentAutoID']);

                if (empty($paySupplierInvoiceMaster)) {
                    return $this->sendError(trans('custom.pay_supplier_invoice_master_not_found'));
                }

                $existCheck = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                    ->where('PayMasterAutoId', $input['paymentAutoID'])
                    ->where('matchingConfirmedYN', 0)
                    ->where('matchingOption', 1)
                    ->where('documentSystemID',$paySupplierInvoiceMaster->documentSystemID)
                    ->first();

                if($existCheck){
                    return $this->sendError(trans('custom.a_matching_document_for_the_selected_advanced_paym'), 500);
                }

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)->where('documentSystemCode', $input['paymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                        return $this->sendError(trans('custom.selected_payment_voucher_is_not_updated_in_general'), 500);
                    }
                } else {
                    return $this->sendError(trans('custom.selected_payment_voucher_is_not_updated_in_general'), 500);
                }

                //when adding a new matching, checking whether advance payment more than the document value
                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input['paymentAutoID'])->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)->where('matchingOption', 1)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }

                if ($paySupplierInvoiceMaster->payAmountSuppTrans == $machAmount || $machAmount > $paySupplierInvoiceMaster->payAmountSuppTrans) {
                    return $this->sendError('Advance payment amount is more than document value, please check again', 500);
                }

                $input['matchingType'] = 'AP';
                $input['PayMasterAutoId'] = $input['paymentAutoID'];
                $input['documentSystemID'] = $paySupplierInvoiceMaster->documentSystemID;
                $input['documentID'] = $paySupplierInvoiceMaster->documentID;
                $input['BPVcode'] = $paySupplierInvoiceMaster->BPVcode;
                $input['BPVdate'] = $paySupplierInvoiceMaster->BPVdate;
                $input['BPVNarration'] = $paySupplierInvoiceMaster->BPVNarration;
                $input['directPaymentPayeeSelectEmp'] = $paySupplierInvoiceMaster->directPaymentPayeeSelectEmp;
                $input['directPaymentPayee'] = $paySupplierInvoiceMaster->directPaymentPayee;
                $input['directPayeeCurrency'] = $paySupplierInvoiceMaster->directPayeeCurrency;
                $input['BPVsupplierID'] = $paySupplierInvoiceMaster->BPVsupplierID;
                $input['supplierGLCodeSystemID'] = $paySupplierInvoiceMaster->supplierGLCodeSystemID;
                $input['supplierGLCode'] = $paySupplierInvoiceMaster->supplierGLCode;
                $input['supplierTransCurrencyID'] = $paySupplierInvoiceMaster->supplierTransCurrencyID;
                $input['supplierTransCurrencyER'] = $paySupplierInvoiceMaster->supplierTransCurrencyER;
                $input['supplierDefCurrencyID'] = $paySupplierInvoiceMaster->supplierDefCurrencyID;
                $input['supplierDefCurrencyER'] = $paySupplierInvoiceMaster->supplierDefCurrencyER;
                $input['localCurrencyID'] = $paySupplierInvoiceMaster->localCurrencyID;
                $input['localCurrencyER'] = $paySupplierInvoiceMaster->localCurrencyER;
                $input['companyRptCurrencyID'] = $paySupplierInvoiceMaster->companyRptCurrencyID;
                $input['companyRptCurrencyER'] = $paySupplierInvoiceMaster->companyRptCurrencyER;
                $input['payAmountBank'] = $paySupplierInvoiceMaster->payAmountBank;
                $input['payAmountSuppTrans'] = $paySupplierInvoiceMaster->payAmountSuppTrans;
                $input['payAmountSuppDef'] = $paySupplierInvoiceMaster->payAmountSuppDef;
                $input['suppAmountDocTotal'] = $paySupplierInvoiceMaster->suppAmountDocTotal;
                $input['payAmountCompLocal'] = $paySupplierInvoiceMaster->payAmountCompLocal;
                $input['payAmountCompRpt'] = $paySupplierInvoiceMaster->payAmountCompRpt;
                $input['invoiceType'] = $paySupplierInvoiceMaster->invoiceType;
                $input['matchingOption'] = 1;//$input['matchingOptionID'];
                $input['matchInvoice'] = $paySupplierInvoiceMaster->matchInvoice;
                $input['matchingAmount'] = 0;

                $input['confirmedYN'] = $paySupplierInvoiceMaster->confirmedYN;
                $input['confirmedByEmpID'] = $paySupplierInvoiceMaster->confirmedByEmpID;
                $input['confirmedByEmpSystemID'] = $paySupplierInvoiceMaster->confirmedByEmpSystemID;
                $input['confirmedByName'] = $paySupplierInvoiceMaster->confirmedByName;
                $input['confirmedDate'] = $paySupplierInvoiceMaster->confirmedDate;
                $input['approved'] = $paySupplierInvoiceMaster->approved;
                $input['approvedDate'] = $paySupplierInvoiceMaster->approvedDate;

            }            


            $input['matchingDocCode'] = 0;
            $input['matchingDocdate'] = date('Y-m-d H:i:s');

            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();


            $currentFinanceYear = \Helper::companyFinanceYear($input['companySystemID'], 0);


            if(isset($currentFinanceYear) && count($currentFinanceYear) > 0)
            {
                
                $companyfinanceyear = CompanyFinanceYear::select('bigginingDate','endingDate')->where('companyFinanceYearID', $currentFinanceYear[0]->companyFinanceYearID)
                ->where('companySystemID', $input['companySystemID'])
                ->first();
                    if ($companyfinanceyear) {
                        $input['companyFinanceYearID'] = $currentFinanceYear[0]->companyFinanceYearID;

                        $companyFinancePeriod = CompanyFinancePeriod::select('companyFinancePeriodID')->where('companySystemID', '=', $input['companySystemID'])
                        ->where('companyFinanceYearID', $currentFinanceYear[0]->companyFinanceYearID)
                        ->where('departmentSystemID', 1)
                        ->where('isActive', -1)
                        ->where('isCurrent', -1)
                        ->first();

                        if($companyFinancePeriod)
                        {
                            $input['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
                        }

                    }
            }
            
            $matchDocumentMasters = $this->matchDocumentMasterRepository->create($input);

            return $this->sendResponse($matchDocumentMasters->toArray(), trans('custom.match_document_master_saved_successfully'));
        }
        elseif ($input['tempType'] == 'RVM') {

            if (!isset($input['custReceivePaymentAutoID'])) {
                return $this->sendError(trans('custom.please_select_receipt_voucher'), 500);
            }

            $validator = \Validator::make($request->all(), [
                'companySystemID' => 'required',
                'matchType' => 'required',
                'custReceivePaymentAutoID' => 'required',
                'customerID' => 'required',
                'tempType' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }
            $input['isAutoCreateDocument'] = false;
            $matchDocumentMasters = \App\Services\API\ReceiptMatchingAPIService::createReceiptMatching($input);
            if($matchDocumentMasters['status'] == true){
                return $this->sendResponse($matchDocumentMasters['data'], trans('custom.match_document_master_saved_successfully'));
            }else{
                return $this->sendError($matchDocumentMasters['message'], 500);
            }

        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Display the specified MatchDocumentMaster",
     *      tags={"MatchDocumentMaster"},
     *      description="Get MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
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
     *                  ref="#/definitions/MatchDocumentMaster"
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
        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->with(['created_by', 'confirmed_by', 'company', 'modified_by','localcurrency','rptcurrency','supplier','customer','employee', 'payment_voucher','reciept_voucher'])->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError(trans('custom.match_document_master_not_found_1'));
        }

        return $this->sendResponse($matchDocumentMaster->toArray(), trans('custom.match_document_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMatchDocumentMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Update the specified MatchDocumentMaster in storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Update MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MatchDocumentMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MatchDocumentMaster")
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
     *                  ref="#/definitions/MatchDocumentMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMatchDocumentMasterAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $created_by = $input['created_by'];
            $input = array_except($input, ['created_by', 'BPVsupplierID', 'company', 'confirmed_by', 'modified_by','localcurrency','rptcurrency','supplier','employee','customer', 'payment_voucher','reciept_voucher']);
            $input = $this->convertArrayToValue($input);

            
            $employee = \Helper::getEmployeeInfo();

            /** @var MatchDocumentMaster $matchDocumentMaster */
            $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

            $user_type = $matchDocumentMaster->user_type;

            if (empty($matchDocumentMaster)) {
                return $this->sendError(trans('custom.match_document_master_not_found_1'));
            }

            $user_type = $matchDocumentMaster->user_type;
            if (isset($input['matchingDocdate'])) {
                if ($input['matchingDocdate']) {
                    $input['matchingDocdate'] = new Carbon($input['matchingDocdate']);
                }
            }

            if(!isset($input['companyFinanceYearID']) )
            {
                return $this->sendError('No Active Finance Year Found', 500);
            }
           

            if($input['matchingType'] == 'AP'){
                $department = 1;
            }elseif ($input['matchingType'] == 'AR'){
                $department = 4;
            }else{
                return $this->sendError('Matching Type Found', 500);
            }


            if(($input['companyFinancePeriodID']) == null)
            {
                return $this->sendError('No Active Finance Year Found', 500);
            }
        
            $companyFinancePeriods = CompanyFinancePeriod::where('companyFinancePeriodID',$input['companyFinancePeriodID'])->get();

            $isInFinancePeriod = false;
            foreach ($companyFinancePeriods as $period) {

                $FYPeriodDateFrom = $period->dateFrom;
                $FYPeriodDateTo = $period->dateTo;

                if($input['matchingDocdate'] >= $FYPeriodDateFrom && $input['matchingDocdate'] <= $FYPeriodDateTo){
                    $isInFinancePeriod = true;
                    break;
                }
            }

            if(!$isInFinancePeriod){
                return $this->sendError(trans('custom.document_date_should_be_between_financial_period_start_end'),500);
            }


            
            // end of check date within financial period
            if($matchDocumentMaster->matchingOption != 1) {
                $detailAmountTotTran = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                    ->sum('supplierPaymentAmount');

                $detailAmountTotLoc = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                    ->sum('paymentLocalAmount');

                $detailAmountTotRpt = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                    ->sum('paymentComRptAmount');

                $input['matchingAmount'] = $detailAmountTotTran;
                $input['matchedAmount'] = $detailAmountTotTran;
                $input['matchLocalAmount'] = \Helper::roundValue($detailAmountTotLoc);
                $input['matchRptAmount'] = \Helper::roundValue($detailAmountTotRpt);
            }
            if($matchDocumentMaster->matchingOption == 1) {
                $detailAmountTotTran = AdvancePaymentDetails::where('matchingDocID', $id)
                    ->sum('supplierTransAmount');

                $detailAmountTotLoc = AdvancePaymentDetails::where('matchingDocID', $id)
                    ->sum('localAmount');

                $detailAmountTotRpt = AdvancePaymentDetails::where('matchingDocID', $id)
                    ->sum('comRptAmount');

                $input['matchingAmount'] = $detailAmountTotTran;
                $input['matchedAmount'] = $detailAmountTotTran;
                $input['matchLocalAmount'] = \Helper::roundValue($detailAmountTotLoc);
                $input['matchRptAmount'] = \Helper::roundValue($detailAmountTotRpt);


            }

            //checking below posted data
            if ($input['documentSystemID'] == 4) {

                $paySupplierInvoice = PaySupplierInvoiceMaster::find($matchDocumentMaster->PayMasterAutoId);

                $postedDate = date("Y-m-d", strtotime($paySupplierInvoice->postedDate));

                $formattedMatchingDate = date("Y-m-d", strtotime($input['matchingDocdate']));

                if ($formattedMatchingDate < $postedDate) {
                    return $this->sendError(trans('custom.advance_payment_posted_date_error', ['date' => $postedDate]), 500);
                }

            } elseif ($input['documentSystemID'] == 15) {

                $DebitNoteMaster = DebitNote::find($matchDocumentMaster->PayMasterAutoId);

                $postedDate = date("Y-m-d", strtotime($DebitNoteMaster->postedDate));

                $formattedMatchingDate = date("Y-m-d", strtotime($input['matchingDocdate']));

                if ($formattedMatchingDate < $postedDate) {
                    return $this->sendError(trans('custom.debit_note_posted_date_error', ['date' => $postedDate]), 500);
                }
            }

            $customValidation = CustomValidation::validation(70, $matchDocumentMaster, 2, $input);

            
            if (!$customValidation["success"]) {
                return $this->sendError($customValidation["message"], 500, array('type' => 'already_confirmed'));
            }
          
            if ($matchDocumentMaster->matchingConfirmedYN == 0 && $input['matchingConfirmedYN'] == 1) {

                if($matchDocumentMaster->matchingOption != 1) {
                    $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('matchingDocID'))
                        ->where('matchingDocID', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError(trans('custom.matching_document_cannot_confirm_without_details'), 500, ['type' => 'confirm']);
                    }
                }

                if($matchDocumentMaster->matchingOption == 1) {
                    $pvDetailExist = AdvancePaymentDetails::select(DB::raw('matchingDocID'))
                        ->where('matchingDocID', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError(trans('custom.matching_document_cannot_confirm_without_details'), 500, ['type' => 'confirm']);
                    }
                }
                $currencyValidate = CurrencyValidation::validateCurrency("payment_matching", $matchDocumentMaster);
                if (!$currencyValidate['status']) {
                    return $this->sendError($currencyValidate['message'], 500, ['type' => 'confirm']);
                }
                if($matchDocumentMaster->matchingOption != 1) {

                    $checkAmount = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                        ->where('supplierPaymentAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError(trans('custom.matching_amount_cannot_be_0'), 500, ['type' => 'confirm']);
                    }
                }

                if($matchDocumentMaster->matchingOption == 1) {

                    $checkAmount = AdvancePaymentDetails::where('matchingDocID', $id)
                        ->where('supplierTransAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError(trans('custom.matching_amount_cannot_be_0'), 500, ['type' => 'confirm']);
                    }
                }


                if ($input['matchingDocCode'] == 0) {

                    $company = Company::find($input['companySystemID']);

                    $lastSerial = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                        ->where('matchDocumentMasterAutoID', '<>', $input['matchDocumentMasterAutoID'])
                        ->where('matchingType', 'AP')
                        ->orderBy('serialNo', 'desc')
                        ->first();

                    $lastSerialNumber = 1;
                    if ($lastSerial) {
                        $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                    }

                    $matchingDocCode = ($company->CompanyID . '\\' . 'MT' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

                    $input['serialNo'] = $lastSerialNumber;
                    $input['matchingDocCode'] = $matchingDocCode;
                }

             
                //
                $itemExistArray = array();
                if($matchDocumentMaster->matchingOption != 1) {

                      // return $user_type;
                    $pvDetailExist = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                        ->get();

                    foreach ($pvDetailExist as $item) {


                        if($user_type == 1)
                        {
                            $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                            ->where('apAutoID', $item['apAutoID'])
                            ->whereHas('matching_master',function($query){
                                $query->where('user_type',1);
                             })
                            ->first();

                        }
                        else if($user_type == 2)
                        {
                            $payDetailMoreBooked = PaySupplierInvoiceDetail::selectRaw('IFNULL(SUM(IFNULL(supplierPaymentAmount,0)),0) as supplierPaymentAmount')
                            ->where('apAutoID', $item['apAutoID'])
                            ->whereHas('matching_master',function($query){
                                $query->where('user_type',2);
                             })
                            ->first();
                        }

                      

                        if ($item['addedDocumentSystemID'] == 11) {
                            //supplier invoice

                            if (Helper::roundValue($item['supplierInvoiceAmount'] - $payDetailMoreBooked->supplierPaymentAmount) < 0) {
                                $itemDrt = "Selected invoice " . $item['bookingInvDocCode'] . " booked more than the invoice amount.";
                                $itemExistArray[] = [$itemDrt];
                            }
                        }
                    }
                    
                    if (!empty($itemExistArray)) {
                        return $this->sendError($itemExistArray, 422);
                    }

                    $detailAmountTotTran = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                        ->sum('supplierPaymentAmount');

                    if (($detailAmountTotTran - $input['matchBalanceAmount']) > 0.00001) {
                        return $this->sendError(trans('custom.detail_amount_cannot_be_greater_than_balance_amoun'), 500, ['type' => 'confirm']);
                    }

                    // updating flags in accounts payable ledger
                    $pvDetailExist = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                        ->get();

                        

                       
                    foreach ($pvDetailExist as $val) {

                        if($user_type == 2)
                        {
                           
                            $updatePayment = EmployeeLedger::find($val->apAutoID);
                        }
                        else
                        {
                            $updatePayment = AccountsPayableLedger::find($val->apAutoID);
                        }
                        
                        if ($updatePayment) {


                            if($user_type == 2)
                            {
                                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                ->where('apAutoID', $val->apAutoID)
                                ->whereHas('matching_master',function($query){
                                    $query->where('user_type',2);
                                 })
                                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                ->first();
                            }
                            else if($user_type == 1)
                            {
                                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                                ->where('apAutoID', $val->apAutoID)
                                ->whereHas('matching_master',function($query){
                                    $query->where('user_type',1);
                                 })
                                ->groupBy('erp_paysupplierinvoicedetail.apAutoID')
                                ->first();
                            }
                       

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
                            }
                        }
                    }

                    //updating master table
                    if ($matchDocumentMaster->documentSystemID == 4) {

                        $paySupplierInvoice = PaySupplierInvoiceMaster::find($matchDocumentMaster->PayMasterAutoId);
                        
                        if($matchDocumentMaster->matchingOption != 1) {
                            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->whereNull('matchingOption')->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)->where('documentSystemID', $matchDocumentMaster->documentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                        }
                        else
                        {
                            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('matchingOption',1)->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)->where('documentSystemID', $matchDocumentMaster->documentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                        }


                        $machAmount = 0;
                        if ($matchedAmount) {
                            $machAmount = $matchedAmount["SumOfmatchedAmount"];
                        }

                        if ($machAmount == 0) {
                            $paySupplierInvoice->matchInvoice = 0;
                            $paySupplierInvoice->save();
                        } else if ($paySupplierInvoice->payAmountSuppTrans == $machAmount || $machAmount > $paySupplierInvoice->payAmountSuppTrans) {
                            $paySupplierInvoice->matchInvoice = 2;
                            $paySupplierInvoice->save();
                        } else if (($paySupplierInvoice->payAmountSuppTrans > $machAmount) && ($machAmount > 0)) {
                            $paySupplierInvoice->matchInvoice = 1;
                            $paySupplierInvoice->save();
                        }

                    } elseif ($matchDocumentMaster->documentSystemID == 15) {

                        $DebitNoteMaster = DebitNote::find($matchDocumentMaster->PayMasterAutoId);

                        //when adding a new matching, checking whether debit amount more than the document value
                        $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.supplierInvoiceAmount, addedDocumentSystemID, bookingInvSystemCode, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')
                            ->where('addedDocumentSystemID', $DebitNoteMaster->documentSystemID)
                            ->where('bookingInvSystemCode', $DebitNoteMaster->debitNoteAutoID)
                            ->groupBy('addedDocumentSystemID', 'bookingInvSystemCode')
                            ->first();

                        if($matchDocumentMaster->matchingOption != 1) {
                            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->whereNull('matchingOption')->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)->where('documentSystemID', $matchDocumentMaster->documentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                        }
                        else
                        {
                            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('matchingOption',1)->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)->where('documentSystemID', $matchDocumentMaster->documentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                        }


                        $machAmount = 0;
                        if ($matchedAmount) {
                            $machAmount = $matchedAmount["SumOfmatchedAmount"];
                        }

                        if (!$supplierPaidAmountSum) {
                            $supplierPaidAmountSum["SumOfsupplierPaymentAmount"] = 0;
                        }

                        $totalPaidAmount = (($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] * -1) + $machAmount);

                        if ($totalPaidAmount == 0) {
                            $DebitNoteMaster->matchInvoice = 0;
                            $DebitNoteMaster->save();
                        } else if ($DebitNoteMaster->debitAmountTrans == $totalPaidAmount || $totalPaidAmount > $DebitNoteMaster->debitAmountTrans) {
                            $DebitNoteMaster->matchInvoice = 2;
                            $DebitNoteMaster->save();
                        } else if (($DebitNoteMaster->debitAmountTrans > $totalPaidAmount) && ($totalPaidAmount > 0)) {
                            $DebitNoteMaster->matchInvoice = 1;
                            $DebitNoteMaster->save();
                        }
                    }
                }


                if($matchDocumentMaster->matchingOption == 1) {

                    if (!empty($itemExistArray)) {
                        return $this->sendError($itemExistArray, 422);
                    }

                    $detailAmountTotTran = AdvancePaymentDetails::where('matchingDocID', $id)
                        ->sum('supplierTransAmount');

                    if (($detailAmountTotTran - $input['matchBalanceAmount']) > 0.00001) {
                        return $this->sendError(trans('custom.detail_amount_cannot_be_greater_than_balance_amoun'), 500, ['type' => 'confirm']);
                    }

                    $details = AdvancePaymentDetails::where('matchingDocID', $id)->get();

                    foreach ($details as $val) {
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


                    }

                }


                $input['matchingConfirmedYN'] = 1;
                $input['matchingConfirmedByEmpSystemID'] = $employee->employeeSystemID;
                $input['matchingConfirmedByEmpID'] = $employee->empID;
                $input['matchingConfirmedByName'] = $employee->empName;
                $input['matchingConfirmedDate'] = \Helper::currentDateTime();

                // Booking of Exchange Gain or Loss at Matching for debit note
                if ($matchDocumentMaster->documentSystemID == 15) {

                    $diffLocal = 0;
                    $diffRpt = 0;
                    $DebitNoteMasterExData = DebitNote::find($matchDocumentMaster->PayMasterAutoId);

                    $companyData = Company::find($DebitNoteMasterExData->companySystemID);

                    $totalAmountPayEx = PaySupplierInvoiceDetail::selectRaw("COALESCE(SUM(supplierPaymentAmount),0) as supplierPaymentAmount, COALESCE(SUM(paymentLocalAmount),0) as paymentLocalAmount, COALESCE(SUM(paymentComRptAmount),0) as paymentComRptAmount")
                        ->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)
                        ->where('documentSystemID', 15)
                        ->where('companySystemID', $matchDocumentMaster->companySystemID)
                        ->first();


                    if (round($DebitNoteMasterExData->debitAmountTrans - $totalAmountPayEx->supplierPaymentAmount, 2) == 0) {

                        if ((round($DebitNoteMasterExData->debitAmountLocal - $totalAmountPayEx->paymentLocalAmount, 2) != 0) || (round($DebitNoteMasterExData->debitAmountRpt - $totalAmountPayEx->paymentComRptAmount, 2) != 0)) {

                            $checkExchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($matchDocumentMaster->companySystemID, $matchDocumentMaster->documentSystemID , "exchange-gainloss-gl");
                            if (is_null($checkExchangeGainLossAccount)) {
                                $checkExchangeGainLossAccountCode = SystemGlCodeScenarioDetail::getGlCodeByScenario($matchDocumentMaster->companySystemID, $matchDocumentMaster->documentSystemID, "exchange-gainloss-gl");

                                if ($checkExchangeGainLossAccountCode) {
                                    return $this->sendError(trans('custom.please_assign_exchange'), 500);
                                }
                                return $this->sendError(trans('custom.please_configure_exchange_gain_loss_account_for_this_company'), 500);
                            }

                            $data = [];
                            $finalData = [];
                            $diffLocal = $totalAmountPayEx->paymentLocalAmount - $DebitNoteMasterExData->debitAmountLocal;
                            $diffRpt = $totalAmountPayEx->paymentComRptAmount - $DebitNoteMasterExData->debitAmountRpt;

                            //echo $diffLocal.' - '. $diffRpt;
                            //exit();
                            $data['companySystemID'] = $DebitNoteMasterExData->companySystemID;
                            $data['companyID'] = $DebitNoteMasterExData->companyID;
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $DebitNoteMasterExData->documentSystemID;
                            $data['documentID'] = $DebitNoteMasterExData->documentID;
                            $data['documentSystemCode'] = $matchDocumentMaster->PayMasterAutoId;
                            $data['documentCode'] = $DebitNoteMasterExData->debitNoteCode;
                            $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                            $data['documentYear'] = \Helper::dateYear($matchDocumentMaster->matchingDocdate);
                            $data['documentMonth'] = \Helper::dateMonth($matchDocumentMaster->matchingDocdate);
                            $data['documentConfirmedDate'] = $DebitNoteMasterExData->confirmedDate;
                            $data['documentConfirmedBy'] = $DebitNoteMasterExData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $DebitNoteMasterExData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $DebitNoteMasterExData->approvedDate;
                            $data['documentFinalApprovedBy'] = $DebitNoteMasterExData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $DebitNoteMasterExData->approvedByUserSystemID;
                            $data['documentNarration'] = 'Exchange Gain/Loss Entry from ' . $input['matchingDocCode'];
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $DebitNoteMasterExData->supplierID;

                            $data['chartOfAccountSystemID'] = $DebitNoteMasterExData->liabilityAccountSysemID;
                            $data['glCode'] = $DebitNoteMasterExData->liabilityAccount;
                            $data['glAccountType'] = 'BS';
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $DebitNoteMasterExData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $DebitNoteMasterExData->supplierTransactionCurrencyER;
                            $data['documentLocalCurrencyID'] = $DebitNoteMasterExData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $DebitNoteMasterExData->localCurrencyER;
                            $data['documentRptCurrencyID'] = $DebitNoteMasterExData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $DebitNoteMasterExData->companyReportingER;

                            $data['documentTransAmount'] = 0;
                            if ($diffLocal > 0) {
                                $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                            } else {
                                $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                            }

                            if ($diffRpt > 0) {
                                $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                            } else {
                                $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                            }

                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['documentType'] = $DebitNoteMasterExData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $employee->empID;
                            $data['createdUserSystemID'] = $employee->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                            array_push($finalData, $data);

                            $exchangeGainServiceLine = SegmentMaster::where('companySystemID',$DebitNoteMasterExData->companySystemID)
                                ->where('isPublic',1)
                                ->where('isActive',1)
                                ->first();

                            if(!empty($exchangeGainServiceLine)){
                                $data['serviceLineSystemID'] = $exchangeGainServiceLine->serviceLineSystemID;
                                $data['serviceLineCode']     = $exchangeGainServiceLine->ServiceLineCode;
                            }else{
                                $data['serviceLineSystemID'] = 24;
                                $data['serviceLineCode'] = 'X';
                            }

                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($DebitNoteMasterExData->companySystemID, $DebitNoteMasterExData->documentSystemID, "exchange-gainloss-gl");
                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($DebitNoteMasterExData->companySystemID, $DebitNoteMasterExData->documentSystemID, "exchange-gainloss-gl");
                            $data['glAccountType'] = 'PL';
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            if ($diffLocal > 0) {
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal) * -1);
                            } else {
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal));
                            }
                            if ($diffRpt > 0) {
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt) * -1);
                            } else {
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt));
                            }
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($finalData) {
                                $storeSupplierInvoiceHistory = GeneralLedger::insert($finalData);
                            }

                        }

                    }

                }
                else if ($matchDocumentMaster->documentSystemID == 4) {
                    
                    $diffLocal = 0;
                    $diffRpt = 0;
                    $PaySupplierInvoiceMasterExData = PaySupplierInvoiceMaster::find($matchDocumentMaster->PayMasterAutoId);

                    $companyData = Company::find($PaySupplierInvoiceMasterExData->companySystemID);

                    $totalAmountPayEx = PaySupplierInvoiceDetail::selectRaw("COALESCE(SUM(supplierPaymentAmount),0) as supplierPaymentAmount, COALESCE(SUM(paymentLocalAmount),0) as paymentLocalAmount, COALESCE(SUM(paymentComRptAmount),0) as paymentComRptAmount")
                        ->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)
                        ->where('documentSystemID', 4)
                        ->where('companySystemID', $matchDocumentMaster->companySystemID)
                        ->first();

                    
                    if (round($PaySupplierInvoiceMasterExData->payAmountSuppTrans - $totalAmountPayEx->supplierPaymentAmount, 2) == 0) {

                        if ((round($PaySupplierInvoiceMasterExData->payAmountCompLocal - $totalAmountPayEx->paymentLocalAmount, 2) != 0) || (round($PaySupplierInvoiceMasterExData->payAmountCompRpt - $totalAmountPayEx->paymentComRptAmount, 2) != 0)) {

                            $checkExchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($matchDocumentMaster->companySystemID, $matchDocumentMaster->documentSystemID , "exchange-gainloss-gl");
                            if (is_null($checkExchangeGainLossAccount)) {
                                $checkExchangeGainLossAccountCode = SystemGlCodeScenarioDetail::getGlCodeByScenario($matchDocumentMaster->companySystemID, $matchDocumentMaster->documentSystemID, "exchange-gainloss-gl");
                                if ($checkExchangeGainLossAccountCode) {
                                    return $this->sendError(trans('custom.please_assign_exchange'), 500);
                                }
                                return $this->sendError(trans('custom.please_configure_exchange_gain_loss_account_for_this_company'), 500);
                            }

                            $data = [];
                            $finalData = [];
                            $diffLocal = $totalAmountPayEx->paymentLocalAmount - $PaySupplierInvoiceMasterExData->payAmountCompLocal;
                            $diffRpt = $totalAmountPayEx->paymentComRptAmount - $PaySupplierInvoiceMasterExData->payAmountCompRpt;

                            //echo $diffLocal.' - '. $diffRpt;
                            //exit();
                            $data['companySystemID'] = $PaySupplierInvoiceMasterExData->companySystemID;
                            $data['companyID'] = $PaySupplierInvoiceMasterExData->companyID;
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $PaySupplierInvoiceMasterExData->documentSystemID;
                            $data['documentID'] = $PaySupplierInvoiceMasterExData->documentID;
                            $data['documentSystemCode'] = $matchDocumentMaster->PayMasterAutoId;
                            $data['documentCode'] = $PaySupplierInvoiceMasterExData->BPVcode;
                            $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                            $data['documentYear'] = \Helper::dateYear($matchDocumentMaster->matchingDocdate);
                            $data['documentMonth'] = \Helper::dateMonth($matchDocumentMaster->matchingDocdate);
                            $data['documentConfirmedDate'] = $PaySupplierInvoiceMasterExData->confirmedDate;
                            $data['documentConfirmedBy'] = $PaySupplierInvoiceMasterExData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $PaySupplierInvoiceMasterExData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $PaySupplierInvoiceMasterExData->approvedDate;
                            $data['documentFinalApprovedBy'] = $PaySupplierInvoiceMasterExData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $PaySupplierInvoiceMasterExData->approvedByUserSystemID;
                            $data['documentNarration'] = 'Exchange Gain/Loss Entry from ' . $input['matchingDocCode'];
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $PaySupplierInvoiceMasterExData->BPVsupplierID;

                            $data['chartOfAccountSystemID'] = $PaySupplierInvoiceMasterExData->supplierGLCodeSystemID;
                            $data['glCode'] = $PaySupplierInvoiceMasterExData->supplierGLCode;
                            $data['glAccountType'] = 'BS';
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $PaySupplierInvoiceMasterExData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $PaySupplierInvoiceMasterExData->supplierTransCurrencyER;
                            $data['documentLocalCurrencyID'] = $PaySupplierInvoiceMasterExData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $PaySupplierInvoiceMasterExData->localCurrencyER;
                            $data['documentRptCurrencyID'] = $PaySupplierInvoiceMasterExData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $PaySupplierInvoiceMasterExData->companyRptCurrencyER;

                            $data['documentTransAmount'] = 0;
                            if ($diffLocal > 0) {
                                $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                            } else {
                                $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                            }

                            if ($diffRpt > 0) {
                                $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                            } else {
                                $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                            }

                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['documentType'] = $PaySupplierInvoiceMasterExData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $employee->empID;
                            $data['createdUserSystemID'] = $employee->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                            // array_push($finalData, $data);

                            $exchangeGainServiceLine = SegmentMaster::where('companySystemID',$PaySupplierInvoiceMasterExData->companySystemID)
                                ->where('isPublic',1)
                                ->where('isActive',1)
                                ->first();

                            if(!empty($exchangeGainServiceLine)){
                                $data['serviceLineSystemID'] = $exchangeGainServiceLine->serviceLineSystemID;
                                $data['serviceLineCode']     = $exchangeGainServiceLine->ServiceLineCode;
                            }else{
                                $data['serviceLineSystemID'] = 24;
                                $data['serviceLineCode'] = 'X';
                            }
                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($PaySupplierInvoiceMasterExData->companySystemID, $PaySupplierInvoiceMasterExData->documentSystemID, "exchange-gainloss-gl");
                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($PaySupplierInvoiceMasterExData->companySystemID, $PaySupplierInvoiceMasterExData->documentSystemID, "exchange-gainloss-gl");
                            $data['glAccountType'] = 'PL';
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            if ($diffLocal > 0) {
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal) * -1);
                            } else {
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal));
                            }
                            if ($diffRpt > 0) {
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt) * -1);
                            } else {
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt));
                            }
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                            if ($finalData) {
                                $storeSupplierInvoiceHistory = GeneralLedger::insert($finalData);
                            }

                        }

                    }

                    else if((($totalAmountPayEx->supplierPaymentAmount / $PaySupplierInvoiceMasterExData->localCurrencyER) - $totalAmountPayEx->paymentLocalAmount != 0) || (($totalAmountPayEx->supplierPaymentAmount / $PaySupplierInvoiceMasterExData->companyRptCurrencyER) - $totalAmountPayEx->paymentComRptAmount != 0)){

                        $checkExchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($matchDocumentMaster->companySystemID, $matchDocumentMaster->documentSystemID , "exchange-gainloss-gl");
                        if (is_null($checkExchangeGainLossAccount)) {
                            $checkExchangeGainLossAccountCode = SystemGlCodeScenarioDetail::getGlCodeByScenario($matchDocumentMaster->companySystemID, $matchDocumentMaster->documentSystemID, "exchange-gainloss-gl");
                            if ($checkExchangeGainLossAccountCode) {
                                return $this->sendError(trans('custom.please_assign_exchange'), 500);
                            }
                            return $this->sendError(trans('custom.please_configure_exchange_gain_loss_account_for_this_company'), 500);
                        }


                        $data = [];
                        $finalData = [];
                        $diffLocal = $totalAmountPayEx->paymentLocalAmount - ($totalAmountPayEx->supplierPaymentAmount / $PaySupplierInvoiceMasterExData->localCurrencyER);
                        $diffRpt = $totalAmountPayEx->paymentComRptAmount - ($totalAmountPayEx->supplierPaymentAmount / $PaySupplierInvoiceMasterExData->companyRptCurrencyER);

                        $data['companySystemID'] = $PaySupplierInvoiceMasterExData->companySystemID;
                        $data['companyID'] = $PaySupplierInvoiceMasterExData->companyID;
                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                        $data['masterCompanyID'] = null;
                        $data['documentSystemID'] = $PaySupplierInvoiceMasterExData->documentSystemID;
                        $data['documentID'] = $PaySupplierInvoiceMasterExData->documentID;
                        $data['documentSystemCode'] = $matchDocumentMaster->PayMasterAutoId;
                        $data['documentCode'] = $PaySupplierInvoiceMasterExData->BPVcode;
                        $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                        $data['documentYear'] = \Helper::dateYear($matchDocumentMaster->matchingDocdate);
                        $data['documentMonth'] = \Helper::dateMonth($matchDocumentMaster->matchingDocdate);
                        $data['documentConfirmedDate'] = $PaySupplierInvoiceMasterExData->confirmedDate;
                        $data['documentConfirmedBy'] = $PaySupplierInvoiceMasterExData->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $PaySupplierInvoiceMasterExData->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $PaySupplierInvoiceMasterExData->approvedDate;
                        $data['documentFinalApprovedBy'] = $PaySupplierInvoiceMasterExData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $PaySupplierInvoiceMasterExData->approvedByUserSystemID;
                        $data['documentNarration'] = 'Exchange Gain/Loss Entry from ' . $input['matchingDocCode'];
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $PaySupplierInvoiceMasterExData->BPVsupplierID;

                        $data['chartOfAccountSystemID'] = $PaySupplierInvoiceMasterExData->supplierGLCodeSystemID;
                        $data['glCode'] = $PaySupplierInvoiceMasterExData->supplierGLCode;
                        $data['glAccountType'] = 'BS';
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $PaySupplierInvoiceMasterExData->supplierTransCurrencyID;
                        $data['documentTransCurrencyER'] = $PaySupplierInvoiceMasterExData->supplierTransCurrencyER;
                        $data['documentLocalCurrencyID'] = $PaySupplierInvoiceMasterExData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $PaySupplierInvoiceMasterExData->localCurrencyER;
                        $data['documentRptCurrencyID'] = $PaySupplierInvoiceMasterExData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $PaySupplierInvoiceMasterExData->companyRptCurrencyER;

                        $data['documentTransAmount'] = 0;
                        if ($diffLocal > 0) {
                            $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                        } else {
                            $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                        }

                        if ($diffRpt > 0) {
                            $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                        } else {
                            $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                        }

                        $data['holdingShareholder'] = null;
                        $data['holdingPercentage'] = 0;
                        $data['nonHoldingPercentage'] = 0;
                        $data['documentType'] = $PaySupplierInvoiceMasterExData->documentType;
                        $data['createdDateTime'] = \Helper::currentDateTime();
                        $data['createdUserID'] = $employee->empID;
                        $data['createdUserSystemID'] = $employee->employeeSystemID;
                        $data['createdUserPC'] = gethostname();
                        $data['timestamp'] = \Helper::currentDateTime();
                        $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                        $exchangeGainServiceLine = SegmentMaster::where('companySystemID',$PaySupplierInvoiceMasterExData->companySystemID)
                            ->where('isPublic',1)
                            ->where('isActive',1)
                            ->first();

                        if(!empty($exchangeGainServiceLine)){
                            $data['serviceLineSystemID'] = $exchangeGainServiceLine->serviceLineSystemID;
                            $data['serviceLineCode']     = $exchangeGainServiceLine->ServiceLineCode;
                        }else{
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                        }
                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($PaySupplierInvoiceMasterExData->companySystemID, $PaySupplierInvoiceMasterExData->documentSystemID, "exchange-gainloss-gl");
                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($PaySupplierInvoiceMasterExData->companySystemID, $PaySupplierInvoiceMasterExData->documentSystemID, "exchange-gainloss-gl");
                        $data['glAccountType'] = 'PL';
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        if ($diffLocal > 0) {
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal) * -1);
                        } else {
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal));
                        }
                        if ($diffRpt > 0) {
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt) * -1);
                        } else {
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt));
                        }
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                        if ($finalData) {
                            $storeSupplierInvoiceHistory = GeneralLedger::insert($finalData);
                        }
                    }

                }

            }

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = $employee->empID;
            $input['modifiedUserSystemID'] = $employee->employeeSystemID;

            $matchDocumentMaster = $this->matchDocumentMasterRepository->update($input, $id);

            if ($input['matchingConfirmedYN'] == 1) 
            {

                $advancePaymentEmployee =  PaySupplierInvoiceDetail::where('matchingDocID', $id)->where('documentSystemID',4)
                    ->where('PayMasterAutoId', $input["PayMasterAutoId"])
                    ->whereHas('matching_master', function($query) {
                        $query->where('user_type', 2);
                    });
                if($advancePaymentEmployee->count() > 0) {

                    $isEmpAdvConfigured = SystemGlCodeScenarioDetail::getGlByScenario($input['companySystemID'], $input['documentSystemID'], "employee-advance-account");

                    if (is_null($isEmpAdvConfigured)) {
                        return $this->sendError('Please configure employee advance account for this company');
                    }

                    $totAdvancePayment = $advancePaymentEmployee->selectRaw("SUM(paymentLocalAmount) as localAmount,SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount")->first();
                    $finalData = [];
                    $masterData = PaySupplierInvoiceMaster::with(['bank', 'financeperiod_by', 'transactioncurrency', 'localcurrency', 'rptcurrency'])->find($input["PayMasterAutoId"]);

                    if ($matchDocumentMaster) {
                        $data['companySystemID'] = $matchDocumentMaster->companySystemID;
                        $data['companyID'] = $matchDocumentMaster->companyID;
                        $data['serviceLineSystemID'] = null;
                        $data['serviceLineCode'] = null;
                        $data['masterCompanyID'] = null;
                        $data['documentSystemID'] = $matchDocumentMaster->documentSystemID;
                        $data['documentID'] = $matchDocumentMaster->documentID;
                        $data['documentSystemCode'] = $input["PayMasterAutoId"];
                        $data['documentCode'] = $matchDocumentMaster->BPVcode;
                        $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                        $data['documentYear'] = \Helper::dateYear($matchDocumentMaster->matchingDocdate);
                        $data['documentMonth'] = \Helper::dateMonth($matchDocumentMaster->matchingDocdate);
                        $data['documentConfirmedDate'] = $matchDocumentMaster->confirmedDate;
                        $data['documentConfirmedBy'] = $matchDocumentMaster->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $matchDocumentMaster->matchingConfirmedDate;
                        $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                        $data['documentNarration'] = "Matching Entry " . $matchDocumentMaster->matchingDocCode;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $matchDocumentMaster->BPVsupplierID;
                        $data['holdingShareholder'] = null;
                        $data['holdingPercentage'] = 0;
                        $data['nonHoldingPercentage'] = 0;
                        $data['contraYN'] = 1;
                        $data['chequeNumber'] = $masterData->BPVchequeNo;
                        $data['documentType'] = $masterData->invoiceType;
                        $data['createdDateTime'] = \Helper::currentDateTime();
                        $data['createdUserID'] = $created_by['empID'];
                        $data['createdUserSystemID'] = $created_by['employeeSystemID'];
                        $data['createdUserPC'] = gethostname();
                        $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;
                        $data['timestamp'] = \Helper::currentDateTime();

                        if ($totAdvancePayment) {
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['chartOfAccountSystemID'] = $masterData->employeeAdvanceAccountSystemID;
                            $data['glCode'] = $masterData->employeeAdvanceAccount;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                            $conversion = \Helper::convertAmountToLocalRpt(204, $matchDocumentMaster->matchDocumentMasterAutoID, $totAdvancePayment->transAmount);

                            $data['documentTransAmount'] = \Helper::roundValue($totAdvancePayment->transAmount) * -1;;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($conversion['localAmount']) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($conversion['reportingAmount']) * -1;
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);


                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['chartOfAccountSystemID'] = $masterData->advanceAccountSystemID;
                            $data['glCode'] = $masterData->AdvanceAccount;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($totAdvancePayment->transAmount);
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($totAdvancePayment->localAmount);
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($totAdvancePayment->rptAmount);
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }

                        foreach ($finalData as $data) {
                            GeneralLedger::create($data);
                        }
                    }
                }


                $is_advance_payment =  PaySupplierInvoiceDetail::where('matchingDocID', $id)->where('documentSystemID',4)
                                                               ->where('PayMasterAutoId', $input["PayMasterAutoId"])
                                                               ->whereHas('matching_master', function($query) {
                                                                    $query->where('user_type', '!=', 2);
                                                               });
                if($is_advance_payment->count() > 0)
                {
                   
                    $ap = $is_advance_payment->selectRaw("SUM(paymentLocalAmount) as localAmount,SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount")->first();
                    $finalData = [];
                    $masterData = PaySupplierInvoiceMaster::with(['bank', 'financeperiod_by', 'transactioncurrency', 'localcurrency', 'rptcurrency'])->find($input["PayMasterAutoId"]);
                   // $ap = AdvancePaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER")->WHERE('PayMasterAutoId', $input["PayMasterAutoId"])->first();
                    $masterDocumentDate = date('Y-m-d H:i:s');
                    if ($masterData->financeperiod_by->isActive == -1) {
                        $masterDocumentDate = $masterData->BPVdate;
                    }   

                    $supDetail = SupplierAssigned::where('supplierCodeSytem', $masterData->BPVsupplierID)->where('companySystemID', $input['companySystemID'])->first();
              
                    
                    if ($matchDocumentMaster) {
                        $data['companySystemID'] = $matchDocumentMaster->companySystemID;
                        $data['companyID'] = $matchDocumentMaster->companyID;
                        $data['serviceLineSystemID'] = null;
                        $data['serviceLineCode'] = null;
                        $data['masterCompanyID'] = null;
                        $data['documentSystemID'] = $matchDocumentMaster->documentSystemID;
                        $data['documentID'] = $matchDocumentMaster->documentID;
                        $data['documentSystemCode'] = $input["PayMasterAutoId"];
                        $data['documentCode'] = $matchDocumentMaster->BPVcode;
                        $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                        $data['documentYear'] = \Helper::dateYear($matchDocumentMaster->matchingDocdate);
                        $data['documentMonth'] = \Helper::dateMonth($matchDocumentMaster->matchingDocdate);
                        $data['documentConfirmedDate'] = $matchDocumentMaster->confirmedDate;
                        $data['documentConfirmedBy'] = $matchDocumentMaster->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $matchDocumentMaster->matchingConfirmedDate;
                        $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                        $data['documentNarration'] = "Matching Entry ".$matchDocumentMaster->matchingDocCode;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $matchDocumentMaster->BPVsupplierID;
                        $data['holdingShareholder'] = null;
                        $data['holdingPercentage'] = 0;
                        $data['nonHoldingPercentage'] = 0;
                        $data['contraYN'] = 1;
                        $data['chequeNumber'] = $masterData->BPVchequeNo;
                        $data['documentType'] = $masterData->invoiceType;
                        $data['createdDateTime'] = \Helper::currentDateTime();
                        $data['createdUserID'] = $created_by['empID'];
                        $data['createdUserSystemID'] = $created_by['employeeSystemID'];
                        $data['createdUserPC'] = gethostname();
                        $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;
                        $data['timestamp'] = \Helper::currentDateTime();

                        if ($ap) {
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['chartOfAccountSystemID'] = $masterData->advanceAccountSystemID;
                            $data['glCode'] = $masterData->AdvanceAccount;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($ap->transAmount) * -1;;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($ap->localAmount - $diffLocal) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($ap->rptAmount - $diffRpt) * -1;
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);


                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['chartOfAccountSystemID'] = $supDetail->liabilityAccountSysemID;
                            $data['glCode'] = $supDetail->liabilityAccount;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($ap->transAmount);
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] =   \Helper::roundValue($ap->localAmount);
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($ap->rptAmount);
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }

                        if ($masterData->applyVAT == 1 && $masterData->invoiceType == 5) {
                            $taxLedgerData = [];
                            $advancePaymentVATAmount = AdvancePaymentDetails::where('PayMasterAutoId', $input["PayMasterAutoId"])
                                                                            ->sum('VATAmount');

                            if ($advancePaymentVATAmount > 0) {
                                $supplierInvoiceVAT = TaxService::processMatchingVAT($matchDocumentMaster->matchDocumentMasterAutoID);

                                if (isset($supplierInvoiceVAT['supplierInvoiceVAT']) && $supplierInvoiceVAT['supplierInvoiceVAT'] > 0) {
                                    $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);
                                    if (!empty($taxData)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                            $data['documentTransAmount'] = \Helper::roundValue($supplierInvoiceVAT['supplierInvoiceVAT']);
                                            $data['documentLocalAmount'] = \Helper::roundValue($supplierInvoiceVAT['supplierInvoiceVATLocal']);
                                            $data['documentRptAmount'] = \Helper::roundValue($supplierInvoiceVAT['supplierInvoiceVATRpt']);

                                            array_push($finalData, $data);

                                            $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        } else {
                                            return $this->sendError(trans('custom.cannot_confirm_input_vat_transfer_gl_account_not_a'), 500);
                                        }
                                    } else {
                                        return $this->sendError(trans('custom.cannot_confirm_input_vat_transfer_gl_account_not_c'), 500);
                                    }

                                    $taxData2 = TaxService::getInputVATGLAccount($masterData->companySystemID);
                                    if (!empty($taxData2)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData2->inputVatGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                            $data['documentTransAmount'] = \Helper::roundValue($supplierInvoiceVAT['supplierInvoiceVAT']) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue($supplierInvoiceVAT['supplierInvoiceVATLocal']) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue($supplierInvoiceVAT['supplierInvoiceVATRpt']) * -1;

                                            array_push($finalData, $data);

                                            $taxLedgerData['inputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        } else {
                                            return $this->sendError(trans('custom.cannot_confirm_input_vat_gl_account_not_assigned_t'), 500);
                                        }
                                    } else {
                                        return $this->sendError(trans('custom.cannot_confirm_input_vat_gl_account_not_configured'), 500);
                                    }
                                }

                                if (count($taxLedgerData) > 0) {
                                    $masterModel = [
                                        'employeeSystemID' => $created_by['employeeSystemID'],
                                        'documentSystemID' => $matchDocumentMaster->documentSystemID,
                                        'matchDocumentMasterAutoID' => $matchDocumentMaster->matchDocumentMasterAutoID,
                                        'autoID' => $input['PayMasterAutoId'],
                                        'matching' => true,
                                        'companySystemID' => $matchDocumentMaster->companySystemID
                                    ];

                                    $taxResponse = PaymentVoucherTaxLedgerService::processEntry($taxLedgerData, $masterModel);

                                    if ($taxResponse['status']) {
                                        $finalDataTax = $taxResponse['data']['finalData'];
                                        $finalDetailDataTax = $taxResponse['data']['finalDetailData'];


                                        if ($finalDataTax) {
                                            foreach ($finalDataTax as $data)
                                            {
                                                TaxLedger::create($data);
                                            }

                                            foreach ($finalDetailDataTax as $data)
                                            {
                                                TaxLedgerDetail::create($data);
                                            }
                                        }
                                    } 
                                }
                            }
                        }

                        foreach ($finalData as $data) {
                            GeneralLedger::create($data);
                        }
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($matchDocumentMaster->toArray(), trans('custom.record_updated_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }


    public function updateReceiptVoucherMatching(Request $request)
    {
        DB::beginTransaction();
        try {

                 $input = $request->all();
                $created_by = $input['created_by'];
                $input = array_except($input, ['created_by', 'BPVsupplierID', 'company', 'confirmed_by', 'modified_by','localcurrency','rptcurrency','customer','supplier','payment_voucher','reciept_voucher']);        
                $input = $this->convertArrayToValue($input);
                
                $employee = \Helper::getEmployeeInfo();

                $id = $input['matchDocumentMasterAutoID'];

                /** @var MatchDocumentMaster $matchDocumentMaster */
                $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

                if (empty($matchDocumentMaster)) {
                    return $this->sendError(trans('custom.match_document_master_not_found_1'));
                }

                $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($matchDocumentMaster->supplierTransCurrencyID);

                if (isset($input['matchingDocdate'])) {
                    if ($input['matchingDocdate']) {
                        $input['matchingDocdate'] = new Carbon($input['matchingDocdate']);
                    }
                }

                $customValidation = CustomValidation::validation(70, $matchDocumentMaster, 2, $input);
                if (!$customValidation["success"]) {
                    return $this->sendError($customValidation["message"], 500, array('type' => 'already_confirmed'));
                }

                $detailAmountTotTran = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                    ->sum('receiveAmountTrans');

                $detailAmountTotLoc = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                    ->sum('receiveAmountLocal');

                $detailAmountTotRpt = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                    ->sum('receiveAmountRpt');

                $input['matchingAmount'] = $detailAmountTotTran;
                $input['matchedAmount'] = $detailAmountTotTran;
                $input['matchLocalAmount'] = \Helper::roundValue($detailAmountTotLoc);
                $input['matchRptAmount'] = \Helper::roundValue($detailAmountTotRpt);


                //checking below posted data
                if ($input['documentSystemID'] == 21) {

                    $CustomerReceivePaymentDataUpdateCHK = CustomerReceivePayment::find($input['PayMasterAutoId']);

                    $postedDate = date("Y-m-d", strtotime($CustomerReceivePaymentDataUpdateCHK->postedDate));

                    $formattedMatchingDate = date("Y-m-d", strtotime($input['matchingDocdate']));

                    if ($formattedMatchingDate < $postedDate) {
                        return $this->sendError(trans('custom.receipt_voucher_posted_date_error', ['date' => $postedDate]), 500);
                    }

                } elseif ($input['documentSystemID'] == 19) {

                    $creditNoteDataUpdateCHK = CreditNote::find($input['PayMasterAutoId']);
                    if (empty($creditNoteDataUpdateCHK)) {
                        return $this->sendError(trans('custom.credit_note_not_found'));
                    }

                    $postedDate = date("Y-m-d", strtotime($creditNoteDataUpdateCHK->postedDate));

                    $formattedMatchingDate = date("Y-m-d", strtotime($input['matchingDocdate']));

                    if ($formattedMatchingDate < $postedDate) {
                        return $this->sendError(trans('custom.credit_note_posted_date_error', ['date' => $postedDate]), 500);
                    }
                }

                if ($matchDocumentMaster->matchingConfirmedYN == 0 && $input['matchingConfirmedYN'] == 1) {

                    $pvDetailExist = CustomerReceivePaymentDetail::select(DB::raw('matchingDocID,addedDocumentSystemID'))
                        ->where('matchingDocID', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError(trans('custom.matching_document_cannot_confirm_without_details'), 500, ['type' => 'confirm']);
                    }

                    $currencyValidate = CurrencyValidation::validateCurrency("receipt_matching", $matchDocumentMaster);
                    if (!$currencyValidate['status']) {
                        return $this->sendError($currencyValidate['message'], 500, ['type' => 'confirm']);
                    }

                    $detailAllRecords = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                        ->get();

                    if ($detailAllRecords) {
                        foreach ($detailAllRecords as $row) {
                            if ($row['addedDocumentSystemID'] == 20) {
                                $checkAmount = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                                    ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                                    ->where('receiveAmountTrans', '<=', 0)
                                    ->count();

                                if ($checkAmount > 0) {
                                    return $this->sendError(trans('custom.matching_amount_cannot_be_0'), 500, ['type' => 'confirm']);
                                }
                            } elseif ($row['addedDocumentSystemID'] == 19) {
                                $checkAmount = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                                    ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                                    ->where('receiveAmountTrans', '=', 0)
                                    ->count();

                                if ($checkAmount > 0) {
                                    return $this->sendError(trans('custom.matching_amount_cannot_be_0'), 500, ['type' => 'confirm']);
                                }
                            }
                        }
                    }

                    $detailAmountTotTran = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                        ->sum('receiveAmountTrans');

                    if (round($detailAmountTotTran, $supplierCurrencyDecimalPlace) > round($input['matchBalanceAmount'], $supplierCurrencyDecimalPlace)) {
                        return $this->sendError(trans('custom.detail_amount_cannot_be_greater_than_balance_amoun'), 500, ['type' => 'confirm']);
                    }

                    if ($input['matchingDocCode'] == 0) {

                        $company = Company::find($input['companySystemID']);

                        $lastSerial = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                            ->where('matchDocumentMasterAutoID', '<>', $input['matchDocumentMasterAutoID'])
                            ->where('matchingType', 'AR')
                            ->orderBy('serialNo', 'desc')
                            ->first();

                        $lastSerialNumber = 1;
                        if ($lastSerial) {
                            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                        }

                        $matchingDocCode = ($company->CompanyID . '\\' . 'MT' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

                        $input['serialNo'] = $lastSerialNumber;
                        $input['matchingDocCode'] = $matchingDocCode;
                    }
                    $itemExistArray = array();

                    foreach ($detailAllRecords as $item) {

                        $payDetailMoreBooked = CustomerReceivePaymentDetail::selectRaw('IFNULL(SUM(IFNULL(receiveAmountTrans,0)),0) as receiveAmountTrans')
                            ->where('arAutoID', $item['arAutoID'])
                            ->first();

                        $a = $payDetailMoreBooked->receiveAmountTrans;
                        $b = $item['bookingAmountTrans'];
                        $epsilon = 0.00001;
                        if(($a-$b) > $epsilon) {
                            $itemDrt = "Selected invoice " . $item['bookingInvCode'] . " booked more than the invoice amount.";
                            $itemExistArray[] = [$itemDrt];

                        }
                    }

                    if (!empty($itemExistArray)) {
                        return $this->sendError($itemExistArray, 422);
                    }

                    foreach ($detailAllRecords as $val) {

                        $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $val['arAutoID'])
                            ->sum('receiveAmountTrans');

                        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
                            ->where('companySystemID', $val["companySystemID"])
                            ->where('PayMasterAutoId', $val["bookingInvCodeSystem"])
                            ->where('documentSystemID', $val["addedDocumentSystemID"])
                            ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')
                            ->first();

                        if(!$matchedAmount){
                            $matchedAmount['SumOfmatchedAmount'] = 0;
                        }

                        $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];

                        $arLedgerUpdate = AccountsReceivableLedger::find($val['arAutoID']);

                        if ($val['addedDocumentSystemID'] == 20) {
                            if ($totReceiveAmount == 0) {
                                $arLedgerUpdate->fullyInvoiced = 0;
                                $arLedgerUpdate->selectedToPaymentInv = 0;
                            } else if (($val->bookingAmountTrans == $totReceiveAmount) || ($totReceiveAmount > $val->bookingAmountTrans)) {
                                $arLedgerUpdate->fullyInvoiced = 2;
                                $arLedgerUpdate->selectedToPaymentInv = -1;
                            } else if (($val->bookingAmountTrans > $totReceiveAmount) && ($totReceiveAmount > 0)) {
                                $arLedgerUpdate->fullyInvoiced = 1;
                                $arLedgerUpdate->selectedToPaymentInv = 0;
                            }
                        } else if ($val['addedDocumentSystemID'] == 19) {
                            if ($totReceiveAmount == 0) {
                                $arLedgerUpdate->fullyInvoiced = 0;
                                $arLedgerUpdate->selectedToPaymentInv = 0;
                            } else if (($val->bookingAmountTrans == $totReceiveAmount) || ($totReceiveAmount < $val->bookingAmountTrans)) {
                                $arLedgerUpdate->fullyInvoiced = 2;
                                $arLedgerUpdate->selectedToPaymentInv = -1;
                            } else if (($val->bookingAmountTrans < $totReceiveAmount) && ($totReceiveAmount < 0)) {
                                $arLedgerUpdate->fullyInvoiced = 1;
                                $arLedgerUpdate->selectedToPaymentInv = 0;
                            }
                        }

                        $arLedgerUpdate->save();
                    }


                    //updating master table
                    if ($input['documentSystemID'] == 21) {

                        $CustomerReceivePaymentDataUpdate = CustomerReceivePayment::find($input['PayMasterAutoId']);

                        $customerSettleAmountSum = CustomerReceivePaymentDetail::selectRaw('erp_custreceivepaymentdet.bookingAmountTrans, 
                                                                addedDocumentSystemID, 
                                                                bookingInvCodeSystem, 
                                                                Sum(erp_custreceivepaymentdet.receiveAmountTrans) AS SumDetailAmount')
                                                    ->where('custReceivePaymentAutoID', $input['PayMasterAutoId'])
                                                    ->where('bookingInvCode', '0')
                                                    ->groupBy('custReceivePaymentAutoID')
                                                    ->first();


                        $directDetails = DirectReceiptDetail::selectRaw("SUM(localAmount) as SumDetailAmountLocal, 
                                                                            SUM(comRptAmount) as SumDetailAmountRpt,
                                                                            SUM(DRAmount) as SumDetailAmountTrans")
                                                                ->where('directReceiptAutoID', $input['PayMasterAutoId'])
                                                                ->groupBy('directReceiptAutoID')
                                                                ->first();

                        $advReceiptDetails = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as SumDetailAmountLocal, 
                                                                                SUM(comRptAmount) as SumDetailAmountRpt,
                                                                                SUM(paymentAmount) as SumAdvDetailAmountTrans")
                                                                    ->where('custReceivePaymentAutoID', $input['PayMasterAutoId'])
                                                                    ->groupBy('custReceivePaymentAutoID')
                                                                    ->first();

                        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentSystemID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')
                                                            ->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)
                                                            ->where('documentSystemID', $matchDocumentMaster->documentSystemID)
                                                            ->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')
                                                            ->first();

                        $machAmount = 0;
                        if ($matchedAmount) {
                            $machAmount = $matchedAmount["SumOfmatchedAmount"];
                        }
                        $receiveAmountTot = 0;
                        if ($customerSettleAmountSum) {
                            $receiveAmountTot = $customerSettleAmountSum["SumDetailAmount"];
                        }

                        if($directDetails){
                            $receiveAmountTot += $directDetails["SumDetailAmountTrans"];
                        }

                        if($advReceiptDetails){
                            $receiveAmountTot += $advReceiptDetails["SumAdvDetailAmountTrans"];
                        }

                        $RoundedMachAmount = round($machAmount, $supplierCurrencyDecimalPlace);
                        $RoundedReceiveAmountTot = round($receiveAmountTot, $supplierCurrencyDecimalPlace);

                        if ($machAmount == 0) {
                            $CustomerReceivePaymentDataUpdate->matchInvoice = 0;
                        } else if ($RoundedReceiveAmountTot == $RoundedMachAmount || $RoundedMachAmount > $RoundedReceiveAmountTot) {
                            $CustomerReceivePaymentDataUpdate->matchInvoice = 2;
                        } else if ($RoundedReceiveAmountTot > $RoundedMachAmount && $RoundedMachAmount > 0) {
                            $CustomerReceivePaymentDataUpdate->matchInvoice = 1;
                        }
                        $CustomerReceivePaymentDataUpdate->save();
                    }
                    if ($input['documentSystemID'] == 19) {

                        $creditNoteDataUpdate = CreditNote::find($input['PayMasterAutoId']);
                        if (empty($creditNoteDataUpdate)) {
                            return $this->sendError(trans('custom.credit_note_not_found'));
                        }

                        //when adding a new matching, checking whether debit amount more than the document value
                        $customerSettleAmountSum = CustomerReceivePaymentDetail::selectRaw('erp_custreceivepaymentdet.bookingAmountTrans, addedDocumentSystemID, bookingInvCodeSystem, companySystemID, Sum(erp_custreceivepaymentdet.receiveAmountTrans) AS SumDetailAmount')
                            ->where('addedDocumentSystemID', $creditNoteDataUpdate->documentSystemiD)
                            ->where('bookingInvCodeSystem', $creditNoteDataUpdate->creditNoteAutoID)
                            ->groupBy('addedDocumentSystemID', 'bookingInvCodeSystem')
                            ->first();


                        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')
                            ->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)
                            ->where('documentSystemID', $matchDocumentMaster->documentSystemID)
                            ->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')
                            ->first();

                        $machAmount = 0;
                        if ($matchedAmount) {
                            $machAmount = $matchedAmount["SumOfmatchedAmount"];
                        }

                        $customerDetailSum = 0;
                        if ($customerSettleAmountSum) {
                            $customerDetailSum = abs($customerSettleAmountSum["SumDetailAmount"]);
                        }

                        $totalPaidAmount = ($customerDetailSum + $machAmount);
                        $RoundedTotalPaidAmount = round($totalPaidAmount, $supplierCurrencyDecimalPlace);
                        $RoundedCreditAmountTrans = round($creditNoteDataUpdate->creditAmountTrans, $supplierCurrencyDecimalPlace);

                        if ($totalPaidAmount == 0) {
                            $creditNoteDataUpdate->matchInvoice = 0;
                        } elseif ($RoundedCreditAmountTrans == $RoundedTotalPaidAmount) {
                            $creditNoteDataUpdate->matchInvoice = 2;
                        } elseif ($RoundedTotalPaidAmount > $RoundedCreditAmountTrans) {
                            $creditNoteDataUpdate->matchInvoice = 2;
                        } elseif ($RoundedCreditAmountTrans > $RoundedTotalPaidAmount && ($RoundedTotalPaidAmount > 0)) {
                            $creditNoteDataUpdate->matchInvoice = 1;
                        }
                        $creditNoteDataUpdate->save();
                    }


                    $input['matchingConfirmedYN'] = 1;
                    $input['matchingConfirmedByEmpSystemID'] = $employee->employeeSystemID;
                    $input['matchingConfirmedByEmpID'] = $employee->empID;
                    $input['matchingConfirmedByName'] = $employee->empName;
                    $input['matchingConfirmedDate'] = \Helper::currentDateTime();

                    $data = [];
                    $taxLedgerData = [];
                    $finalData = [];

                    $validatePostedDate = GlPostedDateService::validatePostedDate($input['PayMasterAutoId'], $input["documentSystemID"]);

                    if (!$validatePostedDate['status']) {
                        return ['status' => false, 'message' => $validatePostedDate['message']];
                    }

                    $masterDocumentDate =  $validatePostedDate['postedDate'];

                    $this->matchDocumentMasterRepository->update($input, $id);

                    $matchDocumentMaster = $this->matchDocumentMasterRepository->with('segment')->findWithoutFail($id);

                    if ($input['documentSystemID'] == 21)
                    {
                        $masterData = CustomerReceivePayment::with(['bank', 'finance_period_by'])->find($input['PayMasterAutoId']);

                        $data['companySystemID'] = $matchDocumentMaster->companySystemID;
                        $data['companyID'] = $matchDocumentMaster->companyID;
                        $data['serviceLineSystemID'] = null;
                        $data['serviceLineCode'] = null;
                        $data['masterCompanyID'] = null;
                        $data['documentSystemID'] = $matchDocumentMaster->documentSystemID;
                        $data['documentID'] = $matchDocumentMaster->documentID;
                        $data['documentSystemCode'] = $input["PayMasterAutoId"];
                        $data['documentCode'] = $masterData->custPaymentReceiveCode;
                        $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                        $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                        $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                        $data['documentConfirmedDate'] = $matchDocumentMaster->matchingConfirmedDate;
                        $data['documentConfirmedBy'] = $matchDocumentMaster->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $matchDocumentMaster->approvedDate;
                        $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                        $data['documentNarration'] = "Matching Entry ".$matchDocumentMaster->matchingDocCode;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $masterData->customerID;
                        $data['holdingShareholder'] = null;
                        $data['holdingPercentage'] = 0;
                        $data['nonHoldingPercentage'] = 0;
                        $data['chequeNumber'] = $masterData->custChequeNo;
                        $data['documentType'] = $masterData->documentType;
                        $data['createdDateTime'] = \Helper::currentDateTime();
                        $data['createdUserID'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $data['createdUserPC'] = gethostname();
                        $data['timestamp'] = \Helper::currentDateTime();
                        $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                        $directReceipts = DirectReceiptDetail::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode, SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt")
                                                                ->WHERE('directReceiptAutoID', $input['PayMasterAutoId'])
                                                                ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID')
                                                                ->get();

                        $advReceipts = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount, SUM(paymentAmount) as transAmount, localCurrencyID as localCurrencyID, comRptCurrencyID as reportingCurrencyID,customerTransCurrencyID as transCurrencyID, comRptER as reportingCurrencyER, localER, customerTransER as transCurrencyER,serviceLineSystemID,serviceLineCode, SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt")
                                                                ->WHERE('custReceivePaymentAutoID', $input["PayMasterAutoId"])
                                                                ->groupBy('serviceLineSystemID')
                                                                ->get();



                                foreach ($directReceipts as $directReceipt)
                                {
                                    if($matchDocumentMaster->serviceLineSystemID == $directReceipt->serviceLineSystemID) {
                                        foreach ($detailAllRecords as $detailRecord) {
                                            $data['serviceLineSystemID'] = $directReceipt->serviceLineSystemID;
                                            $data['serviceLineCode'] = $directReceipt->serviceLineCode;
                                            $data['chartOfAccountSystemID'] = $masterData->custAdvanceAccountSystemID;
                                            $data['glCode'] = $masterData->custAdvanceAccount;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                            $data['documentTransAmount'] =  Helper::roundValue(abs($detailRecord->receiveAmountTrans));
                                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                            $data['documentLocalAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->localCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->localCurrencyER);
                                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                            $data['documentRptAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->companyRptCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->companyRptCurrencyER);
                                            $data['timestamp'] = Helper::currentDateTime();
                                            array_push($finalData, $data);
                                        }
                                    }
                                }

                                foreach ($directReceipts as $directReceipt)
                                {
                                    if($matchDocumentMaster->serviceLineSystemID == $directReceipt->serviceLineSystemID) {
                                        foreach ($detailAllRecords as $detailRecord) {
                                            $data['serviceLineSystemID'] = $detailRecord->serviceLineSystemID != null ? $detailRecord->serviceLineSystemID : $directReceipt->serviceLineSystemID;
                                            $data['serviceLineCode'] = $detailRecord->serviceLineCode != null ? $detailRecord->serviceLineCode : $directReceipt->serviceLineCode;
                                            $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                                            $data['glCode'] = $masterData->customerGLCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransCurrencyID'] = $detailRecord->custTransactionCurrencyID;
                                            $data['documentTransCurrencyER'] = $detailRecord->custTransactionCurrencyER;
                                            $data['documentTransAmount'] =  Helper::roundValue($detailRecord->receiveAmountTrans) * -1;
                                            $data['documentLocalCurrencyID'] = $detailRecord->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $detailRecord->localCurrencyER;
                                            $data['documentLocalAmount'] = Helper::roundValue($detailRecord->receiveAmountLocal) * -1;
                                            $data['documentRptCurrencyID'] = $detailRecord->companyReportingCurrencyID;
                                            $data['documentRptCurrencyER'] = $detailRecord->companyReportingER;
                                            $data['documentRptAmount'] = Helper::roundValue($detailRecord->receiveAmountRpt) * -1;
                                            $data['timestamp'] = Helper::currentDateTime();
                                            array_push($finalData, $data);
                                        }
                                    }
                                }




                                foreach ($advReceipts as $advReceipt) {
                                    if($matchDocumentMaster->serviceLineSystemID == $advReceipt->serviceLineSystemID) {
                                        foreach ($detailAllRecords as $detailRecord) {
                                            $data['serviceLineSystemID'] = $advReceipt->serviceLineSystemID;
                                            $data['serviceLineCode'] = $advReceipt->serviceLineCode;
                                            $data['chartOfAccountSystemID'] = $masterData->custAdvanceAccountSystemID;
                                            $data['glCode'] = $masterData->custAdvanceAccount;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                            $data['documentTransAmount'] =  Helper::roundValue(abs($detailRecord->receiveAmountTrans));
                                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                            $data['documentLocalAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->localCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->localCurrencyER);
                                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                            $data['documentRptAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->companyRptCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->companyRptCurrencyER);
                                            $data['timestamp'] = Helper::currentDateTime();
                                            array_push($finalData, $data);
                                        }
                                    }
                                }




                                foreach ($advReceipts as $advReceipt)
                                {
                                    if($matchDocumentMaster->serviceLineSystemID == $advReceipt->serviceLineSystemID) {
                                        foreach ($detailAllRecords as $detailRecord) {
                                            $data['serviceLineSystemID'] = $detailRecord->serviceLineSystemID != null ? $detailRecord->serviceLineSystemID : $advReceipt->serviceLineSystemID;
                                            $data['serviceLineCode'] = $detailRecord->serviceLineCode != null ? $detailRecord->serviceLineCode : $advReceipt->serviceLineCode;
                                            $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                                            $data['glCode'] = $masterData->customerGLCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransCurrencyID'] = $detailRecord->custTransactionCurrencyID;
                                            $data['documentTransCurrencyER'] = $detailRecord->custTransactionCurrencyER;
                                            $data['documentTransAmount'] =  \Helper::roundValue($detailRecord->receiveAmountTrans) * -1;
                                            $data['documentLocalCurrencyID'] = $detailRecord->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $detailRecord->localCurrencyER;
                                            $data['documentLocalAmount'] = \Helper::roundValue($detailRecord->receiveAmountLocal) * -1;
                                            $data['documentRptCurrencyID'] = $detailRecord->companyReportingCurrencyID;
                                            $data['documentRptCurrencyER'] = $detailRecord->companyReportingER;
                                            $data['documentRptAmount'] = \Helper::roundValue($detailRecord->receiveAmountRpt) * -1;
                                            $data['timestamp'] = \Helper::currentDateTime();
                                            array_push($finalData, $data);
                                        }
                                    }
                                }
                                
                                if ($masterData->isVATApplicable == 1 && $masterData->documentType == 15) {

                                    if($input['validInvoice'])
                                    {    
                                        $detailAllRecordsObj = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                                        ->with('reciept_vocuher')->get();
            
                                            foreach($detailAllRecordsObj as $records)
                                            {
                                                if($records->reciept_vocuher->VATAmount == 0)
                                                {
                                                    return $this->sendError('Invoice without VAT is being matched with reciept with VAT.This will nullify the VAT entries to zero.Are you sure you want to proceed ?', 300,['type' => 'UnconfirmAsset']);
            
                                                }
            
                                            }   
                                    }
                            


                                    $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, 
                                                                SUM(rptAmount) as rptAmount,
                                                                SUM(amount) as transAmount,
                                                                localCurrencyID,
                                                                rptCurrencyID as reportingCurrencyID,
                                                                currency as supplierTransactionCurrencyID,
                                                                currencyER as supplierTransactionER,
                                                                rptCurrencyER as companyReportingER,
                                                                localCurrencyER")
                                                                ->WHERE('documentSystemCode', $input["PayMasterAutoId"])
                                                                ->WHERE('documentSystemID', $input["documentSystemID"])
                                                                ->groupBy('documentSystemCode')
                                                                ->first();
                                        $taxLedgerData = [];

                                        $customerMatchingDetails = CustomerReceivePaymentDetail::with(['ar_data'])->selectRaw("SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt,arAutoID")
                                                            ->where('custReceivePaymentAutoID', $input["PayMasterAutoId"])
                                                            ->get();

                                        $taxConfigData = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                                        if (!empty($taxConfigData)) {  // out put vat entries
                                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                                                ->where('companySystemID', $input["companySystemID"])
                                                ->first();
                        
                                            if (!empty($chartOfAccountData)) {
                                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                                $taxLedgerData['outputVatGLAccountID'] = $data['chartOfAccountSystemID'];
                                            } else {
                                                Log::info('Receipt voucher VAT GL Entry Issues Id :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                                Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                            }
                                        } else {
                                            Log::info('Receipt voucher VAT GL Entry IssuesId :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                                        }

                                        $data['clientContractID'] = 'X';
                                        $data['contractUID'] = 159;
                                        
                                        if($tax)
                                        {   
                                            $data['documentTransCurrencyID'] = $tax->supplierTransactionCurrencyID;
                                            $data['documentTransCurrencyER'] = $tax->supplierTransactionER;
                                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                            $data['documentRptCurrencyID'] = $tax->reportingCurrencyID;
                                            $data['documentRptCurrencyER'] = $tax->companyReportingER;
                                        }
                                        else
                                        {
                                            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                        }


                                        foreach ($customerMatchingDetails as $key => $value) {
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS($value->VATAmount)) ;
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($value->VATAmountLocal)) ;
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS($value->VATAmountRpt)) ;
                                            $data['serviceLineSystemID'] = $matchDocumentMaster->segment->serviceLineSystemID;
                                            $data['serviceLineCode'] = $matchDocumentMaster->segment->ServiceLineCode;
                                            array_push($finalData, $data);
                                        }

                        
                                        $taxConfigData = TaxService::getOutputVATTransferGLAccount($input["companySystemID"]);
                    
                                        if (!empty($taxConfigData)) {
                                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatTransferGLAccountAutoID)
                                                ->where('companySystemID', $masterData->companySystemID)
                                                ->first();
                    
                                            if (!empty($chartOfAccountData)) {
                                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    
                                                $taxLedgerData['outputVatTransferGLAccountID'] = $data['chartOfAccountSystemID'];
                                            } else {
                                                Log::info('Receipt voucher VAT GL Entry Issues Id :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                                Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                                            }
                                        } else {
                                            Log::info('Receipt voucher VAT GL Entry IssuesId :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                            Log::info('Output VAT transfer GL Account not configured' . date('H:i:s'));
                                        }
                                        foreach ($customerMatchingDetails as $key => $value) {
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS($value->VATAmount)) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($value->VATAmountLocal)) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS($value->VATAmountRpt)) * -1;
                                            $data['serviceLineSystemID'] = $matchDocumentMaster->segment->serviceLineSystemID;
                                            $data['serviceLineCode'] = $matchDocumentMaster->segment->ServiceLineCode;
                                            array_push($finalData, $data);
                                        }
                                        

                                        if (count($taxLedgerData) > 0) {
                                            $masterModel = [
                                                'employeeSystemID' => $created_by['employeeSystemID'],
                                                'documentSystemID' => $matchDocumentMaster->documentSystemID,
                                                'matchDocumentMasterAutoID' => $matchDocumentMaster->matchDocumentMasterAutoID,
                                                'autoID' => $input['PayMasterAutoId'],
                                                'matching' => true,
                                                'companySystemID' => $matchDocumentMaster->companySystemID,
                                                'documentDate' => $matchDocumentMaster->matchingDocdate
                                            ];

                                            $taxResponse = RecieptVoucherTaxLedgerService::processEntry($taxLedgerData, $masterModel);

                                            if ($taxResponse['status']) {
                                                $finalDataTax = $taxResponse['data']['finalData'];
                                                $finalDetailDataTax = $taxResponse['data']['finalDetailData'];


                                                if ($finalDataTax) {
                                                    foreach ($finalDataTax as $tempFinalDataTax)
                                                    {
                                                        TaxLedger::create($tempFinalDataTax);
                                                    }

                                                    foreach ($finalDetailDataTax as $tempFinalDetailDataTax)
                                                    {
                                                        TaxLedgerDetail::create($tempFinalDetailDataTax);
                                                    }
                                                }
                                            } 
                                        }

                                }

                                $tempCollection = collect($finalData);
                                $finalLocalAmount = $tempCollection->sum('documentLocalAmount') * -1;
                                $finalRptAmount = $tempCollection->sum('documentRptAmount') * -1;

                                $epsilon = 0.00001;

                                if((abs($finalLocalAmount) > $epsilon) || (abs($finalRptAmount) > $epsilon)) {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentTransAmount'] = 0;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] =\Helper::roundValue($finalLocalAmount);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($finalRptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    if(isset($advReceipt)) {
                                        $data['serviceLineSystemID'] = $advReceipt->serviceLineSystemID;
                                        $data['serviceLineCode'] = $advReceipt->serviceLineCode;
                                    }
                                    elseif (isset($directReceipt)) {
                                        $data['serviceLineSystemID'] = $directReceipt->serviceLineSystemID;
                                        $data['serviceLineCode'] = $directReceipt->serviceLineCode;
                                    }
                                    else {
                                        $data['serviceLineSystemID'] = '';
                                        $data['serviceLineCode'] = '';
                                    }
                                    array_push($finalData, $data);
                                }
                    }

                    if ($input['documentSystemID'] == 19) {

                        $creditNoteMasterData = CreditNote::with('details')->find($input['PayMasterAutoId']);

                        $data['companySystemID'] = $matchDocumentMaster->companySystemID;
                        $data['companyID'] = $matchDocumentMaster->companyID;
                        $data['serviceLineSystemID'] = null;
                        $data['serviceLineCode'] = null;
                        $data['masterCompanyID'] = null;
                        $data['documentSystemID'] = $matchDocumentMaster->documentSystemID;
                        $data['documentID'] = $matchDocumentMaster->documentID;
                        $data['documentSystemCode'] = $input["PayMasterAutoId"];
                        $data['documentCode'] = $creditNoteMasterData->creditNoteCode;
                        $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                        $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                        $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                        $data['documentConfirmedDate'] = $matchDocumentMaster->matchingConfirmedDate;
                        $data['documentConfirmedBy'] = $matchDocumentMaster->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $matchDocumentMaster->approvedDate;
                        $data['documentFinalApprovedBy'] = $creditNoteMasterData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                        $data['documentNarration'] = "Matching Entry ".$matchDocumentMaster->matchingDocCode;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $creditNoteMasterData->customerID;
                        $data['holdingShareholder'] = null;
                        $data['holdingPercentage'] = 0;
                        $data['nonHoldingPercentage'] = 0;
                        $data['chequeNumber'] = 0;
                        $data['documentType'] = $creditNoteMasterData->documentType;
                        $data['createdDateTime'] = \Helper::currentDateTime();
                        $data['createdUserID'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        $data['createdUserPC'] = gethostname();
                        $data['timestamp'] = \Helper::currentDateTime();
                        $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                        $gainLocalAmount = $gainRptAmount = 0;

                        foreach ($detailAllRecords as $row) {

                            $tempValue = $row['receiveAmountLocal'] - Helper::conversionCurrencyByER(1,2,$row['receiveAmountTrans'],$creditNoteMasterData['localCurrencyER']);
                            $gainLocalAmount += round($tempValue,5);


                            $tempValue = $row['receiveAmountRpt'] - Helper::conversionCurrencyByER(1,2,$row['receiveAmountTrans'],$creditNoteMasterData['companyReportingER']);
                            $gainRptAmount += round($tempValue,5);
                        }

                        if(($gainLocalAmount != 0) || ($gainRptAmount != 0)) {
                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($creditNoteMasterData->companySystemID, $creditNoteMasterData->documentSystemiD, "exchange-gainloss-gl");
                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($creditNoteMasterData->companySystemID, $creditNoteMasterData->documentSystemiD, "exchange-gainloss-gl");
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $creditNoteMasterData->customerCurrencyID;
                            $data['documentTransCurrencyER'] = $creditNoteMasterData->customerCurrencyER;
                            $data['documentTransAmount'] = 0;
                            $data['documentLocalCurrencyID'] = $creditNoteMasterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $creditNoteMasterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($gainLocalAmount);
                            $data['documentRptCurrencyID'] = $creditNoteMasterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $creditNoteMasterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue($gainRptAmount);
                            $data['timestamp'] = \Helper::currentDateTime();
                            $data['serviceLineSystemID'] = $creditNoteMasterData->details->first()->serviceLineSystemID;
                            $data['serviceLineCode'] = $creditNoteMasterData->details->first()->serviceLineCode;
                            array_push($finalData, $data);

                            $data['chartOfAccountSystemID'] = $creditNoteMasterData->customerGLCodeSystemID;
                            $data['glCode'] = $creditNoteMasterData->customerGLCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $creditNoteMasterData->customerCurrencyID;
                            $data['documentTransCurrencyER'] = $creditNoteMasterData->customerCurrencyER;
                            $data['documentTransAmount'] = 0;
                            $data['documentLocalCurrencyID'] = $creditNoteMasterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $creditNoteMasterData->localCurrencyER;
                            if($gainLocalAmount < 0) {
                                $data['documentLocalAmount'] = \Helper::roundValue(abs($gainLocalAmount));
                            }
                            else {
                                $data['documentLocalAmount'] = \Helper::roundValue($gainLocalAmount) * -1;
                            }
                            $data['documentRptCurrencyID'] = $creditNoteMasterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $creditNoteMasterData->companyReportingER;
                            if($gainRptAmount < 0) {
                                $data['documentRptAmount'] = \Helper::roundValue(abs($gainRptAmount));
                            }
                            else {
                                $data['documentRptAmount'] = \Helper::roundValue($gainRptAmount) * -1;
                            }
                            $data['timestamp'] = \Helper::currentDateTime();
                            $data['serviceLineSystemID'] = $creditNoteMasterData->details->first()->serviceLineSystemID;
                            $data['serviceLineCode'] = $creditNoteMasterData->details->first()->serviceLineCode;
                            array_push($finalData, $data);
                        }
                    }

                    foreach ($finalData as $storeData) {
                        GeneralLedger::create($storeData);
                    }
                }

                $input['modifiedPc'] = gethostname();
                $input['modifiedUser'] = $employee->empID;
                $input['modifiedUserSystemID'] = $employee->employeeSystemID;

                $matchDocumentMaster = $this->matchDocumentMasterRepository->update($input, $id);


            DB::commit();
            return $this->sendReponseWithDetails($matchDocumentMaster->toArray(), trans('custom.receipt_voucher_matching_updated_successfully'),1,$confirm['data'] ?? null);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

        
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/matchDocumentMasters/{id}",
     *      summary="Remove the specified MatchDocumentMaster from storage",
     *      tags={"MatchDocumentMaster"},
     *      description="Delete MatchDocumentMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MatchDocumentMaster",
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
        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError(trans('custom.match_document_master_not_found_1'));
        }

        $matchDocumentMaster->delete();

        return $this->sendResponse($id, trans('custom.match_document_master_deleted_successfully'));
    }

    public function getMatchDocumentMasterFormData(Request $request)
    {
        $companyId = $request['companyId'];

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = MatchDocumentMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $employees = Employee::selectRaw('empID, empName, employeeSystemID')->where('discharegedYN','<>', 2);
        if(Helper::checkHrmsIntergrated($companyId)){
            $employees = $employees->whereHas('hr_emp', function($q){
                $q->where('isDischarged', 0)->where('empConfirmedYN', 1);
            });
        }
        $employees = $employees->get();   

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $customer = CustomerAssigned::select('*')
                                    ->whereHas('customer_master',function($q){
                                        $q->where('isCustomerActive',1);
                                    })   
                                    ->where('companySystemID', $companyId)
                                    ->where('isAssigned', '-1')
                                    ->where('isActive', '1')
                                    ->get();
        $companyFinanceYear = \Helper::companyFinanceYear($companyId, 1);
        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'suppliers' => $supplier,
            'employees' => $employees,
            'customer' => $customer,
            'isAdvanceReceipt' => Helper::checkPolicy($companyId,49),
            'companyFinanceYear' => $companyFinanceYear,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getMatchDocumentMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'supplierID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        $search = $request->input('search.value');

        $invMaster = $this->matchDocumentMasterRepository->matchDocumentListQuery($request, $input, $search, $supplierID);

        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('matchDocumentMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getPaymentVoucherMatchPullingDetail(Request $request)
    {
        $input = $request->all();

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $matchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);
        if (empty($matchDocumentMasterData)) {
            return $this->sendError(trans('custom.matching_document_not_found'));
        }

        $decimalPlaces  = Helper::getCurrencyDecimalPlace($matchDocumentMasterData->supplierTransCurrencyID);

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $matchingDocdate = Carbon::parse($matchDocumentMasterData->matchingDocdate)->format('Y-m-d');
        $user_type = $matchDocumentMasterData->user_type;
        if($user_type == 2)
        {

                $qry1 = 'SELECT
                employee_ledger.id,
                1 as type,
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
                erp_matchdocumentmaster.companySystemID = ' . $matchDocumentMasterData->companySystemID . ' 
                AND erp_matchdocumentmaster.documentSystemID = 15
                GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                ) md ON md.documentSystemID = employee_ledger.documentSystemID 
                AND md.PayMasterAutoId = employee_ledger.documentSystemCode 
                AND md.BPVsupplierID = employee_ledger.employeeSystemID 
                AND md.supplierTransCurrencyID = employee_ledger.supplierTransCurrencyID 
                AND md.companySystemID = employee_ledger.companySystemID 
                LEFT JOIN currencymaster ON employee_ledger.supplierTransCurrencyID = currencymaster.currencyID 
            WHERE
                employee_ledger.invoiceType IN ( 0, 1, 4, 7 ) 
                AND DATE_FORMAT(employee_ledger.documentDate,"%Y-%m-%d") <= "' . $matchingDocdate . '" 
                AND employee_ledger.selectedToPaymentInv = 0 
                AND employee_ledger.fullyInvoice <> 2 
                AND employee_ledger.documentSystemID = 11
                AND employee_ledger.companySystemID = ' . $matchDocumentMasterData->companySystemID . ' 
                AND employee_ledger.employeeSystemID = ' . $matchDocumentMasterData->employee_id . ' 
                AND employee_ledger.supplierTransCurrencyID = ' . $matchDocumentMasterData->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount, '.$decimalPlaces.') != 0 ORDER BY employee_ledger.id DESC';



        $qry2 = 'SELECT
                employee_ledger.id,
                2 as type,
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
                        erp_matchdocumentmaster.companySystemID = ' . $matchDocumentMasterData->companySystemID . ' 
                        AND erp_matchdocumentmaster.documentSystemID = 15
                        GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                        ) md ON md.documentSystemID = employee_ledger.documentSystemID 
                        AND md.PayMasterAutoId = employee_ledger.documentSystemCode 
                        AND md.BPVsupplierID = employee_ledger.employeeSystemID 
                        AND md.supplierTransCurrencyID = employee_ledger.supplierTransCurrencyID 
                        AND md.companySystemID = employee_ledger.companySystemID 
                        LEFT JOIN currencymaster ON employee_ledger.supplierTransCurrencyID = currencymaster.currencyID 
                WHERE
                    employee_ledger.invoiceType IN ( 0, 1, 4, 7 ) 
                    AND DATE_FORMAT(employee_ledger.documentDate,"%Y-%m-%d") <= "' . $matchingDocdate . '" 
                    AND employee_ledger.selectedToPaymentInv = 0 
                    AND employee_ledger.fullyInvoice <> 2 
                    AND employee_ledger.companySystemID = ' . $matchDocumentMasterData->companySystemID . ' 
                    AND employee_ledger.employeeSystemID = ' . $matchDocumentMasterData->employee_id . ' 
                    AND employee_ledger.supplierTransCurrencyID = ' . $matchDocumentMasterData->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount, '.$decimalPlaces.') != 0 ORDER BY employee_ledger.id DESC';


              
                $invMaster = DB::select($qry1);
                $output = DB::select($qry2);

                //$finalQry = 'SELECT * FROM (' . $qry2 . ' UNION ALL ' . $qry1 . ') as t1';
                foreach($invMaster as $key=>$val)
                {
                    foreach($output as $out)
                    {
                        if($val->bookingInvDocCode == $out->bookingInvDocCode)
                        {
                            $invMaster[$key]->matchedAmount = $val->matchedAmount + $out->matchedAmount;
                            $invMaster[$key]->paymentBalancedAmount = $val->supplierInvoiceAmount - $invMaster[$key]->matchedAmount;
                            break;
                        }
                        
                    }
        
                
                
                }


        }
        else
        {

            $filter = '';
            $search = $request->input('search.value');
            if ($search) {
                $search = str_replace("\\", "\\\\\\\\", $search);
                $filter = " AND (( erp_accountspayableledger.documentCode LIKE '%{$search}%') OR ( erp_accountspayableledger.supplierInvoiceNo LIKE '%{$search}%'))";
            }
            
          
    
            $qry = "SELECT
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
                LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId
                LEFT JOIN erp_debitnote ON erp_paysupplierinvoicedetail.PayMasterAutoId = erp_debitnote.debitNoteAutoID
                WHERE (erp_paysupplierinvoicemaster.PayMasterAutoId IS NULL OR (erp_paysupplierinvoicemaster.invoiceType != 6 AND erp_paysupplierinvoicemaster.invoiceType != 7)) AND (erp_debitnote.type = 1 OR erp_debitnote.debitNoteAutoID IS NULL)
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
                    SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount,
                    erp_matchdocumentmaster.matchingConfirmedYN
                FROM
                    erp_matchdocumentmaster
                WHERE
                    erp_matchdocumentmaster.companySystemID = $matchDocumentMasterData->companySystemID
                    AND erp_matchdocumentmaster.documentSystemID = 15
                    GROUP BY companySystemID,PayMasterAutoId,documentSystemID,BPVsupplierID,supplierTransCurrencyID
                    ) md ON md.documentSystemID = erp_accountspayableledger.documentSystemID
                    AND md.PayMasterAutoId = erp_accountspayableledger.documentSystemCode
                    AND md.BPVsupplierID = erp_accountspayableledger.supplierCodeSystem
                    AND md.supplierTransCurrencyID = erp_accountspayableledger.supplierTransCurrencyID
                    AND md.companySystemID = erp_accountspayableledger.companySystemID
                    LEFT JOIN currencymaster ON erp_accountspayableledger.supplierTransCurrencyID = currencymaster.currencyID
                WHERE
                    erp_accountspayableledger.invoiceType IN ( 0, 1, 4, 7 )
                    AND DATE_FORMAT(erp_accountspayableledger.documentDate,'%Y-%m-%d') <= '{$matchingDocdate}'
                    {$filter}
                    AND erp_accountspayableledger.selectedToPaymentInv = 0
                    AND erp_accountspayableledger.documentSystemID = 11
                    AND erp_accountspayableledger.fullyInvoice <> 2
                    AND erp_accountspayableledger.companySystemID =  $matchDocumentMasterData->companySystemID
                    AND erp_accountspayableledger.supplierCodeSystem = $matchDocumentMasterData->BPVsupplierID
                    AND erp_accountspayableledger.supplierTransCurrencyID = $matchDocumentMasterData->supplierTransCurrencyID HAVING ROUND(paymentBalancedAmount,$decimalPlaces) != 0 ORDER BY erp_accountspayableledger.apAutoID DESC";

            $invMaster = DB::select($qry);

        }
     


       



        $col[0] = $input['order'][0]['column'];
        $col[1] = $input['order'][0]['dir'];
        $request->request->remove('order');
        $data['order'] = [];
        /*  $data['order'][0]['column'] = '';
          $data['order'][0]['dir'] = '';*/
        $data['search']['value'] = '';
        $request->merge($data);

        $request->request->remove('search.value');

        return \DataTables::of($invMaster)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getMatchDocumentMasterRecord(Request $request)
    {
        $id = $request->get('matchDocumentMasterAutoID');

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->with(['created_by', 'confirmed_by', 'modified_by', 'cancelled_by', 'company', 'transactioncurrency', 'supplier', 'detail' => function($query) {
            $query->with(['pomaster']);
        }])->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError(trans('custom.match_document_master_not_found_1'));
        }

        return $this->sendResponse($matchDocumentMaster, trans('custom.data_retrieved_successfully'));
    }

    public function PaymentVoucherMatchingCancel(Request $request)
    {
        $input = $request->all();
        $employee = Helper::getEmployeeInfo();
        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $MatchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);

        if (empty($MatchDocumentMasterData)) {
            return $this->sendError(trans('custom.match_document_master_not_found_1'));
        }

        if ($MatchDocumentMasterData->matchingConfirmedYN == 1) {
            return $this->sendError(trans('custom.you_cannot_cancel_this_matching_it_is_confirmed'));
        }


        $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('matchingDocID'))
            ->where('matchingDocID', $matchDocumentMasterAutoID)
            ->first();

        if (!empty($pvDetailExist)) {
            return $this->sendError(trans('custom.cannot_cancel_delete_the_invoices_added_to_the_det'));
        }

        $poAdvanceRequestDetailExist = AdvancePaymentDetails::select(DB::raw('matchingDocID'))
        ->where('matchingDocID', $matchDocumentMasterAutoID)
        ->first();

        if (!empty($poAdvanceRequestDetailExist)) {
            return $this->sendError(trans('custom.cannot_cancel_delete_the_po_advance_requests_added'));
        }

        if ($MatchDocumentMasterData->matchingDocCode != '0') {
            $updateData = [
                'cancelledYN' => 1,
                'cancelledByEmpSystemID' => $employee->employeeSystemID,
                'cancelledComment' => $input['comment'],
                'cancelledDate' => date('Y-m-d H:i:s'),
                'PayMasterAutoId' => null,
                'BPVcode' => null,
                'BPVdate' => null,
                'BPVNarration' => null,
                'BPVsupplierID' => null,
                'payAmountSuppTrans' => 0,
                'matchBalanceAmount' => 0,
            ];
            
            $deleteDocument = MatchDocumentMaster::where('matchDocumentMasterAutoID', $matchDocumentMasterAutoID)
                    ->update($updateData);
        } else {
            $deleteDocument = MatchDocumentMaster::where('matchDocumentMasterAutoID', $matchDocumentMasterAutoID)
                ->delete();
        }

        if ($deleteDocument) {

            AuditTrial::insertAuditTrial('MatchDocumentMaster',$matchDocumentMasterAutoID,$input['comment'],'Cancelled');

            return $this->sendResponse($MatchDocumentMasterData, trans('custom.document_canceled_successfully'));
        } else {
            return $this->sendResponse($MatchDocumentMasterData, 'Document not canceled, try again');
        }

    }

    public function getRVMatchDocumentMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year', 'customerID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $customerID = $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');

        $search = $request->input('search.value');

        $invMaster = $this->matchDocumentMasterRepository->receiptVoucherMatchingListQuery($request, $input, $search ,$customerID);

        return \DataTables::eloquent($invMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('matchDocumentMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getReceiptVoucherMatchItems(Request $request)
    {
        $input = $request->all();

        if (!isset($input['matchType'])) {
            return $this->sendError(trans('custom.please_select_match_type'));
        }

        if (!isset($input['BPVsupplierID'])) {
            return $this->sendError(trans('custom.please_select_customer'));
        }

        $invoiceMaster = [];

        if ($input['matchType'] == 1) {
            $invoiceMaster = DB::select("SELECT
                                           erp_customerreceivepayment.custReceivePaymentAutoID as masterAutoID,
                                           erp_customerreceivepayment.documentSystemID,
                                           erp_customerreceivepayment.companySystemID,
                                           erp_customerreceivepayment.companyID,
                                           erp_customerreceivepayment.custPaymentReceiveCode as docMatchedCode,
                                           erp_customerreceivepayment.custPaymentReceiveDate as docMatchedDate,
                                           erp_customerreceivepayment.customerID,
                                           Sum(
                                               erp_custreceivepaymentdet.receiveAmountTrans
                                           ) AS SumOfreceiveAmountTrans,
                                           Sum(
                                               erp_custreceivepaymentdet.receiveAmountLocal
                                           ) AS SumOfreceiveAmountLocal,
                                           Sum(
                                               erp_custreceivepaymentdet.receiveAmountRpt
                                           ) AS SumOfreceiveAmountRpt,
                                           IFNULL(advd.SumOfmatchingAmount, 0) AS SumOfmatchingAmount,
                                           ROUND((COALESCE (SUM(erp_custreceivepaymentdet.receiveAmountTrans),0) - IFNULL(advd.SumOfmatchingAmount, 0)
                                           ),currency.DecimalPlaces) AS BalanceAmt,
                                               currency.CurrencyCode,
                                           currency.DecimalPlaces
                                        FROM
                                           erp_customerreceivepayment
                                        INNER JOIN erp_custreceivepaymentdet ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                        INNER JOIN currencymaster AS currency ON currency.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
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
                                               erp_matchdocumentmaster.documentSystemID,
                                               erp_matchdocumentmaster.companySystemID
                                        ) AS advd ON (
                                           erp_customerreceivepayment.custReceivePaymentAutoID = advd.PayMasterAutoId
                                           AND erp_customerreceivepayment.documentSystemID = advd.documentSystemID
                                           AND erp_customerreceivepayment.companySystemID = advd.companySystemID
                                        )
                                        WHERE
                                           erp_custreceivepaymentdet.companySystemID = " . $input['companySystemID'] . "
                                        AND erp_custreceivepaymentdet.bookingInvCode = '0'
                                        AND erp_customerreceivepayment.approved = -1
                                        AND customerID = " . $input['BPVsupplierID'] . "
                                        AND erp_customerreceivepayment.matchInvoice < 2
                                        GROUP BY
                                           erp_custreceivepaymentdet.custReceivePaymentAutoID,
                                           erp_customerreceivepayment.documentSystemID,
                                           erp_custreceivepaymentdet.companySystemID,
                                           erp_customerreceivepayment.customerID
                                        HAVING
                                           (
                                               ROUND(
                                                   BalanceAmt,
                                                   1
                                               ) > 0
                                           )");
        } elseif ($input['matchType'] == 2) {
            $invoiceMaster = DB::select("SELECT
                                            erp_creditnotedetails.creditNoteDetailsID AS masterAutoID,
                                            erp_creditnote.documentSystemID,
                                            erp_creditnote.companySystemID,
                                            erp_creditnote.companyID,
                                            erp_creditnote.creditNoteCode AS docMatchedCode,
                                            erp_creditnote.creditNoteDate AS docMatchedDate,
                                            erp_creditnote.customerID,
                                            currency.CurrencyCode,
                                            currency.DecimalPlaces,
                                            SUM(erp_creditnotedetails.creditAmount) AS SumOfreceiveAmountTrans,
                                            erp_creditnotedetails.serviceLineCode AS serviceLineCode,
                                            (
                                                SUM(erp_creditnotedetails.creditAmount) - (
                                                    (IFNULL(
                                                        receipt.SumOfreceiptAmount,
                                                        0
                                                    )* -1) + IFNULL(advd.SumOfmatchingAmount, 0)
                                                )
                                            ) AS BalanceAmt
                                        FROM
                                            erp_creditnotedetails
                                        INNER JOIN erp_creditnote AS erp_creditnote ON erp_creditnote.creditNoteAutoID = erp_creditnotedetails.creditNoteAutoID
                                        INNER JOIN currencymaster AS currency ON currency.currencyID = erp_creditnote.customerCurrencyID
                                        LEFT JOIN (
                                            SELECT
                                                custReceivePaymentAutoID,
                                                addedDocumentSystemID,
                                                bookingInvCodeSystem,
                                                bookingInvCode,
                                                erp_custreceivepaymentdet.companySystemID,
                                                erp_accountsreceivableledger.serviceLineSystemID,
                                                COALESCE (SUM(receiveAmountTrans), 0) AS SumOfreceiptAmount
                                            FROM
                                                erp_custreceivepaymentdet
                                            INNER JOIN erp_accountsreceivableledger ON erp_accountsreceivableledger.arAutoID = erp_custreceivepaymentdet.arAutoID
                                            WHERE
                                                bookingInvCode <> '0'
                                            GROUP BY
                                                addedDocumentSystemID,
                                                bookingInvCodeSystem,
                                                companySystemID,
                                                erp_accountsreceivableledger.serviceLineSystemID
                                        ) AS receipt ON (
                                            receipt.bookingInvCodeSystem = erp_creditnote.creditNoteAutoID
                                            AND receipt.addedDocumentSystemID = erp_creditnote.documentSystemiD
                                            AND receipt.companySystemID = erp_creditnote.companySystemID
                                            AND receipt.serviceLineSystemID = erp_creditnotedetails.serviceLineSystemID
                                        )
                                        LEFT JOIN (
                                            SELECT
                                                erp_matchdocumentmaster.PayMasterAutoId,
                                                erp_matchdocumentmaster.documentSystemID,
                                                erp_matchdocumentmaster.companySystemID,
                                                erp_matchdocumentmaster.BPVcode,
                                                erp_matchdocumentmaster.serviceLineSystemID,
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
                                                erp_matchdocumentmaster.companySystemID,
                                                erp_matchdocumentmaster.serviceLineSystemID
                                        ) AS advd ON (
                                            erp_creditnote.creditNoteAutoID = advd.PayMasterAutoId
                                            AND erp_creditnote.documentSystemiD = advd.documentSystemID
                                            AND erp_creditnote.companySystemID = advd.companySystemID
                                            AND erp_creditnotedetails.serviceLineSystemID = advd.serviceLineSystemID
                                        )
                                        WHERE
                                            erp_creditnote.companySystemID = " . $input['companySystemID'] . "
                                        AND erp_creditnote.approved = - 1
                                        AND erp_creditnote.matchInvoice <> 2
                                        AND erp_creditnote.customerID = " . $input['BPVsupplierID'] . "
                                        GROUP BY
                                            erp_creditnotedetails.serviceLineSystemID,
                                            erp_creditnote.creditNoteAutoID,
                                            erp_creditnote.documentSystemiD,
                                            erp_creditnote.companySystemID,
                                            erp_creditnote.customerID
                                        HAVING
                                            (
                                                ROUND(BalanceAmt, DecimalPlaces) > 0
                                            ) ORDER BY erp_creditnote.creditNoteDate");
        } else if ($input['matchType'] == 3) {
            $invoiceMaster = DB::select("SELECT *  FROM
                                        (SELECT
                                            erp_directreceiptdetails.directReceiptDetailsID AS masterAutoID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_customerreceivepayment.companySystemID,
                                            erp_customerreceivepayment.companyID,
                                            erp_customerreceivepayment.custPaymentReceiveCode AS docMatchedCode,
                                            erp_customerreceivepayment.custPaymentReceiveDate AS docMatchedDate,
                                            erp_customerreceivepayment.customerID,
                                             erp_directreceiptdetails.serviceLineCode AS serviceLineCode,
                                            Sum( erp_directreceiptdetails.DRAmount ) AS SumOfreceiveAmountTrans,
                                            Sum( erp_directreceiptdetails.localAmount ) AS SumOfreceiveAmountLocal,
                                            Sum( erp_directreceiptdetails.comRptAmount ) AS SumOfreceiveAmountRpt,
                                            IFNULL( advd.SumOfmatchingAmount, 0 ) AS SumOfmatchingAmount,
                                            ROUND(
                                            ( COALESCE ( SUM( erp_directreceiptdetails.DRAmount ), 0 ) - IFNULL( advd.SumOfmatchingAmount, 0 ) ),
                                            currency.DecimalPlaces 
                                            ) AS BalanceAmt,
                                            currency.CurrencyCode,
                                            currency.DecimalPlaces,
                                            1 AS tableType
                                        FROM
                                            erp_customerreceivepayment
                                            INNER JOIN erp_directreceiptdetails ON erp_directreceiptdetails.directReceiptAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                            INNER JOIN currencymaster AS currency ON currency.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
                                            LEFT JOIN (
                                        SELECT
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID,
                                            erp_matchdocumentmaster.BPVcode,
                                            erp_matchdocumentmaster.tableType,
                                            erp_matchdocumentmaster.serviceLineSystemID,
                                            COALESCE ( SUM( erp_matchdocumentmaster.matchingAmount ), 0 ) AS SumOfmatchingAmount 
                                        FROM
                                            erp_matchdocumentmaster 
                                            where companySystemID = " . $input['companySystemID'] . " 
                                        GROUP BY
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID, 
                                            erp_matchdocumentmaster.serviceLineSystemID
                                            ) AS advd ON ( erp_directreceiptdetails.directReceiptAutoID = advd.PayMasterAutoId AND erp_customerreceivepayment.documentSystemID = advd.documentSystemID AND erp_customerreceivepayment.companySystemID = advd.companySystemID AND advd.tableType = 1 AND erp_directreceiptdetails.serviceLineSystemID = advd.serviceLineSystemID) 
                                        WHERE
                                            erp_directreceiptdetails.companySystemID = " . $input['companySystemID'] . " 
                                            AND erp_customerreceivepayment.documentType = 15 
                                            AND erp_customerreceivepayment.approved = - 1 
                                            AND customerID = " . $input['BPVsupplierID'] . "
                                            AND erp_customerreceivepayment.matchInvoice < 2 
                                        GROUP BY
                                            erp_directreceiptdetails.serviceLineSystemID,
                                            erp_directreceiptdetails.directReceiptAutoID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_directreceiptdetails.companySystemID,
                                            erp_customerreceivepayment.customerID 
                                        HAVING
                                            ( ROUND( BalanceAmt, 1 ) > 0 )
                                            
                                            Union 
                                            
                                            SELECT
                                            erp_advancereceiptdetails.advanceReceiptDetailAutoID AS masterAutoID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_customerreceivepayment.companySystemID,
                                            erp_customerreceivepayment.companyID,
                                            erp_customerreceivepayment.custPaymentReceiveCode AS docMatchedCode,
                                            erp_customerreceivepayment.custPaymentReceiveDate AS docMatchedDate,
                                            erp_customerreceivepayment.customerID,
                                            erp_advancereceiptdetails.serviceLineCode AS serviceLineCode,
                                            Sum( erp_advancereceiptdetails.supplierTransAmount ) AS SumOfreceiveAmountTrans,
                                            Sum( erp_advancereceiptdetails.localAmount ) AS SumOfreceiveAmountLocal,
                                            Sum( erp_advancereceiptdetails.comRptAmount ) AS SumOfreceiveAmountRpt,
                                            IFNULL( advd.SumOfmatchingAmount, 0 ) AS SumOfmatchingAmount,
                                            ROUND(
                                            ( COALESCE ( SUM( erp_advancereceiptdetails.supplierTransAmount ), 0 ) - IFNULL( advd.SumOfmatchingAmount, 0 ) ),
                                            currency.DecimalPlaces 
                                            ) AS BalanceAmt,
                                            currency.CurrencyCode,
                                            currency.DecimalPlaces ,
                                            2 AS tableType
                                        FROM
                                            erp_customerreceivepayment
                                            INNER JOIN erp_advancereceiptdetails ON erp_advancereceiptdetails.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                            INNER JOIN currencymaster AS currency ON currency.currencyID = erp_customerreceivepayment.custTransactionCurrencyID
                                            LEFT JOIN (
                                        SELECT
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID,
                                            erp_matchdocumentmaster.BPVcode,
                                            erp_matchdocumentmaster.tableType,
                                            erp_matchdocumentmaster.serviceLineSystemID,
                                            COALESCE ( SUM( erp_matchdocumentmaster.matchingAmount ), 0 ) AS SumOfmatchingAmount 
                                        FROM
                                            erp_matchdocumentmaster 
                                            where companySystemID = " . $input['companySystemID'] . "
                                        GROUP BY
                                            erp_matchdocumentmaster.PayMasterAutoId,
                                            erp_matchdocumentmaster.documentSystemID,
                                            erp_matchdocumentmaster.companySystemID,
                                            erp_matchdocumentmaster.serviceLineSystemID 
                                            ) AS advd ON ( erp_advancereceiptdetails.custReceivePaymentAutoID = advd.PayMasterAutoId AND erp_customerreceivepayment.documentSystemID = advd.documentSystemID AND erp_customerreceivepayment.companySystemID = advd.companySystemID AND advd.tableType = 2 AND erp_advancereceiptdetails.serviceLineSystemID = advd.serviceLineSystemID) 
                                        WHERE
                                            erp_advancereceiptdetails.companySystemID = " . $input['companySystemID'] . "
                                            AND erp_customerreceivepayment.documentType = 15 
                                            AND erp_customerreceivepayment.approved = - 1 
                                            AND customerID = " . $input['BPVsupplierID'] . "
                                            AND erp_customerreceivepayment.matchInvoice < 2 
                                        GROUP BY
                                            erp_advancereceiptdetails.custReceivePaymentAutoID,
                                            erp_advancereceiptdetails.serviceLineSystemID,
                                            erp_customerreceivepayment.documentSystemID,
                                            erp_advancereceiptdetails.companySystemID,
                                            erp_customerreceivepayment.customerID 
                                        HAVING
                                            ( ROUND( BalanceAmt, 1 ) > 0 )) as final");
        }

        return $this->sendResponse($invoiceMaster, trans('custom.data_retrived_successfully'));
    }

    public function getReceiptVoucherPullingDetail(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $matchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);
        if (empty($matchDocumentMasterData)) {
            return $this->sendError(trans('custom.matching_document_not_found'));
        }

       if($matchDocumentMasterData->documentSystemID == 19) {
           $creditNoteDetails = CreditNoteDetails::where('creditNoteAutoID',$matchDocumentMasterData->PayMasterAutoId)->where('serviceLineSystemID',$matchDocumentMasterData->serviceLineSystemID)->first();
           if (empty($creditNoteDetails)) {
               return $this->sendError(trans('custom.credit_note_details_not_found'));
           }
           $serviceLineSystemID = $creditNoteDetails->serviceLineSystemID;
       }

        if($matchDocumentMasterData->documentSystemID == 21) {
            if($matchDocumentMasterData->tableType == 1) {
                $directReceiptDetails = DirectReceiptDetail::where('directReceiptAutoID',$matchDocumentMasterData->PayMasterAutoId)->where('serviceLineSystemID',$matchDocumentMasterData->serviceLineSystemID)->first();
            }
            if($matchDocumentMasterData->tableType == 2) {

                $directReceiptDetails = AdvanceReceiptDetails::where('custReceivePaymentAutoID',$matchDocumentMasterData->PayMasterAutoId)->where('serviceLineSystemID',$matchDocumentMasterData->serviceLineSystemID)->first();
            }

            if (empty($directReceiptDetails)) {
                return $this->sendError(trans('custom.direct_receipt_details_not_found'));
            }
            $serviceLineSystemID = $directReceiptDetails->serviceLineSystemID;

        }

        $filter = '';
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\\\\\", $search);
            $filter = " AND (( erp_accountsreceivableledger.documentCode LIKE '%{$search}%') OR ( erp_accountsreceivableledger.InvoiceNo LIKE '%{$search}%'))";
        }

        $matchingDocdate = Carbon::parse($matchDocumentMasterData->matchingDocdate)->format('Y-m-d');

        $segmentCheckPolicy = Helper::checkPolicy($matchDocumentMasterData->companySystemID,95);

        $qry = "SELECT
	erp_accountsreceivableledger.arAutoID,
	erp_accountsreceivableledger.documentCodeSystem AS bookingInvCodeSystem,
	erp_accountsreceivableledger.serviceLineCode AS segment,
	erp_accountsreceivableledger.custTransCurrencyID,
	erp_accountsreceivableledger.custTransER,
	erp_accountsreceivableledger.localCurrencyID,
	erp_accountsreceivableledger.localER,
	erp_accountsreceivableledger.localAmount,
	erp_accountsreceivableledger.comRptCurrencyID,
	erp_accountsreceivableledger.comRptER,
	erp_accountsreceivableledger.comRptAmount,
	erp_accountsreceivableledger.companySystemID,
	erp_accountsreceivableledger.companyID,
	erp_accountsreceivableledger.documentSystemID AS addedDocumentSystemID,
	erp_accountsreceivableledger.documentID AS addedDocumentID,
	erp_accountsreceivableledger.documentCode AS bookingInvDocCode,
	erp_accountsreceivableledger.documentDate AS bookingInvoiceDate,
	erp_accountsreceivableledger.documentType AS addedDocumentType,
	erp_accountsreceivableledger.InvoiceNo AS docInvoiceNumber,
	erp_accountsreceivableledger.customerID,
	CurrencyCode,
	DecimalPlaces,
	IFNULL(custInvoiceAmount, 0) AS custInvoiceAmount,
	IFNULL(sumReturnTransactionAmount, 0) AS sumReturnTransactionAmount,
	IFNULL(sid.SumOfreceiveAmountTrans, 0) AS SumOfreceiveAmountTrans,
	IFNULL(md.matchedAmount, 0) AS matchedAmount,
	Round((IFNULL(custInvoiceAmount, 0) - IFNULL(sid.SumOfreceiveAmountTrans, 0) - IFNULL( sumReturnTransactionAmount, 0) - (IFNULL(md.matchedAmount, 0)) * -1),3) as balanceMemAmount,
	(IFNULL(custInvoiceAmount, 0) - IFNULL(sid.SumOfreceiveAmountTrans, 0) - IFNULL( sumReturnTransactionAmount, 0) - (IFNULL(md.matchedAmount, 0)) * -1) as balanceMemAmountNotRounded,
	false as isChecked
FROM
	erp_accountsreceivableledger
LEFT JOIN (
	SELECT
		erp_custreceivepaymentdet.arAutoID,
		IFNULL(
			Sum(
				erp_custreceivepaymentdet.bookingAmountTrans
			),
			0
		) AS SumOfsupplierPaymentAmount,
		IFNULL(
			Sum(
				erp_custreceivepaymentdet.custbalanceAmount
			),
			0
		) AS SumOfcustbalanceAmount,
		IFNULL(Sum(erp_custreceivepaymentdet.receiveAmountTrans), 0) AS SumOfreceiveAmountTrans
	FROM
		erp_custreceivepaymentdet
	GROUP BY
		erp_custreceivepaymentdet.arAutoID
) sid ON sid.arAutoID = erp_accountsreceivableledger.arAutoID
LEFT JOIN (
	SELECT
		erp_matchdocumentmaster.PayMasterAutoId,
		erp_matchdocumentmaster.companyID,
		erp_matchdocumentmaster.companySystemID,
		erp_matchdocumentmaster.documentSystemID,
		erp_matchdocumentmaster.BPVcode,
		erp_matchdocumentmaster.BPVsupplierID,
		erp_matchdocumentmaster.supplierTransCurrencyID,
		SUM(
			erp_matchdocumentmaster.matchedAmount
		) AS matchedAmount,
		SUM(
			erp_matchdocumentmaster.matchLocalAmount
		) AS matchLocalAmount,
		SUM(
			erp_matchdocumentmaster.matchRptAmount
		) AS matchRptAmount,
		erp_matchdocumentmaster.matchingConfirmedYN
	FROM
		erp_matchdocumentmaster
	WHERE
		erp_matchdocumentmaster.companySystemID = $matchDocumentMasterData->companySystemID
	AND erp_matchdocumentmaster.documentSystemID = $matchDocumentMasterData->documentSystemID
	GROUP BY
		companySystemID,
		PayMasterAutoId,
		documentSystemID,
		BPVsupplierID,
		supplierTransCurrencyID
) md ON md.documentSystemID = erp_accountsreceivableledger.documentSystemID
AND md.PayMasterAutoId = erp_accountsreceivableledger.documentCodeSystem
AND md.BPVsupplierID = erp_accountsreceivableledger.customerID
AND md.supplierTransCurrencyID = erp_accountsreceivableledger.custTransCurrencyID
AND md.companySystemID = erp_accountsreceivableledger.companySystemID
LEFT JOIN (
    SELECT 
        salesreturndetails.deliveryOrderDetailID,
        erp_customerinvoiceitemdetails.custInvoiceDirectAutoID,
        salesreturndetails.salesReturnID,
        salesreturndetails.companySystemID,
        sum(salesreturndetails.transactionAmount + (salesreturndetails.transactionAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnTransactionAmount,
        sum(salesreturndetails.companyLocalAmount + (salesreturndetails.companyLocalAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnLocalAmount,
        sum(salesreturndetails.companyReportingAmount + (salesreturndetails.companyReportingAmount * salesreturndetails.VATPercentage / 100)) AS sumReturnRptAmount
        FROM salesreturndetails
        LEFT JOIN salesreturn ON salesReturnID = salesreturn.id
        INNER JOIN erp_customerinvoiceitemdetails ON salesreturndetails.deliveryOrderDetailID = erp_customerinvoiceitemdetails.deliveryOrderDetailID
        WHERE
        salesreturndetails.companySystemID = $matchDocumentMasterData->companySystemID
        AND salesreturn.approvedYN = -1
        AND salesreturndetails.deliveryOrderDetailID <> 0
        GROUP BY salesreturndetails.deliveryOrderDetailID
) sr ON sr.custInvoiceDirectAutoID = erp_accountsreceivableledger.documentCodeSystem AND erp_accountsreceivableledger.documentSystemID = 20
LEFT JOIN currencymaster ON erp_accountsreceivableledger.custTransCurrencyID = currencymaster.currencyID
WHERE
	erp_accountsreceivableledger.documentType IN (11, 12)
AND date(erp_accountsreceivableledger.documentDate) <= '{$matchingDocdate}'
AND erp_accountsreceivableledger.documentSystemID = 20
AND erp_accountsreceivableledger.selectedToPaymentInv = 0
AND erp_accountsreceivableledger.fullyInvoiced <> 2
AND erp_accountsreceivableledger.companySystemID =  $matchDocumentMasterData->companySystemID";

        if (!$segmentCheckPolicy) {
            $qry .= "\nAND erp_accountsreceivableledger.serviceLineSystemID =  $serviceLineSystemID";
        }

        $qry .= "\nAND erp_accountsreceivableledger.customerID = $matchDocumentMasterData->BPVsupplierID
AND erp_accountsreceivableledger.custTransCurrencyID = $matchDocumentMasterData->supplierTransCurrencyID
{$filter}
HAVING
	ROUND(
		balanceMemAmount,
		1
	) != 0
ORDER BY
	erp_accountsreceivableledger.arAutoID DESC";

        $invMaster = DB::select($qry);

        $col[0] = $input['order'][0]['column'];
        $col[1] = $input['order'][0]['dir'];
        $request->request->remove('order');
        $data['order'] = [];
        /*  $data['order'][0]['column'] = '';
          $data['order'][0]['dir'] = '';*/
        $data['search']['value'] = '';
        $request->merge($data);

        $request->request->remove('search.value');

        return \DataTables::of($invMaster)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function receiptVoucherMatchingCancel(Request $request)
    {
        $input = $request->all();
        $employee = Helper::getEmployeeInfo();
        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $MatchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);

        if (empty($MatchDocumentMasterData)) {
            return $this->sendError(trans('custom.match_document_master_not_found_1'));
        }

        if ($MatchDocumentMasterData->matchingConfirmedYN == 1) {
            return $this->sendError(trans('custom.you_cannot_cancel_this_matching_it_is_confirmed'));
        }


        $pvDetailExist = CustomerReceivePaymentDetail::select(DB::raw('matchingDocID'))
            ->where('matchingDocID', $matchDocumentMasterAutoID)
            ->first();

        if (!empty($pvDetailExist)) {
            return $this->sendError(trans('custom.cannot_cancel_delete_the_invoices_added_to_the_det'));
        }

        if ($MatchDocumentMasterData->matchingDocCode != '0') {

            $updateData = [
                'cancelledYN' => 1,
                'cancelledByEmpSystemID' => $employee->employeeSystemID,
                'cancelledComment' => $input['comment'],
                'cancelledDate' => date('Y-m-d H:i:s'),
                'PayMasterAutoId' => null,
                'BPVcode' => null,
                'BPVdate' => null,
                'BPVNarration' => null,
                'BPVsupplierID' => null,
                'payAmountSuppTrans' => 0,
                'matchBalanceAmount' => 0,
            ];
            
            $deleteDocument = MatchDocumentMaster::where('matchDocumentMasterAutoID', $matchDocumentMasterAutoID)
                    ->update($updateData);
        } else {
            $deleteDocument = MatchDocumentMaster::where('matchDocumentMasterAutoID', $matchDocumentMasterAutoID)
                ->delete();
        }

        if ($deleteDocument) {
            return $this->sendResponse($MatchDocumentMasterData, trans('custom.document_canceled_successfully_1'));
        } else {
            return $this->sendResponse($MatchDocumentMasterData, 'Document not canceled, try again');
        }

    }

    public function printPaymentMatching(Request $request)
    {
        $id = $request->get('matchDocumentMasterAutoID');
        $lang = $request->get('lang', 'en');

        $MatchDocumentMasterData = MatchDocumentMaster::find($id);

        if (empty($MatchDocumentMasterData)) {
            return $this->sendError(trans('custom.match_document_master_not_found'));
        }

        $matchDocumentRecord = MatchDocumentMaster::where('matchDocumentMasterAutoID', $id)->with(['created_by', 'confirmed_by', 'modified_by', 'company', 'transactioncurrency', 'supplier', 'detail' => function($query){
            $query->with(['pomaster']);
        }])->first();

        if (empty($matchDocumentRecord)) {
            return $this->sendError(trans('custom.match_document_master_not_found'));
        }
        $transDecimal = 2;
        if ($matchDocumentRecord->transactioncurrency) {
            $transDecimal = $matchDocumentRecord->transactioncurrency->DecimalPlaces;
        }

        $order = array(
            'masterdata' => $matchDocumentRecord,
            'transDecimal' => $transDecimal,
            'lang' => $lang
        );

        $time = strtotime("now");
        $fileName = 'payment_matching_' . $id . '_' . $time . '.pdf';
        
        // Check if Arabic language for RTL support
        $isRTL = ($lang === 'ar');
        
        // Configure mPDF for RTL support if Arabic
        $mpdfConfig = [
            'tempDir' => public_path('tmp'), 
            'mode' => 'utf-8', 
            'format' => 'A4', 
            'setAutoTopMargin' => 'stretch', 
            'autoMarginPadding' => -10
        ];
        
        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl';
        }
        
        $html = view('print.payment_matching', $order);
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }

    public function deleteAllRVMDetails(Request $request)
    {
        $input = $request->all();

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $MatchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);

        if (empty($MatchDocumentMasterData)) {
            return $this->sendError(trans('custom.match_document_master_not_found'));
        }

        if ($MatchDocumentMasterData->matchingConfirmedYN == 1) {
            return $this->sendError(trans('custom.you_cannot_delete_the_detail_document_already_conf'));
        }


        $detailExistAll = CustomerReceivePaymentDetail::where('matchingDocID', $matchDocumentMasterAutoID)
            ->where('companySystemID', $MatchDocumentMasterData->companySystemID )
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDetail) {

                $deleteDetails = CustomerReceivePaymentDetail::where('custRecivePayDetAutoID', $cvDetail['custRecivePayDetAutoID'])->delete();

                $updateMaster = AccountsReceivableLedger::where('arAutoID', $cvDetail['arAutoID'])
                    ->update(array('selectedToPaymentInv' => 0, 'fullyInvoiced' => 1));

            }
        }

        return $this->sendResponse($matchDocumentMasterAutoID, trans('custom.details_deleted_successfully'));
    }

    public function amendReceiptMatchingReview(Request $request)
    {
        $input = $request->all();

        $id = $input['matchDocumentMasterAutoID'];

        $employee = Helper::getEmployeeInfo();
        $emails = array();
        $masterData = MatchDocumentMaster::find($id);

        $documentName = "";


        if (empty($masterData)) {
            return $this->sendError(trans('custom.document_not_found'));
        }

        if($masterData->documentSystemID == 19  || $masterData->documentSystemID == 21){
            $documentName = 'Receipt Matching';
        }else if($masterData->documentSystemID == 4  || $masterData->documentSystemID == 15){
            $documentName = 'Payment Voucher Matching';
        }

        if ($masterData->matchingConfirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_return_back_to_amend_this').$documentName.trans('custom.document_not_confirmed'));
        }

        $isAPIDocument = DocumentSystemMapping::where('documentId',$id)->where('documentSystemID',70)->exists();
        if ($isAPIDocument){
            return $this->sendError(trans('custom.the_autogenerated_documents_cannot_be_amended'));
        }

        $matchingMasterID = $id;
        $documentAutoId = $masterData->PayMasterAutoId;
        $documentSystemID = $masterData->documentSystemID;


        $checkBalance = GeneralLedgerService::validateDebitCredit($documentSystemID, $documentAutoId);
        if (!$checkBalance['status']) {
            $allowValidateDocumentAmend = false;
        } else {
            $allowValidateDocumentAmend = true;
        }

        if($masterData->documentSystemID == 4 ){
            
            $validateCloseFinanceYear = ValidateDocumentAmend::validateCLoseFinanceYear($documentSystemID, $matchingMasterID);
            if(isset($validateCloseFinanceYear['status']) && $validateCloseFinanceYear['status'] == false){
                if(isset($validateCloseFinanceYear['message']) && $validateCloseFinanceYear['message']){
                    return $this->sendError($validateCloseFinanceYear['message']);
                }
            }
    
            $validateCloseFinancePeriod = ValidateDocumentAmend::validateCLoseFinancePeriod($documentSystemID, $matchingMasterID);
            if(isset($validateCloseFinancePeriod['status']) && $validateCloseFinancePeriod['status'] == false){
                if(isset($validateCloseFinancePeriod['message']) && $validateCloseFinancePeriod['message']){
                    return $this->sendError($validateCloseFinancePeriod['message']);
                }
            }
        }



        if($masterData->approved == -1 && $masterData->documentSystemID != 19 && $masterData->matchingOption != 1){
            if($masterData->documentSystemID == 15){
                $totalAmountPayEx = PaySupplierInvoiceDetail::selectRaw("COALESCE(SUM(supplierPaymentAmount),0) as supplierPaymentAmount, COALESCE(SUM(paymentLocalAmount),0) as paymentLocalAmount, COALESCE(SUM(paymentComRptAmount),0) as paymentComRptAmount")
                ->where('PayMasterAutoId', $masterData->PayMasterAutoId)
                ->where('documentSystemID', 15)
                ->where('companySystemID', $masterData->companySystemID)
                ->first();
                $DebitNoteMasterExData = DebitNote::find($masterData->PayMasterAutoId);

				   
				
                if (round($DebitNoteMasterExData->debitAmountTrans - $totalAmountPayEx->supplierPaymentAmount, 2) == 0) {

                    if ((round($DebitNoteMasterExData->debitAmountLocal - $totalAmountPayEx->paymentLocalAmount, 2) != 0) || (round($DebitNoteMasterExData->debitAmountRpt - $totalAmountPayEx->paymentComRptAmount, 2) != 0)) {

                        if($allowValidateDocumentAmend){
                            $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId, $documentSystemID, $matchingMasterID);
                            if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                                if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                                    return $this->sendError($validatePendingGlPost['message']);
                                }
                            }
                        }

                        $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID, $matchingMasterID);
                        if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                            if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                                return $this->sendError($validateFinanceYear['message']);
                            }
                        }
                        
                        $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID, $matchingMasterID);
                        if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                            if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                                return $this->sendError($validateFinancePeriod['message']);
                            }
                        }
                    } else {

                            $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID, $matchingMasterID);
                            if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                                if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                                    return $this->sendError($validateFinanceYear['message']);
                                }
                            }
                            
                            $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID, $matchingMasterID);
                            if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                                if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                                    return $this->sendError($validateFinancePeriod['message']);
                                }
                            }
                    }

                }  else {
                        $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID, $matchingMasterID);
                        if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                            if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                                return $this->sendError($validateFinanceYear['message']);
                            }
                        }
                        
                        $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID, $matchingMasterID);
                        if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                            if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                                return $this->sendError($validateFinancePeriod['message']);
                            }
                        }
                    }
            } else {

                if($allowValidateDocumentAmend){
                    $validatePendingGlPost = ValidateDocumentAmend::validatePendingGlPost($documentAutoId, $documentSystemID, $matchingMasterID);
                    if(isset($validatePendingGlPost['status']) && $validatePendingGlPost['status'] == false){
                        if(isset($validatePendingGlPost['message']) && $validatePendingGlPost['message']){
                            return $this->sendError($validatePendingGlPost['message']);
                        }
                    }
                }
                
                $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID, $matchingMasterID);
                if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                    if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                        return $this->sendError($validateFinanceYear['message']);
                    }
                }
                
                $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID, $matchingMasterID);
                if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                    if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                        return $this->sendError($validateFinancePeriod['message']);
                    }
                }
            }
        }

        if($masterData->documentSystemID == 19) {
            $validateFinanceYear = ValidateDocumentAmend::validateFinanceYear($documentAutoId,$documentSystemID, $matchingMasterID);
            if(isset($validateFinanceYear['status']) && $validateFinanceYear['status'] == false){
                if(isset($validateFinanceYear['message']) && $validateFinanceYear['message']){
                    return $this->sendError($validateFinanceYear['message']);
                }
            }
            
            $validateFinancePeriod = ValidateDocumentAmend::validateFinancePeriod($documentAutoId,$documentSystemID, $matchingMasterID);
            if(isset($validateFinancePeriod['status']) && $validateFinancePeriod['status'] == false){
                if(isset($validateFinancePeriod['message']) && $validateFinancePeriod['message']){
                    return $this->sendError($validateFinancePeriod['message']);
                }
            }
        }


        $emailBody = __('email.matching_document_returned_to_amend_body', [
            'documentCode' => $masterData->matchingDocCode,
            'empName' => $employee->empName,
            'returnComment' => $input['returnComment']
        ]);
        $emailSubject = __('email.matching_document_returned_to_amend', [
            'documentCode' => $masterData->matchingDocCode
        ]);

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
                    'docCode' => $masterData->matchingDocCode
                );
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            // updating fields
            $masterData->matchingConfirmedYN = 0 ;
            $masterData->matchingConfirmedByEmpSystemID = null;
            $masterData->matchingConfirmedByEmpID = null;
            $masterData->matchingConfirmedByName = null;
            $masterData->matchingConfirmedDate = null;

            if($masterData->documentSystemID == 4){
                $paySupplierInvoice = PaySupplierInvoiceMaster::find($masterData->PayMasterAutoId);
                if (!empty($paySupplierInvoice)) {
                    $paySupplierInvoice->matchInvoice = 0;
                    $paySupplierInvoice->save();
                }
            }else if($masterData->documentSystemID == 15){
                $debitNote = DebitNote::find($masterData->PayMasterAutoId);
                if (!empty($debitNote)) {
                    $debitNote->matchInvoice = 0;
                    $debitNote->save();
                }
            }
            else if($masterData->documentSystemID == 21){
                $receiveVoucher = CustomerReceivePayment::find($masterData->PayMasterAutoId);
                if (!empty($receiveVoucher)) {
                    $receiveVoucher->matchInvoice = 0;
                    $receiveVoucher->save();
                }
            }else if($masterData->documentSystemID == 19){
                $creditNoteMaster = CreditNote::find($masterData->PayMasterAutoId);
                if (!empty($creditNoteMaster)) {
                    $creditNoteMaster->matchInvoice = 0;
                    $creditNoteMaster->save();

                    $masterData->matchedAmount = null;
                    $masterData->matchLocalAmount = null;
                    $masterData->matchRptAmount = null;
                }
            }

            $masterData->save();

            if($masterData->documentSystemID == 4 || $masterData->documentSystemID == 15 || $masterData->documentSystemID == 21 || $masterData->documentSystemID == 19){
                GeneralLedger::where('documentSystemID',$masterData->documentSystemID)
                               ->where('documentSystemCode',$masterData->PayMasterAutoId)
                               ->where('documentSystemID',$masterData->documentSystemID)
                               ->where('matchDocumentMasterAutoID',$masterData->matchDocumentMasterAutoID)
                               ->delete();


                $deleteTaxLedgerData = TaxLedger::where('documentMasterAutoID', $masterData->PayMasterAutoId)
                    ->where('companySystemID', $masterData->companySystemID)
                    ->where('documentSystemID', $masterData->documentSystemID)
                    ->where('matchDocumentMasterAutoID',$masterData->matchDocumentMasterAutoID)
                    ->delete();

                TaxLedgerDetail::where('documentMasterAutoID', $masterData->PayMasterAutoId)
                    ->where('companySystemID', $masterData->companySystemID)
                    ->where('documentSystemID', $masterData->documentSystemID)
                    ->where('matchDocumentMasterAutoID',$masterData->matchDocumentMasterAutoID)
                    ->delete();
            }

            AuditTrial::insertAuditTrial('MatchDocumentMaster',$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), $documentName.' Document amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}

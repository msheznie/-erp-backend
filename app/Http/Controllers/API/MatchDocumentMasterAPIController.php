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
 * -- Date: 02-October 2018 By: Nazir Description: Added new functions named as PaymentVoucherMatchingCancel()
 * -- Date: 16-October 2018 By: Nazir Description: Added new functions named as getRVMatchDocumentMasterView()
 * -- Date: 22-October 2018 By: Nazir Description: Added new functions named as getReceiptVoucherPullingDetail()
 * -- Date: 25-October 2018 By: Nazir Description: Added new functions named as receiptVoucherMatchingCancel()
 * -- Date: 25-October 2018 By: Nazir Description: Added new functions named as updateReceiptVoucherMatching()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMatchDocumentMasterAPIRequest;
use App\Http\Requests\API\UpdateMatchDocumentMasterAPIRequest;
use App\Models\CreditNote;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SupplierAssigned;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Company;
use App\Repositories\MatchDocumentMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;

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

        return $this->sendResponse($matchDocumentMasters->toArray(), 'Match Document Masters retrieved successfully');
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

            if (!isset($input['paymentAutoID'])) {
                return $this->sendError('Please select a payment voucher !', 500);
            }

            $validator = \Validator::make($request->all(), [
                'companySystemID' => 'required',
                'matchType' => 'required',
                'paymentAutoID' => 'required',
                'supplierID' => 'required',
                'tempType' => 'required'
            ]);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            if ($input['matchType'] == 1) {

                $paySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($input['paymentAutoID']);
                if (empty($paySupplierInvoiceMaster)) {
                    return $this->sendError('Pay Supplier Invoice Master not found');
                }

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $paySupplierInvoiceMaster->documentSystemID)->where('companySystemID', $paySupplierInvoiceMaster->companySystemID)->where('documentSystemCode', $input['paymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if ($glCheck->SumOfdocumentLocalAmount != 0 || $glCheck->SumOfdocumentRptAmount != 0) {
                        return $this->sendError('Selected payment voucher is not updated in general ledger. Please check again');
                    }
                } else {
                    return $this->sendError('Selected payment voucher is not updated in general ledger. Please check again');
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
                if (empty($debitNoteMaster)) {
                    return $this->sendError('Debit Note not found');
                }
                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $debitNoteMaster->documentSystemID)->where('companySystemID', $debitNoteMaster->companySystemID)->where('documentSystemCode', $input['paymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if ($glCheck->SumOfdocumentLocalAmount != 0 || $glCheck->SumOfdocumentRptAmount != 0) {
                        return $this->sendError('Selected debit note is not updated in general ledger. Please check again');
                    }
                } else {
                    return $this->sendError('Selected debit note is not updated in general ledger. Please check again');
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
                $input['BPVsupplierID'] = $debitNoteMaster->supplierID;
                $input['supplierGLCodeSystemID'] = $debitNoteMaster->supplierGLCodeSystemID;
                $input['supplierGLCode'] = $debitNoteMaster->supplierGLCode;
                $input['supplierTransCurrencyID'] = $debitNoteMaster->supplierTransactionCurrencyID;
                $input['supplierTransCurrencyER'] = $debitNoteMaster->supplierTransactionCurrencyER;
                $input['supplierDefCurrencyID'] = $debitNoteMaster->supplierTransactionCurrencyID;
                $input['supplierDefCurrencyER'] = $debitNoteMaster->supplierTransactionCurrencyER;
                $input['localCurrencyID'] = $debitNoteMaster->localCurrencyID;
                $input['localCurrencyER'] = $debitNoteMaster->localCurrencyER;
                $input['companyRptCurrencyID'] = $debitNoteMaster->companyRptCurrencyID;
                $input['companyRptCurrencyER'] = $debitNoteMaster->companyRptCurrencyER;
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
        } elseif ($input['tempType'] == 'RVM') {

            if (!isset($input['custReceivePaymentAutoID'])) {
                return $this->sendError('Please select a receipt voucher !', 500);
            }

            $validator = \Validator::make($request->all(), [
                'companySystemID' => 'required',
                'matchType' => 'required',
                'custReceivePaymentAutoID' => 'required',
                'customerID' => 'required',
                'tempType' => 'required'
            ]);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            if ($input['matchType'] == 1) {

                $customerReceivePaymentMaster = CustomerReceivePayment::find($input['custReceivePaymentAutoID']);
                if (empty($customerReceivePaymentMaster)) {
                    return $this->sendError('Customer Receive Payment not found');
                }

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $customerReceivePaymentMaster->documentSystemID)->where('companySystemID', $customerReceivePaymentMaster->companySystemID)->where('documentSystemCode', $input['custReceivePaymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if ($glCheck->SumOfdocumentLocalAmount != 0 || $glCheck->SumOfdocumentRptAmount != 0) {
                        return $this->sendError('Selected customer receive payment is not updated in general ledger. Please check again');
                    }
                } else {
                    return $this->sendError('Selected customer receive payment is not updated in general ledger. Please check again');
                }

                $input['matchingType'] = 'AR';
                $input['PayMasterAutoId'] = $input['custReceivePaymentAutoID'];
                $input['documentSystemID'] = $customerReceivePaymentMaster->documentSystemID;
                $input['documentID'] = $customerReceivePaymentMaster->documentID;
                $input['BPVcode'] = $customerReceivePaymentMaster->custPaymentReceiveCode;
                $input['BPVdate'] = $customerReceivePaymentMaster->custPaymentReceiveDate;
                $input['BPVNarration'] = $customerReceivePaymentMaster->narration;
                $input['directPaymentPayeeSelectEmp'] = $customerReceivePaymentMaster->PayeeSelectEmp;
                $input['directPaymentPayee'] = $customerReceivePaymentMaster->PayeeName;
                $input['directPayeeCurrency'] = $customerReceivePaymentMaster->PayeeCurrency;
                $input['BPVsupplierID'] = $customerReceivePaymentMaster->customerID;
                $input['supplierGLCodeSystemID'] = $customerReceivePaymentMaster->customerGLCodeSystemID;
                $input['supplierGLCode'] = $customerReceivePaymentMaster->customerGLCode;
                $input['supplierTransCurrencyID'] = $customerReceivePaymentMaster->custTransactionCurrencyID;
                $input['supplierTransCurrencyER'] = $customerReceivePaymentMaster->custTransactionCurrencyER;
           /*   $input['supplierDefCurrencyID'] = $customerReceivePaymentMaster->supplierDefCurrencyID;
                $input['supplierDefCurrencyER'] = $customerReceivePaymentMaster->supplierDefCurrencyER;*/
                $input['localCurrencyID'] = $customerReceivePaymentMaster->localCurrencyID;
                $input['localCurrencyER'] = $customerReceivePaymentMaster->localCurrencyER;
                $input['companyRptCurrencyID'] = $customerReceivePaymentMaster->companyRptCurrencyID;
                $input['companyRptCurrencyER'] = $customerReceivePaymentMaster->companyRptCurrencyER;
                $input['payAmountBank'] = $customerReceivePaymentMaster->bankID;
                //$input['payAmountSuppTrans'] = $customerReceivePaymentMaster->custTransactionCurrencyID;
                //$input['payAmountSuppDef'] = $customerReceivePaymentMaster->payAmountSuppDef;
                //$input['suppAmountDocTotal'] = $customerReceivePaymentMaster->suppAmountDocTotal;
                //$input['payAmountCompLocal'] = $customerReceivePaymentMaster->payAmountCompLocal;
                //$input['payAmountCompRpt'] = $customerReceivePaymentMaster->payAmountCompRpt;
                $input['invoiceType'] = $customerReceivePaymentMaster->documentType;
                $input['matchInvoice'] = $customerReceivePaymentMaster->matchInvoice;
                $input['matchingAmount'] = 0;

                $input['confirmedYN'] = $customerReceivePaymentMaster->confirmedYN;
                $input['confirmedByEmpID'] = $customerReceivePaymentMaster->confirmedByEmpID;
                $input['confirmedByEmpSystemID'] = $customerReceivePaymentMaster->confirmedByEmpSystemID;
                $input['confirmedByName'] = $customerReceivePaymentMaster->confirmedByName;
                $input['confirmedDate'] = $customerReceivePaymentMaster->confirmedDate;
                $input['approved'] = $customerReceivePaymentMaster->approved;
                $input['approvedDate'] = $customerReceivePaymentMaster->approvedDate;
            }else if ($input['matchType'] == 2) {
                $creditNoteMaster = CreditNote::find($input['custReceivePaymentAutoID']);
                if (empty($creditNoteMaster)) {
                    return $this->sendError('Credit Note not found');
                }
                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $creditNoteMaster->documentSystemiD)->where('companySystemID', $creditNoteMaster->companySystemID)->where('documentSystemCode', $input['custReceivePaymentAutoID'])->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();

                if ($glCheck) {
                    if ($glCheck->SumOfdocumentLocalAmount != 0 || $glCheck->SumOfdocumentRptAmount != 0) {
                        return $this->sendError('Selected credit note is not updated in general ledger. Please check again', 500);
                    }
                } else {
                    return $this->sendError('Selected credit note is not updated in general ledger. Please check again', 500);
                }
                $input['matchingType'] = 'AR';
                $input['PayMasterAutoId'] = $input['custReceivePaymentAutoID'];
                $input['documentSystemID'] = $creditNoteMaster->documentSystemiD;
                $input['documentID'] = $creditNoteMaster->documentID;
                $input['BPVcode'] = $creditNoteMaster->creditNoteCode;
                $input['BPVdate'] = $creditNoteMaster->creditNoteDate;
                $input['BPVNarration'] = $creditNoteMaster->comments;
                //$input['directPaymentPayeeSelectEmp'] = $creditNoteMaster->directPaymentPayeeSelectEmp;
                //$input['directPaymentPayee'] = $creditNoteMaster->directPaymentPayee;
                //$input['directPayeeCurrency'] = $creditNoteMaster->supplierTransactionCurrencyID;
                $input['BPVsupplierID'] = $creditNoteMaster->customerID;
                $input['supplierGLCodeSystemID'] = $creditNoteMaster->customerGLCodeSystemID;
                $input['supplierGLCode'] = $creditNoteMaster->customerGLCode;
                $input['supplierTransCurrencyID'] = $creditNoteMaster->customerCurrencyID;
                $input['supplierTransCurrencyER'] = $creditNoteMaster->customerCurrencyER;
                //$input['supplierDefCurrencyID'] = $creditNoteMaster->supplierTransactionCurrencyID;
                //$input['supplierDefCurrencyER'] = $creditNoteMaster->supplierTransactionCurrencyER;
                $input['localCurrencyID'] = $creditNoteMaster->localCurrencyID;
                $input['localCurrencyER'] = $creditNoteMaster->localCurrencyER;
                $input['companyRptCurrencyID'] = $creditNoteMaster->companyRptCurrencyID;
                $input['companyRptCurrencyER'] = $creditNoteMaster->companyRptCurrencyER;
                //$input['payAmountBank'] = $creditNoteMaster->payAmountBank;
                $input['payAmountSuppTrans'] = $creditNoteMaster->creditAmountTrans;
                //$input['payAmountSuppDef'] = $creditNoteMaster->debitAmountTrans;
                //$input['suppAmountDocTotal'] = $creditNoteMaster->suppAmountDocTotal;
                $input['payAmountCompLocal'] = $creditNoteMaster->creditAmountLocal;
                $input['payAmountCompRpt'] = $creditNoteMaster->creditAmountRpt;
                $input['invoiceType'] = $creditNoteMaster->documentType;
                $input['matchingAmount'] = 0;
                $input['confirmedYN'] = $creditNoteMaster->confirmedYN;
                $input['confirmedByEmpID'] = $creditNoteMaster->confirmedByEmpID;
                $input['confirmedByEmpSystemID'] = $creditNoteMaster->confirmedByEmpSystemID;
                $input['confirmedByName'] = $creditNoteMaster->confirmedByName;
                $input['confirmedDate'] = $creditNoteMaster->confirmedDate;
                $input['approved'] = $creditNoteMaster->approved;
                $input['approvedDate'] = $creditNoteMaster->approvedDate;
            }
        }

        $input['matchingDocCode'] = 0;
        $input['matchingDocdate'] = date('Y-m-d H:i:s');

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $matchDocumentMasters = $this->matchDocumentMasterRepository->create($input);

        return $this->sendResponse($matchDocumentMasters->toArray(), 'Match Document Master saved successfully');
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
        $matchDocumentMaster = $this->matchDocumentMasterRepository->with(['created_by', 'confirmed_by', 'company', 'modified_by'])->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        return $this->sendResponse($matchDocumentMaster->toArray(), 'Match Document Master retrieved successfully');
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
        $input = $request->all();
        $input = array_except($input, ['created_by', 'BPVsupplierID', 'company', 'confirmed_by', 'modified_by']);
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        if (isset($input['matchingDocdate'])) {
            if ($input['matchingDocdate']) {
                $input['matchingDocdate'] = new Carbon($input['matchingDocdate']);
            }
        }

        if ($input['matchingDocCode'] == 0) {

            $company = Company::find($input['companySystemID']);

            $lastSerial = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                ->where('matchDocumentMasterAutoID', '<>', $input['matchDocumentMasterAutoID'])
                ->orderBy('matchDocumentMasterAutoID', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $matchingDocCode = ($company->CompanyID . '\\' . 'MT' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

            $input['serialNo'] = $lastSerialNumber;
            $input['matchingDocCode'] = $matchingDocCode;
        }


        if ($matchDocumentMaster->matchingConfirmedYN == 0 && $input['matchingConfirmedYN'] == 1) {

            $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('matchingDocID'))
                ->where('matchingDocID', $id)
                ->first();

            if (empty($pvDetailExist)) {
                return $this->sendError('PV Matching document cannot confirm without details', 500, ['type' => 'confirm']);
            }

            $checkAmount = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                ->where('supplierPaymentAmount', '<=', 0)
                ->count();

            if ($checkAmount > 0) {
                return $this->sendError('Matching amount cannot be 0', 500, ['type' => 'confirm']);
            }

            $detailAmountTotTran = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                ->sum('supplierPaymentAmount');

            $detailAmountTotLoc = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                ->sum('paymentLocalAmount');

            $detailAmountTotRpt = PaySupplierInvoiceDetail::where('matchingDocID', $id)
                ->sum('paymentComRptAmount');


            if ($detailAmountTotTran > $input['matchBalanceAmount']) {
                return $this->sendError('Detail amount cannot be greater than balance amount to match', 500, ['type' => 'confirm']);
            }
            //$currency = \Helper::convertAmountToLocalRpt(203, $id, $detailAmountTot);

            $input['matchingAmount'] = $detailAmountTotTran;
            $input['matchedAmount'] = $detailAmountTotTran;
            //$input['matchLocalAmount'] = \Helper::roundValue($currency['localAmount']);
            //$input['matchRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

            $input['matchLocalAmount'] = \Helper::roundValue($detailAmountTotLoc);
            $input['matchRptAmount'] = \Helper::roundValue($detailAmountTotRpt);

            $input['matchingConfirmedYN'] = 1;
            $input['matchingConfirmedByEmpSystemID'] = $employee->employeeSystemID;;
            $input['matchingConfirmedByEmpID'] = $employee->empID;
            $input['matchingConfirmedByName'] = $employee->empName;
            $input['matchingConfirmedDate'] = \Helper::currentDateTime();
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $matchDocumentMaster = $this->matchDocumentMasterRepository->update($input, $id);

        return $this->sendResponse($matchDocumentMaster->toArray(), 'Record updated successfully');
    }


    public function updateReceiptVoucherMatching(Request $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'BPVsupplierID', 'company', 'confirmed_by', 'modified_by']);
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $id =  $input['matchDocumentMasterAutoID'];

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        if (isset($input['matchingDocdate'])) {
            if ($input['matchingDocdate']) {
                $input['matchingDocdate'] = new Carbon($input['matchingDocdate']);
            }
        }

        if ($input['matchingDocCode'] == 0) {

            $company = Company::find($input['companySystemID']);

            $lastSerial = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                ->where('matchDocumentMasterAutoID', '<>', $input['matchDocumentMasterAutoID'])
                ->orderBy('matchDocumentMasterAutoID', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            $matchingDocCode = ($company->CompanyID . '\\' . 'MT' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

            $input['serialNo'] = $lastSerialNumber;
            $input['matchingDocCode'] = $matchingDocCode;
        }

        if ($matchDocumentMaster->matchingConfirmedYN == 0 && $input['matchingConfirmedYN'] == 1) {

            $pvDetailExist = CustomerReceivePaymentDetail::select(DB::raw('matchingDocID'))
                ->where('matchingDocID', $id)
                ->first();

            if (empty($pvDetailExist)) {
                return $this->sendError('Matching document cannot confirm without details', 500, ['type' => 'confirm']);
            }

            $checkAmount = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                ->where('receiveAmountTrans', '<=', 0)
                ->count();

            if ($checkAmount > 0) {
                return $this->sendError('Matching amount cannot be 0', 500, ['type' => 'confirm']);
            }

            $detailAmountTotTran = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                ->sum('receiveAmountTrans');

            $detailAmountTotLoc = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                ->sum('receiveAmountLocal');

            $detailAmountTotRpt = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                ->sum('receiveAmountRpt');


            if ($detailAmountTotTran > $input['matchBalanceAmount']) {
                return $this->sendError('Detail amount cannot be greater than balance amount to match', 500, ['type' => 'confirm']);
            }
            //$currency = \Helper::convertAmountToLocalRpt(203, $id, $detailAmountTot);

            $input['matchingAmount'] = $detailAmountTotTran;
            $input['matchedAmount'] = $detailAmountTotTran;
            //$input['matchLocalAmount'] = \Helper::roundValue($currency['localAmount']);
            //$input['matchRptAmount'] = \Helper::roundValue($currency['reportingAmount']);

            $input['matchLocalAmount'] = \Helper::roundValue($detailAmountTotLoc);
            $input['matchRptAmount'] = \Helper::roundValue($detailAmountTotRpt);

            $input['matchingConfirmedYN'] = 1;
            $input['matchingConfirmedByEmpSystemID'] = $employee->employeeSystemID;;
            $input['matchingConfirmedByEmpID'] = $employee->empID;
            $input['matchingConfirmedByName'] = $employee->empName;
            $input['matchingConfirmedDate'] = \Helper::currentDateTime();
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $matchDocumentMaster = $this->matchDocumentMasterRepository->update($input, $id);

        return $this->sendResponse($matchDocumentMaster->toArray(), 'Receipt voucher matching updated successfully');
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
            return $this->sendError('Match Document Master not found');
        }

        $matchDocumentMaster->delete();

        return $this->sendResponse($id, 'Match Document Master deleted successfully');
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

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $customer = CustomerAssigned::select('*')->where('companySystemID', $companyId)->where('isAssigned', '-1')->where('isActive', '1')->get();

        $output = array('yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'suppliers' => $supplier,
            'customer' => $customer
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
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

        $invMaster = MatchDocumentMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->whereIn('documentSystemID', [4, 15]);
        $invMaster->with(['created_by' => function ($query) {
        }, 'supplier' => function ($query) {
        }, 'transactioncurrency' => function ($query) {
        }]);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('matchingConfirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('matchingDocdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('matchingDocdate', '=', $input['year']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $invMaster->where('BPVsupplierID', $input['supplierID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('matchingDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('supplierName', 'like', "%{$search}%");
                    });
            });
        }

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
            return $this->sendError('Matching document not found');
        }

        $BPVdate = Carbon::parse($matchDocumentMasterData->BPVdate)->format('Y-m-d');

        $output = DB::select('SELECT
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
	SUM(erp_matchdocumentmaster.matchRptAmount) as matchRptAmount,
	erp_matchdocumentmaster.matchingConfirmedYN
FROM
	erp_matchdocumentmaster
WHERE
	erp_matchdocumentmaster.companySystemID = ' . $matchDocumentMasterData->companySystemID . '
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
	AND DATE_FORMAT(erp_accountspayableledger.documentDate,"%Y-%m-%d") <= "' . $BPVdate . '"
	AND erp_accountspayableledger.selectedToPaymentInv = 0
	AND erp_accountspayableledger.fullyInvoice <> 2
	AND erp_accountspayableledger.companySystemID = ' . $matchDocumentMasterData->companySystemID . '
	AND erp_accountspayableledger.supplierCodeSystem = ' . $matchDocumentMasterData->BPVsupplierID . '
	AND erp_accountspayableledger.supplierTransCurrencyID = ' . $matchDocumentMasterData->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount,2) != 0 ORDER BY erp_accountspayableledger.apAutoID DESC');

        return $this->sendResponse($output, 'Data retrived successfully');
    }

    public function getMatchDocumentMasterRecord(Request $request)
    {
        $id = $request->get('matchDocumentMasterAutoID');

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = $this->matchDocumentMasterRepository->with(['created_by', 'confirmed_by', 'modified_by'])->findWithoutFail($id);

        if (empty($matchDocumentMaster)) {
            return $this->sendError('Match Document Master not found');
        }

        return $this->sendResponse($matchDocumentMaster, 'Data retrieved successfully');
    }

    public function PaymentVoucherMatchingCancel(Request $request)
    {
        $input = $request->all();

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $MatchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);

        if (empty($MatchDocumentMasterData)) {
            return $this->sendError('Match Document Master not found');
        }

        if ($MatchDocumentMasterData->matchingConfirmedYN == 1) {
            return $this->sendError('You cannot cancel this matching, it is confirmed');
        }

        $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('matchingDocID'))
            ->where('matchingDocID', $matchDocumentMasterAutoID)
            ->first();

        if (!empty($pvDetailExist)) {
            return $this->sendError('Details are exist, You cannot cancel this document ');
        }

        $deleteDocument = MatchDocumentMaster::where('matchDocumentMasterAutoID', $matchDocumentMasterAutoID)
            ->delete();

        if ($deleteDocument) {
            return $this->sendResponse($MatchDocumentMasterData, 'Document canceled successfully ');
        } else {
            return $this->sendResponse($MatchDocumentMasterData, 'Document not canceled');
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

        $invMaster = MatchDocumentMaster::where('companySystemID', $input['companySystemID']);
        $invMaster->whereIn('documentSystemID', [19, 21]);
        $invMaster->with(['created_by' => function ($query) {
        }, 'customer' => function ($query) {
        }, 'transactioncurrency' => function ($query) {
        }]);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('matchingConfirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('matchingDocdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('matchingDocdate', '=', $input['year']);
            }
        }

        if (array_key_exists('customerID', $input)) {
            if ($input['customerID'] && !is_null($input['customerID'])) {
                $invMaster->where('BPVsupplierID', $input['customerID']);
            }
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('matchingDocCode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('CustomerName', 'like', "%{$search}%");
                    });
            });
        }

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
            return $this->sendError('Please select a match type');
        }

        if ($input['matchType'] == 1) {
            $invoiceMaster = DB::select('SELECT
	erp_custreceivepaymentdet.custReceivePaymentAutoID,
	erp_customerreceivepayment.documentID,
	erp_custreceivepaymentdet.companyID,
	erp_customerreceivepayment.custPaymentReceiveCode as docMatchedCode,
	erp_customerreceivepayment.custPaymentReceiveDate as docMatchedDate,
	erp_custreceivepaymentdet.bookingInvCodeSystem,
	erp_custreceivepaymentdet.bookingInvCode,
	erp_customerreceivepayment.customerID,
	erp_customerreceivepayment.customerGLCode,
	erp_customerreceivepayment.custTransactionCurrencyID,
	erp_customerreceivepayment.custTransactionCurrencyER,
	erp_customerreceivepayment.localCurrencyID,
	erp_customerreceivepayment.localCurrencyER,
	erp_customerreceivepayment.companyRptCurrencyID,
	erp_customerreceivepayment.companyRptCurrencyER,
	Sum(
		erp_custreceivepaymentdet.receiveAmountTrans
	) AS SumOfreceiveAmountTrans,
	Sum(
		erp_custreceivepaymentdet.receiveAmountLocal
	) AS SumOfreceiveAmountLocal,
	Sum(
		erp_custreceivepaymentdet.receiveAmountRpt
	) AS SumOfreceiveAmountRpt,
	erp_customerreceivepayment.confirmedYN,
	erp_customerreceivepayment.confirmedByEmpID,
	erp_customerreceivepayment.confirmedByName,
	erp_customerreceivepayment.confirmedDate,
	erp_customerreceivepayment.matchInvoice,
	erp_customerreceivepayment.documentType,
	erp_customerreceivepayment.approved,
	erp_customerreceivepayment.approvedDate,
	IFNULL(advd.SumOfmatchingAmount, 0) AS SumOfmatchingAmount,
	(
		erp_custreceivepaymentdet.receiveAmountTrans - IFNULL(advd.SumOfmatchingAmount, 0)
	) AS BalanceAmt,
		currency.CurrencyCode,
	currency.DecimalPlaces
FROM
	erp_custreceivepaymentdet
INNER JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
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
		erp_matchdocumentmaster.documentID,
		erp_matchdocumentmaster.companyID,
		erp_matchdocumentmaster.BPVcode
) AS advd ON (
	erp_custreceivepaymentdet.custReceivePaymentAutoID = advd.PayMasterAutoId
	AND erp_custreceivepaymentdet.addedDocumentSystemID = advd.documentSystemID
	AND erp_custreceivepaymentdet.companySystemID = advd.companySystemID
)
WHERE
	erp_custreceivepaymentdet.companySystemID = ' . $input['companySystemID'] . '
AND erp_custreceivepaymentdet.bookingInvCode = 0
AND erp_customerreceivepayment.approved = -1
AND customerID = ' . $input['BPVsupplierID'] . '
GROUP BY
	erp_custreceivepaymentdet.custReceivePaymentAutoID,
	erp_customerreceivepayment.documentID,
	erp_custreceivepaymentdet.companyID,
	erp_customerreceivepayment.custPaymentReceiveCode,
	erp_customerreceivepayment.custPaymentReceiveDate,
	erp_customerreceivepayment.customerID,
	erp_customerreceivepayment.customerGLCode,
	erp_customerreceivepayment.custTransactionCurrencyID,
	erp_customerreceivepayment.custTransactionCurrencyER,
	erp_customerreceivepayment.localCurrencyID,
	erp_customerreceivepayment.localCurrencyER,
	erp_customerreceivepayment.companyRptCurrencyID,
	erp_customerreceivepayment.companyRptCurrencyER,
	erp_customerreceivepayment.confirmedYN,
	erp_customerreceivepayment.confirmedByEmpID,
	erp_customerreceivepayment.confirmedByName,
	erp_customerreceivepayment.confirmedDate,
	erp_customerreceivepayment.matchInvoice,
	erp_customerreceivepayment.documentType,
	erp_customerreceivepayment.approved,
	erp_customerreceivepayment.approvedDate
HAVING
	(
		ROUND(
			BalanceAmt,
			currency.DecimalPlaces
		) > 0
	)');
        } elseif ($input['matchType'] == 2) {
            $invoiceMaster = DB::select('SELECT
	erp_custreceivepaymentdet.custReceivePaymentAutoID,
	erp_creditnote.documentID,
	erp_custreceivepaymentdet.companyID,
	erp_creditnote.creditNoteCode as docMatchedCode,
	erp_creditnote.creditNoteDate as docMatchedDate,
	erp_custreceivepaymentdet.bookingInvCodeSystem,
	erp_custreceivepaymentdet.bookingInvCode,
	erp_creditnote.customerID,
	erp_creditnote.customerGLCode,
	erp_creditnote.customerCurrencyID,
	erp_creditnote.customerCurrencyER,
	erp_creditnote.localCurrencyID,
	erp_creditnote.localCurrencyER,
	erp_creditnote.companyReportingCurrencyID,
	erp_creditnote.companyReportingER,
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
	(
		erp_custreceivepaymentdet.receiveAmountTrans - IFNULL(advd.SumOfmatchingAmount, 0)
	) AS BalanceAmt,
		currency.CurrencyCode,
	currency.DecimalPlaces
FROM
	erp_custreceivepaymentdet
INNER JOIN erp_creditnote ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_creditnote.creditNoteAutoID
INNER JOIN currencymaster AS currency ON currency.currencyID = erp_creditnote.customerCurrencyID
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
		erp_matchdocumentmaster.documentID,
		erp_matchdocumentmaster.companyID,
		erp_matchdocumentmaster.BPVcode
) AS advd ON (
	erp_custreceivepaymentdet.custReceivePaymentAutoID = advd.PayMasterAutoId
	AND erp_custreceivepaymentdet.addedDocumentSystemID = advd.documentSystemID
	AND erp_custreceivepaymentdet.companySystemID = advd.companySystemID
)
WHERE
	erp_custreceivepaymentdet.companySystemID = ' . $input['companySystemID'] . '
AND erp_custreceivepaymentdet.bookingInvCode = 0
AND erp_creditnote.approved = -1
AND erp_creditnote.matchInvoice <> 2
AND customerID = ' . $input['BPVsupplierID'] . '
GROUP BY
	erp_custreceivepaymentdet.custReceivePaymentAutoID,
	erp_creditnote.documentID,
	erp_custreceivepaymentdet.companyID,
	erp_creditnote.creditNoteCode
HAVING
	(
		ROUND(
			BalanceAmt,
			currency.DecimalPlaces
		) > 0
	)');
        }

        return $this->sendResponse($invoiceMaster, 'Data retrived successfully');
    }

    public function getReceiptVoucherPullingDetail(Request $request)
    {
        $input = $request->all();

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $matchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);
        if (empty($matchDocumentMasterData)) {
            return $this->sendError('Matching document not found');
        }

        $output = DB::select('SELECT
	erp_accountsreceivableledger.arAutoID,
	erp_accountsreceivableledger.documentCodeSystem AS bookingInvCodeSystem,
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
	erp_accountsreceivableledger.customerID,
	CurrencyCode,
	DecimalPlaces,
	IFNULL(custInvoiceAmount, 0) AS custInvoiceAmount,
	Round((IFNULL(custInvoiceAmount, 0) - IFNULL(sid.SumOfreceiveAmountTrans, 0) - (IFNULL(md.matchedAmount, 0)) * -1),3) as balanceMemAmount,
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
		erp_matchdocumentmaster.companySystemID = ' . $matchDocumentMasterData->companySystemID . '
	AND erp_matchdocumentmaster.documentSystemID = ' . $matchDocumentMasterData->documentSystemID . '
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
LEFT JOIN currencymaster ON erp_accountsreceivableledger.custTransCurrencyID = currencymaster.currencyID
WHERE
	erp_accountsreceivableledger.documentType IN (11, 12)
AND date(erp_accountsreceivableledger.documentDate) <= "' . $matchDocumentMasterData->BPVdate . '"
AND erp_accountsreceivableledger.selectedToPaymentInv = 0
AND erp_accountsreceivableledger.fullyInvoiced <> 2
AND erp_accountsreceivableledger.companySystemID = ' . $matchDocumentMasterData->companySystemID . '
AND erp_accountsreceivableledger.customerID = ' . $matchDocumentMasterData->BPVsupplierID . '
AND erp_accountsreceivableledger.custTransCurrencyID = ' . $matchDocumentMasterData->supplierTransCurrencyID . '
HAVING
	ROUND(
		balanceMemAmount,
		DecimalPlaces
	) != 0
ORDER BY
	erp_accountsreceivableledger.arAutoID DESC');

        return $this->sendResponse($output, 'Data retrived successfully');
    }


    public function receiptVoucherMatchingCancel(Request $request)
    {
        $input = $request->all();

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $MatchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);

        if (empty($MatchDocumentMasterData)) {
            return $this->sendError('Match Document Master not found');
        }

        if ($MatchDocumentMasterData->matchingConfirmedYN == 1) {
            return $this->sendError('You cannot cancel this matching, it is confirmed');
        }

        $pvDetailExist = CustomerReceivePaymentDetail::select(DB::raw('matchingDocID'))
            ->where('matchingDocID', $matchDocumentMasterAutoID)
            ->first();

        if (!empty($pvDetailExist)) {
            return $this->sendError('Details are exist, You cannot cancel this document ');
        }

        $deleteDocument = MatchDocumentMaster::where('matchDocumentMasterAutoID', $matchDocumentMasterAutoID)
            ->delete();

        if ($deleteDocument) {
            return $this->sendResponse($MatchDocumentMasterData, 'Document canceled successfully ');
        } else {
            return $this->sendResponse($MatchDocumentMasterData, 'Document not canceled');
        }

    }

}

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
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceMasterAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceMasterAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Models\AdvancePaymentDetails;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\DirectPaymentDetails;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\EmployeesDepartment;
use App\Models\ExpenseClaimType;
use App\Models\MatchDocumentMaster;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\SegmentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceMasterController
 * @package App\Http\Controllers\API
 */
class PaySupplierInvoiceMasterAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterRepository */
    private $paySupplierInvoiceMasterRepository;

    public function __construct(PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo)
    {
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
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

            $validator = \Validator::make($request->all(), [
                'invoiceType' => 'required',
                'supplierTransCurrencyID' => 'required',
                'BPVNarration' => 'required',
                'BPVbank' => 'required',
                'BPVAccount' => 'required',
                'BPVdate' => 'required|date',
                'BPVchequeDate' => 'required|date',
            ]);

            if ($validator->fails()) {//echo 'in';exit;
                return $this->sendError($validator->messages(), 422);
            }

            $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
            if (!$companyFinanceYear["success"]) {
                return $this->sendError($companyFinanceYear["message"], 500);
            } else {
                $input['FYBiggin'] = $companyFinanceYear["message"]->bigginingDate;
                $input['FYEnd'] = $companyFinanceYear["message"]->endingDate;
            }

            $inputParam = $input;
            $inputParam["departmentSystemID"] = 1;
            $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
            if (!$companyFinancePeriod["success"]) {
                return $this->sendError($companyFinancePeriod["message"], 500);
            } else {
                $input['FYPeriodDateFrom'] = $companyFinancePeriod["message"]->dateFrom;
                $input['FYPeriodDateTo'] = $companyFinancePeriod["message"]->dateTo;
            }

            unset($inputParam);

            $input['BPVdate'] = new Carbon($input['BPVdate']);
            $input['BPVchequeDate'] = new Carbon($input['BPVchequeDate']);

            $monthBegin = $input['FYPeriodDateFrom'];
            $monthEnd = $input['FYPeriodDateTo'];

            if (($input['BPVdate'] >= $monthBegin) && ($input['BPVdate'] <= $monthEnd)) {
            } else {
                return $this->sendError('Payment voucher date is not within financial period!', 500);
            }

            $company = Company::find($input['companySystemID']);
            if ($company) {
                $input['companyID'] = $company->CompanyID;
            }

            $documentMaster = DocumentMaster::find($input['documentSystemID']);
            if ($documentMaster) {
                $input['documentID'] = $documentMaster->documentID;
            }

            $lastSerial = PaySupplierInvoiceMaster::where('companySystemID', $input['companySystemID'])
                ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                ->orderBy('serialNo', 'desc')
                ->first();

            $lastSerialNumber = 1;
            if ($lastSerial) {
                $lastSerialNumber = intval($lastSerial->serialNo) + 1;
            }

            if ($companyFinanceYear["message"]) {
                $startYear = $companyFinanceYear["message"]['bigginingDate'];
                $finYearExp = explode('-', $startYear);
                $finYear = $finYearExp[0];
            } else {
                $finYear = date("Y");
            }
            if ($documentMaster) {
                $documentCode = ($company->CompanyID . '\\' . $finYear . '\\' . $documentMaster->documentID . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
                $input['BPVcode'] = $documentCode;
            }
            $input['serialNo'] = $lastSerialNumber;

            if (isset($input['BPVsupplierID']) && !empty($input['BPVsupplierID'])) {
                $supDetail = SupplierAssigned::where('supplierCodeSytem', $input['BPVsupplierID'])->where('companySystemID', $input['companySystemID'])->first();

                $supCurrency = SupplierCurrency::where('supplierCodeSystem', $input['BPVsupplierID'])->where('isAssigned', -1)->where('isDefault', -1)->first();

                if ($supDetail) {
                    $input['supplierGLCode'] = $supDetail->liabilityAccount;
                    $input['supplierGLCodeSystemID'] = $supDetail->liabilityAccountSysemID;
                }
                $input['supplierTransCurrencyER'] = 1;
                if ($supCurrency) {
                    $input['supplierDefCurrencyID'] = $supCurrency->currencyID;
                    $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransCurrencyID'], $supCurrency->currencyID, 0);
                    if ($currencyConversionDefaultMaster) {
                        $input['supplierDefCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                    }
                }
                $supplier = SupplierMaster::find($input['BPVsupplierID']);
                $input['directPaymentPayee'] = $supplier->supplierName;
            }

            $bankAccount = BankAccount::find($input['BPVAccount']);
            if ($bankAccount) {
                $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;
                $currencyConversionDefaultMaster = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransCurrencyID'], $bankAccount->accountCurrencyID, 0);
                if ($currencyConversionDefaultMaster) {
                    $input['BPVbankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                }
            }

            $companyCurrency = \Helper::companyCurrency($input['companySystemID']);
            if ($companyCurrency) {
                $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;
                $input['companyRptCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransCurrencyID'], $input['supplierTransCurrencyID'], 0);
                if ($companyCurrencyConversion) {
                    $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                    $input['companyRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
                }
            }

            if ($input['invoiceType'] == 3) {
                if ($input['payeeType'] == 3) {
                    $input['directPaymentpayeeYN'] = -1;
                }
                if ($input['payeeType'] == 2) {
                    $input['directPaymentPayeeSelectEmp'] = -1;
                    $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                    $input['directPaymentPayee'] = $emp->empFullName;
                }
            }

            if (isset($input['chequePaymentYN'])) {
                if ($input['chequePaymentYN']) {
                    $input['chequePaymentYN'] = -1;
                } else {
                    $input['chequePaymentYN'] = 0;
                }
            } else {
                $input['chequePaymentYN'] = 0;
            }

            $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];

            $input['createdPcID'] = gethostname();
            $input['createdUserID'] = \Helper::getEmployeeID();
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

            $paySupplierInvoiceMasters = $this->paySupplierInvoiceMasterRepository->create($input);

            DB::commit();
            return $this->sendResponse($paySupplierInvoiceMasters->toArray(), 'Pay Supplier Invoice Master saved successfully');
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
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->with(['confirmed_by', 'bankaccount', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError('Pay Supplier Invoice Master not found');
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

            $companySystemID = $paySupplierInvoiceMaster->companySystemID;
            $documentSystemID = $paySupplierInvoiceMaster->documentSystemID;

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
            }

            $bankAccount = BankAccount::find($input['BPVAccount']);
            if ($bankAccount) {
                $input['BPVbankCurrency'] = $bankAccount->accountCurrencyID;
                $currencyConversionDefaultMaster = \Helper::currencyConversion($companySystemID, $input['supplierTransCurrencyID'], $bankAccount->accountCurrencyID, 0);
                if ($currencyConversionDefaultMaster) {
                    $input['BPVbankCurrencyER'] = $currencyConversionDefaultMaster['transToDocER'];
                }
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
                }
                if ($input['payeeType'] == 2) {
                    $input['directPaymentPayeeSelectEmp'] = -1;
                    $emp = Employee::find($input["directPaymentPayeeEmpID"]);
                    $input['directPaymentPayee'] = $emp->empFullName;
                }
            }

            $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];

            if (isset($input['chequePaymentYN'])) {
                if ($input['chequePaymentYN']) {
                    $input['chequePaymentYN'] = -1;
                } else {
                    $input['chequePaymentYN'] = 0;
                }
            } else {
                $input['chequePaymentYN'] = 0;
            }

            $warningMessage = '';

            if ($input['BPVbankCurrency'] == $input['localCurrencyID'] && $input['supplierTransCurrencyID'] == $input['localCurrencyID']) {

            } else {
                $input['chequePaymentYN'] = 0;
                $warningMessage = "Cheque number won't be generated. The bank currency and the local currency is not equal.";
            }

            if ($paySupplierInvoiceMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

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

                $input['BPVdate'] = new Carbon($input['BPVdate']);
                $input['BPVchequeDate'] = new Carbon($input['BPVchequeDate']);

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
                // po payment
                if ($paySupplierInvoiceMaster->invoiceType == 2) {
                    $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $checkAmountGreater = PaySupplierInvoiceDetail::selectRaw('SUM(supplierPaymentAmount) as supplierPaymentAmount')->where('PayMasterAutoId', $id)->first();

                    if ($checkAmountGreater['supplierPaymentAmount'] < 0) {
                        return $this->sendError('Total Amount should be equal or greater than zero', 500, ['type' => 'confirm']);
                    }

                    $checkAmount = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->where('supplierPaymentAmount', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have a payment amount', 500, ['type' => 'confirm']);
                    }

                    $pvDetailExist = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->get();

                    foreach ($pvDetailExist as $val) {
                        $updatePayment = AccountsPayableLedger::find($val->apAutoID);
                        if ($updatePayment) {

                            $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->where('apAutoID', $val->apAutoID)->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

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
                if ($paySupplierInvoiceMaster->invoiceType == 5) {
                    $pvDetailExist = AdvancePaymentDetails::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $checkAmountGreater = AdvancePaymentDetails::selectRaw('SUM(paymentAmount) as supplierPaymentAmount')->where('PayMasterAutoId', $id)->first();

                    if ($checkAmountGreater['paymentAmount'] < 0) {
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
                    }

                }

                // Direct payment
                if ($paySupplierInvoiceMaster->invoiceType == 3) {
                    $pvDetailExist = DirectPaymentDetails::where('directPaymentAutoID', $id)->get();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $finalError = array(
                        'required_serviceLine' => array(),
                        'active_serviceLine' => array(),
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
                        $nextChequeNo = $bankAccount->chquePrintedStartingNo + 1;

                        $checkChequeNoDuplicate = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('BPVbank', $input['BPVbank'])->where('BPVAccount', $input['BPVAccount'])->where('BPVchequeNo', $nextChequeNo)->first();

                        if ($checkChequeNoDuplicate) {
                            return $this->sendError('The cheque no ' . $nextChequeNo . ' is already taken in ' . $checkChequeNoDuplicate['BPVcode'] . ' Please check again.', 500, ['type' => 'confirm']);
                        }

                        if ($bankAccount->isPrintedActive == 1) {
                            $chequeNumber = $bankAccount->chquePrintedStartingNo + 1;
                            $input['BPVchequeNo'] = $chequeNumber;

                            $bankAccount->chquePrintedStartingNo = $chequeNumber;
                            $bankAccount->save();
                        }
                    } else {
                        $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('PayMasterAutoId', 'DESC')->first();
                        if ($chkCheque) {
                            $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                        } else {
                            $input['BPVchequeNo'] = 1;
                        }
                    }
                } else {
                    $chkCheque = PaySupplierInvoiceMaster::where('companySystemID', $paySupplierInvoice->companySystemID)->where('BPVchequeNo', '>', 0)->where('chequePaymentYN', 0)->where('confirmedYN', 1)->where('PayMasterAutoId', '<>', $paySupplierInvoice->PayMasterAutoId)->orderBY('PayMasterAutoId', 'DESC')->first();
                    if ($chkCheque) {
                        $input['BPVchequeNo'] = $chkCheque->BPVchequeNo + 1;
                    } else {
                        $input['BPVchequeNo'] = 1;
                    }
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 2) {
                $totalAmount = PaySupplierInvoiceDetail::selectRaw("SUM(supplierInvoiceAmount) as supplierInvoiceAmount,SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierPaymentAmount) as supplierPaymentAmount, SUM(paymentBalancedAmount) as paymentBalancedAmount, SUM(paymentSupplierDefaultAmount) as paymentSupplierDefaultAmount, SUM(paymentLocalAmount) as paymentLocalAmount, SUM(paymentComRptAmount) as paymentComRptAmount")->where('PayMasterAutoId', $id)->first();

                if (!empty($totalAmount->supplierPaymentAmount)) {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierPaymentAmount);
                    $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->paymentLocalAmount);
                    $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->paymentComRptAmount);
                    $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
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
                $totalAmount = AdvancePaymentDetails::selectRaw("SUM(paymentAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(supplierTransAmount) as supplierTransAmount")->where('PayMasterAutoId', $id)->first();

                if (!empty($totalAmount->supplierTransAmount)) {
                    $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierTransAmount);
                    $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierTransAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierDefaultAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->localAmount);
                    $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->comRptAmount);
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
                    $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
                    $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->paymentAmount);
                    $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->paymentAmount);
                    $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->localAmount);
                    $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->comRptAmount);
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

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $id);

            $message = [['status' => 'success', 'message' => 'PaySupplierInvoiceMaster updated successfully'], ['status' => 'warning', 'message' => $warningMessage]];
            DB::commit();
            return $this->sendResponse($paySupplierInvoiceMaster->toArray(), $message);
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
    public function destroy($id)
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
            ->with(['supplier', 'bankaccount', 'transactioncurrency', 'supplierdetail', 'company', 'localcurrency', 'rptcurrency', 'advancedetail', 'confirmed_by', 'directdetail' => function ($query) {
                $query->with('segment');
            }, 'approved_by' => function ($query) {
                $query->with('employee');
                $query->where('documentSystemID', 4);
            }, 'created_by', 'cancelled_by'])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');

    }


    public function getAllPaymentVoucherByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year', 'cancelYN', 'confirmedYN', 'approved', 'invoiceType', 'supplierID'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $paymentVoucher = PaySupplierInvoiceMaster::with(['supplier', 'created_by', 'suppliercurrency', 'bankcurrency'])->whereIN('companySystemID', $subCompanies);

        if (array_key_exists('cancelYN', $input)) {
            if (($input['cancelYN'] == 0 || $input['cancelYN'] == -1) && !is_null($input['cancelYN'])) {
                $paymentVoucher->where('cancelYN', $input['cancelYN']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $paymentVoucher->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $paymentVoucher->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $paymentVoucher->whereMonth('BPVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $paymentVoucher->whereYear('BPVdate', '=', $input['year']);
            }
        }

        if (array_key_exists('invoiceType', $input)) {
            if ($input['invoiceType'] && !is_null($input['invoiceType'])) {
                $paymentVoucher->where('invoiceType', $input['invoiceType']);
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($input['supplierID'])) {
                $paymentVoucher->where('BPVsupplierID', $input['supplierID']);
            }
        }

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $paymentVoucher = $paymentVoucher->where(function ($query) use ($search) {
                $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%");
            });
        }

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
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        $supplier = SupplierAssigned::whereIn("companySystemID", $subCompanies);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $supplier = $supplier->where('isActive', 1);
        }
        $supplier = $supplier->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);
        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();
        $currency = CurrencyMaster::all();

        $years = PaySupplierInvoiceMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $bank = BankAssign::where('companySystemID', $companyId)->where('isActive', 1)->where('isAssigned', -1)->get();

        $payee = Employee::where('empCompanySystemID', $companyId)->where('discharegedYN', '<>', 2)->get();

        $segment = SegmentMaster::ofCompany($subCompanies)->IsActive()->get();

        $expenseClaimType = ExpenseClaimType::all();

        $interCompanyTo = Company::where('isGroup', 0)->get();

        $companyCurrency = \Helper::companyCurrency($companyId);

        $output = array(
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'supplier' => $supplier,
            'payee' => $payee,
            'bank' => $bank,
            'currency' => $currency,
            'segments' => $segment,
            'expenseClaimType' => $expenseClaimType,
            'interCompany' => $interCompanyTo,
            'companyCurrency' => $companyCurrency,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getBankAccount(Request $request)
    {
        $bankAccount = DB::table('erp_bankaccount')->leftjoin('currencymaster', 'currencyID', 'accountCurrencyID')->where('bankmasterAutoID', $request["bankmasterAutoID"])->where('erp_bankaccount.companySystemID', $request["companyID"])->where('isAccountActive', 1)->where('approvedYN', 1)->get();
        return $this->sendResponse($bankAccount, 'Record retrieved successfully');
    }

    public function checkPVDocumentActive(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var PaySupplierInvoiceMaster $paySupplierInvoiceMaster */
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

    public function getPOPaymentForPV(Request $request)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request["PayMasterAutoId"]);
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
	erp_accountspayableledger.invoiceType IN ( 0, 1, 4, 7 ) 
	AND DATE_FORMAT(erp_accountspayableledger.documentDate,"%Y-%d-%m") <= "' . $BPVdate . '" 
	AND erp_accountspayableledger.selectedToPaymentInv = 0 
	AND erp_accountspayableledger.fullyInvoice <> 2 
	AND erp_accountspayableledger.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
	AND erp_accountspayableledger.supplierCodeSystem = ' . $paySupplierInvoiceMaster->BPVsupplierID . ' 
	AND erp_accountspayableledger.supplierTransCurrencyID = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount,2) != 0 ORDER BY erp_accountspayableledger.apAutoID DESC';

        $output = DB::select($sql);
        return $this->sendResponse($output, 'Record retrieved successfully');
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

        if (!isset($input['matchType'])) {
            return $this->sendError('Please select a match type');
        }

        if ($input['matchType'] == 1) {
            $invoiceMaster = DB::select('SELECT
	MASTER .PayMasterAutoId,
	MASTER .BPVcode as documentCode,
	MASTER .BPVdate as docDate,
	MASTER .payAmountSuppTrans as transAmount,
	MASTER .BPVsupplierID,
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
AND invoiceType = 5
AND matchInvoice <> 2
AND MASTER.companySystemID = ' . $input['companySystemID'] . ' AND BPVsupplierID = ' . $input['BPVsupplierID'] . ' HAVING (ROUND(BalanceAmt, currency.DecimalPlaces) > 0)');
        } elseif ($input['matchType'] == 2) {
            $invoiceMaster = DB::select('SELECT
	MASTER .debitNoteAutoID,
	MASTER .debitNoteCode as documentCode,
	MASTER .debitNoteDate as docDate,
	MASTER .debitAmountTrans as transAmount,
	MASTER .supplierID,
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
	MASTER .debitNoteAutoID = advd.PayMasterAutoId
	AND MASTER .documentSystemID = advd.documentSystemID
	AND MASTER .companySystemID = advd.companySystemID
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
AND MASTER.companySystemID = ' . $input['companySystemID'] . '
AND supplierID = ' . $input['BPVsupplierID'] . '
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

    public function paymentVoucherReopen(Request $request)
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

            $deleteApproval = DocumentApproved::where('documentSystemCode', $id)
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

            if ($payInvoice->invoiceType == 2) {
                $pvDetailExist = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                    ->get();
                foreach ($pvDetailExist as $val) {
                    $updatePayment = AccountsPayableLedger::find($val->apAutoID);
                    if ($updatePayment) {
                        $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->where('apAutoID', $val->apAutoID)->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

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

            DB::commit();
            return $this->sendResponse($payInvoice->toArray(), 'Payment Voucher reopened successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function paymentVoucherCancel(Request $request)
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

        return $this->sendResponse($payInvoice->toArray(), 'Payment Voucher cancelled successfully');

    }

}

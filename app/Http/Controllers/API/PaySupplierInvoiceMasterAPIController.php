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
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceMasterAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceMasterAPIRequest;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\Company;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\Months;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
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
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($request->all(), [
            'invoiceType' => 'required',
            'supplierTransCurrencyID' => 'required',
            'BPVchequeNo' => 'required',
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
            ->orderBy('PayMasterAutoId', 'desc')
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

        $input['directPayeeCurrency'] = $input['supplierTransCurrencyID'];

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $paySupplierInvoiceMasters = $this->paySupplierInvoiceMasterRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceMasters->toArray(), 'Pay Supplier Invoice Master saved successfully');
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
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->with(['confirmed_by'])->findWithoutFail($id);

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

                if ($paySupplierInvoiceMaster->invoiceType == 2) {
                    $pvDetailExist = PaySupplierInvoiceDetail::select(DB::raw('PayMasterAutoId'))
                        ->where('PayMasterAutoId', $id)
                        ->first();

                    if (empty($pvDetailExist)) {
                        return $this->sendError('PV document cannot confirm without details', 500, ['type' => 'confirm']);
                    }

                    $checkAmount = PaySupplierInvoiceDetail::where('PayMasterAutoId', $id)
                        ->where('supplierPaymentAmount', '<=', 0)
                        ->count();

                    if ($checkAmount > 0) {
                        return $this->sendError('Every item should have payment amount', 500, ['type' => 'confirm']);
                    }

                    $params = array('autoID' => $id, 'company' => $companySystemID, 'document' => $documentSystemID, 'segment' => '', 'category' => '', 'amount' => 0);
                    $confirm = \Helper::confirmDocument($params);
                    if (!$confirm["success"]) {
                        return $this->sendError($confirm["message"]);
                    }
                }
            }

            if ($paySupplierInvoiceMaster->invoiceType == 2) {
                $totalAmount = PaySupplierInvoiceDetail::selectRaw("SUM(supplierInvoiceAmount) as supplierInvoiceAmount,SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierPaymentAmount) as supplierPaymentAmount, SUM(paymentBalancedAmount) as paymentBalancedAmount, SUM(paymentSupplierDefaultAmount) as paymentSupplierDefaultAmount, SUM(paymentLocalAmount) as paymentLocalAmount, SUM(paymentComRptAmount) as paymentComRptAmount")->where('PayMasterAutoId', $id)->first();

                $bankAmount = \Helper::currencyConversion($companySystemID,$paySupplierInvoiceMaster->supplierTransCurrencyID,$paySupplierInvoiceMaster->supplierTransCurrencyID,$totalAmount->supplierPaymentAmount,$paySupplierInvoiceMaster->BPVAccount);

                $input['payAmountBank'] = \Helper::roundValue($bankAmount["bankAmount"]);
                $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierPaymentAmount);
                $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->paymentLocalAmount);
                $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->paymentComRptAmount);
            }

            $input['modifiedPc'] = gethostname();
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();

            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $id);
            DB::commit();
            return $this->sendResponse($paySupplierInvoiceMaster->toArray(), 'PaySupplierInvoiceMaster updated successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
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
            }])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');

    }


    public function getAllPaymentVoucherByCompany(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('supplier', 'created_by', 'suppliercurrency', 'bankcurrency'));

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
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getBankAccount(Request $request)
    {
        $bankAccount = BankAccount::where('bankmasterAutoID', $request["bankmasterAutoID"])->where('companySystemID', $request["companyID"])->where('isAccountActive', 1)->where('approvedYN', 1)->get();
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

        $bankAccount = BankAccount::where('isAccountActive', 1)->where('approvedYN', 1)->find($input['BPVAccount']);

        if ($bankAccount) {
            return $this->sendResponse($bankAccount, 'Record retrieved successfully');
        } else {
            return $this->sendError('Bank account is not active', 500);
        }
    }

    public function getPOPaymentForPV(Request $request)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request["PayMasterAutoId"]);

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
	IFNULL(sid.SumOfpaymentBalancedAmount,0) as paymentBalancedAmount,
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
	erp_matchdocumentmaster.documentSystemID,
	erp_matchdocumentmaster.BPVcode,
	erp_matchdocumentmaster.BPVsupplierID,
	erp_matchdocumentmaster.supplierTransCurrencyID,
	erp_matchdocumentmaster.matchedAmount,
	erp_matchdocumentmaster.matchLocalAmount,
	erp_matchdocumentmaster.matchRptAmount,
	erp_matchdocumentmaster.matchingConfirmedYN
FROM
	erp_matchdocumentmaster 
WHERE
	erp_matchdocumentmaster.companySystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
	AND erp_matchdocumentmaster.documentSystemID = ' . $paySupplierInvoiceMaster->documentSystemID . '
	) md ON md.documentSystemID = erp_accountspayableledger.documentSystemID 
	AND md.PayMasterAutoId = erp_accountspayableledger.documentSystemCode 
	AND md.BPVsupplierID = erp_accountspayableledger.supplierCodeSystem 
	AND md.supplierTransCurrencyID = erp_accountspayableledger.supplierTransCurrencyID 
	LEFT JOIN currencymaster ON erp_accountspayableledger.supplierTransCurrencyID = currencymaster.currencyID 
WHERE
	erp_accountspayableledger.invoiceType IN ( 0, 1, 4, 7 ) 
	AND erp_accountspayableledger.documentDate < "' . $paySupplierInvoiceMaster->BPVdate . '" 
	AND erp_accountspayableledger.selectedToPaymentInv = 0 
	AND erp_accountspayableledger.fullyInvoice <> 2 
	AND erp_accountspayableledger.companysystemID = ' . $paySupplierInvoiceMaster->companySystemID . ' 
	AND erp_accountspayableledger.supplierCodeSystem = ' . $paySupplierInvoiceMaster->BPVsupplierID . ' 
	AND erp_accountspayableledger.supplierTransCurrencyID = ' . $paySupplierInvoiceMaster->supplierTransCurrencyID . ' HAVING ROUND(paymentBalancedAmount,DecimalPlaces) > 0');
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getADVPaymentForPV(Request $request)
    {
        $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->findWithoutFail($request["PayMasterAutoId"]);
        $output = DB::select('SELECT
	erp_purchaseorderadvpayment.poAdvPaymentID,
	erp_purchaseorderadvpayment.companyID,
	erp_purchaseorderadvpayment.poID,
	erp_purchaseorderadvpayment.poCode,
	erp_purchaseorderadvpayment.supplierID,
	erp_purchaseorderadvpayment.narration,
	erp_purchaseorderadvpayment.currencyID,
	currencymaster.CurrencyCode,
	currencymaster.DecimalPlaces,
	IFNULL( erp_purchaseorderadvpayment.reqAmount, 0 ) AS reqAmount,
	( IFNULL( erp_purchaseorderadvpayment.reqAmount, 0 ) - IFNULL( advd.SumOfpaymentAmount, 0 ) ) AS BalanceAmount,
	erp_purchaseordermaster.supplierTransactionCurrencyID,
	erp_purchaseordermaster.supplierTransactionER,
	erp_purchaseordermaster.localCurrencyID,
	erp_purchaseordermaster.localCurrencyER,
	erp_purchaseordermaster.companyReportingCurrencyID,
	erp_purchaseordermaster.companyReportingER 
FROM
	( ( erp_purchaseorderadvpayment LEFT JOIN currencymaster ON erp_purchaseorderadvpayment.currencyID = currencymaster.currencyID ) INNER JOIN erp_purchaseordermaster ON erp_purchaseorderadvpayment.poID = erp_purchaseordermaster.purchaseOrderID )
	LEFT JOIN (
SELECT
	erp_advancepaymentdetails.poAdvPaymentID,
	erp_advancepaymentdetails.companyID,
	erp_advancepaymentdetails.purchaseOrderID,
	IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount 
FROM
	erp_advancepaymentdetails 
GROUP BY
	erp_advancepaymentdetails.poAdvPaymentID,
	erp_advancepaymentdetails.companyID,
	erp_advancepaymentdetails.purchaseOrderID 
HAVING
	( ( ( erp_advancepaymentdetails.purchaseOrderID ) IS NOT NULL ) ) 
	) AS advd ON ( erp_purchaseorderadvpayment.poID = advd.purchaseOrderID ) 
	AND ( erp_purchaseorderadvpayment.poAdvPaymentID = advd.poAdvPaymentID ) 
	AND ( erp_purchaseorderadvpayment.companyID = advd.companyID ) 
WHERE
	(
	( ( erp_purchaseorderadvpayment.companySystemID ) = '.$paySupplierInvoiceMaster->companySystemID.' ) 
	AND ( ( erp_purchaseorderadvpayment.supplierID ) = '.$paySupplierInvoiceMaster->BPVsupplierID.' ) 
	AND ( ( erp_purchaseorderadvpayment.currencyID ) = '.$paySupplierInvoiceMaster->supplierTransCurrencyID.' ) 
	AND ( ( erp_purchaseorderadvpayment.selectedToPayment ) = 0 ) 
	AND ( ( erp_purchaseordermaster.poCancelledYN ) = 0 ) 
	AND ( ( erp_purchaseordermaster.poConfirmedYN ) = 1 ) 
	AND ( ( erp_purchaseordermaster.approved ) =- 1 ) 
	AND ( ( erp_purchaseordermaster.WO_confirmedYN ) = 1 ) 
	AND ( ( erp_purchaseordermaster.fullyPaid ) <> 2 )
	);');
        return $this->sendResponse($output, 'Record retrieved successfully');

    }
}

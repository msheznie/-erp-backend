<?php
/**
 * =============================================
 * -- File Name : QuotationMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 22 - January 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Master
 * -- REVISION HISTORY
 * -- Date: 23-January 2019 By: Nazir Description: Added new function getSalesQuotationFormData(),
 * -- Date: 23-January 2019 By: Nazir Description: Added new function getAllSalesQuotation(),
 * -- Date: 24-January 2019 By: Nazir Description: Added new function getItemsForSalesQuotation(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getSalesQuotationApprovals(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getApprovedSalesQuotationForUser(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function approveSalesQuotation(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function rejectSalesQuotation(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getSalesQuotationMasterRecord(),
 * -- Date: 25-January 2019 By: Nazir Description: Added new function getSalesQuotationPrintPDF(),
 * -- Date: 29-January 2019 By: Nazir Description: Added new function salesQuotationReopen(),
 * -- Date: 29-January 2019 By: Nazir Description: Added new function salesQuotationVersionCreate(),
 * -- Date: 03-February 2019 By: Nazir Description: Added new function salesQuotationAmend(),
 * -- Date: 05-February 2019 By: Nazir Description: Added new function salesQuotationAudit(),
 */

namespace App\Http\Controllers\API;
use App\helper\Helper;
use Illuminate\Support\Facades\Storage;

use App\helper\TaxService;
use App\Http\Requests\API\CreateQuotationMasterAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\AdvanceReceiptDetails;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\DeliveryOrderDetail;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ItemAssigned;
use App\Models\Months;
use App\Models\PoPaymentTerms;
use App\Models\PoPaymentTermTypes;
use App\Models\QuotationDetails;
use App\Models\QuotationDetailsRefferedback;
use App\Models\QuotationMaster;
use App\Models\QuotationMasterRefferedback;
use App\Models\QuotationMasterVersion;
use App\Models\QuotationVersionDetails;
use App\Models\SalesPersonMaster;
use App\Models\SegmentMaster;
use App\Models\SoPaymentTerms;
use App\Models\YesNoSelection;
use App\Models\Company;
use App\Models\YesNoSelectionForMinus;
use App\Models\ChartOfAccount;
use App\Models\CustomerCurrency;
use App\Models\QuotationStatusMaster;
use App\Models\CompanyPolicyMaster;
use App\Repositories\QuotationMasterRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerContactDetails;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Jobs\AddMultipleItemsToQuotation;
use Carbon\Carbon;
use Response;
use Auth;
use App\Jobs\DocumentAttachments\SoSentToCustomerJob;

/**
 * Class QuotationMasterController
 * @package App\Http\Controllers\API
 */
class QuotationMasterAPIController extends AppBaseController
{
    /** @var  QuotationMasterRepository */
    private $quotationMasterRepository;

    public function __construct(QuotationMasterRepository $quotationMasterRepo)
    {
        $this->quotationMasterRepository = $quotationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters",
     *      summary="Get a listing of the QuotationMasters.",
     *      tags={"QuotationMaster"},
     *      description="Get all QuotationMasters",
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
     *                  @SWG\Items(ref="#/definitions/QuotationMaster")
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
        $this->quotationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationMasters = $this->quotationMasterRepository->all();

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Masters retrieved successfully');
    }

    /**
     * @param CreateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationMasters",
     *      summary="Store a newly created QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Store QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        if (isset($input['documentDate'])) {
            if ($input['documentDate']) {
                $input['documentDate'] = new Carbon($input['documentDate']);
            }
        }

        if (isset($input['documentExpDate'])) {
            if ($input['documentExpDate']) {
                $input['documentExpDate'] = new Carbon($input['documentExpDate']);
            }
        }

        if ($input['documentExpDate'] < $input['documentDate']) {

            return $this->sendError('Document expiry date cannot be less than document date!');
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['vatRegisteredYN'] = $company->vatRegisteredYN;
        }else{
            $input['companyID'] = 0;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $input['documentID'] = $documentMaster->documentID;
        }

        $customerData = CustomerMaster::where('customerCodeSystem', $input['customerSystemCode'])->first();

        if ($customerData) {
            $input['customerCode'] = $customerData->CutomerCode;
            $input['customerName'] = $customerData->CustomerName;
            $input['customerAddress'] = $customerData->customerAddress1;
            //$input['customerTelephone'] = $customerData->CutomerCode;
            //$input['customerFax'] = $customerData->CutomerCode;
            //$input['customerEmail'] = $customerData->CutomerCode;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $input['transactionCurrencyID'], 0);

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['companyLocalCurrencyID'] = $company->localCurrencyID;
            $input['companyLocalExchangeRate'] = $companyCurrencyConversion['trasToLocER'];
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingExchangeRate'] = $companyCurrencyConversion['trasToRptER'];

        }

        //updating transaction currency details
        $transactionCurrencyData = CurrencyMaster::where('currencyID', $input['transactionCurrencyID'])->first();
        if ($transactionCurrencyData) {
            $input['transactionCurrency'] = $transactionCurrencyData->CurrencyCode;
            $input['transactionExchangeRate'] = 1;
            $input['transactionCurrencyDecimalPlaces'] = $transactionCurrencyData->DecimalPlaces;
        }

        //updating local currency details
        $localCurrencyData = CurrencyMaster::where('currencyID', $input['companyLocalCurrencyID'])->first();
        if ($localCurrencyData) {
            $input['companyLocalCurrency'] = $localCurrencyData->CurrencyCode;
            $input['companyLocalCurrencyDecimalPlaces'] = $localCurrencyData->DecimalPlaces;
        }

        //updating reporting currency details
        $reportingCurrencyData = CurrencyMaster::where('currencyID', $input['companyLocalCurrencyID'])->first();
        if ($reportingCurrencyData) {
            $input['companyReportingCurrency'] = $reportingCurrencyData->CurrencyCode;
            $input['companyReportingCurrencyDecimalPlaces'] = $reportingCurrencyData->DecimalPlaces;
        }

        //updating customer GL update
        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerSystemCode'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($customerGLCodeUpdate) {

            $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
            $chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $customerGLCodeUpdate->custGLAccountSystemID)->first();
            if ($chartOfAccountData) {
                $input['customerReceivableAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
                $input['customerReceivableGLAccount'] = $chartOfAccountData->AccountCode;
                $input['customerReceivableDescription'] = $chartOfAccountData->AccountDescription;
                $input['customerReceivableType'] = $chartOfAccountData->controlAccounts;
            }

        }

        $customerCurrency = CustomerCurrency::where('customerCodeSystem', $input['customerSystemCode'])->where('isDefault', -1)->first();
        if ($customerCurrency) {

            $customerCurrencyMasterData = CurrencyMaster::where('currencyID', $customerCurrency->currencyID)->first();

            $input['customerCurrencyID'] = $customerCurrency->currencyID;
            $input['customerCurrency'] = $customerCurrencyMasterData->CurrencyCode;
            $input['customerCurrencyDecimalPlaces'] = $customerCurrencyMasterData->DecimalPlaces;

            //updating customer currency exchange rate
            $currencyConversionCustomerDefault = \Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $customerCurrency->currencyID, 0);

            if ($currencyConversionCustomerDefault) {
                $input['customerCurrencyExchangeRate'] = $currencyConversionCustomerDefault['transToDocER'];
            }
        }

        // creating document code
        $lastSerial = QuotationMaster::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->orderBy('quotationMasterID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

        if ($documentMaster) {
            $quotationCode = ($company->CompanyID . '\\' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
            $input['quotationCode'] = $quotationCode;
        }

        $input['serialNumber'] = $lastSerialNumber;

        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;

        if(isset($input['quoId'])) {
            $quoMaster = QuotationMaster::find($input['quoId']);
            $quoMaster->isInSO = 1;
            $quoMaster->save();
        }

        $quotationMasters = $this->quotationMasterRepository->create($input);

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters/{id}",
     *      summary="Display the specified QuotationMaster",
     *      tags={"QuotationMaster"},
     *      description="Get QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
     *                  ref="#/definitions/QuotationMaster"
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->with(['created_by', 'confirmed_by','customer','segment'])->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        return $this->sendResponse($quotationMaster->toArray(), 'Quotation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationMasters/{id}",
     *      summary="Update the specified QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Update QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmedByName', 'confirmedByEmpID', 'confirmedDate', 'company', 'confirmed_by', 'confirmedByEmpSystemID','isVatEligible','customer','segment']);
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $tempName = '';
        if ($input['documentSystemID'] == 67) {
            $tempName = 'quotation';
        } else if ($input['documentSystemID'] == 68) {
            $tempName = 'order';
        }

        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);
        if(isset($quotationMaster->detail()->first()['soQuotationMasterID'])) {
            $quotationParent = $this->quotationMasterRepository->findWithoutFail(($quotationMaster->detail()->first()['soQuotationMasterID']));
            $details_count = QuotationDetails::where('soQuotationMasterID',$quotationMaster->detail()->first()['soQuotationMasterID'])->get();
            foreach($quotationParent->detail as $item) {
                foreach($details_count as $itemNew) {
                    if($item->requestedQty == $itemNew->requestedQty) {
                        $itemNew->fullyOrdered = 1;
                        $itemNew->save();
                    }   
                }
            }

            if(isset($quotationParent->detail)) {
                if($quotationParent->detail->sum('requestedQty') == $details_count->sum('requestedQty')){
                    $quotationParent->isInSO = 2 ;
                    $quotationParent->orderStatus = 2 ;
                }else {
                    $quotationParent->isInSO = 1 ;
                }
            }else {
                $quotationParent->isInSO = 1 ;
            }
            $quotationParent->update();

        } 

        if (empty($quotationMaster)) {
            return $this->sendError('Sales ' . $tempName . ' not found');
        }

        if($input['serviceLineSystemID']  == null || $input['serviceLineSystemID'] == 0){
            return $this->sendError('Please select a segment');
        }

        if (isset($input['documentDate'])) {
            if ($input['documentDate']) {
                $input['documentDate'] = new Carbon($input['documentDate']);
            }
        }

        if (isset($input['documentExpDate'])) {
            if ($input['documentExpDate']) {
                $input['documentExpDate'] = new Carbon($input['documentExpDate']);
            }
        }

        if ($input['documentExpDate'] < $input['documentDate']) {

            return $this->sendError('Document expiry date cannot be less than document date!');
        }

        $customerData = CustomerMaster::where('customerCodeSystem', $input['customerSystemCode'])->first();

        if ($customerData) {
            $input['customerCode'] = $customerData->CutomerCode;
            $input['customerName'] = $customerData->CustomerName;
            $input['customerAddress'] = $customerData->customerAddress1;
            //$input['customerTelephone'] = $customerData->CutomerCode;
            //$input['customerFax'] = $customerData->CutomerCode;
            //$input['customerEmail'] = $customerData->CutomerCode;
        }

        //updating transaction currency details
        $transactionCurrencyData = CurrencyMaster::where('currencyID', $input['transactionCurrencyID'])->first();
        if ($transactionCurrencyData) {
            $input['transactionCurrency'] = $transactionCurrencyData->CurrencyCode;
            $input['transactionExchangeRate'] = 1;
            $input['transactionCurrencyDecimalPlaces'] = $transactionCurrencyData->DecimalPlaces;
        }


        //updating customer GL update
        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerSystemCode'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($customerGLCodeUpdate) {
            $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
            $chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $customerGLCodeUpdate->custGLAccountSystemID)->first();
            if ($chartOfAccountData) {
                $input['customerReceivableAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
                $input['customerReceivableGLAccount'] = $chartOfAccountData->AccountCode;
                $input['customerReceivableDescription'] = $chartOfAccountData->AccountDescription;
                $input['customerReceivableType'] = $chartOfAccountData->controlAccounts;
            }

        }

        $customerCurrency = CustomerCurrency::where('customerCodeSystem', $input['customerSystemCode'])->where('isDefault', -1)->first();
        if ($customerCurrency) {

            $customerCurrencyMasterData = CurrencyMaster::where('currencyID', $customerCurrency->currencyID)->first();

            $input['customerCurrencyID'] = $customerCurrency->currencyID;
            $input['customerCurrency'] = $customerCurrencyMasterData->CurrencyCode;
            $input['customerCurrencyDecimalPlaces'] = $customerCurrencyMasterData->DecimalPlaces;

            //updating customer currency exchange rate
            $currencyConversionCustomerDefault = \Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $customerCurrency->currencyID, 0);

            if ($currencyConversionCustomerDefault) {
                $input['customerCurrencyExchangeRate'] = $currencyConversionCustomerDefault['transToDocER'];
            }
        }

        // updating header amounts
        $totalAmount = QuotationDetails::selectRaw("COALESCE(SUM(transactionAmount),0) as totalTransactionAmount,
                                                     COALESCE(SUM(companyLocalAmount),0) as totalLocalAmount, 
                                                     COALESCE(SUM(companyReportingAmount),0) as totalReportingAmount, 
                                                     COALESCE(SUM(customerAmount),0) as totalCustomerAmount,
                                                     COALESCE(SUM(VATAmount * requestedQty),0) as totalVATAmount,
                                                     COALESCE(SUM(VATAmountLocal * requestedQty),0) as totalVATAmountLocal,
                                                     COALESCE(SUM(VATAmountRpt * requestedQty),0) as totalVATAmountRpt
                                                     ")
                                         ->where('quotationMasterID', $id)->first();
        $input['transactionAmount'] = \Helper::roundValue($totalAmount->totalTransactionAmount + $totalAmount->totalVATAmount);
        $input['companyLocalAmount'] = \Helper::roundValue($totalAmount->totalLocalAmount + $totalAmount->totalVATAmountLocal);
        $input['companyReportingAmount'] = \Helper::roundValue($totalAmount->totalReportingAmount + $totalAmount->totalVATAmountRpt);
        $input['customerCurrencyAmount'] = \Helper::roundValue($totalAmount->totalCustomerAmount);

        if(!TaxService::checkPOVATEligible($input['customerVATEligible'],$input['vatRegisteredYN'])){
            $input['VATAmount'] = 0;
            $input['VATAmountLocal'] = 0;
            $input['VATAmountRpt'] = 0;
        }else{
            $input['VATAmount'] = \Helper::roundValue($totalAmount->totalVATAmount);
            $input['VATAmountLocal'] = \Helper::roundValue($totalAmount->totalVATAmountLocal);
            $input['VATAmountRpt'] = \Helper::roundValue($totalAmount->totalVATAmountRpt);
        }

        if ($quotationMaster->confirmedYN == 0 && $input['confirmedYN'] == 1) {

            $validator = \Validator::make($input, [
                'documentDate' => 'required',
                'documentExpDate' => 'required',
                'customerSystemCode' => 'required|numeric|min:1',
                'transactionCurrencyID' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $qoDetailExist = QuotationDetails::where('quotationMasterID', $id)
                ->count();

            if ($qoDetailExist == 0) {
                return $this->sendError('Sales ' . $tempName . ' cannot be confirmed without any details');
            }

            $checkQuantity = QuotationDetails::where('quotationMasterID', $id)
                ->where('requestedQty', '<', 0.1)
                ->count();

            if ($checkQuantity > 0) {
                return $this->sendError('Every item should have at least one minimum qty requested', 500);
            }

            if ($qoDetailExist > 0) {
                $checkAmount = QuotationDetails::where('quotationMasterID', $id)
                    ->where(function ($q) {
                        $q->where('transactionAmount', '<=', 0)
                            ->orWhereNull('companyLocalAmount', '<=', 0)
                            ->orWhereNull('companyReportingAmount', '<=', 0)
                            ->orWhereNull('transactionAmount')
                            ->orWhereNull('companyLocalAmount')
                            ->orWhereNull('companyReportingAmount');
                    })
                    ->count();
                if ($checkAmount > 0) {
                    return $this->sendError('Amount should be greater than 0 for every items', 500);
                }
            }

            //

            if($quotationMaster->documentSystemID == 68){
                //checking atleast one po payment terms should exist
                $soPaymentTerms = SoPaymentTerms::where('soID', $id)
                    ->count();

                if ($soPaymentTerms == 0) {
                    return $this->sendError('Sales Order should have at least one payment term');
                }

                // checking payment term amount value 0

                $checkPoPaymentTermsAmount = SoPaymentTerms::where('soID', $id)
                    ->where('comAmount', '<=', 0)
                    ->count();

                if ($checkPoPaymentTermsAmount > 0) {
                    return $this->sendError('You cannot confirm payment term with 0 amount', 500);
                }

                //po payment terms exist
                $PoPaymentTerms = SoPaymentTerms::where('soID', $id)
                    ->where('LCPaymentYN', 2)
                    ->where('isRequested', 0)
                    ->first();

                if (!empty($PoPaymentTerms)) {
                    return $this->sendError('Advance payment request is pending');
                }

                //getting total sum of So Payment Terms
                $paymentTotalSum = SoPaymentTerms::select(DB::raw('IFNULL(SUM(comAmount),0) as paymentTotalSum'))
                    ->where('soID', $id)
                    ->first();

                $soMasterSumDeducted = $input['transactionAmount'];

                //return floatval($soMasterSumDeducted)." - ".floatval($paymentTotalSum['paymentTotalSum']);

                //return $soMasterSumDeducted.'-'.$paymentTotalSum['paymentTotalSum'];

                if ($paymentTotalSum['paymentTotalSum'] > 0) {
                    $soMasterSumDeductedCheckAmount = floatval(sprintf("%.".$input['transactionCurrencyDecimalPlaces']."f", $soMasterSumDeducted));
                    $paymentTotalSumCheckAmount = floatval(sprintf("%.".$input['transactionCurrencyDecimalPlaces']."f", $paymentTotalSum['paymentTotalSum']));

                    $epsilon = 0.00001;
                    if(abs($soMasterSumDeductedCheckAmount - $paymentTotalSumCheckAmount) > $epsilon) {
                        return $this->sendError('Payment terms total is not matching with the SO total');
                    }
                } 
            }

            $input['RollLevForApp_curr'] = 1;

            unset($input['confirmedYN']);
            unset($input['confirmedByEmpSystemID']);
            unset($input['confirmedByEmpID']);
            unset($input['confirmedByName']);
            unset($input['confirmedDate']);

            $params = array(
                'autoID' => $id,
                'company' => $input["companySystemID"],
                'document' => $input["documentSystemID"],
                'segment' => 0,
                'category' => 0,
                'amount' => $input['transactionAmount']
            );
            $confirm = \Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"]);
            }
        }
        $input['modifiedDateTime'] = Carbon::now();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;

        $quotationMaster = $this->quotationMasterRepository->update($input, $id);

        return $this->sendResponse($quotationMaster->toArray(), 'Sales ' . $tempName . ' updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationMasters/{id}",
     *      summary="Remove the specified QuotationMaster from storage",
     *      tags={"QuotationMaster"},
     *      description="Delete QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotationMaster->delete();

        return $this->sendResponse($id, 'Quotation Master deleted successfully');
    }

    public function getSalesQuotationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $customer = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
            ->whereHas('customer_master',function($q){
                $q->where('isCustomerActive',1);
            })
            ->where('companySystemID', $subCompanies)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $salespersons = SalesPersonMaster::select(DB::raw("salesPersonID,CONCAT(SalesPersonCode, ' | ' ,SalesPersonName) as SalesPersonName"))
            ->where('companySystemID', $subCompanies)
            ->get();

        $years = QuotationMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $month = Months::all();

        $quotationStatuses = QuotationStatusMaster::where('isAdmin', 0)->get();

        $segments = SegmentMaster::whereIn("companySystemID", $subCompanies)
                                  ->where('isActive', 1)
                                  ->get();
        $soPaymentTermsDrop = PoPaymentTermTypes::all();


        /* check add new item policy */
        $addNewItem = CompanyPolicyMaster::where('companyPolicyCategoryID', 64)
        ->where('companySystemID', $companyId)
        ->first();

        // check policy 44 is on for CI
        $isEQOINVPolicyOn = CompanyPolicyMaster::where('companySystemID', $companyId)
            ->where('companyPolicyCategoryID', 44)
            ->where('isYesNO', 1)
            ->exists();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'quotationStatuses' => $quotationStatuses,
            'customer' => $customer,
            'salespersons' => $salespersons,
            'segments' => $segments,
            'soPaymentTermsDrop' => $soPaymentTermsDrop,
            'addNewItemPolicy' => ($addNewItem) ? $addNewItem->isYesNO : false,
            'isEQOINVPolicyOn' => ($isEQOINVPolicyOn) ? $isEQOINVPolicyOn : false
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getAllSalesQuotation(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $quotationMaster = $this->quotationMasterRepository->quotationMasterListQuery($request, $input, $search);

        return \DataTables::eloquent($quotationMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('quotationMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getItemsForSalesQuotation(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $fromSalesQuotation = isset($input['fromSalesQuotation'])?$input['fromSalesQuotation']:0;

        $items = ItemAssigned::where('companySystemID', $companySystemID)
            ->where('isActive', 1)
            ->where('isAssigned', -1);
        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $items = $items->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        if($fromSalesQuotation == 1){
            $items = $items->whereIn('financeCategoryMaster',[1,2,4]);
        }
        $items = $items
            ->take(20)
            ->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function getSalesQuotationApprovals(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $documentSystemID = $request->documentSystemID;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'erp_quotationmaster.quotationMasterID',
            'erp_quotationmaster.quotationCode',
            'erp_quotationmaster.documentSystemID',
            'erp_quotationmaster.referenceNo',
            'erp_quotationmaster.documentDate',
            'erp_quotationmaster.documentExpDate',
            'erp_quotationmaster.narration',
            'erp_quotationmaster.createdDateTime',
            'erp_quotationmaster.confirmedDate',
            'erp_quotationmaster.transactionAmount',
            'erp_quotationmaster.customerName',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID, $documentSystemID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            $query
                ->where('employeesdepartments.documentSystemID', $documentSystemID)
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_quotationmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'quotationMasterID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_quotationmaster.companySystemID', $companyID)
                ->where('erp_quotationmaster.approvedYN', 0)
                ->where('erp_quotationmaster.confirmedYN', 1)
                ->where('erp_quotationmaster.cancelledYN', 0);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('quotationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('customerName', 'LIKE', "%{$search}%");
            });
        }
        $grvMasters = $grvMasters->groupBy('quotationMasterID');

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

    public function getApprovedSalesQuotationForUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $documentSystemID = $request->documentSystemID;
        $empID = \Helper::getEmployeeSystemID();

        $grvMasters = DB::table('erp_documentapproved')->select(
            'erp_quotationmaster.quotationMasterID',
            'erp_quotationmaster.quotationCode',
            'erp_quotationmaster.documentSystemID',
            'erp_quotationmaster.referenceNo',
            'erp_quotationmaster.documentDate',
            'erp_quotationmaster.documentExpDate',
            'erp_quotationmaster.narration',
            'erp_quotationmaster.createdDateTime',
            'erp_quotationmaster.confirmedDate',
            'erp_quotationmaster.transactionAmount',
            'erp_quotationmaster.approvedDate',
            'erp_quotationmaster.customerName',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_quotationmaster', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'quotationMasterID')
                ->where('erp_quotationmaster.companySystemID', $companyID)
                ->where('erp_quotationmaster.approvedYN', -1)
                ->where('erp_quotationmaster.confirmedYN', 1)
                ->where('erp_quotationmaster.cancelledYN', 0);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->where('erp_documentapproved.companySystemID', $companyID)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMasters = $grvMasters->where(function ($query) use ($search) {
                $query->where('quotationCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('customerName', 'LIKE', "%{$search}%");
            });
        }
        $grvMasters = $grvMasters->groupBy('quotationMasterID');

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

    public function approveSalesQuotation(Request $request)
    {
        $approve = \Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectSalesQuotation(Request $request)
    {
        $reject = \Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function getSalesQuotationMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = QuotationMaster::where('quotationMasterID', $input['quotationMasterID'])->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->whereIn('documentSystemID',[67,68]);
        }, 'company', 'detail', 'confirmed_by', 'created_by', 'modified_by', 'sales_person', 'paymentTerms_by' => function($query) {
            $query->with(['term_description']);
        }])->first();

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function getSalesQuotationPrintPDF(Request $request)
    {
        $id = $request->get('id');

        $quotationMasterData = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }

        $output = QuotationMaster::where('quotationMasterID', $id)->with(['approved_by' => function ($query) {
            $query->with('employee');
            $query->whereIn('documentSystemID', [67,68]);
        }, 'company', 'detail', 'confirmed_by', 'created_by', 'modified_by', 'sales_person'])->first();

        $netTotal = QuotationDetails::where('quotationMasterID', $id)
            ->sum('transactionAmount');

        $soPaymentTerms = SoPaymentTerms::where('soID', $id)
                                        ->with(['term_description'])
                                        ->get();

        $paymentTermsView = '';

        if ($soPaymentTerms) {
            foreach ($soPaymentTerms as $val) {
                $paymentTermsView .= $val['term_description']['categoryDescription'] .' '.$val['comAmount'].' '.$output['transactionCurrency'].' '.$val['paymentTemDes'].' '.$val['inDays'] . ' in days, ';
            }
        }

        $order = array(
            'masterdata' => $output,
            'paymentTermsView' => $paymentTermsView,
            'netTotal' => $netTotal
        );

        $html = view('print.sales_quotation', $order);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4', 'portrait')->setWarnings(false)->stream();
    }

    public function updateSentCustomerDetail(Request $request){
        $input = $request->quomaster;
        $id = $input['quotationMasterID'];
        $quotationMasterData = QuotationMaster::find($id);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }

        $emailSentTo = 0;
        $customerCodeSystem = $input['customer']['customerCodeSystem'];

        $fetchCusEmail = CustomerContactDetails::where('customerID', $customerCodeSystem)
                                                ->where('isDefault' , -1)
                                                ->get();


        if (count($fetchCusEmail) > 0) {
            foreach ($fetchCusEmail as $row) {
                if ($row->contactPersonEmail) {
                    $emailSentTo = 1;
                }
            }
        }


        if ($emailSentTo == 0) {
            return $this->sendResponse($emailSentTo, 'Customer email is not updated. report is not sent');
        } else {
            SoSentToCustomerJob::dispatch($request->db, $input);
            return $this->sendResponse($emailSentTo, 'Customer sales quotation report sent');
        }
    }


    public function salesQuotationReopen(request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];

        $quotationMasterData = QuotationMaster::find($quotationMasterID);
        $emails = array();
        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation master not found');
        }

        if ($quotationMasterData->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this sales quotation it is already partially approved');
        }

        if ($quotationMasterData->approved == -1) {
            return $this->sendError('You cannot reopen this sales quotation it is already fully approved');
        }

        if ($quotationMasterData->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this sales quotation, it is not confirmed');
        }

        // updating fields
        $quotationMasterData->confirmedYN = 0;
        $quotationMasterData->confirmedByEmpSystemID = null;
        $quotationMasterData->confirmedByEmpID = null;
        $quotationMasterData->confirmedByName = null;
        $quotationMasterData->confirmedDate = null;
        $quotationMasterData->RollLevForApp_curr = 1;
        $quotationMasterData->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $quotationMasterData->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $quotationMasterData->quotationCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $quotationMasterData->quotationCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemCode', $quotationMasterData->custInvoiceDirectAutoID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $quotationMasterData->companySystemID)
                    ->where('documentSystemID', $quotationMasterData->documentSystemID)
                    ->first();

                /*if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }*/

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                /*  if ($companyDocument['isServiceLineApproval'] == -1) {
                      $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                  }*/

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

        DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($quotationMasterData->documentSystemID,$quotationMasterID,$input['reopenComments'],'Reopened');

        return $this->sendResponse('s', 'Sales quotation reopened successfully');

    }

    public function salesQuotationVersionCreate(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();
        $currentVersion = 0;

        $quotationMasterData = QuotationMaster::find($quotationMasterID);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation master not found');
        }

        /*check order is already added to invoice or delivery order*/
        $existsinCI = CustomerInvoiceItemDetails::where('quotationMasterID',$quotationMasterID)->exists();
        $existsinDO = DeliveryOrderDetail::where('quotationMasterID',$quotationMasterID)->exists();
        $existsinSO = QuotationDetails::where('soQuotationMasterID',$quotationMasterID)->exists();
        $quotOrSales = ($quotationMasterData->documentSystemID == 68)?'Sales Order':'Quotation';

        if($existsinCI || $quotationMasterData->isInDOorCI == 2){
            return $this->sendError($quotOrSales.' is added to a customer invoice',500);
        }

        if($existsinDO || $quotationMasterData->isInDOorCI == 1){
            return $this->sendError($quotOrSales.' is added to a delivery order',500);
        }

        if($existsinSO || $quotationMasterData->isInSO == 1){
            return $this->sendError($quotOrSales.' is added to a sales order',500);
        }

        $quotationMasterArray = array_except($quotationMasterData->toArray(),'isVatEligible');
        unset($quotationMasterArray['quotation_last_status']);
        $storeQuotationMasterVersion = QuotationMasterVersion::insert($quotationMasterArray);

        $fetchQuotationDetails = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        if (!empty($fetchQuotationDetails)) {
            foreach ($fetchQuotationDetails as $bookDetail) {
                $bookDetail['versionNo'] = $quotationMasterData->versionNo;
            }
        }

        $quotationDetailsArray = $fetchQuotationDetails->toArray();

        $storeQuotationVersionDetails = QuotationVersionDetails::insert($quotationDetailsArray);

        // sending email to the relevant party

        $emailBody = '<p>' . $quotationMasterData->quotationCode . ' is being revised by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $quotationMasterData->quotationCode . ' has been created new version';

        if ($quotationMasterData->confirmedYN == 1) {
            $emails[] = array('empSystemID' => $quotationMasterData->confirmedByEmpSystemID,
                'companySystemID' => $quotationMasterData->companySystemID,
                'docSystemID' => $quotationMasterData->documentSystemID,
                'alertMessage' => $emailSubject,
                'emailAlertMessage' => $emailBody,
                'docSystemCode' => $quotationMasterID);
        }

        $documentApproval = DocumentApproved::where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemCode', $quotationMasterID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->get();

        foreach ($documentApproval as $da) {
            if ($da->approvedYN == -1) {
                $emails[] = array('empSystemID' => $da->employeeSystemID,
                    'companySystemID' => $quotationMasterData->companySystemID,
                    'docSystemID' => $quotationMasterData->documentSystemID,
                    'alertMessage' => $emailSubject,
                    'emailAlertMessage' => $emailBody,
                    'docSystemCode' => $quotationMasterID);
            }
        }

        $sendEmail = \Email::sendEmail($emails);
        if (!$sendEmail["success"]) {
            return $this->sendError($sendEmail["message"], 500);
        }

        $deleteApproval = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->delete();

        if ($quotationMasterData) {
            $currentVersion = $quotationMasterData->versionNo + 1;
        }

        if ($deleteApproval) {
            // updating fields
            $quotationMasterData->versionNo = $currentVersion;
            $quotationMasterData->confirmedYN = 0;
            $quotationMasterData->confirmedByEmpSystemID = null;
            $quotationMasterData->confirmedByEmpID = null;
            $quotationMasterData->confirmedByName = null;
            $quotationMasterData->confirmedDate = null;
            $quotationMasterData->RollLevForApp_curr = 1;

            $quotationMasterData->approvedYN = 0;
            $quotationMasterData->approvedEmpSystemID = null;
            $quotationMasterData->approvedbyEmpID = null;
            $quotationMasterData->approvedbyEmpName = null;
            $quotationMasterData->approvedDate = null;
            $quotationMasterData->save();
        }

        return $this->sendResponse($quotationMasterData->toArray(), 'Quotation version created successfully');
    }

    public function salesQuotationAmend(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $quotationMasterData = QuotationMaster::find($quotationMasterID);

        if (empty($quotationMasterData)) {
            return $this->sendError('Sales quotation not found');
        }

        if ($quotationMasterData->refferedBackYN != -1) {
            return $this->sendError('You cannot refer back this Sales quotation');
        }

        $salesQuotationArray = $quotationMasterData->toArray();
        $salesQuotationArray = array_except($salesQuotationArray,['quotation_last_status', 'isVatEligible','assetID','isFrom']);

        $storeSalesQuotationHistory = QuotationMasterRefferedback::insert($salesQuotationArray);

        $fetchQuotationDetails = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        if (!empty($fetchQuotationDetails)) {
            foreach ($fetchQuotationDetails as $bookDetail) {
                $bookDetail['timesReferred'] = $quotationMasterData->timesReferred;
            }
        }

        $salesQuotationDetailArray = $fetchQuotationDetails->toArray();

        $storeSalesQuotationDetailHistory = QuotationDetailsRefferedback::insert($salesQuotationDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $quotationMasterData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $quotationMasterID)
            ->where('companySystemID', $quotationMasterData->companySystemID)
            ->where('documentSystemID', $quotationMasterData->documentSystemID)
            ->delete();

        if ($deleteApproval) {
            $quotationMasterData->refferedBackYN = 0;
            $quotationMasterData->confirmedYN = 0;
            $quotationMasterData->confirmedByEmpSystemID = null;
            $quotationMasterData->confirmedByEmpID = null;
            $quotationMasterData->confirmedByName = null;
            $quotationMasterData->confirmedDate = null;
            $quotationMasterData->RollLevForApp_curr = 1;
            $quotationMasterData->save();
        }

        return $this->sendResponse($quotationMasterData->toArray(), 'Sales quotation amend successfully');
    }

    public function salesQuotationAudit(Request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];
        $quotationMasterdata = $this->quotationMasterRepository->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->whereIn('documentSystemID', [67, 68]);
        }, 'company','audit_trial.modified_by'])->findWithoutFail($quotationMasterID);


        if (empty($quotationMasterdata)) {
            return $this->sendError('Sales quotation not found');
        }

        return $this->sendResponse($quotationMasterdata->toArray(), 'Sales quotation retrieved successfully');
    }

    public function salesQuotationForCustomerInvoice(Request $request){
        $input = $request->all();
        $invoice = CustomerInvoiceDirect::find($input['custInvoiceDirectAutoID']);

        $documentSystemID = 0;
        if($invoice->isPerforma == 4){ //Sales Order
            $documentSystemID = 68;
        } elseif ($invoice->isPerforma==5){ ////Quotation
            $documentSystemID = 67;
        }

        $master = QuotationMaster::where('documentSystemID',$documentSystemID)
            ->where('companySystemID',$input['companySystemID'])
            ->where('approvedYN', -1)
            ->where('selectedForDeliveryOrder', 0)
            ->where('selectedForSalesOrder', 0)
            ->where('isInDOorCI', '!=',1)
            ->where('isInSO', '!=',1)
            ->where('closedYN',0)
            ->where('cancelledYN',0)
            ->where('manuallyClosed',0)
            ->where('serviceLineSystemID', $invoice->serviceLineSystemID)
            ->where('customerSystemCode', $invoice->customerID)
            ->where('transactionCurrencyID', $invoice->custTransactionCurrencyID)
            ->whereDate('documentDate', '<=',$invoice->bookingDate)
            ->orderBy('quotationMasterID','DESC')
            ->get();

        return $this->sendResponse($master->toArray(), 'Quotations retrieved successfully');
    }

    public function getSalesQuotationRecord(Request $request){

        $input = $request->all();
        /*$id = $input['deliveryOrderID'];
        $companySystemID = $input['companySystemID'];
        $deliveryOrder = DeliveryOrder::with(['company','customer','transaction_currency', 'sales_person','detail' => function($query){
            $query->with(['quotation','uom_default','uom_issuing']);
        },'approved_by' => function($query) use($companySystemID){
            $query->where('companySystemID',$companySystemID)
                ->where('documentSystemID',71)
                ->with(['employee']);
        }])->find($id);

        if (empty($deliveryOrder)) {
            return $this->sendError('Delivery Order not found');
        }

        return $this->sendResponse($deliveryOrder->toArray(), 'Delivery Order retrieved successfully');*/
    }

    function getInvoiceDetailsForSQ(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $detail = CustomerInvoiceItemDetails::where('quotationMasterID',$quotationMasterID)
            ->with(['master'=> function($query){
                $query->with(['currency']);
            },'sales_quotation_detail','uom_issuing'])
            ->get();
        return $this->sendResponse($detail, 'Details retrieved successfully');
    }


     public function salesQuotationForSO(Request $request){
        $input = $request->all();
        $documentSystemID = 67;
      
        $salesOrderData = QuotationMaster::find($input['salesOrderID']);

        $quotaionDetails = QuotationDetails::where('quotationMasterID',$input['salesOrderID'])->pluck('soQuotationMasterID')->values()->toArray();
       
        $existsSo = QuotationMaster::where('documentSystemID',$documentSystemID)
            ->where('companySystemID',$input['companySystemID'])
            ->whereIn('quotationMasterID',$quotaionDetails)
            ->where('serviceLineSystemID', $salesOrderData->serviceLineSystemID)
            ->where('customerSystemCode', $salesOrderData->customerSystemCode)
            ->where('transactionCurrencyID', $salesOrderData->transactionCurrencyID)
            ->orderBy('quotationMasterID','DESC')
            ->get();
        $master = QuotationMaster::where('documentSystemID',$documentSystemID)
            ->where('companySystemID',$input['companySystemID'])
            ->where('approvedYN', -1)
            ->where('selectedForDeliveryOrder', 0)
            ->where('selectedForSalesOrder', 0)
            ->where('isInDOorCI', '!=',2)
            ->where('isInDOorCI', '!=',1)
            ->where('closedYN',0)
            ->where('cancelledYN',0)
            ->where('manuallyClosed',0)
            ->where('serviceLineSystemID', $salesOrderData->serviceLineSystemID)
            ->where('customerSystemCode', $salesOrderData->customerSystemCode)
            ->where('transactionCurrencyID', $salesOrderData->transactionCurrencyID)
            ->orderBy('quotationMasterID','DESC')
            ->get();


        return $this->sendResponse($master->merge($existsSo)->toArray(), 'Quotations retrieved successfully');
    }

    public function getSalesQuoatationDetailForSO(Request $request){
        $input = $request->all();
        $id = $input['quotationMasterID'];

        $detail = DB::select('SELECT
                                quotationdetails.*,
                                erp_quotationmaster.serviceLineSystemID,
                                "" AS isChecked,
                                "" AS noQty,
                                IFNULL(sodetails.soTakenQty,0) as soTakenQty 
                            FROM
                                erp_quotationdetails quotationdetails
                                INNER JOIN erp_quotationmaster ON quotationdetails.quotationMasterID = erp_quotationmaster.quotationMasterID
                                LEFT JOIN ( SELECT erp_quotationdetails.quotationDetailsID,soQuotationDetailID, SUM( requestedQty ) AS soTakenQty FROM erp_quotationdetails GROUP BY soQuotationDetailID, itemAutoID ) AS sodetails ON quotationdetails.quotationDetailsID = sodetails.soQuotationDetailID 
                            WHERE
                                quotationdetails.quotationMasterID = ' . $id . ' 
                                AND fullyOrdered != 2 AND erp_quotationmaster.isInDOorCI != 2 AND erp_quotationmaster.isInDOorCI != 1');

        return $this->sendResponse($detail, 'Quotation Details retrieved successfully');
    }


    function getOrderDetailsForSQ(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $detail = QuotationDetails::where('soQuotationMasterID',$quotationMasterID)
            ->with(['sales_order_detail','uom_issuing',
                'master'=> function($query){
                    $query->with(['transaction_currency']);
                }])
            ->get();
        return $this->sendResponse($detail, 'Details retrieved successfully');
    }


    public function amendSalesQuotationReview(Request $request)
    {
        $input = $request->all();

        $id = $input['quotationMasterID'];


        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = QuotationMaster::find($id);

        if (empty($masterData)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotOrSales = ($masterData->documentSystemID == 68)?'Sales Order':'Quotation';

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this '.$quotOrSales.', it is not confirmed');
        }

        /*check order is already added to invoice or delivery order*/

        if(CustomerInvoiceItemDetails::where('quotationMasterID',$id)->exists() || $masterData->isInDOorCI == 2){
            return $this->sendError('You cannot return back to amend this '.$quotOrSales.'. It is added to a customer invoice',500);
        }

        if(DeliveryOrderDetail::where('quotationMasterID',$id)->exists() || $masterData->isInDOorCI == 1){
            return $this->sendError('You cannot return back to amend this '.$quotOrSales.'. It is added to a delivery order',500);
        }

        if(QuotationDetails::where('soQuotationMasterID',$id)->exists() || $masterData->isInSO == 1){
            return $this->sendError('You cannot return back to amend this '.$quotOrSales.'. It is added to a sales order',500);
        }


        if(QuotationMasterVersion::where('quotationMasterID',$id)->exists()){
            return $this->sendError('You cannot return back to amend this '.$quotOrSales.', versions created for it');
        }

        if(AdvanceReceiptDetails::where('salesOrderID',$id)->exists()){
            return $this->sendError('You cannot return back to amend this '.$quotOrSales.', It is added to advance receipt voucher');
        }

        $emailBody = '<p>' . $masterData->quotationCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->quotationCode . ' has been return back to amend';

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
                    'docCode' => $masterData->quotationCode
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
                        'docCode' => $masterData->quotationCode
                    );
                }
            }

            $sendEmail = \Email::sendEmail($emails);
            if (!$sendEmail["success"]) {
                return $this->sendError($sendEmail["message"], 500);
            }

            //deleting from approval table
            DocumentApproved::where('documentSystemCode', $id)
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
            $masterData->approvedEmpSystemID = null;
            $masterData->approvedbyEmpID = null;
            $masterData->approvedbyEmpName = null;
            $masterData->approvedDate = null;
            $masterData->save();

            AuditTrial::createAuditTrial($masterData->documentSystemID,$id,$input['returnComment'],'returned back to amend');

            DB::commit();
            return $this->sendResponse($masterData->toArray(), 'Return back to amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function cancelQuatation(Request $request)
    {
      
        $input = $request->all();
        $id = $input['quotationMasterID'];
        $comment = $input['cancelComments'];

        $doc_id = $input['documentSystemID'];


        $order_type = '';
        $is_return = false;

        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);


        if($doc_id == 67)
        {
            $order_type = 'Quotation';
        }
        else
        {
            $order_type = 'Sales Order';
            $is_return = $quotationMaster->is_return;
        }

        
        if ($quotationMaster->manuallyClosed == 1) {
            return $this->sendError('This '.$order_type.' already manually closed');
        }

        if ($quotationMaster->cancelledYN == -1) {
            return $this->sendError('This '.$order_type.' already cancelled');
        }
      
        if($doc_id == 67)
        {
           
            $sales_order = QuotationDetails::where('soQuotationMasterID','=',$id)->count();
            if ($sales_order > 0) {
                return $this->sendError('Quotation  added to sales order');
            }
        }   


        if(!$is_return)
        {
            $delivery_order = DeliveryOrderDetail::where('quotationMasterID','=',$id)->count();
            if ($delivery_order > 0) {
                return $this->sendError($order_type.' added to delivery order');
            }
    
            $invoice = CustomerInvoiceItemDetails::where('quotationMasterID','=',$id)->count();
            if ($invoice > 0) {
                return $this->sendError($order_type.' added to invoice ');
            }
        }

        $msg = $order_type.' successfully canceled';
        
        $employee = \Helper::getEmployeeInfo();

        $quotationMaster->cancelledYN =-1;
        $quotationMaster->cancelledByEmpID = $employee->empID;
        $quotationMaster->manuallyClosedByEmpSystemID = $employee->employeeSystemID;
        $quotationMaster->cancelledByEmpName = $employee->empName;
        $quotationMaster->cancelledComments = $comment;
        $quotationMaster->cancelledDate =  now();
        $quotationMaster->save();

        return $this->sendResponse($quotationMaster, $msg);

    }


    public function closeQuatation(Request $request)
    {
       
     
        $input = $request->all();
        $id = $input['quotationMasterID'];
        $comment = $input['closeComments'];

        $doc_id = $input['documentSystemID'];


        $orderStatus = $input['orderStatus'];
        $invoiceStatus = $input['invoiceStatus'];
        $deliveryStatus = $input['deliveryStatus'];


        $order_type = '';
        if($doc_id == 67)
        {
            $order_type = 'Quotation';
        }   
        else
        {
            $order_type = 'Sales Order';
        }

        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if ($quotationMaster->cancelledYN == -1) {
            return $this->sendError('This '.$order_type.' already cancelled');
        }

        if ($quotationMaster->manuallyClosed == 1) {
            return $this->sendError('This '.$order_type.' already manually closed');
        }




        $is_partially_added = false;

        if($orderStatus == 1)
        {
            $is_partially_added = true;
        }

        if($invoiceStatus == 1)
        {
            $is_partially_added = true;
        }

        if($deliveryStatus == 1)
        {
            $is_partially_added = true;
            
        }


        if(!$is_partially_added)
        {
            return $this->sendError($order_type.' cannot be closed, not partially added to any orders');
        }   
        
  

        $employee = \Helper::getEmployeeInfo();

        $quotationMaster->manuallyClosed =1;
        $quotationMaster->manuallyClosedByEmpID = $employee->empID;
        $quotationMaster->manuallyClosedByEmpSystemID = $employee->employeeSystemID;
        $quotationMaster->manuallyClosedByEmpName = $employee->empName;
        $quotationMaster->manuallyClosedComment = $comment;
        $quotationMaster->manuallyClosedDate =  now();
        $quotationMaster->save();

        return $this->sendResponse($quotationMaster, $order_type.' successfully closed');

    }


    public function checkItemExists(Request $request) {
        $input = $request->all();
        $count = 0;
        $quotationRequestedCount = QuotationDetails::where('quotationMasterID',$input['quotationMasterID'])->where('itemAutoID',$input['itemAutoID'])->sum('requestedQty');
        
        if($input['doc'] == "SO") {
            $master = QuotationDetails::where('soQuotationMasterID',$input['soQuotationMasterID'])->where('itemAutoID',$input['itemAutoID'])->first();
            $count = QuotationDetails::where('soQuotationMasterID',$input['quotationMasterID'])->where('itemAutoID',$input['itemAutoID'])->sum('requestedQty');
        }else if($input['doc'] == "DO") {
            $master = DeliveryOrderDetail::where('quotationMasterID',$input['soQuotationMasterID'])->where('itemCodeSystem',$input['itemAutoID'])->first();
            // $count = DeliveryOrderDetail::where('quotationMasterID',$input['quotationMasterID'])->where('itemCodeSystem',$input['itemAutoID'])->sum('qtyIssued');

        }else {
            $master = CustomerInvoiceItemDetails::where('quotationMasterID',$input['quotationMasterID'])->where('itemCodeSystem',$input['itemAutoID'])->first();
            $count = CustomerInvoiceItemDetails::where('quotationMasterID',$input['quotationMasterID'])->where('itemCodeSystem',$input['itemAutoID'])->sum('qtyIssued');
        }

        if($count !=0 && $quotationRequestedCount != $count) {
            $input['requestedQty'] = ($quotationRequestedCount - $count);
            $input['qtyIssued'] = ($quotationRequestedCount - $count);
            $input['qtyIssuedDefaultMeasure'] = ($quotationRequestedCount - $count);
            return $this->sendResponse(['status' => false , 'data' => $input],'False');
        }

        if(isset($master)) {
                return $this->sendResponse(['status' => true , 'data' => $input],'success');

        }else {
                return $this->sendResponse(['status' => false , 'data' => $input],'False');

        }
    }

    public function downloadQuotationItemUploadTemplate(Request $request) {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
        if ($exists = Storage::disk($disk)->exists('quotation_template/quotation_template.xlsx')) {
            return Storage::disk($disk)->download('quotation_template/quotation_template.xlsx', 'template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    public function poItemsUpload(Request $request) {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $excelUpload = $input['itemExcelUpload'];
            $input = array_except($request->all(), 'itemExcelUpload');
            $input = $this->convertArrayToValue($input);

            $decodeFile = base64_decode($excelUpload[0]['file']);
            $originalFileName = $excelUpload[0]['filename'];
            $extension = $excelUpload[0]['filetype'];
            $size = $excelUpload[0]['size'];


            $masterData = QuotationMaster::find($input['requestID']);


            if (empty($masterData)) {
                return $this->sendError('Quotation not found', 500);
            }


            $allowedExtensions = ['xlsx','xls'];

            if (!in_array($extension, $allowedExtensions))
            {
                return $this->sendError('This type of file not allow to upload.you can only upload .xlsx (or) .xls',500);
            }

            if ($size > 20000000) {
                return $this->sendError('The maximum size allow to upload is 20 MB',500);
            }

            $disk = 'local';
            Storage::disk($disk)->put($originalFileName, $decodeFile);

            $finalData = [];
            $formatChk = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->get()->toArray();

            $totalRecords = count(collect($formatChk)->toArray());

            $uniqueData = array_filter(collect($formatChk)->toArray());
            $uniqueData = collect($uniqueData)->unique('item_code')->toArray();
            $validateHeaderCode = false;
            $validateHeaderQty = false;
            $validateHeaderPrice = false;
            $validateVat = false;
            $totalItemCount = 0;

            $allowItemToTypePolicy = false;
            $itemNotound = false;
            // $allowItemToType = CompanyPolicyMaster::where('companyPolicyCategoryID', 64)
            //                                     ->where('companySystemID', $purchaseOrder->companySystemID)
            //                                     ->first();

            // if ($allowItemToType) {
            //     if ($allowItemToType->isYesNO) {
            //         $allowItemToTypePolicy = true;
            //     }
            // }.
            foreach ($uniqueData as $key => $value) {
                
                if(!array_key_exists('vat',$value) || !array_key_exists('item_code',$value) || !array_key_exists('sales_price',$value)  || !array_key_exists('qty',$value)) {
                     return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
                }

                if (isset($value['item_code'])) {
                    $validateHeaderCode = true;
                }

                if (isset($value['qty']) && ($value['qty']) && $value['qty'] != 0 ) {
                    $validateHeaderQty = true;
                }

                
                if (isset($value['sales_price']) && is_numeric($value['sales_price']) && $value['sales_price'] != 0) {
                    $validateHeaderPrice = true;
                }

                if($masterData->isVatEligible) {
                   if (isset($value['vat']) && is_numeric($value['vat'])) {
                        $validateVat = true;
                   }
                }else {
                    $validateVat = true;
                }

                if ($masterData->isVatEligible && (isset($value['vat']) && !is_null($value['vat'])) || (isset($value['item_code']) && !is_null($value['item_code'])) || isset($value['qty']) && !is_null($value['qty']) || isset($value['sales_price']) && !is_null($value['sales_price'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateHeaderCode || !$validateHeaderCode || !$validateVat) {
                return $this->sendError('Items cannot be uploaded, as there are null values found', 500);
            }


            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item_code', 'qty', 'sales_price','vat','discount','comments'))->get()->toArray();
            $uploadSerialNumber = array_filter(collect($record)->toArray());
            if ($masterData->cancelledYN == -1) {
                return $this->sendError('This Quotation already closed. You can not add.', 500);
            }

            if ($masterData->approvedYN == 1) {
                return $this->sendError('This Quotation fully approved. You can not add.', 500);
            }

            $finalArray = [];
            $count = 0;
            $uniqueData =  collect($uniqueData)->unique('item_code')->toArray();

            foreach($uniqueData as $finalRecords) {
                 if((is_numeric($finalRecords['qty']) && $finalRecords['qty'] != 0)  &&  (is_numeric($finalRecords['sales_price']) && $finalRecords['sales_price'] != 0) &&  (is_numeric($finalRecords['discount']) && $finalRecords['discount'] < $finalRecords['sales_price']) && (is_numeric($finalRecords['vat']) && $finalRecords['vat'] <= 100)) {
                     $exists_item = QuotationDetails::where('quotationMasterID',$masterData->quotationMasterID)->where('itemSystemCode',$finalRecords['item_code'])->first();
 
                     if(!$exists_item) {
                     $count++;
                    array_push($finalArray,$finalRecords);
                    }

                }           
            }



            if (count($record) > 0) {
                $db = isset($input['db']) ? $input['db'] : ""; 
                AddMultipleItemsToQuotation::dispatch(array_filter($finalArray),($masterData->toArray()),$db,Auth::id());
            } else {
                return $this->sendError('No Records found!', 500);
            }

            DB::commit();
            return $this->sendResponse([], 'Out of '.$totalRecords.', '.$count.'Items uploaded Successfully!!');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }
    
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesReturnAPIRequest;
use App\Http\Requests\API\UpdateSalesReturnAPIRequest;
use App\Models\ChartOfAccountsAssigned;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SalesReturn;
use App\Models\ItemMaster;
use App\Models\Company;
use App\Models\CompanyDocumentAttachment;
use App\Models\SalesReturnRefferedBack;
use App\Models\CompanyFinancePeriod;
use App\Models\SalesReturnDetailRefferedBack;
use App\Models\DeliveryOrder;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\SegmentMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\DeliveryOrderDetail;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\SalesReturnDetail;
use App\Models\DocumentMaster;
use App\Models\CustomerMaster;
use App\Models\DocumentReferedHistory;
use App\Models\DocumentApproved;
use App\Models\Taxdetail;
use App\Models\EmployeesDepartment;
use App\Repositories\SalesReturnRepository;
use App\Traits\AuditTrial;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use App\helper\inventory;
use App\helper\Helper;
use App\helper\TaxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\helper\ItemTracking;

/**
 * Class SalesReturnController
 * @package App\Http\Controllers\API
 */

class SalesReturnAPIController extends AppBaseController
{
    /** @var  SalesReturnRepository */
    private $salesReturnRepository;

    public function __construct(SalesReturnRepository $salesReturnRepo)
    {
        $this->salesReturnRepository = $salesReturnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturns",
     *      summary="Get a listing of the SalesReturns.",
     *      tags={"SalesReturn"},
     *      description="Get all SalesReturns",
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
     *                  @SWG\Items(ref="#/definitions/SalesReturn")
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
        $this->salesReturnRepository->pushCriteria(new RequestCriteria($request));
        $this->salesReturnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesReturns = $this->salesReturnRepository->all();

        return $this->sendResponse($salesReturns->toArray(), trans('custom.sales_returns_retrieved_successfully'));
    }

    /**
     * @param CreateSalesReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesReturns",
     *      summary="Store a newly created SalesReturn in storage",
     *      tags={"SalesReturn"},
     *      description="Store SalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturn that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturn")
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
     *                  ref="#/definitions/SalesReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $messages = [
            'transactionCurrencyID.required' => 'Currency field is required',
            'customerID.required' => 'Customer field is required',
            'companyFinanceYearID.required' => 'Finance Year field is required',
            'companyFinancePeriodID.required' => 'Finance Period field is required',
            'serviceLineSystemID.required' => 'Segment field is required',
            'wareHouseSystemCode.required' => 'Warehouse field is required',
            'salesReturnDate.required' => 'Document Date field is required',
        ];

        $validator = \Validator::make($input, [
            'returnType' => 'required|numeric|min:1',
            'companySystemID' => 'required|numeric|min:1',
            'documentSystemID' => 'required|numeric|min:1',
            'customerID' => 'required',
            'transactionCurrencyID' => 'required',
            'companyFinanceYearID' => 'required',
            'companyFinancePeriodID' => 'required',
            'serviceLineSystemID' => 'required',
            'wareHouseSystemCode' => 'required',
            'salesReturnDate' => 'required|date'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['salesReturnDate'] = new Carbon($input['salesReturnDate']);

        $input = $this->convertArrayToSelectedValue($input, array('transactionCurrencyID','companyFinancePeriodID','companyFinanceYearID'));

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;

        $customer = CustomerMaster::find($input['customerID']);
        if(empty($customer)){
            return $this->sendError(trans('custom.selected_customer_not_found_on_db'),500);
        }

        if(!$customer->custGLAccountSystemID){
            return $this->sendError('GL account is not configured for this customer',500);
        }

        if(!$customer->custUnbilledAccountSystemID){
            return $this->sendError('Unbilled receivable account is not configured for this customer',500);
        }

        $input['custGLAccountSystemID'] = $customer->custGLAccountSystemID;
        $input['custGLAccountCode'] = $customer->custGLaccount;
        $input['custUnbilledAccountSystemID'] = $customer->custUnbilledAccountSystemID;
        $input['custUnbilledAccountCode'] = $customer->custUnbilledAccount;

        $lastSerial = SalesReturn::where('companySystemID', $input['companySystemID'])
                                ->where('companyFinanceYearID', $input['companyFinanceYearID'])
                                ->orderBy('serialNo', 'desc')
                                ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }
        $company = Company::where('companySystemID', $input['companySystemID'])->first()->toArray();
        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $input['salesReturnCode'] = ($company['CompanyID'] . '\\' . $y . '\\SLR' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['serialNo'] = $lastSerialNumber;
        $input['companyID'] = $company['CompanyID'];
        $input['documentID'] = 'SLR';

        if(isset($input['serviceLineSystemID']) && $input['serviceLineSystemID']){
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;

        // check date within financial period
        if (!(($input['salesReturnDate'] >= $input['FYPeriodDateFrom']) && ($input['salesReturnDate'] <= $input['FYPeriodDateTo']))) {
            return $this->sendError('Document date should be between financial period start date and end date',500);
        }

        $companyCurrency = Helper::companyCurrency($input['companySystemID']);
        $companyCurrencyConversion = Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $input['transactionCurrencyID'], 0);

        $input['transactionCurrencyER'] = 1;
        $input['companyLocalCurrencyID'] = $companyCurrency->localcurrency->currencyID;
        $input['companyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
        $input['companyReportingCurrencyER'] = $companyCurrencyConversion['trasToRptER'];

        $employee = Helper::getEmployeeInfo();
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;

        $salesReturn = $this->salesReturnRepository->create($input);

        return $this->sendResponse($salesReturn->toArray(), trans('custom.sales_return_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturns/{id}",
     *      summary="Display the specified SalesReturn",
     *      tags={"SalesReturn"},
     *      description="Get SalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturn",
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
     *                  ref="#/definitions/SalesReturn"
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
        /** @var SalesReturn $salesReturn */

        $salesReturn = $this->salesReturnRepository->with(['tax','customer','transaction_currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'detail' => function($query){
            $query->with(['delivery_order','uom_default', 'delivery_order_detail', 'sales_invoice', 'item_by']);
        },'segment','warehouse'])->findWithoutFail($id);

        if (empty($salesReturn)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        return $this->sendResponse($salesReturn->toArray(), trans('custom.sales_return_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalesReturnAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesReturns/{id}",
     *      summary="Update the specified SalesReturn in storage",
     *      tags={"SalesReturn"},
     *      description="Update SalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturn",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturn that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturn")
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
     *                  ref="#/definitions/SalesReturn"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, Request $request)
    {
        $input = $request->all();
  
        $salesReturn = $this->salesReturnRepository->findWithoutFail($id);

        if (empty($salesReturn)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }
        $input = $this->convertArrayToSelectedValue($input, array('transactionCurrencyID','confirmedYN','customerID','returnType','salesPersonID','serviceLineSystemID','wareHouseSystemCode','companyFinancePeriodID'));
        $input = array_except($input,['finance_period_by','finance_year_by','transaction_currency','customer','detail','segment','warehouse']);

        if($salesReturn->transactionCurrencyID != $input['transactionCurrencyID']){
            $companyCurrency = Helper::companyCurrency($input['companySystemID']);
            $companyCurrencyConversion = Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $input['transactionCurrencyID'], 0);

            $input['transactionCurrencyER'] = 1;
            $input['companyLocalCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $input['companyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $input['companyReportingCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        }

        $input['documentID'] = 'SLR';

        if($salesReturn->serviceLineSystemID != $input['serviceLineSystemID']){
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        $input['salesReturnDate'] = Carbon::parse($input['salesReturnDate'])->format('Y-m-d') . ' 00:00:00';

        $detailAmount = SalesReturnDetail::
        select(DB::raw("
        IFNULL(SUM(qtyReturnedDefaultMeasure * (unitTransactionAmount-discountAmount)),0) as transAmount,
        IFNULL(SUM(qtyReturnedDefaultMeasure * (companyLocalAmount-(companyLocalAmount*discountPercentage/100))),0) as localAmount,
        IFNULL(SUM(qtyReturnedDefaultMeasure * (companyReportingAmount-(companyReportingAmount*discountPercentage/100))),0) as reportAmount"))
            ->where('salesReturnID', $id)
            ->first();

        $input['transactionAmount'] = $detailAmount->transAmount;
        $input['companyLocalAmount'] = $detailAmount->localAmount;
        $input['companyReportingAmount'] = $detailAmount->reportAmount;

        $input['transactionAmount'] = Helper::roundValue($input['transactionAmount']);
        $input['companyLocalAmount'] = Helper::roundValue($input['companyLocalAmount']);
        $input['companyReportingAmount'] = Helper::roundValue($input['companyReportingAmount']);

        $employee = Helper::getEmployeeInfo();
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;
        $input['modifiedDateTime'] = Carbon::now();

        // check gl account is configure for new customer
        if(!empty($input['customerID'])){
            if($input['customerID'] != $salesReturn->customerID){
                // check customer master unbilled gl account configured
                $customer = CustomerMaster::find($input['customerID']);

                if(!$customer->custGLAccountSystemID){
                    return $this->sendError('GL account is not configured for this customer',500);
                }

                if(!$customer->custUnbilledAccountSystemID){
                    return $this->sendError('Unbilled receivable account is not configured for this customer',500);
                }

                $input['custGLAccountSystemID'] = $customer->custGLAccountSystemID;
                $input['custGLAccountCode'] = $customer->custGLaccount;
                $input['custUnbilledAccountSystemID'] = $customer->custUnbilledAccountSystemID;
                $input['custUnbilledAccountCode'] = $customer->custUnbilledAccount;

            }
        }

        DB::beginTransaction();
        try {
            if ($input['confirmedYN'] == 1 && $salesReturn->confirmedYN == 0) {

                // check document date between financial period
                if (($input['salesReturnDate'] >= $input['FYPeriodDateFrom']) && ($input['salesReturnDate'] <= $input['FYPeriodDateTo'])) {
                } else {
                    return $this->sendError('Document date should be between the selected financial period start date and end date.', 500);
                }

                $messages = [
                    'transactionCurrencyID.required' => 'Currency field is required',
                    'customerID.required' => 'Customer field is required',
                    'companyFinanceYearID.required' => 'Finance Year field is required',
                    'companyFinancePeriodID.required' => 'Finance Period field is required',
                    'serviceLineSystemID.required' => 'Segment field is required',
                    'wareHouseSystemCode.required' => 'Warehouse field is required',
                    'salesReturnDate.required' => 'Document Date field is required',
                ];

                $validator = \Validator::make($input, [
                    'returnType' => 'required|numeric|min:1',
                    'companySystemID' => 'required|numeric|min:1',
                    'documentSystemID' => 'required|numeric|min:1',
                    'customerID' => 'required',
                    'transactionCurrencyID' => 'required',
                    'companyFinanceYearID' => 'required',
                    'companyFinancePeriodID' => 'required',
                    'serviceLineSystemID' => 'required',
                    'wareHouseSystemCode' => 'required',
                    'salesReturnDate' => 'required|date'
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }


                $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($salesReturn->documentSystemID, $id);

                if (!$trackingValidation['status']) {
                    return $this->sendError($trackingValidation["message"], 500, ['type' => 'confirm']);
                }

                // check customer master unbilled gl account configured
                $customer = CustomerMaster::find($input['customerID']);
                if(!empty($customer) && !$customer->custUnbilledAccountSystemID){
                    return $this->sendError('Unbilled receivable account is not configured for this customer', 500);
                }
                $input['custGLAccountSystemID'] = $customer->custGLAccountSystemID;
                $input['custGLAccountCode'] = $customer->custGLaccount;
                $input['custUnbilledAccountSystemID'] = $customer->custUnbilledAccountSystemID;
                $input['custUnbilledAccountCode'] = $customer->custUnbilledAccount;


                $detail = SalesReturnDetail::where('salesReturnID', $id)->get();
                if(count((array)$detail) == 0){
                    return  $this->sendError(trans('custom.return_detail_not_found'), 500);
                }

                $checkQuantity = SalesReturnDetail::where('salesReturnID', $id)
                    ->where(function ($q) {
                        $q->where('qtyReturned', '<=', 0)
                            ->orWhereNull('qtyReturned');
                    })
                    ->exists();
                if ($checkQuantity) {
                    return $this->sendError('Every Item should have at least one minimum Qty Returned', 500);
                }

                foreach ($detail as $item) {
                    
                    $quotation_detail_id = DeliveryOrderDetail::where('deliveryOrderDetailID',$item->deliveryOrderDetailID)->select('quotationDetailsID')->first();
                    if(isset($quotation_detail_id))
                    {
                        $return_quantity = DeliveryOrderDetail::where('deliveryOrderDetailID',$item->deliveryOrderDetailID)->sum('returnQty');
                        $do_qua = QuotationDetails::where('quotationDetailsID', $quotation_detail_id->quotationDetailsID)->select('doQuantity')->first();
                        
                        if(isset($do_qua))
                        {
                            if($do_qua->doQuantity == $return_quantity)
                            {
                                $update = QuotationDetails::where('quotationDetailsID', $quotation_detail_id->quotationDetailsID)
                                 ->update(['fullyOrdered' => 0]);
                            }
                            else
                            {
                                $update = QuotationDetails::where('quotationDetailsID', $quotation_detail_id->quotationDetailsID)
                                ->update(['fullyOrdered' => 1]);
                            }
                        }
                 
                    }

                    $chartOfAccountAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$item->reasonGLCode)->where('companySystemID',$salesReturn->companySystemID)->where('isActive', 1)->where('isAssigned', -1)->first();
                    if(empty($chartOfAccountAssigned) && $item->reasonGLCode != null && $item->isPostItemLedger == 0 && $item->reasonCode != null){
                        return $this->sendError($item->itemPrimaryCode.'-'.'Reason Code Master GL Code is not assigned to the company ', 500);
                    }
          
     


                    //If the revenue account or cost account or BS account is null do not allow to confirm
                    if(!($item->financeGLcodebBSSystemID > 0)){
                        return $this->sendError(trans('custom.bs_account_cannot_be_null_for').$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                    }elseif (!($item->financeGLcodePLSystemID > 0)){
                        return $this->sendError(trans('custom.cost_account_cannot_be_null_for').$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                    }elseif (!($item->financeCogsGLcodePLSystemID > 0)){
                        return $this->sendError(trans('custom.cogs_gl_account_cannot_be_null_for').$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                    }elseif (!($item->financeGLcodeRevenueSystemID > 0)){
                        return $this->sendError(trans('custom.revenue_account_cannot_be_null_for').$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                    }

                    $updateItem = SalesReturnDetail::find($item['salesReturnDetailID']);

                    $data = array(
                        'companySystemID' => $salesReturn->companySystemID,
                        'itemCodeSystem' => $item->itemCodeSystem,
                        'wareHouseId' => $salesReturn->wareHouseSystemCode
                    );

                    $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                    $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                    $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                    $updateItem->currentStockQtyInDamageReturn = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];

                    // $updateItem->wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
                    // $updateItem->wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];

                    //discount calculation
                    $discountedUnit = $updateItem->unitTransactionAmount;

                    if($updateItem->discountAmount > 0) {
                        $discountedUnit = $updateItem->unitTransactionAmount - $updateItem->discountAmount;
                    }

                    $updateItem->transactionAmount = $discountedUnit * $updateItem->qtyReturnedDefaultMeasure;

                    $currencyConversion = Helper::currencyConversion($salesReturn->companySystemID,$updateItem->transactionCurrencyID,$updateItem->transactionCurrencyID,$updateItem->transactionAmount);
                    if(!empty($currencyConversion)){
                        $updateItem->companyLocalAmount = $currencyConversion['localAmount'];
                    }

                    $currencyConversion = Helper::currencyConversion($salesReturn->companySystemID,$updateItem->transactionCurrencyID,$updateItem->transactionCurrencyID,$updateItem->transactionAmount);
                    if(!empty($currencyConversion)){
                        $updateItem->companyReportingAmount = $currencyConversion['reportingAmount'];
                    }

                    $updateItem->unitTransactionAmount = Helper::roundValue($updateItem->unitTransactionAmount);
                    $updateItem->discountPercentage = Helper::roundValue($updateItem->discountPercentage);
                    $updateItem->discountAmount = Helper::roundValue($updateItem->discountAmount);
                    $updateItem->transactionAmount = Helper::roundValue($updateItem->transactionAmount);
                    $updateItem->companyLocalAmount = Helper::roundValue($updateItem->companyLocalAmount);
                    $updateItem->companyReportingAmount = Helper::roundValue($updateItem->companyReportingAmount);

                    $updateItem->save();


                    if ($updateItem->unitTransactionAmount < 0) {
                        return $this->sendError('Item must not have negative cost', 500);
                    }
                }

                if($updateItem->discountPercentage != 0){
                    $amount = SalesReturnDetail::where('salesReturnID', $id)
                        ->sum(DB::raw('qtyReturnedDefaultMeasure * (companyReportingAmount-(companyReportingAmount*discountPercentage/100))'));
                }else{
                    $amount = SalesReturnDetail::where('salesReturnID', $id)
                        ->sum(DB::raw('qtyReturnedDefaultMeasure * companyReportingAmount'));
                }


                //check Input Vat Transfer GL Account if vat exist
                $totalVAT = $salesReturn->VATAmount;
                if(TaxService::checkCompanyVATEligible($salesReturn->companySystemID) && $totalVAT > 0){
                    if ($salesReturn->returnType == 2) {
                        if(empty(TaxService::getOutputVATGLAccount($salesReturn->companySystemID))){
                            return $this->sendError(trans('custom.cannot_confirm_output_vat_control_gl_account_not_c'), 500);
                        }
                    } else {
                         $invoiceDetails = SalesReturnDetail::selectRaw('SUM(transactionAmount) as amount, deliveryOrderID, salesReturnID')
                                                    ->where('salesReturnID', $id)
                                                    ->with(['delivery_order'])
                                                    ->groupBy('deliveryOrderID')
                                                    ->get();

                        foreach ($invoiceDetails as $key => $value) {
                            if (isset($value->delivery_order->selectedForCustomerInvoice) && $value->delivery_order->selectedForCustomerInvoice == -1) {
                                if(empty(TaxService::getOutputVATGLAccount($salesReturn->companySystemID))){
                                    return $this->sendError(trans('custom.cannot_confirm_output_vat_control_gl_account_not_c'), 500);
                                }
                            } else {
                                if(empty(TaxService::getOutputVATTransferGLAccount($salesReturn->companySystemID))){
                                    return $this->sendError(trans('custom.cannot_confirm_output_vat_transfer_gl_account_not_'), 500);
                                }
                            }
                        }
                    }
                }

                $params = array(
                    'autoID' => $id,
                    'company' => $salesReturn->companySystemID,
                    'document' => $salesReturn->documentSystemID,
                    'segment' => '',
                    'category' => '',
                    'amount' => $amount
                );
                $update = array_except($input,['confirmedYN']);
                $salesReturn = $this->salesReturnRepository->update($update, $id);
                $confirm = Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"], 500);
                } else {
                    DB::commit();
                    return $this->sendReponseWithDetails($salesReturn->toArray(), 'Sales Return confirmed successfully',1,$confirm['data'] ?? null);
                }

            }else{
                $salesReturn = $this->salesReturnRepository->update($input, $id);
                DB::commit();
                return $this->sendResponse($salesReturn->toArray(), trans('custom.sales_return_updated_successfully'));
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesReturns/{id}",
     *      summary="Remove the specified SalesReturn from storage",
     *      tags={"SalesReturn"},
     *      description="Delete SalesReturn",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturn",
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
        /** @var SalesReturn $salesReturn */
        $salesReturn = $this->salesReturnRepository->findWithoutFail($id);

        if (empty($salesReturn)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        $salesReturn->delete();

        return $this->sendSuccess('Sales Return deleted successfully');
    }


    public function getAllSalesReturn(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $salesReturn = $this->salesReturnRepository->salesReturnListQuery($request, $input, $search);

        return \DataTables::eloquent($salesReturn)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function deliveryNoteForForSR(Request $request){
        $input = $request->all();
        $documentSystemID = 0;
        $salesReturn = SalesReturn::find($input['salesReturnID']);
        if($input['type'] == 1){ //Delivery Order
            $master = DeliveryOrder::where('erp_delivery_order.companySystemID',$input['companySystemID'])
                                    ->where('erp_delivery_order.approvedYN', -1)
                                    ->where('erp_delivery_order.selectedForSalesReturn', 0)
                                    // ->where('closedYN',0)
                                    ->where('erp_delivery_order.serviceLineSystemID', $salesReturn->serviceLineSystemID)
                                    ->where('erp_delivery_order.wareHouseSystemCode', $salesReturn->wareHouseSystemCode)
                                    ->where('erp_delivery_order.customerID', $salesReturn->customerID)
                                    ->where('erp_delivery_order.transactionCurrencyID', $salesReturn->transactionCurrencyID)
                                    ->whereDate("erp_delivery_order.postedDate", '<=', $salesReturn->salesReturnDate)
                                    ->leftJoin('erp_customerinvoiceitemdetails', 'erp_delivery_order.deliveryOrderID', '=', 'erp_customerinvoiceitemdetails.deliveryOrderID')
                                    ->leftJoin('erp_custreceivepaymentdet', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custreceivepaymentdet.bookingInvCodeSystem')
                                    ->whereNull('erp_custreceivepaymentdet.bookingInvCodeSystem')
                                    ->orderBy('erp_delivery_order.deliveryOrderID','DESC')
                                    ->groupBy('erp_delivery_order.deliveryOrderID')
                                    ->select('erp_delivery_order.*')
                                    ->get();
        } elseif ($input['type']==2){ ////sales Invoice
            $master = CustomerInvoiceDirect::where('erp_custinvoicedirect.companySystemID',$input['companySystemID'])
                                    ->where('erp_custinvoicedirect.approved', -1)
                                    ->where('erp_custinvoicedirect.isPOS', 0)
                                    ->where('erp_custinvoicedirect.selectedForSalesReturn', 0)
                                    ->whereIn('erp_custinvoicedirect.isPerforma', [2,4,5])
                                    // ->where('closedYN',0)
                                    ->where('erp_custinvoicedirect.serviceLineSystemID', $salesReturn->serviceLineSystemID)
                                    ->where('erp_custinvoicedirect.wareHouseSystemCode', $salesReturn->wareHouseSystemCode)
                                    ->where('erp_custinvoicedirect.customerID', $salesReturn->customerID)
                                    ->where('erp_custinvoicedirect.custTransactionCurrencyID', $salesReturn->transactionCurrencyID)
                                    ->whereDate("erp_custinvoicedirect.postedDate", '<=', $salesReturn->salesReturnDate)
                                    ->leftJoin('erp_custreceivepaymentdet', 'erp_custinvoicedirect.custInvoiceDirectAutoID', '=', 'erp_custreceivepaymentdet.bookingInvCodeSystem')
                                    ->whereNull('erp_custreceivepaymentdet.bookingInvCodeSystem')
                                    ->orderBy('erp_custinvoicedirect.custInvoiceDirectAutoID','DESC')
                                    ->select('erp_custinvoicedirect.*')
                                    ->get();
        }

        return $this->sendResponse($master->toArray(), trans('custom.quotations_retrieved_successfully'));
    }


    public function getSalesInvoiceDeliveryOrderDetail(Request $request){
        $input = $request->all();
        $id = $input['documentAutoID'];


        if ($input['type'] == 1) {
             $detail = DB::select('SELECT
                                dodetail.*,
                                erp_delivery_order.serviceLineSystemID,
                                "" AS isChecked,
                                "" AS noQty,
                                IFNULL(sum(rtnDetails.rtnTakenQty),0) as rtnTakenQty 
                            FROM
                                erp_delivery_order_detail dodetail
                                INNER JOIN erp_delivery_order ON dodetail.deliveryOrderID = erp_delivery_order.deliveryOrderID
                                LEFT JOIN ( SELECT salesreturndetails.salesReturnDetailID,deliveryOrderDetailID, SUM( qtyReturnedDefaultMeasure ) AS rtnTakenQty FROM salesreturndetails GROUP BY salesReturnDetailID, itemCodeSystem ) AS rtnDetails ON dodetail.deliveryOrderDetailID = rtnDetails.deliveryOrderDetailID 
                            WHERE
                                dodetail.deliveryOrderID = ' . $id . ' 
                                AND fullyReturned != 2
                                GROUP BY dodetail.deliveryOrderDetailID');

        } else {
            $detail = DB::select('SELECT
                                invDetails.*,
                                erp_custinvoicedirect.serviceLineSystemID,
                                "" AS isChecked,
                                "" AS noQty,
                                IFNULL(sum(rtnDetails.rtnTakenQty),0) as rtnTakenQty 
                            FROM
                                erp_customerinvoiceitemdetails invDetails
                                INNER JOIN erp_custinvoicedirect ON invDetails.custInvoiceDirectAutoID = erp_custinvoicedirect.custInvoiceDirectAutoID
                                LEFT JOIN ( SELECT salesreturndetails.salesReturnDetailID,customerItemDetailID, SUM( qtyReturnedDefaultMeasure ) AS rtnTakenQty FROM salesreturndetails GROUP BY salesReturnDetailID, itemCodeSystem ) AS rtnDetails ON invDetails.customerItemDetailID = rtnDetails.customerItemDetailID 
                            WHERE
                                invDetails.custInvoiceDirectAutoID = ' . $id . ' 
                                AND fullyReturned != 2
                                GROUP BY invDetails.customerItemDetailID');
        }
       
        return $this->sendResponse($detail, trans('custom.delivery_order_details_retrieved_successfully'));
    }


    public function storeReturnDetailFromSIDO(Request $request)
    {
      
        $input = $request->all();

        if ($input['type'] == 2) {
            return $this->storeReturnDetailFromSalesInvoice($input);
        }

        $invDetail_arr = array();
        $validator = array();
        $salesReturnID = $input['salesReturnID'];

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => 'Return quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if($newValidation['noQty'] == 0){
                    return $this->sendError('Return Quantity should be greater than zero', 500);
                }
            }
        }

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $doDetailExist = SalesReturnDetail::select(DB::raw('itemPrimaryCode'))
                                                ->where('salesReturnID', $salesReturnID)
                                                ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
                                                ->get();

                if (!empty($doDetailExist)) {
                    foreach ($doDetailExist as $row) {
                        $itemDrt = $row['itemPrimaryCode'] . " is already added";
                        $itemExistArray[] = [$itemDrt];
                    }
                }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

        $salesReturn = SalesReturn::where('id', $salesReturnID)->first();

        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {

                $deliveryOrder = DeliveryOrder::find($itemExist['deliveryOrderID']);

                if($deliveryOrder->serviceLineSystemID != $salesReturn->serviceLineSystemID){
                    // return $this->sendError("Segment is different from order");
                }
            }
        }


        $checkOtherPrns = SalesReturnDetail::with(['master' => function ($query) {
                                                    $query->where('approvedYN', 0);
                                               }])
                                               ->where('salesReturnID','!=', $input['salesReturnID'])
                                               ->where('deliveryOrderID', $input['documentAutoID'])
                                               ->whereHas('master', function($query) {
                                                    $query->where('approvedYN', 0);
                                               })
                                               ->first();

        if ($checkOtherPrns) {
            return $this->sendError("There is a Sales Return (" . $checkOtherPrns->master->salesReturnCode . ") pending for approval for the Delivery Order you are trying to add. Please check again.", 500);
        }


        DB::beginTransaction();
        try {

            

            foreach ($input['detailTable'] as $new) {



                $deliveryOrder = DeliveryOrder::find($new['deliveryOrderID']);

                $doDetailExist = SalesReturnDetail::select(DB::raw('salesReturnDetailID'))
                                                ->where('salesReturnID', $salesReturnID)
                                                ->where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                                                ->first();

                if (empty($doDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in delivery order
                        $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalNoQty'))
                                                    ->where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                                                    ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['qtyIssuedDefaultMeasure'] == $totalAddedQty) {
                            $fullyReturned = 2;
                            $closedYN = -1;
                            $selectedForSalesReturn= -1;
                        } else {
                            $fullyReturned = 1;
                            $closedYN = 0;
                            $selectedForSalesReturn = 0;
                        }

                        // checking the qty request is matching with sum total
                        if ($new['qtyIssuedDefaultMeasure'] >= $new['noQty']) {

                            $invDetail_arr['salesReturnID'] = $salesReturnID;
                            $invDetail_arr['deliveryOrderID'] = $new['deliveryOrderID'];
                            $invDetail_arr['trackingType'] = $new['trackingType'];
                            $invDetail_arr['deliveryOrderDetailID'] = $new['deliveryOrderDetailID'];
                            $invDetail_arr['itemCodeSystem'] = $new['itemCodeSystem'];
                            $invDetail_arr['itemPrimaryCode'] = $new['itemPrimaryCode'];
                            $invDetail_arr['itemDescription'] = $new['itemDescription'];
                            $invDetail_arr['companySystemID'] = $new['companySystemID'];
                            $invDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                            $invDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];
                            $invDetail_arr['VATPercentage'] = $new['VATPercentage'];
                            $invDetail_arr['VATAmount'] = $new['VATAmount'];
                            $invDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                            $invDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                            $invDetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                            $invDetail_arr['documentSystemID'] = 87;

                            $item = ItemMaster::find($new['itemCodeSystem']);
                            if(empty($item)){
                                return $this->sendError(trans('custom.item_not_found'),500);
                            }

                            $data = array(
                                'companySystemID' => $deliveryOrder->companySystemID,
                                'itemCodeSystem' => $new['itemCodeSystem'],
                                'wareHouseId' => $deliveryOrder->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $invDetail_arr['doInvRemainingQty'] = floatval($new['qtyIssuedDefaultMeasure']) - floatval($new['rtnTakenQty']);
                            $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $invDetail_arr['wacValueLocal'] = $new['wacValueLocal'];
                            $invDetail_arr['wacValueReporting'] = $new['wacValueReporting'];
                            $invDetail_arr['convertionMeasureVal'] = 1;

                            $invDetail_arr['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                            $invDetail_arr['itemFinanceCategorySubID'] = $item->financeCategorySub;

                            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $new['companySystemID'])
                                                                                            ->where('mainItemCategoryID', $invDetail_arr['itemFinanceCategoryID'])
                                                                                            ->where('itemCategorySubID', $invDetail_arr['itemFinanceCategorySubID'])
                                                                                            ->first();

                            if (!empty($financeItemCategorySubAssigned)) {
                                $invDetail_arr['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                $invDetail_arr['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $invDetail_arr['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                $invDetail_arr['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                $invDetail_arr['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                                $invDetail_arr['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                                $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError("Account code not updated for ".$new['itemPrimaryCode'].".", 500);
                            }

                            if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                || !$invDetail_arr['financeCogsGLcodePL'] || !$invDetail_arr['financeCogsGLcodePLSystemID']
                                || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemPrimaryCode'].".", 500);
                            }


                            $invDetail_arr['transactionCurrencyID'] = $deliveryOrder->transactionCurrencyID;
                            $invDetail_arr['transactionCurrencyER'] = $deliveryOrder->transactionCurrencyER;
                            $invDetail_arr['companyLocalCurrencyID'] = $deliveryOrder->companyLocalCurrencyID;
                            $invDetail_arr['companyLocalCurrencyER'] = $deliveryOrder->companyLocalCurrencyER;
                            $invDetail_arr['companyReportingCurrencyID'] = $deliveryOrder->companyReportingCurrencyID;
                            $invDetail_arr['companyReportingCurrencyER'] = $deliveryOrder->companyReportingCurrencyER;

                            $invDetail_arr['itemUnitOfMeasure'] = $new['itemUnitOfMeasure'];
                            $invDetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureIssued'];
                            $invDetail_arr['qtyReturned'] = $new['noQty'];
                            $invDetail_arr['qtyReturnedDefaultMeasure'] = $new['noQty'];

                            $invDetail_arr['marginPercentage'] = 0;
                            if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                                $invDetail_arr['unitTransactionAmount'] = ($new['unitTransactionAmount']) - ($new['unitTransactionAmount']*$new['discountPercentage']/100);
                            }else{
                                $invDetail_arr['unitTransactionAmount'] = $new['unitTransactionAmount'];
                            }

                            $totalNetcost = ($new['unitTransactionAmount'] - $new['discountAmount']) * $new['noQty'];

                            $invDetail_arr['transactionAmount'] = \Helper::roundValue($totalNetcost);
                            $invDetail_arr['unitTransactionAmount'] = \Helper::roundValue($invDetail_arr['unitTransactionAmount']);
                            
                            $item = SalesReturnDetail::create($invDetail_arr);

                            $update = DeliveryOrderDetail::where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                                ->update(['fullyReturned' => $fullyReturned, 'returnQty' => $totalAddedQty]);
                        }

                        // fetching the total count records from purchase Request Details table
                        $doDetailTotalcount = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as detailCount'))
                                                                ->where('deliveryOrderID', $new['deliveryOrderID'])
                                                                ->first();

                        // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                        $doDetailExist = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as count'))
                                                            ->where('deliveryOrderID', $new['deliveryOrderID'])
                                                            ->where('fullyReturned', 2)
                                                            ->first();

                        // Updating PR Master Table After All Detail Table records updated
                        if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                            $updatedo = DeliveryOrder::find($new['deliveryOrderID'])
                                                    ->update(['selectedForSalesReturn' => -1, 'closedYN' => -1]);
                        }
                    }
                }

                //check all details fullyOrdered in DO Master
                $doMasterfullyOrdered = DeliveryOrderDetail::where('deliveryOrderID', $new['deliveryOrderID'])
                                                            ->whereIn('fullyReturned', [1, 0])
                                                            ->get()->toArray();

                if (empty($doMasterfullyOrdered)) {
                    DeliveryOrder::find($new['deliveryOrderID'])
                        ->update([
                            'selectedForSalesReturn' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    DeliveryOrder::find($new['deliveryOrderID'])
                        ->update([
                            'selectedForSalesReturn' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateDOReturnedStatus($new['deliveryOrderID']);

                $resVat = $this->updateVatOfSalesReturn($salesReturnID);


                $quotation_count = DeliveryOrderDetail::where('quotationMasterID', $new['quotationMasterID'])
                ->count();

                $quotation_returned = DeliveryOrderDetail::where('quotationMasterID', $new['quotationMasterID'])
                ->where('fullyReturned', 2)
                ->count();

                if($quotation_count == $quotation_returned)
                {
                    $QuotationMaster = QuotationMaster::find($new['quotationMasterID']);
                    $QuotationMaster->is_return = true;
                    $QuotationMaster->update();
                }
                

                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 
            }

             DB::commit();
            return $this->sendResponse([], trans('custom.sales_return_item_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }
        
    }

    public function updateVatOfSalesReturn($salesReturnID)
    {
        $salesReturnMasterData = SalesReturn::find($salesReturnID);

        $totalAmount = 0;
        $totalTaxAmount = 0;
        if ($salesReturnMasterData->returnType == 1) {
            $invoiceDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                                                ->with(['delivery_order_detail'])
                                                ->get();

            foreach ($invoiceDetails as $key => $value) {
                $totalTaxAmount += $value->qtyReturned * ((isset($value->delivery_order_detail->VATAmount) && !is_null($value->delivery_order_detail->VATAmount)) ? $value->delivery_order_detail->VATAmount : 0);
            }
        } else {
            $invoiceDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
                                                ->with(['sales_invoice_detail'])
                                                ->get();

            foreach ($invoiceDetails as $key => $value) {
                $totalTaxAmount += $value->qtyReturned * ((isset($value->sales_invoice_detail->VATAmount) && !is_null($value->sales_invoice_detail->VATAmount)) ? $value->sales_invoice_detail->VATAmount : 0);
            }
        }

        if ($totalTaxAmount > 0) {
            $taxDelete = Taxdetail::where('documentSystemCode', $salesReturnID)
                                  ->where('documentSystemID', 87)
                                  ->delete();

            $res = $this->saveSalesReturnTaxDetails($salesReturnID, $totalTaxAmount);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']]; 
            } 
        } else {
            $taxDelete = Taxdetail::where('documentSystemCode', $salesReturnID)
                                  ->where('documentSystemID', 87)
                                  ->delete();

            $vatAmount['vatOutputGLCodeSystemID'] = 0;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            SalesReturn::where('id', $salesReturnID)->update($vatAmount);

        }

        return ['status' => true];
    }


     public function saveSalesReturnTaxDetails($salesReturnID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = SalesReturn::where('id', $salesReturnID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Sales Return not found.'];
        }

        $invoiceDetail = SalesReturnDetail::where('salesReturnID', $salesReturnID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Sales Return Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->transactionCurrencyID);

        $totalDetail = SalesReturnDetail::select(DB::raw("SUM(transactionAmount) as amount"))
                                          ->where('salesReturnID', $salesReturnID)
                                          ->groupBy('salesReturnID')
                                          ->first();

        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $salesReturnID)
                                ->where('documentSystemID', 87)
                                ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->transactionCurrencyID, $master->transactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'SLR';
        $_post['documentSystemID'] = $master->documentSystemID;
        $_post['documentSystemCode'] = $salesReturnID;
        $_post['documentCode'] = $master->salesReturnCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->transactionCurrencyID;
        $_post['currencyER'] = $master->transactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->transactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->transactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->companyLocalCurrencyID;
        $_post['localCurrencyER'] = $master->companyLocalCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingCurrencyER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalVATAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalVATAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalVATAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalVATAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalVATAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalVATAmount / $_post['localCurrencyER']);
                }
            }
        }

        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);
       
        Taxdetail::create($_post);
        $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

        $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
        $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
        $vatAmount['VATPercentage'] = $percentage;
        $vatAmount['VATAmount'] = $_post['amount'];
        $vatAmount['VATAmountLocal'] = $_post["localAmount"];
        $vatAmount['VATAmountRpt'] = $_post["rptAmount"];

        SalesReturn::where('id', $salesReturnID)->update($vatAmount);

        return ['status' => true];
    }


    public function storeReturnDetailFromSalesInvoice($input)
    {
        $invDetail_arr = array();
        $validator = array();
        $salesReturnID = $input['salesReturnID'];

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => 'Return quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if($newValidation['noQty'] == 0){
                    return $this->sendError('Return Quantity should be greater than zero', 500);
                }
            }
        }

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $doDetailExist = SalesReturnDetail::select(DB::raw('itemPrimaryCode'))
                                                ->where('salesReturnID', $salesReturnID)
                                                ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
                                                ->get();

                if (!empty($doDetailExist)) {
                    foreach ($doDetailExist as $row) {
                        $itemDrt = $row['itemPrimaryCode'] . " is already added";
                        $itemExistArray[] = [$itemDrt];
                    }
                }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

        $salesReturn = SalesReturn::where('id', $salesReturnID)->first();


        $checkOtherPrns = SalesReturnDetail::with(['master' => function ($query) {
                                                    $query->where('approvedYN', 0);
                                               }])
                                               ->where('salesReturnID','!=', $input['salesReturnID'])
                                               ->where('custInvoiceDirectAutoID', $input['documentAutoID'])
                                               ->whereHas('master', function($query) {
                                                    $query->where('approvedYN', 0);
                                               })
                                               ->first();

        if ($checkOtherPrns) {
            return $this->sendError("There is a Sales Return (" . $checkOtherPrns->master->salesReturnCode . ") pending for approval for the Sales Invoice you are trying to add. Please check again.", 500);
        }

        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $customerInvoice = CustomerInvoiceDirect::find($new['custInvoiceDirectAutoID']);

                $doDetailExist = SalesReturnDetail::select(DB::raw('salesReturnDetailID'))
                                                ->where('salesReturnID', $salesReturnID)
                                                ->where('customerItemDetailID', $new['customerItemDetailID'])
                                                ->first();

                if (empty($doDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in delivery order
                        $detailSum = SalesReturnDetail::select(DB::raw('COALESCE(SUM(qtyReturnedDefaultMeasure),0) as totalNoQty'))
                                                    ->where('customerItemDetailID', $new['customerItemDetailID'])
                                                    ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['qtyIssuedDefaultMeasure'] == $totalAddedQty) {
                            $fullyReturned = 2;
                            $closedYN = -1;
                            $selectedForSalesReturn= -1;
                        } else {
                            $fullyReturned = 1;
                            $closedYN = 0;
                            $selectedForSalesReturn = 0;
                        }

                        // checking the qty request is matching with sum total
                        if ($new['qtyIssuedDefaultMeasure'] >= $new['noQty']) {

                            $invDetail_arr['salesReturnID'] = $salesReturnID;
                            $invDetail_arr['custInvoiceDirectAutoID'] = $new['custInvoiceDirectAutoID'];
                            $invDetail_arr['customerItemDetailID'] = $new['customerItemDetailID'];
                            $invDetail_arr['itemCodeSystem'] = $new['itemCodeSystem'];
                            $invDetail_arr['itemPrimaryCode'] = $new['itemPrimaryCode'];
                            $invDetail_arr['trackingType'] = $new['trackingType'];
                            $invDetail_arr['itemDescription'] = $new['itemDescription'];
                            $invDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                            $invDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];
                            $invDetail_arr['companySystemID'] = $customerInvoice->companySystemID;
                            $invDetail_arr['VATPercentage'] = $new['VATPercentage'];
                            $invDetail_arr['VATAmount'] = $new['VATAmount'];
                            $invDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                            $invDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                            $invDetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                            // $invDetail_arr['documentSystemID'] = $new['companySystemID'];

                            $item = ItemMaster::find($new['itemCodeSystem']);
                            if(empty($item)){
                                return $this->sendError(trans('custom.item_not_found'),500);
                            }

                            $data = array(
                                'companySystemID' => $customerInvoice->companySystemID,
                                'itemCodeSystem' => $new['itemCodeSystem'],
                                'wareHouseId' => $customerInvoice->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $invDetail_arr['doInvRemainingQty'] = floatval($new['qtyIssuedDefaultMeasure']) - floatval($new['rtnTakenQty']);
                            $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $invDetail_arr['wacValueLocal'] = $new['issueCostLocal'];
                            $invDetail_arr['wacValueReporting'] = $new['issueCostRpt'];
                            $invDetail_arr['convertionMeasureVal'] = 1;

                            $invDetail_arr['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                            $invDetail_arr['itemFinanceCategorySubID'] = $item->financeCategorySub;

                            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $customerInvoice->companySystemID)
                                                                                            ->where('mainItemCategoryID', $invDetail_arr['itemFinanceCategoryID'])
                                                                                            ->where('itemCategorySubID', $invDetail_arr['itemFinanceCategorySubID'])
                                                                                            ->first();

                            if (!empty($financeItemCategorySubAssigned)) {
                                $invDetail_arr['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                $invDetail_arr['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $invDetail_arr['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                $invDetail_arr['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                $invDetail_arr['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                                $invDetail_arr['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                                $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }

                            if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                || !$invDetail_arr['financeCogsGLcodePL'] || !$invDetail_arr['financeCogsGLcodePLSystemID']
                                || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }


                            $invDetail_arr['transactionCurrencyID'] = $customerInvoice->custTransactionCurrencyID;
                            $invDetail_arr['transactionCurrencyER'] = $customerInvoice->custTransactionCurrencyER;
                            $invDetail_arr['companyLocalCurrencyID'] = $customerInvoice->localCurrencyID;
                            $invDetail_arr['companyLocalCurrencyER'] = $customerInvoice->localCurrencyER;
                            $invDetail_arr['companyReportingCurrencyID'] = $customerInvoice->companyReportingCurrencyID;
                            $invDetail_arr['companyReportingCurrencyER'] = $customerInvoice->companyReportingER;

                            $invDetail_arr['itemUnitOfMeasure'] = $new['itemUnitOfMeasure'];
                            $invDetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureIssued'];
                            $invDetail_arr['qtyReturned'] = $new['noQty'];
                            $invDetail_arr['qtyReturnedDefaultMeasure'] = $new['noQty'];

                            // $invDetail_arr['marginPercentage'] = 0;
                            // if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                            //     $invDetail_arr['unitTransactionAmount'] = ($new['unitTransactionAmount']) - ($new['unitTransactionAmount']*$new['discountPercentage']/100);
                            // }else{
                                $invDetail_arr['unitTransactionAmount'] = $new['sellingCostAfterMargin'];
                            // }

                            $totalNetcost = $new['sellingCostAfterMargin'] * $new['noQty'];

                            $invDetail_arr['transactionAmount'] = \Helper::roundValue($totalNetcost);
                            $invDetail_arr['unitTransactionAmount'] = \Helper::roundValue($invDetail_arr['unitTransactionAmount']);
                            
                            $item = SalesReturnDetail::create($invDetail_arr);

                            $update = CustomerInvoiceItemDetails::where('customerItemDetailID', $new['customerItemDetailID'])
                                ->update(['fullyReturned' => $fullyReturned, 'returnQty' => $totalAddedQty]);
                        }

                        // fetching the total count records from purchase Request Details table
                        $doDetailTotalcount = CustomerInvoiceItemDetails::select(DB::raw('count(customerItemDetailID) as detailCount'))
                                                                ->where('custInvoiceDirectAutoID', $new['custInvoiceDirectAutoID'])
                                                                ->first();

                        // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                        $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('count(deliveryOrderDetailID) as count'))
                                                            ->where('custInvoiceDirectAutoID', $new['custInvoiceDirectAutoID'])
                                                            ->where('fullyReturned', 2)
                                                            ->first();

                        // Updating PR Master Table After All Detail Table records updated
                        if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                            $updatedo = CustomerInvoiceDirect::find($new['custInvoiceDirectAutoID'])
                                                    ->update(['selectedForSalesReturn' => -1, 'closedYN' => -1]);
                        }
                    }
                }

                //check all details fullyOrdered in DO Master
                $doMasterfullyOrdered = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $new['custInvoiceDirectAutoID'])
                                                            ->whereIn('fullyReturned', [1, 0])
                                                            ->get()->toArray();

                if (empty($doMasterfullyOrdered)) {
                    CustomerInvoiceDirect::find($new['custInvoiceDirectAutoID'])
                        ->update([
                            'selectedForSalesReturn' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    CustomerInvoiceDirect::find($new['custInvoiceDirectAutoID'])
                        ->update([
                            'selectedForSalesReturn' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateInvoiceReturnedStatus($new['custInvoiceDirectAutoID']);

                $resVat = $this->updateVatOfSalesReturn($salesReturnID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 

            }

            DB::commit();
            return $this->sendResponse([], trans('custom.sales_return_item_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }
    }


    private function updateDOReturnedStatus($deliveryOrderID){

        $status = 0;
        $invQty = DeliveryOrderDetail::where('deliveryOrderID',$deliveryOrderID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $doQty = SalesReturnDetail::where('deliveryOrderID',$deliveryOrderID)->sum('qtyReturnedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return DeliveryOrder::where('deliveryOrderID',$deliveryOrderID)->update(['returnStatus'=>$status]);

    }

    private function updateInvoiceReturnedStatus($custInvoiceDirectAutoID){

        $status = 0;
        $invQty = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $doQty = SalesReturnDetail::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->sum('qtyReturnedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)->update(['returnStatus'=>$status]);

    }


    public function getSalesReturnApprovals(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companySystemID = $request->companySystemID;
        $documentSystemID = $request->documentSystemID;
        $empID = Helper::getEmployeeSystemID();

        $doMasters = DB::table('erp_documentapproved')->select(
            'employeesdepartments.approvalDeligated',
            'salesreturn.id',
            'salesreturn.returnType',
            'salesreturn.salesReturnCode',
            'salesreturn.documentSystemID',
            'salesreturn.referenceNo',
            'salesreturn.salesReturnDate',
            'salesreturn.narration',
            'salesreturn.createdDateTime',
            'salesreturn.confirmedDate',
            'salesreturn.transactionAmount',
            'salesreturn.VATAmount',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'serviceline.ServiceLineDes As ServiceLineDes',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companySystemID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');
            $query->where('employeesdepartments.documentSystemID', 87)
                ->where('employeesdepartments.companySystemID', $companySystemID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('salesreturn', function ($query) use ($companySystemID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('salesreturn.companySystemID', $companySystemID)
                ->where('salesreturn.approvedYN', 0)
                ->where('salesreturn.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->leftJoin('serviceline', 'salesreturn.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.companySystemID', $companySystemID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $doMasters = $doMasters->where(function ($query) use ($search) {
                $query->where('salesReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($doMasters)
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

    public function getApprovedSalesReturnForUser(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companySystemID = $request->companySystemID;
        $documentSystemID = $request->documentSystemID;
        $empID = Helper::getEmployeeSystemID();

        $doMasters = DB::table('erp_documentapproved')->select(
            'salesreturn.id',
            'salesreturn.returnType',
            'salesreturn.salesReturnCode',
            'salesreturn.documentSystemID',
            'salesreturn.referenceNo',
            'salesreturn.salesReturnDate',
            'salesreturn.narration',
            'salesreturn.createdDateTime',
            'salesreturn.confirmedDate',
            'salesreturn.transactionAmount',
            'salesreturn.VATAmount',
            'salesreturn.approvedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'serviceline.ServiceLineDes As ServiceLineDes',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('salesreturn', function ($query) use ($companySystemID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->where('salesreturn.companySystemID', $companySystemID)
                ->where('salesreturn.approvedYN', -1)
                ->where('salesreturn.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->leftJoin('serviceline', 'salesreturn.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.documentSystemID', 87)
            ->where('erp_documentapproved.companySystemID', $companySystemID)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $doMasters = $doMasters->where(function ($query) use ($search) {
                $query->where('salesReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($doMasters)
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

     public function getSalesReturnRecord(Request $request){

        $input = $request->all();
        $id = $input['salesReturnID'];
        $companySystemID = $input['companySystemID'];
        $salesReturn = SalesReturn::with(['tax','company','customer','transaction_currency', 'sales_person','detail' => function($query){
            $query->with(['delivery_order','uom_default','uom_issuing', 'sales_invoice']);
        },'approved_by' => function($query) use($companySystemID){
            $query->where('companySystemID',$companySystemID)
                ->where('documentSystemID',87)
            ->with(['employee']);
        }])->find($id);

        if (empty($salesReturn)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        return $this->sendResponse($salesReturn->toArray(), trans('custom.sales_return_retrieved_successfully'));
    }

    function printSalesReturn(Request $request){
        $id = $request->get('id');
        $lang = $request->get('lang', 'en'); // Added to capture language

        $do = $this->salesReturnRepository->with(['tax','created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 71);
        }, 'company','customer','transaction_currency','detail'=> function($query){
            $query->with(['uom_issuing','delivery_order', 'sales_invoice']);
        }])->findWithoutFail($id);


        if (empty($do)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        if($do->transaction_currency){
            $do->currency = (isset($do->transaction_currency->DecimalPlaces) && $do->transaction_currency->DecimalPlaces)?$do->transaction_currency->DecimalPlaces:2;
        }

        $do->docRefNo = Helper::getCompanyDocRefNo($do->companySystemID, $do->documentSystemID);
        $do->logoExists = false;
        $companyLogo = isset($do->company->logo_url)? $do->company->logo_url:'';

        $disk = Helper::policyWiseDisk($do->company->masterCompanySystemIDReorting, 'local_public');

        $logoExists = Storage::disk($disk)->exists($do->company->logoPath);
        if ($logoExists) {
            $do->logoExists = true;
            $do->companyLogo = $companyLogo;
        }      

        $array = array('entity' => $do, 'lang' => $lang); // Pass lang to view
        $time = strtotime("now");
        $fileName = 'sales_return_' . $id . '_' . $time . '.pdf';
        
        $isRTL = ($lang === 'ar'); // Check if Arabic language for RTL support

        $mpdfConfig = [
            'tempDir' => public_path('tmp'),
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape format
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => -10
        ];

        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl'; // Set RTL direction for mPDF
        }

        $html = view('print.sales_return', $array);
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';

        try {
            $mpdf->WriteHTML($html);
            return $mpdf->Output($fileName, 'I');
        } catch (\Exception $e) {
            \Log::error('mPDF Error in printSalesReturn: ' . $e->getMessage());
            return $this->sendError(trans('custom.pdf_generation_failed') . $e->getMessage());
        }
    }


    public function salesReturnAudit(Request $request)
    {
        $input = $request->all();
        $salesReturnID = $input['salesReturnID'];
        $data = $this->salesReturnRepository->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 87);
        }, 'company','audit_trial.modified_by'])->findWithoutFail($salesReturnID);


        if (empty($data)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        return $this->sendResponse($data->toArray(), trans('custom.sales_return_retrieved_successfully'));
    }


    public function salesReturnReopen(Request $request)
    {
        $input = $request->all();

        $salesReturnID = $input['salesReturnID'];

        $salesReturn= SalesReturn::find($salesReturnID);
        $emails = array();
        if (empty($salesReturn)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        if ($salesReturn->RollLevForApp_curr > 1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_sales_return_it_is_already_'));
        }

        if ($salesReturn->approvedYN == -1) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_sales_return_it_is_already__1'));
        }

        if ($salesReturn->confirmedYN == 0) {
            return $this->sendError(trans('custom.you_cannot_reopen_this_sales_return_it_is_not_conf'));
        }

        // updating fields
        $salesReturn->confirmedYN = 0;
        $salesReturn->confirmedByEmpSystemID = null;
        $salesReturn->confirmedByEmpID = null;
        $salesReturn->confirmedByName = null;
        $salesReturn->confirmedDate = null;
        $salesReturn->RollLevForApp_curr = 1;
        $salesReturn->save();

        $employee = Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $salesReturn->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $salesReturn->salesReturnCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $salesReturn->salesReturnCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $salesReturn->companySystemID)
                                            ->where('documentSystemCode', $salesReturn->id)
                                            ->where('documentSystemID', $salesReturn->documentSystemID)
                                            ->where('rollLevelOrder', 1)
                                            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $salesReturn->companySystemID)
                                                            ->where('documentSystemID', $salesReturn->documentSystemID)
                                                            ->first();


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

        DocumentApproved::where('documentSystemCode', $salesReturnID)
                                        ->where('companySystemID', $salesReturn->companySystemID)
                                        ->where('documentSystemID', $salesReturn->documentSystemID)
                                        ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($salesReturn->documentSystemID,$salesReturnID,$input['reopenComments'],'Reopened');

        return $this->sendResponse($salesReturn->toArray(), trans('custom.sales_return_reopened_successfully'));
    }


    public function getSalesReturnAmend(Request $request)
    {
        $input = $request->all();

        $salesReturnID = $input['salesReturnID'];

        $doData = SalesReturn::find($salesReturnID);
        if (empty($doData)) {
            return $this->sendError(trans('custom.sales_return_not_found'));
        }

        if ($doData->refferedBackYN != -1) {
            return $this->sendError(trans('custom.you_cannot_amend_this_sales_return'));
        }

        $salesReturnArray = $doData->toArray();
        $salesReturnArray['salesReturnID'] = $salesReturnArray['id'];
        unset($salesReturnArray['id']);
        $storeDeliveryOrderHistory = SalesReturnRefferedBack::insert($salesReturnArray);

        $fetchSalesReturnDetails = SalesReturnDetail::where('salesReturnID', $salesReturnID)
            ->get();

        $srDetailArray = $fetchSalesReturnDetails->toArray();

        $storeDeliveryOrderDetaillHistory = SalesReturnDetailRefferedBack::insert($srDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $salesReturnID)
                                                ->where('companySystemID', $doData->companySystemID)
                                                ->where('documentSystemID', $doData->documentSystemID)
                                                ->get();

        if (!empty($fetchDocumentApproved)) {
            foreach ($fetchDocumentApproved as $DocumentApproved) {
                $DocumentApproved['refTimes'] = $doData->timesReferred;
            }
        }

        $DocumentApprovedArray = $fetchDocumentApproved->toArray();

        $storeDocumentReferedHistory = DocumentReferedHistory::insert($DocumentApprovedArray);

        $deleteApproval = DocumentApproved::where('documentSystemCode', $salesReturnID)
                                        ->where('companySystemID', $doData->companySystemID)
                                        ->where('documentSystemID', $doData->documentSystemID)
                                        ->delete();

        if ($deleteApproval) {
            $doData->refferedBackYN = 0;
            $doData->confirmedYN = 0;
            $doData->confirmedByEmpSystemID = null;
            $doData->confirmedByEmpID = null;
            $doData->confirmedByName = null;
            $doData->confirmedDate = null;
            $doData->RollLevForApp_curr = 1;
            $doData->save();
        }

        return $this->sendResponse($doData->toArray(), trans('custom.sales_return_amend_successfully'));
    }

    public function approveSalesReturn(Request $request)
    {
        $approve = Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectSalesReturn(Request $request)
    {
        $reject = Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    function getSalesReturnDetailsForDO(Request $request)
    {
        $input = $request->all();

        $deliveryOrderID = $input['deliveryOrderID'];

        $detail = SalesReturnDetail::where('deliveryOrderID',$deliveryOrderID)
            ->with(['master'=> function($query){
                $query->with(['transaction_currency']);
            },'delivery_order_detail','uom_issuing'])
            ->get();
        return $this->sendResponse($detail, trans('custom.details_retrieved_successfully'));
    }

    function getSalesReturnDetailsForSI(Request $request)
    {
        $input = $request->all();

        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $detail = SalesReturnDetail::where('custInvoiceDirectAutoID',$custInvoiceDirectAutoID)
            ->with(['master'=> function($query){
                $query->with(['transaction_currency']);
            },'sales_invoice_detail','uom_issuing'])
            ->get();
        return $this->sendResponse($detail, trans('custom.details_retrieved_successfully'));
    }

}

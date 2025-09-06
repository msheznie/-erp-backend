<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\inventory;
use App\helper\TaxService;
use App\Http\Requests\API\CreateDeliveryOrderAPIRequest;
use App\Http\Requests\API\UpdateDeliveryOrderAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\PurchaseReturn;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\StockTransfer;
use App\Models\CurrencyMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DeliveryOrderDetailRefferedback;
use App\Models\DeliveryOrderRefferedback;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Models\ItemIssueMaster;
use App\Models\GeneralLedger;
use App\Models\Months;
use App\Models\TaxMaster;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SalesPersonMaster;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\DeliveryOrderRepository;
use App\Services\ChartOfAccountValidationService;
use App\Traits\AuditTrial;
use App\Models\Taxdetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\ItemTracking;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentSystemMapping;
use App\Services\GeneralLedgerService;
use App\Services\ValidateDocumentAmend;
use App\Services\DeliveryOrderServices;
use App\Models\StockCount;
use App\Models\StockAdjustment;

/**
 * Class DeliveryOrderController
 * @package App\Http\Controllers\API
 */

class DeliveryOrderAPIController extends AppBaseController
{
    /** @var  DeliveryOrderRepository */
    private $deliveryOrderRepository;
    private $deliveryOrderServices;

    public function __construct(DeliveryOrderRepository $deliveryOrderRepo,DeliveryOrderServices $deliveryOrderServices)
    {
        $this->deliveryOrderRepository = $deliveryOrderRepo;
        $this->deliveryOrderServices = $deliveryOrderServices;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrders",
     *      summary="Get a listing of the DeliveryOrders.",
     *      tags={"DeliveryOrder"},
     *      description="Get all DeliveryOrders",
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
     *                  @SWG\Items(ref="#/definitions/DeliveryOrder")
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
        $this->deliveryOrderRepository->pushCriteria(new RequestCriteria($request));
        $this->deliveryOrderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $deliveryOrders = $this->deliveryOrderRepository->all();

        return $this->sendResponse($deliveryOrders->toArray(), 'Delivery Orders retrieved successfully');
    }

    /**
     * @param CreateDeliveryOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/deliveryOrders",
     *      summary="Store a newly created DeliveryOrder in storage",
     *      tags={"DeliveryOrder"},
     *      description="Store DeliveryOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrder that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrder")
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
     *                  ref="#/definitions/DeliveryOrder"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeliveryOrderAPIRequest $request)
    {
        $input = $request->all();

        $messages = [
            'transactionCurrencyID.required' => 'Currency field is required',
            'customerID.required' => 'Customer field is required',
            'companyFinanceYearID.required' => 'Finance Year field is required',
            'companyFinancePeriodID.required' => 'Finance Period field is required',
            'serviceLineSystemID.required' => 'Segment field is required',
            'wareHouseSystemCode.required' => 'Warehouse field is required',
            'deliveryOrderDate.required' => 'Document Date field is required',
        ];

        $validator = \Validator::make($input, [
            'orderType' => 'required|numeric|min:1',
            'companySystemID' => 'required|numeric|min:1',
            'documentSystemID' => 'required|numeric|min:1',
            'customerID' => 'required',
            'transactionCurrencyID' => 'required',
            'companyFinanceYearID' => 'required',
            'companyFinancePeriodID' => 'required',
            'serviceLineSystemID' => 'required',
            'wareHouseSystemCode' => 'required',
            'deliveryOrderDate' => 'required|date'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $input['deliveryOrderDate'] = new Carbon($input['deliveryOrderDate']);

        $input = $this->convertArrayToSelectedValue($input, array('transactionCurrencyID','companyFinancePeriodID','companyFinanceYearID'));

        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;

        $customer = CustomerMaster::where('customerCodeSystem',$input['customerID'])->first();
        if(empty($customer)){
            return $this->sendError('Selected customer not found on db',500);
        }


        if(!$customer->custGLAccountSystemID){
            return $this->sendError('GL account is not configured for this customer',500);
        }

        if(!$customer->custUnbilledAccountSystemID){
            return $this->sendError('Unbilled receivable account is not configured for this customer',500);
        }

        $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
            ->where('companySystemID', $input['companySystemID'])
            ->first();
        if ($customerGLCodeUpdate) {
            $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
        }

        $input['custGLAccountSystemID'] = $customer->custGLAccountSystemID;
        $input['custGLAccountCode'] = $customer->custGLaccount;
        $input['custUnbilledAccountSystemID'] = $customer->custUnbilledAccountSystemID;
        $input['custUnbilledAccountCode'] = $customer->custUnbilledAccount;


        //deliveryOrderCode
        $lastSerial = DeliveryOrder::where('companySystemID', $input['companySystemID'])
            ->where('companyFinanceYearID', $input['companyFinanceYearID'])
            ->orderBy('serialNo', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNo) + 1;
        }
        $company = Company::where('companySystemID', $input['companySystemID'])->first()->toArray();
        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $input['deliveryOrderCode'] = ($company['CompanyID'] . '\\' . $y . '\\DEO' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['serialNo'] = $lastSerialNumber;
        $input['companyID'] = $company['CompanyID'];
        $input['documentID'] = 'DEO';

        $input['vatRegisteredYN'] = $company['vatRegisteredYN'];

        if(isset($input['serviceLineSystemID']) && $input['serviceLineSystemID']){
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;

        // check date within financial period
        if (!(($input['deliveryOrderDate'] >= $input['FYPeriodDateFrom']) && ($input['deliveryOrderDate'] <= $input['FYPeriodDateTo']))) {
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

        $deliveryOrder = $this->deliveryOrderRepository->create($input);

        return $this->sendResponse($deliveryOrder->toArray(), 'Delivery Order saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrders/{id}",
     *      summary="Display the specified DeliveryOrder",
     *      tags={"DeliveryOrder"},
     *      description="Get DeliveryOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrder",
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
     *                  ref="#/definitions/DeliveryOrder"
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
       
        /** @var DeliveryOrder $deliveryOrder */
        $deliveryOrder = $this->deliveryOrderRepository->with(['tax','customer','transaction_currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'detail' => function($query){
            $query->with(['quotation','uom_default', 'item_by']);
        },'segment','warehouse'])->findWithoutFail($id);
        if (empty($deliveryOrder)) {
            return $this->sendError('Delivery Order not found');
        }

        return $this->sendResponse($deliveryOrder->toArray(), 'Delivery Order retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDeliveryOrderAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/deliveryOrders/{id}",
     *      summary="Update the specified DeliveryOrder in storage",
     *      tags={"DeliveryOrder"},
     *      description="Update DeliveryOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrder",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrder that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrder")
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
     *                  ref="#/definitions/DeliveryOrder"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeliveryOrderAPIRequest $request)
    {
        $input = $request->all();

        /** @var DeliveryOrder $deliveryOrder */
        $deliveryOrder = $this->deliveryOrderRepository->findWithoutFail($id);

        if (empty($deliveryOrder)) {
            return $this->sendError('Delivery Order not found');
        }
        
        $deliveryOrderDetails = DeliveryOrderDetail::where('deliveryOrderID', $id)->count();

        if ($deliveryOrderDetails > 0) {
            $deliveryOrderDetaiCountl = DeliveryOrderDetail::where('quotationMasterID',$deliveryOrder->detail()->get()[0]['quotationMasterID'])->count();

            $quotationMaster = QuotationMaster::find($deliveryOrder->detail()->get()[0]['quotationMasterID']);
                
            if ($quotationMaster) {
                $count  = $quotationMaster->detail->count();

                if($deliveryOrderDetaiCountl == $count) {
                    $quotationMaster->isInDOorCI = 1;
                    $quotationMaster->save();
                }else {
                    $quotationMaster->isInDOorCI = 3;
                    $quotationMaster->save();

                }
            }
        }


        $input = $this->convertArrayToSelectedValue($input, array('transactionCurrencyID','confirmedYN','customerID','orderType','salesPersonID','serviceLineSystemID','wareHouseSystemCode','companyFinancePeriodID'));
        $input = array_except($input,['finance_period_by','finance_year_by','transaction_currency','customer','detail','segment','warehouse']);

        if($deliveryOrder->transactionCurrencyID != $input['transactionCurrencyID']){
            $companyCurrency = Helper::companyCurrency($input['companySystemID']);
            $companyCurrencyConversion = Helper::currencyConversion($input['companySystemID'], $input['transactionCurrencyID'], $input['transactionCurrencyID'], 0);

            $input['transactionCurrencyER'] = 1;
            $input['companyLocalCurrencyID'] = $companyCurrency->localcurrency->currencyID;
            $input['companyLocalCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
            $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
            $input['companyReportingCurrencyER'] = $companyCurrencyConversion['trasToRptER'];
        }

        $input['documentID'] = 'DEO';

        if($deliveryOrder->serviceLineSystemID != $input['serviceLineSystemID']){
            $segment = SegmentMaster::find($input['serviceLineSystemID']);
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        $input['deliveryOrderDate'] = Carbon::parse($input['deliveryOrderDate'])->format('Y-m-d') . ' 00:00:00';

        $detailAmount = DeliveryOrderDetail::
        select(DB::raw("
        IFNULL(SUM(qtyIssuedDefaultMeasure * (unitTransactionAmount-discountAmount)),0) as transAmount,
        IFNULL(SUM(qtyIssuedDefaultMeasure * (companyLocalAmount-(companyLocalAmount*discountPercentage/100))),0) as localAmount,
        IFNULL(SUM(qtyIssuedDefaultMeasure * (companyReportingAmount-(companyReportingAmount*discountPercentage/100))),0) as reportAmount"))
            ->where('deliveryOrderID', $id)
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
            if($input['customerID'] != $deliveryOrder->customerID){
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

            $customerGLCodeUpdate = CustomerAssigned::where('customerCodeSystem', $input['customerID'])
                                                    ->where('companySystemID', $input['companySystemID'])
                                                    ->first();
            if ($customerGLCodeUpdate) {
                $input['customerVATEligible'] = $customerGLCodeUpdate->vatEligible;
            }

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if ($company) {
                $input['vatRegisteredYN'] = $company->vatRegisteredYN;
            }
        }


        if ($input['confirmedYN'] == 1 && $deliveryOrder->confirmedYN == 0) {

            // check document date between financial period
            if (($input['deliveryOrderDate'] >= $input['FYPeriodDateFrom']) && ($input['deliveryOrderDate'] <= $input['FYPeriodDateTo'])) {
            } else {
                return $this->sendError('Document date should be between the selected financial period start date and end date.', 500);
            }

            $trackingValidation = ItemTracking::validateTrackingOnDocumentConfirmation($deliveryOrder->documentSystemID, $id);

            if (!$trackingValidation['status']) {
                return $this->sendError($trackingValidation["message"], 500, ['type' => 'confirm']);
            }


            if ($deliveryOrderDetails == 0) {
                return $this->sendError('Every order should have at least one item', 500);
            }

            $messages = [
                'transactionCurrencyID.required' => 'Currency field is required',
                'customerID.required' => 'Customer field is required',
                'companyFinanceYearID.required' => 'Finance Year field is required',
                'companyFinancePeriodID.required' => 'Finance Period field is required',
                'serviceLineSystemID.required' => 'Segment field is required',
                'wareHouseSystemCode.required' => 'Warehouse field is required',
                'deliveryOrderDate.required' => 'Document Date field is required',
            ];

            $validator = \Validator::make($input, [
                'orderType' => 'required|numeric|min:1',
                'companySystemID' => 'required|numeric|min:1',
                'documentSystemID' => 'required|numeric|min:1',
                'customerID' => 'required',
                'transactionCurrencyID' => 'required',
                'companyFinanceYearID' => 'required',
                'companyFinancePeriodID' => 'required',
                'serviceLineSystemID' => 'required',
                'wareHouseSystemCode' => 'required',
                'deliveryOrderDate' => 'required|date'
            ], $messages);

            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
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


            $detail = DeliveryOrderDetail::where('deliveryOrderID', $id)->get();
            if(count((array)$detail) == 0){
                return  $this->sendError('Order detail not found', 500);
            }

            $financeCategories = $detail->pluck('itemFinanceCategoryID')->toArray();
            if (count(array_unique($financeCategories)) > 1) {
                return $this->sendError('Multiple finance category cannot be added. Different finance category found on saved details.',500);
            }

            $checkQuantity = DeliveryOrderDetail::where('deliveryOrderID', $id)
                ->where(function ($q) {
                    $q->where('qtyIssued', '<=', 0)
                        ->orWhereNull('qtyIssued');
                })
                ->exists();
            if ($checkQuantity) {
                return $this->sendError('Every Item should have at least one minimum Qty Requested', 500);
            }



            foreach ($detail as $item) {

                $updateItem = DeliveryOrderDetail::find($item['deliveryOrderDetailID']);

                //If the revenue account or cost account or BS account is null do not allow to confirm
                if((!($item->financeGLcodebBSSystemID > 0)) && $updateItem->itemFinanceCategoryID!=2){
                    return $this->sendError('BS account cannot be null for '.$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                }elseif (!($item->financeGLcodePLSystemID > 0)){
                    return $this->sendError('Cost account cannot be null for '.$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                }elseif (!($item->financeGLcodeRevenueSystemID > 0)){
                    return $this->sendError('Revenue account cannot be null for '.$item->itemPrimaryCode.'-'.$item->itemDescription, 500);
                }

                $data = array(
                    'companySystemID' => $deliveryOrder->companySystemID,
                    'itemCodeSystem' => $updateItem->itemCodeSystem,
                    'wareHouseId' => $deliveryOrder->wareHouseSystemCode
                );

                $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                $updateItem->currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                $updateItem->currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                $updateItem->currentStockQtyInDamageReturn = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];

                $updateItem->wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
                $updateItem->wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];

                //discount calculation
                $discountedUnit = $updateItem->unitTransactionAmount;

                if($updateItem->discountAmount > 0) {
                    $discountedUnit = $updateItem->unitTransactionAmount - $updateItem->discountAmount;
                }

                $updateItem->transactionAmount = $discountedUnit*$updateItem->qtyIssuedDefaultMeasure;

                if($updateItem->transactionCurrencyID != $updateItem->companyLocalCurrencyID){
                    $currencyConversion = Helper::currencyConversion($deliveryOrder->companySystemID,$updateItem->transactionCurrencyID,$updateItem->transactionCurrencyID,$updateItem->unitTransactionAmount);
                    if(!empty($currencyConversion)){
                        $updateItem->companyLocalAmount = $currencyConversion['localAmount'];
                    }
                }else{
                    $updateItem->companyLocalAmount = $updateItem->unitTransactionAmount;
                }

                if($updateItem->transactionCurrencyID != $updateItem->companyReportingCurrencyID){
                    $currencyConversion = Helper::currencyConversion($deliveryOrder->companySystemID,$updateItem->transactionCurrencyID,$updateItem->transactionCurrencyID,$updateItem->unitTransactionAmount);
                    if(!empty($currencyConversion)){
                        $updateItem->companyReportingAmount = $currencyConversion['reportingAmount'];
                    }
                }else{
                    $updateItem->companyReportingAmount = $updateItem->unitTransactionAmount;
                }

                $updateItem->unitTransactionAmount = Helper::roundValue($updateItem->unitTransactionAmount);
                $updateItem->discountPercentage = Helper::roundValue($updateItem->discountPercentage);
                $updateItem->discountAmount = Helper::roundValue($updateItem->discountAmount);
                $updateItem->transactionAmount = Helper::roundValue($updateItem->transactionAmount);
                $updateItem->companyLocalAmount = Helper::roundValue($updateItem->companyLocalAmount);
                $updateItem->companyReportingAmount = Helper::roundValue($updateItem->companyReportingAmount);

                $updateItem->save();

                if ($updateItem->unitTransactionAmount == 0) {
                    return $this->sendError('Item must not have zero cost', 500);
                }
                if ($updateItem->unitTransactionAmount < 0) {
                    return $this->sendError('Item must not have negative cost', 500);
                }

                if($updateItem->itemFinanceCategoryID==1){
                    if ($updateItem->currentWareHouseStockQty <= 0) {
                        return $this->sendError('Warehouse stock Qty is 0 for '.$updateItem->itemPrimaryCode.' - '.$updateItem->itemDescription, 500);
                    }
                    if ($updateItem->currentStockQty <= 0) {
                        return $this->sendError('Stock Qty is 0 for '.$updateItem->itemPrimaryCode.' - '.$updateItem->itemDescription, 500);
                    }
                    if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentStockQty) {
                        return $this->sendError('Insufficient Stock Qty for '.$updateItem->itemPrimaryCode.' - '.$updateItem->itemDescription, 500);
                    }

                    if ($updateItem->qtyIssuedDefaultMeasure > $updateItem->currentWareHouseStockQty) {
                        return $this->sendError('Insufficient Warehouse Qty for '.$updateItem->itemPrimaryCode.' - '.$updateItem->itemDescription, 500);
                    }
                }

            }

            if($updateItem->discountPercentage != 0){
                $amount = DeliveryOrderDetail::where('deliveryOrderID', $id)
                    ->sum(DB::raw('qtyIssuedDefaultMeasure * (companyReportingAmount-(companyReportingAmount*discountPercentage/100))'));
            }else{
                $amount = DeliveryOrderDetail::where('deliveryOrderID', $id)
                    ->sum(DB::raw('qtyIssuedDefaultMeasure * companyReportingAmount'));
            }

            // VAT configuration validation
            $taxSum = Taxdetail::where('documentSystemCode', $id)
                ->where('companySystemID', $deliveryOrder->companySystemID)
                ->where('documentSystemID', $deliveryOrder->documentSystemID)
                ->sum('amount');

            if($taxSum  > 0 && empty(TaxService::getOutputVATTransferGLAccount($deliveryOrder->companySystemID))){
                return $this->sendError('Cannot confirm. Output VAT GL Account not configured.', 500);
            }

            $object = new ChartOfAccountValidationService();
            $result = $object->checkChartOfAccountStatus($input["documentSystemID"], $id, $input["companySystemID"]);

            if (isset($result) && !empty($result["accountCodes"])) {
                return $this->sendError($result["errorMsg"]);
            }

            $params = array('autoID' => $id,
                'company' => $deliveryOrder->companySystemID,
                'document' => $deliveryOrder->documentSystemID,
                'segment' => '',
                'category' => '',
                'amount' => $amount
            );


            $update = array_except($input,['confirmedYN', 'tax']);
            $deliveryOrder = $this->deliveryOrderRepository->update($update, $id);
            $confirm = Helper::confirmDocument($params);
            if (!$confirm["success"]) {
                return $this->sendError($confirm["message"], 500);
            } else {
                return $this->sendReponseWithDetails($deliveryOrder->toArray(), 'Delivery order confirmed successfully',1,$confirm['data'] ?? null);
            }

        }else{
            $deliveryOrder = $this->deliveryOrderRepository->update($input, $id);
            return $this->sendResponse($deliveryOrder->toArray(), 'DeliveryOrder updated successfully');
        }

    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/deliveryOrders/{id}",
     *      summary="Remove the specified DeliveryOrder from storage",
     *      tags={"DeliveryOrder"},
     *      description="Delete DeliveryOrder",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrder",
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
        /** @var DeliveryOrder $deliveryOrder */
        $deliveryOrder = $this->deliveryOrderRepository->findWithoutFail($id);

        if (empty($deliveryOrder)) {
            return $this->sendError('Delivery Order not found');
        }

        $deliveryOrder->delete();

        return $this->sendSuccess('Delivery Order deleted successfully');
    }

    public function getDeliveryOrderFormData(Request $request){
        $companyId = $request['companyId'];

        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($companyId);
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

        $segments = SegmentMaster::whereIn("companySystemID", $subCompanies)->where('isActive', 1)->approved()->withAssigned($companyId)->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
        $companyFinanceYear = Helper::companyFinanceYear($companyId, 1);

        $orderType = array(array('value' => 1, 'label' => 'Direct Order'), array('value' => 2, 'label' => 'Quotation Based'),array('value' => 3, 'label' => 'Sales Order Based'));
        $wareHouses = WarehouseMaster::where("companySystemID", $companyId)->where('isActive', 1)->get();

        $isVATEligible = TaxService::checkCompanyVATEligible($companyId);

        $taxData = TaxMaster::where('taxType', 2)
                            ->where('companySystemID', $companyId)
                            ->get();
        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'isVATEligible' => $isVATEligible,
            'customer' => $customer,
            'taxData' => $taxData,
            'salespersons' => $salespersons,
            'segments' => $segments,
            'financialYears' => $financialYears,
            'orderType' => $orderType,
            'companyFinanceYear'=>$companyFinanceYear,
            'wareHouses'=>$wareHouses
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getAllDeliveryOrder(Request $request){
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $deliveryOrder = $this->deliveryOrderRepository->deliveryOrderListQuery($request, $input, $search);

        return \DataTables::eloquent($deliveryOrder)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('deliveryOrderID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function salesQuotationForDO(Request $request){
        $input = $request->all();
        $documentSystemID = 0;
        if($input['type'] == 2){ //Quotation
            $documentSystemID = 67;
        } elseif ($input['type']==3){ ////sales order
            $documentSystemID = 68;
        }
        $deliveryOrder = DeliveryOrder::find($input['deliveryOrderID']);

        $deliveryOrderDetails = DeliveryOrderDetail::where('deliveryOrderID',$input['deliveryOrderID'])->pluck('quotationMasterID')->toArray();

        $existsMaster =  QuotationMaster::where('documentSystemID',$documentSystemID)
            ->where('companySystemID',$input['companySystemID'])
            ->whereIn('quotationMasterID',$deliveryOrderDetails)
            ->where('serviceLineSystemID', $deliveryOrder->serviceLineSystemID)
            ->where('customerSystemCode', $deliveryOrder->customerID)
            ->where('transactionCurrencyID', $deliveryOrder->transactionCurrencyID)
            ->whereDate('documentDate', '<=',$deliveryOrder->deliveryOrderDate)
            ->orderBy('quotationMasterID','DESC')
            ->get();

        $master = QuotationMaster::where('documentSystemID',$documentSystemID)
            ->where('companySystemID',$input['companySystemID'])
            ->where('approvedYN', -1)
            ->where('selectedForDeliveryOrder', 0)
            ->where('selectedForSalesOrder', 0)
            ->where('isInDOorCI', '!=',2)
            ->where('isInSO', '!=',1)
            ->where('closedYN',0)
            ->where('cancelledYN',0)
            ->where('manuallyClosed',0)
            ->where('serviceLineSystemID', $deliveryOrder->serviceLineSystemID)
            ->where('customerSystemCode', $deliveryOrder->customerID)
            ->where('transactionCurrencyID', $deliveryOrder->transactionCurrencyID)
            ->whereDate('documentDate', '<=',$deliveryOrder->deliveryOrderDate)
            ->orderBy('quotationMasterID','DESC')
            ->get();



        return $this->sendResponse($master->merge($existsMaster)->toArray(), 'Quotations retrieved successfully');
    }

    public function getSalesQuoatationDetailForDO(Request $request){
        $input = $request->all();
        $id = $input['quotationMasterID'];

        $detail = DB::select('SELECT
	quotationdetails.*,
	erp_quotationmaster.serviceLineSystemID,
	"" AS isChecked,
	"" AS noQty,
	IFNULL(dodetails.doTakenQty,0) as doTakenQty,
	IFNULL(dodetails.doReturnQty,0) as doReturnQty
FROM
	erp_quotationdetails quotationdetails
	INNER JOIN erp_quotationmaster ON quotationdetails.quotationMasterID = erp_quotationmaster.quotationMasterID
	LEFT JOIN ( SELECT erp_delivery_order_detail.deliveryOrderDetailID,quotationDetailsID, SUM( qtyIssued ) AS doTakenQty, SUM( approvedReturnQty ) AS doReturnQty FROM erp_delivery_order_detail GROUP BY quotationDetailsID, itemCodeSystem ) AS dodetails ON quotationdetails.quotationDetailsID = dodetails.quotationDetailsID 
WHERE
	quotationdetails.quotationMasterID = ' . $id . ' 
	AND fullyOrdered != 2 AND erp_quotationmaster.isInDOorCI != 2 AND erp_quotationmaster.isInSO != 1');

        return $this->sendResponse($detail, 'Quotation Details retrieved successfully');
    }

    public function getDeliveryOrderApprovals(Request $request)
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
            'erp_delivery_order.deliveryOrderID',
            'erp_delivery_order.orderType',
            'erp_delivery_order.deliveryOrderCode',
            'erp_delivery_order.documentSystemID',
            'erp_delivery_order.referenceNo',
            'erp_delivery_order.deliveryOrderDate',
            'erp_delivery_order.narration',
            'erp_delivery_order.createdDateTime',
            'erp_delivery_order.confirmedDate',
            'erp_delivery_order.transactionAmount',
            'erp_delivery_order.VATAmount',
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
            $query->where('employeesdepartments.documentSystemID', 71)
                ->where('employeesdepartments.companySystemID', $companySystemID)
                ->where('employeesdepartments.employeeSystemID', $empID);
        })->join('erp_delivery_order', function ($query) use ($companySystemID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'deliveryOrderID')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('erp_delivery_order.companySystemID', $companySystemID)
                ->where('erp_delivery_order.approvedYN', 0)
                ->where(function($query) {
                    $query->where('erp_delivery_order.isFrom','!=',5)
                          ->orWhereNull('erp_delivery_order.isFrom');
                })
                ->where('erp_delivery_order.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->leftJoin('serviceline', 'erp_delivery_order.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.companySystemID', $companySystemID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $doMasters = $doMasters->where(function ($query) use ($search) {
                $query->where('deliveryOrderCode', 'LIKE', "%{$search}%")
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

    public function getApprovedDeliveryOrderForUser(Request $request)
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
            'erp_delivery_order.deliveryOrderID',
            'erp_delivery_order.orderType',
            'erp_delivery_order.deliveryOrderCode',
            'erp_delivery_order.documentSystemID',
            'erp_delivery_order.referenceNo',
            'erp_delivery_order.deliveryOrderDate',
            'erp_delivery_order.narration',
            'erp_delivery_order.createdDateTime',
            'erp_delivery_order.confirmedDate',
            'erp_delivery_order.transactionAmount',
            'erp_delivery_order.VATAmount',
            'erp_delivery_order.approvedDate',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'currencymaster.DecimalPlaces As DecimalPlaces',
            'currencymaster.CurrencyCode As CurrencyCode',
            'customermaster.CustomerName As CustomerName',
            'serviceline.ServiceLineDes As ServiceLineDes',
            'approvalLevelID',
            'documentSystemCode',
            'employees.empName As created_user'
        )->join('erp_delivery_order', function ($query) use ($companySystemID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'deliveryOrderID')
                ->where('erp_delivery_order.companySystemID', $companySystemID)
                ->where('erp_delivery_order.approvedYN', -1)
                ->where(function($query) {
                    $query->where('erp_delivery_order.isFrom','!=',5)
                          ->orWhereNull('erp_delivery_order.isFrom');
                })
                ->where('erp_delivery_order.confirmedYN', 1);
        })->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('currencymaster', 'transactionCurrencyID', 'currencymaster.currencyID')
            ->leftJoin('customermaster', 'customerID', 'customermaster.customerCodeSystem')
            ->leftJoin('serviceline', 'erp_delivery_order.serviceLineSystemID', 'serviceline.serviceLineSystemID')
            ->where('erp_documentapproved.documentSystemID', 71)
            ->where('erp_documentapproved.companySystemID', $companySystemID)
            ->where('erp_documentapproved.documentSystemID', $documentSystemID)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $doMasters = $doMasters->where(function ($query) use ($search) {
                $query->where('deliveryOrderCode', 'LIKE', "%{$search}%")
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

    public function approveDeliveryOrder(Request $request)
    {
        $approve = Helper::approveDocument($request);
        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }

    }

    public function rejectDeliveryOrder(Request $request)
    {
        $reject = Helper::rejectDocument($request);
        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }

    public function deliveryOrderAudit(Request $request)
    {
        $input = $request->all();
        $deliveryOrderID = $input['deliveryOrderID'];
        $data = $this->deliveryOrderRepository->with(['created_by', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 71);
        }, 'company','audit_trial.modified_by'])->findWithoutFail($deliveryOrderID);


        if (empty($data)) {
            return $this->sendError('Delivery Order not found');
        }

        return $this->sendResponse($data->toArray(), 'Delivery Order retrieved successfully');
    }

    public function deliveryOrderReopen(Request $request)
    {
        $input = $request->all();

        $deliveryOrderID = $input['deliveryOrderID'];

        $deliveryOrder= DeliveryOrder::find($deliveryOrderID);
        $emails = array();
        if (empty($deliveryOrder)) {
            return $this->sendError('Delivery Order not found');
        }

        if ($deliveryOrder->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this delivery order. it is already partially approved');
        }

        if ($deliveryOrder->approvedYN == -1) {
            return $this->sendError('You cannot reopen this delivery order. it is already fully approved');
        }

        if ($deliveryOrder->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this delivery order. it is not confirmed');
        }

        // updating fields
        $deliveryOrder->confirmedYN = 0;
        $deliveryOrder->confirmedByEmpSystemID = null;
        $deliveryOrder->confirmedByEmpID = null;
        $deliveryOrder->confirmedByName = null;
        $deliveryOrder->confirmedDate = null;
        $deliveryOrder->RollLevForApp_curr = 1;
        $deliveryOrder->save();

        $employee = Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $deliveryOrder->documentSystemID)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $deliveryOrder->deliveryOrderCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $deliveryOrder->deliveryOrderCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $deliveryOrder->companySystemID)
            ->where('documentSystemCode', $deliveryOrder->deliveryOrderID)
            ->where('documentSystemID', $deliveryOrder->documentSystemID)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $deliveryOrder->companySystemID)
                    ->where('documentSystemID', $deliveryOrder->documentSystemID)
                    ->first();


                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                /* if ($companyDocument['isServiceLineApproval'] == -1) {
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

        DocumentApproved::where('documentSystemCode', $deliveryOrderID)
            ->where('companySystemID', $deliveryOrder->companySystemID)
            ->where('documentSystemID', $deliveryOrder->documentSystemID)
            ->delete();

        /*Audit entry*/
        AuditTrial::createAuditTrial($deliveryOrder->documentSystemID,$deliveryOrderID,$input['reopenComments'],'Reopened');

        return $this->sendResponse($deliveryOrder->toArray(), 'Delivery Order reopened successfully');
    }

    function getInvoiceDetailsForDO(Request $request)
    {
        $input = $request->all();

        $deliveryOrderID = $input['deliveryOrderID'];

        $detail = CustomerInvoiceItemDetails::where('deliveryOrderID',$deliveryOrderID)
            ->with(['master'=> function($query){
                $query->with(['currency']);
            },'delivery_order_detail','uom_issuing'])
            ->get();
        return $this->sendResponse($detail, 'Details retrieved successfully');
    }

    function printDeliveryOrder(Request $request){
        $id = $request->get('id');
        $lang = $request->get('lang', 'en'); // Added to capture language

        $do = $this->deliveryOrderRepository->with(['created_by', 'confirmed_by', 'modified_by', 'tax','approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 71);
        }, 'company','customer','transaction_currency','detail'=> function($query){
            $query->with(['uom_issuing','quotation','item_by']);
        }])->findWithoutFail($id);


        if (empty($do)) {
            return $this->sendError('Delivery order not found');
        }

        if($do->transaction_currency){
            $do->currency = (isset($do->transaction_currency->DecimalPlaces) && $do->transaction_currency->DecimalPlaces)?$do->transaction_currency->DecimalPlaces:2;
        }

        $do->docRefNo = Helper::getCompanyDocRefNo($do->companySystemID, $do->documentSystemID);
        $do->logoExists = false;
        $companyLogo = isset($do->company->logo_url) ? $do->company->logo_url:'';

        $disk = Helper::policyWiseDisk($do->company->masterCompanySystemIDReorting, 'local_public');

        $logoExists = Storage::disk($disk)->exists($do->company->logoPath);
        if ($logoExists) {
            $do->logoExists = true;
            $do->companyLogo = $companyLogo;
        }      
        
        $array = array('entity' => $do, 'lang' => $lang); // Pass lang to view
        $time = strtotime("now");
        $fileName = 'delivery_order_' . $id . '_' . $time . '.pdf';
        
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

        $html = view('print.delivery_order', $array);
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';

        try {
            $mpdf->WriteHTML($html);
            return $mpdf->Output($fileName, 'I');
        } catch (\Exception $e) {
            \Log::error('mPDF Error in printDeliveryOrder: ' . $e->getMessage());
            return $this->sendError('PDF generation failed: ' . $e->getMessage());
        }
    }


    public function getDeliveryOrderAmend(Request $request)
    {
        $input = $request->all();

        $deliveryOrderID = $input['deliveryOrderID'];

        $doData = DeliveryOrder::find($deliveryOrderID);
        if (empty($doData)) {
            return $this->sendError('Customer Invoice not found');
        }

        if ($doData->refferedBackYN != -1) {
            return $this->sendError('You cannot amend this delivery order');
        }

        $deliveryOrderArray = array_except($doData->toArray(),['isSUPDAmendAccess','isFrom','assetMaintenanceID','isVatEligible']);

        $storeDeliveryOrderHistory = DeliveryOrderRefferedback::insert($deliveryOrderArray);

        $fetchDeliveryOrderDetails = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)
            ->get();

//        if (!empty($fetchDeliveryOrderDetails)) {
//            foreach ($fetchDeliveryOrderDetails as $doDetail) {
//                $doDetail['timesReferred'] = $doData->timesReferred;
//            }
//        }

        $doDetailArray = $fetchDeliveryOrderDetails->toArray();

        unset($doDetailArray['assetMaintenanceID']);
        
        $storeDeliveryOrderDetaillHistory = DeliveryOrderDetailRefferedback::insert($doDetailArray);

        $fetchDocumentApproved = DocumentApproved::where('documentSystemCode', $deliveryOrderID)
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

        $deleteApproval = DocumentApproved::where('documentSystemCode', $deliveryOrderID)
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

        return $this->sendResponse($doData->toArray(), 'Delivery Order Amend successfully');
    }

    public function getInvoiceDetailsForDeliveryOrderPrintView(Request $request){
        $input = $request->all();
        $id = $input['id'];

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = CustomerInvoiceDirect::with(['company', 'secondarycompany', 'customer', 'tax', 'createduser', 'bankaccount', 'currency','report_currency', 'local_currency', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        },
            'issue_item_details' => function ($query) {
                $query->with(['uom_default', 'uom_issuing']);
            }
        ])->find($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        } else {
            return $this->sendResponse($customerInvoiceDirect, 'Customer Invoice Direct retrieved successfully');
        }
    }

    function getDeliveryDetailsForSQ(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $detail = DeliveryOrderDetail::where('quotationMasterID',$quotationMasterID)
            ->with(['sales_quotation_detail','uom_issuing',
                'master'=> function($query){
                    $query->with(['transaction_currency']);
                }])
            ->get();
        return $this->sendResponse($detail, 'Details retrieved successfully');
    }

    public function isLinkItem(Request $request) {
        $input = $request->all();
        $companyId = $input['companySystemID'];

        $addNewItem = CompanyPolicyMaster::where('companyPolicyCategoryID', 64)
        ->where('companySystemID', $companyId)
        ->first();

        if($addNewItem) {
            return $this->sendResponse($addNewItem->isYesNO, 'Details retrieved successfully');
        }else {
            return $this->sendResponse(false, 'Details retrieved successfully');
        }

    }

    public function getCommonFormData(Request $request) {
        $input = $request->all();
        $finacialYear =  \Helper::companyFinanceYear($input['companySystemID'],0);
        $companyFinancePeriod =  \Helper::companyFinancePeriod($input['companySystemID'],$finacialYear[0]->companyFinanceYearID,11);

        if($companyFinancePeriod && $finacialYear) {
            return ['finacialYear' => $finacialYear[0],'companyFinancePeriod' => $companyFinancePeriod[0]];
        }else {
            return $this->sendError("Financial year or Financial Period not found for this department.",500);
        }

    }


    public function validateDeliveryOrder(Request $request) {
         $input = $request->all();

         $companySystemID = $input['companySystemID'];
         $qntyCanIssue = 0;
         $deliveryOrder = DeliveryOrderDetail::with([
                'master'=> function($query){
                    $query->where('confirmedYN',0);
                    $query->where('approvedYN',0);
                }])->where('itemCodeSystem',$input['itemAutoID'])->where('documentSystemID',$input['documentSystemID'])->orderBy('deliveryOrderDetailID','DESC')->first();
        
        if($deliveryOrder) {
            $qntyCanIssue = ($deliveryOrder->currentWareHouseStockQty - $deliveryOrder->qtyIssued);
        }else {
            $qntyCanIssue = 0;
        }

          // check the item pending pending for approval in other modules
            $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $companySystemID)
//            ->where('wareHouseFrom', $customerInvoiceDirect->wareHouseSystemCode)
                ->select([
                    'erp_itemissuemaster.itemIssueAutoID',
                    'erp_itemissuemaster.companySystemID',
                    'erp_itemissuemaster.wareHouseFromCode',
                    'erp_itemissuemaster.itemIssueCode',
                    'erp_itemissuemaster.approved'
                ])
                ->groupBy(
                    'erp_itemissuemaster.itemIssueAutoID',
                    'erp_itemissuemaster.companySystemID',
                    'erp_itemissuemaster.wareHouseFromCode',
                    'erp_itemissuemaster.itemIssueCode',
                    'erp_itemissuemaster.approved'
                )
                ->whereHas('details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemAutoID']);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherItemIssueMaster)) {
                return $this->sendError("There is a Materiel Issue (" . $checkWhetherItemIssueMaster->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }

            $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)
//            ->where('locationFrom', $customerInvoiceDirect->wareHouseSystemCode)
                ->select([
                    'erp_stocktransfer.stockTransferAutoID',
                    'erp_stocktransfer.companySystemID',
                    'erp_stocktransfer.locationFrom',
                    'erp_stocktransfer.stockTransferCode',
                    'erp_stocktransfer.approved'
                ])
                ->groupBy(
                    'erp_stocktransfer.stockTransferAutoID',
                    'erp_stocktransfer.companySystemID',
                    'erp_stocktransfer.locationFrom',
                    'erp_stocktransfer.stockTransferCode',
                    'erp_stocktransfer.approved'
                )
                ->whereHas('details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemAutoID']);
                })
                ->where('approved', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherStockTransfer)) {
                return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }

            $checkWhetherInvoice = CustomerInvoiceDirect::where('companySystemID', $companySystemID)
                ->select([
                    'erp_custinvoicedirect.custInvoiceDirectAutoID',
                    'erp_custinvoicedirect.bookingInvCode',
                    'erp_custinvoicedirect.wareHouseSystemCode',
                    'erp_custinvoicedirect.approved'
                ])
                ->groupBy(
                    'erp_custinvoicedirect.custInvoiceDirectAutoID',
                    'erp_custinvoicedirect.companySystemID',
                    'erp_custinvoicedirect.bookingInvCode',
                    'erp_custinvoicedirect.wareHouseSystemCode',
                    'erp_custinvoicedirect.approved'
                )
                ->whereHas('issue_item_details', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemAutoID']);
                })
                ->where('approved', 0)
                ->where('canceledYN', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherInvoice)) {
                return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }
        //         // check in delivery order
        // $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
        //     ->select([
        //         'erp_delivery_order.deliveryOrderID',
        //         'erp_delivery_order.deliveryOrderCode'
        //     ])
        //     ->groupBy(
        //         'erp_delivery_order.deliveryOrderID',
        //         'erp_delivery_order.companySystemID'
        //     )
        //     ->whereHas('detail', function ($query) use ($companySystemID, $input) {
        //         $query->where('itemCodeSystem', $input['itemAutoID']);
        //     })
        //     ->where('approvedYN', 0)
        //     ->first();

        // if (!empty($checkWhetherDeliveryOrder)) {
        //         return $this->sendError("There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.",500);
        // }

         /*Check in purchase return*/
            $checkWhetherPR = PurchaseReturn::where('companySystemID', $companySystemID)
                ->select([
                    'erp_purchasereturnmaster.purhaseReturnAutoID',
                    'erp_purchasereturnmaster.companySystemID',
                    'erp_purchasereturnmaster.purchaseReturnLocation',
                    'erp_purchasereturnmaster.purchaseReturnCode',
                ])
                ->groupBy(
                    'erp_purchasereturnmaster.purhaseReturnAutoID',
                    'erp_purchasereturnmaster.companySystemID',
                    'erp_purchasereturnmaster.purchaseReturnLocation'
                )
                ->whereHas('details', function ($query) use ($input) {
                    $query->where('itemCode', $input['itemAutoID']);
                })
                ->where('approved', 0)
                ->first();

            if (!empty($checkWhetherPR)) {
                return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }

        return $this->sendResponse(["qnty"=>$qntyCanIssue,"data"=>true], 'Details retrieved successfully');
   
    }

    public function downloadQuotationItemUploadTemplate(Request $request) {
        $input = $request->all();
        $disk = Helper::policyWiseDisk($input['companySystemID'], 'public');
        if ($exists = Storage::disk($disk)->exists('delivery_order_template/delivery_order_template.xlsx')) {
            return Storage::disk($disk)->download('delivery_order_template/delivery_order_template.xlsx', 'delivery_order_template.xlsx');
        } else {
            return $this->sendError('Attachments not found', 500);
        }
    }

    
    public function amendDeliveryorderReview(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
          'deliveryMasterID' => 'required|integer',
        ]);

        if ($validator->fails()) {
             return $this->sendError($validator->messages(), 500);
        }

        $id = $input['deliveryMasterID'];

        $employee = \Helper::getEmployeeInfo();
        $emails = array();

        $masterData = DeliveryOrder::find($id);

        if (empty($masterData)) {
            return $this->sendError('Delivery Order not found');
        }

        if ($masterData->confirmedYN == 0) {
            return $this->sendError('You cannot return back to amend this Delivery order, it is not confirmed');
        }

        $isAPIDocument = DocumentSystemMapping::where('documentId',$id)->where('documentSystemID',71)->exists();
        if ($isAPIDocument){
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

        if($masterData->approvedYN == -1){
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
                $errorMessage = "Customer Invoice " . $validateVatReturnFilling['message'];
                return $this->sendError($errorMessage);
            }
        }
        

        $emailBody = '<p>' . $masterData->quotationCode . ' has been return back to amend by ' . $employee->empName . ' due to below reason.</p><p>Comment : ' . $input['returnComment'] . '</p>';
        $emailSubject = $masterData->quotationCode . ' has been return back to amend';

        DB::beginTransaction();
        try {
            $employee = \Helper::getEmployeeInfo();
            $amendCI = $this->deliveryOrderServices->amendDeliveryOrder($id,$masterData,$input,$employee);

            if(isset($amendCI['status']) && $amendCI['status'] == false){
                return $this->sendError($amendCI['message']);
            }


            DB::commit();
            return $this->sendResponse($masterData->toArray(), 'Return back to amend saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}

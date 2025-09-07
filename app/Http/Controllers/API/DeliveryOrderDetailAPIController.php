<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\helper\inventory;
use App\Http\Requests\API\CreateDeliveryOrderDetailAPIRequest;
use App\Http\Requests\API\UpdateDeliveryOrderDetailAPIRequest;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\ErpItemLedger;
use App\Models\Company;
use App\Models\DeliveryOrderDetail;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\PurchaseReturn;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\StockTransfer;
use App\Models\DocumentSubProduct;
use App\Models\ItemSerial;
use App\Models\Taxdetail;
use App\Repositories\DeliveryOrderDetailRepository;
use App\Jobs\AddMultipleItemsToDeliveryOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\ItemTracking;
use Illuminate\Support\Facades\Storage;
use Auth;
/**
 * Class DeliveryOrderDetailController
 * @package App\Http\Controllers\API
 */

class DeliveryOrderDetailAPIController extends AppBaseController
{
    /** @var  DeliveryOrderDetailRepository */
    private $deliveryOrderDetailRepository;

    public function __construct(DeliveryOrderDetailRepository $deliveryOrderDetailRepo)
    {
        $this->deliveryOrderDetailRepository = $deliveryOrderDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrderDetails",
     *      summary="Get a listing of the DeliveryOrderDetails.",
     *      tags={"DeliveryOrderDetail"},
     *      description="Get all DeliveryOrderDetails",
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
     *                  @SWG\Items(ref="#/definitions/DeliveryOrderDetail")
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
        $this->deliveryOrderDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->deliveryOrderDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $deliveryOrderDetails = $this->deliveryOrderDetailRepository->all();

        return $this->sendResponse($deliveryOrderDetails->toArray(), trans('custom.delivery_order_details_retrieved_successfully_1'));
    }

    /**
     * @param CreateDeliveryOrderDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/deliveryOrderDetails",
     *      summary="Store a newly created DeliveryOrderDetail in storage",
     *      tags={"DeliveryOrderDetail"},
     *      description="Store DeliveryOrderDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrderDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrderDetail")
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
     *                  ref="#/definitions/DeliveryOrderDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeliveryOrderDetailAPIRequest $request)
    {
        $input = $request->all();
        $companySystemID = $input['companySystemID'];
        $item = ItemMaster::find($input['itemCodeSystem']);
        if(empty($item)){
            return $this->sendError(trans('custom.item_not_found'),500);
        }

        $deliveryOrderMaster = DeliveryOrder::find($input['deliveryOrderID']);
        if(empty($deliveryOrderMaster)){
            return $this->sendError(trans('custom.delivery_order_not_found_1'),500);
        }
        $category = $item->financeCategoryMaster;

        $alreadyAdded = DeliveryOrder::where('deliveryOrderID', $input['deliveryOrderID'])
            ->whereHas('detail', function ($query) use ($input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->exists();

            if(($category != 2 )&& ($category != 4 ))
            {
                if ($alreadyAdded) {
                    return $this->sendError("Selected item is already added. Please check again", 500);
                }
            }

        if(DeliveryOrderDetail::where('deliveryOrderID',$input['deliveryOrderID'])->where('itemFinanceCategoryID','!=',$item->financeCategoryMaster)->exists()){
            return $this->sendError('Different finance category found. You can not add different finance category items for same order',500);
        }

        if($item->financeCategoryMaster==1){
            // check the item pending pending for approval in other delivery orders

            $checkWhether = DeliveryOrder::where('deliveryOrderID', '!=', $deliveryOrderMaster->deliveryOrderID)
                ->where('companySystemID', $companySystemID)
                ->select([
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.deliveryOrderCode'
                ])
                ->groupBy(
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.companySystemID'
                )
                ->whereHas('detail', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approvedYN', 0)
                ->first();
            if (!empty($checkWhether)) {
                return $this->sendError("There is a Delivery Order (" . $checkWhether->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
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
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
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
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
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
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->where('canceledYN', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherInvoice)) {
                return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }

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
                    $query->where('itemCode', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->first();

            if (!empty($checkWhetherPR)) {
                return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }
        }


        /* approved=0*/

        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->primaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->unit;
        $input['unitOfMeasureIssued'] = $item->unit;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
        $input['trackingType'] = $item->trackingType;
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        if (!empty($financeItemCategorySubAssigned)) {
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $input['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
            $input['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
            $input['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
            $input['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
        } else {
            return $this->sendError("Finance Item category sub assigned not found", 500);
        }

        if((!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']) && $item->financeCategoryMaster!=2){
            return $this->sendError(trans('custom.bs_account_cannot_be_null_for') . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }elseif (!$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']){
            return $this->sendError(trans('custom.cost_account_cannot_be_null_for') . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }elseif (!$input['financeCogsGLcodePL'] || !$input['financeCogsGLcodePLSystemID']){
            return $this->sendError(trans('custom.cogs_gl_account_cannot_be_null_for_2') . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }elseif (!$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']){
            return $this->sendError(trans('custom.revenue_account_cannot_be_null_for') . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }

        /*if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']
            || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']
            || !$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']) {
            return $this->sendError("Account code not updated.", 500);
        }*/
        $input['convertionMeasureVal'] = 1;

        // $input['qtyIssued'] = 0;
        // $input['qtyIssuedDefaultMeasure'] = 0;

        $data = array(
            'companySystemID' => $companySystemID,
            'itemCodeSystem' => $input['itemCodeSystem'],
            'wareHouseId' => $deliveryOrderMaster->wareHouseSystemCode
        );

        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $input['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];

        $input['wacValueLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $input['wacValueReporting'] = $itemCurrentCostAndQty['wacValueReporting'];

        if($item->financeCategoryMaster==1){
            if ($input['currentStockQty'] <= 0) {
                return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['currentWareHouseStockQty'] <= 0) {
                return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['wacValueLocal'] == 0 || $input['wacValueReporting'] == 0) {
                return $this->sendError("Cost is 0. You cannot issue.", 500);
            }

            if ($input['wacValueLocal'] < 0 || $input['wacValueReporting'] < 0) {
                return $this->sendError("Cost is negative. You cannot issue.", 500);
            }
        }

        if($deliveryOrderMaster->transactionCurrencyID == $deliveryOrderMaster->companyLocalCurrencyID){

            $input['unitTransactionAmount'] = $input['wacValueLocal'];
            $input['companyLocalAmount'] = $input['wacValueLocal'];

        }elseif ($deliveryOrderMaster->transactionCurrencyID == $deliveryOrderMaster->companyReportingCurrencyID){

            $input['unitTransactionAmount'] = $input['wacValueReporting'];
            $input['companyReportingAmount'] = $input['wacValueReporting'];

        }else{

            $currencyConversion = Helper::currencyConversion($deliveryOrderMaster->companySystemID,$deliveryOrderMaster->companyLocalCurrencyID,$deliveryOrderMaster->transactionCurrencyID,$input['wacValueLocal']);
            if(!empty($currencyConversion)){
                $input['unitTransactionAmount'] = $currencyConversion['documentAmount'];
            }
        }

        $amounts = $this->updateAmountsByTransactionAmount($input,$deliveryOrderMaster);
        $input['companyLocalAmount'] = $amounts['companyLocalAmount'];
        $input['companyReportingAmount'] = $amounts['companyReportingAmount'];

        $input['transactionCurrencyID'] = $deliveryOrderMaster->transactionCurrencyID;
        $input['transactionCurrencyER'] = $deliveryOrderMaster->transactionCurrencyER;
        $input['companyLocalCurrencyID'] = $deliveryOrderMaster->companyLocalCurrencyID;
        $input['companyLocalCurrencyER'] = $deliveryOrderMaster->companyLocalCurrencyER;
        $input['companyReportingCurrencyID'] = $deliveryOrderMaster->companyReportingCurrencyID;
        $input['companyReportingCurrencyER'] = $deliveryOrderMaster->companyReportingCurrencyER;

        $input['discountPercentage'] = 0;
        $input['discountAmount'] = 0;
        $input['transactionAmount'] = 0;

        if ($deliveryOrderMaster->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($deliveryOrderMaster->companySystemID, $input['itemCodeSystem'], $deliveryOrderMaster->customerID,0);
            $input['VATPercentage'] = $vatDetails['percentage'];
            $input['VATApplicableOn'] = $vatDetails['applicableOn'];
            $input['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $input['VATAmount'] = 0;
            if (isset($input['unittransactionAmount']) && $input['unittransactionAmount'] > 0) {
                $input['VATAmount'] = (($input['unittransactionAmount'] / 100) * $vatDetails['percentage']);
            }
            $currencyConversionVAT = \Helper::currencyConversion($deliveryOrderMaster->companySystemID, $deliveryOrderMaster->transactionCurrencyID, $deliveryOrderMaster->transactionCurrencyID, $input['VATAmount']);

            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }



        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->create($input);

        $resVat = $this->updateVatFromSalesQuotation($deliveryOrderMaster->deliveryOrderID);
        if (!$resVat['status']) {
           return $this->sendError($resVat['message']); 
        } 

        // update maser table amount field
        $this->deliveryOrderDetailRepository->updateMasterTableTransactionAmount($input['deliveryOrderID']);

        return $this->sendResponse($deliveryOrderDetail->toArray(), trans('custom.delivery_order_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrderDetails/{id}",
     *      summary="Display the specified DeliveryOrderDetail",
     *      tags={"DeliveryOrderDetail"},
     *      description="Get DeliveryOrderDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderDetail",
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
     *                  ref="#/definitions/DeliveryOrderDetail"
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
        /** @var DeliveryOrderDetail $deliveryOrderDetail */
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            return $this->sendError(trans('custom.delivery_order_detail_not_found'));
        }

        return $this->sendResponse($deliveryOrderDetail->toArray(), trans('custom.delivery_order_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDeliveryOrderDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/deliveryOrderDetails/{id}",
     *      summary="Update the specified DeliveryOrderDetail in storage",
     *      tags={"DeliveryOrderDetail"},
     *      description="Update DeliveryOrderDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrderDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrderDetail")
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
     *                  ref="#/definitions/DeliveryOrderDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeliveryOrderDetailAPIRequest $request)
    {
        $input = array_except($request->all(), ['uom_default', 'uom_issuing','item_by','issueUnits', 'quotation']);

        $input = $this->convertArrayToValue($input);

        /** @var DeliveryOrderDetail $deliveryOrderDetail */
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            return $this->sendError(trans('custom.delivery_order_detail_not_found'));
        }

        $deliveryOrderMaster = DeliveryOrder::find($deliveryOrderDetail->deliveryOrderID);
        if(empty($deliveryOrderMaster)){
            return $this->sendError(trans('custom.delivery_order_not_found_1'),500);
        }

        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($deliveryOrderMaster->documentSystemID, $deliveryOrderMaster->companySystemID, $id, $input);

        if (!$validateVATCategories['status']) {
            return $this->sendError($validateVATCategories['message'], 500, array('type' => 'vat'));
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
        }

        $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'];

        if($deliveryOrderDetail->itemFinanceCategoryID == 1){
            if ($deliveryOrderDetail->currentStockQty <= 0) {
                $this->deliveryOrderDetailRepository->update(['transactionAmount' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
            }

            if ($deliveryOrderDetail->currentWareHouseStockQty <= 0) {
                $this->deliveryOrderDetailRepository->update(['transactionAmount' => 0,'qtyIssued' => 0], $id);
                return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['qtyIssuedDefaultMeasure'] > $deliveryOrderDetail->currentStockQty) {
                $this->deliveryOrderDetailRepository->update(['transactionAmount' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Current stock Qty is: " . $deliveryOrderDetail->currentStockQty . " .You cannot issue more than the current stock qty.", 500);
            }

            if ($input['qtyIssuedDefaultMeasure'] > $deliveryOrderDetail->currentWareHouseStockQty) {
                $this->deliveryOrderDetailRepository->update(['transactionAmount' => 0,'qtyIssued' => 0], $id);
                return $this->sendError("Current warehouse stock Qty is: " . $deliveryOrderDetail->currentWareHouseStockQty . " .You cannot issue more than the current warehouse stock qty.", 500);
            }
        }
        // discount calculation
        $discountedUnit = $input['unitTransactionAmount'];

        if(isset($input['by']) && ($input['by'] == 'amount' || $input['by'] == 'percentage')){
            if ($input['by']=='amount' && $input['discountAmount'] > 0){
                $discountedUnit = $input['unitTransactionAmount']-$input['discountAmount'];
                if($input['unitTransactionAmount']){
                    $input['discountPercentage'] = $input['discountAmount']/$input['unitTransactionAmount']*100;
                }
            }elseif ($input['by'] == 'percentage' && $input['discountPercentage'] != 0){
                $discountedUnit = $input['unitTransactionAmount']-($input['discountPercentage']/100*$input['unitTransactionAmount']);
                $input['discountAmount'] = $input['unitTransactionAmount']-$discountedUnit;
            }else{
                $input['discountPercentage'] = 0;
                $input['discountAmount'] = 0;
            }
        }else{

            if($discountedUnit> 0 ){

                if($input['discountPercentage'] != 0){
                    $discountedUnit = $input['unitTransactionAmount']-($input['unitTransactionAmount']*$input['discountPercentage']/100);
                    $input['discountAmount'] = $input['unitTransactionAmount']-$discountedUnit;
                }else{
                    $discountedUnit = $input['unitTransactionAmount']-$input['discountAmount'];
                    if($input['unitTransactionAmount']){
                        $input['discountPercentage'] = $input['discountAmount']/$input['unitTransactionAmount']*100;
                    }

                }

            }

        }

         $netUnitAmount = 0;
         if ($input['VATApplicableOn'] === 1) { // before discount
            $netUnitAmount = $input["unitTransactionAmount"];
         } else {
            $netUnitAmount = $discountedUnit;
         }

        if(isset($input['by']) && ($input['by'] == 'VATPercentage' || $input['by'] == 'VATAmount')){
            if ($input['by'] === 'VATPercentage') {
              $input["VATAmount"] = $netUnitAmount * $input["VATPercentage"] / 100;
            } else if ($input['by'] === 'VATAmount') {
                if ($netUnitAmount != 0) {
                    $input["VATPercentage"] = ($input["VATAmount"] / $netUnitAmount) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        } else {
            if ($input['VATPercentage'] != 0) {
              $input["VATAmount"] = $netUnitAmount * $input["VATPercentage"] / 100;
            } else {
                if ($netUnitAmount != 0) {
                    $input["VATPercentage"] = ($input["VATAmount"] / $netUnitAmount) * 100;
                } else {
                    $input["VATPercentage"] = 0; 
                }
            }
        }

        $currencyConversionVAT = \Helper::currencyConversion($deliveryOrderMaster->companySystemID, $deliveryOrderMaster->transactionCurrencyID, $deliveryOrderMaster->transactionCurrencyID, $input['VATAmount']);

        $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
        $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        $input['VATAmount'] = \Helper::roundValue($input['VATAmount']);

        $input['transactionAmount'] = $discountedUnit*$input['qtyIssuedDefaultMeasure'];

        $amounts = $this->updateAmountsByTransactionAmount($input,$deliveryOrderMaster);
        $input['companyLocalAmount'] = $amounts['companyLocalAmount'];
        $input['companyReportingAmount'] = $amounts['companyReportingAmount'];

        $input['unitTransactionAmount'] = Helper::roundValue($input['unitTransactionAmount']);
        $input['discountPercentage'] = Helper::roundValue($input['discountPercentage']);
        $input['discountAmount'] = Helper::roundValue($input['discountAmount']);
        $input['transactionAmount'] = Helper::roundValue($input['transactionAmount']);
        $input['companyLocalAmount'] = Helper::roundValue($input['companyLocalAmount']);
        $input['companyReportingAmount'] = Helper::roundValue($input['companyReportingAmount']);

        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->update($input, $id);

       


        $resVat = $this->updateVatFromSalesQuotation($deliveryOrderMaster->deliveryOrderID);
        if (!$resVat['status']) {
           return $this->sendError($resVat['message']); 
        } 

        // update maser table amount field
        $this->deliveryOrderDetailRepository->updateMasterTableTransactionAmount($deliveryOrderDetail->deliveryOrderID);

        return $this->sendResponse($deliveryOrderDetail->toArray(), trans('custom.deliveryorderdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/deliveryOrderDetails/{id}",
     *      summary="Remove the specified DeliveryOrderDetail from storage",
     *      tags={"DeliveryOrderDetail"},
     *      description="Delete DeliveryOrderDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderDetail",
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
        /** @var DeliveryOrderDetail $deliveryOrderDetail */
        $deliveryOrderDetail = $this->deliveryOrderDetailRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetail)) {
            return $this->sendError(trans('custom.delivery_order_detail_not_found'));
        }
        $deliveryOrder = DeliveryOrder::find($deliveryOrderDetail->deliveryOrderID);
        if(!empty($deliveryOrder)){
            if($deliveryOrder->confirmedYN == 1){
                return $this->sendError(trans('custom.order_was_already_confirmed_you_cannot_delete'),500);
            }

            // $taxExist = Taxdetail::where('documentSystemCode', $deliveryOrder->deliveryOrderID)
            //                     ->where('documentSystemID', $deliveryOrder->documentSystemID)
            //                     ->exists();
            // if($taxExist && $deliveryOrder->orderType != 3){
            //     return $this->sendError('VAT is added. Please delete the tax and try again.',500);
            // }

            $deliveryOrderDetail->delete();


             if ($deliveryOrderDetail->trackingType == 2) {
                $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $deliveryOrder->documentSystemID)
                                                             ->where('documentDetailID', $id)
                                                             ->where('sold', 1)
                                                             ->first();

                if ($validateSubProductSold) {
                    return $this->sendError(trans('custom.you_cannot_delete_this_line_item_serial_details_ar'), 422);
                }

                $subProduct = DocumentSubProduct::where('documentSystemID', $deliveryOrder->documentSystemID)
                                                 ->where('documentDetailID', $id);

                $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
                $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

                if (count($productInIDs) > 0) {
                    $updateSerial = ItemSerial::whereIn('id', $serialIds)
                                              ->update(['soldFlag' => 0]);

                    $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
                                              ->update(['sold' => 0, 'soldQty' => 0]);

                    $subProduct->delete();
                }
            } else if ($deliveryOrderDetail->trackingType == 1) {
                $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($deliveryOrder->documentSystemID, $id);

                if (!$deleteBatch['status']) {
                    return $this->sendError($deleteBatch['message'], 422);
                }
            }

            // update maser table amount field
            $this->deliveryOrderDetailRepository->updateMasterTableTransactionAmount($deliveryOrderDetail->deliveryOrderID);

            if($deliveryOrder->orderType == 2 || $deliveryOrder->orderType == 3){

                if (!empty($deliveryOrderDetail->deliveryOrderDetailID) && !empty($deliveryOrderDetail->deliveryOrderID)) {
                    $updateQuotationMaster = QuotationMaster::find($deliveryOrderDetail->quotationMasterID)
                        ->update([
                            'selectedForDeliveryOrder' => 0,
                            'closedYN' => 0
                        ]);


                    //checking the fullyOrdered or partial in po
                    $detailSum = DeliveryOrderDetail::select(DB::raw('COALESCE(SUM(qtyIssued),0) as totalQty'))
                        ->where('quotationDetailsID', $deliveryOrderDetail->quotationDetailsID)
                        ->first();

                    $updatedQuoQty = $detailSum['totalQty'];

                    if ($updatedQuoQty == 0) {
                        $fullyOrdered = 0;
                    } else {
                        $fullyOrdered = 1;
                    }

                    QuotationDetails::where('quotationDetailsID', $deliveryOrderDetail->quotationDetailsID)
                        ->update([ 'fullyOrdered' => $fullyOrdered, 'doQuantity' => $updatedQuoQty]);

                    $this->updateSalesQuotationDeliveryStatus($deliveryOrderDetail->quotationMasterID);

                      $resVat = $this->updateVatEligibilityOfDeliveryOrder($deliveryOrderDetail->deliveryOrderID);
                      if (!$resVat['status']) {
                           return $this->sendError($resVat['message']); 
                      } 
                }


                //calculate tax amount according to the percantage for tax update

                //getting total sum of PO detail Amount
//                $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
//                    ->where('purchaseOrderMasterID', $purchaseOrder->purchaseOrderID)
//                    ->first();
//
//                //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
//                if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
//                    $calculatVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);
//
//                    $currencyConversionVatAmount = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculatVatAmount);
//
//                    $updatePOMaster = ProcumentOrder::find($purchaseOrder->purchaseOrderID)
//                        ->update([
//                            'VATAmount' => $calculatVatAmount,
//                            'VATAmountLocal' => round($currencyConversionVatAmount['localAmount'], 8),
//                            'VATAmountRpt' => round($currencyConversionVatAmount['reportingAmount'], 8)
//                        ]);
//                }

            }

//            $taxExist = Taxdetail::where('documentSystemCode', $deliveryOrder->deliveryOrderID)
//                ->where('documentSystemID', $deliveryOrder->documentSystemiD)
//                ->exists();
//            if($taxExist){
//                return $this->sendError('Tax is added. Please delete the tax and try again.',500);
//            }

            $resVat = $this->updateVatFromSalesQuotation($deliveryOrderDetail->deliveryOrderID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 
        }


        return $this->sendResponse([],trans('custom.delivery_order_detail_deleted_successfully'));
    }

    private function updateAmountsByTransactionAmount($input,$deliveryOrder){
        $output = array();
        if($deliveryOrder->transactionCurrencyID != $deliveryOrder->companyLocalCurrencyID){
            $currencyConversion = Helper::currencyConversion($deliveryOrder->companySystemID,$deliveryOrder->transactionCurrencyID,$deliveryOrder->transactionCurrencyID,$input['unitTransactionAmount']);
            if(!empty($currencyConversion)){
                $output['companyLocalAmount'] = $currencyConversion['localAmount'];
            }
        }else{
            $output['companyLocalAmount'] = $input['unitTransactionAmount'];
        }

        if($deliveryOrder->transactionCurrencyID != $deliveryOrder->companyReportingCurrencyID){
            $currencyConversion = Helper::currencyConversion($deliveryOrder->companySystemID,$deliveryOrder->transactionCurrencyID,$deliveryOrder->transactionCurrencyID,$input['unitTransactionAmount']);
            if(!empty($currencyConversion)){
                $output['companyReportingAmount'] = $currencyConversion['reportingAmount'];
            }
        }else{
            $output['companyReportingAmount'] = $input['unitTransactionAmount'];
        }

        return $output;
    }


    public function storeDeliveryDetailFromSalesQuotation(Request $request)
    {
        $input = $request->all();
        $DODetail_arr = array();
        $deliveryOrderID = $input['deliveryOrderID'];

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        $inputDetails = $input['detailTable'];
        $inputDetails = collect($inputDetails)->where('isChecked',1)->toArray();
        $financeCategories = collect($inputDetails)->pluck('itemCategory')->toArray();
        if (count(array_unique($financeCategories)) > 1) {
            return $this->sendError(trans('custom.multiple_finance_category_cannot_be_added_differen_1'),500);
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => 'DO quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
            }
        }

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $QuoDetailExist = DeliveryOrderDetail::select(DB::raw('quotationDetailsID,itemPrimaryCode'))
                    ->where('deliveryOrderID', $deliveryOrderID)
                    ->where('itemCodeSystem', $itemExist['itemAutoID'])
                    ->get();


                $item = ItemAssigned::with(['item_master'])
                ->where('itemCodeSystem', $itemExist['itemAutoID'])
                ->where('companySystemID', $itemExist['companySystemID'])
                ->first();

                
                $QuoDetailExistDetails = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)

                    ->where('itemCodeSystem', $itemExist['itemAutoID'])
                    ->first();
                if (!empty($QuoDetailExistDetails)) {
                    if(isset($item->financeCategoryMaster) && $item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                    {
                        if($QuoDetailExistDetails->qtyIssued + (int) $inputDetails[0]['noQty'] <= $QuoDetailExistDetails->requestedQty) {
                            $QuoDetailExistDetails->qtyIssued += (int)$inputDetails[0]['noQty'];
                            $QuoDetailExistDetails->save();
                    }
                }
                }else {
                    if (!empty($QuoDetailExist)) 
                    {
                        if(isset($item->financeCategoryMaster) && $item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                        {
                            foreach ($QuoDetailExist as $row) {
                                $itemDrt = $row['itemPrimaryCode'] . " already exist";
                                $itemExistArray[] = [$itemDrt];
                            }
                        }

                    }
                }        
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

        $deliveryOrder = DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->first();
        //check PO segment is correct with PR pull segment

        /*foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {

                $qoMaster = QuotationMaster::find($itemExist['quotationMasterID']);

                if($deliveryOrder->serviceLineSystemID != $qoMaster->serviceLineSystemID){
                    return $this->sendError("Segment is different from order");
                }
            }
        }*/

        //check stock and wac for selected item

        foreach ($input['detailTable'] as $row) {

            if ($row['isChecked'] && $row['noQty'] > 0) {

                $data = array(
                    'companySystemID' => $deliveryOrder->companySystemID,
                    'itemCodeSystem' => $row['itemAutoID'],
                    'wareHouseId' => $deliveryOrder->wareHouseSystemCode
                );

                $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
                $currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                $currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                $wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
                $wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];

                if($row['itemCategory'] == 1){
                    if ($currentStockQty <= 0) {
                        return $this->sendError("Stock Qty is 0 for ".$row['itemSystemCode'].". You cannot issue.", 500);
                    }

                    if ($currentWareHouseStockQty <= 0) {
                        return $this->sendError("Warehouse stock Qty is 0 for ".$row['itemSystemCode'].". You cannot issue.", 500);
                    }

                    if ($wacValueLocal == 0 || $wacValueReporting == 0) {
                        return $this->sendError("WAC Cost is 0 for  ".$row['itemSystemCode'].". You cannot issue.", 500);
                    }

                    if ($wacValueLocal < 0 || $wacValueReporting < 0) {
                        return $this->sendError("WAC Cost is negative for ".$row['itemSystemCode'].". You cannot issue.", 500);
                    }

                    if ($row['noQty'] > $currentStockQty) {
                        return $this->sendError('Insufficient Stock Qty for '.$row['itemSystemCode'], 500);
                    }

                    if ($row['noQty'] > $currentWareHouseStockQty) {
                        return $this->sendError('Insufficient Warehouse Qty for '.$row['itemSystemCode'], 500);
                    }

                    /*pending approval checking*/
                    // check the item pending pending for approval in other delivery orders

                    $checkWhether = DeliveryOrder::where('deliveryOrderID', '!=', $deliveryOrder->deliveryOrderID)
                        ->where('companySystemID', $row['companySystemID'])
                        ->select([
                            'erp_delivery_order.deliveryOrderID',
                            'erp_delivery_order.deliveryOrderCode'
                        ])
                        ->groupBy(
                            'erp_delivery_order.deliveryOrderID',
                            'erp_delivery_order.companySystemID'
                        )
                        ->whereHas('detail', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approvedYN', 0)
                        ->first();

                    if (!empty($checkWhether)) {
                        return $this->sendError("There is a Delivery Order (" . $checkWhether->deliveryOrderCode . ") pending for approval for ".$row['itemSystemCode'].". Please check again.", 500);
                    }


                    // check the item pending pending for approval in other modules
                    $checkWhetherItemIssueMaster = ItemIssueMaster::where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('details', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherItemIssueMaster)) {
                        return $this->sendError("There is a Materiel Issue (" . $checkWhetherItemIssueMaster->itemIssueCode . ") pending for approval for ".$row['itemSystemCode'].". Please check again.", 500);
                    }

                    $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('details', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherStockTransfer)) {
                        return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for ".$row['itemSystemCode'].". Please check again.", 500);
                    }

                    $checkWhetherInvoice = CustomerInvoiceDirect::where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('issue_item_details', function ($query) use ($row) {
                            $query->where('itemCodeSystem', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->where('canceledYN', 0)
                        ->first();
                    /* approved=0*/

                    if (!empty($checkWhetherInvoice)) {
                        return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for ".$row['itemSystemCode'].". Please check again.", 500);
                    }

                    /*Check in purchase return*/
                    $checkWhetherPR = PurchaseReturn::where('companySystemID', $row['companySystemID'])
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
                        ->whereHas('details', function ($query) use ($row) {
                            $query->where('itemCode', $row['itemAutoID']);
                        })
                        ->where('approved', 0)
                        ->first();

                    if (!empty($checkWhetherPR)) {
                        return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                    }
                }

            }
        }


        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $qoMaster = QuotationMaster::find($new['quotationMasterID']);

                $qoDetailExist = DeliveryOrderDetail::select(DB::raw('deliveryOrderDetailID'))
                    ->where('deliveryOrderID', $deliveryOrderID)
                    ->where('quotationDetailsID', $new['quotationDetailsID'])
                    ->first();

                if (empty($qoDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in po
                        $detailSum = DeliveryOrderDetail::select(DB::raw('COALESCE(SUM(qtyIssued),0) as totalNoQty'))
                            ->where('quotationDetailsID', $new['quotationDetailsID'])
                            ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['requestedQty'] == $totalAddedQty) {
                            $fullyOrdered = 2;
                        } else {
                            $fullyOrdered = 1;
                        }


                        // checking the qty request is matching with sum total
                        if ($new['requestedQty'] >= $new['noQty']) {


                            $DODetail_arr['deliveryOrderID'] = $deliveryOrderID;
                            $DODetail_arr['companySystemID'] = $new['companySystemID'];
                            $DODetail_arr['documentSystemID'] = 71;
                            $DODetail_arr['serviceLineSystemID'] = $new['serviceLineSystemID'];
                            $DODetail_arr['quotationMasterID'] = $new['quotationMasterID'];
                            $DODetail_arr['quotationDetailsID'] = $new['quotationDetailsID'];
                            $DODetail_arr['itemCodeSystem'] = $new['itemAutoID'];
                            $DODetail_arr['itemPrimaryCode'] = $new['itemSystemCode'];
                            $DODetail_arr['itemDescription'] = $new['itemDescription'];


                            if ($qoMaster->documentSystemID == 67) {
                                $vatDetails = TaxService::getVATDetailsByItem($deliveryOrder->companySystemID, $new['itemAutoID'], $deliveryOrder->customerID,0);
                                $DODetail_arr['VATApplicableOn'] = $vatDetails['applicableOn'];
                                $DODetail_arr['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $DODetail_arr['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                $DODetail_arr['VATPercentage'] = $vatDetails['percentage'];
                                $DODetail_arr['VATAmount'] = 0;

                                if (isset($new['unittransactionAmount']) && $new['unittransactionAmount'] > 0) {
                                    $DODetail_arr['VATAmount'] = (($new['unittransactionAmount'] / 100) * $vatDetails['percentage']);
                                }
                                $currencyConversionVAT = \Helper::currencyConversion($deliveryOrder->companySystemID, $deliveryOrder->transactionCurrencyID, $deliveryOrder->transactionCurrencyID, $DODetail_arr['VATAmount']);

                                $DODetail_arr['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                $DODetail_arr['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                            } else {
                                $DODetail_arr['VATPercentage'] = $new['VATPercentage'];
                                $DODetail_arr['VATAmount'] = $new['VATAmount'];
                                $DODetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                                $DODetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                                $DODetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                                $DODetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];
                                $DODetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                            }

                            $data = array(
                                'companySystemID' => $deliveryOrder->companySystemID,
                                'itemCodeSystem' => $new['itemAutoID'],
                                'wareHouseId' => $deliveryOrder->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $DODetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $DODetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $DODetail_arr['wacValueLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                            $DODetail_arr['wacValueReporting'] = $itemCurrentCostAndQty['wacValueReporting'];
                            $DODetail_arr['convertionMeasureVal'] = 1;

                            $item = ItemMaster::find($new['itemAutoID']);
                            if(empty($item)){
                                return $this->sendError(trans('custom.item_not_found'),500);
                            }

                            $DODetail_arr['itemFinanceCategoryID'] = $item->financeCategoryMaster;
                            $DODetail_arr['itemFinanceCategorySubID'] = $item->financeCategorySub;
                            $DODetail_arr['trackingType'] = $item->trackingType;

                            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $new['companySystemID'])
                                ->where('mainItemCategoryID', $DODetail_arr['itemFinanceCategoryID'])
                                ->where('itemCategorySubID', $DODetail_arr['itemFinanceCategorySubID'])
                                ->first();

                            if (!empty($financeItemCategorySubAssigned)) {
                                $DODetail_arr['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                                $DODetail_arr['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                                $DODetail_arr['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                                $DODetail_arr['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                                $DODetail_arr['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                                $DODetail_arr['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                                $DODetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $DODetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError("Finance Item category sub assigned not found", 500);
                            }

                            if((!$DODetail_arr['financeGLcodebBS'] || !$DODetail_arr['financeGLcodebBSSystemID']) && $item->financeCategoryMaster != 2){
                                return $this->sendError(trans('custom.bs_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }elseif (!$DODetail_arr['financeGLcodePL'] || !$DODetail_arr['financeGLcodePLSystemID']){
                                return $this->sendError(trans('custom.cost_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }elseif (!$DODetail_arr['financeCogsGLcodePL'] || !$DODetail_arr['financeCogsGLcodePLSystemID']){
                                return $this->sendError(trans('custom.cogs_gl_account_cannot_be_null_for_2') . $new['itemSystemCode'], 500);
                            }elseif (!$DODetail_arr['financeGLcodeRevenueSystemID'] || !$DODetail_arr['financeGLcodeRevenue']){
                                return $this->sendError(trans('custom.revenue_account_cannot_be_null_for') . $new['itemSystemCode'], 500);
                            }

                            /*if (!$DODetail_arr['financeGLcodebBS'] || !$DODetail_arr['financeGLcodebBSSystemID']
                                || !$DODetail_arr['financeGLcodePL'] || !$DODetail_arr['financeGLcodePLSystemID']
                                || !$DODetail_arr['financeGLcodeRevenueSystemID'] || !$DODetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }*/


                            $DODetail_arr['transactionCurrencyID'] = $deliveryOrder->transactionCurrencyID;
                            $DODetail_arr['transactionCurrencyER'] = $deliveryOrder->transactionCurrencyER;
                            $DODetail_arr['companyLocalCurrencyID'] = $deliveryOrder->companyLocalCurrencyID;
                            $DODetail_arr['companyLocalCurrencyER'] = $deliveryOrder->companyLocalCurrencyER;
                            $DODetail_arr['companyReportingCurrencyID'] = $deliveryOrder->companyReportingCurrencyID;
                            $DODetail_arr['companyReportingCurrencyER'] = $deliveryOrder->companyReportingCurrencyER;

                            $DODetail_arr['itemUnitOfMeasure'] = $new['unitOfMeasureID'];
                            $DODetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureID'];
                            $DODetail_arr['qtyIssued'] = $new['noQty'];
                            $DODetail_arr['qtyIssuedDefaultMeasure'] = $new['noQty'];

                            $balanceQty = ($new['requestedQty'] - $new['doTakenQty']);
                            $DODetail_arr['balanceQty'] = $balanceQty;
                            $DODetail_arr['requestedQty'] = $new['requestedQty'];

                            $DODetail_arr['unitTransactionAmount'] = $new['unittransactionAmount'];
                            $DODetail_arr['discountPercentage'] = $new['discountPercentage'];
                            $DODetail_arr['discountAmount'] = $new['discountAmount'];

                            $totalNetcost = ($new['unittransactionAmount'] - $new['discountAmount']) * $new['noQty'];

                            $DODetail_arr['transactionAmount'] = $totalNetcost;

                            $amounts = $this->updateAmountsByTransactionAmount($DODetail_arr,$deliveryOrder);
                            $DODetail_arr['companyLocalAmount'] = $amounts['companyLocalAmount'];
                            $DODetail_arr['companyReportingAmount'] = $amounts['companyReportingAmount'];

                            $DODetail_arr['unitTransactionAmount'] = Helper::roundValue($DODetail_arr['unitTransactionAmount']);
                            $DODetail_arr['discountPercentage'] = Helper::roundValue($DODetail_arr['discountPercentage']);
                            $DODetail_arr['discountAmount'] = Helper::roundValue($DODetail_arr['discountAmount']);
                            $DODetail_arr['transactionAmount'] = Helper::roundValue($DODetail_arr['transactionAmount']);
                            $DODetail_arr['companyLocalAmount'] = Helper::roundValue($DODetail_arr['companyLocalAmount']);
                            $DODetail_arr['companyReportingAmount'] = Helper::roundValue($DODetail_arr['companyReportingAmount']);

                            $this->deliveryOrderDetailRepository->create($DODetail_arr);

                            QuotationDetails::where('quotationDetailsID', $new['quotationDetailsID'])
                                ->update(['fullyOrdered' => $fullyOrdered, 'doQuantity' => $totalAddedQty]);

                        }

                    }
                }

                //check all details fullyOrdered in PR Master
                $quoMasterfullyOrdered = QuotationDetails::where('quotationMasterID', $new['quotationMasterID'])
                    ->whereIn('fullyOrdered', [1, 0])
                    ->get()->toArray();
                if (empty($quoMasterfullyOrdered)) {
                      $updateQuotation = QuotationMaster::find($new['quotationMasterID']);
                      $updateQuotation->selectedForDeliveryOrder = -1 ;
                      $updateQuotation->closedYN = -1 ;
                      $updateQuotation->save();
                   
                } else {
                    $updateQuotation = QuotationMaster::find($new['quotationMasterID'])
                        ->update([
                            'selectedForDeliveryOrder' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateSalesQuotationDeliveryStatus($new['quotationMasterID']);

                // update maser table amount field
                $this->deliveryOrderDetailRepository->updateMasterTableTransactionAmount($input['deliveryOrderID']);

            }

            $resVat = $this->updateVatFromSalesQuotation($deliveryOrderID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            $resVat = $this->updateVatEligibilityOfDeliveryOrder($deliveryOrderID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            DB::commit();
            return $this->sendResponse([], trans('custom.delivery_order_details_saved_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'). $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }

    public function updateVatEligibilityOfDeliveryOrder($deliveryOrderID)
    { 
        $doDetailData = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)
                                           ->groupBy('quotationMasterID')
                                           ->get();

        $quMasterIds = $doDetailData->pluck('quotationMasterID');

        $quotaionVatEligibleCheck = QuotationMaster::whereIn('quotationMasterID', $quMasterIds)
                                                   ->where('vatRegisteredYN', 1)
                                                   ->where('customerVATEligible', 1)
                                                   ->first();
        $vatRegisteredYN = 0;
        $customerVATEligible = 0;
        if ($quotaionVatEligibleCheck) {
            $customerVATEligible = 1;
            $vatRegisteredYN = 1;
        } 

        $updateRes = DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)
                                  ->update(['vatRegisteredYN' => $vatRegisteredYN, 'customerVATEligible' => $customerVATEligible]);

        return ['status' => true];
    }

    public function updateVatFromSalesQuotation($deliveryOrderID)
    {
        $invoiceDetails = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)
                                            ->with(['sales_quotation_detail'])
                                            ->get();

        $deliveryOrderData = DeliveryOrder::find($deliveryOrderID);

        $totalVATAmount = 0;

        foreach ($invoiceDetails as $key => $value) {
            if ($deliveryOrderData->orderType == 3) {
                $totalVATAmount += $value->qtyIssued * ((isset($value->sales_quotation_detail->VATAmount) && !is_null($value->sales_quotation_detail->VATAmount)) ? $value->sales_quotation_detail->VATAmount : 0);
            } else {
                $totalVATAmount += ($value->qtyIssued * $value->VATAmount);
            }
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $deliveryOrderID)
                              ->where('documentSystemID', 71)
                              ->delete();
        if ($totalVATAmount > 0) {

            $res = $this->saveDeliveryOrderTaxDetails($deliveryOrderID, $totalVATAmount);

            if (!$res['status']) {
               return ['status' => false, 'message' => $res['message']]; 
            } 
        } else {
            $vatAmount['vatOutputGLCodeSystemID'] = null;
            $vatAmount['vatOutputGLCode'] = null;
            $vatAmount['VATPercentage'] = 0;
            $vatAmount['VATAmount'] = 0;
            $vatAmount['VATAmountLocal'] = 0;
            $vatAmount['VATAmountRpt'] = 0;

            DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->update($vatAmount);
        }

        return ['status' => true];
    }


     public function saveDeliveryOrderTaxDetails($deliveryOrderID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => trans('custom.delivery_order_not_found_3')];
        }

        $invoiceDetail = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Delivery Order Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->transactionCurrencyID);

        $totalDetail = DeliveryOrderDetail::select(DB::raw("SUM(transactionAmount) as amount"))
                                          ->where('deliveryOrderID', $deliveryOrderID)
                                          ->groupBy('deliveryOrderID')
                                          ->first();

        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $deliveryOrderID)
                                ->where('documentSystemID', 71)
                                ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->transactionCurrencyID, $master->transactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'DEO';
        $_post['documentSystemID'] = $master->documentSystemID;
        $_post['documentSystemCode'] = $deliveryOrderID;
        $_post['documentCode'] = $master->deliveryOrderCode;
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


        DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->update($vatAmount);

        return ['status' => true];
    }

    private function updateSalesQuotationDeliveryStatus($quotationMasterID){

        $status = 0;
        $isInDO = 0;
        $invQty = DeliveryOrderDetail::where('quotationMasterID',$quotationMasterID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $quotationQty = QuotationDetails::where('quotationMasterID',$quotationMasterID)->sum('requestedQty');
            if($invQty == $quotationQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
            $isInDO = 1;
        }
        return QuotationMaster::where('quotationMasterID',$quotationMasterID)->update(['deliveryStatus'=>$status,'isInDOorCI'=>$isInDO]);

    }


    public function saveDeliveryOrderTaxDetail(Request $request)
    {
        $input = $request->all();
        $deliveryOrderID = isset($input['deliveryOrderID'])?$input['deliveryOrderID']:0;
        $percentage = isset($input['percentage'])?$input['percentage']:0;

        if (empty($input['taxMasterAutoID'])) {
            $input['taxMasterAutoID'] = 0;
        }

        $taxMasterAutoID = $input['taxMasterAutoID'];

        $master = DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->first();

        if (empty($master)) {
            return $this->sendResponse('e', trans('custom.delivery_order_not_found_3'));
        }

        $invoiceDetail = DeliveryOrderDetail::where('deliveryOrderID', $deliveryOrderID)->first();
        if (empty($invoiceDetail)) {
            return $this->sendResponse('e', trans('custom.delivery_details_not_found'));
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->transactionCurrencyID);

        $totalDetail = DeliveryOrderDetail::select(DB::raw("SUM(transactionAmount) as amount"))
                                          ->where('deliveryOrderID', $deliveryOrderID)
                                          ->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }
        $totalAmount = ($percentage / 100) * $totalAmount;
      

        $Taxdetail = Taxdetail::where('documentSystemCode', $deliveryOrderID)
                                ->where('documentSystemID', 71)
                                ->first();

        if (!empty($Taxdetail)) {
            return $this->sendResponse('e', trans('custom.vat_detail_already_exist_1'));
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->transactionCurrencyID, $master->transactionCurrencyID, $totalAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'DEO';
        $_post['documentSystemID'] = $master->documentSystemID;
        $_post['documentSystemCode'] = $deliveryOrderID;
        $_post['documentCode'] = $master->deliveryOrderCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->transactionCurrencyID;
        $_post['currencyER'] = $master->transactionCurrencyER;
        $_post['amount'] = round($totalAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->transactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->transactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalAmount, $decimal);
        $_post['localCurrencyID'] = $master->companyLocalCurrencyID;
        $_post['localCurrencyER'] = $master->companyLocalCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingCurrencyER;

        if ($_post['currency'] == $_post['rptCurrencyID']) {
            $MyRptAmount = $totalAmount;
        } else {
            if ($_post['rptCurrencyER'] > $_post['currencyER']) {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalAmount / $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalAmount * $_post['rptCurrencyER']);
                }
            } else {
                if ($_post['rptCurrencyER'] > 1) {
                    $MyRptAmount = ($totalAmount * $_post['rptCurrencyER']);
                } else {
                    $MyRptAmount = ($totalAmount / $_post['rptCurrencyER']);
                }
            }
        }
        $_post["rptAmount"] = \Helper::roundValue($MyRptAmount);
        if ($_post['currency'] == $_post['localCurrencyID']) {
            $MyLocalAmount = $totalAmount;
        } else {
            if ($_post['localCurrencyER'] > $_post['currencyER']) {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalAmount / $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalAmount * $_post['localCurrencyER']);
                }
            } else {
                if ($_post['localCurrencyER'] > 1) {
                    $MyLocalAmount = ($totalAmount * $_post['localCurrencyER']);
                } else {
                    $MyLocalAmount = ($totalAmount / $_post['localCurrencyER']);
                }
            }
        }
        $_post["localAmount"] = \Helper::roundValue($MyLocalAmount);


        DB::beginTransaction();
        try {
            Taxdetail::create($_post);
            $company = Company::select('vatOutputGLCode', 'vatOutputGLCodeSystemID')->where('companySystemID', $master->companySystemID)->first();

            $vatAmount['vatOutputGLCodeSystemID'] = $company->vatOutputGLCodeSystemID;
            $vatAmount['vatOutputGLCode'] = $company->vatOutputGLCode;
            $vatAmount['VATPercentage'] = $percentage;
            $vatAmount['VATAmount'] = $_post['amount'];
            $vatAmount['VATAmountLocal'] = $_post["localAmount"];
            $vatAmount['VATAmountRpt'] = $_post["rptAmount"];


            DeliveryOrder::where('deliveryOrderID', $deliveryOrderID)->update($vatAmount);

            DB::commit();
            return $this->sendResponse('s', trans('custom.successfully_added'));
        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError($exception->getMessage(),500);
        }
    }

    
    public function uploadItemsDeliveryOrder(Request $request) {
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
            $id = $input['requestID'];
            $companySystemID = $input['companySystemID'];
            $masterData = DeliveryOrder::find($input['requestID']);


            if (empty($masterData)) {
                return $this->sendError(trans('custom.delivery_order_not_found'), 500);
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
            $validateVat = false;
            $totalItemCount = 0;

            $allowItemToTypePolicy = false;
            $itemNotound = false;

            foreach ($uniqueData as $key => $value) {

                if(!array_key_exists('vat',$value) || !array_key_exists('item_code',$value) || !array_key_exists('qty',$value)) {
                     return $this->sendError(trans('custom.items_cannot_be_uploaded_as_there_are_null_values_'), 500);
                }


                if (isset($value['item_code'])) {
                    $validateHeaderCode = true;
                }

                if (isset($value['qty']) && is_numeric($value['qty'])) {
                    $validateHeaderQty = true;
                }


                if($masterData->isVatEligible) {
                   if (isset($value['vat']) && is_numeric($value['vat'])) {
                        $validateVat = true;
                   }
                }else {
                    $validateVat = true;
                }

                if ($masterData->isVatEligible && (isset($value['vat']) && !is_null($value['vat'])) || (isset($value['item_code']) && !is_null($value['item_code'])) || isset($value['qty']) && !is_null($value['qty'])) {
                    $totalItemCount = $totalItemCount + 1;
                }
            }

            if (!$validateHeaderCode || !$validateHeaderCode || !$validateVat) {
                return $this->sendError(trans('custom.items_cannot_be_uploaded_as_there_are_null_values_'), 500);
            }


            $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk($disk)->url('app/' . $originalFileName), function ($reader) {
            })->select(array('item_code', 'qty','vat','discount'))->get()->toArray();
            $uploadSerialNumber = array_filter(collect($record)->toArray());

            if ($masterData->cancelledYN == -1) {
                return $this->sendError(trans('custom.this_quotation_already_closed_you_can_not_add'), 500);
            }

            if ($masterData->approvedYN == 1) {
                return $this->sendError('This Quotation fully approved. You can not add.', 500);
            }

            $finalItems = [];
            $count = 0;

            $totalVATAmount = 0;
            $totalAmount = 0;
            $decimal = \Helper::getCurrencyDecimalPlace($masterData->transactionCurrencyID);
            foreach($record as $item) {
                if(is_numeric($item['qty'])  && ($masterData->isVatEligible && isset($item['vat']))  && is_numeric($item['discount'])) { 
                    $itemDetails  = ItemMaster::where('primaryCode',$item['item_code'])->first();
                    if(isset($itemDetails->itemCodeSystem)) {
                        $data = [
                            'deliveryOrderID' => $input['requestID'],
                            'itemCodeSystem' => $itemDetails->itemCodeSystem
                        ];

                        $validateItem =  $this->validateItemBeforeUpload($data,$itemDetails,$input['companySystemID']);

                        $itemArray = [];
                        $itemArray['deliveryOrderID'] =  $input['requestID'];
                        $itemArray['itemCodeSystem'] = $itemDetails->itemCodeSystem;
                        $itemArray['itemPrimaryCode'] = $itemDetails->primaryCode;
                        $itemArray['itemDescription'] = $itemDetails->itemDescription;
                        $itemArray['itemUnitOfMeasure'] = $itemDetails->unit;
                        $itemArray['unitOfMeasureIssued'] = $itemDetails->unit;
                        $itemArray['itemFinanceCategoryID'] = $itemDetails->financeCategoryMaster;
                        $itemArray['itemFinanceCategorySubID'] = $itemDetails->financeCategorySub;
                        $itemArray['trackingType'] = $itemDetails->trackingType;
                        $itemArray['qtyIssued'] = $item['qty'];

                        $itemAssigned = ItemAssigned::where('itemCodeSystem',$itemDetails->itemCodeSystem)->where('companySystemID',$companySystemID)->first();

                        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                            ->where('mainItemCategoryID', $itemAssigned['financeCategoryMaster'])
                            ->where('itemCategorySubID', $itemAssigned['financeCategorySub'])
                            ->first();

                        if (!empty($financeItemCategorySubAssigned)) {
                            $itemArray['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                            $itemArray['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                            $itemArray['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                            $itemArray['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                            $itemArray['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                            $itemArray['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                            $itemArray['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                            $itemArray['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;

                            $data = array(
                            'companySystemID' => $companySystemID,
                            'itemCodeSystem' => $itemDetails['itemCodeSystem'],
                            'wareHouseId' => $masterData->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
                            

                            $itemArray['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $itemArray['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $itemArray['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];
                            $itemArray['wacValueLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                            $itemArray['wacValueReporting'] = $itemCurrentCostAndQty['wacValueReporting'];

                            if($masterData->transactionCurrencyID == $masterData->companyLocalCurrencyID){

                                $itemArray['unitTransactionAmount'] = $itemCurrentCostAndQty['wacValueLocal'];
                                $itemArray['companyLocalAmount'] = $itemCurrentCostAndQty['wacValueLocal'];

                            }elseif ($masterData->transactionCurrencyID == $masterData->companyReportingCurrencyID){

                                $itemArray['unitTransactionAmount'] = $itemCurrentCostAndQty['wacValueReporting'];
                                $itemArray['companyReportingAmount'] = $itemCurrentCostAndQty['wacValueReporting'];

                            }else{

                                $currencyConversion = Helper::currencyConversion($masterData->companySystemID,$masterData->companyLocalCurrencyID,$masterData->transactionCurrencyID,$itemArray['wacValueLocal']);
                                if(!empty($currencyConversion)){
                                    $itemArray['unitTransactionAmount'] = $currencyConversion['documentAmount'];
                                }
                            }

                            $amounts = $this->updateAmountsByTransactionAmount($itemArray,$masterData);
                            $itemArray['companyLocalAmount'] = $amounts['companyLocalAmount'];
                            $itemArray['companyReportingAmount'] = $amounts['companyReportingAmount'];

                            $itemArray['transactionCurrencyID'] = $masterData->transactionCurrencyID;
                            $itemArray['transactionCurrencyER'] = $masterData->transactionCurrencyER;
                            $itemArray['companyLocalCurrencyID'] = $masterData->companyLocalCurrencyID;
                            $itemArray['companyLocalCurrencyER'] = $masterData->companyLocalCurrencyER;
                            $itemArray['companyReportingCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $itemArray['companyReportingCurrencyER'] = $masterData->companyReportingCurrencyER;
                            $itemArray['qtyIssuedDefaultMeasure'] = $item['qty'];
                            $itemArray['transactionAmount'] = 0;
                            $itemArray['discountAmount'] = $item['discount'];

                            $itemArray['discountPercentage'] =  ($itemArray['unitTransactionAmount'] != 0 && $itemArray['discountAmount'] != 0) ?  number_format((($itemArray['discountAmount']  * 100) / ($itemArray['unitTransactionAmount'])),$decimal): 0;
                            $itemArray['transactionAmount'] =  ($itemArray['unitTransactionAmount'] != 0) ? $item['qty'] * ($itemArray['unitTransactionAmount'] - $itemArray['discountAmount']) : 0 ;
                            
                            $totalAmount +=  $itemArray['transactionAmount'];
                             $itemArray['VATAmount'] = 0;
                            if ($masterData->customerVATEligible) {
                                $vatDetails = TaxService::getVATDetailsByItem($masterData->companySystemID, $itemArray['itemCodeSystem'], $masterData->customerID,0);
                                $itemArray['VATPercentage'] = $item['vat'];
                                $itemArray['VATApplicableOn'] = $vatDetails['applicableOn'];
                                $itemArray['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $itemArray['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                               
                                if (isset($item['vat'])) {
                                    $itemArray['VATAmount'] = round(($itemArray['unitTransactionAmount'] -  $itemArray['discountAmount']) * ($item['vat'] / 100),3);
                                }
                                $currencyConversionVAT = \Helper::currencyConversion($masterData->companySystemID, $masterData->transactionCurrencyID, $masterData->transactionCurrencyID, $itemArray['VATAmount']);

                                $itemArray['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                $itemArray['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);

                            }
                            
                            $currentStockQty = ErpItemLedger::where('itemSystemCode', $itemDetails['itemCodeSystem'])
                            ->where('companySystemID', $masterData->companySystemID)
                            ->groupBy('itemSystemCode')
                            ->sum('inOutQty');


                            if($validateItem) {
                                if($currentStockQty > 0 && ($item['qty'] <= $currentStockQty && $item['qty'] <= $itemArray['currentWareHouseStockQty']) && $item['vat'] <= 100) {
                                    $exists_item = DeliveryOrderDetail::where('deliveryOrderID',$masterData->deliveryOrderID)->where('itemCodeSystem',$item['item_code'])->first();

                                    $exists_already_in_delivery_order = DeliveryOrder::where('companySystemID',$companySystemID)->whereHas('detail', function ($query) use ($item) {
                                            $query->where('itemPrimaryCode', $item['item_code'])
                                               ->where('approvedYN', 0);
                                    })->get();
                                    if(!$exists_item && count($exists_already_in_delivery_order) == 0) {
                                        $totalVATAmount += ($itemArray['VATAmount'] * $item['qty']) ;

                                        array_push($finalItems,$itemArray);
                                    }
                                }
                                
                            }

                        }
                    }
                }
                
            }

        
        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;

        $_post['taxMasterAutoID'] = 0;
        $_post['companyID'] = $masterData->companyID;
        $_post['companySystemID'] = $masterData->companySystemID;
        $_post['documentID'] = 'DEO';
        $_post['documentSystemID'] = $masterData->documentSystemID;
        $_post['documentSystemCode'] = $masterData->deliveryOrderID;
        $_post['documentCode'] = $masterData->deliveryOrderCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = round($percentage,3); //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $masterData->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $masterData->transactionCurrencyID;
        $_post['currencyER'] = $masterData->transactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $masterData->transactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $masterData->transactionCurrencyER;
        $_post['payeeDefaultAmount'] = $totalVATAmount;
        $_post['localCurrencyID'] = $masterData->companyLocalCurrencyID;
        $_post['localCurrencyER'] = $masterData->companyLocalCurrencyER;

        $_post['rptCurrencyID'] = $masterData->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $masterData->companyReportingCurrencyER;

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
        $finalItems =  collect($finalItems)->unique('itemPrimaryCode')->toArray();

        if(count($finalItems) == 0) {
             return $this->sendError('No Records to upload!', 500);
        }

        $count = count($finalItems);
        Taxdetail::create($_post);
            
            if (count($record) > 0) {
                $db = isset($input['db']) ? $input['db'] : ""; 
                AddMultipleItemsToDeliveryOrder::dispatch(array_filter($finalItems),($masterData->toArray()),$db,Auth::id());
            } else {
                return $this->sendError('No Records found!', 500);
            }

            DB::commit();
            return $this->sendResponse([], 'Out of '.$totalRecords.' , '.$count.' Items uploaded Successfully!!');
        }else {
            return $this->sendError("Unit Transcation amount is zero",500);
        }
        
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function validateDeliveryOrderItem(Request $request) {
        $input = $request->all();

        $input['deliveryOrderID'] = $input['deliveryOrderId'];
        $input['itemCodeSystem'] = $input['itemCodeSystem'];
        $item = ItemMaster::find($input['itemCodeSystem']);
        $companySystemId = $input['companySystemID'];

       return $this->validateItemBeforeUpload($input, $item, $companySystemId);
    }

    private function validateItemBeforeUpload($input,$item,$companySystemID) {

        $deliveryOrderMaster = DeliveryOrder::find($input['deliveryOrderID']);
        if(empty($deliveryOrderMaster)){
            return $this->sendError(trans('custom.delivery_order_not_found_1'),500);
        }

        $alreadyAdded = DeliveryOrder::where('deliveryOrderID', $input['deliveryOrderID'])
            ->whereHas('detail', function ($query) use ($input) {
                $query->where('itemCodeSystem', $input['itemCodeSystem']);
            })
            ->exists();

        if ($alreadyAdded) {
            return $this->sendError("Selected item is already added. Please check again", 500);
        }

        $data = array(
            'companySystemID' => $companySystemID,
            'itemCodeSystem' => $item['itemCodeSystem'],
            'wareHouseId' => $deliveryOrderMaster->wareHouseSystemCode
        );

        $itemCurrentCostAndQty  = inventory::itemCurrentCostAndQty($data);

        if ($item->financeCategoryMaster == 1) {
            if (isset($itemCurrentCostAndQty['currentWareHouseStockQty']) && ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0)) {
                return $this->sendError(trans('custom.stock_qty_is_0_you_cannot_issue'), 500);
            }

            if ((float)$itemCurrentCostAndQty['wacValueLocal'] == 0 || (float)$itemCurrentCostAndQty['wacValueReporting'] == 0) {
                return $this->sendError(trans('custom.cost_is_0_you_cannot_issue_1'), 500);
            }
        }


        if(DeliveryOrderDetail::where('deliveryOrderID',$input['deliveryOrderID'])->where('itemFinanceCategoryID','!=',$item->financeCategoryMaster)->exists()){
            return $this->sendError('Different finance category found. You can not add different finance category items for same order',500);
        }

        if($item->financeCategoryMaster==1){
            // check the item pending pending for approval in other delivery orders

            $checkWhether = DeliveryOrder::where('deliveryOrderID', '!=', $deliveryOrderMaster->deliveryOrderID)
                ->where('companySystemID', $companySystemID)
                ->select([
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.deliveryOrderCode'
                ])
                ->groupBy(
                    'erp_delivery_order.deliveryOrderID',
                    'erp_delivery_order.companySystemID'
                )
                ->whereHas('detail', function ($query) use ($companySystemID, $input) {
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approvedYN', 0)
                ->first();
            if (!empty($checkWhether)) {
                return $this->sendError("There is a Delivery Order (" . $checkWhether->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
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
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
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
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
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
                    $query->where('itemCodeSystem', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->where('canceledYN', 0)
                ->first();
            /* approved=0*/

            if (!empty($checkWhetherInvoice)) {
                return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }

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
                    $query->where('itemCode', $input['itemCodeSystem']);
                })
                ->where('approved', 0)
                ->first();

            if (!empty($checkWhetherPR)) {
                return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }
        }

        return response()->json(['status' => true, 'message' => ''], 200);
    }

    public function deleteAllItemsFromDeliveryOrder(Request $request)
    {
        if(!isset($request->deliveryOrderID))
            return $this->sendError("Delivery Order Not Found!");

        $deliveryOrder = DeliveryOrder::where('deliveryOrderID',$request->deliveryOrderID)->first();

        if(!$deliveryOrder)
            return $this->sendError("Delivery Order Not Found!");


        DeliveryOrderDetail::where('deliveryOrderID',$request->deliveryOrderID)->delete();

        return $this->sendResponse([],"Item deleted Successfully");

    }
}

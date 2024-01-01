<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\TaxService;
use App\helper\inventory;
use App\helper\ItemTracking;
use App\Http\Requests\API\CreateCustomerInvoiceItemDetailsAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceItemDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemClientReferenceNumberMaster;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\PurchaseReturn;
use App\Models\DocumentSubProduct;
use App\Models\ItemSerial;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\Company;
use App\Models\StockTransfer;
use App\Models\Taxdetail;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Repositories\CustomerInvoiceItemDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerInvoiceLogistic;
use App\Models\DeliveryTermsMaster;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceItemDetailsController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceItemDetailsAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceItemDetailsRepository */
    private $customerInvoiceItemDetailsRepository;

    public function __construct(CustomerInvoiceItemDetailsRepository $customerInvoiceItemDetailsRepo)
    {
        $this->customerInvoiceItemDetailsRepository = $customerInvoiceItemDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceItemDetails",
     *      summary="Get a listing of the CustomerInvoiceItemDetails.",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Get all CustomerInvoiceItemDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceItemDetails")
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
        $this->customerInvoiceItemDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceItemDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->all();

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), 'Customer Invoice Item Details retrieved successfully');
    }

    /**
     * @param CreateCustomerInvoiceItemDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceItemDetails",
     *      summary="Store a newly created CustomerInvoiceItemDetails in storage",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Store CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceItemDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceItemDetails")
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
     *                  ref="#/definitions/CustomerInvoiceItemDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceItemDetailsAPIRequest $request)
    {
        $input = $request->all();
        $companySystemID = $input['companySystemID'];

       
        if(isset($input['isInDOorCI'])) {
            unset($input['timesReferred']);
        $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        }else {
        $item = ItemAssigned::with(['item_master'])
            ->where('idItemAssigned', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        }
        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $customerInvoiceDirect = CustomerInvoiceDirect::find($input['custInvoiceDirectAutoID']);

       

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct Not Found');
        }

        $is_pref = $customerInvoiceDirect->isPerforma;

        if(CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->where('itemFinanceCategoryID','!=',$item->financeCategoryMaster)->exists()){
            return $this->sendError('Different finance category found. You can not add different finance category items for same invoice',500);
        }

        /* TODO confirm approve check here*/

        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;

        $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
        $input['trackingType'] = isset($item->item_master->trackingType) ? $item->item_master->trackingType : null;
        $input['convertionMeasureVal'] = 1;

        if(!isset($input['qtyIssued'])) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
        }
        
        $input['comments'] = '';
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['localCurrencyID'] = $customerInvoiceDirect->localCurrencyID;
        $input['localCurrencyER'] = $customerInvoiceDirect->localCurrencyER;


        $data = array('companySystemID' => $companySystemID,
            'itemCodeSystem' => $input['itemCodeSystem'],
            'wareHouseId' => $customerInvoiceDirect->wareHouseSystemCode);

        $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
        $input['currentStockQtyInDamageReturn'] = $itemCurrentCostAndQty['currentStockQtyInDamageReturn'];


        $input['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
        $input['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];

        if ($item->financeCategoryMaster == 1){
            if ($input['currentStockQty'] <= 0) {
                return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['currentWareHouseStockQty'] <= 0) {
                return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['issueCostLocal'] == 0 || $input['issueCostRpt'] == 0) {
                return $this->sendError("Cost is 0. You cannot issue.", 500);
            }

            if ($input['issueCostLocal'] < 0 || $input['issueCostRpt'] < 0) {
                return $this->sendError("Cost is negative. You cannot issue.", 500);
            }
        }



        $input['issueCostLocalTotal'] =  $input['issueCostLocal'] * $input['qtyIssuedDefaultMeasure'];

        $input['reportingCurrencyID'] = $customerInvoiceDirect->companyReportingCurrencyID;
        $input['reportingCurrencyER'] = $customerInvoiceDirect->companyReportingER;

        $input['issueCostRptTotal'] = $input['issueCostRpt'] * $input['qtyIssuedDefaultMeasure'];
        $input['marginPercentage'] = 0;

        $companyCurrencyConversion = Helper::currencyConversion($companySystemID,$customerInvoiceDirect->companyReportingCurrencyID,$customerInvoiceDirect->custTransactionCurrencyID,$input['issueCostRpt']);
        $input['sellingCurrencyID'] = $customerInvoiceDirect->custTransactionCurrencyID;
        $input['sellingCurrencyER'] = $customerInvoiceDirect->custTransactionCurrencyER;
        $input['sellingCost'] = ($companyCurrencyConversion['documentAmount'] != 0) ? $companyCurrencyConversion['documentAmount'] : 1.0;
        if((isset($input['customerCatalogDetailID']) && $input['customerCatalogDetailID']>0)){
            $catalogDetail = CustomerCatalogDetail::find($input['customerCatalogDetailID']);

            if(empty($catalogDetail)){
                return $this->sendError('Customer catalog Not Found');
            }

            if($customerInvoiceDirect->custTransactionCurrencyID != $catalogDetail->localCurrencyID){
                $currencyConversion = Helper::currencyConversion($customerInvoiceDirect->companySystemID,$catalogDetail->localCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID,$catalogDetail->localPrice);
                if(!empty($currencyConversion)){
                    $catalogDetail->localPrice = $currencyConversion['documentAmount'];
                }
            }

            $input['sellingCostAfterMargin'] = $catalogDetail->localPrice;
            $input['marginPercentage'] = ($input['sellingCostAfterMargin'] - $input['sellingCost'])/$input['sellingCost']*100;
            $input['part_no'] = $catalogDetail->partNo;
        }else{
            $input['sellingCostAfterMargin'] = $input['sellingCost'];
            $input['part_no'] = $item->secondaryItemCode;
        }

        if(isset($input['marginPercentage']) && $input['marginPercentage'] != 0){
//            $input['sellingCostAfterMarginLocal'] = ($input['issueCostLocal']) + ($input['issueCostLocal']*$input['marginPercentage']/100);
//            $input['sellingCostAfterMarginRpt'] = ($input['issueCostRpt']) + ($input['issueCostRpt']*$input['marginPercentage']/100);
        }else{
            $input['sellingCostAfterMargin'] = $input['sellingCost'];
//            $input['sellingCostAfterMarginLocal'] = $input['issueCostLocal'];
//            $input['sellingCostAfterMarginRpt'] = $input['issueCostRpt'];
        }

        $costs = $this->updateCostBySellingCost($input,$customerInvoiceDirect);
        $input['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
        $input['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

        $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'];

        /*round to 7 decimals*/
        $input['issueCostLocal'] = Helper::roundValue($input['issueCostLocal']);
        $input['issueCostLocalTotal'] = Helper::roundValue($input['issueCostLocalTotal']);
        $input['issueCostRpt'] = Helper::roundValue($input['issueCostRpt']);
        $input['issueCostRptTotal'] = Helper::roundValue($input['issueCostRptTotal']);
        $input['sellingCost'] = Helper::roundValue($input['sellingCost']);
        $input['sellingCostAfterMargin'] = Helper::roundValue($input['sellingCostAfterMargin']);
        $input['salesPrice'] = Helper::roundValue($input['sellingCostAfterMargin']);
        $input['sellingTotal'] = Helper::roundValue($input['sellingTotal']);
        $input['sellingCostAfterMarginLocal'] = Helper::roundValue($input['sellingCostAfterMarginLocal']);
        $input['sellingCostAfterMarginRpt'] = Helper::roundValue($input['sellingCostAfterMarginRpt']);

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

        if((!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']) && $input['itemFinanceCategoryID'] != 2){
            return $this->sendError('BS account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }elseif (!$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']){
            return $this->sendError('Cost account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }elseif (!$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']){
            return $this->sendError('Revenue account cannot be null for ' . $item->itemPrimaryCode . '-' . $item->itemDescription, 500);
        }

        /*if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']
            || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']
            || !$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']) {
            return $this->sendError("Account code not updated.", 500);
        }*/
        

        if ($input['itemFinanceCategoryID'] == 1 || $input['itemFinanceCategoryID'] == 2 || $input['itemFinanceCategoryID'] == 4) {
            $alreadyAdded = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->where('itemCodeSystem',$item->itemCodeSystem)->first();
         
            if ($alreadyAdded) {
                if(($input['itemFinanceCategoryID'] != 2 )&& ($input['itemFinanceCategoryID'] != 4 ))
                {
                    return $this->sendError("Selected item is already added. Please check again", 500);
                }
                
            }

        }
    
        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if($item->financeCategoryMaster == 1){
            $checkWhether = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $customerInvoiceDirect->custInvoiceDirectAutoID)
                ->where('companySystemID', $companySystemID)
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

            if (!empty($checkWhether)) {
                return $this->sendError("There is a Customer Invoice (" . $checkWhether->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }


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

            // check in delivery order
            $checkWhetherDeliveryOrder = DeliveryOrder::where('companySystemID', $companySystemID)
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

            if (!empty($checkWhetherDeliveryOrder)) {
                return $this->sendError("There is a Delivery Order (" . $checkWhetherDeliveryOrder->deliveryOrderCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
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
            /* approved=0*/

            if (!empty($checkWhetherPR)) {
                return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
            }
        }

        if ($customerInvoiceDirect->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($customerInvoiceDirect->companySystemID, $input['itemCodeSystem'], $customerInvoiceDirect->customerID,0);
            $input['VATPercentage'] = $vatDetails['percentage'];
            $input['VATApplicableOn'] = $vatDetails['applicableOn'];
            $input['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $input['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $input['VATAmount'] = 0;
            if (isset($input['sellingCostAfterMargin']) && $input['sellingCostAfterMargin'] > 0) {
                $input['VATAmount'] = (($input['sellingCostAfterMargin'] / 100) * $vatDetails['percentage']);
            }
            $currencyConversionVAT = \Helper::currencyConversion($customerInvoiceDirect->companySystemID, $customerInvoiceDirect->custTransactionCurrencyID, $customerInvoiceDirect->custTransactionCurrencyID, $input['VATAmount']);

            $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }

        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->create($input);

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), 'Customer Invoice Item Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceItemDetails/{id}",
     *      summary="Display the specified CustomerInvoiceItemDetails",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Get CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetails",
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
     *                  ref="#/definitions/CustomerInvoiceItemDetails"
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
        /** @var CustomerInvoiceItemDetails $customerInvoiceItemDetails */
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetails)) {
            return $this->sendError('Customer Invoice Item Details not found');
        }

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), 'Customer Invoice Item Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceItemDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceItemDetails/{id}",
     *      summary="Update the specified CustomerInvoiceItemDetails in storage",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Update CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceItemDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceItemDetails")
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
     *                  ref="#/definitions/CustomerInvoiceItemDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceItemDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($request->all(), ['uom_default', 'uom_issuing','item_by','issueUnits','delivery_order','sales_quotation', 'issueCostTransTotal', 'issueCostTrans']);
        $input = $this->convertArrayToValue($input);
        $qtyError = array('type' => 'qty');
        $message = "Item updated successfully";
        /** @var CustomerInvoiceItemDetails $customerInvoiceItemDetails */
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->findWithoutFail($id);



        if (empty($customerInvoiceItemDetails)) {
            return $this->sendError('Customer Invoice Item Details not found');
        }

        $customerDirectInvoice = CustomerInvoiceDirect::find($customerInvoiceItemDetails->custInvoiceDirectAutoID);

        if (empty($customerDirectInvoice)) {
            return $this->sendError('Customer Invoice Details not found');
        }

        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($customerDirectInvoice->documentSystemiD, $customerDirectInvoice->companySystemID, $id, $input, $customerDirectInvoice->customerID, $customerDirectInvoice->isPerforma);

        if (!$validateVATCategories['status']) {
            return $this->sendError($validateVATCategories['message'], 500, array('type' => 'vat'));
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
        }

        if (isset($input["discountPercentage"]) && $input["discountPercentage"] > 100) {
            return $this->sendError('Discount Percentage cannot be greater than 100 percentage');
        }

        if (isset($input["discountAmount"]) && isset($input['salesPrice']) && $input['discountAmount'] > $input['salesPrice']) {
            return $this->sendError('Discount amount cannot be greater than sales price');
        }

        if ($input['itemUnitOfMeasure'] != $input['unitOfMeasureIssued']) {
            $unitConvention = UnitConversion::where('masterUnitID', $input['itemUnitOfMeasure'])
                ->where('subUnitID', $input['unitOfMeasureIssued'])
                ->first();
            if (empty($unitConvention)) {
                return $this->sendError("Unit conversion isn't valid or configured", 500);
            }

            if ($unitConvention) {
                $convention = $unitConvention->conversion;
                $input['convertionMeasureVal'] = $convention;
                if ($convention > 0) {
                    $input['qtyIssuedDefaultMeasure'] = round(($input['qtyIssued'] / $convention), 2);
                } else {
                    $input['qtyIssuedDefaultMeasure'] = round(($input['qtyIssued'] * $convention), 2);
                }
            }
        } else {
            $input['qtyIssuedDefaultMeasure'] = $input['qtyIssued'];
        }

        /*margin calculation*/
        if(isset($input['by']) && $input['by']== 'salesPrice' ){
            if($input['sellingCost'] > 0 && $input['issueCostRpt'] > 0){
                $input['marginPercentage'] = ($input['salesPrice'] - $input['sellingCost'])/$input['sellingCost']*100;
            }else{
                $input['marginPercentage']=0;
                if($customerInvoiceItemDetails->itemFinanceCategoryID != 1){
                    $input['sellingCost'] = $input['salesPrice'];
                }
            }
        }elseif (isset($input['by']) && $input['by']== 'margin'){
            $input['salesPrice'] = ($input['sellingCost']) + ($input['sellingCost']*$input['marginPercentage']/100);
        }else{
            if (isset($input['marginPercentage']) && $input['marginPercentage'] != 0){
                $input['salesPrice'] = ($input['sellingCost']) + ($input['sellingCost']*$input['marginPercentage']/100);
            }else{
                if($customerInvoiceItemDetails->itemFinanceCategoryID == 1){
                    $input['salesPrice'] = $input['sellingCost'];
                }else{
                    $input['sellingCost'] = $input['salesPrice'];
                }
            }
        }

        $input['sellingCostAfterMargin'] = $input['salesPrice'];

        if(isset($input['by']) && ($input['by'] == 'discountPercentage' || $input['by'] == 'discountAmount')){
            if ($input['by'] === 'discountPercentage') {
              $input["discountAmount"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            } else if ($input['by'] === 'discountAmount') {
                if($input['salesPrice'] > 0){
                    $input["discountPercentage"] = ($input["discountAmount"] / $input['salesPrice']) * 100;
                } else {
                    $input["discountPercentage"] = 0;
                }
            }
        } else {
            if ($input['discountPercentage'] != 0) {
              $input["discountAmount"] = $input['salesPrice'] * $input["discountPercentage"] / 100;
            } else {
                if($input['salesPrice'] > 0){
                    $input["discountPercentage"] = ($input["discountAmount"] / $input['salesPrice']) * 100;
                } else {
                    $input["discountPercentage"] = 0;
                }
            }
        }

        $input['sellingCostAfterMargin'] = $input['sellingCostAfterMargin'] - $input["discountAmount"];


        $costs = $this->updateCostBySellingCost($input,$customerDirectInvoice);
        $input['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
        $input['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];


        if(isset($input['by']) && ($input['by'] == 'VATPercentage' || $input['by'] == 'VATAmount')){
            if ($input['by'] === 'VATPercentage') {
              $input["VATAmount"] = $input['sellingCostAfterMargin'] * $input["VATPercentage"] / 100;
            } else if ($input['by'] === 'VATAmount') {
                if($input['sellingCostAfterMargin'] > 0){
                    $input["VATPercentage"] = ($input["VATAmount"] / $input['sellingCostAfterMargin']) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        } else {
            if ($input['VATPercentage'] != 0) {
              $input["VATAmount"] = $input['sellingCostAfterMargin'] * $input["VATPercentage"] / 100;
            } else {
                if($input['sellingCostAfterMargin'] > 0){
                    $input["VATPercentage"] = ($input["VATAmount"] / $input['sellingCostAfterMargin']) * 100;
                } else {
                    $input["VATPercentage"] = 0;
                }
            }
        }

        $currencyConversionVAT = \Helper::currencyConversion($customerDirectInvoice->companySystemID, $customerDirectInvoice->custTransactionCurrencyID, $customerDirectInvoice->custTransactionCurrencyID, $input['VATAmount']);

        $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
        $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);

        if($customerInvoiceItemDetails->itemFinanceCategoryID == 1){
            if ($customerInvoiceItemDetails->issueCostLocal == 0) {
                $this->customerInvoiceItemDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Cost is 0. You cannot issue.", 500);
            }

            if ($customerInvoiceItemDetails->issueCostLocal < 0 || $customerInvoiceItemDetails->issueCostRpt < 0) {
                $this->customerInvoiceItemDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Cost is negative. You cannot issue.", 500);
            }

            if ($customerInvoiceItemDetails->currentStockQty <= 0) {
                $this->customerInvoiceItemDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
            }

            if ($customerInvoiceItemDetails->currentWareHouseStockQty <= 0) {
                $this->customerInvoiceItemDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
            }

            if ($input['qtyIssuedDefaultMeasure'] > $customerInvoiceItemDetails->currentStockQty) {
                $this->customerInvoiceItemDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Current stock Qty is: " . $customerInvoiceItemDetails->currentStockQty . " .You cannot issue more than the current stock qty.", 500, $qtyError);
            }

            if ($input['qtyIssuedDefaultMeasure'] > $customerInvoiceItemDetails->currentWareHouseStockQty) {
                $this->customerInvoiceItemDetailsRepository->update(['issueCostRptTotal' => 0,'qtyIssuedDefaultMeasure' => 0, 'qtyIssued' => 0], $id);
                return $this->sendError("Current warehouse stock Qty is: " . $customerInvoiceItemDetails->currentWareHouseStockQty . " .You cannot issue more than the current warehouse stock qty.", 500, $qtyError);
            }
        }

        $input['issueCostLocalTotal'] = $customerInvoiceItemDetails->issueCostLocal * $input['qtyIssuedDefaultMeasure'];
        $input['issueCostRptTotal'] = $customerInvoiceItemDetails->issueCostRpt * $input['qtyIssuedDefaultMeasure'];
        $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'];


        if ($input['qtyIssued'] == '' || is_null($input['qtyIssued'])) {
            $input['qtyIssued'] = 0;
            $input['qtyIssuedDefaultMeasure'] = 0;
        }

        $input['issueCostLocal'] = Helper::roundValue($input['issueCostLocal']);
        $input['issueCostLocalTotal'] = Helper::roundValue($input['issueCostLocalTotal']);
        $input['issueCostRpt'] = Helper::roundValue($input['issueCostRpt']);
        $input['issueCostRptTotal'] = Helper::roundValue($input['issueCostRptTotal']);
        $input['sellingCost'] = Helper::roundValue($input['sellingCost']);
        $input['sellingCostAfterMargin'] = Helper::roundValue($input['sellingCostAfterMargin']);
        $input['sellingTotal'] = Helper::roundValue($input['sellingTotal']);
        $input['sellingCostAfterMarginLocal'] = Helper::roundValue($input['sellingCostAfterMarginLocal']);
        $input['sellingCostAfterMarginRpt'] = Helper::roundValue($input['sellingCostAfterMarginRpt']);

       $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->update($input, $id);

        $customerInvoiceItemDetails->warningMsg = 0;

        if($customerInvoiceItemDetails->itemFinanceCategoryID == 1){
            if (($customerInvoiceItemDetails->currentStockQty - $customerInvoiceItemDetails->qtyIssuedDefaultMeasure) < $customerInvoiceItemDetails->minQty) {
                $minQtyPolicy = CompanyPolicyMaster::where('companySystemID', $customerInvoiceItemDetails->companySystemID)
                    ->where('companyPolicyCategoryID', 6)
                    ->first();
                if (!empty($minQtyPolicy)) {
                    if ($minQtyPolicy->isYesNO == 1) {
                        $customerInvoiceItemDetails->warningMsg = 1;
                        $message = 'Quantity is falling below the minimum inventory level.';
                    }
                }
            }
        }

        $resVat = $this->updateVatFromSalesQuotation($customerDirectInvoice->custInvoiceDirectAutoID);
        if (!$resVat['status']) {
           return $this->sendError($resVat['message']); 
        } 

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), $message);
    }

    public function custItemDetailUpdate($id, UpdateCustomerInvoiceItemDetailsAPIRequest $request){
        $comments = $request->comments;

        $input = array();
        $input['comments'] = $comments;
        $message = "Item updated successfully";

        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceItemDetails->toArray(), $message);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceItemDetails/{id}",
     *      summary="Remove the specified CustomerInvoiceItemDetails from storage",
     *      tags={"CustomerInvoiceItemDetails"},
     *      description="Delete CustomerInvoiceItemDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetails",
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
        /** @var CustomerInvoiceItemDetails $customerInvoiceItemDetails */
        $customerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetails)) {
            return $this->sendError('Customer Invoice Item Details not found');
        }

        $customerInvoice = CustomerInvoiceDirect::find($customerInvoiceItemDetails->custInvoiceDirectAutoID);
        if(!empty($customerInvoice)){
            if($customerInvoice->confirmedYN == 1){
                return $this->sendError('Invoice was already confirmed. you cannot delete',500);
            }
            $taxExist = Taxdetail::where('documentSystemCode', $customerInvoice->custInvoiceDirectAutoID)
                ->where('documentSystemID', $customerInvoice->documentSystemiD)
                ->exists();
            if($taxExist && $customerInvoice->isPerforma != 4 && $customerInvoice->isPerforma != 5 && $customerInvoice->isPerforma != 3 &&  $customerInvoice->isPerforma != 2){
                return $this->sendError('VAT is added. Please delete the tax and try again.',500);
            }

        }

        if ($customerInvoiceItemDetails->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $customerInvoice->documentSystemiD)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError('You cannot delete this line item. Serial details are sold already.', 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $customerInvoice->documentSystemiD)
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
        } else if ($customerInvoiceItemDetails->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($customerInvoice->documentSystemID, $id);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }


        $customerInvoiceItemDetails->delete();

        /*for Customer Invoice type -> From Delivery Note*/

        if($customerInvoice->isPerforma == 3){

            if (!empty($customerInvoiceItemDetails->deliveryOrderDetailID) && !empty($customerInvoiceItemDetails->deliveryOrderID)) {
                DeliveryOrder::find($customerInvoiceItemDetails->deliveryOrderID)
                    ->update([
                        'selectedForCustomerInvoice' => 0,
                        'closedYN' => 0
                    ]);


                //checking the fullyOrdered or partial in po
                $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalQty'))
                    ->where('deliveryOrderDetailID', $customerInvoiceItemDetails->deliveryOrderDetailID)
                    ->first();

                $updatedQuoQty = $detailSum['totalQty'];

                if ($updatedQuoQty == 0) {
                    $fullyReceived = 0;
                } else {
                    $fullyReceived = 1;
                }

                $updateDetail = DeliveryOrderDetail::where('deliveryOrderDetailID', $customerInvoiceItemDetails->deliveryOrderDetailID)
                    ->update([ 'fullyReceived' => $fullyReceived, 'invQty' => $updatedQuoQty]);

                $taxDelete = Taxdetail::where('documentSystemCode', $customerInvoiceItemDetails->custInvoiceDirectAutoID)
                                  ->where('documentSystemID', 20)
                                  ->delete();

                $resVat = $this->updateVatFromSalesDeliveryOrder($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 

                $resVat = $this->updateVatEligibilityOfCustomerInvoiceFromDO($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 
            }
            $this->updateDOInvoicedStatus($customerInvoiceItemDetails->deliveryOrderID);

        }elseif ($customerInvoice->isPerforma == 4 || $customerInvoice->isPerforma == 5){    /*for Customer Invoice type -> From Sales Order, Quotation*/
            if (!empty($customerInvoiceItemDetails->quotationMasterID) && !empty($customerInvoiceItemDetails->quotationDetailsID)) {
                QuotationMaster::find($customerInvoiceItemDetails->quotationMasterID)
                    ->update([
                        'selectedForDeliveryOrder' => 0,
                        'closedYN' => 0
                    ]);

                //checking the fullyOrdered or partial in po
                $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalQty'))
                    ->where('quotationDetailsID', $customerInvoiceItemDetails->quotationDetailsID)
                    ->first();

                $updatedQuoQty = $detailSum['totalQty'];

                if ($updatedQuoQty == 0) {
                    $fullyOrdered = 0;
                } else {
                    $fullyOrdered = 1;
                }

                QuotationDetails::where('quotationDetailsID', $customerInvoiceItemDetails->quotationDetailsID)
                    ->update([ 'fullyOrdered' => $fullyOrdered, 'doQuantity' => $updatedQuoQty]);

                $this->updateSalesQuotationInvoicedStatus($customerInvoiceItemDetails->quotationMasterID);
                $taxDelete = Taxdetail::where('documentSystemCode', $customerInvoiceItemDetails->custInvoiceDirectAutoID)
                                  ->where('documentSystemID', 20)
                                  ->delete();

                $resVat = $this->updateVatFromSalesQuotation($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 

                $resVat = $this->updateVatEligibilityOfCustomerInvoice($customerInvoiceItemDetails->custInvoiceDirectAutoID);
                if (!$resVat['status']) {
                   return $this->sendError($resVat['message']); 
                } 
            }

        } else if ($customerInvoice->isPerforma == 2) {
            $resVat = $this->updateVatFromSalesQuotation($customerInvoiceItemDetails->custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 
        }

        return $this->sendResponse($id, 'Customer Invoice Item Details deleted successfully');
    }

    public function getItemByCustomerInvoiceItemDetail(Request $request)
    {
        $input = $request->all();
        $id = $input['custInvoiceDirectAutoID'];

        $items = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
            ->with(['uom_default', 'uom_issuing','item_by','delivery_order','sales_quotation'])
            ->get();

        foreach ($items as $item) {

            $issueUnit = Unit::all();
            $issueUnits = array();

            if ($issueUnit) {
                foreach ($issueUnit as $unit){
                    $temArray = array('value' => $unit->UnitID, 'label' => $unit->UnitShortCode);
                    array_push($issueUnits,$temArray);
                }
            }
            
            $item->issueUnits = $issueUnits;
        }

        return $this->sendResponse($items->toArray(), 'Item Details retrieved successfully');
    }

    public function getDeliveryTerms(Request $request)
    {
        $items = DeliveryTermsMaster::where('is_deleted', 0)
            ->get();

        return $this->sendResponse($items->toArray(), 'Delivery Terms retrieved successfully');
    }

    public function getDeliveryTermsFormData(Request $request)
    {
        $input = $request->all();
        $id = $input[0];
        $items = CustomerInvoiceLogistic::where('custInvoiceDirectAutoID', $id)->first();

        return $this->sendResponse($items, 'Delivery Terms retrieved successfully');
    }

    private function updateCostBySellingCost($input,$customerDirectInvoice){
        $output = array();
        if($customerDirectInvoice->custTransactionCurrencyID != $customerDirectInvoice->localCurrencyID){
            $currencyConversion = Helper::currencyConversion($customerDirectInvoice->companySystemID,$customerDirectInvoice->custTransactionCurrencyID,$customerDirectInvoice->localCurrencyID,$input['sellingCostAfterMargin']);
            if(!empty($currencyConversion)){
                $output['sellingCostAfterMarginLocal'] = $currencyConversion['documentAmount'];
            }
        }else{
            $output['sellingCostAfterMarginLocal'] = $input['sellingCostAfterMargin'];
        }

        if($customerDirectInvoice->custTransactionCurrencyID != $customerDirectInvoice->companyReportingCurrencyID){
            $currencyConversion = Helper::currencyConversion($customerDirectInvoice->companySystemID,$customerDirectInvoice->custTransactionCurrencyID,$customerDirectInvoice->companyReportingCurrencyID,$input['sellingCostAfterMargin']);
            if(!empty($currencyConversion)){
                $output['sellingCostAfterMarginRpt'] = $currencyConversion['documentAmount'];
            }
        }else{
            $output['sellingCostAfterMarginRpt'] = $input['sellingCostAfterMargin'];
        }

        return $output;
    }

    public function deliveryOrderForCustomerInvoice(Request $request){
        $input = $request->all();
        $invoice = CustomerInvoiceDirect::find($input['custInvoiceDirectAutoID']);

        $master = DeliveryOrder::where('companySystemID',$input['companySystemID'])
            ->where('approvedYN', -1)
            ->where('selectedForCustomerInvoice', 0)
            ->where('closedYN',0)
            ->where('serviceLineSystemID', $invoice->serviceLineSystemID)
            ->where('wareHouseSystemCode', $invoice->wareHouseSystemCode)
            ->where('customerID', $invoice->customerID)
            ->where('transactionCurrencyID', $invoice->custTransactionCurrencyID)
            ->whereDate("postedDate", '<=', $invoice->bookingDate)
            ->orderBy('deliveryOrderID','DESC')
            ->get();

        return $this->sendResponse($master->toArray(), 'Delivery  order retrieved successfully');
    }

    public function getDeliveryOrderDetailForInvoice(Request $request){
        $input = $request->all();
        $id = $input['deliveryOrderID'];

        $detail = DB::select('SELECT
	dodetail.*,
	erp_delivery_order.serviceLineSystemID,
	"" AS isChecked,
	"" AS noQty,
	IFNULL(sum(invdetails.invTakenQty),0) as invTakenQty 
FROM
	erp_delivery_order_detail dodetail
	INNER JOIN erp_delivery_order ON dodetail.deliveryOrderID = erp_delivery_order.deliveryOrderID
	LEFT JOIN ( SELECT erp_customerinvoiceitemdetails.customerItemDetailID,deliveryOrderDetailID, SUM( qtyIssuedDefaultMeasure ) AS invTakenQty FROM erp_customerinvoiceitemdetails GROUP BY customerItemDetailID, itemCodeSystem ) AS invdetails ON dodetail.deliveryOrderDetailID = invdetails.deliveryOrderDetailID 
WHERE
	dodetail.deliveryOrderID = ' . $id . ' 
	AND fullyReceived != 2 
	GROUP BY dodetail.deliveryOrderDetailID');

        return $this->sendResponse($detail, 'Delivery order Details retrieved successfully');
    }

    public function storeInvoiceDetailFromDeliveryOrder(Request $request){
        
        $input = $request->all();
        $invDetail_arr = array();
        $validator = array();
        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];

        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        $inputDetails = $input['detailTable'];
        $inputDetails = collect($inputDetails)->where('isChecked',1)->toArray();
        $financeCategories = collect($inputDetails)->pluck('itemFinanceCategoryID')->toArray();
        if (count(array_unique($financeCategories)) > 1) {
            return $this->sendError('Multiple finance category cannot be added. Different finance category found on selected details.',500);
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => 'Invoice quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if($newValidation['noQty'] == 0){
                    return $this->sendError('Invoice Quantity should be greater than zero', 500);
                }
            }
        }
        

        $customerInvoioce = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first(); 
        $is_pref = $customerInvoioce->isPerforma;

       
        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {
            $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
            ->where('companySystemID', $itemExist['companySystemID'])
            ->first();
            
           

            $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
            ->where('companySystemID', $itemExist['companySystemID'])
            ->first();

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('itemPrimaryCode'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('itemCodeSystem', $itemExist['itemCodeSystem'])
                    ->get();


                if($item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                {
                    if (!empty($doDetailExist)) {
                        foreach ($doDetailExist as $row) {
                            $itemDrt = $row['itemPrimaryCode'] . " is already added";
                            $itemExistArray[] = [$itemDrt];
                        }
                    }
                }

          
            }
        }
        

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }
        

        foreach ($input['detailTable'] as $itemExist) {

            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {

                $deliveryOrder = DeliveryOrder::find($itemExist['deliveryOrderID']);

                if($deliveryOrder->serviceLineSystemID != $customerInvoioce->serviceLineSystemID){
//                    return $this->sendError("Segment is different from order");
                }
            }
        }

        // We are not check stock qty. bcz delivery order already made gl and item ledger entry

        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $deliveryOrder = DeliveryOrder::find($new['deliveryOrderID']);

                $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('customerItemDetailID'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                    ->first();

                if (empty($doDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in delivery order
                        $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalNoQty'))
                            ->where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                            ->first();

                        $totalAddedQty = $new['noQty'] + $detailSum['totalNoQty'];

                        if ($new['qtyIssuedDefaultMeasure'] == $totalAddedQty) {
                            $fullyReceived = 2;
                            $closedYN = -1;
                            $selectedForCustomerInvoice= -1;
                        } else {
                            $fullyReceived = 1;
                            $closedYN = 0;
                            $selectedForCustomerInvoice = 0;
                        }

                        // checking the qty request is matching with sum total
                        if ($new['qtyIssuedDefaultMeasure'] >= $new['noQty']) {

                            $invDetail_arr['custInvoiceDirectAutoID'] = $custInvoiceDirectAutoID;

                            $invDetail_arr['deliveryOrderID'] = $new['deliveryOrderID'];
                            $invDetail_arr['deliveryOrderDetailID'] = $new['deliveryOrderDetailID'];
                            $invDetail_arr['itemCodeSystem'] = $new['itemCodeSystem'];
                            $invDetail_arr['itemPrimaryCode'] = $new['itemPrimaryCode'];
                            $invDetail_arr['itemDescription'] = $new['itemDescription'];

                            $invDetail_arr['VATPercentage'] = $new['VATPercentage'];
                            $invDetail_arr['VATAmount'] = $new['VATAmount'];
                            $invDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                            $invDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                            $invDetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                            $invDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                            $invDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];

                            $item = ItemMaster::find($new['itemCodeSystem']);
                            if(empty($item)){
                                return $this->sendError('Item not found',500);
                            }

                            $data = array(
                                'companySystemID' => $deliveryOrder->companySystemID,
                                'itemCodeSystem' => $new['itemCodeSystem'],
                                'wareHouseId' => $deliveryOrder->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $invDetail_arr['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                            $invDetail_arr['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
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
                                $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError("Finance Item category sub assigned not found", 500);
//                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }

                            if((!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']) && $item->financeCategoryMaster!=2){
                                return $this->sendError('BS account cannot be null for ' . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']){
                                return $this->sendError('Cost account cannot be null for ' . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']){
                                return $this->sendError('Revenue account cannot be null for ' . $new['itemSystemCode'], 500);
                            }

                            /*if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }*/


                            $invDetail_arr['sellingCurrencyID'] = $deliveryOrder->transactionCurrencyID;
                            $invDetail_arr['sellingCurrencyER'] = $deliveryOrder->transactionCurrencyER;
                            $invDetail_arr['localCurrencyID'] = $deliveryOrder->companyLocalCurrencyID;
                            $invDetail_arr['localCurrencyER'] = $deliveryOrder->companyLocalCurrencyER;
                            $invDetail_arr['reportingCurrencyID'] = $deliveryOrder->companyReportingCurrencyID;
                            $invDetail_arr['reportingCurrencyER'] = $deliveryOrder->companyReportingCurrencyER;

                            $invDetail_arr['itemUnitOfMeasure'] = $new['itemUnitOfMeasure'];
                            $invDetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureIssued'];
                            $invDetail_arr['qtyIssued'] = $new['noQty'];
                            $invDetail_arr['qtyIssuedDefaultMeasure'] = $new['noQty'];

                            $invDetail_arr['marginPercentage'] = 0;
                            if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                                $invDetail_arr['sellingCost'] = ($new['unitTransactionAmount']) - ($new['unitTransactionAmount']*$new['discountPercentage']/100);
                            }else{
                                $invDetail_arr['sellingCost'] = $new['unitTransactionAmount'];
                            }
                            $invDetail_arr['sellingCostAfterMargin'] = $invDetail_arr['sellingCost'];

                            $costs = $this->updateCostBySellingCost($invDetail_arr,$customerInvoioce);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
                            $invDetail_arr['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

                            $invDetail_arr['issueCostLocalTotal'] = $invDetail_arr['issueCostLocal'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['issueCostRptTotal'] = $invDetail_arr['issueCostRpt'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['sellingTotal'] = $invDetail_arr['sellingCostAfterMargin'] * $invDetail_arr['qtyIssuedDefaultMeasure'];

                            $invDetail_arr['issueCostLocal'] = Helper::roundValue($invDetail_arr['issueCostLocal']);
                            $invDetail_arr['issueCostLocalTotal'] = Helper::roundValue($invDetail_arr['issueCostLocalTotal']);
                            $invDetail_arr['issueCostRpt'] = Helper::roundValue($invDetail_arr['issueCostRpt']);
                            $invDetail_arr['issueCostRptTotal'] = Helper::roundValue($invDetail_arr['issueCostRptTotal']);
                            $invDetail_arr['sellingCost'] = Helper::roundValue($invDetail_arr['sellingCost']);
                            $invDetail_arr['sellingCostAfterMargin'] = Helper::roundValue($invDetail_arr['sellingCostAfterMargin']);
                            $invDetail_arr['sellingTotal'] = Helper::roundValue($invDetail_arr['sellingTotal']);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginLocal']);
                            $invDetail_arr['sellingCostAfterMarginRpt'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginRpt']);

                            $item = $this->customerInvoiceItemDetailsRepository->create($invDetail_arr);

                            $update = DeliveryOrderDetail::where('deliveryOrderDetailID', $new['deliveryOrderDetailID'])
                                ->update(['fullyReceived' => $fullyReceived, 'invQty' => $totalAddedQty]);
                        }

                        // fetching the total count records from purchase Request Details table
                        $doDetailTotalcount = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as detailCount'))
                            ->where('deliveryOrderID', $new['deliveryOrderID'])
                            ->first();

                        // fetching the total count records from purchase Request Details table where fullyOrdered = 2
                        $doDetailExist = DeliveryOrderDetail::select(DB::raw('count(deliveryOrderDetailID) as count'))
                            ->where('deliveryOrderID', $new['deliveryOrderID'])
                            ->where('fullyReceived', 2)
//                        ->where('selectedForPO', -1)
                            ->first();

                        // Updating PR Master Table After All Detail Table records updated
                        if ($doDetailTotalcount['detailCount'] == $doDetailExist['count']) {
                            $updatedo = DeliveryOrder::find($new['deliveryOrderID'])
                                ->update(['selectedForCustomerInvoice' => -1, 'closedYN' => -1]);
                        }
                    }
                }

                //check all details fullyOrdered in DO Master
                $doMasterfullyOrdered = DeliveryOrderDetail::where('deliveryOrderID', $new['deliveryOrderID'])
                    ->whereIn('fullyReceived', [1, 0])
                    ->get()->toArray();

                if (empty($doMasterfullyOrdered)) {
                    DeliveryOrder::find($new['deliveryOrderID'])
                        ->update([
                            'selectedForCustomerInvoice' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    DeliveryOrder::find($new['deliveryOrderID'])
                        ->update([
                            'selectedForCustomerInvoice' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateDOInvoicedStatus($new['deliveryOrderID']);

            }

            $resVat = $this->updateVatFromSalesDeliveryOrder($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            $resVat = $this->updateVatEligibilityOfCustomerInvoiceFromDO($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            DB::commit();
            return $this->sendResponse([], 'Customer Invoice Item Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }
        
    }

    public function getDeliveryOrderRecord(Request $request){

        $input = $request->all();
        $id = $input['deliveryOrderID'];
        $companySystemID = $input['companySystemID'];
        $deliveryOrder = DeliveryOrder::with(['company','customer','transaction_currency', 'tax','sales_person','detail' => function($query){
            $query->with(['quotation','uom_default','uom_issuing']);
        },'approved_by' => function($query) use($companySystemID){
            $query->where('companySystemID',$companySystemID)
                ->where('documentSystemID',71)
            ->with(['employee']);
        }])->find($id);

        if (empty($deliveryOrder)) {
            return $this->sendError('Delivery Order not found');
        }

        return $this->sendResponse($deliveryOrder->toArray(), 'Delivery Order retrieved successfully');
    }

    private function updateDOInvoicedStatus($deliveryOrderID){

        $status = 0;
        $invQty = CustomerInvoiceItemDetails::where('deliveryOrderID',$deliveryOrderID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $doQty = DeliveryOrderDetail::where('deliveryOrderID',$deliveryOrderID)->sum('qtyIssuedDefaultMeasure');
            if($invQty == $doQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
        }
        return DeliveryOrder::where('deliveryOrderID',$deliveryOrderID)->update(['invoiceStatus'=>$status]);

    }

    public function storeInvoiceDetailFromSalesQuotation(Request $request){

        $input = $request->all();
        $invDetail_arr = array();
        $custInvoiceDirectAutoID = $input['custInvoiceDirectAutoID'];
        $isCheckArr = collect($input['detailTable'])->pluck('isChecked')->toArray();
        if (!in_array(true, $isCheckArr)) {
            return $this->sendError("No items selected to add.");
        }

        $inputDetails = $input['detailTable'];
        $inputDetails = collect($inputDetails)->where('isChecked',1)->toArray();
        $financeCategories = collect($inputDetails)->pluck('itemCategory')->toArray();
        if (count(array_unique($financeCategories)) > 1) {
            return $this->sendError('Multiple finance category cannot be added. Different finance category found on selected details.',500);
        }

        foreach ($input['detailTable'] as $newValidation) {
            if (($newValidation['isChecked'] && $newValidation['noQty'] == "") || ($newValidation['isChecked'] && $newValidation['noQty'] == 0) || ($newValidation['isChecked'] == '' && $newValidation['noQty'] > 0)) {

                $messages = [
                    'required' => 'Invoice quantity field is required.',
                ];

                $validator = \Validator::make($newValidation, [
                    'noQty' => 'required',
                    'isChecked' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if($newValidation['noQty'] == 0){
                    return $this->sendError('Invoice Quantity should be greater than zero', 500);
                }
            }
        }

        $customerInvoioce = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first(); 
        $is_pref = $customerInvoioce->isPerforma;
   

        $itemExistArray = array();
        //check added item exist
        foreach ($input['detailTable'] as $itemExist) {


             $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $itemExist['itemAutoID'])
            ->where('companySystemID', $itemExist['companySystemID'])
            ->first();


            if ($itemExist['isChecked'] && $itemExist['noQty'] > 0) {
                $doDetailExist = CustomerInvoiceItemDetails::select(DB::raw('itemPrimaryCode'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('itemCodeSystem', $itemExist['itemAutoID'])
                    ->get();

                    if(isset($item->financeCategoryMaster) && $item->financeCategoryMaster != 2 && $item->financeCategoryMaster != 4 )
                    {
                        if (!empty($doDetailExist)) {
                            foreach ($doDetailExist as $row) {
                                $itemDrt = $row['itemPrimaryCode'] . " is already added";
                                $itemExistArray[] = [$itemDrt];
                            }
                        }
                    }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }

    
       

        // check qty and validations

        foreach ($input['detailTable'] as $row) {

            if ($row['isChecked'] && $row['noQty'] > 0) {

                if($row['itemCategory'] == 1){
                    $data = array(
                        'companySystemID' => $customerInvoioce->companySystemID,
                        'itemCodeSystem' => $row['itemAutoID'],
                        'wareHouseId' => $customerInvoioce->wareHouseSystemCode
                    );

                    $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
                    $currentStockQty = $itemCurrentCostAndQty['currentStockQty'];
                    $currentWareHouseStockQty = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                    $wacValueLocal = $itemCurrentCostAndQty['wacValueLocal'];
                    $wacValueReporting = $itemCurrentCostAndQty['wacValueReporting'];

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

                    $checkWhether = DeliveryOrder::where('companySystemID', $row['companySystemID'])
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
                    /* approved=0*/

                    if (!empty($checkWhetherPR)) {
                        return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                    }

                    $checkWhetherInvoice = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', '!=', $customerInvoioce->custInvoiceDirectAutoID)
                        ->where('companySystemID', $row['companySystemID'])
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

                }

            }
        }



        DB::beginTransaction();
        try {

            foreach ($input['detailTable'] as $new) {

                $quotationMaster = QuotationMaster::find($new['quotationMasterID']);

                $quotationDetailExist = CustomerInvoiceItemDetails::select(DB::raw('customerItemDetailID'))
                    ->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                    ->where('quotationDetailsID', $new['quotationDetailsID'])
                    ->first();

                if (empty($quotationDetailExist)) {

                    if ($new['isChecked'] && $new['noQty'] > 0) {

                        //checking the fullyOrdered or partial in delivery order
                        $detailSum = CustomerInvoiceItemDetails::select(DB::raw('COALESCE(SUM(qtyIssuedDefaultMeasure),0) as totalNoQty'))
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

                            $invDetail_arr['custInvoiceDirectAutoID'] = $custInvoiceDirectAutoID;

                            $invDetail_arr['quotationMasterID'] = $new['quotationMasterID'];
                            $invDetail_arr['quotationDetailsID'] = $new['quotationDetailsID'];
                            $invDetail_arr['itemCodeSystem'] = $new['itemAutoID'];
                            $invDetail_arr['itemPrimaryCode'] = $new['itemSystemCode'];
                            $invDetail_arr['itemDescription'] = $new['itemDescription'];
                            $invDetail_arr['sellingCost'] = ($new['unittransactionAmount'] - $new['discountAmount']);
                            if ($quotationMaster->documentSystemID == 67) {
                                $vatDetails = TaxService::getVATDetailsByItem($customerInvoioce->companySystemID, $new['itemAutoID'], $customerInvoioce->customerID,0);
                                $invDetail_arr['VATApplicableOn'] = $vatDetails['applicableOn'];
                                $invDetail_arr['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
                                $invDetail_arr['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
                                $invDetail_arr['VATPercentage'] = $vatDetails['percentage'];
                                $invDetail_arr['VATAmount'] = 0;

                                if (isset($invDetail_arr['sellingCost']) && $invDetail_arr['sellingCost'] > 0) {
                                    $invDetail_arr['VATAmount'] = (($invDetail_arr['sellingCost'] / 100) * $vatDetails['percentage']);
                                }
                                $currencyConversionVAT = \Helper::currencyConversion($customerInvoioce->companySystemID, $customerInvoioce->custTransactionCurrencyID, $customerInvoioce->custTransactionCurrencyID, $invDetail_arr['VATAmount']);

                                $invDetail_arr['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                                $invDetail_arr['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                            } else {
                                $invDetail_arr['VATPercentage'] = $new['VATPercentage'];
                                $invDetail_arr['VATAmount'] = $new['VATAmount'];
                                $invDetail_arr['VATAmountLocal'] = $new['VATAmountLocal'];
                                $invDetail_arr['VATAmountRpt'] = $new['VATAmountRpt'];
                                $invDetail_arr['VATApplicableOn'] = $new['VATApplicableOn'];
                                $invDetail_arr['vatMasterCategoryID'] = $new['vatMasterCategoryID'];
                                $invDetail_arr['vatSubCategoryID'] = $new['vatSubCategoryID'];
                            }

                            $item = ItemMaster::find($new['itemAutoID']);
                            if(empty($item)){
                                return $this->sendError('Item not found',500);
                            }

                            $data = array(
                                'companySystemID' => $customerInvoioce->companySystemID,
                                'itemCodeSystem' => $new['itemAutoID'],
                                'wareHouseId' => $customerInvoioce->wareHouseSystemCode
                            );

                            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);

                            $invDetail_arr['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
                            $invDetail_arr['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
                            $invDetail_arr['issueCostLocal'] = $itemCurrentCostAndQty['wacValueLocal'];
                            $invDetail_arr['issueCostRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
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
                                $invDetail_arr['financeCogsGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeCogsGLcodePLSystemID;
                                $invDetail_arr['financeCogsGLcodePL'] = $financeItemCategorySubAssigned->financeCogsGLcodePL;
                                $invDetail_arr['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
                                $invDetail_arr['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
                            } else {
                                return $this->sendError("Finance Item category sub assigned not found", 500);
                            }

                            if((!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']) && $item->financeCategoryMaster!=2){
                                return $this->sendError('BS account cannot be null for ' . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']){
                                return $this->sendError('Cost account cannot be null for ' . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeCogsGLcodePL'] || !$invDetail_arr['financeCogsGLcodePLSystemID']){
                                return $this->sendError('COGS GL account cannot be null for ' . $new['itemSystemCode'], 500);
                            }elseif (!$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']){
                                return $this->sendError('Revenue account cannot be null for ' . $new['itemSystemCode'], 500);
                            }

                            /*if (!$invDetail_arr['financeGLcodebBS'] || !$invDetail_arr['financeGLcodebBSSystemID']
                                || !$invDetail_arr['financeGLcodePL'] || !$invDetail_arr['financeGLcodePLSystemID']
                                || !$invDetail_arr['financeGLcodeRevenueSystemID'] || !$invDetail_arr['financeGLcodeRevenue']) {
                                return $this->sendError("Account code not updated for ".$new['itemSystemCode'].".", 500);
                            }*/


                            $invDetail_arr['sellingCurrencyID'] = $quotationMaster->transactionCurrencyID;
                            $invDetail_arr['sellingCurrencyER'] = $quotationMaster->transactionExchangeRate;
                            $invDetail_arr['localCurrencyID'] = $quotationMaster->companyLocalCurrencyID;
                            $invDetail_arr['localCurrencyER'] = $quotationMaster->companyLocalExchangeRate;
                            $invDetail_arr['reportingCurrencyID'] = $quotationMaster->companyReportingCurrencyID;
                            $invDetail_arr['reportingCurrencyER'] = $quotationMaster->companyReportingExchangeRate;
                            $invDetail_arr['part_no'] = $item->secondaryItemCode;


                            $invDetail_arr['itemUnitOfMeasure'] = $new['unitOfMeasureID'];
                            $invDetail_arr['unitOfMeasureIssued'] = $new['unitOfMeasureID'];
                            $invDetail_arr['qtyIssued'] = $new['noQty'];
                            $invDetail_arr['qtyIssuedDefaultMeasure'] = $new['noQty'];

                            $invDetail_arr['marginPercentage'] = 0;
                            /*if (isset($new['discountPercentage']) && $new['discountPercentage'] != 0){
                                $invDetail_arr['sellingCost'] = ($new['unittransactionAmount']) - ($new['unittransactionAmount']*$new['discountPercentage']/100);
                            }else{
                                $invDetail_arr['sellingCost'] = $new['unittransactionAmount'];
                            }*/

                            
                            $invDetail_arr['sellingCostAfterMargin'] = $invDetail_arr['sellingCost'];

                            $costs = $this->updateCostBySellingCost($invDetail_arr,$customerInvoioce);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = $costs['sellingCostAfterMarginLocal'];
                            $invDetail_arr['sellingCostAfterMarginRpt'] = $costs['sellingCostAfterMarginRpt'];

                            $invDetail_arr['issueCostLocalTotal'] = $invDetail_arr['issueCostLocal'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['issueCostRptTotal'] = $invDetail_arr['issueCostRpt'] * $invDetail_arr['qtyIssuedDefaultMeasure'];
                            $invDetail_arr['sellingTotal'] = $invDetail_arr['sellingCostAfterMargin'] * $invDetail_arr['qtyIssuedDefaultMeasure'];

                            $invDetail_arr['issueCostLocal'] = Helper::roundValue($invDetail_arr['issueCostLocal']);
                            $invDetail_arr['issueCostLocalTotal'] = Helper::roundValue($invDetail_arr['issueCostLocalTotal']);
                            $invDetail_arr['issueCostRpt'] = Helper::roundValue($invDetail_arr['issueCostRpt']);
                            $invDetail_arr['issueCostRptTotal'] = Helper::roundValue($invDetail_arr['issueCostRptTotal']);
                            $invDetail_arr['sellingCost'] = Helper::roundValue($invDetail_arr['sellingCost']);
                            $invDetail_arr['sellingCostAfterMargin'] = Helper::roundValue($invDetail_arr['sellingCostAfterMargin']);
                            $invDetail_arr['sellingTotal'] = Helper::roundValue($invDetail_arr['sellingTotal']);
                            $invDetail_arr['sellingCostAfterMarginLocal'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginLocal']);
                            $invDetail_arr['sellingCostAfterMarginRpt'] = Helper::roundValue($invDetail_arr['sellingCostAfterMarginRpt']);

                            $this->customerInvoiceItemDetailsRepository->create($invDetail_arr);

                            QuotationDetails::where('quotationDetailsID', $new['quotationDetailsID'])
                                ->update(['fullyOrdered' => $fullyOrdered, 'doQuantity' => $totalAddedQty]);
                        }

                    }
                }

                //check all details fullyOrdered in Quotation Master
                $QuotationMasterfullyOrdered = QuotationDetails::where('quotationMasterID', $new['quotationMasterID'])
                    ->whereIn('fullyOrdered', [1, 0])
                    ->get()->toArray();

                if (empty($QuotationMasterfullyOrdered)) {
                    QuotationMaster::find($new['quotationMasterID'])
                        ->update([
                            'selectedForDeliveryOrder' => -1,
                            'closedYN' => -1,
                        ]);
                } else {
                    QuotationMaster::find($new['quotationMasterID'])
                        ->update([
                            'selectedForDeliveryOrder' => 0,
                            'closedYN' => 0,
                        ]);
                }

                $this->updateSalesQuotationInvoicedStatus($new['quotationMasterID']);

            }

            $resVat = $this->updateVatFromSalesQuotation($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            $resVat = $this->updateVatEligibilityOfCustomerInvoice($custInvoiceDirectAutoID);
            if (!$resVat['status']) {
               return $this->sendError($resVat['message']); 
            } 

            DB::commit();
            return $this->sendResponse([], 'Customer Invoice Item Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred'. $exception->getMessage() . 'Line :' . $exception->getLine());
        }

    }

    public function updateVatEligibilityOfCustomerInvoice($custInvoiceDirectAutoID)
    { 
        $doDetailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
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

        $updateRes = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                  ->update(['vatRegisteredYN' => $vatRegisteredYN, 'customerVATEligible' => $customerVATEligible]);

        return ['status' => true];
    }

    public function updateVatEligibilityOfCustomerInvoiceFromDO($custInvoiceDirectAutoID)
    { 
        $doDetailData = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                           ->groupBy('quotationMasterID')
                                           ->get();

        $quMasterIds = $doDetailData->pluck('deliveryOrderID');

        $quotaionVatEligibleCheck = DeliveryOrder::whereIn('deliveryOrderID', $quMasterIds)
                                                   ->where('vatRegisteredYN', 1)
                                                   ->where('customerVATEligible', 1)
                                                   ->first();
        $vatRegisteredYN = 0;
        $customerVATEligible = 0;
        if ($quotaionVatEligibleCheck) {
            $customerVATEligible = 1;
            $vatRegisteredYN = 1;
        } 

        $updateRes = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                  ->update(['vatRegisteredYN' => $vatRegisteredYN, 'customerVATEligible' => $customerVATEligible]);

        return ['status' => true];
    }

    public function updateVatFromSalesQuotation($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                                    ->with(['sales_quotation_detail'])
                                                    ->get();

        $totalVATAmount = 0;
        $invoice = CustomerInvoiceDirect::find($custInvoiceDirectAutoID);

        foreach ($invoiceDetails as $key => $value) {
            if ($invoice->isPerforma == 2 || $invoice->isPerforma == 5) {
                $totalVATAmount += $value->qtyIssued * $value->VATAmount;
            } else {
                $totalVATAmount += $value->qtyIssued * ((isset($value->sales_quotation_detail->VATAmount) && !is_null($value->sales_quotation_detail->VATAmount)) ? $value->sales_quotation_detail->VATAmount : 0);
            }
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                              ->where('documentSystemID', 20)
                              ->delete();
        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

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

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }


        return ['status' => true];
    }

    public function updateVatFromSalesDeliveryOrder($custInvoiceDirectAutoID)
    {
        $invoiceDetails = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
                                                    ->with(['delivery_order_detail'])
                                                    ->get();

        $totalVATAmount = 0;
        foreach ($invoiceDetails as $key => $value) {
            $totalVATAmount += $value->qtyIssued * (isset($value->delivery_order_detail->VATAmount) ? $value->delivery_order_detail->VATAmount : 0);
        }

        $taxDelete = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
                              ->where('documentSystemID', 20)
                              ->delete();
        if ($totalVATAmount > 0) {
            $res = $this->savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount);

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

            CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);
        }

        return ['status' => true];
    }

    public function savecustomerInvoiceTaxDetails($custInvoiceDirectAutoID, $totalVATAmount)
    {
        $percentage = 0;
        $taxMasterAutoID = 0;

        $master = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();

        if (empty($master)) {
            return ['status' => false, 'message' => 'Customer Invoice not found.'];
        }

        $invoiceDetail = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
      
        if (empty($invoiceDetail)) {
            return ['status' => false, 'message' => 'Invoice Details not found.'];
        }

        $totalAmount = 0;
        $decimal = \Helper::getCurrencyDecimalPlace($master->custTransactionCurrencyID);

        $totalDetail = CustomerInvoiceItemDetails::select(DB::raw("SUM(sellingTotal) as amount"))->where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->first();
        if (!empty($totalDetail)) {
            $totalAmount = $totalDetail->amount;
        }

        if ($totalAmount > 0) {
            $percentage = ($totalVATAmount / $totalAmount) * 100;
        }

        $Taxdetail = Taxdetail::where('documentSystemCode', $custInvoiceDirectAutoID)
            ->where('documentSystemID', 20)
            ->first();

        if (!empty($Taxdetail)) {
            return ['status' => false, 'message' => 'VAT Detail Already exist.'];
        }

        $currencyConversion = \Helper::currencyConversion($master->companySystemID, $master->custTransactionCurrencyID, $master->custTransactionCurrencyID, $totalVATAmount);


        $_post['taxMasterAutoID'] = $taxMasterAutoID;
        $_post['companyID'] = $master->companyID;
        $_post['companySystemID'] = $master->companySystemID;
        $_post['documentID'] = 'INV';
        $_post['documentSystemID'] = $master->documentSystemiD;
        $_post['documentSystemCode'] = $custInvoiceDirectAutoID;
        $_post['documentCode'] = $master->bookingInvCode;
        $_post['taxShortCode'] = ''; //$taxMaster->taxShortCode;
        $_post['taxDescription'] = ''; //$taxMaster->taxDescription;
        $_post['taxPercent'] = $percentage; //$taxMaster->taxPercent;
        $_post['payeeSystemCode'] = $master->customerID; //$taxMaster->payeeSystemCode;
        $_post['currency'] = $master->custTransactionCurrencyID;
        $_post['currencyER'] = $master->custTransactionCurrencyER;
        $_post['amount'] = round($totalVATAmount, $decimal);
        $_post['payeeDefaultCurrencyID'] = $master->custTransactionCurrencyID;
        $_post['payeeDefaultCurrencyER'] = $master->custTransactionCurrencyER;
        $_post['payeeDefaultAmount'] = round($totalVATAmount, $decimal);
        $_post['localCurrencyID'] = $master->localCurrencyID;
        $_post['localCurrencyER'] = $master->localCurrencyER;

        $_post['rptCurrencyID'] = $master->companyReportingCurrencyID;
        $_post['rptCurrencyER'] = $master->companyReportingER;

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


        CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)->update($vatAmount);

        return ['status' => true];
    }

    private function updateSalesQuotationInvoicedStatus($quotationMasterID){

        $status = 0;
        $isInDO = 0;
        $invQty = CustomerInvoiceItemDetails::where('quotationMasterID',$quotationMasterID)->sum('qtyIssuedDefaultMeasure');

        if($invQty!=0) {
            $quotationQty = QuotationDetails::where('quotationMasterID',$quotationMasterID)->sum('requestedQty');
            if($invQty == $quotationQty){
                $status = 2;    // fully invoiced
            }else{
                $status = 1;    // partially invoiced
            }
            $isInDO = 2;
        }
        return QuotationMaster::where('quotationMasterID',$quotationMasterID)->update(['invoiceStatus'=>$status,'isInDOorCI'=>$isInDO]);

    }


    public function validateCustomerInvoiceDetails(Request $request) {
        $rows = $request['detailTable'];
            foreach($rows[0] as $row) {
                        /*pending approval checking*/
                        // check the item pending pending for approval in other delivery orders

                        $checkWhether = DeliveryOrder::where('companySystemID', $row['companySystemID'])
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
                        /* approved=0*/

                        if (!empty($checkWhetherPR)) {
                            return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                        }

                        // check policy 18

                        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
                            ->where('companySystemID', $row['companySystemID'])
                            ->first();
                        $item = ItemMaster::find($row['itemAutoID']);
                        if($item->financeCategoryMaster == 1){
                            $checkWhether = CustomerInvoiceDirect::where('companySystemID', $row['companySystemID'])
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
                            if (!empty($checkWhether)) {
                                return $this->sendError("There is a Customer Invoice (" . $checkWhether->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
                            }

                        }        
            }
    }

}

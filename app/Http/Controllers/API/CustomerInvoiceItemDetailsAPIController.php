<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\helper\inventory;
use App\Http\Requests\API\CreateCustomerInvoiceItemDetailsAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceItemDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerCatalogDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemClientReferenceNumberMaster;
use App\Models\ItemIssueMaster;
use App\Models\StockTransfer;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Repositories\CustomerInvoiceItemDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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

        $item = ItemAssigned::where('idItemAssigned', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError('Item not found');
        }

        $customerInvoiceDirect = CustomerInvoiceDirect::find($input['custInvoiceDirectAutoID']);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct Not Found');
        }

        /* TODO confirm approve check here*/

        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;

        $input['unitOfMeasureIssued'] = $item->itemUnitOfMeasure;
        $input['convertionMeasureVal'] = 1;

        $input['qtyIssued'] = 0;
        $input['qtyIssuedDefaultMeasure'] = 0;

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

        $input['issueCostLocalTotal'] =  $input['issueCostLocal'] * $input['qtyIssuedDefaultMeasure'];

        $input['reportingCurrencyID'] = $customerInvoiceDirect->companyReportingCurrencyID;
        $input['reportingCurrencyER'] = $customerInvoiceDirect->companyReportingER;

        $input['issueCostRptTotal'] = $input['issueCostRpt'] * $input['qtyIssuedDefaultMeasure'];
        $input['marginPercentage'] = 0;

        $companyCurrencyConversion = Helper::currencyConversion($companySystemID,$customerInvoiceDirect->companyReportingCurrencyID,$customerInvoiceDirect->custTransactionCurrencyID,$input['issueCostRpt']);
        $input['sellingCurrencyID'] = $customerInvoiceDirect->custTransactionCurrencyID;
        $input['sellingCurrencyER'] = $customerInvoiceDirect->custTransactionCurrencyER;
        $input['sellingCost'] = $companyCurrencyConversion['documentAmount'];
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
        }else{
            $input['sellingCostAfterMargin'] = $input['sellingCost'];
        }

        if(isset($input['marginPercentage']) && $input['marginPercentage'] != 0){
            $input['sellingCostAfterMarginLocal'] = ($input['issueCostLocal']) + ($input['issueCostLocal']*$input['marginPercentage']/100);
            $input['sellingCostAfterMarginRpt'] = ($input['issueCostRpt']) + ($input['issueCostRpt']*$input['marginPercentage']/100);
        }else{
            $input['sellingCostAfterMargin'] = $input['sellingCost'];
            $input['sellingCostAfterMarginLocal'] = $input['issueCostLocal'];
            $input['sellingCostAfterMarginRpt'] = $input['issueCostRpt'];
        }

        $input['sellingTotal'] = $input['sellingCostAfterMargin'] * $input['qtyIssuedDefaultMeasure'];

        /*round to 7 decimals*/
        $input['issueCostLocal'] = Helper::roundValue($input['issueCostLocal']);
        $input['issueCostLocalTotal'] = Helper::roundValue($input['issueCostLocalTotal']);
        $input['issueCostRpt'] = Helper::roundValue($input['issueCostRpt']);
        $input['issueCostRptTotal'] = Helper::roundValue($input['issueCostRptTotal']);
        $input['sellingCost'] = Helper::roundValue($input['sellingCost']);
        $input['sellingCostAfterMargin'] = Helper::roundValue($input['sellingCostAfterMargin']);
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
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['financeGLcodeRevenueSystemID'] = $financeItemCategorySubAssigned->financeGLcodeRevenueSystemID;
            $input['financeGLcodeRevenue'] = $financeItemCategorySubAssigned->financeGLcodeRevenue;
        } else {
            return $this->sendError("Account code not updated.", 500);
        }

        if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID']
            || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']
            || !$input['financeGLcodeRevenueSystemID'] || !$input['financeGLcodeRevenue']) {
            return $this->sendError("Account code not updated.", 500);
        }

        if ($input['itemFinanceCategoryID'] == 1) {
            $alreadyAdded = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->where('itemCodeSystem',$item->itemCodeSystem)->first();
            if ($alreadyAdded) {
                return $this->sendError("Selected item is already added. Please check again", 500);
            }
        }

        // check policy 18

        $allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

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
        $input = array_except($request->all(), ['uom_default', 'uom_issuing','item_by','issueUnits']);
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

        if ($input['itemUnitOfMeasure'] != $input['unitOfMeasureIssued']) {
            $unitConvention = UnitConversion::where('masterUnitID', $input['itemUnitOfMeasure'])
                ->where('subUnitID', $input['unitOfMeasureIssued'])
                ->first();
            if (empty($unitConvention)) {
                return $this->sendError('Unit Convention not found', 500);
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

        if(isset($input['by']) && $input['by']== 'cost'){
            $input['marginPercentage'] = ($input['sellingCostAfterMargin'] - $input['sellingCost'])/$input['sellingCost']*100;
        }elseif (isset($input['by']) && $input['by']== 'margin'){
            $input['sellingCostAfterMargin'] = ($input['sellingCost']) + ($input['sellingCost']*$input['marginPercentage']/100);
            $input['sellingCostAfterMarginLocal'] = ($input['issueCostLocal']) + ($input['issueCostLocal']*$input['marginPercentage']/100);
            $input['sellingCostAfterMarginRpt'] = ($input['issueCostRpt']) + ($input['issueCostRpt']*$input['marginPercentage']/100);
        }else{
            if (isset($input['marginPercentage']) && $input['marginPercentage'] != 0){
                $input['sellingCostAfterMargin'] = ($input['sellingCost']) + ($input['sellingCost']*$input['marginPercentage']/100);
                $input['sellingCostAfterMarginLocal'] = ($input['issueCostLocal']) + ($input['issueCostLocal']*$input['marginPercentage']/100);
                $input['sellingCostAfterMarginRpt'] = ($input['issueCostRpt']) + ($input['issueCostRpt']*$input['marginPercentage']/100);
            }else{
                $input['sellingCostAfterMargin'] = $input['sellingCost'];
                $input['sellingCostAfterMarginLocal'] = $input['issueCostLocal'];
                $input['sellingCostAfterMarginRpt'] = $input['issueCostRpt'];
            }
        }


        if ($customerInvoiceItemDetails->issueCostLocal == 0 || $customerInvoiceItemDetails->issueCostLocal == 0) {
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

        /*TODO confirm approve check*/

        $customerInvoiceItemDetails->delete();

        return $this->sendResponse($id, 'Customer Invoice Item Details deleted successfully');
    }

    public function getItemByCustomerInvoiceItemDetail(Request $request)
    {
        $input = $request->all();
        $id = $input['custInvoiceDirectAutoID'];

        $items = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $id)
            ->with(['uom_default', 'uom_issuing','item_by'])
            ->get();


        foreach ($items as $item) {

            $issueUnit = Unit::where('UnitID', $item['itemUnitOfMeasure'])->with(['unitConversion.sub_unit'])->first();

            $issueUnits = array();
            foreach ($issueUnit->unitConversion as $unit) {
                $temArray = array('value' => $unit->sub_unit->UnitID, 'label' => $unit->sub_unit->UnitShortCode);
                array_push($issueUnits, $temArray);
            }

            $item->issueUnits = $issueUnits;
        }

        return $this->sendResponse($items->toArray(), 'Item Details retrieved successfully');
    }
}

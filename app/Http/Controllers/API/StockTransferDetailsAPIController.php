<?php
/**
 * =============================================
 * -- File Name : StockTransferDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer Details
 * -- Author : Mohamed Nazir
 * -- Create date : 16-July 2018
 * -- Description : This file contains the all CRUD for Stock Transfer
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateStockTransferDetailsAPIRequest;
use App\Http\Requests\API\UpdateStockTransferDetailsAPIRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerInvoiceDirect;
use App\Models\DeliveryOrder;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\PurchaseReturn;
use App\Models\SegmentMaster;
use App\Models\StockTransferDetails;
use App\Models\StockTransfer;
use App\Models\DocumentSubProduct;
use App\Models\ItemSerial;
use App\Models\Company;
use App\Models\WarehouseMaster;
use App\Repositories\StockTransferDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Response;
use App\helper\ItemTracking;
use App\Models\ItemMaster;
use App\Models\UnitConversion;
/**
 * Class StockTransferDetailsController
 * @package App\Http\Controllers\API
 */
class StockTransferDetailsAPIController extends AppBaseController
{
    /** @var  StockTransferDetailsRepository */
    private $stockTransferDetailsRepository;
    private $userRepository;

    public function __construct(StockTransferDetailsRepository $stockTransferDetailsRepo, UserRepository $userRepo)
    {
        $this->stockTransferDetailsRepository = $stockTransferDetailsRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferDetails",
     *      summary="Get a listing of the StockTransferDetails.",
     *      tags={"StockTransferDetails"},
     *      description="Get all StockTransferDetails",
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
     *                  @SWG\Items(ref="#/definitions/StockTransferDetails")
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
        $this->stockTransferDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->stockTransferDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $stockTransferDetails = $this->stockTransferDetailsRepository->all();

        return $this->sendResponse($stockTransferDetails->toArray(), trans('custom.stock_transfer_details_retrieved_successfully_1'));
    }

    /**
     * @param CreateStockTransferDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/stockTransferDetails",
     *      summary="Store a newly created StockTransferDetails in storage",
     *      tags={"StockTransferDetails"},
     *      description="Store StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferDetails")
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
     *                  ref="#/definitions/StockTransferDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateStockTransferDetailsAPIRequest $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input = array_except($request->all(), 'unit_by');
        $input = $this->convertArrayToValue($input);

        $companySystemID = $input['companySystemID'];

        $item = ItemAssigned::with(['item_master'])
            ->where('itemCodeSystem', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemExist = StockTransferDetails::where('itemCodeSystem', $input['itemCode'])
            ->where('stockTransferAutoID', $input['stockTransferAutoID'])
            ->first();

        if (!empty($itemExist)) {
            return $this->sendError(trans('custom.selected_item_already_exist'), 500);
        }

        if (empty($item)) {
            return $this->sendError(trans('custom.item_not_found'));
        }

        $stockTransferMaster = StockTransfer::where('stockTransferAutoID', $input['stockTransferAutoID'])
            ->first();

        if (empty($stockTransferMaster)) {
            return $this->sendError(trans('custom.stock_transfer_not_found'), 500);
        }

        $validator = \Validator::make($stockTransferMaster->toArray(), [
            'locationFrom' => 'required|numeric|min:1',
            'locationTo' => 'required|numeric|min:1',
            'companyToSystemID' => 'required|numeric|min:1',
            'companyFromSystemID' => 'required|numeric|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $segment = SegmentMaster::where("serviceLineSystemID", $stockTransferMaster->serviceLineSystemID)
            ->where('companySystemID', $stockTransferMaster->companySystemID)
            ->where('isActive', 1)
            ->first();

        if (empty($segment)) {
            return $this->sendError('Selected department is not active. Please select an active department', 500);
        }


        $checkWareHouseActiveFrom = WarehouseMaster::find($stockTransferMaster->locationFrom);
        if (empty($checkWareHouseActiveFrom)) {
            return $this->sendError(trans('custom.location_from_not_found'), 500);
        }

        if ($checkWareHouseActiveFrom->isActive == 0) {
            return $this->sendError('Selected location from is not active. Please select an active location from', 500);
        }


        $checkWareHouseActiveTo = WarehouseMaster::find($stockTransferMaster->locationTo);
        if (empty($checkWareHouseActiveTo)) {
            return $this->sendError(trans('custom.location_to_not_found'), 500);
        }

        if ($checkWareHouseActiveTo->isActive == 0) {
            return $this->sendError('Selected location to is not active.Please select an active location to', 500);
        }


        $checkWhetherItemIssuePending = ItemIssueMaster::where('companySystemID', $companySystemID)
            ->where('wareHouseFrom', $stockTransferMaster->locationFrom)
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
            )->whereHas('details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCode']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherItemIssuePending)) {
            return $this->sendError("There is a Materiel Issue (" . $checkWhetherItemIssuePending->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $checkWhetherStockTransfer = StockTransfer::where('stockTransferAutoID', '!=', $stockTransferMaster->stockTransferAutoID)
            ->where('companySystemID', $companySystemID)
            ->where('locationFrom', $stockTransferMaster->locationFrom)
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
            )->whereHas('details', function ($query) use ($companySystemID, $input) {
                $query->where('itemCodeSystem', $input['itemCode']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherStockTransfer)) {
            return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        /*check item sales invoice*/
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
                $query->where('itemCodeSystem', $input['itemCode']);
            })
            ->where('approved', 0)
            ->where('canceledYN', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherInvoice)) {
            return $this->sendError("There is a Customer Invoice (" . $checkWhetherInvoice->bookingInvCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
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
                $query->where('itemCodeSystem', $input['itemCode']);
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
                $query->where('itemCode', $input['itemCode']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherPR)) {
            return $this->sendError("There is a Purchase Return (" . $checkWhetherPR->purchaseReturnCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $input['stockTransferCode'] = $stockTransferMaster->stockTransferCode;
        $input['itemCodeSystem'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $input['unitCostLocal'] = $item->wacValueLocal;
        $input['unitCostRpt'] = $item->wacValueReporting;
        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;
        $input['trackingType'] = isset($item->item_master->trackingType) ? $item->item_master->trackingType : null;

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        // if (WarehouseMaster::checkManuefactoringWareHouse($stockTransferMaster->locationFrom)) {
        //     $input['financeGLcodebBS'] = WarehouseMaster::getWIPGLCode($stockTransferMaster->locationFrom);
        //     $input['financeGLcodebBSSystemID'] = WarehouseMaster::getWIPGLSystemID($stockTransferMaster->locationFrom);
        // } else {
        //     $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
        //     $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
        // }

        $input['financeGLcodebBS'] = $financeItemCategorySubAssigned ? $financeItemCategorySubAssigned->financeGLcodebBS : null;
        $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned ? $financeItemCategorySubAssigned->financeGLcodebBSSystemID : null;
        

        


        $data = array('companySystemID' => $stockTransferMaster->companySystemID,
            'itemCodeSystem' => $input['itemCode'],
            'wareHouseId' => $stockTransferMaster->locationFrom);

        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['warehouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];

        if ($itemCurrentCostAndQty['currentWareHouseStockQty'] <= 0) {
            return $this->sendError("Warehouse stock Qty is 0. You cannot issue", 500);
        }

        if ($itemCurrentCostAndQty['currentStockQty'] <= 0) {
            return $this->sendError("Stock Qty is 0. You cannot issue", 500);
        }

        if ($input['unitCostLocal'] == 0 || $input['unitCostRpt'] == 0) {
            return $this->sendError("Cost is 0. You cannot issue", 500);
        }

        if ($input['unitCostLocal'] < 0 || $input['unitCostRpt'] < 0) {
            return $this->sendError("Cost is negative. You cannot issue", 500);
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['reportingCurrencyID'] = $company->reportingCurrency;
        }

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['employeeSystemID'];

        $stockTransferDetails = $this->stockTransferDetailsRepository->create($input);
        return $this->sendResponse($stockTransferDetails->toArray(), trans('custom.stock_transfer_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/stockTransferDetails/{id}",
     *      summary="Display the specified StockTransferDetails",
     *      tags={"StockTransferDetails"},
     *      description="Get StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetails",
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
     *                  ref="#/definitions/StockTransferDetails"
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
        /** @var StockTransferDetails $stockTransferDetails */
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            return $this->sendError(trans('custom.stock_transfer_details_not_found'));
        }

        return $this->sendResponse($stockTransferDetails->toArray(), trans('custom.stock_transfer_details_retrieved_successfully_1'));
    }

    /**
     * @param int $id
     * @param UpdateStockTransferDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/stockTransferDetails/{id}",
     *      summary="Update the specified StockTransferDetails in storage",
     *      tags={"StockTransferDetails"},
     *      description="Update StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="StockTransferDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/StockTransferDetails")
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
     *                  ref="#/definitions/StockTransferDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateStockTransferDetailsAPIRequest $request)
    {
        $input = $request->all();

        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);
        $input = array_except($request->all(), ['unit_by','item_by']);
        $input = $this->convertArrayToValue($input);
        $qtyError = array('type' => 'qty');

        /** @var StockTransferDetails $stockTransferDetails */
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            return $this->sendError(trans('custom.stock_transfer_details_not_found'));
        }

        $stockTransfer = StockTransfer::where("stockTransferAutoID", $stockTransferDetails->stockTransferAutoID)->first();
        if (empty($stockTransfer)) {
            return $this->sendError(trans('custom.stock_transfer_not_found'));
        }

        if ($stockTransferDetails->unitCostLocal == 0 || $stockTransferDetails->unitCostRpt == 0) {
            $input['qty'] = 0;
            $this->stockTransferDetailsRepository->update($input, $id);
            return $this->sendError("Cost is 0. You cannot issue", 500);
        }

        if ($stockTransferDetails->unitCostLocal < 0 || $stockTransferDetails->unitCostRpt < 0) {
            $input['qty'] = 0;
            $this->stockTransferDetailsRepository->update($input, $id);
            return $this->sendError("Cost is negative. You cannot issue", 500);
        }

        if ($stockTransferDetails->currentStockQty <= 0) {
            $input['qty'] = 0;
            $this->stockTransferDetailsRepository->update($input, $id);
            return $this->sendError("Stock Qty is 0. You cannot issue.", 500);
        }

        if ($stockTransferDetails->warehouseStockQty <= 0) {
            $input['qty'] = 0;
            $this->stockTransferDetailsRepository->update($input, $id);
            return $this->sendError("Warehouse stock Qty is 0. You cannot issue.", 500);
        }

        $itemDefaultUnit = ItemMaster::where('itemCodeSystem',$input['itemCodeSystem'])->select('unit')->first();
        $conversionUnit = UnitConversion::where('masterUnitID',$itemDefaultUnit->unit)->where('subUnitID',$input['unitOfMeasure'])->first();

        if (($input['qty'] / (isset($conversionUnit)? $conversionUnit->conversion:1)) > $stockTransferDetails->warehouseStockQty) {
            $input['qty'] = 0;
            $this->stockTransferDetailsRepository->update($input, $id);
            return $this->sendError("Current warehouse stock Qty is: " . $stockTransferDetails->warehouseStockQty . " .You cannot issue more than the current warehouse stock qty.", 500, $qtyError);
        }

        if (($input['qty'] / (isset($conversionUnit)? $conversionUnit->conversion:1)) > $stockTransferDetails->currentStockQty) {
            $input['qty'] = 0;
            $this->stockTransferDetailsRepository->update($input, $id);
            return $this->sendError("Current stock Qty is: " . $stockTransferDetails->currentStockQty . " .You cannot issue more than the current stock qty.", 500, $qtyError);
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];

        $item = ItemAssigned::where('itemCodeSystem', $input['itemCodeSystem'])
        ->where('companySystemID', $stockTransfer->companySystemID)
        ->first();

        $input['unitCostLocal'] = $itemDefaultUnit->unit != $input['unitOfMeasure'] && isset($conversionUnit)?$item->wacValueLocal/$conversionUnit->conversion:$item->wacValueLocal;
        $input['unitCostRpt'] = $itemDefaultUnit->unit != $input['unitOfMeasure'] && isset($conversionUnit)?$item->wacValueReporting/$conversionUnit->conversion:$item->wacValueReporting;
 

        $stockTransferDetails = $this->stockTransferDetailsRepository->update($input, $id);


        $message = "Item updated successfully";
        $stockTransferDetails->warningMsg = 0;

        $item = ItemAssigned::where('itemCodeSystem', $stockTransferDetails->itemCodeSystem)
            ->where('companySystemID', $stockTransfer->companySystemID)
            ->first();

        if (!empty($item)) {
            if (($stockTransferDetails->currentStockQty - ($stockTransferDetails->qty / (isset($conversionUnit)? $conversionUnit->conversion:1))) < $item->minimumQty) {
                $minQtyPolicy = CompanyPolicyMaster::where('companySystemID', $stockTransfer->companySystemID)
                    ->where('companyPolicyCategoryID', 6)
                    ->first();
                if (!empty($minQtyPolicy)) {
                    if ($minQtyPolicy->isYesNO == 1) {
                        $stockTransferDetails->warningMsg = 1;
                        $message = 'Quantity is falling below the minimum inventory level.';
                    }
                }
            }
        }
        return $this->sendResponse($stockTransferDetails->toArray(), $message);
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/stockTransferDetails/{id}",
     *      summary="Remove the specified StockTransferDetails from storage",
     *      tags={"StockTransferDetails"},
     *      description="Delete StockTransferDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of StockTransferDetails",
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
        /** @var StockTransferDetails $stockTransferDetails */
        $stockTransferDetails = $this->stockTransferDetailsRepository->findWithoutFail($id);

        if (empty($stockTransferDetails)) {
            return $this->sendError(trans('custom.stock_transfer_details_not_found'));
        }

        $stockTransfer = StockTransfer::find($stockTransferDetails->stockTransferAutoID);

        if (!$stockTransfer) {
            return $this->sendError(trans('custom.stock_transfer_not_found'));
        }

        if ($stockTransferDetails->trackingType == 2) {
            $validateSubProductSold = DocumentSubProduct::where('documentSystemID', $stockTransfer->documentSystemID)
                                                         ->where('documentDetailID', $id)
                                                         ->where('sold', 1)
                                                         ->first();

            if ($validateSubProductSold) {
                return $this->sendError(trans('custom.you_cannot_delete_this_line_item_serial_details_ar'), 422);
            }

            $subProduct = DocumentSubProduct::where('documentSystemID', $stockTransfer->documentSystemID)
                                             ->where('documentDetailID', $id);

            $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
            $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

            if (count($productInIDs) > 0) {
                $updateSerial = ItemSerial::whereIn('id', $serialIds)
                                          ->update(['wareHouseSystemID' => $stockTransfer->locationFrom]);

                $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
                                          ->update(['sold' => 0, 'soldQty' => 0]);

                $subProduct->delete();
            }
        } else if ($stockTransferDetails->trackingType == 1) {
            $deleteBatch = ItemTracking::revertBatchTrackingSoldStatus($stockTransfer->documentSystemID, $id, true);

            if (!$deleteBatch['status']) {
                return $this->sendError($deleteBatch['message'], 422);
            }
        }


        $stockTransferDetails->delete();

        return $this->sendResponse($id, trans('custom.stock_transfer_details_deleted_successfully'));
    }

    public function getStockTransferDetails(Request $request)
    {
        $input = $request->all();
        $stockTransferAutoID = $input['stockTransferAutoID'];

        $items = StockTransferDetails::select(DB::raw('stockTransferDetailsID,"" as totalCost,unitCostRpt,unitOfMeasure,itemCodeSystem,itemPrimaryCode,itemDescription,qty, currentStockQty,warehouseStockQty, trackingType'))
            ->where('stockTransferAutoID', $stockTransferAutoID)
            ->with(['unit_by' => function ($query) {
            },'item_by'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.stock_transfer_details_retrieved_successfully'));
    }
}

<?php
/**
 * =============================================
 * -- File Name : InventoryReclassificationDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Inventory
 * -- Author : Mohamed Mubashir
 * -- Create date : 10 - August 2018
 * -- Description : This file contains the all CRUD for Inventory Reclassification Detail
 * -- REVISION HISTORY
 * -- Date: 13-August 2018 By:Mubashir  Description: Added new functions named as getItemsByReclassification()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInventoryReclassificationDetailAPIRequest;
use App\Http\Requests\API\UpdateInventoryReclassificationDetailAPIRequest;
use App\Models\Company;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemAssigned;
use App\Models\ItemIssueMaster;
use App\Models\SegmentMaster;
use App\Models\StockTransfer;
use App\Models\WarehouseMaster;
use App\Repositories\InventoryReclassificationDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InventoryReclassificationDetailController
 * @package App\Http\Controllers\API
 */
class InventoryReclassificationDetailAPIController extends AppBaseController
{
    /** @var  InventoryReclassificationDetailRepository */
    private $inventoryReclassificationDetailRepository;

    public function __construct(InventoryReclassificationDetailRepository $inventoryReclassificationDetailRepo)
    {
        $this->inventoryReclassificationDetailRepository = $inventoryReclassificationDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassificationDetails",
     *      summary="Get a listing of the InventoryReclassificationDetails.",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Get all InventoryReclassificationDetails",
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
     *                  @SWG\Items(ref="#/definitions/InventoryReclassificationDetail")
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
        $this->inventoryReclassificationDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->inventoryReclassificationDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $inventoryReclassificationDetails = $this->inventoryReclassificationDetailRepository->all();

        return $this->sendResponse($inventoryReclassificationDetails->toArray(), trans('custom.inventory_reclassification_details_retrieved_succe'));
    }

    /**
     * @param CreateInventoryReclassificationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/inventoryReclassificationDetails",
     *      summary="Store a newly created InventoryReclassificationDetail in storage",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Store InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassificationDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassificationDetail")
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
     *                  ref="#/definitions/InventoryReclassificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInventoryReclassificationDetailAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $companySystemID = $input['companySystemID'];
        $reclassification = InventoryReclassification::find($input['inventoryreclassificationID']);

        if (empty($reclassification)) {
            return $this->sendError(trans('custom.reclassification_not_found'), 500);
        }

        if ($reclassification->serviceLineSystemID) {
            $checkDepartmentActive = SegmentMaster::find($reclassification->serviceLineSystemID);
            if (empty($checkDepartmentActive)) {
                return $this->sendError(trans('custom.department_not_found'));
            }
            if ($checkDepartmentActive->isActive == 0) {
                return $this->sendError('Please select a active department', 500);
            }
        } else {
            return $this->sendError('Please select a department.', 500);
        }

        if ($reclassification->wareHouseSystemCode) {
            $checkWarehouseActive = WarehouseMaster::find($reclassification->wareHouseSystemCode);
            if (empty($checkWarehouseActive)) {
                return $this->sendError(trans('custom.warehouse_not_found'));
            }
            if ($checkWarehouseActive->isActive == 0) {
                return $this->sendError('Please select an active warehouse', 500);
            }
        }
        else {
            return $this->sendError('Please select a warehouse.', 500);
        }

        $item = ItemAssigned::where('idItemAssigned', $input['itemCode'])
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return $this->sendError(trans('custom.item_not_found'));
        }

        $company = Company::where('companySystemID', $companySystemID)->first();

        if (empty($company)) {
            return $this->sendError(trans('custom.company_not_found'));
        }

        $input['itemSystemCode'] = $item->itemCodeSystem;
        $input['itemPrimaryCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['unitOfMeasure'] = $item->itemUnitOfMeasure;

        $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $input['unitCostLocal'] = $item->wacValueLocal;
        $input['unitCostRpt'] = $item->wacValueReporting;

        $input['localCurrencyID'] = $item->wacValueLocalCurrencyID;
        $input['reportingCurrencyID'] = $item->wacValueReportingCurrencyID;

        if ($input['unitCostLocal'] == 0 || $input['unitCostRpt'] == 0) {
            return $this->sendError("Cost is 0. You cannot add.", 500);
        }

        if ($input['unitCostLocal'] < 0 || $input['unitCostRpt'] < 0) {
            return $this->sendError("Cost is negative. You cannot add.", 500);
        }

        $checkWhether = ItemIssueMaster::where('companySystemID', $companySystemID)->where('wareHouseFrom', $reclassification->wareHouseSystemCode)
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
                $query->where('itemCodeSystem', $input['itemSystemCode']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhether)) {
            return $this->sendError("There is a Materiel Issue (" . $checkWhether->itemIssueCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $checkWhetherStockTransfer = StockTransfer::where('companySystemID', $companySystemID)->where('locationFrom', $reclassification->wareHouseSystemCode)
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
                $query->where('itemCodeSystem', $input['itemSystemCode']);
            })
            ->where('approved', 0)
            ->first();
        /* approved=0*/

        if (!empty($checkWhetherStockTransfer)) {
            return $this->sendError("There is a Stock Transfer (" . $checkWhetherStockTransfer->stockTransferCode . ") pending for approval for the item you are trying to add. Please check again.", 500);
        }

        $data = array('companySystemID' => $reclassification->companySystemID,
            'itemCodeSystem' => $input['itemSystemCode'],
            'wareHouseId' => $reclassification->wareHouseSystemCode);
        $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
        $input['currentStockQty'] = $itemCurrentCostAndQty['currentStockQty'];
        $input['currentWareHouseStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];


        if ($input['currentStockQty'] <= 0) {
            return $this->sendError("Stock Qty is 0. You cannot reclassify.", 500);
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
            ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
            ->first();

        if (!empty($financeItemCategorySubAssigned)) {
            $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
            $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
            $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
            $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
        } else {
            return $this->sendError("Account code not updated.", 500);
        }

        if (!$input['financeGLcodebBS'] || !$input['financeGLcodebBSSystemID'] || !$input['financeGLcodePL'] || !$input['financeGLcodePLSystemID']) {
            return $this->sendError("Account code not updated.", 500);
        }

        if ($input['itemFinanceCategoryID'] == 1) {
            $alreadyAdded = InventoryReclassificationDetail::where('inventoryreclassificationID', $input['inventoryreclassificationID'])->where('itemSystemCode', $input['itemSystemCode'])->exists();

            if ($alreadyAdded) {
                return $this->sendError("Selected item is already added. Please check again", 500);
            }
        }

        $inventoryReclassificationDetails = $this->inventoryReclassificationDetailRepository->create($input);

        return $this->sendResponse($inventoryReclassificationDetails->toArray(), trans('custom.inventory_reclassification_detail_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassificationDetails/{id}",
     *      summary="Display the specified InventoryReclassificationDetail",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Get InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassificationDetail",
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
     *                  ref="#/definitions/InventoryReclassificationDetail"
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
        /** @var InventoryReclassificationDetail $inventoryReclassificationDetail */
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            return $this->sendError(trans('custom.inventory_reclassification_detail_not_found'));
        }

        return $this->sendResponse($inventoryReclassificationDetail->toArray(), trans('custom.inventory_reclassification_detail_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdateInventoryReclassificationDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/inventoryReclassificationDetails/{id}",
     *      summary="Update the specified InventoryReclassificationDetail in storage",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Update InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassificationDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassificationDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassificationDetail")
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
     *                  ref="#/definitions/InventoryReclassificationDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInventoryReclassificationDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var InventoryReclassificationDetail $inventoryReclassificationDetail */
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            return $this->sendError(trans('custom.inventory_reclassification_detail_not_found'));
        }

        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->update($input, $id);

        return $this->sendResponse($inventoryReclassificationDetail->toArray(), trans('custom.inventoryreclassificationdetail_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/inventoryReclassificationDetails/{id}",
     *      summary="Remove the specified InventoryReclassificationDetail from storage",
     *      tags={"InventoryReclassificationDetail"},
     *      description="Delete InventoryReclassificationDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassificationDetail",
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
        /** @var InventoryReclassificationDetail $inventoryReclassificationDetail */
        $inventoryReclassificationDetail = $this->inventoryReclassificationDetailRepository->findWithoutFail($id);

        if (empty($inventoryReclassificationDetail)) {
            return $this->sendError(trans('custom.inventory_reclassification_detail_not_found'));
        }

        $inventoryReclassificationDetail->delete();

        return $this->sendResponse($id, trans('custom.inventory_reclassification_detail_deleted_successf'));
    }


    public function getItemsByReclassification(Request $request)
    {
        $input = $request->all();
        $items = InventoryReclassificationDetail::with(['unit', 'itemmaster','localcurrency','reportingcurrency'])->where('inventoryreclassificationID', $input["inventoryreclassificationID"])->get();
        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }
}

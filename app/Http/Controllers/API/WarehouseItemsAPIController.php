<?php
/**
 * =============================================
 * -- File Name : WarehouseItemsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Warehouse Items
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - September 2018
 * -- Description : This file contains the all CRUD for Warehouse Items
 * -- REVISION HISTORY
 * -- Date: 07-September 2018 By: Fayas Description: Added new functions named as getAllAssignedItemsByWarehouse()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseItemsAPIRequest;
use App\Http\Requests\API\UpdateWarehouseItemsAPIRequest;
use App\Models\WarehouseItems;
use App\Repositories\WarehouseItemsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WarehouseItemsController
 * @package App\Http\Controllers\API
 */

class WarehouseItemsAPIController extends AppBaseController
{
    /** @var  WarehouseItemsRepository */
    private $warehouseItemsRepository;

    public function __construct(WarehouseItemsRepository $warehouseItemsRepo)
    {
        $this->warehouseItemsRepository = $warehouseItemsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseItems",
     *      summary="Get a listing of the WarehouseItems.",
     *      tags={"WarehouseItems"},
     *      description="Get all WarehouseItems",
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
     *                  @SWG\Items(ref="#/definitions/WarehouseItems")
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
        $this->warehouseItemsRepository->pushCriteria(new RequestCriteria($request));
        $this->warehouseItemsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $warehouseItems = $this->warehouseItemsRepository->all();

        return $this->sendResponse($warehouseItems->toArray(), 'Warehouse Items retrieved successfully');
    }

    /**
     * @param CreateWarehouseItemsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/warehouseItems",
     *      summary="Store a newly created WarehouseItems in storage",
     *      tags={"WarehouseItems"},
     *      description="Store WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseItems that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseItems")
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
     *                  ref="#/definitions/WarehouseItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWarehouseItemsAPIRequest $request)
    {
        $input = $request->all();

        $warehouseItems = $this->warehouseItemsRepository->create($input);

        return $this->sendResponse($warehouseItems->toArray(), 'Warehouse Items saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseItems/{id}",
     *      summary="Display the specified WarehouseItems",
     *      tags={"WarehouseItems"},
     *      description="Get WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseItems",
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
     *                  ref="#/definitions/WarehouseItems"
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
        /** @var WarehouseItems $warehouseItems */
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            return $this->sendError('Warehouse Items not found');
        }

        return $this->sendResponse($warehouseItems->toArray(), 'Warehouse Items retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateWarehouseItemsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/warehouseItems/{id}",
     *      summary="Update the specified WarehouseItems in storage",
     *      tags={"WarehouseItems"},
     *      description="Update WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseItems",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseItems that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseItems")
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
     *                  ref="#/definitions/WarehouseItems"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWarehouseItemsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input,['binNumber']);
        /** @var WarehouseItems $warehouseItems */
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            return $this->sendError('Warehouse Items not found');
        }

        if(!isset($input['binNumber'])){
            $input['binNumber'] = 0;
        }

        $warehouseItems = $this->warehouseItemsRepository->update(array_only($input, ['binNumber']), $id);

        return $this->sendResponse($warehouseItems->toArray(), 'WarehouseItems updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/warehouseItems/{id}",
     *      summary="Remove the specified WarehouseItems from storage",
     *      tags={"WarehouseItems"},
     *      description="Delete WarehouseItems",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseItems",
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
        /** @var WarehouseItems $warehouseItems */
        $warehouseItems = $this->warehouseItemsRepository->findWithoutFail($id);

        if (empty($warehouseItems)) {
            return $this->sendError('Warehouse Items not found');
        }

        $warehouseItems->delete();

        return $this->sendResponse($id, 'Warehouse Items deleted successfully');
    }

    /**
     * Display a listing of the Items by warehouse.
     * POST /getAllAssignedItemsByWarehouse
     *
     * @param Request $request
     * @return Response
     */

    public function getAllAssignedItemsByWarehouse(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('financeCategoryMaster', 'financeCategorySub', 'isActive'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $itemMasters = WarehouseItems::with(['warehouse_by','binLocation','unit', 'financeMainCategory', 'financeSubCategory', 'local_currency', 'rpt_currency'])
            ->whereIn('companySystemID', $childCompanies)
            ->where('warehouseSystemCode', $input['warehouseSystemCode']);

        if (array_key_exists('financeCategoryMaster', $input)) {
            if ($input['financeCategoryMaster'] > 0 && !is_null($input['financeCategoryMaster'])) {
                $itemMasters->where('financeCategoryMaster', $input['financeCategoryMaster']);
            }
        }

        if (array_key_exists('financeCategorySub', $input)) {
            if ($input['financeCategorySub'] > 0 && !is_null($input['financeCategorySub'])) {
                $itemMasters->where('financeCategorySub', $input['financeCategorySub']);
            }
        }

        if (array_key_exists('isActive', $input)) {
            if (($input['isActive'] == 0 || $input['isActive'] == 1) && !is_null($input['isActive'])) {
                $itemMasters->where('isActive', $input['isActive']);
            }
        }
        if (array_key_exists('itemApprovedYN', $input)) {
            if (($input['itemApprovedYN'] == 0 || $input['itemApprovedYN'] == 1) && !is_null($input['itemApprovedYN'])) {
                $itemMasters->where('itemApprovedYN', $input['itemApprovedYN']);
            }
        }

        $search = $input['search']['value'];
        if ($search) {
            $itemMasters = $itemMasters->where(function ($query) use ($search) {
                $query->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('itemDescription', 'LIKE', "%{$search}%");
            });
        }

        $data = \DataTables::eloquent($itemMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('warehouseItemsID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('current', function ($row) {
                $data = array('companySystemID' => $row->companySystemID,
                    'itemCodeSystem' => $row->itemSystemCode,
                    'wareHouseId' => $row->warehouseSystemCode);
                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

                $array = array('local' => $itemCurrentCostAndQty['wacValueLocalWarehouse'],
                    'rpt' => $itemCurrentCostAndQty['wacValueReportingWarehouse'],
                    'wareHouseStock' => $itemCurrentCostAndQty['currentWareHouseStockQty'],
                    'totalWacCostLocal' => $itemCurrentCostAndQty['totalWacCostLocalWarehouse'],
                    'totalWacCostRpt' => $itemCurrentCostAndQty['totalWacCostRptWarehouse'],
                );
                return $array;

            })
            ->make(true);
        return $data;
        ///return $this->sendResponse($itemMasters->toArray(), 'Item Masters retrieved successfully');*/
    }
}

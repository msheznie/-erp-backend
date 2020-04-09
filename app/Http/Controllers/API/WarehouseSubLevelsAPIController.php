<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateWarehouseSubLevelsAPIRequest;
use App\Http\Requests\API\UpdateWarehouseSubLevelsAPIRequest;
use App\Models\WarehouseMaster;
use App\Models\WarehouseSubLevels;
use App\Repositories\WarehouseSubLevelsRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WarehouseSubLevelsController
 * @package App\Http\Controllers\API
 */
class WarehouseSubLevelsAPIController extends AppBaseController
{
    /** @var  WarehouseSubLevelsRepository */
    private $warehouseSubLevelsRepository;

    public function __construct(WarehouseSubLevelsRepository $warehouseSubLevelsRepo)
    {
        $this->warehouseSubLevelsRepository = $warehouseSubLevelsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseSubLevels",
     *      summary="Get a listing of the WarehouseSubLevels.",
     *      tags={"WarehouseSubLevels"},
     *      description="Get all WarehouseSubLevels",
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
     *                  @SWG\Items(ref="#/definitions/WarehouseSubLevels")
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
        $this->warehouseSubLevelsRepository->pushCriteria(new RequestCriteria($request));
        $this->warehouseSubLevelsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->all();

        return $this->sendResponse($warehouseSubLevels->toArray(), 'Warehouse Sub Levels retrieved successfully');
    }

    /**
     * @param CreateWarehouseSubLevelsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/warehouseSubLevels",
     *      summary="Store a newly created WarehouseSubLevels in storage",
     *      tags={"WarehouseSubLevels"},
     *      description="Store WarehouseSubLevels",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseSubLevels that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseSubLevels")
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
     *                  ref="#/definitions/WarehouseSubLevels"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWarehouseSubLevelsAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'warehouse_id' => 'required:numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $warehouse = WarehouseMaster::find($input['warehouse_id']);

        if (empty($warehouse)) {
            return $this->sendError('Warehouse not found');
        }

        $parentId = isset($input['parent_id']) ? $input['parent_id'] : 0;
        $input['level'] = 1;
        if ($parentId) {
            $warehouseParentSubLevel = $this->warehouseSubLevelsRepository->find($parentId);

            if (empty($warehouseParentSubLevel)) {
                return $this->sendError('Warehouse sub level not found');
            }

            $input['level'] = $warehouseParentSubLevel->level + 1;
        }

        if ($input['level'] == 3) {
            $input['isFinalLevel'] = 1;
        }

        $input['created_pc'] = gethostname();
        $input['created_by'] = Helper::getEmployeeSystemID();

        $warehouseSubLevels = $this->warehouseSubLevelsRepository->create($input);

        return $this->sendResponse($warehouseSubLevels->toArray(), 'Warehouse Sub Levels saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseSubLevels/{id}",
     *      summary="Display the specified WarehouseSubLevels",
     *      tags={"WarehouseSubLevels"},
     *      description="Get WarehouseSubLevels",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseSubLevels",
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
     *                  ref="#/definitions/WarehouseSubLevels"
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
        /** @var WarehouseSubLevels $warehouseSubLevels */
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->withcount(['children', 'bin_locations'])->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            return $this->sendError('Warehouse Sub Levels not found');
        }

        return $this->sendResponse($warehouseSubLevels->toArray(), 'Warehouse Sub Levels retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateWarehouseSubLevelsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/warehouseSubLevels/{id}",
     *      summary="Update the specified WarehouseSubLevels in storage",
     *      tags={"WarehouseSubLevels"},
     *      description="Update WarehouseSubLevels",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseSubLevels",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseSubLevels that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseSubLevels")
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
     *                  ref="#/definitions/WarehouseSubLevels"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWarehouseSubLevelsAPIRequest $request)
    {
        $input = $request->all();

        /** @var WarehouseSubLevels $warehouseSubLevels */
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            return $this->sendError('Warehouse Sub Levels not found');
        }

        $input['updated_pc'] = gethostname();
        $input['updated_by'] = Helper::getEmployeeSystemID();
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->update(array_only($input,
            ['name', 'updated_pc', 'updated_by', 'description', 'isFinalLevel']), $id);

        return $this->sendResponse($warehouseSubLevels->toArray(), 'WarehouseSubLevels updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/warehouseSubLevels/{id}",
     *      summary="Remove the specified WarehouseSubLevels from storage",
     *      tags={"WarehouseSubLevels"},
     *      description="Delete WarehouseSubLevels",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseSubLevels",
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
        /** @var WarehouseSubLevels $warehouseSubLevels */
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->withcount(['children', 'bin_locations'])->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            return $this->sendError('Warehouse Sub Levels not found');
        }

        if ($warehouseSubLevels->children_count > 0) {
            return $this->sendError('Cannot delete, This Level has Sub Levels.');
        }

        if ($warehouseSubLevels->bin_locations_count > 0) {
            return $this->sendError('Cannot delete, This Level has some bin locations.');
        }

        $warehouseSubLevels->delete();

        return $this->sendResponse($id, 'Warehouse Sub Levels deleted successfully');
    }

    public function getAllWarehouseSubLevels(Request $request)
    {

        $input = $request->all();

        $id = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;

        $warehouse = WarehouseMaster::withcount(['sub_levels' => function ($q) {
            $q->where('parent_id', 0)
                ->where('level', 1);
        }, 'bin_locations'])
            ->with(['sub_levels' => function ($q) {
                $q->where('parent_id', 0)
                    ->where('level', 1)
                    ->withcount(['children', 'bin_locations'])
                    ->with(['children' => function ($q1) { // 1st level
                        $q1->withcount(['children', 'bin_locations'])
                            ->with(['children' => function ($q3) { // 2nd level
                                $q3->withcount(['children', 'bin_locations'])
                                    ->with(['children']); // 3rd level
                            }]);
                    }]);
            }])
            ->find($id);

        if (empty($warehouse)) {
            return $this->sendError('Warehouse not found');
        }


        return $this->sendResponse($warehouse, 'Warehouse Sub Levels retrieved successfully');
    }

    public function getSubLevelsByWarehouse(Request $request)
    {

        $input = $request->all();

        $id = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;

        $warehouse = WarehouseMaster::find($id);

        if (empty($warehouse)) {
            return $this->sendError('Warehouse not found');
        }

        $subLevels = WarehouseSubLevels::where('warehouse_id',$id)->where('isFinalLevel',1)->get(['id','name']);


        return $this->sendResponse($subLevels, 'Warehouse Sub Levels retrieved successfully');
    }
}

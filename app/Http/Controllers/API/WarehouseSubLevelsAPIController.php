<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateWarehouseSubLevelsAPIRequest;
use App\Http\Requests\API\UpdateWarehouseSubLevelsAPIRequest;
use App\Models\WarehouseMaster;
use App\Models\WarehouseSubLevels;
use App\Repositories\WarehouseBinLocationRepository;
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
    private $warehouseBinLocationRepository;

    public function __construct(WarehouseSubLevelsRepository $warehouseSubLevelsRepo,
                                WarehouseBinLocationRepository $warehouseBinLocationRepo)
    {
        $this->warehouseSubLevelsRepository = $warehouseSubLevelsRepo;
        $this->warehouseBinLocationRepository = $warehouseBinLocationRepo;
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
        $input = $this->convertArrayToSelectedValue($request->all(),['isActive']);
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
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->withcount(['children' => function($q1){
            $q1->where('is_deleted', 0);
        }, 'bin_locations' => function($q1){
            $q1->where('isDeleted', 0);
        }])->findWithoutFail($id);

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
        $input = $this->convertArrayToSelectedValue($request->all(),['isActive']);

        /** @var WarehouseSubLevels $warehouseSubLevels */
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            return $this->sendError('Warehouse Sub Levels not found');
        }
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'warehouse_id' => 'required:numeric'
        ]);


        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $input['updated_pc'] = gethostname();
        $input['updated_by'] = Helper::getEmployeeSystemID();
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->update(array_only($input,
            ['name', 'updated_pc', 'updated_by', 'description', 'isFinalLevel','isActive']), $id);

        if(isset($input['isActive']) && $input['isActive'] == 0){
            $warehouseSubLevels1 = $this->warehouseSubLevelsRepository->findWhere(['parent_id' => $id]); // 2nd level
            $this->activateWarehouseSubLevel($warehouseSubLevels1,0);

            foreach ($warehouseSubLevels1 as $item){

                $warehouseSubLevels2 = $this->warehouseSubLevelsRepository->findWhere(['parent_id' => $item['id']]); // 3rd level
                $this->activateWarehouseSubLevel($warehouseSubLevels2,0);

                foreach ($warehouseSubLevels2 as $item1) {
                    $warehouseBinLocation3 = $this->warehouseBinLocationRepository->findWhere(['warehouseSubLevelId' => $item1['id']]);
                    $this->activateBinLocation($warehouseBinLocation3,0);
                }

                $warehouseBinLocation2 = $this->warehouseBinLocationRepository->findWhere(['warehouseSubLevelId' => $item['id']]);
                $this->activateBinLocation($warehouseBinLocation2,0);
            }

            $warehouseBinLocation1 = $this->warehouseBinLocationRepository->findWhere(['warehouseSubLevelId' => $id]);
            $this->activateBinLocation($warehouseBinLocation1,0);
        }

        if(isset($input['isActive']) && $input['isActive'] == 1 && isset($input['activeAllSubLevels']) && $input['activeAllSubLevels']){
            $warehouseSubLevels1 = $this->warehouseSubLevelsRepository->findWhere(['parent_id' => $id]); // 2nd level
            $this->activateWarehouseSubLevel($warehouseSubLevels1,1);

            foreach ($warehouseSubLevels1 as $item){

                $warehouseSubLevels2 = $this->warehouseSubLevelsRepository->findWhere(['parent_id' => $item['id']]); // 3rd level
                $this->activateWarehouseSubLevel($warehouseSubLevels2,1);

                foreach ($warehouseSubLevels2 as $item1) {
                    $warehouseBinLocation3 = $this->warehouseBinLocationRepository->findWhere(['warehouseSubLevelId' => $item1['id']]);
                    $this->activateBinLocation($warehouseBinLocation3,-1);
                }

                $warehouseBinLocation2 = $this->warehouseBinLocationRepository->findWhere(['warehouseSubLevelId' => $item['id']]);
                $this->activateBinLocation($warehouseBinLocation2,-1);
            }

            $warehouseBinLocation1 = $this->warehouseBinLocationRepository->findWhere(['warehouseSubLevelId' => $id]);
            $this->activateBinLocation($warehouseBinLocation1,-1);
        }


        return $this->sendResponse($warehouseSubLevels->toArray(), 'WarehouseSubLevels updated successfully');
    }

    public function activateBinLocation($array,$value){
        foreach ($array as $item){
            $this->warehouseBinLocationRepository->update(['isActive' => $value],$item['binLocationID']);
        }
    }

    public function activateWarehouseSubLevel($array,$value){
        foreach ($array as $item){
            $this->warehouseSubLevelsRepository->update(['isActive' => $value],$item['id']);
        }
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
        $warehouseSubLevels = $this->warehouseSubLevelsRepository->withcount(['children' => function($q1){
                $q1->where('is_deleted', 0);
            }, 'bin_locations' => function($q1){
                $q1->where('isDeleted', 0);
            }])->findWithoutFail($id);

        if (empty($warehouseSubLevels)) {
            return $this->sendError('Warehouse Sub Levels not found');
        }

        if ($warehouseSubLevels->children_count > 0) {
            return $this->sendError('Cannot delete, Selected Level Level has sub levels.');
        }

        if ($warehouseSubLevels->bin_locations_count > 0) {
            return $this->sendError('Cannot delete, Selected Level has bin locations created.');
        }

        $data['is_deleted'] = 1;
        $data['deleted_by'] = Helper::getEmployeeSystemID();
        $data['deleted_at'] = now();

        $this->warehouseSubLevelsRepository->update($data,$id);
        //$warehouseSubLevels->delete();

        return $this->sendResponse($id, 'Warehouse Sub Levels deleted successfully');
    }

    public function getAllWarehouseSubLevels(Request $request)
    {

        $input = $request->all();

        $id = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;

        $warehouse = WarehouseMaster::withcount(['sub_levels' => function ($q) {
            $q->where('parent_id', 0)
                ->where('level', 1)
                ->where('is_deleted', 0);
        }, 'bin_locations' => function($q1){
            $q1->where('isDeleted', 0);
        }])
            ->with(['sub_levels' => function ($q) {
                $q->where('parent_id', 0)
                    ->where('level', 1)
                    ->where('is_deleted', 0)
                    ->withcount(['children' => function($q1){
                            $q1->where('is_deleted', 0);
                        }, 'bin_locations' => function($q1){
                        $q1->where('isDeleted', 0);
                    }])
                    ->with(['children' => function ($q1) { // 1st level
                        $q1->where('is_deleted', 0)
                            ->withcount(['children' => function($q3){
                            $q3->where('is_deleted', 0);
                        }, 'bin_locations' => function($q1){
                                $q1->where('isDeleted', 0);
                            }])
                            ->with(['children' => function ($q3) { // 2nd level
                                $q3->where('is_deleted', 0)
                                    ->withcount(['children' => function($q4){
                                        $q4->where('is_deleted', 0);
                                    }, 'bin_locations' => function($q1){
                                        $q1->where('isDeleted', 0);
                                    }])
                                    ->with(['children' => function($q4){ // 3rd level
                                        $q4->where('is_deleted', 0);
                                    }]);
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

        $subLevels = WarehouseSubLevels::where('warehouse_id',$id)
                                        ->where('isFinalLevel',1)
                                        ->where('is_deleted',0)
                                        ->where('isActive',1)
                                        ->get(['id','name']);


        return $this->sendResponse($subLevels, 'Warehouse Sub Levels retrieved successfully');
    }
}

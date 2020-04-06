<?php
/**
 * =============================================
 * -- File Name : WarehouseBinLocationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Warehouse Bin Location
 * -- Author : Mohamed Fayas
 * -- Create date : 07 - September 2018
 * -- Description : This file contains the all CRUD for Warehouse Bin Location
 * -- REVISION HISTORY
 * -- Date: 11-September 2018 By: Fayas Description: Added new functions named as getAllBinLocationsByWarehouse()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseBinLocationAPIRequest;
use App\Http\Requests\API\UpdateWarehouseBinLocationAPIRequest;
use App\Models\Company;
use App\Models\WarehouseBinLocation;
use App\Models\WarehouseItems;
use App\Models\WarehouseMaster;
use App\Repositories\WarehouseBinLocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class WarehouseBinLocationController
 * @package App\Http\Controllers\API
 */
class WarehouseBinLocationAPIController extends AppBaseController
{
    /** @var  WarehouseBinLocationRepository */
    private $warehouseBinLocationRepository;

    public function __construct(WarehouseBinLocationRepository $warehouseBinLocationRepo)
    {
        $this->warehouseBinLocationRepository = $warehouseBinLocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseBinLocations",
     *      summary="Get a listing of the WarehouseBinLocations.",
     *      tags={"WarehouseBinLocation"},
     *      description="Get all WarehouseBinLocations",
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
     *                  @SWG\Items(ref="#/definitions/WarehouseBinLocation")
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
        $this->warehouseBinLocationRepository->pushCriteria(new RequestCriteria($request));
        $this->warehouseBinLocationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $warehouseBinLocations = $this->warehouseBinLocationRepository->all();

        return $this->sendResponse($warehouseBinLocations->toArray(), 'Warehouse Bin Locations retrieved successfully');
    }

    /**
     * @param CreateWarehouseBinLocationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/warehouseBinLocations",
     *      summary="Store a newly created WarehouseBinLocation in storage",
     *      tags={"WarehouseBinLocation"},
     *      description="Store WarehouseBinLocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseBinLocation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseBinLocation")
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
     *                  ref="#/definitions/WarehouseBinLocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateWarehouseBinLocationAPIRequest $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        $input['createdBy'] = $employee->empID;

        $validator = \Validator::make($input, [
            'wareHouseSystemCode' => 'required',
            'binLocationDes' => 'required',
            'companySystemID' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $warehouse           =  WarehouseMaster::find($input['wareHouseSystemCode']);

        if(empty($warehouse)){
            return $this->sendError('Warehouse not found');
        }
        $input['companySystemID'] = $warehouse->companySystemID;
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if (empty($company)) {
            return $this->sendError('Company not found');
        }
        $input['companyID'] = $company->CompanyID;
        $warehouseBinLocations = $this->warehouseBinLocationRepository->create($input);

        return $this->sendResponse($warehouseBinLocations->toArray(), 'Warehouse Bin Location saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/warehouseBinLocations/{id}",
     *      summary="Display the specified WarehouseBinLocation",
     *      tags={"WarehouseBinLocation"},
     *      description="Get WarehouseBinLocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseBinLocation",
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
     *                  ref="#/definitions/WarehouseBinLocation"
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
        /** @var WarehouseBinLocation $warehouseBinLocation */
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            return $this->sendError('Warehouse Bin Location not found');
        }

        return $this->sendResponse($warehouseBinLocation->toArray(), 'Warehouse Bin Location retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateWarehouseBinLocationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/warehouseBinLocations/{id}",
     *      summary="Update the specified WarehouseBinLocation in storage",
     *      tags={"WarehouseBinLocation"},
     *      description="Update WarehouseBinLocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseBinLocation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="WarehouseBinLocation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/WarehouseBinLocation")
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
     *                  ref="#/definitions/WarehouseBinLocation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateWarehouseBinLocationAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'wareHouseSystemCode' => 'required',
            'binLocationDes' => 'required',
            'companySystemID' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var WarehouseBinLocation $warehouseBinLocation */
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            return $this->sendError('Warehouse Bin Location not found');
        }

        $checkIsAssigned = WarehouseItems::where('binNumber',$id)->count();

        if($checkIsAssigned > 0){
            return $this->sendError('Bin location you are trying to change is already assigned to an inventory.',500);
        }

        $warehouseBinLocation = $this->warehouseBinLocationRepository->update(array_only($input, ['binLocationDes']), $id);

        return $this->sendResponse($warehouseBinLocation->toArray(), 'WarehouseBinLocation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/warehouseBinLocations/{id}",
     *      summary="Remove the specified WarehouseBinLocation from storage",
     *      tags={"WarehouseBinLocation"},
     *      description="Delete WarehouseBinLocation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of WarehouseBinLocation",
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
        /** @var WarehouseBinLocation $warehouseBinLocation */
        $warehouseBinLocation = $this->warehouseBinLocationRepository->findWithoutFail($id);

        if (empty($warehouseBinLocation)) {
            return $this->sendError('Warehouse Bin Location not found');
        }

        $checkIsAssigned = WarehouseItems::where('binNumber',$id)->count();

        if($checkIsAssigned > 0){
            return $this->sendError('Bin location you are trying to delete is already assigned to an inventory.',500);
        }

        $warehouseBinLocation->delete();

        return $this->sendResponse($id, 'Warehouse Bin Location deleted successfully');
    }

    public function getAllBinLocationsByWarehouse(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('wareHouseSystemCode'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];

        $warehouseSystemCode = isset($input['warehouseSystemCode']) ? $input['warehouseSystemCode'] : 0;

        $warehouse           =  WarehouseMaster::find($warehouseSystemCode);

        if(!empty($warehouse)){
            $selectedCompanyId = $warehouse->companySystemID;
        }

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $warehouseSubLevelId = isset($input['warehouseSubLevelId']) ? $input['warehouseSubLevelId'] : 0;

        $warehouseBinLocation = WarehouseBinLocation::whereIn('companySystemID', $subCompanies)
                                            ->where('wareHouseSystemCode', $input['wareHouseSystemCode'])
                                            ->with('warehouse_by');

        if($warehouseSubLevelId){
            $warehouseBinLocation = $warehouseBinLocation->where('warehouseSubLevelId',$warehouseSubLevelId);
        }


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $warehouseBinLocation = $warehouseBinLocation->where(function ($query) use ($search) {
                $query->where('binLocationDes', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($warehouseBinLocation)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('binLocationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}

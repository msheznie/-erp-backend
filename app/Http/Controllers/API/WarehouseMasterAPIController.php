<?php
/**
=============================================
-- File Name : WarehouseMasterController.php
-- Project Name : ERP
-- Module Name :  Bank Master
-- Author : Pasan Madhuranga
-- Create date :  15 - March 2018
-- Description : This file contains the all CRUD for Warehouse Master
-- REVISION HISTORY
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as getWarehouseMasterFormData()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as getCompanyById()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as getAllWarehouseMaster()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as updateWarehouseMaster()
-- Date: 10 - April 2018 By: Mubashir Description: Changed warehouse not found error message
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseMasterAPIRequest;
use App\Http\Requests\API\UpdateWarehouseMasterAPIRequest;
use App\Models\WarehouseMaster;
use App\Models\Company;
use App\Models\ErpLocation;
use App\Models\YesNoSelection;
use App\Repositories\WarehouseMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Validation\Rule;
/**
 * Class WarehouseMasterController
 * @package App\Http\Controllers\API
 */

class WarehouseMasterAPIController extends AppBaseController
{
    /** @var  WarehouseMasterRepository */
    private $warehouseMasterRepository;

    public function __construct(WarehouseMasterRepository $warehouseMasterRepo)
    {
        $this->warehouseMasterRepository = $warehouseMasterRepo;
    }

    /**
     * Display a listing of the WarehouseMaster.
     * GET|HEAD /warehouseMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->warehouseMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->warehouseMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $warehouseMasters = $this->warehouseMasterRepository->all();

        return $this->sendResponse($warehouseMasters->toArray(), 'Warehouse Masters retrieved successfully');
    }

    /**
     * Store a newly created WarehouseMaster in storage.
     * POST /warehouseMasters
     *
     * @param CreateWarehouseMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateWarehouseMasterAPIRequest $request)
    {
        $input = $request->all();
        if(isset($input['companySystemID']))
        {
            $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        }

        $messages = array(
            'wareHouseCode.unique'   => 'Warehouse code already exists'
        );

        $validator = \Validator::make($input, [
            'wareHouseCode' => 'unique:warehousemaster'
        ],$messages);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422 );
        }

        $warehouseMasters = $this->warehouseMasterRepository->create($input);

        return $this->sendResponse($warehouseMasters->toArray(), 'Warehouse Master saved successfully');
    }

    /**
     * Display the specified WarehouseMaster.
     * GET|HEAD /warehouseMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var WarehouseMaster $warehouseMaster */
        $warehouseMaster = $this->warehouseMasterRepository->findWithoutFail($id);

        if (empty($warehouseMaster)) {
            return $this->sendError('Warehouse Master not found');
        }

        return $this->sendResponse($warehouseMaster->toArray(), 'Warehouse Master retrieved successfully');
    }

    /**
     * Update the specified WarehouseMaster in storage.
     * PUT/PATCH /warehouseMasters/{id}
     *
     * @param  int $id
     * @param UpdateWarehouseMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateWarehouseMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var WarehouseMaster $warehouseMaster */
        $warehouseMaster = $this->warehouseMasterRepository->findWithoutFail($id);

        if (empty($warehouseMaster)) {
            return $this->sendError('Warehouse Master not found');
        }

        $warehouseMaster = $this->warehouseMasterRepository->update($input, $id);

        return $this->sendResponse($warehouseMaster->toArray(), 'WarehouseMaster updated successfully');
    }

    /**
     * Remove the specified WarehouseMaster from storage.
     * DELETE /warehouseMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var WarehouseMaster $warehouseMaster */
        $warehouseMaster = $this->warehouseMasterRepository->findWithoutFail($id);

        if (empty($warehouseMaster)) {
            return $this->sendError('Warehouse Master not found');
        }

        $warehouseMaster->delete();

        return $this->sendResponse($id, 'Warehouse Master deleted successfully');
    }

    /**
     * Get warehouse related dropdown data for create warehouse master
     * @param Request $request
     * @return mixed
     */
    public function getWarehouseMasterFormData(Request $request)
    {
        /** all Company  Drop Down */
        $allCompanies = Company::select('companySystemID', 'CompanyID', 'CompanyName')->where("isGroup",0)->get();

        /** all Locations Drop Down */
        $erpLocations = ErpLocation::select('locationID', 'locationName')->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $output = array(
            'allCompanies' => $allCompanies,
            'erpLocations' => $erpLocations,
            'yesNoSelection' => $yesNoSelection
        );

        return $this->sendResponse($output, 'Record retrieved successfully');

    }

    /**
     * Get company by id
     * @param $companySystemID
     * @return mixed
     */
    private function getCompanyById($companySystemID)
    {
        $company = Company::select('CompanyID')->where("companySystemID",$companySystemID)->first();

        return $company->CompanyID;
    }

    /**
     * Get warehouse master data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllWarehouseMaster(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $warehouseMasters = WarehouseMaster::with(['location', 'company'])
            ->select('warehousemaster.*');

        $search = $request->input('search.value');
        if($search){
            $warehouseMasters = $warehouseMasters->where('wareHouseCode','LIKE',"%{$search}%")
                ->orWhere( 'wareHouseDescription', 'LIKE', "%{$search}%");
        }


        return \DataTables::eloquent($warehouseMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('wareHouseSystemCode', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Update warehouse master
     * @param Request $request
     * @return mixed
     */
    public function updateWarehouseMaster(Request $request)
    {
        $input = $request->all();

        if(isset($input['companySystemID']))
        {
            $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        }

        if (is_array($input['companySystemID']))
            $input['companySystemID'] = $input['companySystemID'][0];

        if (is_array($input['isActive']))
            $input['isActive'] = $input['isActive'][0];

        if (is_array($input['wareHouseLocation']))
            $input['wareHouseLocation'] = $input['wareHouseLocation'][0];

        $messages = array(
            'wareHouseCode.unique'   => 'Warehouse code already exists'
        );

        $validator = \Validator::make($input, [
            'wareHouseCode' => Rule::unique('warehousemaster')->ignore($input['wareHouseSystemCode'], 'wareHouseSystemCode')
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }
        $data =array_except($input, ['wareHouseSystemCode', 'timestamp']);

        $warehouseMaster = $this->warehouseMasterRepository->update($data, $input['wareHouseSystemCode']);

        return $this->sendResponse($warehouseMaster->toArray(), 'Warehouse master updated successfully');
    }
}

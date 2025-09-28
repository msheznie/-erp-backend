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
-- Date: 13 - July 2018 By: Nazir Description:  Added a new function named as getAllWarehouseForSelectedCompany()
-- Date: 02 - January 2018 By: Fayas Description:  Modified function getAllWarehouseMaster()
-- Date: 03 - January 2018 By: Fayas Description:  Modified function uploadWarehouseImage()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWarehouseMasterAPIRequest;
use App\Http\Requests\API\UpdateWarehouseMasterAPIRequest;
use App\Models\WarehouseMaster;
use App\Models\Company;
use App\Models\ErpLocation;
use App\Models\YesNoSelection;
use App\Repositories\WarehouseBinLocationRepository;
use App\Repositories\WarehouseMasterRepository;
use App\Repositories\WarehouseSubLevelsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
    private $warehouseSubLevelsRepository;
    private $warehouseBinLocationRepository;

    public function __construct(WarehouseMasterRepository $warehouseMasterRepo,
                                WarehouseSubLevelsRepository $warehouseSubLevelsRepo,
                                WarehouseBinLocationRepository $warehouseBinLocationRepo)
    {
        $this->warehouseMasterRepository = $warehouseMasterRepo;
        $this->warehouseSubLevelsRepository = $warehouseSubLevelsRepo;
        $this->warehouseBinLocationRepository = $warehouseBinLocationRepo;
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

        return $this->sendResponse($warehouseMasters->toArray(), trans('custom.warehouse_masters_retrieved_successfully'));
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
        $input = $this->convertArrayToValue($input);
        $entityName = trans('custom.warehouse');
        if(isset($input['isPosLocation']) && $input['isPosLocation'])
        {
            $entityName = trans('custom.outlet');
        }

        $messages = array(
            'wareHouseCode.unique'   => trans('custom.warehouse_code_unique', ['entityName' => $entityName]),
            'wareHouseCode.required'   => trans('custom.warehouse_code_required_with_entity', ['entityName' => $entityName]),
            'wareHouseLocation.unique'   => trans('custom.warehouse_location_unique'),
        );

        $validator = \Validator::make($input, [
            'wareHouseCode' => 'required|unique:warehousemaster',
            'companySystemID' => 'required',
            'wareHouseLocation' => 'required',
            'wareHouseDescription' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }
        $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        $employee = \Helper::getEmployeeInfo();
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserSystemID'] = $employee->employeeSystemID;
        $input['createdUserName'] = $employee->empName;

        $warehouseMasters = $this->warehouseMasterRepository->create($input);

        return $this->sendResponse($warehouseMasters->toArray(), $entityName.' '.trans('custom.saved_successfully'));
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
        $warehouseMaster = $this->warehouseMasterRepository->withcount(['sub_levels' => function ($q) {
            $q->where('is_deleted', 0);
        }, 'bin_locations' => function($q1){
            $q1->where('isDeleted', 0);
        }])->findWithoutFail($id);

        if (empty($warehouseMaster)) {
            return $this->sendError(trans('custom.warehouse_master_not_found'));
        }

        return $this->sendResponse($warehouseMaster->toArray(), trans('custom.warehouse_master_retrieved_successfully'));
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
        if(isset($input['wareHouseImage'])){
            $wareHouseImage = $input['wareHouseImage'];
        }else{
            $wareHouseImage = null;
        }

        $input = array_except($input, ['wareHouseImage']);
        $input = $this->convertArrayToValue($input);
        $entityName = trans('custom.warehouse');
        if(isset($input['isPosLocation']) && $input['isPosLocation'])
        {
            $entityName = trans('custom.outlet');
        }

        $messages = array(
            'wareHouseCode.unique'   => trans('custom.warehouse_code_unique', ['entityName' => $entityName]),
            'wareHouseCode.required'   => trans('custom.warehouse_code_required_with_entity', ['entityName' => $entityName]),
            'wareHouseLocation.unique'   => trans('custom.warehouse_location_unique'),
        );

        $validator = \Validator::make($input, [
            'wareHouseCode' => Rule::unique('warehousemaster')->ignore($input['wareHouseSystemCode'], 'wareHouseSystemCode'),
            'companySystemID' => 'required',
            'wareHouseLocation' => 'required',
            'wareHouseDescription' => 'required'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        /** @var WarehouseMaster $warehouseMaster */
        $warehouseMaster = $this->warehouseMasterRepository->findWithoutFail($id);

        if (empty($warehouseMaster)) {
            return $this->sendError($entityName.' '.trans('custom.error_not_found'));
        }
        $input['companyID'] = $this->getCompanyById($input['companySystemID']);
        $employee = \Helper::getEmployeeInfo();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserSystemID'] = $employee->employeeSystemID;
        $input['modifiedUserName'] = $employee->empName;
        $input['modifiedDateTime'] = now();

        if(!empty($wareHouseImage)){
            $to_path   = "warehouse/".$id;
            $destination = public_path($to_path);
            if (!file_exists($destination)) {
                File::makeDirectory($destination, 0777, true);
            }
            if (Storage::disk('local_public')->exists($wareHouseImage['path'])) {
                //Storage::disk('local_public')->move($wareHouseImage['path'], $to_path);
                File::move(public_path($wareHouseImage['path']), $to_path.'/'.$wareHouseImage['file_name']);
                $input['templateImgUrl'] = $to_path.'/'.$wareHouseImage['file_name'];
            }
        }

        $warehouseMaster = $this->warehouseMasterRepository->update($input, $id);

        if(isset($input['isActive']) && $input['isActive'] == 0){
                  $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWhere(['warehouse_id' => $id]);

                  foreach ($warehouseSubLevels as $item){
                      $this->warehouseSubLevelsRepository->update(['isActive' => 0],$item['id']);
                  }
                  $warehouseBinLocation = $this->warehouseBinLocationRepository->findWhere(['wareHouseSystemCode' => $id]);

                  foreach ($warehouseBinLocation as $item){
                    $this->warehouseBinLocationRepository->update(['isActive' => 0],$item['binLocationID']);
                 }
        }

        if(isset($input['isActive']) && $input['isActive'] == 1 && isset($input['activeAllSubLevels']) && $input['activeAllSubLevels']){
            $warehouseSubLevels = $this->warehouseSubLevelsRepository->findWhere(['warehouse_id' => $id]);

            foreach ($warehouseSubLevels as $item){
                $this->warehouseSubLevelsRepository->update(['isActive' => 1],$item['id']);
            }
            $warehouseBinLocation = $this->warehouseBinLocationRepository->findWhere(['wareHouseSystemCode' => $id]);

            foreach ($warehouseBinLocation as $item){
                $this->warehouseBinLocationRepository->update(['isActive' => -1],$item['binLocationID']);
            }
        }


        return $this->sendResponse($warehouseMaster->toArray(), $entityName.' '.trans('custom.updated_successfully'));
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
            return $this->sendError(trans('custom.warehouse_master_not_found'));
        }

        $warehouseMaster->delete();

        return $this->sendResponse($id, trans('custom.warehouse_master_deleted_successfully'));
    }

    /**
     * Get warehouse related dropdown data for create warehouse master
     * @param Request $request
     * @return mixed
     */
    public function getWarehouseMasterFormData(Request $request)
    {

        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }
        $allCompanies = Company::whereIn("companySystemID",$subCompanies)
            ->select('companySystemID', 'CompanyID', 'CompanyName')
            ->get();
        /** all Locations Drop Down */
        $erpLocations = ErpLocation::where('is_deleted', 0)->select('locationID', 'locationName')->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        $output = array(
            'allCompanies' => $allCompanies,
            'erpLocations' => $erpLocations,
            'yesNoSelection' => $yesNoSelection
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));

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

        $input['isActive'] = isset($input['isActive']) ? $input['isActive'] : -1;

        $input = $this->convertArrayToSelectedValue($input,['isActive']);

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }

        $warehouseMasters = WarehouseMaster::with(['location', 'company'])
            ->whereIn('companySystemID',$childCompanies)
            ->when(request('isPosLocation') == -1, function ($q) {
                $q->where('isPosLocation',-1);
            })
            ->when(($input['isActive'] == 1 || $input['isActive'] == 0) && !is_null($input['isActive']), function ($q) use($input){
                $q->where('isActive',$input['isActive']);
            });

        $search = $request->input('search.value');
        if($search){
            $warehouseMasters =   $warehouseMasters->where(function ($query) use($search) {
                $query->where('wareHouseCode','LIKE',"%{$search}%")
                      ->orWhere( 'wareHouseDescription', 'LIKE', "%{$search}%")
                      ->orWhereHas('company',function ($q) use($search){
                          $q->where('CompanyName','LIKE',"%{$search}%")
                            ->orWhere('CompanyID','LIKE',"%{$search}%");
                      });
            });
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

        $entityName = trans('custom.warehouse');
        if(isset($input['isPosLocation']) && $input['isPosLocation'])
        {
            $entityName = trans('custom.outlet');
        }

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
            'wareHouseCode.unique'   => trans('custom.warehouse_code_unique', ['entityName' => $entityName])
        );

        $validator = \Validator::make($input, [
            'wareHouseCode' => Rule::unique('warehousemaster')->ignore($input['wareHouseSystemCode'], 'wareHouseSystemCode')
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }
        $data = array_except($input, ['wareHouseSystemCode', 'timestamp']);

        $warehouseMaster = $this->warehouseMasterRepository->update($data, $input['wareHouseSystemCode']);

        return $this->sendResponse($warehouseMaster->toArray(), $entityName.' updated successfully');
    }

    public function getAllWarehouseForSelectedCompany(Request $request)
    {
        $companyId = $request['selectedCompany'];

        $warehouseMasters = WarehouseMaster::where('companySystemID',$companyId)
            ->where('isActive', 1)
            ->get();

        return $this->sendResponse($warehouseMasters->toArray(), trans('custom.record_retrieved_successfully_1'));
    }


    public function uploadWarehouseImage(Request $request)
    {
        try {
            if ($request->hasFile('file')) {
                $img = $request->file('file');
                $file_ext = $img->getClientOriginalExtension();
                $filename = "template_image_".md5(microtime()).$request->get('id').'.'.$file_ext;
                $fileSize = $img->getClientSize();
                $fileName = $img->getClientOriginalName();
                $temp_path = "warehouse_temp_file/".$request->get('id');

                $destination = public_path($temp_path);
                if (!file_exists($destination)) {
                    File::makeDirectory($destination, 0777, true);
                }

                Storage::disk('local_public')->putFileAs($temp_path, $img, $filename);

                $response = [
                    'file_name' => $filename,
                    'file_ext' => $file_ext,
                    'path' => $temp_path.'/'.$filename,
                    'file_size' => round($fileSize / 1024, 2),
                    'original_file_name' => $fileName
                ];
                return $this->sendResponse($response, trans('custom.image_uploaded_successfully'));
            } else {
                return $this->sendError(trans('custom.image_cannot_be_uploaded'),500);
            }
        } catch (\Exception $exception) {
            return $this->sendError(trans('custom.image_cannot_be_uploaded'),500);
        }
    }

}

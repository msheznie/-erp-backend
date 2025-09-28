<?php
/**
=============================================
-- File Name : UnitAPIController.php
-- Project Name : ERP
-- Module Name :  Unit Master
-- Author : Pasan Madhuranga
-- Create date :  22 - March 2018
-- Description : This file contains the all CRUD for Unit Master
-- REVISION HISTORY
-- Date: 22 - March 2018 By: Pasan Description: Added a new function named as getAllUnitMaster()
-- Date: 22 - March 2018 By: Pasan Description: Added a new function named as updateUnitMaster()
-- Date: 22 - March 2018 By: Pasan Description: Added a new function named as getUnitMasterFormData()
-- Date: 22 - March 2018 By: Pasan Description: Added a new function named as getUnitConversionsByUnitId()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnitAPIRequest;
use App\Http\Requests\API\UpdateUnitAPIRequest;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Repositories\UnitRepository;
use App\Repositories\UnitConversionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Class UnitController
 * @package App\Http\Controllers\API
 */

class UnitAPIController extends AppBaseController
{
    /** @var  UnitRepository */
    private $unitRepository;
    private $unitConversionRepository;

    public function __construct(UnitRepository $unitRepo, UserRepository $userRepo, UnitConversionRepository $unitConversionRepository)
    {
        $this->unitRepository = $unitRepo;
        $this->userRepository = $userRepo;
        $this->unitConversionRepository = $unitConversionRepository;
    }

    /**
     * Display a listing of the Unit.
     * GET|HEAD /units
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unitRepository->pushCriteria(new RequestCriteria($request));
        $this->unitRepository->pushCriteria(new LimitOffsetCriteria($request));
        $units = $this->unitRepository->all();

        return $this->sendResponse($units->toArray(), trans('custom.units_retrieved_successfully'));
    }

    /**
     * Store a newly created Unit in storage.
     * POST /units
     *
     * @param CreateUnitAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUnitAPIRequest $request)
    {
        $input = $request->all();

        $messages = array(
            'UnitShortCode.unique'   => trans('custom.unit_code_already_exists')
        );

        $validator = \Validator::make($input, [
            'UnitShortCode' => 'unique:units'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $id = \Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdUserID'] = $empId;
        $input['createdPcID'] = gethostname();

        $units = $this->unitRepository->create($input);

        // add conversion to created unit

        if($units) {
            $unitConversion = [
                'masterUnitID' => $units->UnitID,
                'subUnitID' => $units->UnitID,
                'conversion' => "1"
            ];

           $this->unitConversionRepository->create($unitConversion);

        }



        return $this->sendResponse($units->toArray(), trans('custom.unit_saved_successfully'));
    }

    /**
     * Display the specified Unit.
     * GET|HEAD /units/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);

        if (empty($unit)) {
            return $this->sendError(trans('custom.unit_not_found'));
        }

        return $this->sendResponse($unit->toArray(), trans('custom.unit_retrieved_successfully'));
    }

    /**
     * Update the specified Unit in storage.
     * PUT/PATCH /units/{id}
     *
     * @param  int $id
     * @param UpdateUnitAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnitAPIRequest $request)
    {
        $input = $request->all();

        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);

        if (empty($unit)) {
            return $this->sendError(trans('custom.unit_not_found'));
        }

        $unit = $this->unitRepository->update($input, $id);

        return $this->sendResponse($unit->toArray(), trans('custom.unit_updated_successfully'));
    }

    /**
     * Remove the specified Unit from storage.
     * DELETE /units/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Unit $unit */
        $unit = $this->unitRepository->findWithoutFail($id);

        if (empty($unit)) {
            return $this->sendError(trans('custom.unit_not_found'));
        }
        $unit->unitConversion()->delete();
        $unit->delete();

        return $this->sendResponse($id, trans('custom.unit_deleted_successfully'));
    }

    /**
     * Get unit master data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllUnitMaster(Request $request)
    {
        $input = $request->all();
        $keyword = $input['search']['value'];

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $unitMasters = Unit::select('UnitID', 'UnitShortCode', 'UnitDes')->select('units.*');

        $search = $request->input('search.value');
        if($search){
            $unitMasters =   $unitMasters->where('UnitShortCode','LIKE',"%{$search}%")
                ->orWhere('UnitDes', 'LIKE', "%{$search}%");
        }

        return \DataTables::eloquent($unitMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('UnitID', $input['order'][0]['dir']);
                    } else if($input['order'][0]['column'] == 1)
                    {
                        $query->orderBy('UnitShortCode', $input['order'][0]['dir']);
                    } else
                    {
                        $query->orderBy('UnitDes', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Update unit master details by unit id
     * @param Request $request
     * @return mixed
     */
    public function updateUnitMaster(Request $request)
    {
        $input = $request->all();

        $messages = array(
            'UnitShortCode.unique'   => trans('custom.unit_short_code_taken')
        );

        $validator = \Validator::make($input, [
            'UnitShortCode' => Rule::unique('units')->ignore($input['UnitID'], 'UnitID')
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $id = \Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $input['modifiedUser'] = $empId;
        $input['modifiedPc'] = gethostname();
        $data =array_except($input, ['UnitID', 'timeStamp', 'createdDateTime']);

        $unitMaster = $this->unitRepository->update($data, $input['UnitID']);

        return $this->sendResponse($unitMaster->toArray(), trans('custom.unit_master_updated_successfully'));
    }

    /**
     * Get unit master related dropdown data
     * @param Request $request
     * @return mixed
     */
    public function getUnitMasterFormData(Request $request)
    {
        $unitId = $request['UnitID'];

        $unitData = $this->getUnitConversionsByUnitId($unitId);

        $unitMaster = Unit::select('UnitID', 'UnitShortCode', 'UnitDes')
            ->whereNotIn('UnitID', $unitData['unitIdArray'])
            ->get();

        $output = array(
            'allUnits' => $unitMaster,
            'unitConversion' => $unitData['dataArray'],
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));

    }

    /**
     * Get all conversions for a given master unit
     * @param $unitId
     * @return array
     */
    private function getUnitConversionsByUnitId($unitId)
    {
        $subUnitConversion = UnitConversion::select('unitsConversionAutoID', 'subUnitID','conversion')
                                ->where('masterUnitID',$unitId )
                                ->get();
        $unitIdArray = [];
        $dataArray = [];
        foreach($subUnitConversion as $key=>$val)
        {
            $unitData = Unit::select('UnitID', 'UnitShortCode', 'UnitDes')
                            ->where('UnitID', $val->subUnitID)
                            ->first();

            $val->UnitID        = $unitData->UnitID;
            $val->UnitShortCode = $unitData->UnitShortCode;
            $val->UnitDes       = $unitData->UnitDes;
            $dataArray[]        = $val;
            $unitIdArray[]      = $unitData->UnitID;
        }

        $returnData = [
            'dataArray'     => $dataArray,
            'unitIdArray'   => $unitIdArray
        ];

        return $returnData;
    }
}

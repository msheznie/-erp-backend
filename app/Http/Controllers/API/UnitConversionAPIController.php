<?php
/**
=============================================
-- File Name : UnitConversionAPIController.php
-- Project Name : ERP
-- Module Name :  Unit Conversion
-- Author : Pasan Madhuranga
-- Create date :  22 - March 2018
-- Description : This file contains the all CRUD for Unit Conversion
-- REVISION HISTORY
-- Date: 22 - March 2018 By: Pasan Description: Added a new function named as getUnitConversionFormData()
-- Date: 22 - March 2018 By: Pasan Description: Added a new function named as updateUnitConversion()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnitConversionAPIRequest;
use App\Http\Requests\API\UpdateUnitConversionAPIRequest;
use App\Models\UnitConversion;
use App\Repositories\UnitConversionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class UnitConversionController
 * @package App\Http\Controllers\API
 */

class UnitConversionAPIController extends AppBaseController
{
    /** @var  UnitConversionRepository */
    private $unitConversionRepository;

    public function __construct(UnitConversionRepository $unitConversionRepo)
    {
        $this->unitConversionRepository = $unitConversionRepo;
    }

    /**
     * Display a listing of the UnitConversion.
     * GET|HEAD /unitConversions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->unitConversionRepository->pushCriteria(new RequestCriteria($request));
        $this->unitConversionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $unitConversions = $this->unitConversionRepository->all();

        return $this->sendResponse($unitConversions->toArray(), trans('custom.unit_conversions_retrieved_successfully'));
    }

    /**
     * Store a newly created UnitConversion in storage.
     * POST /unitConversions
     *
     * @param CreateUnitConversionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateUnitConversionAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'conversion' => 'numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $unitConversions = $this->unitConversionRepository->create($input);

        return $this->sendResponse($unitConversions->toArray(), trans('custom.unit_conversion_saved_successfully'));
    }

    /**
     * Display the specified UnitConversion.
     * GET|HEAD /unitConversions/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var UnitConversion $unitConversion */
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            return $this->sendError(trans('custom.unit_conversion_not_found'));
        }

        return $this->sendResponse($unitConversion->toArray(), trans('custom.unit_conversion_retrieved_successfully'));
    }

    /**
     * Update the specified UnitConversion in storage.
     * PUT/PATCH /unitConversions/{id}
     *
     * @param  int $id
     * @param UpdateUnitConversionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUnitConversionAPIRequest $request)
    {
        $input = $request->all();

        /** @var UnitConversion $unitConversion */
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            return $this->sendError(trans('custom.unit_conversion_not_found'));
        }

        $unitConversion = $this->unitConversionRepository->update($input, $id);

        return $this->sendResponse($unitConversion->toArray(), trans('custom.unitconversion_updated_successfully'));
    }

    /**
     * Remove the specified UnitConversion from storage.
     * DELETE /unitConversions/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var UnitConversion $unitConversion */
        $unitConversion = $this->unitConversionRepository->findWithoutFail($id);

        if (empty($unitConversion)) {
            return $this->sendError(trans('custom.unit_conversion_not_found'));
        }

        $unitConversion->delete();

        return $this->sendResponse($id, trans('custom.unit_conversion_deleted_successfully'));
    }

    /**
     * Get unit conversion related data
     * @param Request $request
     * @return mixed
     */
    public function getUnitConversionFormData(Request $request)
    {
        $unitsConversionAutoID = $request['unitsConversionAutoID'];

        $conversionData = UnitConversion::select('conversion')
                        ->where('unitsConversionAutoID', $unitsConversionAutoID)
                        ->first();

        $output = array(
            'conversionData' => $conversionData
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));

    }

    /**
     * update unit conversions by id
     * @param Request $request
     * @return mixed
     */
    public function updateUnitConversion(Request $request)
    {
        $input = $request->all();

        $data = [
            'masterUnitID'  => $input['masterUnitID'],
            'subUnitID'     => is_array($input['subUnitID']) ? $input['subUnitID'][0] : $input['subUnitID'],
            'conversion'    => $input['updateConversion'],
        ];

        $validator = \Validator::make($data, [
            'conversion' => 'numeric'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $unitConversion = $this->unitConversionRepository->update($data, $input['unitsConversionAutoID']);

        return $this->sendResponse($unitConversion->toArray(), trans('custom.unit_conversion_updated_successfully'));
    }
}

<?php

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

        return $this->sendResponse($unitConversions->toArray(), 'Unit Conversions retrieved successfully');
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

        return $this->sendResponse($unitConversions->toArray(), 'Unit Conversion saved successfully');
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
            return $this->sendError('Unit Conversion not found');
        }

        return $this->sendResponse($unitConversion->toArray(), 'Unit Conversion retrieved successfully');
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
            return $this->sendError('Unit Conversion not found');
        }

        $unitConversion = $this->unitConversionRepository->update($input, $id);

        return $this->sendResponse($unitConversion->toArray(), 'UnitConversion updated successfully');
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
            return $this->sendError('Unit Conversion not found');
        }

        $unitConversion->delete();

        return $this->sendResponse($id, 'Unit Conversion deleted successfully');
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

        return $this->sendResponse($output, 'Record retrieved successfully');

    }
}

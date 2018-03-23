<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpLocationAPIRequest;
use App\Http\Requests\API\UpdateErpLocationAPIRequest;
use App\Models\ErpLocation;
use App\Repositories\ErpLocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpLocationController
 * @package App\Http\Controllers\API
 */

class ErpLocationAPIController extends AppBaseController
{
    /** @var  ErpLocationRepository */
    private $erpLocationRepository;

    public function __construct(ErpLocationRepository $erpLocationRepo)
    {
        $this->erpLocationRepository = $erpLocationRepo;
    }

    /**
     * Display a listing of the ErpLocation.
     * GET|HEAD /erpLocations
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpLocationRepository->pushCriteria(new RequestCriteria($request));
        $this->erpLocationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpLocations = $this->erpLocationRepository->all();

        return $this->sendResponse($erpLocations->toArray(), 'Erp Locations retrieved successfully');
    }

    /**
     * Store a newly created ErpLocation in storage.
     * POST /erpLocations
     *
     * @param CreateErpLocationAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateErpLocationAPIRequest $request)
    {
        $input = $request->all();

        $erpLocations = $this->erpLocationRepository->create($input);

        return $this->sendResponse($erpLocations->toArray(), 'Erp Location saved successfully');
    }

    /**
     * Display the specified ErpLocation.
     * GET|HEAD /erpLocations/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ErpLocation $erpLocation */
        $erpLocation = $this->erpLocationRepository->findWithoutFail($id);

        if (empty($erpLocation)) {
            return $this->sendError('Erp Location not found');
        }

        return $this->sendResponse($erpLocation->toArray(), 'Erp Location retrieved successfully');
    }

    /**
     * Update the specified ErpLocation in storage.
     * PUT/PATCH /erpLocations/{id}
     *
     * @param  int $id
     * @param UpdateErpLocationAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpLocationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpLocation $erpLocation */
        $erpLocation = $this->erpLocationRepository->findWithoutFail($id);

        if (empty($erpLocation)) {
            return $this->sendError('Erp Location not found');
        }

        $erpLocation = $this->erpLocationRepository->update($input, $id);

        return $this->sendResponse($erpLocation->toArray(), 'ErpLocation updated successfully');
    }

    /**
     * Remove the specified ErpLocation from storage.
     * DELETE /erpLocations/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ErpLocation $erpLocation */
        $erpLocation = $this->erpLocationRepository->findWithoutFail($id);

        if (empty($erpLocation)) {
            return $this->sendError('Erp Location not found');
        }

        $erpLocation->delete();

        return $this->sendResponse($id, 'Erp Location deleted successfully');
    }
}

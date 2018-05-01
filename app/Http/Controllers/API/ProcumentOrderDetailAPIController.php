<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentOrderDetailAPIRequest;
use App\Http\Requests\API\UpdateProcumentOrderDetailAPIRequest;
use App\Models\ProcumentOrderDetail;
use App\Repositories\ProcumentOrderDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ProcumentOrderDetailController
 * @package App\Http\Controllers\API
 */

class ProcumentOrderDetailAPIController extends AppBaseController
{
    /** @var  ProcumentOrderDetailRepository */
    private $procumentOrderDetailRepository;

    public function __construct(ProcumentOrderDetailRepository $procumentOrderDetailRepo)
    {
        $this->procumentOrderDetailRepository = $procumentOrderDetailRepo;
    }

    /**
     * Display a listing of the ProcumentOrderDetail.
     * GET|HEAD /procumentOrderDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->procumentOrderDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->procumentOrderDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $procumentOrderDetails = $this->procumentOrderDetailRepository->all();

        return $this->sendResponse($procumentOrderDetails->toArray(), 'Procurement Order Details retrieved successfully');
    }

    /**
     * Store a newly created ProcumentOrderDetail in storage.
     * POST /procumentOrderDetails
     *
     * @param CreateProcumentOrderDetailAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProcumentOrderDetailAPIRequest $request)
    {
        $input = $request->all();

        $procumentOrderDetails = $this->procumentOrderDetailRepository->create($input);

        return $this->sendResponse($procumentOrderDetails->toArray(), 'Procurement Order Detail saved successfully');
    }

    /**
     * Display the specified ProcumentOrderDetail.
     * GET|HEAD /procumentOrderDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProcumentOrderDetail $procumentOrderDetail */
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            return $this->sendError('Procurement Order Detail not found');
        }

        return $this->sendResponse($procumentOrderDetail->toArray(), 'Procurement Order Detail retrieved successfully');
    }

    /**
     * Update the specified ProcumentOrderDetail in storage.
     * PUT/PATCH /procumentOrderDetails/{id}
     *
     * @param  int $id
     * @param UpdateProcumentOrderDetailAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcumentOrderDetailAPIRequest $request)
    {
        $input = array_except($request->all(), 'unit');
        $input = $this->convertArrayToValue($input);

        /** @var ProcumentOrderDetail $procumentOrderDetail */
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            return $this->sendError('Procurement Order Detail not found');
        }

        $procumentOrderDetail = $this->procumentOrderDetailRepository->update($input, $id);

        return $this->sendResponse($procumentOrderDetail->toArray(), 'Procurement Order Detail updated successfully');
    }

    /**
     * Remove the specified ProcumentOrderDetail from storage.
     * DELETE /procumentOrderDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProcumentOrderDetail $procumentOrderDetail */
        $procumentOrderDetail = $this->procumentOrderDetailRepository->findWithoutFail($id);

        if (empty($procumentOrderDetail)) {
            return $this->sendError('Procurement Order Detail not found');
        }

        $procumentOrderDetail->delete();

        return $this->sendResponse($id, 'Procurement Order Detail deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateErpAddressAPIRequest;
use App\Http\Requests\API\UpdateErpAddressAPIRequest;
use App\Models\ErpAddress;
use App\Repositories\ErpAddressRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ErpAddressController
 * @package App\Http\Controllers\API
 */

class ErpAddressAPIController extends AppBaseController
{
    /** @var  ErpAddressRepository */
    private $erpAddressRepository;

    public function __construct(ErpAddressRepository $erpAddressRepo)
    {
        $this->erpAddressRepository = $erpAddressRepo;
    }

    /**
     * Display a listing of the ErpAddress.
     * GET|HEAD /erpAddresses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->erpAddressRepository->pushCriteria(new RequestCriteria($request));
        $this->erpAddressRepository->pushCriteria(new LimitOffsetCriteria($request));
        $erpAddresses = $this->erpAddressRepository->all();

        return $this->sendResponse($erpAddresses->toArray(), 'Erp Addresses retrieved successfully');
    }

    /**
     * Store a newly created ErpAddress in storage.
     * POST /erpAddresses
     *
     * @param CreateErpAddressAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateErpAddressAPIRequest $request)
    {
        $input = $request->all();

        $erpAddresses = $this->erpAddressRepository->create($input);

        return $this->sendResponse($erpAddresses->toArray(), 'Erp Address saved successfully');
    }

    /**
     * Display the specified ErpAddress.
     * GET|HEAD /erpAddresses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ErpAddress $erpAddress */
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            return $this->sendError('Erp Address not found');
        }

        return $this->sendResponse($erpAddress->toArray(), 'Erp Address retrieved successfully');
    }

    /**
     * Update the specified ErpAddress in storage.
     * PUT/PATCH /erpAddresses/{id}
     *
     * @param  int $id
     * @param UpdateErpAddressAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateErpAddressAPIRequest $request)
    {
        $input = $request->all();

        /** @var ErpAddress $erpAddress */
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            return $this->sendError('Erp Address not found');
        }

        $erpAddress = $this->erpAddressRepository->update($input, $id);

        return $this->sendResponse($erpAddress->toArray(), 'ErpAddress updated successfully');
    }

    /**
     * Remove the specified ErpAddress from storage.
     * DELETE /erpAddresses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ErpAddress $erpAddress */
        $erpAddress = $this->erpAddressRepository->findWithoutFail($id);

        if (empty($erpAddress)) {
            return $this->sendError('Erp Address not found');
        }

        $erpAddress->delete();

        return $this->sendResponse($id, 'Erp Address deleted successfully');
    }
}

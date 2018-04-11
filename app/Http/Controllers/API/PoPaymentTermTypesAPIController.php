<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoPaymentTermTypesAPIRequest;
use App\Http\Requests\API\UpdatePoPaymentTermTypesAPIRequest;
use App\Models\PoPaymentTermTypes;
use App\Repositories\PoPaymentTermTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PoPaymentTermTypesController
 * @package App\Http\Controllers\API
 */

class PoPaymentTermTypesAPIController extends AppBaseController
{
    /** @var  PoPaymentTermTypesRepository */
    private $poPaymentTermTypesRepository;

    public function __construct(PoPaymentTermTypesRepository $poPaymentTermTypesRepo)
    {
        $this->poPaymentTermTypesRepository = $poPaymentTermTypesRepo;
    }

    /**
     * Display a listing of the PoPaymentTermTypes.
     * GET|HEAD /poPaymentTermTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poPaymentTermTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->poPaymentTermTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->all();

        return $this->sendResponse($poPaymentTermTypes->toArray(), 'Po Payment Term Types retrieved successfully');
    }

    /**
     * Store a newly created PoPaymentTermTypes in storage.
     * POST /poPaymentTermTypes
     *
     * @param CreatePoPaymentTermTypesAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePoPaymentTermTypesAPIRequest $request)
    {
        $input = $request->all();

        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->create($input);

        return $this->sendResponse($poPaymentTermTypes->toArray(), 'Po Payment Term Types saved successfully');
    }

    /**
     * Display the specified PoPaymentTermTypes.
     * GET|HEAD /poPaymentTermTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PoPaymentTermTypes $poPaymentTermTypes */
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            return $this->sendError('Po Payment Term Types not found');
        }

        return $this->sendResponse($poPaymentTermTypes->toArray(), 'Po Payment Term Types retrieved successfully');
    }

    /**
     * Update the specified PoPaymentTermTypes in storage.
     * PUT/PATCH /poPaymentTermTypes/{id}
     *
     * @param  int $id
     * @param UpdatePoPaymentTermTypesAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoPaymentTermTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoPaymentTermTypes $poPaymentTermTypes */
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            return $this->sendError('Po Payment Term Types not found');
        }

        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->update($input, $id);

        return $this->sendResponse($poPaymentTermTypes->toArray(), 'PoPaymentTermTypes updated successfully');
    }

    /**
     * Remove the specified PoPaymentTermTypes from storage.
     * DELETE /poPaymentTermTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PoPaymentTermTypes $poPaymentTermTypes */
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            return $this->sendError('Po Payment Term Types not found');
        }

        $poPaymentTermTypes->delete();

        return $this->sendResponse($id, 'Po Payment Term Types deleted successfully');
    }
}

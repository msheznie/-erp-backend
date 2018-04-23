<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxAPIRequest;
use App\Http\Requests\API\UpdateTaxAPIRequest;
use App\Models\Tax;
use App\Repositories\TaxRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxController
 * @package App\Http\Controllers\API
 */

class TaxAPIController extends AppBaseController
{
    /** @var  TaxRepository */
    private $taxRepository;

    public function __construct(TaxRepository $taxRepo)
    {
        $this->taxRepository = $taxRepo;
    }

    /**
     * Display a listing of the Tax.
     * GET|HEAD /taxes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxRepository->pushCriteria(new RequestCriteria($request));
        $this->taxRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxes = $this->taxRepository->all();

        return $this->sendResponse($taxes->toArray(), 'Taxes retrieved successfully');
    }

    /**
     * Store a newly created Tax in storage.
     * POST /taxes
     *
     * @param CreateTaxAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxAPIRequest $request)
    {
        $input = $request->all();

        $taxes = $this->taxRepository->create($input);

        return $this->sendResponse($taxes->toArray(), 'Tax saved successfully');
    }

    /**
     * Display the specified Tax.
     * GET|HEAD /taxes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('Tax not found');
        }

        return $this->sendResponse($tax->toArray(), 'Tax retrieved successfully');
    }

    /**
     * Update the specified Tax in storage.
     * PUT/PATCH /taxes/{id}
     *
     * @param  int $id
     * @param UpdateTaxAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxAPIRequest $request)
    {
        $input = $request->all();

        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('Tax not found');
        }

        $tax = $this->taxRepository->update($input, $id);

        return $this->sendResponse($tax->toArray(), 'Tax updated successfully');
    }

    /**
     * Remove the specified Tax from storage.
     * DELETE /taxes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Tax $tax */
        $tax = $this->taxRepository->findWithoutFail($id);

        if (empty($tax)) {
            return $this->sendError('Tax not found');
        }

        $tax->delete();

        return $this->sendResponse($id, 'Tax deleted successfully');
    }
}

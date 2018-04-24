<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxFormulaDetailAPIRequest;
use App\Http\Requests\API\UpdateTaxFormulaDetailAPIRequest;
use App\Models\TaxFormulaDetail;
use App\Repositories\TaxFormulaDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxFormulaDetailController
 * @package App\Http\Controllers\API
 */

class TaxFormulaDetailAPIController extends AppBaseController
{
    /** @var  TaxFormulaDetailRepository */
    private $taxFormulaDetailRepository;

    public function __construct(TaxFormulaDetailRepository $taxFormulaDetailRepo)
    {
        $this->taxFormulaDetailRepository = $taxFormulaDetailRepo;
    }

    /**
     * Display a listing of the TaxFormulaDetail.
     * GET|HEAD /taxFormulaDetails
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxFormulaDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->taxFormulaDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxFormulaDetails = $this->taxFormulaDetailRepository->all();

        return $this->sendResponse($taxFormulaDetails->toArray(), 'Tax Formula Details retrieved successfully');
    }

    /**
     * Store a newly created TaxFormulaDetail in storage.
     * POST /taxFormulaDetails
     *
     * @param CreateTaxFormulaDetailAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxFormulaDetailAPIRequest $request)
    {
        $input = $request->all();

        $taxFormulaDetails = $this->taxFormulaDetailRepository->create($input);

        return $this->sendResponse($taxFormulaDetails->toArray(), 'Tax Formula Detail saved successfully');
    }

    /**
     * Display the specified TaxFormulaDetail.
     * GET|HEAD /taxFormulaDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var TaxFormulaDetail $taxFormulaDetail */
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            return $this->sendError('Tax Formula Detail not found');
        }

        return $this->sendResponse($taxFormulaDetail->toArray(), 'Tax Formula Detail retrieved successfully');
    }

    /**
     * Update the specified TaxFormulaDetail in storage.
     * PUT/PATCH /taxFormulaDetails/{id}
     *
     * @param  int $id
     * @param UpdateTaxFormulaDetailAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxFormulaDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxFormulaDetail $taxFormulaDetail */
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            return $this->sendError('Tax Formula Detail not found');
        }

        $taxFormulaDetail = $this->taxFormulaDetailRepository->update($input, $id);

        return $this->sendResponse($taxFormulaDetail->toArray(), 'TaxFormulaDetail updated successfully');
    }

    /**
     * Remove the specified TaxFormulaDetail from storage.
     * DELETE /taxFormulaDetails/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var TaxFormulaDetail $taxFormulaDetail */
        $taxFormulaDetail = $this->taxFormulaDetailRepository->findWithoutFail($id);

        if (empty($taxFormulaDetail)) {
            return $this->sendError('Tax Formula Detail not found');
        }

        $taxFormulaDetail->delete();

        return $this->sendResponse($id, 'Tax Formula Detail deleted successfully');
    }
}

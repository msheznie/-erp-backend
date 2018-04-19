<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxAuthorityAPIRequest;
use App\Http\Requests\API\UpdateTaxAuthorityAPIRequest;
use App\Models\TaxAuthority;
use App\Repositories\TaxAuthorityRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxAuthorityController
 * @package App\Http\Controllers\API
 */

class TaxAuthorityAPIController extends AppBaseController
{
    /** @var  TaxAuthorityRepository */
    private $taxAuthorityRepository;

    public function __construct(TaxAuthorityRepository $taxAuthorityRepo)
    {
        $this->taxAuthorityRepository = $taxAuthorityRepo;
    }

    /**
     * Display a listing of the TaxAuthority.
     * GET|HEAD /taxAuthorities
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxAuthorityRepository->pushCriteria(new RequestCriteria($request));
        $this->taxAuthorityRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxAuthorities = $this->taxAuthorityRepository->all();

        return $this->sendResponse($taxAuthorities->toArray(), 'Tax Authorities retrieved successfully');
    }

    /**
     * Store a newly created TaxAuthority in storage.
     * POST /taxAuthorities
     *
     * @param CreateTaxAuthorityAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxAuthorityAPIRequest $request)
    {
        $input = $request->all();

        $taxAuthorities = $this->taxAuthorityRepository->create($input);

        return $this->sendResponse($taxAuthorities->toArray(), 'Tax Authority saved successfully');
    }

    /**
     * Display the specified TaxAuthority.
     * GET|HEAD /taxAuthorities/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var TaxAuthority $taxAuthority */
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            return $this->sendError('Tax Authority not found');
        }

        return $this->sendResponse($taxAuthority->toArray(), 'Tax Authority retrieved successfully');
    }

    /**
     * Update the specified TaxAuthority in storage.
     * PUT/PATCH /taxAuthorities/{id}
     *
     * @param  int $id
     * @param UpdateTaxAuthorityAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxAuthorityAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxAuthority $taxAuthority */
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            return $this->sendError('Tax Authority not found');
        }

        $taxAuthority = $this->taxAuthorityRepository->update($input, $id);

        return $this->sendResponse($taxAuthority->toArray(), 'TaxAuthority updated successfully');
    }

    /**
     * Remove the specified TaxAuthority from storage.
     * DELETE /taxAuthorities/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var TaxAuthority $taxAuthority */
        $taxAuthority = $this->taxAuthorityRepository->findWithoutFail($id);

        if (empty($taxAuthority)) {
            return $this->sendError('Tax Authority not found');
        }

        $taxAuthority->delete();

        return $this->sendResponse($id, 'Tax Authority deleted successfully');
    }
}

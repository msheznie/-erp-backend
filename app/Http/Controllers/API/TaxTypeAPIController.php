<?php
/**
 * =============================================
 * -- File Name : TaxTypeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Tax Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file contains all CRUD for tax type
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxTypeAPIRequest;
use App\Http\Requests\API\UpdateTaxTypeAPIRequest;
use App\Models\TaxType;
use App\Repositories\TaxTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxTypeController
 * @package App\Http\Controllers\API
 */

class TaxTypeAPIController extends AppBaseController
{
    /** @var  TaxTypeRepository */
    private $taxTypeRepository;

    public function __construct(TaxTypeRepository $taxTypeRepo)
    {
        $this->taxTypeRepository = $taxTypeRepo;
    }

    /**
     * Display a listing of the TaxType.
     * GET|HEAD /taxTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->taxTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->taxTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxTypes = $this->taxTypeRepository->all();

        return $this->sendResponse($taxTypes->toArray(), trans('custom.vat_types_retrieved_successfully'));
    }

    /**
     * Store a newly created TaxType in storage.
     * POST /taxTypes
     *
     * @param CreateTaxTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateTaxTypeAPIRequest $request)
    {
        $input = $request->all();

        $taxTypes = $this->taxTypeRepository->create($input);

        return $this->sendResponse($taxTypes->toArray(), trans('custom.vat_type_saved_successfully'));
    }

    /**
     * Display the specified TaxType.
     * GET|HEAD /taxTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var TaxType $taxType */
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            return $this->sendError(trans('custom.vat_type_not_found'));
        }

        return $this->sendResponse($taxType->toArray(), trans('custom.vat_type_retrieved_successfully'));
    }

    /**
     * Update the specified TaxType in storage.
     * PUT/PATCH /taxTypes/{id}
     *
     * @param  int $id
     * @param UpdateTaxTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTaxTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxType $taxType */
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            return $this->sendError(trans('custom.vat_type_not_found'));
        }

        $taxType = $this->taxTypeRepository->update($input, $id);

        return $this->sendResponse($taxType->toArray(), trans('custom.vat_type_updated_successfully'));
    }

    /**
     * Remove the specified TaxType from storage.
     * DELETE /taxTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var TaxType $taxType */
        $taxType = $this->taxTypeRepository->findWithoutFail($id);

        if (empty($taxType)) {
            return $this->sendError(trans('custom.vat_type_not_found'));
        }

        $taxType->delete();

        return $this->sendResponse($id, trans('custom.vat_type_deleted_successfully'));
    }
}

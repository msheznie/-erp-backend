<?php
/**
 * =============================================
 * -- File Name : PoPaymentTermTypesAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Payment Term Types
 * -- Author : Mohamed Nazir
 * -- Create date : 02 - April 2018
 * -- Description : This file contains the all CRUD for Po Payment Term Types
 * -- REVISION HISTORY
 **/
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
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->with('translations')->all();

        return $this->sendResponse($poPaymentTermTypes->toArray(), trans('custom.po_payment_term_types_retrieved_successfully'));
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

        return $this->sendResponse($poPaymentTermTypes->toArray(), trans('custom.po_payment_term_types_saved_successfully'));
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
        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->with('translations')->findWithoutFail($id);

        if (empty($poPaymentTermTypes)) {
            return $this->sendError(trans('custom.po_payment_term_types_not_found'));
        }

        return $this->sendResponse($poPaymentTermTypes->toArray(), trans('custom.po_payment_term_types_retrieved_successfully'));
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
            return $this->sendError(trans('custom.po_payment_term_types_not_found'));
        }

        $poPaymentTermTypes = $this->poPaymentTermTypesRepository->update($input, $id);

        return $this->sendResponse($poPaymentTermTypes->toArray(), trans('custom.popaymenttermtypes_updated_successfully'));
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
            return $this->sendError(trans('custom.po_payment_term_types_not_found'));
        }

        $poPaymentTermTypes->delete();

        return $this->sendResponse($id, trans('custom.po_payment_term_types_deleted_successfully'));
    }
}

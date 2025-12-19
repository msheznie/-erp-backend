<?php
/**
=============================================
-- File Name : SupplierTypeAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Type
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Supplier Type
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierTypeAPIRequest;
use App\Http\Requests\API\UpdateSupplierTypeAPIRequest;
use App\Models\SupplierType;
use App\Repositories\SupplierTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierTypeController
 * @package App\Http\Controllers\API
 */

class SupplierTypeAPIController extends AppBaseController
{
    /** @var  SupplierTypeRepository */
    private $supplierTypeRepository;

    public function __construct(SupplierTypeRepository $supplierTypeRepo)
    {
        $this->supplierTypeRepository = $supplierTypeRepo;
    }

    /**
     * Display a listing of the SupplierType.
     * GET|HEAD /supplierTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierTypes = $this->supplierTypeRepository->all();

        return $this->sendResponse($supplierTypes->toArray(), trans('custom.supplier_types_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierType in storage.
     * POST /supplierTypes
     *
     * @param CreateSupplierTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierTypeAPIRequest $request)
    {
        $input = $request->all();

        $supplierTypes = $this->supplierTypeRepository->create($input);

        return $this->sendResponse($supplierTypes->toArray(), trans('custom.supplier_type_saved_successfully'));
    }

    /**
     * Display the specified SupplierType.
     * GET|HEAD /supplierTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierType $supplierType */
        $supplierType = $this->supplierTypeRepository->findWithoutFail($id);

        if (empty($supplierType)) {
            return $this->sendError(trans('custom.supplier_type_not_found'));
        }

        return $this->sendResponse($supplierType->toArray(), trans('custom.supplier_type_retrieved_successfully'));
    }

    /**
     * Update the specified SupplierType in storage.
     * PUT/PATCH /supplierTypes/{id}
     *
     * @param  int $id
     * @param UpdateSupplierTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierType $supplierType */
        $supplierType = $this->supplierTypeRepository->findWithoutFail($id);

        if (empty($supplierType)) {
            return $this->sendError(trans('custom.supplier_type_not_found'));
        }

        $supplierType = $this->supplierTypeRepository->update($input, $id);

        return $this->sendResponse($supplierType->toArray(), trans('custom.suppliertype_updated_successfully'));
    }

    /**
     * Remove the specified SupplierType from storage.
     * DELETE /supplierTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierType $supplierType */
        $supplierType = $this->supplierTypeRepository->findWithoutFail($id);

        if (empty($supplierType)) {
            return $this->sendError(trans('custom.supplier_type_not_found'));
        }

        $supplierType->delete();

        return $this->sendResponse($id, trans('custom.supplier_type_deleted_successfully'));
    }
}

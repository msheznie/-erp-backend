<?php

namespace App\Http\Controllers\API;
/**
=============================================
-- File Name : SupplierImportanceAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Importance
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for  Supplier Importance
-- REVISION HISTORY
 */
use App\Http\Requests\API\CreateSupplierImportanceAPIRequest;
use App\Http\Requests\API\UpdateSupplierImportanceAPIRequest;
use App\Models\SupplierImportance;
use App\Repositories\SupplierImportanceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierImportanceController
 * @package App\Http\Controllers\API
 */

class SupplierImportanceAPIController extends AppBaseController
{
    /** @var  SupplierImportanceRepository */
    private $supplierImportanceRepository;

    public function __construct(SupplierImportanceRepository $supplierImportanceRepo)
    {
        $this->supplierImportanceRepository = $supplierImportanceRepo;
    }

    /**
     * Display a listing of the SupplierImportance.
     * GET|HEAD /supplierImportances
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierImportanceRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierImportanceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierImportances = $this->supplierImportanceRepository->all();

        return $this->sendResponse($supplierImportances->toArray(), trans('custom.supplier_importances_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierImportance in storage.
     * POST /supplierImportances
     *
     * @param CreateSupplierImportanceAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierImportanceAPIRequest $request)
    {
        $input = $request->all();

        $supplierImportances = $this->supplierImportanceRepository->create($input);

        return $this->sendResponse($supplierImportances->toArray(), trans('custom.supplier_importance_saved_successfully'));
    }

    /**
     * Display the specified SupplierImportance.
     * GET|HEAD /supplierImportances/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierImportance $supplierImportance */
        $supplierImportance = $this->supplierImportanceRepository->findWithoutFail($id);

        if (empty($supplierImportance)) {
            return $this->sendError(trans('custom.supplier_importance_not_found'));
        }

        return $this->sendResponse($supplierImportance->toArray(), trans('custom.supplier_importance_retrieved_successfully'));
    }

    /**
     * Update the specified SupplierImportance in storage.
     * PUT/PATCH /supplierImportances/{id}
     *
     * @param  int $id
     * @param UpdateSupplierImportanceAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierImportanceAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierImportance $supplierImportance */
        $supplierImportance = $this->supplierImportanceRepository->findWithoutFail($id);

        if (empty($supplierImportance)) {
            return $this->sendError(trans('custom.supplier_importance_not_found'));
        }

        $supplierImportance = $this->supplierImportanceRepository->update($input, $id);

        return $this->sendResponse($supplierImportance->toArray(), trans('custom.supplierimportance_updated_successfully'));
    }

    /**
     * Remove the specified SupplierImportance from storage.
     * DELETE /supplierImportances/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierImportance $supplierImportance */
        $supplierImportance = $this->supplierImportanceRepository->findWithoutFail($id);

        if (empty($supplierImportance)) {
            return $this->sendError(trans('custom.supplier_importance_not_found'));
        }

        $supplierImportance->delete();

        return $this->sendResponse($id, trans('custom.supplier_importance_deleted_successfully'));
    }
}

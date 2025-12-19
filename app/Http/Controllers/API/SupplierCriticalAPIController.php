<?php

namespace App\Http\Controllers\API;
/**
=============================================
-- File Name : SupplierCriticalAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Critical
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for  Supplier Critical
-- REVISION HISTORY
 */
use App\Http\Requests\API\CreateSupplierCriticalAPIRequest;
use App\Http\Requests\API\UpdateSupplierCriticalAPIRequest;
use App\Models\SupplierCritical;
use App\Repositories\SupplierCriticalRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCriticalController
 * @package App\Http\Controllers\API
 */

class SupplierCriticalAPIController extends AppBaseController
{
    /** @var  SupplierCriticalRepository */
    private $supplierCriticalRepository;

    public function __construct(SupplierCriticalRepository $supplierCriticalRepo)
    {
        $this->supplierCriticalRepository = $supplierCriticalRepo;
    }

    /**
     * Display a listing of the SupplierCritical.
     * GET|HEAD /supplierCriticals
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCriticalRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCriticalRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCriticals = $this->supplierCriticalRepository->all();

        return $this->sendResponse($supplierCriticals->toArray(), trans('custom.supplier_criticals_retrieved_successfully'));
    }

    /**
     * Store a newly created SupplierCritical in storage.
     * POST /supplierCriticals
     *
     * @param CreateSupplierCriticalAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCriticalAPIRequest $request)
    {
        $input = $request->all();

        $supplierCriticals = $this->supplierCriticalRepository->create($input);

        return $this->sendResponse($supplierCriticals->toArray(), trans('custom.supplier_critical_saved_successfully'));
    }

    /**
     * Display the specified SupplierCritical.
     * GET|HEAD /supplierCriticals/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierCritical $supplierCritical */
        $supplierCritical = $this->supplierCriticalRepository->findWithoutFail($id);

        if (empty($supplierCritical)) {
            return $this->sendError(trans('custom.supplier_critical_not_found'));
        }

        return $this->sendResponse($supplierCritical->toArray(), trans('custom.supplier_critical_retrieved_successfully'));
    }

    /**
     * Update the specified SupplierCritical in storage.
     * PUT/PATCH /supplierCriticals/{id}
     *
     * @param  int $id
     * @param UpdateSupplierCriticalAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCriticalAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCritical $supplierCritical */
        $supplierCritical = $this->supplierCriticalRepository->findWithoutFail($id);

        if (empty($supplierCritical)) {
            return $this->sendError(trans('custom.supplier_critical_not_found'));
        }

        $supplierCritical = $this->supplierCriticalRepository->update($input, $id);

        return $this->sendResponse($supplierCritical->toArray(), trans('custom.suppliercritical_updated_successfully'));
    }

    /**
     * Remove the specified SupplierCritical from storage.
     * DELETE /supplierCriticals/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierCritical $supplierCritical */
        $supplierCritical = $this->supplierCriticalRepository->findWithoutFail($id);

        if (empty($supplierCritical)) {
            return $this->sendError(trans('custom.supplier_critical_not_found'));
        }

        $supplierCritical->delete();

        return $this->sendResponse($id, trans('custom.supplier_critical_deleted_successfully'));
    }
}

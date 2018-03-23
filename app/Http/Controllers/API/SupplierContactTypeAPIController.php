<?php

namespace App\Http\Controllers\API;
/**
=============================================
-- File Name : SupplierContactTypeAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Contact Type
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for  Supplier Contact Type
-- REVISION HISTORY
 */
use App\Http\Requests\API\CreateSupplierContactTypeAPIRequest;
use App\Http\Requests\API\UpdateSupplierContactTypeAPIRequest;
use App\Models\SupplierContactType;
use App\Repositories\SupplierContactTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierContactTypeController
 * @package App\Http\Controllers\API
 */

class SupplierContactTypeAPIController extends AppBaseController
{
    /** @var  SupplierContactTypeRepository */
    private $supplierContactTypeRepository;

    public function __construct(SupplierContactTypeRepository $supplierContactTypeRepo)
    {
        $this->supplierContactTypeRepository = $supplierContactTypeRepo;
    }

    /**
     * Display a listing of the SupplierContactType.
     * GET|HEAD /supplierContactTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierContactTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierContactTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierContactTypes = $this->supplierContactTypeRepository->all();

        return $this->sendResponse($supplierContactTypes->toArray(), 'Supplier Contact Types retrieved successfully');
    }

    /**
     * Store a newly created SupplierContactType in storage.
     * POST /supplierContactTypes
     *
     * @param CreateSupplierContactTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierContactTypeAPIRequest $request)
    {
        $input = $request->all();

        $supplierContactTypes = $this->supplierContactTypeRepository->create($input);

        return $this->sendResponse($supplierContactTypes->toArray(), 'Supplier Contact Type saved successfully');
    }

    /**
     * Display the specified SupplierContactType.
     * GET|HEAD /supplierContactTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierContactType $supplierContactType */
        $supplierContactType = $this->supplierContactTypeRepository->findWithoutFail($id);

        if (empty($supplierContactType)) {
            return $this->sendError('Supplier Contact Type not found');
        }

        return $this->sendResponse($supplierContactType->toArray(), 'Supplier Contact Type retrieved successfully');
    }

    /**
     * Update the specified SupplierContactType in storage.
     * PUT/PATCH /supplierContactTypes/{id}
     *
     * @param  int $id
     * @param UpdateSupplierContactTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierContactTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierContactType $supplierContactType */
        $supplierContactType = $this->supplierContactTypeRepository->findWithoutFail($id);

        if (empty($supplierContactType)) {
            return $this->sendError('Supplier Contact Type not found');
        }

        $supplierContactType = $this->supplierContactTypeRepository->update($input, $id);

        return $this->sendResponse($supplierContactType->toArray(), 'SupplierContactType updated successfully');
    }

    /**
     * Remove the specified SupplierContactType from storage.
     * DELETE /supplierContactTypes/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierContactType $supplierContactType */
        $supplierContactType = $this->supplierContactTypeRepository->findWithoutFail($id);

        if (empty($supplierContactType)) {
            return $this->sendError('Supplier Contact Type not found');
        }

        $supplierContactType->delete();

        return $this->sendResponse($id, 'Supplier Contact Type deleted successfully');
    }
}

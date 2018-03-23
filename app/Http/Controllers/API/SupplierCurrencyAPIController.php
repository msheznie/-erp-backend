<?php

namespace App\Http\Controllers\API;
/**
=============================================
-- File Name : SupplierCurrencyAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Currency
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for  Supplier Currency
-- REVISION HISTORY
 */
use App\Http\Requests\API\CreateSupplierCurrencyAPIRequest;
use App\Http\Requests\API\UpdateSupplierCurrencyAPIRequest;
use App\Models\SupplierCurrency;
use App\Repositories\SupplierCurrencyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierCurrencyController
 * @package App\Http\Controllers\API
 */

class SupplierCurrencyAPIController extends AppBaseController
{
    /** @var  SupplierCurrencyRepository */
    private $supplierCurrencyRepository;

    public function __construct(SupplierCurrencyRepository $supplierCurrencyRepo)
    {
        $this->supplierCurrencyRepository = $supplierCurrencyRepo;
    }

    /**
     * Display a listing of the SupplierCurrency.
     * GET|HEAD /supplierCurrencies
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->supplierCurrencyRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierCurrencyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierCurrencies = $this->supplierCurrencyRepository->all();

        return $this->sendResponse($supplierCurrencies->toArray(), 'Supplier Currencies retrieved successfully');
    }

    /**
     * Store a newly created SupplierCurrency in storage.
     * POST /supplierCurrencies
     *
     * @param CreateSupplierCurrencyAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierCurrencyAPIRequest $request)
    {
        $input = $request->all();

        $supplierCurrencies = $this->supplierCurrencyRepository->create($input);

        return $this->sendResponse($supplierCurrencies->toArray(), 'Supplier Currency saved successfully');
    }

    /**
     * Display the specified SupplierCurrency.
     * GET|HEAD /supplierCurrencies/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var SupplierCurrency $supplierCurrency */
        $supplierCurrency = $this->supplierCurrencyRepository->findWithoutFail($id);

        if (empty($supplierCurrency)) {
            return $this->sendError('Supplier Currency not found');
        }

        return $this->sendResponse($supplierCurrency->toArray(), 'Supplier Currency retrieved successfully');
    }

    /**
     * Update the specified SupplierCurrency in storage.
     * PUT/PATCH /supplierCurrencies/{id}
     *
     * @param  int $id
     * @param UpdateSupplierCurrencyAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierCurrencyAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierCurrency $supplierCurrency */
        $supplierCurrency = $this->supplierCurrencyRepository->findWithoutFail($id);

        if (empty($supplierCurrency)) {
            return $this->sendError('Supplier Currency not found');
        }

        $supplierCurrency = $this->supplierCurrencyRepository->update($input, $id);

        return $this->sendResponse($supplierCurrency->toArray(), 'SupplierCurrency updated successfully');
    }

    /**
     * Remove the specified SupplierCurrency from storage.
     * DELETE /supplierCurrencies/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var SupplierCurrency $supplierCurrency */
        $supplierCurrency = $this->supplierCurrencyRepository->findWithoutFail($id);

        if (empty($supplierCurrency)) {
            return $this->sendError('Supplier Currency not found');
        }

        $supplierCurrency->delete();

        return $this->sendResponse($id, 'Supplier Currency deleted successfully');
    }
}

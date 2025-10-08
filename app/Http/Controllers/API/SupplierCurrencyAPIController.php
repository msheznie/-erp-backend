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
use App\Models\CurrencyMaster;
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

        return $this->sendResponse($supplierCurrencies->toArray(), trans('custom.supplier_currencies_retrieved_successfully'));
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

        return $this->sendResponse($supplierCurrencies->toArray(), trans('custom.supplier_currency_saved_successfully'));
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
            return $this->sendError(trans('custom.supplier_currency_not_found'));
        }

        return $this->sendResponse($supplierCurrency->toArray(), trans('custom.supplier_currency_retrieved_successfully'));
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
            return $this->sendError(trans('custom.supplier_currency_not_found'));
        }

        $supplierCurrency = $this->supplierCurrencyRepository->update($input, $id);

        return $this->sendResponse($supplierCurrency->toArray(), trans('custom.suppliercurrency_updated_successfully'));
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
            return $this->sendError(trans('custom.supplier_currency_not_found'));
        }

        $supplierCurrency->delete();

        return $this->sendResponse($id, trans('custom.supplier_currency_deleted_successfully'));
    }

    public function getCurrencyDetails(Request $request)
    {
        $input = $request->all();

        $resData = [
            'localCurrency' => isset($input['localCurrencyID']) ? CurrencyMaster::find($input['localCurrencyID']) : null,
            'reportingCurrency' => isset($input['reportingCurrencyID']) ? CurrencyMaster::find($input['reportingCurrencyID']) : null
        ];

        return $this->sendResponse($resData, trans('custom.currency_details_retrieved_successfully'));
    }
}

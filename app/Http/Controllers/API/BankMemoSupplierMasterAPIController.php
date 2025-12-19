<?php
/**
=============================================
-- File Name : BankMemoSupplierMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Bank Memo Supplier Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for supplier bank memo master.
--REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankMemoSupplierMasterAPIRequest;
use App\Http\Requests\API\UpdateBankMemoSupplierMasterAPIRequest;
use App\Models\BankMemoSupplierMaster;
use App\Repositories\BankMemoSupplierMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankMemoSupplierMasterController
 * @package App\Http\Controllers\API
 */

class BankMemoSupplierMasterAPIController extends AppBaseController
{
    /** @var  BankMemoSupplierMasterRepository */
    private $bankMemoSupplierMasterRepository;

    public function __construct(BankMemoSupplierMasterRepository $bankMemoSupplierMasterRepo)
    {
        $this->bankMemoSupplierMasterRepository = $bankMemoSupplierMasterRepo;
    }

    /**
     * Display a listing of the BankMemoSupplierMaster.
     * GET|HEAD /bankMemoSupplierMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankMemoSupplierMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bankMemoSupplierMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankMemoSupplierMasters = $this->bankMemoSupplierMasterRepository->all();

        return $this->sendResponse($bankMemoSupplierMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
    }

    /**
     * Store a newly created BankMemoSupplierMaster in storage.
     * POST /bankMemoSupplierMasters
     *
     * @param CreateBankMemoSupplierMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankMemoSupplierMasterAPIRequest $request)
    {
        $input = $request->all();

        $bankMemoSupplierMasters = $this->bankMemoSupplierMasterRepository->create($input);

        return $this->sendResponse($bankMemoSupplierMasters->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
    }

    /**
     * Display the specified BankMemoSupplierMaster.
     * GET|HEAD /bankMemoSupplierMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankMemoSupplierMaster $bankMemoSupplierMaster */
        $bankMemoSupplierMaster = $this->bankMemoSupplierMasterRepository->findWithoutFail($id);

        if (empty($bankMemoSupplierMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
        }

        return $this->sendResponse($bankMemoSupplierMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
    }

    /**
     * Update the specified BankMemoSupplierMaster in storage.
     * PUT/PATCH /bankMemoSupplierMasters/{id}
     *
     * @param  int $id
     * @param UpdateBankMemoSupplierMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankMemoSupplierMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankMemoSupplierMaster $bankMemoSupplierMaster */
        $bankMemoSupplierMaster = $this->bankMemoSupplierMasterRepository->findWithoutFail($id);

        if (empty($bankMemoSupplierMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
        }

        $bankMemoSupplierMaster = $this->bankMemoSupplierMasterRepository->update($input, $id);

        return $this->sendResponse($bankMemoSupplierMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
    }

    /**
     * Remove the specified BankMemoSupplierMaster from storage.
     * DELETE /bankMemoSupplierMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BankMemoSupplierMaster $bankMemoSupplierMaster */
        $bankMemoSupplierMaster = $this->bankMemoSupplierMasterRepository->findWithoutFail($id);

        if (empty($bankMemoSupplierMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
        }

        $bankMemoSupplierMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_memo_supplier_masters')]));
    }
}

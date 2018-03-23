<?php

/**
  =============================================
-- File Name : BankMemoSupplierAPIController.php
-- Project Name : ERP
-- Module Name :  Supplier Bank Memo
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for supplier bank memo.
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as getBankMemoBySupplierCurrency(),deleteBankMemo()
*/

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankMemoSupplierAPIRequest;
use App\Http\Requests\API\UpdateBankMemoSupplierAPIRequest;
use App\Models\BankMemoSupplier;
use App\Repositories\BankMemoSupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Response;

/**
 * Class BankMemoSupplierController
 * @package App\Http\Controllers\API
 */

class BankMemoSupplierAPIController extends AppBaseController
{
    /** @var  BankMemoSupplierRepository */
    private $bankMemoSupplierRepository;
    private $userRepository;
    public function __construct(BankMemoSupplierRepository $bankMemoSupplierRepo,UserRepository $userRepo)
    {
        $this->bankMemoSupplierRepository = $bankMemoSupplierRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the BankMemoSupplier.
     * GET|HEAD /bankMemoSuppliers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankMemoSupplierRepository->pushCriteria(new RequestCriteria($request));
        $this->bankMemoSupplierRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankMemoSuppliers = $this->bankMemoSupplierRepository->all();

        return $this->sendResponse($bankMemoSuppliers->toArray(), 'Bank Memo Suppliers retrieved successfully');
    }

    /**
     * get all bank memo by supplier currency.
     * GET /getBankMemoBySupplierCurrency
     *
     * @param Request $request
     *
     * @return Response
     */

     public function getBankMemoBySupplierCurrency(Request $request){

         $bankMemoSuppliers = BankMemoSupplier::where("supplierCurrencyID",$request['supplierCurrencyID'])
                                               ->where("supplierCodeSystem",$request['supplierCodeSystem'])
                                               ->get();

         return $this->sendResponse($bankMemoSuppliers->toArray(), 'Bank Memo Suppliers retrieved successfully');
     }

    /**
     * Store a newly created BankMemoSupplier in storage.
     * POST /bankMemoSuppliers
     *
     * @param CreateBankMemoSupplierAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankMemoSupplierAPIRequest $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        $input['updatedByUserID'] = $empId;
        $input['updatedByUserName'] = $empName;

        if( array_key_exists ('bankMemoID' , $input )){
            $bankMemoSuppliers = $this->bankMemoSupplierRepository->update($input,$input['bankMemoID']);
        }else{
            $bankMemoSuppliers = $this->bankMemoSupplierRepository->create($input);
        }

        return $this->sendResponse($bankMemoSuppliers->toArray(), 'Bank Memo Supplier saved successfully');
    }

    /**
     * Display the specified BankMemoSupplier.
     * GET|HEAD /bankMemoSuppliers/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankMemoSupplier $bankMemoSupplier */
        $bankMemoSupplier = $this->bankMemoSupplierRepository->findWithoutFail($id);

        if (empty($bankMemoSupplier)) {
            return $this->sendError('Bank Memo Supplier not found');
        }

        return $this->sendResponse($bankMemoSupplier->toArray(), 'Bank Memo Supplier retrieved successfully');
    }

    /**
     * Update the specified BankMemoSupplier in storage.
     * PUT/PATCH /bankMemoSuppliers/{id}
     *
     * @param  int $id
     * @param UpdateBankMemoSupplierAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankMemoSupplierAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankMemoSupplier $bankMemoSupplier */
        $bankMemoSupplier = $this->bankMemoSupplierRepository->findWithoutFail($id);

        if (empty($bankMemoSupplier)) {
            return $this->sendError('Bank Memo Supplier not found');
        }

        $bankMemoSupplier = $this->bankMemoSupplierRepository->update($input, $id);

        return $this->sendResponse($bankMemoSupplier->toArray(), 'BankMemoSupplier updated successfully');
    }

    /**
     * Remove the specified BankMemoSupplier from storage.
     * POST /deleteBankMemo
     *
     * @param  int bankMemoID
     *
     * @return Response
     */
    public function deleteBankMemo(Request $request)
    {

        /** @var BankMemoSupplier $bankMemoSupplier */
        $bankMemoSupplier =  BankMemoSupplier::where('bankMemoID',$request['bankMemoID'])->first();

        if (empty($bankMemoSupplier)) {
            return $this->sendError('Bank Memo Supplier not found');
        }

        $bankMemoSupplier->delete();

        return $this->sendResponse($request['bankMemoID'], 'Bank Memo Supplier deleted successfully');
    }

    /**
     * Remove the specified BankMemoSupplier from storage.
     * DELETE /bankMemoSuppliers/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {

        return $id;
        /** @var BankMemoSupplier $bankMemoSupplier */
        $bankMemoSupplier = $this->bankMemoSupplierRepository->findWithoutFail($id);

        if (empty($bankMemoSupplier)) {
            return $this->sendError('Bank Memo Supplier not found');
        }

        $bankMemoSupplier->delete();

        return $this->sendResponse($id, 'Bank Memo Supplier deleted successfully');
    }
}

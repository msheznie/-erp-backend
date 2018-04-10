<?php
/**
=============================================
-- File Name : CurrencyMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Currency Master
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Currency Master
-- REVISION HISTORY
-- Date: 14-March 2018 By: Fayas Description: Added new functions named as getAllCurrencies(),getCurrenciesBySupplier(),
   addCurrencyToSupplier(),updateCurrencyToSupplier()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCurrencyMasterAPIRequest;
use App\Http\Requests\API\UpdateCurrencyMasterAPIRequest;
use App\Models\BankMemoSupplier;
use App\Models\BankMemoSupplierMaster;
use App\Models\CurrencyMaster;
use App\Repositories\CurrencyMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\SupplierMaster;
use App\Models\SupplierCurrency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;

/**
 * Class CurrencyMasterController
 * @package App\Http\Controllers\API
 */

class CurrencyMasterAPIController extends AppBaseController
{
    /** @var  CurrencyMasterRepository */
    private $currencyMasterRepository;
    private $userRepository;
    public function __construct(CurrencyMasterRepository $currencyMasterRepo,UserRepository $userRepo)
    {
        $this->currencyMasterRepository = $currencyMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the CurrencyMaster.
     * GET|HEAD /currencyMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->currencyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyMasters = $this->currencyMasterRepository->all();

        return $this->sendResponse($currencyMasters->toArray(), 'Currency Masters retrieved successfully');
    }

    /**
     * Display a listing of the CurrencyMaster.
     * GET|HEAD /getAllCurrencies
     *
     * @param Request $request
     * @return Response
     */

    public function getAllCurrencies(Request $request){

        $this->currencyMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyMasterRepository = $this->currencyMasterRepository->all();

        return $this->sendResponse($currencyMasterRepository->toArray(), 'Country Masters retrieved successfully');
    }

    /**
     * Display a listing of assigned Currency for supplier
     * GET|HEAD /getCurrenciesBySupplier
     *
     * @param Request $request
     * @return Response
     */
    public function getCurrenciesBySupplier(Request $request){

        $supplierId = $request['supplierId'];
        $supplier = SupplierMaster::where('supplierCodeSystem','=',$supplierId)->first();

        if($supplier){
            //$supplierCurrencies = SupplierCurrency::where('supplierCodeSystem','=',$supplierId)->get();
            $supplierCurrencies = DB::table('suppliercurrency')
                ->leftJoin('currencymaster', 'suppliercurrency.currencyID', '=', 'currencymaster.currencyID')
                ->where('supplierCodeSystem','=',$supplierId)
                ->orderBy('supplierCurrencyID', 'DESC')
                ->get();
        }else{
            $supplierCurrencies = [];
        }

        return $this->sendResponse($supplierCurrencies, 'Supplier Currencies retrieved successfully');
    }

    /**
     * Store a newly created CurrencyMaster in storage.
     * POST /addCurrencyToSupplier
     *
     * @param Request $request
     *
     * @return Response
     */

    public function addCurrencyToSupplier (Request $request){

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        $supplierCurrency = new SupplierCurrency();
        $supplierCurrency->supplierCodeSystem = $request['supplierId'];
        $supplierCurrency->currencyID = $request['currencyId'];
        $supplierCurrency->isAssigned = 1;
        $supplierCurrency->isDefault  = 0;
        $supplierCurrency->save();

        $supplier = SupplierMaster::where('supplierCodeSystem',$request['supplierId'])->first();


         $companyDefaultBankMemos = BankMemoSupplierMaster::where('companySystemID',$supplier->primaryCompanySystemID)->get();

        foreach ($companyDefaultBankMemos as $value){
            $temBankMemo = new BankMemoSupplier();
            $temBankMemo->memoHeader = $value['memoHeader'];
            $temBankMemo->memoDetail = $value['memoDetail'];
            $temBankMemo->supplierCodeSystem = $supplier->supplierCodeSystem;
            $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
            $temBankMemo->updatedByUserID = $empId;
            $temBankMemo->updatedByUserName = $empName;
            $temBankMemo->save();
        }

        return $this->sendResponse($supplierCurrency, 'Supplier Currencies added successfully');
    }

    /**
     * Update Supplier currency assign.
     * Post /updateCurrencyToSupplier
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function updateCurrencyToSupplier (Request $request){

        $supplierCurrency = SupplierCurrency::where('supplierCurrencyID',$request['supplierCurrencyID'])->first();

        if($supplierCurrency){
            if($request['isDefault'] == true){
                $supplierCurrencies = SupplierCurrency::where('supplierCodeSystem',$request['supplierCodeSystem'])->get();
                foreach ($supplierCurrencies as $sc){
                    $tem_sc = SupplierCurrency::where('supplierCurrencyID',$sc['supplierCurrencyID'])->first();
                    $tem_sc->isDefault  = 0;
                    $tem_sc->save();
                }
            }

            if($request['isDefault'] == true || $request['isDefault'] == 1){
                $request['isDefault'] = -1;
            }

            $supplierCurrency->isDefault  = $request['isDefault'];
            $supplierCurrency->isAssigned  = $request['isAssigned'];
            $supplierCurrency->save();
        }
        return $this->sendResponse($supplierCurrency, 'Supplier Currencies updated successfully');
    }

    /**
     * Store a newly created CurrencyMaster in storage.
     * POST /currencyMasters
     *
     * @param CreateCurrencyMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCurrencyMasterAPIRequest $request)
    {
        $input = $request->all();

        $currencyMasters = $this->currencyMasterRepository->create($input);

        return $this->sendResponse($currencyMasters->toArray(), 'Currency Master saved successfully');
    }

    /**
     * Display the specified CurrencyMaster.
     * GET|HEAD /currencyMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CurrencyMaster $currencyMaster */
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);

        if (empty($currencyMaster)) {
            return $this->sendError('Currency Master not found');
        }

        return $this->sendResponse($currencyMaster->toArray(), 'Currency Master retrieved successfully');
    }

    /**
     * Update the specified CurrencyMaster in storage.
     * PUT/PATCH /currencyMasters/{id}
     *
     * @param  int $id
     * @param UpdateCurrencyMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCurrencyMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyMaster $currencyMaster */
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);

        if (empty($currencyMaster)) {
            return $this->sendError('Currency Master not found');
        }

        $currencyMaster = $this->currencyMasterRepository->update($input, $id);

        return $this->sendResponse($currencyMaster->toArray(), 'CurrencyMaster updated successfully');
    }

    /**
     * Remove the specified CurrencyMaster from storage.
     * DELETE /currencyMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CurrencyMaster $currencyMaster */
        $currencyMaster = $this->currencyMasterRepository->findWithoutFail($id);

        if (empty($currencyMaster)) {
            return $this->sendError('Currency Master not found');
        }

        $currencyMaster->delete();

        return $this->sendResponse($id, 'Currency Master deleted successfully');
    }
}

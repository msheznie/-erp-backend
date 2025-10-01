<?php

/**
 * =============================================
 * -- File Name : BankMemoSupplierAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Bank Memo
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for supplier bank memo.
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getBankMemoBySupplierCurrency(),deleteBankMemo(),supplierBankMemoDeleteAll()
 * -- Date: 30-October 2018 By: Fayas Description: Added new functions named as addBulkMemos(),exportSupplierCurrencyMemos()
 * -- Date: 09-November 2018 By: Fayas Description: Added new functions named as getBankMemoBySupplierCurrencyId()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankMemoSupplierAPIRequest;
use App\Http\Requests\API\UpdateBankMemoSupplierAPIRequest;
use App\Models\BankMemoSupplier;
use App\Models\BankMemoTypes;
use App\Models\SupplierCurrency;
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

    public function __construct(BankMemoSupplierRepository $bankMemoSupplierRepo, UserRepository $userRepo)
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

        return $this->sendResponse($bankMemoSuppliers->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_suppliers')]));
    }

    /**
     * get all bank memo by supplier currency.
     * GET /getBankMemoBySupplierCurrency
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getBankMemoBySupplierCurrency(Request $request)
    {

        $count = BankMemoSupplier::where("supplierCurrencyID", $request['supplierCurrencyID'])
            ->where("supplierCodeSystem", $request['supplierCodeSystem'])
            ->count();

        $bankMemoSuppliers = BankMemoSupplier::where("supplierCurrencyID", $request['supplierCurrencyID'])
                                                ->where("supplierCodeSystem", $request['supplierCodeSystem'])
                                                ->orderBySort()
                                                ->get();

        $data = array('bankMemos' => $bankMemoSuppliers->toArray(), 'count' => $count);

        return $this->sendResponse($data, trans('custom.bank_memo_suppliers_retrieved_successfully'));
    }

    public function getBankMemoBySupplierCurrencyId(Request $request)
    {
        $supplierCurrencyID = -1;
        $supplierCurrency = SupplierCurrency::where('currencyID',$request['supplierCurrencyID'])
                                                ->where('supplierCodeSystem',$request['supplierCodeSystem'])
                                                    ->first();

        if(!empty($supplierCurrency)){
            $supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
        }

        $bankMemoSuppliers = BankMemoSupplier::where("supplierCurrencyID", $supplierCurrencyID)
                                                ->where("supplierCodeSystem", $request['supplierCodeSystem'])
                                                ->orderBySort()
                                                ->get();

        return $this->sendResponse($bankMemoSuppliers, trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_suppliers')]));
    }

    public function exportSupplierCurrencyMemos(Request $request)
    {

        $bankMemoSuppliers = BankMemoSupplier::where("supplierCurrencyID", $request['supplierCurrencyID'])
            ->where("supplierCodeSystem", $request['supplierCodeSystem'])
            ->get();
        $type = $request->get('type');
        if ($bankMemoSuppliers) {
            $x = 0;
            foreach ($bankMemoSuppliers as $val) {
                $data[$x]['Header Text'] = $val->memoHeader;
                $data[$x]['Detail Text'] = $val->memoDetail;
                $x++;
            }
        } else {
            $data = array();
        }

         \Excel::create('supplier_currency_memos', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                
                // Set right-to-left for Arabic locale
                if (app()->getLocale() == 'ar') {
                    $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $sheet->setRightToLeft(true);
                }
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse($data, trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_suppliers')]));
    }


    public function addBulkMemos(Request $request)
    {


        $companyDefaultBankMemos = $request->get('memos');
        $createdArray = array();

        $employee = \Helper::getEmployeeInfo();
        foreach ($companyDefaultBankMemos as $value) {
            if($value['isChecked']){
                $temBankMemo = new BankMemoSupplier();
                $temBankMemo->memoHeader = $value['bankMemoHeader'];
                $temBankMemo->bankMemoTypeID = $value['bankMemoTypeID'];
                $temBankMemo->memoDetail = '';
                $temBankMemo->supplierCodeSystem = $request['supplierCodeSystem'];
                $temBankMemo->supplierCurrencyID = $request['supplierCurrencyID'];
                $temBankMemo->updatedByUserID = $employee->empID;
                $temBankMemo->updatedByUserName = $employee->empName;
                $temBankMemo->save();
                array_push($createdArray,$temBankMemo);
            }
        }

        return $this->sendResponse($createdArray, trans('custom.save', ['attribute' => trans('custom.bank_memo_suppliers')]));
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
        $input['timestamp'] = now();

        if (array_key_exists('bankMemoID', $input)) {
            $bankMemoSuppliers = $this->bankMemoSupplierRepository->update($input, $input['bankMemoID']);
        } else {
            $bankMemoSuppliers = $this->bankMemoSupplierRepository->create($input);
        }

        return $this->sendResponse($bankMemoSuppliers->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_memo_suppliers')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_suppliers')]));
        }

        return $this->sendResponse($bankMemoSupplier->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_suppliers')]));
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
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_suppliers')]));
        }
        $bankMemoSupplier = $this->bankMemoSupplierRepository->update($input, $id);

        return $this->sendResponse($bankMemoSupplier->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_memo_suppliers')]));
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
        $bankMemoSupplier = BankMemoSupplier::where('bankMemoID', $request['bankMemoID'])->first();

        if (empty($bankMemoSupplier)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_suppliers')]));
        }

        $bankMemoSupplier->delete();

        return $this->sendResponse($request['bankMemoID'], trans('custom.delete', ['attribute' => trans('custom.bank_memo_suppliers')]));
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

        /** @var BankMemoSupplier $bankMemoSupplier */
        $bankMemoSupplier = $this->bankMemoSupplierRepository->findWithoutFail($id);

        if (empty($bankMemoSupplier)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_suppliers')]));
        }

        $bankMemoSupplier->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_memo_suppliers')]));
    }

    public function supplierBankMemoDeleteAll(Request $request)
    {

        $bankMemoSupplier = BankMemoSupplier::where('supplierCurrencyID', $request['supplierCurrencyID'])
            ->where('supplierCodeSystem', $request['supplierCodeSystem'])
            ->delete();


        return $this->sendResponse($bankMemoSupplier, trans('custom.delete', ['attribute' => trans('custom.bank_memo_suppliers')]));
    }
}

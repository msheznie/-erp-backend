<?php
/**
=============================================
-- File Name : BankMasterController.php
-- Project Name : ERP
-- Module Name :  Bank Master
-- Author : Pasan Madhuranga
-- Create date : 21 - March 2018
-- Description : This file contains the all CRUD for Bank Master
-- REVISION HISTORY
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as updateBankMaster()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as assignedCompaniesByBank()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as getBankMasterFormData()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankMasterAPIRequest;
use App\Http\Requests\API\UpdateBankMasterAPIRequest;
use App\Models\BankMaster;
use App\Models\BankAssign;
use App\Models\Company;
use App\Repositories\BankMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Repositories\UserRepository;
use Illuminate\Validation\Rule;

/**
 * Class BankMasterController
 * @package App\Http\Controllers\API
 */

class BankMasterAPIController extends AppBaseController
{
    /** @var  BankMasterRepository */
    private $bankMasterRepository;
    private $userRepository;

    public function __construct(BankMasterRepository $bankMasterRepo, UserRepository $userRepo)
    {
        $this->bankMasterRepository = $bankMasterRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the BankMaster.
     * GET|HEAD /bankMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->bankMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankMasters = $this->bankMasterRepository->all();

        return $this->sendResponse($bankMasters->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_masters')]));
    }

    /**
     * Store a newly created BankMaster in storage.
     * POST /bankMasters
     *
     * @param CreateBankMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankMasterAPIRequest $request)
    {
        $input = $request->all();

        $messages = array(
            'bankShortCode.unique'   => trans('custom.bank_code_exists')
        );

        $validator = \Validator::make($input, [
            'bankShortCode' => 'unique:erp_bankmaster'
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $id = \Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $empId = $user->employee['empID'];
        $input['createdByEmpID'] = $empId;

        $bankMasters = $this->bankMasterRepository->create($input);

        return $this->sendResponse($bankMasters->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_masters')]));
    }

    /**
     * Display the specified BankMaster.
     * GET|HEAD /bankMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankMaster $bankMaster */
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_masters')]));
        }

        return $this->sendResponse($bankMaster->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_masters')]));
    }

    /**
     * Update the specified BankMaster in storage.
     * PUT/PATCH /bankMasters/{id}
     *
     * @param  int $id
     * @param UpdateBankMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankMaster $bankMaster */
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_masters')]));
        }

        $bankMaster = $this->bankMasterRepository->update($input, $id);

        return $this->sendResponse($bankMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_masters')]));
    }

    /**
     * Remove the specified BankMaster from storage.
     * DELETE /bankMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BankMaster $bankMaster */
        $bankMaster = $this->bankMasterRepository->findWithoutFail($id);

        if (empty($bankMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_masters')]));
        }

        $bankMaster->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_masters')]));
    }

    /**
     * Get bank master data for list
     * @param Request $request
     * @return mixed
     */
    public function getAllBankMaster(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankMasters = BankMaster::select('*');

        $search = $request->input('search.value');
        if($search){
            $bankMasters =   $bankMasters->where(function ($q) use($search){
               $q->where('bankShortCode','LIKE',"%{$search}%")
                   ->orWhere('bankName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('bankmasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Update bank master details by bank id
     * @param Request $request
     * @return mixed
     */
    public function updateBankMaster(Request $request)
    {
        $input = $request->all();

        $messages = array(
            'bankShortCode.unique'   => trans('custom.bank_short_code_taken')
        );

        $validator = \Validator::make($input, [
            'bankShortCode' => Rule::unique('erp_bankmaster')->ignore($input['bankmasterAutoID'], 'bankmasterAutoID')
        ],$messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422 );
        }

        $data =array_except($input, ['bankmasterAutoID', 'TimeStamp', 'createdByEmpID', 'createdDateTime']);

        $bankMaster = $this->bankMasterRepository->update($data, $input['bankmasterAutoID']);

        return $this->sendResponse($bankMaster->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_masters')]));
    }

    /**
     * Display all assigned bankAssigned for specific Bank Master.
     * GET|HEAD /assignedCompaniesByBank
     *
     * @param  int itemCodeSystem
     *
     * @return Response
     */
    public function assignedCompaniesByBank(Request $request)
    {
        $bankId = $request['bankmasterAutoID'];
        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $itemCompanies = BankAssign::with(['company'])
                                   ->whereHas('company')
                                   ->where('bankmasterAutoID',$bankId)
                                   ->whereIn("companySystemID",$subCompanies);

        return \DataTables::of($itemCompanies)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('erp_bankassigned.bankAssignedAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Get bank master related dropdown data
     * @param Request $request
     * @return mixed
     */
    public function getBankMasterFormData(Request $request)
    {
        $bankId = $request['bankmasterAutoID'];
         $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }
        /** Get not assign company list */

        $allCompanies = Company::whereIn("companySystemID",$subCompanies)
                            ->whereDoesntHave('bank_assigned',function ($query) use ($bankId) {
                                $query->where('bankmasterAutoID', '=', $bankId);
                            })->where('isGroup',0)
                            ->get();

        $output = array(
            'allCompanies' => $allCompanies,
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.bank_masters')]));

    }
}

<?php
/**
=============================================
-- File Name : BankAssignController.php
-- Project Name : ERP
-- Module Name :  Bank Assigned
-- Author : Pasan Madhuranga
-- Create date : 21 - March 2018
-- Description : This file contains the all CRUD for Bank Assigned
-- REVISION HISTORY
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as getCompanyById()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as updateBankAssingCompany()
-- Date: 21 - March 2018 By: Pasan Description: Added a new function named as updateBankAssingCompany()
-- Date: 20 - Decemmber 2018 By: Pasan Description: Added a new function named as getBankMasterByCompany()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankAssignAPIRequest;
use App\Http\Requests\API\UpdateBankAssignAPIRequest;
use App\Models\BankAssign;
use App\Models\Company;
use App\Repositories\BankAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Repositories\UserRepository;
use Illuminate\Support\Arr;
use App\helper\Helper;

/**
 * Class BankAssignController
 * @package App\Http\Controllers\API
 */

class BankAssignAPIController extends AppBaseController
{
    /** @var  BankAssignRepository */
    private $bankAssignRepository;

    public function __construct(BankAssignRepository $bankAssignRepo, UserRepository $userRepo)
    {
        $this->bankAssignRepository = $bankAssignRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the BankAssign.
     * GET|HEAD /bankAssigns
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bankAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->bankAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankAssigns = $this->bankAssignRepository->all();

        return $this->sendResponse($bankAssigns->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_assigns')]));
    }

    /**
     * Store a newly created BankAssign in storage.
     * POST /bankAssigns
     *
     * @param CreateBankAssignAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAssignAPIRequest $request)
    {
        $input = $request->all();

        $input['companyID'] = $this->getCompanyById($input['companySystemID']);

        if($input["isEdit"] == 0)
        {
            $id = \Auth::id();
            $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

            $empId = $user->employee['empID'];
            $input['createdByEmpID'] = $empId;

            $input['isAssigned'] = -1;
            $input['isActive']   = 1;
            $input['isDefault']  = 0;

            $data = Arr::except($input, ['isEdit', 'TimeStamp']);

            $bankAssigns = $this->bankAssignRepository->create($data);
        }


        return $this->sendResponse($bankAssigns->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_assigns')]));
    }

    /**
     * Get company by id
     * @param $companySystemID
     * @return mixed
     */
    private function getCompanyById($companySystemID)
    {
        $company = Company::select('CompanyID')->where("companySystemID",$companySystemID)->first();

        return $company->CompanyID;
    }

    /**
     * Display the specified BankAssign.
     * GET|HEAD /bankAssigns/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankAssign $bankAssign */
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_assigns')]));
        }

        return $this->sendResponse($bankAssign->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_assigns')]));
    }

    /**
     * Update the specified BankAssign in storage.
     * PUT/PATCH /bankAssigns/{id}
     *
     * @param  int $id
     * @param UpdateBankAssignAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankAssign $bankAssign */
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_assigns')]));
        }

        $bankAssign = $this->bankAssignRepository->update($input, $id);

        return $this->sendResponse($bankAssign->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_assigns')]));
    }

    /**
     * Remove the specified BankAssign from storage.
     * DELETE /bankAssigns/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BankAssign $bankAssign */
        $bankAssign = $this->bankAssignRepository->findWithoutFail($id);

        if (empty($bankAssign)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_assigns')]));
        }

        $bankAssign->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_assigns')]));
    }


    /**
     * Update Bank Company assign.
     * Post /updateBankAssingCompany
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function updateBankAssingCompany (Request $request)
    {
        if($request['isDefault'] == true)
        {
            $bankAssign = BankAssign::where('companySystemID', $request['companySystemID'])->update(array('isDefault' => 0));
        }

        $data = [
            'isDefault' => $request['isDefault'] ? -1 : 0,
            'isAssigned' => $request['isAssigned'] ? -1 : 0,
            'isActive' => $request['isActive'] ? 1 : 0
        ];

        $bankAssignUpdated = BankAssign::where('bankAssignedAutoID', $request['bankAssignedAutoID'])->update($data);

        return $this->sendResponse($bankAssignUpdated, trans('custom.update', ['attribute' => trans('custom.bank_assigns')]));
    }


    public function getBankMasterByCompany(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $isGroup = Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $bankMasters = BankAssign::whereIn('companySystemID',$childCompanies)
                                 ->where('isAssigned',-1);

        $search = $request->input('search.value');
        if($search){
            $bankMasters =   $bankMasters->where(function ($q) use($search){
                $q->where('bankShortCode','LIKE',"%{$search}%")
                    ->orWhere('bankName', 'LIKE', "%{$search}%");
            });
        }

        $bankMasters =   $bankMasters->groupBy('bankmasterAutoID');

        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);

        return \DataTables::eloquent($bankMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('bankAssignedAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}

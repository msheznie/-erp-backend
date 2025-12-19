<?php
/**
=============================================
-- File Name : CustomerAssignedAPIController.php
-- Project Name : ERP
-- Module Name :  Customer Assigned
-- Author : Mohamed Fayas
-- Create date : 19 - March 2018
-- Description : This file contains the all CRUD for Customer Assigned
-- REVISION HISTORY
 -- Date: 21-March 2018 By: Fayas Description: Added new functions named as getNotAssignedCompaniesByCustomer()
 -- Date: 11-September 2018 By: Fayas Description: Added new functions named as getAllCustomersByCompany()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerAssignedAPIRequest;
use App\Http\Requests\API\UpdateCustomerAssignedAPIRequest;
use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Repositories\CustomerAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;

/**
 * Class CustomerAssignedController
 * @package App\Http\Controllers\API
 */

class CustomerAssignedAPIController extends AppBaseController
{
    /** @var  CustomerAssignedRepository */
    private $customerAssignedRepository;
    private $userRepository;

    public function __construct(CustomerAssignedRepository $customerAssignedRepo,UserRepository $userRepo)
    {
        $this->customerAssignedRepository = $customerAssignedRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the CustomerAssigned.
     * GET|HEAD /customerAssigneds
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->customerAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->customerAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerAssigneds = $this->customerAssignedRepository->all();

        return $this->sendResponse($customerAssigneds->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_assigneds')]));
    }

    /**
     * Store a newly created CustomerAssigned in storage.
     * POST /customerAssigneds
     *
     * @param CreateCustomerAssignedAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerAssignedAPIRequest $request)
    {
       
        $input = $request->all();
        $id = Auth::id();
        $companies = $input['companySystemID'];
        unset($input['companySystemID']);
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];
        $input = array_except($input, ['final_approved_by','company', 'gl_account', 'unbilled_account']);
        $input = $this->convertArrayToValue($input);

        if( array_key_exists ('customerAssignedID' , $input )){

         
                if($input['isAssigned'] == 1 || $input['isAssigned'] == true){
                    $validatorResult = \Helper::checkCompanyForMasters($companies, $input['customerCodeSystem'], 'customer', true);
                    if (!$validatorResult['success']) {
                        return $this->sendError($validatorResult['message']);
                    }
                    $input['isAssigned'] = -1;
                }
                $data = [
                    'isAssigned'    => $input['isAssigned'],
                    'isActive'      => $input['isActive'],
                    'vatEligible'   => $input['vatEligible'],
                    'vatNumber'     => $input['vatNumber'],
                    'vatPercentage' => $input['vatPercentage']
                ];
    
                $customerAssigneds = $this->customerAssignedRepository->update($data, $input['customerAssignedID']);

          


        }else{

            foreach($companies as $companie)
            {
                $validatorResult = \Helper::checkCompanyForMasters($companie['id'], $input['customerCodeSystem'], 'customer');
                if (!$validatorResult['success']) {
                    return $this->sendError($validatorResult['message']);
                }
    
                $input = $this->convertArrayToValue($input);
                $input['isAssigned'] = -1;
                $company = Company::where('companySystemID', $companie['id'])->first();
                if($company){
                    $input['companyID'] = $company->CompanyID;
                }
                $input['companySystemID'] = $companie['id'];
                $customerAssigneds = $this->customerAssignedRepository->create($input);
            }

        }

        return $this->sendResponse($customerAssigneds->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_assigneds')]));
    }

    /**
     *  Display a listing of the companies not assigned for specific customer.
     * Get /getNotAssignedCompaniesByCustomer
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getNotAssignedCompaniesByCustomer(Request $request)
    {

        $customerId = $request->get('customerId');

        $selectedCompanyId = $request->get('selectedCompanyId');

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $companies = Company::whereIn('companySystemID', $subCompanies)
            ->whereDoesntHave('customerAssigned',function ($query) use ($customerId) {
                $query->where('customerCodeSystem', '=', $customerId);
            })->where('isGroup',0)
            ->get(['companySystemID','CompanyID','CompanyName']);

        return $this->sendResponse($companies->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.companies')]));
    }

    /**
     * Display the specified CustomerAssigned.
     * GET|HEAD /customerAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CustomerAssigned $customerAssigned */
        $customerAssigned = $this->customerAssignedRepository->findWithoutFail($id);

        if (empty($customerAssigned)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_assigneds')]));
        }

        return $this->sendResponse($customerAssigned->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_assigneds')]));
    }

    /**
     * Update the specified CustomerAssigned in storage.
     * PUT/PATCH /customerAssigneds/{id}
     *
     * @param  int $id
     * @param UpdateCustomerAssignedAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerAssigned $customerAssigned */
        $customerAssigned = $this->customerAssignedRepository->findWithoutFail($id);

        if (empty($customerAssigned)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_assigneds')]));
        }

        $customerAssigned = $this->customerAssignedRepository->update($input, $id);

        return $this->sendResponse($customerAssigned->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_assigneds')]));
    }

    /**
     * Remove the specified CustomerAssigned from storage.
     * DELETE /customerAssigneds/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CustomerAssigned $customerAssigned */
        $customerAssigned = $this->customerAssignedRepository->findWithoutFail($id);

        if (empty($customerAssigned)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_assigneds')]));
        }

        $customerAssigned->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_assigneds')]));
    }

    /**
     * Display a listing of the Customers by company.
     * GET|HEAD /getAllCustomersByCompany
     *
     * @param Request $request
     * @return Response
     */
    public function getAllCustomersByCompany(Request $request){

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if($isGroup){
            $childCompanies = \Helper::getGroupCompany($companyId);
        }else{
            $childCompanies = [$companyId];
        }
        $customerMasters = CustomerAssigned::with(['country'])
                                        ->whereIn('companySystemID',$childCompanies)
                                            ->where('isAssigned',-1);

        $search = $request->input('search.value');
        if($search){
            $customerMasters =   $customerMasters->where(function ($query) use($search) {
                $query->where('CutomerCode','LIKE',"%{$search}%")
                    ->orWhere('customerShortCode', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($customerMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('customerAssignedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

}

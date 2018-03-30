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

        return $this->sendResponse($customerAssigneds->toArray(), 'Customer Assigneds retrieved successfully');
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
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['empID'];
        $empName = $user->employee['empName'];

        if( array_key_exists ('customerAssignedID' , $input )){

            $inputData = $request->all();
            $data = [
                'isAssigned'    => $inputData['isAssigned'],
                'isActive'      => $inputData['isActive'],
                'vatEligible'   => $inputData['vatEligible'],
                'vatNumber'     => $inputData['vatNumber'],
                'vatPercentage' => $inputData['vatPercentage']
            ];

            $customerAssigneds = $this->customerAssignedRepository->update($data, $inputData['customerAssignedID']);

//            $customerAssigneds = CustomerAssigned::where('customerAssignedID', $input['customerAssignedID'])->first();
//
//            if (empty($customerAssigneds)) {
//                return $this->sendError('customer assign not found');
//            }
//            $customerAssigneds->isAssigned = $input['isAssigned'];
//            $customerAssigneds->isActive = $input['isActive'];
//            $customerAssigneds->vatEligible = $input['vatEligible'];
//            $customerAssigneds->vatNumber = $input['vatNumber'];
//            $customerAssigneds->vatPercentage = $input['vatPercentage'];
//
//            $customerAssigneds->save();
        }else{

            $input = $this->convertArrayToValue($input);

            $company = Company::where('companySystemID', $input['companySystemID'])->first();
            if($company){
                $input['companyID'] = $company->CompanyID;
            }

            $customerAssigneds = $this->customerAssignedRepository->create($input);
        }

        return $this->sendResponse($customerAssigneds->toArray(), 'Customer Assigned saved successfully');
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
        $companies = Company::where('isGroup', 0)
            ->whereDoesntHave('customerAssigned',function ($query) use ($customerId) {
                $query->where('customerCodeSystem', '=', $customerId);
            })
            ->get(['companySystemID',
                'CompanyID',
                'CompanyName']);

        return $this->sendResponse($companies->toArray(), 'Companies retrieved successfully');
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
            return $this->sendError('Customer Assigned not found');
        }

        return $this->sendResponse($customerAssigned->toArray(), 'Customer Assigned retrieved successfully');
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
            return $this->sendError('Customer Assigned not found');
        }

        $customerAssigned = $this->customerAssignedRepository->update($input, $id);

        return $this->sendResponse($customerAssigned->toArray(), 'CustomerAssigned updated successfully');
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
            return $this->sendError('Customer Assigned not found');
        }

        $customerAssigned->delete();

        return $this->sendResponse($id, 'Customer Assigned deleted successfully');
    }
}

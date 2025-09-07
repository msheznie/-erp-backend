<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerMasterCategoryAssignedAPIRequest;
use App\Http\Requests\API\UpdateCustomerMasterCategoryAssignedAPIRequest;
use App\Models\CustomerMasterCategoryAssigned;
use App\Models\Company;
use App\Models\CustomerMasterCategory;
use App\Repositories\CustomerMasterCategoryAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;

/**
 * Class CustomerMasterCategoryAssignedController
 * @package App\Http\Controllers\API
 */

class CustomerMasterCategoryAssignedAPIController extends AppBaseController
{
    /** @var  CustomerMasterCategoryAssignedRepository */
    private $customerMasterCategoryAssignedRepository;
    private $userRepository;

    public function __construct(CustomerMasterCategoryAssignedRepository $customerMasterCategoryAssignedRepo, UserRepository $userRepo)
    {
        $this->customerMasterCategoryAssignedRepository = $customerMasterCategoryAssignedRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerMasterCategoryAssigneds",
     *      summary="Get a listing of the CustomerMasterCategoryAssigneds.",
     *      tags={"CustomerMasterCategoryAssigned"},
     *      description="Get all CustomerMasterCategoryAssigneds",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/CustomerMasterCategoryAssigned")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->customerMasterCategoryAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->customerMasterCategoryAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerMasterCategoryAssigneds = $this->customerMasterCategoryAssignedRepository->all();

        return $this->sendResponse($customerMasterCategoryAssigneds->toArray(), trans('custom.customer_master_category_assigneds_retrieved_succe'));
    }

    /**
     * @param CreateCustomerMasterCategoryAssignedAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerMasterCategoryAssigneds",
     *      summary="Store a newly created CustomerMasterCategoryAssigned in storage",
     *      tags={"CustomerMasterCategoryAssigned"},
     *      description="Store CustomerMasterCategoryAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerMasterCategoryAssigned that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerMasterCategoryAssigned")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomerMasterCategoryAssigned"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerMasterCategoryAssignedAPIRequest $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $empId = $user->employee['employeeSystemID'];

        if( array_key_exists ('id' , $input )){
            if($input['isAssigned'] == 1 || $input['isAssigned'] == true){
                $input['isAssigned'] = 1;
            }
            $data = [
                'isAssigned'    => $input['isAssigned'],
                'isActive'      => $input['isActive']
            ];

            $customerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepository->update($data, $input['id']);
        }else{
            $validatorResult = \Helper::checkCompanyForMasters($input['companySystemID'], $input['categoryID'], 'customerCategory');
            if (!$validatorResult['success']) {
                return $this->sendError($validatorResult['message']);
            }

            $createData = [
                'customerMasterCategoryID' => $input['categoryID'],
                'companySystemID' => $input['companySystemID'],
                'categoryDescription' => $input['categoryDescription'],
                'createdUserID' => $empId,
                'isAssigned' => 1,
                'isActive' => 1,
            ];

            $customerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepository->create($createData);
        }

        return $this->sendResponse($customerMasterCategoryAssigned->toArray(), trans('custom.customer_master_category_assigned_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerMasterCategoryAssigneds/{id}",
     *      summary="Display the specified CustomerMasterCategoryAssigned",
     *      tags={"CustomerMasterCategoryAssigned"},
     *      description="Get CustomerMasterCategoryAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterCategoryAssigned",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomerMasterCategoryAssigned"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CustomerMasterCategoryAssigned $customerMasterCategoryAssigned */
        $customerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepository->findWithoutFail($id);

        if (empty($customerMasterCategoryAssigned)) {
            return $this->sendError(trans('custom.customer_master_category_assigned_not_found'));
        }

        return $this->sendResponse($customerMasterCategoryAssigned->toArray(), trans('custom.customer_master_category_assigned_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdateCustomerMasterCategoryAssignedAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerMasterCategoryAssigneds/{id}",
     *      summary="Update the specified CustomerMasterCategoryAssigned in storage",
     *      tags={"CustomerMasterCategoryAssigned"},
     *      description="Update CustomerMasterCategoryAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterCategoryAssigned",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerMasterCategoryAssigned that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerMasterCategoryAssigned")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/CustomerMasterCategoryAssigned"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerMasterCategoryAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerMasterCategoryAssigned $customerMasterCategoryAssigned */
        $customerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepository->findWithoutFail($id);

        if (empty($customerMasterCategoryAssigned)) {
            return $this->sendError(trans('custom.customer_master_category_assigned_not_found'));
        }

        $customerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepository->update($input, $id);

        return $this->sendResponse($customerMasterCategoryAssigned->toArray(), trans('custom.customermastercategoryassigned_updated_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerMasterCategoryAssigneds/{id}",
     *      summary="Remove the specified CustomerMasterCategoryAssigned from storage",
     *      tags={"CustomerMasterCategoryAssigned"},
     *      description="Delete CustomerMasterCategoryAssigned",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterCategoryAssigned",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CustomerMasterCategoryAssigned $customerMasterCategoryAssigned */
        $customerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepository->findWithoutFail($id);

        if (empty($customerMasterCategoryAssigned)) {
            return $this->sendError(trans('custom.customer_master_category_assigned_not_found'));
        }

        $customerMasterCategoryAssigned->delete();

        return $this->sendSuccess('Customer Master Category Assigned deleted successfully');
    }

    public function assignedCompaniesByCustomerCategory(Request $request)
    {

        $categoryID = $request['categoryID'];
        $selectedCompanyId = $request['selectedCompanyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if($isGroup){
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $subCompanies = [$selectedCompanyId];
        }

        $CustomerMasterCategory = CustomerMasterCategory::where('categoryID', '=', $categoryID)->first();
        if ($CustomerMasterCategory) {
            $customerCompanies = CustomerMasterCategoryAssigned::where('customerMasterCategoryID', $categoryID)
                ->with(['company'])
                ->whereIn("companySystemID",$subCompanies)
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $customerCompanies = [];
        }

        return $this->sendResponse($customerCompanies, trans('custom.customer_category_companies_retrieved_successfully'));
    }
}

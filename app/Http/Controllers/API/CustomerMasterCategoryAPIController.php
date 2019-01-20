<?php
/**
 * =============================================
 * -- File Name : CustomerMasterCategoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  CustomerMasterCategory
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - January 2019
 * -- Description : This file contains the all CRUD for Customer Master Category
 * -- REVISION HISTORY
 * -- Date: 20-January 2019 By: Nazir Description: Added new function getAllCustomerCategories(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerMasterCategoryAPIRequest;
use App\Http\Requests\API\UpdateCustomerMasterCategoryAPIRequest;
use App\Models\CustomerMasterCategory;
use App\Models\Company;
use App\Repositories\CustomerMasterCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerMasterCategoryController
 * @package App\Http\Controllers\API
 */
class CustomerMasterCategoryAPIController extends AppBaseController
{
    /** @var  CustomerMasterCategoryRepository */
    private $customerMasterCategoryRepository;

    public function __construct(CustomerMasterCategoryRepository $customerMasterCategoryRepo)
    {
        $this->customerMasterCategoryRepository = $customerMasterCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerMasterCategories",
     *      summary="Get a listing of the CustomerMasterCategories.",
     *      tags={"CustomerMasterCategory"},
     *      description="Get all CustomerMasterCategories",
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
     *                  @SWG\Items(ref="#/definitions/CustomerMasterCategory")
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
        $this->customerMasterCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->customerMasterCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerMasterCategories = $this->customerMasterCategoryRepository->all();

        return $this->sendResponse($customerMasterCategories->toArray(), 'Customer Master Categories retrieved successfully');
    }

    /**
     * @param CreateCustomerMasterCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerMasterCategories",
     *      summary="Store a newly created CustomerMasterCategory in storage",
     *      tags={"CustomerMasterCategory"},
     *      description="Store CustomerMasterCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerMasterCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerMasterCategory")
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
     *                  ref="#/definitions/CustomerMasterCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerMasterCategoryAPIRequest $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();

        if (isset($request->categoryID)) {

            $validate = CustomerMasterCategory::where('companySystemID', $request->companySystemID)
                ->where('categoryDescription', $request->categoryDescription)
                ->where('categoryID','<>' ,$request->categoryID)
                ->exists();

            if ($validate) {
                return $this->sendError('category description already exists');
            } else {

                $categosryMasterUpdate = CustomerMasterCategory::find($request->categoryID);
                $categosryMasterUpdate->categoryDescription = $input["categoryDescription"];
                $categosryMasterUpdate->modifiedPCID = gethostname();
                $categosryMasterUpdate->modifiedUserID = $employee->empID;
                $categosryMasterUpdate->save();
            }
        } else {
            $companyMaster = Company::find($input['companySystemID']);

            $validate = CustomerMasterCategory::where('companySystemID', $request->companySystemID)
                ->where('categoryDescription', $request->categoryDescription)
                ->exists();

            if ($validate) {
                return $this->sendError('category description already exists');
            } else {
                $input['companyID'] = $companyMaster->CompanyID;
                $input['createdPCID'] = gethostname();
                $input['createdUserID'] = $employee->empID;
                $customerMasterCategories = $this->customerMasterCategoryRepository->create($input);
            }
        }
        return $this->sendResponse($customerMasterCategories->toArray(), 'Customer Master Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerMasterCategories/{id}",
     *      summary="Display the specified CustomerMasterCategory",
     *      tags={"CustomerMasterCategory"},
     *      description="Get CustomerMasterCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterCategory",
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
     *                  ref="#/definitions/CustomerMasterCategory"
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
        /** @var CustomerMasterCategory $customerMasterCategory */
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            return $this->sendError('Customer Master Category not found');
        }

        return $this->sendResponse($customerMasterCategory->toArray(), 'Customer Master Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerMasterCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerMasterCategories/{id}",
     *      summary="Update the specified CustomerMasterCategory in storage",
     *      tags={"CustomerMasterCategory"},
     *      description="Update CustomerMasterCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerMasterCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerMasterCategory")
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
     *                  ref="#/definitions/CustomerMasterCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerMasterCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerMasterCategory $customerMasterCategory */
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            return $this->sendError('Customer Master Category not found');
        }

        $customerMasterCategory = $this->customerMasterCategoryRepository->update($input, $id);

        return $this->sendResponse($customerMasterCategory->toArray(), 'CustomerMasterCategory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerMasterCategories/{id}",
     *      summary="Remove the specified CustomerMasterCategory from storage",
     *      tags={"CustomerMasterCategory"},
     *      description="Delete CustomerMasterCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerMasterCategory",
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
        /** @var CustomerMasterCategory $customerMasterCategory */
        $customerMasterCategory = $this->customerMasterCategoryRepository->findWithoutFail($id);

        if (empty($customerMasterCategory)) {
            return $this->sendError('Customer Master Category not found');
        }

        $customerMasterCategory->delete();

        return $this->sendResponse($id, 'Customer Master Category deleted successfully');
    }

    public function getAllCustomerCategories(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $customerMasterCategory = CustomerMasterCategory::whereIn('companySystemID', $childCompanies);

        $search = $request->input('search.value');
        if ($search) {
            $customerMasterCategory = $customerMasterCategory->where(function ($query) use ($search) {
                $query->where('wareHouseDescription', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($customerMasterCategory)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('categoryID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}

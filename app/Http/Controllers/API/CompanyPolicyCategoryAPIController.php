<?php
/**
 * =============================================
 * -- File Name : CompanyPolicyCategoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :   Company Policy Category
 * -- Author : Fayas
 * -- Create date : 11 - May 2018
 * -- Description : This file contains the all CRUD for  Company Policy Category.
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyPolicyCategoryAPIRequest;
use App\Http\Requests\API\UpdateCompanyPolicyCategoryAPIRequest;
use App\Models\CompanyPolicyCategory;
use App\Repositories\CompanyPolicyCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyPolicyCategoryController
 * @package App\Http\Controllers\API
 */

class CompanyPolicyCategoryAPIController extends AppBaseController
{
    /** @var  CompanyPolicyCategoryRepository */
    private $companyPolicyCategoryRepository;

    public function __construct(CompanyPolicyCategoryRepository $companyPolicyCategoryRepo)
    {
        $this->companyPolicyCategoryRepository = $companyPolicyCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyPolicyCategories",
     *      summary="Get a listing of the CompanyPolicyCategories.",
     *      tags={"CompanyPolicyCategory"},
     *      description="Get all CompanyPolicyCategories",
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
     *                  @SWG\Items(ref="#/definitions/CompanyPolicyCategory")
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
        $this->companyPolicyCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->companyPolicyCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyPolicyCategories = $this->companyPolicyCategoryRepository->all();

        return $this->sendResponse($companyPolicyCategories->toArray(), 'Company Policy Categories retrieved successfully');
    }

    /**
     * @param CreateCompanyPolicyCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/companyPolicyCategories",
     *      summary="Store a newly created CompanyPolicyCategory in storage",
     *      tags={"CompanyPolicyCategory"},
     *      description="Store CompanyPolicyCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyPolicyCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyPolicyCategory")
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
     *                  ref="#/definitions/CompanyPolicyCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyPolicyCategoryAPIRequest $request)
    {
        $input = $request->all();

        $companyPolicyCategories = $this->companyPolicyCategoryRepository->create($input);

        return $this->sendResponse($companyPolicyCategories->toArray(), 'Company Policy Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/companyPolicyCategories/{id}",
     *      summary="Display the specified CompanyPolicyCategory",
     *      tags={"CompanyPolicyCategory"},
     *      description="Get CompanyPolicyCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyCategory",
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
     *                  ref="#/definitions/CompanyPolicyCategory"
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
        /** @var CompanyPolicyCategory $companyPolicyCategory */
        $companyPolicyCategory = $this->companyPolicyCategoryRepository->findWithoutFail($id);

        if (empty($companyPolicyCategory)) {
            return $this->sendError('Company Policy Category not found');
        }

        return $this->sendResponse($companyPolicyCategory->toArray(), 'Company Policy Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCompanyPolicyCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/companyPolicyCategories/{id}",
     *      summary="Update the specified CompanyPolicyCategory in storage",
     *      tags={"CompanyPolicyCategory"},
     *      description="Update CompanyPolicyCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CompanyPolicyCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CompanyPolicyCategory")
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
     *                  ref="#/definitions/CompanyPolicyCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyPolicyCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyPolicyCategory $companyPolicyCategory */
        $companyPolicyCategory = $this->companyPolicyCategoryRepository->findWithoutFail($id);

        if (empty($companyPolicyCategory)) {
            return $this->sendError('Company Policy Category not found');
        }

        $companyPolicyCategory = $this->companyPolicyCategoryRepository->update($input, $id);

        return $this->sendResponse($companyPolicyCategory->toArray(), 'CompanyPolicyCategory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/companyPolicyCategories/{id}",
     *      summary="Remove the specified CompanyPolicyCategory from storage",
     *      tags={"CompanyPolicyCategory"},
     *      description="Delete CompanyPolicyCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CompanyPolicyCategory",
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
        /** @var CompanyPolicyCategory $companyPolicyCategory */
        $companyPolicyCategory = $this->companyPolicyCategoryRepository->findWithoutFail($id);

        if (empty($companyPolicyCategory)) {
            return $this->sendError('Company Policy Category not found');
        }

        $companyPolicyCategory->delete();

        return $this->sendResponse($id, 'Company Policy Category deleted successfully');
    }
}

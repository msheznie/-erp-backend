<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxVatMainCategoriesAPIRequest;
use App\Http\Requests\API\UpdateTaxVatMainCategoriesAPIRequest;
use App\Models\TaxVatMainCategories;
use App\Repositories\TaxVatMainCategoriesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxVatMainCategoriesController
 * @package App\Http\Controllers\API
 */

class TaxVatMainCategoriesAPIController extends AppBaseController
{
    /** @var  TaxVatMainCategoriesRepository */
    private $taxVatMainCategoriesRepository;

    public function __construct(TaxVatMainCategoriesRepository $taxVatMainCategoriesRepo)
    {
        $this->taxVatMainCategoriesRepository = $taxVatMainCategoriesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxVatMainCategories",
     *      summary="Get a listing of the TaxVatMainCategories.",
     *      tags={"TaxVatMainCategories"},
     *      description="Get all TaxVatMainCategories",
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
     *                  @SWG\Items(ref="#/definitions/TaxVatMainCategories")
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
        $this->taxVatMainCategoriesRepository->pushCriteria(new RequestCriteria($request));
        $this->taxVatMainCategoriesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->all();

        return $this->sendResponse($taxVatMainCategories->toArray(), 'Tax Vat Main Categories retrieved successfully');
    }

    /**
     * @param CreateTaxVatMainCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxVatMainCategories",
     *      summary="Store a newly created TaxVatMainCategories in storage",
     *      tags={"TaxVatMainCategories"},
     *      description="Store TaxVatMainCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxVatMainCategories that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxVatMainCategories")
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
     *                  ref="#/definitions/TaxVatMainCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxVatMainCategoriesAPIRequest $request)
    {
        $input = $request->all();

        if(!(isset($input['taxMasterAutoID']) && $input['taxMasterAutoID'])){
            return $this->sendError('Tax Master Auto ID is not found',500);
        }

        $validator = \Validator::make($input, [
            'mainCategoryDescription' => 'required|unique:erp_tax_vat_main_categories'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->create($input);

        return $this->sendResponse($taxVatMainCategories->toArray(), 'Tax Vat Main Categories saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxVatMainCategories/{id}",
     *      summary="Display the specified TaxVatMainCategories",
     *      tags={"TaxVatMainCategories"},
     *      description="Get TaxVatMainCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatMainCategories",
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
     *                  ref="#/definitions/TaxVatMainCategories"
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
        /** @var TaxVatMainCategories $taxVatMainCategories */
        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatMainCategories)) {
            return $this->sendError('Tax Vat Main Categories not found');
        }

        return $this->sendResponse($taxVatMainCategories->toArray(), 'Tax Vat Main Categories retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTaxVatMainCategoriesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxVatMainCategories/{id}",
     *      summary="Update the specified TaxVatMainCategories in storage",
     *      tags={"TaxVatMainCategories"},
     *      description="Update TaxVatMainCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatMainCategories",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxVatMainCategories that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxVatMainCategories")
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
     *                  ref="#/definitions/TaxVatMainCategories"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxVatMainCategoriesAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxVatMainCategories $taxVatMainCategories */
        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatMainCategories)) {
            return $this->sendError('Tax Vat Main Categories not found');
        }

        $validator = \Validator::make($input, [
            'mainCategoryDescription' => ['required', Rule::unique('erp_tax_vat_main_categories')->ignore($id, 'taxVatMainCategoriesAutoID')],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->update($input, $id);

        return $this->sendResponse($taxVatMainCategories->toArray(), 'TaxVatMainCategories updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxVatMainCategories/{id}",
     *      summary="Remove the specified TaxVatMainCategories from storage",
     *      tags={"TaxVatMainCategories"},
     *      description="Delete TaxVatMainCategories",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxVatMainCategories",
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
        /** @var TaxVatMainCategories $taxVatMainCategories */
        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatMainCategories)) {
            return $this->sendError('Tax Vat Main Categories not found');
        }

        $taxVatMainCategories->delete();

        return $this->sendSuccess('Tax Vat Main Categories deleted successfully');
    }
}

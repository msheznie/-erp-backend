<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxVatMainCategoriesAPIRequest;
use App\Http\Requests\API\UpdateTaxVatMainCategoriesAPIRequest;
use App\Models\TaxVatCategories;
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

        return $this->sendResponse($taxVatMainCategories->toArray(), trans('custom.tax_vat_main_categories_retrieved_successfully'));
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
            return $this->sendError(trans('custom.tax_master_auto_id_is_not_found'),500);
        }

        $validator = \Validator::make($input, [
            'mainCategoryDescription' => 'required|unique:erp_tax_vat_main_categories'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->create($input);

        return $this->sendResponse($taxVatMainCategories->toArray(), trans('custom.tax_vat_main_categories_saved_successfully'));
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
            return $this->sendError(trans('custom.tax_vat_main_categories_not_found'));
        }

        return $this->sendResponse($taxVatMainCategories->toArray(), trans('custom.tax_vat_main_categories_retrieved_successfully'));
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
        $input = array_except($input,['tax']);
        /** @var TaxVatMainCategories $taxVatMainCategories */
        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->findWithoutFail($id);

        if (empty($taxVatMainCategories)) {
            return $this->sendError(trans('custom.tax_vat_main_categories_not_found'));
        }

        $validator = \Validator::make($input, [
            'mainCategoryDescription' => ['required', Rule::unique('erp_tax_vat_main_categories')->ignore($id, 'taxVatMainCategoriesAutoID')],
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $taxVatMainCategories = $this->taxVatMainCategoriesRepository->update($input, $id);

        return $this->sendResponse($taxVatMainCategories->toArray(), trans('custom.taxvatmaincategories_updated_successfully'));
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
            return $this->sendError(trans('custom.tax_vat_main_categories_not_found'));
        }

        $isExists = TaxVatCategories::where('mainCategory',$id)->exists();
        if ($isExists) {
            return $this->sendError(trans('custom.you_cannot_delete_this_main_category_has_assigned_'));
        }

        $taxVatMainCategories->delete();

        return $this->sendResponse([],trans('custom.tax_vat_main_categories_deleted_successfully'));
    }

    public function getAllVatMainCategories(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $taxMasterAutoID = $request['taxMasterAutoID'];

        $vatMainCategories = TaxVatMainCategories::where('taxMasterAutoID', $taxMasterAutoID)
            ->with(['tax']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $vatMainCategories = $vatMainCategories->where(function ($query) use ($search) {
                $query->whereHas('tax', function($q)use ($search){
                    $q->where('taxShortCode','LIKE', "%{$search}%")
                        ->orWhere('taxDescription','LIKE', "%{$search}%");
                })
                    ->orWhere('mainCategoryDescription','LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($vatMainCategories)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('taxVatMainCategoriesAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}

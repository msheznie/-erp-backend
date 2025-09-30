<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSrpErpFormCategoryAPIRequest;
use App\Http\Requests\API\UpdateSrpErpFormCategoryAPIRequest;
use App\Models\SrpErpFormCategory;
use App\Repositories\SrpErpFormCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SrpErpFormCategoryController
 * @package App\Http\Controllers\API
 */

class SrpErpFormCategoryAPIController extends AppBaseController
{
    /** @var  SrpErpFormCategoryRepository */
    private $srpErpFormCategoryRepository;

    public function __construct(SrpErpFormCategoryRepository $srpErpFormCategoryRepo)
    {
        $this->srpErpFormCategoryRepository = $srpErpFormCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpFormCategories",
     *      summary="Get a listing of the SrpErpFormCategories.",
     *      tags={"SrpErpFormCategory"},
     *      description="Get all SrpErpFormCategories",
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
     *                  @SWG\Items(ref="#/definitions/SrpErpFormCategory")
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
        $this->srpErpFormCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->srpErpFormCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $srpErpFormCategories = $this->srpErpFormCategoryRepository->all();

        return $this->sendResponse($srpErpFormCategories->toArray(), trans('custom.srp_erp_form_categories_retrieved_successfully'));
    }

    /**
     * @param CreateSrpErpFormCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/srpErpFormCategories",
     *      summary="Store a newly created SrpErpFormCategory in storage",
     *      tags={"SrpErpFormCategory"},
     *      description="Store SrpErpFormCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpFormCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpFormCategory")
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
     *                  ref="#/definitions/SrpErpFormCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSrpErpFormCategoryAPIRequest $request)
    {
        $input = $request->all();

        $srpErpFormCategory = $this->srpErpFormCategoryRepository->create($input);

        return $this->sendResponse($srpErpFormCategory->toArray(), trans('custom.srp_erp_form_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/srpErpFormCategories/{id}",
     *      summary="Display the specified SrpErpFormCategory",
     *      tags={"SrpErpFormCategory"},
     *      description="Get SrpErpFormCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpFormCategory",
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
     *                  ref="#/definitions/SrpErpFormCategory"
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
        /** @var SrpErpFormCategory $srpErpFormCategory */
        $srpErpFormCategory = $this->srpErpFormCategoryRepository->findWithoutFail($id);

        if (empty($srpErpFormCategory)) {
            return $this->sendError(trans('custom.srp_erp_form_category_not_found'));
        }

        return $this->sendResponse($srpErpFormCategory->toArray(), trans('custom.srp_erp_form_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSrpErpFormCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/srpErpFormCategories/{id}",
     *      summary="Update the specified SrpErpFormCategory in storage",
     *      tags={"SrpErpFormCategory"},
     *      description="Update SrpErpFormCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpFormCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SrpErpFormCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SrpErpFormCategory")
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
     *                  ref="#/definitions/SrpErpFormCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSrpErpFormCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var SrpErpFormCategory $srpErpFormCategory */
        $srpErpFormCategory = $this->srpErpFormCategoryRepository->findWithoutFail($id);

        if (empty($srpErpFormCategory)) {
            return $this->sendError(trans('custom.srp_erp_form_category_not_found'));
        }

        $srpErpFormCategory = $this->srpErpFormCategoryRepository->update($input, $id);

        return $this->sendResponse($srpErpFormCategory->toArray(), trans('custom.srperpformcategory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/srpErpFormCategories/{id}",
     *      summary="Remove the specified SrpErpFormCategory from storage",
     *      tags={"SrpErpFormCategory"},
     *      description="Delete SrpErpFormCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SrpErpFormCategory",
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
        /** @var SrpErpFormCategory $srpErpFormCategory */
        $srpErpFormCategory = $this->srpErpFormCategoryRepository->findWithoutFail($id);

        if (empty($srpErpFormCategory)) {
            return $this->sendError(trans('custom.srp_erp_form_category_not_found'));
        }

        $srpErpFormCategory->delete();

        return $this->sendSuccess('Srp Erp Form Category deleted successfully');
    }
}

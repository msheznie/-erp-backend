<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFillingCategoryAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFillingCategoryAPIRequest;
use App\Models\VatReturnFillingCategory;
use App\Repositories\VatReturnFillingCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFillingCategoryController
 * @package App\Http\Controllers\API
 */

class VatReturnFillingCategoryAPIController extends AppBaseController
{
    /** @var  VatReturnFillingCategoryRepository */
    private $vatReturnFillingCategoryRepository;

    public function __construct(VatReturnFillingCategoryRepository $vatReturnFillingCategoryRepo)
    {
        $this->vatReturnFillingCategoryRepository = $vatReturnFillingCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingCategories",
     *      summary="Get a listing of the VatReturnFillingCategories.",
     *      tags={"VatReturnFillingCategory"},
     *      description="Get all VatReturnFillingCategories",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFillingCategory")
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
        $this->vatReturnFillingCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFillingCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFillingCategories = $this->vatReturnFillingCategoryRepository->all();

        return $this->sendResponse($vatReturnFillingCategories->toArray(), trans('custom.vat_return_filling_categories_retrieved_successful'));
    }

    /**
     * @param CreateVatReturnFillingCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFillingCategories",
     *      summary="Store a newly created VatReturnFillingCategory in storage",
     *      tags={"VatReturnFillingCategory"},
     *      description="Store VatReturnFillingCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingCategory")
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
     *                  ref="#/definitions/VatReturnFillingCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFillingCategoryAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFillingCategory = $this->vatReturnFillingCategoryRepository->create($input);

        return $this->sendResponse($vatReturnFillingCategory->toArray(), trans('custom.vat_return_filling_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFillingCategories/{id}",
     *      summary="Display the specified VatReturnFillingCategory",
     *      tags={"VatReturnFillingCategory"},
     *      description="Get VatReturnFillingCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingCategory",
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
     *                  ref="#/definitions/VatReturnFillingCategory"
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
        /** @var VatReturnFillingCategory $vatReturnFillingCategory */
        $vatReturnFillingCategory = $this->vatReturnFillingCategoryRepository->findWithoutFail($id);

        if (empty($vatReturnFillingCategory)) {
            return $this->sendError(trans('custom.vat_return_filling_category_not_found'));
        }

        return $this->sendResponse($vatReturnFillingCategory->toArray(), trans('custom.vat_return_filling_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFillingCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFillingCategories/{id}",
     *      summary="Update the specified VatReturnFillingCategory in storage",
     *      tags={"VatReturnFillingCategory"},
     *      description="Update VatReturnFillingCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFillingCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFillingCategory")
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
     *                  ref="#/definitions/VatReturnFillingCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFillingCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFillingCategory $vatReturnFillingCategory */
        $vatReturnFillingCategory = $this->vatReturnFillingCategoryRepository->findWithoutFail($id);

        if (empty($vatReturnFillingCategory)) {
            return $this->sendError(trans('custom.vat_return_filling_category_not_found'));
        }

        $vatReturnFillingCategory = $this->vatReturnFillingCategoryRepository->update($input, $id);

        return $this->sendResponse($vatReturnFillingCategory->toArray(), trans('custom.vatreturnfillingcategory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFillingCategories/{id}",
     *      summary="Remove the specified VatReturnFillingCategory from storage",
     *      tags={"VatReturnFillingCategory"},
     *      description="Delete VatReturnFillingCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFillingCategory",
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
        /** @var VatReturnFillingCategory $vatReturnFillingCategory */
        $vatReturnFillingCategory = $this->vatReturnFillingCategoryRepository->findWithoutFail($id);

        if (empty($vatReturnFillingCategory)) {
            return $this->sendError(trans('custom.vat_return_filling_category_not_found'));
        }

        $vatReturnFillingCategory->delete();

        return $this->sendSuccess('Vat Return Filling Category deleted successfully');
    }
}

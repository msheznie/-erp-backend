<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVatReturnFilledCategoryAPIRequest;
use App\Http\Requests\API\UpdateVatReturnFilledCategoryAPIRequest;
use App\Models\VatReturnFilledCategory;
use App\Repositories\VatReturnFilledCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class VatReturnFilledCategoryController
 * @package App\Http\Controllers\API
 */

class VatReturnFilledCategoryAPIController extends AppBaseController
{
    /** @var  VatReturnFilledCategoryRepository */
    private $vatReturnFilledCategoryRepository;

    public function __construct(VatReturnFilledCategoryRepository $vatReturnFilledCategoryRepo)
    {
        $this->vatReturnFilledCategoryRepository = $vatReturnFilledCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFilledCategories",
     *      summary="Get a listing of the VatReturnFilledCategories.",
     *      tags={"VatReturnFilledCategory"},
     *      description="Get all VatReturnFilledCategories",
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
     *                  @SWG\Items(ref="#/definitions/VatReturnFilledCategory")
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
        $this->vatReturnFilledCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->vatReturnFilledCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $vatReturnFilledCategories = $this->vatReturnFilledCategoryRepository->all();

        return $this->sendResponse($vatReturnFilledCategories->toArray(), trans('custom.vat_return_filled_categories_retrieved_successfull'));
    }

    /**
     * @param CreateVatReturnFilledCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/vatReturnFilledCategories",
     *      summary="Store a newly created VatReturnFilledCategory in storage",
     *      tags={"VatReturnFilledCategory"},
     *      description="Store VatReturnFilledCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFilledCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFilledCategory")
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
     *                  ref="#/definitions/VatReturnFilledCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateVatReturnFilledCategoryAPIRequest $request)
    {
        $input = $request->all();

        $vatReturnFilledCategory = $this->vatReturnFilledCategoryRepository->create($input);

        return $this->sendResponse($vatReturnFilledCategory->toArray(), trans('custom.vat_return_filled_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/vatReturnFilledCategories/{id}",
     *      summary="Display the specified VatReturnFilledCategory",
     *      tags={"VatReturnFilledCategory"},
     *      description="Get VatReturnFilledCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFilledCategory",
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
     *                  ref="#/definitions/VatReturnFilledCategory"
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
        /** @var VatReturnFilledCategory $vatReturnFilledCategory */
        $vatReturnFilledCategory = $this->vatReturnFilledCategoryRepository->findWithoutFail($id);

        if (empty($vatReturnFilledCategory)) {
            return $this->sendError(trans('custom.vat_return_filled_category_not_found'));
        }

        return $this->sendResponse($vatReturnFilledCategory->toArray(), trans('custom.vat_return_filled_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateVatReturnFilledCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/vatReturnFilledCategories/{id}",
     *      summary="Update the specified VatReturnFilledCategory in storage",
     *      tags={"VatReturnFilledCategory"},
     *      description="Update VatReturnFilledCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFilledCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="VatReturnFilledCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/VatReturnFilledCategory")
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
     *                  ref="#/definitions/VatReturnFilledCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateVatReturnFilledCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var VatReturnFilledCategory $vatReturnFilledCategory */
        $vatReturnFilledCategory = $this->vatReturnFilledCategoryRepository->findWithoutFail($id);

        if (empty($vatReturnFilledCategory)) {
            return $this->sendError(trans('custom.vat_return_filled_category_not_found'));
        }

        $vatReturnFilledCategory = $this->vatReturnFilledCategoryRepository->update($input, $id);

        return $this->sendResponse($vatReturnFilledCategory->toArray(), trans('custom.vatreturnfilledcategory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/vatReturnFilledCategories/{id}",
     *      summary="Remove the specified VatReturnFilledCategory from storage",
     *      tags={"VatReturnFilledCategory"},
     *      description="Delete VatReturnFilledCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of VatReturnFilledCategory",
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
        /** @var VatReturnFilledCategory $vatReturnFilledCategory */
        $vatReturnFilledCategory = $this->vatReturnFilledCategoryRepository->findWithoutFail($id);

        if (empty($vatReturnFilledCategory)) {
            return $this->sendError(trans('custom.vat_return_filled_category_not_found'));
        }

        $vatReturnFilledCategory->delete();

        return $this->sendSuccess('Vat Return Filled Category deleted successfully');
    }
}

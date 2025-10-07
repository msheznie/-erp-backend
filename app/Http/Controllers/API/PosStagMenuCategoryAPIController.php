<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePosStagMenuCategoryAPIRequest;
use App\Http\Requests\API\UpdatePosStagMenuCategoryAPIRequest;
use App\Models\PosStagMenuCategory;
use App\Repositories\PosStagMenuCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PosStagMenuCategoryController
 * @package App\Http\Controllers\API
 */

class PosStagMenuCategoryAPIController extends AppBaseController
{
    /** @var  PosStagMenuCategoryRepository */
    private $posStagMenuCategoryRepository;

    public function __construct(PosStagMenuCategoryRepository $posStagMenuCategoryRepo)
    {
        $this->posStagMenuCategoryRepository = $posStagMenuCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/posStagMenuCategories",
     *      summary="Get a listing of the PosStagMenuCategories.",
     *      tags={"PosStagMenuCategory"},
     *      description="Get all PosStagMenuCategories",
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
     *                  @SWG\Items(ref="#/definitions/PosStagMenuCategory")
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
        $this->posStagMenuCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->posStagMenuCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $posStagMenuCategories = $this->posStagMenuCategoryRepository->all();

        return $this->sendResponse($posStagMenuCategories->toArray(), trans('custom.pos_stag_menu_categories_retrieved_successfully'));
    }

    /**
     * @param CreatePosStagMenuCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posStagMenuCategories",
     *      summary="Store a newly created PosStagMenuCategory in storage",
     *      tags={"PosStagMenuCategory"},
     *      description="Store PosStagMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosStagMenuCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosStagMenuCategory")
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
     *                  ref="#/definitions/PosStagMenuCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePosStagMenuCategoryAPIRequest $request)
    {
        $input = $request->all();

        $posStagMenuCategory = $this->posStagMenuCategoryRepository->create($input);

        return $this->sendResponse($posStagMenuCategory->toArray(), trans('custom.pos_stag_menu_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/posStagMenuCategories/{id}",
     *      summary="Display the specified PosStagMenuCategory",
     *      tags={"PosStagMenuCategory"},
     *      description="Get PosStagMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosStagMenuCategory",
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
     *                  ref="#/definitions/PosStagMenuCategory"
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
        /** @var PosStagMenuCategory $posStagMenuCategory */
        $posStagMenuCategory = $this->posStagMenuCategoryRepository->findWithoutFail($id);

        if (empty($posStagMenuCategory)) {
            return $this->sendError(trans('custom.pos_stag_menu_category_not_found'));
        }

        return $this->sendResponse($posStagMenuCategory->toArray(), trans('custom.pos_stag_menu_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePosStagMenuCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/posStagMenuCategories/{id}",
     *      summary="Update the specified PosStagMenuCategory in storage",
     *      tags={"PosStagMenuCategory"},
     *      description="Update PosStagMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosStagMenuCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosStagMenuCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosStagMenuCategory")
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
     *                  ref="#/definitions/PosStagMenuCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePosStagMenuCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PosStagMenuCategory $posStagMenuCategory */
        $posStagMenuCategory = $this->posStagMenuCategoryRepository->findWithoutFail($id);

        if (empty($posStagMenuCategory)) {
            return $this->sendError(trans('custom.pos_stag_menu_category_not_found'));
        }

        $posStagMenuCategory = $this->posStagMenuCategoryRepository->update($input, $id);

        return $this->sendResponse($posStagMenuCategory->toArray(), trans('custom.posstagmenucategory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posStagMenuCategories/{id}",
     *      summary="Remove the specified PosStagMenuCategory from storage",
     *      tags={"PosStagMenuCategory"},
     *      description="Delete PosStagMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosStagMenuCategory",
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
        /** @var PosStagMenuCategory $posStagMenuCategory */
        $posStagMenuCategory = $this->posStagMenuCategoryRepository->findWithoutFail($id);

        if (empty($posStagMenuCategory)) {
            return $this->sendError(trans('custom.pos_stag_menu_category_not_found'));
        }

        $posStagMenuCategory->delete();

        return $this->sendSuccess('Pos Stag Menu Category deleted successfully');
    }
}

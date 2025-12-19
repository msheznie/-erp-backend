<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePosSourceMenuCategoryAPIRequest;
use App\Http\Requests\API\UpdatePosSourceMenuCategoryAPIRequest;
use App\Models\PosSourceMenuCategory;
use App\Repositories\PosSourceMenuCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PosSourceMenuCategoryController
 * @package App\Http\Controllers\API
 */

class PosSourceMenuCategoryAPIController extends AppBaseController
{
    /** @var  PosSourceMenuCategoryRepository */
    private $posSourceMenuCategoryRepository;

    public function __construct(PosSourceMenuCategoryRepository $posSourceMenuCategoryRepo)
    {
        $this->posSourceMenuCategoryRepository = $posSourceMenuCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/posSourceMenuCategories",
     *      summary="Get a listing of the PosSourceMenuCategories.",
     *      tags={"PosSourceMenuCategory"},
     *      description="Get all PosSourceMenuCategories",
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
     *                  @SWG\Items(ref="#/definitions/PosSourceMenuCategory")
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
        $this->posSourceMenuCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->posSourceMenuCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $posSourceMenuCategories = $this->posSourceMenuCategoryRepository->all();

        return $this->sendResponse($posSourceMenuCategories->toArray(), trans('custom.pos_source_menu_categories_retrieved_successfully'));
    }

    /**
     * @param CreatePosSourceMenuCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posSourceMenuCategories",
     *      summary="Store a newly created PosSourceMenuCategory in storage",
     *      tags={"PosSourceMenuCategory"},
     *      description="Store PosSourceMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosSourceMenuCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosSourceMenuCategory")
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
     *                  ref="#/definitions/PosSourceMenuCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePosSourceMenuCategoryAPIRequest $request)
    {
        $input = $request->all();

        $posSourceMenuCategory = $this->posSourceMenuCategoryRepository->create($input);

        return $this->sendResponse($posSourceMenuCategory->toArray(), trans('custom.pos_source_menu_category_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/posSourceMenuCategories/{id}",
     *      summary="Display the specified PosSourceMenuCategory",
     *      tags={"PosSourceMenuCategory"},
     *      description="Get PosSourceMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosSourceMenuCategory",
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
     *                  ref="#/definitions/PosSourceMenuCategory"
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
        /** @var PosSourceMenuCategory $posSourceMenuCategory */
        $posSourceMenuCategory = $this->posSourceMenuCategoryRepository->findWithoutFail($id);

        if (empty($posSourceMenuCategory)) {
            return $this->sendError(trans('custom.pos_source_menu_category_not_found'));
        }

        return $this->sendResponse($posSourceMenuCategory->toArray(), trans('custom.pos_source_menu_category_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePosSourceMenuCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/posSourceMenuCategories/{id}",
     *      summary="Update the specified PosSourceMenuCategory in storage",
     *      tags={"PosSourceMenuCategory"},
     *      description="Update PosSourceMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosSourceMenuCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PosSourceMenuCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PosSourceMenuCategory")
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
     *                  ref="#/definitions/PosSourceMenuCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePosSourceMenuCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PosSourceMenuCategory $posSourceMenuCategory */
        $posSourceMenuCategory = $this->posSourceMenuCategoryRepository->findWithoutFail($id);

        if (empty($posSourceMenuCategory)) {
            return $this->sendError(trans('custom.pos_source_menu_category_not_found'));
        }

        $posSourceMenuCategory = $this->posSourceMenuCategoryRepository->update($input, $id);

        return $this->sendResponse($posSourceMenuCategory->toArray(), trans('custom.possourcemenucategory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/posSourceMenuCategories/{id}",
     *      summary="Remove the specified PosSourceMenuCategory from storage",
     *      tags={"PosSourceMenuCategory"},
     *      description="Delete PosSourceMenuCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PosSourceMenuCategory",
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
        /** @var PosSourceMenuCategory $posSourceMenuCategory */
        $posSourceMenuCategory = $this->posSourceMenuCategoryRepository->findWithoutFail($id);

        if (empty($posSourceMenuCategory)) {
            return $this->sendError(trans('custom.pos_source_menu_category_not_found'));
        }

        $posSourceMenuCategory->delete();

        return $this->sendSuccess('Pos Source Menu Category deleted successfully');
    }
}

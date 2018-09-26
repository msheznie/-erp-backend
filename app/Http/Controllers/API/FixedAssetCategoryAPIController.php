<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFixedAssetCategoryAPIRequest;
use App\Http\Requests\API\UpdateFixedAssetCategoryAPIRequest;
use App\Models\FixedAssetCategory;
use App\Repositories\FixedAssetCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FixedAssetCategoryController
 * @package App\Http\Controllers\API
 */

class FixedAssetCategoryAPIController extends AppBaseController
{
    /** @var  FixedAssetCategoryRepository */
    private $fixedAssetCategoryRepository;

    public function __construct(FixedAssetCategoryRepository $fixedAssetCategoryRepo)
    {
        $this->fixedAssetCategoryRepository = $fixedAssetCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCategories",
     *      summary="Get a listing of the FixedAssetCategories.",
     *      tags={"FixedAssetCategory"},
     *      description="Get all FixedAssetCategories",
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
     *                  @SWG\Items(ref="#/definitions/FixedAssetCategory")
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
        $this->fixedAssetCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->fixedAssetCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fixedAssetCategories = $this->fixedAssetCategoryRepository->all();

        return $this->sendResponse($fixedAssetCategories->toArray(), 'Fixed Asset Categories retrieved successfully');
    }

    /**
     * @param CreateFixedAssetCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fixedAssetCategories",
     *      summary="Store a newly created FixedAssetCategory in storage",
     *      tags={"FixedAssetCategory"},
     *      description="Store FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCategory")
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
     *                  ref="#/definitions/FixedAssetCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFixedAssetCategoryAPIRequest $request)
    {
        $input = $request->all();

        $fixedAssetCategories = $this->fixedAssetCategoryRepository->create($input);

        return $this->sendResponse($fixedAssetCategories->toArray(), 'Fixed Asset Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fixedAssetCategories/{id}",
     *      summary="Display the specified FixedAssetCategory",
     *      tags={"FixedAssetCategory"},
     *      description="Get FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategory",
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
     *                  ref="#/definitions/FixedAssetCategory"
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
        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError('Fixed Asset Category not found');
        }

        return $this->sendResponse($fixedAssetCategory->toArray(), 'Fixed Asset Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateFixedAssetCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fixedAssetCategories/{id}",
     *      summary="Update the specified FixedAssetCategory in storage",
     *      tags={"FixedAssetCategory"},
     *      description="Update FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FixedAssetCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FixedAssetCategory")
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
     *                  ref="#/definitions/FixedAssetCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFixedAssetCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError('Fixed Asset Category not found');
        }

        $fixedAssetCategory = $this->fixedAssetCategoryRepository->update($input, $id);

        return $this->sendResponse($fixedAssetCategory->toArray(), 'FixedAssetCategory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fixedAssetCategories/{id}",
     *      summary="Remove the specified FixedAssetCategory from storage",
     *      tags={"FixedAssetCategory"},
     *      description="Delete FixedAssetCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FixedAssetCategory",
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
        /** @var FixedAssetCategory $fixedAssetCategory */
        $fixedAssetCategory = $this->fixedAssetCategoryRepository->findWithoutFail($id);

        if (empty($fixedAssetCategory)) {
            return $this->sendError('Fixed Asset Category not found');
        }

        $fixedAssetCategory->delete();

        return $this->sendResponse($id, 'Fixed Asset Category deleted successfully');
    }
}

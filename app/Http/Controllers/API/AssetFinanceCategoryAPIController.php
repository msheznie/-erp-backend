<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAssetFinanceCategoryAPIRequest;
use App\Http\Requests\API\UpdateAssetFinanceCategoryAPIRequest;
use App\Models\AssetFinanceCategory;
use App\Repositories\AssetFinanceCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AssetFinanceCategoryController
 * @package App\Http\Controllers\API
 */

class AssetFinanceCategoryAPIController extends AppBaseController
{
    /** @var  AssetFinanceCategoryRepository */
    private $assetFinanceCategoryRepository;

    public function __construct(AssetFinanceCategoryRepository $assetFinanceCategoryRepo)
    {
        $this->assetFinanceCategoryRepository = $assetFinanceCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetFinanceCategories",
     *      summary="Get a listing of the AssetFinanceCategories.",
     *      tags={"AssetFinanceCategory"},
     *      description="Get all AssetFinanceCategories",
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
     *                  @SWG\Items(ref="#/definitions/AssetFinanceCategory")
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
        $this->assetFinanceCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->assetFinanceCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $assetFinanceCategories = $this->assetFinanceCategoryRepository->all();

        return $this->sendResponse($assetFinanceCategories->toArray(), 'Asset Finance Categories retrieved successfully');
    }

    /**
     * @param CreateAssetFinanceCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/assetFinanceCategories",
     *      summary="Store a newly created AssetFinanceCategory in storage",
     *      tags={"AssetFinanceCategory"},
     *      description="Store AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetFinanceCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetFinanceCategory")
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
     *                  ref="#/definitions/AssetFinanceCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAssetFinanceCategoryAPIRequest $request)
    {
        $input = $request->all();

        $assetFinanceCategories = $this->assetFinanceCategoryRepository->create($input);

        return $this->sendResponse($assetFinanceCategories->toArray(), 'Asset Finance Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/assetFinanceCategories/{id}",
     *      summary="Display the specified AssetFinanceCategory",
     *      tags={"AssetFinanceCategory"},
     *      description="Get AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetFinanceCategory",
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
     *                  ref="#/definitions/AssetFinanceCategory"
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
        /** @var AssetFinanceCategory $assetFinanceCategory */
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            return $this->sendError('Asset Finance Category not found');
        }

        return $this->sendResponse($assetFinanceCategory->toArray(), 'Asset Finance Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAssetFinanceCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/assetFinanceCategories/{id}",
     *      summary="Update the specified AssetFinanceCategory in storage",
     *      tags={"AssetFinanceCategory"},
     *      description="Update AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetFinanceCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AssetFinanceCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AssetFinanceCategory")
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
     *                  ref="#/definitions/AssetFinanceCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAssetFinanceCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var AssetFinanceCategory $assetFinanceCategory */
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            return $this->sendError('Asset Finance Category not found');
        }

        $assetFinanceCategory = $this->assetFinanceCategoryRepository->update($input, $id);

        return $this->sendResponse($assetFinanceCategory->toArray(), 'AssetFinanceCategory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/assetFinanceCategories/{id}",
     *      summary="Remove the specified AssetFinanceCategory from storage",
     *      tags={"AssetFinanceCategory"},
     *      description="Delete AssetFinanceCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AssetFinanceCategory",
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
        /** @var AssetFinanceCategory $assetFinanceCategory */
        $assetFinanceCategory = $this->assetFinanceCategoryRepository->findWithoutFail($id);

        if (empty($assetFinanceCategory)) {
            return $this->sendError('Asset Finance Category not found');
        }

        $assetFinanceCategory->delete();

        return $this->sendResponse($id, 'Asset Finance Category deleted successfully');
    }

    public function getAllAssetFinanceCategory(Request $request){
            $this->assetFinanceCategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->assetFinanceCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
            //$assetFinanceCategories = $this->assetFinanceCategoryRepository->all();

            return \DataTables::of($this->assetFinanceCategoryRepository)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }
}

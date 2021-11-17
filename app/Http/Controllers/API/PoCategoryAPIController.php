<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoCategoryAPIRequest;
use App\Http\Requests\API\UpdatePoCategoryAPIRequest;
use App\Models\PoCategory;
use App\Repositories\PoCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PoCategoryController
 * @package App\Http\Controllers\API
 */

class PoCategoryAPIController extends AppBaseController
{
    /** @var  PoCategoryRepository */
    private $poCategoryRepository;

    public function __construct(PoCategoryRepository $poCategoryRepo)
    {
        $this->poCategoryRepository = $poCategoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/poCategories",
     *      summary="Get a listing of the PoCategories.",
     *      tags={"PoCategory"},
     *      description="Get all PoCategories",
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
     *                  @SWG\Items(ref="#/definitions/PoCategory")
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
        $this->poCategoryRepository->pushCriteria(new RequestCriteria($request));
        $this->poCategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poCategories = $this->poCategoryRepository->all();

        return $this->sendResponse($poCategories->toArray(), 'Po Categories retrieved successfully');
    }

    /**
     * @param CreatePoCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/poCategories",
     *      summary="Store a newly created PoCategory in storage",
     *      tags={"PoCategory"},
     *      description="Store PoCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoCategory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoCategory")
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
     *                  ref="#/definitions/PoCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoCategoryAPIRequest $request)
    {
        $input = $request->all();

        $poCategory = $this->poCategoryRepository->create($input);

        return $this->sendResponse($poCategory->toArray(), 'Po Category saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/poCategories/{id}",
     *      summary="Display the specified PoCategory",
     *      tags={"PoCategory"},
     *      description="Get PoCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoCategory",
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
     *                  ref="#/definitions/PoCategory"
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
        /** @var PoCategory $poCategory */
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            return $this->sendError('Po Category not found');
        }

        return $this->sendResponse($poCategory->toArray(), 'Po Category retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePoCategoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/poCategories/{id}",
     *      summary="Update the specified PoCategory in storage",
     *      tags={"PoCategory"},
     *      description="Update PoCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoCategory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoCategory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoCategory")
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
     *                  ref="#/definitions/PoCategory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoCategoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoCategory $poCategory */
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            return $this->sendError('Po Category not found');
        }

        $poCategory = $this->poCategoryRepository->update($input, $id);

        return $this->sendResponse($poCategory->toArray(), 'PoCategory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/poCategories/{id}",
     *      summary="Remove the specified PoCategory from storage",
     *      tags={"PoCategory"},
     *      description="Delete PoCategory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoCategory",
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
        /** @var PoCategory $poCategory */
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            return $this->sendError('Po Category not found');
        }

        $poCategory->delete();

        return $this->sendSuccess('Po Category deleted successfully');
    }
}

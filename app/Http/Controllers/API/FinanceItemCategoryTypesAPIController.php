<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFinanceItemCategoryTypesAPIRequest;
use App\Http\Requests\API\UpdateFinanceItemCategoryTypesAPIRequest;
use App\Models\FinanceItemCategoryTypes;
use App\Repositories\FinanceItemCategoryTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FinanceItemCategoryTypesController
 * @package App\Http\Controllers\API
 */

class FinanceItemCategoryTypesAPIController extends AppBaseController
{
    /** @var  FinanceItemCategoryTypesRepository */
    private $financeItemCategoryTypesRepository;

    public function __construct(FinanceItemCategoryTypesRepository $financeItemCategoryTypesRepo)
    {
        $this->financeItemCategoryTypesRepository = $financeItemCategoryTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/financeItemCategoryTypes",
     *      summary="getFinanceItemCategoryTypesList",
     *      tags={"FinanceItemCategoryTypes"},
     *      description="Get all FinanceItemCategoryTypes",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/FinanceItemCategoryTypes")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->financeItemCategoryTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->financeItemCategoryTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $financeItemCategoryTypes = $this->financeItemCategoryTypesRepository->all();

        return $this->sendResponse($financeItemCategoryTypes->toArray(), trans('custom.finance_item_category_types_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/financeItemCategoryTypes",
     *      summary="createFinanceItemCategoryTypes",
     *      tags={"FinanceItemCategoryTypes"},
     *      description="Create FinanceItemCategoryTypes",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/FinanceItemCategoryTypes"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFinanceItemCategoryTypesAPIRequest $request)
    {
        $input = $request->all();

        $financeItemCategoryTypes = $this->financeItemCategoryTypesRepository->create($input);

        return $this->sendResponse($financeItemCategoryTypes->toArray(), trans('custom.finance_item_category_types_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/financeItemCategoryTypes/{id}",
     *      summary="getFinanceItemCategoryTypesItem",
     *      tags={"FinanceItemCategoryTypes"},
     *      description="Get FinanceItemCategoryTypes",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinanceItemCategoryTypes",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/FinanceItemCategoryTypes"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var FinanceItemCategoryTypes $financeItemCategoryTypes */
        $financeItemCategoryTypes = $this->financeItemCategoryTypesRepository->findWithoutFail($id);

        if (empty($financeItemCategoryTypes)) {
            return $this->sendError(trans('custom.finance_item_category_types_not_found'));
        }

        return $this->sendResponse($financeItemCategoryTypes->toArray(), trans('custom.finance_item_category_types_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/financeItemCategoryTypes/{id}",
     *      summary="updateFinanceItemCategoryTypes",
     *      tags={"FinanceItemCategoryTypes"},
     *      description="Update FinanceItemCategoryTypes",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinanceItemCategoryTypes",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/FinanceItemCategoryTypes"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFinanceItemCategoryTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var FinanceItemCategoryTypes $financeItemCategoryTypes */
        $financeItemCategoryTypes = $this->financeItemCategoryTypesRepository->findWithoutFail($id);

        if (empty($financeItemCategoryTypes)) {
            return $this->sendError(trans('custom.finance_item_category_types_not_found'));
        }

        $financeItemCategoryTypes = $this->financeItemCategoryTypesRepository->update($input, $id);

        return $this->sendResponse($financeItemCategoryTypes->toArray(), trans('custom.financeitemcategorytypes_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/financeItemCategoryTypes/{id}",
     *      summary="deleteFinanceItemCategoryTypes",
     *      tags={"FinanceItemCategoryTypes"},
     *      description="Delete FinanceItemCategoryTypes",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of FinanceItemCategoryTypes",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var FinanceItemCategoryTypes $financeItemCategoryTypes */
        $financeItemCategoryTypes = $this->financeItemCategoryTypesRepository->findWithoutFail($id);

        if (empty($financeItemCategoryTypes)) {
            return $this->sendError(trans('custom.finance_item_category_types_not_found'));
        }

        $financeItemCategoryTypes->delete();

        return $this->sendSuccess('Finance Item Category Types deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemMasterCategoryTypeAPIRequest;
use App\Http\Requests\API\UpdateItemMasterCategoryTypeAPIRequest;
use App\Models\ItemMasterCategoryType;
use App\Repositories\ItemMasterCategoryTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemMasterCategoryTypeController
 * @package App\Http\Controllers\API
 */

class ItemMasterCategoryTypeAPIController extends AppBaseController
{
    /** @var  ItemMasterCategoryTypeRepository */
    private $itemMasterCategoryTypeRepository;

    public function __construct(ItemMasterCategoryTypeRepository $itemMasterCategoryTypeRepo)
    {
        $this->itemMasterCategoryTypeRepository = $itemMasterCategoryTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/itemMasterCategoryTypes",
     *      summary="getItemMasterCategoryTypeList",
     *      tags={"ItemMasterCategoryType"},
     *      description="Get all ItemMasterCategoryTypes",
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
     *                  @OA\Items(ref="#/definitions/ItemMasterCategoryType")
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
        $this->itemMasterCategoryTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->itemMasterCategoryTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemMasterCategoryTypes = $this->itemMasterCategoryTypeRepository->all();

        return $this->sendResponse($itemMasterCategoryTypes->toArray(), trans('custom.item_master_category_types_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/itemMasterCategoryTypes",
     *      summary="createItemMasterCategoryType",
     *      tags={"ItemMasterCategoryType"},
     *      description="Create ItemMasterCategoryType",
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
     *                  ref="#/definitions/ItemMasterCategoryType"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemMasterCategoryTypeAPIRequest $request)
    {
        $input = $request->all();

        $itemMasterCategoryType = $this->itemMasterCategoryTypeRepository->create($input);

        return $this->sendResponse($itemMasterCategoryType->toArray(), trans('custom.item_master_category_type_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/itemMasterCategoryTypes/{id}",
     *      summary="getItemMasterCategoryTypeItem",
     *      tags={"ItemMasterCategoryType"},
     *      description="Get ItemMasterCategoryType",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemMasterCategoryType",
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
     *                  ref="#/definitions/ItemMasterCategoryType"
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
        /** @var ItemMasterCategoryType $itemMasterCategoryType */
        $itemMasterCategoryType = $this->itemMasterCategoryTypeRepository->findWithoutFail($id);

        if (empty($itemMasterCategoryType)) {
            return $this->sendError(trans('custom.item_master_category_type_not_found'));
        }

        return $this->sendResponse($itemMasterCategoryType->toArray(), trans('custom.item_master_category_type_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/itemMasterCategoryTypes/{id}",
     *      summary="updateItemMasterCategoryType",
     *      tags={"ItemMasterCategoryType"},
     *      description="Update ItemMasterCategoryType",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemMasterCategoryType",
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
     *                  ref="#/definitions/ItemMasterCategoryType"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemMasterCategoryTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemMasterCategoryType $itemMasterCategoryType */
        $itemMasterCategoryType = $this->itemMasterCategoryTypeRepository->findWithoutFail($id);

        if (empty($itemMasterCategoryType)) {
            return $this->sendError(trans('custom.item_master_category_type_not_found'));
        }

        $itemMasterCategoryType = $this->itemMasterCategoryTypeRepository->update($input, $id);

        return $this->sendResponse($itemMasterCategoryType->toArray(), trans('custom.itemmastercategorytype_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/itemMasterCategoryTypes/{id}",
     *      summary="deleteItemMasterCategoryType",
     *      tags={"ItemMasterCategoryType"},
     *      description="Delete ItemMasterCategoryType",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemMasterCategoryType",
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
        /** @var ItemMasterCategoryType $itemMasterCategoryType */
        $itemMasterCategoryType = $this->itemMasterCategoryTypeRepository->findWithoutFail($id);

        if (empty($itemMasterCategoryType)) {
            return $this->sendError(trans('custom.item_master_category_type_not_found'));
        }

        $itemMasterCategoryType->delete();

        return $this->sendSuccess('Item Master Category Type deleted successfully');
    }
}

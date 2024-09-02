<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemCategoryTypeMasterAPIRequest;
use App\Http\Requests\API\UpdateItemCategoryTypeMasterAPIRequest;
use App\Models\ItemCategoryTypeMaster;
use App\Repositories\ItemCategoryTypeMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemCategoryTypeMasterController
 * @package App\Http\Controllers\API
 */

class ItemCategoryTypeMasterAPIController extends AppBaseController
{
    /** @var  ItemCategoryTypeMasterRepository */
    private $itemCategoryTypeMasterRepository;

    public function __construct(ItemCategoryTypeMasterRepository $itemCategoryTypeMasterRepo)
    {
        $this->itemCategoryTypeMasterRepository = $itemCategoryTypeMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/itemCategoryTypeMasters",
     *      summary="getItemCategoryTypeMasterList",
     *      tags={"ItemCategoryTypeMaster"},
     *      description="Get all ItemCategoryTypeMasters",
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
     *                  @OA\Items(ref="#/definitions/ItemCategoryTypeMaster")
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
        $this->itemCategoryTypeMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemCategoryTypeMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemCategoryTypeMasters = $this->itemCategoryTypeMasterRepository->all();

        return $this->sendResponse($itemCategoryTypeMasters->toArray(), 'Item Category Type Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/itemCategoryTypeMasters",
     *      summary="createItemCategoryTypeMaster",
     *      tags={"ItemCategoryTypeMaster"},
     *      description="Create ItemCategoryTypeMaster",
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
     *                  ref="#/definitions/ItemCategoryTypeMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemCategoryTypeMasterAPIRequest $request)
    {
        $input = $request->all();

        $itemCategoryTypeMaster = $this->itemCategoryTypeMasterRepository->create($input);

        return $this->sendResponse($itemCategoryTypeMaster->toArray(), 'Item Category Type Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/itemCategoryTypeMasters/{id}",
     *      summary="getItemCategoryTypeMasterItem",
     *      tags={"ItemCategoryTypeMaster"},
     *      description="Get ItemCategoryTypeMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemCategoryTypeMaster",
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
     *                  ref="#/definitions/ItemCategoryTypeMaster"
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
        /** @var ItemCategoryTypeMaster $itemCategoryTypeMaster */
        $itemCategoryTypeMaster = $this->itemCategoryTypeMasterRepository->findWithoutFail($id);

        if (empty($itemCategoryTypeMaster)) {
            return $this->sendError('Item Category Type Master not found');
        }

        return $this->sendResponse($itemCategoryTypeMaster->toArray(), 'Item Category Type Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/itemCategoryTypeMasters/{id}",
     *      summary="updateItemCategoryTypeMaster",
     *      tags={"ItemCategoryTypeMaster"},
     *      description="Update ItemCategoryTypeMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemCategoryTypeMaster",
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
     *                  ref="#/definitions/ItemCategoryTypeMaster"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemCategoryTypeMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemCategoryTypeMaster $itemCategoryTypeMaster */
        $itemCategoryTypeMaster = $this->itemCategoryTypeMasterRepository->findWithoutFail($id);

        if (empty($itemCategoryTypeMaster)) {
            return $this->sendError('Item Category Type Master not found');
        }

        $itemCategoryTypeMaster = $this->itemCategoryTypeMasterRepository->update($input, $id);

        return $this->sendResponse($itemCategoryTypeMaster->toArray(), 'ItemCategoryTypeMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/itemCategoryTypeMasters/{id}",
     *      summary="deleteItemCategoryTypeMaster",
     *      tags={"ItemCategoryTypeMaster"},
     *      description="Delete ItemCategoryTypeMaster",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemCategoryTypeMaster",
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
        /** @var ItemCategoryTypeMaster $itemCategoryTypeMaster */
        $itemCategoryTypeMaster = $this->itemCategoryTypeMasterRepository->findWithoutFail($id);

        if (empty($itemCategoryTypeMaster)) {
            return $this->sendError('Item Category Type Master not found');
        }

        $itemCategoryTypeMaster->delete();

        return $this->sendSuccess('Item Category Type Master deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemCategoryTypeMasterTranslationAPIRequest;
use App\Http\Requests\API\UpdateItemCategoryTypeMasterTranslationAPIRequest;
use App\Models\ItemCategoryTypeMasterTranslation;
use App\Repositories\ItemCategoryTypeMasterTranslationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemCategoryTypeMasterTranslationController
 * @package App\Http\Controllers\API
 */

class ItemCategoryTypeMasterTranslationAPIController extends AppBaseController
{
    /** @var  ItemCategoryTypeMasterTranslationRepository */
    private $itemCategoryTypeMasterTranslationRepository;

    public function __construct(ItemCategoryTypeMasterTranslationRepository $itemCategoryTypeMasterTranslationRepo)
    {
        $this->itemCategoryTypeMasterTranslationRepository = $itemCategoryTypeMasterTranslationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/itemCategoryTypeMasterTranslations",
     *      summary="getItemCategoryTypeMasterTranslationList",
     *      tags={"ItemCategoryTypeMasterTranslation"},
     *      description="Get all ItemCategoryTypeMasterTranslations",
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
     *                  @OA\Items(ref="#/definitions/ItemCategoryTypeMasterTranslation")
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
        $this->itemCategoryTypeMasterTranslationRepository->pushCriteria(new RequestCriteria($request));
        $this->itemCategoryTypeMasterTranslationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemCategoryTypeMasterTranslations = $this->itemCategoryTypeMasterTranslationRepository->all();

        return $this->sendResponse($itemCategoryTypeMasterTranslations->toArray(), 'Item Category Type Master Translations retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/itemCategoryTypeMasterTranslations",
     *      summary="createItemCategoryTypeMasterTranslation",
     *      tags={"ItemCategoryTypeMasterTranslation"},
     *      description="Create ItemCategoryTypeMasterTranslation",
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
     *                  ref="#/definitions/ItemCategoryTypeMasterTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemCategoryTypeMasterTranslationAPIRequest $request)
    {
        $input = $request->all();

        $itemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepository->create($input);

        return $this->sendResponse($itemCategoryTypeMasterTranslation->toArray(), 'Item Category Type Master Translation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/itemCategoryTypeMasterTranslations/{id}",
     *      summary="getItemCategoryTypeMasterTranslationItem",
     *      tags={"ItemCategoryTypeMasterTranslation"},
     *      description="Get ItemCategoryTypeMasterTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemCategoryTypeMasterTranslation",
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
     *                  ref="#/definitions/ItemCategoryTypeMasterTranslation"
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
        /** @var ItemCategoryTypeMasterTranslation $itemCategoryTypeMasterTranslation */
        $itemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepository->findWithoutFail($id);

        if (empty($itemCategoryTypeMasterTranslation)) {
            return $this->sendError('Item Category Type Master Translation not found');
        }

        return $this->sendResponse($itemCategoryTypeMasterTranslation->toArray(), 'Item Category Type Master Translation retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/itemCategoryTypeMasterTranslations/{id}",
     *      summary="updateItemCategoryTypeMasterTranslation",
     *      tags={"ItemCategoryTypeMasterTranslation"},
     *      description="Update ItemCategoryTypeMasterTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemCategoryTypeMasterTranslation",
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
     *                  ref="#/definitions/ItemCategoryTypeMasterTranslation"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemCategoryTypeMasterTranslationAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemCategoryTypeMasterTranslation $itemCategoryTypeMasterTranslation */
        $itemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepository->findWithoutFail($id);

        if (empty($itemCategoryTypeMasterTranslation)) {
            return $this->sendError('Item Category Type Master Translation not found');
        }

        $itemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepository->update($input, $id);

        return $this->sendResponse($itemCategoryTypeMasterTranslation->toArray(), 'ItemCategoryTypeMasterTranslation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/itemCategoryTypeMasterTranslations/{id}",
     *      summary="deleteItemCategoryTypeMasterTranslation",
     *      tags={"ItemCategoryTypeMasterTranslation"},
     *      description="Delete ItemCategoryTypeMasterTranslation",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ItemCategoryTypeMasterTranslation",
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
        /** @var ItemCategoryTypeMasterTranslation $itemCategoryTypeMasterTranslation */
        $itemCategoryTypeMasterTranslation = $this->itemCategoryTypeMasterTranslationRepository->findWithoutFail($id);

        if (empty($itemCategoryTypeMasterTranslation)) {
            return $this->sendError('Item Category Type Master Translation not found');
        }

        $itemCategoryTypeMasterTranslation->delete();

        return $this->sendSuccess('Item Category Type Master Translation deleted successfully');
    }
}

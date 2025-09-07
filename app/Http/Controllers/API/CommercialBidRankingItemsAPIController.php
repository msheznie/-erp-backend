<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCommercialBidRankingItemsAPIRequest;
use App\Http\Requests\API\UpdateCommercialBidRankingItemsAPIRequest;
use App\Models\CommercialBidRankingItems;
use App\Repositories\CommercialBidRankingItemsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CommercialBidRankingItemsController
 * @package App\Http\Controllers\API
 */

class CommercialBidRankingItemsAPIController extends AppBaseController
{
    /** @var  CommercialBidRankingItemsRepository */
    private $commercialBidRankingItemsRepository;

    public function __construct(CommercialBidRankingItemsRepository $commercialBidRankingItemsRepo)
    {
        $this->commercialBidRankingItemsRepository = $commercialBidRankingItemsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/commercialBidRankingItems",
     *      summary="getCommercialBidRankingItemsList",
     *      tags={"CommercialBidRankingItems"},
     *      description="Get all CommercialBidRankingItems",
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
     *                  @OA\Items(ref="#/definitions/CommercialBidRankingItems")
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
        $this->commercialBidRankingItemsRepository->pushCriteria(new RequestCriteria($request));
        $this->commercialBidRankingItemsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->all();

        return $this->sendResponse($commercialBidRankingItems->toArray(), trans('custom.commercial_bid_ranking_items_retrieved_successfull'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/commercialBidRankingItems",
     *      summary="createCommercialBidRankingItems",
     *      tags={"CommercialBidRankingItems"},
     *      description="Create CommercialBidRankingItems",
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
     *                  ref="#/definitions/CommercialBidRankingItems"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCommercialBidRankingItemsAPIRequest $request)
    {
        $input = $request->all();

        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->create($input);

        return $this->sendResponse($commercialBidRankingItems->toArray(), trans('custom.commercial_bid_ranking_items_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/commercialBidRankingItems/{id}",
     *      summary="getCommercialBidRankingItemsItem",
     *      tags={"CommercialBidRankingItems"},
     *      description="Get CommercialBidRankingItems",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CommercialBidRankingItems",
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
     *                  ref="#/definitions/CommercialBidRankingItems"
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
        /** @var CommercialBidRankingItems $commercialBidRankingItems */
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            return $this->sendError(trans('custom.commercial_bid_ranking_items_not_found'));
        }

        return $this->sendResponse($commercialBidRankingItems->toArray(), trans('custom.commercial_bid_ranking_items_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/commercialBidRankingItems/{id}",
     *      summary="updateCommercialBidRankingItems",
     *      tags={"CommercialBidRankingItems"},
     *      description="Update CommercialBidRankingItems",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CommercialBidRankingItems",
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
     *                  ref="#/definitions/CommercialBidRankingItems"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCommercialBidRankingItemsAPIRequest $request)
    {
        $input = $request->all();

        /** @var CommercialBidRankingItems $commercialBidRankingItems */
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            return $this->sendError(trans('custom.commercial_bid_ranking_items_not_found'));
        }

        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->update($input, $id);

        return $this->sendResponse($commercialBidRankingItems->toArray(), trans('custom.commercialbidrankingitems_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/commercialBidRankingItems/{id}",
     *      summary="deleteCommercialBidRankingItems",
     *      tags={"CommercialBidRankingItems"},
     *      description="Delete CommercialBidRankingItems",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CommercialBidRankingItems",
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
        /** @var CommercialBidRankingItems $commercialBidRankingItems */
        $commercialBidRankingItems = $this->commercialBidRankingItemsRepository->findWithoutFail($id);

        if (empty($commercialBidRankingItems)) {
            return $this->sendError(trans('custom.commercial_bid_ranking_items_not_found'));
        }

        $commercialBidRankingItems->delete();

        return $this->sendSuccess('Commercial Bid Ranking Items deleted successfully');
    }
}

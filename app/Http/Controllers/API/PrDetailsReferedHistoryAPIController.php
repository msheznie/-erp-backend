<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePrDetailsReferedHistoryAPIRequest;
use App\Http\Requests\API\UpdatePrDetailsReferedHistoryAPIRequest;
use App\Models\PrDetailsReferedHistory;
use App\Repositories\PrDetailsReferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PrDetailsReferedHistoryController
 * @package App\Http\Controllers\API
 */

class PrDetailsReferedHistoryAPIController extends AppBaseController
{
    /** @var  PrDetailsReferedHistoryRepository */
    private $prDetailsReferedHistoryRepository;

    public function __construct(PrDetailsReferedHistoryRepository $prDetailsReferedHistoryRepo)
    {
        $this->prDetailsReferedHistoryRepository = $prDetailsReferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/prDetailsReferedHistories",
     *      summary="Get a listing of the PrDetailsReferedHistories.",
     *      tags={"PrDetailsReferedHistory"},
     *      description="Get all PrDetailsReferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/PrDetailsReferedHistory")
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
        $this->prDetailsReferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->prDetailsReferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $prDetailsReferedHistories = $this->prDetailsReferedHistoryRepository->all();

        return $this->sendResponse($prDetailsReferedHistories->toArray(), 'Pr Details Refered Histories retrieved successfully');
    }

    /**
     * @param CreatePrDetailsReferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/prDetailsReferedHistories",
     *      summary="Store a newly created PrDetailsReferedHistory in storage",
     *      tags={"PrDetailsReferedHistory"},
     *      description="Store PrDetailsReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PrDetailsReferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PrDetailsReferedHistory")
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
     *                  ref="#/definitions/PrDetailsReferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePrDetailsReferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $prDetailsReferedHistories = $this->prDetailsReferedHistoryRepository->create($input);

        return $this->sendResponse($prDetailsReferedHistories->toArray(), 'Pr Details Refered History saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/prDetailsReferedHistories/{id}",
     *      summary="Display the specified PrDetailsReferedHistory",
     *      tags={"PrDetailsReferedHistory"},
     *      description="Get PrDetailsReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrDetailsReferedHistory",
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
     *                  ref="#/definitions/PrDetailsReferedHistory"
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
        /** @var PrDetailsReferedHistory $prDetailsReferedHistory */
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            return $this->sendError('Pr Details Refered History not found');
        }

        return $this->sendResponse($prDetailsReferedHistory->toArray(), 'Pr Details Refered History retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePrDetailsReferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/prDetailsReferedHistories/{id}",
     *      summary="Update the specified PrDetailsReferedHistory in storage",
     *      tags={"PrDetailsReferedHistory"},
     *      description="Update PrDetailsReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrDetailsReferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PrDetailsReferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PrDetailsReferedHistory")
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
     *                  ref="#/definitions/PrDetailsReferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePrDetailsReferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PrDetailsReferedHistory $prDetailsReferedHistory */
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            return $this->sendError('Pr Details Refered History not found');
        }

        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->update($input, $id);

        return $this->sendResponse($prDetailsReferedHistory->toArray(), 'PrDetailsReferedHistory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/prDetailsReferedHistories/{id}",
     *      summary="Remove the specified PrDetailsReferedHistory from storage",
     *      tags={"PrDetailsReferedHistory"},
     *      description="Delete PrDetailsReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PrDetailsReferedHistory",
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
        /** @var PrDetailsReferedHistory $prDetailsReferedHistory */
        $prDetailsReferedHistory = $this->prDetailsReferedHistoryRepository->findWithoutFail($id);

        if (empty($prDetailsReferedHistory)) {
            return $this->sendError('Pr Details Refered History not found');
        }

        $prDetailsReferedHistory->delete();

        return $this->sendResponse($id, 'Pr Details Refered History deleted successfully');
    }
}

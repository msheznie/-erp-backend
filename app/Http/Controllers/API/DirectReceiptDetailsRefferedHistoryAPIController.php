<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectReceiptDetailsRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateDirectReceiptDetailsRefferedHistoryAPIRequest;
use App\Models\DirectReceiptDetailsRefferedHistory;
use App\Repositories\DirectReceiptDetailsRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectReceiptDetailsRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class DirectReceiptDetailsRefferedHistoryAPIController extends AppBaseController
{
    /** @var  DirectReceiptDetailsRefferedHistoryRepository */
    private $directReceiptDetailsRefferedHistoryRepository;

    public function __construct(DirectReceiptDetailsRefferedHistoryRepository $directReceiptDetailsRefferedHistoryRepo)
    {
        $this->directReceiptDetailsRefferedHistoryRepository = $directReceiptDetailsRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directReceiptDetailsRefferedHistories",
     *      summary="Get a listing of the DirectReceiptDetailsRefferedHistories.",
     *      tags={"DirectReceiptDetailsRefferedHistory"},
     *      description="Get all DirectReceiptDetailsRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/DirectReceiptDetailsRefferedHistory")
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
        $this->directReceiptDetailsRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->directReceiptDetailsRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directReceiptDetailsRefferedHistories = $this->directReceiptDetailsRefferedHistoryRepository->all();

        return $this->sendResponse($directReceiptDetailsRefferedHistories->toArray(), 'Direct Receipt Details Reffered Histories retrieved successfully');
    }

    /**
     * @param CreateDirectReceiptDetailsRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directReceiptDetailsRefferedHistories",
     *      summary="Store a newly created DirectReceiptDetailsRefferedHistory in storage",
     *      tags={"DirectReceiptDetailsRefferedHistory"},
     *      description="Store DirectReceiptDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectReceiptDetailsRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectReceiptDetailsRefferedHistory")
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
     *                  ref="#/definitions/DirectReceiptDetailsRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectReceiptDetailsRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $directReceiptDetailsRefferedHistories = $this->directReceiptDetailsRefferedHistoryRepository->create($input);

        return $this->sendResponse($directReceiptDetailsRefferedHistories->toArray(), 'Direct Receipt Details Reffered History saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directReceiptDetailsRefferedHistories/{id}",
     *      summary="Display the specified DirectReceiptDetailsRefferedHistory",
     *      tags={"DirectReceiptDetailsRefferedHistory"},
     *      description="Get DirectReceiptDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetailsRefferedHistory",
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
     *                  ref="#/definitions/DirectReceiptDetailsRefferedHistory"
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
        /** @var DirectReceiptDetailsRefferedHistory $directReceiptDetailsRefferedHistory */
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            return $this->sendError('Direct Receipt Details Reffered History not found');
        }

        return $this->sendResponse($directReceiptDetailsRefferedHistory->toArray(), 'Direct Receipt Details Reffered History retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectReceiptDetailsRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directReceiptDetailsRefferedHistories/{id}",
     *      summary="Update the specified DirectReceiptDetailsRefferedHistory in storage",
     *      tags={"DirectReceiptDetailsRefferedHistory"},
     *      description="Update DirectReceiptDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetailsRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectReceiptDetailsRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectReceiptDetailsRefferedHistory")
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
     *                  ref="#/definitions/DirectReceiptDetailsRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectReceiptDetailsRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectReceiptDetailsRefferedHistory $directReceiptDetailsRefferedHistory */
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            return $this->sendError('Direct Receipt Details Reffered History not found');
        }

        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($directReceiptDetailsRefferedHistory->toArray(), 'DirectReceiptDetailsRefferedHistory updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directReceiptDetailsRefferedHistories/{id}",
     *      summary="Remove the specified DirectReceiptDetailsRefferedHistory from storage",
     *      tags={"DirectReceiptDetailsRefferedHistory"},
     *      description="Delete DirectReceiptDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectReceiptDetailsRefferedHistory",
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
        /** @var DirectReceiptDetailsRefferedHistory $directReceiptDetailsRefferedHistory */
        $directReceiptDetailsRefferedHistory = $this->directReceiptDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($directReceiptDetailsRefferedHistory)) {
            return $this->sendError('Direct Receipt Details Reffered History not found');
        }

        $directReceiptDetailsRefferedHistory->delete();

        return $this->sendResponse($id, 'Direct Receipt Details Reffered History deleted successfully');
    }


    public function getRVDetailDirectAmendHistory(Request $request)
    {
        $input = $request->all();
        $directReceiptAutoID = $input['directReceiptAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = DirectReceiptDetailsRefferedHistory::where('directReceiptAutoID', $directReceiptAutoID)
            ->where('timesReferred', $timesReferred)
            ->with(['segment'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Purchase Order Details Reffered History retrieved successfully');
    }
}

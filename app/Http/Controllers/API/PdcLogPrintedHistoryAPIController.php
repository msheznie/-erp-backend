<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePdcLogPrintedHistoryAPIRequest;
use App\Http\Requests\API\UpdatePdcLogPrintedHistoryAPIRequest;
use App\Models\PdcLogPrintedHistory;
use App\Repositories\PdcLogPrintedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PdcLogPrintedHistoryController
 * @package App\Http\Controllers\API
 */

class PdcLogPrintedHistoryAPIController extends AppBaseController
{
    /** @var  PdcLogPrintedHistoryRepository */
    private $pdcLogPrintedHistoryRepository;

    public function __construct(PdcLogPrintedHistoryRepository $pdcLogPrintedHistoryRepo)
    {
        $this->pdcLogPrintedHistoryRepository = $pdcLogPrintedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/pdcLogPrintedHistories",
     *      summary="getPdcLogPrintedHistoryList",
     *      tags={"PdcLogPrintedHistory"},
     *      description="Get all PdcLogPrintedHistories",
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
     *                  @OA\Items(ref="#/definitions/PdcLogPrintedHistory")
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
        $this->pdcLogPrintedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->pdcLogPrintedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pdcLogPrintedHistories = $this->pdcLogPrintedHistoryRepository->all();

        return $this->sendResponse($pdcLogPrintedHistories->toArray(), trans('custom.pdc_log_printed_histories_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/pdcLogPrintedHistories",
     *      summary="createPdcLogPrintedHistory",
     *      tags={"PdcLogPrintedHistory"},
     *      description="Create PdcLogPrintedHistory",
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
     *                  ref="#/definitions/PdcLogPrintedHistory"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePdcLogPrintedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $pdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepository->create($input);

        return $this->sendResponse($pdcLogPrintedHistory->toArray(), trans('custom.pdc_log_printed_history_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/pdcLogPrintedHistories/{id}",
     *      summary="getPdcLogPrintedHistoryItem",
     *      tags={"PdcLogPrintedHistory"},
     *      description="Get PdcLogPrintedHistory",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PdcLogPrintedHistory",
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
     *                  ref="#/definitions/PdcLogPrintedHistory"
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
        /** @var PdcLogPrintedHistory $pdcLogPrintedHistory */
        $pdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepository->findWithoutFail($id);

        if (empty($pdcLogPrintedHistory)) {
            return $this->sendError(trans('custom.pdc_log_printed_history_not_found'));
        }

        return $this->sendResponse($pdcLogPrintedHistory->toArray(), trans('custom.pdc_log_printed_history_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/pdcLogPrintedHistories/{id}",
     *      summary="updatePdcLogPrintedHistory",
     *      tags={"PdcLogPrintedHistory"},
     *      description="Update PdcLogPrintedHistory",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PdcLogPrintedHistory",
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
     *                  ref="#/definitions/PdcLogPrintedHistory"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePdcLogPrintedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PdcLogPrintedHistory $pdcLogPrintedHistory */
        $pdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepository->findWithoutFail($id);

        if (empty($pdcLogPrintedHistory)) {
            return $this->sendError(trans('custom.pdc_log_printed_history_not_found'));
        }

        $pdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepository->update($input, $id);

        return $this->sendResponse($pdcLogPrintedHistory->toArray(), trans('custom.pdclogprintedhistory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/pdcLogPrintedHistories/{id}",
     *      summary="deletePdcLogPrintedHistory",
     *      tags={"PdcLogPrintedHistory"},
     *      description="Delete PdcLogPrintedHistory",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PdcLogPrintedHistory",
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
        /** @var PdcLogPrintedHistory $pdcLogPrintedHistory */
        $pdcLogPrintedHistory = $this->pdcLogPrintedHistoryRepository->findWithoutFail($id);

        if (empty($pdcLogPrintedHistory)) {
            return $this->sendError(trans('custom.pdc_log_printed_history_not_found'));
        }

        $pdcLogPrintedHistory->delete();

        return $this->sendSuccess('Pdc Log Printed History deleted successfully');
    }
}

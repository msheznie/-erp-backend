<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateScheduleBidFormatDetailsLogAPIRequest;
use App\Http\Requests\API\UpdateScheduleBidFormatDetailsLogAPIRequest;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Repositories\ScheduleBidFormatDetailsLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ScheduleBidFormatDetailsLogController
 * @package App\Http\Controllers\API
 */

class ScheduleBidFormatDetailsLogAPIController extends AppBaseController
{
    /** @var  ScheduleBidFormatDetailsLogRepository */
    private $scheduleBidFormatDetailsLogRepository;

    public function __construct(ScheduleBidFormatDetailsLogRepository $scheduleBidFormatDetailsLogRepo)
    {
        $this->scheduleBidFormatDetailsLogRepository = $scheduleBidFormatDetailsLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/scheduleBidFormatDetailsLogs",
     *      summary="getScheduleBidFormatDetailsLogList",
     *      tags={"ScheduleBidFormatDetailsLog"},
     *      description="Get all ScheduleBidFormatDetailsLogs",
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
     *                  @OA\Items(ref="#/definitions/ScheduleBidFormatDetailsLog")
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
        $this->scheduleBidFormatDetailsLogRepository->pushCriteria(new RequestCriteria($request));
        $this->scheduleBidFormatDetailsLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $scheduleBidFormatDetailsLogs = $this->scheduleBidFormatDetailsLogRepository->all();

        return $this->sendResponse($scheduleBidFormatDetailsLogs->toArray(), trans('custom.schedule_bid_format_details_logs_retrieved_success'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/scheduleBidFormatDetailsLogs",
     *      summary="createScheduleBidFormatDetailsLog",
     *      tags={"ScheduleBidFormatDetailsLog"},
     *      description="Create ScheduleBidFormatDetailsLog",
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
     *                  ref="#/definitions/ScheduleBidFormatDetailsLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateScheduleBidFormatDetailsLogAPIRequest $request)
    {
        $input = $request->all();

        $scheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepository->create($input);

        return $this->sendResponse($scheduleBidFormatDetailsLog->toArray(), trans('custom.schedule_bid_format_details_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/scheduleBidFormatDetailsLogs/{id}",
     *      summary="getScheduleBidFormatDetailsLogItem",
     *      tags={"ScheduleBidFormatDetailsLog"},
     *      description="Get ScheduleBidFormatDetailsLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ScheduleBidFormatDetailsLog",
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
     *                  ref="#/definitions/ScheduleBidFormatDetailsLog"
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
        /** @var ScheduleBidFormatDetailsLog $scheduleBidFormatDetailsLog */
        $scheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepository->findWithoutFail($id);

        if (empty($scheduleBidFormatDetailsLog)) {
            return $this->sendError(trans('custom.schedule_bid_format_details_log_not_found'));
        }

        return $this->sendResponse($scheduleBidFormatDetailsLog->toArray(), trans('custom.schedule_bid_format_details_log_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/scheduleBidFormatDetailsLogs/{id}",
     *      summary="updateScheduleBidFormatDetailsLog",
     *      tags={"ScheduleBidFormatDetailsLog"},
     *      description="Update ScheduleBidFormatDetailsLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ScheduleBidFormatDetailsLog",
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
     *                  ref="#/definitions/ScheduleBidFormatDetailsLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateScheduleBidFormatDetailsLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var ScheduleBidFormatDetailsLog $scheduleBidFormatDetailsLog */
        $scheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepository->findWithoutFail($id);

        if (empty($scheduleBidFormatDetailsLog)) {
            return $this->sendError(trans('custom.schedule_bid_format_details_log_not_found'));
        }

        $scheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepository->update($input, $id);

        return $this->sendResponse($scheduleBidFormatDetailsLog->toArray(), trans('custom.schedulebidformatdetailslog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/scheduleBidFormatDetailsLogs/{id}",
     *      summary="deleteScheduleBidFormatDetailsLog",
     *      tags={"ScheduleBidFormatDetailsLog"},
     *      description="Delete ScheduleBidFormatDetailsLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ScheduleBidFormatDetailsLog",
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
        /** @var ScheduleBidFormatDetailsLog $scheduleBidFormatDetailsLog */
        $scheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepository->findWithoutFail($id);

        if (empty($scheduleBidFormatDetailsLog)) {
            return $this->sendError(trans('custom.schedule_bid_format_details_log_not_found'));
        }

        $scheduleBidFormatDetailsLog->delete();

        return $this->sendSuccess('Schedule Bid Format Details Log deleted successfully');
    }
}

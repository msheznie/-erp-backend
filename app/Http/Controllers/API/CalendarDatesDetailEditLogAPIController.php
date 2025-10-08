<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCalendarDatesDetailEditLogAPIRequest;
use App\Http\Requests\API\UpdateCalendarDatesDetailEditLogAPIRequest;
use App\Models\CalendarDatesDetailEditLog;
use App\Repositories\CalendarDatesDetailEditLogRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CalendarDatesDetailEditLogController
 * @package App\Http\Controllers\API
 */

class CalendarDatesDetailEditLogAPIController extends AppBaseController
{
    /** @var  CalendarDatesDetailEditLogRepository */
    private $calendarDatesDetailEditLogRepository;

    public function __construct(CalendarDatesDetailEditLogRepository $calendarDatesDetailEditLogRepo)
    {
        $this->calendarDatesDetailEditLogRepository = $calendarDatesDetailEditLogRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/calendarDatesDetailEditLogs",
     *      summary="getCalendarDatesDetailEditLogList",
     *      tags={"CalendarDatesDetailEditLog"},
     *      description="Get all CalendarDatesDetailEditLogs",
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
     *                  @OA\Items(ref="#/definitions/CalendarDatesDetailEditLog")
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
        $this->calendarDatesDetailEditLogRepository->pushCriteria(new RequestCriteria($request));
        $this->calendarDatesDetailEditLogRepository->pushCriteria(new LimitOffsetCriteria($request));
        $calendarDatesDetailEditLogs = $this->calendarDatesDetailEditLogRepository->all();

        return $this->sendResponse($calendarDatesDetailEditLogs->toArray(), trans('custom.calendar_dates_detail_edit_logs_retrieved_successf'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/calendarDatesDetailEditLogs",
     *      summary="createCalendarDatesDetailEditLog",
     *      tags={"CalendarDatesDetailEditLog"},
     *      description="Create CalendarDatesDetailEditLog",
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
     *                  ref="#/definitions/CalendarDatesDetailEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCalendarDatesDetailEditLogAPIRequest $request)
    {
        $input = $request->all();

        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->create($input);

        return $this->sendResponse($calendarDatesDetailEditLog->toArray(), trans('custom.calendar_dates_detail_edit_log_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/calendarDatesDetailEditLogs/{id}",
     *      summary="getCalendarDatesDetailEditLogItem",
     *      tags={"CalendarDatesDetailEditLog"},
     *      description="Get CalendarDatesDetailEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CalendarDatesDetailEditLog",
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
     *                  ref="#/definitions/CalendarDatesDetailEditLog"
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
        /** @var CalendarDatesDetailEditLog $calendarDatesDetailEditLog */
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            return $this->sendError(trans('custom.calendar_dates_detail_edit_log_not_found'));
        }

        return $this->sendResponse($calendarDatesDetailEditLog->toArray(), trans('custom.calendar_dates_detail_edit_log_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/calendarDatesDetailEditLogs/{id}",
     *      summary="updateCalendarDatesDetailEditLog",
     *      tags={"CalendarDatesDetailEditLog"},
     *      description="Update CalendarDatesDetailEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CalendarDatesDetailEditLog",
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
     *                  ref="#/definitions/CalendarDatesDetailEditLog"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCalendarDatesDetailEditLogAPIRequest $request)
    {
        $input = $request->all();

        /** @var CalendarDatesDetailEditLog $calendarDatesDetailEditLog */
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            return $this->sendError(trans('custom.calendar_dates_detail_edit_log_not_found'));
        }

        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->update($input, $id);

        return $this->sendResponse($calendarDatesDetailEditLog->toArray(), trans('custom.calendardatesdetaileditlog_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/calendarDatesDetailEditLogs/{id}",
     *      summary="deleteCalendarDatesDetailEditLog",
     *      tags={"CalendarDatesDetailEditLog"},
     *      description="Delete CalendarDatesDetailEditLog",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CalendarDatesDetailEditLog",
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
        /** @var CalendarDatesDetailEditLog $calendarDatesDetailEditLog */
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            return $this->sendError(trans('custom.calendar_dates_detail_edit_log_not_found'));
        }

        $calendarDatesDetailEditLog->delete();

        return $this->sendSuccess('Calendar Dates Detail Edit Log deleted successfully');
    }
}

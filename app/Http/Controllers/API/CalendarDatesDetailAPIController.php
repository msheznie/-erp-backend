<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCalendarDatesDetailAPIRequest;
use App\Http\Requests\API\UpdateCalendarDatesDetailAPIRequest;
use App\Models\CalendarDatesDetail;
use App\Repositories\CalendarDatesDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CalendarDatesDetailController
 * @package App\Http\Controllers\API
 */

class CalendarDatesDetailAPIController extends AppBaseController
{
    /** @var  CalendarDatesDetailRepository */
    private $calendarDatesDetailRepository;

    public function __construct(CalendarDatesDetailRepository $calendarDatesDetailRepo)
    {
        $this->calendarDatesDetailRepository = $calendarDatesDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/calendarDatesDetails",
     *      summary="Get a listing of the CalendarDatesDetails.",
     *      tags={"CalendarDatesDetail"},
     *      description="Get all CalendarDatesDetails",
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
     *                  @SWG\Items(ref="#/definitions/CalendarDatesDetail")
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
        $this->calendarDatesDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->calendarDatesDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $calendarDatesDetails = $this->calendarDatesDetailRepository->all();

        return $this->sendResponse($calendarDatesDetails->toArray(), trans('custom.calendar_dates_details_retrieved_successfully'));
    }

    /**
     * @param CreateCalendarDatesDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/calendarDatesDetails",
     *      summary="Store a newly created CalendarDatesDetail in storage",
     *      tags={"CalendarDatesDetail"},
     *      description="Store CalendarDatesDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CalendarDatesDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CalendarDatesDetail")
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
     *                  ref="#/definitions/CalendarDatesDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCalendarDatesDetailAPIRequest $request)
    {
        $input = $request->all();

        $calendarDatesDetail = $this->calendarDatesDetailRepository->create($input);

        return $this->sendResponse($calendarDatesDetail->toArray(), trans('custom.calendar_dates_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/calendarDatesDetails/{id}",
     *      summary="Display the specified CalendarDatesDetail",
     *      tags={"CalendarDatesDetail"},
     *      description="Get CalendarDatesDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalendarDatesDetail",
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
     *                  ref="#/definitions/CalendarDatesDetail"
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
        /** @var CalendarDatesDetail $calendarDatesDetail */
        $calendarDatesDetail = $this->calendarDatesDetailRepository->findWithoutFail($id);

        if (empty($calendarDatesDetail)) {
            return $this->sendError(trans('custom.calendar_dates_detail_not_found'));
        }

        return $this->sendResponse($calendarDatesDetail->toArray(), trans('custom.calendar_dates_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCalendarDatesDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/calendarDatesDetails/{id}",
     *      summary="Update the specified CalendarDatesDetail in storage",
     *      tags={"CalendarDatesDetail"},
     *      description="Update CalendarDatesDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalendarDatesDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CalendarDatesDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CalendarDatesDetail")
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
     *                  ref="#/definitions/CalendarDatesDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCalendarDatesDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CalendarDatesDetail $calendarDatesDetail */
        $calendarDatesDetail = $this->calendarDatesDetailRepository->findWithoutFail($id);

        if (empty($calendarDatesDetail)) {
            return $this->sendError(trans('custom.calendar_dates_detail_not_found'));
        }

        $calendarDatesDetail = $this->calendarDatesDetailRepository->update($input, $id);

        return $this->sendResponse($calendarDatesDetail->toArray(), trans('custom.calendardatesdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/calendarDatesDetails/{id}",
     *      summary="Remove the specified CalendarDatesDetail from storage",
     *      tags={"CalendarDatesDetail"},
     *      description="Delete CalendarDatesDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalendarDatesDetail",
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
        /** @var CalendarDatesDetail $calendarDatesDetail */
        $calendarDatesDetail = $this->calendarDatesDetailRepository->findWithoutFail($id);

        if (empty($calendarDatesDetail)) {
            return $this->sendError(trans('custom.calendar_dates_detail_not_found'));
        }

        $calendarDatesDetail->delete();

        return $this->sendSuccess('Calendar Dates Detail deleted successfully');
    }
}

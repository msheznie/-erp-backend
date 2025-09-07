<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCalendarDatesAPIRequest;
use App\Http\Requests\API\UpdateCalendarDatesAPIRequest;
use App\Models\CalendarDates;
use App\Repositories\CalendarDatesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CalendarDatesController
 * @package App\Http\Controllers\API
 */

class CalendarDatesAPIController extends AppBaseController
{
    /** @var  CalendarDatesRepository */
    private $calendarDatesRepository;

    public function __construct(CalendarDatesRepository $calendarDatesRepo)
    {
        $this->calendarDatesRepository = $calendarDatesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/calendarDates",
     *      summary="Get a listing of the CalendarDates.",
     *      tags={"CalendarDates"},
     *      description="Get all CalendarDates",
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
     *                  @SWG\Items(ref="#/definitions/CalendarDates")
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
        $this->calendarDatesRepository->pushCriteria(new RequestCriteria($request));
        $this->calendarDatesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $calendarDates = $this->calendarDatesRepository->all();

        return $this->sendResponse($calendarDates->toArray(), trans('custom.calendar_dates_retrieved_successfully'));
    }

    /**
     * @param CreateCalendarDatesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/calendarDates",
     *      summary="Store a newly created CalendarDates in storage",
     *      tags={"CalendarDates"},
     *      description="Store CalendarDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CalendarDates that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CalendarDates")
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
     *                  ref="#/definitions/CalendarDates"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCalendarDatesAPIRequest $request)
    {
        $input = $request->all();

        $calendarDates = $this->calendarDatesRepository->create($input);

        return $this->sendResponse($calendarDates->toArray(), trans('custom.calendar_dates_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/calendarDates/{id}",
     *      summary="Display the specified CalendarDates",
     *      tags={"CalendarDates"},
     *      description="Get CalendarDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalendarDates",
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
     *                  ref="#/definitions/CalendarDates"
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
        /** @var CalendarDates $calendarDates */
        $calendarDates = $this->calendarDatesRepository->findWithoutFail($id);

        if (empty($calendarDates)) {
            return $this->sendError(trans('custom.calendar_dates_not_found'));
        }

        return $this->sendResponse($calendarDates->toArray(), trans('custom.calendar_dates_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCalendarDatesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/calendarDates/{id}",
     *      summary="Update the specified CalendarDates in storage",
     *      tags={"CalendarDates"},
     *      description="Update CalendarDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalendarDates",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CalendarDates that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CalendarDates")
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
     *                  ref="#/definitions/CalendarDates"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCalendarDatesAPIRequest $request)
    {
        $input = $request->all();

        /** @var CalendarDates $calendarDates */
        $calendarDates = $this->calendarDatesRepository->findWithoutFail($id);

        if (empty($calendarDates)) {
            return $this->sendError(trans('custom.calendar_dates_not_found'));
        }

        $calendarDates = $this->calendarDatesRepository->update($input, $id);

        return $this->sendResponse($calendarDates->toArray(), trans('custom.calendardates_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/calendarDates/{id}",
     *      summary="Remove the specified CalendarDates from storage",
     *      tags={"CalendarDates"},
     *      description="Delete CalendarDates",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CalendarDates",
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
        /** @var CalendarDates $calendarDates */
        $calendarDates = $this->calendarDatesRepository->findWithoutFail($id);

        if (empty($calendarDates)) {
            return $this->sendError(trans('custom.calendar_dates_not_found'));
        }

        $calendarDates->delete();

        return $this->sendSuccess('Calendar Dates deleted successfully');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCalendarDatesDetailEditLogRequest;
use App\Http\Requests\UpdateCalendarDatesDetailEditLogRequest;
use App\Repositories\CalendarDatesDetailEditLogRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CalendarDatesDetailEditLogController extends AppBaseController
{
    /** @var  CalendarDatesDetailEditLogRepository */
    private $calendarDatesDetailEditLogRepository;

    public function __construct(CalendarDatesDetailEditLogRepository $calendarDatesDetailEditLogRepo)
    {
        $this->calendarDatesDetailEditLogRepository = $calendarDatesDetailEditLogRepo;
    }

    /**
     * Display a listing of the CalendarDatesDetailEditLog.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->calendarDatesDetailEditLogRepository->pushCriteria(new RequestCriteria($request));
        $calendarDatesDetailEditLogs = $this->calendarDatesDetailEditLogRepository->all();

        return view('calendar_dates_detail_edit_logs.index')
            ->with('calendarDatesDetailEditLogs', $calendarDatesDetailEditLogs);
    }

    /**
     * Show the form for creating a new CalendarDatesDetailEditLog.
     *
     * @return Response
     */
    public function create()
    {
        return view('calendar_dates_detail_edit_logs.create');
    }

    /**
     * Store a newly created CalendarDatesDetailEditLog in storage.
     *
     * @param CreateCalendarDatesDetailEditLogRequest $request
     *
     * @return Response
     */
    public function store(CreateCalendarDatesDetailEditLogRequest $request)
    {
        $input = $request->all();

        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->create($input);

        Flash::success('Calendar Dates Detail Edit Log saved successfully.');

        return redirect(route('calendarDatesDetailEditLogs.index'));
    }

    /**
     * Display the specified CalendarDatesDetailEditLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            Flash::error('Calendar Dates Detail Edit Log not found');

            return redirect(route('calendarDatesDetailEditLogs.index'));
        }

        return view('calendar_dates_detail_edit_logs.show')->with('calendarDatesDetailEditLog', $calendarDatesDetailEditLog);
    }

    /**
     * Show the form for editing the specified CalendarDatesDetailEditLog.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            Flash::error('Calendar Dates Detail Edit Log not found');

            return redirect(route('calendarDatesDetailEditLogs.index'));
        }

        return view('calendar_dates_detail_edit_logs.edit')->with('calendarDatesDetailEditLog', $calendarDatesDetailEditLog);
    }

    /**
     * Update the specified CalendarDatesDetailEditLog in storage.
     *
     * @param  int              $id
     * @param UpdateCalendarDatesDetailEditLogRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCalendarDatesDetailEditLogRequest $request)
    {
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            Flash::error('Calendar Dates Detail Edit Log not found');

            return redirect(route('calendarDatesDetailEditLogs.index'));
        }

        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->update($request->all(), $id);

        Flash::success('Calendar Dates Detail Edit Log updated successfully.');

        return redirect(route('calendarDatesDetailEditLogs.index'));
    }

    /**
     * Remove the specified CalendarDatesDetailEditLog from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $calendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepository->findWithoutFail($id);

        if (empty($calendarDatesDetailEditLog)) {
            Flash::error('Calendar Dates Detail Edit Log not found');

            return redirect(route('calendarDatesDetailEditLogs.index'));
        }

        $this->calendarDatesDetailEditLogRepository->delete($id);

        Flash::success('Calendar Dates Detail Edit Log deleted successfully.');

        return redirect(route('calendarDatesDetailEditLogs.index'));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\CalendarDates;
use App\Repositories\CalendarDatesRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class TenderCalendarDatesController extends AppBaseController
{
    private $calendarDatesRepository;

    public function __construct(CalendarDatesRepository $calendarDatesRepository)
    {
        $this->calendarDatesRepository = $calendarDatesRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws ValidatorException
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $companySystemID = $request->input('companySystemID');
        $validator = \Validator::make($input, [
            'calendar_date' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $calendarDatesExist = CalendarDates::select('id', 'calendar_date')
            ->where('calendar_date', '=', $input['calendar_date'])->first();

        if (!empty($calendarDatesExist)) {
            return $this->sendError('Calendar date description \'' . $input['calendar_date'] . '\' is already exists.');
        }

        $input['created_at'] = Carbon::now();
        $input['created_by'] = Helper::getEmployeeSystemID();
        $input['company_id'] = $companySystemID;
        $calendarDate = $this->calendarDatesRepository->create($input);
        return $this->sendResponse($calendarDate->toArray(), 'Calendar date description saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $calendarDate = CalendarDates::find($id);

        if (empty($calendarDate)) {
            return $this->sendError('Calendar date description not found');
        }

        return $this->sendResponse($calendarDate->toArray(), 'Calendar date description retrieved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $calendarDate = CalendarDates::find($id);

        if (empty($calendarDate)) {
            return $this->sendError('Calendar date description not found');
        }

        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'calendar_date' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $calendarDateExist = CalendarDates::select('id', 'calendar_date')
            ->where('calendar_date', '=', $input['calendar_date'])
            ->where('id', '!=', $id)
            ->first();

        if (!empty($calendarDateExist)) {
            return $this->sendError('Calendar date description \'' . $input['calendar_date'] . '\' is already exists.');
        }

        $input['updated_by'] = Helper::getEmployeeSystemID();
        $input['updated_at'] = Carbon::now();

        $calendarDates = CalendarDates::where('id', $id)->update($input);

        return $this->sendResponse($calendarDates, 'Calendar date description updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $calendarDate = CalendarDates::find($id);

        if (empty($calendarDate)) {
            return $this->sendError('Calendar date not found');
        }

        $calendarDate->delete();

        return $this->sendResponse($id, 'Calendar date description deleted successfully');
    }

    public function getAllCalendarDates(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'desc';
        } else {
            $sort = 'asc';
        }
        $calendarDates = CalendarDates::with(['calendar_dates_detail'])
            ->where("company_id", $input['companyId'])
            ->orWhere("is_default",'!=', 0)
            ->orderByRaw('CASE WHEN is_default IN (1, 2) THEN 0 ELSE 1 END, is_default')
            ->orderBy('id', $sort);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $calendarDates = $calendarDates->where(function ($query) use ($search) {
                $query->where('calendar_date', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($calendarDates)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }
}

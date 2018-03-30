<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMonthsRequest;
use App\Http\Requests\UpdateMonthsRequest;
use App\Repositories\MonthsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class MonthsController extends AppBaseController
{
    /** @var  MonthsRepository */
    private $monthsRepository;

    public function __construct(MonthsRepository $monthsRepo)
    {
        $this->monthsRepository = $monthsRepo;
    }

    /**
     * Display a listing of the Months.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->monthsRepository->pushCriteria(new RequestCriteria($request));
        $months = $this->monthsRepository->all();

        return view('months.index')
            ->with('months', $months);
    }

    /**
     * Show the form for creating a new Months.
     *
     * @return Response
     */
    public function create()
    {
        return view('months.create');
    }

    /**
     * Store a newly created Months in storage.
     *
     * @param CreateMonthsRequest $request
     *
     * @return Response
     */
    public function store(CreateMonthsRequest $request)
    {
        $input = $request->all();

        $months = $this->monthsRepository->create($input);

        Flash::success('Months saved successfully.');

        return redirect(route('months.index'));
    }

    /**
     * Display the specified Months.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            Flash::error('Months not found');

            return redirect(route('months.index'));
        }

        return view('months.show')->with('months', $months);
    }

    /**
     * Show the form for editing the specified Months.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            Flash::error('Months not found');

            return redirect(route('months.index'));
        }

        return view('months.edit')->with('months', $months);
    }

    /**
     * Update the specified Months in storage.
     *
     * @param  int              $id
     * @param UpdateMonthsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMonthsRequest $request)
    {
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            Flash::error('Months not found');

            return redirect(route('months.index'));
        }

        $months = $this->monthsRepository->update($request->all(), $id);

        Flash::success('Months updated successfully.');

        return redirect(route('months.index'));
    }

    /**
     * Remove the specified Months from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            Flash::error('Months not found');

            return redirect(route('months.index'));
        }

        $this->monthsRepository->delete($id);

        Flash::success('Months deleted successfully.');

        return redirect(route('months.index'));
    }
}

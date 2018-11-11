<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMonthlyAdditionDetailRequest;
use App\Http\Requests\UpdateMonthlyAdditionDetailRequest;
use App\Repositories\MonthlyAdditionDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class MonthlyAdditionDetailController extends AppBaseController
{
    /** @var  MonthlyAdditionDetailRepository */
    private $monthlyAdditionDetailRepository;

    public function __construct(MonthlyAdditionDetailRepository $monthlyAdditionDetailRepo)
    {
        $this->monthlyAdditionDetailRepository = $monthlyAdditionDetailRepo;
    }

    /**
     * Display a listing of the MonthlyAdditionDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->monthlyAdditionDetailRepository->pushCriteria(new RequestCriteria($request));
        $monthlyAdditionDetails = $this->monthlyAdditionDetailRepository->all();

        return view('monthly_addition_details.index')
            ->with('monthlyAdditionDetails', $monthlyAdditionDetails);
    }

    /**
     * Show the form for creating a new MonthlyAdditionDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('monthly_addition_details.create');
    }

    /**
     * Store a newly created MonthlyAdditionDetail in storage.
     *
     * @param CreateMonthlyAdditionDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateMonthlyAdditionDetailRequest $request)
    {
        $input = $request->all();

        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->create($input);

        Flash::success('Monthly Addition Detail saved successfully.');

        return redirect(route('monthlyAdditionDetails.index'));
    }

    /**
     * Display the specified MonthlyAdditionDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            Flash::error('Monthly Addition Detail not found');

            return redirect(route('monthlyAdditionDetails.index'));
        }

        return view('monthly_addition_details.show')->with('monthlyAdditionDetail', $monthlyAdditionDetail);
    }

    /**
     * Show the form for editing the specified MonthlyAdditionDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            Flash::error('Monthly Addition Detail not found');

            return redirect(route('monthlyAdditionDetails.index'));
        }

        return view('monthly_addition_details.edit')->with('monthlyAdditionDetail', $monthlyAdditionDetail);
    }

    /**
     * Update the specified MonthlyAdditionDetail in storage.
     *
     * @param  int              $id
     * @param UpdateMonthlyAdditionDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMonthlyAdditionDetailRequest $request)
    {
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            Flash::error('Monthly Addition Detail not found');

            return redirect(route('monthlyAdditionDetails.index'));
        }

        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->update($request->all(), $id);

        Flash::success('Monthly Addition Detail updated successfully.');

        return redirect(route('monthlyAdditionDetails.index'));
    }

    /**
     * Remove the specified MonthlyAdditionDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $monthlyAdditionDetail = $this->monthlyAdditionDetailRepository->findWithoutFail($id);

        if (empty($monthlyAdditionDetail)) {
            Flash::error('Monthly Addition Detail not found');

            return redirect(route('monthlyAdditionDetails.index'));
        }

        $this->monthlyAdditionDetailRepository->delete($id);

        Flash::success('Monthly Addition Detail deleted successfully.');

        return redirect(route('monthlyAdditionDetails.index'));
    }
}

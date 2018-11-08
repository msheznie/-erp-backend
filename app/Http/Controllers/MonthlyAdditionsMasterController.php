<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMonthlyAdditionsMasterRequest;
use App\Http\Requests\UpdateMonthlyAdditionsMasterRequest;
use App\Repositories\MonthlyAdditionsMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class MonthlyAdditionsMasterController extends AppBaseController
{
    /** @var  MonthlyAdditionsMasterRepository */
    private $monthlyAdditionsMasterRepository;

    public function __construct(MonthlyAdditionsMasterRepository $monthlyAdditionsMasterRepo)
    {
        $this->monthlyAdditionsMasterRepository = $monthlyAdditionsMasterRepo;
    }

    /**
     * Display a listing of the MonthlyAdditionsMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->monthlyAdditionsMasterRepository->pushCriteria(new RequestCriteria($request));
        $monthlyAdditionsMasters = $this->monthlyAdditionsMasterRepository->all();

        return view('monthly_additions_masters.index')
            ->with('monthlyAdditionsMasters', $monthlyAdditionsMasters);
    }

    /**
     * Show the form for creating a new MonthlyAdditionsMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('monthly_additions_masters.create');
    }

    /**
     * Store a newly created MonthlyAdditionsMaster in storage.
     *
     * @param CreateMonthlyAdditionsMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateMonthlyAdditionsMasterRequest $request)
    {
        $input = $request->all();

        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->create($input);

        Flash::success('Monthly Additions Master saved successfully.');

        return redirect(route('monthlyAdditionsMasters.index'));
    }

    /**
     * Display the specified MonthlyAdditionsMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            Flash::error('Monthly Additions Master not found');

            return redirect(route('monthlyAdditionsMasters.index'));
        }

        return view('monthly_additions_masters.show')->with('monthlyAdditionsMaster', $monthlyAdditionsMaster);
    }

    /**
     * Show the form for editing the specified MonthlyAdditionsMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            Flash::error('Monthly Additions Master not found');

            return redirect(route('monthlyAdditionsMasters.index'));
        }

        return view('monthly_additions_masters.edit')->with('monthlyAdditionsMaster', $monthlyAdditionsMaster);
    }

    /**
     * Update the specified MonthlyAdditionsMaster in storage.
     *
     * @param  int              $id
     * @param UpdateMonthlyAdditionsMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMonthlyAdditionsMasterRequest $request)
    {
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            Flash::error('Monthly Additions Master not found');

            return redirect(route('monthlyAdditionsMasters.index'));
        }

        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->update($request->all(), $id);

        Flash::success('Monthly Additions Master updated successfully.');

        return redirect(route('monthlyAdditionsMasters.index'));
    }

    /**
     * Remove the specified MonthlyAdditionsMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $monthlyAdditionsMaster = $this->monthlyAdditionsMasterRepository->findWithoutFail($id);

        if (empty($monthlyAdditionsMaster)) {
            Flash::error('Monthly Additions Master not found');

            return redirect(route('monthlyAdditionsMasters.index'));
        }

        $this->monthlyAdditionsMasterRepository->delete($id);

        Flash::success('Monthly Additions Master deleted successfully.');

        return redirect(route('monthlyAdditionsMasters.index'));
    }
}

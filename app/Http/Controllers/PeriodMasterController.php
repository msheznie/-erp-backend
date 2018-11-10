<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePeriodMasterRequest;
use App\Http\Requests\UpdatePeriodMasterRequest;
use App\Repositories\PeriodMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PeriodMasterController extends AppBaseController
{
    /** @var  PeriodMasterRepository */
    private $periodMasterRepository;

    public function __construct(PeriodMasterRepository $periodMasterRepo)
    {
        $this->periodMasterRepository = $periodMasterRepo;
    }

    /**
     * Display a listing of the PeriodMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->periodMasterRepository->pushCriteria(new RequestCriteria($request));
        $periodMasters = $this->periodMasterRepository->all();

        return view('period_masters.index')
            ->with('periodMasters', $periodMasters);
    }

    /**
     * Show the form for creating a new PeriodMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('period_masters.create');
    }

    /**
     * Store a newly created PeriodMaster in storage.
     *
     * @param CreatePeriodMasterRequest $request
     *
     * @return Response
     */
    public function store(CreatePeriodMasterRequest $request)
    {
        $input = $request->all();

        $periodMaster = $this->periodMasterRepository->create($input);

        Flash::success('Period Master saved successfully.');

        return redirect(route('periodMasters.index'));
    }

    /**
     * Display the specified PeriodMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            Flash::error('Period Master not found');

            return redirect(route('periodMasters.index'));
        }

        return view('period_masters.show')->with('periodMaster', $periodMaster);
    }

    /**
     * Show the form for editing the specified PeriodMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            Flash::error('Period Master not found');

            return redirect(route('periodMasters.index'));
        }

        return view('period_masters.edit')->with('periodMaster', $periodMaster);
    }

    /**
     * Update the specified PeriodMaster in storage.
     *
     * @param  int              $id
     * @param UpdatePeriodMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePeriodMasterRequest $request)
    {
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            Flash::error('Period Master not found');

            return redirect(route('periodMasters.index'));
        }

        $periodMaster = $this->periodMasterRepository->update($request->all(), $id);

        Flash::success('Period Master updated successfully.');

        return redirect(route('periodMasters.index'));
    }

    /**
     * Remove the specified PeriodMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $periodMaster = $this->periodMasterRepository->findWithoutFail($id);

        if (empty($periodMaster)) {
            Flash::error('Period Master not found');

            return redirect(route('periodMasters.index'));
        }

        $this->periodMasterRepository->delete($id);

        Flash::success('Period Master deleted successfully.');

        return redirect(route('periodMasters.index'));
    }
}

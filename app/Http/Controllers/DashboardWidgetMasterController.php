<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDashboardWidgetMasterRequest;
use App\Http\Requests\UpdateDashboardWidgetMasterRequest;
use App\Repositories\DashboardWidgetMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DashboardWidgetMasterController extends AppBaseController
{
    /** @var  DashboardWidgetMasterRepository */
    private $dashboardWidgetMasterRepository;

    public function __construct(DashboardWidgetMasterRepository $dashboardWidgetMasterRepo)
    {
        $this->dashboardWidgetMasterRepository = $dashboardWidgetMasterRepo;
    }

    /**
     * Display a listing of the DashboardWidgetMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->dashboardWidgetMasterRepository->pushCriteria(new RequestCriteria($request));
        $dashboardWidgetMasters = $this->dashboardWidgetMasterRepository->all();

        return view('dashboard_widget_masters.index')
            ->with('dashboardWidgetMasters', $dashboardWidgetMasters);
    }

    /**
     * Show the form for creating a new DashboardWidgetMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('dashboard_widget_masters.create');
    }

    /**
     * Store a newly created DashboardWidgetMaster in storage.
     *
     * @param CreateDashboardWidgetMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateDashboardWidgetMasterRequest $request)
    {
        $input = $request->all();

        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->create($input);

        Flash::success('Dashboard Widget Master saved successfully.');

        return redirect(route('dashboardWidgetMasters.index'));
    }

    /**
     * Display the specified DashboardWidgetMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            Flash::error('Dashboard Widget Master not found');

            return redirect(route('dashboardWidgetMasters.index'));
        }

        return view('dashboard_widget_masters.show')->with('dashboardWidgetMaster', $dashboardWidgetMaster);
    }

    /**
     * Show the form for editing the specified DashboardWidgetMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            Flash::error('Dashboard Widget Master not found');

            return redirect(route('dashboardWidgetMasters.index'));
        }

        return view('dashboard_widget_masters.edit')->with('dashboardWidgetMaster', $dashboardWidgetMaster);
    }

    /**
     * Update the specified DashboardWidgetMaster in storage.
     *
     * @param  int              $id
     * @param UpdateDashboardWidgetMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDashboardWidgetMasterRequest $request)
    {
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            Flash::error('Dashboard Widget Master not found');

            return redirect(route('dashboardWidgetMasters.index'));
        }

        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->update($request->all(), $id);

        Flash::success('Dashboard Widget Master updated successfully.');

        return redirect(route('dashboardWidgetMasters.index'));
    }

    /**
     * Remove the specified DashboardWidgetMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $dashboardWidgetMaster = $this->dashboardWidgetMasterRepository->findWithoutFail($id);

        if (empty($dashboardWidgetMaster)) {
            Flash::error('Dashboard Widget Master not found');

            return redirect(route('dashboardWidgetMasters.index'));
        }

        $this->dashboardWidgetMasterRepository->delete($id);

        Flash::success('Dashboard Widget Master deleted successfully.');

        return redirect(route('dashboardWidgetMasters.index'));
    }
}

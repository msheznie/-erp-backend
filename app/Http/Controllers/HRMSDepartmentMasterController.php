<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHRMSDepartmentMasterRequest;
use App\Http\Requests\UpdateHRMSDepartmentMasterRequest;
use App\Repositories\HRMSDepartmentMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class HRMSDepartmentMasterController extends AppBaseController
{
    /** @var  HRMSDepartmentMasterRepository */
    private $hRMSDepartmentMasterRepository;

    public function __construct(HRMSDepartmentMasterRepository $hRMSDepartmentMasterRepo)
    {
        $this->hRMSDepartmentMasterRepository = $hRMSDepartmentMasterRepo;
    }

    /**
     * Display a listing of the HRMSDepartmentMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->hRMSDepartmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $hRMSDepartmentMasters = $this->hRMSDepartmentMasterRepository->all();

        return view('h_r_m_s_department_masters.index')
            ->with('hRMSDepartmentMasters', $hRMSDepartmentMasters);
    }

    /**
     * Show the form for creating a new HRMSDepartmentMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('h_r_m_s_department_masters.create');
    }

    /**
     * Store a newly created HRMSDepartmentMaster in storage.
     *
     * @param CreateHRMSDepartmentMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateHRMSDepartmentMasterRequest $request)
    {
        $input = $request->all();

        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->create($input);

        Flash::success('H R M S Department Master saved successfully.');

        return redirect(route('hRMSDepartmentMasters.index'));
    }

    /**
     * Display the specified HRMSDepartmentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            Flash::error('H R M S Department Master not found');

            return redirect(route('hRMSDepartmentMasters.index'));
        }

        return view('h_r_m_s_department_masters.show')->with('hRMSDepartmentMaster', $hRMSDepartmentMaster);
    }

    /**
     * Show the form for editing the specified HRMSDepartmentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            Flash::error('H R M S Department Master not found');

            return redirect(route('hRMSDepartmentMasters.index'));
        }

        return view('h_r_m_s_department_masters.edit')->with('hRMSDepartmentMaster', $hRMSDepartmentMaster);
    }

    /**
     * Update the specified HRMSDepartmentMaster in storage.
     *
     * @param  int              $id
     * @param UpdateHRMSDepartmentMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHRMSDepartmentMasterRequest $request)
    {
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            Flash::error('H R M S Department Master not found');

            return redirect(route('hRMSDepartmentMasters.index'));
        }

        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->update($request->all(), $id);

        Flash::success('H R M S Department Master updated successfully.');

        return redirect(route('hRMSDepartmentMasters.index'));
    }

    /**
     * Remove the specified HRMSDepartmentMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $hRMSDepartmentMaster = $this->hRMSDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hRMSDepartmentMaster)) {
            Flash::error('H R M S Department Master not found');

            return redirect(route('hRMSDepartmentMasters.index'));
        }

        $this->hRMSDepartmentMasterRepository->delete($id);

        Flash::success('H R M S Department Master deleted successfully.');

        return redirect(route('hRMSDepartmentMasters.index'));
    }
}

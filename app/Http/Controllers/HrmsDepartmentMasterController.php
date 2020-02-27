<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHrmsDepartmentMasterRequest;
use App\Http\Requests\UpdateHrmsDepartmentMasterRequest;
use App\Repositories\HrmsDepartmentMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class HrmsDepartmentMasterController extends AppBaseController
{
    /** @var  HrmsDepartmentMasterRepository */
    private $hrmsDepartmentMasterRepository;

    public function __construct(HrmsDepartmentMasterRepository $hrmsDepartmentMasterRepo)
    {
        $this->hrmsDepartmentMasterRepository = $hrmsDepartmentMasterRepo;
    }

    /**
     * Display a listing of the HrmsDepartmentMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->hrmsDepartmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $hrmsDepartmentMasters = $this->hrmsDepartmentMasterRepository->all();

        return view('hrms_department_masters.index')
            ->with('hrmsDepartmentMasters', $hrmsDepartmentMasters);
    }

    /**
     * Show the form for creating a new HrmsDepartmentMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('hrms_department_masters.create');
    }

    /**
     * Store a newly created HrmsDepartmentMaster in storage.
     *
     * @param CreateHrmsDepartmentMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateHrmsDepartmentMasterRequest $request)
    {
        $input = $request->all();

        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->create($input);

        Flash::success('Hrms Department Master saved successfully.');

        return redirect(route('hrmsDepartmentMasters.index'));
    }

    /**
     * Display the specified HrmsDepartmentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            Flash::error('Hrms Department Master not found');

            return redirect(route('hrmsDepartmentMasters.index'));
        }

        return view('hrms_department_masters.show')->with('hrmsDepartmentMaster', $hrmsDepartmentMaster);
    }

    /**
     * Show the form for editing the specified HrmsDepartmentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            Flash::error('Hrms Department Master not found');

            return redirect(route('hrmsDepartmentMasters.index'));
        }

        return view('hrms_department_masters.edit')->with('hrmsDepartmentMaster', $hrmsDepartmentMaster);
    }

    /**
     * Update the specified HrmsDepartmentMaster in storage.
     *
     * @param  int              $id
     * @param UpdateHrmsDepartmentMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateHrmsDepartmentMasterRequest $request)
    {
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            Flash::error('Hrms Department Master not found');

            return redirect(route('hrmsDepartmentMasters.index'));
        }

        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->update($request->all(), $id);

        Flash::success('Hrms Department Master updated successfully.');

        return redirect(route('hrmsDepartmentMasters.index'));
    }

    /**
     * Remove the specified HrmsDepartmentMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $hrmsDepartmentMaster = $this->hrmsDepartmentMasterRepository->findWithoutFail($id);

        if (empty($hrmsDepartmentMaster)) {
            Flash::error('Hrms Department Master not found');

            return redirect(route('hrmsDepartmentMasters.index'));
        }

        $this->hrmsDepartmentMasterRepository->delete($id);

        Flash::success('Hrms Department Master deleted successfully.');

        return redirect(route('hrmsDepartmentMasters.index'));
    }
}

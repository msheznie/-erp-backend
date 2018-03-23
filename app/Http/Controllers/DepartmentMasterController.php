<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentMasterRequest;
use App\Http\Requests\UpdateDepartmentMasterRequest;
use App\Repositories\DepartmentMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DepartmentMasterController extends AppBaseController
{
    /** @var  DepartmentMasterRepository */
    private $departmentMasterRepository;

    public function __construct(DepartmentMasterRepository $departmentMasterRepo)
    {
        $this->departmentMasterRepository = $departmentMasterRepo;
    }

    /**
     * Display a listing of the DepartmentMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->departmentMasterRepository->pushCriteria(new RequestCriteria($request));
        $departmentMasters = $this->departmentMasterRepository->all();

        return view('department_masters.index')
            ->with('departmentMasters', $departmentMasters);
    }

    /**
     * Show the form for creating a new DepartmentMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('department_masters.create');
    }

    /**
     * Store a newly created DepartmentMaster in storage.
     *
     * @param CreateDepartmentMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateDepartmentMasterRequest $request)
    {
        $input = $request->all();

        $departmentMaster = $this->departmentMasterRepository->create($input);

        Flash::success('Department Master saved successfully.');

        return redirect(route('departmentMasters.index'));
    }

    /**
     * Display the specified DepartmentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            Flash::error('Department Master not found');

            return redirect(route('departmentMasters.index'));
        }

        return view('department_masters.show')->with('departmentMaster', $departmentMaster);
    }

    /**
     * Show the form for editing the specified DepartmentMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            Flash::error('Department Master not found');

            return redirect(route('departmentMasters.index'));
        }

        return view('department_masters.edit')->with('departmentMaster', $departmentMaster);
    }

    /**
     * Update the specified DepartmentMaster in storage.
     *
     * @param  int              $id
     * @param UpdateDepartmentMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDepartmentMasterRequest $request)
    {
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            Flash::error('Department Master not found');

            return redirect(route('departmentMasters.index'));
        }

        $departmentMaster = $this->departmentMasterRepository->update($request->all(), $id);

        Flash::success('Department Master updated successfully.');

        return redirect(route('departmentMasters.index'));
    }

    /**
     * Remove the specified DepartmentMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $departmentMaster = $this->departmentMasterRepository->findWithoutFail($id);

        if (empty($departmentMaster)) {
            Flash::error('Department Master not found');

            return redirect(route('departmentMasters.index'));
        }

        $this->departmentMasterRepository->delete($id);

        Flash::success('Department Master deleted successfully.');

        return redirect(route('departmentMasters.index'));
    }
}

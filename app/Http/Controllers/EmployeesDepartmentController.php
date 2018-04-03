<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeesDepartmentRequest;
use App\Http\Requests\UpdateEmployeesDepartmentRequest;
use App\Repositories\EmployeesDepartmentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class EmployeesDepartmentController extends AppBaseController
{
    /** @var  EmployeesDepartmentRepository */
    private $employeesDepartmentRepository;

    public function __construct(EmployeesDepartmentRepository $employeesDepartmentRepo)
    {
        $this->employeesDepartmentRepository = $employeesDepartmentRepo;
    }

    /**
     * Display a listing of the EmployeesDepartment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeesDepartmentRepository->pushCriteria(new RequestCriteria($request));
        $employeesDepartments = $this->employeesDepartmentRepository->all();

        return view('employees_departments.index')
            ->with('employeesDepartments', $employeesDepartments);
    }

    /**
     * Show the form for creating a new EmployeesDepartment.
     *
     * @return Response
     */
    public function create()
    {
        return view('employees_departments.create');
    }

    /**
     * Store a newly created EmployeesDepartment in storage.
     *
     * @param CreateEmployeesDepartmentRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeesDepartmentRequest $request)
    {
        $input = $request->all();

        $employeesDepartment = $this->employeesDepartmentRepository->create($input);

        Flash::success('Employees Department saved successfully.');

        return redirect(route('employeesDepartments.index'));
    }

    /**
     * Display the specified EmployeesDepartment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            Flash::error('Employees Department not found');

            return redirect(route('employeesDepartments.index'));
        }

        return view('employees_departments.show')->with('employeesDepartment', $employeesDepartment);
    }

    /**
     * Show the form for editing the specified EmployeesDepartment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            Flash::error('Employees Department not found');

            return redirect(route('employeesDepartments.index'));
        }

        return view('employees_departments.edit')->with('employeesDepartment', $employeesDepartment);
    }

    /**
     * Update the specified EmployeesDepartment in storage.
     *
     * @param  int              $id
     * @param UpdateEmployeesDepartmentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeesDepartmentRequest $request)
    {
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            Flash::error('Employees Department not found');

            return redirect(route('employeesDepartments.index'));
        }

        $employeesDepartment = $this->employeesDepartmentRepository->update($request->all(), $id);

        Flash::success('Employees Department updated successfully.');

        return redirect(route('employeesDepartments.index'));
    }

    /**
     * Remove the specified EmployeesDepartment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            Flash::error('Employees Department not found');

            return redirect(route('employeesDepartments.index'));
        }

        $this->employeesDepartmentRepository->delete($id);

        Flash::success('Employees Department deleted successfully.');

        return redirect(route('employeesDepartments.index'));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeesDepartmentAPIRequest;
use App\Http\Requests\API\UpdateEmployeesDepartmentAPIRequest;
use App\Models\EmployeesDepartment;
use App\Repositories\EmployeesDepartmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeesDepartmentController
 * @package App\Http\Controllers\API
 */

class EmployeesDepartmentAPIController extends AppBaseController
{
    /** @var  EmployeesDepartmentRepository */
    private $employeesDepartmentRepository;

    public function __construct(EmployeesDepartmentRepository $employeesDepartmentRepo)
    {
        $this->employeesDepartmentRepository = $employeesDepartmentRepo;
    }

    /**
     * Display a listing of the EmployeesDepartment.
     * GET|HEAD /employeesDepartments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeesDepartmentRepository->pushCriteria(new RequestCriteria($request));
        $this->employeesDepartmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employeesDepartments = $this->employeesDepartmentRepository->all();

        return $this->sendResponse($employeesDepartments->toArray(), 'Employees Departments retrieved successfully');
    }

    /**
     * Store a newly created EmployeesDepartment in storage.
     * POST /employeesDepartments
     *
     * @param CreateEmployeesDepartmentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeesDepartmentAPIRequest $request)
    {
        $input = $request->all();

        $employeesDepartments = $this->employeesDepartmentRepository->create($input);

        return $this->sendResponse($employeesDepartments->toArray(), 'Employees Department saved successfully');
    }

    /**
     * Display the specified EmployeesDepartment.
     * GET|HEAD /employeesDepartments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var EmployeesDepartment $employeesDepartment */
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        return $this->sendResponse($employeesDepartment->toArray(), 'Employees Department retrieved successfully');
    }

    /**
     * Update the specified EmployeesDepartment in storage.
     * PUT/PATCH /employeesDepartments/{id}
     *
     * @param  int $id
     * @param UpdateEmployeesDepartmentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeesDepartmentAPIRequest $request)
    {
        $input = $request->all();

        /** @var EmployeesDepartment $employeesDepartment */
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        $employeesDepartment = $this->employeesDepartmentRepository->update($input, $id);

        return $this->sendResponse($employeesDepartment->toArray(), 'EmployeesDepartment updated successfully');
    }

    /**
     * Remove the specified EmployeesDepartment from storage.
     * DELETE /employeesDepartments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var EmployeesDepartment $employeesDepartment */
        $employeesDepartment = $this->employeesDepartmentRepository->findWithoutFail($id);

        if (empty($employeesDepartment)) {
            return $this->sendError('Employees Department not found');
        }

        $employeesDepartment->delete();

        return $this->sendResponse($id, 'Employees Department deleted successfully');
    }
}

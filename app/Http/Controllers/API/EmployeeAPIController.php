<?php
/**
 * =============================================
 * -- File Name : EmployeeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Employee
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Employee
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Fayas Description: Added new functions named as getItemMasterPurchaseHistory(),exportPurchaseHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeAPIRequest;
use App\Http\Requests\API\UpdateEmployeeAPIRequest;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class EmployeeController
 * @package App\Http\Controllers\API
 */
class EmployeeAPIController extends AppBaseController
{
    /** @var  EmployeeRepository */
    private $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepo)
    {
        $this->employeeRepository = $employeeRepo;
    }

    /**
     * Display a listing of the Employee.
     * GET|HEAD /employees
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->employeeRepository->pushCriteria(new RequestCriteria($request));
        $this->employeeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $employees = $this->employeeRepository->all();

        return $this->sendResponse($employees->toArray(), 'Employees retrieved successfully');
    }

    /**
     * Store a newly created Employee in storage.
     * POST /employees
     *
     * @param CreateEmployeeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEmployeeAPIRequest $request)
    {
        $input = $request->all();

        $employees = $this->employeeRepository->create($input);

        return $this->sendResponse($employees->toArray(), 'Employee saved successfully');
    }

    /**
     * Display the specified Employee.
     * GET|HEAD /employees/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }

        return $this->sendResponse($employee->toArray(), 'Employee retrieved successfully');
    }

    /**
     * Update the specified Employee in storage.
     * PUT/PATCH /employees/{id}
     *
     * @param  int $id
     * @param UpdateEmployeeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEmployeeAPIRequest $request)
    {
        $input = $request->all();

        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }

        $employee = $this->employeeRepository->update($input, $id);

        return $this->sendResponse($employee->toArray(), 'Employee updated successfully');
    }

    /**
     * Remove the specified Employee from storage.
     * DELETE /employees/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }

        $employee->delete();

        return $this->sendResponse($id, 'Employee deleted successfully');
    }


    public function getTypeheadEmployees(Request $request)
    {
        $input = $request->all();
        $employees = "";
        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $employees = Employee::where(function ($query) use ($search) {
                $query->where('empID', 'LIKE', "%{$search}%")
                    ->orWhere('empName', 'LIKE', "%{$search}%");
            });
        }

        $employees = $employees
            ->take(20)
            ->get();

        return $this->sendResponse($employees->toArray(), 'Data retrieved successfully');
    }


}

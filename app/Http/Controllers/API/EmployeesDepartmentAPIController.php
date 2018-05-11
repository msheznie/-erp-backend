<?php
/**
 * =============================================
 * -- File Name : EmployeesDepartmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Employees department.
 * -- REVISION HISTORY
 * -- Date: 11-May 2018 By: Mubashir Description: Added new function getApprovalAccessRights(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeesDepartmentAPIRequest;
use App\Http\Requests\API\UpdateEmployeesDepartmentAPIRequest;
use App\Models\ApprovalGroups;
use App\Models\Company;
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

    public function getApprovalAccessRights(Request $request)
    {
        $employeesDepartment = EmployeesDepartment::where('employeeSystemID',$request->employeeSystemID)->get();
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup="";
        if(\Helper::checkIsCompanyGroup($selectedCompanyId)){
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $companiesByGroup = (array)$selectedCompanyId;
        }
        $groupCompany = Company::whereIN("companySystemID", $companiesByGroup)->get();
        if (empty($employeesDepartment)) {
            return $this->sendError('No records found');
        }

        $employeesDepartment = array('employeesDepartment' => $employeesDepartment, 'company' => $groupCompany, 'approvalGroup' => ApprovalGroups::all());

        return $this->sendResponse($employeesDepartment, 'Employees Department retrieved successfully');

    }
}

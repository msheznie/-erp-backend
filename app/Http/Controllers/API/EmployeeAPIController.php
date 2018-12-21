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
 * -- Date: 18-December 2018 By: Nazir Description: Added new functions named as getEmployeeMasterView(),
 * -- Date: 18-December 2018 By: Nazir Description: Added new functions named as confirmEmployeePasswordReset(),
 * -- Date: 19-December 2018 By: Nazir Description: Added new functions named as getEmployeeMasterData(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEmployeeAPIRequest;
use App\Http\Requests\API\UpdateEmployeeAPIRequest;
use App\Models\Alert;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
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
        $input = array_except($input, ['desi_master','manager','emp_company']);
        $input = $this->convertArrayToValue($input);

        /** @var Employee $employee */
        $employee = $this->employeeRepository->findWithoutFail($id);

        if (empty($employee)) {
            return $this->sendError('Employee not found');
        }
        if (isset($input['isBasicUser']) && $input['isBasicUser']) {
            $input['isBasicUser'] = -1;
        }
        if (isset($input['isManager']) && $input['isManager']) {
            $input['isManager'] = -1;
        }
        if (isset($input['isApproval']) && $input['isApproval']) {
            $input['isApproval'] = -1;
        }
        if (isset($input['isAdmin']) && $input['isAdmin']) {
            $input['isAdmin'] = -1;
        }
        if (isset($input['isSuperAdmin']) && $input['isSuperAdmin']) {
            $input['isSuperAdmin'] = -1;
        }
        if (isset($input['discharegedYN']) && $input['discharegedYN']) {
            $input['discharegedYN'] = -1;
        }
        if (isset($input['ActivationFlag']) && $input['ActivationFlag']) {
            $input['ActivationFlag'] = -1;
        }
        if (isset($input['isSupportAdmin']) && $input['isSupportAdmin']) {
            $input['isSupportAdmin'] = -1;
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
            })->where('discharegedYN', 0);
        }

        $employees = $employees
            ->take(20)
            ->get();

        return $this->sendResponse($employees->toArray(), 'Data retrieved successfully');
    }

    public function getEmployeeMasterView(Request $request)
    {
        $input = $request->all();

        //$input = $this->convertArrayToSelectedValue($input, array('cancelYN', 'confirmedYN', 'approved', 'month', 'year', 'supplierID', 'documentType'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $companyId = $request['empCompanySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $empMaster = Employee::whereIn('empCompanySystemID', $childCompanies);
        $empMaster->with(['emp_company', 'manager', 'desi_master' => function ($query) {
            $query->with('designation');
        }]);

        $empMaster = $empMaster->select(
            ['employees.employeeSystemID',
                'employees.empID',
                'employees.empName',
                'employees.empUserName',
                'employees.designation',
                'employees.empCompanyID',
                'employees.empEmail',
                'employees.empManagerAttached',
                'employees.isBasicUser',
                'employees.isManager',
                'employees.isApproval',
                'employees.isAdmin',
                'employees.isSuperAdmin',
                'employees.discharegedYN',
                'employees.empLoginActive',
                'employees.empActive',
                'employees.ActivationFlag',
                'employees.isSupportAdmin',
                'employees.isLock',
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $empMaster = $empMaster->where(function ($query) use ($search) {
                $query->where('empID', 'LIKE', "%{$search}%")
                    ->orWhere('empName', 'LIKE', "%{$search}%")
                    ->orWhere('empUserName', 'LIKE', "%{$search}%")
                    ->orWhere('empEmail', 'LIKE', "%{$search}%");
            });
        }

        $request->request->remove('search.value');
        return \DataTables::of($empMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('employeeSystemID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function confirmEmployeePasswordReset(Request $request)
    {
        $input = $request->all();

        $employeeSystemID = $input['employeeSystemID'];
        $password = 'Gears123@';

        $employeeMasterData = Employee::find($employeeSystemID);
        if (empty($employeeMasterData)) {
            return $this->sendError('Employee not found');
        }

        // updating fields
        $employeeMasterData->isLock = 0;
        $employeeMasterData->empPassword = $password;
        $employeeMasterData->save();

        //updating users table
        $usersMasterData = User::where('employee_id', $employeeSystemID)->first();
        if (!empty($usersMasterData)) {
            $usersMasterUpdate = User::where('employee_id', $employeeSystemID)
                ->update([
                    'password' => bcrypt($password)
                ]);
        }

        //sending emails
        $subject = 'GEARS Password Reset';

        $footer = "<font size='1.5'><i><p><br><br><br>SAVE PAPER - THINK BEFORE YOU PRINT!" .
            "<br>This is an auto generated email. Please do not reply to this email because we are not" .
            "monitoring this inbox. To get in touch with us, email us to systems@gulfenergy-int.com.</font>";

        $body = "Dear " . $employeeMasterData->empName . ',<p> Your GEARS password has been reset to Gears123@' . $footer;

        $dataEmail['empSystemID'] = $employeeMasterData->employeeSystemID;
        $dataEmail['empID'] = $employeeMasterData->empID;
        $dataEmail['empName'] = $employeeMasterData->empName;
        $dataEmail['empEmail'] = $employeeMasterData->empEmail;
        $dataEmail['companySystemID'] = $employeeMasterData->empCompanySystemID;
        $dataEmail['companyID'] = $employeeMasterData->empCompanyID;
        $dataEmail['ccEmailID'] = $employeeMasterData->empEmail;
        $dataEmail['isEmailSend'] = 0;
        $dataEmail['alertMessage'] = $subject;
        $dataEmail['emailAlertMessage'] = $body;

        $sendEmail = Alert::create($dataEmail);

        return $this->sendResponse($employeeMasterData->toArray(), 'Employee password reset successfully');
    }

    public function getEmployeeMasterData(Request $request)
    {
        $selectedCompanySystemID = $request['selectedCompanySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanySystemID);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanySystemID);
        } else {
            $subCompanies = [$selectedCompanySystemID];
        }

        /**  Companies by group  Drop Down */
        $companies = Company::whereIn("companySystemID", $subCompanies)->get();

        $output = array('companies' => $companies);

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


}

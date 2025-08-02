<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyDepartmentEmployeeAPIRequest;
use App\Http\Requests\API\UpdateCompanyDepartmentEmployeeAPIRequest;
use App\Models\CompanyDepartmentEmployee;
use App\Models\Employee;
use App\Models\CompanyDepartment;
use App\Models\BudgetControl;
use App\Models\DepartmentUserBudgetControl;
use App\Repositories\CompanyDepartmentEmployeeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Traits\AuditLogsTrait;
use Yajra\DataTables\DataTables;
use Auth;
use DB;

/**
 * Class CompanyDepartmentEmployeeController
 * @package App\Http\Controllers\API
 */

class CompanyDepartmentEmployeeAPIController extends AppBaseController
{
    use AuditLogsTrait;

    /** @var  CompanyDepartmentEmployeeRepository */
    private $companyDepartmentEmployeeRepository;

    public function __construct(CompanyDepartmentEmployeeRepository $companyDepartmentEmployeeRepo)
    {
        $this->companyDepartmentEmployeeRepository = $companyDepartmentEmployeeRepo;
    }

    /**
     * Get all department employees with DataTables
     *
     * @param Request $request
     * @return Response
     */
    public function getAllDepartmentEmployees(Request $request)
    {
        $departmentSystemID = $request->get('departmentSystemID');
        
        if (!$departmentSystemID) {
            return $this->sendError('Department ID is required');
        }

        $query = CompanyDepartmentEmployee::where('departmentSystemID', $departmentSystemID)
                 ->with(['employee', 'department'])
                 ->orderBy('departmentEmployeeSystemID', 'asc')
                 ->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('employeeCode', function ($departmentEmployee) {
                return $departmentEmployee->employee ? $departmentEmployee->employee->empID : '';
            })
            ->addColumn('employeeName', function ($departmentEmployee) {
                return $departmentEmployee->employee ? $departmentEmployee->employee->empName : '';
            })
            ->addColumn('hodStatus', function ($departmentEmployee) {
                return $departmentEmployee->isHOD == 1 ? 'Yes' : 'No';
            })
            ->addColumn('activeStatus', function ($departmentEmployee) {
                return $departmentEmployee->isActive == 1 ? 'Active' : 'Inactive';
            })
            ->make(true);
    }

    /**
     * Get form data for department employees
     *
     * @param Request $request
     * @return Response
     */
    public function getDepartmentEmployeeFormData(Request $request)
    {
        try {
            // Get all active employees (not discharged)
            $employees = DB::table('employees')
                ->where('isActive', 1)
                ->where('isDischarge', 0)
                ->select('employeeSystemID as value', 
                        DB::raw("CONCAT(employeeCode, ' | ', employeeName) as label"))
                ->orderBy('employeeName')
                ->get()
                ->toArray();

            return $this->sendResponse([
                'employees' => $employees,
                'yesNoSelection' => [
                    ['value' => 0, 'label' => 'No'],
                    ['value' => 1, 'label' => 'Yes']
                ]
            ], 'Form data retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving form data');
        }
    }

    /**
     * Store a newly created department employee
     *
     * @param CreateCompanyDepartmentEmployeeAPIRequest $request
     * @return Response
     */
    public function store(CreateCompanyDepartmentEmployeeAPIRequest $request)
    {
        $input = $request->all();

        try {
            DB::beginTransaction();

            // Handle bulk employee assignment
            if (isset($input['employees']) && is_array($input['employees'])) {
                $results = [];
                $errorMessages = [];

                foreach ($input['employees'] as $employeeData) {
                    $processedData = $this->processUpdateData($employeeData);

                    // Validate HOD constraints
                    if ($processedData['isHOD'] == 1) {
                        // Check if employee is already HOD in another department
                        if ($this->companyDepartmentEmployeeRepository->isEmployeeHODInAnotherDepartment($processedData['employeeSystemID'])) {
                            $errorMessages[] = 'Employee ' . Employee::getEmployeeCode($processedData['employeeSystemID']) . ' is already HOD in another department';
                            continue;
                        }

                        // Check if department already has HOD
                        if ($this->companyDepartmentEmployeeRepository->departmentHasHOD($processedData['departmentSystemID'])) {
                            $errorMessages[] = 'This department already has an HOD assigned';
                            continue;
                        }
                    }

                    // Check if employee is already assigned to this department
                    $exists = CompanyDepartmentEmployee::where('departmentSystemID', $processedData['departmentSystemID'])
                                                      ->where('employeeSystemID', $processedData['employeeSystemID'])
                                                      ->exists();
                    if ($exists) {
                        $errorMessages[] = 'Employee ' . Employee::getEmployeeCode($processedData['employeeSystemID']) . ' is already assigned to this department';
                        continue;
                    }

                    $companyDepartmentEmployee = $this->companyDepartmentEmployeeRepository->create($processedData);
                    
                    // Auto-assign budget controls if finance department
                    $this->autoAssignBudgetControlsForFinance($companyDepartmentEmployee->departmentEmployeeSystemID);
                    
                    // Audit log
                    $uuid = $request->get('tenant_uuid', 'local');
                    $db = $request->get('db', '');
                    $this->auditLog($db, $companyDepartmentEmployee->departmentEmployeeSystemID, $uuid, "company_departments_employees", "Employee assigned to department", "C", $companyDepartmentEmployee->toArray(), [], $processedData['departmentSystemID'], 'company_departments');
                    
                    $results[] = $companyDepartmentEmployee;
                }
                
                if (!empty($errorMessages)) {
                    DB::rollback();
                    return $this->sendError('Some employees could not be assigned: ' . implode(', ', $errorMessages));
                }
                DB::commit();

                return $this->sendResponse($results, count($results) . ' employee(s) assigned to department successfully');
            } else {
                // Handle single employee assignment (backward compatibility)
                $processedData = $this->processUpdateData($input);

                // Validate HOD constraints
                if ($processedData['isHOD'] == 1) {
                    // Check if employee is already HOD in another department
                    if ($this->companyDepartmentEmployeeRepository->isEmployeeHODInAnotherDepartment($processedData['employeeSystemID'])) {
                        return $this->sendError('This employee is already HOD in another department');
                    }

                    // Check if department already has HOD
                    if ($this->companyDepartmentEmployeeRepository->departmentHasHOD($processedData['departmentSystemID'])) {
                        return $this->sendError('This department already has an HOD assigned');
                    }
                }

                $companyDepartmentEmployee = $this->companyDepartmentEmployeeRepository->create($processedData);

                // Auto-assign budget controls if finance department
                $this->autoAssignBudgetControlsForFinance($companyDepartmentEmployee->departmentEmployeeSystemID);

                // Audit log
                $uuid = $request->get('tenant_uuid', 'local');
                $db = $request->get('db', '');
                $this->auditLog($db, $companyDepartmentEmployee->departmentEmployeeSystemID, $uuid, "company_departments_employees", "Employee assigned to department", "C", $companyDepartmentEmployee->toArray(), [], $processedData['departmentSystemID'], 'company_departments');

                DB::commit();

                return $this->sendResponse($companyDepartmentEmployee->toArray(), 'Employee assigned to department successfully');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error assigning employee to department - '.$e->getMessage());
        }
    }

    /**
     * Update the specified department employee
     *
     * @param int $id
     * @param UpdateCompanyDepartmentEmployeeAPIRequest $request
     * @return Response
     */
    public function update($id, UpdateCompanyDepartmentEmployeeAPIRequest $request)
    {
        $companyDepartmentEmployee = $this->companyDepartmentEmployeeRepository->find($id);

        if (empty($companyDepartmentEmployee)) {
            return $this->sendError('Department Employee not found');
        }

        $input = $request->all();
        $oldValues = $companyDepartmentEmployee->toArray();

        try {
            DB::beginTransaction();

            // Process array inputs to single values
            $processedData = $this->processUpdateData($input);

            // Handle HOD logic
            if ($processedData['isHOD'] == 1) {
                // Check if employee is already HOD in another department (excluding current)
                if ($this->companyDepartmentEmployeeRepository->isEmployeeHODInAnotherDepartment($processedData['employeeSystemID'], $processedData['departmentSystemID'])) {
                    return $this->sendError('This employee is already HOD in another department');
                }

                // Remove HOD status from any existing HOD in this department
                CompanyDepartmentEmployee::where('departmentSystemID', $processedData['departmentSystemID'])
                    ->where('isHOD', 1)
                    ->where('departmentEmployeeSystemID', '!=', $id)
                    ->update(['isHOD' => 0]);
            }

            $companyDepartmentEmployee = $this->companyDepartmentEmployeeRepository->update($processedData, $id);

            // Audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $id, $uuid, "company_departments_employees", "Department employee assignment updated", "U", $companyDepartmentEmployee->toArray(), $oldValues, $processedData['departmentSystemID'], 'company_departments');

            DB::commit();

            return $this->sendResponse($companyDepartmentEmployee->toArray(), 'Department Employee updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error updating department employee', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified department employee
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        $companyDepartmentEmployee = $this->companyDepartmentEmployeeRepository->find($id);

        if (empty($companyDepartmentEmployee)) {
            return $this->sendError('Department Employee not found');
        }

        try {
            $previousValue = $companyDepartmentEmployee->toArray();
            
            $companyDepartmentEmployee->delete();

            // Audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $id, $uuid, "company_departments_employees", "Employee removed from department", "D", [], $previousValue, $previousValue['departmentSystemID'], 'company_departments');

            return $this->sendResponse($id, 'Employee removed from department successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error removing employee from department', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Process update data to handle arrays and convert to proper format
     *
     * @param array $input
     * @return array
     */
    private function processUpdateData($input)
    {
        $allowedFields = [
            'departmentSystemID', 'employeeSystemID', 'isHOD', 'isActive'
        ];
        
        $processedData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $value = $input[$field];
                
                // Convert array to single value
                if (is_array($value)) {
                    $value = count($value) > 0 ? $value[0] : null;
                }
                
                // Cast specific fields to integers
                switch ($field) {
                    case 'isHOD':
                    case 'isActive':
                    case 'departmentSystemID':
                    case 'employeeSystemID':
                        $processedData[$field] = is_numeric($value) ? (int)$value : $value;
                        break;
                    default:
                        $processedData[$field] = $value;
                        break;
                }
            }
        }
        
        return $processedData;
    }

    /**
     * Auto-assign all budget controls to employee if department is finance
     */
    private function autoAssignBudgetControlsForFinance($departmentEmployeeSystemID)
    {
        try {
            // Get department info through the employee assignment
            $departmentEmployee = CompanyDepartmentEmployee::find($departmentEmployeeSystemID);
            if (!$departmentEmployee) {
                return;
            }

            $department = CompanyDepartment::find($departmentEmployee->departmentSystemID);
            if (!$department) {
                return;
            }

            if ($department->isFinance) {
                // Get all active budget controls
                $budgetControls = BudgetControl::where('isActive', 1)->get();
                
                // Assign all budget controls to this employee
                foreach ($budgetControls as $budgetControl) {
                    // Check if already assigned to avoid duplicates
                    $exists = DepartmentUserBudgetControl::where('departmentEmployeeSystemID', $departmentEmployeeSystemID)
                                                        ->where('budgetControlID', $budgetControl->budgetControlID)
                                                        ->exists();
                    
                    if (!$exists) {
                        DepartmentUserBudgetControl::create([
                            'departmentEmployeeSystemID' => $departmentEmployeeSystemID,
                            'budgetControlID' => $budgetControl->budgetControlID
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the main process
            \Log::error('Error auto-assigning budget controls: ' . $e->getMessage());
        }
    }
} 
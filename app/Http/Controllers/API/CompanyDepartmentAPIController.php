<?php
/**
 * =============================================
 * -- File Name : CompanyDepartmentAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Company Department
 * -- Author : System Generated
 * -- Create date : 18 - December 2024
 * -- Description : This file contains the all CRUD for Company Department
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCompanyDepartmentAPIRequest;
use App\Http\Requests\API\UpdateCompanyDepartmentAPIRequest;
use App\Models\CompanyDepartment;
use App\Models\Company;
use App\Models\YesNoSelection;
use App\Repositories\CompanyDepartmentRepository;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditLogsTrait;
use Yajra\DataTables\DataTables;
use App\helper\CreateExcel;

/**
 * Class CompanyDepartmentController
 * @package App\Http\Controllers\API
 */
class CompanyDepartmentAPIController extends AppBaseController
{
    /** @var  CompanyDepartmentRepository */
    private $companyDepartmentRepository;
    private $userRepository;
    use AuditLogsTrait;

    public function __construct(CompanyDepartmentRepository $companyDepartmentRepo, UserRepository $userRepo)
    {
        $this->companyDepartmentRepository = $companyDepartmentRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Process and clean update data
     * Convert arrays to single values and filter allowed fields
     */
    private function processUpdateData($input)
    {
        $allowedFields = [
            'departmentCode',
            'departmentDescription',
            'companySystemID',
            'type',
            'parentDepartmentID',
            'isFinance',
            'isActive'
        ];

        $processedData = [];

        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $value = $input[$field];
                
                // Convert arrays to single values
                if (is_array($value)) {
                    $value = count($value) > 0 ? $value[0] : null;
                }
                
                // Handle specific field conversions
                switch ($field) {
                    case 'parentDepartmentID':
                        // Convert 'company' to null
                        $processedData[$field] = ($value === 'company') ? null : $value;
                        break;
                    case 'isFinance':
                    case 'isActive':
                    case 'type':
                    case 'companySystemID':
                        // Ensure these are integers
                        $processedData[$field] = is_numeric($value) ? (int)$value : $value;
                        break;
                    default:
                        $processedData[$field] = $value;
                        break;
                }
            } else {
                $processedData[$field] = null;
            }
        }

        return $processedData;
    }

    /**
     * Display a listing of the CompanyDepartment.
     * GET|HEAD /companyDepartments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyDepartmentRepository->pushCriteria(new RequestCriteria($request));
        $this->companyDepartmentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyDepartments = $this->companyDepartmentRepository->all();

        return $this->sendResponse($companyDepartments->toArray(), trans('custom.company_departments_retrieved_successfully'));
    }

    /**
     * Get all departments with DataTables support
     * 
     * @param Request $request
     * @return Response
     */
    public function getAllCompanyDepartments(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'] ?? null;
        
        $search = $request->input('search.value');

        $query = CompanyDepartment::with(['company', 'parent', 'created_by', 'modified_by', 'hod.employee'])->orderBy('departmentSystemID', 'desc');

        if ($companyId) {
            $query->where('companySystemID', $companyId);
        }

        if (isset($input['isActive']) && $input['isActive'] !== null && $input['isActive'] !== '') {
            $query->where('isActive', $input['isActive']);
        }

        if (isset($input['departmentSystemID']) && $input['departmentSystemID'] !== null && $input['departmentSystemID'] !== '') {
            $query->where('departmentSystemID', $input['departmentSystemID']);
        }

        if (isset($input['hod']) && $input['hod'] !== null && $input['hod'] !== '') {
            $query->whereHas('hod', function($q) use ($input) {
                $q->where('employeeSystemID', $input['hod']);
            });
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('departmentCode', 'like', "%$search%")
                  ->orWhere('departmentDescription', 'like', "%$search%")
                  ->orWhereHas('parent', function($q) use ($search) {
                    $q->where('departmentDescription', 'like', "%$search%")
                      ->orWhere('departmentCode', 'like', "%$search%");                        
                  })
                  ->orWhere(function($q) use ($search) {
                    $q->whereHas('company', function($q) use ($search) {
                        $q->where('CompanyName', 'like', "%$search%");
                    })
                    ->whereNull('parentDepartmentID');
                  })
                  ->orWhereHas('hod.employee', function($q) use ($search) {
                    $q->where('empName', 'like', "%$search%");
                  });
            });
        }

        $request->request->remove('order');
        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);
        $request->request->remove('search.value');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('parentDepartment', function ($row) {
                return $row->parent ? $row->parent->departmentDescription : ($row->company ? $row->company->CompanyName : '-');
            })
            ->rawColumns(['departmentCode', 'departmentDescription'])
            ->make(true);
    }

    /**
     * Store a newly created CompanyDepartment in storage.
     * POST /companyDepartments
     *
     * @param CreateCompanyDepartmentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyDepartmentAPIRequest $request)
    {
        $input = $request->all();
        
        // Process and clean input data
        $createData = $this->processUpdateData($input);
        
        // Check if department code is unique
        $existingDepartment = CompanyDepartment::where('departmentCode', $createData['departmentCode'])
                                               ->where('companySystemID', $createData['companySystemID'])
                                               ->first();
        
        if ($existingDepartment) {
            return $this->sendAPIError(trans('custom.department_code_must_be_unique'), 422);
        }

        // Check if trying to set as finance department when one already exists
        if (isset($createData['isFinance']) && $createData['isFinance'] == 1) {
            if ($this->companyDepartmentRepository->hasFinanceDepartment($createData['companySystemID'])) {
                return $this->sendAPIError(trans('custom.finance_department_already_created'), 422);
            }
        }

        // Set default values
        $createData['createdUserSystemID'] = Auth::id();
        $createData['modifiedUserSystemID'] = Auth::id();

        $companyDepartment = $this->companyDepartmentRepository->create($createData);

        // Add audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $companyDepartment->departmentSystemID, $uuid, "company_departments", "Department master ".$companyDepartment->departmentDescription." has been created", "C", $companyDepartment->toArray(), []);

        return $this->sendResponse($companyDepartment->toArray(), trans('custom.company_department_saved_successfully'));
    }

    /**
     * Display the specified CompanyDepartment.
     * GET|HEAD /companyDepartments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $companyDepartment = $this->companyDepartmentRepository->findWithoutFail($id);

        if (empty($companyDepartment)) {
            return $this->sendError(trans('custom.company_department_not_found'));
        }

        $companyDepartment->load(['company', 'parent', 'children', 'created_by', 'modified_by']);

        return $this->sendResponse($companyDepartment->toArray(), trans('custom.company_department_retrieved_successfully'));
    }

    /**
     * Update the specified CompanyDepartment in storage.
     * PUT/PATCH /companyDepartments/{id}
     *
     * @param  int $id
     * @param UpdateCompanyDepartmentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyDepartmentAPIRequest $request)
    {
        $companyDepartment = $this->companyDepartmentRepository->findWithoutFail($id);

        if (empty($companyDepartment)) {
            return $this->sendError(trans('custom.company_department_not_found'));
        }

        $input = $request->all();
        $oldValues = $companyDepartment->toArray();

        // Process and clean input data
        $updateData = $this->processUpdateData($input);

        // Check if trying to change parent to final when has children
        if (isset($updateData['type']) && $updateData['type'] == 2 && $companyDepartment->type == 1) {
            if ($this->companyDepartmentRepository->hasChildren($id)) {
                return $this->sendAPIError(trans('custom.child_department_already_created'), 422);
            }
        }

        // Check if trying to set as finance department when one already exists
        if (isset($updateData['isFinance']) && $updateData['isFinance'] == 1 && $companyDepartment->isFinance != 1) {
            if ($this->companyDepartmentRepository->hasFinanceDepartment($updateData['companySystemID'], $id)) {
                return $this->sendAPIError(trans('custom.finance_department_already_created'), 422);
            }
        }

        // same department cannot be set as parent department
        if (isset($updateData['parentDepartmentID']) && $updateData['parentDepartmentID'] == $id) {
            return $this->sendAPIError(trans('custom.department_cannot_be_parent'), 422);
        }

        // Check if department code is unique
        $existingDepartment = CompanyDepartment::where('departmentCode', $updateData['departmentCode'])
                                               ->where('companySystemID', $updateData['companySystemID'])
                                               ->where('departmentSystemID', '!=', $id)
                                               ->first();
        
        if ($existingDepartment) {  
            return $this->sendAPIError(trans('custom.department_code_must_be_unique'), 422);
        }

        $updateData['modifiedUserSystemID'] = Auth::id();

        $companyDepartment = $this->companyDepartmentRepository->update($updateData, $id);

        // Add audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "company_departments", "Department master ".$companyDepartment->departmentDescription." has been updated", "U", $companyDepartment->toArray(), $oldValues);

        return $this->sendResponse($companyDepartment->toArray(), trans('custom.company_department_updated_successfully'));
    }

    /**
     * Check if department has finance team employees
     * GET /company-departments/{id}/check-finance-employees
     *
     * @param  int $id
     *
     * @return Response
     */
    public function checkFinanceEmployees($id)
    {
        $companyDepartment = $this->companyDepartmentRepository->findWithoutFail($id);

        if (empty($companyDepartment)) {
            return $this->sendError(trans('custom.company_department_not_found'));
        }

        // Check if department has employees assigned
        $employeeCount = \App\Models\CompanyDepartmentEmployee::where('departmentSystemID', $id)->count();
        
        $hasEmployees = $employeeCount > 0;
        
        return $this->sendResponse([
            'hasEmployees' => $hasEmployees,
            'employeeCount' => $employeeCount
        ], trans('custom.finance_team_employees_check_completed'));
    }

    /**
     * Remove the specified CompanyDepartment from storage.
     * DELETE /companyDepartments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request)
    {
        $companyDepartment = $this->companyDepartmentRepository->findWithoutFail($id);

        if (empty($companyDepartment)) {
            return $this->sendError(trans('custom.company_department_not_found'));
        }

        $previousValue = $companyDepartment->toArray();

        // Check if department has children
        if ($this->companyDepartmentRepository->hasChildren($id)) {
            return $this->sendAPIError(trans('custom.cannot_delete_department_with_children'), 422);
        }

        // Check if department has employees assigned
        $employeeCount = \App\Models\CompanyDepartmentEmployee::where('departmentSystemID', $id)->count();
        if ($employeeCount > 0) {
            return $this->sendAPIError(trans('custom.cannot_delete_department_with_employees'), 422);
        }

        // Check if department has segments assigned
        $segmentCount = \App\Models\CompanyDepartmentSegment::where('departmentSystemID', $id)->count();
        if ($segmentCount > 0) {
            return $this->sendAPIError(trans('custom.cannot_delete_department_with_segments'), 422);
        }

        // Check if department has budget templates assigned
        $budgetTemplateCount = \App\Models\DepartmentBudgetTemplate::where('departmentSystemID', $id)->count();
        if ($budgetTemplateCount > 0) {
            return $this->sendAPIError(trans('custom.cannot_delete_department_with_budget_templates'), 422);
        }

        // Delete the department
        $companyDepartment->delete();

        // Add audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "company_departments", "Department master ".$companyDepartment->departmentDescription." has been deleted", "D", [], $previousValue);

        return $this->sendResponse($id, trans('custom.company_department_deleted_successfully'));
    }

    /**
     * Get form data for department creation/editing
     *
     * @param Request $request
     * @return Response
     */
    public function getCompanyDepartmentFormData(Request $request)
    {
        $companyId = $request->get('companyId');
        
        // Get company information
        $company = Company::where('companySystemID', $companyId)->first();
        
        // Get parent departments for the company
        $parentDepartments = $this->companyDepartmentRepository->getParentDepartments($companyId);

        $department = CompanyDepartment::where('companySystemID', $companyId)->get();

        $hods = DB::table('company_departments_employees')
            ->join('employees', 'company_departments_employees.employeeSystemID', '=', 'employees.employeeSystemID')
            ->join('company_departments', 'company_departments_employees.departmentSystemID', '=', 'company_departments.departmentSystemID')
            ->where('company_departments.companySystemID', $companyId)
            ->where('company_departments_employees.isHOD', 1)
            ->select('company_departments_employees.employeeSystemID', 'employees.empName', 'employees.empID')
            ->get();
        
        $data = [
            'yesNoSelection' => YesNoSelection::all(),
            'typeSelection' => [
                ['value' => 1, 'label' => trans('custom.parent')],
                ['value' => 2, 'label' => trans('custom.final')]
            ],
            'company' => $company,
            'department' => $department,
            'parentDepartments' => $parentDepartments,
            'hods' => $hods
        ];

        return $this->sendResponse($data, trans('custom.form_data_retrieved_successfully'));
    }



    /**
     * Get department structure
     *
     * @param Request $request
     * @return Response
     */
    public function getDepartmentStructure(Request $request)
    {
        $companyId = $request->get('companyId');
        
        // Get company with departments like segment structure
        $orgStructure = Company::withcount(['departments'])
                            ->with(['departments' => function ($q) {
                                $q->whereNull('parentDepartmentID')
                                  ->with('children.hod.employee')
                                  ->withCount('employees')
                                  ->with('hod.employee');
                            }])
                            ->find($companyId);

        if (empty($orgStructure)) {
            return $this->sendError(trans('custom.company_not_found'));
        }

        return $this->sendResponse(['orgData' => $orgStructure, 'isGroup' => false], trans('custom.department_structure_retrieved_successfully'));
    }

    /**
     * Export departments to Excel
     *
     * @param Request $request
     * @return Response
     */
    public function exportCompanyDepartments(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyId'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $departmentId = $input['departmentSystemID'] ?? null;
        $isActive = $input['isActive'] ?? null;
        $type = $input['type'] ?? null;
        $parentDepartmentID = $input['parentDepartmentID'] ?? null;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        $departments = CompanyDepartment::whereIn('companySystemID', $childCompanies)
            ->with(['company', 'parent']);

        if (isset($departmentId) && !is_null($departmentId)) {
            $departments->where('departmentSystemID', $departmentId);
        }

        if (isset($isActive) && !is_null($isActive)) {
            $departments->where('isActive', $isActive);
        }

        if (isset($type) && !is_null($type)) {
            $departments->where('type', $type);
        }

        if (isset($parentDepartmentID) && !is_null($parentDepartmentID)) {
            $departments->where('parentDepartmentID', $parentDepartmentID);
        }

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $departments = $departments->where(function ($query) use ($search) {
                $query->where('departmentCode', 'LIKE', "%{$search}%")
                    ->orWhere('departmentDescription', 'LIKE', "%{$search}%");
            });
        }

        $departments = $departments->orderBy('departmentSystemID', 'desc')->get();

        $data = array();
        $x = 0;
        foreach ($departments as $val) {
            $x++;
            $data[$x]['Department Code'] = $val->departmentCode;
            $data[$x]['Department Description'] = $val->departmentDescription;
            // $data[$x]['Type'] = ($val->type == 1) ? 'Parent' : 'Final';
            $data[$x]['Parent Department'] = !is_null($val->parentDepartmentID) ? ($val->parent ? $val->parent->departmentDescription : '-') : $val->company->CompanyName;
            // $data[$x]['Is Finance'] = ($val->isFinance == 1) ? 'Yes' : 'No';
            $data[$x]['HOD'] = $val->hod ? $val->hod->employee->empName : '-';
            $data[$x]['Active Status'] = ($val->isActive == 1) ? trans('custom.yes') : trans('custom.no');  
            // $data[$x]['Company'] = $val->company ? $val->company->companyName : '';
            // $data[$x]['Created Date'] = $val->created_at ? $val->created_at->format('d/m/Y') : '';
        }

        $companyMaster = Company::find(isset($request->companyId) ? $request->companyId : null);
        $companyCode = $companyMaster->CompanyID ?? 'common';
        $detail_array = array(
            'company_code' => $companyCode,
        );

        $fileName = 'department_master';
        $path = 'system/department_master/excel/';
        $type = 'xls';
        $basePath = CreateExcel::process($data, $type, $fileName, $path, $detail_array);

        if ($basePath == '') {
            return $this->sendError(trans('custom.unable_to_export_excel'));
        } else {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }
} 
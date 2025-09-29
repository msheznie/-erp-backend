<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetTemplateAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetTemplateAPIRequest;
use App\Jobs\ProcessDepartmentBudgetPlanningDetailsJob;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetTemplate;
use App\Repositories\DepartmentBudgetTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Yajra\DataTables\DataTables;
use App\Traits\AuditLogsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepartmentBudgetTemplateAPIController extends AppBaseController
{
    use AuditLogsTrait;
    
    private $departmentBudgetTemplateRepository;

    public function __construct(DepartmentBudgetTemplateRepository $departmentBudgetTemplateRepo)
    {
        $this->departmentBudgetTemplateRepository = $departmentBudgetTemplateRepo;
    }

    /**
     * Display a listing of the DepartmentBudgetTemplate.
     * GET|HEAD /departmentBudgetTemplates
     */
    public function index(Request $request)
    {
        $departmentBudgetTemplates = $this->departmentBudgetTemplateRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($departmentBudgetTemplates->toArray(), trans('custom.department_budget_templates_retrieved_successfully'));
    }

    /**
     * Store a newly created DepartmentBudgetTemplate in storage.
     * POST /departmentBudgetTemplates
     */
    public function store(CreateDepartmentBudgetTemplateAPIRequest $request)
    {
        $input = $request->all();

        // Check if template is already assigned to department
        if ($this->departmentBudgetTemplateRepository->isTemplateAssigned($input['departmentSystemID'], $input['budgetTemplateID'])) {
            return $this->sendError(trans('custom.this_budget_template_is_already_assigned_to_the_de'));
        }

        // Get the budget template to check its type
        $budgetTemplate = \App\Models\BudgetTemplate::find($input['budgetTemplateID']);
        if (!$budgetTemplate) {
            return $this->sendError(trans('custom.budget_template_not_found'));
        }

        // Check if department already has an active template of the same type
        $hasActiveTemplateOfType = $this->departmentBudgetTemplateRepository
            ->hasActiveTemplateOfType($input['departmentSystemID'], $budgetTemplate->type);

        $input['isActive'] = 0;
        // // If there's already an active template of this type, set new template as inactive
        // if ($hasActiveTemplateOfType) {
        //     $input['isActive'] = 0;
        //     $message = 'Budget template assigned successfully as inactive (another template of this type is already active)';
        // } else {
        //     // If no active template of this type exists, set as active
        //     $input['isActive'] = 1;
        //     $message = 'Budget template assigned successfully.';
        // }
        $message = 'Budget template assigned successfully.';

        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->create($input);

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $departmentBudgetTemplate->departmentBudgetTemplateID, $uuid, "department_budget_templates", "Budget template assigned to department", "C", $departmentBudgetTemplate->toArray(), [], $input['departmentSystemID'], 'company_departments');

        return $this->sendResponse($departmentBudgetTemplate->toArray(), $message);
    }

    /**
     * Display the specified DepartmentBudgetTemplate.
     * GET|HEAD /departmentBudgetTemplates/{id}
     */
    public function show($id)
    {
        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($id);

        if (empty($departmentBudgetTemplate)) {
            return $this->sendError(trans('custom.department_budget_template_not_found'));
        }

        return $this->sendResponse($departmentBudgetTemplate->toArray(), trans('custom.department_budget_template_retrieved_successfully'));
    }

    /**
     * Update the specified DepartmentBudgetTemplate in storage.
     * PUT/PATCH /departmentBudgetTemplates/{id}
     */
    public function update($id, UpdateDepartmentBudgetTemplateAPIRequest $request)
    {
        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($id);

        if (empty($departmentBudgetTemplate)) {
            return $this->sendError(trans('custom.department_budget_template_not_found'));
        }

        $oldValues = $departmentBudgetTemplate->toArray();
        $input = $request->all();

        // If activating a template, handle business logic
        if (isset($input['isActive']) && $input['isActive'] == 1) {
            // Get the budget template to check its type
            $budgetTemplate = \App\Models\BudgetTemplate::find($departmentBudgetTemplate->budgetTemplateID);
            
            if ($budgetTemplate) {
                // Deactivate other templates of the same type for this department
                $this->departmentBudgetTemplateRepository->deactivateOtherTemplatesOfType(
                    $departmentBudgetTemplate->departmentSystemID,
                    $budgetTemplate->type,
                    $departmentBudgetTemplate->budgetTemplateID
                );
                
                $message = 'Template activated successfully. Other templates of the same type have been deactivated.';
            } else {
                $message = 'Template updated successfully';
            }
        } else {
            $message = 'Template updated successfully';
        }

        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->update($input, $id);

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "department_budget_templates", "Department budget template updated", "U", $departmentBudgetTemplate->toArray(), $oldValues, $departmentBudgetTemplate->departmentSystemID, 'company_departments');

        return $this->sendResponse($departmentBudgetTemplate->toArray(), $message);
    }

    /**
     * Remove the specified DepartmentBudgetTemplate from storage.
     * DELETE /departmentBudgetTemplates/{id}
     */
    public function destroy($id, Request $request)
    {
        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($id);

        if (empty($departmentBudgetTemplate)) {
            return $this->sendError(trans('custom.department_budget_template_not_found'));
        }

        $previousValue = $departmentBudgetTemplate->toArray();

        //delete all gl codes assigned to the template
        \App\Models\DepBudgetTemplateGl::where('departmentBudgetTemplateID', $id)->delete();

        $departmentBudgetTemplate->delete();

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "department_budget_templates", "Department budget template deleted", "D", [], $previousValue, $previousValue['departmentSystemID'], 'company_departments');

        return $this->sendResponse($id, trans('custom.department_budget_template_deleted_successfully'));
    }

    /**
     * Get department budget templates for DataTables
     * POST /departmentBudgetTemplates/datatable/{departmentSystemID}
     */
    public function getDepartmentBudgetTemplates($departmentSystemID, Request $request)
    {
        $query = DepartmentBudgetTemplate::where('departmentSystemID', $departmentSystemID)
                 ->with(['budgetTemplate'])
                 ->withCount('depBudgetTemplateGls')
                 ->orderBy('departmentBudgetTemplateID', 'desc')
                 ->get();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('template_type_label', function ($row) {

                if (isset($row->budgetTemplate) && isset($row->budgetTemplate->type)) {
                    switch ($row->budgetTemplate->type) {
                        case '1':
                            return 'OPEX';
                        case '2':
                            return 'CAPEX';
                        case '3':
                            return 'Common';
                        default:
                            return 'Unknown';
                    }
                } else {
                    return 'Unknown';
                }
            })
            ->addColumn('is_active_label', function ($row) {
                return $row->isActive ? 'Yes' : 'No';
            })
            ->addColumn('gl_codes_count', function ($row) {
                return $row->dep_budget_template_gls_count ?? 0;
            })
            ->make(true);
    }

    /**
     * Get budget templates by type
     * GET /departmentBudgetTemplates/templates-by-type/{type}
     */
    public function getBudgetTemplatesByType($type)
    {
        $templates = $this->departmentBudgetTemplateRepository->getBudgetTemplatesByType($type);

        return $this->sendResponse($templates->toArray(), trans('custom.budget_templates_retrieved_successfully'));
    }

    /**
     * Get form data
     */
    public function getFormData()
    {
        try {
            $budgetTypes = [
                ['value' => 1, 'label' => 'OPEX'],
                ['value' => 2, 'label' => 'CAPEX'],
                ['value' => 3, 'label' => 'Both']
            ];

            return $this->sendResponse(['budgetTypes' => $budgetTypes], trans('custom.form_data_retrieved_successfully'));
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_fetching_form_data'), $e->getMessage());
        }
    }

    /**
     * Get chart of accounts for template type
     */
    public function getChartOfAccountsForTemplate($templateType)
    {
        try {
            // Get approved chart of accounts assigned to company
            $chartOfAccounts = \App\Models\ChartOfAccount::where('isApproved', 1)
                ->where('companySystemID', auth()->user()->companySystemID ?? 1)
                ->select('chartOfAccountSystemID', 'glCode', 'description', 'accountType')
                ->selectRaw("CONCAT(glCode, ' - ', description) as glCodeDescription")
                ->orderBy('glCode')
                ->get();

            return $this->sendResponse($chartOfAccounts, trans('custom.chart_of_accounts_retrieved_successfully'));
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_fetching_chart_of_accounts'), $e->getMessage());
        }
    }

    public function getChartOfAccountsByBudgetTemplate(Request $request)
    {
        $input = $request->all();

        $items = \App\Models\ChartOfAccount::where('isActive', 1)->where('isApproved', 1)
            ->whereHas('chartofaccount_assigned', function ($query) use ($input) {
                $query->where('companySystemID', $input['companySystemID'])
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            })->when($input['templateType'] == 1, function ($query) {
                $query->where('catogaryBLorPL', 'PL');
            })->when($input['templateType'] == 2, function ($query) {
                $query->where('catogaryBLorPL', 'BS');
            })
            ->whereNotNull('reportTemplateCategory')
            ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'catogaryBLorPL', 'controlAccounts');


        if (isset($input['departmentBudgetTemplateID']))
        {
            $tempDetail = \App\Models\DepBudgetTemplateGl::where('departmentBudgetTemplateID', $input['departmentBudgetTemplateID'])->pluck('chartOfAccountSystemID')->toArray();
        }else {
            $budgetPlanning = DepartmentBudgetPlanning::with('budgetPlanningDetails')->find($input['budgetPlanningID']);
            $departmentBudgeTemplateID = DepartmentBudgetTemplate::where('departmentSystemID',$budgetPlanning->departmentID)->where('budgetTemplateID',$budgetPlanning->budgetPlanningDetails->first()['budget_template_id'])->first();
            $input['departmentBudgetTemplateID'] = $departmentBudgeTemplateID->departmentBudgetTemplateID;
            $tempDetail = \App\Models\DepBudgetTemplateGl::where('departmentBudgetTemplateID', $input['departmentBudgetTemplateID'])->pluck('chartOfAccountSystemID')->toArray();
        }
        $items = $items->whereNotIn('chartOfAccountSystemID', array_filter($tempDetail))->get();

        return $this->sendResponse($items, trans('custom.chart_of_accounts_retrieved_successfully'));
    }

    /**
     * Get chart of accounts by type
     */
    public function getChartOfAccountsByType($templateType, $accountType)
    {
        try {
            $query = \App\Models\ChartOfAccount::where('isApproved', 1)
                ->where('companySystemID', auth()->user()->companySystemID ?? 1);

            // Filter by account type
            switch (strtoupper($accountType)) {
                case 'BS':
                    $query->where('accountType', 'BS');
                    break;
                case 'BSA':
                    $query->where('accountType', 'BSA');
                    break;
                case 'BSL':
                    $query->where('accountType', 'BSL');
                    break;
                case 'BSE':
                    $query->where('accountType', 'BSE');
                    break;
                case 'PL':
                    $query->where('accountType', 'PL');
                    break;
                case 'PLE':
                    $query->where('accountType', 'PLE');
                    break;
                case 'PLI':
                    $query->where('accountType', 'PLI');
                    break;
            }

            $chartOfAccounts = $query->select('chartOfAccountSystemID', 'glCode', 'description', 'accountType')
                ->selectRaw("CONCAT(glCode, ' - ', description) as glCodeDescription")
                ->orderBy('glCode')
                ->get();

            return $this->sendResponse($chartOfAccounts, trans('custom.chart_of_accounts_retrieved_successfully'));
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_fetching_chart_of_accounts'), $e->getMessage());
        }
    }

    /**
     * Assign GL codes to department budget template
     */
    public function assignGLCodes(Request $request)
    {
        try {
            $input = $request->all();
            
            $departmentBudgetTemplateID = $input['departmentBudgetTemplateID'];
            $chartOfAccountSystemIDs = $input['chartOfAccountSystemIDs'];
            $selectedSegments = $input['selectedSegments'] ?? null;


            if(!empty($selectedSegments))
            {
                $selectedSegments = collect($selectedSegments)->pluck('id')->toArray();
            }
            if(is_null($departmentBudgetTemplateID))
            {
                if($input['budgetPlanningID']){
                    $budgetPlanning = DepartmentBudgetPlanning::with('budgetPlanningDetails')->find($input['budgetPlanningID']);
                    $departmentBudgeTemplateID = DepartmentBudgetTemplate::where('departmentSystemID',$budgetPlanning->departmentID)->where('budgetTemplateID',$budgetPlanning->budgetPlanningDetails->first()['budget_template_id'])->first();
                    $departmentBudgetTemplateID = $departmentBudgeTemplateID->departmentBudgetTemplateID;

                    if($budgetPlanning->workStatus == 2 && empty($selectedSegments))
                    {
                        return $this->sendError("Please select at least one segment");
                    }
                }
            }

            // Validate that the department budget template exists
            $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($departmentBudgetTemplateID);


            if (!$departmentBudgetTemplate) {
                return $this->sendError(trans('custom.department_budget_template_not_found'));
            }

            // // Delete existing GL assignments for this template
            // \App\Models\DepBudgetTemplateGl::where('departmentBudgetTemplateID', $departmentBudgetTemplateID)->delete();

            // Create new GL assignments
            $assignedCount = 0;
            foreach ($chartOfAccountSystemIDs as $chartOfAccountSystemID) {
                \App\Models\DepBudgetTemplateGl::create([
                    'departmentBudgetTemplateID' => $departmentBudgetTemplateID,
                    'chartOfAccountSystemID' => $chartOfAccountSystemID,
                    'createdUserSystemID' => auth()->id(),
                    'modifiedUserSystemID' => auth()->id()
                ]);
                $assignedCount++;
            }

            if(isset($input['budgetPlanningID'])){
                $db = $request->input('db', '');

                // Dispatch job to process department budget planning details
                \App\Jobs\ProcessDepartmentBudgetPlanningDetailsJob::dispatch(
                    $db,
                    $input['budgetPlanningID'],
                    auth()->id(),
                    $chartOfAccountSystemIDs,
                    $selectedSegments
                );
            }

            return $this->sendResponse(
                ['assignedCount' => $assignedCount], 
                "Successfully assigned {$assignedCount} GL codes to the budget template"
            );
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_assigning_gl_codes'), $e->getMessage());
        }
    }

    /**
     * Get assigned GL codes for a department budget template
     */
    public function getAssignedGLCodes(Request $request)
    {
        try {
            $departmentBudgetTemplateID = $request->get('departmentBudgetTemplateID');

            if (!$departmentBudgetTemplateID) {
                return $this->sendError(trans('custom.department_budget_template_id_is_required'));
            }

            // Get assigned GL codes with chart of account details
            $assignedGLCodes = \App\Models\DepBudgetTemplateGl::where('departmentBudgetTemplateID', $departmentBudgetTemplateID)
                ->join('chartofaccounts', 'dep_budget_template_gl.chartOfAccountSystemID', '=', 'chartofaccounts.chartOfAccountSystemID')
                ->join('erp_companyreporttemplatedetails', 'erp_companyreporttemplatedetails.detID', '=', 'chartofaccounts.reportTemplateCategory')
                ->select(
                    'dep_budget_template_gl.*',
                    'chartofaccounts.AccountCode',
                    'chartofaccounts.AccountDescription',
                    'chartofaccounts.catogaryBLorPL',
                    'erp_companyreporttemplatedetails.description',
                    'chartofaccounts.controlAccounts'
                )
                ->orderBy('chartofaccounts.catogaryBLorPL')
                ->orderBy('chartofaccounts.controlAccounts')
                ->orderBy('chartofaccounts.AccountCode')
                ->get();

            return $this->sendResponse($assignedGLCodes, trans('custom.assigned_gl_codes_retrieved_successfully'));
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_fetching_assigned_gl_codes'), $e->getMessage());
        }
    }
} 

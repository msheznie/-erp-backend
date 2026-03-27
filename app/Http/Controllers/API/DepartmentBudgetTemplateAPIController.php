<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetTemplateAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetTemplateAPIRequest;
use App\Jobs\ProcessDepartmentBudgetPlanningDetailsJob;
use App\Services\DepartmentBudgetTemplateService;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Yajra\DataTables\DataTables;
use App\Traits\AuditLogsTrait;

class DepartmentBudgetTemplateAPIController extends AppBaseController
{
    use AuditLogsTrait;

    public function __construct(
        private DepartmentBudgetTemplateService $departmentBudgetTemplateService
    ) {
    }

    /**
     * Display a listing of the DepartmentBudgetTemplate.
     * GET|HEAD /departmentBudgetTemplates
     */
    public function index(Request $request)
    {
        $departmentBudgetTemplates = $this->departmentBudgetTemplateService->getList(
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
        [$departmentBudgetTemplate, $errorMessage] = $this->departmentBudgetTemplateService->store($request->all());

        // Check if department already has a template of the same type (do not allow duplicate type)
        if ($this->departmentBudgetTemplateRepository->hasTemplateOfType($input['departmentSystemID'], $budgetTemplate->type)) {
            return $this->sendError(trans('custom.budget_template_type_already_assigned_to_department'));
        }

        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $departmentBudgetTemplate->departmentBudgetTemplateID, $uuid, 'department_budget_templates', '', 'C', $departmentBudgetTemplate->toArray(), [], $departmentBudgetTemplate->departmentSystemID, 'company_departments');

        return $this->sendResponse($departmentBudgetTemplate->toArray(), 'Budget template assigned successfully.');
    }

    /**
     * Display the specified DepartmentBudgetTemplate.
     * GET|HEAD /departmentBudgetTemplates/{id}
     */
    public function show($id)
    {
        $departmentBudgetTemplate = $this->departmentBudgetTemplateService->find($id);

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
        $departmentBudgetTemplate = $this->departmentBudgetTemplateService->find($id);
        if (empty($departmentBudgetTemplate)) {
            return $this->sendError(trans('custom.department_budget_template_not_found'));
        }

        $oldValues = $departmentBudgetTemplate->toArray();
        [$departmentBudgetTemplate, $message] = $this->departmentBudgetTemplateService->update((int) $id, $request->all());

        if ($departmentBudgetTemplate === null) {
            return $this->sendError($message);
        }

        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, 'department_budget_templates', '', 'U', $departmentBudgetTemplate->toArray(), $oldValues, $departmentBudgetTemplate->departmentSystemID, 'company_departments');

        return $this->sendResponse($departmentBudgetTemplate->toArray(), $message);
    }

    /**
     * Remove the specified DepartmentBudgetTemplate from storage.
     * DELETE /departmentBudgetTemplates/{id}
     */
    public function destroy($id, Request $request)
    {
        [$deletedId, $errorMessage, $previousValue] = $this->departmentBudgetTemplateService->destroy((int) $id);

        if ($errorMessage) {
            return $this->sendError($errorMessage);
        }

        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, 'department_budget_templates', '', 'D', [], $previousValue, $previousValue['departmentSystemID'] ?? null, 'company_departments');

        return $this->sendResponse($deletedId, trans('custom.department_budget_template_deleted_successfully'));
    }

    /**
     * Get department budget templates for DataTables
     * POST /departmentBudgetTemplates/datatable/{departmentSystemID}
     */
    public function getDepartmentBudgetTemplates($departmentSystemID, Request $request)
    {
        $query = $this->departmentBudgetTemplateService->getDepartmentBudgetTemplatesForDataTable((int) $departmentSystemID);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('template_type_label', fn ($row) => DepartmentBudgetTemplateService::getTemplateTypeLabel($row))
            ->addColumn('is_active_label', fn ($row) => $row->isActive ? 'Yes' : 'No')
            ->addColumn('gl_codes_count', fn ($row) => $row->dep_budget_template_gls_count ?? 0)
            ->make(true);
    }

    /**
     * Get budget templates by type
     * GET /departmentBudgetTemplates/templates-by-type/{type}
     */
    public function getBudgetTemplatesByType($type)
    {
        $templates = $this->departmentBudgetTemplateService->getBudgetTemplatesByType($type);

        return $this->sendResponse($templates->toArray(), trans('custom.budget_templates_retrieved_successfully'));
    }

    /**
     * Get form data
     */
    public function getFormData()
    {
        try {
            $data = $this->departmentBudgetTemplateService->getFormData();
            return $this->sendResponse($data, trans('custom.form_data_retrieved_successfully'));
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
            $companySystemID = auth()->user()->companySystemID ?? 1;
            $chartOfAccounts = $this->departmentBudgetTemplateService->getChartOfAccountsForTemplate($companySystemID);
            return $this->sendResponse($chartOfAccounts, trans('custom.chart_of_accounts_retrieved_successfully'));
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_fetching_chart_of_accounts'), $e->getMessage());
        }
    }

    /**
     * Get chart of accounts by budget template
     */
    public function getChartOfAccountsByBudgetTemplate(Request $request)
    {
        $items = $this->departmentBudgetTemplateService->getChartOfAccountsByBudgetTemplate($request->all());
        return $this->sendResponse($items, trans('custom.chart_of_accounts_retrieved_successfully'));
    }

    /**
     * Get chart of accounts by type
     */
    public function getChartOfAccountsByType($templateType, $accountType)
    {
        try {
            $companySystemID = auth()->user()->companySystemID ?? 1;
            $chartOfAccounts = $this->departmentBudgetTemplateService->getChartOfAccountsByType($companySystemID, $accountType);
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
            $input['db'] = $request->input('db', '');

            [$result, $errorMessage] = $this->departmentBudgetTemplateService->assignGLCodes($input);

            if ($errorMessage) {
                return $this->sendError($errorMessage);
            }

            if (!empty($result['dispatchJob'])) {
                ProcessDepartmentBudgetPlanningDetailsJob::dispatch(
                    $request->input('db', ''),
                    $result['budgetPlanningID'],
                    auth()->id(),
                    $result['chartOfAccountSystemIDs'],
                    $result['selectedSegments']
                );
            }

            $assignedCount = $result['assignedCount'];
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

            $assignedGLCodes = $this->departmentBudgetTemplateService->getAssignedGLCodes((int) $departmentBudgetTemplateID);

            return $this->sendResponse($assignedGLCodes, trans('custom.assigned_gl_codes_retrieved_successfully'));
        } catch (Exception $e) {
            return $this->sendError(trans('custom.error_occurred_while_fetching_assigned_gl_codes'), $e->getMessage());
        }
    }
}

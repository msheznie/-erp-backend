<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetTemplate;
use App\Models\DepBudgetTemplateGl;
use App\Models\BudgetTemplate;
use App\Repositories\DepartmentBudgetTemplateRepository;
use Illuminate\Support\Collection;

class DepartmentBudgetTemplateService
{
    public function __construct(
        private DepartmentBudgetTemplateRepository $departmentBudgetTemplateRepository,
        private BudgetPlanningValidationService $budgetPlanningValidationService
    ) {
    }

    /**
     * Find a department budget template by ID.
     */
    public function find($id): ?DepartmentBudgetTemplate
    {
        return $this->departmentBudgetTemplateRepository->find($id);
    }

    /**
     * Get list of department budget templates (for index).
     */
    public function getList(array $filters, ?int $skip = null, ?int $limit = null): Collection
    {
        return $this->departmentBudgetTemplateRepository->all($filters, $skip, $limit);
    }

    /**
     * Store a new department budget template.
     * Returns [data, message] on success or [null, errorMessage] on validation failure.
     */
    public function store(array $input): array
    {
        if ($this->departmentBudgetTemplateRepository->isTemplateAssigned($input['departmentSystemID'], $input['budgetTemplateID'])) {
            return [null, trans('custom.this_budget_template_is_already_assigned_to_the_de')];
        }

        $budgetTemplate = BudgetTemplate::find($input['budgetTemplateID']);
        if (!$budgetTemplate) {
            return [null, trans('custom.budget_template_not_found')];
        }

        if ($this->departmentBudgetTemplateRepository->hasTemplateOfType($input['departmentSystemID'], $budgetTemplate->type)) {
            return [null, trans('custom.budget_template_type_already_assigned_to_department')];
        }

        $input['isActive'] = 0;
        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->create($input);

        return [$departmentBudgetTemplate, 'Budget template assigned successfully.'];
    }

    /**
     * Update a department budget template.
     * Returns [data, message] on success or [null, errorMessage] on failure.
     */
    public function update(int $id, array $input): array
    {
        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($id);
        if (!$departmentBudgetTemplate) {
            return [null, trans('custom.department_budget_template_not_found')];
        }

        $message = 'Template updated successfully';

        if (isset($input['isActive']) && $input['isActive'] == 1) {
            $budgetTemplate = BudgetTemplate::find($departmentBudgetTemplate->budgetTemplateID);
            if ($budgetTemplate) {
                $this->departmentBudgetTemplateRepository->deactivateOtherTemplatesOfType(
                    $departmentBudgetTemplate->departmentSystemID,
                    $budgetTemplate->type,
                    $departmentBudgetTemplate->budgetTemplateID
                );
                $message = 'Template activated successfully. Other templates of the same type have been deactivated.';
            }
        }

        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->update($input, $id);

        return [$departmentBudgetTemplate, $message];
    }

    /**
     * Delete a department budget template and its GL assignments.
     * Returns [id, null, previousValue] on success or [null, errorMessage, null] on failure.
     */
    public function destroy(int $id): array
    {
        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($id);
        if (!$departmentBudgetTemplate) {
            return [null, trans('custom.department_budget_template_not_found'), null];
        }

        $previousValue = $departmentBudgetTemplate->toArray();
        DepBudgetTemplateGl::where('departmentBudgetTemplateID', $id)->delete();
        $departmentBudgetTemplate->delete();

        return [$id, null, $previousValue];
    }

    /**
     * Get department budget templates query for DataTables (collection).
     */
    public function getDepartmentBudgetTemplatesForDataTable(int $departmentSystemID): Collection
    {
        return DepartmentBudgetTemplate::where('departmentSystemID', $departmentSystemID)
            ->with(['budgetTemplate'])
            ->withCount('depBudgetTemplateGls')
            ->orderBy('departmentBudgetTemplateID', 'desc')
            ->get();
    }

    /**
     * Get template type label for a row.
     */
    public static function getTemplateTypeLabel($row): string
    {
        if (isset($row->budgetTemplate) && isset($row->budgetTemplate->type)) {
            return match ((string) $row->budgetTemplate->type) {
                '1' => 'OPEX',
                '2' => 'CAPEX',
                '3' => 'Common',
                default => 'Unknown',
            };
        }
        return 'Unknown';
    }

    /**
     * Get budget templates by type.
     */
    public function getBudgetTemplatesByType($type): Collection
    {
        return $this->departmentBudgetTemplateRepository->getBudgetTemplatesByType($type);
    }

    /**
     * Get form data (budget types for dropdowns).
     */
    public function getFormData(): array
    {
        return [
            'budgetTypes' => [
                ['value' => 1, 'label' => 'OPEX'],
                ['value' => 2, 'label' => 'CAPEX'],
                ['value' => 3, 'label' => 'Both'],
            ],
        ];
    }

    /**
     * Get chart of accounts for template type (approved, by company).
     */
    public function getChartOfAccountsForTemplate(int $companySystemID): Collection
    {
        return ChartOfAccount::where('isApproved', 1)
            ->where('companySystemID', $companySystemID)
            ->select('chartOfAccountSystemID', 'glCode', 'description', 'accountType')
            ->selectRaw("CONCAT(glCode, ' - ', description) as glCodeDescription")
            ->orderBy('glCode')
            ->get();
    }

    /**
     * Get chart of accounts by budget template (with exclusions for assigned GLs).
     */
    public function getChartOfAccountsByBudgetTemplate(array $input): Collection
    {
        $query = ChartOfAccount::where('isActive', 1)
            ->where('isApproved', 1)
            ->whereHas('chartofaccount_assigned', function ($q) use ($input) {
                $q->where('companySystemID', $input['companySystemID'])
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            })
            ->when($input['templateType'] == 1, fn ($q) => $q->where('catogaryBLorPL', 'PL'))
            ->when($input['templateType'] == 2, fn ($q) => $q->where('catogaryBLorPL', 'BS'))
            ->whereNotNull('reportTemplateCategory')
            ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'catogaryBLorPL', 'controlAccounts');

        if (isset($input['departmentBudgetTemplateID'])) {
            $tempDetail = DepBudgetTemplateGl::where('departmentBudgetTemplateID', $input['departmentBudgetTemplateID'])
                ->pluck('chartOfAccountSystemID')
                ->toArray();
        } else {
            $budgetPlanning = DepartmentBudgetPlanning::with('budgetPlanningDetails')->find($input['budgetPlanningID']);
            $departmentBudgetTemplate = DepartmentBudgetTemplate::where('departmentSystemID', $budgetPlanning->departmentID)
                ->where('budgetTemplateID', $budgetPlanning->budgetPlanningDetails->first()['budget_template_id'])
                ->first();
            $input['departmentBudgetTemplateID'] = $departmentBudgetTemplate->departmentBudgetTemplateID;
            $tempDetail = DepBudgetTemplateGl::where('departmentBudgetTemplateID', $input['departmentBudgetTemplateID'])
                ->pluck('chartOfAccountSystemID')
                ->toArray();
        }

        return $query->whereNotIn('chartOfAccountSystemID', array_filter($tempDetail))->get();
    }

    /**
     * Get chart of accounts by template type and account type.
     */
    public function getChartOfAccountsByType(int $companySystemID, string $accountType): Collection
    {
        $query = ChartOfAccount::where('isApproved', 1)
            ->where('companySystemID', $companySystemID);

        $accountType = strtoupper($accountType);
        $validTypes = ['BS', 'BSA', 'BSL', 'BSE', 'PL', 'PLE', 'PLI'];
        if (in_array($accountType, $validTypes)) {
            $query->where('accountType', $accountType);
        }

        return $query->select('chartOfAccountSystemID', 'glCode', 'description', 'accountType')
            ->selectRaw("CONCAT(glCode, ' - ', description) as glCodeDescription")
            ->orderBy('glCode')
            ->get();
    }

    /**
     * Assign GL codes to a department budget template.
     * Returns [['assignedCount' => n], null] on success or [null, errorMessage] on failure.
     */
    public function assignGLCodes(array $input): array
    {
        $departmentBudgetTemplateID = $input['departmentBudgetTemplateID'] ?? null;
        $chartOfAccountSystemIDs = $input['chartOfAccountSystemIDs'] ?? [];
        $selectedSegments = $input['selectedSegments'] ?? null;

        if (!empty($selectedSegments)) {
            $selectedSegments = collect($selectedSegments)->pluck('id')->toArray();
        }

        if (is_null($departmentBudgetTemplateID) && !empty($input['budgetPlanningID'])) {
            $budgetPlanning = DepartmentBudgetPlanning::with(['budgetPlanningDetails', 'workflow'])->find($input['budgetPlanningID']);
            $departmentBudgetTemplate = DepartmentBudgetTemplate::where('departmentSystemID', $budgetPlanning->departmentID)
                ->where('budgetTemplateID', $budgetPlanning->budgetPlanningDetails->first()['budget_template_id'])
                ->first();
            $departmentBudgetTemplateID = $departmentBudgetTemplate->departmentBudgetTemplateID;

            if ($budgetPlanning->workflow->method == 1 && empty($selectedSegments)) {
                return [null, 'Please select at least one segment'];
            }
        }

        $departmentBudgetTemplate = $this->departmentBudgetTemplateRepository->find($departmentBudgetTemplateID);
        if (!$departmentBudgetTemplate) {
            return [null, trans('custom.department_budget_template_not_found')];
        }

        $assignedCount = 0;
        $userId = auth()->id();
        foreach ($chartOfAccountSystemIDs as $chartOfAccountSystemID) {
            DepBudgetTemplateGl::create([
                'departmentBudgetTemplateID' => $departmentBudgetTemplateID,
                'chartOfAccountSystemID' => $chartOfAccountSystemID,
                'createdUserSystemID' => $userId,
                'modifiedUserSystemID' => $userId,
            ]);
            $assignedCount++;
        }

        $result = ['assignedCount' => $assignedCount];
        if (!empty($input['budgetPlanningID'])) {
            $result['dispatchJob'] = true;
            $result['budgetPlanningID'] = (int) $input['budgetPlanningID'];
            $result['chartOfAccountSystemIDs'] = $chartOfAccountSystemIDs;
            $result['selectedSegments'] = $selectedSegments;
        }

        return [$result, null];
    }

    /**
     * Get assigned GL codes for a department budget template.
     */
    public function getAssignedGLCodes(int $departmentBudgetTemplateID): Collection
    {
        return DepBudgetTemplateGl::where('departmentBudgetTemplateID', $departmentBudgetTemplateID)
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
    }
}

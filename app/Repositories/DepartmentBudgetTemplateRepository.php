<?php

namespace App\Repositories;

use App\Models\DepartmentBudgetTemplate;
use InfyOm\Generator\Common\BaseRepository;

class DepartmentBudgetTemplateRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'departmentSystemID',
        'budgetTemplateID',
        'isActive'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return DepartmentBudgetTemplate::class;
    }

    /**
     * Get department budget templates with related data for DataTables
     */
    public function getDepartmentBudgetTemplatesQuery($departmentSystemID)
    {
        return $this->model->newQuery()
            ->with(['budgetTemplate'])
            ->where('departmentSystemID', $departmentSystemID)
            ->select([
                'department_budget_templates.*',
                'budget_templates.description as template_description',
                'budget_templates.type as template_type'
            ])
            ->leftJoin('budget_templates', 'department_budget_templates.budgetTemplateID', '=', 'budget_templates.budgetTemplateID');
    }

    /**
     * Get budget templates by type for dropdown
     */
    public function getBudgetTemplatesByType($type)
    {
        $query = \App\Models\BudgetTemplate::where('isActive', 1);
        
        if ($type && $type !== 'both') {
            $typeValue = $this->getTypeValue($type);
            $query->where('type', $typeValue);
        }
        
        return $query->select('budgetTemplateID', 'description', 'type')
                    ->orderBy('description')
                    ->get();
    }

    /**
     * Convert type string to integer value
     */
    private function getTypeValue($type)
    {
        switch (strtolower($type)) {
            case 'opex':
                return 1;
            case 'capex':
                return 2;
            case 'common':
                return 3;
            default:
                return null;
        }
    }

    /**
     * Check if template is already assigned to department
     */
    public function isTemplateAssigned($departmentSystemID, $budgetTemplateID)
    {
        return $this->model->newQuery()
            ->where('departmentSystemID', $departmentSystemID)
            ->where('budgetTemplateID', $budgetTemplateID)
            ->exists();
    }

    /**
     * Check if department already has an active template of the same type
     */
    public function hasActiveTemplateOfType($departmentSystemID, $templateType)
    {
        return $this->model->newQuery()
            ->join('budget_templates', 'department_budget_templates.budgetTemplateID', '=', 'budget_templates.budgetTemplateID')
            ->where('department_budget_templates.departmentSystemID', $departmentSystemID)
            ->where('department_budget_templates.isActive', 1)
            ->where('budget_templates.type', $templateType)
            ->exists();
    }

    /**
     * Deactivate other templates of the same type for a department
     */
    public function deactivateOtherTemplatesOfType($departmentSystemID, $templateType, $excludeTemplateID = null)
    {
        // First get the IDs of templates to deactivate
        $query = $this->model->newQuery()
            ->join('budget_templates', 'department_budget_templates.budgetTemplateID', '=', 'budget_templates.budgetTemplateID')
            ->where('department_budget_templates.departmentSystemID', $departmentSystemID)
            ->where('budget_templates.type', $templateType)
            ->where('department_budget_templates.isActive', 1);

        if ($excludeTemplateID) {
            $query->where('department_budget_templates.budgetTemplateID', '!=', $excludeTemplateID);
        }

        $templateIds = $query->pluck('department_budget_templates.departmentBudgetTemplateID');

        // Now update only the department_budget_templates table
        if ($templateIds->isNotEmpty()) {
            return $this->model->newQuery()
                ->whereIn('departmentBudgetTemplateID', $templateIds)
                ->update([
                    'isActive' => 0,
                    'updated_at' => now()
                ]);
        }

        return 0;
    }
} 
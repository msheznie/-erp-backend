<?php

namespace App\Repositories;

use App\Models\BudgetTemplatePreColumn;
use InfyOm\Generator\Common\BaseRepository;

class BudgetTemplatePreColumnRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'columnName',
        'slug',
        'columnType',
        'description'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return BudgetTemplatePreColumn::class;
    }

    /**
     * Get all available columns grouped by type
     */
    public function getAvailableColumnsGrouped()
    {
        $columns = BudgetTemplatePreColumn::orderBy('columnType')
            ->orderBy('columnName')
            ->get();

        $grouped = [];
        foreach ($columns as $column) {
            $typeLabel = $column->getColumnTypeLabel();
            if (!isset($grouped[$typeLabel])) {
                $grouped[$typeLabel] = [];
            }
            $grouped[$typeLabel][] = $column;
        }

        return $grouped;
    }

    /**
     * Get columns not assigned to a specific template
     */
    public function getUnassignedColumns($budgetTemplateID)
    {
        return BudgetTemplatePreColumn::whereNotIn('preColumnID', function($query) use ($budgetTemplateID) {
            $query->select('preColumnID')
                  ->from('budget_template_columns')
                  ->where('budgetTemplateID', $budgetTemplateID);
        })->orderBy('columnType')->orderBy('columnName')->get();
    }

    /**
     * Get column type options for dropdown
     */
    public function getColumnTypeOptions()
    {
        return [
            ['value' => '1', 'label' => 'Text'],
            ['value' => '2', 'label' => 'Number'],
            ['value' => '3', 'label' => 'Dropdown'],
            ['value' => '4', 'label' => 'Formula']
        ];
    }
} 
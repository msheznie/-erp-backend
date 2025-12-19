<?php

namespace App\Repositories;

use App\Models\BudgetTemplateColumn;
use InfyOm\Generator\Common\BaseRepository;

class BudgetTemplateColumnRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'budgetTemplateID',
        'preColumnID',
        'fieldCode'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    public function model()
    {
        return BudgetTemplateColumn::class;
    }

    /**
     * Get columns assigned to a template with pre-column details
     */
    public function getTemplateColumns($budgetTemplateID)
    {
        return BudgetTemplateColumn::with('preColumn')
            ->where('budgetTemplateID', $budgetTemplateID)
            ->orderBy('sortOrder')
            ->get();
    }

    /**
     * Update sort order for template columns
     */
    public function updateSortOrder($budgetTemplateID, $sortOrderData)
    {
        foreach ($sortOrderData as $item) {
            BudgetTemplateColumn::where('templateColumnID', $item['templateColumnID'])
                ->where('budgetTemplateID', $budgetTemplateID)
                ->update(['sortOrder' => $item['sortOrder']]);
        }
    }

    /**
     * Remove column from template
     */
    public function removeFromTemplate($budgetTemplateID, $preColumnID)
    {
        return BudgetTemplateColumn::where('budgetTemplateID', $budgetTemplateID)
            ->where('preColumnID', $preColumnID)
            ->delete();
    }

    /**
     * Add column to template
     */
    public function addToTemplate($data)
    {
        // Get the next sort order
        $maxSortOrder = BudgetTemplateColumn::where('budgetTemplateID', $data['budgetTemplateID'])
            ->max('sortOrder');
        
        $data['sortOrder'] = ($maxSortOrder ?? 0) + 1;

        return BudgetTemplateColumn::create($data);
    }

    /**
     * Get columns available for formula reference
     */
    public function getFormulaReferenceColumns($budgetTemplateID, $excludeColumnID = null)
    {
        $query = BudgetTemplateColumn::with('preColumn')
            ->where('budgetTemplateID', $budgetTemplateID)
            ->whereHas('preColumn', function($q) {
                $q->whereIn('columnType', [2]); // Only number fields can be referenced in formulas
            });

        if ($excludeColumnID) {
            $query->where('templateColumnID', '!=', $excludeColumnID);
        }

        return $query->orderBy('sortOrder')->get();
    }

    /**
     * Get columns that reference a specific column in their formulas
     */
    public function getColumnsReferencingColumn($budgetTemplateID, $referencedColumnID)
    {
        return BudgetTemplateColumn::with('preColumn')
            ->where('budgetTemplateID', $budgetTemplateID)
            ->where('templateColumnID', '!=', $referencedColumnID)
            ->whereNotNull('formulaColumnIDs')
            ->where('formulaColumnIDs', '!=', '')
            ->where(function($query) use ($referencedColumnID) {
                // Check if the column ID appears in the comma-separated list
                $query->where('formulaColumnIDs', 'LIKE', $referencedColumnID)
                      ->orWhere('formulaColumnIDs', 'LIKE', $referencedColumnID . ',%')
                      ->orWhere('formulaColumnIDs', 'LIKE', '%,' . $referencedColumnID)
                      ->orWhere('formulaColumnIDs', 'LIKE', '%,' . $referencedColumnID . ',%');
            })
            ->get();
    }
} 
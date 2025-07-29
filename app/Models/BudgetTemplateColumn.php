<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTemplateColumn extends Model
{
    public $table = 'budget_template_columns';
    public $primaryKey = 'templateColumnID';

    public $fillable = [
        'budgetTemplateID',
        'preColumnID',
        'isMandatory',
        'sortOrder',
        'fieldCode',
        'formulaExpression',
        'formulaColumnIDs'
    ];

    protected $casts = [
        'templateColumnID' => 'integer',
        'budgetTemplateID' => 'integer',
        'preColumnID' => 'integer',
        'isMandatory' => 'boolean',
        'sortOrder' => 'integer'
    ];

    public static $rules = [
        'budgetTemplateID' => 'required|integer|exists:budget_templates,budgetTemplateID',
        'preColumnID' => 'required|integer|exists:budget_template_pre_columns,preColumnID',
        'isMandatory' => 'boolean',
        'sortOrder' => 'integer|min:0',
        'fieldCode' => 'nullable|string|max:50',
        'formulaExpression' => 'nullable|string',
        'formulaColumnIDs' => 'nullable|string'
    ];

    /**
     * Relationship with budget template
     */
    public function budgetTemplate()
    {
        return $this->belongsTo(BudgetTemplate::class, 'budgetTemplateID', 'budgetTemplateID');
    }

    /**
     * Relationship with pre column
     */
    public function preColumn()
    {
        return $this->belongsTo(BudgetTemplatePreColumn::class, 'preColumnID', 'preColumnID');
    }

    /**
     * Get formula column IDs as array
     */
    public function getFormulaColumnIDsArray()
    {
        if (empty($this->formulaColumnIDs)) {
            return [];
        }

        return array_map('intval', explode(',', $this->formulaColumnIDs));
    }

    /**
     * Set formula column IDs from array
     */
    public function setFormulaColumnIDsArray($columnIDs)
    {
        if (is_array($columnIDs)) {
            $this->formulaColumnIDs = implode(',', $columnIDs);
        } else {
            $this->formulaColumnIDs = $columnIDs;
        }
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DepBudgetTemplateGl
 * @package App\Models
 */
class DepBudgetTemplateGl extends Model
{
    public $table = 'dep_budget_template_gl';
    public $primaryKey = 'depBudgetTemplateGlID';

    public $fillable = [
        'departmentBudgetTemplateID',
        'chartOfAccountSystemID',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'depBudgetTemplateGlID' => 'integer',
        'departmentBudgetTemplateID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'departmentBudgetTemplateID' => 'required|integer|exists:department_budget_templates,departmentBudgetTemplateID',
        'chartOfAccountSystemID' => 'required|integer|exists:chart_of_accounts,chartOfAccountSystemID',
        'createdUserSystemID' => 'nullable|integer',
        'modifiedUserSystemID' => 'nullable|integer'
    ];

    /**
     * Relationship to DepartmentBudgetTemplate
     */
    public function departmentBudgetTemplate()
    {
        return $this->belongsTo(DepartmentBudgetTemplate::class, 'departmentBudgetTemplateID', 'departmentBudgetTemplateID');
    }

    /**
     * Relationship to ChartOfAccount (assuming this model exists)
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class, 'chartOfAccountSystemID', 'chartOfAccountSystemID');
    }
} 
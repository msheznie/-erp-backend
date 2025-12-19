<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentBudgetTemplate extends Model
{
    public $table = 'department_budget_templates';
    public $primaryKey = 'departmentBudgetTemplateID';

    public $fillable = [
        'departmentSystemID',
        'budgetTemplateID',
        'isActive',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    protected $casts = [
        'departmentBudgetTemplateID' => 'integer',
        'departmentSystemID' => 'integer',
        'budgetTemplateID' => 'integer',
        'isActive' => 'boolean',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    public static $rules = [
        'departmentSystemID' => 'required|integer|exists:company_departments,departmentSystemID',
        'budgetTemplateID' => 'required|integer|exists:budget_templates,budgetTemplateID',
        'isActive' => 'boolean',
        'createdUserSystemID' => 'nullable|integer',
        'modifiedUserSystemID' => 'nullable|integer'
    ];

    /**
     * Relationship to BudgetTemplate
     */
    public function budgetTemplate()
    {
        return $this->belongsTo(BudgetTemplate::class, 'budgetTemplateID', 'budgetTemplateID');
    }

    /**
     * Relationship to Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'departmentSystemID', 'departmentSystemID');
    }

    /**
     * Relationship to DepBudgetTemplateGl (one to many)
     */
    public function depBudgetTemplateGls()
    {
        return $this->hasMany(DepBudgetTemplateGl::class, 'departmentBudgetTemplateID', 'departmentBudgetTemplateID');
    }
} 
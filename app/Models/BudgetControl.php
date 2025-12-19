<?php
namespace App\Models;
use Eloquent as Model;

class BudgetControl extends Model
{
    public $table = 'budget_controls';
    public $primaryKey = 'budgetControlID';

    public $fillable = [
        'controlName',
        'controlDescription',
        'isActive'
    ];

    protected $casts = [
        'budgetControlID' => 'integer',
        'controlName' => 'string',
        'controlDescription' => 'string',
        'isActive' => 'integer'
    ];

    public static $rules = [
        'controlName' => 'required|string|max:255',
        'controlDescription' => 'nullable|string',
        'isActive' => 'integer|in:0,1'
    ];

    public function departmentUserBudgetControls()
    {
        return $this->hasMany('App\Models\DepartmentUserBudgetControl', 'budgetControlID', 'budgetControlID');
    }

    public function assignedEmployees()
    {
        return $this->belongsToMany('App\Models\CompanyDepartmentEmployee', 'department_user_budget_control', 'budgetControlID', 'departmentEmployeeSystemID');
    }
} 
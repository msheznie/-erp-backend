<?php
namespace App\Models;
use Eloquent as Model;

class DepartmentUserBudgetControl extends Model
{
    public $table = 'department_user_budget_control';
    public $primaryKey = 'id';

    public $fillable = [
        'departmentEmployeeSystemID',
        'budgetControlID'
    ];

    protected $casts = [
        'id' => 'integer',
        'departmentEmployeeSystemID' => 'integer',
        'budgetControlID' => 'integer'
    ];

    public static $rules = [
        'departmentEmployeeSystemID' => 'required|integer',
        'budgetControlID' => 'required|integer'
    ];

    public function departmentEmployee()
    {
        return $this->belongsTo('App\Models\CompanyDepartmentEmployee', 'departmentEmployeeSystemID', 'departmentEmployeeSystemID');
    }

    public function budgetControl()
    {
        return $this->belongsTo('App\Models\BudgetControl', 'budgetControlID', 'budgetControlID');
    }
} 
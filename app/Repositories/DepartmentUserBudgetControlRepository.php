<?php
namespace App\Repositories;
use App\Models\DepartmentUserBudgetControl;
use InfyOm\Generator\Common\BaseRepository;

class DepartmentUserBudgetControlRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'departmentEmployeeSystemID',
        'budgetControlID'
    ];

    public function model()
    {
        return DepartmentUserBudgetControl::class;
    }

    public function getUserBudgetControls($departmentEmployeeSystemID)
    {
        return $this->model()::where('departmentEmployeeSystemID', $departmentEmployeeSystemID)
                              ->with(['budgetControl'])
                              ->get();
    }

    public function syncUserBudgetControls($departmentEmployeeSystemID, $budgetControlIds)
    {
        // First, delete all existing controls for this user
        $this->model()::where('departmentEmployeeSystemID', $departmentEmployeeSystemID)->delete();

        // Then, create the selected controls
        foreach ($budgetControlIds as $budgetControlId) {
            $this->model()::create([
                'departmentEmployeeSystemID' => $departmentEmployeeSystemID,
                'budgetControlID' => $budgetControlId
            ]);
        }
    }
} 
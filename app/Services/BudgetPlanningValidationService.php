<?php

namespace App\Services;

use App\Models\DepartmentBudgetPlanning;
use App\Models\BudgetDelegateAccessRecord;

class BudgetPlanningValidationService
{

    public function checkBudgetPlanningInProgress(int $id)
    {


        $departmentBudgetPlanning = BudgetDelegateAccessRecord::with('delegatee','budgetPlanningDetail')
                                    ->whereHas('budgetPlanningDetail', function($query) use ($id) {
                                        $query->whereIn('work_status', [1,2]);
                                    })
                                    ->where('delegatee_id', $id)
                                    ->exists();

        if ($departmentBudgetPlanning) {
            return true;
        }
        return false;
    }
}


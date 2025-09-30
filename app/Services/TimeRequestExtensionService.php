<?php

namespace App\Services;

use App\Models\DeptBudgetPlanningTimeRequest;

class TimeRequestExtensionService
{

    public function checkExistingRecord($record)
    {
        return DeptBudgetPlanningTimeRequest::where('department_budget_planning_id',$record['department_budget_planning_id'])
                ->where('created_by',$record['created_by'])
                ->byStatus(1)
                ->exists();
    }
}

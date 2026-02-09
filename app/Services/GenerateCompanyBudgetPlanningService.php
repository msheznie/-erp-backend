<?php

namespace App\Services;

use App\Models\DepartmentBudgetPlanning;
/**
 * Class GenerateCompanyBudgetPlanningService
 * @package App\Services
 */
class GenerateCompanyBudgetPlanningService
{
    /**
     * Generate company budget planning for the given department budget planning.
     *
     * @param int $id Department budget planning ID
     * @param int $departmentID Department system ID
     * @return array
     */
    public function generate(int $id, int $departmentID): array
    {
       $this->validation($id, $departmentID);
    }

    private function validation(int $id, int $departmentID): array
    {
        $department = DepartmentBudgetPlanning::where('departmentID', $departmentID)->where('companyBudgetPlanningID', $id)->first();
        if (!$department) {
            throw new \Exception('Department budget planning not found');
        }
        dd($department);
    }
}

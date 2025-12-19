<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\DeptBudgetPlanningTimeRequest;
use App\Models\DepartmentBudgetPlanning;

class UniqueRequestCodePerBudgetPlanning implements Rule
{
    protected $budgetPlanningId;
    protected $excludeId;

    /**
     * Create a new rule instance.
     *
     * @param int $budgetPlanningId
     * @param int|null $excludeId
     */
    public function __construct($budgetPlanningId, $excludeId = null)
    {
        $this->budgetPlanningId = $budgetPlanningId;
        $this->excludeId = $excludeId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Verify that the budget planning ID exists
        $departmentBudgetPlanning = DepartmentBudgetPlanning::find($this->budgetPlanningId);

        if (!$departmentBudgetPlanning) {
            return false;
        }

        // Build the query to check for existing request codes for the same budget planning ID
        $query = DeptBudgetPlanningTimeRequest::where('department_budget_planning_id', $this->budgetPlanningId)
            ->where('request_code', $value);

        // Exclude current record if updating
        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }

        return $query->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The request code has already been taken for this budget planning.';
    }
}

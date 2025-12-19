<?php

namespace App\Http\Requests\API;

use App\Models\DepartmentBudgetPlanningDetail;
use InfyOm\Generator\Request\APIRequest;

class CreateDepartmentBudgetPlanningDetailAPIRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'department_planning_id' => 'required|integer|exists:department_budget_plannings,id',
            'department_segment_id' => 'nullable|integer|exists:company_departments_segments,departmentSegmentSystemID',
            'budget_template_gl_id' => 'required|integer|exists:dep_budget_template_gl,depBudgetTemplateGlID',
            'request_amount' => 'required|numeric|min:0',
            'responsible_person' => 'nullable|integer|exists:users,id',
            'responsible_person_type' => 'required|integer|in:1,2',
            'time_for_submission' => 'nullable|date',
            'previous_year_budget' => 'nullable|numeric',
            'current_year_budget' => 'nullable|numeric',
            'amount_given_by_finance' => 'nullable|numeric|min:0',
            'amount_given_by_hod' => 'nullable|numeric|min:0',
            'internal_status' => 'nullable|integer|in:1,2,3,4'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'department_planning_id.required' => 'Department Planning ID is required.',
            'department_planning_id.exists' => 'Selected Department Planning does not exist.',
            'budget_template_gl_id.required' => 'Budget Template GL is required.',
            'budget_template_gl_id.exists' => 'Selected Budget Template GL does not exist.',
            'request_amount.required' => 'Request amount is required.',
            'request_amount.numeric' => 'Request amount must be a valid number.',
            'request_amount.min' => 'Request amount cannot be negative.',
            'responsible_person_type.required' => 'Responsible person type is required.',
            'responsible_person_type.in' => 'Responsible person type must be 1 (HOD) or 2 (Delegate).',
            'internal_status.in' => 'Internal status must be 1 (Pending), 2 (Approved), 3 (Rejected), or 4 (Under Review).',
            'time_for_submission.date' => 'Time for submission must be a valid date.',
            'amount_given_by_finance.numeric' => 'Amount given by finance must be a valid number.',
            'amount_given_by_finance.min' => 'Amount given by finance cannot be negative.',
            'amount_given_by_hod.numeric' => 'Amount given by HOD must be a valid number.',
            'amount_given_by_hod.min' => 'Amount given by HOD cannot be negative.'
        ];
    }
}
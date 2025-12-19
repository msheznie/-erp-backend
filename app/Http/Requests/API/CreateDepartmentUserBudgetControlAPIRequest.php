<?php
namespace App\Http\Requests\API;
use InfyOm\Generator\Request\APIRequest;

class CreateDepartmentUserBudgetControlAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'departmentEmployeeSystemID' => 'required|integer',
            'budgetControlIds' => 'nullable|array',
            'budgetControlIds.*' => 'integer|exists:budget_controls,budgetControlID'
        ];
    }

    public function messages()
    {
        return [
            'departmentEmployeeSystemID.required' => 'Department Employee ID is required',
            'departmentEmployeeSystemID.integer' => 'Department Employee ID must be an integer',
            'budgetControlIds.array' => 'Budget control IDs must be an array',
            'budgetControlIds.*.integer' => 'Each budget control ID must be an integer',
            'budgetControlIds.*.exists' => 'Invalid budget control ID'
        ];
    }
} 
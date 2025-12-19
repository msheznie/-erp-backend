<?php

namespace App\Http\Requests\API;

use App\Models\CompanyDepartmentEmployee;
use InfyOm\Generator\Request\APIRequest;

class CreateCompanyDepartmentEmployeeAPIRequest extends APIRequest
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
        // Handle bulk employee assignment
        if ($this->has('employees') && is_array($this->input('employees'))) {
            return [
                'employees' => 'required|array|min:1',
                'employees.*.departmentSystemID' => 'required|integer',
                'employees.*.employeeSystemID' => 'required|integer',
                'employees.*.isHOD' => 'integer|in:0,1',
                'employees.*.isActive' => 'integer|in:0,1'
            ];
        } else {
            // Handle single employee assignment
            $rules = CompanyDepartmentEmployee::$rules;
            return $rules;
        }
    }

    /**
     * Get custom error messages for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'employeeSystemID.unique' => 'This employee is already assigned to this department',
            'departmentSystemID.required' => 'Department is required',
            'employeeSystemID.required' => 'Employee is required',
            'isHOD.in' => 'HOD field must be 0 or 1',
            'isActive.in' => 'Active status must be 0 or 1'
        ];
    }
} 
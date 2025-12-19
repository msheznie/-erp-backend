<?php

namespace App\Http\Requests\API;

use App\Models\CompanyDepartmentEmployee;
use InfyOm\Generator\Request\APIRequest;

class UpdateCompanyDepartmentEmployeeAPIRequest extends APIRequest
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
        $rules = CompanyDepartmentEmployee::$rules;
        
        // Get the current record ID for unique constraint
        $id = $this->route('company_department_employee');
        
        // Add unique constraint excluding current record
        $rules['employeeSystemID'] = 'required|integer|unique:company_departments_employees,employeeSystemID,' . $id . ',departmentEmployeeSystemID,departmentSystemID,' . $this->input('departmentSystemID');
        
        return $rules;
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
<?php

namespace App\Http\Requests\API;

use App\Models\CompanyDepartment;
use InfyOm\Generator\Request\APIRequest;

class CreateCompanyDepartmentAPIRequest extends APIRequest
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
            'departmentCode' => 'required|string|max:15',
            'departmentDescription' => 'required|string|max:255',
            'companySystemID' => 'required|integer|exists:companymaster,companySystemID',
            'type' => 'required|integer|in:1,2',
            'parentDepartmentID' => 'nullable|integer|exists:company_departments,departmentSystemID',
            'isFinance' => 'nullable|boolean', //integer or boolean  
            'isActive' => 'required|integer|in:0,1'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'departmentCode.required' => 'Department code is required',
            'departmentCode.unique' => 'The department code must be unique, and this code is already being used by another department',
            'departmentDescription.required' => 'Department description is required',
            'companySystemID.required' => 'Company is required',
            'companySystemID.exists' => 'Selected company does not exist',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be either Parent or Final',
            'parentDepartmentID.required_if' => 'Parent department is required for Final type',
            'parentDepartmentID.exists' => 'Selected parent department does not exist',
            'isActive.required' => 'Active status is required',
            'isActive.in' => 'Active status must be Yes or No'
        ];
    }
} 
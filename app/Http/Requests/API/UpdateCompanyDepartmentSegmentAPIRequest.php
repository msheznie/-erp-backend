<?php
namespace App\Http\Requests\API;
use App\Models\CompanyDepartmentSegment;
use InfyOm\Generator\Request\APIRequest;

class UpdateCompanyDepartmentSegmentAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = CompanyDepartmentSegment::$rules;
        return $rules;
    }

    public function messages()
    {
        return [
            'departmentSystemID.required' => 'Department is required',
            'serviceLineSystemID.required' => 'Segment is required',
            'isActive.in' => 'Active status must be 0 or 1'
        ];
    }
} 
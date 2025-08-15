<?php
namespace App\Http\Requests\API;
use App\Models\CompanyDepartmentSegment;
use InfyOm\Generator\Request\APIRequest;

class CreateCompanyDepartmentSegmentAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($this->has('segments') && is_array($this->input('segments'))) {
            return [
                'segments' => 'required|array|min:1',
                'segments.*.departmentSystemID' => 'required|integer',
                'segments.*.serviceLineSystemID' => 'required|integer',
                'segments.*.isActive' => 'integer|in:0,1'
            ];
        } else {
            $rules = CompanyDepartmentSegment::$rules;
            return $rules;
        }
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
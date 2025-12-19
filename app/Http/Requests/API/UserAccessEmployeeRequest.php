<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UserAccessEmployeeRequest extends FormRequest
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
            'userId' => 'required',
            'moduleId' => 'required',
            'tenderId' => 'required',
            'companyId' => 'required'
        ];
    }

    /**
     * Customize validation messages.
     */
    public function messages(): array
    {
        return [
            'userId.required' => 'Employee field is required.',
            'moduleId.required' => 'Module id field is required.',
            'tenderId.required' => 'Tender id is required.',
            'companyId.required' => 'Company id is required.'
        ];
    }
}

<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CompanyValidateAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'companySystemId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'companySystemId.required' => 'Company is required.',
        ];
    }
}

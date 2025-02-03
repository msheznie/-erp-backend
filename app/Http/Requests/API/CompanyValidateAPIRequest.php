<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class CompanyValidateAPIRequest extends APIRequest
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

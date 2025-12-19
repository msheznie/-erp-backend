<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class ViewContractAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contractId' => 'required',
            'companySystemId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'companySystemID.required' => 'Company is required.',
            'contractType.required' => 'Contract ID is required.',
        ];
    }
}

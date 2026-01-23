<?php

namespace App\Http\Requests\API;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ViewContractAPIRequest extends FormRequest
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

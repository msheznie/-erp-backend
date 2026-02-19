<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloneTenderAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'companySystemId' => 'required',
            'uuid' => 'required',
            'isTender' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'uuid.required' => 'Tender Uuid is required.',
            'companySystemID.required' => 'Company is required.',
            'isTender.required' => 'Document type is required.',
        ];
    }
}

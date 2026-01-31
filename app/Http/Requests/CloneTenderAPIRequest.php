<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;

class CloneTenderAPIRequest extends APIRequest
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

<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;

class AddAttachmentAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tenderId' => 'required',
            'companySystemID' => 'required',
            'documentSystemID' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'tenderId.required' => 'Tender Uuid is required.',
            'companySystemID.required' => 'Company is required.',
            'documentSystemID.required' => 'Document ID is required.',
        ];
    }
}

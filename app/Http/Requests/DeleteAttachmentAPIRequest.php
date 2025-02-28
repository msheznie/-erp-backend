<?php

namespace App\Http\Requests;

use InfyOm\Generator\Request\APIRequest;

class DeleteAttachmentAPIRequest extends APIRequest
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
            'attachmentId' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'tenderId.required' => 'Tender Uuid is required.',
            'companySystemID.required' => 'Company is required.',
            'attachmentId.required' => 'Attachment ID is required.',
        ];
    }
}

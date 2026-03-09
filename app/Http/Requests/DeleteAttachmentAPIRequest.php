<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAttachmentAPIRequest extends FormRequest
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

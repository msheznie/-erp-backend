<?php

namespace App\Http\Requests\API;

use App\Models\AttachmentTypeConfiguration;
use InfyOm\Generator\Request\APIRequest;

class CreateAttachmentTypeConfigurationAPIRequest extends APIRequest
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
            'companyDocumentAttachmentID' => 'required',
            'documentSystemID' => 'required',
            'companySystemID' => 'required',
            'selected_type_ids' => 'nullable|array',
            'selected_type_ids.*' => 'integer'
        ];
    }

    public function messages()
    {
        return [
            'companyDocumentAttachmentID.required' => 'Company Document Attachment ID is required.',
            'documentSystemID.required' => 'Document System ID is required.',
            'companySystemID.required' => 'Company ID is required.',
            'selected_type_ids.array' => 'Selected type IDs must be an array.',
            'selected_type_ids.*.integer' => 'Each selected type ID must be an integer.',
        ];
    }
}

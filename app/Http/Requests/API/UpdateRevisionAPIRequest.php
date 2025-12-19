<?php

namespace App\Http\Requests\API;

use App\Http\Requests\AppBaseFormRequest;

/**
 * Class UpdateRevisionAPIRequest
 * @package App\Http\Requests\API
 */
class UpdateRevisionAPIRequest extends AppBaseFormRequest
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
            'submittedBy' => 'sometimes|string|max:255',
            'submittedDate' => 'sometimes|date_format:Y-m-d',
            'reviewComments' => 'sometimes|string',
            'revisionType' => 'sometimes|string|in:amount_adjustment,missing_justification,incorrect_gl_allocation,line_item_clarification,exceeds_threshold,template_mismatch,department_scope_error,other',
            'reopenEditableSection' => 'sometimes|boolean',
            'revisionStatus' => 'sometimes|integer|in:1,2,3',
            'completionComments' => 'sometimes|string',
            'attachments' => 'sometimes|nullable|array',
            'attachments.*.fileName' => 'string|max:255',
            'attachments.*.fileContent' => 'string',
            'attachments.*.fileSize' => 'integer|min:1'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'submittedBy.string' => 'Submitted By must be a string',
            'submittedBy.max' => 'Submitted By cannot exceed 255 characters',
            'submittedDate.date' => 'Submitted Date must be a valid date',
            'reviewComments.string' => 'Review Comments must be a string',
            'revisionType.string' => 'Revision Type must be a string',
            'revisionType.in' => 'Invalid revision type selected',
            'reopenEditableSection.boolean' => 'Reopen Editable Section must be true or false',
            'revisionStatus.integer' => 'Revision Status must be an integer',
            'revisionStatus.in' => 'Invalid revision status selected',
            'completionComments.string' => 'Completion Comments must be a string',
            'attachments.array' => 'Attachments must be an array',
            'attachments.*.fileName.string' => 'File name must be a string',
            'attachments.*.fileName.max' => 'File name cannot exceed 255 characters',
            'attachments.*.fileContent.string' => 'File content must be a string',
            'attachments.*.fileSize.integer' => 'File size must be an integer',
            'attachments.*.fileSize.min' => 'File size must be at least 1 byte'
        ];
    }
}

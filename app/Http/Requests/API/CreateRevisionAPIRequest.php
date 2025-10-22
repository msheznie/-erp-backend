<?php

namespace App\Http\Requests\API;

use App\Http\Requests\AppBaseFormRequest;

/**
 * Class CreateRevisionAPIRequest
 * @package App\Http\Requests\API
 */
class CreateRevisionAPIRequest extends AppBaseFormRequest
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
            'budgetPlanningId' => 'required|integer|exists:department_budget_plannings,id',
            'submittedBy' => 'required|string|max:255',
            'submittedDate' => 'required|date_format:Y-m-d',
            'reviewComments' => 'required|string',
            'revisionType' => 'required|string|in:amount_adjustment,missing_justification,incorrect_gl_allocation,line_item_clarification,exceeds_threshold,template_mismatch,department_scope_error,other',
            'attachments' => 'nullable|array',
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
            'budgetPlanningId.required' => 'Budget Planning ID is required',
            'budgetPlanningId.integer' => 'Budget Planning ID must be an integer',
            'budgetPlanningId.exists' => 'Budget Planning not found',
            'submittedBy.required' => 'Submitted By is required',
            'submittedBy.string' => 'Submitted By must be a string',
            'submittedBy.max' => 'Submitted By cannot exceed 255 characters',
            'submittedDate.required' => 'Submitted Date is required',
            'submittedDate.date' => 'Submitted Date must be a valid date',
            'reviewComments.required' => 'Review Comments are required',
            'reviewComments.string' => 'Review Comments must be a string',
            'revisionType.required' => 'Revision Type is required',
            'revisionType.string' => 'Revision Type must be a string',
            'revisionType.in' => 'Invalid revision type selected',
            'reopenEditableSection.boolean' => 'Reopen Editable Section must be true or false',
            'attachments.array' => 'Attachments must be an array',
            'attachments.*.fileName.string' => 'File name must be a string',
            'attachments.*.fileName.max' => 'File name cannot exceed 255 characters',
            'attachments.*.fileContent.string' => 'File content must be a string',
            'attachments.*.fileSize.integer' => 'File size must be an integer',
            'attachments.*.fileSize.min' => 'File size must be at least 1 byte'
        ];
    }
}

<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CreateBudgetTemplateCommentAPIRequest extends FormRequest
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
            'budget_detail_id' => 'required|integer|exists:department_budget_planning_details,id',
            'comment_text' => 'required|string|max:5000',
            'parent_comment_id' => 'nullable|integer|exists:budget_template_comments,commentID'
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
            'budget_detail_id.required' => 'Budget detail ID is required.',
            'budget_detail_id.exists' => 'The specified budget detail does not exist.',
            'comment_text.required' => 'Comment text is required.',
            'comment_text.max' => 'Comment text cannot exceed 5000 characters.',
            'parent_comment_id.exists' => 'The specified parent comment does not exist.'
        ];
    }
}

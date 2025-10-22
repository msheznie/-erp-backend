<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CreateBudgetDetailCommentAPIRequest extends FormRequest
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
            'budgetDetailId' => 'required|integer|exists:department_budget_planning_details,id',
            'comment' => 'required|string|max:5000'
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
            'budgetDetailId.required' => 'Budget detail ID is required.',
            'budgetDetailId.exists' => 'The specified budget detail does not exist.',
            'comment.required' => 'Comment text is required.',
            'comment.max' => 'Comment text cannot exceed 5000 characters.'
        ];
    }
}
<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetDetailCommentAPIRequest extends FormRequest
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
            'comment' => 'required|string|max:5000',
            'companySystemID' => 'required|integer'
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
            'comment.required' => 'Comment text is required.',
            'comment.max' => 'Comment text cannot exceed 5000 characters.',
            'companySystemID.required' => 'Company system ID is required.'
        ];
    }
}
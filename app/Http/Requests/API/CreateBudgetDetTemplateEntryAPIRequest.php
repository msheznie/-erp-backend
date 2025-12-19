<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateBudgetDetTemplateEntryAPIRequest
 * @package App\Http\Requests\API
 *
 * @property integer $budget_detail_id
 * @property integer $rowNumber
 * @property integer $created_by
 * @property string $timestamp
 */
class CreateBudgetDetTemplateEntryAPIRequest extends FormRequest
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
            'rowNumber' => 'required|integer|min:1',
            'created_by' => 'required|integer|exists:users,id',
            'timestamp' => 'nullable|date'
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
            'budget_detail_id.exists' => 'The selected budget detail does not exist.',
            'rowNumber.required' => 'Row number is required.',
            'rowNumber.min' => 'Row number must be at least 1.',
            'created_by.required' => 'Created by user ID is required.',
            'created_by.exists' => 'The selected user does not exist.',
            'timestamp.date' => 'Timestamp must be a valid date.'
        ];
    }
} 
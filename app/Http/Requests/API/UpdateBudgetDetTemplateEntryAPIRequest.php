<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateBudgetDetTemplateEntryAPIRequest
 * @package App\Http\Requests\API
 *
 * @property integer $budget_detail_id
 * @property integer $rowNumber
 * @property integer $created_by
 * @property string $timestamp
 */
class UpdateBudgetDetTemplateEntryAPIRequest extends FormRequest
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
            'budget_detail_id' => 'sometimes|integer|exists:department_budget_planning_details,id',
            'rowNumber' => 'sometimes|integer|min:1',
            'created_by' => 'sometimes|integer|exists:users,id',
            'timestamp' => 'sometimes|nullable|date'
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
            'budget_detail_id.integer' => 'Budget detail ID must be an integer.',
            'budget_detail_id.exists' => 'The selected budget detail does not exist.',
            'rowNumber.integer' => 'Row number must be an integer.',
            'rowNumber.min' => 'Row number must be at least 1.',
            'created_by.integer' => 'Created by user ID must be an integer.',
            'created_by.exists' => 'The selected user does not exist.',
            'timestamp.date' => 'Timestamp must be a valid date.'
        ];
    }
} 
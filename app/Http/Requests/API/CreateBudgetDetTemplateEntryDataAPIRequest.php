<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateBudgetDetTemplateEntryDataAPIRequest
 * @package App\Http\Requests\API
 *
 * @property integer $entryID
 * @property integer $templateColumnID
 * @property string $value
 * @property string $timestamp
 */
class CreateBudgetDetTemplateEntryDataAPIRequest extends FormRequest
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
            'entryID' => 'required|integer|exists:budget_det_template_entries,entryID',
            'templateColumnID' => 'required|integer|exists:budget_template_columns,templateColumnID',
            'value' => 'nullable|string|max:65535',
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
            'entryID.required' => 'Entry ID is required.',
            'entryID.exists' => 'The selected entry does not exist.',
            'templateColumnID.required' => 'Template column ID is required.',
            'templateColumnID.exists' => 'The selected template column does not exist.',
            'value.string' => 'Value must be a string.',
            'value.max' => 'Value may not be greater than 65535 characters.',
            'timestamp.date' => 'Timestamp must be a valid date.'
        ];
    }
} 
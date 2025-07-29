<?php

namespace App\Http\Requests\API;

use App\Models\BudgetTemplatePreColumn;
use InfyOm\Generator\Request\APIRequest;

class UpdateBudgetTemplatePreColumnAPIRequest extends APIRequest
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
        $rules = BudgetTemplatePreColumn::$rules;
        
        // Make unique rules ignore current record
        $id = $this->route('budgetTemplatePreColumn');
        if ($id) {
            $rules['columnName'] = 'required|string|max:255|unique:budget_template_pre_columns,columnName,' . $id . ',preColumnID';
            $rules['slug'] = 'nullable|string|max:255|unique:budget_template_pre_columns,slug,' . $id . ',preColumnID';
        }
        
        return $rules;
    }
} 
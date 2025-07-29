<?php

namespace App\Http\Requests\API;

use App\Models\BudgetTemplateColumn;
use InfyOm\Generator\Request\APIRequest;

class UpdateBudgetTemplateColumnAPIRequest extends APIRequest
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
        $rules = BudgetTemplateColumn::$rules;
        
        // For updates, make budgetTemplateID and preColumnID optional if they're not being changed
        // But if they are provided, they still need to be valid
        $rules['budgetTemplateID'] = 'sometimes|required|integer|exists:budget_templates,budgetTemplateID';
        $rules['preColumnID'] = 'sometimes|required|integer|exists:budget_template_pre_columns,preColumnID';
        
        return $rules;
    }
} 
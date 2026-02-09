<?php

namespace App\Http\Requests\API;

use App\Models\BankReconciliationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBankReconciliationRulesAPIRequest extends FormRequest
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
        $rules = BankReconciliationRules::$rules;
        
        return $rules;
    }
}

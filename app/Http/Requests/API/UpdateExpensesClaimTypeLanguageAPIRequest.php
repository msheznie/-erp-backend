<?php

namespace App\Http\Requests\API;

use App\Models\ExpensesClaimTypeLanguage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExpensesClaimTypeLanguageAPIRequest extends FormRequest
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
        $rules = ExpensesClaimTypeLanguage::$rules;
        
        return $rules;
    }
}

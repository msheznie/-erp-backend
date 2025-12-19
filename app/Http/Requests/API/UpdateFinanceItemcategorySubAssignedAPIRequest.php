<?php

namespace App\Http\Requests\API;

use App\Models\FinanceItemcategorySubAssigned;
use InfyOm\Generator\Request\APIRequest;

class UpdateFinanceItemcategorySubAssignedAPIRequest extends APIRequest
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
        return FinanceItemcategorySubAssigned::$rules;
    }
}

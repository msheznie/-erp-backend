<?php

namespace App\Http\Requests\API;

use App\Models\ErpBudgetAdditionDetail;
use InfyOm\Generator\Request\APIRequest;

class CreateErpBudgetAdditionDetailAPIRequest extends APIRequest
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
            'budgetAdditionFormAutoID' => 'required',
            'templateDetailID' => 'required|min:1',
            'serviceLineSystemID' => 'required|numeric|min:1',
            'chartOfAccountSystemID' => 'required|numeric|min:1',
            'adjustmentAmountRpt' => 'required|numeric',
            'remarks' => 'required'
        ];
    }
}

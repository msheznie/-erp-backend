<?php

namespace App\Http\Requests\API;

use App\Models\PurchaseReturnLogistic;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseReturnLogisticAPIRequest extends FormRequest
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
        $rules = PurchaseReturnLogistic::$rules;
        
        return $rules;
    }
}

<?php

namespace App\Http\Requests\API;

use App\Models\POSSourceMenuSalesServiceCharge;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePOSSourceMenuSalesServiceChargeAPIRequest extends FormRequest
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
        $rules = POSSourceMenuSalesServiceCharge::$rules;
        
        return $rules;
    }
}

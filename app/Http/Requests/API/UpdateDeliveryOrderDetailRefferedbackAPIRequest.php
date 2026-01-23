<?php

namespace App\Http\Requests\API;

use App\Models\DeliveryOrderDetailRefferedback;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryOrderDetailRefferedbackAPIRequest extends FormRequest
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
        $rules = DeliveryOrderDetailRefferedback::$rules;
        
        return $rules;
    }
}

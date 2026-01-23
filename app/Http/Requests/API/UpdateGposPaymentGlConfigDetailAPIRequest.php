<?php

namespace App\Http\Requests\API;

use App\Models\GposPaymentGlConfigDetail;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGposPaymentGlConfigDetailAPIRequest extends FormRequest
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
        return GposPaymentGlConfigDetail::$rules;
    }
}

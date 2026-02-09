<?php

namespace App\Http\Requests\API;

use App\Models\POSStagInvoicePayment;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePOSStagInvoicePaymentAPIRequest extends FormRequest
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
        $rules = POSStagInvoicePayment::$rules;
        
        return $rules;
    }
}

<?php

namespace App\Http\Requests\API;

use App\Models\POSSTAGInvoiceDetail;
use Illuminate\Foundation\Http\FormRequest;

class CreatePOSSTAGInvoiceDetailAPIRequest extends FormRequest
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
        return POSSTAGInvoiceDetail::$rules;
    }
}

<?php

namespace App\Http\Requests\API;

use App\Models\PayAdvanceReceiptDetail;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePayAdvanceReceiptDetailAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = PayAdvanceReceiptDetail::$rules;

        return $rules;
    }
}


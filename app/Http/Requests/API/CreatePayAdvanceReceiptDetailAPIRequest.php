<?php

namespace App\Http\Requests\API;

use App\Models\PayAdvanceReceiptDetail;
use Illuminate\Foundation\Http\FormRequest;

class CreatePayAdvanceReceiptDetailAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return PayAdvanceReceiptDetail::$rules;
    }
}


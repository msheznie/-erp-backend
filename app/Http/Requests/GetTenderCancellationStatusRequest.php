<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTenderCancellationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'tender_id' => 'required|integer|min:1',
            'company_id' => 'required|integer|min:1',
        ];
    }
}


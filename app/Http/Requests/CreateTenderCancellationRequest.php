<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenderCancellationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'tender_id' => 'required|integer|min:1',
            'company_id' => 'nullable|integer|min:1',
            'companySystemID' => 'nullable|integer|min:1',
            'internal_comment' => 'required|string',
            'external_comment' => 'required|string',
        ];
    }
}


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenderCustomEmailRequest extends FormRequest
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
        return [
            'tender_uuid' => 'required',
            'supplier_uuid' => 'required|array',
            'company_id' => 'required|integer',
            'cc_email' => 'nullable|string',
            'email_body' => 'required|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTenderRfxAwardEmailDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'tender_id'  => 'required|integer|min:1',
            'company_id' => 'required|integer|min:1',
            'email_type' => 'required|in:award',
            'supplier_id'=> 'nullable|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'tender_id.required'  => trans('srm_tender_rfx.item_wise_tender_id_required'),
            'company_id.required' => trans('srm_tender_rfx.company_is_required'),
            'email_type.required' => 'Email type is required.',
            'email_type.in'       => 'Only schedule-wise award email data is supported.',
        ];
    }
}


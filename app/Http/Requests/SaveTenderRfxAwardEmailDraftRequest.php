<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveTenderRfxAwardEmailDraftRequest extends FormRequest
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
            'tender_id'     => 'required|integer|min:1',
            'company_id'    => 'required|integer|min:1',
            'email_type'    => 'required|in:award,regret',
            'supplier_id'   => 'nullable|integer|min:0',
            'email_subject' => 'nullable|string',
            'email_body'    => 'nullable|string',
            'cc_emails'     => 'nullable|array',
            'cc_emails.*'   => 'nullable|string',
            'document_id'   => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'tender_id.required'  => trans('srm_tender_rfx.item_wise_tender_id_required'),
            'company_id.required' => trans('srm_tender_rfx.company_is_required'),
            'email_type.required' => 'Email type (award or regret) is required.',
            'email_type.in'       => 'Email type must be award or regret.',
        ];
    }
}


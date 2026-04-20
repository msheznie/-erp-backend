<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendItemWiseAwardEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'tender_id' => 'required|integer|min:1',
            'supplier_id' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'tender_id.required' => trans('srm_tender_rfx.item_wise_tender_id_required'),
            'supplier_id.required' => trans('srm_tender_rfx.item_wise_supplier_id_required'),
        ];
    }
}


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetItemWiseLoiLoaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tender_id' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'tender_id.required' => trans('srm_tender_rfx.item_wise_tender_id_required'),
            'tender_id.integer'  => trans('srm_tender_rfx.item_wise_tender_id_integer'),
            'tender_id.min'      => trans('srm_tender_rfx.item_wise_tender_id_min'),
        ];
    }
}
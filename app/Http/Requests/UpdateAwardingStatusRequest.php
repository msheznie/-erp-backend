<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAwardingStatusRequest extends FormRequest
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
            'tender_id' => 'required|integer',
            'user_id'   => 'required|integer',
            'status'    => 'required|in:0,1',
            'remarks'   => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'tender_id.required' => trans('srm_tender_rfx.tender_id_required'),
            'tender_id.integer'  => trans('srm_tender_rfx.tender_id_integer'),

            'user_id.required'   => trans('srm_tender_rfx.user_id_required'),
            'user_id.integer'    => trans('srm_tender_rfx.user_id_integer'),

            'status.required'    => trans('srm_tender_rfx.awarding_status_required'),
            'status.in'          => trans('srm_tender_rfx.awarding_status_invalid'),

            'remarks.string'     => trans('srm_tender_rfx.remarks_string'),
        ];
    }
}

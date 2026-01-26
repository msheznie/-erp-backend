<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardingApprovalCountRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            'tender_id.required' => trans('srm_tender_rfx.tender_id_required'),
            'tender_id.integer'  => trans('srm_tender_rfx.tender_id_integer'),
        ];
    }
}

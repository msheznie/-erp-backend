<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SrmPublicLinkRequest extends FormRequest
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

        $rules =
        [
            'companyId' => 'required|integer',
            'expireDate' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'companyId.required' =>  trans('srm_supplier_master.company_id_is_required'),
            'expireDate.required' => trans('srm_supplier_master.expire_date_is_required'),
        ];
    }
}

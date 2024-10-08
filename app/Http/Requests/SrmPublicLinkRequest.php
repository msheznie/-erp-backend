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
            'description' => 'required|string',
            'expireDate' => 'required',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'companyId.required' =>  'Company id is required',
            'description.required' => 'Description is required',
            'expireDate.required' => 'Expire date is required',
        ];
    }
}

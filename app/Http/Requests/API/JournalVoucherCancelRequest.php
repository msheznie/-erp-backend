<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class JournalVoucherCancelRequest extends FormRequest
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
            'jvMasterAutoId' => 'required|integer|min:1',
            'cancelComments' => 'nullable|string|min:10|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'jvMasterAutoId.required' => 'The jv master auto id field is required.',
            'jvMasterAutoId.integer' => 'The jv master auto id must be an integer.',
            'jvMasterAutoId.min' => 'The jv master auto id must be at least 1.',
            'cancelComments.string' => 'The cancel comments must be a string.',
            'cancelComments.min' => 'The cancel comments must be at least 10 characters.',
            'cancelComments.max' => 'The cancel comments may not be greater than 500 characters.',
        ];
    }
}

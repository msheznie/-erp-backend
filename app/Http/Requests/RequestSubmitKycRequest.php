<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestSubmitKycRequest extends FormRequest
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
            'email' => 'required|email',
            'companyId' => 'required',
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'uuid' => 'required|string',
            'tenderCode' => 'required|string',
            'api_key' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'companyId.required' => 'The company ID is required.',
            'api_key.required' => 'The API key is required.',
            'uuid.required' => 'The uuid is required.',
            'title.required' => 'The title is required.',
            'tenderCode.required' => 'The tenderCode is required.',
        ];
    }
}

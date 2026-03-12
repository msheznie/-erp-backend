<?php

namespace App\Http\Requests\API;

use App\Utils\ResponseUtil;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Response;

class PullBankMasterRequest extends FormRequest
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
            'company_id' => 'sometimes|nullable|integer',
            'bank_short_code' => 'sometimes|nullable|string',
            'bank_short_codes' => 'sometimes|nullable|array',
            'bank_short_codes.*' => 'string',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:500',
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
            'company_id.integer' => trans('custom.companySystemID_is_required'),
            'page.integer' => trans('custom.page_must_be_an_integer'),
            'page.min' => trans('custom.page_must_be_at_least_1'),
            'per_page.integer' => trans('custom.per_page_must_be_an_integer'),
            'per_page.min' => trans('custom.per_page_must_be_at_least_1'),
            'per_page.max' => trans('custom.per_page_cannot_exceed_500'),
        ];
    }

    /**
     * Handle a failed validation attempt (return same format as sendError).
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errorMessage = $validator->errors()->first();
        throw new HttpResponseException(
            Response::json(ResponseUtil::makeError($errorMessage, ['type' => '']), 422)
        );
    }
}

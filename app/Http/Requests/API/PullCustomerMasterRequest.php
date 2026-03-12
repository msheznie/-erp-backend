<?php

namespace App\Http\Requests\API;

use App\Utils\ResponseUtil;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Response;

class PullCustomerMasterRequest extends FormRequest
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
            'company_id' => 'required|integer',
            'category' => 'sometimes|array',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:50',
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
            'company_id.required' => trans('custom.companySystemID_is_required'),
            'category.array' => trans('custom.category_must_be_an_array'),
            'page.integer' => trans('custom.page_must_be_an_integer'),
            'page.min' => trans('custom.page_must_be_at_least_1'),
            'per_page.integer' => trans('custom.per_page_must_be_an_integer'),
            'per_page.min' => trans('custom.per_page_must_be_at_least_1'),
            'per_page.max' => trans('custom.per_page_cannot_exceed_50'),
        ];
    }
}

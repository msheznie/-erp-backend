<?php

namespace App\Http\Requests\API;

use App\Utils\ResponseUtil;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Response;

class PullChartofAccountAPIRequest extends FormRequest
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
            'category' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
                Rule::in(['Balance sheet', 'Profit and loss']),
            ],
            'controlAccounts' => 'sometimes|array',
            'controlAccounts.*' => Rule::in(['PLI', 'PLE', 'BSA', 'BSL', 'BSE']),
            'accountCode' => 'sometimes|array',
            'controlAccountYN' => 'sometimes|nullable|in:Yes,No',
            'isBank' => 'sometimes|nullable|in:Yes,No',
            'defaultTemplateCategory' => 'sometimes|array',
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
            'category.string' => trans('custom.category_must_be_a_string'),
            'category.in' => trans('custom.chart_of_account_category_input_value_is_incorrect'),
            'controlAccounts.array' => trans('custom.control_accounts_must_be_an_array'),
            'controlAccounts.*.in' => trans('custom.chart_of_account_control_account_input_value_is_incorrect'),
            'accountCode.array' => trans('custom.account_code_must_be_an_array'),
            'controlAccountYN.in' => trans('custom.control_account_yn_input_value_is_incorrect'),
            'defaultTemplateCategory.array' => trans('custom.default_template_category_must_be_an_array'),
            'isBank.in' => trans('custom.is_bank_input_value_is_incorrect'),
            'page.integer' => trans('custom.page_must_be_an_integer'),
            'page.min' => trans('custom.page_must_be_at_least_1'),
            'per_page.integer' => trans('custom.per_page_must_be_an_integer'),
            'per_page.min' => trans('custom.per_page_must_be_at_least_1'),
            'per_page.max' => trans('custom.per_page_cannot_exceed_50'),
        ];
    }


}

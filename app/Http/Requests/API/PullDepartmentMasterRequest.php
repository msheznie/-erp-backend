<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PullDepartmentMasterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'sometimes|nullable|integer',
            'code' => 'sometimes|nullable|array',
            'code.*' => 'sometimes|nullable|string|max:255',
            'status' => ['sometimes', 'nullable', 'string', Rule::in(['ACTIVE', 'INACTIVE'])],
            'type' => ['sometimes', 'nullable', 'string', Rule::in(['Parent', 'Final'])],
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'companyID.integer' => trans('custom.companySystemID_is_required'),
            'page.integer' => trans('custom.page_must_be_an_integer'),
            'page.min' => trans('custom.page_must_be_at_least_1'),
            'per_page.integer' => trans('custom.per_page_must_be_an_integer'),
            'per_page.min' => trans('custom.per_page_must_be_at_least_1'),
            'per_page.max' => trans('custom.per_page_cannot_exceed_500'),
            'status.in' => trans('custom.status_input_value_is_incorrect'),
            'type.in' => trans('custom.type_input_value_is_incorrect'),
        ];
    }
}

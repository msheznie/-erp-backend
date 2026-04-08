<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Utils\ResponseUtil;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class GetApprovedCustomerInvoiceBalancesAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                ResponseUtil::makeError($validator->errors()->first(), $validator->errors()->toArray()),
                HttpResponse::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }

    protected function prepareForValidation()
    {
        $raw = $this->input('generated_from');

        if ($raw === null || $raw === '') {
            $this->merge(['generated_from' => null]);
            return;
        }

        // generated_from MUST be an array when present
        if (! is_array($raw)) {
            return;
        }

        $normalized = [];
        foreach ($raw as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $normalized[] = strtoupper(trim((string) $v));
        }

        $this->merge([
            'generated_from' => $normalized === [] ? null : array_values(array_unique($normalized)),
        ]);
    }

    public function rules()
    {
        return [
            'company_id' => ['bail', 'required', 'integer'],

            'invoice_code' => ['bail', 'nullable', 'array'],
            'invoice_code.*' => ['bail', 'string'],

            'customer_code' => ['bail', 'nullable', 'array'],
            'customer_code.*' => ['bail', 'string'],

            'invoice_type' => ['bail', 'nullable', 'string'],

            'generated_from' => ['bail', 'nullable', 'array'],
            'generated_from.*' => ['bail', 'in:POS,CLUB'],
            'page' => ['bail', 'sometimes', 'integer', 'min:1'],
            'per_page' => ['bail', 'sometimes', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            'invoice_code.array' => 'invoice_code must be an array',
            'invoice_code.*.string' => 'Invoice code must be a string',

            'customer_code.array' => 'Customer ode must be an array',
            'customer_code.*.string' => 'Customer code must be a string',
            'company_id.required' => 'Company ID is required',
            'company_id.integer' => 'Company ID must be an integer',
            'invoice_type.string' => 'Invoice type must be a string',
            'generated_from.array' => 'generated_from must be an array (e.g. ["POS"] or ["POS","CLUB"])',
            'generated_from.*.in' => 'Generated from not match with system',
            'page.integer' => 'page must be an integer',
            'page.min' => 'page must be at least 1',
            'per_page.integer' => 'per_page must be an integer',
            'per_page.min' => 'per_page must be at least 1',
            'per_page.max' => 'per_page cannot exceed 500',
        ];
    }
}


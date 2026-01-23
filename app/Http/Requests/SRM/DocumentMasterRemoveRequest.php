<?php

namespace App\Http\Requests\SRM;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidUuid;

class DocumentMasterRemoveRequest extends FormRequest
{
    /**
     * Authorize request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'uuid' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'uuid.required' => 'Document UUID is required.',
        ];
    }
}

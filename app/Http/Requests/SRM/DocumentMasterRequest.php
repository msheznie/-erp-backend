<?php

namespace App\Http\Requests\SRM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentMasterRequest extends FormRequest
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
            'document_names' => [
                'required',
                'string',
                'max:255',
                Rule::unique('srm_document_master', 'document_name')
                    ->where(function ($query) {
                        $query->where('document_area', $this->document_area);
                    })
                    ->ignore($this->uuid, 'uuid') // ignore current record on edit
            ],
            'document_area' => 'required|integer|min:1',

            'default_to_tender' => 'required|integer|in:0,1',
            'default_to_rfx' => 'required|integer|in:0,1',

            'envelope_type' => 'required_if:document_area,2|nullable|integer|in:1,2,3',

            // Attachment validation (only if file exists)
            'attachment.file' => 'sometimes',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'document_name.required' => 'Document name is required.',

            'default_to_tender.required' => 'Default to Tender field is required.',
            'default_to_tender.in' => 'Default to Tender must be 0 or 1.',

            'default_to_rfx.required' => 'Default to RFX field is required.',
            'default_to_rfx.in' => 'Default to RFX must be 0 or 1.',

            'envelope_type.required_if' =>
                'Envelope type is required when Document Area is "Information to be Provided by Bidders".',

            'envelope_type.integer' =>
                'Envelope type must be a valid number.',

            'envelope_type.in' =>
                'Envelope type must be one of: Commercial, Technical, Common.',
        ];
    }

    /**
     * Extra validation logic
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if (empty($this->isInline) || $this->isInline !== true) {
                $hasOriginalFile = !empty($this->original_file_name);
                $hasNewFile = !empty($this->input('attachment.file'));

                // CREATE → attachment required
                if (!$hasOriginalFile && !$hasNewFile) {
                    $validator->errors()->add(
                        'attachment.file',
                        'Attachment file is required.'
                    );
                }
            }

            // Existing validation
            if (
                (int) $this->default_to_tender === 0 &&
                (int) $this->default_to_rfx === 0
            ) {
                $validator->errors()->add(
                    'default_to_tender',
                    'At least one of "Default to Tender" or "Default to RFX" must be selected.'
                );
            }
        });
    }

    /**
     * Normalize incoming values BEFORE validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'default_to_tender' => (int) filter_var(
                $this->input('default_to_tender'),
                FILTER_VALIDATE_BOOLEAN
            ),
            'default_to_rfx' => (int) filter_var(
                $this->input('default_to_rfx'),
                FILTER_VALIDATE_BOOLEAN
            ),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = self::generateUuid();
            }
        });
    }

    /**
     * Generate "UUID" using Str::random
     */
    public static function generateUuid()
    {
        return Str::random(36); // 36-character random string
    }
}

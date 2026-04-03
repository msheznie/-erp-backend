<?php

namespace App\Http\Requests\SRM;

use Illuminate\Foundation\Http\FormRequest;

class SRMScenarioRequest extends FormRequest
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
            'scenarioId' => 'required|integer|exists:srm_email_scenario_master,id',
            'email_subject' => 'required|string|max:500',
            'email_body' => 'required|string',
            'cc_emails' => 'nullable|array',
            'cc_emails.*' => 'nullable|email',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'scenarioId.required' => 'Email scenario is required.',
            'scenarioId.integer' => 'Email scenario must be a valid ID.',
            'scenarioId.exists' => 'Selected email scenario does not exist.',
            'email_subject.required' => 'Email subject is required.',
            'email_subject.max' => 'Email subject may not be greater than 500 characters.',
            'email_body.required' => 'Email body is required.',
            'email_body.string' => 'Email body must be a valid text.',
            'cc_emails.array' => 'CC emails must be an object (key-value pairs).',
            'cc_emails.*.email' => 'Each CC email must be a valid email address.',
        ];
    }
}

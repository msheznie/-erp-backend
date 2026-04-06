<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetInvitationEmailDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'tenderId' => 'nullable|integer|min:1',
            'uuid' => 'nullable|string',
            'companySystemId' => 'nullable|integer|min:1',
            'companyId' => 'nullable|integer|min:1',
            'rfx' => 'nullable|boolean',
        ];
    }
}

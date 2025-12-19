<?php

namespace App\Http\Requests\API;

use App\Models\SMECompanyPolicyMaster;
use InfyOm\Generator\Request\APIRequest;

class CreateSMECompanyPolicyMasterAPIRequest extends APIRequest
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
        return SMECompanyPolicyMaster::$rules;
    }
}

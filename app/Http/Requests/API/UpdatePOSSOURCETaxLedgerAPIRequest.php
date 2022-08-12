<?php

namespace App\Http\Requests\API;

use App\Models\POSSOURCETaxLedger;
use InfyOm\Generator\Request\APIRequest;

class UpdatePOSSOURCETaxLedgerAPIRequest extends APIRequest
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
        $rules = POSSOURCETaxLedger::$rules;
        
        return $rules;
    }
}

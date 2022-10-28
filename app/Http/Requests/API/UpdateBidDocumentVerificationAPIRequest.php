<?php

namespace App\Http\Requests\API;

use App\Models\BidDocumentVerification;
use InfyOm\Generator\Request\APIRequest;

class UpdateBidDocumentVerificationAPIRequest extends APIRequest
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
        $rules = BidDocumentVerification::$rules;
        
        return $rules;
    }
}

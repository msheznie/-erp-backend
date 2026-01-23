<?php

namespace App\Http\Requests\API;

use App\Models\AssetTransferReferredback;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetTransferReferredbackAPIRequest extends FormRequest
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
        $rules = AssetTransferReferredback::$rules;
        
        return $rules;
    }
}

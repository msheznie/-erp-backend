<?php

namespace App\Http\Requests\API;

use App\Models\ERPAssetTransferDetail;
use Illuminate\Foundation\Http\FormRequest;

class CreateERPAssetTransferDetailAPIRequest extends FormRequest
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
        return ERPAssetTransferDetail::$rules;
    }
}

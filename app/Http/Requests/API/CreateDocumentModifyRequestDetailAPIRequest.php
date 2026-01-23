<?php

namespace App\Http\Requests\API;

use App\Models\DocumentModifyRequestDetail;
use Illuminate\Foundation\Http\FormRequest;

class CreateDocumentModifyRequestDetailAPIRequest extends FormRequest
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
        return DocumentModifyRequestDetail::$rules;
    }
}

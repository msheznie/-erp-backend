<?php

namespace App\Http\Requests\API;

use App\Models\DocumentCodeTypeBased;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentCodeTypeBasedAPIRequest extends FormRequest
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
        $rules = DocumentCodeTypeBased::$rules;
        
        return $rules;
    }
}

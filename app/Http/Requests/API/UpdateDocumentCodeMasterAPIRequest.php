<?php

namespace App\Http\Requests\API;

use App\Models\DocumentCodeMaster;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentCodeMasterAPIRequest extends FormRequest
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
        $rules = DocumentCodeMaster::$rules;
        
        return $rules;
    }
}

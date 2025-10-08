<?php

namespace App\Http\Requests\API;

use App\Models\DocumentCodeModuleTranslations;
use InfyOm\Generator\Request\APIRequest;

class UpdateDocumentCodeModuleTranslationsAPIRequest extends APIRequest
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
        $rules = DocumentCodeModuleTranslations::$rules;
        
        return $rules;
    }
}

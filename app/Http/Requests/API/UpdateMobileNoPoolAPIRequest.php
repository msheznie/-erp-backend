<?php

namespace App\Http\Requests\API;

use App\Models\MobileNoPool;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMobileNoPoolAPIRequest extends FormRequest
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
        $rules = MobileNoPool::$rules;
        
        return $rules;
    }
}

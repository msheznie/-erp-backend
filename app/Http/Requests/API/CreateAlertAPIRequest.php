<?php

namespace App\Http\Requests\API;

use App\Models\Alert;
use Illuminate\Foundation\Http\FormRequest;

class CreateAlertAPIRequest extends FormRequest
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
        return Alert::$rules;
    }
}

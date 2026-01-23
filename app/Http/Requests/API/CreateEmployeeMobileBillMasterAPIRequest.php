<?php

namespace App\Http\Requests\API;

use App\Models\EmployeeMobileBillMaster;
use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeMobileBillMasterAPIRequest extends FormRequest
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
        return EmployeeMobileBillMaster::$rules;
    }
}

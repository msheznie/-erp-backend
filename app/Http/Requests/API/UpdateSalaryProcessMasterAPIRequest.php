<?php

namespace App\Http\Requests\API;

use App\Models\SalaryProcessMaster;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSalaryProcessMasterAPIRequest extends FormRequest
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
        return SalaryProcessMaster::$rules;
    }
}

<?php

namespace App\Http\Requests\API;

use App\Models\SrmDepartmentMaster;
use InfyOm\Generator\Request\APIRequest;

class UpdateSrmDepartmentMasterAPIRequest extends APIRequest
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
        $rules = SrmDepartmentMaster::$rules;
        
        return $rules;
    }
}

<?php

namespace App\Http\Requests\API;

use App\Models\MolContribution;
use InfyOm\Generator\Request\APIRequest;

class CreateMolContributionAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = MolContribution::$rules;
        $rules['description'] = 'required|unique:mol_contribution,description,NULL,id,company_id,' . $this->company_id;

        
        return $rules;
    }

    public function messages()
    {
        return [
            'description.unique' => trans('custom.contribution_description_already_exists'),
        ];
    }
}

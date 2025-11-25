<?php

namespace App\Http\Requests\API;

use App\Models\MolContribution;
use InfyOm\Generator\Request\APIRequest;

class UpdateMolContributionAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = MolContribution::$rules;
        
        $id = $this->route('mol_contribution') ?? $this->route('id') ?? $this->route('molContribution');
        
        if ($id) {
            $rules['description'] = 'required|unique:mol_contribution,description,' . $id . ',id';
        } else {
            $rules['description'] = 'required|unique:mol_contribution,description';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'description.unique' => trans('custom.contribution_description_already_exists'),
        ];
    }
}

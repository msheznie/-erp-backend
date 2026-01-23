<?php

namespace App\Http\Requests\API;

use App\Models\MolContribution;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMolContributionAPIRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = MolContribution::$rules;

        $id = $this->route('mol_contribution') ?? $this->route('id') ?? $this->route('molContribution');

        $rules['description'] = 'required|unique:mol_contribution,description,' . ($id ?? 'NULL') . ',id,company_id,' . $this->company_id;

        return $rules;
    }

    public function messages()
    {
        return [
            'description.unique' => trans('custom.contribution_description_already_exists'),
        ];
    }
}

<?php

namespace App\Http\Requests\API;

use App\Models\ContractMaster;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class CreateContractMasterAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tenderId' => 'required',
            'companySystemID' => 'required',
            'contractType' => 'required',
            'title' => ['required',
                Rule::unique('cm_contract_master')
                    ->where('companySystemID', $this->input('companySystemID'))]
        ];
    }

    public function messages()
    {
        return [
            'companySystemID.required' => 'Company is required.',
            'tenderId.required' => 'Tender ID is required.',
            'contractType.required' => 'Contract Type is required.',
            'title.required' => 'Contract Title is required.',
            'title.unique' => 'Contract Title cannot be duplicated.',
        ];
    }
}

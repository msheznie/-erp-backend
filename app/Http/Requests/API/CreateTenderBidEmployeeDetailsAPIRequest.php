<?php

namespace App\Http\Requests\API;

use App\Models\SrmTenderBidEmployeeDetails;
use Illuminate\Foundation\Http\FormRequest;

class CreateTenderBidEmployeeDetailsAPIRequest extends FormRequest
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
        return [
            'emp_id' => 'required',
            'tender_id' => 'required'
        ]; 
    }

    /**
     * Customize validation messages.
     */
    public function messages(): array
    {
        return [
            'emp_id.required' => 'Employee ID/s are required.',
            'tender_id.required' => 'Tender ID is required.',
        ];
    }
}

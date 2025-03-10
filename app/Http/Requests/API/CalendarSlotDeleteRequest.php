<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class CalendarSlotDeleteRequest extends FormRequest
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
            'dateFrom' => 'required|date',
            'dateTo' => 'required|date|after_or_equal:dateFrom',
        ];
    }
    /**
     * Customize validation messages.
     */
    public function messages(): array
    {
        return [
            'dateFrom.required' => 'From Date is required.',
            'dateTo.required' => 'To Date is required.',
            'dateTo.after_or_equal' => 'To Date must be greater than or equal to From Date.',
        ];
    }
}

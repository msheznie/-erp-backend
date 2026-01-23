<?php

namespace App\Http\Requests\API;

use App\Models\BidSchedule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBidScheduleAPIRequest extends FormRequest
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
        $rules = BidSchedule::$rules;
        
        return $rules;
    }
}

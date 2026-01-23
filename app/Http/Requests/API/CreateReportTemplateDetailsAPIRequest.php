<?php

namespace App\Http\Requests\API;

use App\Models\ReportTemplateDetails;
use Illuminate\Foundation\Http\FormRequest;

class CreateReportTemplateDetailsAPIRequest extends FormRequest
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
        return ReportTemplateDetails::$rules;
    }
}

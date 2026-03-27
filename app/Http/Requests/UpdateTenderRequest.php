<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input before validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'award_visibility_type' => is_array($this->award_visibility_type)
                ? ($this->award_visibility_type[0] ?? null)
                : $this->award_visibility_type,

            'evaluation_type_id' => is_array($this->evaluation_type_id)
                ? ($this->evaluation_type_id[0] ?? null)
                : $this->evaluation_type_id,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules()
    {
        return [
            'show_award_detail' => 'nullable|boolean',

            'award_visibility_type' => [
                'nullable',
                'required_if:show_award_detail,1',
                'integer',
                'in:1,2,3',
            ],

            'evaluation_type_id' => [
                'nullable',
                'integer',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'award_visibility_type.required_if' => trans('srm_tender_rfx.select_award_details_area'),
            'award_visibility_type.in'          => trans('srm_tender_rfx.select_award_details_area'),
        ];
    }

    /**
     * Complex conditional validation
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $showAwardDetail     = (bool) $this->input('show_award_detail');
            $awardVisibilityType = (int) $this->input('award_visibility_type');
            $evaluationTypeId    = (int) $this->input('evaluation_type_id');

            if ($showAwardDetail) {

                // Evaluation Type 1 → Only allow 3
                if ($evaluationTypeId === 1 && $awardVisibilityType !== 3) {
                    $validator->errors()->add(
                        'award_visibility_type',
                        trans('srm_tender_rfx.select_award_details_area')
                    );
                }

                // Evaluation Type 2 → Only allow 1 or 2
                if ($evaluationTypeId === 2 && !in_array($awardVisibilityType, [1, 2], true)) {
                    $validator->errors()->add(
                        'award_visibility_type',
                        trans('srm_tender_rfx.select_award_details_area')
                    );
                }
            }
        });
    }
}

<?php

namespace App\Http\Requests\SRM;

use App\Models\TenderMaster;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
class UpdateTenderCalendarDaysRequest extends FormRequest
{
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
        $rules =
            [
                'tenderCode' => 'required',
                'submissionClosingDate' => 'required',
                'bidSubmissionClosingTime' => 'required',
                'comment' => 'required',
            ];

       $tenderData = TenderMaster::getTenderByUuid($this->input('tenderCode'));

       if($tenderData['stage'] == 1)
       {

           $rules = array_merge($rules, [
                   'bidOpeningStartDate' => 'required',
                   'bidOpeningEndDate' => 'required',
                   'bidOpeningStarDateTime' => 'required',
                   'bidOpeningEndDateTime' => 'required',
               ]);
       }


        if($tenderData['stage'] == 2)
        {

            $rules = array_merge($rules, [
                'technicalBidOpeningStartDate' => 'required',
                'technicalBidOpeningStarDateTime' => 'required',
                'commercialBidOpeningStartDate' => 'required',
                'commercialBidOpeningStarDateTime' => 'required',
            ]);

            if ($this->input('technicalBidOpeningEndDate')) {
                $rules['technicalBidOpeningEndDateTime'] = 'required';
            }

            if ($this->input('commercialBidOpeningEndDate')) {
                $rules['commercialBidOpeningEndDateTime'] = 'required';
            }

        }



        return $rules;
    }

    public function messages()
    {
        return [
            'supplierId.required' => 'Supplier id is required',
            'tenderCode.required' => 'Tender Code id is required',
            'submissionClosingDate.required' => 'Bid Submission to Date is required',
            'bidSubmissionClosingTime.required' => 'Bid Submission to time is required',
            'bidOpeningStartDate.required' => 'Bid Opening start date is required',
            'bidOpeningEndDate.required' => 'Bid Opening end date is required',
            'bidOpeningStarDateTime.required' => 'Bid Opening from time is required',
            'bidOpeningEndDateTime.required' => 'Bid Opening to time is required',
            'technicalBidOpeningStarDateTime.required' => 'Technical Bid Opening from time is required',
            'technicalBidOpeningStartDate.required' => 'Technical Bid Opening start date is required',
            'commercialBidOpeningStartDate.required' => 'Commercial Bid Opening from time is required',
            'commercialBidOpeningStarDateTime.required' => 'Commercial Bid Opening start date is required',
            'technicalBidOpeningEndDateTime.required' => 'Technical bid opening to time is required',
            'commercialBidOpeningEndDateTime.required' => 'Commercial Bid Opening  to time is required',
            'comment.required' => 'Comment is required',
        ];
    }
}

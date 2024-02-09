<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceiptApiRequest extends FormRequest
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
            'data.*.receiptType' => "required",
            'data.*.paymentMode' => "required",
            'data.*.payeeType' => "required_if:*.receiptType,1",
            'data.*.customer' => "required",
            'data.*.narration' => "required",
            'data.*.currency' => "required",
            'data.*.bank' => "required",
            'data.*.account'  => "required",
            'data.*.bankCurrency' => "required",
            'data.*.confirmedBy' => "required",
            'data.*.approvedBy' => "required",
            'data.*.documentDate' => "date_format:d-m-Y",
            'data.*.details.*.invoiceCode' => "required_if:*.receiptType,2",
            'data.*.details.*.segmentCode' => "required_if:*.receiptType,2",
            'data.*.details.*.receiptAmount' => "required_if:*.receiptType,2",
            'data.*.details.*.glCode' => "required_if:*.receiptType,1",
            'data.*.details.*.segmentCode' => "required_if:*.receiptType,1",
            'data.*.details.*.amount' => "required_if:*.receiptType,1",
            'data.*.details.*.segmentCode' => "required_if:*.receiptType,3",
            'data.*.details.*.amount' => "required_if:*.receiptType,3",
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages(): array
    {
        return [
            '*.documentDate.date_format' => 'Document Date format should be dd-mm-YYYY',
        ];
    }
}

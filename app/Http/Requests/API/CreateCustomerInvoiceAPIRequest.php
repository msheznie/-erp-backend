<?php

namespace App\Http\Requests\API;

use App\Models\CustomerInvoice;
use Illuminate\Validation\Rule;
use InfyOm\Generator\Request\APIRequest;

class CreateCustomerInvoiceAPIRequest extends APIRequest
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
            'company_id' => ['bail','required','integer'],
            'invoices' => ['bail','required','array'],
            'invoices.*.invoice_type' => ['bail','required','integer',Rule::in([1,2])],
            'invoices.*.segment_code' => ['bail','required_if:invoices.*.invoice_type,2'],
            'invoices.*.warehouse_code' => ['bail','required_if:invoices.*.invoice_type,2'],
            'invoices.*.customer_code' => ['bail','required'],
            'invoices.*.currency_code' => ['bail','required'],
            'invoices.*.document_date' => ['bail','required','date_format:Y-m-d'],
            'invoices.*.customer_invoice_number' => ['bail','required'],
            'invoices.*.bank_code' => ['bail','required'],
            'invoices.*.account_number' => ['bail','required'],
            'invoices.*.comment' => ['bail','required'],
            'invoices.*.details' => ['bail','required','array'],
            'invoices.*.details.*.gl_code' => ['bail','required_if:invoices.*.invoice_type,1'],
            'invoices.*.details.*.service_code' => ['bail','required_if:invoices.*.invoice_type,2'],
            'invoices.*.details.*.segment_code' => ['bail','required_if:invoices.*.invoice_type,1'],
            'invoices.*.details.*.uom' => ['bail','required'],
            'invoices.*.details.*.quantity' => ['bail','required','integer','min:1'],
            'invoices.*.details.*.sales_price' => ['bail','required','numeric','min:1'],
            'invoices.*.details.*.discount_percentage' => ['bail','sometimes','required','numeric','min:0'],
            'invoices.*.details.*.discount_amount' => ['bail','sometimes','required','numeric','min:0'],
            'invoices.*.details.*.margin_percentage' => ['bail','sometimes','required_if:invoices.*.invoice_type,2','numeric','min:0'],
            'invoices.*.details.*.vat_percentage' => ['bail','sometimes','required','numeric','min:0'],
            'invoices.*.details.*.vat_amount' => ['bail','sometimes','required','numeric','min:0']
        ];
    }
}

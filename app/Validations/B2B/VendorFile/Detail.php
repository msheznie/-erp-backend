<?php

namespace App\Validations\B2B\VendorFile;
use Illuminate\Support\Facades\Validator;

class Detail
{
    public $data;
    public $validaitons = array();

    public function __construct($data)
    {
        $this->data  = $data;
        $this->validateData();
    }

    private function validateData()
    {

        foreach ($this->data as $dt)
        {
            $errorArray = !is_null($this->validateDetailData($dt)) ? array_flatten($this->validateDetailData($dt)) : [];
            $this->validaitons[] = (count($errorArray) != 0) ? ['key' => $dt['payment_voucher_code'], 'errors' => $errorArray, 'errorCount' => count($errorArray)] : [] ;
        }
    }

    private function validateDetailData($data)
    {

        $rules = [
            'section_index' => 'required|in:S2',
            'transfer_method' => 'required|in:TRF,SWF,LCL',
            'credit_amount' => 'required|numeric|min:0.01',
            'credit_currency' => 'required|string|size:3',
            'exchange_rate' => 'nullable|numeric',
            'deal_ref_no' => 'nullable|string|max:10',
            'value_date' => 'required|date_format:d/m/Y',
            'debit_account_no' => 'required|numeric',
            'credit_account_no' => 'required|alpha_num',
            'transaction_reference' => 'required_if:payment_details_1,!=,null|string|unique:customermaster,CutomerCode',
            'payment_details_1' => 'required|string',
            'payment_details_2' => 'required|string',
            'beneficiary_name' => 'required|string|max:35',
            'institution_name_address_1' => 'required|string',
            'swift' => 'required|alpha_num|size:8,11',
            'charges_type' => 'required|in:OUR,BEN,SHA',
            'transactor_code' => 'required|string',
            'transaction_reference' => 'nullable|string|max:20',
            'debit_narrative' => 'nullable|string|max:35',
            'credit_narrative' => 'nullable|string|max:35',
            'beneficiary_address' => 'nullable|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'intermediary_account' => 'nullable|string|max:35',
            'email' => 'nullable|email|max:100',
            'dispatch_mode' => 'nullable|in:E',
        ];

        $messages = [
            'section_index.required' => 'Section Index is required.',
            'section_index.in' => 'Section Index must be "S2".',
            'transfer_method.required' => 'Transfer Method is required.',
            'transfer_method.in' => 'Invalid Transfer Method. Allowed values: TRF, SWF, LCL.',
            'credit_amount.required' => 'Credit Amount is required.',
            'credit_amount.numeric' => 'Credit Amount must be a valid number.',
            'credit_amount.min' => 'Credit Amount must be greater than 0.',
            'credit_currency.required' => 'Credit Currency is required.',
            'credit_currency.size' => 'Credit Currency must be exactly 3 characters long.',
            'value_date.required' => 'Value Date is required.',
            'value_date.date_format' => 'Value Date format must be DD/MM/YYYY.',
            'debit_account_no.required' => 'Debit Account Number is required.',
            'debit_account_no.numeric' => 'Debit Account Number must be numeric.',
            'credit_account_no.required' => 'Credit Account Number is required.',
            'credit_account_no.alpha_num' => 'Credit Account Number must be alphanumeric.',
            'transaction_reference.required_if' => 'Transaction Reference is required when Payment Details 1 is provided.',
            'transaction_reference.unique' => 'Transaction Reference must be unique.',
            'payment_details_1.required' => 'Payment Details 1 is required.',
            'payment_details_2.required' => 'Payment Details 2 is required.',
            'beneficiary_name.required' => 'Beneficiary Name is required.',
            'beneficiary_name.max' => 'Beneficiary Name cannot exceed 35 characters.',
            'institution_name_address_1.required' => 'Institution Name is required.',
            'swift.required' => 'SWIFT Code is required.',
            'swift.alpha_num' => 'SWIFT Code must be alphanumeric.',
            'swift.size' => 'SWIFT Code must be either 8 or 11 characters.',
            'charges_type.required' => 'Charges Type is required.',
            'charges_type.in' => 'Charges Type must be either OUR, BEN, or SHA.',
            'transactor_code.required' => 'Transactor Code is required.',
            'exchange_rate.numeric' => 'Exchange rate must be a number.',
            'exchange_rate.min' => 'Exchange rate cannot be negative.',
            'deal_ref_no.string' => 'Deal Reference must be a string.',
            'deal_ref_no.max' => 'Deal Reference cannot exceed 10 characters.',
            'transaction_reference.string' => 'Transaction Reference must be a string.',
            'transaction_reference.max' => 'Transaction Reference cannot exceed 20 characters.',
            'beneficiary_address.regex' => 'Beneficiary Address can only contain letters, numbers, and spaces.',
            'email.email' => 'Email must be a valid email address.',
            'dispatch_mode.in' => 'Dispatch Mode must be "E" if provided.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
    }

}

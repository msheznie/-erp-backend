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
            $errorArray = !empty($this->validateDetailData($dt)) ? array_flatten($this->validateDetailData($dt)) : [];
            $this->validaitons[] = (count($errorArray) != 0) ? ['key' => $dt['payment_voucher_code'], 'errors' => $errorArray, 'errorCount' => count($errorArray)] : [] ;
        }
    }

    private function validateDetailData($data)
    {

        $rules = [
            'section_index' => 'required|in:S2',
            'transfer_method' => 'required|in:TRF,SWF,LCL',
            'credit_amount' => 'required|numeric|min:0.001|max:9999999999999.99',
            'credit_currency' => 'required|string|size:3',
            'exchange_rate' => 'nullable|numeric|max:99999999',
            'deal_ref_no' => 'nullable|string|max:10',
            'value_date' => 'required|date_format:d/m/Y',
            'debit_account_no' => 'required|string|regex:/^\d+$/|max:13',
            'credit_account_no' => 'required|alpha_num|max:30',
            'transaction_reference' => 'nullable|string',
            'payment_details_1' => 'required|string',
            'payment_details_2' => 'required|string',
            'beneficiary_name' => 'required|string',
            'beneficiary_address1' => 'nullable|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'beneficiary_address2' => 'nullable|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'institution_name_address_1' => 'required|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'institution_name_address_2' => 'nullable|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'institution_name_address_3' => 'nullable|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'institution_name_address_4' => 'nullable|string|max:35|regex:/^[a-zA-Z0-9 ]+$/',
            'swift' => 'required|alpha_num|size:8',
            'charges_type' => 'required|in:OUR,BEN,SHA',
            'transactor_code' => 'required|string',
            'debit_narrative' => 'nullable|string',
            'credit_narrative' => 'nullable|string|max:35',
            'intermediary_account' => 'nullable|string|max:35',
            'email' => 'nullable|email|max:100',
            'sort_code_beneficiary_bank' => 'nullable|max:15',
            'IFSC' => 'nullable|max:15|regex:/^[a-zA-Z0-9 ]+$/',
            'fedwire' => 'nullable|max:15|regex:/^[a-zA-Z0-9 ]+$/',
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
            'credit_amount.max' => 'The Credit Amount must not exceed 15 digits in total, including decimals.',
            'credit_currency.required' => 'Credit Currency is required.',
            'credit_currency.size' => 'Credit Currency must be exactly 3 characters long.',
            'value_date.required' => 'Value Date is required.',
            'value_date.date_format' => 'Value Date format must be DD/MM/YYYY.',
            'debit_account_no.required' => 'Debit Account Number is required.',
            'debit_account_no.numeric' => 'The debit account number must be a numeric value.',
            'debit_account_no.regex' => 'The debit account number cannot contain special characters,letters or spaces.',
            'debit_account_no.max' => 'Debit Account Number cannot be more than 13 characters.',
            'credit_account_no.required' => 'Credit Account Number is required.',
            'credit_account_no.alpha_num' => 'Credit Account Number must be alphanumeric.',
            'credit_account_no.max' => 'Credit Account Number cannot be more than 30 characters.',
            'transaction_reference.required' => 'Transaction Reference is required when Payment Details 1 is provided.',
            'payment_details_1.required' => 'Payment Details 1 is required.',
            'payment_details_2.required' => 'Payment Details 2 is required.',
            'beneficiary_name.required' => 'Beneficiary Name is required.',
            'beneficiary_name.max' => 'Beneficiary Name cannot exceed 35 characters.',
            'institution_name_address_1.required' => 'Institution Name Address 1 is required.',
            'swift.required' => 'SWIFT Code is required.',
            'swift.alpha_num' => 'SWIFT Code must be alphanumeric.',
            'swift.size' => 'SWIFT Code must be 8 characters.',
            'charges_type.required' => 'Charges Type is required.',
            'charges_type.in' => 'Charges Type must be either OUR, BEN, or SHA.',
            'transactor_code.required' => 'Transactor Code is required.',
            'exchange_rate.numeric' => 'Exchange rate must be a number.',
            'exchange_rate.max' => 'Exchange rate should be 8 characters.',
            'deal_ref_no.string' => 'Deal Reference must be a string.',
            'deal_ref_no.max' => 'Deal Reference cannot exceed 10 characters.',
            'transaction_reference.string' => 'Transaction Reference must be a string.',
            'transaction_reference.max' => 'Transaction Reference cannot exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'dispatch_mode.in' => 'Dispatch Mode must be "E" if provided.',
            'beneficiary_address1.regex' => 'The beneficiary address1 should only contain letters, numbers, and spaces.',
            'beneficiary_address1.max' => 'The beneficiary address1 must not exceed 35 characters.',
            'institution_name_address_2.regex' => 'The beneficiary address2 should only contain letters, numbers, and spaces.',
            'institution_name_address_2.max' => 'The beneficiary address2 must not exceed 35 characters.',
            'institution_name_address_3.regex' => 'The beneficiary address2 should only contain letters, numbers, and spaces.',
            'institution_name_address_3.max' => 'The beneficiary address2 must not exceed 35 characters.',
            'institution_name_address_4.regex' => 'The beneficiary address2 should only contain letters, numbers, and spaces.',
            'institution_name_address_4.max' => 'The beneficiary address2 must not exceed 35 characters.',
            'institution_name_address_1.regex' => 'Institution Name Address 1 should only contain letters, numbers, and spaces.',
            'institution_name_address_1.max' => 'Institution Name Address 1 must not exceed 35 characters.',
            'intermediary_account.max' => 'Intermediary Account must not exceed 35 characters',
            'sort_code_beneficiary_bank.max' => 'The sort code of beneficiary bank must not exceed 15 characters',
            'IFSC.max' => 'IFSC code must not exceed 15 characters',
            'fedwire.max' => 'Fedwire code must not exceed 15 characters',
            'IFSC.regex' => 'The IFSC code cannot contain special characters,letters or spaces.',
            'fedwire.regex' => 'The Fedwire code cannot contain special characters,letters or spaces.'
        ];

        $validatorDetails = Validator::make($data, $rules, $messages);

        if ($validatorDetails->fails()) {
            return $validatorDetails->errors()->toArray();
        }
    }

}

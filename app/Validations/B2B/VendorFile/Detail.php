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
            'swift' => 'required|alpha_num',
            'charges_type' => 'required|in:OUR,BEN,SHA',
            'transactor_code' => 'required|string',
            'debit_narrative' => 'nullable|string',
            'credit_narrative' => 'nullable|string|max:35',
            'intermediary_account' => 'nullable|string|max:35',
            'sort_code_beneficiary_bank' => 'nullable|max:15',
            'IFSC' => 'nullable|max:15|regex:/^[a-zA-Z0-9 ]+$/',
            'fedwire' => 'nullable|max:15|regex:/^[a-zA-Z0-9 ]+$/',
            'dispatch_mode' => 'nullable|in:E',
            'exchange_rate' => [
                'nullable',
                'numeric',
                'max:99999999',
                function ($attribute, $value, $fail) {
                    if ($value !== null && strlen((string) $value) > 8) {
                        $fail(trans('custom.exchange_rate_max_characters'));
                    }
                },
            ],
            'swift' => [
                'required',
                'alpha_num',
                function ($attribute, $value, $fail) {
                    if (!in_array(strlen($value), [8, 11])) {
                        $fail(trans('custom.swift_code_length'));
                    }
                }
            ],
            'email' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $emails = explode(';', $value);
                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail(str_replace(':email', $email, trans('custom.email_invalid_format')));
                        }
                    }
                }
            ],
        ];

        $messages = [
            'section_index.required' => trans('custom.section_index_required'),
            'section_index.in' => trans('custom.section_index_in'),
            'transfer_method.required' => trans('custom.transfer_method_required'),
            'transfer_method.in' => trans('custom.transfer_method_in'),
            'credit_amount.required' => trans('custom.credit_amount_required'),
            'credit_amount.numeric' => trans('custom.credit_amount_numeric'),
            'credit_amount.min' => trans('custom.credit_amount_min'),
            'credit_amount.max' => trans('custom.credit_amount_max'),
            'credit_currency.required' => trans('custom.credit_currency_required'),
            'credit_currency.size' => trans('custom.credit_currency_size'),
            'value_date.required' => trans('custom.value_date_required'),
            'value_date.date_format' => trans('custom.value_date_date_format'),
            'debit_account_no.required' => trans('custom.debit_account_no_required'),
            'debit_account_no.numeric' => trans('custom.debit_account_no_numeric'),
            'debit_account_no.regex' => trans('custom.debit_account_no_regex'),
            'debit_account_no.max' => trans('custom.debit_account_no_max'),
            'credit_account_no.required' => trans('custom.credit_account_no_required'),
            'credit_account_no.alpha_num' => trans('custom.credit_account_no_alpha_num'),
            'credit_account_no.max' => trans('custom.credit_account_no_max'),
            'transaction_reference.required' => trans('custom.transaction_reference_required'),
            'payment_details_1.required' => trans('custom.payment_details_1_required'),
            'payment_details_2.required' => trans('custom.payment_details_2_required'),
            'beneficiary_name.required' => trans('custom.beneficiary_name_required'),
            'beneficiary_name.max' => trans('custom.beneficiary_name_max'),
            'institution_name_address_1.required' => trans('custom.institution_name_address_1_required'),
            'swift.required' => trans('custom.swift_required'),
            'swift.alpha_num' => trans('custom.swift_alpha_num'),
            'charges_type.required' => trans('custom.charges_type_required'),
            'charges_type.in' => trans('custom.charges_type_in'),
            'transactor_code.required' => trans('custom.transactor_code_required'),
            'exchange_rate.numeric' => trans('custom.exchange_rate_numeric'),
            'exchange_rate.max' => trans('custom.exchange_rate_max'),
            'deal_ref_no.string' => trans('custom.deal_ref_no_string'),
            'deal_ref_no.max' => trans('custom.deal_ref_no_max'),
            'transaction_reference.string' => trans('custom.transaction_reference_string'),
            'transaction_reference.max' => trans('custom.transaction_reference_max'),
            'email.email' => trans('custom.email_email'),
            'dispatch_mode.in' => trans('custom.dispatch_mode_in'),
            'beneficiary_address1.regex' => trans('custom.beneficiary_address1_regex'),
            'beneficiary_address1.max' => trans('custom.beneficiary_address1_max'),
            'institution_name_address_2.regex' => trans('custom.institution_name_address_2_regex'),
            'institution_name_address_2.max' => trans('custom.institution_name_address_2_max'),
            'institution_name_address_3.regex' => trans('custom.institution_name_address_3_regex'),
            'institution_name_address_3.max' => trans('custom.institution_name_address_3_max'),
            'institution_name_address_4.regex' => trans('custom.institution_name_address_4_regex'),
            'institution_name_address_4.max' => trans('custom.institution_name_address_4_max'),
            'institution_name_address_1.regex' => trans('custom.institution_name_address_1_regex'),
            'institution_name_address_1.max' => trans('custom.institution_name_address_1_max'),
            'intermediary_account.max' => trans('custom.intermediary_account_max'),
            'sort_code_beneficiary_bank.max' => trans('custom.sort_code_beneficiary_bank_max'),
            'IFSC.max' => trans('custom.IFSC_max'),
            'fedwire.max' => trans('custom.fedwire_max'),
            'IFSC.regex' => trans('custom.IFSC_regex'),
            'fedwire.regex' => trans('custom.fedwire_regex')
        ];


        $validatorDetails = Validator::make($data, $rules, $messages);

        if ($validatorDetails->fails()) {
            return $validatorDetails->errors()->toArray();
        }
    }

}

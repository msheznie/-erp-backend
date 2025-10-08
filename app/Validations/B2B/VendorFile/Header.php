<?php

namespace App\Validations\B2B\VendorFile;
use Illuminate\Support\Facades\Validator;

class Header
{
    public $data;
    public $validations;

    public function __construct($data)
    {
        $this->data  = $data;
        $this->validateData();
    }


    private function validateData()
    {
        foreach ($this->data as $data)
        {
            $errorArray = !is_null($this->validateHeaderData($data)) ? array_flatten($this->validateHeaderData($data)) : [];
            $this->validaitons[] = (count($errorArray) != 0) ? ['key' => 'header', 'errors' => $errorArray, 'errorCount' => count($errorArray)] : [] ;
        }

    }


    private function validateHeaderData($data)
    {

        $validator2 = Validator::make([
            'index_0' => $data[0],
            'index_1' => $data[1],
            'index_2' => $data[2],
            'index_3' => $data[3],
            'index_4' => $data[4],
            'index_5' => $data[5],
            'index_6' => $data[6],
            'index_7' => $data[7],
        ], [
            'index_0' => 'required|string|min:2',
            'index_1' => 'required|string|max:15',
            'index_2' => 'required|string|regex:/^\d+$/|max:13',
            'index_3' => 'required|in:MXD',
            'index_4' => 'required|in:1,M',
            'index_6' => 'required|date_format:d/m/Y',
            'index_7' => 'required|string|max:25',
        ], [
            'index_0.required' => trans('custom.header_section_index_required'),
            'index_0.string' => trans('custom.header_section_index_string'),
            'index_0.min' => trans('custom.header_section_index_min'),
            'index_1.required' => trans('custom.header_company_cr_required'),
            'index_1.regex' => trans('custom.header_company_cr_regex'),
            'index_1.max' => trans('custom.header_company_cr_max'),
            'index_2.required' => trans('custom.header_debit_account_no_required'),
            'index_2.numeric' => trans('custom.header_debit_account_no_numeric'),
            'index_2.size' => trans('custom.header_debit_account_no_size'),
            'index_2.regex' => trans('custom.header_debit_account_no_regex'),
            'index_2.max' => trans('custom.header_debit_account_no_max'),
            'index_3.required' => trans('custom.header_transfer_method_required'),
            'index_3.in' => trans('custom.header_transfer_method_in'),
            'index_4.required' => trans('custom.header_debit_mode_required'),
            'index_4.in' => trans('custom.header_debit_mode_in'),
            'index_6.required' => trans('custom.header_request_date_required'),
            'index_6.date_format' => trans('custom.header_request_date_format'),
            'index_7.required' => trans('custom.header_seventh_index_required'),
            'index_7.max' => trans('custom.header_batch_reference_max'),

        ]);


        if ($validator2->fails()) {
            // Return validation errors as an array
            return $validator2->errors()->toArray();
        }

    }
}

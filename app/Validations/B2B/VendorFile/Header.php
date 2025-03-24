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
            'index_0.required' => 'Section Index is required.',
            'index_0.string' => 'The Section index must be a string.',
            'index_0.min' => 'The Section index must be at least 2 characters.',
            'index_1.required' => 'The Company CR index is required.',
            'index_1.regex' => 'The Company CR index must match the format CRxxxxxxxxxxxxx (where x is a digit).',
            'index_1.max' => 'The Company CR cannot be more than 15 characters.',
            'index_2.required' => 'The Debit Account No index is required.',
            'index_2.numeric' => 'The Debit Account No must be a numeric value.',
            'index_2.size' => 'The Debit Account must be 13 characters.',
            'index_2.regex' => 'The debit account number cannot contain special characters,letters or spaces.',
            'index_2.max' => 'The Debit Account cannot be more than 13 digits.',
            'index_3.required' => 'The Transfer method is required.',
            'index_3.in' => 'The Transfer method must always be MXD.',
            'index_4.required' => 'The Debit modeis required.',
            'index_4.in' => 'The Debit mode must be either 1 or M.',
            'index_6.required' => 'The Request Date is required.',
            'index_6.date_format' => 'The Request Date must be a valid date and must be DD/MM/YYYY.',
            'index_7.required' => 'The seventh index is required.',
            'index_7.max' => 'The batch referece cannot be more than 25 digits.',

        ]);


        if ($validator2->fails()) {
            // Return validation errors as an array
            return $validator2->errors()->toArray();
        }

    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\StoreReceiptApiRequest;
use App\Mapper\LaravelValidationToAPIJSON;
use App\Services\API\ReceiptAPIService;
use App\Traits\DocumentSystemMappingTrait;
use Illuminate\Http\Request;

class ReceiptAPIController extends AppBaseController
{

    use DocumentSystemMappingTrait;
    public function store(Request $request,ReceiptAPIService $receiptAPIService)
    {
        $input = $request->input();
        $header = $request->header('Authorization');
        $db = isset($request->db) ? $request->db : "";
        $rules = [
            '*.receiptType' => 'required|integer',
            '*.paymentMode' => 'required|integer',
            '*.payeeType' => 'required|integer',
            '*.customer' => 'nullable|string',
            '*.currency' => 'required|string|max:3',
            '*.narration' => 'required|string',
            '*.documentDate' => 'required|date_format:d-m-Y',
            '*.bank' => 'required|string',
            '*.account' => 'required|string',
            '*.bankCurrency' => 'required|string|max:3',
            '*.vatApplicable' => 'required|in:yes,no',
            '*.details.*.invoiceCode' => 'required_if:*.receiptType,2',
            '*.details.*.segmentCode' => 'required_if:*.receiptType,1,2,3',
            '*.details.*.receiptAmount' => 'required_if:*.receiptType,2',
            '*.details.*.glCode' => 'required_if:*.receiptType,1',
            '*.details.*.amount' => 'required_if:*.receiptType,1,3',
            '*.other' => 'nullable',

        ];

        $messages =  [
            '*.receiptType.required' => 'The receipt type is required.',
            '*.paymentMode.required' => 'The payment mode is required.',
            '*.payeeType.required' => 'The payee type is required.',
            '*.customer.required' => 'The customer field is required.',
            '*.customer.string' => 'The customer field must be a valid string.',
            '*.currency.required' => 'The currency is required and must be a 3-letter code.',
            '*.narration.required' => 'Please provide a narration.',
            '*.documentDate.required' => 'The document date is required and must follow the format dd-MM-yyyy.',
            '*.customer.required' => 'The customer field is required.',
            '*.currency.required' => 'The currency is required and must be a 3-letter code.',
            '*.narration.required' => 'Please provide a narration.',
            '*.documentDate.required' => 'The document date is required and must follow the format dd-MM-yyyy.',
            '*.documentDate.date_format' => 'The document date must follow the format dd-MM-yyyy.',
            '*.bank.required' => 'The bank field is required.',
            '*.account.required' => 'The account field is required.',
            '*.bankCurrency.required' => 'The bank currency is required and must be a 3-letter code.',
            '*.vatApplicable.required' => 'The VAT applicability is required and must be either yes or no.',
            '*.details.*.invoiceCode.required_if' => 'The invoice code is required when receipt type is 2.',
            '*.details.*.segmentCode.required_if' => 'The segment code is required when receipt type is 1, 2, or 3.',
            '*.details.*.receiptAmount.required_if' => 'The receipt amount is required when receipt type is 2.',
            '*.details.*.glCode.required_if' => 'The GL code is required when receipt type is 1.',
            '*.details.*.amount.required_if' => 'The amount is required when receipt type is 1 or 3.',
        ];

        $validator = \Validator::make($input['data'], $rules,$messages);

        $validator->after(function ($validator) use ($input) {
            foreach ($input['data'] as $index => $data) {
                if (isset($data['receiptType']) && $data['receiptType'] == 1 && isset($data['payeeType']) && $data['payeeType'] == 3) {
                    if (empty($data['other'])) {
                        $validator->errors()->add("data.$index.other", 'The other field is required when receipt type is 1 and payee type is 3.');
                    }


                    if(!empty($data['other']) && !is_string($data['other']))
                    {
                        $validator->errors()->add("data.$index.other", 'The other field must be a valid string.');
                    }
                }

                if (isset($data['receiptType']) && $data['receiptType'] == 1 && isset($data['payeeType']) && $data['payeeType'] == 2) {
                    if (empty($data['employee'])) {
                        $validator->errors()->add("data.$index.employee", 'The employee field is required when receipt type is 1 and payee type is 3.');
                    }

                    if(!empty($data['employee']) && !is_string($data['employee']))
                    {
                        $validator->errors()->add("data.$index.employee", 'The employee field must be a valid string.');
                    }

                }else {
                    if(isset($data['employee']))
                    {
                        $validator->errors()->add("data.$index.employee", 'The employee field is required when receipt type is 1 and payee type is 3.');
                    }
                }

                if ((isset($data['receiptType']) && $data['receiptType'] == 2) || (isset($data['receiptType']) && $data['receiptType'] == 3) ||
                (isset($data['receiptType']) && $data['receiptType'] == 1 && isset($data['payeeType']) && $data['payeeType'] == 1)) {
                    if (empty($data['customer'])) {
                        $validator->errors()->add("data.$index.customer", 'The customer field is required when receipt type is 2 or 3 or when receipt type is 1 and payee type is 1.');
                    }
                 }
            }
        });



        if ($validator->fails()) {
            $messages = new LaravelValidationToAPIJSON();
            $data = $messages->getMessage($validator);
            return $this->sendAPIError("Validation Failed", 422, $data->toArray());
        }
       
        $data = $receiptAPIService->buildDataToStore($input,$db);
        $createReceiptVoucher = $receiptAPIService->storeReceiptVoucherData($data,$db);
        if($createReceiptVoucher['status'] === 'success') {
            $receiptVoucherIds = collect($createReceiptVoucher['data'])->pluck('custReceivePaymentAutoID')->toArray();
            $documentSystemID = 21;
            $this->storeToDocumentSystemMapping($documentSystemID,$receiptVoucherIds,$header);
            return $this->sendResponse($createReceiptVoucher['data'],'Receipt Voucher Created Successfully!');

        }else {
            if(isset($createReceiptVoucher['code']))
                return $this->sendAPIError("Receipt voucher creation failed",422,$createReceiptVoucher['data']);

            return $this->sendAPIError("Receipt voucher creation failed",500,$createReceiptVoucher['message']);

        }
    }
}

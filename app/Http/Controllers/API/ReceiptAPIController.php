<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\StoreReceiptApiRequest;
use App\Services\API\ReceiptAPIService;
use App\Traits\DocumentSystemMappingTrait;

class ReceiptAPIController extends AppBaseController
{

    use DocumentSystemMappingTrait;
    public function store(StoreReceiptApiRequest $request,ReceiptAPIService $receiptAPIService)
    {
        $input = $request->input();
        $header = $request->header('Authorization');
        $db = isset($request->db) ? $request->db : "";

        if(!isset($input['data'])) {
            return $this->sendError("Unprocessable Entity",422);
        }

        $data = $receiptAPIService->buildDataToStore($input,$db);
        $createReceiptVoucher = $receiptAPIService->storeReceiptVoucherData($data,$db);

        if($createReceiptVoucher['status'] === 'success') {
            $receiptVoucherIds = collect($createReceiptVoucher['data'])->pluck('custReceivePaymentAutoID')->toArray();
            $documentSystemID = 21;
            $this->storeToDocumentSystemMapping($documentSystemID,$receiptVoucherIds,$header);
            return $this->sendResponse($createReceiptVoucher,'Receipt Voucher Created Successfully!');

        }else {
            if(isset($createReceiptVoucher['code']))
                return $this->sendError($createReceiptVoucher['data'],422);

            return $this->sendError($createReceiptVoucher['message']);

        }
    }
}

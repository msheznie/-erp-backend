<?php

namespace App\Services\API;

class ReceiptDetailsAPIService
{

    public static function storeReceiptDetails($directReceipt, $receiptVoucher)
    {
        switch ($receiptVoucher->documentType) {
            case 15:
            case 14 :
                $return = DirectReceiptDetailAPIService::storeDirectReceiptDetail($directReceipt, $receiptVoucher);
                return $return;
                break;
            case 13 :
                $result = CustomerInvoiceReceiptDetailsService::storeReceiptDetails($directReceipt, $receiptVoucher);
                return $result;
                break;
            default:
                break;
        }
    }
}

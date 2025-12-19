<?php

namespace App\Services;

use App\Models\JobErrorLog;
use App\Models\AccountsPayableLedger;
use App\Services\AccountPayableLedger\DebitNoteAPLedgerService;
use App\Services\AccountPayableLedger\SupplierInvoiceAPLedgerService;
use App\Services\AccountPayableLedger\PaymentVoucherAPLedgerService;
use App\Services\AccountPayableLedger\PurchaseReturnAPLedgerService;

class AccountPayableLedgerService
{
	public static function postLedgerEntry($masterModel)
	{
        switch ($masterModel["documentSystemID"]) {
            case 15: // Debit Note
                $result = DebitNoteAPLedgerService::processEntry($masterModel);
                break;
            case 11: // SI - Supplier Invoice
                $result = SupplierInvoiceAPLedgerService::processEntry($masterModel);
                break;
            case 4: // Payment Voucher
                $result = PaymentVoucherAPLedgerService::processEntry($masterModel);
                break;
            case 24: // Purchase return
                $result = PurchaseReturnAPLedgerService::processEntry($masterModel);
                break;
            default:
                Log::warning('Document ID not found ' . date('H:i:s'));
        }

        if (!$result['status']) {
            return $result;
        } 

        $finalData = $result['data']['finalData'];

        if ($finalData) {
            foreach ($finalData as $data)
            {
                AccountsPayableLedger::create($data);
            }
        }
        
        return ['status' => true];
	}
}
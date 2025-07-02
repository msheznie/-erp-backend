<?php

namespace App\Services;

use App\Jobs\CreateConsoleJV;
use App\Services\AccountReceivableLedger\CreditNoteARLedgerService;
use App\Services\AccountReceivableLedger\CustomerInvoiceARLedgerService;
use App\Services\AccountReceivableLedger\ReceiptVoucherARLedgerService;
use App\Services\AccountReceivableLedger\SalesReturnARLedgerService;
use App\Models\AccountsReceivableLedger;
use Illuminate\Support\Facades\Log;

class AccountReceivableLedgerService
{
	public static function postLedgerEntry($masterModel, $otherData = null)
    {
         switch ($masterModel["documentSystemID"]) {
            case 19: // Credit Note
                $result = CreditNoteARLedgerService::processEntry($masterModel); 
                break;
            case 20: // Customer Invoice
                $result = CustomerInvoiceARLedgerService::processEntry($masterModel); 
                break;
            case 21: // Receipt Voucher
                $result = ReceiptVoucherARLedgerService::processEntry($masterModel); 
                break;
            case 87: // Sales Return
                $result = SalesReturnARLedgerService::processEntry($masterModel); 
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
                AccountsReceivableLedger::create($data);
            }

            self::processDataRelatedWithAccountReceivableLedger($masterModel, $otherData);
        }

        return ['status' => true];
	}

    public static function processDataRelatedWithAccountReceivableLedger($masterModel, $otherData) {
        if (!is_null($otherData)) {
            switch ($masterModel["documentSystemID"]) {
                case 20: // Customer Invoice
                    CreateConsoleJV::dispatch($otherData);
                    break;
                default:
                    Log::warning('Document ID not found ' . date('H:i:s'));
            }
        }
    }
}

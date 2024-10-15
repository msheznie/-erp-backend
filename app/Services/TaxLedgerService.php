<?php

namespace App\Services;

use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Services\TaxLedger\CreditNoteTaxLedgerService;
use App\Services\TaxLedger\DebitNoteTaxLedgerService;
use App\Services\TaxLedger\DOTaxLedgerService;
use App\Services\TaxLedger\GRVTaxLedgerService;
use App\Services\TaxLedger\PRTaxLedgerService;
use App\Services\TaxLedger\SalesInvoiceTaxLedgerService;
use App\Services\TaxLedger\SRTaxLedgerService;
use App\Services\TaxLedger\SupplierInvoiceTaxLedgerService;
use App\Services\TaxLedger\PaymentVoucherTaxLedgerService;
use App\Services\TaxLedger\GPOSSalesTaxLedgerService;
use App\Services\TaxLedger\RPOSSalesTaxLedgerService;
use App\Services\TaxLedger\RecieptVoucherTaxLedgerService;
use Illuminate\Support\Facades\Log;

class TaxLedgerService
{
	public static function postLedgerEntry($taxLedgerData, $masterModel)
	{
        
        switch ($masterModel["documentSystemID"]) {
            case 3: //GRV
                $result = GRVTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 24://Purchase Return
                $result = PRTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 20://Sales Invoice
                $result = SalesInvoiceTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 71://Delivery Order
                $result = DOTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 87://SalesReturn
                $result = SRTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 15://Debit Note
                $result = DebitNoteTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 19://Credit Note
                $result = CreditNoteTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 11://Supplier Invoice
                $result = SupplierInvoiceTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 4://Payment Voucher
                $result = PaymentVoucherTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 110://GPOS Sales
                $result = GPOSSalesTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 111://RPOS Sales
                $result = RPOSSalesTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            case 21:// Customer Receive Payment
                $result = RecieptVoucherTaxLedgerService::processEntry($taxLedgerData, $masterModel);
                break;
            default:
                $result = ['status' => false];
                break;
        }

        if (!$result['status']) {
            return $result;
        } 

        $finalData = $result['data']['finalData'];
        $finalDetailData = $result['data']['finalDetailData'];


        if ($finalData) {
            foreach ($finalData as $data)
            {
                TaxLedger::create($data);
            }

            foreach ($finalDetailData as $data)
            {
                TaxLedgerDetail::create($data);
            }
        }

        return ['status' => true];
	}
}

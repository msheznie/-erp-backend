<?php

namespace App\Services\API;

use App\Models\ChartOfAccount;
use App\Models\CompanyPolicyMaster;
use App\Models\DirectReceiptDetail;
use App\Models\SegmentMaster;

class DirectReceiptDetailAPIService
{

    public static function storeDirectReceiptDetail($directReceipt, $receiptVoucher) {
        $objDirectReceipt = new DirectReceiptDetail();
        $objDirectReceipt->directReceiptAutoID = $receiptVoucher->custReceivePaymentAutoID;
        $objDirectReceipt->companyID = $receiptVoucher->companyID;
        $objDirectReceipt->companySystemID = $receiptVoucher->companySystemID;
        if(isset($directReceipt['glCode']))
            $objDirectReceipt = self::setGlDetails($directReceipt,$objDirectReceipt);
        $objDirectReceipt = self::setSegemntDetails($directReceipt,$objDirectReceipt);
        $objDirectReceipt = self::setCommonDetails($directReceipt,$objDirectReceipt,$receiptVoucher);
        $objDirectReceipt = self::setCustomerDetails($directReceipt,$objDirectReceipt,$receiptVoucher);

        $receiptVoucher->directdetails()->create($objDirectReceipt->toArray());
    }

    private static function setCustomerDetails($directReceipt,$objDirectReceipt,$receiptVoucher):DirectReceiptDetail {

        $objDirectReceipt->DRAmountCurrency = $receiptVoucher->custTransactionCurrencyID;
        $objDirectReceipt->DDRAmountCurrencyER = $receiptVoucher->custTransactionCurrencyER;
        $objDirectReceipt->DRAmount = $directReceipt['amount'];

        return $objDirectReceipt;
    }

    private static function setGlDetails($directReceipt,$objDirectReceipt): DirectReceiptDetail {
        $chartOfAccountDetails = ChartOfAccount::select(['chartOfAccountSystemID','AccountCode','AccountDescription'])->where('AccountCode',$directReceipt['glCode'])->first();
        $objDirectReceipt->chartOfAccountSystemID = $chartOfAccountDetails->chartOfAccountSystemID;
        $objDirectReceipt->glCode = $chartOfAccountDetails->AccountCode;
        $objDirectReceipt->glCodeDes = $chartOfAccountDetails->AccountDescription;

        return $objDirectReceipt;
    }

    private static function setSegemntDetails($directReceipt,$objDirectReceipt): DirectReceiptDetail {
        $segmentDetails = SegmentMaster::select(['serviceLineCode','serviceLineSystemID'])->where('ServiceLineCode',$directReceipt['segmentCode'])->first();
        $objDirectReceipt->serviceLineSystemID = $segmentDetails->serviceLineSystemID;
        $objDirectReceipt->serviceLineCode = $segmentDetails->serviceLineCode;
        return $objDirectReceipt;
    }

    private static function setCommonDetails($directReceipt,$objDirectReceipt,$receiptVoucher):DirectReceiptDetail {
        $objDirectReceipt->localCurrency = $receiptVoucher->localCurrency->currencyID;
        $objDirectReceipt->localCurrencyER = $receiptVoucher->localCurrencyER;
        $objDirectReceipt->comRptCurrency = $receiptVoucher->companyRptCurrencyID;
        $objDirectReceipt->comRptCurrencyER = $receiptVoucher->companyRptCurrencyER;
        $vatAmount = ($receiptVoucher->isVATApplicable) ? $directReceipt['vatAmount'] : 0;
        $objDirectReceipt->VATAmount = $vatAmount;

        $currency = \Helper::convertAmountToLocalRpt($receiptVoucher->documentSystemID, $objDirectReceipt->directReceiptAutoID, $directReceipt['amount']);
        $objDirectReceipt->comRptAmount = \Helper::roundValue($currency['reportingAmount']);
        $objDirectReceipt->localAmount = \Helper::roundValue($currency['localAmount']);

        $currencyVAT = \Helper::convertAmountToLocalRpt($receiptVoucher->documentSystemID, $objDirectReceipt->directReceiptAutoID, $vatAmount);
        $policy = CompanyPolicyMaster::where('companySystemID', $receiptVoucher->companySystemID)
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        if($policy == true) {
            $objDirectReceipt->VATAmountLocal = $vatAmount / $receiptVoucher->localCurrencyER;
            $objDirectReceipt->VATAmountRpt = $vatAmount / $receiptVoucher->companyRptCurrencyER;
        }  if($policy == false) {
            $objDirectReceipt->VATAmountRpt = \Helper::roundValue($currencyVAT['reportingAmount']);
            $objDirectReceipt->VATAmountLocal = \Helper::roundValue($currencyVAT['localAmount']);
        }

        $netAmount = ($directReceipt['amount'] - $vatAmount);
        $objDirectReceipt->netAmount = $netAmount;
        $currencyNet = \Helper::convertAmountToLocalRpt($receiptVoucher->documentSystemID, $objDirectReceipt->directReceiptAutoID, $netAmount);
        if($policy == true) {
            $objDirectReceipt->netAmountRpt = \Helper::roundValue($netAmount/$receiptVoucher->companyRptCurrencyER);
            $objDirectReceipt->netAmountLocal = \Helper::roundValue($netAmount/$receiptVoucher->localCurrencyER);
        }
        if($policy == false) {
            $objDirectReceipt->netAmountRpt = \Helper::roundValue($currencyNet['reportingAmount']);
            $objDirectReceipt->netAmountLocal = \Helper::roundValue($currencyNet['localAmount']);
        }

        $objDirectReceipt->comments = $directReceipt['comments'];
        return $objDirectReceipt;
    }

}

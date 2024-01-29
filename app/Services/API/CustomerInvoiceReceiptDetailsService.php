<?php

namespace App\Services\API;

use App\Models\AccountsReceivableLedger;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\CustomerInvoice;

class CustomerInvoiceReceiptDetailsService
{
    public static function storeReceiptDetails($details,$receiptVoucher) {
        $customerInvoiceDetailsObj = new CustomerReceivePaymentDetail();
        $customerInvoiceDetailsObj->custReceivePaymentAutoID = $receiptVoucher->custReceivePaymentAutoID;
        $customerInvoiceDetailsObj = self::setCustomerInvoiceDetails($details,$customerInvoiceDetailsObj);
        $customerInvoiceDetailsObj = self::setAccountLedgerDetails($details,$customerInvoiceDetailsObj);
        $customerInvoiceDetailsObj = self::setAmountDetails($details,$customerInvoiceDetailsObj,$receiptVoucher);
        $customerInvoiceDetailsObj = self::setCompanyDetails($receiptVoucher,$customerInvoiceDetailsObj);
        $validation = self::validateTotalAmount($details,$customerInvoiceDetailsObj);
        if($validation['status'] === 'success') {
            $receiptVoucher->details()->create($customerInvoiceDetailsObj->toArray());
        }else {
            return $validation;
        }

    }

    private static function setCompanyDetails($receiptVoucher,$customerInvoiceDetailsObj):CustomerReceivePaymentDetail {
        $customerInvoiceDetailsObj->companySystemID = $receiptVoucher->companySystemID;
        $customerInvoiceDetailsObj->companyID = $receiptVoucher->companyID;

        return $customerInvoiceDetailsObj;
    }

    private static function validateTotalAmount($details,$customerInvoiceDetailsObj):Array {
        $totalAmountReceived = CustomerReceivePaymentDetail::where('arAutoID',$customerInvoiceDetailsObj->arAutoID)->sum('receiveAmountTrans');
        if(($totalAmountReceived+$details['receiptAmount']) > $customerInvoiceDetailsObj->bookingAmountTrans) {
            return ['status'=>'fail','message' => 'Total received amount cannot be greater the invoice amount'];
        }else {
            return ['status'=>'success','message'=>'success'];
        }
    }

    private static function setCustomerInvoiceDetails($details,$customerInvoiceDetailsObj):CustomerReceivePaymentDetail {
        $invCode = $details['invoiceCode'];
        $invoice = CustomerInvoice::where('bookingInvCode',$invCode)->first();

        $customerInvoiceDetailsObj->bookingInvCodeSystem = $invoice->custInvoiceDirectAutoID;
        $customerInvoiceDetailsObj->bookingInvCode = $invoice->bookingInvCode;
        $customerInvoiceDetailsObj->bookingDate = $invoice->bookingDate;
        $customerInvoiceDetailsObj->bookingAmountRpt = $invoice->bookingAmountRpt + $invoice->VATAmountRpt;
        $customerInvoiceDetailsObj->bookingAmountLocal = $invoice->bookingAmountLocal + $invoice->VATAmountLocal;
        $customerInvoiceDetailsObj->bookingAmountTrans = $invoice->bookingAmountTrans + $invoice->VATAmount;
        $customerInvoiceDetailsObj->addedDocumentSystemID = $invoice->documentSystemiD;
        $customerInvoiceDetailsObj->addedDocumentID = $invoice->documentID;
        $customerInvoiceDetailsObj->custTransactionCurrencyID = $invoice->custTransactionCurrencyID;
        $customerInvoiceDetailsObj->custTransactionCurrencyER = $invoice->custTransactionCurrencyER;
        $customerInvoiceDetailsObj->companyReportingCurrencyID = $invoice->companyReportingCurrencyID;
        $customerInvoiceDetailsObj->companyReportingER = $invoice->companyReportingER;
        $customerInvoiceDetailsObj->localCurrencyID = $invoice->localCurrencyID;
        $customerInvoiceDetailsObj->localCurrencyER = $invoice->localCurrencyER;

        return $customerInvoiceDetailsObj;
    }

    private static function setAmountDetails($details,$customerInvoiceDetailsObj,$receiptVoucher):CustomerReceivePaymentDetail {

        $receivedAmountConversion = \Helper::convertAmountToLocalRpt($receiptVoucher->documentSystemID, $customerInvoiceDetailsObj->custReceivePaymentAutoID, $details['receiptAmount']);
        $customerInvoiceDetailsObj->receiveAmountRpt = \Helper::roundValue($receivedAmountConversion['reportingAmount']);
        $customerInvoiceDetailsObj->receiveAmountLocal = \Helper::roundValue($receivedAmountConversion['localAmount']);
        $customerInvoiceDetailsObj->receiveAmountTrans = $details['receiptAmount'];
        $totalAmountReceived = CustomerReceivePaymentDetail::where('arAutoID',$customerInvoiceDetailsObj->arAutoID)->sum('receiveAmountTrans');
        $customerInvoiceDetailsObj->custbalanceAmount = ($customerInvoiceDetailsObj->bookingAmountTrans - ($totalAmountReceived + $customerInvoiceDetailsObj->receiveAmountTrans));
        return $customerInvoiceDetailsObj;
    }

    private static function setAccountLedgerDetails($details,$customerInvoiceDetailsObj):CustomerReceivePaymentDetail {
        $invCode = $details['invoiceCode'];
        $invoice = CustomerInvoice::where('bookingInvCode',$invCode)->first();
        $accountReceivableLedgerDetails = AccountsReceivableLedger::where('documentCodeSystem',$invoice->custInvoiceDirectAutoID)->first();
        $customerInvoiceDetailsObj->arAutoID = $accountReceivableLedgerDetails->arAutoID;
        $customerInvoiceDetailsObj->custReceiveCurrencyID = $accountReceivableLedgerDetails->custTransCurrencyID;
        $customerInvoiceDetailsObj->custReceiveCurrencyER = $accountReceivableLedgerDetails->custTransER;
        return $customerInvoiceDetailsObj;
    }
}

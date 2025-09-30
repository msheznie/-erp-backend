<?php

namespace App\Services\AccountPayableLedger\Report;

use App\Exports\AccountsPayable\InvoiceToPayment\InvoiceToPaymentDetails;
use App\Services\Currency\CurrencyService;
use App\helper\Helper;

class InvoiceToPaymentReportService
{


    public function getInvoiceToPaymentExportToExcelData($output, $decimalPlaces): Array
    {
        $data = array();
        if ($output) {
            if(empty($data)) {
                $objInvoiceToPaymentDetailsHeader = new InvoiceToPaymentDetails();
                array_push($data,collect($objInvoiceToPaymentDetailsHeader->getHeader())->toArray());
            }
            foreach ($output as $val) {
                $objInvoiceToPaymentDetails = new InvoiceToPaymentDetails();
                $objInvoiceToPaymentDetails->setDocumentCode($val->documentCode);
                $objInvoiceToPaymentDetails->setSupplierName($val->supplierName);
                $objInvoiceToPaymentDetails->setSupplierInvoiceNo($val->supplierInvoiceNo);
                $objInvoiceToPaymentDetails->setSupplierInvoiceDate(Helper::dateFormat($val->supplierInvoiceDate));
                $objInvoiceToPaymentDetails->setCurrencyCode($val->CurrencyCode);
                $objInvoiceToPaymentDetails->setTotalAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->rptAmount, $decimalPlaces)));
                $objInvoiceToPaymentDetails->setConfirmedDate(Helper::dateFormat($val->confirmedDate));
                $objInvoiceToPaymentDetails->setFinalApprovedDate(Helper::dateFormat($val->approvedDate));
                $objInvoiceToPaymentDetails->setPostedDate(Helper::dateFormat($val->postedDate));
                $objInvoiceToPaymentDetails->setPaymentVoucherNo($val->BPVcode);
                $objInvoiceToPaymentDetails->setPaidAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->paidRPTAmount, $decimalPlaces)));
                $objInvoiceToPaymentDetails->setChequeNo($val->BPVchequeNo);
                $objInvoiceToPaymentDetails->setChequeDate(Helper::dateFormat($val->BPVchequeDate));
                $objInvoiceToPaymentDetails->setChequePrintedBy($val->chequePrintedByEmpName);
                $objInvoiceToPaymentDetails->setChequePrintedDate(Helper::dateFormat($val->chequePrintedDateTime));
                $objInvoiceToPaymentDetails->setPaymentClearedDate(Helper::dateFormat($val->trsClearedDate));

                array_push($data,collect($objInvoiceToPaymentDetails)->toArray());
            }
        }
        return $data;
    }


}

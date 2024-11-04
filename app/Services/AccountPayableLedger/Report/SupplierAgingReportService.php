<?php

namespace App\Services\AccountPayableLedger\Report;

use App\Exports\AccountsPayable\SupplierAging\SupplierAgingDetailAdvanceReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingDetailReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingSummaryAdvanceReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingSummaryReport;
use App\Services\Currency\CurrencyService;

class SupplierAgingReportService
{
    public function getSupplierAgingExportToExcelData($output, $typeAging): Array {
        $data = array();

        if ($output['data'] && $output['aging']) {
            if(empty($data)) {
                $objSupplierAgingDetailHeader = new SupplierAgingDetailReport();
                array_push($data,collect($objSupplierAgingDetailHeader->getHeader($typeAging, $output['aging']))->toArray());
            }
            foreach ($output['data'] as $index => $val) {
                $objSupplierAgingDetail = new SupplierAgingDetailReport();
                $objSupplierAgingDetail->setCompanyID($val->companyID);
                $objSupplierAgingDetail->setCompanyName($val->CompanyName);
                $objSupplierAgingDetail->setDocumentDate($val->documentDate);
                $objSupplierAgingDetail->setDocumentCode($val->documentCode);
                $objSupplierAgingDetail->setAccount($val->glCode . "-" . $val->AccountDescription);
                $objSupplierAgingDetail->setSupplierCode($val->SupplierCode);
                $objSupplierAgingDetail->setSupplierName($val->suppliername);
                $objSupplierAgingDetail->setInvoiceNumber($val->invoiceNumber);
                $objSupplierAgingDetail->setInvoiceDate($val->invoiceDate);
                $objSupplierAgingDetail->setCurrency($val->documentCurrency);
                $objSupplierAgingDetail->setAgingDays($val->ageDays);



                array_push($data,collect($objSupplierAgingDetail)->toArray());

            }
            foreach ($output['data'] as $index => $val) {
                $lineTotal = 0;

                foreach ($output['aging'] as $val2) {
                    $data[$index + 1][$val2] = $val->$val2;
                    $lineTotal += $val->$val2;
                }


                $data[$index + 1]['Advance/UnAllocated Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($val->unAllocatedAmount, $val->balanceDecimalPlaces));

                $data[$index + 1]['Total'] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal + $val->unAllocatedAmount, $val->balanceDecimalPlaces));
            }
        }



        return $data;

    }



    public function getSupplierAgingSummaryExportToExcelData($output, $typeAging): Array
    {
        $data = array();
        if ($output['data'] && $output['aging']) {
            if(empty($data)) {
                $objSupplierAgingDetailHeader = new SupplierAgingSummaryReport();
                array_push($data,collect($objSupplierAgingDetailHeader->getHeader($typeAging, $output['aging']))->toArray());
            }
            foreach ($output['data'] as $val) {
                $objSupplierAgingDetail = new SupplierAgingSummaryReport();
                $objSupplierAgingDetail->setCompanyID($val->companyID);
                $objSupplierAgingDetail->setCompanyName($val->CompanyName);
                $objSupplierAgingDetail->setAccount($val->glCode . "-" . $val->AccountDescription);
                $objSupplierAgingDetail->setSupplierCode($val->SupplierCode);
                $objSupplierAgingDetail->setSupplierName($val->suppliername);
                $objSupplierAgingDetail->setCreditPeriod($val->creditPeriod);
                $objSupplierAgingDetail->setCurrency($val->documentCurrency);
                $objSupplierAgingDetail->setAgingDays($val->ageDays);

                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }

            foreach ($output['data'] as $index => $val) {
                $lineTotal = 0;

                foreach ($output['aging'] as $val2) {
                    $data[$index + 1][$val2] = $val->$val2;
                    $lineTotal += $val->$val2;
                }


                $data[$index + 1]['Advance/UnAllocated Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($val->unAllocatedAmount,$val->balanceDecimalPlaces));

                $data[$index + 1]['Total'] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal + $val->unAllocatedAmount,$val->balanceDecimalPlaces));
            }
        }

        return $data;
    }

    public function getSupplierAgingDetailAdvanceExportToExcelData($output, $typeAging): Array {
        $data = array();
        if ($output['data'] && $output['aging']) {
            if(empty($data)) {
                $objSupplierAgingDetailAdvanceReportHeader = new SupplierAgingDetailAdvanceReport();
                array_push($data,collect($objSupplierAgingDetailAdvanceReportHeader->getHeader($typeAging, $output['aging']))->toArray());
            }
            foreach ($output['data'] as $val) {
                $objSupplierAgingDetail = new SupplierAgingDetailAdvanceReport();
                $objSupplierAgingDetail->setCompanyID($val->companyID);
                $objSupplierAgingDetail->setCompanyName($val->CompanyName);
                $objSupplierAgingDetail->setDocumentDate($val->documentDate);
                $objSupplierAgingDetail->setDocumentCode($val->documentCode);
                $objSupplierAgingDetail->setAccount($val->glCode . "-" . $val->AccountDescription);
                $objSupplierAgingDetail->setNarrtion($val->documentNarration);
                $objSupplierAgingDetail->setSupplierCode($val->SupplierCode);
                $objSupplierAgingDetail->setSupplierName($val->suppliername);
                $objSupplierAgingDetail->setInvoiceNumber($val->invoiceNumber);
                $objSupplierAgingDetail->setInvoiceDate($val->invoiceDate);
                $objSupplierAgingDetail->setCurrency($val->documentCurrency);
                $objSupplierAgingDetail->setAgingDays($val->ageDays);

                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }

            foreach ($output['data'] as $index => $val) {

                $lineTotal = 0;

                foreach ($output['aging'] as $val2) {
                    $data[$index + 1][$val2] = $val->$val2;
                    $lineTotal += $val->$val2;
                }

                $data[$index + 1]['Advance/UnAllocated Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal,$val->balanceDecimalPlaces));

            }
        }

        return $data;
    }

    public function getSupplierAgingSummaryAdvanceExportToExcelData($output, $typeAging): Array {
        $data = array();
        if ($output['data'] && $output['aging']) {
            if(empty($data)) {
                $objSupplierAgingDetailHeader = new SupplierAgingSummaryAdvanceReport();
                array_push($data,collect($objSupplierAgingDetailHeader->getHeader($typeAging, $output['aging']))->toArray());
            }
            foreach ($output['data'] as $val) {
                $objSupplierAgingDetail = new SupplierAgingSummaryAdvanceReport();
                $objSupplierAgingDetail->setCompanyID($val->companyID);
                $objSupplierAgingDetail->setCompanyName($val->CompanyName);
                $objSupplierAgingDetail->setAccount($val->glCode . "-" . $val->AccountDescription);
                $objSupplierAgingDetail->setSupplierCode($val->SupplierCode);
                $objSupplierAgingDetail->setSupplierName($val->suppliername);
                $objSupplierAgingDetail->setCreditPeriod($val->creditPeriod);
                $objSupplierAgingDetail->setCurrency($val->documentCurrency);
                $objSupplierAgingDetail->setAgingDays($val->ageDays);

                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }

            foreach ($output['data'] as $index => $val) {
                $lineTotal = 0;

                foreach ($output['aging'] as $val2) {
                    $data[$index + 1][$val2] = $val->$val2;
                    $lineTotal += $val->$val2;
                }

                $data[$index + 1]['Total'] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal,$val->balanceDecimalPlaces));
            }
        }
        return $data;
    }
}

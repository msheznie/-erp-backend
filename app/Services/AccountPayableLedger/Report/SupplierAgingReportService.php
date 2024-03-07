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

        if ($output['data']) {
            $x = 0;
            if(empty($data)) {
                $objSupplierAgingDetailHeader = new SupplierAgingDetailReport();
                array_push($data,collect($objSupplierAgingDetailHeader->getHeader($typeAging))->toArray());
            }
            foreach ($output['data'] as $val) {
                $lineTotal = 0;
                $column1 = $output['aging'][0];
                $column2 = $output['aging'][1];
                $column3 = $output['aging'][2];
                $column4 = $output['aging'][3];
                $column5 = $output['aging'][4];
                $lineTotal = ((float) $val->$column1 + (float) $val->$column2 + (float) $val->$column3 + (float) $val->$column4 + (float) $val->$column5);
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
                $objSupplierAgingDetail->setColumn1(CurrencyService::convertNumberFormatToNumber(number_format($val->$column1,2)));
                $objSupplierAgingDetail->setColumn2(CurrencyService::convertNumberFormatToNumber(number_format($val->$column2,2)));
                $objSupplierAgingDetail->setColumn3(CurrencyService::convertNumberFormatToNumber(number_format($val->$column3,2)));
                $objSupplierAgingDetail->setColumn4(CurrencyService::convertNumberFormatToNumber(number_format($val->$column4,2)));
                $objSupplierAgingDetail->setColumn5(CurrencyService::convertNumberFormatToNumber(number_format($val->$column5,2)));
                $objSupplierAgingDetail->setAdvanceAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->unAllocatedAmount,$val->balanceDecimalPlaces)));
                $objSupplierAgingDetail->setTotal(CurrencyService::convertNumberFormatToNumber(number_format($lineTotal + $val->unAllocatedAmount,$val->balanceDecimalPlaces)));
                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }
        }

        return $data;

    }

    public function getSupplierAgingSummaryExportToExcelData($output, $typeAging): Array
    {
        $data = array();
        if ($output['data']) {
            if(empty($data)) {
                $objSupplierAgingDetailHeader = new SupplierAgingSummaryReport();
                array_push($data,collect($objSupplierAgingDetailHeader->getHeader($typeAging))->toArray());
            }
            foreach ($output['data'] as $val) {
                $lineTotal = 0;
                $column1 = $output['aging'][0];
                $column2 = $output['aging'][1];
                $column3 = $output['aging'][2];
                $column4 = $output['aging'][3];
                $column5 = $output['aging'][4];
                $lineTotal = ((float) $val->$column1 + (float) $val->$column2 + (float) $val->$column3 + (float) $val->$column4 + (float) $val->$column5);
                $objSupplierAgingDetail = new SupplierAgingSummaryReport();
                $objSupplierAgingDetail->setCompanyID($val->companyID);
                $objSupplierAgingDetail->setCompanyName($val->CompanyName);
                $objSupplierAgingDetail->setAccount($val->glCode . "-" . $val->AccountDescription);
                $objSupplierAgingDetail->setSupplierCode($val->SupplierCode);
                $objSupplierAgingDetail->setSupplierName($val->suppliername);
                $objSupplierAgingDetail->setCreditPeriod($val->creditPeriod);
                $objSupplierAgingDetail->setCurrency($val->documentCurrency);
                $objSupplierAgingDetail->setAgingDays($val->ageDays);
                $objSupplierAgingDetail->setColumn1(CurrencyService::convertNumberFormatToNumber(number_format($val->$column1,2)));
                $objSupplierAgingDetail->setColumn2(CurrencyService::convertNumberFormatToNumber(number_format($val->$column2,2)));
                $objSupplierAgingDetail->setColumn3(CurrencyService::convertNumberFormatToNumber(number_format($val->$column3,2)));
                $objSupplierAgingDetail->setColumn4(CurrencyService::convertNumberFormatToNumber(number_format($val->$column4,2)));
                $objSupplierAgingDetail->setColumn5(CurrencyService::convertNumberFormatToNumber(number_format($val->$column5,2)));
                $objSupplierAgingDetail->setAdvanceAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->unAllocatedAmount,$val->balanceDecimalPlaces)));
                $objSupplierAgingDetail->setTotal(CurrencyService::convertNumberFormatToNumber(number_format($lineTotal + $val->unAllocatedAmount,$val->balanceDecimalPlaces)));
                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }
        }

        return $data;
    }

    public function getSupplierAgingDetailAdvanceExportToExcelData($output, $typeAging): Array {
        $data = array();
        if ($output['data']) {
            $x = 0;
            if(empty($data)) {
                $objSupplierAgingDetailAdvanceReportHeader = new SupplierAgingDetailAdvanceReport();
                array_push($data,collect($objSupplierAgingDetailAdvanceReportHeader->getHeader($typeAging))->toArray());
            }
            $lineTotal = 0;
            foreach ($output['data'] as $val) {
                $column1 = $output['aging'][0];
                $column2 = $output['aging'][1];
                $column3 = $output['aging'][2];
                $column4 = $output['aging'][3];
                $column5 = $output['aging'][4];
                $lineTotal = ((float) $val->$column1 + (float) $val->$column2 + (float) $val->$column3 + (float) $val->$column4 + (float) $val->$column5);
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
                $objSupplierAgingDetail->setColumn1(CurrencyService::convertNumberFormatToNumber(number_format($val->$column1,2)));
                $objSupplierAgingDetail->setColumn2(CurrencyService::convertNumberFormatToNumber(number_format($val->$column2,2)));
                $objSupplierAgingDetail->setColumn3(CurrencyService::convertNumberFormatToNumber(number_format($val->$column3,2)));
                $objSupplierAgingDetail->setColumn4(CurrencyService::convertNumberFormatToNumber(number_format($val->$column4,2)));
                $objSupplierAgingDetail->setColumn5(CurrencyService::convertNumberFormatToNumber(number_format($val->$column5,2)));
                $objSupplierAgingDetail->setAdvanceAmount(CurrencyService::convertNumberFormatToNumber(number_format($lineTotal,$val->balanceDecimalPlaces)));
                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }
        }

        return $data;
    }

    public function getSupplierAgingSummaryAdvanceExportToExcelData($output, $typeAging): Array {
        $data = array();
        if ($output['data']) {
            if(empty($data)) {
                $objSupplierAgingDetailHeader = new SupplierAgingSummaryAdvanceReport();
                array_push($data,collect($objSupplierAgingDetailHeader->getHeader($typeAging))->toArray());
            }
            foreach ($output['data'] as $val) {
                $lineTotal = 0;
                $column1 = $output['aging'][0];
                $column2 = $output['aging'][1];
                $column3 = $output['aging'][2];
                $column4 = $output['aging'][3];
                $column5 = $output['aging'][4];
                $lineTotal = ((float) $val->$column1 + (float) $val->$column2 + (float) $val->$column3 + (float) $val->$column4 + (float) $val->$column5);
                $objSupplierAgingDetail = new SupplierAgingSummaryAdvanceReport();
                $objSupplierAgingDetail->setCompanyID($val->companyID);
                $objSupplierAgingDetail->setCompanyName($val->CompanyName);
                $objSupplierAgingDetail->setAccount($val->glCode . "-" . $val->AccountDescription);
                $objSupplierAgingDetail->setSupplierCode($val->SupplierCode);
                $objSupplierAgingDetail->setSupplierName($val->suppliername);
                $objSupplierAgingDetail->setCreditPeriod($val->creditPeriod);
                $objSupplierAgingDetail->setCurrency($val->documentCurrency);
                $objSupplierAgingDetail->setAgingDays($val->ageDays);
                $objSupplierAgingDetail->setColumn1(CurrencyService::convertNumberFormatToNumber(number_format($val->$column1,2)));
                $objSupplierAgingDetail->setColumn2(CurrencyService::convertNumberFormatToNumber(number_format($val->$column2,2)));
                $objSupplierAgingDetail->setColumn3(CurrencyService::convertNumberFormatToNumber(number_format($val->$column3,2)));
                $objSupplierAgingDetail->setColumn4(CurrencyService::convertNumberFormatToNumber(number_format($val->$column4,2)));
                $objSupplierAgingDetail->setColumn5(CurrencyService::convertNumberFormatToNumber(number_format($val->$column5,2)));
                $objSupplierAgingDetail->setTotal(CurrencyService::convertNumberFormatToNumber(number_format($lineTotal,$val->balanceDecimalPlaces)));
                array_push($data,collect($objSupplierAgingDetail)->toArray());
            }
        }
        return $data;
    }
}

<?php

namespace App\Services\GeneralLedger\Reports;

use App\Exports\GeneralLedger\VAT\InputOutputVatReport;
use App\Services\Currency\CurrencyService;

class VatReportService
{

    private function setHeaderObj() {
        $headerRow = new InputOutputVatReport();
        $headerRow->setDocumentType('Document Type');
        $headerRow->setDocumentCode('Document Code');
        $headerRow->setReferenceNo("Reference No");
        $headerRow->setDocumentDate('Document Date',false);
        $headerRow->setPartyName("Party Name");
        $headerRow->setCountry("Country");
        $headerRow->setVatIn("VATIN");
        $headerRow->setApporvedBy("Approved By");
        $headerRow->setDocumentTotalAmount("Document Total Amount");
        $headerRow->setDocumentVatAmount("Document Vat Amount");
        $headerRow->setVatMainCategory("VAT Main Category");
        $headerRow->setVatType('VAT Type');
        $headerRow->setIsClaimed("Is Claimed");

        return $headerRow;
    }
    public function getExcelExportData($output) : Array{
        $dataArray = [];
        $rptAmountTotal = 0;
        $documentReportingAmountTotal = 0;
        $localAmountTotal = 0;
        $documentLocalAmountTotal = 0;
        $localDecimalPlaces = 2;
        if(empty($dataArray)) {
            $headerObj = $this->setHeaderObj();
            array_push($dataArray,collect($headerObj)->toArray());
        }

        foreach ($output as $val) {
            $inputOutputVatReport = new InputOutputVatReport();
            $inputOutputVatReport->setDocumentType($val->document_master->documentID);
            $inputOutputVatReport->setDocumentCode($val->documentCode);
            if($val->documentSystemID == 11){
                if($val->supplier_invoice) {
                    $inputOutputVatReport->setReferenceNo($val->supplier_invoice->supplierInvoiceNo);
                }
            }
            else if($val->documentSystemID == 3){
                if($val->grv) {
                    $inputOutputVatReport->setReferenceNo($val->grv->grvDoRefNo);

                }
            }
            else if($val->documentSystemID == 24){
                if($val->purchase_return) {
                    $inputOutputVatReport->setReferenceNo($val->purchase_return->purchaseReturnRefNo);

                }
            }

            $inputOutputVatReport->setDocumentDate($val->documentDate,true);
            if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                $inputOutputVatReport->setPartyName(
                    $val->supplier->supplierName ?? ($val->supplier['supplierName'] ?? null)
                );
            }elseif (in_array($val->documentSystemID, [19, 20, 21, 71, 87])){
                if(isset($val->bank_receipt['payeeTypeID']) && $val->bank_receipt['payeeTypeID'] == 2){
                    if(isset($val->employee['empFullName'])) {
                        $inputOutputVatReport->setPartyName($val->employee['empFullName']);
                    }
                } else {
                    if(isset($val->customer->CustomerName)) {
                        $inputOutputVatReport->setPartyName($val->customer->CustomerName);
                    }
                }
            }

            if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                $inputOutputVatReport->setCountry(
                    $val->supplier->country->countryName ?? ($val->supplier['country']['countryName'] ?? null)
                );
            }elseif (in_array($val->documentSystemID, [19, 20, 21, 71, 87])){
                if(isset($val->bank_receipt['payeeTypeID']) && $val->bank_receipt['payeeTypeID'] == 2) {
                }
                else {
                    if(isset($val->customer->country->countryName)) {
                        $inputOutputVatReport->setCountry($val->customer->country->countryName);
                    }
                }
            }

            if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                isset($val->supplier->vatNumber) ? $inputOutputVatReport->setVatIn($val->supplier->vatNumber): $inputOutputVatReport->setVatIn('');
            }elseif (in_array($val->documentSystemID, [19, 20, 21, 71, 87])){
                isset($val->customer->vatNumber) ? $inputOutputVatReport->setVatIn($val->customer->vatNumber): $inputOutputVatReport->setVatIn('');
            }

            isset($val->final_approved_by->empName)? $inputOutputVatReport->setApporvedBy($val->final_approved_by->empName) : '';

            $localDecimalPlaces = isset($val->localcurrency->DecimalPlaces) ? $val->localcurrency->DecimalPlaces : 3;
            $rptDecimalPlaces = isset($val->rptcurrency->DecimalPlaces) ? $val->rptcurrency->DecimalPlaces : 2;

            $inputOutputVatReport->setDocumentTotalAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmount,$localDecimalPlaces)));
            $inputOutputVatReport->setDocumentVatAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->localAmount,$localDecimalPlaces)));
            if(isset($input['currencyID'])&&$input['currencyID']==2){
                $inputOutputVatReport->setDocumentTotalAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->documentReportingAmount,$rptDecimalPlaces)));
                $inputOutputVatReport->setDocumentVatAmount(CurrencyService::convertNumberFormatToNumber(number_format($val->rptAmount,$rptDecimalPlaces)));
            }

            isset($val->main_category->mainCategoryDescription) ? $inputOutputVatReport->setVatMainCategory($val->main_category->mainCategoryDescription) : $inputOutputVatReport->setVatMainCategory('-');
            isset($val->sub_category->subCategoryDescription) ? $inputOutputVatReport->setVatType($val->sub_category->subCategoryDescription) : $inputOutputVatReport->setVatType('-');
            ($val->isClaimed == 1) ? $inputOutputVatReport->setIsClaimed('Claimed') : $inputOutputVatReport->setIsClaimed("Not Claimed");
            $rptAmountTotal += $val->rptAmount;
            $documentReportingAmountTotal += $val->documentReportingAmount;
            $localAmountTotal += $val->localAmount;
            $documentLocalAmountTotal += $val->documentLocalAmount;
            array_push($dataArray,collect($inputOutputVatReport)->toArray());
        }

        $lastRowData = new InputOutputVatReport();
        $lastRowData->setApporvedBy('Total');
        $lastRowData->setDocumentTotalAmount(CurrencyService::convertNumberFormatToNumber(number_format($documentLocalAmountTotal,$localDecimalPlaces)));
        $lastRowData->setDocumentVatAmount(CurrencyService::convertNumberFormatToNumber(number_format($localAmountTotal,$localDecimalPlaces)));


        if(isset($input['currencyID'])&&$input['currencyID']==2){
            $lastRowData->setDocumentTotalAmount(CurrencyService::convertNumberFormatToNumber(number_format($documentReportingAmountTotal,$rptDecimalPlaces)));
            $lastRowData->setDocumentVatAmount(CurrencyService::convertNumberFormatToNumber(number_format($rptAmountTotal,$rptDecimalPlaces)));
        }

        array_push($dataArray,collect($lastRowData)->toArray());

        return $dataArray;
    }


}

<?php

namespace App\Services\AccountPayableLedger\Report;

use App\Exports\AccountsPayable\SupplierAging\SupplierAgingDetailAdvanceReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingDetailReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingSummaryAdvanceReport;
use App\Exports\AccountsPayable\SupplierAging\SupplierAgingSummaryReport;
use App\helper\Helper;
use App\Services\Currency\CurrencyService;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SupplierAgingReportService
{
    /**
     * Build rows as numeric-indexed arrays in the same column order as getHeader() on the export class.
     * Associative rows from collect($object)->toArray() are not safe: key order can differ from the header row,
     * which breaks PhpSpreadsheet fromArray alignment.
     */
    public function getSupplierAgingExportToExcelData($output, $typeAging): array
    {
        $outputData = array_values($output['data'] ?? []);
        $data = [];
        if (!$outputData || empty($output['aging'])) {
            return $data;
        }

        $export = new SupplierAgingDetailReport();
        $data[] = array_values($export->getHeader($typeAging, $output['aging']));

        foreach ($outputData as $val) {
            $lineTotal = 0;
            foreach ($output['aging'] as $agingKey) {
                $lineTotal += (float) ($val->$agingKey ?? 0);
            }

            $advanceUnallocatedAmount = CurrencyService::convertNumberFormatToNumber(
                number_format($val->advanceUnallocatedAmount, $val->balanceDecimalPlaces)
            );
            $debitNoteUnallocatedAmount = CurrencyService::convertNumberFormatToNumber(
                number_format($val->debitNoteUnallocatedAmount, $val->balanceDecimalPlaces)
            );
            $totalAmount = CurrencyService::convertNumberFormatToNumber(
                number_format($lineTotal + $val->unAllocatedAmount, $val->balanceDecimalPlaces)
            );

            $docDate = ($val->documentDate) ? ExcelDate::PHPToExcel(Helper::dateFormat($val->documentDate)) : null;
            $invDate = ($val->invoiceDate) ? ExcelDate::PHPToExcel(Helper::dateFormat($val->invoiceDate)) : null;

            if ($typeAging == 1) {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $docDate,
                    $val->documentCode,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->supplierGroupName,
                    $val->invoiceNumber,
                    $invDate,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            } else {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $docDate,
                    $val->documentCode,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->invoiceNumber,
                    $invDate,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            }

            foreach ($output['aging'] as $agingKey) {
                $row[] = $val->$agingKey;
            }
            $row[] = $advanceUnallocatedAmount;
            $row[] = $debitNoteUnallocatedAmount;
            $row[] = $totalAmount;
            $data[] = $row;
        }

        return $this->appendGrandTotalNumericRow($data, $output['aging'], 3);
    }

    public function getSupplierAgingSummaryExportToExcelData($output, $typeAging): array
    {
        $data = [];
        if (empty($output['data']) || empty($output['aging'])) {
            return $data;
        }

        $export = new SupplierAgingSummaryReport();
        $data[] = array_values($export->getHeader($typeAging, $output['aging']));

        foreach ($output['data'] as $val) {
            $lineTotal = 0;
            foreach ($output['aging'] as $agingKey) {
                $lineTotal += (float) ($val->$agingKey ?? 0);
            }

            if ($typeAging == 1) {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->supplierGroupName,
                    $val->creditPeriod,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            } else {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->creditPeriod,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            }

            foreach ($output['aging'] as $agingKey) {
                $row[] = $val->$agingKey;
            }
            $row[] = CurrencyService::convertNumberFormatToNumber(number_format($val->advanceUnallocatedAmount, $val->balanceDecimalPlaces));
            $row[] = CurrencyService::convertNumberFormatToNumber(number_format($val->debitNoteUnallocatedAmount, $val->balanceDecimalPlaces));
            $row[] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal + $val->unAllocatedAmount, $val->balanceDecimalPlaces));
            $data[] = $row;
        }

        return $this->appendGrandTotalNumericRow($data, $output['aging'], 3);
    }

    public function getSupplierAgingDetailAdvanceExportToExcelData($output, $typeAging): array
    {
        $data = [];
        if (empty($output['data']) || empty($output['aging'])) {
            return $data;
        }

        $export = new SupplierAgingDetailAdvanceReport();
        $data[] = array_values($export->getHeader($typeAging, $output['aging']));

        foreach ($output['data'] as $val) {
            $lineTotal = 0;
            foreach ($output['aging'] as $agingKey) {
                $lineTotal += (float) ($val->$agingKey ?? 0);
            }

            $docDate = ($val->documentDate) ? ExcelDate::PHPToExcel(Helper::dateFormat($val->documentDate)) : null;
            $invDate = ($val->invoiceDate) ? ExcelDate::PHPToExcel(Helper::dateFormat($val->invoiceDate)) : null;

            if ($typeAging == 1) {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $docDate,
                    $val->documentCode,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->documentNarration,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->supplierGroupName,
                    $val->invoiceNumber,
                    $invDate,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            } else {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $docDate,
                    $val->documentCode,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->documentNarration,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->invoiceNumber,
                    $invDate,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            }

            foreach ($output['aging'] as $agingKey) {
                $row[] = $val->$agingKey;
            }
            $row[] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal, $val->balanceDecimalPlaces));
            $data[] = $row;
        }

        return $this->appendGrandTotalNumericRow($data, $output['aging'], 1);
    }

    public function getSupplierAgingSummaryAdvanceExportToExcelData($output, $typeAging): array
    {
        $data = [];
        if (empty($output['data']) || empty($output['aging'])) {
            return $data;
        }

        $export = new SupplierAgingSummaryAdvanceReport();
        $data[] = array_values($export->getHeader($typeAging, $output['aging']));

        foreach ($output['data'] as $val) {
            $lineTotal = 0;
            foreach ($output['aging'] as $agingKey) {
                $lineTotal += (float) ($val->$agingKey ?? 0);
            }

            if ($typeAging == 1) {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->supplierGroupName,
                    $val->creditPeriod,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            } else {
                $row = [
                    $val->companyID,
                    $val->CompanyName,
                    $val->glCode . '-' . $val->AccountDescription,
                    $val->SupplierCode,
                    $val->suppliername,
                    $val->creditPeriod,
                    $val->documentCurrency,
                    $val->ageDays,
                ];
            }

            foreach ($output['aging'] as $agingKey) {
                $row[] = $val->$agingKey;
            }
            $row[] = CurrencyService::convertNumberFormatToNumber(number_format($lineTotal, $val->balanceDecimalPlaces));
            $data[] = $row;
        }

        return $this->appendGrandTotalNumericRow($data, $output['aging'], 1);
    }

    /**
     * Appends a Grand Total row (label + column sums). No Excel styling — same formatting as normal data rows.
     *
     * @param  array<int, array<int, mixed>>  $data  Row 0 = header; data rows numeric
     * @param  array<int, string>  $agingKeys
     * @param  int  $trailingColumnCount  1 = single Total (SASA/SADA); 3 = Advance + Debit + Total (SAD/SAS)
     */
    private function appendGrandTotalNumericRow(array $data, array $agingKeys, int $trailingColumnCount): array
    {
        if (count($data) < 2) {
            return $data;
        }

        $agingKeys = array_values($agingKeys);
        $agingCount = count($agingKeys);
        $width = count($data[0]);
        $fixedColumnCount = $width - $agingCount - $trailingColumnCount;
        if ($fixedColumnCount < 1) {
            return $data;
        }

        $row = array_fill(0, $width, '');
        $labelColumnIndex = max(0, $fixedColumnCount - 1);
        $row[$labelColumnIndex] = __('custom.grand_total');

        for ($j = 0; $j < $agingCount; $j++) {
            $sum = 0.0;
            for ($r = 1, $n = count($data); $r < $n; $r++) {
                $sum += (float) ($data[$r][$fixedColumnCount + $j] ?? 0);
            }
            $row[$fixedColumnCount + $j] = $sum;
        }

        for ($t = 0; $t < $trailingColumnCount; $t++) {
            $colIdx = $fixedColumnCount + $agingCount + $t;
            $sum = 0.0;
            for ($r = 1, $n = count($data); $r < $n; $r++) {
                $sum += (float) ($data[$r][$colIdx] ?? 0);
            }
            $row[$colIdx] = $sum;
        }

        $data[] = $row;

        return $data;
    }
}

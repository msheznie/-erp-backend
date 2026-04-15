<?php

namespace App\Services\AccountPayableLedger\Report;

class AccountsPayableReportSortingService
{
    public function sortAgingDataRows($rows, $request)
    {
        if (empty($rows) || !isset($request->sortKey) || !isset($request->sortDir)) {
            return $rows;
        }
        $sortMap = [
            'documentDate' => 'documentDate',
            'documentCode' => 'documentCode',
            'account' => 'glCode',
            'SupplierCode' => 'SupplierCode',
            'suppliername' => 'suppliername',
            'supplierGroupName' => 'supplierGroupName',
            'invoiceNumber' => 'invoiceNumber',
            'invoiceDate' => 'invoiceDate',
            'documentCurrency' => 'documentCurrency',
            'ageDays' => 'ageDays',
            'advanceUnallocatedAmount' => 'advanceUnallocatedAmount',
            'debitNoteUnallocatedAmount' => 'debitNoteUnallocatedAmount',
        ];
        if (!isset($sortMap[$request->sortKey])) {
            return $rows;
        }
        $column = $sortMap[$request->sortKey];
        $direction = strtolower($request->sortDir) === 'desc' ? -1 : 1;
        usort($rows, function ($a, $b) use ($column, $direction) {
            $aValue = isset($a->{$column}) ? $a->{$column} : null;
            $bValue = isset($b->{$column}) ? $b->{$column} : null;
            if ($aValue == $bValue) {
                return 0;
            }

            return ($aValue < $bValue ? -1 : 1) * $direction;
        });

        return $rows;
    }

    public function getSupplierLedgerOrderByClause($request): string
    {
        $allowedSortColumns = [
            'documentCode' => 'documentCode',
            'documentDate' => 'documentDate',
            'account' => 'AccountDescription',
            'invoiceNumber' => 'invoiceNumber',
            'invoiceDate' => 'invoiceDate',
            'currency' => 'documentCurrency',
            'documentAmount' => 'invoiceAmount',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        $sortDir = isset($request->sortDir) ? strtolower($request->sortDir) : 'asc';
        if (!isset($allowedSortColumns[$sortKey])) {
            return 'ORDER BY documentDate ASC, suppliername ASC';
        }
        if ($sortDir !== 'desc') {
            $sortDir = 'asc';
        }

        return 'ORDER BY ' . $allowedSortColumns[$sortKey] . ' ' . strtoupper($sortDir) . ', suppliername ASC';
    }

    /**
     * Whitelist ORDER BY for Supplier Statement (report type SS). Uses outer SELECT aliases.
     */
    public function getSupplierStatementOrderByClause($request): string
    {
        $allowedSortColumns = [
            'documentID' => 'documentID',
            'documentCode' => 'documentCode',
            'documentDate' => 'documentDate',
            'account' => 'glCode',
            'narration' => 'documentNarration',
            'invoiceNumber' => 'invoiceNumber',
            'invoiceDate' => 'invoiceDate',
            'currency' => 'documentCurrency',
            'ageDays' => 'ageDays',
            'documentAmount' => 'invoiceAmount',
            'balanceAmount' => 'balanceAmount',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        $sortDir = isset($request->sortDir) ? strtolower($request->sortDir) : 'asc';
        if (isset($allowedSortColumns[$sortKey])) {
            if ($sortDir !== 'desc') {
                $sortDir = 'asc';
            }

            return 'ORDER BY ' . $allowedSortColumns[$sortKey] . ' ' . strtoupper($sortDir) . ', suppliername ASC';
        }
        $path = isset($request->fromPath) ? $request->fromPath : null;
        if ($path == 'pdf') {
            return 'ORDER BY companySystemID ASC';
        }

        return 'ORDER BY documentDate ASC, suppliername ASC';
    }

    public function getSupplierBalanceReconcileOrderByClause($request): string
    {
        $allowedSortColumns = [
            'companyID' => 'companyID',
            'documentDate' => 'documentDate',
            'documentCode' => 'documentCode',
            'SupplierCode' => 'SupplierCode',
            'suppliername' => 'suppliername',
            'invoiceNumber' => 'invoiceNumber',
            'invoiceDate' => 'invoiceDate',
            'documentCurrency' => 'documentCurrency',
            'invoiceAmountDoc' => 'invoiceAmountDoc',
            'balanceAmountDoc' => 'balanceAmountDoc',
            'documentCurrencyLoc' => 'documentCurrencyLoc',
            'invoiceAmountLoc' => 'invoiceAmountLoc',
            'balanceAmountLoc' => 'balanceAmountLoc',
            'documentCurrencyRpt' => 'documentCurrencyRpt',
            'invoiceAmountRpt' => 'invoiceAmountRpt',
            'balanceAmountRpt' => 'balanceAmountRpt',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        $sortDir = isset($request->sortDir) ? strtolower($request->sortDir) : 'asc';
        if (!isset($allowedSortColumns[$sortKey])) {
            return 'ORDER BY documentDate ASC';
        }
        if ($sortDir !== 'desc') {
            $sortDir = 'asc';
        }

        return 'ORDER BY ' . $allowedSortColumns[$sortKey] . ' ' . strtoupper($sortDir);
    }
}

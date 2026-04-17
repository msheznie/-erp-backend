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

    public function getSupplierBalanceSummaryOrderByClause($request): string
    {
        $allowedSortColumns = [
            'SupplierCode' => 'SupplierCode',
            'supplierName' => 'supplierName',
            'supplierGroupName' => 'supplierGroupName',
            'account' => 'AccountDescription',
            'currency' => 'documentCurrency',
            'amount' => 'documentAmount',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        $sortDir = isset($request->sortDir) ? strtolower($request->sortDir) : 'asc';
        if (!isset($allowedSortColumns[$sortKey])) {
            return 'ORDER BY supplierName ASC';
        }
        if ($sortDir !== 'desc') {
            $sortDir = 'asc';
        }

        return 'ORDER BY ' . $allowedSortColumns[$sortKey] . ' ' . strtoupper($sortDir) . ', supplierName ASC';
    }

    public function getApPaymentsByYearOrderByClause($request): string
    {
        $reportTypeID = isset($request->reportTypeID) ? $request->reportTypeID : null;
        $reportSD = isset($request->reportSD) ? $request->reportSD : null;

        if ($reportTypeID === 'APLWS') {
            return $this->getApPaymentsListsStatusOrderByClause($request);
        }

        if ($reportTypeID === 'APPSY') {
            if ($reportSD === 'detail') {
                return $this->getApPaymentSuppliersByYearDetailOrderByClause($request);
            }
            return $this->getApPaymentSuppliersByYearSummaryOrderByClause($request);
        }

        if ($reportTypeID === 'APDPY') {
            if ($reportSD === 'detail') {
                return $this->getApDirectPaymentsByYearDetailOrderByClause($request);
            }
            return $this->getApDirectPaymentsByYearSummaryOrderByClause($request);
        }

        if ($reportTypeID === 'APAPY') {
            return $this->getApAllPaymentsByYearOrderByClause($request);
        }

        return '';
    }

    private function normalizeSortDir($dir): string
    {
        $sortDir = is_string($dir) ? strtolower($dir) : 'asc';
        return $sortDir === 'desc' ? 'DESC' : 'ASC';
    }

    private function getApPaymentSuppliersByYearSummaryOrderByClause($request): string
    {
        $allowed = [
            'supplierCode' => 'supplierCode',
            'supplierName' => 'supplierName',
            'supplierGroupName' => 'supplierGroupName',
            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'Aug' => 'Aug',
            'Sept' => 'Sept',
            'Oct' => 'Oct',
            'Nov' => 'Nov',
            'Dece' => 'Dece',
            'Total' => 'Total',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return 'ORDER BY Total DESC';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir) . ', Total DESC';
    }

    private function getApPaymentSuppliersByYearDetailOrderByClause($request): string
    {
        $allowed = [
            'supplierCode' => 'supplierCode',
            'supplierName' => 'supplierName',
            'supplierGroupName' => 'supplierGroupName',
            'PaymentType' => 'PaymentType',
            'documentDate' => 'documentDate',
            'documentCode' => 'documentCode',
            'documentLocalAmount' => 'documentLocalAmount',
            'documentRptAmount' => 'documentRptAmount',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return 'ORDER BY paymentsBySupplierSummary.documentRptAmount DESC';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir);
    }

    private function getApDirectPaymentsByYearSummaryOrderByClause($request): string
    {
        $allowed = [
            'glCode' => 'glCode',
            'AccountDescription' => 'AccountDescription',
            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'Aug' => 'Aug',
            'Sept' => 'Sept',
            'Oct' => 'Oct',
            'Nov' => 'Nov',
            'Dece' => 'Dece',
            'Total' => 'Total',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return 'ORDER BY Total DESC';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir) . ', Total DESC';
    }

    private function getApDirectPaymentsByYearDetailOrderByClause($request): string
    {
        $allowed = [
            'glCode' => 'glCode',
            'AccountDescription' => 'AccountDescription',
            'documentCode' => 'documentCode',
            'documentDate' => 'documentDate',
            'documentLocalAmount' => 'documentLocalAmount',
            'documentRptAmount' => 'documentRptAmount',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return '';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir);
    }

    private function getApAllPaymentsByYearOrderByClause($request): string
    {
        $allowed = [
            'docCode' => 'docCode',
            'docDes' => 'docDes',
            'docType' => 'docType',
            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'Aug' => 'Aug',
            'Sept' => 'Sept',
            'Oct' => 'Oct',
            'Nov' => 'Nov',
            'Dece' => 'Dece',
            'Total' => 'Total',
        ];
        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return 'ORDER BY Total DESC';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir) . ', Total DESC';
    }

    private function getApPaymentsListsStatusOrderByClause($request): string
    {
        $currencyID = isset($request->currencyID) ? $request->currencyID : null;
        $amountColumn = 'payAmountCompRpt';
        if ($currencyID == 1) {
            $amountColumn = 'payAmountSuppTrans';
        } else if ($currencyID == 2) {
            $amountColumn = 'payAmountCompLocal';
        }

        $allowed = [
            'BPVcode' => 'BPVcode',
            'BPVdate' => 'BPVdate',
            'confirmedDate' => 'confirmedDate',
            'PayeeName' => 'PayeeName',
            'creditPeriod' => 'creditPeriod',
            'bankName' => 'bankName',
            'AccountNo' => 'AccountNo',
            'BPVchequeNo' => 'BPVchequeNo',
            'ChequeDate' => 'ChequeDate',
            'chequePrintedByEmpName' => 'chequePrintedByEmpName',
            'chequePrintedDate' => 'chequePrintedDate',
            'amount' => $amountColumn,
            'ApprovalStatus' => 'ApprovalStatus',
        ];

        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return 'ORDER BY BPVdate DESC, BPVcode DESC';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir) . ', BPVdate DESC';
    }

    public function getInvoiceToPaymentOrderByClause($request): string
    {
        $allowed = [
            'documentCode' => 'erp_generalledger.documentCode',
            'supplierName' => 'suppliermaster.supplierName',
            'supplierInvoiceNo' => 'erp_bookinvsuppmaster.supplierInvoiceNo',
            'supplierInvoiceDate' => 'erp_bookinvsuppmaster.supplierInvoiceDate',
            'CurrencyCode' => 'currencymaster.CurrencyCode',
            'rptAmount' => 'rptAmount',
            'confirmedDate' => 'erp_bookinvsuppmaster.confirmedDate',
            'approvedDate' => 'erp_bookinvsuppmaster.approvedDate',
            'postedDate' => 'postedDate',
            'BPVcode' => 'paymentinfor.BPVcode',
            'paidRPTAmount' => 'paymentinfor.paidRPTAmount',
            'BPVchequeNo' => 'erp_paysupplierinvoicemaster.BPVchequeNo',
            'BPVchequeDate' => 'erp_paysupplierinvoicemaster.BPVchequeDate',
            'chequePrintedByEmpName' => 'erp_paysupplierinvoicemaster.chequePrintedByEmpName',
            'chequePrintedDateTime' => 'erp_paysupplierinvoicemaster.chequePrintedDateTime',
            'trsClearedDate' => 'erp_bankledger.trsClearedDate',
        ];

        $sortKey = isset($request->sortKey) ? $request->sortKey : null;
        if (!isset($allowed[$sortKey])) {
            return 'ORDER BY erp_generalledger.documentDate ASC';
        }

        return 'ORDER BY ' . $allowed[$sortKey] . ' ' . $this->normalizeSortDir($request->sortDir) . ', erp_generalledger.documentDate ASC';
    }

    /**
     * In-memory sorter for APUGRV (unbilled) reports. We sort the flat rows BEFORE
     * controller groups (UGRVD/UGRVAD) so UI + export are consistent.
     */
    public function sortUnbilledRows($rows, $request)
    {
        if (empty($rows) || !isset($request->sortKey) || !isset($request->sortDir)) {
            return $rows;
        }

        $sortKey = $request->sortKey;
        $dir = strtolower($request->sortDir) === 'desc' ? -1 : 1;

        $allowed = [
            // Common (UGRVD/UGRVS/UGRVAD/UGRVAS)
            'companyID' => 'companyID',
            'documentCode' => 'documentCode',
            'documentDate' => 'documentDate',
            'pendingBSICode' => 'pendingBSICode',
            'supplierCode' => 'supplierCode',
            'supplierName' => 'supplierName',
            'documentLocalAmount' => 'documentLocalAmount',
            'matchedLocalAmount' => 'matchedLocalAmount',
            'balanceLocalAmount' => 'balanceLocalAmount',
            'documentRptAmount' => 'documentRptAmount',
            'matchedRptAmount' => 'matchedRptAmount',
            'balanceRptAmount' => 'balanceRptAmount',
            'ageDays' => 'ageDays',
            'case1' => 'case1',
            'case2' => 'case2',
            'case3' => 'case3',
            'case4' => 'case4',
            'case5' => 'case5',
            'case6' => 'case6',
            'case7' => 'case7',
            'case8' => 'case8',
            'case9' => 'case9',
            'case10' => 'case10',

            // ULD (logistics)
            'purchaseOrderCode' => 'purchaseOrderCode',
            'grvPrimaryCode' => 'grvPrimaryCode',
            'grvDate' => 'grvDate',
            'primarySupplierCode' => 'primarySupplierCode',
            'LogisticAmountTransaction' => 'LogisticAmountTransaction',
            'LogisticAmountRpt' => 'LogisticAmountRpt',
            'PaidAmountTrans' => 'PaidAmountTrans',
            'PaidAmountRpt' => 'PaidAmountRpt',
            'BalanceTransAmount' => 'BalanceTransAmount',
            'BalanceRptAmount' => 'BalanceRptAmount',
        ];

        if (!isset($allowed[$sortKey])) {
            return $rows;
        }

        $column = $allowed[$sortKey];

        usort($rows, function ($a, $b) use ($column, $dir) {
            $aValue = isset($a->{$column}) ? $a->{$column} : null;
            $bValue = isset($b->{$column}) ? $b->{$column} : null;

            if ($aValue == $bValue) {
                return 0;
            }

            return ($aValue < $bValue ? -1 : 1) * $dir;
        });

        return $rows;
    }

    /**
     * In-memory sort for Advance Payment Request report (server returns full row set; DataTables paginates in memory).
     *
     * @param  \Illuminate\Support\Collection|array  $rows
     * @return \Illuminate\Support\Collection|array
     */
    public function sortAdvancePaymentRequestRows($rows, array $input)
    {
        if (empty($rows)) {
            return $rows;
        }

        $sortKey = isset($input['sortKey']) ? $input['sortKey'] : null;
        $sortDir = isset($input['sortDir']) ? strtolower((string) $input['sortDir']) : 'desc';
        if ($sortKey === null || $sortKey === '') {
            return $rows;
        }
        if (! in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        $reportType = $this->normalizeAdvancePaymentReportTypeId($input);
        $currencyId = $this->normalizeAdvancePaymentCurrencyId($input);

        $field = $this->resolveAdvancePaymentSortField($reportType, $sortKey, $currencyId);
        if ($field === null) {
            return $rows;
        }

        $list = $rows instanceof \Illuminate\Support\Collection ? $rows->all() : (array) $rows;
        $list = array_values($list);

        $dir = $sortDir === 'asc' ? 1 : -1;

        $that = $this;
        usort($list, function ($a, $b) use ($that, $field, $dir) {
            $va = $that->getAdvancePaymentSortComparableValue($a, $field);
            $vb = $that->getAdvancePaymentSortComparableValue($b, $field);
            if ($va == $vb) {
                return 0;
            }
            $cmp = ($va < $vb) ? -1 : 1;

            return $cmp * $dir;
        });

        return collect($list);
    }

    private function normalizeAdvancePaymentReportTypeId(array $input): string
    {
        $r = isset($input['reportTypeID']) ? $input['reportTypeID'] : 'APRD';
        if (is_array($r)) {
            return isset($r[0]) ? (string) $r[0] : 'APRD';
        }

        return (string) $r;
    }

    private function normalizeAdvancePaymentCurrencyId(array $input): int
    {
        $c = isset($input['currencyID']) ? $input['currencyID'] : 1;
        if (is_array($c)) {
            return isset($c[0]) ? (int) $c[0] : 1;
        }

        return (int) $c;
    }

    /**
     * Map UI sort key to row property name (whitelist per report type).
     *
     * @return string|null
     */
    private function resolveAdvancePaymentSortField(string $reportType, string $sortKey, int $currencyId)
    {
        $poAmt = $this->advancePaymentPoAmountFieldName($currencyId);
        $reqAmt = $this->advancePaymentReqAmountFieldName($currencyId);
        $poCur = $this->advancePaymentPoCurrencyFieldName($currencyId);
        $reqCur = $this->advancePaymentReqCurrencyFieldName($currencyId);

        $common = [
            'primarySupplierCode' => 'primarySupplierCode',
            'supplierName' => 'supplierName',
            'poCode' => 'poCode',
            'reqDate' => 'reqDate',
            'poCurrency' => $poCur,
            'poAmount' => $poAmt,
            'reqCurrency' => $reqCur,
            'reqAmount' => $reqAmt,
            'status' => 'status',
        ];

        if ($reportType === 'APRA') {
            $map = array_merge($common, [
                'case1' => 'case1',
                'case2' => 'case2',
                'case3' => 'case3',
                'case4' => 'case4',
                'case5' => 'case5',
                'case6' => 'case6',
                'case7' => 'case7',
                'case8' => 'case8',
                'case9' => 'case9',
                'case10' => 'case10',
            ]);
        } else {
            $map = array_merge($common, [
                'narration' => 'narration',
            ]);
        }

        return isset($map[$sortKey]) ? $map[$sortKey] : null;
    }

    private function advancePaymentPoAmountFieldName(int $currencyId): string
    {
        if ($currencyId === 2) {
            return 'poTotalLocalCurrency';
        }
        if ($currencyId === 3) {
            return 'poTotalComRptCurrency';
        }

        return 'poTotalSupplierTransactionCurrency';
    }

    private function advancePaymentReqAmountFieldName(int $currencyId): string
    {
        if ($currencyId === 2) {
            return 'reqAmountInPOLocalCur';
        }
        if ($currencyId === 3) {
            return 'reqAmountInPORptCur';
        }

        return 'reqAmount';
    }

    private function advancePaymentReqCurrencyFieldName(int $currencyId): string
    {
        if ($currencyId === 2) {
            return 'localCurrencyCode';
        }
        if ($currencyId === 3) {
            return 'rptCurrencyCode';
        }

        return 'trnsCurrencyCode';
    }

    private function advancePaymentPoCurrencyFieldName(int $currencyId): string
    {
        if ($currencyId === 2) {
            return 'localCurrencyCode';
        }
        if ($currencyId === 3) {
            return 'rptCurrencyCode';
        }

        return 'potrnsCurrencyCode';
    }

    /**
     * @param  object|array  $row
     * @return mixed
     */
    private function getAdvancePaymentSortComparableValue($row, string $field)
    {
        $val = is_object($row) ? (isset($row->{$field}) ? $row->{$field} : null) : (isset($row[$field]) ? $row[$field] : null);

        if ($field === 'supplierName' && $val !== null && $val !== '') {
            return strtolower(strip_tags((string) $val));
        }

        if ($field === 'reqDate' && $val !== null && $val !== '') {
            $ts = strtotime((string) $val);

            return $ts !== false ? $ts : 0;
        }

        if (in_array($field, [
            'poTotalSupplierTransactionCurrency', 'poTotalLocalCurrency', 'poTotalComRptCurrency',
            'reqAmount', 'reqAmountInPOLocalCur', 'reqAmountInPORptCur',
            'case1', 'case2', 'case3', 'case4', 'case5', 'case6', 'case7', 'case8', 'case9', 'case10',
            'status',
        ], true)) {
            return is_numeric($val) ? (float) $val : 0.0;
        }

        if ($val === null) {
            return '';
        }

        return strtolower((string) $val);
    }
}

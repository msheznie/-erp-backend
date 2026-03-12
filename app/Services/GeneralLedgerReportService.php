<?php

namespace App\Services;

/**
 * Service for General Ledger report operations (sorting, etc.).
 */
class GeneralLedgerReportService
{
    /**
     * Sort general ledger collection by sortKey and sortDir. Puts null/blank values last.
     *
     * sortKey: glCode, accountDescription, documentType, documentNumber, documentDate, documentNarration,
     *          segment, contract, supplierCustomer, confirmedBy, confirmedDate, approvedBy, approvedDate,
     *          debitLocal, creditLocal, balanceLocal, debitRpt, creditRpt, balanceRpt
     *
     * @param array|\Illuminate\Support\Collection $collection
     * @param string $sortKey
     * @param string $sortDir 'asc' or 'desc'
     * @return array
     */
    public function sortGeneralLedgerCollection($collection, $sortKey, $sortDir)
    {
        $arr = is_array($collection) ? $collection : collect($collection)->values()->all();
        $dir = strtolower($sortDir) === 'desc' ? -1 : 1;

        $getValue = function ($row) use ($sortKey) {
            if (!is_object($row)) {
                return null;
            }
            switch ($sortKey) {
                case 'glCode':
                    return $row->glCode ?? '';
                case 'accountDescription':
                    return $row->AccountDescription ?? '';
                case 'documentType':
                    return $row->documentID ?? '';
                case 'documentNumber':
                    return $row->documentCode ?? '';
                case 'documentDate':
                    return isset($row->documentDate) && $row->documentDate ? $row->documentDate : null;
                case 'documentNarration':
                    return $row->documentNarration ?? '';
                case 'segment':
                    return $row->serviceLineCode ?? '';
                case 'contract':
                    return $row->clientContractID ?? '';
                case 'supplierCustomer':
                    return $row->isCustomer ?? '';
                case 'confirmedBy':
                    return $row->confirmedBy ?? '';
                case 'confirmedDate':
                    return isset($row->documentConfirmedDate) && $row->documentConfirmedDate ? $row->documentConfirmedDate : null;
                case 'approvedBy':
                    return $row->approvedBy ?? '';
                case 'approvedDate':
                    return isset($row->documentFinalApprovedDate) && $row->documentFinalApprovedDate ? $row->documentFinalApprovedDate : null;
                case 'debitLocal':
                    return isset($row->localDebit) ? (float)$row->localDebit : null;
                case 'creditLocal':
                    return isset($row->localCredit) ? (float)$row->localCredit : null;
                case 'balanceLocal':
                    if (isset($row->documentNarration) && $row->documentNarration === 'Retained Earnings system calculated') {
                        return isset($row->localCredit, $row->localDebit) ? (float)($row->localCredit - $row->localDebit) : null;
                    }
                    return isset($row->doucmentLocalBalanceAmount) ? (float)$row->doucmentLocalBalanceAmount : null;
                case 'debitRpt':
                    return isset($row->rptDebit) ? (float)$row->rptDebit : null;
                case 'creditRpt':
                    return isset($row->rptCredit) ? (float)$row->rptCredit : null;
                case 'balanceRpt':
                    if (isset($row->documentNarration) && $row->documentNarration === 'Retained Earnings system calculated') {
                        return isset($row->rptCredit, $row->rptDebit) ? (float)($row->rptCredit - $row->rptDebit) : null;
                    }
                    return isset($row->documentRptBalanceAmount) ? (float)$row->documentRptBalanceAmount : null;
                default:
                    return null;
            }
        };

        $isBlank = function ($v, $key) {
            if ($key === 'documentDate' || $key === 'confirmedDate' || $key === 'approvedDate') {
                return $v === null || $v === '';
            }
            if (in_array($key, ['debitLocal', 'creditLocal', 'balanceLocal', 'debitRpt', 'creditRpt', 'balanceRpt'])) {
                return $v === null;
            }
            return $v === null || $v === '';
        };

        $isDateKey = in_array($sortKey, ['documentDate', 'confirmedDate', 'approvedDate']);
        $isNumericKey = in_array($sortKey, ['debitLocal', 'creditLocal', 'balanceLocal', 'debitRpt', 'creditRpt', 'balanceRpt']);

        usort($arr, function ($a, $b) use ($getValue, $isBlank, $sortKey, $dir, $isDateKey, $isNumericKey) {
            $va = $getValue($a);
            $vb = $getValue($b);
            $aBlank = $isBlank($va, $sortKey);
            $bBlank = $isBlank($vb, $sortKey);
            if ($aBlank && $bBlank) {
                return 0;
            }
            if ($aBlank) {
                return 1;
            }
            if ($bBlank) {
                return -1;
            }
            $cmp = 0;
            if ($isNumericKey) {
                $cmp = (float)$va <=> (float)$vb;
            } elseif ($isDateKey) {
                $ta = strtotime($va);
                $tb = strtotime($vb);
                $cmp = ($ta <=> $tb);
            } else {
                $cmp = strcasecmp((string)$va, (string)$vb);
            }
            return $cmp * $dir;
        });

        return $arr;
    }
}

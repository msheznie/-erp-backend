<?php

namespace App\Services\AccountPayableLedger\Report;

use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvAgingSummaryReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvDetailsReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvDetailsSummaryReport;
use App\Exports\AccountsPayable\UnbilledGRV\UnbilledGrvLogisticDetails;
use App\Exports\AccountsPayable\InvoiceToPayment\InvoiceToPaymentDetails;
use App\Services\Currency\CurrencyService;
use App\helper\Helper;

class UnbilledGrvReportService
{

    public function getUnbilledGrvExportToExcelData($output): Array {
        $data = array();
        if ($output) {
            if(empty($data)) {
                $objUnbilledGrvDetailsReportHeader = new UnbilledGrvDetailsReport();
                array_push($data,collect($objUnbilledGrvDetailsReportHeader->getHeader())->toArray());
            }
            foreach ($output as $val) {
                $objUnbilledGrvDetailsReport = new UnbilledGrvDetailsReport();
                $objUnbilledGrvDetailsReport->setCompanyId($val->companyID);
                $objUnbilledGrvDetailsReport->setSupplierCode($val->supplierCode);
                $objUnbilledGrvDetailsReport->setSupplierName($val->supplierName);
                $objUnbilledGrvDetailsReport->setDocNumber($val->documentCode);
                $objUnbilledGrvDetailsReport->setDocDate($val->documentDate);
                $objUnbilledGrvDetailsReport->setPendingSICode($val->pendingBSICode);
                $objUnbilledGrvDetailsReport->setDocValueLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmount, CurrencyService::getCurrencyDecimalPlace($val->documentLocalCurrencyID))));
                $objUnbilledGrvDetailsReport->setMatachedValueLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->matchedLocalAmount, CurrencyService::getCurrencyDecimalPlace($val->documentLocalCurrencyID))));
                $objUnbilledGrvDetailsReport->setBalanceLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceLocalAmount, CurrencyService::getCurrencyDecimalPlace($val->documentLocalCurrencyID))));
                $objUnbilledGrvDetailsReport->setDocValueReportingCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->documentRptAmount, CurrencyService::getCurrencyDecimalPlace($val->documentRptCurrencyID))));
                $objUnbilledGrvDetailsReport->setMatchedValueReportingCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->matchedRptAmount, CurrencyService::getCurrencyDecimalPlace($val->documentRptCurrencyID))));
                $objUnbilledGrvDetailsReport->setBalanceReportingCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceRptAmount, CurrencyService::getCurrencyDecimalPlace($val->documentRptCurrencyID))));
                array_push($data,collect($objUnbilledGrvDetailsReport)->toArray());
            }
        }

        return $data;
    }

    public function getUnbilledGrvSummaryExportToExcelData($output): Array {
        $data = array();
        if(empty($data)) {
            $objUnbilledGrvDetailsReportHeader = new UnbilledGrvDetailsSummaryReport();
            array_push($data,collect($objUnbilledGrvDetailsReportHeader->getHeader())->toArray());
        }

        if ($output) {
            foreach ($output as $val) {
                $objUnbilledGrvDetailsReport = new UnbilledGrvDetailsSummaryReport();
                $objUnbilledGrvDetailsReport->setCompanyId($val->companyID);
                $objUnbilledGrvDetailsReport->setSupplierCode($val->supplierCode);
                $objUnbilledGrvDetailsReport->setSupplierName($val->supplierName);
                $objUnbilledGrvDetailsReport->setDocValueLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmount, CurrencyService::getCurrencyDecimalPlace($val->documentLocalCurrencyID))));
                $objUnbilledGrvDetailsReport->setMatachedValueLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->matchedLocalAmount, CurrencyService::getCurrencyDecimalPlace($val->documentLocalCurrencyID))));
                $objUnbilledGrvDetailsReport->setBalanceLocalCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceLocalAmount, CurrencyService::getCurrencyDecimalPlace($val->documentLocalCurrencyID))));
                $objUnbilledGrvDetailsReport->setDocValueReportingCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->documentRptAmount, CurrencyService::getCurrencyDecimalPlace($val->documentRptCurrencyID))));
                $objUnbilledGrvDetailsReport->setMatchedValueReportingCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->matchedRptAmount, CurrencyService::getCurrencyDecimalPlace($val->documentRptCurrencyID))));
                $objUnbilledGrvDetailsReport->setBalanceReportingCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceRptAmount, CurrencyService::getCurrencyDecimalPlace($val->documentRptCurrencyID))));
                array_push($data,collect($objUnbilledGrvDetailsReport)->toArray());
            }
        }
        return $data;
    }


    public function getUnbilledGrvAgingDetailExportToExcelData($output,$request): Array {
        $data = array();
        $decimal = 2;
        if ($output) {
            $x = 0;
            foreach ($output as $val) {
                $data[$x][ trans('custom.company_id')] = $val->companyID;
                $data[$x][trans('custom.supplier_code')] = $val->supplierCode;
                $data[$x][ trans('custom.supplier_name')] = $val->supplierName;
                $data[$x][trans('custom.doc_number')] = $val->documentCode;
                $data[$x][trans('custom.doc_date')] = ($val->documentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Helper::dateFormat($val->documentDate)) : null;
                if ($request->currencyID == 2) {
                    $decimal = 3;
                    $data[$x][trans('custom.doc_value_local_currency')] = CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmount, CurrencyService::getCurrencyDecimalPlace($request->currencyID)));
                    $data[$x][trans('custom.matched_value_local_currency')] = CurrencyService::convertNumberFormatToNumber(number_format($val->matchedLocalAmount, CurrencyService::getCurrencyDecimalPlace($request->currencyID)));
                    $data[$x][trans('custom.balance_local_currency')] = CurrencyService::convertNumberFormatToNumber(number_format($val->balanceLocalAmount, CurrencyService::getCurrencyDecimalPlace($request->currencyID)));
                } else {
                    $data[$x][trans('custom.doc_value_reporting_currency')] = CurrencyService::convertNumberFormatToNumber(number_format($val->documentRptAmount, CurrencyService::getCurrencyDecimalPlace($request->currencyID)));
                    $data[$x][trans('custom.matched_value_reporting_currency')] = CurrencyService::convertNumberFormatToNumber(number_format($val->matchedRptAmount, CurrencyService::getCurrencyDecimalPlace($request->currencyID)));
                    $data[$x][trans('custom.balance_reporting_currency')] = CurrencyService::convertNumberFormatToNumber(number_format($val->balanceRptAmount, CurrencyService::getCurrencyDecimalPlace($request->currencyID)));
                }

                $data[$x]['<=30'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case1, $decimal));
                $data[$x]['31 to 60'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case2, $decimal));
                $data[$x]['61 to 90'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case3, $decimal));
                $data[$x]['91 to 120'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case4, $decimal));
                $data[$x]['121 to 150'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case5, $decimal));
                $data[$x]['151 to 180'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case6, $decimal));
                $data[$x]['181 to 210'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case7, $decimal));
                $data[$x]['211 to 240'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case8, $decimal));
                $data[$x]['241 to 365'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case9, $decimal));
                $data[$x]['Over 365'] = CurrencyService::convertNumberFormatToNumber(number_format($val->case10, $decimal));

                $x++;
            }
        }
        return $data;
    }


    public function getUnbilledGrvAgingSummaryExportToExcelData($output,$request): Array {
        $data = array();
        $decimal = CurrencyService::getCurrencyDecimalPlace($request->currencyID);
        if ($output) {
            $x = 0;
            if(empty($data)) {
                $objUnbilledGrvAgingSummaryReportHeader = new UnbilledGrvAgingSummaryReport();
                array_push($data,collect($objUnbilledGrvAgingSummaryReportHeader->getHeader($request->currencyID))->toArray());
            }
            foreach ($output as $val) {
                $objUnbilledGrvAgingSummaryReport = new UnbilledGrvAgingSummaryReport();
                $objUnbilledGrvAgingSummaryReport->setCompanyId($val->companyID);
                $objUnbilledGrvAgingSummaryReport->setSupplierCode($val->supplierCode);
                $objUnbilledGrvAgingSummaryReport->setSupplierName($val->supplierName);
                if ($request->currencyID == 2) {
                    $objUnbilledGrvAgingSummaryReport->setDocValueCurerncy(CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmount, $decimal)));
                    $objUnbilledGrvAgingSummaryReport->setMatchedValueCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->matchedLocalAmount, $decimal)));
                    $objUnbilledGrvAgingSummaryReport->setBalanceCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceLocalAmount, $decimal)));
                } else {
                    $objUnbilledGrvAgingSummaryReport->setDocValueCurerncy(CurrencyService::convertNumberFormatToNumber(number_format($val->documentRptAmount,$decimal)));
                    $objUnbilledGrvAgingSummaryReport->setMatchedValueCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->matchedRptAmount, $decimal)));
                    $objUnbilledGrvAgingSummaryReport->setBalanceCurrency(CurrencyService::convertNumberFormatToNumber(number_format($val->balanceRptAmount, $decimal)));
                }

                $objUnbilledGrvAgingSummaryReport->setLessThan30(CurrencyService::convertNumberFormatToNumber(number_format($val->case1, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn2(CurrencyService::convertNumberFormatToNumber(number_format($val->case2, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn3(CurrencyService::convertNumberFormatToNumber(number_format($val->case3, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn4(CurrencyService::convertNumberFormatToNumber(number_format($val->case4, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn5(CurrencyService::convertNumberFormatToNumber(number_format($val->case5, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn6(CurrencyService::convertNumberFormatToNumber(number_format($val->case6, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn7(CurrencyService::convertNumberFormatToNumber(number_format($val->case7, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn8(CurrencyService::convertNumberFormatToNumber(number_format($val->case8, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn9(CurrencyService::convertNumberFormatToNumber(number_format($val->case9, $decimal)));
                $objUnbilledGrvAgingSummaryReport->setColumn10(CurrencyService::convertNumberFormatToNumber(number_format($val->case10, $decimal)));

                array_push($data,collect($objUnbilledGrvAgingSummaryReport)->toArray());
            }
        }
        return $data;
    }

    public function getUnbilledGrvLogisticDetailExportToExcelData($output): Array {
        $data = array();
        if ($output) {
            if(empty($data)) {
                $objUnbilledGrvLogisticDetailsHeader = new UnbilledGrvLogisticDetails();
                array_push($data,collect($objUnbilledGrvLogisticDetailsHeader->getHeader())->toArray());
            }

            foreach ($output as $val) {
                $objUnbilledGrvLogisticDetails = new UnbilledGrvLogisticDetails();
                $objUnbilledGrvLogisticDetails->setCompanyId($val->companyID);
                $objUnbilledGrvLogisticDetails->setPoNumber($val->purchaseOrderCode);
                $objUnbilledGrvLogisticDetails->setGrv($val->grvPrimaryCode);
                $objUnbilledGrvLogisticDetails->setGrvDate($val->grvDate);
                $objUnbilledGrvLogisticDetails->setSupplierCode($val->primarySupplierCode);
                $objUnbilledGrvLogisticDetails->setSupplierName($val->supplierName);
                $objUnbilledGrvLogisticDetails->setTranscationCurrency($val->TransactionCurrencyCode);
                $objUnbilledGrvLogisticDetails->setLogisticAmountTrans(CurrencyService::convertNumberFormatToNumber(number_format($val->LogisticAmountTransaction, $val->TransactionCurrencyDecimalPlaces)));
                $objUnbilledGrvLogisticDetails->setRptCurrency($val->RptCurrencyCode);
                $objUnbilledGrvLogisticDetails->setLogisticAmountRpt(CurrencyService::convertNumberFormatToNumber(number_format($val->LogisticAmountRpt, $val->RptCurrencyDecimalPlaces)));
                $objUnbilledGrvLogisticDetails->setPaidAmountTrans(CurrencyService::convertNumberFormatToNumber(number_format($val->PaidAmountTrans, $val->TransactionCurrencyDecimalPlaces)));
                $objUnbilledGrvLogisticDetails->setPaidAmountRpt(CurrencyService::convertNumberFormatToNumber(number_format($val->PaidAmountRpt, $val->RptCurrencyDecimalPlaces)));
                $objUnbilledGrvLogisticDetails->setBalanceTrans(CurrencyService::convertNumberFormatToNumber(number_format($val->BalanceTransAmount, $val->TransactionCurrencyDecimalPlaces)));
                $objUnbilledGrvLogisticDetails->setBalanceRpt(CurrencyService::convertNumberFormatToNumber(number_format($val->BalanceRptAmount, $val->RptCurrencyDecimalPlaces)));

                array_push($data,collect($objUnbilledGrvLogisticDetails)->toArray());
            }
        }
        return $data;
    }

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

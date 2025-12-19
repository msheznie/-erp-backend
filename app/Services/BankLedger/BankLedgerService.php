<?php

namespace App\Services\BankLedger;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BankLedgerService
{
	public static function getBankLedgerData($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $accounts = (array)$request->accounts;

        $bankAccountAutoIDs = collect($accounts)->pluck('bankAccountAutoID')->toArray();

        //contracts
        $query = 'SELECT * 
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_bankledger.companySystemID,
                        erp_bankledger.companyID,
                        erp_bankledger.documentSystemID,
                        erp_bankledger.documentID,
                        erp_bankledger.documentSystemCode,
                        erp_bankledger.documentCode,
                        erp_bankledger.documentDate,
                        erp_bankledger.bankAccountID,
                        erp_bankledger.documentNarration,
                        erp_bankledger.payeeID,
                        erp_bankledger.localCurrencyID,
                        companymaster.CompanyName,
                    IF
                        ( erp_bankledger.documentSystemID IN (21, 110), (erp_bankledger.payAmountCompLocal * -1), 0 ) AS localDebit,
                    IF
                        ( erp_bankledger.documentSystemID = 4, erp_bankledger.payAmountCompLocal, 0 ) AS localCredit,
                        erp_bankledger.companyRptCurrencyID,
                    IF
                        ( erp_bankledger.documentSystemID IN (21, 110), (erp_bankledger.payAmountCompRpt * -1), 0 ) AS rptDebit,
                    IF
                        ( erp_bankledger.documentSystemID = 4, erp_bankledger.payAmountCompRpt, 0 ) AS rptCredit,
                    IF
                        ( erp_bankledger.documentSystemID IN (21, 110), customermaster.CustomerName, suppliermaster.supplierName ) AS partyName,
                        erp_bankaccount.bankName,
                        erp_bankaccount.AccountNo,
                        chartofaccounts.AccountDescription,
                    IF
                        ( erp_bankledger.documentSystemID IN (21, 110), (erp_bankledger.payAmountBank * -1), 0 ) AS bankDebit,
                    IF
                        ( erp_bankledger.documentSystemID = 4, erp_bankledger.payAmountBank, 0 ) AS bankCredit,
                        currencymaster.CurrencyCode as bankCurrency,
                        currencymaster.DecimalPlaces as bankCurrencyDecimal,
                    IF
                        ( erp_bankledger.documentSystemID = 21, confirmEmpBrv.empName, confirmEmpPv.empName ) AS confirmBy,
                    IF
                        ( erp_bankledger.documentSystemID = 21, approveEmpBrv.empName, approveEmpPv.empName ) AS approvedBy,
                    IF
                        ( erp_bankledger.documentSystemID = 21, erp_customerreceivepayment.confirmedDate, erp_paysupplierinvoicemaster.confirmedDate ) AS confirmDate,
                    IF
                        ( erp_bankledger.documentSystemID = 21, erp_customerreceivepayment.approvedDate, erp_paysupplierinvoicemaster.approvedDate ) AS approvedDate 
                    FROM
                        erp_bankledger
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_bankledger.payeeID
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_bankledger.payeeID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_bankledger.companySystemID 
                        LEFT JOIN erp_bankaccount ON erp_bankaccount.bankAccountAutoID = erp_bankledger.bankAccountID 
                        LEFT JOIN chartofaccounts ON erp_bankaccount.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        LEFT JOIN currencymaster ON currencymaster.currencyID = erp_bankledger.bankCurrency 
                        LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_bankledger.documentSystemCode
                        LEFT JOIN employees as approveEmpPv ON erp_paysupplierinvoicemaster.approvedByUserSystemID = approveEmpPv.employeeSystemID
                        LEFT JOIN employees as confirmEmpPv ON erp_paysupplierinvoicemaster.confirmedByEmpSystemID = confirmEmpPv.employeeSystemID
                        LEFT JOIN erp_customerreceivepayment ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_bankledger.documentSystemCode
                        LEFT JOIN employees as approveEmpBrv ON erp_customerreceivepayment.approvedByUserSystemID = approveEmpBrv.employeeSystemID
                        LEFT JOIN employees as confirmEmpBrv ON erp_customerreceivepayment.confirmedByEmpSystemID = confirmEmpBrv.employeeSystemID
                    WHERE
                        erp_bankledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_bankledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_bankledger.bankAccountID IN (' . join(',', $bankAccountAutoIDs) . ')
                        ) AS erp_qry_GL UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_bankledger.companySystemID,
                        erp_bankledger.companyID,
                        "" AS documentSystemID,
                        "" AS documentID,
                        "" AS documentSystemCode,
                        "" AS documentCode,
                        "" AS documentDate,
                        erp_bankledger.bankAccountID,
                        "Opening Balance" AS documentNarration,
                        "" AS payeeID,
                        erp_bankledger.localCurrencyID,
                        companymaster.CompanyName,
                        sum( IF ( erp_bankledger.documentSystemID IN (21, 110), (erp_bankledger.payAmountCompLocal * -1), 0 ) ) AS localDebit,
                        sum( IF ( erp_bankledger.documentSystemID = 4, erp_bankledger.payAmountCompLocal, 0 ) ) AS localCredit,
                        erp_bankledger.companyRptCurrencyID,
                        sum( IF ( erp_bankledger.documentSystemID IN (21, 110), (erp_bankledger.payAmountCompRpt * -1), 0 ) ) AS rptDebit,
                        sum( IF ( erp_bankledger.documentSystemID = 4, erp_bankledger.payAmountCompRpt, 0 ) ) AS rptCredit,
                        "" AS partyName,
                        erp_bankaccount.bankName,
                        erp_bankaccount.AccountNo,
                        chartofaccounts.AccountDescription,
                        sum( IF ( erp_bankledger.documentSystemID IN (21, 110), (erp_bankledger.payAmountBank * -1), 0 ) ) AS bankDebit,
                        sum( IF ( erp_bankledger.documentSystemID = 4, erp_bankledger.payAmountBank, 0 ) ) AS bankCredit,
                        currencymaster.CurrencyCode as bankCurrency,
                        currencymaster.DecimalPlaces as bankCurrencyDecimal,
                        "" AS confirmBy,
                        "" AS approvedBy,
                        "" AS confirmDate,
                        "" AS approvedDate 
                    FROM
                        erp_bankledger
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_bankledger.payeeID
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_bankledger.payeeID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_bankledger.companySystemID 
                        LEFT JOIN erp_bankaccount ON erp_bankaccount.bankAccountAutoID = erp_bankledger.bankAccountID
                        LEFT JOIN chartofaccounts ON erp_bankaccount.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                        LEFT JOIN currencymaster ON currencymaster.currencyID = erp_bankledger.bankCurrency
                        LEFT JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_bankledger.documentSystemCode
                        LEFT JOIN employees as approveEmpPv ON erp_paysupplierinvoicemaster.approvedByUserSystemID = approveEmpPv.employeeSystemID
                        LEFT JOIN employees as confirmEmpPv ON erp_paysupplierinvoicemaster.confirmedByEmpSystemID = confirmEmpPv.employeeSystemID
                        LEFT JOIN erp_customerreceivepayment ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_bankledger.documentSystemCode
                        LEFT JOIN employees as approveEmpBrv ON erp_customerreceivepayment.approvedByUserSystemID = approveEmpBrv.employeeSystemID
                        LEFT JOIN employees as confirmEmpBrv ON erp_customerreceivepayment.confirmedByEmpSystemID = confirmEmpBrv.employeeSystemID
                        WHERE
                        erp_bankledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND  erp_bankledger.bankAccountID IN (' . join(',', $bankAccountAutoIDs) . ')
                        AND DATE(erp_bankledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_bankledger.companySystemID,
                        erp_bankledger.bankAccountID
                        ) AS erp_qry_gl_bf 
                        ) AS GL_final 
                    ORDER BY
                        documentDate ASC';

        return  \DB::select($query);
    }

}
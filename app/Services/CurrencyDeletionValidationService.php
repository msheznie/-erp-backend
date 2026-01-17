<?php

namespace App\Services;

use App\Models\Company;
use App\Models\ConsoleJVMaster;
use App\Models\CreditNote;
use App\Models\CurrencyConversionDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\DeliveryOrder;
use App\Models\BookInvSuppMaster;
use App\Models\GRVMaster;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseReturn;
use App\Models\QuotationMaster;
use App\Models\RecurringVoucherSetup;
use App\Models\SalesReturn;

class CurrencyDeletionValidationService
{
    public function validate($currencyId)
    {
        if (Company::where('localCurrencyID', $currencyId)
                   ->orWhere('reportingCurrency', $currencyId)
                   ->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.cannot_delete_local_or_reporting_currency')
            ];
        }

        if (CurrencyConversionDetail::where('masterCurrencyID', $currencyId)
                                   ->orWhere('subCurrencyID', $currencyId)
                                   ->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_linked_with_conversions')
            ];
        }

        if (GRVMaster::where(function($query) use ($currencyId) {
            $query->where('supplierTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId)
                  ->orWhere('supplierDefaultCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (PurchaseReturn::where(function($query) use ($currencyId) {
            $query->where('supplierTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (PurchaseRequest::where(function($query) use ($currencyId) {
            $query->where('currency', $currencyId)
                  ->orWhere('supplierTransactionCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (ProcumentOrder::where(function($query) use ($currencyId) {
            $query->where('supplierTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId)
                  ->orWhere('supplierDefaultCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (BookInvSuppMaster::where(function($query) use ($currencyId) {
            $query->where('supplierTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (DebitNote::where(function($query) use ($currencyId) {
            $query->where('supplierTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (PaySupplierInvoiceMaster::where(function($query) use ($currencyId) {
            $query->where('supplierTransCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyRptCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (CustomerInvoiceDirect::where(function($query) use ($currencyId) {
            $query->where('custTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (CreditNote::where(function($query) use ($currencyId) {
            $query->where('customerCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (CustomerReceivePayment::where(function($query) use ($currencyId) {
            $query->where('custTransactionCurrencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('companyRptCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (QuotationMaster::where(function($query) use ($currencyId) {
            $query->where('customerCurrencyID', $currencyId)
                  ->orWhere('transactionCurrencyID', $currencyId)
                  ->orWhere('companyLocalCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (DeliveryOrder::where(function($query) use ($currencyId) {
            $query->where('transactionCurrencyID', $currencyId)
                  ->orWhere('companyLocalCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (SalesReturn::where(function($query) use ($currencyId) {
            $query->where('transactionCurrencyID', $currencyId)
                  ->orWhere('companyLocalCurrencyID', $currencyId)
                  ->orWhere('companyReportingCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (JvMaster::where(function($query) use ($currencyId) {
            $query->where('currencyID', $currencyId)
                  ->orWhere('rptCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (ConsoleJVMaster::where(function($query) use ($currencyId) {
            $query->where('currencyID', $currencyId)
                  ->orWhere('localCurrencyID', $currencyId)
                  ->orWhere('rptCurrencyID', $currencyId);
        })->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        if (RecurringVoucherSetup::where('currencyID', $currencyId)->exists()) {
            return [
                'success' => false,
                'message' => trans('custom.currency_has_transaction_entries')
            ];
        }

        return ['success' => true];
    }
}

<?php
 Route::group(['middleware' => 'print_lang'], function () {
    Route::get('getProcumentOrderPrintPDF', 'ProcumentOrderAPIController@getProcumentOrderPrintPDF')->name('Get procurement order print pdf');
    Route::get('goodReceiptVoucherPrintPDF', 'GRVMasterAPIController@goodReceiptVoucherPrintPDF');
    Route::get('printItemIssue', 'ItemIssueMasterAPIController@printItemIssue');
    Route::get('deliveryPrintItemIssue', 'ItemIssueMasterAPIController@deliveryPrintItemIssue');
    Route::get('printCustomerInvoice', 'CustomerInvoiceDirectAPIController@printCustomerInvoice')->name('Print customer invoice');
    Route::get('printReceiptVoucher', 'CustomerReceivePaymentAPIController@printReceiptVoucher')->name('Print receipt voucher');
    Route::get('printPaymentVoucher', 'PaySupplierInvoiceMasterAPIController@printPaymentVoucher')->name('Print payment voucher');
    Route::get('printPurchaseRequest', 'PurchaseRequestAPIController@printPurchaseRequest')->name('Print purchase request');
    Route::get('printMaterielRequest', 'MaterielRequestAPIController@printMaterielRequest')->name('Print materiel request');
    Route::get('printBudgetTransfer', 'BudgetTransferFormAPIController@printBudgetTransfer')->name('Print budget transfer');
    Route::get('printStockTransfer', 'StockTransferAPIController@printStockTransfer')->name('Print stock transfer');
    Route::get('printItemReturn', 'ItemReturnMasterAPIController@printItemReturn')->name('Print item return');
    Route::get('printStockReceive', 'StockReceiveAPIController@printStockReceive')->name('Print stock receive');
    Route::get('printPurchaseReturn', 'PurchaseReturnAPIController@printPurchaseReturn')->name('Print purchase return');
    Route::get('printExpenseClaim', 'ExpenseClaimAPIController@printExpenseClaim')->name('Print expense claim');
    Route::get('printDebitNote', 'DebitNoteAPIController@printDebitNote')->name('Print debit note');
    Route::get('printExpenseClaimMaster', 'ExpenseClaimMasterAPIController@printExpenseClaimMaster')->name('Print expense claim master');
    Route::get('printBankReconciliation', 'BankReconciliationAPIController@printBankReconciliation')->name('Print bank reconciliation');
    Route::get('printPaymentMatching', 'MatchDocumentMasterAPIController@printPaymentMatching')->name('Print payment matching');
    Route::get('getSalesQuotationPrintPDF', 'QuotationMasterAPIController@getSalesQuotationPrintPDF')->name('Get sales quotation print pdf');
    Route::get('printDeliveryOrder', 'DeliveryOrderAPIController@printDeliveryOrder')->name('Print delivery order');
    Route::get('printSalesReturn', 'SalesReturnAPIController@printSalesReturn')->name('Print sales return');
    Route::get('printRecurringVoucher', 'RecurringVoucherSetupAPIController@printRecurringVoucher')->name('Print recurring voucher');
    Route::get('printChartOfAccount', 'ChartOfAccountAPIController@printChartOfAccount')->name('Print chart of account');
    Route::get('pvSupplierPrint', 'BankLedgerAPIController@pvSupplierPrint')->name('Print pv supplier');
    Route::get('printCreditNote', 'CreditNoteAPIController@printCreditNote')->name('Print credit note');
    Route::post('generateARReportPDF', 'AccountsReceivableReportAPIController@pdfExportReport')->name('Generate ar report pdf');
    Route::post('generateAPReportPDF', 'AccountsPayableReportAPIController@pdfExportReport')->name('Generate ap report pdf');
    Route::get('getPoLogisticPrintPDF', 'PoAdvancePaymentAPIController@getPoLogisticPrintPDF')->name('Get procurement order logistic print pdf');
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::get('printAssetDepreciation', 'FixedAssetDepreciationMasterAPIController@printAssetDepreciation')->name('Print asset depreciation');
            Route::get('printEvaluationTemplate', 'SupplierEvaluationTemplateAPIController@printEvaluationTemplate')->name('Print evaluation template');
            Route::get('supplierEvaluationPrintPDF', 'SupplierEvaluationController@printSupplierEvaluation')->name('Print supplier evaluation pdf');
            Route::get('printSupplierInvoice', 'BookInvSuppMasterAPIController@printSupplierInvoice')->name('Print supplier invoice');
            Route::get('printJournalVoucher', 'JvMasterAPIController@printJournalVoucher')->name('Print journal voucher');
        });
    });
});

Route::post('getReportPDF', 'ReportAPIController@pdfExportReport')->name('Get report pdf');


Route::get('printChequeItems', 'BankLedgerAPIController@printChequeItems')->name('Print cheque items');
Route::get('printSuppliers', 'SupplierMasterAPIController@printSuppliers')->name('Print suppliers');


Route::get('getBatchSubmissionDetailsPrintPDF', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionDetailsPrintPDF')->name('Get batch submission details print pdf');

Route::get('exportPaymentBankTransfer', 'PaymentBankTransferAPIController@exportPaymentBankTransfer')->name('Export payment bank transfer');
Route::get('BidSummaryReport', 'BidSubmissionMasterAPIController@BidSummaryExportReport')->name('Bid summary report');
Route::get('SupplierRankingSummaryReport', 'TenderFinalBidsAPIController@getFinalBidsReport')->name('Supplier ranking summary report');
Route::get('MinutesofTenderAwardingReport', 'TenderFinalBidsAPIController@getTenderAwardingReport')->name('Minutes of tender awarding report');
Route::get('MinutesofBidOpeningReport', 'TenderMasterAPIController@getTenderBidOpeningReport')->name('Minutes of bid opening report');
Route::get('supplier-item-wise-report', 'BidSubmissionMasterAPIController@SupplierItemWiseExportReport')->name('Supplier item wise report');
Route::post('schedule-wise-report', 'BidSubmissionMasterAPIController@SupplierSheduleWiseReport')->name('Schedule wise report');
Route::post('SupplierScheduleWiseExportReport', 'BidSubmissionMasterAPIController@SupplierScheduleWiseExportReport')->name('Supplier schedule wise export report');

Route::post('genearetBarcode', 'BarcodeConfigurationAPIController@genearetBarcode')->name('Generate barcode');
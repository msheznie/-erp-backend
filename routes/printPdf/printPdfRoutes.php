<?php

 Route::group(['middleware' => 'print_lang'], function () {
    Route::get('getProcumentOrderPrintPDF', 'ProcumentOrderAPIController@getProcumentOrderPrintPDF')->name('Get procurement order print pdf');
    Route::get('goodReceiptVoucherPrintPDF', 'GRVMasterAPIController@goodReceiptVoucherPrintPDF');
    Route::get('printItemIssue', 'ItemIssueMasterAPIController@printItemIssue');
    Route::get('deliveryPrintItemIssue', 'ItemIssueMasterAPIController@deliveryPrintItemIssue');
    Route::get('printCustomerInvoice', 'CustomerInvoiceDirectAPIController@printCustomerInvoice');
    Route::get('printReceiptVoucher', 'CustomerReceivePaymentAPIController@printReceiptVoucher');
    Route::get('printPaymentVoucher', 'PaySupplierInvoiceMasterAPIController@printPaymentVoucher');
    Route::get('printPurchaseRequest', 'PurchaseRequestAPIController@printPurchaseRequest');
    Route::get('printMaterielRequest', 'MaterielRequestAPIController@printMaterielRequest');
    
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::get('printEvaluationTemplate', 'SupplierEvaluationTemplateAPIController@printEvaluationTemplate');
            Route::get('supplierEvaluationPrintPDF', 'SupplierEvaluationController@printSupplierEvaluation');

        });
    });
});


Route::get('getPoLogisticPrintPDF', 'PoAdvancePaymentAPIController@getPoLogisticPrintPDF')->name('Get procurement order logistic print pdf');
Route::post('getReportPDF', 'ReportAPIController@pdfExportReport');

Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('generateARReportPDF', 'AccountsReceivableReportAPIController@pdfExportReport');
        Route::get('printSupplierInvoice', 'BookInvSuppMasterAPIController@printSupplierInvoice');
        Route::get('printJournalVoucher', 'JvMasterAPIController@printJournalVoucher');
    });
});

Route::post('generateAPReportPDF', 'AccountsPayableReportAPIController@pdfExportReport');
Route::get('printItemReturn', 'ItemReturnMasterAPIController@printItemReturn');
Route::get('printStockReceive', 'StockReceiveAPIController@printStockReceive');
Route::get('printStockTransfer', 'StockTransferAPIController@printStockTransfer');

Route::get('printPurchaseReturn', 'PurchaseReturnAPIController@printPurchaseReturn');
Route::get('printExpenseClaim', 'ExpenseClaimAPIController@printExpenseClaim');
Route::get('printExpenseClaimMaster', 'ExpenseClaimMasterAPIController@printExpenseClaimMaster');
Route::get('printCreditNote', 'CreditNoteAPIController@printCreditNote');
Route::get('printDebitNote', 'DebitNoteAPIController@printDebitNote');
Route::get('printBankReconciliation', 'BankReconciliationAPIController@printBankReconciliation');
Route::get('printChequeItems', 'BankLedgerAPIController@printChequeItems');
Route::get('printSuppliers', 'SupplierMasterAPIController@printSuppliers');


Route::get('printPaymentMatching', 'MatchDocumentMasterAPIController@printPaymentMatching');
Route::get('getSalesQuotationPrintPDF', 'QuotationMasterAPIController@getSalesQuotationPrintPDF');
Route::get('getBatchSubmissionDetailsPrintPDF', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionDetailsPrintPDF');

Route::get('pvSupplierPrint', 'BankLedgerAPIController@pvSupplierPrint');
Route::get('printDeliveryOrder', 'DeliveryOrderAPIController@printDeliveryOrder');
Route::get('printSalesReturn', 'SalesReturnAPIController@printSalesReturn');

Route::get('exportPaymentBankTransfer', 'PaymentBankTransferAPIController@exportPaymentBankTransfer');
Route::get('BidSummaryReport', 'BidSubmissionMasterAPIController@BidSummaryExportReport');
Route::get('SupplierRankingSummaryReport', 'TenderFinalBidsAPIController@getFinalBidsReport');
Route::get('MinutesofTenderAwardingReport', 'TenderFinalBidsAPIController@getTenderAwardingReport');
Route::get('MinutesofBidOpeningReport', 'TenderMasterAPIController@getTenderBidOpeningReport');
Route::get('supplier-item-wise-report', 'BidSubmissionMasterAPIController@SupplierItemWiseExportReport');
Route::post('schedule-wise-report', 'BidSubmissionMasterAPIController@SupplierSheduleWiseReport');
Route::post('SupplierScheduleWiseExportReport', 'BidSubmissionMasterAPIController@SupplierScheduleWiseExportReport');

Route::post('genearetBarcode', 'BarcodeConfigurationAPIController@genearetBarcode');
Route::get('printRecurringVoucher', 'RecurringVoucherSetupAPIController@printRecurringVoucher');
Route::get('printChartOfAccount', 'ChartOfAccountAPIController@printChartOfAccount');

<?php
 Route::group(['middleware' => 'print_lang'], function () {
    Route::get('getProcumentOrderPrintPDF', 'SignedPdfController@handleDirectPdf')->name('Get procurement order print pdf');
    Route::get('goodReceiptVoucherPrintPDF', 'SignedPdfController@handleDirectPdf');
    Route::get('printItemIssue', 'SignedPdfController@handleDirectPdf');
    Route::get('deliveryPrintItemIssue', 'SignedPdfController@handleDirectPdf');
    Route::get('printCustomerInvoice', 'SignedPdfController@handleDirectPdf');
    Route::get('printReceiptVoucher', 'SignedPdfController@handleDirectPdf');
    Route::get('printPaymentVoucher', 'SignedPdfController@handleDirectPdf');
    Route::get('printPurchaseRequest', 'SignedPdfController@handleDirectPdf');
    Route::get('printMaterielRequest', 'SignedPdfController@handleDirectPdf');
    Route::get('printBudgetTransfer', 'SignedPdfController@handleDirectPdf');
    Route::get('printStockTransfer', 'SignedPdfController@handleDirectPdf');
    Route::get('printItemReturn', 'SignedPdfController@handleDirectPdf');
    Route::get('printStockReceive', 'SignedPdfController@handleDirectPdf');
    Route::get('printPurchaseReturn', 'SignedPdfController@handleDirectPdf');
    Route::get('printExpenseClaim', 'SignedPdfController@handleDirectPdf');
    Route::get('printDebitNote', 'SignedPdfController@handleDirectPdf');
    Route::get('printExpenseClaimMaster', 'SignedPdfController@handleDirectPdf');
    Route::get('printBankReconciliation', 'SignedPdfController@handleDirectPdf');
    Route::get('printPaymentMatching', 'SignedPdfController@handleDirectPdf');
    Route::get('getSalesQuotationPrintPDF', 'SignedPdfController@handleDirectPdf');
    Route::get('printDeliveryOrder', 'SignedPdfController@handleDirectPdf');
    Route::get('printSalesReturn', 'SignedPdfController@handleDirectPdf');
    Route::get('printRecurringVoucher', 'SignedPdfController@handleDirectPdf');
    Route::get('printChartOfAccount', 'SignedPdfController@handleDirectPdf');
    Route::get('pvSupplierPrint', 'SignedPdfController@handleDirectPdf');
    Route::get('printCreditNote', 'SignedPdfController@handleDirectPdf');
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::get('printEvaluationTemplate', 'SignedPdfController@handleDirectPdf');
            Route::get('supplierEvaluationPrintPDF', 'SignedPdfController@handleDirectPdf');
            Route::get('printSupplierInvoice', 'SignedPdfController@handleDirectPdf');
            Route::get('printJournalVoucher', 'SignedPdfController@handleDirectPdf');
        });
    });
});


Route::get('getPoLogisticPrintPDF', 'SignedPdfController@handleDirectPdf')->name('Get procurement order logistic print pdf');
Route::post('getReportPDF', 'SignedPdfController@handleDirectPdf');

Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('generateARReportPDF', 'SignedPdfController@handleDirectPdf');
        Route::post('generateAPReportPDF', 'SignedPdfController@handleDirectPdf');
    });
});



Route::get('printChequeItems', 'SignedPdfController@handleDirectPdf');
Route::get('printSuppliers', 'SignedPdfController@handleDirectPdf');


Route::get('getBatchSubmissionDetailsPrintPDF', 'SignedPdfController@handleDirectPdf');

Route::get('exportPaymentBankTransfer', 'SignedPdfController@handleDirectPdf');
Route::get('BidSummaryReport', 'SignedPdfController@handleDirectPdf');
Route::get('SupplierRankingSummaryReport', 'SignedPdfController@handleDirectPdf');
Route::get('MinutesofTenderAwardingReport', 'SignedPdfController@handleDirectPdf');
Route::get('MinutesofBidOpeningReport', 'SignedPdfController@handleDirectPdf');
Route::get('supplier-item-wise-report', 'SignedPdfController@handleDirectPdf');
Route::post('schedule-wise-report', 'SignedPdfController@handleDirectPdf');
Route::post('SupplierScheduleWiseExportReport', 'SignedPdfController@handleDirectPdf');

Route::post('genearetBarcode', 'SignedPdfController@handleDirectPdf');

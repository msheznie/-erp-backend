<?php

/**
 * This file contains accounts receivable module related routes
 * 
 * 
 * */

//Approvals
Route::group([], function () {

    Route::get('getINVFormData', 'CustomerInvoiceDirectAPIController@getINVFormData')->name("Get INV Form Data");
    Route::post('getCustomerInvoiceApproval', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceApproval')->name("Get Customer Invoice Approval");
    Route::post('getApprovedCustomerInvoiceForCurrentUser', 'CustomerInvoiceDirectAPIController@getApprovedCustomerInvoiceForCurrentUser')->name("Get Approved Customer Invoice For Current User");
    Route::post('approveCustomerInvoice', 'CustomerInvoiceDirectAPIController@approveCustomerInvoice')->name("Approve Customer Invoice");
    Route::post('approvalPreCheckCustomerInvoice', 'CustomerInvoiceDirectAPIController@approvalPreCheckCustomerInvoice')->name("Approval Pre Check Customer Invoice");
    Route::post('rejectCustomerInvoice', 'CustomerInvoiceDirectAPIController@rejectCustomerInvoice')->name("Reject Customer Invoice");

    Route::get('getCreditNoteViewFormData', 'CreditNoteAPIController@getCreditNoteViewFormData')->name("Get Credit Note View Form Data");
    Route::post('getCreditNoteApprovalByUser', 'CreditNoteAPIController@getCreditNoteApprovalByUser')->name("Get Credit Note Approval By User");
    Route::post('getCreditNoteApprovedByUser', 'CreditNoteAPIController@getCreditNoteApprovedByUser')->name("Get Credit Note Approved By User");
    Route::post('approvalPreCheckCreditNote', 'CreditNoteAPIController@approvalPreCheckCreditNote')->name("Approval Pre Check Credit Note");

    Route::get('getRecieptVoucherFormData', 'CustomerReceivePaymentAPIController@getRecieptVoucherFormData')->name("Get Reciept Voucher Form Data");
    Route::post('getReceiptVoucherApproval', 'CustomerReceivePaymentAPIController@getReceiptVoucherApproval')->name("Get Receipt Voucher Approval");
    Route::post('getApprovedRVForCurrentUser', 'CustomerReceivePaymentAPIController@getApprovedRVForCurrentUser')->name("Get Approved RV For Current User");
    Route::post('approvalPreCheckReceiptVoucher', 'CustomerReceivePaymentAPIController@approvalPreCheckReceiptVoucher')->name("Approval Pre Check Receipt Voucher");
    Route::post('approveReceiptVoucher', 'CustomerReceivePaymentAPIController@approveReceiptVoucher')->name("Approve Receipt Voucher");
    Route::post('rejectReceiptVoucher', 'CustomerReceivePaymentAPIController@rejectReceiptVoucher')->name("Reject Receipt Voucher");
});

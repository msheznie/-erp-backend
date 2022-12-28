<?php
/**
 * This file contains accounts payable module related routes
 * 
 * 
 * */


//approval
Route::group([], function(){
    Route::post('getDebitNoteApprovalByUser', 'DebitNoteAPIController@getDebitNoteApprovalByUser')->name("Get pending for approval - Debit Note");
    Route::post('getDebitNoteApprovedByUser', 'DebitNoteAPIController@getDebitNoteApprovedByUser')->name("Get approved - Debit Note");
    Route::post('approvalPreCheckDebitNote', 'DebitNoteAPIController@approvalPreCheckDebitNote')->name("Debit note approval pre check");
    Route::get('getDebitNoteFormData', 'DebitNoteAPIController@getDebitNoteFormData')->name("Get Debit note form data");

    Route::post('getPaymentApprovalByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovalByUser')->name("Get pending for approval - Payment Voucher");
    Route::post('getPaymentApprovedByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovedByUser')->name("Get approved - Payment Voucher");

    Route::post('getInvoiceMasterApproval', 'BookInvSuppMasterAPIController@getInvoiceMasterApproval')->name("Get pending for approval - Supplier Invoice");
    Route::post('getApprovedInvoiceForCurrentUser', 'BookInvSuppMasterAPIController@getApprovedInvoiceForCurrentUser')->name("Get approved - Supplier Invoice");
    Route::post('rejectSupplierInvoice', 'BookInvSuppMasterAPIController@rejectSupplierInvoice')->name("Reject supplier invoice");
});


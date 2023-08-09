<?php
/**
 * This file contains accounts payable module related routes
 * 
 * 
 * */


//transactions
use Illuminate\Support\Facades\Route;

Route::group([], function() {

    Route::resource('supplierInvoiceCRUD', 'BookInvSuppMasterAPIController',['only' => ['store', 'show', 'update']]);
    Route::resource('book_inv_supp_dets', 'BookInvSuppDetAPIController', ['except' => ['index','store']]);
    Route::resource('direct_invoice_details', 'DirectInvoiceDetailsAPIController', ['except' => ['index']]);
    Route::get('getInvoiceMasterRecord', 'BookInvSuppMasterAPIController@getInvoiceMasterRecord')->name("Get invoice master record");
    Route::put('book_inv_supp_local_update/{id}', 'BookInvSuppMasterAPIController@updateLocalER')->name("Update local er");
    Route::put('book_inv_supp_reporting_update/{id}', 'BookInvSuppMasterAPIController@updateReportingER')->name("Update reporting er");
    Route::put('supplierInvoiceUpdateCurrency/{id}', 'BookInvSuppMasterAPIController@updateCurrency')->name("Update currency");
    Route::get('getInvoiceMasterFormData', 'BookInvSuppMasterAPIController@getInvoiceMasterFormData')->name("Get invoice master form data");
    Route::post('getInvoiceMasterView', 'BookInvSuppMasterAPIController@getInvoiceMasterView')->name("Get invoice master view");
    Route::get('getInvoiceSupplierTypeBase', 'BookInvSuppMasterAPIController@getInvoiceSupplierTypeBase')->name("Get invoice supplier type base");
    Route::get('getSupplierInvoiceStatusHistory', 'BookInvSuppMasterAPIController@getSupplierInvoiceStatusHistory')->name("Get supplier invoice status history");
    Route::get('getDirectItems', 'DirectInvoiceDetailsAPIController@getDirectItems')->name("Get direct items");
    Route::get('getSupplierInvoiceGRVItems', 'BookInvSuppDetAPIController@getSupplierInvoiceGRVItems')->name("Get supplier invoice grv items");
    Route::get('getSupplierInvDirectItems', 'SupplierInvoiceDirectItemAPIController@getSupplierInvDirectItems')->name("Get supplier inv direct items");
    Route::get('printSupplierInvoice', 'BookInvSuppMasterAPIController@printSupplierInvoice')->name("Print supplier invoice");
    Route::get('getPurchaseOrderForSI', 'UnbilledGrvGroupByAPIController@getPurchaseOrderForSI')->name("Get purchase order for si");
    Route::get('getUnbilledGRVDetailsForSI', 'UnbilledGrvGroupByAPIController@getUnbilledGRVDetailsForSI')->name("Get unbilled grv details for si");
    Route::post('storePOBaseDetail', 'BookInvSuppDetAPIController@storePOBaseDetail')->name("Store po base detail");
    Route::post('editPOBaseDetail', 'BookInvSuppDetAPIController@editPOBaseDetail')->name("Edit po base detail");
    Route::post('supplierInvoiceReopen', 'BookInvSuppMasterAPIController@supplierInvoiceReopen')->name("Supplier invoice reopen");
    Route::post('clearSupplierInvoiceNo', 'BookInvSuppMasterAPIController@clearSupplierInvoiceNo')->name("Clear supplier invoice no");
    Route::post('getSIMasterAmendHistory', 'BookInvSuppMasterRefferedBackAPIController@getSIMasterAmendHistory')->name("Get si master amend history");
    Route::post('saveSupplierInvoiceTaxDetails', 'BookInvSuppMasterAPIController@saveSupplierInvoiceTaxDetails')->name("Save si tax details");
    Route::get('supplierInvoiceTaxTotal', 'BookInvSuppMasterAPIController@supplierInvoiceTaxTotal')->name("Get si tax total");
    
});

//approval
Route::group([], function(){
    Route::post('getDebitNoteApprovalByUser', 'DebitNoteAPIController@getDebitNoteApprovalByUser')->name("Get pending for approval - Debit Note");
    Route::post('getDebitNoteApprovedByUser', 'DebitNoteAPIController@getDebitNoteApprovedByUser')->name("Get approved - Debit Note");
    Route::post('approvalPreCheckDebitNote', 'DebitNoteAPIController@approvalPreCheckDebitNote')->name("Debit note approval pre check");

    Route::post('getPaymentApprovalByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovalByUser')->name("Get pending for approval - Payment Voucher");
    Route::post('getPaymentApprovedByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovedByUser')->name("Get approved - Payment Voucher");

    Route::post('getInvoiceMasterApproval', 'BookInvSuppMasterAPIController@getInvoiceMasterApproval')->name("Get pending for approval - Supplier Invoice");
    Route::post('getApprovedInvoiceForCurrentUser', 'BookInvSuppMasterAPIController@getApprovedInvoiceForCurrentUser')->name("Get approved - Supplier Invoice");
    Route::post('rejectSupplierInvoice', 'BookInvSuppMasterAPIController@rejectSupplierInvoice')->name("Reject supplier invoice");
});

//Debit Note
Route::group([], function(){
    Route::get('getDebitNoteFormData', 'DebitNoteAPIController@getDebitNoteFormData')->name("Get Debit note form data");
    Route::post('exportDebitNotesByCompany', 'DebitNoteAPIController@exportDebitNotesByCompany')->name('Export debit notes by company');
    Route::post('getDebitNoteAmendHistory', 'DebitNoteMasterRefferedbackAPIController@getDebitNoteAmendHistory')->name('Get debit note amend history');
    Route::get('getDNDetailAmendHistory', 'DebitNoteDetailsRefferedbackAPIController@getDNDetailAmendHistory')->name('Get debit note detail amend history');
    Route::get('getDebitNoteMasterRecord', 'DebitNoteAPIController@getDebitNoteMasterRecord')->name('Get debit note master record');
    Route::post('getAllDebitNotes', 'DebitNoteAPIController@getAllDebitNotes')->name('Get all debit notes');
    Route::put('debitNoteUpdateCurrency/{id}', 'DebitNoteAPIController@updateCurrency')->name('Debit note update currency');
    Route::put('updateDebiteNoteType/{id}', 'DebitNoteAPIController@updateDebiteNoteType')->name('Update debit note type');
    Route::get('getDetailsByDebitNote', 'DebitNoteDetailsAPIController@getDetailsByDebitNote')->name('Get details by debit note');
    Route::put('debitNoteLocalUpdate/{id}', 'DebitNoteAPIController@debitNoteLocalUpdate')->name('Debit note local update');
    Route::put('debitNoteReportingUpdate/{id}','DebitNoteAPIController@debitNoteReportingUpdate')->name('Debit note reporting update');
    Route::post('debitNoteReopen', 'DebitNoteAPIController@debitNoteReopen')->name('Debit note reopen');
    Route::get('getDebitNotePaymentStatusHistory', 'DebitNoteAPIController@getDebitNotePaymentStatusHistory')->name('Get debit note payment status history');
    Route::post('amendDebitNote', 'DebitNoteAPIController@amendDebitNote')->name('Amend debit Note');
    Route::post('amendDebitNoteReview', 'DebitNoteAPIController@amendDebitNoteReview')->name('Amend debit note review');
    Route::post('checkPaymentStatusDNPrint', 'DebitNoteAPIController@checkPaymentStatusDNPrint')->name('Check payment status debit note print');
    Route::resource('debit_note_details', 'DebitNoteDetailsAPIController');
    Route::resource('debitNoteMasterRefferedbacksCRUD', 'DebitNoteMasterRefferedbackAPIController');
    Route::resource('debit_notes', 'DebitNoteAPIController');
});

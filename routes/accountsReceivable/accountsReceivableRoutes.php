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

//CI
Route::group([], function () {
    Route::resource('customer_invoice_directs', 'CustomerInvoiceDirectAPIController');
    Route::resource('taxdetails', 'TaxdetailAPIController');
    Route::resource('customer_invoice_direct_details', 'CustomerInvoiceDirectDetailAPIController');
    Route::resource('customerInvoiceCollectionDetails', 'CustomerInvoiceCollectionDetailAPIController');
    Route::resource('customer_invoice_logistics', 'CustomerInvoiceLogisticAPIController');
    Route::resource('customer_invoice_item_details', 'CustomerInvoiceItemDetailsAPIController');

    Route::get('getcreateINVFormData', 'CustomerInvoiceDirectAPIController@getcreateINVFormData')->name("Get Create INV Form Data");
    Route::get('customerInvoiceDetails', 'CustomerInvoiceDirectAPIController@customerInvoiceDetails')->name("Customer Invoice Details");
    Route::get('AllDeleteCustomerInvoiceDetails', 'CustomerInvoiceDirectAPIController@AllDeleteCustomerInvoiceDetails')->name("All Delete Customer Invoice Details");
    Route::get('getAllcontractbyclient', 'CustomerInvoiceDirectAPIController@getAllcontractbyclient')->name("Get All Contract By Client");
    Route::get('customerInvoiceAudit', 'CustomerInvoiceDirectAPIController@customerInvoiceAudit')->name("Customer Invoice Audit");
    Route::get('customerInvoiceReceiptStatus', 'CustomerInvoiceDirectAPIController@customerInvoiceReceiptStatus')->name("Customer Invoice Receipt Status");
    Route::get('getCustomerCollectionItems', 'CustomerInvoiceCollectionDetailAPIController@getCustomerCollectionItems')->name("Get Customer Collection Items");
    Route::get('getInvoiceLogistic', 'CustomerInvoiceLogisticAPIController@getInvoiceLogistic')->name("Get Invoice Logistic");
    Route::get('getDeliveryOrderRecord','CustomerInvoiceItemDetailsAPIController@getDeliveryOrderRecord')->name("Get Delivery Order Record");
    Route::get('deliveryOrderForCustomerInvoice','CustomerInvoiceItemDetailsAPIController@deliveryOrderForCustomerInvoice')->name("Delivery Order For Customer Invoice");
    Route::get('getDeliveryOrderDetailForInvoice','CustomerInvoiceItemDetailsAPIController@getDeliveryOrderDetailForInvoice')->name("Get Delivery Order Detail For Invoice");
    Route::get('getItemByCustomerInvoiceItemDetail', 'CustomerInvoiceItemDetailsAPIController@getItemByCustomerInvoiceItemDetail')->name("Get Item By Customer Invoice Item Detail");
    Route::get('getDeliveryTerms', 'CustomerInvoiceItemDetailsAPIController@getDeliveryTerms')->name("Get Delivery Terms");

    Route::post('getDeliveryTermsFormData', 'CustomerInvoiceItemDetailsAPIController@getDeliveryTermsFormData')->name("Get Delivery Terms Form Data");
    Route::post('getCustomerCatalogDetailByCustomerItem', 'CustomerCatalogMasterAPIController@getCustomerCatalogDetailByCustomerItem')->name("Get Customer Catalog Detail By Customer Item");
    Route::post('storeInvoiceDetailFromDeliveryOrder','CustomerInvoiceItemDetailsAPIController@storeInvoiceDetailFromDeliveryOrder')->name("Store Invoice Detail From Delivery Order");
    Route::post('customerInvoiceTaxDetail', 'TaxdetailAPIController@customerInvoiceTaxDetail')->name("Customer Invoice Tax Detail");
    Route::post('getCustomerInvoiceMasterView', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceMasterView')->name("Get Customer Invoice Master View");
    Route::post('getCustomerInvoicePerformaDetails', 'CustomerInvoiceDirectAPIController@getCustomerInvoicePerformaDetails')->name("Get Customer Invoice Perform aDetails");
    Route::post('saveCustomerinvoicePerforma', 'CustomerInvoiceDirectAPIController@saveCustomerinvoicePerforma')->name("Save Customer Invoice Performa");
    Route::post('savecustomerInvoiceTaxDetails', 'CustomerInvoiceDirectAPIController@savecustomerInvoiceTaxDetails')->name("Save Customer Invoice Tax Details");
    Route::post('updateCustomerInvoiceGRV', 'CustomerInvoiceDirectAPIController@updateCustomerInvoiceGRV')->name("Update Customer Invoice GRV");
    Route::post('customerInvoiceReopen', 'CustomerInvoiceDirectAPIController@customerInvoiceReopen')->name("Customer Invoice Reopen");
    Route::post('clearCustomerInvoiceNumber', 'CustomerInvoiceDirectAPIController@clearCustomerInvoiceNumber')->name("Clear Customer Invoice Number");
    Route::post('getCustomerInvoiceAmend', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceAmend')->name("Get Customer Invoice Amend");
    Route::post('customerInvoiceCancel', 'CustomerInvoiceDirectAPIController@customerInvoiceCancel')->name("Customer Invoice Cancel");
    Route::post('amendCustomerInvoiceReview', 'CustomerInvoiceDirectAPIController@amendCustomerInvoiceReview')->name("Amend Customer Invoice Review");
    Route::post('addDirectInvoiceDetails', 'CustomerInvoiceDirectDetailAPIController@addDirectInvoiceDetails')->name("Add Direct Invoice Details");
    Route::post('updateDirectInvoice', 'CustomerInvoiceDirectDetailAPIController@updateDirectInvoice')->name("Update Direct Invoice");
    Route::post('addNote', 'CustomerInvoiceLogisticAPIController@addNote')->name("Add Note");
    Route::post('validateCustomerInvoiceDetails','CustomerInvoiceItemDetailsAPIController@validateCustomerInvoiceDetails')->name("Validate Customer Invoice Details");
    Route::post('storeInvoiceDetailFromSalesQuotation','CustomerInvoiceItemDetailsAPIController@storeInvoiceDetailFromSalesQuotation')->name("Store Invoice Detail From Sales Quotation");

    Route::put('custItemDetailUpdate/{id}', 'CustomerInvoiceItemDetailsAPIController@custItemDetailUpdate')->name("Cust Item Detail Update");
    Route::put('customerInvoiceCurrencyUpdate/{id}', 'CustomerInvoiceDirectAPIController@updateCurrency')->name("Update Currency");
    Route::put('customerInvoiceLocalUpdate/{id}', 'CustomerInvoiceDirectAPIController@customerInvoiceLocalUpdate')->name("Customer Invoice Local Update");
    Route::put('customerInvoiceReportingUpdate/{id}', 'CustomerInvoiceDirectAPIController@customerInvoiceReportingUpdate')->name("Customer Invoice Reporting Update");
});

//Credit Note
Route::group([], function () {
    Route::resource('credit_notes', 'CreditNoteAPIController');
    Route::resource('credit_note_details', 'CreditNoteDetailsAPIController');

    Route::get('getCreditNoteMasterRecord', 'CreditNoteAPIController@getCreditNoteMasterRecord')->name("Get Credit Note Master Record");
    Route::get('creditNoteDetails', 'CreditNoteDetailsAPIController@creditNoteDetails')->name("Credit Note Details");
    Route::get('getAllcontractbyclientbase', 'CreditNoteDetailsAPIController@getAllcontractbyclientbase')->name("Get All Contract By Client Base");
    Route::get('creditNoteAudit', 'CreditNoteAPIController@creditNoteAudit')->name("Credit Note Audit");
    Route::get('getFilteredDebitNote', 'CreditNoteAPIController@getFilteredDebitNote')->name("Get Filtered Debit Note");
    Route::get('creditNoteReceiptStatus', 'CreditNoteAPIController@creditNoteReceiptStatus')->name("Credit Note Receipt Status");

    Route::post('creditNoteMasterDataTable', 'CreditNoteAPIController@creditNoteMasterDataTable')->name("Credit Note Master Data Table");
    Route::post('addcreditNoteDetails', 'CreditNoteDetailsAPIController@addcreditNoteDetails')->name("Add Credit Note Details");
    Route::post('updateCreditNote', 'CreditNoteDetailsAPIController@updateCreditNote')->name("Update Credit Note");
    Route::post('creditNoteReopen', 'CreditNoteAPIController@creditNoteReopen')->name("Credit Note Reopen");
    Route::post('amendCreditNote', 'CreditNoteAPIController@amendCreditNote')->name("Amend Credit Note");
    Route::post('amendCreditNoteReview', 'CreditNoteAPIController@amendCreditNoteReview')->name("Amend Credit Note Review");

    Route::put('creditNoteLocalUpdate/{id}', 'CreditNoteAPIController@creditNoteLocalUpdate')->name("Credit Note Local Update");
    Route::put('creditNoteReportingUpdate/{id}','CreditNoteAPIController@creditNoteReportingUpdate')->name("Credit Note Reporting Update");
    Route::put('updateCreditNote/{id}', 'CreditNoteAPIController@updateCurrency')->name("Update Currency");
});
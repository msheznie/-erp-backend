<?php 


//approval
Route::group([], function () {
    //salesQuotationApproval
    Route::post('getSalesQuotationApprovals', 'QuotationMasterAPIController@getSalesQuotationApprovals')->name("Get sales quotation approvals");
    Route::post('getApprovedSalesQuotationForUser', 'QuotationMasterAPIController@getApprovedSalesQuotationForUser')->name("Get approved sales quotation for user");
    Route::post('approveSalesQuotation', 'QuotationMasterAPIController@approveSalesQuotation')->name("Approve sales quotation");
    Route::post('rejectSalesQuotation', 'QuotationMasterAPIController@rejectSalesQuotation')->name("Reject sales quotation");

    Route::get('getSalesQuotationMasterRecord', 'QuotationMasterAPIController@getSalesQuotationMasterRecord')->name("Get sales quotation master records");

    //salesDeliveryOrderApproval
    Route::post('getDeliveryOrderApprovals', 'DeliveryOrderAPIController@getDeliveryOrderApprovals')->name("Get delivery order approvals");
    Route::post('getApprovedDeliveryOrderForUser', 'DeliveryOrderAPIController@getApprovedDeliveryOrderForUser')->name("Get approved delivery order for user");
    Route::post('rejectDeliveryOrder', 'DeliveryOrderAPIController@rejectDeliveryOrder')->name("Reject delivery order");
    Route::post('approveDeliveryOrder', 'DeliveryOrderAPIController@approveDeliveryOrder')->name("Approve delivery order");

    //salesReturnApproval
    Route::post('getSalesReturnApprovals', 'SalesReturnAPIController@getSalesReturnApprovals')->name("Get sales return approvals");
    Route::post('getApprovedSalesReturnForUser', 'SalesReturnAPIController@getApprovedSalesReturnForUser')->name("Get approved sales return for user");
    Route::post('rejectSalesReturn', 'SalesReturnAPIController@rejectSalesReturn')->name("Reject sales return");
    Route::get('getSalesReturnRecord', 'SalesReturnAPIController@getSalesReturnRecord')->name("Get sales return record");


});

//Transactions

//Quotation
Route::group([], function () { 

    Route::post('getAllSalesQuotation', 'QuotationMasterAPIController@getAllSalesQuotation')->name("Get all sales quotation");
    Route::post('getSalesQuotationAmendHistory', 'QuotationMasterRefferedbackAPIController@getSalesQuotationAmendHistory')->name("Get sales quotation amend history");
    Route::post('getDeliveryDetailsForSQ', 'DeliveryOrderAPIController@getDeliveryDetailsForSQ')->name("Get delivery details for sales quotation");
    Route::post('getCommonFormData','DeliveryOrderAPIController@getCommonFormData')->name("Get common form data");
    Route::post('validateDeliveryOrder','DeliveryOrderAPIController@validateDeliveryOrder')->name("Validate delivery order");
    Route::post('salesQuotationDetailsDeleteAll', 'QuotationDetailsAPIController@salesQuotationDetailsDeleteAll')->name("Delete all sales quotation details");
    Route::post('salesQuotationReopen', 'QuotationMasterAPIController@salesQuotationReopen')->name("Reopen sales quotation");
    Route::post('salesQuotationAmend', 'QuotationMasterAPIController@salesQuotationAmend')->name("Amend sales quotation");
    Route::post('mapLineItemQo', 'QuotationDetailsAPIController@mapLineItemQo')->name("Map line item quotation");
    Route::post('storeSalesOrderFromSalesQuotation', 'QuotationDetailsAPIController@storeSalesOrderFromSalesQuotation')->name("Store sales order from sales quotation");
    Route::post('amendSalesQuotationReview', 'QuotationMasterAPIController@amendSalesQuotationReview')->name("Amend sales quotation review");
    Route::post('updateSentCustomerDetail', 'QuotationMasterAPIController@updateSentCustomerDetail')->name("Update sent customer detail");
    Route::post('getInvoiceDetailsForSQ', 'QuotationMasterAPIController@getInvoiceDetailsForSQ')->name("Get invoice details for sales quotation");
    Route::post('salesQuotationVersionCreate', 'QuotationMasterAPIController@salesQuotationVersionCreate')->name("Create sales quotation version");
    Route::post('getSalesQuotationRevisionHistory', 'QuotationMasterVersionAPIController@getSalesQuotationRevisionHistory')->name("Get sales quotation revision history");
    Route::post('uploadItems','QuotationMasterAPIController@poItemsUpload')->name("PO items upload sales quotation");

    Route::get('getSalesQuotationFormData', 'QuotationMasterAPIController@getSalesQuotationFormData')->name("Get sales quotation form data");
    Route::get('salesQuotationAudit', 'QuotationMasterAPIController@salesQuotationAudit')->name("Get sales quotation audit");
    Route::get('getItemsForSalesQuotation', 'QuotationMasterAPIController@getItemsForSalesQuotation')->name("Get items for sales quotation");
    Route::get('getSalesQuotationDetails', 'QuotationDetailsAPIController@getSalesQuotationDetails')->name("Get sales quotation details");
    Route::get('getQuotationStatus', 'QuotationStatusAPIController@getQuotationStatus')->name("Get sales quotation status");
    Route::get('salesQuotationForSO', 'QuotationMasterAPIController@salesQuotationForSO')->name("Get sales quotation for SO");
    Route::get('getSalesQuoatationDetailForSO', 'QuotationMasterAPIController@getSalesQuoatationDetailForSO')->name("Get sales quotation details for SO");
    Route::get('getSalesOrderPaymentTerms', 'SoPaymentTermsAPIController@getSalesOrderPaymentTerms')->name("Get sales order payment terms");
    Route::get('soPaymentTermsAdvanceDetailView', 'SalesOrderAdvPaymentAPIController@soPaymentTermsAdvanceDetailView')->name("Get SO payment terms advance detail view");
    Route::get('getSoLogisticPrintDetail', 'SalesOrderAdvPaymentAPIController@getSoLogisticPrintDetail')->name("Get SO logistic print detail");
    Route::get('getSQVDetailsHistory', 'QuotationVersionDetailsAPIController@getSQVDetailsHistory')->name("Get SQV details history");

    Route::resource('quotationMasterRefferedbacks', 'QuotationMasterRefferedbackAPIController');
    Route::resource('quotationMasters', 'QuotationMasterAPIController');
    Route::resource('quotationDetails', 'QuotationDetailsAPIController');
    Route::resource('delivery_orders', 'DeliveryOrderAPIController');
    Route::resource('delivery_order_details', 'DeliveryOrderDetailAPIController');
    Route::resource('quotation_statuses', 'QuotationStatusAPIController');
    Route::resource('so_payment_terms', 'SoPaymentTermsAPIController');
    Route::resource('sales_order_adv_payments', 'SalesOrderAdvPaymentAPIController');
    Route::resource('quotationMasterVersions', 'QuotationMasterVersionAPIController');

});
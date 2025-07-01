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

//Delivery Order
Route::group([], function () { 

    Route::get('getDeliveryOrderFormData', 'DeliveryOrderAPIController@getDeliveryOrderFormData')->name("Get delivery order form data");
    Route::post('getAllDeliveryOrder', 'DeliveryOrderAPIController@getAllDeliveryOrder')->name("Get all delivery order");
    Route::post('getInvoiceDetailsForDO', 'DeliveryOrderAPIController@getInvoiceDetailsForDO')->name("Get invoice details status");
    Route::post('getSalesReturnDetailsForDO', 'SalesReturnAPIController@getSalesReturnDetailsForDO')->name("Get sales return details for delivery order");
    Route::post('sales-order/is-link-item', 'DeliveryOrderAPIController@isLinkItem')->name("Get status for linked item");
    Route::get('deliveryOrderAudit', 'DeliveryOrderAPIController@deliveryOrderAudit')->name("Delivery order audit");
    Route::post('deliveryOrderReopen', 'DeliveryOrderAPIController@deliveryOrderReopen')->name("Delivery order reopen");
    Route::post('getDeliveryOrderAmend', 'DeliveryOrderAPIController@getDeliveryOrderAmend')->name("Get delivery order amend");
    Route::get('getDeliveryOrderAmendHistory', 'DeliveryOrderRefferedbackAPIController@getDeliveryOrderAmendHistory')->name("Get delivery order amend history");
    Route::resource('do_refferedbacks', 'DeliveryOrderRefferedbackAPIController');
    Route::get('downloadDeliveryOrderUploadTemplate', 'DeliveryOrderAPIController@downloadQuotationItemUploadTemplate')->name("Download quotation item upload template");
    Route::post('uploadItemsDeliveryOrder','DeliveryOrderDetailAPIController@uploadItemsDeliveryOrder')->name("Upload items delivery order");
    Route::post('deleteAllItemsFromDeliveryOrder','DeliveryOrderDetailAPIController@deleteAllItemsFromDeliveryOrder')->name("Delete items delivery order");
    Route::get('salesQuotationForDO', 'DeliveryOrderAPIController@salesQuotationForDO')->name("Sales Quotation for delivery order");
    Route::post('storeDeliveryDetailFromSalesQuotation', 'DeliveryOrderDetailAPIController@storeDeliveryDetailFromSalesQuotation')->name("Store delivery detail from sales quotation");
    Route::get('getSalesQuoatationDetailForDO', 'DeliveryOrderAPIController@getSalesQuoatationDetailForDO')->name("Get sales quotation detail for delivery order");
    Route::post('deliveryOrderValidateItem', 'DeliveryOrderDetailAPIController@validateDeliveryOrderItem')->name("Validate Delivery Order Item");
    Route::post('amendDeliveryorderReview', 'DeliveryOrderAPIController@amendDeliveryorderReview')->name("Amend delivery order");

});

//Sales Return
Route::group([], function () { 

    Route::post('getAllSalesReturn', 'SalesReturnAPIController@getAllSalesReturn')->name("Get all sales return");
    Route::resource('sales_returns', 'SalesReturnAPIController');
    Route::resource('reasonCodeMasters', 'ReasonCodeMasterAPIController');
    Route::get('salesReturnAudit', 'SalesReturnAPIController@salesReturnAudit')->name("Sales return audit");
    Route::post('salesReturnReopen', 'SalesReturnAPIController@salesReturnReopen')->name("Sales return reopen");
    Route::post('getSalesReturnAmend', 'SalesReturnAPIController@getSalesReturnAmend')->name("Get sales return amend");
    Route::get('deliveryNoteForForSR', 'SalesReturnAPIController@deliveryNoteForForSR')->name("Delivery note for sales return");
    Route::get('getSalesInvoiceDeliveryOrderDetail', 'SalesReturnAPIController@getSalesInvoiceDeliveryOrderDetail')->name("Get sales invoice delivery order detail");
    Route::post('storeReturnDetailFromSIDO', 'SalesReturnAPIController@storeReturnDetailFromSIDO')->name("Store return detail from sales invoice delivery order");
    Route::resource('sales_return_details', 'SalesReturnDetailAPIController');

});

//Masters

//Customer Master
Route::group([], function () { 

    Route::post('getAllCustomersByCompany', 'CustomerAssignedAPIController@getAllCustomersByCompany')->name("Get all customers by company");

});

//Sales Person
Route::group([], function () { 

    Route::post('getAllSalesPersons', 'SalesPersonMasterAPIController@getAllSalesPersons')->name("Get all sales persons");
    Route::get('getSalesPersonFormData', 'SalesPersonMasterAPIController@getSalesPersonFormData')->name("Get sales person form data");
    Route::resource('salesPersonMasters', 'SalesPersonMasterAPIController');
    Route::resource('employeeMasterCRUD', 'EmployeeAPIController');
    Route::get('getSalesPersonTargetDetails', 'SalesPersonTargetAPIController@getSalesPersonTargetDetails')->name("Get sales person target details");
    Route::get('checkSalesPersonLastTarget', 'SalesPersonTargetAPIController@checkSalesPersonLastTarget')->name("Check sales person last target");
    Route::resource('salesPersonTargets', 'SalesPersonTargetAPIController');

});

//Reports

//Quotation
Route::group([], function () { 

    Route::post('getSalesMarketFilterData', 'SalesMarketingReportAPIController@getSalesMarketFilterData')->name("Get sales market filter data");
    Route::post('getSubcategoriesBymainCategories', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategories')->name("Get sub categories by main categories");
    Route::post('validateSalesMarketReport', 'SalesMarketingReportAPIController@validateReport')->name("Validate sales market report");
    Route::post('generateSalesMarketReport', 'SalesMarketingReportAPIController@generateReport')->name("Generate sales market report");
    Route::post('exportSalesMarketReport', 'SalesMarketingReportAPIController@exportReport')->name("Export sales market report");

});

//Sales Order To Receipt Report
Route::group([], function () { 

    Route::get('reportSoToReceiptFilterOptions', 'SalesMarketingReportAPIController@reportSoToReceiptFilterOptions')->name("Report sales order to receipt filter options");
    Route::get('getCompanyReportingCurrencyCode', 'CurrencyMasterAPIController@getCompanyReportingCurrencyCode')->name("Get company reporting currency code");
    Route::post('reportSoToReceipt', 'SalesMarketingReportAPIController@reportSoToReceipt')->name("Report sales order to receipt");
    Route::post('exportSoToReceiptReport', 'SalesMarketingReportAPIController@exportSoToReceiptReport')->name("Export sales order to receipt report");

});

//Sales Analysis Report
Route::group([], function () { 

    Route::get('getSalesAnalysisFilterData', 'SalesMarketingReportAPIController@getSalesAnalysisFilterData')->name("Get sales analysis filter data");

});

//Review

//Quotation
Route::group([], function () { 

    Route::post('getOrderDetailsForSQ', 'QuotationMasterAPIController@getOrderDetailsForSQ')->name("Get order details for sales quotation");
    Route::post('cancelQuatation', 'QuotationMasterAPIController@cancelQuatation')->name("Cancel quotation");
    Route::post('closeQuatation', 'QuotationMasterAPIController@closeQuatation')->name("Close quotation");
    Route::post('checkItemExists','QuotationMasterAPIController@checkItemExists')->name("Check item exists");
    Route::post('getCIMasterAmendHistory', 'CustomerInvoiceDirectRefferedbackAPIController@getCIMasterAmendHistory')->name("Get customer invoice master amend history");
    Route::resource('customerInvoiceRefferedbacksCRUD', 'CustomerInvoiceDirectRefferedbackAPIController');
    Route::get('getCIDetailsForAmendHistory', 'CustomerInvoiceDirectDetRefferedbackAPIController@getCIDetailsForAmendHistory')->name("Get CI details for amend history");
    Route::get('getFilteredGRV', 'GRVMasterAPIController@getFilteredGRV')->name("Get Filtered GRV");
    Route::get('salesQuotationForCustomerInvoice','QuotationMasterAPIController@salesQuotationForCustomerInvoice')->name("Sales Quotation For Customer Invoice");
    Route::get('getSalesQuotationDetailForInvoice','QuotationDetailsAPIController@getSalesQuotationDetailForInvoice')->name("Get Sales Quotation Detail For Invoice");
    Route::get('getSQHDetailsHistory', 'QuotationDetailsRefferedbackAPIController@getSQHDetailsHistory')->name("Get SQ details history");
    Route::get('downloadQuotationItemUploadTemplate','QuotationMasterAPIController@downloadQuotationItemUploadTemplate')->name("Download quotation item upload template");
    Route::post('quotation/validate-item', 'QuotationDetailsAPIController@validateItem')->name("Validate quotation item");
    Route::post('quotation/add-multiple-items', 'QuotationDetailsAPIController@addMultipleItems')->name("Add multiple items to quotation");

});


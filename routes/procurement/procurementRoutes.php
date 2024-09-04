<?php
/**
 * This file contains procurement module related routes
 * 
 * 
 * */


//Approvals
Route::group([], function() {


    Route::post('getPOMasterApproval', 'ProcumentOrderAPIController@getPOMasterApproval')->name('Get PO master approval');
    Route::post('getApprovedPOForCurrentUser', 'ProcumentOrderAPIController@getApprovedPOForCurrentUser')->name('Get approved PO for current user');
    Route::post('checkBudgetCutOffForPo', 'ProcumentOrderAPIController@checkBudgetCutOffForPo')->name('Check budget cut off for PO');
    Route::post('getPurchaseRequestApprovalByUser', 'PurchaseRequestAPIController@getPurchaseRequestApprovalByUser')->name('Get purchase request approval by user');
    Route::post('getPurchaseRequestApprovedByUser', 'PurchaseRequestAPIController@getPurchaseRequestApprovedByUser')->name('Get purchase request approved by user');
});

// Purchase Request
Route::group([], function() {
    Route::get('getPurchaseRequestFormData', 'PurchaseRequestAPIController@getPurchaseRequestFormData')->name('Get purchase request form data');
    Route::get('purchaseRequestAudit', 'PurchaseRequestAPIController@purchaseRequestAudit')->name('Purchase request audit');
    Route::get('purchase_requests-isPulled', 'PurchaseRequestAPIController@isPulledFromMR')->name('Purchase requests is pulled from mr');
    Route::get('purchase_requests/pull/items/', 'PulledItemFromMRController@pullAllItemsByPr')->name('Pull all items by pr');
    Route::get('getEligibleMr', 'PurchaseRequestAPIController@getEligibleMr')->name('get eligible mr');
    Route::get('getItemsByPurchaseRequest', 'PurchaseRequestDetailsAPIController@getItemsByPurchaseRequest')->name('Get items by purchase request');
    Route::get('getItemsOptionForPurchaseRequest', 'PurchaseRequestAPIController@getItemsOptionForPurchaseRequest')->name('Get items option for purchase request');
    Route::get('get-all-uom-options', 'PurchaseRequestAPIController@getAllUomOptions')->name('Get all uom options');
    Route::get('getItemMasterPurchaseHistory', 'PurchaseOrderDetailsAPIController@getItemMasterPurchaseHistory')->name('Get item master purchase history');
    Route::get('copy_pr/{id}', 'PurchaseRequestDetailsAPIController@copyPr')->name('Copy purchase request');
    Route::get('purchaseRequestsPOHistory', 'PurchaseRequestAPIController@purchaseRequestsPOHistory')->name('Purchase requests po history');
    Route::get('getProcurementOrderRecord', 'ProcumentOrderAPIController@getProcurementOrderRecord')->name('Get procurement order record');
    Route::get('getProcumentOrderAddons', 'PoAddonsAPIController@getProcumentOrderAddons')->name('Get procurement order addons');
    Route::get('getQtyOrderDetails', 'PurchaseRequestDetailsAPIController@getQtyOrderDetails')->name('Get purchase request qty order details');
    Route::get('getPrItemsForAmendHistory', 'PrDetailsReferedHistoryAPIController@getPrItemsForAmendHistory')->name('Get pr items for amend history');
    Route::get('cancelPurchaseRequestPreCheck', 'PurchaseRequestAPIController@cancelPurchaseRequestPreCheck')->name('Cancel purchase request precheck');
    Route::get('returnPurchaseRequestPreCheck', 'PurchaseRequestAPIController@returnPurchaseRequestPreCheck')->name('Return purchase request precheck');
    Route::get('manualClosePurchaseRequestPreCheck', 'PurchaseRequestAPIController@manualClosePurchaseRequestPreCheck')->name('Manual close purchase request precheck');
    Route::get('getWarehouseStockDetails', 'PurchaseRequestDetailsAPIController@getWarehouseStockDetails')->name('Get warehouse stock details');
    Route::post('getWarehouse', 'PurchaseRequestAPIController@getWarehouse')->name('Get purchase request warehouse');
    Route::get('getPurchaseRequestTotal', 'PurchaseRequestAPIController@getPurchaseRequestTotal')->name('Get purchase request total');
    Route::get('exportPurchaseHistory', 'PurchaseOrderDetailsAPIController@exportPurchaseHistory')->name('Export Purchase History');
    Route::get('purchase_request_data', 'PurchaseRequestAPIController@show')->name('Get purchase request data for portal');

    Route::post('pull-mr-details', 'PurchaseRequestAPIController@pullMrDetails')->name('Pull mr details');
    Route::post('remove-pulled-mr-details', 'PulledItemFromMRController@removeMRDetails')->name('Remove pulled mr details');
    Route::post('allItemFinanceCategories', 'FinanceItemCategoryMasterAPIController@allItemFinanceCategories')->name('All items finance categories');
    Route::post('purchase-request-validate-item', 'PurchaseRequestAPIController@validateItem')->name('Purchase request validate item');
    Route::post('purchase-request-add-all-items', 'PurchaseRequestDetailsAPIController@addAllItemsToPurchaseRequest')->name('Purchase request add all items');
    Route::post('getPurchaseRequestByDocumentType', 'PurchaseRequestAPIController@getPurchaseRequestByDocumentType')->name('Get purchase request by document type');
    Route::post('createPrMaterialRequest', 'PurchaseRequestAPIController@createPrMaterialRequest')->name('Create pr material request');
    Route::post('get-item-qnty-by-pr', 'PurchaseRequestAPIController@getItemQntyByPR')->name('Get item quantity by pr');
    Route::post('purchase-request/remove-all-items/{id}', 'PurchaseRequestDetailsAPIController@removeAllItems')->name('Purchase request remove all items');
    Route::post('getPurchaseRequestReopen', 'PurchaseRequestAPIController@getPurchaseRequestReopen')->name('Get purchase request reopen');
    Route::post('getPurchaseRequestReferBack', 'PurchaseRequestAPIController@getPurchaseRequestReferBack')->name('Get purchase request refer back');
    Route::post('updateQtyOnOrder', 'PurchaseRequestDetailsAPIController@updateQtyOnOrder')->name('Update quantity on order');
    Route::post('getSegmentAllocatedItems', 'SegmentAllocatedItemAPIController@getSegmentAllocatedItems')->name('Get segment allocated items');
    Route::post('getSegmentAllocatedFormData', 'SegmentAllocatedItemAPIController@getSegmentAllocatedFormData')->name('Get segment allocated form data');
    Route::post('allocateSegmentWiseItem', 'SegmentAllocatedItemAPIController@allocateSegmentWiseItem')->name('Allocate segment wise item');
    Route::post('prItemsUpload', 'PurchaseRequestDetailsAPIController@prItemsUpload')->name('Purchase request items upload');
    Route::post('getPoMasterAmendHistory', 'PurchaseOrderMasterRefferedHistoryAPIController@getPoMasterAmendHistory')->name('Get po master amend history');
    Route::post('getPrMasterAmendHistory', 'PurchaseRequestReferredAPIController@getPrMasterAmendHistory')->name('Get pr master amend history');
    Route::post('cancelPurchaseRequest', 'PurchaseRequestAPIController@cancelPurchaseRequest')->name('Cancel purchase request');
    Route::post('returnPurchaseRequest', 'PurchaseRequestAPIController@returnPurchaseRequest')->name('Return purchase request');
    Route::post('manualClosePurchaseRequest', 'PurchaseRequestAPIController@manualClosePurchaseRequest')->name('Manual close purchase request');
    Route::post('amendPurchaseRequest', 'PurchaseRequestAPIController@amendPurchaseRequest')->name('Amend purchase request');

    Route::resource('pulled-mr-details', 'PulledItemFromMRController');
    Route::resource('purchase_request_details', 'PurchaseRequestDetailsAPIController');
    Route::post('purchase_request_details_update/{id}', 'PurchaseRequestDetailsAPIController@update');
    Route::post('update_segment_allocated_items/{id}', 'SegmentAllocatedItemAPIController@update');
    Route::post('delete_segment_allocated_items/{id}', 'SegmentAllocatedItemAPIController@destroy');
    Route::post('purchase_request_details_delete/{id}', 'PurchaseRequestDetailsAPIController@destroy');
    Route::post('purchase_requests/{id}', 'PurchaseRequestAPIController@update');
    Route::resource('purchase_requests', 'PurchaseRequestAPIController');
    Route::resource('item-specification', 'ItemSpecificationController');
    Route::get('item-specification-portal/{id}', 'ItemSpecificationController@show');
    Route::resource('segment_allocated_items', 'SegmentAllocatedItemAPIController');
    Route::resource('purchaseRequestReferreds', 'PurchaseRequestReferredAPIController');
});

//Report Open Requests
Route::group([], function() {
    Route::post('getReportOpenRequest', 'PurchaseRequestAPIController@getReportOpenRequest')->name('Get report open request');
    Route::post('exportReportOpenRequest', 'PurchaseRequestAPIController@exportReportOpenRequest')->name('Export report open request');
});


//Purchase Order
Route::group([], function() {
    Route::get('poCheckDetailExistinGrv', 'ProcumentOrderAPIController@poCheckDetailExistinGrv')->name('Po check detail exist in grv');
    Route::get('amendProcurementOrderPreCheck', 'ProcumentOrderAPIController@amendProcurementOrderPreCheck')->name('Amend procurement order precheck');
    Route::get('segment/projects', 'ProcumentOrderAPIController@getProjectsBySegment')->name('Get projects by segment');
    Route::get('getItemsByProcumentOrder', 'PurchaseOrderDetailsAPIController@getItemsByProcumentOrder')->name('Get items by procurement order');
    Route::get('getItemsOptionForProcumentOrder', 'ProcumentOrderAPIController@getItemsOptionForProcumentOrder')->name('Get items option for procurement order');
    Route::get('getShippingAndInvoiceDetails', 'ProcumentOrderAPIController@getShippingAndInvoiceDetails')->name('Get shipping and invoice details');
    Route::get('getProcumentOrderPaymentTerms', 'PoPaymentTermsAPIController@getProcumentOrderPaymentTerms')->name('Get procurement order payment terms');
    Route::get('getProcumentOrderPaymentTermConfigs', 'PoPaymentTermsAPIController@getProcumentOrderPaymentTermConfigs')->name('Get procurement order payment term configs');
    Route::get('procumentOrderDetailTotal', 'ProcumentOrderAPIController@procumentOrderDetailTotal')->name('Procurement order detail total');
    Route::get('poPaymentTermsAdvanceDetailView', 'PoAdvancePaymentAPIController@poPaymentTermsAdvanceDetailView')->name('Po payment terms advance detail view');
    Route::get('getLogisticsItemsByProcumentOrder', 'PoAdvancePaymentAPIController@loadPoPaymentTermsLogistic')->name('Get logistics items by procurement order');
    Route::get('checkEOSPolicyAndSupplier', 'ProcumentOrderAPIController@checkEOSPolicyAndSupplier')->name('Check EOS policy and supplier');
    Route::get('getVatCategoryFormData', 'TaxVatCategoriesAPIController@getVatCategoryFormData')->name('Get vat category form data');
    Route::get('downloadPoItemUploadTemplate', 'ProcumentOrderAPIController@downloadPoItemUploadTemplate')->name('Download procurement order item upload template');
    Route::get('getPurchaseRequestForPO', 'PurchaseRequestAPIController@getPurchaseRequestForPO')->name('Get purchase request for procurement order');
    Route::get('getPurchaseRequestDetailForPO', 'PurchaseRequestDetailsAPIController@getPurchaseRequestDetailForPO')->name('Get purchase request detail for procurement order');
    Route::get('getGoodReceivedNoteDetailsForPO', 'ProcumentOrderAPIController@getGoodReceivedNoteDetailsForPO')->name('Get good recieved note details for procurement order');
    Route::get('getInvoiceDetailsForPO', 'ProcumentOrderAPIController@getInvoiceDetailsForPO')->name('Get invoice details for procurement order');
    Route::get('getPurchasePaymentStatusHistory', 'ProcumentOrderAPIController@getPurchasePaymentStatusHistory')->name('Get purchase payment status history');
    Route::get('procumentOrderPrHistory', 'ProcumentOrderAPIController@procumentOrderPrHistory')->name('Procurement order pr history');
    Route::get('getPoItemsForAmendHistory', 'PurchaseOrderDetailsRefferedHistoryAPIController@getPoItemsForAmendHistory')->name('Get procurement order items for amend history');
    Route::get('getPoPaymentTermsForAmendHistory', 'PoPaymentTermsRefferedbackAPIController@getPoPaymentTermsForAmendHistory')->name('Get procurement order payment terms for amend history');
    Route::get('getPoLogisticsItemsForAmendHistory', 'PurchaseOrderAdvPaymentRefferedbackAPIController@getPoLogisticsItemsForAmendHistory')->name('Get procurement order logistics items for amend history');
    Route::get('getPoAddonsForAmendHistory', 'PoAddonsRefferedBackAPIController@getPoAddonsForAmendHistory')->name('Get procurement order addons for amend history');
    Route::get('getAllStatusByPurchaseOrder', 'PurchaseOrderStatusAPIController@getAllStatusByPurchaseOrder')->name('Get all status by purchase order');
    Route::get('destroyPreCheck', 'PurchaseOrderStatusAPIController@destroyPreCheck')->name('destroy precheck');
    Route::get('ProcurementOrderAudit', 'ProcumentOrderAPIController@ProcurementOrderAudit')->name('Procurement order audit');
    Route::get('getLogisticPrintDetail', 'PoAdvancePaymentAPIController@getLogisticPrintDetail')->name('Get logistic print detail');
    Route::get('procumentOrderTotals', 'ProcumentOrderAPIController@procumentOrderTotals')->name('Procurement order totals');
    Route::get('getItemBulkUploadError', 'PoBulkUploadErrorLogAPIController@getItemBulkUploadError')->name('Procurement order totals');

    Route::put('poConfigDescriptionUpdate/{id}', 'ProcumentOrderAPIController@poConfigDescriptionUpdate')->name('Update purchase order configuration description');

    Route::post('updatePoConfigSelection', 'ProcumentOrderAPIController@updatePoConfigSelection')->name('Update po config selection for print');
    Route::post('allocateExpectedDeliveryDates', 'PoDetailExpectedDeliveryDateAPIController@allocateExpectedDeliveryDates')->name('Allocate expected delivery dates');
    Route::post('getAllocatedExpectedDeliveryDates', 'PoDetailExpectedDeliveryDateAPIController@getAllocatedExpectedDeliveryDates')->name('Get allocated expected delivery dates');
    Route::post('exportProcumentOrderMaster', 'ProcumentOrderAPIController@exportProcumentOrderMaster')->name('Export procurement order master');
    Route::post('amendProcumentSubWorkOrderReview', 'ProcumentOrderAPIController@amendProcumentSubWorkOrderReview')->name('Amend procurement sub work order review');
    Route::post('poExpectedDeliveryDateAmend', 'ProcumentOrderAPIController@poExpectedDeliveryDateAmend')->name('Po expected delivery date amend');
    Route::post('getProcumentOrderAllAmendments', 'ProcumentOrderAPIController@getProcumentOrderAllAmendments')->name('Get procurement order all amendments');
    Route::post('procumentOrderCancel', 'ProcumentOrderAPIController@procumentOrderCancel')->name('Procurement order cancel');
    Route::post('procumentOrderReturnBack', 'ProcumentOrderAPIController@procumentOrderReturnBack')->name('Procurement order return back');
    Route::post('manualCloseProcurementOrder', 'ProcumentOrderAPIController@manualCloseProcurementOrder')->name('Manual close procurement order');
    Route::post('manualCloseProcurementOrderPrecheck', 'ProcumentOrderAPIController@manualCloseProcurementOrderPrecheck')->name('Manual close procurement order precheck');
    Route::post('amendProcurementOrder', 'ProcumentOrderAPIController@amendProcurementOrder')->name('Amend procurement order');
    Route::post('procumentOrderChangeSupplier', 'ProcumentOrderAPIController@procumentOrderChangeSupplier')->name('Procurement order change supplier');
    Route::post('updateGRVLogistic', 'ProcumentOrderAPIController@updateGRVLogistic')->name('Update grv logistic');
    Route::post('procumentOrderSegmentchk', 'ProcumentOrderAPIController@procumentOrderSegmentchk')->name('Procurement order segment check');
    Route::post('advancePaymentTermCancel', 'PoAdvancePaymentAPIController@advancePaymentTermCancel')->name('Advance payment term cancel');
    Route::post('procumentOrderDeleteAllDetails', 'PurchaseOrderDetailsAPIController@procumentOrderDeleteAllDetails')->name('Procurement order delete all details');
    Route::post('procumentOrderTotalDiscountUD', 'PurchaseOrderDetailsAPIController@procumentOrderTotalDiscountUD')->name('Procurement order total discount UD');
    Route::post('procumentOrderTotalTaxUD', 'PurchaseOrderDetailsAPIController@procumentOrderTotalTaxUD')->name('Procurement order total tax UD');
    Route::post('unlinkLogistic', 'PoAdvancePaymentAPIController@unlinkLogistic')->name('Unlink logistic');
    Route::post('getProcurementOrderReopen', 'ProcumentOrderAPIController@getProcurementOrderReopen')->name('Get procurement order reopen');
    Route::post('procumentOrderPRAttachment', 'ProcumentOrderAPIController@procumentOrderPRAttachment')->name('Procurement order PR Attachment');
    Route::post('getProcurementOrderReferBack', 'ProcumentOrderAPIController@getProcurementOrderReferBack')->name('Get procurement order refer back');
    Route::post('updateSentSupplierDetail', 'ProcumentOrderAPIController@updateSentSupplierDetail')->name('Update sent supplier detail');
    Route::post('updateAllPaymentTerms', 'PoPaymentTermsAPIController@updateAllPaymentTerms')->name('Update all payment terms');
    Route::post('amendProcumentSubWorkOrder', 'ProcumentOrderAPIController@amendProcumentSubWorkOrder')->name('Amend procurement sub work order');
    Route::post('mapLineItemPr', 'PurchaseRequestDetailsAPIController@mapLineItemPr')->name('Map line item pr');
    Route::post('validateItemAlllocationInPO', 'PurchaseOrderDetailsAPIController@validateItemAlllocationInPO')->name('Validate item allocation in procurement order');
    Route::post('purchase_order_details_frm_pr', 'PurchaseOrderDetailsAPIController@storePurchaseOrderDetailsFromPR')->name('Store purchase order details from pr');
    Route::post('getSupplierCatalogDetailBySupplierAllItem', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierAllItem')->name('Get supplier catalog detail by supplier all item');
    Route::post('poItemsUpload', 'ProcumentOrderAPIController@poItemsUpload')->name('procurement order items upload');
    Route::post('currencyConvert', 'CurrencyConversionAPIController@currencyConvert')->name('Currency convert');
    Route::post('storePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@storePoPaymentTermsLogistic')->name('Store procurement order payment terms logistic');
    Route::post('purchaseOrderStatusesSendEmail', 'PurchaseOrderStatusAPIController@purchaseOrderStatusesSendEmail')->name('Purchase order statuses send email');
    Route::post('purchaseOrderValidateItem', 'PurchaseOrderDetailsAPIController@purchaseOrderValidateItem')->name('Procurement order validate item');
    Route::post('purchaseOrderDetailsAddAllItems', 'PurchaseOrderDetailsAPIController@purchaseOrderDetailsAddAllItems')->name('Procurement order add all item');
    Route::post('deletePoItemUploadErrorLog/{id}', 'PoBulkUploadErrorLogAPIController@deletePoItemUploadErrorLog');

    Route::resource('po_detail_expected_delivery_dates', 'PoDetailExpectedDeliveryDateAPIController');
    Route::resource('procurement-order', 'ProcumentOrderAPIController');
    Route::resource('purchase_order_details', 'PurchaseOrderDetailsAPIController');
    Route::resource('procumentOrderPaymentTermsCRUD', 'PoPaymentTermsAPIController');
    Route::resource('po_addons', 'PoAddonsAPIController');
    Route::resource('procumentOrderPaymentTermsUD', 'PoPaymentTermsAPIController');
    Route::resource('poPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');
    Route::resource('procumentOrderAdvpaymentUD', 'PoAdvancePaymentAPIController');
    Route::resource('poMaster_reffered_histories', 'PurchaseOrderMasterRefferedHistoryAPIController');
    Route::resource('purchase_order_statuses', 'PurchaseOrderStatusAPIController');
    Route::resource('purchase_order_categories', 'PurchaseOrderCategoryAPIController');

});

//Report Open Requests
Route::group([], function() {
    Route::get('getReportSavingFliterData', 'ProcumentOrderAPIController@getReportSavingFliterData')->name('Get Report Saving Fliter Data');
    Route::post('getItemSavingReport', 'ReportAPIController@getItemSavingReport')->name('Get Item Saving Report');
    Route::post('exportExcelSavingReport', 'ReportAPIController@exportExcelSavingReport')->name('Export Excel SavingReport');
});

//Report Spent Analysis Report
Route::group([], function() {
    Route::post('reportSpentAnalysisHeader', 'ProcumentOrderAPIController@reportSpentAnalysisHeader')->name('Report Spent Analysis Header');
    Route::get('reportSpentAnalysisBySupplierFilter', 'ProcumentOrderAPIController@reportSpentAnalysisBySupplierFilter')->name('Report Spent Analysis By Supplier Filter');
    Route::post('reportSpentAnalysis', 'ProcumentOrderAPIController@reportSpentAnalysis')->name('Report Spent Analysis');
    Route::post('reportSpentAnalysisExport', 'ProcumentOrderAPIController@reportSpentAnalysisExport')->name('Report Spent Analysis Export');

});

//Report Pr to grv
Route::group([], function() {
    Route::get('reportPrToGrvFilterOptions', 'PurchaseRequestAPIController@reportPrToGrvFilterOptions')->name('Report Filter Options');
    Route::post('reportPrToGrv', 'PurchaseRequestAPIController@reportPrToGrv')->name('Report PR To Grv');
    Route::post('exportPrToGrvReport', 'PurchaseRequestAPIController@exportPrToGrvReport')->name('Export PR To Grv');


});

//Report Po to payment
Route::group([], function() {
    Route::get('reportPoToPaymentFilterOptions', 'ProcumentOrderAPIController@reportPoToPaymentFilterOptions')->name('Report po to payment filter options');
    Route::post('reportPoToPayment', 'ProcumentOrderAPIController@reportPoToPayment')->name('Report po to payment');
    Route::post('exportPoToPaymentReport', 'ProcumentOrderAPIController@exportPoToPaymentReport')->name('Export po to payment report');
});

//Review

//Procurement Order
Route::group([], function() {
    Route::post('getSupplierCatalogDetailBySupplierItemForPo', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierItemForPo')->name('Get supplier catalog detail by supplier item for pro');
    Route::get('getGRVBasedPODropdowns', 'ProcumentOrderAPIController@getGRVBasedPODropdowns')->name('Get grv based po dropdowns');
});

//Masters

//Purchase Address
Route::group([], function() {
    Route::post('getAllAddresses', 'AddressAPIController@getAllAddresses')->name('Get all addresses');
    Route::get('getAddressFormData', 'AddressAPIController@getAddressFormData')->name('Get address form data');

    Route::resource('addresses', 'AddressAPIController');
});

//Supplier Evaluation
Route::group([], function() {
    Route::post('getAllSupplierEvaluationsList', 'SupplierEvaluationController@getAllSupplierEvaluations')->name('Get all supplier evaluations');
    Route::get('getSupplierEvaluationFormData', 'SupplierEvaluationController@getSupplierEvaluationFormData')->name("Get supplier evaluation form data");
});

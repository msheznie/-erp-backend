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
    Route::get('getWarehouse', 'PurchaseRequestAPIController@getWarehouse')->name('Get purchase request warehouse');

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
    Route::resource('purchase_requests', 'PurchaseRequestAPIController');
    Route::resource('item-specification', 'ItemSpecificationController');
    Route::resource('segment_allocated_items', 'SegmentAllocatedItemAPIController');
    Route::resource('purchaseRequestReferreds', 'PurchaseRequestReferredAPIController');
});

//Report Open Requests
Route::group([], function() {
    Route::post('getReportOpenRequest', 'PurchaseRequestAPIController@getReportOpenRequest')->name('Get report open request');
    Route::post('exportReportOpenRequest', 'PurchaseRequestAPIController@exportReportOpenRequest')->name('Export report open request');
});
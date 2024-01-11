<?php

/**
 * This file contains inventory module related routes
 * 
 * 
 * */


//approval
Route::group([], function () {
    Route::post('getGRVMasterApproval', 'GRVMasterAPIController@getGRVMasterApproval')->name("Get pending for approval - GRV");
    Route::post('getApprovedGRVForCurrentUser', 'GRVMasterAPIController@getApprovedGRVForCurrentUser')->name("Get approved - GRV");
    Route::post('approveGoodReceiptVoucher', 'GRVMasterAPIController@approveGoodReceiptVoucher')->name("Approve GRV");
    Route::post('rejectGoodReceiptVoucher', 'GRVMasterAPIController@rejectGoodReceiptVoucher')->name("Reject GRV");

    Route::post('getInvReclassificationApprovalByUser', 'InventoryReclassificationAPIController@getInvReclassificationApprovalByUser')->name("Get pending for approval - Inventory reclassification");
    Route::post('getInvReclassificationApprovedByUser', 'InventoryReclassificationAPIController@getInvReclassificationApprovedByUser')->name("Get approved - Inventory reclassification");
    Route::get('getInvReclassificationFormData', 'InventoryReclassificationAPIController@getInvReclassificationFormData')->name("Get Inv Reclassification Form Data");

    Route::post('getMaterielIssueApprovalByUser', 'ItemIssueMasterAPIController@getMaterielIssueApprovalByUser')->name("Get pending for approval - Material issue");
    Route::post('getMaterielIssueApprovedByUser', 'ItemIssueMasterAPIController@getMaterielIssueApprovedByUser')->name("Get approved - Material issue");
    Route::get('getMaterielIssueFormData', 'ItemIssueMasterAPIController@getMaterielIssueFormData')->name("Get Materiel Issue Form Data");
    Route::get('getTypeheadActiveEmployees', 'ItemIssueMasterAPIController@getTypeheadActiveEmployees')->name("Get type head active employees");

    Route::post('getAllNotApprovedRequestByUser', 'MaterielRequestAPIController@getAllNotApprovedRequestByUser')->name("Get pending for approval - Material Request");
    Route::post('getApprovedMaterielRequestsByUser', 'MaterielRequestAPIController@getApprovedMaterielRequestsByUser')->name("Get approved - Material Request");
    Route::get('getRequestFormData', 'MaterielRequestAPIController@getRequestFormData')->name("Get Material Request Form Data");

    Route::post('getMaterielReturnApprovalByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovalByUser')->name("Get pending for approval - Material Return");
    Route::post('getMaterielReturnApprovedByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovedByUser')->name("Get approved - Material Return");
    Route::get('getMaterielReturnFormData', 'ItemReturnMasterAPIController@getMaterielReturnFormData')->name("Get Materiel Return Form Data");

    Route::post('getPurchaseReturnApprovalByUser', 'PurchaseReturnAPIController@getPurchaseReturnApprovalByUser')->name("Get pending for approval - Purchase Return");
    Route::post('getPurchaseReturnApprovedByUser', 'PurchaseReturnAPIController@getPurchaseReturnApprovedByUser')->name("Get approved - Purchase Return");
    Route::get('getPurchaseReturnFormData', 'PurchaseReturnAPIController@getPurchaseReturnFormData')->name("Get Purchase Return Form Data");

    Route::post('getStockAdjustmentApprovalByUser', 'StockAdjustmentAPIController@getStockAdjustmentApprovalByUser')->name("Get pending for approval - Stock Adjustment");
    Route::post('getStockAdjustmentApprovedByUser', 'StockAdjustmentAPIController@getStockAdjustmentApprovedByUser')->name("Get approved - Stock Adjustment");
    Route::get('getStockAdjustmentFormData', 'StockAdjustmentAPIController@getStockAdjustmentFormData')->name("Get Stock Adjustment Form Data");

    Route::post('getStockCountApprovalByUser', 'StockCountAPIController@getStockCountApprovalByUser')->name("Get pending for approval - Stock Count");
    Route::post('getStockCountApprovedByUser', 'StockCountAPIController@getStockCountApprovedByUser')->name("Get approved - Stock Count");

    Route::post('getStockReceiveApproval', 'StockReceiveAPIController@getStockReceiveApproval')->name("Get pending for approval - Stock Receive");
    Route::post('getApprovedSRForCurrentUser', 'StockReceiveAPIController@getApprovedSRForCurrentUser')->name("Get approved - Stock Receive");

    Route::post('getStockTransferApproval', 'StockTransferAPIController@getStockTransferApproval')->name("Get pending for approval - Stock Transfer");
    Route::post('getApprovedSTForCurrentUser', 'StockTransferAPIController@getApprovedSTForCurrentUser')->name("Get approved - Stock Transfer");
    Route::post('approveStockTransfer', 'StockTransferAPIController@approveStockTransfer')->name("Approve Stock Transfer");
});

//GRV Trans
Route::group([], function () {
    Route::resource('grvMasterRefferedbacksCRUD', 'GrvMasterRefferedbackAPIController');
    Route::resource('g_r_v_details', 'GRVDetailsAPIController');
    Route::resource('goodReceiptVoucherCRUD', 'GRVMasterAPIController');

    Route::get('getGRVFormData', 'GRVMasterAPIController@getGRVFormData')->name("Get GRV form data");
    Route::get('getGRVDetailsAmendHistory', 'GrvDetailsRefferedbackAPIController@getGRVDetailsAmendHistory')->name("Get GRV Details Amend History");
    Route::get('goodReceiptVoucherAudit', 'GRVMasterAPIController@goodReceiptVoucherAudit')->name("Good Receipt Voucher Audit");
    Route::get('segment/projects', 'ProcumentOrderAPIController@getProjectsBySegment')->name("Get Projects By Segment");
    Route::get('getBinLocationsByWarehouse', 'GRVMasterAPIController@getBinLocationsByWarehouse')->name("Get Bin Locations By Warehouse");
    Route::get('getItemsByGRVMaster', 'GRVDetailsAPIController@getItemsByGRVMaster')->name("Get Items By GRV Master");
    Route::get('getLogisticsItemsByGRV', 'PoAdvancePaymentAPIController@loadPoPaymentTermsLogisticForGRV')->name("Load Po Payment Terms Logistic For GRV");
    Route::get('getItemsOptionForGRV', 'GRVMasterAPIController@getItemsOptionForGRV')->name("Get Items Option For GRV");
    Route::get('getLogisticPrintDetail', 'PoAdvancePaymentAPIController@getLogisticPrintDetail')->name("Get Logistic Print Detail");
    Route::get('purchaseOrderForGRV', 'ProcumentOrderAPIController@purchaseOrderForGRV')->name("Purchase Order For GRV");
    Route::get('getPurchaseOrderDetailForGRV', 'PurchaseOrderDetailsAPIController@getPurchaseOrderDetailForGRV')->name("Get Purchase Order Detail For GRV");
    Route::get('getGRVDetailsReversalHistory', 'GrvDetailsRefferedbackAPIController@getGRVDetailsReversalHistory')->name("Get GRV Details Reversal History");
    Route::get('purchaseReturnForGRV', 'PurchaseReturnAPIController@purchaseReturnForGRV')->name("Purchase Return For GRV");
    Route::get('getPurchaseReturnDetailForGRV', 'PurchaseReturnAPIController@getPurchaseReturnDetailForGRV')->name("Get Purchase Return Detail For GRV");
    Route::get('grvReturnDetails', 'PurchaseReturnDetailsAPIController@grvReturnDetails')->name("Grv Return Details");

    Route::post('getGoodReceiptVoucherMasterView', 'GRVMasterAPIController@getGoodReceiptVoucherMasterView')->name("Get Good Receipt Voucher Master View");
    Route::post('getGRVMasterAmendHistory', 'GrvMasterRefferedbackAPIController@getGRVMasterAmendHistory')->name("Get GRV Master Amend History");
    Route::post('GRVSegmentChkActive', 'GRVMasterAPIController@GRVSegmentChkActive')->name("GRV Segment Chk Active");
    Route::post('grvDeleteAllDetails', 'GRVDetailsAPIController@grvDeleteAllDetails')->name("Grv Delete All Details");
    Route::post('pullPOAttachment', 'GRVMasterAPIController@pullPOAttachment')->name("Pull PO Attachment");
    Route::post('getGoodReceiptVoucherReopen', 'GRVMasterAPIController@getGoodReceiptVoucherReopen')->name("Get Good Receipt Voucher Reopen");
    Route::post('storeGRVDetailsDirect', 'GRVDetailsAPIController@storeGRVDetailsDirect')->name("Store GRV Details Direct");
    Route::post('updateGRVDetailsDirect', 'GRVDetailsAPIController@updateGRVDetailsDirect')->name("Update GRV Details Direct");
    Route::post('getGoodReceiptVoucherAmend', 'GRVMasterAPIController@getGoodReceiptVoucherAmend')->name("Get Good Receipt Voucher Amend");
    Route::post('grvMarkupUpdate', 'GRVDetailsAPIController@grvMarkupUpdate')->name("Grv Markup Update");
    Route::post('grvMarkupfinalyze', 'GRVMasterAPIController@grvMarkupfinalyze')->name("Grv Markup finalyze");
    Route::post('storeGRVDetailsFromPO', 'GRVDetailsAPIController@storeGRVDetailsFromPO')->name("Store GRV Details From PO");
    Route::post('storeGRVDetailsFromPR', 'GRVDetailsAPIController@storeGRVDetailsFromPR')->name("Store GRV Details From PR");
});


//Material request Trans
Route::group([], function () {
    Route::resource('materiel_requests', 'MaterielRequestAPIController');
    Route::resource('request_reffered_back', 'RequestRefferedBackAPIController');
    Route::resource('request_details_reffered_backs', 'RequestDetailsRefferedBackAPIController');
    Route::resource('materiel_request_details', 'MaterielRequestDetailsAPIController');
    Route::resource('item_issue_details', 'ItemIssueDetailsAPIController');
    Route::resource('item_issue_masters', 'ItemIssueMasterAPIController');

    Route::get('materielRequestAudit', 'MaterielRequestAPIController@materielRequestAudit')->name("Materiel Request Audit");
    Route::get('getItemRequestDetailsReferBack', 'RequestDetailsRefferedBackAPIController@getItemRequestDetailsReferBack')->name("Get Item Request Details ReferBack");
    Route::get('getItemWarehouseQnty', 'MaterielRequestDetailsAPIController@getItemWarehouseQnty')->name("Get Item Warehouse Qnty");
    Route::get('cancelMaterielRequest', 'MaterielRequestAPIController@cancelMaterielRequest')->name("Cancel Materiel Request");
    Route::get('update-qnty-by-location', 'MaterielRequestAPIController@updateQntyByLocation')->name("Update Qnty By Location");
    Route::get('materiel_request/details/{id}', 'MaterielRequestAPIController@getMaterielRequestDetails')->name("Get Materiel Request Details");
    Route::get('returnMaterialRequestPreCheck', 'MaterielRequestAPIController@returnMaterialRequestPreCheck')->name("Return Material Request Pre Check");
    Route::get('getItemsByMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsByMaterielRequest')->name("Get Items By Materiel Request");
    Route::get('getItemsByMaterielRequestByLimit', 'MaterielRequestDetailsAPIController@getItemsByMaterielRequestByLimit')->name("Get Items By Materiel Request By Limit");
    Route::get('getItemsOptionForMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsOptionForMaterielRequest')->name("Get Items Option For Materiel Request");
    Route::get('materiel_requests/{id}/purchase-requests', 'MaterielRequestAPIController@checkPurcahseRequestExist')->name("Check Purcahse Request Exist");
    Route::get('purchase_requests/check/product/{itemCode}/{companySystemID}', 'PurchaseRequestAPIController@checkProductExistInIssues')->name("Check Product Exist In Issues");
    Route::get('material-issue-by-refno', 'ItemIssueMasterAPIController@getMaterialIssueByRefNo')->name("Get Material Issue By RefNo");
    Route::get('material-issue/check/product/{id}/{companySystemID}', 'ItemIssueMasterAPIController@checkProductExistInIssues')->name("Check Product Exist In Issues");
    Route::post('item-master/check/item', 'ItemIssueMasterAPIController@checkProductExistInItemMaster')->name("Check Item Exist In Item Master");

    Route::get('getCompanyLocalCurrency', 'CurrencyMasterAPIController@getCompanyLocalCurrency')->name("Get Company Local Currency");

    Route::post('returnMaterialRequest', 'MaterielRequestAPIController@returnMaterialRequest')->name("Return Material Request");
    Route::post('getReferBackHistoryByRequest', 'RequestRefferedBackAPIController@getReferBackHistoryByRequest')->name("Get ReferBack History By Request");
    Route::post('requestReopen', 'MaterielRequestAPIController@requestReopen')->name("Material Request Reopen");
    Route::post('requestReferBack', 'MaterielRequestAPIController@requestReferBack')->name("Material Request ReferBack");
    Route::post('getAllRequestByCompany', 'MaterielRequestAPIController@getAllRequestByCompany')->name("Get All Material Request By Company");
    Route::post('requestDetailsAddAllItems', 'MaterielRequestDetailsAPIController@requestDetailsAddAllItems')->name('Material request add all item');
    Route::post('materialRequestValidateItem', 'MaterielRequestDetailsAPIController@materialRequestValidateItem')->name('Material request validate item');
    Route::post('material_requests/remove_all_items/{id}', 'MaterielRequestDetailsAPIController@removeAllItems')->name('Material request remove all items');

});

//Material Issue Trans
Route::group([], function () {
    Route::resource('item_issue_masters', 'ItemIssueMasterAPIController');
    Route::resource('materiel_requests', 'MaterielRequestAPIController');
    Route::resource('item_issue_referred_back', 'ItemIssueMasterRefferedBackAPIController');
    Route::resource('item_issue_details_reffered_backs', 'ItemIssueDetailsRefferedBackAPIController');
    Route::resource('materiel_request_details', 'MaterielRequestDetailsAPIController');
    Route::resource('item_issue_details', 'ItemIssueDetailsAPIController');

    Route::get('allMaterielRequestNotSelectedForIssue', 'ItemIssueMasterAPIController@getAllMaterielRequestNotSelectedForIssueByCompany')->name("Get All Materiel Request Not Selected For Issue By Company");
    Route::get('getMaterielIssueAudit', 'ItemIssueMasterAPIController@getMaterielIssueAudit')->name("Get Materiel Issue Audit");
    Route::get('material-issue/update-qnty-by-location', 'ItemIssueMasterAPIController@updateQntyByLocation')->name("Update Qnty By Location");
    Route::get('checkManWareHouse', 'ItemIssueMasterAPIController@checkManWareHouse')->name("Check Man Ware House");
    Route::get('getItemsByMaterielIssue', 'ItemIssueDetailsAPIController@getItemsByMaterielIssue')->name("Get Items By Materiel Issue");
    Route::get('getItemsByMaterielIssueByLimit', 'ItemIssueDetailsAPIController@getItemsByMaterielIssueByLimit')->name("Get Items By Materiel Issue By Limit");
    Route::get('getItemsOptionsMaterielIssue', 'ItemIssueDetailsAPIController@getItemsOptionsMaterielIssue')->name("Get Items Options Materiel Issue");
    Route::get('getItemIssueDetailsReferBack', 'ItemIssueDetailsRefferedBackAPIController@getItemIssueDetailsReferBack')->name("Get Item Issue Details ReferBack");
    Route::post('materialIssuetDetailsAddAllItems', 'ItemIssueDetailsAPIController@materialIssuetDetailsAddAllItems')->name('Material issue add all item');
    Route::post('materialIssueValidateItem', 'ItemIssueDetailsAPIController@materialIssueValidateItem')->name('Material issue validate item');
    Route::post('material_issue/remove_all_items/{id}', 'ItemIssueDetailsAPIController@removeAllItems')->name('Material Issue remove all items');

    Route::post('materielIssueReopen', 'ItemIssueMasterAPIController@materielIssueReopen')->name("Material Issue Reopen");
    Route::post('materielIssueReferBack', 'ItemIssueMasterAPIController@materielIssueReferBack')->name("Material Issue Refer Back");
    Route::post('getAllMaterielIssuesByCompany', 'ItemIssueMasterAPIController@getAllMaterielIssuesByCompany')->name("Get All Material Issue By Company");
    Route::post('getReferBackHistoryByMaterielIssues', 'ItemIssueMasterRefferedBackAPIController@getReferBackHistoryByMaterielIssues')->name("Get ReferBack History By Materiel Issues");
});

//Material return Trans
Route::group([], function () {
    Route::resource('item_return_masters', 'ItemReturnMasterAPIController');
    Route::resource('item_return_details', 'ItemReturnDetailsAPIController');
    Route::resource('mr_master_referred_back', 'ItemReturnMasterRefferedBackAPIController');
    Route::resource('mr_details_reffered_backs', 'ItemReturnDetailsRefferedBackAPIController');

    Route::get('getMaterielReturnAudit', 'ItemReturnMasterAPIController@getMaterielReturnAudit')->name("Get Materiel Return Audit");
    Route::get('getItemsByMaterielReturn', 'ItemReturnDetailsAPIController@getItemsByMaterielReturn')->name("Get Items By Materiel Return");
    Route::get('getItemsOptionsMaterielReturn', 'ItemReturnDetailsAPIController@getItemsOptionsMaterielReturn')->name("Get Items Options Materiel Return");
    Route::get('getItemReturnDetailsReferBack', 'ItemReturnDetailsRefferedBackAPIController@getItemReturnDetailsReferBack')->name("Get Item Return Details Referback");

    Route::post('materielReturnReopen', 'ItemReturnMasterAPIController@materielReturnReopen')->name("Materiel Return Reopen");
    Route::post('materielReturnReferBack', 'ItemReturnMasterAPIController@materielReturnReferBack')->name("Materiel Return Referback");
    Route::post('getAllMaterielReturnByCompany', 'ItemReturnMasterAPIController@getAllMaterielReturnByCompany')->name("Get All Materiel Return By Company");
    Route::post('getReferBackHistoryByMaterielReturn', 'ItemReturnMasterRefferedBackAPIController@getReferBackHistoryByMaterielReturn')->name("Get Referback History By Materiel Return");
});

//Stock Transfer
Route::group([], function () {
    Route::resource('stock_transfer_details', 'StockTransferDetailsAPIController');
    Route::resource('stock_transfers', 'StockTransferAPIController');
    Route::resource('stock_transfer_reffered_backs', 'StockTransferRefferedBackAPIController');
    Route::resource('st_details_reffered_backs', 'StockTransferDetailsRefferedBackAPIController');
    
    Route::get('getStockTransferFormData', 'StockTransferAPIController@getStockTransferFormData')->name("Get Stock Transfer Form Data");
    Route::get('getStockTransferDetails', 'StockTransferDetailsAPIController@getStockTransferDetails')->name("Get Stock Transfer Details");
    Route::get('getItemsOptionForStockTransfer', 'StockTransferAPIController@getItemsOptionForStockTransfer')->name("Get Items Option For Stock Transfer");
    Route::get('StockTransferAudit', 'StockTransferAPIController@StockTransferAudit')->name("Stock Transfer Audit");
    Route::get('getStockTransferForReceive', 'StockTransferAPIController@getStockTransferForReceive')->name("Get Stock Transfer For Receive");
    Route::get('getStockTransferDetailsByMaster', 'StockTransferAPIController@getStockTransferDetailsByMaster')->name("Get Stock Transfer Details By Master");
    Route::get('getStockTransferDetailsReferBack', 'StockTransferDetailsRefferedBackAPIController@getStockTransferDetailsReferBack')->name("Get Stock Transfer Details ReferBack");

    Route::post('rejectStockTransfer', 'StockTransferAPIController@rejectStockTransfer')->name("Reject Stock Transfer");
    Route::post('stockTransferReferBack', 'StockTransferAPIController@stockTransferReferBack')->name("Stock Transfer ReferBack");
    Route::post('getReferBackHistoryByStockTransfer', 'StockTransferAPIController@getReferBackHistoryByStockTransfer')->name("Get ReferBack History By Stock Transfer");
    Route::post('stock_transfer_reffered_backs', 'StockTransferAPIController@stockTransferRefferedBacks')->name("Stock Transfer RefferedBacks");
    Route::post('stockTransferReopen', 'StockTransferAPIController@stockTransferReopen')->name("Stock Transfer Reopen");
    Route::post('getAllStockTransferByCompany', 'StockTransferAPIController@getStockTransferMasterView')->name("Get All Stock Transfer By Company");
    Route::post('getReferBackHistoryByStockTransfer', 'StockTransferRefferedBackAPIController@getReferBackHistoryByStockTransfer')->name("Get ReferBack History By Stock Transfer");

});

//Stock Receive
Route::group([], function () {
    Route::resource('stock_receive_details', 'StockReceiveDetailsAPIController');
    Route::resource('stock_receives', 'StockReceiveAPIController');
    Route::resource('sr_details_reffered_backs', 'StockReceiveDetailsRefferedBackAPIController');
    Route::resource('stock_receive_reffered_backs', 'StockReceiveRefferedBackAPIController');
    
    Route::get('getStockReceiveFormData', 'StockReceiveAPIController@getStockReceiveFormData')->name("Get Stock Receive Form Data");
    Route::get('stockReceiveAudit', 'StockReceiveAPIController@stockReceiveAudit')->name("Stock Receive Audit");
    Route::get('getStockReceiveDetailsByMaster', 'StockReceiveDetailsAPIController@getStockReceiveDetailsByMaster')->name("Get Stock Receive Details By Master");
    Route::get('getStockReceiveDetailsReferBack', 'StockReceiveDetailsRefferedBackAPIController@getStockReceiveDetailsReferBack')->name("Get StockReceive Details ReferBack");
    Route::get('getItemsOptionForStockReceive', 'StockReceiveAPIController@getItemsOptionForStockReceive')->name("Get Items Option For Stock Receive");

    Route::post('stockReceiveReferBack', 'StockReceiveAPIController@stockReceiveReferBack')->name("Stock Receive ReferBack");
    Route::post('getAllStockReceiveByCompany', 'StockReceiveAPIController@getAllStockReceiveByCompany')->name("Get All Stock Receive By Company");
    Route::post('stockReceiveReopen', 'StockReceiveAPIController@stockReceiveReopen')->name("Stock Receive Reopen");
    Route::post('storeReceiveDetailsFromTransfer', 'StockReceiveDetailsAPIController@storeReceiveDetailsFromTransfer')->name("Store Receive Details From Transfer");
    Route::post('srPullFromTransferPreCheck', 'StockReceiveAPIController@srPullFromTransferPreCheck')->name("Sr Pull From Transfer Pre Check");
    Route::post('getReferBackHistoryByStockReceive', 'StockReceiveRefferedBackAPIController@getReferBackHistoryByStockReceive')->name("Get ReferBack History By Stock Receive");

});

//Stock Adjustment
Route::group([], function () {
    Route::resource('stock_adjustments', 'StockAdjustmentAPIController');
    Route::resource('stock_adjustment_details', 'StockAdjustmentDetailsAPIController');
    Route::resource('stockAdjustmentRefferedBack', 'StockAdjustmentRefferedBackAPIController');
    Route::resource('sAdjustmentDetailsRefferedBack', 'StockAdjustmentDetailsRefferedBackAPIController');
    Route::resource('stock_counts', 'StockCountAPIController');
    Route::resource('stock_count_details', 'StockCountDetailAPIController');
    Route::resource('stock_count_reffered_backs', 'StockCountRefferedBackAPIController');
    Route::resource('stockcountdetailsreffered', 'StockCountDetailsRefferedBackAPIController');

    Route::get('getSADetailsReferBack', 'StockAdjustmentDetailsRefferedBackAPIController@getSADetailsReferBack')->name("Get SA Details ReferBack");
    Route::get('getStockAdjustmentAudit', 'StockAdjustmentAPIController@getStockAdjustmentAudit')->name("Get Stock Adjustment Audit");
    Route::get('getItemsByStockAdjustment', 'StockAdjustmentDetailsAPIController@getItemsByStockAdjustment')->name("Get Items By Stock Adjustment");
    Route::get('getItemsOptionsStockAdjustment', 'StockAdjustmentDetailsAPIController@getItemsOptionsStockAdjustment')->name("Get Items Options Stock Adjustment");
    Route::get('stockCountAudit', 'StockCountAPIController@getStockCountAudit')->name("Get Stock Count Audit");
    Route::get('getItemsByStockCount', 'StockCountDetailAPIController@getItemsByStockCount')->name("Get Items By Stock Count");
    Route::get('getSCDetailsReferBack', 'StockCountDetailsRefferedBackAPIController@getSCDetailsReferBack')->name("Get SC Details ReferBack");

    Route::post('stockAdjustmentReferBack', 'StockAdjustmentAPIController@stockAdjustmentReferBack')->name("Stock Adjustment ReferBack");
    Route::post('stockAdjustmentReopen', 'StockAdjustmentAPIController@stockAdjustmentReopen')->name("Stock Adjustment Reopen");
    Route::post('getReferBackHistoryByStockAdjustments', 'StockAdjustmentRefferedBackAPIController@getReferBackHistoryByStockAdjustments')->name("Get ReferBack History By Stock Adjustments");
    Route::post('getAllStockAdjustmentsByCompany', 'StockAdjustmentAPIController@getAllStockAdjustmentsByCompany')->name("Get All Stock Adjustments By Company");
    Route::post('getAllStockCountsByCompany', 'StockCountAPIController@getAllStockCountsByCompany')->name("Get All Stock Counts By Company");
    Route::post('stockCountReopen', 'StockCountAPIController@stockCountReopen')->name("Stock Count Reopen");
    Route::post('stockCountReferBack', 'StockCountAPIController@stockCountReferBack')->name("Stock Count ReferBack");
    Route::post('removeAllStockCountItems', 'StockCountDetailAPIController@removeAllStockCountItems')->name("Remove All Stock Count Items");
    Route::post('getReferBackHistoryByStockCounts', 'StockCountRefferedBackAPIController@getReferBackHistoryByStockCounts')->name("Get ReferBack History By Stock Counts");
});

//Purchase Return
Route::group([], function () {
    Route::resource('purchase_return_details', 'PurchaseReturnDetailsAPIController');
    Route::resource('purchase_returns', 'PurchaseReturnAPIController');
    Route::resource('prMasterRefferedbacksCRUD', 'PurchaseReturnMasterRefferedBackAPIController');
    // Route::resource('purchase_return_details_reffered_backs', 'PurchaseReturnDetailsRefferedBackAPIController');
    Route::resource('purchase_return_logistics', 'PurchaseReturnLogisticAPIController');


    Route::get('getPurchaseReturnAudit', 'PurchaseReturnAPIController@getPurchaseReturnAudit')->name("Get Purchase Return Audit");            
    Route::get('grvForPurchaseReturn', 'PurchaseReturnAPIController@grvForPurchaseReturn')->name("Grv For Purchase Return");
    Route::get('grvDetailByMasterForPurchaseReturn', 'PurchaseReturnAPIController@grvDetailByMasterForPurchaseReturn')->name("Grv Detail By Master For Purchase Return");
    Route::get('getPRDetailsAmendHistory', 'PurchaseReturnDetailsRefferedBackAPIController@getPRDetailsAmendHistory')->name("Get PR Details Amend History");
    Route::get('getItemsByPurchaseReturnMaster', 'PurchaseReturnDetailsAPIController@getItemsByPurchaseReturnMaster')->name("Get Items By Purchase Return Master");

    Route::post('purchaseReturnSegmentChkActive', 'PurchaseReturnAPIController@purchaseReturnSegmentChkActive')->name("Purchase Return Segment Chk Active");
    Route::post('purchaseReturnReopen', 'PurchaseReturnAPIController@purchaseReturnReopen')->name("Purchase Return Reopen");
    Route::post('getPurchaseReturnByCompany', 'PurchaseReturnAPIController@getPurchaseReturnByCompany')->name("Get Purchase Return By Company");
    Route::post('getPurchaseReturnAmendHistory', 'PurchaseReturnMasterRefferedBackAPIController@getPurchaseReturnAmendHistory')->name("Get Purchase Return Amend History");
    Route::post('purchaseReturnAmend', 'PurchaseReturnAPIController@purchaseReturnAmend')->name("Purchase Return Amend");
    Route::post('storePurchaseReturnDetailsFromGRV', 'PurchaseReturnDetailsAPIController@storePurchaseReturnDetailsFromGRV')->name("Store Purchase Return Details From GRV");
    Route::post('purchaseReturnDeleteAllDetails', 'PurchaseReturnDetailsAPIController@purchaseReturnDeleteAllDetails')->name("Purchase Return Delete All Details");

});

/* INV Reports Start */
// Stock Ledger
Route::group([], function () {
    Route::resource('erp_item_ledgers', 'ErpItemLedgerAPIController');

    Route::get('getWarehouse', 'ErpItemLedgerAPIController@getWarehouse')->name('Get Warehouse');
    Route::get('getErpLedger', 'ErpItemLedgerAPIController@getErpLedger')->name('Get Erp Ledger');

    Route::post('getErpLedgerItems', 'ErpItemLedgerAPIController@getErpLedgerItems')->name('Get Erp Ledger Items');
    Route::post('validateStockLedgerReport', 'ErpItemLedgerAPIController@validateStockLedgerReport')->name('Validate Stock Ledger Report');
    Route::post('generateStockLedgerReport', 'ErpItemLedgerAPIController@generateStockLedgerReport')->name('Generate Stock Ledger Report');
    Route::post('exportStockLedgerReport', 'ErpItemLedgerAPIController@exportStockLedgerReport')->name('Export Stock Ledger Report');
    Route::post('generateStockLedger', 'ErpItemLedgerAPIController@generateStockLedger')->name('Generate Stock Ledger');
    Route::post('getItemStockDetails', 'ErpItemLedgerAPIController@getItemStockDetails')->name('Get Item Stock Details');   

});

// Stock Valuation
Route::group([], function () {
    Route::resource('erp_stock_valuation', 'InventoryReportAPIController');
    Route::resource('erp_item_ledgers', 'ErpItemLedgerAPIController');

    Route::get('getINVFilterData', 'InventoryReportAPIController@getInventoryFilterData')->name('Get Inventory Filter Data');

    Route::post('validateStockValuationReport', 'ErpItemLedgerAPIController@validateStockValuationReport')->name('Validate Stock Valuation Report');
    Route::post('generateStockValuationReport', 'ErpItemLedgerAPIController@generateStockValuationReport')->name('Generate Stock Valuation Report');
    Route::post('exportStockEvaluation', 'ErpItemLedgerAPIController@exportStockEvaluation')->name('Export Stock Evaluation');
    Route::post('validateStockTakingReport', 'ErpItemLedgerAPIController@validateStockTakingReport')->name('Validate Stock Taking Report');

});

// Stock Aging
Route::group([], function () {
    Route::resource('erp_stock_valuation', 'InventoryReportAPIController');
    
    Route::get('getScrapFilterData', 'InventoryReportAPIController@getScarpInventoryFilterData')->name('Get Scarp Inventory Filter Data');

    Route::post('validateINVReport', 'InventoryReportAPIController@validateReport')->name('Validate Inv Report');
    Route::post('generateINVReport', 'InventoryReportAPIController@generateReport')->name('Generate Inv Report');
    Route::post('exportINVReport', 'InventoryReportAPIController@exportReport')->name('Export Inv Report');

});

// Stock Taking
Route::group([], function () {
    Route::resource('erp_item_ledgers', 'ErpItemLedgerAPIController');
    
    Route::post('generateStockTakingReport', 'ErpItemLedgerAPIController@generateStockTakingReport')->name('Generate Stock Taking Report');
    Route::post('exportStockTaking', 'ErpItemLedgerAPIController@exportStockTaking')->name('Export Stock Taking');
});

// Inventory Min & Max Analysis
Route::group([], function () {

    Route::get('minAndMaxAnalysis', 'InventoryReportAPIController@minAndMaxAnalysis')->name('Min And Max Analysis');

    Route::post('generateScrapReport', 'InventoryReportAPIController@generateScrapReport')->name('Generate Scrap Report');

});
/* INV Reports End */

/* INV Master Start */

// Items
Route::group([], function () {
    Route::resource('item/assigneds', 'ItemAssignedAPIController', ['names' => 'Item assigned']);
    Route::resource('item/masters', 'ItemMasterAPIController',['names' => 'Item master']);
    Route::resource('warehouse_items', 'WarehouseItemsAPIController');
    Route::resource('itemMasterRefferedBack', 'ItemMasterRefferedBackAPIController');
    Route::resource('item_serials', 'ItemSerialAPIController');
    Route::resource('item_batches', 'ItemBatchAPIController');

    Route::get('getItemMasterAudit', 'ItemMasterAPIController@getItemMasterAudit')->name('Get Item Master Audit');
    Route::get('getPosItemSearch', 'ItemMasterAPIController@getPosItemSearch')->name('Get Pos Item Search');
    Route::get('getSupplierCatalog','ItemMasterAPIController@getSupplierByCatalogItemDetail')->name('Get Supplier By Catalog Item Detail');
    Route::get('getGeneratedSerialNumbers', 'ItemSerialAPIController@getGeneratedSerialNumbers')->name('Get Generated Serial Numbers');
    Route::get('getSerialNumbersForOut', 'ItemSerialAPIController@getSerialNumbersForOut')->name('Get Serial Numbers For Out');
    Route::get('getSerialNumbersForReturn', 'ItemSerialAPIController@getSerialNumbersForReturn')->name('Get Serial Numbers For Return');
    Route::get('getBatchNumbersForOut', 'ItemBatchAPIController@getBatchNumbersForOut')->name('Get Batch Numbers For Out');
    Route::get('getBatchNumbersForReturn', 'ItemBatchAPIController@getBatchNumbersForReturn')->name('Get Batch Numbers For Return');
    Route::get('getWareHouseDataForItemOut', 'ItemBatchAPIController@getWareHouseDataForItemOut')->name('Get Ware House Data For Item Out');

    Route::post('getAllAssignedItemsForCompany', 'ItemMasterAPIController@getAllAssignedItemsForCompany')->name('Get all assigned items for company');
    Route::post('exportItemAssignedByCompanyReport', 'ItemAssignedAPIController@exportItemAssignedByCompanyReport')->name('Export Item Assigned By Company Report');
    Route::post('reOrderTest', 'ItemAssignedAPIController@reOrderTest')->name('Re Order Test');//nee to delete
    Route::post('getAllNonPosItemsByCompany', 'ItemAssignedAPIController@getAllNonPosItemsByCompany')->name('Get All Non Pos Items By Company');
    Route::post('getItemsByMainCategoryAndSubCategory', 'ItemAssignedAPIController@getItemsByMainCategoryAndSubCategory')->name('Get Items By Main Category And Sub Category');
    Route::post('savePullItemsFromInventory', 'ItemAssignedAPIController@savePullItemsFromInventory')->name('Save Pull Items From Inventory');
    Route::post('approveItem', 'ItemMasterAPIController@approveItem')->name('Approve Item');
    Route::post('rejectItem', 'ItemMasterAPIController@rejectItem')->name('Reject Item');
    Route::post('referBackHistoryByItemsMaster', 'ItemMasterRefferedBackAPIController@referBackHistoryByItemsMaster')->name('Refer Back History By Items Master');
    Route::post('generateItemSerialNumbers', 'ItemSerialAPIController@generateItemSerialNumbers')->name('Generate Item Serial Numbers');
    Route::post('serialItemDeleteAllDetails', 'ItemSerialAPIController@serialItemDeleteAllDetails')->name('Serial Item Delete All Details');
    Route::post('updateSoldStatusOfSerial', 'ItemSerialAPIController@updateSoldStatusOfSerial')->name('Update Sold Status Of Serial');
    Route::post('updateReturnStatusOfSerial', 'ItemSerialAPIController@updateReturnStatusOfSerial')->name('Update Return Status Of Serial');
    Route::post('updateReturnStatusOfBatch', 'ItemBatchAPIController@updateReturnStatusOfBatch')->name('Update Return Status Of Batch');
    Route::post('updateSoldStatusOfBatch', 'ItemBatchAPIController@updateSoldStatusOfBatch')->name('Update Sold Status Of Batch');

});

//Warehouses
Route::group([], function () {
   Route::resource('warehouse/masters', 'WarehouseMasterAPIController', ['names' => 'Warehouse master']);
   Route::resource('outlet_users', 'OutletUsersAPIController');

   Route::get('getUnAssignUsersByOutlet', 'OutletUsersAPIController@getUnAssignUsersByOutlet')->name('Get UnAssign Users By Outlet');

   Route::post('getAssignedUsersOutlet', 'OutletUsersAPIController@getAssignedUsersOutlet')->name('Get Assigned Users Outlet');
   Route::post('uploadWarehouseImage', 'WarehouseMasterAPIController@uploadWarehouseImage')->name('Upload Warehouse Image');
   /** Warehouse master Created by Pasan  */
   Route::post('updateWarehouseMaster', 'WarehouseMasterAPIController@updateWarehouseMaster')->name('Update Warehouse Master');

});

/* INV Master End */

//Good Receipt Voucher Review
Route::group([], function () {

Route::resource('g_r_v_masters', 'GRVMasterAPIController');

Route::get('getFilteredGRV', 'GRVMasterAPIController@getFilteredGRV')->name('Get Filtered GRV');
Route::get('cancelGRVPreCheck', 'GRVMasterAPIController@cancelGRVPreCheck')->name('Cancel GRV Pre Check');
Route::get('reverseGRVPreCheck', 'GRVMasterAPIController@reverseGRVPreCheck')->name('Reverse GRV Pre Check');
Route::get('getSupplierInvoiceStatusHistoryForGRV', 'GRVMasterAPIController@getSupplierInvoiceStatusHistoryForGRV')->name('Get Supplier Invoice Status History For GRV');

Route::post('cancelGRV', 'GRVMasterAPIController@cancelGRV')->name('Cancel GRV');
Route::post('reverseGRV', 'GRVMasterAPIController@reverseGRV')->name('Reverse GRV');
});

//Inventory Classification
Route::group([], function () {
    Route::post('getAllInvReclassificationByCompany', 'InventoryReclassificationAPIController@getAllInvReclassificationByCompany')->name('Get All Inventory Reclasification By Company');
    Route::post('invRecalssificationReopen', 'InventoryReclassificationAPIController@invRecalssificationReopen')->name('Inventory Recalssification Reopen');
    
    Route::get('getInvReclassificationAudit', 'InventoryReclassificationAPIController@getInvReclassificationAudit')->name('Get Inventory Reclasification Audit');
    Route::get('getItemsOptionForReclassification', 'InventoryReclassificationAPIController@getItemsOptionForReclassification')->name('Get Items Option For Inventory Reclassification');
    Route::get('getItemsByReclassification', 'InventoryReclassificationDetailAPIController@getItemsByReclassification')->name('Get Items By Inventory Reclassification');

    Route::resource('inv_reclassifications', 'InventoryReclassificationAPIController');
    Route::resource('inv_reclassification_details', 'InventoryReclassificationDetailAPIController');

});


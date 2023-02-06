<?php
/**
 * This file contains inventory module related routes
 * 
 * 
 * */


//approval
Route::group([], function(){
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
});

//GRV Trans
Route::group([], function(){
    Route::resource('grvMasterRefferedbacksCRUD', 'GrvMasterRefferedbackAPIController');
    Route::resource('g_r_v_details', 'GRVDetailsAPIController');
    Route::resource('goodReceiptVoucherCRUD', 'GRVMasterAPIController');
    
    Route::get('getGRVFormData', 'GRVMasterAPIController@getGRVFormData')->name("Get GRV form data");
    Route::get('getGRVDetailsAmendHistory', 'GrvDetailsRefferedbackAPIController@getGRVDetailsAmendHistory')->name("Get GRV Details Amend History");
    Route::get('goodReceiptVoucherAudit', 'GRVMasterAPIController@goodReceiptVoucherAudit')->name("Good Receipt Voucher Audit");
    Route::get('segment/projects', 'ProcumentOrderAPIController@getProjectsBySegment')->name("Get Projects By Segment");
    Route::get('getAllFinancePeriod', 'CompanyFinancePeriodAPIController@getAllFinancePeriod')->name("Get All Finance Period");
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
Route::group([], function(){
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
    Route::get('getItemsOptionForMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsOptionForMaterielRequest')->name("Get Items Option For Materiel Request");
    Route::get('materiel_requests/{id}/purchase-requests', 'MaterielRequestAPIController@checkPurcahseRequestExist')->name("Check Purcahse Request Exist");
    Route::get('purchase_requests/check/product/{itemCode}/{companySystemID}', 'PurchaseRequestAPIController@checkProductExistInIssues')->name("Check Product Exist In Issues");
    Route::get('material-issue-by-refno', 'ItemIssueMasterAPIController@getMaterialIssueByRefNo')->name("Get Material Issue By RefNo");
    Route::get('material-issue/check/product/{id}/{companySystemID}', 'ItemIssueMasterAPIController@checkProductExistInIssues')->name("Check Product Exist In Issues");
    Route::get('getCompanyLocalCurrency', 'CurrencyMasterAPIController@getCompanyLocalCurrency')->name("Get Company Local Currency");
    
    Route::post('returnMaterialRequest', 'MaterielRequestAPIController@returnMaterialRequest')->name("Return Material Request");
    Route::post('getReferBackHistoryByRequest', 'RequestRefferedBackAPIController@getReferBackHistoryByRequest')->name("Get ReferBack History By Request");
    Route::post('requestReopen', 'MaterielRequestAPIController@requestReopen')->name("Material Request Reopen");
    Route::post('requestReferBack', 'MaterielRequestAPIController@requestReferBack')->name("Material Request ReferBack");
    Route::post('getAllRequestByCompany', 'MaterielRequestAPIController@getAllRequestByCompany')->name("Get All Material Request By Company");

});

//Material Issue Trans
Route::group([], function(){
    Route::resource('item_issue_masters', 'ItemIssueMasterAPIController');
    Route::resource('materiel_requests', 'MaterielRequestAPIController');
    Route::resource('item_issue_referred_back', 'ItemIssueMasterRefferedBackAPIController');
    Route::resource('item_issue_details_reffered_backs', 'ItemIssueDetailsRefferedBackAPIController');
    Route::resource('materiel_request_details', 'MaterielRequestDetailsAPIController');
    Route::resource('item_issue_details', 'ItemIssueDetailsAPIController');
   
    Route::get('getAllMaterielRequestNotSelectedForIssueByCompany', 'ItemIssueMasterAPIController@getAllMaterielRequestNotSelectedForIssueByCompany')->name("Get All Materiel Request Not Selected For Issue By Company");
    Route::get('allMaterielRequestNotSelectedForIssue', 'ItemIssueMasterAPIController@allMaterielRequestNotSelectedForIssue')->name("All Materiel Request Not Selected For Issue");
    Route::get('getMaterielIssueAudit', 'ItemIssueMasterAPIController@getMaterielIssueAudit')->name("Get Materiel Issue Audit");
    Route::get('material-issue/update-qnty-by-location', 'ItemIssueMasterAPIController@updateQntyByLocation')->name("Update Qnty By Location");
    Route::get('checkManWareHouse', 'ItemIssueMasterAPIController@checkManWareHouse')->name("Check Man Ware House");    
    Route::get('getItemsByMaterielIssue', 'ItemIssueDetailsAPIController@getItemsByMaterielIssue')->name("Get Items By Materiel Issue");
    Route::get('getItemsOptionsMaterielIssue', 'ItemIssueDetailsAPIController@getItemsOptionsMaterielIssue')->name("Get Items Options Materiel Issue");
    Route::get('getItemIssueDetailsReferBack', 'ItemIssueDetailsRefferedBackAPIController@getItemIssueDetailsReferBack')->name("Get Item Issue Details ReferBack"); 

    Route::post('materielIssueReopen', 'ItemIssueMasterAPIController@materielIssueReopen')->name("Material Issue Reopen");
    Route::post('materielIssueReferBack', 'ItemIssueMasterAPIController@materielIssueReferBack')->name("Material Issue Refer Back");
    Route::post('getAllMaterielIssuesByCompany', 'ItemIssueMasterAPIController@getAllMaterielIssuesByCompany')->name("Get All Material Issue By Company");
    Route::post('getReferBackHistoryByMaterielIssues', 'ItemIssueMasterRefferedBackAPIController@getReferBackHistoryByMaterielIssues')->name("Get ReferBack History By Materiel Issues");

});
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


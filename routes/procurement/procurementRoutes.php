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
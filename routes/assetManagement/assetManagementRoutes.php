<?php
/**
 * This file contains asset management module related routes
 * 
 * 
 * */


//approval
Route::group([], function(){
    Route::post('getCostingApprovalByUser', 'FixedAssetMasterAPIController@getCostingApprovalByUser')->name("Get asset costing pending for approval");
    Route::post('getCostingApprovedByUser', 'FixedAssetMasterAPIController@getCostingApprovedByUser')->name("Get asset costing approved");
    Route::get('getAllocationFormData', 'FixedAssetMasterAPIController@getAllocationFormData')->name("Get asset form data");
    Route::get('getAssetCostingByID/{id}', 'FixedAssetMasterAPIController@getAssetCostingByID')->name("Get asset id");
    Route::post('getFixedAssetSubCat', 'FixedAssetMasterAPIController@getFixedAssetSubCat')->name("Get asset sub category");
    Route::get('getPostToGLAccounts', 'FixedAssetMasterAPIController@getPostToGLAccounts')->name("Get post gl account");
    Route::get('assetCostingForPrint', 'FixedAssetMasterAPIController@assetCostingForPrint')->name("Get asset costing for print");
    Route::post('getAssetTransferApprovalByUser', 'ERPAssetTransferAPIController@getAssetTransferApprovalByUser')->name("Get asset transfer pending for approval");
    Route::post('getAssetTransferApprovalByUserApproved', 'ERPAssetTransferAPIController@getAssetTransferApprovalByUserApproved')->name("Get asset transfer approved");
    Route::get('asset-transfer-details', 'ERPAssetTransferDetailAPIController@getAssetTransferDetails')->name("Get asset transfer details");
    Route::get('printERPAssetTransfer', 'ERPAssetTransferDetailAPIController@printERPAssetTransfer')->name("Print asset transfer");
    Route::post('rejectAssetTransfer', 'ERPAssetTransferAPIController@rejectAssetTransfer')->name("Reject asset transfer");
    Route::post('approveAssetTransfer', 'ERPAssetTransferAPIController@approveAssetTransfer')->name("Approve asset transfer");
    Route::post('getCapitalizationApprovalByUser', 'AssetCapitalizationAPIController@getCapitalizationApprovalByUser')->name("Get asset capitalization pending for approval");
    Route::post('getCapitalizationApprovedByUser', 'AssetCapitalizationAPIController@getCapitalizationApprovedByUser')->name("Get asset capitalization approved");
    Route::post('getDisposalApprovalByUser', 'AssetDisposalMasterAPIController@getDisposalApprovalByUser')->name("Get asset disposal pending for approval");
    Route::post('getDisposalApprovedByUser', 'AssetDisposalMasterAPIController@getDisposalApprovedByUser')->name("Get asset disposal approved");
    Route::get('getDepreciationFormData', 'FixedAssetDepreciationMasterAPIController@getDepreciationFormData')->name("Get asset depreciation form data");
    Route::post('getAssetDepApprovalByUser', 'FixedAssetDepreciationMasterAPIController@getAssetDepApprovalByUser')->name("Get asset depreciation pending for approval");
    Route::post('getAssetDepApprovedByUser', 'FixedAssetDepreciationMasterAPIController@getAssetDepApprovedByUser')->name("Get asset depreciation approved");
    Route::post('getVerificationApprovalByUser', 'AssetVerificationAPIController@getVerificationApprovalByUser')->name("Get asset verification pending for approval");
    Route::post('getVerificationApprovedByUser', 'AssetVerificationAPIController@getVerificationApprovedByUser')->name("Get asset verification approved");

});

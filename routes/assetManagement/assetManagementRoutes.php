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

//transaction - Asset Depreciation
Route::group([], function() {
    Route::post('getAllDepreciationByCompany', 'FixedAssetDepreciationMasterAPIController@getAllDepreciationByCompany')->name('Get depreciation by company');
    Route::post('exportAMDepreciation', 'FixedAssetDepreciationPeriodAPIController@exportAMDepreciation')->name('Export asset depreciation');

    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::post('getAssetDepPeriodsByID', 'FixedAssetDepreciationPeriodAPIController@getAssetDepPeriodsByID')->name('Get asset depreciation periods by id');
            Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');

        });
    });

});


//transaction - Asset Allocation
Route::group([], function(){
    Route::post('getAllAllocationByCompany', 'FixedAssetMasterAPIController@getAllAllocationByCompany')->name("Get asset allocation");
    Route::get('getFAGrvDetailsByID', 'FixedAssetMasterAPIController@getFAGrvDetailsByID')->name("Get FAGrv Details");
    Route::post('getAllocatedAssetsForExpense', 'ExpenseAssetAllocationAPIController@getAllocatedAssetsForExpense')->name("Get Allocated Assets For Expense");
    Route::get('getCompanyAsset', 'ExpenseAssetAllocationAPIController@getCompanyAsset')->name('Get company asset');

    Route::resource('fixed_asset_masters', 'FixedAssetMasterAPIController');
    Route::resource('expense_asset_allocations', 'ExpenseAssetAllocationAPIController');
});



//transaction - Asset Costing
Route::group([], function(){
    Route::post('getAllCostingByCompany', 'FixedAssetMasterAPIController@getAllCostingByCompany')->name("Get Asset Costing By Name");
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::post('exportAssetMaster', 'FixedAssetMasterAPIController@exportAssetMaster')->name("Export Asset Master");

        });
    });
    Route::get('getFinanceGLCode', 'FixedAssetMasterAPIController@getFinanceGLCode')->name("Get Financial GL Code");
    Route::get('getCapitalizationFixedAsset', 'AssetCapitalizationAPIController@getCapitalizationFixedAsset')->name("Get Capitilization Fixed Asset");
    Route::post('createFixedAssetCosting', 'FixedAssetMasterAPIController@create')->name("Create Fixed Asset Costing");
    Route::get('downloadAssetTemplate', 'FixedAssetMasterAPIController@downloadAssetTemplate')->name("Download Asset Template");
    Route::post('assetCostingUpload', 'FixedAssetMasterAPIController@assetCostingUpload')->name("Asset Costing Upload");
    Route::get('getAssetCostingMaster', 'FixedAssetMasterAPIController@getAssetCostingMaster')->name("Get Asset Costing Master");
    Route::resource('asset_warranties', 'AssetWarrantyAPIController');
    Route::post('getWarranty', 'AssetWarrantyAPIController@getWarranty')->name("Get asset warranty");
    Route::post('getAssetAttributes', 'FixedAssetMasterAPIController@assetAttributes')->name("Get asset attributes");
    Route::post('updateAttribute', 'FixedAssetMasterAPIController@updateAttribute')->name("Update asset attribute");
    Route::post('updateActionAttribute', 'FixedAssetMasterAPIController@updateActionAttribute')->name("Update asset attribute");


    Route::post('assetCostAttributesUpdate', 'ErpAttributesAPIController@assetCostAttributesUpdate')->name('Asset cost attributes update');
    Route::post('dropdownValuesUpdate', 'ErpAttributesAPIController@dropdownValuesUpdate')->name('Asset cost dropdown values update');
    Route::get('getAttributesDataFormData', 'FinanceItemCategoryMasterAPIController@getAttributesDataFormData')->name('Get attributes data form data');
    Route::post('getAssetCostAttributesData', 'FinanceItemCategoryMasterAPIController@getAssetCostAttributesData')->name('Get asset cost attributes data');
    Route::post('getAttributesDropdownData', 'ErpAttributesDropdownAPIController@getAttributesDropdownData');


});


//transaction - Asset Disposal
Route::group([], function(){
    Route::get('getDisposalFormData', 'AssetDisposalMasterAPIController@getDisposalFormData')->name("Get Asset Disposal Data");
    Route::post('getAllDisposalByCompany', 'AssetDisposalMasterAPIController@getAllDisposalByCompany')->name("Get Asset Disposal By Company");
    Route::resource('asset_disposal_masters', 'AssetDisposalMasterAPIController');
    Route::get('getAssetDisposalDetail', 'AssetDisposalDetailAPIController@getAssetDisposalDetail')->name("Get Disposal Details");
    Route::post('getAllAssetsForDisposal', 'AssetDisposalMasterAPIController@getAllAssetsForDisposal')->name("Get Asset from Disposal");
    Route::resource('asset_disposal_details', 'AssetDisposalDetailAPIController');

});


//transaction - Asset Capitalization
Route::group([], function(){
    Route::get('getCapitalizationFormData', 'AssetCapitalizationAPIController@getCapitalizationFormData')->name("Get Asset Capitalization Form Data");
    Route::post('getAllCapitalizationByCompany', 'AssetCapitalizationAPIController@getAllCapitalizationByCompany')->name("Get Asset Capitalization By Company");
    Route::resource('asset_capitalizations', 'AssetCapitalizationAPIController');
    Route::get('getAssetByCategory', 'AssetCapitalizationAPIController@getAssetByCategory')->name("Get Asset By Category");
    Route::get('getCapitalizationDetails', 'AssetCapitalizationDetailAPIController@getCapitalizationDetails')->name("Get Asset Capitalization Details");
    Route::resource('asset_capitalization_details', 'AssetCapitalizationDetailAPIController');
    Route::get('getAssetNBV', 'AssetCapitalizationAPIController@getAssetNBV')->name("Get Asset NBV");
    Route::get('getAssetCapitalizationMaster', 'AssetCapitalizationAPIController@getAssetCapitalizationMaster')->name("Get Asset Capitalization Master");

});

//transaction - Asset Verification
Route::group([], function(){
    Route::get('getVerificationFormData', 'AssetVerificationAPIController@getVerificationFormData')->name("Get Asset Verification Form data");
    Route::post('getAllAssetVerification', 'AssetVerificationAPIController@index')->name("Get Asset Verification");
    Route::post('storeVerification', 'AssetVerificationAPIController@store')->name("Store Asset Verification");
    Route::delete('deleteAssetVerification/{id}', 'AssetVerificationAPIController@destroy')->name("Delete Asset Verification");
    Route::get('getVerificationById/{id}', 'AssetVerificationAPIController@show')->name("Get VerificationBy Id");
    Route::post('getVerificationDetailsById', 'AssetVerificationDetailAPIController@index')->name("Get Verification detailed by Id");
    Route::post('getAllCostingByCompanyForVerification', 'AssetVerificationAPIController@getAllCostingByCompanyForVerification')->name("Get All Costing By Company For Verification");
    Route::post('addAssetToVerification/{id}', 'AssetVerificationDetailAPIController@store')->name("Add Asset To Verification");
    Route::delete('deleteAssetFromVerification/{id}', 'AssetVerificationDetailAPIController@destroy')->name("Delete Asset From Verification");
    Route::put('updateAssetVerification/{id}', 'AssetVerificationAPIController@update')->name("Update Asset Verification");


});


//transaction - Asset Transfer
Route::group([], function(){
    Route::post('getAllAssetTransferList', 'ERPAssetTransferAPIController@getAllAssetTransferList')->name("Get Asset Transfer list");
    Route::post('getAllAssetRequestList', 'AssetRequestAPIController@getAllAssetRequestList')->name("Get Asset Request list");
    Route::get('getAssetTransferData', 'ERPAssetTransferAPIController@getAssetTransferData')->name("Get Asset Transfer Form Data");
    Route::resource('asset_transfer', 'ERPAssetTransferAPIController');
    Route::get('getassetRequestMaster', 'AssetRequestDetailAPIController@getAssetRequestMaster')->name("Get Asset Request Master");
    Route::get('getAssetDropData', 'AssetRequestDetailAPIController@getAssetDropData')->name("Get Asset Drop Data");
    Route::get('getassetRequestDetailSelected', 'AssetRequestDetailAPIController@getAssetRequestDetailSelected')->name("Get Asset Request Detail Selected");
    Route::post('add-asset-transfer-detail/{id}', 'ERPAssetTransferDetailAPIController@store')->name("Add Asset Transfer Detail");
    Route::get('assetStatus', 'ERPAssetTransferAPIController@assetStatus')->name("Get Asset Status");
    Route::get('get-employee-asset-transfer-details/{id}', 'ERPAssetTransferDetailAPIController@get_employee_asset_transfer_details')->name("Get Employee asset transfer Details");
    Route::resource('asset_transfer_detail', 'ERPAssetTransferDetailAPIController');
    Route::get('fetch-asset-transfer-master/{id}', 'ERPAssetTransferAPIController@fetchAssetTransferMaster')->name("Fetch Asset transfer Master");
    Route::get('getAssetTransferMasterRecord', 'ERPAssetTransferAPIController@getAssetTransferMasterRecord')->name("Fetch Asset transfer Master Record");
    Route::post('assetTransferReopen', 'ERPAssetTransferAPIController@assetTransferReopen')->name("Reopen Asset transfer");
    Route::get('asset-request-details', 'AssetRequestDetailAPIController@getAssetRequestDetails')->name("Get Asset Request Details");
    Route::post('getEmployeesToSelectDrpdwn', 'ERPAssetTransferAPIController@getEmployeesToSelectDrpdwn')->name("Get Employees to Direct to Employee type asset transfer");
    Route::get('asset-employee-value','ERPAssetTransferDetailAPIController@getAssetEmployeeValue')->name('Get Asset Assigned Employee value');
    Route::post('getDepartmentList','ERPAssetTransferDetailAPIController@getDepartmentList')->name('Get department list');
    Route::post('getDepartmentOfAsset','ERPAssetTransferDetailAPIController@getDepartmentOfAsset')->name('Get department of asset');
    Route::post('getCurrentAssigneeOfAsset','ERPAssetTransferDetailAPIController@getCurrentAssigneeOfAsset')->name('Get current assignee of asset');

    
});


//report - Asset Register
Route::group([], function(){
    Route::get('getAssetManagementFilterData', 'AssetManagementReportAPIController@getFilterData')->name("Get Asset Managment Filter Data");
    Route::post('validateAMReport', 'AssetManagementReportAPIController@validateReport')->name('Validate Report asset Management');
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            
            Route::post('generateAMReport', 'AssetManagementReportAPIController@generateReport')->name('Generate Report asset Management');
            Route::post('exportAMReport', 'AssetManagementReportAPIController@exportReport')->name('Export Report asset Management');

        });
    });

});


//Report - Asset Insuarance
Route::group([], function(){
    Route::post('generateAssetInsuranceReport', 'FixedAssetMasterAPIController@generateAssetInsuranceReport')->name('Generate Asset Insurance Report');
    Route::post('exportAssetInsuranceReport', 'FixedAssetMasterAPIController@exportAssetInsuranceReport')->name('Export Asset Insurance Report');

});

//Asset Management - Barcode Configuration
Route::group([], function(){
    Route::post('getAllBarCodeConf', 'BarcodeConfigurationAPIController@getAllBarCodeConf')->name('Get All Barcode Configuration');
    Route::get('getBarcodeConfigurationFormData', 'BarcodeConfigurationAPIController@getBarcodeConfigurationFormData')->name('Get Barcode Configuration');
    Route::resource('barcode_configurations', 'BarcodeConfigurationAPIController');


});

//Report - CWIP Movement
Route::group([], function(){
    Route::post('assetCWIPDrillDown', 'AssetManagementReportAPIController@assetCWIPDrillDown')->name('Asset cwip drill down');
});


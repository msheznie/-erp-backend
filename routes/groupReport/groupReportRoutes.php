<?php
/**
 * This file contains group report module related routes
 *
 *
 * */

use Illuminate\Support\Facades\Route;

//Asset Management

//Asset Register
Route::group([],function (){
    Route::get('getAssetCostingViewByFaID/{id}', 'FixedAssetMasterAPIController@getAssetCostingViewByFaID')->name("Get asset costing view by id");
    Route::post('assetRegisterDrillDown', 'AssetManagementReportAPIController@getAssetRegisterSummaryDrillDownQRY')->name("Get asset register summary drill down");
    Route::post('exportAssetRegisterSummaryDrillDown', 'AssetManagementReportAPIController@getAssetRegisterSummaryDrillDownExport')->name("Get asset register summary drill down export");
});

//Procurement

//Open Requests
Route::group([],function (){
    Route::get('downloadPrItemUploadTemplate', 'PurchaseRequestAPIController@downloadPrItemUploadTemplate')->name("Download pr item upload template");
});

//Order Inquiry
Route::group([],function (){
    Route::get('getPOSuppliers', 'SupplierMasterAPIController@getPOSuppliers')->name("Get po suppliers");
    Route::post('validateReport', 'ReportAPIController@validateReport')->name("Validate report");
    Route::post('generateReport', 'ReportAPIController@generateReport')->name("Generate report");
    Route::post('exportReport', 'ReportAPIController@exportReport')->name("Export report");
});

//Order Status Report
Route::group([],function (){
    Route::get('reportOrderStatusFilterOptions', 'PurchaseOrderStatusAPIController@reportOrderStatusFilterOptions')->name("Report order status filter options");
    Route::post('reportOrderStatusPreCheck', 'PurchaseOrderStatusAPIController@reportOrderStatusPreCheck')->name("Report order status pre check");
    Route::post('reportOrderStatus', 'PurchaseOrderStatusAPIController@reportOrderStatus')->name("Report order status");
    Route::post('exportReportOrderStatus', 'PurchaseOrderStatusAPIController@exportReportOrderStatus')->name("Export report order status");
});

//Employee Performance
Route::group([],function (){
    Route::post('reportPoEmployeePerformance', 'ProcumentOrderAPIController@reportPoEmployeePerformance')->name("Report po employee performance");
    Route::post('exportPoEmployeePerformance', 'ProcumentOrderAPIController@exportPoEmployeePerformance')->name("Export po employee performance");
});


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

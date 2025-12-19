<?php
/**
 * This file contains logistics module related routes
 *
 *
 * */

use Illuminate\Support\Facades\Route;

Route::group([],function (){
    Route::post('getCompanyLocalAndRptAmount', 'LogisticAPIController@getCompanyLocalAndRptAmount')->name('Get company local and rpt amount');
    Route::post('exportLogisticsByCompanyReport', 'LogisticAPIController@exportLogisticsByCompanyReport')->name('Export logistics by company report');
    Route::post('getAllLogisticByCompany', 'LogisticAPIController@getAllLogisticByCompany')->name('Get all logistic by company');
    Route::get('getLogisticFormData', 'LogisticAPIController@getLogisticFormData')->name('Get Logistic form data');
    Route::get('getItemsByLogistic', 'LogisticDetailsAPIController@getItemsByLogistic')->name('Get items by logistic');
    Route::get('getStatusByLogistic', 'LogisticAPIController@getStatusByLogistic')->name('Get status by logistic');
    Route::get('checkPullFromGrv', 'LogisticAPIController@checkPullFromGrv')->name('Check pull from grv');
    Route::get('getPurchaseOrdersForLogistic', 'LogisticDetailsAPIController@getPurchaseOrdersForLogistic')->name('Get purchase order for logistic');
    Route::get('getGrvByPOForLogistic', 'LogisticDetailsAPIController@getGrvByPOForLogistic')->name('Get grv by po for logistic');
    Route::get('getGrvDetailsByGrvForLogistic', 'LogisticDetailsAPIController@getGrvDetailsByGrvForLogistic')->name('Get grv details by grv for logistics');
    Route::post('addLogisticDetails', 'LogisticDetailsAPIController@addLogisticDetails')->name('Add logistic details');
    Route::get('getLogisticAudit', 'LogisticAPIController@getLogisticAudit')->name('Get logistic audit');

    Route::resource('logistic_details', 'LogisticDetailsAPIController');
    Route::resource('logistic_shipping_statuses', 'LogisticShippingStatusAPIController');
    Route::resource('logistics', 'LogisticAPIController');
});

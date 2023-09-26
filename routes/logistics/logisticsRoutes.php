<?php
/**
 * This file contains logistics module related routes
 *
 *
 * */

use Illuminate\Support\Facades\Route;

Route::group([],function (){
    Route::post('getCompanyLocalAndRptAmount', 'LogisticAPIController@getCompanyLocalAndRptAmount')->name('Get company local and rpt amount');
});

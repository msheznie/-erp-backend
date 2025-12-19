<?php
/**
 * This file contains third party routes
 *
 *
 * */

use Illuminate\Support\Facades\Route;

Route::group([], function(){
    Route::get('getSupplierContracts', 'ThirdPartyAPIController@getSupplierContracts')->name("Get supplier contracts");
});

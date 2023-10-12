<?php
/**
 * This file contains navigation module related routes
 * 
 * 
 * */


//Company Assign
Route::group([], function(){
    Route::get('getCompanyNavigation', 'CompanyNavigationMenusAPIController@getCompanyNavigation')->name('Get company navigation'); 
    Route::resource('assignCompanyNavigation', 'CompanyNavigationMenusAPIController');  
});


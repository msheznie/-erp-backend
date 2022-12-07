<?php
/**
 * This file contains approval setup module related routes
 * 
 * 
 * */


//approval group
Route::group([], function(){
	Route::post('getApprovalGroupByCompanyDatatable', 'ApprovalGroupsAPIController@getApprovalGroupByCompanyDatatable')->name("Get approval group by company");
    Route::resource('approval_groups', 'ApprovalGroupsAPIController');
});

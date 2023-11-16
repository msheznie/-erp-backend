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

//User Group
Route::group([], function(){
    Route::get('getEmployeeMasterData', 'EmployeeAPIController@getEmployeeMasterData')->name('Get employee master data'); 
    Route::post('getUserGroupByCompanyDatatable', 'UserGroupAPIController@getUserGroupByCompanyDatatable')->name('Get user group by company data table'); 
    Route::post('getUserGroupEmployeesDatatable', 'EmployeeNavigationAPIController@getUserGroupEmployeesByCompanyDatatable')->name('Get user group employees data table');
    Route::resource('userGroups', 'UserGroupAPIController'); 
    Route::resource('employee_navigations', 'EmployeeNavigationAPIController');
    Route::get('getUserGroup', 'UserGroupAPIController@getUserGroup')->name('Get user group');
});

//User Group Assign
Route::group([], function(){
    Route::get('getUserGroupNavigation', 'UserGroupAssignAPIController@getUserGroupNavigation')->name('Get user group navigation');
    Route::resource('assignUserGroupNavigation', 'UserGroupAssignAPIController');
});

//Navigation Report
Route::group([], function(){
    Route::post('exportNavigationeport', 'UserGroupAssignAPIController@exportNavigationeport')->name('Export navigation report');
});

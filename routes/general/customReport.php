<?php

Route::resource('custom_report_types', 'CustomReportTypeAPIController');
Route::resource('custom_user_reports', 'CustomUserReportsAPIController');
Route::post('getCustomReportsByUser', 'CustomUserReportsAPIController@getCustomReportsByUser')->name('Get custom reports by user');
Route::post('exportCustomReport', 'CustomUserReportsAPIController@exportCustomReport')->name('Export custom report');
Route::resource('custom_report_employees', 'CustomReportEmployeesAPIController');
Route::get('getUnAssignEmployeeByReport', 'CustomReportEmployeesAPIController@getEmployees')->name('Get unassigned employee by report');
Route::post('getCustomReportAssignedEmployee', 'CustomReportEmployeesAPIController@getCustomReportAssignedEmployee')->name('Get custom report assigned employee');
Route::post('customReportView', 'CustomUserReportsAPIController@customReportView')->name('Custom report view');

Route::get('authenticateCustomReport', 'CustomReportEmployeesAPIController@authenticateCustomReport');
Route::post('getBoldReportsDatatable', 'CustomReportEmployeesAPIController@getBoldReportsDatatable')->name('Get Bold Reports with datatable');
Route::get('getBoldReportsTenants', 'CustomReportEmployeesAPIController@getBoldReportsTenants')->name('Get Bold Reports tenants');
Route::get('getBoldReportsCategories', 'CustomReportEmployeesAPIController@getBoldReportsCategories')->name('Get Bold Reports categories');
Route::get('getBoldReportById/{reportId}', 'CustomReportEmployeesAPIController@getBoldReportById')->name('Get Bold Report by ID');
Route::post('getReportDetailsById', 'CustomReportEmployeesAPIController@getReportDetailsById')->name('Get Report Details by ID for Viewer');
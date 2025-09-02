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
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


//approval level
Route::group([], function(){
    Route::post('getGroupApprovalLevelDatatable', 'ApprovalLevelAPIController@getGroupApprovalLevelDatatable')->name("Get group approval level data table");
    Route::post('activateApprovalLevel', 'ApprovalLevelAPIController@activateApprovalLevel')->name("Activate approval level");
    Route::post('assignApprovalGroup', 'ApprovalRoleAPIController@assignApprovalGroup')->name("Assign approval group");
    Route::post('getApprovalPersonsByRoll', 'EmployeesDepartmentAPIController@getApprovalPersonsByRoll')->name("Get approval persons by roll");
    Route::post('updateEmployeeDepartmentActive', 'EmployeesDepartmentAPIController@updateEmployeeDepartmentActive')->name("Update employee department active");
    Route::post('assignEmployeeToApprovalGroup', 'EmployeesDepartmentAPIController@assignEmployeeToApprovalGroup')->name("Assign employee to approval group");

    Route::get('getGroupFilterData', 'ApprovalLevelAPIController@getGroupFilterData')->name("Get approval group filer data");
    Route::get('getApprovalRollByLevel', 'ApprovalRoleAPIController@getApprovalRollByLevel')->name("Get approval roll by level");

    Route::resource('approval_levels', 'ApprovalLevelAPIController');
});


//approval access
Route::group([], function(){
    Route::post('getApprovalAccessRights', 'EmployeesDepartmentAPIController@getApprovalAccessRightsDatatable')->name("Get approval access rights");
    Route::post('deleteAllAccessRights', 'EmployeesDepartmentAPIController@deleteAllAccessRights')->name("Delete all access rights");
    Route::post('approvalAccessActiveInactiveAll', 'EmployeesDepartmentAPIController@approvalAccessActiveInactiveAll')->name("Approval access active inactive all");
    Route::post('approval/matrix', 'EmployeesDepartmentAPIController@approvalMatrixReport')->name("Approval matrix report");
    Route::post('approval/matrix/export', 'EmployeesDepartmentAPIController@exportApprovalMatrixReport')->name("Export approval matrix report");
    Route::post('mirrorAccessRights', 'EmployeesDepartmentAPIController@mirrorAccessRights')->name("Mirror access rights");

    Route::get('getApprovalAccessRightsFormData', 'EmployeesDepartmentAPIController@getApprovalAccessRightsFormData')->name("Get approval access rights form data");
    Route::get('getDepartmentDocument', 'EmployeesDepartmentAPIController@getDepartmentDocument')->name("Get department documents");
    Route::get('getTypeheadEmployees', 'EmployeeAPIController@getTypeheadEmployees')->name("Get type head employees");
    Route::get('getCompanyServiceLine', 'ApprovalLevelAPIController@getCompanyServiceLine')->name("Get company service lines");
    Route::get('getDocumentAccessGroup', 'ApprovalGroupsAPIController@getDocumentAccessGroup')->name("Get document access group");

    Route::resource('employees_departments', 'EmployeesDepartmentAPIController');
});

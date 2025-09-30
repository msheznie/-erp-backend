<?php
Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('generateBudgetReport', 'Budget\BudgetReportController@generateReport');
        Route::post('exportBudgetReport', 'Budget\BudgetReportController@export');
    });
});

//workflow configuration
Route::group([], function () {
    Route::post('getWorkflowConfiguration', 'WorkflowConfigurationAPIController@getWorkflowConfiguration')->name('Get workflow configuration');
    Route::get('getWorkflowConfigurationFormData', 'WorkflowConfigurationAPIController@getWorkflowConfigurationFormData')->name('Get workflow configuration form data');
    Route::post('changeWorkflowConfigurationStatus', 'WorkflowConfigurationAPIController@changeWorkflowConfigurationStatus')->name('Change workflow configuration work status');
});

// Budget Template Routes
Route::resource('budget_templates', 'BudgetTemplateAPIController');
Route::post('getAllBudgetTemplates', 'BudgetTemplateAPIController@getAllBudgetTemplates')->name('Get all budget templates');
Route::get('getBudgetTemplateFormData', 'BudgetTemplateAPIController@getBudgetTemplateFormData')->name('Get budget template form data');
Route::get('getBudgetTemplatesByType/{type}', 'BudgetTemplateAPIController@getBudgetTemplatesByType')->name('Get budget templates by type');
Route::post('exportBudgetTemplates', 'BudgetTemplateAPIController@exportBudgetTemplates')->name('Export budget templates');
Route::post('getSegmentOptionsByBudgetTemplate', 'BudgetTemplateAPIController@getSegmentOptionsByBudgetTemplate')->name('Get segments by budget template');

// Budget Template Pre Columns routes
Route::get('budget_template_pre_columns/grouped', 'BudgetTemplatePreColumnAPIController@getAvailableColumnsGrouped')->name('Get available columns grouped');
Route::get('budget_template_pre_columns/unassigned/{templateId}', 'BudgetTemplatePreColumnAPIController@getUnassignedColumns')->name('Get unassigned columns');
Route::get('budget_template_pre_columns/column-types', 'BudgetTemplatePreColumnAPIController@getColumnTypeOptions')->name('Get column types options');
Route::resource('budget_template_pre_columns', 'BudgetTemplatePreColumnAPIController');

// Budget Template Columns routes
Route::get('getBudgetTemplateColumns/{templateId}', 'BudgetTemplateColumnAPIController@getTemplateColumns')->name('Get template columns');
Route::post('budget_template_columns/template/{templateId}/sort-order', 'BudgetTemplateColumnAPIController@updateSortOrder')->name('Update sort order');
Route::delete('budget_template_columns/template/{templateId}/column/{preColumnId}', 'BudgetTemplateColumnAPIController@removeFromTemplate')->name('Remove template column');
Route::get('budget_template_columns/template/{templateId}/formula-references/{excludeColumnId?}', 'BudgetTemplateColumnAPIController@getFormulaReferenceColumns')->name('Get template column formula');
Route::resource('budget_template_columns', 'BudgetTemplateColumnAPIController');

// Budget Planning Routes
Route::post('getBudgetPlanningUserPermissions', 'CompanyBudgetPlanningAPIController@getBudgetPlanningUserPermissions')->name('Get budget planning user permissions');
Route::post('updateBudgetPlanningDelegateWorkStatus', 'CompanyBudgetPlanningAPIController@updateBudgetPlanningDelegateWorkStatus')->name('Update Budget Planning Deletegate Work Status');
Route::get('getBudgetPlanningFormData', 'CompanyBudgetPlanningAPIController@getBudgetPlanningFormData')->name("Get budget planning form data");
Route::post('getBudgetPlanningMasterData', 'CompanyBudgetPlanningAPIController@getBudgetPlanningMasterData')->name("Get budget planning master data");
Route::post('exportBudgetPlanning', 'CompanyBudgetPlanningAPIController@exportBudgetPlanning')->name('Export budget planning to Excel');
Route::post('validateBudgetPlanning', 'CompanyBudgetPlanningAPIController@validateBudgetPlanning')->name('Validate budget planning');
Route::post('checkBudgetPlanningInProgress', 'CompanyBudgetPlanningAPIController@checkBudgetPlanningInProgress')->name('Check budget planning in progress');
Route::post('updateBudgetPlanningStatus', 'DepartmentBudgetPlanningAPIController@updateStatus')->name('Update budget planning status');
Route::post('createTimeExtensionRequest', 'DepartmentBudgetPlanningAPIController@createTimeExtensionRequest')->name('Create time extension request');
Route::post('getTimeExtensionRequests', 'DepartmentBudgetPlanningAPIController@getTimeExtensionRequests')->name('Get time extension requests');
Route::post('cancelDepartmentTimeExtensionRequests', 'DepartmentBudgetPlanningAPIController@cancelDepartmentTimeExtensionRequests')->name('Cancel time extension requests');
Route::post('deleteTimeExtensionRequest', 'DepartmentBudgetPlanningAPIController@deleteTimeExtensionRequest')->name('Delete time extension request');
Route::post('acceptTimeExtensionRequest', 'DepartmentBudgetPlanningAPIController@acceptTimeExtensionRequest')->name('Accept time extension request');
Route::post('generateTimeExtensionRequestCode', 'DepartmentBudgetPlanningAPIController@generateTimeExtensionRequestCode')->name('Generate time extension request code');
Route::get('getTimeExtensionRequestAttachments/{timeRequestId}', 'DepartmentBudgetPlanningAPIController@getTimeExtensionRequestAttachments')->name('Get time extension request attachments');
Route::post('downloadTimeExtensionAttachment', 'DepartmentBudgetPlanningAPIController@downloadTimeExtensionAttachment')->name('Download time extension attachment');
Route::post('getReversions', 'DepartmentBudgetPlanningAPIController@getReversions')->name('Get reversions');
Route::post('getOptionsForSelectedUnit', 'DepartmentBudgetPlanningDetailAPIController@getOptionsForSelectedUnit')->name('Get options for selected unit');

// Department Budget Planning Details Routes
Route::post('getDepartmentBudgetPlanningDetails', 'DepartmentBudgetPlanningDetailAPIController@getByDepartmentPlanning')->name('Get department budget planning details');
Route::post('updateDepartmentBudgetPlanningDetailStatus', 'DepartmentBudgetPlanningDetailAPIController@updateInternalStatus')->name('Update department budget planning detail status');
Route::post('getDepartmentBudgetPlanningSummary', 'DepartmentBudgetPlanningDetailAPIController@getSummary')->name('Get department budget planning summary');
Route::resource('departmentBudgetPlanningDetails', 'DepartmentBudgetPlanningDetailAPIController');
Route::post('updateDepartmentBudgetPlanningDetailAmount', 'DepartmentBudgetPlanningDetailAPIController@updateDepartmentBudgetPlanningDetailAmount');

// Budget Delegate Access Routes
Route::post('getDelegateAccessRecords', 'BudgetDelegateAPIController@getDelegateAccessRecords')->name('Get delegate access records');
Route::post('createOrUpdateDelegateAccess', 'BudgetDelegateAPIController@createOrUpdateDelegateAccess')->name('Create or update delegate access');
Route::post('removeDelegateAccess', 'BudgetDelegateAPIController@removeDelegateAccess')->name('Remove delegate access');
Route::post('deleteDelegateAccess', 'BudgetDelegateAPIController@deleteDelegateAccess')->name('Delete delegate access');
Route::post('getDelegateAccessSummary', 'BudgetDelegateAPIController@getDelegateAccessSummary')->name('Get delegate access summary');
Route::post('getBudgetDelegateFormData', 'BudgetDelegateAPIController@getBudgetDelegateFormData')->name('Get budget delegate form data');
Route::post('updateDelegateStatus', 'BudgetDelegateAPIController@updateDelegateStatus')->name('Update budget delegate status');
Route::post('updateDelegateAcccessStatus', 'BudgetDelegateAPIController@updateDelegateAcccessStatus')->name('Update delegate permission');

Route::get('verifyBudgetTemplateConfiguration/{budgetTemplateId}', 'DepartmentBudgetPlanningDetailAPIController@verifyBudgetTemplateConfiguration')->name('Verify budget template configuration');
Route::post('saveBudgetDetailTemplateEntries', 'DepartmentBudgetPlanningDetailAPIController@saveBudgetDetailTemplateEntries')->name('Save budget detail template entries');
Route::get('getBudgetDetailTemplateEntries', 'DepartmentBudgetPlanningDetailAPIController@getBudgetDetailTemplateEntries')->name('Get budget detail template entries');
Route::post('getTemplateDetailFormData', 'DepartmentBudgetPlanningDetailAPIController@getTemplateDetailFormData')->name('Get budget template detail form data');
Route::post('deleteBudgetPlanningTemplateDetailRow', 'DepartmentBudgetPlanningDetailAPIController@deleteBudgetPlanningTemplateDetailRow')->name('Delete budget planning template detail row');


// Budget Det Template Entries Routes
Route::resource('budgetDetTemplateEntries', 'BudgetDetTemplateEntryAPIController');
Route::resource('budgetDetTemplateEntryData', 'BudgetDetTemplateEntryDataAPIController');
Route::apiResource('budget_template_comments', 'BudgetTemplateCommentAPIController');

Route::get('budgetDetTemplateEntries/byBudgetDetail/{budgetDetailId}', 'BudgetDetTemplateEntryAPIController@getByBudgetDetail')->name('Get by budget details');
Route::get('budgetDetTemplateEntries/byBudgetDetailPaginated/{budgetDetailId}', 'BudgetDetTemplateEntryAPIController@getByBudgetDetailPaginated')->name('Get by budget detail paginated');
Route::delete('budgetDetTemplateEntries/byBudgetDetail/{budgetDetailId}', 'BudgetDetTemplateEntryAPIController@deleteByBudgetDetail')->name('Delete by budget detail');

// Budget Det Template Entry Data Routes
Route::get('budgetDetTemplateEntryData/byEntry/{entryID}', 'BudgetDetTemplateEntryDataAPIController@getByEntry')->name('Get by entry data');
Route::get('budgetDetTemplateEntryData/byTemplateColumn/{templateColumnID}', 'BudgetDetTemplateEntryDataAPIController@getByTemplateColumn')->name('Get by template column data');
Route::post('budgetDetTemplateEntryData/updateOrCreate', 'BudgetDetTemplateEntryDataAPIController@updateOrCreate')->name('updateOrCreate');
Route::post('budgetDetTemplateEntryData/byEntryIds', 'BudgetDetTemplateEntryDataAPIController@getByEntryIds')->name('Get budget detail template entries');

// Budget Template Comments Routes
Route::get('budget_template_comments_by_detail/{budgetDetailId}', 'BudgetTemplateCommentAPIController@getByBudgetDetail')->name('Get budget detail template comments');
Route::get('budget_template_comment_replies/{commentId}', 'BudgetTemplateCommentAPIController@getReplies')->name('Get budget detail template comments replies');



//Budget Control Routes
Route::post('getBudgetControl', 'BudgetControlInfoAPIController@getBudgetControl')->name("Get budget control");
Route::resource('budget_control_infos', 'BudgetControlInfoAPIController');
Route::resource('budget_control_links', 'BudgetControlLinkAPIController');

// Budget Planning Detail Columns Routes
Route::post('getAllDeptBudgetPlDetColumns', 'DepBudgetPlDetColumnAPIController@getAllDeptBudgetPlDetColumns')->name('Get all department budget planning detail columns');
Route::post('saveDepBudgetPlEmpColumns', 'DepBudgetPlDetEmpColumnAPIController@saveDepBudgetPlEmpColumns')->name('Save budget detail planning detail columns');
Route::post('getDepBudgetPlDetEmpColumns', 'DepBudgetPlDetEmpColumnAPIController@getDepBudgetPlDetEmpColumns')->name('Get budget detail planning detail columns');
Route::resource('budget_pl_temp_attachments', 'BudgetPlanningDetailTempAttachmentAPIController');
Route::post('downloadBudgetTempAttachment', 'BudgetPlanningDetailTempAttachmentAPIController@downloadBudgetTempAttachment')->name('Download time extension attachment');

Route::post('getItemsForBudgetPlanningTemplateDetails', 'BudgetDetTemplateEntryDataAPIController@getItemsForBudgetPlanningTemplateDetails')->name('Get budget detail planning detail columns');
Route::post('deleteBudgetTemplateComment', 'BudgetTemplateCommentAPIController@deleteBudgetTemplateComment')->name('Delete budget template comment');
Route::post('deleteTemplateDetailAttachment', 'BudgetPlanningDetailTempAttachmentAPIController@deleteTemplateDetailAttachment')->name('Delete budget template attachment');
Route::post('updateBudgetTemplateComment', 'BudgetTemplateCommentAPIController@updateBudgetTemplateComment')->name('Update budget template comment');

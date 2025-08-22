<?php
Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('generateBudgetReport', 'Budget\BudgetReportController@generateReport');
        Route::post('exportBudgetReport', 'Budget\BudgetReportController@export');
    });
});

//workflow configuration
Route::group([], function () {
    Route::post('getWorkflowConfiguration', 'WorkflowConfigurationAPIController@getWorkflowConfiguration');
    Route::get('getWorkflowConfigurationFormData', 'WorkflowConfigurationAPIController@getWorkflowConfigurationFormData');
    Route::post('changeWorkflowConfigurationStatus', 'WorkflowConfigurationAPIController@changeWorkflowConfigurationStatus');
});

// Budget Template Routes
Route::resource('budget_templates', 'BudgetTemplateAPIController');
Route::post('getAllBudgetTemplates', 'BudgetTemplateAPIController@getAllBudgetTemplates')->name('Get all budget templates');
Route::get('getBudgetTemplateFormData', 'BudgetTemplateAPIController@getBudgetTemplateFormData')->name('Get budget template form data');
Route::get('getBudgetTemplatesByType/{type}', 'BudgetTemplateAPIController@getBudgetTemplatesByType')->name('Get budget templates by type');
Route::post('exportBudgetTemplates', 'BudgetTemplateAPIController@exportBudgetTemplates')->name('Export budget templates');

// Budget Template Pre Columns routes
Route::get('budget_template_pre_columns/grouped', 'BudgetTemplatePreColumnAPIController@getAvailableColumnsGrouped');
Route::get('budget_template_pre_columns/unassigned/{templateId}', 'BudgetTemplatePreColumnAPIController@getUnassignedColumns');
Route::get('budget_template_pre_columns/column-types', 'BudgetTemplatePreColumnAPIController@getColumnTypeOptions');
Route::resource('budget_template_pre_columns', 'BudgetTemplatePreColumnAPIController');

// Budget Template Columns routes
Route::get('budget_template_columns/template/{templateId}', 'BudgetTemplateColumnAPIController@getTemplateColumns');
Route::post('budget_template_columns/template/{templateId}/sort-order', 'BudgetTemplateColumnAPIController@updateSortOrder');
Route::delete('budget_template_columns/template/{templateId}/column/{preColumnId}', 'BudgetTemplateColumnAPIController@removeFromTemplate');
Route::get('budget_template_columns/template/{templateId}/formula-references/{excludeColumnId?}', 'BudgetTemplateColumnAPIController@getFormulaReferenceColumns');
Route::resource('budget_template_columns', 'BudgetTemplateColumnAPIController');

// Budget Planning Routes
Route::get('getBudgetPlanningFormData', 'CompanyBudgetPlanningAPIController@getBudgetPlanningFormData')->name("Get budget planning form data");
Route::post('getBudgetPlanningMasterData', 'CompanyBudgetPlanningAPIController@getBudgetPlanningMasterData')->name("Get budget planning master data");
Route::post('exportBudgetPlanning', 'CompanyBudgetPlanningAPIController@exportBudgetPlanning')->name('Export budget planning to Excel');
Route::post('validateBudgetPlanning', 'CompanyBudgetPlanningAPIController@validateBudgetPlanning')->name('Validate budget planning');
Route::post('checkBudgetPlanningInProgress', 'CompanyBudgetPlanningAPIController@checkBudgetPlanningInProgress')->name('Check budget planning in progress');
Route::post('updateBudgetPlanningStatus', 'DepartmentBudgetPlanningAPIController@updateStatus')->name('Update budget planning status');
Route::post('createTimeExtensionRequest', 'DepartmentBudgetPlanningAPIController@createTimeExtensionRequest')->name('Create time extension request');
Route::post('getTimeExtensionRequests', 'DepartmentBudgetPlanningAPIController@getTimeExtensionRequests')->name('Get time extension requests');
Route::post('cancelDepartmentTimeExtensionRequests', 'DepartmentBudgetPlanningAPIController@cancelDepartmentTimeExtensionRequests')->name('Cancel time extension requests');
Route::get('generateTimeExtensionRequestCode/{budgetPlanningId}', 'DepartmentBudgetPlanningAPIController@generateTimeExtensionRequestCode')->name('Generate time extension request code');
Route::get('getTimeExtensionRequestAttachments/{timeRequestId}', 'DepartmentBudgetPlanningAPIController@getTimeExtensionRequestAttachments')->name('Get time extension request attachments');
Route::get('downloadTimeExtensionAttachment/{attachmentId}', 'DepartmentBudgetPlanningAPIController@downloadTimeExtensionAttachment')->name('Download time extension attachment');
Route::post('getReversions', 'DepartmentBudgetPlanningAPIController@getReversions')->name('Get reversions');

// Department Budget Planning Details Routes
Route::post('getDepartmentBudgetPlanningDetails', 'DepartmentBudgetPlanningDetailAPIController@getByDepartmentPlanning')->name('Get department budget planning details');
Route::post('updateDepartmentBudgetPlanningDetailStatus', 'DepartmentBudgetPlanningDetailAPIController@updateInternalStatus')->name('Update department budget planning detail status');
Route::post('getDepartmentBudgetPlanningSummary', 'DepartmentBudgetPlanningDetailAPIController@getSummary')->name('Get department budget planning summary');
Route::resource('departmentBudgetPlanningDetails', 'DepartmentBudgetPlanningDetailAPIController');

// Budget Delegate Access Routes
Route::get('getActiveAccessTypes', 'BudgetDelegateAPIController@getActiveAccessTypes')->name('Get active access types');
Route::post('getDepartmentEmployees', 'BudgetDelegateAPIController@getDepartmentEmployees')->name('Get department employees for delegation');
Route::post('getDelegateAccessRecords', 'BudgetDelegateAPIController@getDelegateAccessRecords')->name('Get delegate access records');
Route::post('createOrUpdateDelegateAccess', 'BudgetDelegateAPIController@createOrUpdateDelegateAccess')->name('Create or update delegate access');
Route::post('removeDelegateAccess', 'BudgetDelegateAPIController@removeDelegateAccess')->name('Remove delegate access');
Route::post('getDelegateAccessSummary', 'BudgetDelegateAPIController@getDelegateAccessSummary')->name('Get delegate access summary');
Route::post('getBudgetPlanningChartOfAccounts', 'DepartmentBudgetPlanningDetailAPIController@getBudgetPlanningChartOfAccounts');
Route::post('getBudgetPlanningSegments', 'DepartmentBudgetPlanningDetailAPIController@getBudgetPlanningSegments');

// Budget Template Configuration Verification
Route::get('verifyBudgetTemplateConfiguration/{budgetTemplateId}', 'DepartmentBudgetPlanningDetailAPIController@verifyBudgetTemplateConfiguration')
    ->name('Verify budget template configuration');

// Save Budget Detail Template Entries
Route::post('saveBudgetDetailTemplateEntries', 'DepartmentBudgetPlanningDetailAPIController@saveBudgetDetailTemplateEntries')
    ->name('Save budget detail template entries');

// Get Budget Detail Template Entries
Route::get('getBudgetDetailTemplateEntries/{budgetDetailId}', 'DepartmentBudgetPlanningDetailAPIController@getBudgetDetailTemplateEntries')
    ->name('Get budget detail template entries');

// Budget Det Template Entries Routes
Route::resource('budgetDetTemplateEntries', 'BudgetDetTemplateEntryAPIController');
Route::get('budgetDetTemplateEntries/byBudgetDetail/{budgetDetailId}', 'BudgetDetTemplateEntryAPIController@getByBudgetDetail');
Route::get('budgetDetTemplateEntries/byBudgetDetailPaginated/{budgetDetailId}', 'BudgetDetTemplateEntryAPIController@getByBudgetDetailPaginated');
Route::delete('budgetDetTemplateEntries/byBudgetDetail/{budgetDetailId}', 'BudgetDetTemplateEntryAPIController@deleteByBudgetDetail');

// Budget Det Template Entry Data Routes
Route::resource('budgetDetTemplateEntryData', 'BudgetDetTemplateEntryDataAPIController');
Route::get('budgetDetTemplateEntryData/byEntry/{entryID}', 'BudgetDetTemplateEntryDataAPIController@getByEntry');
Route::get('budgetDetTemplateEntryData/byTemplateColumn/{templateColumnID}', 'BudgetDetTemplateEntryDataAPIController@getByTemplateColumn');
Route::post('budgetDetTemplateEntryData/updateOrCreate', 'BudgetDetTemplateEntryDataAPIController@updateOrCreate');
Route::post('budgetDetTemplateEntryData/byEntryIds', 'BudgetDetTemplateEntryDataAPIController@getByEntryIds');

// Budget Template Comments Routes
Route::apiResource('budget-template-comments', 'BudgetTemplateCommentAPIController');
Route::get('budget-template-comments-by-detail/{budgetDetailId}', 'BudgetTemplateCommentAPIController@getByBudgetDetail');
Route::get('budget-template-comment-replies/{commentId}', 'BudgetTemplateCommentAPIController@getReplies');



//Budget Control Routes
Route::post('getBudgetControl', 'BudgetControlInfoAPIController@getBudgetControl')->name("Get budget control");
Route::resource('budget_control_infos', 'BudgetControlInfoAPIController');
Route::resource('budget_control_links', 'BudgetControlLinkAPIController');

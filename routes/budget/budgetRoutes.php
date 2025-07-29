<?php
Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('generateBudgetReport', 'Budget\BudgetReportController@generateReport');
        Route::post('exportBudgetReport', 'Budget\BudgetReportController@export');
    });
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

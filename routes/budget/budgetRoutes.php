<?php
Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('generateBudgetReport', 'Budget\BudgetReportController@generateReport');
        Route::post('exportBudgetReport', 'Budget\BudgetReportController@export');
    });
});

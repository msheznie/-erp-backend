





Route::resource('monthlyAdditionsMasters', 'MonthlyAdditionsMasterController');

Route::resource('monthlyAdditionDetails', 'MonthlyAdditionDetailController');

Route::resource('employmentTypes', 'EmploymentTypeController');

Route::resource('periodMasters', 'PeriodMasterController');

Route::resource('salaryProcessMasters', 'SalaryProcessMasterController');

Route::resource('salaryProcessEmploymentTypes', 'SalaryProcessEmploymentTypesController');

Route::resource('fixedAssetMasterReferredHistories', 'FixedAssetMasterReferredHistoryController');

Route::resource('depreciationMasterReferredHistories', 'DepreciationMasterReferredHistoryController');

Route::resource('depreciationPeriodsReferredHistories', 'DepreciationPeriodsReferredHistoryController');
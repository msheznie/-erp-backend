<?php

//approval group
Route::group([], function(){

    // Journal Vouchers
    Route::post('getJournalVoucherMasterApproval', 'JvMasterAPIController@getJournalVoucherMasterApproval')->name("Get pending for approval JV");
    Route::post('getApprovedJournalVoucherForCurrentUser', 'JvMasterAPIController@getApprovedJournalVoucherForCurrentUser')->name("Get approved JV");
    Route::post('approveJournalVoucher', 'JvMasterAPIController@approveJournalVoucher')->name("Approve JV");
    Route::post('rejectJournalVoucher', 'JvMasterAPIController@rejectJournalVoucher')->name("Reject JV");;
    Route::post('approvalPreCheckJV', 'JvMasterAPIController@approvalPreCheckJV')->name("Approve Pre Check JV");

    Route::post('getJournalVoucherAmendHistory', 'JvMasterReferredbackAPIController@getJournalVoucherAmendHistory')->name("Get JV amend history");
    Route::resource('jvMasterReferredbacks', 'JvMasterReferredbackAPIController');
    Route::get('getJVDetailAmendHistory', 'JvDetailsReferredbackAPIController@getJVDetailAmendHistory')->name("Get JV detail amend history");
    Route::get('getCompanyReportingCurrency', 'CurrencyMasterAPIController@getCompanyReportingCurrency')->name("Get company reporting currency");
    Route::get('getGLForJournalVoucherDirect', 'ChartOfAccountsAssignedAPIController@getGLForJournalVoucherDirect')->name("Get gl for JV direct");

    
    //budgets
    Route::post('getBudgetApprovedByUser', 'BudgetMasterAPIController@getBudgetApprovedByUser')->name("Get pending for approval - Budget");
    Route::post('getBudgetApprovalByUser', 'BudgetMasterAPIController@getBudgetApprovalByUser')->name("Get approved - Budget");
    Route::post('addBudgetAdjustment', 'BudgetAdjustmentAPIController@addBudgetAdjustment')->name("Add budget adjustment");


    //budget transfers
    Route::post('getBudgetTransferApprovalByUser', 'BudgetTransferFormAPIController@getBudgetTransferApprovalByUser')->name("Get pending for approval - Budget trasfer");
    Route::post('getBudgetTransferApprovedByUser', 'BudgetTransferFormAPIController@getBudgetTransferApprovedByUser')->name("Get approved - Budget trasfer");

    // Contingency Budgets
    Route::post('get_contingency_budget_approved', 'ContingencyBudgetPlanAPIController@get_contingency_budget_approved')->name("Get pending for approval - Contingency budget");
    Route::post('approve_contingency_budget', 'ContingencyBudgetPlanAPIController@approve_contingency_budget')->name("Approve Contingency budget");
    Route::post('reject_contingency_budget', 'ContingencyBudgetPlanAPIController@reject_contingency_budget')->name("Reject Contingency budget");

    // Budget Additions
    Route::post('getBudgetAdditionApprovalByUser', 'ErpBudgetAdditionAPIController@getBudgetAdditionApprovalByUser')->name("Get pending for approval - Budget Addition");
    Route::post('getBudgetAdditionApprovedByUser', 'ErpBudgetAdditionAPIController@getBudgetAdditionApprovedByUser')->name("Get approved - Budget Addition");

    // VAT return filling
    Route::post('getVRFApprovalByUser', 'VatReturnFillingMasterAPIController@getVRFApprovalByUser')->name("Get pending for approval - VRF");
    Route::post('getVRFApprovedByUser', 'VatReturnFillingMasterAPIController@getVRFApprovedByUser')->name("Get approved- VRF");

    //Recurring Voucher
    Route::post('getRecurringVoucherMasterApproval', 'RecurringVoucherSetupAPIController@getRecurringVoucherMasterApproval')->name("Get pending for approval RRV");
    Route::post('getApprovedRecurringVoucherForCurrentUser', 'RecurringVoucherSetupAPIController@getApprovedRecurringVoucherForCurrentUser')->name("Get approved RRV");
    Route::post('approveRecurringVoucher', 'RecurringVoucherSetupAPIController@approveRecurringVoucher')->name("Approve RRV");
    Route::post('rejectRecurringVoucher', 'RecurringVoucherSetupAPIController@rejectRecurringVoucher')->name("Reject RRV");;
});


//Transcations group
Route::group([], function(){

    // Journal Vouchers
    Route::resource('journalVoucherCRUD', 'JvMasterAPIController');
    Route::resource('jv_details', 'JvDetailAPIController');

    Route::get('getJournalVoucherMasterFormData', 'JvMasterAPIController@getJournalVoucherMasterFormData')->name('Get JV master form data');
    Route::get('getJournalVoucherMasterRecord', 'JvMasterAPIController@getJournalVoucherMasterRecord')->name('Get JV master record');
    Route::get('getJournalVoucherDetails', 'JvDetailAPIController@getJournalVoucherDetails')->name('Get JV details');
    Route::get('getJournalVoucherContracts', 'JvDetailAPIController@getJournalVoucherContracts')->name('Get JV contracts');
    Route::get('journalVoucherForSalaryJVMaster', 'JvMasterAPIController@journalVoucherForSalaryJVMaster')->name('JV for salary JV master');
    Route::get('journalVoucherForSalaryJVDetail', 'JvMasterAPIController@journalVoucherForSalaryJVDetail')->name('JV for salary JV details');
    Route::get('journalVoucherForAccrualJVMaster', 'JvMasterAPIController@journalVoucherForAccrualJVMaster')->name('Get JV for accrual JV master');
    Route::get('journalVoucherForAccrualJVDetail', 'JvMasterAPIController@journalVoucherForAccrualJVDetail')->name('Get JV for accrual JV detail');
    Route::get('exportStandardJVFormat', 'JvMasterAPIController@exportStandardJVFormat')->name('Export Standard JV');

    Route::post('journalVoucherDeleteAllSJ', 'JvDetailAPIController@journalVoucherDeleteAllSJ')->name('JV delete all SJ');
    Route::post('journalVoucherSalaryJVDetailStore', 'JvDetailAPIController@journalVoucherSalaryJVDetailStore')->name('Store JV salary  JV details');
    Route::post('generateAllocation', 'JvDetailAPIController@generateAllocation')->name('Generate allocation - JV');
    Route::post('getJournalVoucherMasterView', 'JvMasterAPIController@getJournalVoucherMasterView')->name('Get JV master view');
    Route::post('copyJV', 'JvMasterAPIController@copyJV')->name('Copy JV');
    Route::post('jvDetailsExportToCSV', 'JvDetailAPIController@jvDetailsExportToCSV')->name('JV export to csv');
    Route::post('journalVoucherForPOAccrualJVDetail', 'JvMasterAPIController@journalVoucherForPOAccrualJVDetail')->name('Get JV for PO accrual');
    Route::post('exportJournalVoucherForPOAccrualJVDetail', 'JvMasterAPIController@exportJournalVoucherForPOAccrualJVDetail')->name('Export JV for PO accrual');
    Route::post('journalVoucherAccrualJVDetailStore', 'JvDetailAPIController@journalVoucherAccrualJVDetailStore')->name('Store JV for JV details');
    Route::post('journalVoucherPOAccrualJVDetailStore', 'JvDetailAPIController@journalVoucherPOAccrualJVDetailStore')->name('Store JV for PO JV details');
    Route::post('journalVoucherDeleteAllAJ', 'JvDetailAPIController@journalVoucherDeleteAllAJ')->name('Delete JV all AJ');
    Route::post('journalVoucherDeleteAllPOAJ', 'JvDetailAPIController@journalVoucherDeleteAllPOAJ')->name('Delete JV all POAJ');
    Route::post('journalVoucherDeleteAllDetails', 'JvDetailAPIController@journalVoucherDeleteAllDetails')->name('Delete JV All Details');
    Route::post('journalVoucherReopen', 'JvMasterAPIController@journalVoucherReopen')->name('JV Reopen');
    Route::post('getJournalVoucherAmend', 'JvMasterAPIController@getJournalVoucherAmend')->name('JV Amend');
    Route::post('amendJournalVoucherReview', 'JvMasterAPIController@amendJournalVoucherReview')->name('JV Review');
    Route::post('journalVoucherBudgetUpload', 'JvMasterAPIController@journalVoucherBudgetUpload')->name('JV Budget Upload');
    Route::post('standardJvExcelUpload', 'JvMasterAPIController@standardJvExcelUpload')->name('JV excel upload');
    Route::post('getBudgetUploads', 'BudgetMasterAPIController@getBudgetUploads')->name('Get upload budgets');
    Route::post('downloadBudgetTemplate', 'BudgetMasterAPIController@downloadBudgetTemplate')->name('Download budget template');
    Route::post('syncGlBudget', 'BudjetdetailsAPIController@syncGlBudget')->name('Sync gl budget');
    Route::post('getBudgetDetailComment', 'BudgetDetailCommentAPIController@getBudgetDetailComment')->name('Get budget detail comment');
    Route::post('getBudgetDetailHistory', 'BudjetdetailsAPIController@getBudgetDetailHistory')->name('Get budget detail history');

    //budgets
    Route::resource('budget_masters', 'BudgetMasterAPIController');
    Route::resource('budget_master_reffered_histories', 'BudgetMasterRefferedHistoryAPIController');
    Route::resource('budget_details_reffered_histories', 'BudgetDetailsRefferedHistoryAPIController');
    Route::resource('budget_detail_comments', 'BudgetDetailCommentAPIController');
    

    Route::get('getBudgetFormData', 'BudgetMasterAPIController@getBudgetFormData')->name('Get budget form data');
    Route::get('downloadBudgetUploadTemplate', 'BudgetMasterAPIController@downloadBudgetUploadTemplate')->name('Download budget upload template');
    Route::get('getBudgetAudit', 'BudgetMasterAPIController@getBudgetAudit')->name('Get budget audit');

    Route::post('getBudgetsByCompany', 'BudgetMasterAPIController@getBudgetsByCompany')->name('Get budget by company');
    Route::post('budgetReferBack', 'BudgetMasterAPIController@budgetReferBack')->name('Budget Referback');
    Route::post('updateCutOffPeriod', 'BudgetMasterAPIController@updateCutOffPeriod')->name('Update budget cutoff period');
    Route::post('budgetReopen', 'BudgetMasterAPIController@budgetReopen')->name('Budget reopen');
    Route::post('reportBudgetGLCodeWise', 'BudgetMasterAPIController@reportBudgetGLCodeWise')->name('Get budget GL Wise report');
    Route::post('budgetGLCodeWiseDetails', 'BudgetMasterAPIController@budgetGLCodeWiseDetails')->name('Get budget GL Wise report details');
    Route::post('exportBudgetGLCodeWise', 'BudgetMasterAPIController@exportBudgetGLCodeWise')->name('Export budget GL code wise');
    Route::post('exportBudgetTemplateCategoryWise', 'BudgetMasterAPIController@exportBudgetTemplateCategoryWise')->name('Export budget template category wise');
    Route::post('exportBudgetGLCodeWiseDetails', 'BudgetMasterAPIController@exportBudgetGLCodeWiseDetails')->name('Export budget GL code wise details');
    Route::post('reportBudgetTemplateCategoryWise', 'BudgetMasterAPIController@reportBudgetTemplateCategoryWise')->name('Report budget template category wise');
    Route::post('getBudgetAmendHistory', 'BudgetMasterRefferedHistoryAPIController@getBudgetAmendHistory');
    Route::post('getDetailsByBudgetRefereback', 'BudgetDetailsRefferedHistoryAPIController@getDetailsByBudgetRefereback');

    //budget transfers
    Route::resource('budget_transfer', 'BudgetTransferFormAPIController');
    Route::resource('budget_transfer_details', 'BudgetTransferFormDetailAPIController');
    Route::resource('budget_review_transfer_additions', 'BudgetReviewTransferAdditionAPIController');

    Route::get('getBudgetTransferAudit', 'BudgetTransferFormAPIController@getBudgetTransferAudit')->name('Get budget transfer audit');
    Route::get('getBudgetTransferFormData', 'BudgetTransferFormAPIController@getBudgetTransferFormData')->name('Get budget transfer form data');
    Route::get('checkBudgetAllocation', 'BudgetTransferFormDetailAPIController@checkBudgetAllocation')->name('Check budget allocation');
    Route::get('getDetailsByBudgetTransfer', 'BudgetTransferFormDetailAPIController@getDetailsByBudgetTransfer')->name('Get details by budget transfer');
    Route::get('getBudgetReviewTransferAddition', 'BudgetReviewTransferAdditionAPIController@getBudgetReviewTransferAddition')->name('Get budget review transfer additions');
    Route::get('budget_transfer_amend/{id}', 'BudgetTransferFormRefferedBackAPIController@budgetTransferAmend')->name('Budget transfer amend');
    Route::get('getDetailsByBudgetTransferAmend', 'BudgetTransferFormDetailRefferedBackAPIController@getDetailsByBudgetTransferAmend')->name('Get details by budget transfer amend');
    Route::get('budget_addition_amend/{id}', 'BudgetAdditionRefferedBackAPIController@budget_addition_amend');
    Route::get('getDetailsByBudgetAdditionAmend', 'BudgetAdditionRefferedBackAPIController@getDetailsByBudgetAdditionAmend');


    Route::post('budgetTransferCreateFromReview', 'BudgetTransferFormAPIController@budgetTransferCreateFromReview')->name('Create Budget Transfer from review');
    Route::post('budgetTransferReopen', 'BudgetTransferFormAPIController@budgetTransferReopen')->name('Budget transfer reopen');
    Route::post('getBudgetTransferMasterByCompany', 'BudgetTransferFormAPIController@getBudgetTransferMasterByCompany')->name('Get budget master by company');
    Route::post('amendBudgetTrasfer', 'BudgetTransferFormAPIController@amendBudgetTrasfer')->name('Amend budget transfer');
    Route::post('getBudgetTransferAmendHistory', 'BudgetTransferFormRefferedBackAPIController@getBudgetTransferAmendHistory')->name('Get budget transfer amend history');
    Route::post('amendBudgetAddition', 'ErpBudgetAdditionAPIController@amendBudgetAddition')->name('Amend budget addition');
    Route::post('getBudgetAdditionAmendHistory', 'BudgetAdditionRefferedBackAPIController@getBudgetAdditionAmendHistory');

    // Contingency Budgets
    Route::resource('contingency_budget_plans', 'ContingencyBudgetPlanAPIController');

    Route::get('contingency_budget_list', 'ContingencyBudgetPlanAPIController@budget_list')->name('Get contingency budget list');
    Route::get('getContingencyBudgetFormData', 'ContingencyBudgetPlanAPIController@getFormData')->name('Get contingency budget form data');
    Route::get('getBudgetAmount/{id}', 'ContingencyBudgetPlanAPIController@getBudgetAmount')->name('Get contingency budget amount');

    // Budget Additions
    Route::resource('budget_addition', 'ErpBudgetAdditionAPIController');
    Route::resource('budget_addition_details', 'ErpBudgetAdditionDetailAPIController');
    Route::resource('budjetdetails', 'BudjetdetailsAPIController');
    Route::resource('templates_g_l_codes', 'TemplatesGLCodeAPIController');
    Route::resource('templates_masters', 'TemplatesMasterAPIController');
    Route::resource('templates_details', 'TemplatesDetailsAPIController');

    Route::get('getTemplatesDetailsByBudgetAddition', 'ErpBudgetAdditionAPIController@getTemplatesDetailsByBudgetAddition')->name('Budget Addition get template details');
    Route::get('getAllGLCodesByBudgetAddition', 'ErpBudgetAdditionAPIController@getAllGLCodesByBudgetAddition')->name('Budget Additon Get All Gl codes');
    Route::get('getDetailsByBudgetAddition', 'ErpBudgetAdditionDetailAPIController@getDetailsByBudgetAddition')->name('Get Details by Budget Addition');
    Route::get('getTemplateByGLCodeByBudgetAddition', 'ErpBudgetAdditionAPIController@getTemplateByGLCodeByBudgetAddition')->name('Get template by budget addition');
    Route::get('getBudgetAdditionFormData', 'ErpBudgetAdditionAPIController@getBudgetAdditionFormData')->name('Get Budget addition form data');
    Route::get('getTemplatesDetailsByMaster', 'TemplatesDetailsAPIController@getTemplatesDetailsByMaster')->name('Get templates details by master');
    Route::get('getTemplatesDetailsById', 'TemplatesDetailsAPIController@getTemplatesDetailsById')->name('Get templates details by id');
    Route::get('getAllGLCodesByTemplate', 'TemplatesDetailsAPIController@getAllGLCodesByTemplate')->name('Get all gl codes by template');
    Route::get('getAllGLCodes', 'TemplatesDetailsAPIController@getAllGLCodes')->name('Get all gl codes');
    Route::get('getTemplateByGLCode', 'TemplatesDetailsAPIController@getTemplateByGLCode')->name('Get template by gl code');
    Route::get('getBudgetDetailTotalSummary', 'BudjetdetailsAPIController@getBudgetDetailTotalSummary')->name('Get budget details total summary');
    
    Route::post('budget_additions', 'ErpBudgetAdditionAPIController@index')->name('Get Budget Addition');
    Route::post('getDetailsByBudget', 'BudjetdetailsAPIController@getDetailsByBudget')->name('Get details by budget addition');
    Route::post('getDetailsByBudgetNew', 'BudjetdetailsAPIController@getDetailsByBudgetNew')->name('Get details by budget addition new');
    Route::post('getGLCodesByBudgetCategory', 'BudjetdetailsAPIController@getGLCodesByBudgetCategory')->name('Get glcodes by budget category');
    Route::post('exportDetailsByBudget', 'BudjetdetailsAPIController@exportReport')->name('Export details by budget addition');
    Route::post('removeBudgetDetails', 'BudjetdetailsAPIController@removeBudgetDetails')->name('Remove budget addition details');
    Route::post('bulkUpdateBudgetDetails', 'BudjetdetailsAPIController@bulkUpdateBudgetDetails')->name('Bulk update budget addition');
    Route::post('budgetDetailsUpload', 'BudjetdetailsAPIController@budgetDetailsUpload')->name('Budget details update');
    Route::post('disposalReopen', 'AssetDisposalMasterAPIController@disposalReopen')->name('Asset dissposal reopen');
    Route::post('referBackDisposal', 'AssetDisposalMasterAPIController@referBackDisposal')->name('Refer back disposal');
    Route::post('amendAssetDisposalReview', 'AssetDisposalMasterAPIController@amendAssetDisposalReview')->name('Amend disposal review');
    Route::post('budgetAdditionReopen', 'ErpBudgetAdditionAPIController@budgetAdditionReopen')->name('Budget addition re open');
    Route::get('getBudgetAdditionAudit', 'ErpBudgetAdditionAPIController@getBudgetAdditionAudit')->name('Get budget addition audit');

    // console jv
    Route::resource('console_j_v_masters', 'ConsoleJVMasterAPIController');
    Route::resource('console_j_v_details', 'ConsoleJVDetailAPIController');

    Route::get('getConsoleJVGL', 'ConsoleJVMasterAPIController@getConsoleJVGL')->name('Get Console JVGL');
    Route::get('getConsoleJVMasterFormData', 'ConsoleJVMasterAPIController@getConsoleJVMasterFormData')->name('Get console JV master form data');
    Route::get('getConsoleJVDetailByMaster', 'ConsoleJVDetailAPIController@getConsoleJVDetailByMaster')->name('Get console JV details by master');

    Route::post('getAllConsoleJV', 'ConsoleJVMasterAPIController@getAllConsoleJV')->name('Get All Console JV');
    Route::post('consoleJVReopen', 'ConsoleJVMasterAPIController@consoleJVReopen')->name('Console JV Reopen');
    Route::post('deleteAllConsoleJVDet', 'ConsoleJVDetailAPIController@deleteAllConsoleJVDet')->name('Delete all console JV details');
    Route::post('getConsoleJvApproval', 'ConsoleJVMasterAPIController@getConsoleJvApproval')->name('Get Console JV Approval');
    Route::post('getApprovedConsoleJvForCurrentUser', 'ConsoleJVMasterAPIController@getApprovedConsoleJvForCurrentUser')->name('Get Approved Console JV for current user');
    Route::post('approveConsoleJV', 'ConsoleJVMasterAPIController@approveConsoleJV')->name('Approve Console JV');
    Route::post('rejectConsoleJV', 'ConsoleJVMasterAPIController@rejectConsoleJV')->name('Reject Console JV');
    Route::get('getEliminationLedgerReview', 'EliminationLedgerAPIController@getEliminationLedgerReview')->name('Get elimination ledger review');

    // Budget Review

    Route::post('getBudgetBlockedDocuments', 'BudgetMasterAPIController@getBudgetBlockedDocuments')->name('Get Budget Blocked Documents');

    //VAT Return Filling
    Route::resource('vat_return_filling_masters', 'VatReturnFillingMasterAPIController');
    Route::resource('vat_return_filling_categories', 'VatReturnFillingCategoryAPIController');
    Route::resource('vat_return_filled_categories', 'VatReturnFilledCategoryAPIController');
    Route::resource('vat_sub_category_types', 'VatSubCategoryTypeAPIController');
    Route::resource('vat_return_filling_details', 'VatReturnFillingDetailAPIController');

    Route::get('getVATReturnFillingData', 'VatReturnFillingMasterAPIController@getVATReturnFillingData')->name('Get VAT Return Filling Data');
    Route::get('getVATReturnFillingFormData', 'VatReturnFillingMasterAPIController@getVATReturnFillingFormData')->name('Get VAT Return filling form data');

    Route::post('getVatReturnFillings', 'VatReturnFillingMasterAPIController@getVatReturnFillings')->name('Get VAT Return filling');
    Route::post('getVatReturnFillingDetails', 'VatReturnFillingMasterAPIController@getVatReturnFillingDetails')->name('Get VAT Return filling details');
    Route::post('updateVatReturnFillingDetails', 'VatReturnFillingMasterAPIController@updateVatReturnFillingDetails')->name('Update VAT Return filling');
    Route::post('vatReturnFillingReopen', 'VatReturnFillingMasterAPIController@vatReturnFillingReopen')->name('VAT Return filling reopen');
    Route::post('getVRFAmend', 'VatReturnFillingMasterAPIController@getVRFAmend')->name('Get VRF Amend');
    Route::post('generateDocumentAgainstVRF', 'VRFDocumentGenerateController@store')->name('Get Document Against VRF');

    // contiungency Budget

    Route::resource('contingency_budget_plans', 'ContingencyBudgetPlanAPIController');
    Route::get('contingencyBudgetAmend/{id}', 'ContingencyBudgetRefferedBackAPIController@contingencyBudgetAmend')->name('Get Contigency Budget');

    Route::get('getContingencyBudgetFormData', 'ContingencyBudgetPlanAPIController@getFormData') ->name('Get Contigency Budget From Data');
    Route::get('getBudgetAmount/{id}', 'ContingencyBudgetPlanAPIController@getBudgetAmount')->name('Get Budget Amount');
    Route::post('get_contingency_budget_approved', 'ContingencyBudgetPlanAPIController@get_contingency_budget_approved')->name('Get Budget Amount Approved');
    Route::post('get_contingency_budget_not_approved', 'ContingencyBudgetPlanAPIController@get_contingency_budget_not_approved')->name('Get Budget Amount Not Approved');
    Route::post('approve_contingency_budget', 'ContingencyBudgetPlanAPIController@approve_contingency_budget')->name('Approve Contigency Budget');
    Route::post('reject_contingency_budget', 'ContingencyBudgetPlanAPIController@reject_contingency_budget')->name('Reject Contigency Budget');
    Route::post('amendContingencyBudget', 'ContingencyBudgetPlanAPIController@amendContingencyBudget')->name('Amend Contigency Budget');
    Route::post('getContingencyAmendHistory', 'ContingencyBudgetRefferedBackAPIController@getContingencyAmendHistory')->name('Get Contigency Budget Amend History');
    Route::post('get_contingency_budget', 'ContingencyBudgetPlanAPIController@get_contingency_budget')->name('Get Contigency Budget');

});

// Reports 

Route::group([], function(){ 

    Route::resource('cash_flow_reports', 'CashFlowReportAPIController');
    Route::get('getUtilizationFilterFormData', 'FinancialReportAPIController@getUtilizationFilterFormData')->name('Get Utilization filter form data');
    Route::get('getVATFilterFormData', 'VATReportAPIController@getVATFilterFormData')->name('Get VAT Filters form data');
    Route::get('getCashFlowFormData', 'CashFlowReportAPIController@getCashFlowFormData')->name('Get Cash flow form data');
    Route::get('getCashFlowReportData', 'CashFlowReportAPIController@getCashFlowReportData')->name('Get Cash flow report data');

    Route::post('validatePUReport', 'FinancialReportAPIController@validatePUReport')->name('Validate PU Report');
    Route::post('generateprojectUtilizationReport', 'FinancialReportAPIController@generateprojectUtilizationReport')->name('Generate project utilization report');
    Route::post('generateEmployeeLedgerReport', 'FinancialReportAPIController@generateEmployeeLedgerReport')->name('Generate Employee ledger report');
    Route::post('get_projects', 'ErpProjectMasterAPIController@get_projects')->name('Get Projects');
    Route::post('validateVATReport', 'VATReportAPIController@validateVATReport')->name('Validate VAT report');
    Route::post('generateVATReport', 'VATReportAPIController@generateVATReport')->name('Generate VAT report');
    Route::post('generateVATDetailReport', 'VATReportAPIController@generateVATDetailReport')->name('Generate VAT detail report');
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::post('exportVATReport', 'VATReportAPIController@exportVATReport')->name('Export VAT report');
            Route::post('exportVATDetailReport', 'VATReportAPIController@exportVATDetailReport')->name('Export VAT detail report');
        });
    });
    Route::post('getAllEmployees', 'EmployeeAPIController@getAllEmployees')->name('Get ALL Employees');
    Route::post('getCashFlowReports', 'CashFlowReportAPIController@getCashFlowReports')->name('Get Cash Flow Reports');
    Route::post('cashFlowConfirmation', 'CashFlowReportAPIController@cashFlowConfirmation')->name('Cash flow confirmation');
    Route::post('getCashFlowPullingItems', 'CashFlowReportAPIController@getCashFlowPullingItems')->name('Get Cash Flow pulling items');
    Route::post('getCashFlowPullingItemsForProceeds', 'CashFlowReportAPIController@getCashFlowPullingItemsForProceeds')->name('Get cashflow pulling items for proceeds');
    Route::post('postCashFlowPulledItems', 'CashFlowReportAPIController@postCashFlowPulledItems')->name('Post Cash flow pulled items');
    Route::post('postCashFlowPulledItemsForProceeds', 'CashFlowReportAPIController@postCashFlowPulledItemsForProceeds')->name('Post Cashflow pulled items for proceeds');
    Route::post('downloadEmployeeLedgerReport', 'FinancialReportAPIController@generateEmployeeLedgerReport')->name('Download Employee ledger report');
    Route::post('downloadProjectUtilizationReport', 'FinancialReportAPIController@downloadProjectUtilizationReport')->name('Download project utilization report');
    Route::post('updateBankBalances', 'CashFlowReportAPIController@updateBankBalances')->name('Update balance amount');

    Route::resource('final_return_income_reports', 'FinalReturnIncomeReportsAPIController');
    Route::resource('final_return_income_rd', 'FinalReturnIncomeReportDetailsAPIController');
    Route::resource('final_return_income_rdv', 'FinalReturnIncomeReportDetailValuesAPIController');
    Route::post('getReportList', 'FinalReturnIncomeReportsAPIController@getReportList');
    Route::get('getFinalIncomeReportFormData', 'FinalReturnIncomeReportsAPIController@getFormData');
    Route::post('checkYearExists', 'FinalReturnIncomeReportsAPIController@checkYearExists');
    Route::get('incomeReportDetails/{id}', 'FinalReturnIncomeReportsAPIController@getIncomeReportDetails');
    Route::post('confirmReturnIncomeReport', 'FinalReturnIncomeReportsAPIController@confirmReturnIncomeReport');
    Route::post('syncGLrecords', 'FinalReturnIncomeReportsAPIController@syncGLrecords');
});


// masters 

Route::group([], function(){ 

    Route::resource('company_finance_years', 'CompanyFinanceYearAPIController');
    Route::resource('project_gl_details', 'ProjectGlDetailAPIController');
    Route::resource('company_finance_periods', 'CompanyFinancePeriodAPIController');
    Route::resource('recurring_voucher_setups', 'RecurringVoucherSetupAPIController');
    Route::resource('recurring_voucher_setup_details', 'RecurringVoucherSetupDetailAPIController');
    Route::resource('recurring_voucher_setup_schedules', 'RecurringVoucherSetupScheduleAPIController');
    Route::resource('recurring_voucher_setup_sche_dets', 'RecurringVoucherSetupScheDetAPIController');

    Route::get('getAllocationConfigurationAssignFormData', 'ChartOfAccountAllocationMasterAPIController@getAllocationConfigurationAssignFormData')->name('Get allocation configuration assign form data');
    Route::resource('coa_allocation_details', 'ChartOfAccountAllocationDetailAPIController');
    Route::resource('coa_allocation_masters', 'ChartOfAccountAllocationMasterAPIController');
    Route::post('getSheduleDetails', 'RecurringVoucherSetupScheDetAPIController@getSheduleDetails')->name('Get shedule details');


    Route::get('getFinanceYearFormData', 'CompanyFinanceYearAPIController@getFinanceYearFormData')->name('Get Finance Year Form data');

    Route::post('getFinancialYearsByCompany', 'CompanyFinanceYearAPIController@getFinancialYearsByCompany')->name('Get Financial Years By Company');
    Route::post('getFinancialPeriodsByYear', 'CompanyFinancePeriodAPIController@getFinancialPeriodsByYear')->name('Get Financial periods by year');

    Route::post('erp_project_masters/get_gl_accounts','ChartOfAccountsAssignedAPIController@getGlAccounts')->name('Get GL Accounts');

    Route::get('getRecurringVoucherMasterFormData', 'RecurringVoucherSetupAPIController@getRecurringVoucherMasterFormData')->name('Get recurring voucher master form data');
    Route::post('getRecurringVoucherMasterView', 'RecurringVoucherSetupAPIController@getRecurringVoucherMasterView')->name('Get recurring voucher master view');
    Route::get('getRecurringVoucherDetails', 'RecurringVoucherSetupDetailAPIController@getRecurringVoucherDetails')->name('Get RRV details');
    Route::get('getRecurringVoucherContracts', 'RecurringVoucherSetupDetailAPIController@getRecurringVoucherContracts')->name('Get RRV contracts');
    Route::get('getGLForRecurringVoucherDirect', 'ChartOfAccountsAssignedAPIController@getGLForRecurringVoucherDirect')->name("Get gl for RRV direct");
    Route::get('getRecurringVoucherMasterRecord', 'RecurringVoucherSetupAPIController@getRecurringVoucherMasterRecord')->name('Get rrv master record');

    Route::post('amendRecurringVoucherReview', 'RecurringVoucherSetupAPIController@amendRecurringVoucherReview')->name('RRV Review');
    Route::post('recurringVoucherReopen', 'RecurringVoucherSetupAPIController@recurringVoucherReopen')->name('RRV Reopen');
    Route::post('recurringVoucherDeleteAllDetails', 'RecurringVoucherSetupDetailAPIController@recurringVoucherDeleteAllDetails')->name('RRV details delete all');
    Route::get('getAllRecurringVoucherSchedules', 'RecurringVoucherSetupScheduleAPIController@getAllRecurringVoucherSchedules')->name('Get all recurring voucher schedules');
    Route::put('recurringVoucherSchedulesAllStop', 'RecurringVoucherSetupScheduleAPIController@recurringVoucherSchedulesAllStop')->name('Stop all recurring voucher schedules');

    Route::get('erp_project_masters/form', 'ErpProjectMasterAPIController@formData')->name('Get project masters form');
    Route::get('erp_project_masters/segments_by_company', 'ErpProjectMasterAPIController@segmentsByCompany')->name('Get project masters by company');
    Route::post('erp_project_masters', 'ErpProjectMasterAPIController@index')->name('Get project masters');
    Route::post('erp_project_masters/create', 'ErpProjectMasterAPIController@store')->name('Create project masters');
    Route::get('erp_project_masters/{id}', 'ErpProjectMasterAPIController@show')->name('Get project masters by ID');
    Route::put('erp_project_masters/{id}', 'ErpProjectMasterAPIController@update')->name('Update project masters');
    Route::post('getglDetails','ChartOfAccountsAssignedAPIController@getglDetails')->name('Get Gl Details');

});


//review


Route::group([], function(){ 
    Route::post('getBudgetConsumptionForReview', 'BudgetConsumedDataAPIController@getBudgetConsumptionForReview')->name('Get Buget Consumption for review');
    Route::post('getBudgetConsumptionByDoc', 'BudgetConsumedDataAPIController@getBudgetConsumptionByDoc')->name('Get budget consumption for review');
    Route::post('changeBudgetConsumption', 'BudgetConsumedDataAPIController@changeBudgetConsumption')->name('Change budget consumption');
    Route::post('vatReturnFillingAmend', 'VatReturnFillingMasterAPIController@vatReturnFillingAmend')->name('Vat return filling amend');
});


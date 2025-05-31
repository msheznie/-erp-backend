<?php
/**
 * This file contains treasury management module related routes
 * 
 * 
 * */


//Approvals
Route::group([], function() {

    Route::post('getBankAccountApprovedByUser', 'BankAccountAPIController@getBankAccountApprovedByUser')->name('Get bank account approved by user');
    Route::post('getBankReconciliationApprovalByUser', 'BankReconciliationAPIController@getBankReconciliationApprovalByUser')->name('Get bank reconciliation approval by user');
    Route::post('getBankReconciliationApprovedByUser', 'BankReconciliationAPIController@getBankReconciliationApprovedByUser')->name('Get bank reconciliation approved by user');
    Route::post('getBankTransferApprovalByUser', 'PaymentBankTransferAPIController@getBankTransferApprovalByUser')->name('Get bank transfer approval by user');
    Route::post('getBankAccountApprovalByUser', 'BankAccountAPIController@getBankAccountApprovalByUser')->name('Get bank account approval by user');
    Route::post('getBankTransferApprovedByUser', 'PaymentBankTransferAPIController@getBankTransferApprovedByUser')->name('Get bank transfer approved by user');
    Route::post('getAllCurrencyConversionApproval', 'CurrencyConversionMasterAPIController@getAllCurrencyConversionApproval')->name('Get all currency conversion approval');

    Route::get('bankReconciliationAudit', 'BankReconciliationAPIController@bankReconciliationAudit')->name('Bank reconciliation audit');
    Route::get('printBankReconciliation', 'BankReconciliationAPIController@printBankReconciliation')->name('Print bank reconciliation');

});

//Bank Reconciliation
Route::group([], function() {

    Route::post('getBankAccountPaymentReceiptByType', 'BankLedgerAPIController@getBankAccountPaymentReceiptByType')->name('Get bank account payment receipt by type');
    Route::post('updateTreasuryCollection', 'BankLedgerAPIController@updateTreasuryCollection')->name('Update treasury collection');
    Route::post('getAllBankAccountByCompany', 'BankAccountAPIController@getAllBankAccountByCompany')->name('Get all bank account by company');
    Route::post('getBankReconciliationsByType', 'BankLedgerAPIController@getBankReconciliationsByType')->name('Get bank reconciliations by type');
    Route::post('bankRecReopen', 'BankReconciliationAPIController@bankRecReopen')->name('Bank reconciliation reopen');
    Route::post('bankReconciliationReferBack', 'BankReconciliationAPIController@bankReconciliationReferBack')->name('Bank reconciliation refer back');
    Route::post('getAllBankReconciliationByBankAccount', 'BankReconciliationAPIController@getAllBankReconciliationByBankAccount')->name('Get all bank reconciliation by bank account');
    Route::post('getReferBackHistoryByBankRec', 'BankReconciliationRefferedBackAPIController@getReferBackHistoryByBankRec')->name('Get refer back history by bank reconciliation');
    Route::post('getBankReconciliationAdditionalEntries', 'BankReconciliationAPIController@getBankReconciliationAdditionalEntries')->name('Get bank reconciliations additional entry');
    Route::post('saveAdditionalEntry', 'BankReconciliationAPIController@saveAdditionalEntry')->name('Add bank reconciliation additional entry');

    Route::get('getBankReconciliationFormData', 'BankReconciliationAPIController@getBankReconciliationFormData')->name('Get bank reconciliation form data');
    Route::get('getCheckBeforeCreate', 'BankReconciliationAPIController@getCheckBeforeCreate')->name('Get check before create');
    Route::get('getAllSegments', 'BankReconciliationAPIController@getAllActiveSegments')->name('Get all active segments');

    Route::resource('bank_accounts', 'BankAccountAPIController');
    Route::resource('bank_ledgers', 'BankLedgerAPIController');
    Route::resource('bank_reconciliations', 'BankReconciliationAPIController');
    Route::resource('bankRecRefferedBack', 'BankReconciliationRefferedBackAPIController');
    Route::resource('bankReconciliationRule', 'BankReconciliationRulesAPIController');

    Route::post('uploadBankStatement', 'BankReconciliationAPIController@uploadBankStatement')->name('Upload Bank Statement');
    Route::post('createTemplateMapping', 'BankReconciliationTemplateMappingAPIController@store')->name('Add bank reconciliation template mapping');
    Route::get('getTemplateMappingDetails', 'BankReconciliationTemplateMappingAPIController@getTemplateMappingDetails')->name('Get template mapping details');
    Route::post('getBankStatementImportHistory', 'BankStatementMasterAPIController@getBankStatementImportHistory')->name('Get bank statement import history');
    Route::post('deleteBankStatement/{id}', 'BankStatementMasterAPIController@deleteBankStatement');
    Route::get('getActiveBankAccountsByBankID', 'BankReconciliationAPIController@getActiveBankAccountsByBankID')->name('Get active bank accounts by bank id');
    Route::post('getBankStatementUploadRules', 'BankReconciliationRulesAPIController@getBankStatementUploadRules')->name('Get active bank accounts by bank id');
    Route::get('getMatchingRuleDetails', 'BankReconciliationRulesAPIController@getMatchingRuleDetails')->name('Get matching rule details');
    Route::post('updateDefaultRule', 'BankReconciliationRulesAPIController@updateDefaultRule')->name('Upload default matching rule');
    Route::post('getBankStatementWorkBook', 'BankStatementMasterAPIController@getBankStatementWorkBook')->name('Upload default matching rule');
    Route::get('validateWorkbookCreation', 'BankStatementMasterAPIController@validateWorkbookCreation')->name('Validate workbook creation');
});

//Cheque Register
Route::group([], function() {
    Route::post('getAllChequeRegistersByCompany', 'ChequeRegisterAPIController@getAllChequeRegistersByCompany')->name('Get all cheque registers by company');
    Route::post('getMultipleAccountsByBank', 'PaySupplierInvoiceMasterAPIController@getMultipleAccountsByBank')->name('Get multiple accounts by bank');
    Route::post('exportChequeRegistry', 'ChequeRegisterAPIController@exportChequeRegistry')->name('Export cheque registry');
    Route::post('checkChequeRegisterStatus', 'ChequeRegisterAPIController@checkChequeRegisterStatus')->name('Check cheque register status');
    Route::post('chequeRegisterStatusChange', 'ChequeRegisterAPIController@chequeRegisterStatusChange')->name('Cheque register status change');
    Route::post('chequeRegisterDetailCancellation', 'ChequeRegisterDetailAPIController@chequeRegisterDetailCancellation')->name('Cheque register detail cancellation');
    Route::post('chequeRegisterDetailSwitch', 'ChequeRegisterDetailAPIController@chequeRegisterDetailSwitch')->name('Cheque register detail switch');
    Route::post('getAllChequeRegisterDetails', 'ChequeRegisterDetailAPIController@getAllChequeRegisterDetails')->name('Get all cheque register details');
    
    Route::get('getAllUnusedCheckDetails', 'ChequeRegisterDetailAPIController@getAllUnusedCheckDetails')->name('Get all unused check details');
    Route::get('getChequeRegisterFormData', 'ChequeRegisterAPIController@getChequeRegisterFormData')->name('Get cheque register form data');
    Route::get('chequeRegisterDetailsAudit', 'ChequeRegisterDetailAPIController@chequeRegisterDetailsAudit')->name('Cheque register details audit');
    Route::get('getChequeSwitchFormData', 'ChequeRegisterDetailAPIController@getChequeSwitchFormData')->name('Get cheque switch form data');
    Route::get('getChequeRegisterByMasterID', 'ChequeRegisterAPIController@getChequeRegisterByMasterID')->name('Get cheque register by master id');

    Route::resource('cheque_registers', 'ChequeRegisterAPIController');
    Route::resource('cheque_register_details', 'ChequeRegisterDetailAPIController');
    Route::resource('cheque_update_reasons', 'ChequeUpdateReasonAPIController');

});

//Bank Reconciliation
Route::group([],function () {
    Route::post('getAllBankReconciliationList', 'BankReconciliationAPIController@getAllBankReconciliationList')->name('Get all bank reconciliation list');
});

//Bank Transfer List
Route::group([],function () {

    Route::post('getAllBankTransferList', 'PaymentBankTransferAPIController@getAllBankTransferList')->name('Get all bank transfer list');
    Route::post('getPaymentsByBankTransfer', 'BankLedgerAPIController@getPaymentsByBankTransfer')->name('Get payments by bank transfer');
    Route::post('paymentBankTransferReopen', 'PaymentBankTransferAPIController@paymentBankTransferReopen')->name('Payment bank transfer reopen');
    Route::post('paymentBankTransferReferBack', 'PaymentBankTransferAPIController@paymentBankTransferReferBack')->name('Payment bank transfer referback');
    Route::post('getReferBackHistoryByBankTransfer', 'PaymentBankTransferRefferedBackAPIController@getReferBackHistoryByBankTransfer')->name('Get referback history by bank transfer');
    Route::post('getAllBankTransferByBankAccount', 'PaymentBankTransferAPIController@getAllBankTransferByBankAccount')->name('Get all bank transfer by bank account');
    Route::post('getAllBankTransferSubmissionList', 'PaymentBankTransferAPIController@getAllBankTransferSubmissionList')->name('Get all bank transfer submission list');

    Route::get('getCheckBeforeCreateBankTransfers', 'PaymentBankTransferAPIController@getCheckBeforeCreate')->name('Get check before create bank transfers');
    Route::get('exportPaymentBankTransferPreCheck', 'PaymentBankTransferAPIController@exportPaymentBankTransferPreCheck')->name('Export payment bank transfer precheck');

    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::post('generateVendorFile','B2B\B2BResourceAPIController@generateVendorFile');
            Route::post('downloadErrorLogFromPortal','B2B\B2BResourceAPIController@downloadErrorLogFromPortal');
        });
    });

    Route::resource('payment_bank_transfers', 'PaymentBankTransferAPIController');
    Route::resource('bankTransferRefferedBack', 'PaymentBankTransferRefferedBackAPIController');

});

//PDC logs
Route::group([],function () {

    Route::get('pdc-logs/get-form-data', 'PdcLogAPIController@getFormData')->name('Pdc logs/ Get form data');
    Route::get('getNextChequeNo', 'PdcLogAPIController@getNextChequeNo')->name('Get next cheque no');

    Route::post('get-all-issued-cheques', 'PdcLogAPIController@getIssuedCheques')->name('Get all issued cheques');
    Route::post('get-all-received-cheques', 'PdcLogAPIController@getAllReceivedCheques')->name('Get all received cheques');
    Route::post('printPdcCheque', 'PdcLogAPIController@printPdcCheque')->name('Print pdc cheque');
    Route::post('updatePrintAhliChequeItems', 'BankLedgerAPIController@updatePrintAhliChequeItems')->name('Update print ahli cheque items');
    Route::post('changePdcChequeStatus', 'PdcLogAPIController@changePdcChequeStatus')->name('Change pdc cheque status');
    Route::post('reverseGeneratedChequeNo', 'PdcLogAPIController@reverseGeneratedChequeNo')->name('Reverse generated cheque no');
    Route::post('issueNewCheque', 'PdcLogAPIController@issueNewCheque')->name('Issue new cheque');

});

//Bank Account
Route::group([],function () {

    Route::post('getAccountsByBank', 'BankAccountAPIController@getAccountsByBank')->name('Get accounts by bank');
    Route::post('bankAccountReopen', 'BankAccountAPIController@bankAccountReopen')->name('Bank account reopen');
    Route::post('bankAccountReferBack', 'BankAccountAPIController@bankAccountReferBack')->name('Bank account refer back');
    Route::post('getAccountsReferBackHistory', 'BankAccountRefferedBackAPIController@getAccountsReferBackHistory')->name('Get accounts refer back history');
    Route::post('getBankMasterByCompany', 'BankAssignAPIController@getBankMasterByCompany')->name('Get bank master by company');

    Route::get('bankAccountAudit', 'BankAccountAPIController@bankAccountAudit')->name('Bank account audit');
    Route::get('getBankAccountFormData', 'BankAccountAPIController@getBankAccountFormData')->name('Get bank account form data');

    Route::resource('bankAccountReferedBack', 'BankAccountRefferedBackAPIController');

});

//Bank Reconciliation Review
Route::group([],function () {

    Route::post('amendBankReconciliationReview', 'BankReconciliationAPIController@amendBankReconciliationReview')->name('Amend bank reconciliation review');

});

//Bank Trasfer Review
Route::group([],function () {

    Route::post('amendBankTransferReview', 'BankLedgerAPIController@amendBankTransferReview')->name('Amend bank transfer review');
    Route::post('clearExportBlockConfirm', 'BankLedgerAPIController@clearExportBlockConfirm')->name('Clear export block confirm');

});

//Report
Route::group([],function () {
    
    Route::post('generateBankLedgerReport', 'BankLedgerAPIController@generateBankLedgerReport')->name('Generate bank ledger report');
    Route::post('exportBankLedgerReport', 'BankLedgerAPIController@exportBankLedgerReport')->name('Export bank ledger report');
    Route::post('generateBankLedgerReportPDF', 'BankLedgerAPIController@generateBankLedgerReportPDF')->name('Generate bank ledger report pdf');
    Route::post('validateBankLedgerReport', 'BankLedgerAPIController@validateBankLedgerReport')->name('Validate bank ledger report');
    Route::post('generateTMReport', 'BankReconciliationAPIController@generateTMReport')->name('Generate tm report');
    Route::post('exportTMReport', 'BankReconciliationAPIController@exportReport')->name('Export tm report');
    Route::post('validateTMReport', 'BankReconciliationAPIController@validateTMReport')->name('Validate tm report');

    Route::get('getBankLedgerFilterFormData', 'BankLedgerAPIController@getBankLedgerFilterFormData')->name('Get bank ledger filter form data');
    Route::get('getTreasuryManagementFilterData', 'BankReconciliationAPIController@getTreasuryManagementFilterData')->name('Get treasury management filter data');


});

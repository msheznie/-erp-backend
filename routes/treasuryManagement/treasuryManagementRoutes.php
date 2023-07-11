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
    
    Route::get('getBankReconciliationFormData', 'BankReconciliationAPIController@getBankReconciliationFormData')->name('Get bank reconciliation form data');
    Route::get('getCheckBeforeCreate', 'BankReconciliationAPIController@getCheckBeforeCreate')->name('Get check before create');

    Route::resource('bank_accounts', 'BankAccountAPIController');
    Route::resource('bank_ledgers', 'BankLedgerAPIController');
    Route::resource('bank_reconciliations', 'BankReconciliationAPIController');
    Route::resource('bankRecRefferedBack', 'BankReconciliationRefferedBackAPIController');

});
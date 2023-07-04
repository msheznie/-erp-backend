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
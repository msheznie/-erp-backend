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

    Route::get('getCheckBeforeCreateBankTransfers', 'PaymentBankTransferAPIController@getCheckBeforeCreate')->name('Get check before create bank transfers');
    Route::get('exportPaymentBankTransferPreCheck', 'PaymentBankTransferAPIController@exportPaymentBankTransferPreCheck')->name('Export payment bank transfer precheck');

    Route::resource('payment_bank_transfers', 'PaymentBankTransferAPIController');
    Route::resource('bankTransferRefferedBack', 'PaymentBankTransferRefferedBackAPIController');

});
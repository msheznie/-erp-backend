<?php
/**
 * This file contains accounts payable module related routes
 * 
 * 
 * */


//transactions
use Illuminate\Support\Facades\Route;

Route::group([], function() {

    Route::resource('supplierInvoiceCRUD', 'BookInvSuppMasterAPIController',['only' => ['store', 'show', 'update']]);
    Route::resource('book_inv_supp_dets', 'BookInvSuppDetAPIController', ['except' => ['index','store']]);
    Route::resource('direct_invoice_details', 'DirectInvoiceDetailsAPIController', ['except' => ['index']]);
    Route::get('getInvoiceMasterRecord', 'BookInvSuppMasterAPIController@getInvoiceMasterRecord')->name("Get invoice master record");
    Route::put('book_inv_supp_local_update/{id}', 'BookInvSuppMasterAPIController@updateLocalER')->name("Update local er");
    Route::put('book_inv_supp_reporting_update/{id}', 'BookInvSuppMasterAPIController@updateReportingER')->name("Update reporting er");
    Route::put('supplierInvoiceUpdateCurrency/{id}', 'BookInvSuppMasterAPIController@updateCurrency')->name("Update currency");
    Route::get('getInvoiceMasterFormData', 'BookInvSuppMasterAPIController@getInvoiceMasterFormData')->name("Get invoice master form data");
    Route::post('getInvoiceMasterView', 'BookInvSuppMasterAPIController@getInvoiceMasterView')->name("Get invoice master view");
    Route::get('getInvoiceSupplierTypeBase', 'BookInvSuppMasterAPIController@getInvoiceSupplierTypeBase')->name("Get invoice supplier type base");
    Route::get('getSupplierInvoiceStatusHistory', 'BookInvSuppMasterAPIController@getSupplierInvoiceStatusHistory')->name("Get supplier invoice status history");
    Route::post('supplierInvoiceCancel', 'BookInvSuppMasterAPIController@supplierInvoiceCancel')->name("Supplier invoice cancel");
    Route::get('getDirectItems', 'DirectInvoiceDetailsAPIController@getDirectItems')->name("Get direct items");
    Route::get('getSupplierInvoiceGRVItems', 'BookInvSuppDetAPIController@getSupplierInvoiceGRVItems')->name("Get supplier invoice grv items");
    Route::get('getSupplierInvDirectItems', 'SupplierInvoiceDirectItemAPIController@getSupplierInvDirectItems')->name("Get supplier inv direct items");
    Route::get('getPurchaseOrderForSI', 'UnbilledGrvGroupByAPIController@getPurchaseOrderForSI')->name("Get purchase order for si");
    Route::get('getUnbilledGRVDetailsForSI', 'UnbilledGrvGroupByAPIController@getUnbilledGRVDetailsForSI')->name("Get unbilled grv details for si");
    Route::post('storePOBaseDetail', 'BookInvSuppDetAPIController@storePOBaseDetail')->name("Store po base detail");
    Route::post('editPOBaseDetail', 'BookInvSuppDetAPIController@editPOBaseDetail')->name("Edit po base detail");
    Route::post('supplierInvoiceReopen', 'BookInvSuppMasterAPIController@supplierInvoiceReopen')->name("Supplier invoice reopen");
    Route::post('clearSupplierInvoiceNo', 'BookInvSuppMasterAPIController@clearSupplierInvoiceNo')->name("Clear supplier invoice no");
    Route::post('getSIMasterAmendHistory', 'BookInvSuppMasterRefferedBackAPIController@getSIMasterAmendHistory')->name("Get si master amend history");
    Route::post('saveSupplierInvoiceTaxDetails', 'BookInvSuppMasterAPIController@saveSupplierInvoiceTaxDetails')->name("Save si tax details");
    Route::get('supplierInvoiceTaxTotal', 'BookInvSuppMasterAPIController@supplierInvoiceTaxTotal')->name("Get si tax total");
    
});

//approval
Route::group([], function(){
    Route::post('getDebitNoteApprovalByUser', 'DebitNoteAPIController@getDebitNoteApprovalByUser')->name("Get pending for approval - Debit Note");
    Route::post('getDebitNoteApprovedByUser', 'DebitNoteAPIController@getDebitNoteApprovedByUser')->name("Get approved - Debit Note");
    Route::post('approvalPreCheckDebitNote', 'DebitNoteAPIController@approvalPreCheckDebitNote')->name("Debit note approval pre check");

    Route::post('getPaymentApprovalByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovalByUser')->name("Get pending for approval - Payment Voucher");
    Route::post('getPaymentApprovedByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovedByUser')->name("Get approved - Payment Voucher");

    Route::post('getInvoiceMasterApproval', 'BookInvSuppMasterAPIController@getInvoiceMasterApproval')->name("Get pending for approval - Supplier Invoice");
    Route::post('getApprovedInvoiceForCurrentUser', 'BookInvSuppMasterAPIController@getApprovedInvoiceForCurrentUser')->name("Get approved - Supplier Invoice");
    Route::post('rejectSupplierInvoice', 'BookInvSuppMasterAPIController@rejectSupplierInvoice')->name("Reject supplier invoice");
});

//Debit Note
Route::group([], function(){
    Route::get('getDebitNoteFormData', 'DebitNoteAPIController@getDebitNoteFormData')->name("Get Debit note form data");
    Route::post('exportDebitNotesByCompany', 'DebitNoteAPIController@exportDebitNotesByCompany')->name('Export debit notes by company');
    Route::post('getDebitNoteAmendHistory', 'DebitNoteMasterRefferedbackAPIController@getDebitNoteAmendHistory')->name('Get debit note amend history');
    Route::get('getDNDetailAmendHistory', 'DebitNoteDetailsRefferedbackAPIController@getDNDetailAmendHistory')->name('Get debit note detail amend history');
    Route::get('getDebitNoteMasterRecord', 'DebitNoteAPIController@getDebitNoteMasterRecord')->name('Get debit note master record');
    Route::post('getAllDebitNotes', 'DebitNoteAPIController@getAllDebitNotes')->name('Get all debit notes');
    Route::put('debitNoteUpdateCurrency/{id}', 'DebitNoteAPIController@updateCurrency')->name('Debit note update currency');
    Route::put('updateDebiteNoteType/{id}', 'DebitNoteAPIController@updateDebiteNoteType')->name('Update debit note type');
    Route::get('getDetailsByDebitNote', 'DebitNoteDetailsAPIController@getDetailsByDebitNote')->name('Get details by debit note');
    Route::put('debitNoteLocalUpdate/{id}', 'DebitNoteAPIController@debitNoteLocalUpdate')->name('Debit note local update');
    Route::put('debitNoteReportingUpdate/{id}','DebitNoteAPIController@debitNoteReportingUpdate')->name('Debit note reporting update');
    Route::post('debitNoteReopen', 'DebitNoteAPIController@debitNoteReopen')->name('Debit note reopen');
    Route::get('getDebitNotePaymentStatusHistory', 'DebitNoteAPIController@getDebitNotePaymentStatusHistory')->name('Get debit note payment status history');
    Route::post('amendDebitNote', 'DebitNoteAPIController@amendDebitNote')->name('Amend debit Note');
    Route::post('amendDebitNoteReview', 'DebitNoteAPIController@amendDebitNoteReview')->name('Amend debit note review');
    Route::post('checkPaymentStatusDNPrint', 'DebitNoteAPIController@checkPaymentStatusDNPrint')->name('Check payment status debit note print');
    Route::resource('debit_note_details', 'DebitNoteDetailsAPIController');
    Route::resource('debitNoteMasterRefferedbacksCRUD', 'DebitNoteMasterRefferedbackAPIController');
    Route::resource('debit_notes', 'DebitNoteAPIController');
});

//Payment Voucher
Route::group([],function (){
    Route::post('addPOPaymentDetail', 'PaySupplierInvoiceDetailAPIController@addPOPaymentDetail')->name('Add po payment detail');
    Route::get('getPOPaymentForPV', 'PaySupplierInvoiceMasterAPIController@getPOPaymentForPV')->name('Get po payment for pv');
    Route::get('getADVPaymentForPV', 'PaySupplierInvoiceMasterAPIController@getADVPaymentForPV')->name('Get adv payment for pv');
    Route::post('addADVPaymentDetail', 'AdvancePaymentDetailsAPIController@addADVPaymentDetail')->name('Add adv payment detail');
    Route::get('getBankMemoBySupplierCurrencyId', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrencyId')->name('Get bank memo by supplier currency id');
    Route::get('payeeBankMemosByDocument', 'BankMemoPayeeAPIController@payeeBankMemosByDocument')->name('Payee bank memo by document');
    Route::post('payeeBankMemoDeleteAll', 'BankMemoPayeeAPIController@payeeBankMemoDeleteAll')->name('Payee bank memo delete all');
    Route::post('updateDirectPaymentAccount', 'DirectPaymentDetailsAPIController@updateDirectPaymentAccount')->name('Update direct payment account');
    Route::get('getPaymentVoucherMaster', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherMaster')->name('Get payment voucher master');
    Route::post('getAllPaymentVoucherByCompany', 'PaySupplierInvoiceMasterAPIController@getAllPaymentVoucherByCompany')->name('Get all payment voucher by company');
    Route::get('getPaymentVoucherFormData', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherFormData')->name('Get payment voucher form data');
    Route::put('paymentVoucherLocalUpdate/{id}', 'PaySupplierInvoiceMasterAPIController@paymentVoucherLocalUpdate')->name('Payment voucher local update');
    Route::put('paymentVoucherReportingUpdate/{id}','PaySupplierInvoiceMasterAPIController@paymentVoucherReportingUpdate')->name('Payment voucher reporting update');
    Route::get('validationsForPDC', 'PaySupplierInvoiceMasterAPIController@validationsForPDC')->name('Validation for pdc');
    Route::put('paymentVoucherProjectUpdate/{id}', 'PaySupplierInvoiceMasterAPIController@paymentVoucherProjectUpdate')->name('Payment voucher project update');
    Route::get('getRetentionValues', 'PaySupplierInvoiceMasterAPIController@getRetentionValues')->name('Get retention values');
    Route::put('paymentVoucherUpdateCurrency/{id}', 'PaySupplierInvoiceMasterAPIController@updateCurrency')->name('Payment voucher update currency');
    Route::post('checkPVDocumentActive', 'PaySupplierInvoiceMasterAPIController@checkPVDocumentActive')->name('Check pv document active');
    Route::get('getPOPaymentDetails', 'PaySupplierInvoiceDetailAPIController@getPOPaymentDetails')->name('Get po payment details');
    Route::get('getADVPaymentDetails', 'AdvancePaymentDetailsAPIController@getADVPaymentDetails')->name('Get adv payment details');
    Route::post('deleteAllPOPaymentDetail', 'PaySupplierInvoiceDetailAPIController@deleteAllPOPaymentDetail')->name('Delete all po payment detail');
    Route::post('addADVPaymentDetailNotLinkPo', 'AdvancePaymentDetailsAPIController@addADVPaymentDetailNotLinkPo')->name('Add adv payment detail not link po');
    Route::post('deleteAllADVPaymentDetail', 'AdvancePaymentDetailsAPIController@deleteAllADVPaymentDetail')->name('Delete all adv payment detail');
    Route::get('getDirectPaymentDetails', 'DirectPaymentDetailsAPIController@getDirectPaymentDetails')->name('Get direct payment details');
    Route::post('deleteAllDirectPayment', 'DirectPaymentDetailsAPIController@deleteAllDirectPayment')->name('Delete all direct payment');
    Route::post('paymentVoucherReopen', 'PaySupplierInvoiceMasterAPIController@paymentVoucherReopen')->name('Payment voucher re open');
    Route::get('getPaymentVoucherGL', 'ChartOfAccountsAssignedAPIController@getPaymentVoucherGL')->name('Get payment voucher gl');
    Route::get('getReceiptVoucherMasterRecord', 'CustomerReceivePaymentAPIController@getReceiptVoucherMasterRecord')->name('Get receipt voucher master record');
    Route::get('getReferBackApprovedDetails', 'DocumentReferedHistoryAPIController@getReferBackApprovedDetails')->name('Get refer back approved details');
    Route::post('addPVDetailsByInterCompany', 'DirectPaymentDetailsAPIController@addPVDetailsByInterCompany')->name('Add pv details by inter company');
    Route::get('paymentVoucherHistoryByPVID', 'PaySupplierInvoiceMasterReferbackAPIController@paymentVoucherHistoryByPVID')->name('Payment voucher history by pvid');
    Route::get('getPOPaymentHistoryDetails', 'PaySupplierInvoiceDetailReferbackAPIController@getPOPaymentHistoryDetails')->name('Get po payment history details');
    Route::get('getADVPaymentHistoryDetails', 'AdvancePaymentReferbackAPIController@getADVPaymentHistoryDetails')->name('Get adv payment history details');
    Route::get('getDirectPaymentHistoryDetails', 'DirectPaymentReferbackAPIController@getDirectPaymentHistoryDetails')->name('Get direct payment history details');
    Route::post('generatePdcForPv', 'PaySupplierInvoiceMasterAPIController@generatePdcForPv')->name('Generate pdc for pv');
    Route::post('getPdcCheques', 'PdcLogAPIController@getPdcCheques')->name('Get pdc cheques');
    Route::post('deleteAllPDC', 'PdcLogAPIController@deleteAllPDC')->name('Delete all pdc');
    Route::post('paymentVoucherCancel', 'PaySupplierInvoiceMasterAPIController@paymentVoucherCancel')->name('Payment voucher Cancel');
    Route::post('updateSentToTreasuryDetail', 'PaySupplierInvoiceMasterAPIController@updateSentToTreasuryDetail')->name('Update sent to treasury details');
    Route::post('referBackPaymentVoucher', 'PaySupplierInvoiceMasterAPIController@referBackPaymentVoucher')->name('Refer back payment voucher');
    Route::get('amendPaymentVoucherPreCheck', 'PaySupplierInvoiceMasterAPIController@amendPaymentVoucherPreCheck')->name('Amend payment voucher pre check');
    Route::post('amendPaymentVoucherReview', 'PaySupplierInvoiceMasterAPIController@amendPaymentVoucherReview')->name('Amend payment voucher review');
    Route::post('updateBankBalance', 'PaySupplierInvoiceMasterAPIController@updateBankBalance')->name('Update bank balance');
    Route::post('pv-md-deduction-type', 'DirectPaymentDetailsAPIController@updat_monthly_deduction')->name('Update monthly deduction pv');
    Route::get('getDirectPaymentDetailsHistoryByID', 'DirectPaymentReferbackAPIController@getDirectPaymentDetailsHistoryByID')->name('Get direct payment details history by id');
    Route::get('getDPExchangeRateAmount', 'DirectPaymentDetailsAPIController@getDPExchangeRateAmount')->name('Get dp exchange rate amount');
    Route::get('getDPHistoryExchangeRateAmount', 'DirectPaymentReferbackAPIController@getDPHistoryExchangeRateAmount')->name('Get dp history exchange rate amount');
    Route::post('addDetailsFromExpenseClaim', 'DirectPaymentDetailsAPIController@addDetailsFromExpenseClaim')->name('Add details from expense claim');
    Route::post('addPVBankChargeDetails', 'PaySupplierInvoiceDetailAPIController@storePaymentVoucherBankChargeDetails')->name('Add payment voucher bank charge details');
    Route::post('updatePVBankChargeDetails', 'PaySupplierInvoiceDetailAPIController@updatePaymentVoucherBankChargeDetails')->name('Update payment voucher bank charge details');

    Route::resource('bank_memo_payees', 'BankMemoPayeeAPIController');
    Route::resource('pdc_logs', 'PdcLogAPIController');
    Route::resource('direct_payment_details', 'DirectPaymentDetailsAPIController',['except' => ['index']]);
    Route::resource('advance_payment_details', 'AdvancePaymentDetailsAPIController',['except' => ['index','store']]);
    Route::resource('pay_supplier_invoice_details', 'PaySupplierInvoiceDetailAPIController',['except' => ['index','store']]);
    Route::resource('pay_supplier_invoice_masters', 'PaySupplierInvoiceMasterAPIController', ['only' => ['store', 'show', 'update']]);
    Route::resource('pv_bank_charge_details', 'PaymentVoucherBankChargeDetailsAPIController');
});

//Payment Voucher Matching
Route::group([],function (){
    Route::get('getMatchingADVPaymentDetails', 'AdvancePaymentDetailsAPIController@getMatchingADVPaymentDetails')->name('Get matching adv payment details');
    Route::get('getMatchingPaymentDetails', 'PaySupplierInvoiceDetailAPIController@getMatchingPaymentDetails')->name('Get matching payment details');
    Route::post('deleteMatchingAllADVPaymentDetail', 'AdvancePaymentDetailsAPIController@deleteMatchingAllADVPaymentDetail')->name('Delete matching all adv payment detail');
    Route::post('deleteMatchingADVPaymentItem', 'AdvancePaymentDetailsAPIController@deleteMatchingADVPaymentItem')->name('Delete matching adv payment item');
    Route::post('updatePaymentVoucherMatchingDetail', 'PaySupplierInvoiceDetailAPIController@updatePaymentVoucherMatchingDetail')->name('Update payment voucher matching detail');
    Route::post('PaymentVoucherMatchingCancel', 'MatchDocumentMasterAPIController@PaymentVoucherMatchingCancel')->name('Payment voucher matching cancel');
    Route::post('amendReceiptMatchingReview', 'MatchDocumentMasterAPIController@amendReceiptMatchingReview')->name('Amend receipt matching review');
    Route::post('getPaymentVoucherMatchPullingDetail', 'MatchDocumentMasterAPIController@getPaymentVoucherMatchPullingDetail')->name('Get payment voucher match pulling detail');
    Route::post('addPaymentVoucherMatchingPaymentDetail', 'PaySupplierInvoiceDetailAPIController@addPaymentVoucherMatchingPaymentDetail')->name('Add payment voucher matching payment detail');
    Route::get('getADVPaymentForMatchingDocument', 'PaySupplierInvoiceMasterAPIController@getADVPaymentForMatchingDocument')->name('Get adv payment for matching document');
    Route::post('addADVPaymentDetailForDirectPay', 'AdvancePaymentDetailsAPIController@addADVPaymentDetailForDirectPay')->name('Add adv payment detail for direct pay');
});

//Expense Claim Analysis
Route::group([],function () {
    Route::post('getExpenseClaimMasterByCompany', 'ExpenseClaimMasterAPIController@getExpenseClaimMasterByCompany')->name('Get expense claim master by company');
    Route::get('getExpenseClaimFormData', 'ExpenseClaimAPIController@getExpenseClaimFormData')->name('Get expense claim form data');
    Route::get('getExpenseClaimMasterPaymentStatusHistory', 'ExpenseClaimMasterAPIController@getExpenseClaimMasterPaymentStatusHistory')->name('Get expense claim master payment status history');
    Route::get('getDetailsByExpenseClaimMaster', 'ExpenseClaimDetailsMasterAPIController@getDetailsByExpenseClaimMaster')->name('Get details by expense claim master');
    Route::get('preCheckECDetailMasterEdit', 'ExpenseClaimDetailsMasterAPIController@preCheckECDetailMasterEdit')->name('Pre check ec detail master edit');
    Route::post('amendExpenseClaimReview', 'ExpenseClaimAPIController@amendExpenseClaimReview')->name('Amend expense claim review');
    Route::get('getExpenseClaimAudit', 'ExpenseClaimAPIController@getExpenseClaimAudit')->name('Get expense claim audit');
    Route::get('getExpenseClaimMasterAudit', 'ExpenseClaimMasterAPIController@getExpenseClaimMasterAudit')->name('Get expense claim master audit');

    Route::resource('expense_claim_details', 'ExpenseClaimDetailsAPIController');
    Route::resource('expense_claim_masters', 'ExpenseClaimMasterAPIController');
});

//Monthly Additions
Route::group([],function (){
    Route::post('getMonthlyAdditionsByCompany', 'MonthlyAdditionsMasterAPIController@getMonthlyAdditionsByCompany')->name('Get monthly additions by company');
    Route::get('getMonthlyAdditionFormData', 'MonthlyAdditionsMasterAPIController@getMonthlyAdditionFormData')->name('Get monthly addition form data');
    Route::post('getProcessPeriods', 'MonthlyAdditionsMasterAPIController@getProcessPeriods')->name('Get process periods');
    Route::get('getItemsByMonthlyAddition', 'MonthlyAdditionDetailAPIController@getItemsByMonthlyAddition')->name('Get items by monthly addition');
    Route::post('monthlyAdditionReopen', 'MonthlyAdditionsMasterAPIController@monthlyAdditionReopen')->name('Monthly addition re open');
    Route::get('checkPullFromExpenseClaim', 'MonthlyAdditionDetailAPIController@checkPullFromExpenseClaim')->name('Check pull from expense claim');
    Route::post('deleteAllMonthlyAdditionDetails', 'MonthlyAdditionDetailAPIController@deleteAllMonthlyAdditionDetails')->name('Delete all monthly addition details');
    Route::post('amendEcMonthlyAdditionReview', 'MonthlyAdditionsMasterAPIController@amendEcMonthlyAdditionReview')->name('Amend ec monthly addition review');
    Route::get('getECForMonthlyAddition', 'MonthlyAdditionDetailAPIController@getECForMonthlyAddition')->name('Get ec for monthly addition');
    Route::get('getECDetailsForMonthlyAddition', 'MonthlyAdditionDetailAPIController@getECDetailsForMonthlyAddition')->name('Get ec details for monthly addition');
    Route::post('addMonthlyAdditionDetails', 'MonthlyAdditionDetailAPIController@addMonthlyAdditionDetails')->name('Add monthly addition details');
    Route::get('getMonthlyAdditionAudit', 'MonthlyAdditionsMasterAPIController@getMonthlyAdditionAudit')->name('Get monthly addition audit');

    Route::resource('monthly_additions_masters', 'MonthlyAdditionsMasterAPIController');
});

//Cheque Printing
Route::group([],function (){
    Route::post('getChequePrintingItems', 'BankLedgerAPIController@getChequePrintingItems')->name('Get cheque printing items');
    Route::get('getChequePrintingFormData', 'BankLedgerAPIController@getChequePrintingFormData')->name('Get cheque printing form data');
    Route::get('revertChequePrint', 'BankLedgerAPIController@revertChequePrint')->name('Revert cheque print');
});

//Reports

//Supplier Ledger Report
Route::group([],function (){
    Route::post('getAPFilterData', 'AccountsPayableReportAPIController@getAPFilterData')->name('Get account payable filter data');
    Route::post('validateAPReport', 'AccountsPayableReportAPIController@validateAPReport')->name('Validate account payable report');
    Route::group(['middleware' => 'max_memory_limit'], function () {
        Route::group(['middleware' => 'max_execution_limit'], function () {
            Route::post('exportAPReport', 'AccountsPayableReportAPIController@exportReport')->name('Export account payable report');
            Route::post('generateAPReport', 'AccountsPayableReportAPIController@generateAPReport')->name('Generate account payable report');
        });
    });
    Route::post('sentSupplierLedger', 'AccountsPayableReportAPIController@sentSupplierLedger')->name('Sent supplier ledger');
    Route::get('getJournalVoucherMasterRecord', 'JvMasterAPIController@getJournalVoucherMasterRecord')->name('Get journal voucher master record');
});

//Supplier Statement Report
Route::group([],function (){
    Route::post('sentSupplierStatement', 'AccountsPayableReportAPIController@sentSupplierStatement')->name('Sent supplier statement');
    Route::post('generateAPReportBulkPDF', 'AccountsPayableReportAPIController@generateAPReportBulkPDF')->name('Generate report for bulk data');
});

//Advance Payment Request
Route::group([],function (){
    Route::post('generateAdvancePaymentRequestReport', 'PoAdvancePaymentAPIController@generateAdvancePaymentRequestReport')->name('Generate advance payment request report');
    Route::post('exportAdvancePaymentRequestReport', 'PoAdvancePaymentAPIController@exportAdvancePaymentRequestReport')->name('Export advance payment request report');
    Route::get('getAdvancePaymentRequestStatusHistory', 'ProcumentOrderAPIController@getAdvancePaymentRequestStatusHistory')->name('Get advance payment request status history');
});

//Review

//Supplier Invoice
Route::group([],function (){
    Route::post('checkPaymentStatusSIPrint', 'BookInvSuppMasterAPIController@checkPaymentStatusSIPrint')->name('Check payment status supplier invoice print');
    Route::get('getPurchaseOrdersLikedWithSi', 'BookInvSuppMasterAPIController@getPurchaseOrdersLikedWithSi')->name('Get purchase orders liked with supplier invoice');
    Route::post('getSupplierInvoiceAmend', 'BookInvSuppMasterAPIController@getSupplierInvoiceAmend')->name('Get supplier invoice amend');
    Route::resource('supplierInvoiceAmendHistoryCRUD', 'BookInvSuppMasterRefferedBackAPIController');
    Route::get('getSIDetailDirectAmendHistory', 'DirectInvoiceDetailsRefferedBackAPIController@getSIDetailDirectAmendHistory')->name('Get SI detail direct amend history');
    Route::get('getSIDetailGRVAmendHistory', 'BookInvSuppDetRefferedBackAPIController@getSIDetailGRVAmendHistory')->name('Get SI detail GRV amend history');
    Route::get('getFilteredDirectCustomerInvoice', 'BookInvSuppMasterAPIController@getFilteredDirectCustomerInvoice')->name('Get filtered direct customer invoice');
    Route::get('supplierInvoiceTaxPercentage', 'BookInvSuppMasterAPIController@supplierInvoiceTaxPercentage')->name('Supplier invoice tax percentage');
    Route::get('getGRVDetailsForSupplierInvoice', 'SupplierInvoiceItemDetailAPIController@getGRVDetailsForSupplierInvoice')->name('Get grv details for supplier invoice');
    Route::post('deleteAllSIDirectItemDetail', 'SupplierInvoiceDirectItemAPIController@deleteAllSIDirectItemDetail')->name('Delete all si direct item detail');
    Route::resource('supplier_invoice_direct_items', 'SupplierInvoiceDirectItemAPIController');
    Route::post('validateDirectItemWithAssetExpense', 'ExpenseAssetAllocationAPIController@validateDirectItemWithAssetExpense')->name('Validate direct item with asset expense');
    Route::post('amendSupplierInvoiceReview', 'BookInvSuppMasterAPIController@amendSupplierInvoiceReview')->name('Amend supplier invoice review');
    Route::get('getRetentionPercentage', 'SupplierMasterAPIController@getRetentionPercentage')->name('Get retention percentage');
    Route::get('checkSelectedSupplierIsActive', 'SupplierAssignedAPIController@checkSelectedSupplierIsActive')->name('Check selected supplier is active');
    Route::post('deleteAllSIDirectDetail', 'DirectInvoiceDetailsAPIController@deleteAllSIDirectDetail')->name('Delete all si direct detail');
    Route::post('getPaymentVoucherPendingAmountDetails', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherPendingAmountDetails')->name('Get payment voucher pending amount details');
    Route::post('getAllPaymentVoucherAmendHistory', 'PaySupplierInvoiceMasterReferbackAPIController@getAllPaymentVoucherAmendHistory')->name('Get all payment voucher amend history');
    Route::post('unitCostValidation', 'BookInvSuppMasterAPIController@unitCostValidation')->name('Check unit cost validation');
});

//Payment Voucher Matching
Route::group([],function (){
    Route::get('getPaymentVoucherMatchItems', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherMatchItems')->name('Get payment voucher match items');
});

//Expense Claim
Route::group([],function (){
    Route::post('getExpenseClaimByCompany', 'ExpenseClaimAPIController@getExpenseClaimByCompany')->name('Get expense claim by company');
    Route::resource('sme-attachment', 'AttachmentSMEAPIController');
});

<?php

Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('masterBulkUpload', 'CustomerMasterAPIController@masterBulkUpload')->name("Master data bulk upload");
        Route::post('exportTransactionsRecord', 'TransactionsExportExcel@exportRecord')->name("Export Record");
    });
});

Route::resource('document_attachments', 'DocumentAttachmentsAPIController');  
Route::resource('document_attachment_types', 'DocumentAttachmentTypeAPIController');
Route::resource('general_ledgers', 'GeneralLedgerAPIController');
Route::resource('match_document_masters', 'MatchDocumentMasterAPIController',['only' => ['store', 'show', 'update']]);
Route::resource('expense_employee_allocations', 'ExpenseEmployeeAllocationAPIController');

Route::get('downloadTemplate', 'CustomerMasterAPIController@downloadTemplate')->name('Master data bulk upload template');
Route::get('getExampleTableData', 'ExampleTableTemplateAPIController@getExampleTableData')->name("Get example table for upload");
Route::post('getUserActivityLog', 'UserActivityLogAPIController@getViewLog')->name("Get user Activity log");

Route::get('getSearchCustomerByCompany', 'CustomerMasterAPIController@getSearchCustomerByCompany')->name("Get Search Customer By Company");
Route::get('getContractByCustomer', 'CustomerMasterAPIController@getContractByCustomer')->name("Get Contract By Customer");

Route::get('checkRestrictionByPolicy', 'DocumentRestrictionAssignAPIController@checkRestrictionByPolicy')->name("Check document restriction policy");

Route::get('getApprovedDetails', 'PurchaseRequestAPIController@getApprovedDetails')->name("Get approved details");
Route::get('getSubcategoriesBymainCategory', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategory')->name('Get sub categories by main category');
Route::get('getSubcategoryExpiryStatus', 'FinanceItemCategorySubAPIController@getSubcategoryExpiryStatus')->name('Get sub category expiry status');
Route::get('exportPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@exportPurchaseRequestHistory')->name('Export purchase request history');

Route::get('downloadFile', 'DocumentAttachmentsAPIController@downloadFile')->name('Download file');
Route::get('getuserGroupAssignedCompanies', 'EmployeeNavigationAPIController@getuserGroupAssignedCompanies')->name('Get user group assigned companies');
Route::get('getAllWHForSelectedCompany', 'WarehouseMasterAPIController@getAllWarehouseForSelectedCompany')->name('Get all warehouse for selected company');
Route::get('getGroupCompany', 'CompanyNavigationMenusAPIController@getGroupCompany')->name("Get group company");


Route::get('getAllDocuments', 'DocumentMasterAPIController@getAllDocuments')->name("Get all documents");
Route::get('checkDocumentAttachmentPolicy', 'CompanyDocumentAttachmentAPIController@checkDocumentAttachmentPolicy')->name("Check document attachment policy");

//approval
Route::post('approvalPreCheckAllDoc', 'DocumentApprovedAPIController@approvalPreCheckAllDoc')->name("Approval pre check");
Route::post('approveDocument', 'DocumentApprovedAPIController@approveDocument')->name("Approve Document");
Route::post('rejectPurchaseRequest', 'PurchaseRequestAPIController@rejectPurchaseRequest')->name("Reject Document");
Route::post('approvePurchaseRequest', 'PurchaseRequestAPIController@approvePurchaseRequest')->name("Approve purchase request");
Route::post('rejectProcurementOrder', 'ProcumentOrderAPIController@rejectProcurementOrder')->name('Reject procurement order');

Route::get('getDocumentTracingData', 'ProcumentOrderAPIController@getDocumentTracingData')->name('Get document tracing data');
Route::get('checkBudgetShowPolicy', 'BudgetMasterAPIController@checkBudgetShowPolicy')->name('Check budget show policy');

Route::get('checkUserGroupAccessRights', 'UserGroupAssignAPIController@checkUserGroupAccessRights'); //!! Do not add route name to this route !!
Route::get('getAllFinancePeriod', 'CompanyFinancePeriodAPIController@getAllFinancePeriod')->name('Get all finance period');
Route::get('getSearchSupplierByCompany', 'SupplierMasterAPIController@getSearchSupplierByCompany')->name('Get search supplier by company');

Route::get('getGeneralLedgerReview', 'GeneralLedgerAPIController@getGeneralLedgerReview')->name('Get General Ledger Review');
// Route::get('updateNotPostedGLEntries', 'GeneralLedgerAPIController@updateNotPostedGLEntries');

Route::post('updateGLEntries', 'GeneralLedgerAPIController@updateGLEntries')->name('Update GL Entries');
Route::post('generateSegmentGlReport', 'GeneralLedgerAPIController@generateSegmentGlReport')->name('Generate segment gl report');
Route::post('generateSegmentGlReportExcel', 'GeneralLedgerAPIController@generateSegmentGlReportExcel')->name('Generate segment gl report excel');

Route::get('getVatCategoryFormData', 'TaxVatCategoriesAPIController@getVatCategoryFormData')->name('Get vat category form data');
Route::post('updateItemVatCategories', 'TaxVatCategoriesAPIController@updateItemVatCategories')->name('Update item vat categories');
Route::get('getInvoiceDetailsForDeliveryOrderPrintView', 'DeliveryOrderAPIController@getInvoiceDetailsForDeliveryOrderPrintView')->name('Get invoice details for delivery order print view');

Route::post('getDocumentDetails', 'PurchaseRequestAPIController@getDocumentDetails')->name("Get Document Details");
Route::get('getAllFinancePeriodBasedFY', 'CompanyFinancePeriodAPIController@getAllFinancePeriodBasedFY')->name("Get All Finance Period Based FY");
Route::get('getCustomerByCompany', 'CustomerMasterAPIController@getCustomerByCompany')->name("Get Customer By Company");
Route::get('getDirectInvoiceGL', 'ChartOfAccountsAssignedAPIController@getDirectInvoiceGL')->name("Get Direct Invoice GL");

Route::get('getBankAccount', 'PaySupplierInvoiceMasterAPIController@getBankAccount')->name('Get bank account');
Route::post('getBankBalance', 'BankAccountAPIController@getBankBalance')->name('Get bank balance');
Route::get('getBankAccountsByBankID', 'BankAccountAPIController@getBankAccountsByBankID')->name('Get bank accounts by bank id');

Route::get('checkPolicyForExchangeRates', 'CommonPoliciesAPIController@checkPolicyForExchangeRates')->name('Check policy for exchange rates');

Route::get('getBudgetConsumptionByDocument', 'BudgetMasterAPIController@getBudgetConsumptionByDocument')->name('Get budget consumption by document');

// Matching
Route::get('getMatchDocumentMasterRecord', 'MatchDocumentMasterAPIController@getMatchDocumentMasterRecord')->name('Get match document master record');
Route::post('getMatchDocumentMasterView', 'MatchDocumentMasterAPIController@getMatchDocumentMasterView')->name('Get match document master view');
Route::get('getMatchDocumentMasterFormData', 'MatchDocumentMasterAPIController@getMatchDocumentMasterFormData')->name('Get match document master form data');

Route::post('getAllocatedEmployeesForExpense', 'ExpenseEmployeeAllocationAPIController@getAllocatedEmployeesForExpense')->name('Get allocated employees for expense');
Route::get('getCurrentUserInfo', 'UserAPIController@getCurrentUserInfo')->name("Get Current User Info");

Route::get('sme-attachment/{id}/{docID}/{companyID}', 'AttachmentSMEAPIController@show')->name('Show attachment sme');

Route::get('getAllFinancePeriodForYear', 'CompanyFinancePeriodAPIController@getAllFinancePeriodForYear')->name("Get All Finance Period For Year");
Route::post('postGLEntries', 'ShiftDetailsAPIController@postGLEntries');

Route::post('auditLogs', 'AuditTrailAPIController@auditLogs')->name("Get audit logs");

Route::get('getSearchCustomers', 'CustomerMasterAPIController@getSearchCustomers')->name("Get Search Customers");

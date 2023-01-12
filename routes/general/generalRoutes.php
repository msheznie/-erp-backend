<?php


Route::resource('document_attachments', 'DocumentAttachmentsAPIController');   
Route::resource('document_attachment_types', 'DocumentAttachmentTypeAPIController');


Route::get('downloadTemplate', 'CustomerMasterAPIController@downloadTemplate')->name('Master data bulk upload template');
Route::get('getExampleTableData', 'ExampleTableTemplateAPIController@getExampleTableData')->name("Get example table for upload");
Route::post('masterBulkUpload', 'CustomerMasterAPIController@masterBulkUpload')->name("Master data bulk upload");
Route::post('getUserActivityLog', 'UserActivityLogAPIController@getViewLog')->name("Get user Activity log");

Route::get('checkRestrictionByPolicy', 'DocumentRestrictionAssignAPIController@checkRestrictionByPolicy')->name("Check document restriction policy");

Route::get('getApprovedDetails', 'PurchaseRequestAPIController@getApprovedDetails')->name("Get approved details");
Route::get('getSubcategoriesBymainCategory', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategory')->name('Get sub categories by main category');
Route::get('getSubcategoryExpiryStatus', 'FinanceItemCategorySubAPIController@getSubcategoryExpiryStatus')->name('Get sub category expiry status');
Route::get('getErpLedger', 'ErpItemLedgerAPIController@getErpLedger')->name('Get erp ledger');
Route::get('exportPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@exportPurchaseRequestHistory')->name('Export purchase request history');
Route::post('generateStockLedger', 'ErpItemLedgerAPIController@generateStockLedger')->name('Generate stock ledger');
Route::post('getItemStockDetails', 'ErpItemLedgerAPIController@getItemStockDetails')->name('Get item stock details');
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

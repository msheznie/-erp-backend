<?php


Route::resource('document_attachments', 'DocumentAttachmentsAPIController');   
Route::resource('document_attachment_types', 'DocumentAttachmentTypeAPIController');


Route::get('downloadTemplate', 'CustomerMasterAPIController@downloadTemplate')->name('Master data bulk upload template');
Route::get('getExampleTableData', 'ExampleTableTemplateAPIController@getExampleTableData')->name("Get example table for upload");
Route::post('masterBulkUpload', 'CustomerMasterAPIController@masterBulkUpload')->name("Master data bulk upload");

Route::get('checkRestrictionByPolicy', 'DocumentRestrictionAssignAPIController@checkRestrictionByPolicy')->name("Check document restriction policy");

Route::get('getApprovedDetails', 'PurchaseRequestAPIController@getApprovedDetails')->name("Get approved details");
Route::get('getSubcategoriesBymainCategory', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategory')->name('Get sub categories by main category');
Route::get('getSubcategoryExpiryStatus', 'FinanceItemCategorySubAPIController@getSubcategoryExpiryStatus')->name('get sub category expiry status');
Route::get('getErpLedger', 'ErpItemLedgerAPIController@getErpLedger')->name('get erp ledger');


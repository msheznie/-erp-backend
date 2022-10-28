<?php

//supplier master
Route::get('supplierFormData', 'CompanyAPIController@getSupplierFormData')->name('Supplier Form Data');
Route::post('supplierMasterByCompany', 'SupplierMasterAPIController@getSupplierMasterByCompany')->name('Company wise suppliers list');
Route::get('getAdvanceAccount', 'CompanyAPIController@getAdvanceAccount')->name("Advance accounts for selected company");
Route::get('getApprovedCustomers', 'CustomerMasterAPIController@getApprovedCustomers')->name("Approved customer list");
Route::post('getInterCompaniesForCustomerSupplier', 'CustomerMasterAPIController@getInterCompaniesForCustomerSupplier')->name("Inter companies for customer/supplier");

Route::resource('supplier/masters', 'SupplierMasterAPIController');
Route::post('supplier/masters/update', 'SupplierMasterAPIController@updateSupplierMaster')->name("Update supplier master");
Route::get('supplier/currencies', 'CurrencyMasterAPIController@getCurrenciesBySupplier')->name('Currencies by supplier');

Route::get('supplier/assignedCompanies', 'SupplierMasterAPIController@getAssignedCompaniesBySupplier')->name("Companies assigned by supplier");

Route::post('getAllCatalogsByCompany', 'SupplierCatalogMasterAPIController@getAllCatalogsByCompany')->name("Supplier catalog list");
Route::resource('supplier_catalog_masters', 'SupplierCatalogMasterAPIController');
Route::get('getItemsOptionsSupplierCatalog', 'SupplierCatalogMasterAPIController@getItemsOptionsSupplierCatalog')->name("Get item option for supplier catalog");
Route::resource('supplier_catalog_details', 'SupplierCatalogDetailAPIController');

Route::post('supplier/add/currency', 'CurrencyMasterAPIController@addCurrencyToSupplier')->name("Add supplier currency");		
Route::post('supplier/update/currency', 'CurrencyMasterAPIController@updateCurrencyToSupplier')->name("Update supplier currency");
Route::post('supplier/remove/currency', 'CurrencyMasterAPIController@removeCurrencyToSupplier')->name("Remove supplier currency");

Route::get('getBankMemoBySupplierCurrency', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrency')->name("Bank memo by supplier currency");
Route::resource('bank_memo_suppliers', 'BankMemoSupplierAPIController');
Route::post('exportSupplierCurrencyMemos', 'BankMemoSupplierAPIController@exportSupplierCurrencyMemos')->name("Export supplier currency memos");
Route::post('supplierBankMemoDeleteAll', 'BankMemoSupplierAPIController@supplierBankMemoDeleteAll')->name("Delete all supplier currency memos");
Route::post('addBulkMemos', 'BankMemoSupplierAPIController@addBulkMemos')->name("Add bulk supplier currency memos");
Route::post('deleteBankMemo', 'BankMemoSupplierAPIController@deleteBankMemo')->name("Delete bank memo");
Route::post('addBulkPayeeMemos', 'BankMemoPayeeAPIController@addBulkPayeeMemos')->name("Add bulk payee memos");

Route::resource('supplier/contactDetails', 'SupplierContactDetailsAPIController');
Route::get('contactDetailsBySupplier', 'SupplierContactDetailsAPIController@getContactDetailsBySupplier')->name("Contact details by supplier");

Route::post('addSubCategoryToSupplier', 'SupplierCategorySubAPIController@addSubCategoryToSupplier')->name("Add sub categoreis to supplier");
Route::post('removeSubCategoryToSupplier', 'SupplierCategorySubAPIController@removeSubCategoryToSupplier')->name("Remove supplier sub category");
Route::get('subcategoriesBySupplier', 'SupplierMasterAPIController@getSubcategoriesBySupplier')->name("Sub categoreis by supplier");
Route::get('subCategoriesByMasterCategory', 'SupplierCategorySubAPIController@getSubCategoriesByMasterCategory')->name("Supplier sub categoreis by master category");

Route::get('subICVCategoriesByMasterCategory', 'SupplierCategoryICVMasterAPIController@subICVCategoriesByMasterCategory')->name("Supplier sub ICV categoreis by master category");


Route::post('srmRegistrationLinkHistoryView', 'SupplierMasterAPIController@srmRegistrationLinkHistoryView')->name("SRM registration link create history");
Route::post('srmRegistrationLink', 'SupplierMasterAPIController@srmRegistrationLink')->name("Generate SRM registration link");
Route::post('exportSupplierMaster', 'SupplierMasterAPIController@exportSupplierMaster')->name("Export suppliers list to excel");
Route::get('generateSupplierExternalLink', 'SupplierMasterAPIController@generateSupplierExternalLink')->name("Generate supplier external link");

Route::get('getSupplierMasterAudit', 'SupplierMasterAPIController@getSupplierMasterAudit')->name("Get supplier master audit");

Route::resource('supplier/assigned', 'SupplierAssignedAPIController');

Route::resource('supplier_refer_back', 'SupplierMasterRefferedBackAPIController');
Route::post('supplierReferBack', 'SupplierMasterAPIController@supplierReferBack')->name('Supplier referback');
Route::post('supplierReOpen', 'SupplierMasterAPIController@supplierReOpen')->name("Supplier master re open");
Route::post('validateSupplierAmend', 'SupplierMasterAPIController@validateSupplierAmend')->name("Validate supplier amend");

Route::post('referBackHistoryBySupplierMaster', 'SupplierMasterRefferedBackAPIController@referBackHistoryBySupplierMaster')->name("Referback history by supplier");

//currency

Route::get('allCurrencies', 'CurrencyMasterAPIController@getAllCurrencies')->name("All currencies");

Route::resource('bank_memo_types', 'BankMemoTypesAPIController');
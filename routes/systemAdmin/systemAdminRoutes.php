<?php
/**
 * This file contains system admin module related routes
 * 
 * 
 * */


//supplier master
Route::group([], function(){
	Route::get('supplierFormData', 'CompanyAPIController@getSupplierFormData')->name('Supplier Form Data');
	Route::get('getAdvanceAccount', 'CompanyAPIController@getAdvanceAccount')->name("Advance accounts for selected company");
	Route::get('getApprovedCustomers', 'CustomerMasterAPIController@getApprovedCustomers')->name("Approved customer list");
	Route::get('supplier/currencies', 'CurrencyMasterAPIController@getCurrenciesBySupplier')->name('Currencies by supplier');
	Route::get('supplier/assignedCompanies', 'SupplierMasterAPIController@getAssignedCompaniesBySupplier')->name("Companies assigned by supplier");
	Route::get('getItemsOptionsSupplierCatalog', 'SupplierCatalogMasterAPIController@getItemsOptionsSupplierCatalog')->name("Get item option for supplier catalog");
	Route::get('getBankMemoBySupplierCurrency', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrency')->name("Bank memo by supplier currency");
	Route::get('contactDetailsBySupplier', 'SupplierContactDetailsAPIController@getContactDetailsBySupplier')->name("Contact details by supplier");
	Route::get('subcategoriesBySupplier', 'SupplierMasterAPIController@getSubcategoriesBySupplier')->name("Sub categoreis by supplier");
	Route::get('subCategoriesByMasterCategory', 'SupplierCategorySubAPIController@getSubCategoriesByMasterCategory')->name("Supplier sub categoreis by master category");
	Route::get('subICVCategoriesByMasterCategory', 'SupplierCategoryICVMasterAPIController@subICVCategoriesByMasterCategory')->name("Supplier sub ICV categoreis by master category");
	Route::get('generateSupplierExternalLink', 'SupplierMasterAPIController@generateSupplierExternalLink')->name("Generate supplier external link");
	Route::get('getSupplierMasterAudit', 'SupplierMasterAPIController@getSupplierMasterAudit')->name("Get supplier master audit");

	Route::post('supplierMasterByCompany', 'SupplierMasterAPIController@getSupplierMasterByCompany')->name('Company wise suppliers list');
	Route::post('getInterCompaniesForCustomerSupplier', 'CustomerMasterAPIController@getInterCompaniesForCustomerSupplier')->name("Inter companies for customer/supplier");
	Route::post('supplier/masters/update', 'SupplierMasterAPIController@updateSupplierMaster')->name("Update supplier master");
	Route::post('getAllCatalogsByCompany', 'SupplierCatalogMasterAPIController@getAllCatalogsByCompany')->name("Supplier catalog list");
	Route::post('supplier/add/currency', 'CurrencyMasterAPIController@addCurrencyToSupplier')->name("Add supplier currency");		
	Route::post('supplier/update/currency', 'CurrencyMasterAPIController@updateCurrencyToSupplier')->name("Update supplier currency");
	Route::post('supplier/remove/currency', 'CurrencyMasterAPIController@removeCurrencyToSupplier')->name("Remove supplier currency");
	Route::post('exportSupplierCurrencyMemos', 'BankMemoSupplierAPIController@exportSupplierCurrencyMemos')->name("Export supplier currency memos");
	Route::post('supplierBankMemoDeleteAll', 'BankMemoSupplierAPIController@supplierBankMemoDeleteAll')->name("Delete all supplier currency memos");
	Route::post('addBulkMemos', 'BankMemoSupplierAPIController@addBulkMemos')->name("Add bulk supplier currency memos");
	Route::post('deleteBankMemo', 'BankMemoSupplierAPIController@deleteBankMemo')->name("Delete bank memo");
	Route::post('addBulkPayeeMemos', 'BankMemoPayeeAPIController@addBulkPayeeMemos')->name("Add bulk payee memos");
	Route::post('addSubCategoryToSupplier', 'SupplierCategorySubAPIController@addSubCategoryToSupplier')->name("Add sub categoreis to supplier");
	Route::post('removeSubCategoryToSupplier', 'SupplierCategorySubAPIController@removeSubCategoryToSupplier')->name("Remove supplier sub category");
	Route::post('srmRegistrationLinkHistoryView', 'SupplierMasterAPIController@srmRegistrationLinkHistoryView')->name("SRM registration link create history");
	Route::post('srmRegistrationLink', 'SupplierMasterAPIController@srmRegistrationLink')->name("Generate SRM registration link");
	Route::post('exportSupplierMaster', 'SupplierMasterAPIController@exportSupplierMaster')->name("Export suppliers list to excel");
	Route::post('supplierReferBack', 'SupplierMasterAPIController@supplierReferBack')->name('Supplier referback');
	Route::post('supplierReOpen', 'SupplierMasterAPIController@supplierReOpen')->name("Supplier master re open");
	Route::post('validateSupplierAmend', 'SupplierMasterAPIController@validateSupplierAmend')->name("Validate supplier amend");
	Route::post('referBackHistoryBySupplierMaster', 'SupplierMasterRefferedBackAPIController@referBackHistoryBySupplierMaster')->name("Referback history by supplier");

	Route::resource('supplier_catalog_masters', 'SupplierCatalogMasterAPIController');
	Route::resource('supplier/masters', 'SupplierMasterAPIController', ['names' => 'Supplier master']);
	Route::resource('supplier_catalog_details', 'SupplierCatalogDetailAPIController');
	Route::resource('bank_memo_suppliers', 'BankMemoSupplierAPIController');
	Route::resource('supplier/contactDetails', 'SupplierContactDetailsAPIController', ['names' => 'Supplier contact details']);
	Route::resource('supplier/assigned', 'SupplierAssignedAPIController', ['names' => 'Supplier assigned']);
	Route::resource('supplier_refer_back', 'SupplierMasterRefferedBackAPIController');
});



//currency
Route::group([], function(){
	Route::get('allCurrencies', 'CurrencyMasterAPIController@getAllCurrencies')->name("All currencies");
});

Route::resource('bank_memo_types', 'BankMemoTypesAPIController');

//customer 
Route::group([], function(){
	Route::post('getAllCustomers', 'CustomerMasterAPIController@getAllCustomers')->name("Get all customers");
	Route::post('exportCustomerMaster', 'CustomerMasterAPIController@exportCustomerMaster')->name("Export customer master");
	Route::post('getAllCustomerCatalogsByCompany', 'CustomerCatalogMasterAPIController@getAllCustomerCatalogsByCompany')->name("Get all customer catalog by company");
	Route::post('getAllCustomerCategories', 'CustomerMasterCategoryAPIController@getAllCustomerCategories')->name("Get all customer categoreis");
	Route::post('customerReferBack', 'CustomerMasterAPIController@customerReferBack')->name("Customer referback");
	Route::post('customerReOpen', 'CustomerMasterAPIController@customerReOpen')->name("customer reopen");
	Route::post('validateCustomerAmend', 'CustomerMasterAPIController@validateCustomerAmend')->name("Validate customer amend");
	Route::post('referBackHistoryByCustomerMaster', 'CustomerMasterRefferedBackAPIController@referBackHistoryByCustomerMaster')->name("Customer referback history");
	
	Route::get('getCustomerCatgeoryByCompany', 'CustomerMasterAPIController@getCustomerCatgeoryByCompany')->name("Get customer category by company");
	Route::get('getItemsOptionsCustomerCatalog', 'CustomerCatalogMasterAPIController@getItemsOptionsCustomerCatalog')->name("Get item options for customer catalog");
	Route::get('getAssignedCurrenciesByCustomer', 'CustomerCatalogMasterAPIController@getAssignedCurrenciesByCustomer')->name("Get assigned currencies by customer");
	Route::get('assignedCompaniesByCustomerCategory', 'CustomerMasterCategoryAssignedAPIController@assignedCompaniesByCustomerCategory')->name("Get assigned companies by customer category");
	Route::get('getNotAssignedCompaniesByCustomerCategory', 'CustomerMasterCategoryAPIController@getNotAssignedCompaniesByCustomerCategory')->name("Get not assigned companies by customer category");
	Route::get('getCustomerFormData', 'CustomerMasterAPIController@getCustomerFormData')->name("Customer Form Data");
	Route::get('getSelectedCompanyReportingCurrencyData', 'CustomerMasterAPIController@getSelectedCompanyReportingCurrencyData')->name("Get selected company reporting currency data");
	Route::get('getChartOfAccountsByCompanyForCustomer', 'CustomerMasterAPIController@getChartOfAccountsByCompanyForCustomer')->name("Get chart of account by company for customer");
	Route::get('contactDetailsByCustomer', 'CustomerContactDetailsAPIController@contactDetailsByCustomer')->name("Get contact details by customer");
	Route::get('getLinkedSupplier', 'CustomerMasterAPIController@getLinkedSupplier')->name("Get linked suppliers");
	Route::get('getAssignedCompaniesByCustomer', 'CustomerMasterAPIController@getAssignedCompaniesByCustomer')->name("Get assigned companies by customer");
	Route::get('getNotAssignedCompaniesByCustomer', 'CustomerAssignedAPIController@getNotAssignedCompaniesByCustomer')->name("Get not assigned companies by customer");
	Route::get('getAddedCurrenciesByCustomer', 'CustomerCurrencyAPIController@getAddedCurrenciesByCustomer')->name("Get added currencies by customer");
	Route::get('getNotAddedCurrenciesByCustomer', 'CustomerCurrencyAPIController@getNotAddedCurrenciesByCustomer')->name("Get not added currencies by customer");
	
	Route::resource('customer_catalog_masters', 'CustomerCatalogMasterAPIController');
	Route::resource('customer_catalog_details', 'CustomerCatalogDetailAPIController');
	Route::resource('customer_contact_details', 'CustomerContactDetailsAPIController');
	Route::resource('customerMasterCategories', 'CustomerMasterCategoryAPIController');
	Route::resource('customer_category_assigneds', 'CustomerMasterCategoryAssignedAPIController');
	Route::resource('customer_masters', 'CustomerMasterAPIController');
	Route::resource('customer_refer_back', 'CustomerMasterRefferedBackAPIController');
	Route::resource('customer_assigneds', 'CustomerAssignedAPIController');
	Route::resource('customer_currencies', 'CustomerCurrencyAPIController');
});


//item-master
Route::group([], function() {
    Route::resource('item/masters', 'ItemMasterAPIController',['names' => 'Item master']);
    Route::post('getAllItemsMaster', 'ItemMasterAPIController@getAllItemsMaster')->name('Get all items from master');
    Route::post('getAssignedItemsForCompany', 'ItemMasterAPIController@getAssignedItemsForCompany')->name('Get assigned items for company');
    Route::post('validateItemAmend', 'ItemMasterAPIController@validateItemAmend')->name('Validate item amend');
    Route::get('getAllFixedAssetItems', 'ItemMasterAPIController@getAllFixedAssetItems')->name('Get all fixed asset items');
    Route::post('exportItemMaster', 'ItemMasterAPIController@exportItemMaster')->name('Export item master');
    Route::post('itemMasterBulkCreate', 'ItemMasterAPIController@itemMasterBulkCreate')->name('Item master bulk create');
    Route::post('itemReferBack', 'ItemMasterAPIController@itemReferBack')->name('Item refer back');
    Route::post('itemReOpen', 'ItemMasterAPIController@itemReOpen')->name('Item reopen');
    Route::get('getItemMasterFormData', 'ItemMasterAPIController@getItemMasterFormData')->name('Get item master form');
    Route::get('getInventorySubCat', 'ItemMasterAPIController@getInventorySubCat')->name('Get inventory subcategory');
    Route::get('getItemSubCategory', 'ItemMasterAPIController@getItemSubCategory')->name('Get item subcategory');
    Route::post('updateItemMaster', 'ItemMasterAPIController@updateItemMaster')->name('Update item master');
    Route::get('assignedCompaniesByItem', 'ItemMasterAPIController@getAssignedCompaniesByItem')->name('Get assigned companies by item');
    Route::resource('item/assigneds', 'ItemAssignedAPIController', ['names' => 'Item assigned']);
    Route::post('getAllAssignedItemsByCompany', 'ItemAssignedAPIController@getAllAssignedItemsByCompany')->name('All assigned item by company');
});


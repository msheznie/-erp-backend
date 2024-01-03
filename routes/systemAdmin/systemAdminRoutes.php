<?php
/**
 * This file contains system admin module related routes
 * 
 * 
 * */


//supplier master
use Illuminate\Support\Facades\Route;

Route::group([], function(){
	Route::get('supplierFormData', 'CompanyAPIController@getSupplierFormData')->name('Supplier Form Data');
	Route::get('getAdvanceAccount', 'CompanyAPIController@getAdvanceAccount')->name("Advance accounts for selected company");
	Route::get('getApprovedCustomers', 'CustomerMasterAPIController@getApprovedCustomers')->name("Approved customer list");
	Route::get('supplier/currencies', 'CurrencyMasterAPIController@getCurrenciesBySupplier')->name('Currencies by supplier');
	Route::get('supplier/assignedCompanies', 'SupplierMasterAPIController@getAssignedCompaniesBySupplier')->name("Companies assigned by supplier");
	Route::get('getItemsOptionsSupplierCatalog', 'SupplierCatalogMasterAPIController@getItemsOptionsSupplierCatalog')->name("Get item option for supplier catalog");
	Route::get('getBankMemoBySupplierCurrency', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrency')->name("Bank memo by supplier currency");
	Route::get('contactDetailsBySupplier', 'SupplierContactDetailsAPIController@getContactDetailsBySupplier')->name("Contact details by supplier");
	Route::get('assignBusinessCategoriesBySupplier', 'SupplierMasterAPIController@getAssignBusinessCategoriesBySupplier')->name("Assign Business categories by supplier");
	Route::get('businessCategoriesBySupplier', 'SupplierMasterAPIController@getBusinessCategoriesBySupplier')->name("Business categories by supplier");
    Route::get('subCategoriesByMasterCategory', 'SupplierCategorySubAPIController@getSubCategoriesByMasterCategory')->name("Sub categories by master category");
	Route::get('subICVCategoriesByMasterCategory', 'SupplierCategoryICVMasterAPIController@subICVCategoriesByMasterCategory')->name("Supplier sub ICV categoreis by master category");
	Route::get('generateSupplierExternalLink', 'SupplierMasterAPIController@generateSupplierExternalLink')->name("Generate supplier external link");
	Route::get('getSupplierMasterAudit', 'SupplierMasterAPIController@getSupplierMasterAudit')->name("Get supplier master audit");
	Route::get('getSupplierMaster', 'SupplierMasterAPIController@getSupplierMaster')->name("Get supplier master");

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
	Route::post('addBusinessCategoryToSupplier', 'SupplierMasterAPIController@addBusinessCategoryToSupplier')->name("Add business categories to supplier");
	Route::post('srmRegistrationLinkHistoryView', 'SupplierMasterAPIController@srmRegistrationLinkHistoryView')->name("SRM registration link create history");
	Route::post('srmRegistrationLink', 'SupplierMasterAPIController@srmRegistrationLink')->name("Generate SRM registration link");
	Route::post('exportSupplierMaster', 'SupplierMasterAPIController@exportSupplierMaster')->name("Export suppliers list to excel");
	Route::post('supplierReferBack', 'SupplierMasterAPIController@supplierReferBack')->name('Supplier referback');
	Route::post('supplierReOpen', 'SupplierMasterAPIController@supplierReOpen')->name("Supplier master re open");
	Route::post('validateSupplierAmend', 'SupplierMasterAPIController@validateSupplierAmend')->name("Validate supplier amend");
	Route::post('referBackHistoryBySupplierMaster', 'SupplierMasterRefferedBackAPIController@referBackHistoryBySupplierMaster')->name("Referback history by supplier");
    Route::post('removeSupplierBusinessCategory', 'SupplierBusinessCategoryAssignAPIController@removeSupplierBusinessCategory')->name("Remove supplier business category");

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


//Chart Of Account
Route::group([], function(){
    Route::get('getChartOfAccountFormData', 'ChartOfAccountAPIController@getChartOfAccountFormData')->name("Chart of account form data");
	Route::get('getCompanyWiseSubLedgerAccounts', 'ChartOfAccountsAssignedAPIController@getCompanyWiseSubLedgerAccounts')->name("Chart of account company wise sub ledger");
	Route::get('gl-code-search', 'ChartOfAccountsAssignedAPIController@gl_code_search')->name("Chart of account gl code search");
	Route::get('coa-config-companies', 'SystemGlCodeScenarioAPIController@coa_config_companies')->name("Chart of account config companies");
	Route::get('changeActive', 'ChartOfAccountAPIController@changeActive')->name("Chart of account change active");
	Route::get('assignedCompaniesByChartOfAccount', 'ChartOfAccountAPIController@assignedCompaniesByChartOfAccount')->name("Chart of account assigned companies");
	Route::get('getNotAssignedCompaniesByChartOfAccount', 'ChartOfAccountAPIController@getNotAssignedCompaniesByChartOfAccount')->name("Chart of account not assigned companies");
	Route::get('getReportTemplatesCategoryByTemplate', 'ReportTemplateDetailsAPIController@getReportTemplatesCategoryByTemplate')->name("Chart of account report templates category by template");
	Route::get('getReportTemplatesByCategory', 'ReportTemplateAPIController@getReportTemplatesByCategory')->name("Chart of account report templates by category");
	Route::get('getAssignedReportTemplatesByGl', 'ReportTemplateAPIController@getAssignedReportTemplatesByGl')->name("Chart of account assigned report templates by gl");
	Route::get('isBank/{id}', 'ChartOfAccountAPIController@isBank')->name("Chart of account is bank");

	Route::post('chartOfAccount', 'ChartOfAccountAPIController@getChartOfAccount')->name("Chart of account");
    Route::post('exportChartOfAccounts', 'ChartOfAccountAPIController@exportChartOfAccounts')->name("Chart of account Export");
	Route::post('coa-config-scenario-assign', 'SystemGlCodeScenarioAPIController@scenario_assign')->name("Chart of account scenario assign");
	Route::post('coa-config-scenarios', 'SystemGlCodeScenarioDetailAPIController@list_config_scenarios')->name("Chart of account list config scenarios");
	Route::post('asset_disposal_type_config', 'AssetDisposalTypeAPIController@config_list')->name("Chart of account asset disposal type config");
	Route::post('getMasterChartOfAccountData', 'ChartOfAccountAPIController@getMasterChartOfAccountData')->name("Chart of account master data");
	Route::post('getInterCompanies', 'ChartOfAccountAPIController@getInterCompanies')->name("Chart of account inter companies");
	Route::post('getDefaultTemplateCategories', 'ReportTemplateDetailsAPIController@getDefaultTemplateCategories')->name("Chart of account default template categories");
	Route::post('getChartOfAccountCode', 'ReportTemplateDetailsAPIController@getChartOfAccountCode')->name("Chart of account code");
	Route::post('assignReportTemplateToGl', 'ReportTemplateLinksAPIController@assignReportTemplateToGl')->name("Chart of account assign report template to gl");
	Route::post('chartOfAccountReferBack', 'ChartOfAccountAPIController@chartOfAccountReferBack')->name("Chart of account refer back");
	Route::post('chartOfAccountReopen', 'ChartOfAccountAPIController@chartOfAccountReopen')->name("Chart of account reopen");
	Route::post('referBackHistoryByChartOfAccount', 'ChartOfAccountsRefferedBackAPIController@referBackHistoryByChartOfAccount')->name("Chart of account refer back history");

	Route::resource('gl-config-scenario-details', 'SystemGlCodeScenarioDetailAPIController');
	Route::resource('asset_disposal_types', 'AssetDisposalTypeAPIController');
	Route::resource('chart_of_accounts_assigned', 'ChartOfAccountsAssignedAPIController');
	Route::resource('chart_of_account', 'ChartOfAccountAPIController');
	Route::resource('chartOfAccountsReferBack', 'ChartOfAccountsRefferedBackAPIController');
});

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

//item-finance
Route::group([], function() {
    Route::post('getFinanceItemCategoryMasterExpiryStatus', 'FinanceItemCategoryMasterAPIController@getFinanceItemCategoryMasterExpiryStatus')->name('Get finance item category master expiry status');
    Route::post('getFinanceItemCategoryMasterAttributesStatus', 'FinanceItemCategoryMasterAPIController@getFinanceItemCategoryMasterAttributesStatus')->name('Get finance item category master attributes status');
    Route::post('allItemFinanceSubCategoriesByMainCategory', 'FinanceItemCategoryMasterAPIController@allItemFinanceSubCategoriesByMainCategory')->name('All item finance sub categories by main category');
    Route::get('getSubCategoryFormData', 'FinanceItemCategoryMasterAPIController@getSubCategoryFormData')->name('Get sub category form data');
    Route::post('getAttributesData', 'FinanceItemCategoryMasterAPIController@getAttributesData')->name('Get attributes data');
    Route::post('finance_item_category_subs_update', 'FinanceItemCategorySubAPIController@finance_item_category_subs_update')->name('Finance item category subs update');
    Route::resource('itemcategory_sub_assigneds', 'FinanceItemcategorySubAssignedAPIController');
    Route::get('assignedCompaniesBySubCategory', 'FinanceItemcategorySubAssignedAPIController@assignedCompaniesBySubCategory')->name('Assigned companies by sub category');
    Route::get('getNotAssignedCompanies', 'FinanceItemcategorySubAssignedAPIController@getNotAssignedCompanies')->name('Get not assigned companies');
    Route::post('financeItemCategorySubsExpiryUpdate', 'FinanceItemCategorySubAPIController@financeItemCategorySubsExpiryUpdate')->name('Finance item category subs expiry update');
    Route::resource('finance_item_category_subs', 'FinanceItemCategorySubAPIController');
});
//warehouse
Route::group([], function() {
    Route::get('getWarehouseMasterFormData', 'WarehouseMasterAPIController@getWarehouseMasterFormData')->name('Get warehouse master form data');
    Route::post('getAllWarehouseMaster', 'WarehouseMasterAPIController@getAllWarehouseMaster')->name('Get all warehouse master');
    Route::post('getAllLocation', 'ErpLocationAPIController@getAllLocation')->name('Get all location');
    Route::post('getWarehouseRightEmployees', 'WarehouseRightsAPIController@getWarehouseRightEmployees')->name('Get warehouse right employees');
    Route::post('createLocation', 'ErpLocationAPIController@createLocation')->name('Create location');
    Route::post('deleteLocation', 'ErpLocationAPIController@deleteLocation')->name('Delete location');
    Route::post('getAllAssignedItemsByWarehouse', 'WarehouseItemsAPIController@getAllAssignedItemsByWarehouse')->name('Get all assigned items by warehouse');
    Route::post('exportItemAssignedByWarehouse', 'WarehouseItemsAPIController@exportItemAssignedByWarehouse')->name('Export item assigned by warehouse');
    Route::resource('warehouse/masters', 'WarehouseMasterAPIController', ['names' => 'Warehouse master']);
    Route::post('getAllWarehouseSubLevels', 'WarehouseSubLevelsAPIController@getAllWarehouseSubLevels')->name('Get all warehouse sub levels');
    Route::resource('warehouse_sub_levels', 'WarehouseSubLevelsAPIController');
    Route::resource('warehouse_rights', 'WarehouseRightsAPIController');
    Route::get('getSubLevelsByWarehouse', 'WarehouseSubLevelsAPIController@getSubLevelsByWarehouse')->name('Get sub levels by warehouse');
    Route::post('getAllBinLocationsByWarehouse', 'WarehouseBinLocationAPIController@getAllBinLocationsByWarehouse')->name('Get all bin locations by warehouse');
    Route::resource('warehouse_bin_locations', 'WarehouseBinLocationAPIController');
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
    Route::get('getAllMainItemsByCompany', 'ItemMasterAPIController@getAllMainItemsByCompany')->name('All main items by company');
    Route::get('checkUnitConversions', 'ItemMasterAPIController@checkUnitConversions')->name('Check unit conversions');
    Route::post('updateUnitConversion', 'ItemMasterAPIController@updateUnitConversion')->name('Update unit conversion');
});


	//Bank Master
Route::group([], function() {
	Route::get('getBankMasterFormData', 'BankMasterAPIController@getBankMasterFormData')->name("Get bank master form data");

	Route::post('getAllBankAccounts', 'BankAccountAPIController@getAllBankAccounts')->name("Get all bank accounts");
	Route::post('exportBankAccountMaster', 'BankAccountAPIController@exportBankAccountMaster')->name("Export bank account master");
	Route::post('assignedTemplatesByBank', 'ChequeTemplateBankAPIController@assignedTemplatesByBank')->name("Assigned templates by bank");
	Route::post('bank/update/template', 'ChequeTemplateBankAPIController@updateBankAssingTemplate')->name("Update bank assigned template");
	Route::post('getAllBankMaster', 'BankMasterAPIController@getAllBankMaster')->name("Get all bank master");
	Route::post('updateBankMaster', 'BankMasterAPIController@updateBankMaster')->name("Update bank master");
	Route::post('assignedCompaniesByBank', 'BankMasterAPIController@assignedCompaniesByBank')->name("Assigned companies by bank");
	Route::post('bank/update/assign', 'BankAssignAPIController@updateBankAssingCompany')->name("Update bank assigned company");

	Route::resource('cheque_template_masters', 'ChequeTemplateMasterAPIController');
	Route::resource('cheque_template_banks', 'ChequeTemplateBankAPIController');
	Route::resource('bank/masters', 'BankMasterAPIController', ['names' => 'Bank masters']);
	Route::resource('bank/assign', 'BankAssignAPIController', ['names' => 'Bank assign']);
});


//Units
Route::group([], function() {
	Route::get('getUnitMasterFormData', 'UnitAPIController@getUnitMasterFormData')->name("Get unit master form data");
	Route::get('getUnitConversionFormData', 'UnitConversionAPIController@getUnitConversionFormData')->name("Get unit conversion form data");

	Route::post('getAllUnitMaster', 'UnitAPIController@getAllUnitMaster')->name("Get all unit master");
	Route::post('unit/conversion/update', 'UnitConversionAPIController@updateUnitConversion')->name("Update unit conversion");
	Route::post('updateUnitMaster', 'UnitAPIController@updateUnitMaster')->name("Update unit master");

	Route::resource('unit/masters', 'UnitAPIController', ['names' => 'Unit masters']);
	Route::resource('unit/conversion', 'UnitConversionAPIController', ['names' => 'Unit conversion']);
});


//Registered Suppliers
Route::group([], function() {
	Route::get('bankMemosByRegisteredSupplierCurrency', 'SupplierMasterAPIController@bankMemosByRegisteredSupplierCurrency')->name("Bank memos by registered supplier currency");
	Route::get('getRegisteredSupplierData', 'SupplierMasterAPIController@getRegisteredSupplierData')->name("Get registered supplier data");
	Route::get('downloadSupplierAttachmentFile', 'SupplierMasterAPIController@downloadSupplierAttachmentFile')->name("Download supplier attachment file");

	Route::post('updateRegisteredSupplierBankMemo', 'SupplierMasterAPIController@updateRegisteredSupplierBankMemo')->name("Update registered supplier bank memo");
	Route::post('notApprovedRegisteredSuppliers', 'SupplierMasterAPIController@notApprovedRegisteredSuppliers')->name("Not approved registered suppliers");
	Route::post('approvedRegisteredSuppliers', 'SupplierMasterAPIController@approvedRegisteredSuppliers')->name("Approved registered suppliers");
	Route::post('updateRegisteredSupplierMaster', 'SupplierMasterAPIController@updateRegisteredSupplierMaster')->name("Update registered supplier master");
	Route::post('updateRegisteredSupplierAttachment', 'SupplierMasterAPIController@updateRegisteredSupplierAttachment')->name("Update registered supplier attachment");
	Route::post('updateRegisteredSupplierCurrency', 'SupplierMasterAPIController@updateRegisteredSupplierCurrency')->name("Update registered supplier currency");

	Route::resource('registered_supp_contact_details', 'RegisteredSupplierContactDetailAPIController');
	Route::resource('registered_supplier_attachments', 'RegisteredSupplierAttachmentAPIController');

	// Route::resource('register_supplier_category_assigns', 'RegisterSupplierBusinessCategoryAssignAPIController');
	// Route::resource('register_supplier_subcategory_assigns', 'RegisterSupplierSubcategoryAssignAPIController');

});

//Document Amend
Route::group([], function() {
	Route::get('getDocumentAmendFormData', 'GeneralLedgerAPIController@getDocumentAmendFormData')->name("Get document amend form data");

	Route::post('getDocumentAmendFromGL', 'GeneralLedgerAPIController@getDocumentAmendFromGL')->name("Get document amend from gl");
	Route::post('changePostingDate', 'GeneralLedgerAPIController@changePostingDate')->name("Change posting date document amend");
});

//Attachment Master
Route::group([], function() {
	Route::get('getAttachmentFormData', 'DocumentAttachmentsAPIController@getAttachmentFormData')->name("Get attachment form data");

	Route::post('getAllAttachments', 'DocumentAttachmentsAPIController@getAllAttachments')->name("Get all attachments");
});

//Tax Setup
Route::group([], function() {
	Route::get('getTaxAuthorityFormData', 'TaxAuthorityAPIController@getTaxAuthorityFormData')->name("Get tax authority form data");
	Route::get('getTaxMasterFormData', 'TaxAPIController@getTaxMasterFormData')->name("Get tax master form data");
	Route::get('getAuthorityByCompany', 'TaxAuthorityAPIController@getAuthorityByCompany')->name("Get tax authority by company");
	Route::get('getAccountByAuthority', 'TaxAuthorityAPIController@getAccountByAuthority')->name("Get account by tax authority");
	Route::get('getOtherTax', 'TaxFormulaDetailAPIController@getOtherTax')->name("Get other tax");
	Route::get('getVatCategoriesFormData', 'TaxVatCategoriesAPIController@getVatCategoriesFormData')->name("Get tax vat categories formdata");
	Route::get('getVatSubCategoryItemAssignFromData', 'TaxVatCategoriesAPIController@getVatSubCategoryItemAssignFromData')->name("Get tax vat sub category item assign formdata");

	Route::post('getTaxFormulaDetailDatatable', 'TaxFormulaDetailAPIController@getTaxFormulaDetailDatatable')->name("Get tax formula detail datatable");
	Route::post('getAllVatMainCategories', 'TaxVatMainCategoriesAPIController@getAllVatMainCategories')->name("Get all tax vat main categories");
	Route::post('getAllVatCategories', 'TaxVatCategoriesAPIController@getAllVatCategories')->name("Get all tax vat categories");
	Route::post('getTaxAuthorityDatatable', 'TaxAuthorityAPIController@getTaxAuthorityDatatable')->name("Get tax authority datatable");
	Route::post('getTaxMasterDatatable', 'TaxAPIController@getTaxMasterDatatable')->name("Get tax master datatable");
	Route::post('getTaxFormulaMasterDatatable', 'TaxFormulaMasterAPIController@getTaxFormulaMasterDatatable')->name("Get tax formula master datatable");
	Route::post('getAllVatSubCategoryItemAssign', 'TaxVatCategoriesAPIController@getAllVatSubCategoryItemAssign')->name("Get all tax vat sub category item assign");
	Route::post('assignVatSubCategoryToItem', 'TaxVatCategoriesAPIController@assignVatSubCategoryToItem')->name("Assign tax vat sub category to item ");
	Route::post('removeAssignedItemFromVATSubCategory', 'TaxVatCategoriesAPIController@removeAssignedItemFromVATSubCategory')->name("Remove assigned item from tax vat sub category");

	Route::resource('tax_authorities', 'TaxAuthorityAPIController');
	Route::resource('taxes', 'TaxAPIController');
	Route::resource('tax_formula_details', 'TaxFormulaDetailAPIController');
	Route::resource('tax_formula_masters', 'TaxFormulaMasterAPIController');
	Route::resource('tax_vat_main_categories', 'TaxVatMainCategoriesAPIController');
	Route::resource('tax_vat_categories', 'TaxVatCategoriesAPIController');
});

//Asset Finance Category
Route::group([], function() {
	Route::get('getAssetFinanceCategoryFormData', 'AssetFinanceCategoryAPIController@getAssetFinanceCategoryFormData')->name("Get asset finance category form data");

	Route::post('getAllAssetFinanceCategory', 'AssetFinanceCategoryAPIController@getAllAssetFinanceCategory')->name("Get all asset finance category");

	Route::resource('asset_finance_categories', 'AssetFinanceCategoryAPIController');
});

//Asset Category
Route::group([], function() {
	Route::get('getAssetCategoryFormData', 'FixedAssetCategoryAPIController@getAssetCategoryFormData')->name("Get asset category form data");

	Route::post('getAllAssetCategory', 'FixedAssetCategoryAPIController@getAllAssetCategory')->name("Get all asset category");
	Route::post('getAllAssetSubCategoryByMain', 'FixedAssetCategorySubAPIController@getAllAssetSubCategoryByMain')->name("Get all asset sub category by main");

	Route::resource('fixed_asset_categories', 'FixedAssetCategoryAPIController');
	Route::resource('fixed_asset_category_subs', 'FixedAssetCategorySubAPIController');
});

//Currency Master
Route::group([], function() {
	Route::get('getAllConversionByCurrency', 'CurrencyMasterAPIController@getAllConversionByCurrency')->name("Get all conversion by currency");

	Route::post('updateCrossExchange', 'CurrencyConversionAPIController@updateCrossExchange')->name("Update cross exchange currency conversion");
	Route::post('getCurrencyConversionHistory', 'CurrencyConversionHistoryAPIController@getCurrencyConversionHistory')->name("Get currency conversion history");

	Route::resource('currency_conversions', 'CurrencyConversionAPIController');
	Route::resource('currency_masters', 'CurrencyMasterAPIController');
});

//Currency Conversion
Route::group([], function() {
	Route::get('getConversionMaster', 'CurrencyConversionMasterAPIController@getConversionMaster')->name("Get conversion master");
	Route::get('getAllTempConversionByCurrency', 'CurrencyConversionMasterAPIController@getAllTempConversionByCurrency')->name("Get all temp conversion by currency");

	Route::post('getAllCurrencyConversions', 'CurrencyConversionMasterAPIController@getAllCurrencyConversions')->name("Get all currency conversions");
	Route::post('currencyConversionReopen', 'CurrencyConversionMasterAPIController@currencyConversionReopen')->name("Currency conversion reopen");
	Route::post('updateTempCrossExchange', 'CurrencyConversionDetailAPIController@updateTempCrossExchange')->name("Update temp cross exchange");

	Route::resource('currency_conversion_masters', 'CurrencyConversionMasterAPIController');
	Route::resource('currency_conversion_details', 'CurrencyConversionDetailAPIController');
});

//Logistic Configuration
Route::group([], function() {
	Route::get('getAllcountry', 'CountryMasterAPIController@index')->name("Logistic Configuration get all country");

	Route::post('createDeliveryTerms', 'DeliveryTermsMasterAPIController@store')->name("Logistic Configuration create delivery terms");
	Route::post('createPort', 'PortMasterAPIController@store')->name("Logistic Configuration create Port");
	Route::post('getAllPort', 'PortMasterAPIController@getAllPort')->name("Logistic Configuration get all Port");
	Route::post('getAllDeliveryTerms', 'DeliveryTermsMasterAPIController@getAllDeliveryTerms')->name("Logistic Configuration get all delivery terms");
	Route::post('deletePort', 'PortMasterAPIController@deletePort')->name("Logistic Configuration delete Port");
	Route::post('deleteDeliveryTerms', 'DeliveryTermsMasterAPIController@deleteDeliveryTerms')->name("Logistic Configuration delete delivery terms");

});

	//Logistic Categories
Route::group([], function() {
	Route::get('getItemsOptionForLogistic', 'AddonCostCategoriesAPIController@getItemsOptionForLogistic')->name("Get items option for logistic");

	Route::post('getLogisticCategories', 'AddonCostCategoriesAPIController@getLogisticCategories')->name("Get logistic categories");

	Route::resource('addon_cost_categories', 'AddonCostCategoriesAPIController');
});

//Supplier Business Category
Route::group([], function() {
    Route::post('getAllSupplierBusinessCategories', 'SupplierCategoryMasterAPIController@getAllSupplierBusinessCategories')->name("Get all supplier business categories");
    Route::get('supplierBusinessCategoryFormData', 'SupplierCategoryMasterAPIController@getSupplierBusinessCategoryFormData')->name("Get supplier business category form data");
    Route::post('validateSupplierBusinessCategoryAmend', 'SupplierCategoryMasterAPIController@validateSupplierBusinessCategoryAmend')->name("Validate supplier business category amend");
    Route::get('supplierBusinessCategoryDestroyCheck/{id}', 'SupplierCategoryMasterAPIController@destroyCheck')->name("Validate supplier business category can delete");
    Route::resource('supplierBusinessCategories', 'SupplierCategoryMasterAPIController');
});

//Supplier Business Sub Category
Route::group([], function() {
    Route::post('getAllSupplierBusinessSubCategories', 'SupplierCategorySubAPIController@getAllSupplierBusinessSubCategories')->name("Get all supplier business sub categories");
    Route::get('supplierBusinessSubCategoryFormData', 'SupplierCategorySubAPIController@getSupplierBusinessSubCategoryFormData')->name("Get supplier business sub category form data");
    Route::post('validateSupplierBusinessSubCategoryAmend', 'SupplierCategorySubAPIController@validateSupplierBusinessSubCategoryAmend')->name("Validate supplier business sub category amend");
    Route::get('supplierBusinessSubCategoryDestroyCheck/{id}', 'SupplierCategorySubAPIController@destroyCheck')->name("Validate supplier business sub category can delete");
    Route::resource('supplierBusinessSubCategories', 'SupplierCategorySubAPIController');
});

//Reason Code Master
Route::group([],function (){
    Route::post('getAllReasonCodeMaster', 'ReasonCodeMasterAPIController@getAllReasonCodeMaster')->name("Get all reason code master");
    Route::post('updateReasonCodeMaster', 'ReasonCodeMasterAPIController@update')->name("Update reason code master");
    Route::get('getAllGLCodesForReasonMaster', 'ReasonCodeMasterAPIController@getAllGLCodes')->name("Get all gl codes for reason master");
});

//Approvals

//Item
Route::group([],function (){
    Route::post('getAllItemsMasterApproval', 'ItemMasterAPIController@getAllItemsMasterApproval')->name("Get All Items Master Approval");
});

//Chart of Account
Route::group([],function (){
	Route::post('getAllChartOfAccountApproval', 'ChartOfAccountAPIController@getAllChartOfAccountApproval')->name("Get All Chart OF Account Approval");
	Route::post('rejectChartOfAccount', 'ChartOfAccountAPIController@rejectChartOfAccount')->name("Reject Chart OF Account");

});

//Supplier
Route::group([],function (){
    Route::post('getAllSupplierMasterApproval', 'SupplierMasterAPIController@getAllSupplierMasterApproval')->name("Get all supplier master approval");
    Route::post('rejectSupplier', 'SupplierMasterAPIController@rejectSupplier')->name("Reject Supplier");
});

//Customer
Route::group([],function (){
    Route::post('getAllCustomerMasterApproval', 'CustomerMasterAPIController@getAllCustomerMasterApproval')->name("Get all customer master approval");
    Route::post('rejectCustomer', 'CustomerMasterAPIController@rejectCustomer')->name("Reject Customer");
});

//Registered Supplier
Route::group([],function (){
    Route::post('getAllRegisteredSupplierApproval', 'SupplierMasterAPIController@getAllRegisteredSupplierApproval')->name("Get all registered supplier approval");
    Route::post('approveRegisteredSupplier', 'SupplierMasterAPIController@approveRegisteredSupplier')->name("Approve registered supplier");
    Route::post('rejectRegisteredSupplier', 'SupplierMasterAPIController@rejectRegisteredSupplier')->name("Reject registered supplier");
});

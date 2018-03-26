<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

     phpinfo();
    //return view('welcome');
});


Route::resource('supplierMasters', 'SupplierMasterController');

Route::resource('supplierCategoryMasters', 'SupplierCategoryMasterController');

Route::resource('supplierCurrencies', 'SupplierCurrencyController');

Route::resource('currencyMasters', 'CurrencyMasterController');



Route::resource('countryMasters', 'CountryMasterController');

Route::resource('supplierCategoryMasters', 'SupplierCategoryMasterController');

Route::resource('supplierImportances', 'SupplierImportanceController');

Route::resource('supplierImportances', 'SupplierImportanceController');

Route::resource('suppliernatures', 'suppliernatureController');

Route::resource('supplierTypes', 'SupplierTypeController');

Route::resource('supplierCategoryMasters', 'SupplierCategoryMasterController');

Route::resource('supplierSubCategoryAssigns', 'SupplierSubCategoryAssignController');




Route::resource('currencyMasters', 'CurrencyMasterController');

Route::resource('supplierCurrencies', 'SupplierCurrencyController');

Route::resource('supplierCurrencies', 'SupplierCurrencyController');

Route::resource('currencyMasters', 'CurrencyMasterController');

Route::resource('supplierCriticals', 'SupplierCriticalController');

Route::resource('supplierAssigneds', 'SupplierAssignedController');

Route::resource('yesNoSelections', 'YesNoSelectionController');

Route::resource('documentMasters', 'DocumentMasterController');

Route::resource('supplierContactDetails', 'SupplierContactDetailsController');

Route::resource('supplierContactTypes', 'SupplierContactTypeController');

Route::resource('bankMemoSuppliers', 'BankMemoSupplierController');

Route::resource('bankMemoSupplierMasters', 'BankMemoSupplierMasterController');

Route::resource('itemMasters', 'ItemMasterController');

Route::resource('units', 'UnitController');

Route::resource('financeItemcategorySubs', 'FinanceItemcategorySubController');

Route::resource('financeItemCategorySubs', 'FinanceItemCategorySubController');

Route::resource('financeItemcategorySubAssigneds', 'FinanceItemcategorySubAssignedController');

Route::resource('financeItemCategoryMasters', 'FinanceItemCategoryMasterController');

Route::resource('itemAssigneds', 'ItemAssignedController');

Route::resource('purchaseOrderDetails', 'PurchaseOrderDetailsController');





Route::resource('controlAccounts', 'ControlAccountController');

Route::resource('accountsTypes', 'AccountsTypeController');Route::resource('purchaseOrderDetails', 'PurchaseOrderDetailsController');

Route::resource('warehouseMasters', 'WarehouseMasterController');

Route::resource('erpLocations', 'ErpLocationController');

Route::resource('segmentMasters', 'SegmentMasterController');

Route::resource('bankMasters', 'BankMasterController');

Route::resource('bankAssigns', 'BankAssignController');

Route::resource('units', 'UnitController');

Route::resource('unitConversions', 'UnitConversionController');



Route::resource('purchaseRequests', 'PurchaseRequestController');

Route::resource('priorities', 'PriorityController');

Route::resource('locations', 'LocationController');
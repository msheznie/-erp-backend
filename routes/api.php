<?php

use Illuminate\Http\Request;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function(){

    /** Warehouse master Created by Fayas  */
    Route::resource('employees', 'EmployeeAPIController');

    Route::resource('employee_navigations', 'EmployeeNavigationAPIController');

    Route::resource('navigation_menuses', 'NavigationMenusAPIController');

    Route::resource('navigation_user_group_setups', 'NavigationUserGroupSetupAPIController');

    Route::get('user/companies', 'UserAPIController@userCompanies');
    Route::get('checkUser', 'UserAPIController@checkUser');

    Route::post('supplierMasterByCompany', 'SupplierMasterAPIController@getSupplierMasterByCompany');

    Route::get('user/menu', 'NavigationUserGroupSetupAPIController@userMenu');


    Route::get('subCategoriesByMasterCategory', 'SupplierCategorySubAPIController@getSubCategoriesByMasterCategory');
    Route::resource('supplier/masters', 'SupplierMasterAPIController');

    Route::post('supplier/masters/update', 'SupplierMasterAPIController@updateSupplierMaster');

    Route::get('supplier/assignedCompanies', 'SupplierMasterAPIController@getAssignedCompaniesBySupplier');
    
    Route::get('allCurrencies', 'CurrencyMasterAPIController@getAllCurrencies');
    Route::get('supplier/currencies', 'CurrencyMasterAPIController@getCurrenciesBySupplier');

    Route::post('supplier/add/currency', 'CurrencyMasterAPIController@addCurrencyToSupplier');
    Route::post('supplier/update/currency', 'CurrencyMasterAPIController@updateCurrencyToSupplier');
    Route::post('supplier/remove/currency', 'CurrencyMasterAPIController@removeCurrencyToSupplier');

    Route::resource('supplier/assigned', 'SupplierAssignedAPIController');

    Route::get('contactDetailsBySupplier', 'SupplierContactDetailsAPIController@getContactDetailsBySupplier');

    Route::resource('supplier/contactDetails', 'SupplierContactDetailsAPIController');

    Route::resource('users', 'UserAPIController');

    Route::resource('companies', 'CompanyAPIController');

    Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');

    Route::get('supplierFormData', 'CompanyAPIController@getSupplierFormData');

    Route::resource('country_masters', 'CountryMasterAPIController');
    Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');
    Route::resource('supplier_category_subs', 'SupplierCategorySubAPIController');

    Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');

    Route::resource('supplier_importances', 'SupplierImportanceAPIController');

    Route::resource('supplier_importances', 'SupplierImportanceAPIController');

    Route::resource('suppliernatures', 'suppliernatureAPIController');

    Route::resource('supplier_types', 'SupplierTypeAPIController');

    Route::post('addSubCategoryToSupplier', 'SupplierCategorySubAPIController@addSubCategoryToSupplier');

    Route::get('subcategoriesBySupplier', 'SupplierMasterAPIController@getSubcategoriesBySupplier');

    Route::post('removeSubCategoryToSupplier', 'SupplierCategorySubAPIController@removeSubCategoryToSupplier');

    Route::resource('supplier_currencies', 'SupplierCurrencyAPIController');

    Route::resource('currency_masters', 'CurrencyMasterAPIController');

    Route::resource('supplier_criticals', 'SupplierCriticalAPIController');

    Route::resource('yes_no_selections', 'YesNoSelectionAPIController');

    Route::resource('document_masters', 'DocumentMasterAPIController');

    Route::resource('supplier_contact_types', 'SupplierContactTypeAPIController');

    Route::resource('bank_memo_suppliers', 'BankMemoSupplierAPIController');

    Route::get('getBankMemoBySupplierCurrency', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrency');

    Route::resource('bank_memo_supplier_masters', 'BankMemoSupplierMasterAPIController');
    Route::post('deleteBankMemo', 'BankMemoSupplierAPIController@deleteBankMemo');

    Route::resource('item/masters', 'ItemMasterAPIController');
    Route::post('getAllItemsMaster', 'ItemMasterAPIController@getAllItemsMaster');
    Route::resource('units', 'UnitAPIController');
    Route::resource('finance_item_category_subs', 'FinanceItemCategorySubAPIController');

    Route::resource('itemcategory_sub_assigneds', 'FinanceItemcategorySubAssignedAPIController');

    Route::resource('finance_item_category_masters', 'FinanceItemCategoryMasterAPIController');

    Route::resource('item/masters', 'ItemMasterAPIController');
    Route::post('itemMasterBulkCreate', 'ItemMasterAPIController@itemMasterBulkCreate');

    Route::get('getItemMasterFormData', 'ItemMasterAPIController@getItemMasterFormData');
    Route::post('updateItemMaster', 'ItemMasterAPIController@updateItemMaster');
    Route::get('assignedCompaniesByItem', 'ItemMasterAPIController@getAssignedCompaniesByItem');

    Route::resource('item/assigneds', 'ItemAssignedAPIController');

    Route::get('getItemMasterPurchaseHistory', 'PurchaseOrderDetailsAPIController@getItemMasterPurchaseHistory');

    Route::get('getSubcategoriesBymainCategory', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategory');
    Route::get('exportPurchaseHistory', 'PurchaseOrderDetailsAPIController@exportPurchaseHistory');

    Route::post('allItemFinanceCategories','FinanceItemCategoryMasterAPIController@allItemFinanceCategories');
    Route::post('allItemFinanceSubCategoriesByMainCategory','FinanceItemCategoryMasterAPIController@allItemFinanceSubCategoriesByMainCategory');
    Route::get('getSubCategoryFormData','FinanceItemCategoryMasterAPIController@getSubCategoryFormData');

    Route::get('assignedCompaniesBySubCategory','FinanceItemcategorySubAssignedAPIController@assignedCompaniesBySubCategory');

    /** Company Navigation Menu access*/
    Route::get('getGroupCompany','CompanyNavigationMenusAPIController@getGroupCompany');
    Route::get('getCompanyNavigation','CompanyNavigationMenusAPIController@getCompanyNavigation');
    Route::resource('company_navigation_menuses', 'CompanyNavigationMenusAPIController');
    Route::resource('assignCompanyNavigation','CompanyNavigationMenusAPIController');
    /** Company user group*/
    Route::post('getUserGroupByCompanyDatatable', 'UserGroupAPIController@getUserGroupByCompanyDatatable');
    Route::resource('userGroups', 'UserGroupAPIController');
    Route::get('getUserGroup', 'UserGroupAPIController@getUserGroup');
    Route::resource('assignUserGroupNavigation','UserGroupAssignAPIController');
    Route::get('getUserGroupNavigation','UserGroupAssignAPIController@getUserGroupNavigation');
    Route::get('getAllCompanies','CompanyAPIController@getAllCompanies');
    Route::get('getNotAssignedCompanies','FinanceItemcategorySubAssignedAPIController@getNotAssignedCompanies');
    Route::resource('user_group_assigns', 'UserGroupAssignAPIController');
    Route::get('checkUserGroupAccessRights','UserGroupAssignAPIController@checkUserGroupAccessRights');
    Route::resource('purchase_order_details', 'PurchaseOrderDetailsAPIController');
    Route::post('purchase_order_details_frm_pr', 'PurchaseOrderDetailsAPIController@storePurchaseOrderDetailsFromPR');
    /** Approval Level*/
    Route::post('getGroupApprovalLevelDatatable', 'ApprovalLevelAPIController@getGroupApprovalLevelDatatable');
    Route::get('getGroupFilterData', 'ApprovalLevelAPIController@getGroupFilterData');
    Route::get('getAllDocuments', 'DocumentMasterAPIController@getAllDocuments');
    Route::resource('approval_levels', 'ApprovalLevelAPIController');
    Route::resource('approval_roles', 'ApprovalRoleAPIController');
    Route::resource('department_masters', 'DepartmentMasterAPIController');
    Route::resource('approval_groups', 'ApprovalGroupsAPIController');

    Route::get('getCompanyServiceLine', 'ApprovalLevelAPIController@getCompanyServiceLine');
    Route::post('activateApprovalLevel', 'ApprovalLevelAPIController@activateApprovalLevel');
    Route::get('getAllApprovalGroup', 'ApprovalGroupsAPIController@getAllApprovalGroup');
    Route::post('assignApprovalGroup', 'ApprovalRoleAPIController@assignApprovalGroup');
    Route::get('getApprovalRollByLevel', 'ApprovalRoleAPIController@getApprovalRollByLevel');

    /** Chart of Account Created by Shafri */
    Route::post('chartOfAccount', 'ChartOfAccountAPIController@getChartOfAccount');
    Route::resource('control_accounts', 'ControlAccountAPIController');
    Route::get('getChartOfAccountFormData', 'ChartOfAccountAPIController@getChartOfAccountFormData');
    Route::resource('chart_of_account', 'ChartOfAccountAPIController');
    Route::get('assignedCompaniesByChartOfAccount', 'ChartOfAccountAPIController@assignedCompaniesByChartOfAccount');
    Route::get('getNotAssignedCompaniesByChartOfAccount', 'ChartOfAccountAPIController@getNotAssignedCompaniesByChartOfAccount');
    Route::resource('chart_of_accounts_assigned', 'ChartOfAccountsAssignedAPIController');



    Route::resource('erp_locations', 'ErpLocationAPIController');
    Route::resource('accounts_types', 'AccountsTypeAPIController');


    /** Segment master Created by Nazir  */

    Route::post('getAllSegmentMaster', 'SegmentMasterAPIController@getAllSegmentMaster');
    Route::get('getSegmentMasterFormData', 'SegmentMasterAPIController@getSegmentMasterFormData');
    Route::resource('segment/masters', 'SegmentMasterAPIController');

    Route::post('updateSegmentMaster', 'SegmentMasterAPIController@updateSegmentMaster');


    /** Warehouse master Created by Pasan  */
    Route::resource('warehouse/masters', 'WarehouseMasterAPIController');
    Route::get('getWarehouseMasterFormData', 'WarehouseMasterAPIController@getWarehouseMasterFormData');
    Route::post('getAllWarehouseMaster', 'WarehouseMasterAPIController@getAllWarehouseMaster');
    Route::post('updateWarehouseMaster', 'WarehouseMasterAPIController@updateWarehouseMaster');

    /** Warehouse master Created by Fayas  */
    Route::resource('customer_masters', 'CustomerMasterAPIController');
    Route::post('getAllCustomers', 'CustomerMasterAPIController@getAllCustomers');
    Route::get('getCustomerFormData', 'CustomerMasterAPIController@getCustomerFormData');
    Route::get('getAssignedCompaniesByCustomer', 'CustomerMasterAPIController@getAssignedCompaniesByCustomer');
    Route::resource('customer_assigneds', 'CustomerAssignedAPIController');
    Route::get('getNotAssignedCompaniesByCustomer', 'CustomerAssignedAPIController@getNotAssignedCompaniesByCustomer');

    /** Bank master Created by Pasan  */
    Route::resource('bank/masters', 'BankMasterAPIController');
    Route::post('getAllBankMaster', 'BankMasterAPIController@getAllBankMaster');
    Route::post('updateBankMaster', 'BankMasterAPIController@updateBankMaster');
    Route::post('assignedCompaniesByBank', 'BankMasterAPIController@assignedCompaniesByBank');
    Route::get('getBankMasterFormData', 'BankMasterAPIController@getBankMasterFormData');
    Route::resource('bank/assign', 'BankAssignAPIController');
    Route::post('bank/update/assign', 'BankAssignAPIController@updateBankAssingCompany');


    Route::resource('customer_currencies', 'CustomerCurrencyAPIController');
    Route::get('getAddedCurrenciesByCustomer', 'CustomerCurrencyAPIController@getAddedCurrenciesByCustomer');
    Route::get('getNotAddedCurrenciesByCustomer', 'CustomerCurrencyAPIController@getNotAddedCurrenciesByCustomer');

    /** Unit master Created by Pasan  */
    Route::resource('unit/masters', 'UnitAPIController');
    Route::post('getAllUnitMaster', 'UnitAPIController@getAllUnitMaster');
    Route::post('updateUnitMaster', 'UnitAPIController@updateUnitMaster');
    Route::get('getUnitMasterFormData', 'UnitAPIController@getUnitMasterFormData');
    Route::resource('unit/conversion', 'UnitConversionAPIController');
    Route::get('getUnitConversionFormData', 'UnitConversionAPIController@getUnitConversionFormData');
    Route::post('unit/conversion/update', 'UnitConversionAPIController@updateUnitConversion');

    /** Approval Group Created by Mubashir  */
    Route::post('getApprovalGroupByCompanyDatatable', 'ApprovalGroupsAPIController@getApprovalGroupByCompanyDatatable');
    Route::resource('approval_groups', 'ApprovalGroupsAPIController');

    Route::resource('purchase_requests', 'PurchaseRequestAPIController');
    Route::post('getPurchaseRequestByDocumentType', 'PurchaseRequestAPIController@getPurchaseRequestByDocumentType');
    Route::get('getPurchaseRequestFormData', 'PurchaseRequestAPIController@getPurchaseRequestFormData');
    Route::get('getPurchaseRequestForPO', 'PurchaseRequestAPIController@getPurchaseRequestForPO');

    Route::resource('procurement-order', 'ProcumentOrderAPIController');
    Route::post('getProcumentOrderByDocumentType', 'ProcumentOrderAPIController@getProcumentOrderByDocumentType');
    Route::get('getProcumentOrderFormData', 'ProcumentOrderAPIController@getProcumentOrderFormData');
    Route::get('getItemsByProcumentOrder', 'PurchaseOrderDetailsAPIController@getItemsByProcumentOrder');
    Route::get('getItemsOptionForProcumentOrder', 'ProcumentOrderAPIController@getItemsOptionForProcumentOrder');
    Route::get('getShippingAndInvoiceDetails', 'ProcumentOrderAPIController@getShippingAndInvoiceDetails');
    Route::get('getProcumentOrderPaymentTerms', 'PoPaymentTermsAPIController@getProcumentOrderPaymentTerms');

    Route::resource('priorities', 'PriorityAPIController');

    Route::resource('locations', 'LocationAPIController');

    Route::resource('yes_no_selection_for_minuses', 'YesNoSelectionForMinusAPIController');

    Route::resource('months', 'MonthsAPIController');

    Route::resource('company_policy_masters', 'CompanyPolicyMasterAPIController');
    Route::resource('company_document_attachments', 'CompanyDocumentAttachmentAPIController');
    Route::resource('purchase_request_details', 'PurchaseRequestDetailsAPIController');
    Route::get('getItemsOptionForPurchaseRequest', 'PurchaseRequestAPIController@getItemsOptionForPurchaseRequest');
    Route::get('getItemsByPurchaseRequest', 'PurchaseRequestDetailsAPIController@getItemsByPurchaseRequest');
    Route::get('getPurchaseRequestDetailForPO', 'PurchaseRequestDetailsAPIController@getPurchaseRequestDetailForPO');

    Route::resource('document_approveds', 'DocumentApprovedAPIController');
    Route::resource('company_policy_masters', 'CompanyPolicyMasterAPIController');

    Route::resource('currency_conversions', 'CurrencyConversionAPIController');

    Route::resource('bank_accounts', 'BankAccountAPIController');
    Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');

    Route::resource('g_r_v_masters', 'GRVMasterAPIController');
    Route::resource('g_r_v_details', 'GRVDetailsAPIController');

    Route::resource('document_attachments', 'DocumentAttachmentsAPIController');
    Route::resource('document_attachment_types', 'DocumentAttachmentTypeAPIController');
    Route::get('downloadFile', 'DocumentAttachmentsAPIController@downloadFile');

    Route::post('getAllItemsMasterApproval', 'ItemMasterAPIController@getAllItemsMasterApproval');
    Route::post('getAllSupplierMasterApproval', 'SupplierMasterAPIController@getAllSupplierMasterApproval');
    Route::post('getAllCustomerMasterApproval', 'CustomerMasterAPIController@getAllCustomerMasterApproval');
    Route::post('getAllChartOfAccountApproval', 'ChartOfAccountAPIController@getAllChartOfAccountApproval');

    Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');
    Route::resource('employees_departments', 'EmployeesDepartmentAPIController');

    Route::post('approveItem', 'ItemMasterAPIController@approveItem');
    Route::post('rejectItem', 'ItemMasterAPIController@rejectItem');

    Route::post('approveSupplier', 'SupplierMasterAPIController@approveSupplier');
    Route::post('rejectSupplier', 'SupplierMasterAPIController@rejectSupplier');

    Route::post('approveCustomer', 'CustomerMasterAPIController@approveCustomer');
    Route::post('rejectCustomer', 'CustomerMasterAPIController@rejectCustomer');

    Route::post('approveChartOfAccount', 'ChartOfAccountAPIController@approveChartOfAccount');
    Route::post('rejectChartOfAccount', 'ChartOfAccountAPIController@rejectChartOfAccount');

    /** Po Related Tables Created by Nazir  */
    Route::resource('erp_addresses', 'ErpAddressAPIController');
    Route::resource('po_payment_terms', 'PoPaymentTermsAPIController');
    Route::resource('po_advance_payments', 'PoAdvancePaymentAPIController');
    Route::resource('procumentOrderPaymentTermsCRUD', 'PoPaymentTermsAPIController');
    Route::resource('procumentOrderPaymentTermsUD', 'PoPaymentTermsAPIController');

    Route::resource('procumentOrderPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');

});

Route::get('exchangerate', 'ApprovalLevelAPIController@confirmDocTest');



Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');
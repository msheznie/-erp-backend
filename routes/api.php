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

Route::group(['middleware' => 'auth:api'], function () {

    /** Warehouse master Created by Fayas  */
    Route::resource('employees', 'EmployeeAPIController');
    Route::get('getTypeheadEmployees', 'EmployeeAPIController@getTypeheadEmployees');

    Route::resource('employee_navigations', 'EmployeeNavigationAPIController');

    Route::resource('navigation_menuses', 'NavigationMenusAPIController');

    Route::resource('navigation_user_group_setups', 'NavigationUserGroupSetupAPIController');

    Route::get('user/companies', 'UserAPIController@userCompanies');
    Route::get('checkUser', 'UserAPIController@checkUser');

    Route::post('supplierMasterByCompany', 'SupplierMasterAPIController@getSupplierMasterByCompany');
    Route::post('exportSupplierMaster', 'SupplierMasterAPIController@exportSupplierMaster');
    Route::get('getPOSuppliers', 'SupplierMasterAPIController@getPOSuppliers');
    Route::get('getSuppliersByCompany', 'SupplierMasterAPIController@getSuppliersByCompany');
    Route::get('getSearchSupplierByCompany', 'SupplierMasterAPIController@getSearchSupplierByCompany');

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

    Route::post('allItemFinanceCategories', 'FinanceItemCategoryMasterAPIController@allItemFinanceCategories');
    Route::post('allItemFinanceSubCategoriesByMainCategory', 'FinanceItemCategoryMasterAPIController@allItemFinanceSubCategoriesByMainCategory');
    Route::get('getSubCategoryFormData', 'FinanceItemCategoryMasterAPIController@getSubCategoryFormData');

    Route::get('assignedCompaniesBySubCategory', 'FinanceItemcategorySubAssignedAPIController@assignedCompaniesBySubCategory');

    /** Company Navigation Menu access*/
    Route::get('getGroupCompany', 'CompanyNavigationMenusAPIController@getGroupCompany');
    Route::get('getCompanyNavigation', 'CompanyNavigationMenusAPIController@getCompanyNavigation');
    Route::resource('company_navigation_menuses', 'CompanyNavigationMenusAPIController');
    Route::resource('assignCompanyNavigation', 'CompanyNavigationMenusAPIController');
    /** Company user group*/
    Route::post('getUserGroupByCompanyDatatable', 'UserGroupAPIController@getUserGroupByCompanyDatatable');
    Route::resource('userGroups', 'UserGroupAPIController');
    Route::get('getUserGroup', 'UserGroupAPIController@getUserGroup');
    Route::post('getUserGroupEmployeesDatatable', 'EmployeeNavigationAPIController@getUserGroupEmployeesByCompanyDatatable');

    Route::resource('assignUserGroupNavigation', 'UserGroupAssignAPIController');
    Route::get('getUserGroupNavigation', 'UserGroupAssignAPIController@getUserGroupNavigation');
    Route::get('getAllCompanies', 'CompanyAPIController@getAllCompanies');
    Route::get('getNotAssignedCompanies', 'FinanceItemcategorySubAssignedAPIController@getNotAssignedCompanies');
    Route::resource('user_group_assigns', 'UserGroupAssignAPIController');
    Route::get('checkUserGroupAccessRights', 'UserGroupAssignAPIController@checkUserGroupAccessRights');
    Route::resource('purchase_order_details', 'PurchaseOrderDetailsAPIController');
    Route::post('purchase_order_details_frm_pr', 'PurchaseOrderDetailsAPIController@storePurchaseOrderDetailsFromPR');
    Route::post('procumentOrderDeleteAllDetails', 'PurchaseOrderDetailsAPIController@procumentOrderDeleteAllDetails');
    Route::get('procumentOrderDetailTotal', 'ProcumentOrderAPIController@procumentOrderDetailTotal');
    Route::get('poPaymentTermsAdvanceDetailView', 'PoAdvancePaymentAPIController@poPaymentTermsAdvanceDetailView');
    Route::post('procumentOrderTotalDiscountUD', 'PurchaseOrderDetailsAPIController@procumentOrderTotalDiscountUD');
    Route::post('procumentOrderTotalTaxUD', 'PurchaseOrderDetailsAPIController@procumentOrderTotalTaxUD');
    Route::get('poCheckDetailExistinGrv', 'ProcumentOrderAPIController@poCheckDetailExistinGrv');

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
    Route::get('getAllWHForSelectedCompany', 'WarehouseMasterAPIController@getAllWarehouseForSelectedCompany');
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
    Route::post('getPOMasterApproval', 'ProcumentOrderAPIController@getPOMasterApproval');
    Route::post('getApprovedPOForCurrentUser', 'ProcumentOrderAPIController@getApprovedPOForCurrentUser');
    Route::post('getProcumentOrderAllAmendments', 'ProcumentOrderAPIController@getProcumentOrderAllAmendments');
    Route::get('getGRVBasedPODropdowns', 'ProcumentOrderAPIController@getGRVBasedPODropdowns');
    Route::get('getLogisticPrintDetail', 'PoAdvancePaymentAPIController@getLogisticPrintDetail');
    Route::get('getLogisticsItemsByProcumentOrder', 'PoAdvancePaymentAPIController@loadPoPaymentTermsLogistic');

    Route::resource('priorities', 'PriorityAPIController');

    Route::resource('locations', 'LocationAPIController');

    Route::resource('yes_no_selection_for_minuses', 'YesNoSelectionForMinusAPIController');

    Route::resource('months', 'MonthsAPIController');

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

    Route::resource('poPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');
    Route::post('storePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@storePoPaymentTermsLogistic');

    Route::resource('document_attachments', 'DocumentAttachmentsAPIController');
    Route::resource('document_attachment_types', 'DocumentAttachmentTypeAPIController');
    Route::get('downloadFile', 'DocumentAttachmentsAPIController@downloadFile');

    Route::post('getAllItemsMasterApproval', 'ItemMasterAPIController@getAllItemsMasterApproval');
    Route::post('getAllSupplierMasterApproval', 'SupplierMasterAPIController@getAllSupplierMasterApproval');
    Route::post('getAllCustomerMasterApproval', 'CustomerMasterAPIController@getAllCustomerMasterApproval');
    Route::post('getAllChartOfAccountApproval', 'ChartOfAccountAPIController@getAllChartOfAccountApproval');

    Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');
    Route::resource('procumentOrderAdvpaymentUD', 'PoAdvancePaymentAPIController');
    Route::post('updatePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@updatePoPaymentTermsLogistic');
    Route::resource('employees_departments', 'EmployeesDepartmentAPIController');
    Route::post('getApprovalAccessRights', 'EmployeesDepartmentAPIController@getApprovalAccessRightsDatatable');
    Route::get('getApprovalAccessRightsFormData', 'EmployeesDepartmentAPIController@getApprovalAccessRightsFormData');
    Route::get('getDepartmentDocument', 'EmployeesDepartmentAPIController@getDepartmentDocument');
    Route::post('deleteAllAccessRights', 'EmployeesDepartmentAPIController@deleteAllAccessRights');

    Route::post('approveItem', 'ItemMasterAPIController@approveItem');
    Route::post('rejectItem', 'ItemMasterAPIController@rejectItem');

    Route::post('approveSupplier', 'SupplierMasterAPIController@approveSupplier');
    Route::post('rejectSupplier', 'SupplierMasterAPIController@rejectSupplier');

    Route::post('approveCustomer', 'CustomerMasterAPIController@approveCustomer');
    Route::post('rejectCustomer', 'CustomerMasterAPIController@rejectCustomer');

    Route::post('approveChartOfAccount', 'ChartOfAccountAPIController@approveChartOfAccount');
    Route::post('rejectChartOfAccount', 'ChartOfAccountAPIController@rejectChartOfAccount');

    Route::post('generateReport', 'ReportAPIController@generateReport');
    Route::post('validateReport', 'ReportAPIController@validateReport');
    Route::post('exportReport', 'ReportAPIController@exportReport');

    Route::post('generateARReport', 'AccountsReceivableReportAPIController@generateReport');
    Route::post('validateARReport', 'AccountsReceivableReportAPIController@validateReport');
    Route::post('exportARReport', 'AccountsReceivableReportAPIController@exportReport');
    Route::get('getAcountReceivableFilterData', 'AccountsReceivableReportAPIController@getAcountReceivableFilterData');

    Route::post('generateAMReport', 'AssetManagementReportAPIController@generateReport');
    Route::post('validateAMReport', 'AssetManagementReportAPIController@validateReport');
    Route::post('exportAMReport', 'AssetManagementReportAPIController@exportReport');
    Route::get('getAssetManagementFilterData', 'AssetManagementReportAPIController@getFilterData');

    Route::post('approveProcurementOrder', 'ProcumentOrderAPIController@approveProcurementOrder');
    Route::post('rejectProcurementOrder', 'ProcumentOrderAPIController@rejectProcurementOrder');
    Route::get('getGoodReceivedNoteDetailsForPO', 'ProcumentOrderAPIController@getGoodReceivedNoteDetailsForPO');
    Route::post('getGRVDrilldownSpentAnalysis', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysis');
    Route::post('getGRVDrilldownSpentAnalysisTotal', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysisTotal');
    Route::get('getInvoiceDetailsForPO', 'ProcumentOrderAPIController@getInvoiceDetailsForPO');

    /** Po Related Tables Created by Nazir  */
    Route::resource('erp_addresses', 'ErpAddressAPIController');
    Route::resource('po_payment_terms', 'PoPaymentTermsAPIController');
    Route::resource('po_advance_payments', 'PoAdvancePaymentAPIController');
    Route::resource('procumentOrderPaymentTermsCRUD', 'PoPaymentTermsAPIController');
    Route::resource('procumentOrderPaymentTermsUD', 'PoPaymentTermsAPIController');
    Route::post('procumentOrderCancel', 'ProcumentOrderAPIController@procumentOrderCancel');
    Route::post('procumentOrderReturnBack', 'ProcumentOrderAPIController@procumentOrderReturnBack');
    Route::post('manualCloseProcurementOrder', 'ProcumentOrderAPIController@manualCloseProcurementOrder');
    Route::post('manualCloseProcurementOrderPrecheck', 'ProcumentOrderAPIController@manualCloseProcurementOrderPrecheck');
    Route::post('procumentOrderSegmentchk', 'ProcumentOrderAPIController@procumentOrderSegmentchk');
    Route::get('ProcurementOrderAudit', 'ProcumentOrderAPIController@ProcurementOrderAudit');
    Route::post('getProcurementOrderReferBack', 'ProcumentOrderAPIController@getProcurementOrderReferBack');
    Route::get('getPurchasePaymentStatusHistory', 'ProcumentOrderAPIController@getPurchasePaymentStatusHistory');

    Route::get('reportSpentAnalysisBySupplierFilter', 'ProcumentOrderAPIController@reportSpentAnalysisBySupplierFilter');
    Route::post('reportSpentAnalysis', 'ProcumentOrderAPIController@reportSpentAnalysis');
    Route::post('reportSpentAnalysisExport', 'ProcumentOrderAPIController@reportSpentAnalysisExport');
    Route::post('reportSpentAnalysisDrilldownExport', 'ProcumentOrderAPIController@reportSpentAnalysisDrilldownExport');
    Route::post('reportSpentAnalysisHeader', 'ProcumentOrderAPIController@reportSpentAnalysisHeader');

    Route::post('reportPrToGrv', 'PurchaseRequestAPIController@reportPrToGrv');
    Route::get('reportPrToGrvFilterOptions', 'PurchaseRequestAPIController@reportPrToGrvFilterOptions');
    Route::get('getApprovedDetails', 'PurchaseRequestAPIController@getApprovedDetails');

    Route::resource('poPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');

    Route::get('exchangerate', 'ApprovalLevelAPIController@confirmDocTest');

    Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');

    Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');

    Route::resource('g_r_v_masters', 'GRVMasterAPIController');

    Route::resource('g_r_v_details', 'GRVDetailsAPIController');

    Route::resource('purchase_order_process_details', 'PurchaseOrderProcessDetailsAPIController');

    Route::get('getProcurementOrderRecord', 'ProcumentOrderAPIController@getProcurementOrderRecord');

    Route::post('getPurchaseRequestApprovalByUser', 'PurchaseRequestAPIController@getPurchaseRequestApprovalByUser');
    Route::post('getPurchaseRequestApprovedByUser', 'PurchaseRequestAPIController@getPurchaseRequestApprovedByUser');

    Route::post('rejectPurchaseRequest', 'PurchaseRequestAPIController@rejectPurchaseRequest');
    Route::post('approvePurchaseRequest', 'PurchaseRequestAPIController@approvePurchaseRequest');

    Route::resource('tax_authorities', 'TaxAuthorityAPIController');
    Route::post('getTaxAuthorityDatatable', 'TaxAuthorityAPIController@getTaxAuthorityDatatable');
    Route::get('getTaxAuthorityFormData', 'TaxAuthorityAPIController@getTaxAuthorityFormData');
    Route::resource('taxes', 'TaxAPIController');
    Route::get('getTaxMasterFormData', 'TaxAPIController@getTaxMasterFormData');
    Route::post('getTaxMasterDatatable', 'TaxAPIController@getTaxMasterDatatable');

    Route::get('getAuthorityByCompany', 'TaxAuthorityAPIController@getAuthorityByCompany');
    Route::get('getAccountByAuthority', 'TaxAuthorityAPIController@getAccountByAuthority');

    Route::resource('tax_types', 'TaxTypeAPIController');

    Route::resource('tax_formula_masters', 'TaxFormulaMasterAPIController');
    Route::post('getTaxFormulaMasterDatatable', 'TaxFormulaMasterAPIController@getTaxFormulaMasterDatatable');
    Route::resource('tax_formula_details', 'TaxFormulaDetailAPIController');
    Route::post('getTaxFormulaDetailDatatable', 'TaxFormulaDetailAPIController@getTaxFormulaDetailDatatable');

    Route::post('cancelPurchaseRequest', 'PurchaseRequestAPIController@cancelPurchaseRequest');
    Route::post('returnPurchaseRequest', 'PurchaseRequestAPIController@returnPurchaseRequest');
    Route::post('manualClosePurchaseRequest', 'PurchaseRequestAPIController@manualClosePurchaseRequest');
    Route::resource('tax_formula_masters', 'TaxFormulaMasterAPIController');

    Route::resource('tax_formula_details', 'TaxFormulaDetailAPIController');
    Route::get('getOtherTax', 'TaxFormulaDetailAPIController@getOtherTax');

    Route::resource('advance_payment_details', 'AdvancePaymentDetailsAPIController');

    Route::resource('alerts', 'AlertAPIController');
    Route::resource('access_tokens', 'AccessTokensAPIController');
    Route::resource('users_log_histories', 'UsersLogHistoryAPIController');


    Route::resource('addresses', 'AddressAPIController');
    Route::post('getAllAddresses', 'AddressAPIController@getAllAddresses');
    Route::get('getAddressFormData', 'AddressAPIController@getAddressFormData');

    Route::resource('address_types', 'AddressTypeAPIController');

    Route::post('getAllCompanyPolicy', 'CompanyPolicyMasterAPIController@getAllCompanyPolicy');
    Route::get('getCompanyPolicyFilterOptions', 'CompanyPolicyMasterAPIController@getCompanyPolicyFilterOptions');

    Route::get('purchaseRequestsPOHistory', 'PurchaseRequestAPIController@purchaseRequestsPOHistory');
    Route::get('purchaseRequestAudit', 'PurchaseRequestAPIController@purchaseRequestAudit');
    Route::resource('company_policy_categories', 'CompanyPolicyCategoryAPIController');

    Route::post('amendProcurementOrder', 'ProcumentOrderAPIController@amendProcurementOrder');
    Route::get('manualClosePurchaseRequestPreCheck', 'PurchaseRequestAPIController@manualClosePurchaseRequestPreCheck');
    Route::get('returnPurchaseRequestPreCheck', 'PurchaseRequestAPIController@returnPurchaseRequestPreCheck');
    Route::get('cancelPurchaseRequestPreCheck', 'PurchaseRequestAPIController@cancelPurchaseRequestPreCheck');
    Route::get('procumentOrderPrHistory', 'ProcumentOrderAPIController@procumentOrderPrHistory');
    Route::get('amendProcurementOrderPreCheck', 'ProcumentOrderAPIController@amendProcurementOrderPreCheck');
    Route::post('procumentOrderChangeSupplier', 'ProcumentOrderAPIController@procumentOrderChangeSupplier');

    Route::get('getErpLedger', 'ErpItemLedgerAPIController@getErpLedger');

    Route::resource('purchase_order_categories', 'PurchaseOrderCategoryAPIController');

    Route::resource('purchase_order_statuses', 'PurchaseOrderStatusAPIController');
    Route::get('getAllStatusByPurchaseOrder', 'PurchaseOrderStatusAPIController@getAllStatusByPurchaseOrder');
    Route::get('destroyPreCheck', 'PurchaseOrderStatusAPIController@destroyPreCheck');
    Route::post('purchaseOrderStatusesSendEmail', 'PurchaseOrderStatusAPIController@purchaseOrderStatusesSendEmail');
    Route::post('reportOrderStatus', 'PurchaseOrderStatusAPIController@reportOrderStatus');
    Route::get('reportOrderStatusFilterOptions', 'PurchaseOrderStatusAPIController@reportOrderStatusFilterOptions');
    Route::post('reportOrderStatusPreCheck', 'PurchaseOrderStatusAPIController@reportOrderStatusPreCheck');
    Route::post('exportReportOrderStatus', 'PurchaseOrderStatusAPIController@exportReportOrderStatus');
    Route::resource('erp_item_ledgers', 'ErpItemLedgerAPIController');
    Route::post('validateStockLedgerReport', 'ErpItemLedgerAPIController@validateStockLedgerReport');
    Route::post('generateStockLedgerReport', 'ErpItemLedgerAPIController@generateStockLedgerReport');
    Route::post('getReportOpenRequest', 'PurchaseRequestAPIController@getReportOpenRequest');
    Route::post('exportReportOpenRequest', 'PurchaseRequestAPIController@exportReportOpenRequest');
    Route::resource('g_r_v_types', 'GRVTypesAPIController');
    Route::resource('budget_consumed_datas', 'BudgetConsumedDataAPIController');
    Route::resource('customer_invoices', 'CustomerInvoiceAPIController');
    Route::resource('company_finance_years', 'CompanyFinanceYearAPIController');
    Route::resource('company_finance_periods', 'CompanyFinancePeriodAPIController');
    Route::resource('customer_invoices', 'CustomerInvoiceAPIController');
    Route::resource('accounts_receivable_ledgers', 'AccountsReceivableLedgerAPIController');

    Route::post('getGoodReceiptVoucherMasterView', 'GRVMasterAPIController@getGoodReceiptVoucherMasterView');
    Route::get('getGRVFormData', 'GRVMasterAPIController@getGRVFormData');
    Route::get('getWarehouse', 'ErpItemLedgerAPIController@getWarehouse');
    Route::post('generateStockValuationReport', 'ErpItemLedgerAPIController@generateStockValuationReport');
    Route::get('getAllFinancePeriod', 'CompanyFinancePeriodAPIController@getAllFinancePeriod');
    Route::resource('goodReceiptVoucherCRUD', 'GRVMasterAPIController');
    Route::get('getItemsByGRVMaster', 'GRVDetailsAPIController@getItemsByGRVMaster');
    Route::get('getLogisticsItemsByGRV', 'PoAdvancePaymentAPIController@loadPoPaymentTermsLogisticForGRV');
    Route::post('GRVSegmentChkActive', 'GRVMasterAPIController@GRVSegmentChkActive');
    Route::get('purchaseOrderForGRV', 'ProcumentOrderAPIController@purchaseOrderForGRV');
    Route::get('getPurchaseOrderDetailForGRV', 'PurchaseOrderDetailsAPIController@getPurchaseOrderDetailForGRV');
    Route::post('storeGRVDetailsFromPO', 'GRVDetailsAPIController@storeGRVDetailsFromPO');
    Route::resource('purchase_order_details', 'PurchaseOrderDetailsAPIController');
    Route::post('grvDeleteAllDetails', 'GRVDetailsAPIController@grvDeleteAllDetails');
    Route::get('goodReceiptVoucherAudit', 'GRVMasterAPIController@goodReceiptVoucherAudit');
    Route::resource('materiel_requests', 'MaterielRequestAPIController');
    Route::post('getAllRequestByCompany', 'MaterielRequestAPIController@getAllRequestByCompany');
    Route::get('getRequestFormData', 'MaterielRequestAPIController@getRequestFormData');
    Route::post('getAllNotApprovedRequestByUser', 'MaterielRequestAPIController@getAllNotApprovedRequestByUser');
    Route::post('getApprovedMaterielRequestsByUser', 'MaterielRequestAPIController@getApprovedMaterielRequestsByUser');
    Route::get('materielRequestAudit', 'MaterielRequestAPIController@materielRequestAudit');
    Route::resource('materiel_request_details', 'MaterielRequestDetailsAPIController');
    Route::get('getItemsByMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsByMaterielRequest');
    Route::get('getItemsOptionForMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsOptionForMaterielRequest');
    Route::post('exportStockEvaluation', 'ErpItemLedgerAPIController@exportStockEvaluation');
    Route::post('validateStockValuationReport', 'ErpItemLedgerAPIController@validateStockValuationReport');

    Route::resource('item_issue_details', 'ItemIssueDetailsAPIController');

    Route::resource('item_issue_masters', 'ItemIssueMasterAPIController');
    Route::post('getAllMaterielIssuesByCompany', 'ItemIssueMasterAPIController@getAllMaterielIssuesByCompany');
    Route::post('getMaterielIssueApprovedByUser', 'ItemIssueMasterAPIController@getMaterielIssueApprovedByUser');
    Route::post('getMaterielIssueApprovalByUser', 'ItemIssueMasterAPIController@getMaterielIssueApprovalByUser');
    Route::get('getMaterielIssueFormData', 'ItemIssueMasterAPIController@getMaterielIssueFormData');
    Route::get('allMaterielRequestNotSelectedForIssue', 'ItemIssueMasterAPIController@getAllMaterielRequestNotSelectedForIssueByCompany');
    Route::get('getMaterielIssueAudit', 'ItemIssueMasterAPIController@getMaterielIssueAudit');
    Route::get('getItemsByMaterielIssue', 'ItemIssueDetailsAPIController@getItemsByMaterielIssue');
    Route::get('getItemsOptionsMaterielIssue', 'ItemIssueDetailsAPIController@getItemsOptionsMaterielIssue');
    Route::post('getGRVMasterApproval', 'GRVMasterAPIController@getGRVMasterApproval');
    Route::post('getApprovedGRVForCurrentUser', 'GRVMasterAPIController@getApprovedGRVForCurrentUser');
    Route::post('approveGoodReceiptVoucher', 'GRVMasterAPIController@approveGoodReceiptVoucher');
    Route::post('rejectGoodReceiptVoucher', 'GRVMasterAPIController@rejectGoodReceiptVoucher');
    Route::resource('general_ledgers', 'GeneralLedgerAPIController');
    Route::resource('item_issue_types', 'ItemIssueTypeAPIController');
    Route::get('getSearchCustomerByCompany', 'CustomerMasterAPIController@getSearchCustomerByCompany');
    Route::post('generateStockTakingReport', 'ErpItemLedgerAPIController@generateStockTakingReport');
    Route::post('exportStockTaking', 'ErpItemLedgerAPIController@exportStockTaking');

    Route::resource('accounts_payable_ledgers', 'AccountsPayableLedgerAPIController');
    Route::get('getAPFilterData', 'AccountsPayableReportAPIController@getAPFilterData');
    Route::post('validateAPReport', 'AccountsPayableReportAPIController@validateAPReport');
    Route::post('generateAPReport', 'AccountsPayableReportAPIController@generateAPReport');
    Route::post('exportAPReport', 'AccountsPayableReportAPIController@exportReport');

    Route::get('getFRFilterData', 'FinancialReportAPIController@getFRFilterData');
    Route::post('validateFRReport', 'FinancialReportAPIController@validateFRReport');
    Route::post('generateFRReport', 'FinancialReportAPIController@generateFRReport');
    Route::post('exportFRReport', 'FinancialReportAPIController@exportReport');

    Route::post('getAllStockTransferByCompany', 'StockTransferAPIController@getStockTransferMasterView');
    Route::get('getStockTransferFormData', 'StockTransferAPIController@getStockTransferFormData');
    Route::get('getStockTransferDetails', 'StockTransferDetailsAPIController@getStockTransferDetails');
    Route::get('getItemsOptionForStockTransfer', 'StockTransferAPIController@getItemsOptionForStockTransfer');
    Route::resource('stock_transfer_details', 'StockTransferDetailsAPIController');
    Route::resource('stock_transfers', 'StockTransferAPIController');
    Route::get('StockTransferAudit', 'StockTransferAPIController@StockTransferAudit');
    Route::post('getStockTransferApproval', 'StockTransferAPIController@getStockTransferApproval');
    Route::post('getApprovedSTForCurrentUser', 'StockTransferAPIController@getApprovedSTForCurrentUser');
    Route::post('approveStockTransfer', 'StockTransferAPIController@approveStockTransfer');
    Route::post('rejectStockTransfer', 'StockTransferAPIController@rejectStockTransfer');
    Route::resource('item_return_details', 'ItemReturnDetailsAPIController');
    Route::resource('item_return_masters', 'ItemReturnMasterAPIController');
    Route::post('getAllMaterielReturnByCompany', 'ItemReturnMasterAPIController@getAllMaterielReturnByCompany');
    Route::post('getMaterielReturnApprovedByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovedByUser');
    Route::post('getMaterielReturnApprovalByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovalByUser');
    Route::get('getMaterielReturnFormData', 'ItemReturnMasterAPIController@getMaterielReturnFormData');
    Route::get('getItemsByMaterielReturn', 'ItemReturnDetailsAPIController@getItemsByMaterielReturn');
    Route::get('getItemsOptionsMaterielReturn', 'ItemReturnDetailsAPIController@getItemsOptionsMaterielReturn');
    Route::get('getMaterielReturnAudit', 'ItemReturnMasterAPIController@getMaterielReturnAudit');
    Route::post('getMaterielReturnApprovalByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovalByUser');
    Route::post('getMaterielReturnApprovedByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovedByUser');
    Route::get('getSupplierMasterAudit', 'SupplierMasterAPIController@getSupplierMasterAudit');
    Route::get('getItemMasterAudit', 'ItemMasterAPIController@getItemMasterAudit');


});

Route::get('getProcumentOrderPrintPDF', 'ProcumentOrderAPIController@getProcumentOrderPrintPDF');
Route::post('getReportPDF', 'ReportAPIController@pdfExportReport');
Route::post('generateARReportPDF', 'AccountsReceivableReportAPIController@pdfExportReport');
Route::get('printPurchaseRequest', 'PurchaseRequestAPIController@printPurchaseRequest');
Route::get('downloadFileFrom', 'DocumentAttachmentsAPIController@downloadFileFrom');


Route::get('getBcryptPassword/{password}', function ($password) {
    echo bcrypt($password);
});


Route::get('runQueue', function () {
    $master  = ['documentSystemID' => 12,'autoID' => 35];
    $job = \App\Jobs\ItemLedgerInsert::dispatch($master);
});

Route::resource('asset_finance_categories', 'AssetFinanceCategoryAPIController');
Route::resource('years', 'YearAPIController');



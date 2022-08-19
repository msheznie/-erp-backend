<?php

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


Route::group(['middleware' => ['tenant','locale']], function () {

    Route::group(['middleware' => ['pos_api']], function (){
        Route::post('pull_customer_category', 'POS\PosAPIController@pullCustomerCategory');
        Route::post('pull_location', 'POS\PosAPIController@pullLocation');
        Route::post('pull_segment', 'POS\PosAPIController@pullSegment');
        Route::post('pull_chart_of_account', 'POS\PosAPIController@pullChartOfAccount');
        Route::post('pull_unit_of_measure', 'POS\PosAPIController@pullUnitOfMeasure');
        Route::post('pull_unit_conversion', 'POS\PosAPIController@pullUnitConversion');
        Route::post('pull_warehouse', 'POS\PosAPIController@pullWarehouse');
        Route::post('pull_warehouse_item', 'POS\PosAPIController@pullWarehouseItem');
        Route::post('srp_erp_warehousebinlocation', 'POS\PosAPIController@pullWarehouseBinLocation');
        Route::post('pull_item', 'POS\PosAPIController@pullItem');
        Route::post('pull_item_bin_location', 'POS\PosAPIController@pullItemBinLocation');
        Route::post('pull_item_sub_category', 'POS\PosAPIController@pullItemSubCategory');
        Route::post('pull_user', 'POS\PosAPIController@pullUser');
        Route::post('pull_item_category', 'POS\PosAPIController@pullItemCategory');
        Route::post('posMappingRequest', 'POS\PosAPIController@handleRequest');
    });

    Route::group(['middleware' => 'auth:api'], function () {

        Route::get('getTypeheadEmployees', 'EmployeeAPIController@getTypeheadEmployees');

        Route::post('getAllEmployees', 'EmployeeAPIController@getAllEmployees');

        Route::resource('employeeMasterCRUD', 'EmployeeAPIController');
        Route::resource('employee_navigations', 'EmployeeNavigationAPIController');
        Route::get('getuserGroupAssignedCompanies', 'EmployeeNavigationAPIController@getuserGroupAssignedCompanies');

        Route::resource('navigation_menuses', 'NavigationMenusAPIController');

        Route::resource('navigation_user_group_setups', 'NavigationUserGroupSetupAPIController');

        Route::get('user/companies', 'UserAPIController@userCompanies');
        Route::get('checkUser', 'UserAPIController@checkUser');

        Route::post('supplierMasterByCompany', 'SupplierMasterAPIController@getSupplierMasterByCompany');
        Route::post('exportSupplierMaster', 'SupplierMasterAPIController@exportSupplierMaster');
        Route::get('getPOSuppliers', 'SupplierMasterAPIController@getPOSuppliers');
        Route::get('getRetentionPercentage', 'SupplierMasterAPIController@getRetentionPercentage');
        Route::get('getSuppliersByCompany', 'SupplierMasterAPIController@getSuppliersByCompany');
        Route::get('getSearchSupplierByCompany', 'SupplierMasterAPIController@getSearchSupplierByCompany');
        Route::get('generateSupplierExternalLink', 'SupplierMasterAPIController@generateSupplierExternalLink');
        Route::get('getRegisteredSupplierData', 'SupplierMasterAPIController@getRegisteredSupplierData');
        Route::get('bankMemosByRegisteredSupplierCurrency', 'SupplierMasterAPIController@bankMemosByRegisteredSupplierCurrency');
        Route::post('notApprovedRegisteredSuppliers', 'SupplierMasterAPIController@notApprovedRegisteredSuppliers');
        Route::post('approvedRegisteredSuppliers', 'SupplierMasterAPIController@approvedRegisteredSuppliers');
        Route::post('updateRegisteredSupplierAttachment', 'SupplierMasterAPIController@updateRegisteredSupplierAttachment');
        Route::post('updateRegisteredSupplierCurrency', 'SupplierMasterAPIController@updateRegisteredSupplierCurrency');
        Route::post('updateRegisteredSupplierBankMemo', 'SupplierMasterAPIController@updateRegisteredSupplierBankMemo');
        Route::post('updateRegisteredSupplierMaster', 'SupplierMasterAPIController@updateRegisteredSupplierMaster');
        Route::post('getAllRegisteredSupplierApproval', 'SupplierMasterAPIController@getAllRegisteredSupplierApproval');
        Route::get('downloadSupplierAttachmentFile', 'SupplierMasterAPIController@downloadSupplierAttachmentFile');
        Route::post('srmRegistrationLink', 'SupplierMasterAPIController@srmRegistrationLink');
        Route::post('srmRegistrationLinkHistoryView', 'SupplierMasterAPIController@srmRegistrationLinkHistoryView');

        Route::resource('registered_supplier_currencies', 'RegisteredSupplierCurrencyAPIController');
        Route::resource('registered_bank_memo_suppliers', 'RegisteredBankMemoSupplierAPIController');
        Route::resource('registered_supp_contact_details', 'RegisteredSupplierContactDetailAPIController');
        Route::resource('registered_supplier_attachments', 'RegisteredSupplierAttachmentAPIController');

        Route::get('user/menu', 'NavigationUserGroupSetupAPIController@userMenu');
        Route::get('getUserMenu', 'NavigationUserGroupSetupAPIController@getUserMenu');


        Route::group(['middleware' => 'max_memory_limit'], function () {
            Route::group(['middleware' => 'max_execution_limit'], function () {
                Route::post('generateAMReport', 'AssetManagementReportAPIController@generateReport');
                Route::post('exportAMReport', 'AssetManagementReportAPIController@exportReport');
                Route::post('exportAssetMaster', 'FixedAssetMasterAPIController@exportAssetMaster');
            });
        });


        Route::get('subCategoriesByMasterCategory', 'SupplierCategorySubAPIController@getSubCategoriesByMasterCategory');
        Route::resource('supplier/masters', 'SupplierMasterAPIController');
        Route::post('supplierReferBack', 'SupplierMasterAPIController@supplierReferBack');

        Route::post('supplier/masters/update', 'SupplierMasterAPIController@updateSupplierMaster');

        Route::get('supplier/assignedCompanies', 'SupplierMasterAPIController@getAssignedCompaniesBySupplier');

        Route::get('allCurrencies', 'CurrencyMasterAPIController@getAllCurrencies');
        Route::get('getAllConversionByCurrency', 'CurrencyMasterAPIController@getAllConversionByCurrency');
        Route::get('supplier/currencies', 'CurrencyMasterAPIController@getCurrenciesBySupplier');

        Route::post('supplier/add/currency', 'CurrencyMasterAPIController@addCurrencyToSupplier');
        Route::post('supplier/update/currency', 'CurrencyMasterAPIController@updateCurrencyToSupplier');
        Route::post('supplier/remove/currency', 'CurrencyMasterAPIController@removeCurrencyToSupplier');
        Route::get('getCompanyLocalCurrency', 'CurrencyMasterAPIController@getCompanyLocalCurrency');
        Route::get('getCompanyReportingCurrency', 'CurrencyMasterAPIController@getCompanyReportingCurrency');
        Route::get('getCompanyReportingCurrencyCode', 'CurrencyMasterAPIController@getCompanyReportingCurrencyCode');
        Route::post('getCompanies', 'CompanyAPIController@getCompanies');
        Route::get('getCompanySettingFormData', 'CompanyAPIController@getCompanySettingFormData');
        
        Route::resource('supplier/assigned', 'SupplierAssignedAPIController');
        Route::get('checkSelectedSupplierIsActive', 'SupplierAssignedAPIController@checkSelectedSupplierIsActive');

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
        Route::get('getBankMemoBySupplierCurrencyId', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrencyId');
        Route::post('addBulkMemos', 'BankMemoSupplierAPIController@addBulkMemos');
        Route::post('exportSupplierCurrencyMemos', 'BankMemoSupplierAPIController@exportSupplierCurrencyMemos');

        Route::resource('bank_memo_supplier_masters', 'BankMemoSupplierMasterAPIController');
        Route::post('deleteBankMemo', 'BankMemoSupplierAPIController@deleteBankMemo');
        Route::post('supplierBankMemoDeleteAll', 'BankMemoSupplierAPIController@supplierBankMemoDeleteAll');
        Route::post('getCurrencyDetails', 'SupplierCurrencyAPIController@getCurrencyDetails');

        Route::resource('item/masters', 'ItemMasterAPIController');
        Route::post('getAllItemsMaster', 'ItemMasterAPIController@getAllItemsMaster');
        Route::post('getAssignedItemsForCompany', 'ItemMasterAPIController@getAssignedItemsForCompany');
        Route::post('getAllAssignedItemsForCompany', 'ItemMasterAPIController@getAllAssignedItemsForCompany');
        Route::post('validateItemAmend', 'ItemMasterAPIController@validateItemAmend');


        Route::get('getAllFixedAssetItems', 'ItemMasterAPIController@getAllFixedAssetItems');
        Route::post('exportItemMaster', 'ItemMasterAPIController@exportItemMaster');
        Route::resource('units', 'UnitAPIController');

        Route::resource('finance_item_category_subs', 'FinanceItemCategorySubAPIController');
        Route::post('finance_item_category_subs_update', 'FinanceItemCategorySubAPIController@finance_item_category_subs_update');
        Route::post('financeItemCategorySubsExpiryUpdate', 'FinanceItemCategorySubAPIController@financeItemCategorySubsExpiryUpdate');
        Route::post('financeItemCategorySubsAttributesUpdate', 'FinanceItemCategorySubAPIController@financeItemCategorySubsAttributesUpdate');

        Route::resource('itemcategory_sub_assigneds', 'FinanceItemcategorySubAssignedAPIController');

        Route::resource('finance_item_category_masters', 'FinanceItemCategoryMasterAPIController');

        Route::resource('item/masters', 'ItemMasterAPIController');
        Route::post('itemMasterBulkCreate', 'ItemMasterAPIController@itemMasterBulkCreate');
        Route::post('itemReferBack', 'ItemMasterAPIController@itemReferBack');
        Route::post('itemReOpen', 'ItemMasterAPIController@itemReOpen');

        Route::resource('reasonCodeMasters', 'ReasonCodeMasterAPIController');
        Route::post('getAllReasonCodeMaster', 'ReasonCodeMasterAPIController@getAllReasonCodeMaster');
        Route::post('updateReasonCodeMaster', 'ReasonCodeMasterAPIController@update');
        Route::get('getAllGLCodesForReasonMaster', 'ReasonCodeMasterAPIController@getAllGLCodes');
        Route::get('reasonCodeMasterRecordSalesReturn/{id}', 'ReasonCodeMasterAPIController@reasonCodeMasterRecordSalesReturn');


        Route::get('getItemMasterFormData', 'ItemMasterAPIController@getItemMasterFormData');
        Route::get('getInventorySubCat', 'ItemMasterAPIController@getInventorySubCat');
        Route::get('getItemSubCategory', 'ItemMasterAPIController@getItemSubCategory');
        
        Route::post('updateItemMaster', 'ItemMasterAPIController@updateItemMaster');
        Route::get('assignedCompaniesByItem', 'ItemMasterAPIController@getAssignedCompaniesByItem');


        Route::resource('example_table_templates', 'ExampleTableTemplateAPIController');
        Route::get('getExampleTableData', 'ExampleTableTemplateAPIController@getExampleTableData');

        Route::resource('item/assigneds', 'ItemAssignedAPIController');
        Route::post('getAllAssignedItemsByCompany', 'ItemAssignedAPIController@getAllAssignedItemsByCompany');
        Route::post('getAllAssignedItemsByWarehouse', 'WarehouseItemsAPIController@getAllAssignedItemsByWarehouse');
        Route::post('exportItemAssignedByWarehouse', 'WarehouseItemsAPIController@exportItemAssignedByWarehouse');
        Route::post('exportItemAssignedByCompanyReport', 'ItemAssignedAPIController@exportItemAssignedByCompanyReport');

        Route::post('reOrderTest', 'ItemAssignedAPIController@reOrderTest');//nee to delete

        Route::get('getItemMasterPurchaseHistory', 'PurchaseOrderDetailsAPIController@getItemMasterPurchaseHistory');

        Route::get('getItemMasterPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@getItemMasterPurchaseRequestHistory');
        Route::get('exportPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@exportPurchaseRequestHistory');

        Route::get('getSubcategoriesBymainCategory', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategory');
        Route::get('getSubcategoryExpiryStatus', 'FinanceItemCategorySubAPIController@getSubcategoryExpiryStatus');
        Route::post('getSubcategoriesBymainCategories', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategories');
        Route::get('exportPurchaseHistory', 'PurchaseOrderDetailsAPIController@exportPurchaseHistory');
        Route::post('validateItemAlllocationInPO', 'PurchaseOrderDetailsAPIController@validateItemAlllocationInPO');

        Route::post('purchase-request-validate-item', 'PurchaseRequestAPIController@validateItem');


        Route::post('allItemFinanceCategories', 'FinanceItemCategoryMasterAPIController@allItemFinanceCategories');
        Route::post('getFinanceItemCategoryMasterExpiryStatus', 'FinanceItemCategoryMasterAPIController@getFinanceItemCategoryMasterExpiryStatus');
        Route::post('getFinanceItemCategoryMasterAttributesStatus', 'FinanceItemCategoryMasterAPIController@getFinanceItemCategoryMasterAttributesStatus');
        Route::post('allItemFinanceSubCategoriesByMainCategory', 'FinanceItemCategoryMasterAPIController@allItemFinanceSubCategoriesByMainCategory');
        Route::get('getSubCategoryFormData', 'FinanceItemCategoryMasterAPIController@getSubCategoryFormData');
        Route::post('getAttributesData', 'FinanceItemCategoryMasterAPIController@getAttributesData');
        Route::get('getDropdownValues', 'FinanceItemCategoryMasterAPIController@getDropdownValues');

        Route::post('addItemAttributes', 'FinanceItemCategoryMasterAPIController@addItemAttributes');



        Route::resource('erp_attributes', 'ErpAttributesAPIController');
        Route::post('itemAttributesIsMandotaryUpdate', 'ErpAttributesAPIController@itemAttributesIsMandotaryUpdate');
        Route::post('itemAttributesDelete', 'ErpAttributesAPIController@itemAttributesDelete');

        Route::resource('erp_attributes_dropdowns', 'ErpAttributesDropdownAPIController');
        Route::post('addDropdownData', 'ErpAttributesDropdownAPIController@addDropdownData');
        Route::post('getDropdownData', 'ErpAttributesDropdownAPIController@getDropdownData');

        Route::resource('erp_attributes_field_types', 'ErpAttributesFieldTypeAPIController');


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
        Route::post('poExpectedDeliveryDateAmend', 'ProcumentOrderAPIController@poExpectedDeliveryDateAmend');
        Route::post('amendProcumentSubWorkOrderReview', 'ProcumentOrderAPIController@amendProcumentSubWorkOrderReview');
        Route::post('generateWorkOrder', 'ProcumentOrderAPIController@generateWorkOrder');
        Route::post('workOrderLog', 'ProcumentOrderAPIController@workOrderLog');

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
        Route::get('getDocumentAccessGroup', 'ApprovalGroupsAPIController@getDocumentAccessGroup');
        Route::post('assignApprovalGroup', 'ApprovalRoleAPIController@assignApprovalGroup');
        Route::get('getApprovalRollByLevel', 'ApprovalRoleAPIController@getApprovalRollByLevel');

        /** Chart of Account Created by Shafri */
        Route::post('chartOfAccount', 'ChartOfAccountAPIController@getChartOfAccount');
        Route::resource('control_accounts', 'ControlAccountAPIController');
        Route::get('getChartOfAccountFormData', 'ChartOfAccountAPIController@getChartOfAccountFormData');
        Route::post('getMasterChartOfAccountData', 'ChartOfAccountAPIController@getMasterChartOfAccountData');
        Route::post('getInterCompanies', 'ChartOfAccountAPIController@getInterCompanies');
        Route::resource('chart_of_account', 'ChartOfAccountAPIController');
        Route::get('isBank/{id}', 'ChartOfAccountAPIController@isBank');
        Route::get('assignedCompaniesByChartOfAccount', 'ChartOfAccountAPIController@assignedCompaniesByChartOfAccount');
        Route::get('getChartOfAccounts', 'ChartOfAccountAPIController@getChartOfAccounts');
        Route::get('getNotAssignedCompaniesByChartOfAccount', 'ChartOfAccountAPIController@getNotAssignedCompaniesByChartOfAccount');
        Route::resource('chart_of_accounts_assigned', 'ChartOfAccountsAssignedAPIController');
        Route::get('getAssignedChartOfAccounts', 'ChartOfAccountsAssignedAPIController@getAssignedChartOfAccounts');


        Route::resource('erp_locations', 'ErpLocationAPIController');
        Route::post('getAllLocation', 'ErpLocationAPIController@getAllLocation');
        Route::post('createLocation', 'ErpLocationAPIController@createLocation');
        Route::post('deleteLocation', 'ErpLocationAPIController@deleteLocation');
        Route::resource('accounts_types', 'AccountsTypeAPIController');


        /** Segment master Created by Nazir  */

        Route::post('getAllSegmentMaster', 'SegmentMasterAPIController@getAllSegmentMaster');
        Route::get('getSegmentMasterFormData', 'SegmentMasterAPIController@getSegmentMasterFormData');
        Route::get('getOrganizationStructure', 'SegmentMasterAPIController@getOrganizationStructure');
        Route::resource('segment/masters', 'SegmentMasterAPIController');

        Route::post('updateSegmentMaster', 'SegmentMasterAPIController@updateSegmentMaster');


        /** Warehouse master Created by Pasan  */
        Route::resource('warehouse/masters', 'WarehouseMasterAPIController');
        Route::get('getWarehouseMasterFormData', 'WarehouseMasterAPIController@getWarehouseMasterFormData');
        Route::post('getAllWarehouseMaster', 'WarehouseMasterAPIController@getAllWarehouseMaster');
        Route::get('getAllWHForSelectedCompany', 'WarehouseMasterAPIController@getAllWarehouseForSelectedCompany');
        Route::post('updateWarehouseMaster', 'WarehouseMasterAPIController@updateWarehouseMaster');

        /** Customer master Created by Fayas  */
        Route::resource('customer_masters', 'CustomerMasterAPIController');
        Route::post('getAllCustomers', 'CustomerMasterAPIController@getAllCustomers');
        Route::post('getInterCompaniesForCustomerSupplier', 'CustomerMasterAPIController@getInterCompaniesForCustomerSupplier');
        Route::post('getAllCustomersByCompany', 'CustomerAssignedAPIController@getAllCustomersByCompany');
        Route::get('getCustomerFormData', 'CustomerMasterAPIController@getCustomerFormData');
        Route::get('getApprovedCustomers', 'CustomerMasterAPIController@getApprovedCustomers');
        Route::get('getLinkedSupplier', 'CustomerMasterAPIController@getLinkedSupplier');
        Route::get('getChartOfAccountsByCompanyForCustomer', 'CustomerMasterAPIController@getChartOfAccountsByCompanyForCustomer');
        Route::get('getCustomerCatgeoryByCompany', 'CustomerMasterAPIController@getCustomerCatgeoryByCompany');
        Route::get('getSelectedCompanyReportingCurrencyData', 'CustomerMasterAPIController@getSelectedCompanyReportingCurrencyData');
        Route::get('getCustomerByCompany', 'CustomerMasterAPIController@getCustomerByCompany');
        Route::get('getAssignedCompaniesByCustomer', 'CustomerMasterAPIController@getAssignedCompaniesByCustomer');
        Route::post('customerReferBack', 'CustomerMasterAPIController@customerReferBack');
        Route::resource('customer_assigneds', 'CustomerAssignedAPIController');
        Route::get('getNotAssignedCompaniesByCustomer', 'CustomerAssignedAPIController@getNotAssignedCompaniesByCustomer');
        Route::post('exportCustomerMaster', 'CustomerMasterAPIController@exportCustomerMaster');
        Route::post('customerReOpen', 'CustomerMasterAPIController@customerReOpen');
        Route::post('validateCustomerAmend', 'CustomerMasterAPIController@validateCustomerAmend');

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
        Route::get('getEligibleMr', 'PurchaseRequestAPIController@getEligibleMr');
        Route::get('getWarehouse', 'PurchaseRequestAPIController@getWarehouse');
        Route::post('createPrMaterialRequest', 'PurchaseRequestAPIController@createPrMaterialRequest');
        Route::get('getPurchaseRequestForPO', 'PurchaseRequestAPIController@getPurchaseRequestForPO');
        Route::post('amendPurchaseRequest', 'PurchaseRequestAPIController@amendPurchaseRequest');
        //confirmation
        Route::post('confirmDocument', 'PurchaseRequestAPIController@confirmDocument');

        Route::resource('procurement-order', 'ProcumentOrderAPIController');
        Route::post('getProcumentOrderByDocumentType', 'ProcumentOrderAPIController@getProcumentOrderByDocumentType');
        Route::get('getProcumentOrderFormData', 'ProcumentOrderAPIController@getProcumentOrderFormData');
        Route::get('segment/projects', 'ProcumentOrderAPIController@getProjectsBySegment');
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
        Route::post('procumentOrderPRAttachment', 'ProcumentOrderAPIController@procumentOrderPRAttachment');
        Route::post('updateSentSupplierDetail', 'ProcumentOrderAPIController@updateSentSupplierDetail');

        Route::resource('item-specification', 'ItemSpecificationController');
        Route::resource('priorities', 'PriorityAPIController');

        Route::resource('locations', 'LocationAPIController');

        Route::resource('yes_no_selection_for_minuses', 'YesNoSelectionForMinusAPIController');

        Route::resource('months', 'MonthsAPIController');
        Route::get('purchase_requests-isPulled', 'PurchaseRequestAPIController@isPulledFromMR');

        Route::resource('company_document_attachments', 'CompanyDocumentAttachmentAPIController');
        Route::resource('purchase_request_details', 'PurchaseRequestDetailsAPIController');
        Route::post('purchase-request/remove-all-items/{id}', 'PurchaseRequestDetailsAPIController@removeAllItems');
        Route::get('getItemsOptionForPurchaseRequest', 'PurchaseRequestAPIController@getItemsOptionForPurchaseRequest');
        Route::get('get-all-uom-options', 'PurchaseRequestAPIController@getAllUomOptions');

        Route::get('getItemsByPurchaseRequest', 'PurchaseRequestDetailsAPIController@getItemsByPurchaseRequest');
        Route::post('mapLineItemPr', 'PurchaseRequestDetailsAPIController@mapLineItemPr');
        Route::get('getPurchaseRequestDetailForPO', 'PurchaseRequestDetailsAPIController@getPurchaseRequestDetailForPO');
        Route::post('delete-item-qnty-by-pr', 'PurchaseRequestAPIController@delteItemQntyPR');

        
        Route::resource('document_approveds', 'DocumentApprovedAPIController');
        Route::resource('company_policy_masters', 'CompanyPolicyMasterAPIController');

        Route::resource('currency_conversions', 'CurrencyConversionAPIController');
        Route::post('updateCrossExchange', 'CurrencyConversionAPIController@updateCrossExchange');
        Route::post('currencyConvert', 'CurrencyConversionAPIController@currencyConvert');

        Route::resource('bank_accounts', 'BankAccountAPIController');
        Route::post('getAllBankAccountByCompany', 'BankAccountAPIController@getAllBankAccountByCompany');


        Route::post('getBankBalance', 'BankAccountAPIController@getBankBalance');
        Route::get('getBankAccountsByBankID', 'BankAccountAPIController@getBankAccountsByBankID');
        Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');

        Route::resource('g_r_v_masters', 'GRVMasterAPIController');

        Route::resource('poPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');
        Route::post('storePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@storePoPaymentTermsLogistic');

        Route::resource('srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController');
        Route::get('get_srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController@geDocumentAttachments');

        Route::resource('document_attachments', 'DocumentAttachmentsAPIController');   
        Route::resource('document_attachment_types', 'DocumentAttachmentTypeAPIController');
        Route::get('downloadFile', 'DocumentAttachmentsAPIController@downloadFile');
        Route::post('store_tender_documents', 'DocumentAttachmentsAPIController@storeTenderDocuments');      

        Route::resource('sme-attachment', 'AttachmentSMEAPIController');
        Route::get('sme-attachment/{id}/{docID}/{companyID}', 'AttachmentSMEAPIController@show');

        Route::post('getAllItemsMasterApproval', 'ItemMasterAPIController@getAllItemsMasterApproval');
        Route::post('getAllSupplierMasterApproval', 'SupplierMasterAPIController@getAllSupplierMasterApproval');
        Route::post('getAllCustomerMasterApproval', 'CustomerMasterAPIController@getAllCustomerMasterApproval');
        Route::post('getAllChartOfAccountApproval', 'ChartOfAccountAPIController@getAllChartOfAccountApproval');
        Route::post('exportChartOfAccounts', 'ChartOfAccountAPIController@exportChartOfAccounts');
        Route::post('chartOfAccountReopen', 'ChartOfAccountAPIController@chartOfAccountReopen');

        Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');
        Route::resource('procumentOrderAdvpaymentUD', 'PoAdvancePaymentAPIController');
        Route::post('updatePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@updatePoPaymentTermsLogistic');
        Route::resource('employees_departments', 'EmployeesDepartmentAPIController');
        Route::post('getApprovalAccessRights', 'EmployeesDepartmentAPIController@getApprovalAccessRightsDatatable');
        Route::post('getApprovalPersonsByRoll', 'EmployeesDepartmentAPIController@getApprovalPersonsByRoll');
        Route::post('updateEmployeeDepartmentActive', 'EmployeesDepartmentAPIController@updateEmployeeDepartmentActive');
        Route::post('mirrorAccessRights', 'EmployeesDepartmentAPIController@mirrorAccessRights');
        Route::get('getApprovalAccessRightsFormData', 'EmployeesDepartmentAPIController@getApprovalAccessRightsFormData');
        Route::get('getDepartmentDocument', 'EmployeesDepartmentAPIController@getDepartmentDocument');
        Route::post('deleteAllAccessRights', 'EmployeesDepartmentAPIController@deleteAllAccessRights');
        Route::post('approvalAccessActiveInactiveAll', 'EmployeesDepartmentAPIController@approvalAccessActiveInactiveAll');
        Route::post('approval/matrix', 'EmployeesDepartmentAPIController@approvalMatrixReport');
        Route::post('approval/matrix/export', 'EmployeesDepartmentAPIController@exportApprovalMatrixReport');
        Route::post('assignEmployeeToApprovalGroup', 'EmployeesDepartmentAPIController@assignEmployeeToApprovalGroup');

        Route::post('approveItem', 'ItemMasterAPIController@approveItem');
        Route::post('rejectItem', 'ItemMasterAPIController@rejectItem');

        Route::post('approveSupplier', 'SupplierMasterAPIController@approveSupplier');
        Route::post('approveRegisteredSupplier', 'SupplierMasterAPIController@approveRegisteredSupplier');
        Route::post('rejectSupplier', 'SupplierMasterAPIController@rejectSupplier');
        Route::post('rejectRegisteredSupplier', 'SupplierMasterAPIController@rejectRegisteredSupplier');

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

        Route::post('validateAMReport', 'AssetManagementReportAPIController@validateReport');
        Route::get('getAssetManagementFilterData', 'AssetManagementReportAPIController@getFilterData');
        Route::post('assetRegisterDrillDown', 'AssetManagementReportAPIController@getAssetRegisterSummaryDrillDownQRY');
        Route::post('exportAssetRegisterSummaryDrillDown', 'AssetManagementReportAPIController@getAssetRegisterSummaryDrillDownExport');
        Route::post('assetCWIPDrillDown', 'AssetManagementReportAPIController@assetCWIPDrillDown');

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
        Route::post('updateAllPaymentTerms', 'PoPaymentTermsAPIController@updateAllPaymentTerms');
        Route::post('procumentOrderCancel', 'ProcumentOrderAPIController@procumentOrderCancel');
        Route::post('procumentOrderReturnBack', 'ProcumentOrderAPIController@procumentOrderReturnBack');
        Route::post('amendProcumentSubWorkOrder', 'ProcumentOrderAPIController@amendProcumentSubWorkOrder');
        Route::post('manualCloseProcurementOrder', 'ProcumentOrderAPIController@manualCloseProcurementOrder');
        Route::post('manualCloseProcurementOrderPrecheck', 'ProcumentOrderAPIController@manualCloseProcurementOrderPrecheck');
        Route::post('procumentOrderSegmentchk', 'ProcumentOrderAPIController@procumentOrderSegmentchk');
        Route::get('ProcurementOrderAudit', 'ProcumentOrderAPIController@ProcurementOrderAudit');
        Route::post('getProcurementOrderReopen', 'ProcumentOrderAPIController@getProcurementOrderReopen');
        Route::post('getProcurementOrderReferBack', 'ProcumentOrderAPIController@getProcurementOrderReferBack');
        Route::get('getPurchasePaymentStatusHistory', 'ProcumentOrderAPIController@getPurchasePaymentStatusHistory');
        Route::get('getAdvancePaymentRequestStatusHistory', 'ProcumentOrderAPIController@getAdvancePaymentRequestStatusHistory');
        Route::post('exportProcumentOrderMaster', 'ProcumentOrderAPIController@exportProcumentOrderMaster');

        Route::get('reportSpentAnalysisBySupplierFilter', 'ProcumentOrderAPIController@reportSpentAnalysisBySupplierFilter');
        Route::post('reportSpentAnalysis', 'ProcumentOrderAPIController@reportSpentAnalysis');
        Route::post('reportSpentAnalysisExport', 'ProcumentOrderAPIController@reportSpentAnalysisExport');
        Route::post('reportSpentAnalysisDrilldownExport', 'ProcumentOrderAPIController@reportSpentAnalysisDrilldownExport');
        Route::post('reportSpentAnalysisHeader', 'ProcumentOrderAPIController@reportSpentAnalysisHeader');
        Route::post('reportPoEmployeePerformance', 'ProcumentOrderAPIController@reportPoEmployeePerformance');
        Route::post('unlinkLogistic', 'PoAdvancePaymentAPIController@unlinkLogistic');

        Route::post('reportPrToGrv', 'PurchaseRequestAPIController@reportPrToGrv');
        Route::post('exportPrToGrvReport', 'PurchaseRequestAPIController@exportPrToGrvReport');

        Route::post('reportPoToPayment', 'ProcumentOrderAPIController@reportPoToPayment');
        Route::post('exportPoToPaymentReport', 'ProcumentOrderAPIController@exportPoToPaymentReport');
        Route::get('reportPoToPaymentFilterOptions', 'ProcumentOrderAPIController@reportPoToPaymentFilterOptions');
        Route::get('getReportSavingFliterData', 'ProcumentOrderAPIController@getReportSavingFliterData');
        Route::get('getDocumentTracingData', 'ProcumentOrderAPIController@getDocumentTracingData');


        Route::get('reportPrToGrvFilterOptions', 'PurchaseRequestAPIController@reportPrToGrvFilterOptions');
        Route::get('getApprovedDetails', 'PurchaseRequestAPIController@getApprovedDetails');
        Route::post('getPurchaseRequestReopen', 'PurchaseRequestAPIController@getPurchaseRequestReopen');
        Route::post('getPurchaseRequestReferBack', 'PurchaseRequestAPIController@getPurchaseRequestReferBack');

        
        Route::post('advancePaymentTermCancel', 'PoAdvancePaymentAPIController@advancePaymentTermCancel');

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

        // Route::resource('tax_formula_mgetAllcompaniesasters', 'TaxFormulaMasterAPIController');
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
        Route::post('exportPoEmployeePerformance', 'ProcumentOrderAPIController@exportPoEmployeePerformance');

        Route::get('getErpLedger', 'ErpItemLedgerAPIController@getErpLedger');
        Route::post('getErpLedgerItems', 'ErpItemLedgerAPIController@getErpLedgerItems');

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
        Route::post('generateStockLedger', 'ErpItemLedgerAPIController@generateStockLedger');        
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
        Route::get('getBinLocationsByWarehouse', 'GRVMasterAPIController@getBinLocationsByWarehouse');
        Route::get('getWarehouse', 'ErpItemLedgerAPIController@getWarehouse');
        Route::post('generateStockValuationReport', 'ErpItemLedgerAPIController@generateStockValuationReport');
        Route::get('getAllFinancePeriod', 'CompanyFinancePeriodAPIController@getAllFinancePeriod');
        Route::get('getAllFinancePeriodBasedFY', 'CompanyFinancePeriodAPIController@getAllFinancePeriodBasedFY');
        Route::get('getAllFinancePeriodForYear', 'CompanyFinancePeriodAPIController@getAllFinancePeriodForYear');
        Route::resource('goodReceiptVoucherCRUD', 'GRVMasterAPIController');
        Route::get('getItemsByGRVMaster', 'GRVDetailsAPIController@getItemsByGRVMaster');
        Route::get('getLogisticsItemsByGRV', 'PoAdvancePaymentAPIController@loadPoPaymentTermsLogisticForGRV');
        Route::post('GRVSegmentChkActive', 'GRVMasterAPIController@GRVSegmentChkActive');
        Route::post('getGoodReceiptVoucherReopen', 'GRVMasterAPIController@getGoodReceiptVoucherReopen');
        Route::get('purchaseOrderForGRV', 'ProcumentOrderAPIController@purchaseOrderForGRV');
        Route::get('purchaseReturnForGRV', 'PurchaseReturnAPIController@purchaseReturnForGRV');
        Route::get('getPurchaseOrderDetailForGRV', 'PurchaseOrderDetailsAPIController@getPurchaseOrderDetailForGRV');
        Route::get('getPurchaseReturnDetailForGRV', 'PurchaseReturnAPIController@getPurchaseReturnDetailForGRV');
        Route::post('storeGRVDetailsFromPO', 'GRVDetailsAPIController@storeGRVDetailsFromPO');
        Route::post('storeGRVDetailsFromPR', 'GRVDetailsAPIController@storeGRVDetailsFromPR');
        Route::resource('purchase_order_details', 'PurchaseOrderDetailsAPIController');
        Route::post('grvDeleteAllDetails', 'GRVDetailsAPIController@grvDeleteAllDetails');
        Route::get('goodReceiptVoucherAudit', 'GRVMasterAPIController@goodReceiptVoucherAudit');
        Route::post('getGoodReceiptVoucherAmend', 'GRVMasterAPIController@getGoodReceiptVoucherAmend');
        Route::resource('materiel_requests', 'MaterielRequestAPIController');
        Route::get('materiel_requests/{id}/purchase-requests', 'MaterielRequestAPIController@checkPurcahseRequestExist');
        Route::post('requestReopen', 'MaterielRequestAPIController@requestReopen');
        Route::post('requestReferBack', 'MaterielRequestAPIController@requestReferBack');
        Route::post('getAllRequestByCompany', 'MaterielRequestAPIController@getAllRequestByCompany');
        Route::get('getRequestFormData', 'MaterielRequestAPIController@getRequestFormData');
        Route::post('getAllNotApprovedRequestByUser', 'MaterielRequestAPIController@getAllNotApprovedRequestByUser');
        Route::post('getApprovedMaterielRequestsByUser', 'MaterielRequestAPIController@getApprovedMaterielRequestsByUser');
        Route::get('materielRequestAudit', 'MaterielRequestAPIController@materielRequestAudit');
        Route::resource('materiel_request_details', 'MaterielRequestDetailsAPIController');
        Route::get('getItemsByMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsByMaterielRequest');
        Route::get('getItemsOptionForMaterielRequest', 'MaterielRequestDetailsAPIController@getItemsOptionForMaterielRequest');
        Route::post('exportStockEvaluation', 'ErpItemLedgerAPIController@exportStockEvaluation');
        Route::post('exportStockLedgerReport', 'ErpItemLedgerAPIController@exportStockLedgerReport');
        Route::post('validateStockValuationReport', 'ErpItemLedgerAPIController@validateStockValuationReport');
        Route::post('validateStockTakingReport', 'ErpItemLedgerAPIController@validateStockTakingReport');
        Route::get('getItemWarehouseQnty', 'MaterielRequestDetailsAPIController@getItemWarehouseQnty');
        Route::get('cancelMaterielRequest', 'MaterielRequestAPIController@cancelMaterielRequest');
        Route::get('update-qnty-by-location', 'MaterielRequestAPIController@updateQntyByLocation');
        Route::get('materiel_request/details/{id}', 'MaterielRequestAPIController@getMaterielRequestDetails');


       

        Route::get('material-issue/update-qnty-by-location', 'ItemIssueMasterAPIController@updateQntyByLocation');
        Route::get('material-issue/check/product/{id}/{companySystemID}', 'ItemIssueMasterAPIController@checkProductExistInIssues');
        Route::get('purchase_requests/check/product/{itemCode}/{companySystemID}', 'PurchaseRequestAPIController@checkProductExistInIssues');
        Route::post('get-item-qnty-by-pr', 'PurchaseRequestAPIController@getItemQntyByPR');


        Route::resource('item_issue_details', 'ItemIssueDetailsAPIController');

        Route::resource('item_issue_masters', 'ItemIssueMasterAPIController');
        Route::post('getAllMaterielIssuesByCompany', 'ItemIssueMasterAPIController@getAllMaterielIssuesByCompany');
        Route::post('getMaterielIssueApprovedByUser', 'ItemIssueMasterAPIController@getMaterielIssueApprovedByUser');
        Route::post('getMaterielIssueApprovalByUser', 'ItemIssueMasterAPIController@getMaterielIssueApprovalByUser');
        Route::get('getMaterielIssueFormData', 'ItemIssueMasterAPIController@getMaterielIssueFormData');
        Route::get('allMaterielRequestNotSelectedForIssue', 'ItemIssueMasterAPIController@getAllMaterielRequestNotSelectedForIssueByCompany');
        Route::get('getMaterielIssueAudit', 'ItemIssueMasterAPIController@getMaterielIssueAudit');
        Route::post('materielIssueReopen', 'ItemIssueMasterAPIController@materielIssueReopen');
        Route::post('materielIssueReferBack', 'ItemIssueMasterAPIController@materielIssueReferBack');
        Route::get('getItemsByMaterielIssue', 'ItemIssueDetailsAPIController@getItemsByMaterielIssue');
        Route::get('getItemsOptionsMaterielIssue', 'ItemIssueDetailsAPIController@getItemsOptionsMaterielIssue');
        Route::post('getGRVMasterApproval', 'GRVMasterAPIController@getGRVMasterApproval');
        Route::post('getApprovedGRVForCurrentUser', 'GRVMasterAPIController@getApprovedGRVForCurrentUser');
        Route::post('approveGoodReceiptVoucher', 'GRVMasterAPIController@approveGoodReceiptVoucher');
        Route::post('rejectGoodReceiptVoucher', 'GRVMasterAPIController@rejectGoodReceiptVoucher');
        Route::resource('general_ledgers', 'GeneralLedgerAPIController');
        Route::get('getGeneralLedgerReview', 'GeneralLedgerAPIController@getGeneralLedgerReview');
        Route::resource('item_issue_types', 'ItemIssueTypeAPIController');
        Route::get('getSearchCustomerByCompany', 'CustomerMasterAPIController@getSearchCustomerByCompany');
        Route::post('generateStockTakingReport', 'ErpItemLedgerAPIController@generateStockTakingReport');
        Route::post('exportStockTaking', 'ErpItemLedgerAPIController@exportStockTaking');

        Route::get('material-issue-by-refno', 'ItemIssueMasterAPIController@getMaterialIssueByRefNo');
        Route::post('getItemStockDetails', 'ErpItemLedgerAPIController@getItemStockDetails');
        

        Route::resource('accounts_payable_ledgers', 'AccountsPayableLedgerAPIController');
        Route::get('getAPFilterData', 'AccountsPayableReportAPIController@getAPFilterData');
        Route::post('validateAPReport', 'AccountsPayableReportAPIController@validateAPReport');
        Route::post('generateAPReport', 'AccountsPayableReportAPIController@generateAPReport');
        Route::post('exportAPReport', 'AccountsPayableReportAPIController@exportReport');

        Route::post('exportNavigationeport', 'UserGroupAssignAPIController@exportNavigationeport');

        Route::get('getFRFilterData', 'FinancialReportAPIController@getFRFilterData');
        Route::get('getAFRFilterChartOfAccounts', 'FinancialReportAPIController@getAFRFilterChartOfAccounts');
        Route::post('validateFRReport', 'FinancialReportAPIController@validateFRReport');
        Route::post('validatePUReport', 'FinancialReportAPIController@validatePUReport');
        Route::post('generateFRReport', 'FinancialReportAPIController@generateFRReport');
        Route::post('generateprojectUtilizationReport', 'FinancialReportAPIController@generateprojectUtilizationReport');

        Route::post('generateEmployeeLedgerReport', 'FinancialReportAPIController@generateEmployeeLedgerReport');

        Route::post('exportFinanceReport', 'FinancialReportAPIController@exportFinanceReport');
        Route::post('getTBUnmatchedData', 'FinancialReportAPIController@getTBUnmatchedData');
        Route::post('exportFRReport', 'FinancialReportAPIController@exportReport');
        Route::post('downloadProjectUtilizationReport', 'FinancialReportAPIController@downloadProjectUtilizationReport');
        Route::post('downloadEmployeeLedgerReport', 'FinancialReportAPIController@downloadEmployeeLedgerReport');

        Route::post('reportTemplateGLDrillDown', 'FinancialReportAPIController@reportTemplateGLDrillDown');
        Route::post('reportTemplateGLDrillDownExport', 'FinancialReportAPIController@reportTemplateGLDrillDownExport');

        Route::post('getAllStockTransferByCompany', 'StockTransferAPIController@getStockTransferMasterView');
        Route::get('getStockTransferFormData', 'StockTransferAPIController@getStockTransferFormData');
        Route::post('stockTransferReopen', 'StockTransferAPIController@stockTransferReopen');
        Route::get('getStockTransferDetails', 'StockTransferDetailsAPIController@getStockTransferDetails');
        Route::get('getItemsOptionForStockTransfer', 'StockTransferAPIController@getItemsOptionForStockTransfer');
        Route::resource('stock_transfer_details', 'StockTransferDetailsAPIController');
        Route::resource('stock_transfers', 'StockTransferAPIController');
        Route::get('StockTransferAudit', 'StockTransferAPIController@StockTransferAudit');
        Route::post('getStockTransferApproval', 'StockTransferAPIController@getStockTransferApproval');
        Route::post('getApprovedSTForCurrentUser', 'StockTransferAPIController@getApprovedSTForCurrentUser');
        Route::post('approveStockTransfer', 'StockTransferAPIController@approveStockTransfer');
        Route::post('rejectStockTransfer', 'StockTransferAPIController@rejectStockTransfer');
        Route::post('stockTransferReferBack', 'StockTransferAPIController@stockTransferReferBack');


        Route::resource('item_return_details', 'ItemReturnDetailsAPIController');
        Route::resource('item_return_masters', 'ItemReturnMasterAPIController');
        Route::post('getAllMaterielReturnByCompany', 'ItemReturnMasterAPIController@getAllMaterielReturnByCompany');
        Route::post('getMaterielReturnApprovedByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovedByUser');
        Route::post('getMaterielReturnApprovalByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovalByUser');
        Route::get('getMaterielReturnFormData', 'ItemReturnMasterAPIController@getMaterielReturnFormData');
        Route::get('getItemsByMaterielReturn', 'ItemReturnDetailsAPIController@getItemsByMaterielReturn');
        Route::get('getItemsOptionsMaterielReturn', 'ItemReturnDetailsAPIController@getItemsOptionsMaterielReturn');
        Route::get('getMaterielReturnAudit', 'ItemReturnMasterAPIController@getMaterielReturnAudit');
        Route::post('materielReturnReopen', 'ItemReturnMasterAPIController@materielReturnReopen');
        Route::post('materielReturnReferBack', 'ItemReturnMasterAPIController@materielReturnReferBack');
        Route::post('getMaterielReturnApprovalByUser', 'ItemReturnMasterAPIController@getMaterielReturnApprovalByUser');


        Route::get('getSupplierMasterAudit', 'SupplierMasterAPIController@getSupplierMasterAudit');
        Route::get('getItemMasterAudit', 'ItemMasterAPIController@getItemMasterAudit');

        Route::resource('po_addons', 'PoAddonsAPIController');
        Route::resource('addon_cost_categories', 'AddonCostCategoriesAPIController');
        Route::get('getProcumentOrderAddons', 'PoAddonsAPIController@getProcumentOrderAddons');
        Route::post('getLogisticCategories', 'AddonCostCategoriesAPIController@getLogisticCategories');
        Route::get('getItemsOptionForLogistic', 'AddonCostCategoriesAPIController@getItemsOptionForLogistic');

        Route::resource('stock_receives', 'StockReceiveAPIController');
        Route::post('stockReceiveReferBack', 'StockReceiveAPIController@stockReceiveReferBack');
        Route::post('getStockReceiveApproval', 'StockReceiveAPIController@getStockReceiveApproval');
        Route::post('getApprovedSRForCurrentUser', 'StockReceiveAPIController@getApprovedSRForCurrentUser');
        Route::post('getAllStockReceiveByCompany', 'StockReceiveAPIController@getAllStockReceiveByCompany');
        Route::post('stockReceiveReopen', 'StockReceiveAPIController@stockReceiveReopen');
        Route::get('getStockReceiveFormData', 'StockReceiveAPIController@getStockReceiveFormData');
        Route::get('stockReceiveAudit', 'StockReceiveAPIController@stockReceiveAudit');
        Route::resource('stock_receive_details', 'StockReceiveDetailsAPIController');
        Route::post('storeReceiveDetailsFromTransfer', 'StockReceiveDetailsAPIController@storeReceiveDetailsFromTransfer');
        Route::get('getStockReceiveDetailsByMaster', 'StockReceiveDetailsAPIController@getStockReceiveDetailsByMaster');
        Route::post('srPullFromTransferPreCheck', 'StockReceiveAPIController@srPullFromTransferPreCheck');
        Route::get('getStockTransferForReceive', 'StockTransferAPIController@getStockTransferForReceive');
        Route::get('getStockTransferDetailsByMaster', 'StockTransferAPIController@getStockTransferDetailsByMaster');


        Route::get('getCurrentUserInfo', 'UserAPIController@getCurrentUserInfo');
        Route::get('getImageByPath', 'DocumentAttachmentsAPIController@getImageByPath');

        Route::resource('poMaster_reffered_histories', 'PurchaseOrderMasterRefferedHistoryAPIController');
        Route::resource('poDetails_reffered_histories', 'PurchaseOrderDetailsRefferedHistoryAPIController');
        Route::resource('poAdv_payment_refferedbacks', 'PurchaseOrderAdvPaymentRefferedbackAPIController');
        Route::resource('po_payment_terms_refferedbacks', 'PoPaymentTermsRefferedbackAPIController');
        Route::resource('document_refered_histories', 'DocumentReferedHistoryAPIController');
        Route::post('getPoMasterAmendHistory', 'PurchaseOrderMasterRefferedHistoryAPIController@getPoMasterAmendHistory');
        Route::get('getPoItemsForAmendHistory', 'PurchaseOrderDetailsRefferedHistoryAPIController@getPoItemsForAmendHistory');
        Route::get('getPoLogisticsItemsForAmendHistory', 'PurchaseOrderAdvPaymentRefferedbackAPIController@getPoLogisticsItemsForAmendHistory');
        Route::get('getPoPaymentTermsForAmendHistory', 'PoPaymentTermsRefferedbackAPIController@getPoPaymentTermsForAmendHistory');
        Route::resource('poAddonsReffered_backs', 'PoAddonsRefferedBackAPIController');
        Route::get('getPoAddonsForAmendHistory', 'PoAddonsRefferedBackAPIController@getPoAddonsForAmendHistory');

        Route::resource('asset_finance_categories', 'AssetFinanceCategoryAPIController');
        Route::post('getAllAssetFinanceCategory', 'AssetFinanceCategoryAPIController@getAllAssetFinanceCategory');
        Route::get('getAssetFinanceCategoryFormData', 'AssetFinanceCategoryAPIController@getAssetFinanceCategoryFormData');
        Route::resource('years', 'YearAPIController');
        Route::resource('unbilled_grv_group_bies', 'UnbilledGrvGroupByAPIController');
        Route::resource('employee_profiles', 'EmployeeProfileAPIController');

        Route::post('pullPOAttachment', 'GRVMasterAPIController@pullPOAttachment');


        Route::resource('employee_details', 'EmployeeDetailsAPIController');
        Route::resource('designations', 'DesignationAPIController');

        Route::resource('purchase_returns', 'PurchaseReturnAPIController');
        Route::post('getPurchaseReturnByCompany', 'PurchaseReturnAPIController@getPurchaseReturnByCompany');
        Route::post('getPurchaseReturnAmendHistory', 'PurchaseReturnMasterRefferedBackAPIController@getPurchaseReturnAmendHistory');
        Route::post('purchaseReturnAmend', 'PurchaseReturnAPIController@purchaseReturnAmend');
        Route::get('getPurchaseReturnAudit', 'PurchaseReturnAPIController@getPurchaseReturnAudit');
        Route::post('getPurchaseReturnApprovalByUser', 'PurchaseReturnAPIController@getPurchaseReturnApprovalByUser');
        Route::post('purchaseReturnReopen', 'PurchaseReturnAPIController@purchaseReturnReopen');
        Route::post('getPurchaseReturnApprovedByUser', 'PurchaseReturnAPIController@getPurchaseReturnApprovedByUser');
        Route::get('grvForPurchaseReturn', 'PurchaseReturnAPIController@grvForPurchaseReturn');
        Route::get('grvDetailByMasterForPurchaseReturn', 'PurchaseReturnAPIController@grvDetailByMasterForPurchaseReturn');
        Route::get('getPurchaseReturnFormData', 'PurchaseReturnAPIController@getPurchaseReturnFormData');
        Route::post('purchaseReturnSegmentChkActive', 'PurchaseReturnAPIController@purchaseReturnSegmentChkActive');
        Route::resource('purchase_return_details', 'PurchaseReturnDetailsAPIController');
        Route::get('getItemsByPurchaseReturnMaster', 'PurchaseReturnDetailsAPIController@getItemsByPurchaseReturnMaster');
        Route::get('grvReturnDetails', 'PurchaseReturnDetailsAPIController@grvReturnDetails');
        Route::post('storePurchaseReturnDetailsFromGRV', 'PurchaseReturnDetailsAPIController@storePurchaseReturnDetailsFromGRV');
        Route::post('purchaseReturnDeleteAllDetails', 'PurchaseReturnDetailsAPIController@purchaseReturnDeleteAllDetails');

        Route::resource('purchaseRequestReferreds', 'PurchaseRequestReferredAPIController');
        Route::resource('prDetailsReferedHistories', 'PrDetailsReferedHistoryAPIController');

        Route::post('getPrMasterAmendHistory', 'PurchaseRequestReferredAPIController@getPrMasterAmendHistory');

        Route::resource('contracts', 'ContractAPIController');
        Route::get('getPrItemsForAmendHistory', 'PrDetailsReferedHistoryAPIController@getPrItemsForAmendHistory');
        Route::resource('customer_invoice_direct_details', 'CustomerInvoiceDirectDetailAPIController');

        Route::get('getINVFilterData', 'InventoryReportAPIController@getInventoryFilterData');
        Route::get('getScrapFilterData', 'InventoryReportAPIController@getScarpInventoryFilterData');
        Route::post('validateINVReport', 'InventoryReportAPIController@validateReport');

        Route::post('generateScrapReport', 'InventoryReportAPIController@generateScrapReport');

        Route::post('generateINVReport', 'InventoryReportAPIController@generateReport');
        Route::post('exportINVReport', 'InventoryReportAPIController@exportReport');

        Route::post('getAllDocumentApproval', 'DocumentApprovedAPIController@getAllDocumentApproval');
        Route::post('getAllDocumentApprovalTest', 'DocumentApprovedAPIController@getAllDocumentApproval');
        Route::post('approvalPreCheckAllDoc', 'DocumentApprovedAPIController@approvalPreCheckAllDoc');

        //Route::get('getTotalCountOfApproval', 'DocumentApprovedAPIController@getTotalCountOfApproval');

        // Supplier Invoice
        Route::resource('supplierInvoiceCRUD', 'BookInvSuppMasterAPIController',['only' => ['store', 'show', 'update']]);
        Route::resource('book_inv_supp_dets', 'BookInvSuppDetAPIController', ['except' => ['index','store']]);
        Route::resource('direct_invoice_details', 'DirectInvoiceDetailsAPIController', ['except' => ['index']]);
        Route::get('getInvoiceMasterRecord', 'BookInvSuppMasterAPIController@getInvoiceMasterRecord');
        Route::put('book_inv_supp_local_update/{id}', 'BookInvSuppMasterAPIController@updateLocalER');
        Route::put('book_inv_supp_reporting_update/{id}', 'BookInvSuppMasterAPIController@updateReportingER');
        Route::put('supplierInvoiceUpdateCurrency/{id}', 'BookInvSuppMasterAPIController@updateCurrency');


        // Payment Voucher
        Route::resource('pay_supplier_invoice_masters', 'PaySupplierInvoiceMasterAPIController', ['only' => ['store', 'show', 'update']]);
        Route::resource('pay_supplier_invoice_details', 'PaySupplierInvoiceDetailAPIController',['except' => ['index','store']]);
        Route::resource('direct_payment_details', 'DirectPaymentDetailsAPIController',['except' => ['index']]);
        Route::resource('advance_payment_details', 'AdvancePaymentDetailsAPIController',['except' => ['index','store']]);
        Route::post('addPVDetailsByInterCompany', 'DirectPaymentDetailsAPIController@addPVDetailsByInterCompany');
        Route::post('pv-md-deduction-type', 'DirectPaymentDetailsAPIController@updat_monthly_deduction');
        Route::post('generatePdcForPv', 'PaySupplierInvoiceMasterAPIController@generatePdcForPv');
        Route::post('updateBankBalance', 'PaySupplierInvoiceMasterAPIController@updateBankBalance');
        Route::put('paymentVoucherUpdateCurrency/{id}', 'PaySupplierInvoiceMasterAPIController@updateCurrency');
        Route::get('getRetentionValues', 'PaySupplierInvoiceMasterAPIController@getRetentionValues');



        Route::post('addPOPaymentDetail', 'PaySupplierInvoiceDetailAPIController@addPOPaymentDetail');
        Route::post('deleteAllPOPaymentDetail', 'PaySupplierInvoiceDetailAPIController@deleteAllPOPaymentDetail');
        Route::post('referBackPaymentVoucher', 'PaySupplierInvoiceMasterAPIController@referBackPaymentVoucher');
        Route::get('getPOPaymentDetails', 'PaySupplierInvoiceDetailAPIController@getPOPaymentDetails');
        Route::get('getMatchingPaymentDetails', 'PaySupplierInvoiceDetailAPIController@getMatchingPaymentDetails');
        Route::get('getADVPaymentDetails', 'AdvancePaymentDetailsAPIController@getADVPaymentDetails');
        Route::get('getMatchingADVPaymentDetails', 'AdvancePaymentDetailsAPIController@getMatchingADVPaymentDetails');
        Route::get('getDirectPaymentDetails', 'DirectPaymentDetailsAPIController@getDirectPaymentDetails');
        Route::post('deleteAllDirectPayment', 'DirectPaymentDetailsAPIController@deleteAllDirectPayment');
        Route::post('updateDirectPaymentAccount', 'DirectPaymentDetailsAPIController@updateDirectPaymentAccount');
        Route::get('getDPExchangeRateAmount', 'DirectPaymentDetailsAPIController@getDPExchangeRateAmount');
        Route::post('addADVPaymentDetail', 'AdvancePaymentDetailsAPIController@addADVPaymentDetail');
        Route::post('addADVPaymentDetailForDirectPay', 'AdvancePaymentDetailsAPIController@addADVPaymentDetailForDirectPay');
        Route::post('deleteAllADVPaymentDetail', 'AdvancePaymentDetailsAPIController@deleteAllADVPaymentDetail');
        Route::post('deleteMatchingAllADVPaymentDetail', 'AdvancePaymentDetailsAPIController@deleteMatchingAllADVPaymentDetail');
        Route::post('deleteMatchingADVPaymentItem', 'AdvancePaymentDetailsAPIController@deleteMatchingADVPaymentItem');
        Route::get('getADVPaymentForPV', 'PaySupplierInvoiceMasterAPIController@getADVPaymentForPV');
        Route::get('getADVPaymentForMatchingDocument', 'PaySupplierInvoiceMasterAPIController@getADVPaymentForMatchingDocument');
        Route::post('paymentVoucherReopen', 'PaySupplierInvoiceMasterAPIController@paymentVoucherReopen');
        Route::post('getPaymentApprovalByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovalByUser');
        Route::post('getPaymentApprovedByUser', 'PaySupplierInvoiceMasterAPIController@getPaymentApprovedByUser');
        Route::get('getPaymentVoucherMaster', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherMaster');
        Route::post('checkPVDocumentActive', 'PaySupplierInvoiceMasterAPIController@checkPVDocumentActive');
        Route::get('getPOPaymentForPV', 'PaySupplierInvoiceMasterAPIController@getPOPaymentForPV');
        Route::post('getPaymentVoucherPendingAmountDetails', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherPendingAmountDetails');

        
        Route::get('getBankAccount', 'PaySupplierInvoiceMasterAPIController@getBankAccount');
        Route::post('getMultipleAccountsByBank', 'PaySupplierInvoiceMasterAPIController@getMultipleAccountsByBank');
        Route::post('getAllPaymentVoucherByCompany', 'PaySupplierInvoiceMasterAPIController@getAllPaymentVoucherByCompany');
        Route::get('getPaymentVoucherFormData', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherFormData');
        Route::post('amendPaymentVoucherReview', 'PaySupplierInvoiceMasterAPIController@amendPaymentVoucherReview');
        Route::get('amendPaymentVoucherPreCheck', 'PaySupplierInvoiceMasterAPIController@amendPaymentVoucherPreCheck');
        Route::get('getAllApprovalDocuments', 'DocumentMasterAPIController@getAllApprovalDocuments');
        Route::get('customerInvoiceDetails', 'CustomerInvoiceDirectAPIController@customerInvoiceDetails');
        Route::put('custItemDetailUpdate/{id}', 'CustomerInvoiceItemDetailsAPIController@custItemDetailUpdate');
        Route::post('getAllInvReclassificationByCompany', 'InventoryReclassificationAPIController@getAllInvReclassificationByCompany');
        Route::get('getInvReclassificationFormData', 'InventoryReclassificationAPIController@getInvReclassificationFormData');
        Route::get('getINVFormData', 'CustomerInvoiceDirectAPIController@getINVFormData');
        Route::post('getCustomerInvoiceMasterView', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceMasterView');
        Route::get('getcreateINVFormData', 'CustomerInvoiceDirectAPIController@getcreateINVFormData');
        Route::post('getCustomerInvoicePerformaDetails', 'CustomerInvoiceDirectAPIController@getCustomerInvoicePerformaDetails');
        Route::get('getContractByCustomer', 'CustomerMasterAPIController@getContractByCustomer');
        Route::get('getJobsByContractAndCustomer', 'CustomerMasterAPIController@getJobsByContractAndCustomer');
        Route::post('saveCustomerinvoicePerforma', 'CustomerInvoiceDirectAPIController@saveCustomerinvoicePerforma');
        Route::post('customerInvoiceTaxDetail', 'TaxdetailAPIController@customerInvoiceTaxDetail');
        Route::post('savecustomerInvoiceTaxDetails', 'CustomerInvoiceDirectAPIController@savecustomerInvoiceTaxDetails');
        Route::post('updateCustomerInvoiceGRV', 'CustomerInvoiceDirectAPIController@updateCustomerInvoiceGRV');
        Route::post('addADVPaymentDetailNotLinkPo', 'AdvancePaymentDetailsAPIController@addADVPaymentDetailNotLinkPo');


        Route::resource('performa_details', 'PerformaDetailsAPIController');
        Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');
        Route::resource('ticket_masters', 'TicketMasterAPIController');
        Route::resource('field_masters', 'FieldMasterAPIController');
        Route::resource('taxdetails', 'TaxdetailAPIController');
        Route::resource('inv_reclassification_details', 'InventoryReclassificationDetailAPIController');
        Route::resource('inv_reclassifications', 'InventoryReclassificationAPIController');
        Route::get('getInvReclassificationAudit', 'InventoryReclassificationAPIController@getInvReclassificationAudit');
        Route::post('getInvReclassificationApprovedByUser', 'InventoryReclassificationAPIController@getInvReclassificationApprovedByUser');
        Route::post('getInvReclassificationApprovalByUser', 'InventoryReclassificationAPIController@getInvReclassificationApprovalByUser');
        Route::get('getItemsOptionForReclassification', 'InventoryReclassificationAPIController@getItemsOptionForReclassification');
        Route::get('getItemsByReclassification', 'InventoryReclassificationDetailAPIController@getItemsByReclassification');

        Route::post('invRecalssificationReopen', 'InventoryReclassificationAPIController@invRecalssificationReopen');

        Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');
        Route::resource('customer_invoice_directs', 'CustomerInvoiceDirectAPIController');
        Route::put('customerInvoiceCurrencyUpdate/{id}', 'CustomerInvoiceDirectAPIController@updateCurrency');

        Route::resource('performa_details', 'PerformaDetailsAPIController');

        Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');

        Route::resource('ticket_masters', 'TicketMasterAPIController');

        Route::resource('field_masters', 'FieldMasterAPIController');

        Route::resource('taxdetails', 'TaxdetailAPIController');

        Route::resource('inv_reclassification_details', 'InventoryReclassificationDetailAPIController');

        Route::resource('inv_reclassifications', 'InventoryReclassificationAPIController');

        Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');
        Route::get('getDebitNoteMasterRecord', 'DebitNoteAPIController@getDebitNoteMasterRecord');
        Route::resource('debit_notes', 'DebitNoteAPIController');
        Route::put('debitNoteUpdateCurrency/{id}', 'DebitNoteAPIController@updateCurrency');
        Route::put('updateDebiteNoteType/{id}', 'DebitNoteAPIController@updateDebiteNoteType');
        Route::post('getAllDebitNotes', 'DebitNoteAPIController@getAllDebitNotes');
        Route::post('exportDebitNotesByCompany', 'DebitNoteAPIController@exportDebitNotesByCompany');
        Route::post('getDebitNoteApprovedByUser', 'DebitNoteAPIController@getDebitNoteApprovedByUser');
        Route::post('getDebitNoteApprovalByUser', 'DebitNoteAPIController@getDebitNoteApprovalByUser');
        Route::post('debitNoteReopen', 'DebitNoteAPIController@debitNoteReopen');
        Route::get('getDebitNoteFormData', 'DebitNoteAPIController@getDebitNoteFormData');
        Route::resource('debit_note_details', 'DebitNoteDetailsAPIController');
        Route::get('getDetailsByDebitNote', 'DebitNoteDetailsAPIController@getDetailsByDebitNote');
        Route::get('getDebitNotePaymentStatusHistory', 'DebitNoteAPIController@getDebitNotePaymentStatusHistory');
        Route::post('amendDebitNote', 'DebitNoteAPIController@amendDebitNote');
        Route::post('amendDebitNoteReview', 'DebitNoteAPIController@amendDebitNoteReview');
        Route::post('approvalPreCheckDebitNote', 'DebitNoteAPIController@approvalPreCheckDebitNote');
        Route::post('checkPaymentStatusDNPrint', 'DebitNoteAPIController@checkPaymentStatusDNPrint');

        Route::resource('performa_masters', 'PerformaMasterAPIController');
        Route::resource('rig_masters', 'RigMasterAPIController');

        Route::get('AllDeleteCustomerInvoiceDetails', 'CustomerInvoiceDirectAPIController@AllDeleteCustomerInvoiceDetails');
        Route::post('getInvoiceMasterView', 'BookInvSuppMasterAPIController@getInvoiceMasterView');
        Route::get('getInvoiceMasterFormData', 'BookInvSuppMasterAPIController@getInvoiceMasterFormData');
        Route::get('getInvoiceSupplierTypeBase', 'BookInvSuppMasterAPIController@getInvoiceSupplierTypeBase');

        Route::resource('stock_adjustments', 'StockAdjustmentAPIController');
        Route::post('stockAdjustmentReopen', 'StockAdjustmentAPIController@stockAdjustmentReopen');
        Route::resource('stock_adjustment_details', 'StockAdjustmentDetailsAPIController');

        Route::post('getAllStockAdjustmentsByCompany', 'StockAdjustmentAPIController@getAllStockAdjustmentsByCompany');

        Route::post('getStockAdjustmentApprovedByUser', 'StockAdjustmentAPIController@getStockAdjustmentApprovedByUser');
        Route::post('getStockAdjustmentApprovalByUser', 'StockAdjustmentAPIController@getStockAdjustmentApprovalByUser');

        Route::get('getStockAdjustmentFormData', 'StockAdjustmentAPIController@getStockAdjustmentFormData');
        Route::get('getStockAdjustmentAudit', 'StockAdjustmentAPIController@getStockAdjustmentAudit');
        Route::get('getItemsByStockAdjustment', 'StockAdjustmentDetailsAPIController@getItemsByStockAdjustment');
        Route::get('getItemsOptionsStockAdjustment', 'StockAdjustmentDetailsAPIController@getItemsOptionsStockAdjustment');
        Route::post('stockAdjustmentReferBack', 'StockAdjustmentAPIController@stockAdjustmentReferBack');

        Route::post('customerInvoiceReopen', 'CustomerInvoiceDirectAPIController@customerInvoiceReopen');
        Route::post('clearCustomerInvoiceNumber', 'CustomerInvoiceDirectAPIController@clearCustomerInvoiceNumber');
        Route::get('getItemsOptionForGRV', 'GRVMasterAPIController@getItemsOptionForGRV');
        Route::post('storeGRVDetailsDirect', 'GRVDetailsAPIController@storeGRVDetailsDirect');
        Route::post('updateGRVDetailsDirect', 'GRVDetailsAPIController@updateGRVDetailsDirect');
        Route::get('getDirectInvoiceGL', 'ChartOfAccountsAssignedAPIController@getDirectInvoiceGL');
        Route::get('gl-code-search', 'ChartOfAccountsAssignedAPIController@gl_code_search');
        Route::get('getCompanyWiseSubLedgerAccounts', 'ChartOfAccountsAssignedAPIController@getCompanyWiseSubLedgerAccounts');
        Route::get('getGLForJournalVoucherDirect', 'ChartOfAccountsAssignedAPIController@getGLForJournalVoucherDirect');


        Route::post('getglDetails','ChartOfAccountsAssignedAPIController@getglDetails');
        Route::post('erp_project_masters/get_gl_accounts','ChartOfAccountsAssignedAPIController@getGlAccounts');
        Route::resource('project_gl_details', 'ProjectGlDetailAPIController');
        
        //Logistic Configuration Master
        Route::get('getAllcountry', 'CountryMasterAPIController@index');
        Route::resource('port_masters', 'PortMasterAPIController');
        Route::post('createPort', 'PortMasterAPIController@store');
        Route::post('getAllPort', 'PortMasterAPIController@getAllPort');
        Route::post('deletePort', 'PortMasterAPIController@deletePort');
        Route::resource('delivery_terms_masters', 'DeliveryTermsMasterAPIController');
        Route::post('createDeliveryTerms', 'DeliveryTermsMasterAPIController@store');
        Route::post('getAllDeliveryTerms', 'DeliveryTermsMasterAPIController@getAllDeliveryTerms');
        Route::post('deleteDeliveryTerms', 'DeliveryTermsMasterAPIController@deleteDeliveryTerms');
        

        Route::get('getPaymentVoucherGL', 'ChartOfAccountsAssignedAPIController@getPaymentVoucherGL');
        Route::get('getAllcontractbyclient', 'CustomerInvoiceDirectAPIController@getAllcontractbyclient');
        Route::post('addDirectInvoiceDetails', 'CustomerInvoiceDirectDetailAPIController@addDirectInvoiceDetails');
        Route::get('customerInvoiceAudit', 'CustomerInvoiceDirectAPIController@customerInvoiceAudit');

        Route::put('customerInvoiceLocalUpdate/{id}', 'CustomerInvoiceDirectAPIController@customerInvoiceLocalUpdate');
        Route::put('customerInvoiceReportingUpdate/{id}', 'CustomerInvoiceDirectAPIController@customerInvoiceReportingUpdate');
        Route::post('updateDirectInvoice', 'CustomerInvoiceDirectDetailAPIController@updateDirectInvoice');
        Route::get('getCreditNoteMasterRecord', 'CreditNoteAPIController@getCreditNoteMasterRecord');
        Route::get('getFilteredGRV', 'GRVMasterAPIController@getFilteredGRV');
        Route::get('getDirectItems', 'DirectInvoiceDetailsAPIController@getDirectItems');
        Route::post('getInvoiceMasterApproval', 'BookInvSuppMasterAPIController@getInvoiceMasterApproval');
        Route::post('supplierInvoiceReopen', 'BookInvSuppMasterAPIController@supplierInvoiceReopen');
        Route::post('getApprovedInvoiceForCurrentUser', 'BookInvSuppMasterAPIController@getApprovedInvoiceForCurrentUser');
        Route::post('approveSupplierInvoice', 'BookInvSuppMasterAPIController@approveSupplierInvoice');
        Route::post('rejectSupplierInvoice', 'BookInvSuppMasterAPIController@rejectSupplierInvoice');
        Route::post('saveSupplierInvoiceTaxDetails', 'BookInvSuppMasterAPIController@saveSupplierInvoiceTaxDetails');
        Route::get('supplierInvoiceTaxTotal', 'BookInvSuppMasterAPIController@supplierInvoiceTaxTotal');
        Route::post('clearSupplierInvoiceNo', 'BookInvSuppMasterAPIController@clearSupplierInvoiceNo');
        Route::get('getCreditNoteViewFormData', 'CreditNoteAPIController@getCreditNoteViewFormData');
        Route::post('creditNoteMasterDataTable', 'CreditNoteAPIController@creditNoteMasterDataTable');
        Route::post('addcreditNoteDetails', 'CreditNoteDetailsAPIController@addcreditNoteDetails');
        Route::get('creditNoteDetails', 'CreditNoteDetailsAPIController@creditNoteDetails');
        Route::get('getAllcontractbyclientbase', 'CreditNoteDetailsAPIController@getAllcontractbyclientbase');
        Route::post('updateCreditNote', 'CreditNoteDetailsAPIController@updateCreditNote');
        Route::post('creditNoteReopen', 'CreditNoteAPIController@creditNoteReopen');
        Route::get('creditNoteAudit', 'CreditNoteAPIController@creditNoteAudit');
        Route::post('getCreditNoteApprovedByUser', 'CreditNoteAPIController@getCreditNoteApprovedByUser');
        Route::post('getCreditNoteApprovalByUser', 'CreditNoteAPIController@getCreditNoteApprovalByUser');
        Route::get('getPurchaseOrderForSI', 'UnbilledGrvGroupByAPIController@getPurchaseOrderForSI');
        Route::post('amendCreditNote', 'CreditNoteAPIController@amendCreditNote');
        Route::post('amendCreditNoteReview', 'CreditNoteAPIController@amendCreditNoteReview');

        Route::resource('warehouse_items', 'WarehouseItemsAPIController');
        Route::get('getUnbilledGRVDetailsForSI', 'UnbilledGrvGroupByAPIController@getUnbilledGRVDetailsForSI');
        Route::post('storePOBaseDetail', 'BookInvSuppDetAPIController@storePOBaseDetail');
        Route::post('editPOBaseDetail', 'BookInvSuppDetAPIController@editPOBaseDetail');
        Route::get('getSupplierInvoiceGRVItems', 'BookInvSuppDetAPIController@getSupplierInvoiceGRVItems');
        Route::resource('warehouse_bin_locations', 'WarehouseBinLocationAPIController');
        Route::post('getAllBinLocationsByWarehouse', 'WarehouseBinLocationAPIController@getAllBinLocationsByWarehouse');

        Route::resource('expense_claims', 'ExpenseClaimAPIController');
        Route::resource('expense_claim_details', 'ExpenseClaimDetailsAPIController');
        Route::resource('expense_claim_types', 'ExpenseClaimTypeAPIController');
        Route::resource('expense_claim_categories', 'ExpenseClaimCategoriesAPIController');
        Route::post('getExpenseClaimByCompany', 'ExpenseClaimAPIController@getExpenseClaimByCompany');
        Route::get('getPaymentStatusHistory', 'ExpenseClaimAPIController@getPaymentStatusHistory');
        Route::get('getExpenseClaimFormData', 'ExpenseClaimAPIController@getExpenseClaimFormData');
        Route::get('getExpenseClaimAudit', 'ExpenseClaimAPIController@getExpenseClaimAudit');
        Route::post('amendExpenseClaimReview', 'ExpenseClaimAPIController@amendExpenseClaimReview');
        Route::get('getDetailsByExpenseClaim', 'ExpenseClaimDetailsAPIController@getDetailsByExpenseClaim');
        Route::get('preCheckECDetailEdit', 'ExpenseClaimDetailsAPIController@preCheckECDetailEdit');

        Route::resource('expense_claim_masters', 'ExpenseClaimMasterAPIController');
        Route::post('getExpenseClaimMasterByCompany', 'ExpenseClaimMasterAPIController@getExpenseClaimMasterByCompany');
        Route::get('getExpenseClaimMasterAudit', 'ExpenseClaimMasterAPIController@getExpenseClaimMasterAudit');
        Route::get('getExpenseClaimMasterPaymentStatusHistory', 'ExpenseClaimMasterAPIController@getExpenseClaimMasterPaymentStatusHistory');

        Route::resource('expense_claim_details_masters', 'ExpenseClaimDetailsMasterAPIController');
        Route::get('getDetailsByExpenseClaimMaster', 'ExpenseClaimDetailsMasterAPIController@getDetailsByExpenseClaimMaster');
        Route::get('preCheckECDetailMasterEdit', 'ExpenseClaimDetailsMasterAPIController@preCheckECDetailMasterEdit');


        Route::resource('expense_claim_categories_masters', 'ExpenseClaimCategoriesMasterAPIController');


        Route::resource('logistic_details', 'LogisticDetailsAPIController');
        Route::get('getItemsByLogistic', 'LogisticDetailsAPIController@getItemsByLogistic');

        Route::get('getPurchaseOrdersForLogistic', 'LogisticDetailsAPIController@getPurchaseOrdersForLogistic');
        Route::get('getGrvByPOForLogistic', 'LogisticDetailsAPIController@getGrvByPOForLogistic');
        Route::get('getGrvDetailsByGrvForLogistic', 'LogisticDetailsAPIController@getGrvDetailsByGrvForLogistic');
        Route::post('addLogisticDetails', 'LogisticDetailsAPIController@addLogisticDetails');


        Route::resource('logistics', 'LogisticAPIController');
        Route::post('getCompanyLocalAndRptAmount', 'LogisticAPIController@getCompanyLocalAndRptAmount');
        Route::get('getLogisticFormData', 'LogisticAPIController@getLogisticFormData');
        Route::get('getStatusByLogistic', 'LogisticAPIController@getStatusByLogistic');
        Route::get('checkPullFromGrv', 'LogisticAPIController@checkPullFromGrv');
        Route::get('getLogisticAudit', 'LogisticAPIController@getLogisticAudit');
        Route::post('getAllLogisticByCompany', 'LogisticAPIController@getAllLogisticByCompany');
        Route::post('exportLogisticsByCompanyReport', 'LogisticAPIController@exportLogisticsByCompanyReport');
        Route::resource('logistic_mode_of_imports', 'LogisticModeOfImportAPIController');
        Route::resource('logistic_shipping_modes', 'LogisticShippingModeAPIController');
        Route::resource('logistic_shipping_statuses', 'LogisticShippingStatusAPIController');
        Route::resource('logistic_statuses', 'LogisticStatusAPIController');

        Route::get('getRecieptVoucherFormData', 'CustomerReceivePaymentAPIController@getRecieptVoucherFormData');
        Route::post('recieptVoucherDataTable', 'CustomerReceivePaymentAPIController@recieptVoucherDataTable');
        Route::get('getReceiptVoucherMasterRecord', 'CustomerReceivePaymentAPIController@getReceiptVoucherMasterRecord');
        Route::post('receiptVoucherReopen', 'CustomerReceivePaymentAPIController@receiptVoucherReopen');
        Route::post('getReceiptVoucherApproval', 'CustomerReceivePaymentAPIController@getReceiptVoucherApproval');
        Route::post('getApprovedRVForCurrentUser', 'CustomerReceivePaymentAPIController@getApprovedRVForCurrentUser');
        Route::post('approveReceiptVoucher', 'CustomerReceivePaymentAPIController@approveReceiptVoucher');
        Route::post('rejectReceiptVoucher', 'CustomerReceivePaymentAPIController@rejectReceiptVoucher');
        Route::post('amendReceiptVoucher', 'CustomerReceivePaymentAPIController@amendReceiptVoucher');
        Route::post('amendReceiptVoucherReview', 'CustomerReceivePaymentAPIController@amendReceiptVoucherReview');
        Route::post('receiptVoucherCancel', 'CustomerReceivePaymentAPIController@receiptVoucherCancel');
        Route::post('approvalPreCheckReceiptVoucher', 'CustomerReceivePaymentAPIController@approvalPreCheckReceiptVoucher');
        Route::put('recieptVoucherLocalUpdate/{id}', 'CustomerReceivePaymentAPIController@recieptVoucherLocalUpdate');
        Route::put('recieptVoucherReportingUpdate/{id}','CustomerReceivePaymentAPIController@recieptVoucherReportingUpdate');

        Route::get('getSupplierInvoiceStatusHistory', 'BookInvSuppMasterAPIController@getSupplierInvoiceStatusHistory');
        Route::post('getSupplierInvoiceAmend', 'BookInvSuppMasterAPIController@getSupplierInvoiceAmend');
        Route::post('amendSupplierInvoiceReview', 'BookInvSuppMasterAPIController@amendSupplierInvoiceReview');
        Route::post('checkPaymentStatusSIPrint', 'BookInvSuppMasterAPIController@checkPaymentStatusSIPrint');
        Route::get('supplierInvoiceTaxPercentage', 'BookInvSuppMasterAPIController@supplierInvoiceTaxPercentage');
        Route::get('customerRecieptDetailsRecords', 'CustomerReceivePaymentDetailAPIController@customerRecieptDetailsRecords');
        Route::get('getReceiptVoucherMatchDetails', 'CustomerReceivePaymentDetailAPIController@getReceiptVoucherMatchDetails');
        Route::post('addReceiptVoucherMatchDetails', 'CustomerReceivePaymentDetailAPIController@addReceiptVoucherMatchDetails');
        Route::get('directRecieptDetailsRecords', 'DirectReceiptDetailAPIController@directRecieptDetailsRecords');
        Route::get('directReceiptContractDropDown', 'DirectReceiptDetailAPIController@directReceiptContractDropDown');

        Route::post('deleteAllSIDirectDetail', 'DirectInvoiceDetailsAPIController@deleteAllSIDirectDetail');

        // Matching
        Route::resource('match_document_masters', 'MatchDocumentMasterAPIController',['only' => ['store', 'show', 'update']]);


        Route::post('getMatchDocumentMasterView', 'MatchDocumentMasterAPIController@getMatchDocumentMasterView');
        Route::get('getMatchDocumentMasterFormData', 'MatchDocumentMasterAPIController@getMatchDocumentMasterFormData');
        Route::post('getPaymentVoucherMatchPullingDetail', 'MatchDocumentMasterAPIController@getPaymentVoucherMatchPullingDetail');
        Route::get('getMatchDocumentMasterRecord', 'MatchDocumentMasterAPIController@getMatchDocumentMasterRecord');
        Route::post('PaymentVoucherMatchingCancel', 'MatchDocumentMasterAPIController@PaymentVoucherMatchingCancel');
        Route::post('receiptVoucherMatchingCancel', 'MatchDocumentMasterAPIController@receiptVoucherMatchingCancel');
        Route::post('getRVMatchDocumentMasterView', 'MatchDocumentMasterAPIController@getRVMatchDocumentMasterView');
        Route::get('getReceiptVoucherMatchItems', 'MatchDocumentMasterAPIController@getReceiptVoucherMatchItems');
        Route::post('getReceiptVoucherPullingDetail', 'MatchDocumentMasterAPIController@getReceiptVoucherPullingDetail');
        Route::post('updateReceiptVoucherMatching', 'MatchDocumentMasterAPIController@updateReceiptVoucherMatching');
        Route::post('amendReceiptMatchingReview', 'MatchDocumentMasterAPIController@amendReceiptMatchingReview');
        Route::post('deleteAllRVMDetails', 'MatchDocumentMasterAPIController@deleteAllRVMDetails');

        Route::get('getPaymentVoucherMatchItems', 'PaySupplierInvoiceMasterAPIController@getPaymentVoucherMatchItems');
        Route::post('paymentVoucherCancel', 'PaySupplierInvoiceMasterAPIController@paymentVoucherCancel');
        Route::post('updateSentToTreasuryDetail', 'PaySupplierInvoiceMasterAPIController@updateSentToTreasuryDetail');

        Route::get('getRVPaymentVoucherMatchItems', 'PaySupplierInvoiceMasterAPIController@getRVPaymentVoucherMatchItems');
        Route::put('paymentVoucherLocalUpdate/{id}', 'PaySupplierInvoiceMasterAPIController@paymentVoucherLocalUpdate');
        Route::put('paymentVoucherReportingUpdate/{id}','PaySupplierInvoiceMasterAPIController@paymentVoucherReportingUpdate');


        Route::post('getCustomerReceiptInvoices', 'AccountsReceivableLedgerAPIController@getCustomerReceiptInvoices');
        Route::post('saveReceiptVoucherUnAllocationsDetails', 'CustomerReceivePaymentDetailAPIController@saveReceiptVoucherUnAllocationsDetails');
        Route::post('addPaymentVoucherMatchingPaymentDetail', 'PaySupplierInvoiceDetailAPIController@addPaymentVoucherMatchingPaymentDetail');
        Route::post('updatePaymentVoucherMatchingDetail', 'PaySupplierInvoiceDetailAPIController@updatePaymentVoucherMatchingDetail');

        Route::resource('bank_ledgers', 'BankLedgerAPIController');
        Route::post('updateTreasuryCollection', 'BankLedgerAPIController@updateTreasuryCollection');
        Route::post('getBankReconciliationsByType', 'BankLedgerAPIController@getBankReconciliationsByType');
        Route::post('getBankAccountPaymentReceiptByType', 'BankLedgerAPIController@getBankAccountPaymentReceiptByType');

        Route::post('getChequePrintingItems', 'BankLedgerAPIController@getChequePrintingItems');
        Route::get('getChequePrintingFormData', 'BankLedgerAPIController@getChequePrintingFormData');
        Route::post('updatePrintChequeItems', 'BankLedgerAPIController@updatePrintChequeItems');
        Route::post('updatePrintAhliChequeItems', 'BankLedgerAPIController@updatePrintAhliChequeItems');


        Route::resource('bank_reconciliations', 'BankReconciliationAPIController');
        Route::get('bankReconciliationAudit', 'BankReconciliationAPIController@bankReconciliationAudit');
        Route::get('getCheckBeforeCreate', 'BankReconciliationAPIController@getCheckBeforeCreate');
        Route::post('getBankReconciliationApprovalByUser', 'BankReconciliationAPIController@getBankReconciliationApprovalByUser');
        Route::post('getBankReconciliationApprovedByUser', 'BankReconciliationAPIController@getBankReconciliationApprovedByUser');
        Route::post('bankRecReopen', 'BankReconciliationAPIController@bankRecReopen');
        Route::post('bankReconciliationReferBack', 'BankReconciliationAPIController@bankReconciliationReferBack');

        Route::get('getBankReconciliationFormData', 'BankReconciliationAPIController@getBankReconciliationFormData');
        Route::post('getAllBankReconciliationByBankAccount', 'BankReconciliationAPIController@getAllBankReconciliationByBankAccount');
        Route::post('getAllBankReconciliationList', 'BankReconciliationAPIController@getAllBankReconciliationList');
        Route::post('amendBankReconciliationReview', 'BankReconciliationAPIController@amendBankReconciliationReview');
        Route::resource('fixed_asset_masters', 'FixedAssetMasterAPIController');
        Route::get('getAllocationFormData', 'FixedAssetMasterAPIController@getAllocationFormData');
        Route::get('getPostToGLAccounts', 'FixedAssetMasterAPIController@getPostToGLAccounts');
        Route::post('getAllAllocationByCompany', 'FixedAssetMasterAPIController@getAllAllocationByCompany');
        Route::post('getAllCostingByCompany', 'FixedAssetMasterAPIController@getAllCostingByCompany');
        Route::post('referBackCosting', 'FixedAssetMasterAPIController@referBackCosting');
        Route::post('createFixedAssetCosting', 'FixedAssetMasterAPIController@create');
        Route::resource('credit_notes', 'CreditNoteAPIController');
        Route::put('updateCreditNote/{id}', 'CreditNoteAPIController@updateCurrency');
        Route::resource('credit_note_details', 'CreditNoteDetailsAPIController');

        // Receipt Voucher
        Route::resource('customer_receive_payments', 'CustomerReceivePaymentAPIController',['only' => ['store', 'show', 'update']]);
        Route::resource('customer_receive_payment_details', 'CustomerReceivePaymentDetailAPIController',['only' => ['store', 'show', 'destroy']]);
        Route::resource('direct_receipt_details', 'DirectReceiptDetailAPIController',['only' => ['show', 'destroy']]);
        Route::post('customerDirectVoucherDetails', 'DirectReceiptDetailAPIController@customerDirectVoucherDetails');
        Route::post('updateDirectReceiptVoucher', 'DirectReceiptDetailAPIController@updateDirectReceiptVoucher');
        Route::post('generatePdcForReceiptVoucher', 'CustomerReceivePaymentAPIController@generatePdcForReceiptVoucher');
        Route::put('customerReceivePaymentsUpdateCurrency/{id}','CustomerReceivePaymentAPIController@UpdateCurrency');


        Route::resource('unbilled_g_r_vs', 'UnbilledGRVAPIController');
        Route::resource('performa_temps', 'PerformaTempAPIController');
        Route::resource('free_billings', 'FreeBillingAPIController');
        Route::get('getSupplierInvoiceStatusHistoryForGRV', 'GRVMasterAPIController@getSupplierInvoiceStatusHistoryForGRV');

        Route::resource('asset_capitalizations', 'AssetCapitalizationAPIController');
        Route::post('getAllCapitalizationByCompany', 'AssetCapitalizationAPIController@getAllCapitalizationByCompany');
        Route::get('getCapitalizationFormData', 'AssetCapitalizationAPIController@getCapitalizationFormData');
        Route::get('getAssetByCategory', 'AssetCapitalizationAPIController@getAssetByCategory');
        Route::get('getAssetNBV', 'AssetCapitalizationAPIController@getAssetNBV');
        Route::get('getCapitalizationFixedAsset', 'AssetCapitalizationAPIController@getCapitalizationFixedAsset');
        Route::post('capitalizationReopen', 'AssetCapitalizationAPIController@capitalizationReopen');
        Route::post('referBackCapitalization', 'AssetCapitalizationAPIController@referBackCapitalization');
        Route::get('getAssetCapitalizationMaster', 'AssetCapitalizationAPIController@getAssetCapitalizationMaster');
        Route::post('getCapitalizationApprovalByUser', 'AssetCapitalizationAPIController@getCapitalizationApprovalByUser');
        Route::post('getCapitalizationApprovedByUser', 'AssetCapitalizationAPIController@getCapitalizationApprovedByUser');
        Route::resource('asset_capitalization_details', 'AssetCapitalizationDetailAPIController');
        Route::get('getCapitalizationDetails', 'AssetCapitalizationDetailAPIController@getCapitalizationDetails');
        Route::post('deleteAllAssetCapitalizationDet', 'AssetCapitalizationDetailAPIController@deleteAllAssetCapitalizationDet');

        Route::resource('journalVoucherCRUD', 'JvMasterAPIController');
        Route::resource('jv_details', 'JvDetailAPIController');
        Route::get('getJournalVoucherMasterFormData', 'JvMasterAPIController@getJournalVoucherMasterFormData');
        Route::post('getJournalVoucherMasterView', 'JvMasterAPIController@getJournalVoucherMasterView');
        Route::post('copyJV', 'JvMasterAPIController@copyJV');
        Route::get('getJournalVoucherMasterRecord', 'JvMasterAPIController@getJournalVoucherMasterRecord');
        Route::get('getJournalVoucherDetails', 'JvDetailAPIController@getJournalVoucherDetails');
        Route::get('getJournalVoucherContracts', 'JvDetailAPIController@getJournalVoucherContracts');
        Route::post('journalVoucherSalaryJVDetailStore', 'JvDetailAPIController@journalVoucherSalaryJVDetailStore');
        Route::post('generateAllocation', 'JvDetailAPIController@generateAllocation');
        Route::get('journalVoucherForSalaryJVMaster', 'JvMasterAPIController@journalVoucherForSalaryJVMaster');
        Route::get('journalVoucherForSalaryJVDetail', 'JvMasterAPIController@journalVoucherForSalaryJVDetail');
        Route::post('journalVoucherDeleteAllSJ', 'JvDetailAPIController@journalVoucherDeleteAllSJ');
        Route::post('jvDetailsExportToCSV', 'JvDetailAPIController@jvDetailsExportToCSV');
        Route::get('journalVoucherForAccrualJVMaster', 'JvMasterAPIController@journalVoucherForAccrualJVMaster');
        Route::get('journalVoucherForAccrualJVDetail', 'JvMasterAPIController@journalVoucherForAccrualJVDetail');
        Route::post('journalVoucherForPOAccrualJVDetail', 'JvMasterAPIController@journalVoucherForPOAccrualJVDetail');
        Route::post('exportJournalVoucherForPOAccrualJVDetail', 'JvMasterAPIController@exportJournalVoucherForPOAccrualJVDetail');
        Route::post('journalVoucherAccrualJVDetailStore', 'JvDetailAPIController@journalVoucherAccrualJVDetailStore');
        Route::post('journalVoucherPOAccrualJVDetailStore', 'JvDetailAPIController@journalVoucherPOAccrualJVDetailStore');
        Route::post('journalVoucherDeleteAllAJ', 'JvDetailAPIController@journalVoucherDeleteAllAJ');
        Route::post('journalVoucherDeleteAllPOAJ', 'JvDetailAPIController@journalVoucherDeleteAllPOAJ');
        Route::post('journalVoucherDeleteAllDetails', 'JvDetailAPIController@journalVoucherDeleteAllDetails');
        Route::post('getJournalVoucherMasterApproval', 'JvMasterAPIController@getJournalVoucherMasterApproval');
        Route::post('getApprovedJournalVoucherForCurrentUser', 'JvMasterAPIController@getApprovedJournalVoucherForCurrentUser');
        Route::get('exportStandardJVFormat', 'JvMasterAPIController@exportStandardJVFormat');
        Route::post('approveJournalVoucher', 'JvMasterAPIController@approveJournalVoucher');
        Route::post('rejectJournalVoucher', 'JvMasterAPIController@rejectJournalVoucher');
        Route::post('journalVoucherReopen', 'JvMasterAPIController@journalVoucherReopen');
        Route::post('getJournalVoucherAmend', 'JvMasterAPIController@getJournalVoucherAmend');
        Route::post('amendJournalVoucherReview', 'JvMasterAPIController@amendJournalVoucherReview');
        Route::post('journalVoucherBudgetUpload', 'JvMasterAPIController@journalVoucherBudgetUpload');
        Route::post('standardJvExcelUpload', 'JvMasterAPIController@standardJvExcelUpload');
        Route::post('approvalPreCheckJV', 'JvMasterAPIController@approvalPreCheckJV');

        Route::resource('supplierInvoiceAmendHistoryCRUD', 'BookInvSuppMasterRefferedBackAPIController');
        Route::resource('bookInvSuppDetRefferedbacks', 'BookInvSuppDetRefferedBackAPIController');
        Route::resource('DirectInvoiceDetRefferedbacks', 'DirectInvoiceDetailsRefferedBackAPIController');
        Route::post('getSIMasterAmendHistory', 'BookInvSuppMasterRefferedBackAPIController@getSIMasterAmendHistory');
        Route::get('getSIDetailGRVAmendHistory', 'BookInvSuppDetRefferedBackAPIController@getSIDetailGRVAmendHistory');
        Route::get('getSIDetailDirectAmendHistory', 'DirectInvoiceDetailsRefferedBackAPIController@getSIDetailDirectAmendHistory');

        Route::resource('bank_memo_types', 'BankMemoTypesAPIController');
        Route::resource('payment_bank_transfers', 'PaymentBankTransferAPIController');
        Route::get('getCheckBeforeCreateBankTransfers', 'PaymentBankTransferAPIController@getCheckBeforeCreate');
        Route::post('getAllBankTransferByBankAccount', 'PaymentBankTransferAPIController@getAllBankTransferByBankAccount');
        Route::post('getAllBankTransferList', 'PaymentBankTransferAPIController@getAllBankTransferList');
        Route::post('getBankTransferApprovalByUser', 'PaymentBankTransferAPIController@getBankTransferApprovalByUser');
        Route::post('getBankTransferApprovedByUser', 'PaymentBankTransferAPIController@getBankTransferApprovedByUser');
        Route::get('exportPaymentBankTransferPreCheck', 'PaymentBankTransferAPIController@exportPaymentBankTransferPreCheck');
        Route::post('paymentBankTransferReopen', 'PaymentBankTransferAPIController@paymentBankTransferReopen');
        Route::post('paymentBankTransferReferBack', 'PaymentBankTransferAPIController@paymentBankTransferReferBack');
        Route::post('getPaymentsByBankTransfer', 'BankLedgerAPIController@getPaymentsByBankTransfer');
        Route::post('amendBankTransferReview', 'BankLedgerAPIController@amendBankTransferReview');
        Route::post('clearExportBlockConfirm', 'BankLedgerAPIController@clearExportBlockConfirm');

        Route::get('getTreasuryManagementFilterData', 'BankReconciliationAPIController@getTreasuryManagementFilterData');
        Route::post('validateTMReport', 'BankReconciliationAPIController@validateTMReport');
        Route::post('generateTMReport', 'BankReconciliationAPIController@generateTMReport');
        Route::post('exportTMReport', 'BankReconciliationAPIController@exportReport');
        Route::get('getAllcompaniesByDepartment', 'DocumentApprovedAPIController@getAllcompaniesByDepartment');

        Route::resource('fixed_asset_masters', 'FixedAssetMasterAPIController');
        Route::post('getFixedAssetSubCat', 'FixedAssetMasterAPIController@getFixedAssetSubCat');
        Route::get('getFinanceGLCode', 'FixedAssetMasterAPIController@getFinanceGLCode');
        Route::get('getFAGrvDetailsByID', 'FixedAssetMasterAPIController@getFAGrvDetailsByID');
        Route::post('assetCostingReopen', 'FixedAssetMasterAPIController@assetCostingReopen');

        Route::post('getCostingApprovalByUser', 'FixedAssetMasterAPIController@getCostingApprovalByUser');
        Route::post('getCostingApprovedByUser', 'FixedAssetMasterAPIController@getCostingApprovedByUser');
        Route::get('getAssetCostingMaster', 'FixedAssetMasterAPIController@getAssetCostingMaster');
        Route::post('amendAssetCostingReview', 'FixedAssetMasterAPIController@amendAssetCostingReview');

        Route::get('getAssetCostingByID/{id}', 'FixedAssetMasterAPIController@getAssetCostingByID');
        Route::get('customerInvoiceReceiptStatus', 'CustomerInvoiceDirectAPIController@customerInvoiceReceiptStatus');
        Route::post('updateCustomerReciept', 'CustomerReceivePaymentDetailAPIController@updateCustomerReciept');
        Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');
        Route::post('getAllDepreciationByCompany', 'FixedAssetDepreciationMasterAPIController@getAllDepreciationByCompany');
        Route::get('getDepreciationFormData', 'FixedAssetDepreciationMasterAPIController@getDepreciationFormData');
        Route::get('assetDepreciationByID/{id}', 'FixedAssetDepreciationMasterAPIController@assetDepreciationByID');
        Route::get('assetDepreciationMaster', 'FixedAssetDepreciationMasterAPIController@assetDepreciationMaster');
        Route::post('assetDepreciationReopen', 'FixedAssetDepreciationMasterAPIController@assetDepreciationReopen');
        Route::post('getAssetDepApprovalByUser', 'FixedAssetDepreciationMasterAPIController@getAssetDepApprovalByUser');
        Route::post('getAssetDepApprovedByUser', 'FixedAssetDepreciationMasterAPIController@getAssetDepApprovedByUser');
        Route::post('referBackDepreciation', 'FixedAssetDepreciationMasterAPIController@referBackDepreciation');
        Route::post('amendAssetDepreciationReview', 'FixedAssetDepreciationMasterAPIController@amendAssetDepreciationReview');
        Route::post('updateReceiptVoucherMatchDetail', 'CustomerReceivePaymentDetailAPIController@updateReceiptVoucherMatchDetail');

        Route::resource('fixed_asset_insurance_details', 'FixedAssetInsuranceDetailAPIController');

        Route::resource('budget_masters', 'BudgetMasterAPIController');
        Route::post('getBudgetsByCompany', 'BudgetMasterAPIController@getBudgetsByCompany');
        Route::post('updateCutOffPeriod', 'BudgetMasterAPIController@updateCutOffPeriod');
        Route::post('budgetReferBack', 'BudgetMasterAPIController@budgetReferBack');
        Route::post('getBudgetBlockedDocuments', 'BudgetMasterAPIController@getBudgetBlockedDocuments');
        Route::post('budgetReopen', 'BudgetMasterAPIController@budgetReopen');
        Route::post('getBudgetApprovedByUser', 'BudgetMasterAPIController@getBudgetApprovedByUser');
        Route::post('getBudgetApprovalByUser', 'BudgetMasterAPIController@getBudgetApprovalByUser');
        Route::get('getBudgetAudit', 'BudgetMasterAPIController@getBudgetAudit');
        Route::post('reportBudgetGLCodeWise', 'BudgetMasterAPIController@reportBudgetGLCodeWise');
        Route::post('budgetGLCodeWiseDetails', 'BudgetMasterAPIController@budgetGLCodeWiseDetails');
        Route::post('exportBudgetGLCodeWise', 'BudgetMasterAPIController@exportBudgetGLCodeWise');
        Route::post('exportBudgetTemplateCategoryWise', 'BudgetMasterAPIController@exportBudgetTemplateCategoryWise');
        Route::post('exportBudgetGLCodeWiseDetails', 'BudgetMasterAPIController@exportBudgetGLCodeWiseDetails');
        Route::post('reportBudgetTemplateCategoryWise', 'BudgetMasterAPIController@reportBudgetTemplateCategoryWise');
        Route::get('getBudgetFormData', 'BudgetMasterAPIController@getBudgetFormData');
        Route::get('downloadBudgetUploadTemplate', 'BudgetMasterAPIController@downloadBudgetUploadTemplate');
        Route::get('checkBudgetShowPolicy', 'BudgetMasterAPIController@checkBudgetShowPolicy');
        Route::get('getBudgetConsumptionByDocument', 'BudgetMasterAPIController@getBudgetConsumptionByDocument');
        Route::post('syncGlBudget', 'BudjetdetailsAPIController@syncGlBudget');
        Route::post('getBudgetDetailHistory', 'BudjetdetailsAPIController@getBudgetDetailHistory');

        Route::get('checkPolicyForExchangeRates', 'CommonPoliciesAPIController@checkPolicyForExchangeRates');
        Route::get('getInvoiceLogistic', 'CustomerInvoiceLogisticAPIController@getInvoiceLogistic');

        Route::resource('budjetdetails', 'BudjetdetailsAPIController');
        Route::post('getDetailsByBudget', 'BudjetdetailsAPIController@getDetailsByBudget');
        Route::post('exportDetailsByBudget', 'BudjetdetailsAPIController@exportReport');
        Route::post('removeBudgetDetails', 'BudjetdetailsAPIController@removeBudgetDetails');
        Route::get('getBudgetDetailTotalSummary', 'BudjetdetailsAPIController@getBudgetDetailTotalSummary');
        Route::post('bulkUpdateBudgetDetails', 'BudjetdetailsAPIController@bulkUpdateBudgetDetails');
        Route::post('budgetDetailsUpload', 'BudjetdetailsAPIController@budgetDetailsUpload');
        Route::resource('templates_g_l_codes', 'TemplatesGLCodeAPIController');
        Route::resource('templates_masters', 'TemplatesMasterAPIController');
        Route::resource('templates_details', 'TemplatesDetailsAPIController');
        Route::get('getTemplatesDetailsByMaster', 'TemplatesDetailsAPIController@getTemplatesDetailsByMaster');
        Route::get('getTemplatesDetailsById', 'TemplatesDetailsAPIController@getTemplatesDetailsById');
        Route::get('getAllGLCodesByTemplate', 'TemplatesDetailsAPIController@getAllGLCodesByTemplate');
        Route::get('getAllGLCodes', 'TemplatesDetailsAPIController@getAllGLCodes');
        Route::get('getTemplateByGLCode', 'TemplatesDetailsAPIController@getTemplateByGLCode');
        Route::resource('asset_disposal_masters', 'AssetDisposalMasterAPIController');
        Route::post('getAllDisposalByCompany', 'AssetDisposalMasterAPIController@getAllDisposalByCompany');
        Route::post('disposalReopen', 'AssetDisposalMasterAPIController@disposalReopen');
        Route::post('getDisposalApprovalByUser', 'AssetDisposalMasterAPIController@getDisposalApprovalByUser');
        Route::post('getDisposalApprovedByUser', 'AssetDisposalMasterAPIController@getDisposalApprovedByUser');
        Route::post('getAllAssetsForDisposal', 'AssetDisposalMasterAPIController@getAllAssetsForDisposal');
        Route::post('referBackDisposal', 'AssetDisposalMasterAPIController@referBackDisposal');
        Route::get('getDisposalFormData', 'AssetDisposalMasterAPIController@getDisposalFormData');
        Route::post('amendAssetDisposalReview', 'AssetDisposalMasterAPIController@amendAssetDisposalReview');

        Route::get('getAssetDisposalDetail', 'AssetDisposalDetailAPIController@getAssetDisposalDetail');
        Route::resource('asset_disposal_details', 'AssetDisposalDetailAPIController');
        Route::post('deleteAllDisposalDetail', 'AssetDisposalDetailAPIController@deleteAllDisposalDetail');
        Route::resource('budget_transfer', 'BudgetTransferFormAPIController');
        Route::post('getBudgetTransferApprovedByUser', 'BudgetTransferFormAPIController@getBudgetTransferApprovedByUser');
        Route::post('budgetTransferCreateFromReview', 'BudgetTransferFormAPIController@budgetTransferCreateFromReview');
        Route::post('getBudgetTransferApprovalByUser', 'BudgetTransferFormAPIController@getBudgetTransferApprovalByUser');
        Route::get('getBudgetTransferAudit', 'BudgetTransferFormAPIController@getBudgetTransferAudit');
        Route::post('budgetTransferReopen', 'BudgetTransferFormAPIController@budgetTransferReopen');
        Route::post('getBudgetTransferMasterByCompany', 'BudgetTransferFormAPIController@getBudgetTransferMasterByCompany');
        Route::get('getBudgetTransferFormData', 'BudgetTransferFormAPIController@getBudgetTransferFormData');
        Route::resource('budget_transfer_details', 'BudgetTransferFormDetailAPIController');
        Route::get('checkBudgetAllocation', 'BudgetTransferFormDetailAPIController@checkBudgetAllocation');
        Route::get('getDetailsByBudgetTransfer', 'BudgetTransferFormDetailAPIController@getDetailsByBudgetTransfer');

        Route::resource('budget_adjustments', 'BudgetAdjustmentAPIController');
        Route::resource('audit_trails', 'AuditTrailAPIController');

        Route::post('generateAssetInsuranceReport', 'FixedAssetMasterAPIController@generateAssetInsuranceReport');
        Route::post('exportAssetInsuranceReport', 'FixedAssetMasterAPIController@exportAssetInsuranceReport');
        Route::resource('fixed_asset_categories', 'FixedAssetCategoryAPIController');
        Route::post('getAllAssetCategory', 'FixedAssetCategoryAPIController@getAllAssetCategory');
        Route::get('getAssetCategoryFormData', 'FixedAssetCategoryAPIController@getAssetCategoryFormData');
        Route::resource('fixed_asset_depreciation_periods', 'FixedAssetDepreciationPeriodAPIController');
        Route::post('getAssetDepPeriodsByID', 'FixedAssetDepreciationPeriodAPIController@getAssetDepPeriodsByID');
        Route::post('exportAMDepreciation', 'FixedAssetDepreciationPeriodAPIController@exportAMDepreciation');
        Route::resource('asset_types', 'AssetTypeAPIController');
        Route::resource('fixed_asset_category_subs', 'FixedAssetCategorySubAPIController');
        Route::post('getAllAssetSubCategoryByMain', 'FixedAssetCategorySubAPIController@getAllAssetSubCategoryByMain');

        Route::resource('h_r_m_s_jv_details', 'HRMSJvDetailsAPIController');
        Route::resource('h_r_m_s_jv_masters', 'HRMSJvMasterAPIController');
        Route::resource('accruaval_from_o_p_masters', 'AccruavalFromOPMasterAPIController');
        Route::resource('fixed_asset_costs', 'FixedAssetCostAPIController');
        Route::resource('insurance_policy_types', 'InsurancePolicyTypeAPIController');
        Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');
        Route::resource('asset_disposal_types', 'AssetDisposalTypeAPIController');
        Route::post('asset_disposal_type_config', 'AssetDisposalTypeAPIController@config_list');
        Route::post('generateAssetDetailDrilldown', 'AssetManagementReportAPIController@generateAssetDetailDrilldown');
        Route::resource('monthly_additions_masters', 'MonthlyAdditionsMasterAPIController');
        Route::get('getMonthlyAdditionAudit', 'MonthlyAdditionsMasterAPIController@getMonthlyAdditionAudit');
        Route::post('monthlyAdditionReopen', 'MonthlyAdditionsMasterAPIController@monthlyAdditionReopen');
        Route::post('getMonthlyAdditionsByCompany', 'MonthlyAdditionsMasterAPIController@getMonthlyAdditionsByCompany');
        Route::get('getMonthlyAdditionFormData', 'MonthlyAdditionsMasterAPIController@getMonthlyAdditionFormData');
        Route::post('getProcessPeriods', 'MonthlyAdditionsMasterAPIController@getProcessPeriods');
        Route::post('amendEcMonthlyAdditionReview', 'MonthlyAdditionsMasterAPIController@amendEcMonthlyAdditionReview');
        Route::resource('monthly_addition_details', 'MonthlyAdditionDetailAPIController');
        Route::get('getItemsByMonthlyAddition', 'MonthlyAdditionDetailAPIController@getItemsByMonthlyAddition');
        Route::get('checkPullFromExpenseClaim', 'MonthlyAdditionDetailAPIController@checkPullFromExpenseClaim');
        Route::get('getECForMonthlyAddition', 'MonthlyAdditionDetailAPIController@getECForMonthlyAddition');
        Route::get('getECDetailsForMonthlyAddition', 'MonthlyAdditionDetailAPIController@getECDetailsForMonthlyAddition');
        Route::post('addMonthlyAdditionDetails', 'MonthlyAdditionDetailAPIController@addMonthlyAdditionDetails');
        Route::post('deleteAllMonthlyAdditionDetails', 'MonthlyAdditionDetailAPIController@deleteAllMonthlyAdditionDetails');
        Route::resource('employment_types', 'EmploymentTypeAPIController');
        Route::resource('period_masters', 'PeriodMasterAPIController');
        Route::resource('salary_process_masters', 'SalaryProcessMasterAPIController');
        Route::resource('salary_process_employment_types', 'SalaryProcessEmploymentTypesAPIController');
        Route::get('getAssetCostingViewByFaID/{id}', 'FixedAssetMasterAPIController@getAssetCostingViewByFaID');
        Route::post('assetCostingUpload', 'FixedAssetMasterAPIController@assetCostingUpload');
        Route::get('downloadAssetTemplate', 'FixedAssetMasterAPIController@downloadAssetTemplate');
        Route::get('downloadPrItemUploadTemplate', 'PurchaseRequestAPIController@downloadPrItemUploadTemplate');
        Route::post('pull-mr-details', 'PurchaseRequestAPIController@pullMrDetails');
        Route::get('downloadQuotationItemUploadTemplate','QuotationMasterAPIController@downloadQuotationItemUploadTemplate');
        
        Route::resource('pulled-mr-details', 'PulledItemFromMRController');
        Route::post('remove-pulled-mr-details', 'PulledItemFromMRController@removeMRDetails');
        Route::get('purchase_requests/pull/items/', 'PulledItemFromMRController@pullAllItemsByPr');

       

        Route::resource('hrms_chart_of_accounts', 'HRMSChartOfAccountsAPIController');
        Route::resource('hrms_department_masters', 'HRMSDepartmentMasterAPIController');
        Route::post('generateAdvancePaymentRequestReport', 'PoAdvancePaymentAPIController@generateAdvancePaymentRequestReport');
        Route::post('exportAdvancePaymentRequestReport', 'PoAdvancePaymentAPIController@exportAdvancePaymentRequestReport');

        Route::post('getAllPaymentVoucherAmendHistory', 'PaySupplierInvoiceMasterReferbackAPIController@getAllPaymentVoucherAmendHistory');
        Route::get('paymentVoucherHistoryByPVID', 'PaySupplierInvoiceMasterReferbackAPIController@paymentVoucherHistoryByPVID');
        Route::resource('advance_payment_referbacks', 'AdvancePaymentReferbackAPIController');
        Route::resource('direct_payment_referbacks', 'DirectPaymentReferbackAPIController');

        Route::get('getPOPaymentHistoryDetails', 'PaySupplierInvoiceDetailReferbackAPIController@getPOPaymentHistoryDetails');
        Route::get('getADVPaymentHistoryDetails', 'AdvancePaymentReferbackAPIController@getADVPaymentHistoryDetails');
        Route::get('getDirectPaymentHistoryDetails', 'DirectPaymentReferbackAPIController@getDirectPaymentHistoryDetails');
        Route::get('getDirectPaymentDetailsHistoryByID', 'DirectPaymentReferbackAPIController@getDirectPaymentDetailsHistoryByID');
        Route::get('getDPHistoryExchangeRateAmount', 'DirectPaymentReferbackAPIController@getDPHistoryExchangeRateAmount');

        Route::resource('paymentVoucherDetailReferbacks', 'PaySupplierInvoiceDetailReferbackAPIController');
        Route::post('addDetailsFromExpenseClaim', 'DirectPaymentDetailsAPIController@addDetailsFromExpenseClaim');

        Route::resource('directReceiptHistories', 'DirectReceiptDetailsRefferedHistoryAPIController');
        Route::resource('PaymentVoucherMasterReferbacks', 'PaySupplierInvoiceMasterReferbackAPIController');

        Route::resource('receiptVoucherAmendHistoryCRUD', 'CustomerReceivePaymentRefferedHistoryAPIController');
        Route::resource('custreceivepaymentdethistories', 'CustReceivePaymentDetRefferedHistoryAPIController');

        Route::resource('advance_payment_referbacks', 'AdvancePaymentReferbackAPIController');
        Route::resource('direct_payment_referbacks', 'DirectPaymentReferbackAPIController');
        Route::post('getReceiptVoucherAmendHistory', 'CustomerReceivePaymentRefferedHistoryAPIController@getReceiptVoucherAmendHistory');
        Route::get('getRVDetailDirectAmendHistory', 'DirectReceiptDetailsRefferedHistoryAPIController@getRVDetailDirectAmendHistory');
        Route::get('getRVDetailAmendHistory', 'CustReceivePaymentDetRefferedHistoryAPIController@getRVDetailAmendHistory');

        Route::resource('bank_memo_payees', 'BankMemoPayeeAPIController');
        Route::get('payeeBankMemosByDocument', 'BankMemoPayeeAPIController@payeeBankMemosByDocument');
        Route::post('addBulkPayeeMemos', 'BankMemoPayeeAPIController@addBulkPayeeMemos');
        Route::post('payeeBankMemoDeleteAll', 'BankMemoPayeeAPIController@payeeBankMemoDeleteAll');

        Route::post('getReferBackHistoryByStockTransfer', 'StockTransferRefferedBackAPIController@getReferBackHistoryByStockTransfer');
        Route::resource('stock_transfer_reffered_backs', 'StockTransferRefferedBackAPIController');
        Route::resource('st_details_reffered_backs', 'StockTransferDetailsRefferedBackAPIController');
        Route::get('getStockTransferDetailsReferBack', 'StockTransferDetailsRefferedBackAPIController@getStockTransferDetailsReferBack');

        Route::post('getCreditNoteAmendHistory', 'CreditNoteReferredbackAPIController@getCreditNoteAmendHistory');
        Route::resource('creditNoteReferredbackCRUD', 'CreditNoteReferredbackAPIController');
        Route::resource('creditNoteDetailsRefferdbacks', 'CreditNoteDetailsRefferdbackAPIController');
        Route::get('getCapitalizationLinkedDocument', 'AssetCapitalizationAPIController@getCapitalizationLinkedDocument');
        Route::get('getCNDetailAmendHistory', 'CreditNoteDetailsRefferdbackAPIController@getCNDetailAmendHistory');

        Route::post('getCustomerInvoiceApproval', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceApproval');
        Route::post('getApprovedCustomerInvoiceForCurrentUser', 'CustomerInvoiceDirectAPIController@getApprovedCustomerInvoiceForCurrentUser');
        Route::post('approveCustomerInvoice', 'CustomerInvoiceDirectAPIController@approveCustomerInvoice');
        Route::post('approvalPreCheckCustomerInvoice', 'CustomerInvoiceDirectAPIController@approvalPreCheckCustomerInvoice');
        Route::post('rejectCustomerInvoice', 'CustomerInvoiceDirectAPIController@rejectCustomerInvoice');
        Route::post('getCustomerInvoiceAmend', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceAmend');
        Route::post('customerInvoiceCancel', 'CustomerInvoiceDirectAPIController@customerInvoiceCancel');
        Route::post('amendCustomerInvoiceReview', 'CustomerInvoiceDirectAPIController@amendCustomerInvoiceReview');

        Route::resource('customerInvoiceRefferedbacksCRUD', 'CustomerInvoiceDirectRefferedbackAPIController');
        Route::resource('customerInvoiceDetRefferedbacks', 'CustomerInvoiceDirectDetRefferedbackAPIController');
        Route::post('getCIMasterAmendHistory', 'CustomerInvoiceDirectRefferedbackAPIController@getCIMasterAmendHistory');
        Route::get('getCIDetailsForAmendHistory', 'CustomerInvoiceDirectDetRefferedbackAPIController@getCIDetailsForAmendHistory');

        Route::resource('sr_details_reffered_backs', 'StockReceiveDetailsRefferedBackAPIController');
        Route::get('getStockReceiveDetailsReferBack', 'StockReceiveDetailsRefferedBackAPIController@getStockReceiveDetailsReferBack');
        Route::resource('stock_receive_reffered_backs', 'StockReceiveRefferedBackAPIController');
        Route::post('getReferBackHistoryByStockReceive', 'StockReceiveRefferedBackAPIController@getReferBackHistoryByStockReceive');

        Route::resource('supplier_category_icv_subs', 'SupplierCategoryICVSubAPIController');
        Route::resource('supplier_category_icv_masters', 'SupplierCategoryICVMasterAPIController');
        Route::get('subICVCategoriesByMasterCategory', 'SupplierCategoryICVMasterAPIController@subICVCategoriesByMasterCategory');

        Route::resource('debitNoteDetailsRefferedbacks', 'DebitNoteDetailsRefferedbackAPIController');
        Route::resource('debitNoteMasterRefferedbacksCRUD', 'DebitNoteMasterRefferedbackAPIController');
        Route::post('getDebitNoteAmendHistory', 'DebitNoteMasterRefferedbackAPIController@getDebitNoteAmendHistory');
        Route::get('getDNDetailAmendHistory', 'DebitNoteDetailsRefferedbackAPIController@getDNDetailAmendHistory');
        Route::put('debitNoteLocalUpdate/{id}', 'DebitNoteAPIController@debitNoteLocalUpdate');
        Route::put('debitNoteReportingUpdate/{id}','DebitNoteAPIController@debitNoteReportingUpdate');

        Route::resource('item_issue_referred_back', 'ItemIssueMasterRefferedBackAPIController');
        Route::post('getReferBackHistoryByMaterielIssues', 'ItemIssueMasterRefferedBackAPIController@getReferBackHistoryByMaterielIssues');
        Route::get('getItemIssueDetailsReferBack', 'ItemIssueDetailsRefferedBackAPIController@getItemIssueDetailsReferBack');
        Route::resource('item_issue_details_reffered_backs', 'ItemIssueDetailsRefferedBackAPIController');

        Route::resource('jvMasterReferredbacks', 'JvMasterReferredbackAPIController');
        Route::resource('jvDetailsReferredbacks', 'JvDetailsReferredbackAPIController');
        Route::post('getJournalVoucherAmendHistory', 'JvMasterReferredbackAPIController@getJournalVoucherAmendHistory');
        Route::get('getJVDetailAmendHistory', 'JvDetailsReferredbackAPIController@getJVDetailAmendHistory');

        Route::resource('mr_master_referred_back', 'ItemReturnMasterRefferedBackAPIController');
        Route::post('getReferBackHistoryByMaterielReturn', 'ItemReturnMasterRefferedBackAPIController@getReferBackHistoryByMaterielReturn');
        Route::resource('mr_details_reffered_backs', 'ItemReturnDetailsRefferedBackAPIController');
        Route::get('getItemReturnDetailsReferBack', 'ItemReturnDetailsRefferedBackAPIController@getItemReturnDetailsReferBack');

        Route::resource('asset_capitalization_referreds', 'AssetCapitalizationReferredAPIController');
        Route::post('getAllCapitalizationAmendHistory', 'AssetCapitalizationReferredAPIController@getAllCapitalizationAmendHistory');
        Route::get('assetCapitalizationHistoryByID', 'AssetCapitalizationReferredAPIController@assetCapitalizationHistoryByID');
        Route::resource('asset_capitalizatio_det_referreds', 'AssetCapitalizatioDetReferredAPIController');
        Route::get('getCapitalizationDetailsHistory', 'AssetCapitalizatioDetReferredAPIController@getCapitalizationDetailsHistory');
        Route::resource('asset_disposal_referreds', 'AssetDisposalReferredAPIController');
        Route::post('getAllAssetDisposalAmendHistory', 'AssetDisposalReferredAPIController@getAllAssetDisposalAmendHistory');
        Route::get('assetDisposalHistoryByID', 'AssetDisposalReferredAPIController@assetDisposalHistoryByAutoID');
        Route::resource('asset_disposal_detail_referreds', 'AssetDisposalDetailReferredAPIController');
        Route::get('getAssetDisposalDetailHistory', 'AssetDisposalDetailReferredAPIController@getAssetDisposalDetailHistory');

        Route::resource('fixedassetmasterreferredhistory', 'FixedAssetMasterReferredHistoryAPIController');
        Route::post('getAllAssetCostingAmendHistory', 'FixedAssetMasterReferredHistoryAPIController@getAllAssetCostingAmendHistory');
        Route::get('assetCostingHistoryByAutoID', 'FixedAssetMasterReferredHistoryAPIController@assetCostingHistoryByAutoID');
        Route::resource('depmasterreferredhistory', 'DepreciationMasterReferredHistoryAPIController');
        Route::post('getAllDepreciationAmendHistory', 'DepreciationMasterReferredHistoryAPIController@getAllDepreciationAmendHistory');
        Route::get('assetDepreciationHistoryByID', 'DepreciationMasterReferredHistoryAPIController@assetDepreciationHistoryByID');
        Route::resource('depperiodsreferredhistory', 'DepreciationPeriodsReferredHistoryAPIController');
        Route::post('getAssetDepPeriodHistoryByID', 'DepreciationPeriodsReferredHistoryAPIController@getAssetDepPeriodHistoryByID');

        Route::resource('request_reffered_back', 'RequestRefferedBackAPIController');
        Route::post('getReferBackHistoryByRequest', 'RequestRefferedBackAPIController@getReferBackHistoryByRequest');
        Route::resource('request_details_reffered_backs', 'RequestDetailsRefferedBackAPIController');
        Route::get('getItemRequestDetailsReferBack', 'RequestDetailsRefferedBackAPIController@getItemRequestDetailsReferBack');

        Route::resource('asset_capitalization_referreds', 'AssetCapitalizationReferredAPIController');
        Route::resource('asset_capitalizatio_det_referreds', 'AssetCapitalizatioDetReferredAPIController');
        Route::resource('asset_disposal_referreds', 'AssetDisposalReferredAPIController');
        Route::resource('asset_disposal_detail_referreds', 'AssetDisposalDetailReferredAPIController');

        Route::resource('bankTransferRefferedBack', 'PaymentBankTransferRefferedBackAPIController');
        Route::post('getReferBackHistoryByBankTransfer', 'PaymentBankTransferRefferedBackAPIController@getReferBackHistoryByBankTransfer');
        Route::resource('bankTransferDetailRefferedBacks', 'PaymentBankTransferDetailRefferedBackAPIController');

        Route::resource('bankRecRefferedBack', 'BankReconciliationRefferedBackAPIController');
        Route::post('getReferBackHistoryByBankRec', 'BankReconciliationRefferedBackAPIController@getReferBackHistoryByBankRec');

        Route::get('getDocumentControlFilterFormData', 'DocumentControlAPIController@getDocumentControlFilterFormData');
        Route::post('generateDocumentControlReport', 'DocumentControlAPIController@generateDocumentControlReport');

        Route::resource('grvMasterRefferedbacksCRUD', 'GrvMasterRefferedbackAPIController');
        Route::resource('grvDetailsRefferedbacks', 'GrvDetailsRefferedbackAPIController');
        Route::post('getGRVMasterAmendHistory', 'GrvMasterRefferedbackAPIController@getGRVMasterAmendHistory');
        Route::get('getGRVDetailsAmendHistory', 'GrvDetailsRefferedbackAPIController@getGRVDetailsAmendHistory');
        Route::get('getGRVDetailsReversalHistory', 'GrvDetailsRefferedbackAPIController@getGRVDetailsReversalHistory');
        Route::resource('document_restriction_assigns', 'DocumentRestrictionAssignAPIController');
        Route::resource('document_restriction_policies', 'DocumentRestrictionPolicyAPIController');
        Route::get('checkRestrictionByPolicy', 'DocumentRestrictionAssignAPIController@checkRestrictionByPolicy');

        Route::resource('itemMasterRefferedBack', 'ItemMasterRefferedBackAPIController');
        Route::post('referBackHistoryByItemsMaster', 'ItemMasterRefferedBackAPIController@referBackHistoryByItemsMaster');

        Route::resource('customerInvoiceCollectionDetails', 'CustomerInvoiceCollectionDetailAPIController');
        Route::get('getCustomerCollectionItems', 'CustomerInvoiceCollectionDetailAPIController@getCustomerCollectionItems');
        Route::resource('supplier_refer_back', 'SupplierMasterRefferedBackAPIController');
        Route::post('referBackHistoryBySupplierMaster', 'SupplierMasterRefferedBackAPIController@referBackHistoryBySupplierMaster');

        Route::resource('customer_invoice_logistics', 'CustomerInvoiceLogisticAPIController');
        Route::post('addNote', 'CustomerInvoiceLogisticAPIController@addNote');

        Route::resource('customer_refer_back', 'CustomerMasterRefferedBackAPIController');
        Route::post('referBackHistoryByCustomerMaster', 'CustomerMasterRefferedBackAPIController@referBackHistoryByCustomerMaster');
        Route::post('getEmployeeMasterView', 'EmployeeAPIController@getEmployeeMasterView');
        Route::post('confirmEmployeePasswordReset', 'EmployeeAPIController@confirmEmployeePasswordReset');
        Route::get('getEmployeeMasterData', 'EmployeeAPIController@getEmployeeMasterData');
        Route::post('chartOfAccountReferBack', 'ChartOfAccountAPIController@chartOfAccountReferBack');
        Route::resource('chartOfAccountsReferBack', 'ChartOfAccountsRefferedBackAPIController');
        Route::post('referBackHistoryByChartOfAccount', 'ChartOfAccountsRefferedBackAPIController@referBackHistoryByChartOfAccount');
        Route::get('getReferBackApprovedDetails', 'DocumentReferedHistoryAPIController@getReferBackApprovedDetails');

        Route::resource('report_templates', 'ReportTemplateAPIController');
        Route::post('getAllReportTemplate', 'ReportTemplateAPIController@getAllReportTemplate');
        Route::get('getAssignedReportTemplatesByGl', 'ReportTemplateAPIController@getAssignedReportTemplatesByGl');
        Route::get('getReportTemplatesByCategory', 'ReportTemplateAPIController@getReportTemplatesByCategory');
        Route::get('getReportTemplatesCategoryByTemplate', 'ReportTemplateDetailsAPIController@getReportTemplatesCategoryByTemplate');
        Route::post('getDefaultTemplateCategories', 'ReportTemplateDetailsAPIController@getDefaultTemplateCategories');
        Route::post('getAllReportTemplateForCopy', 'ReportTemplateAPIController@getAllReportTemplateForCopy');
        Route::get('getReportTemplateFormData', 'ReportTemplateAPIController@getReportTemplateFormData');
        Route::resource('report_template_details', 'ReportTemplateDetailsAPIController');
        Route::get('getReportTemplateDetail/{id}', 'ReportTemplateDetailsAPIController@getReportTemplateDetail');
        Route::get('getReportTemplateSubCat', 'ReportTemplateDetailsAPIController@getReportTemplateSubCat');
        Route::post('addTemplateSubCategory', 'ReportTemplateDetailsAPIController@addSubCategory');
        Route::post('getChartOfAccountCode', 'ReportTemplateDetailsAPIController@getChartOfAccountCode');
        Route::post('mirrorReportTemplateRowConfiguration', 'ReportTemplateDetailsAPIController@mirrorReportTemplateRowConfiguration');
        Route::post('linkPandLGLCodeValidation', 'ReportTemplateDetailsAPIController@linkPandLGLCodeValidation');
        Route::post('linkPandLGLCode', 'ReportTemplateDetailsAPIController@linkPandLGLCode');
        Route::get('getEmployees', 'ReportTemplateAPIController@getEmployees');
        Route::get('getReportHeaderData', 'ReportTemplateAPIController@getReportHeaderData');
        Route::resource('report_template_links', 'ReportTemplateLinksAPIController');
        Route::post('reportTemplateDetailSubCatLink', 'ReportTemplateLinksAPIController@reportTemplateDetailSubCatLink');
        Route::post('assignReportTemplateToGl', 'ReportTemplateLinksAPIController@assignReportTemplateToGl');
        Route::post('deleteAllLinkedGLCodes', 'ReportTemplateLinksAPIController@deleteAllLinkedGLCodes');

        Route::post('getBankMasterByCompany', 'BankAssignAPIController@getBankMasterByCompany');
        Route::post('getAccountsByBank', 'BankAccountAPIController@getAccountsByBank');
        Route::post('getAllBankAccounts', 'BankAccountAPIController@getAllBankAccounts');
        Route::post('exportBankAccountMaster', 'BankAccountAPIController@exportBankAccountMaster');
        Route::get('getBankAccountFormData', 'BankAccountAPIController@getBankAccountFormData');
        Route::post('getBankAccountApprovalByUser', 'BankAccountAPIController@getBankAccountApprovalByUser');
        Route::post('getBankAccountApprovedByUser', 'BankAccountAPIController@getBankAccountApprovedByUser');
        Route::get('bankAccountAudit', 'BankAccountAPIController@bankAccountAudit');
        Route::post('bankAccountReopen', 'BankAccountAPIController@bankAccountReopen');
        Route::post('bankAccountReferBack', 'BankAccountAPIController@bankAccountReferBack');
        Route::resource('bank_account_reffered_backs', 'BankAccountRefferedBackAPIController');
        Route::resource('report_template_columns', 'ReportTemplateColumnsAPIController');
        Route::resource('report_template_column_links', 'ReportTemplateColumnLinkAPIController');
        Route::get('getTemplateColumnLinks', 'ReportTemplateColumnLinkAPIController@getTemplateColumnLinks');
        Route::get('reportTemplateFormulaColumn', 'ReportTemplateColumnLinkAPIController@reportTemplateFormulaColumn');
        Route::post('loadColumnTemplate', 'ReportTemplateColumnLinkAPIController@loadColumnTemplate');
        Route::resource('bankAccountReferedBack', 'BankAccountRefferedBackAPIController');
        Route::post('getAccountsReferBackHistory', 'BankAccountRefferedBackAPIController@getAccountsReferBackHistory');

        Route::post('getFinancialYearsByCompany', 'CompanyFinanceYearAPIController@getFinancialYearsByCompany');
        Route::get('getFinanceYearFormData', 'CompanyFinanceYearAPIController@getFinanceYearFormData');
        Route::post('getFinancialPeriodsByYear', 'CompanyFinancePeriodAPIController@getFinancialPeriodsByYear');
        Route::resource('companyFinanceYearPeriodMasters', 'CompanyFinanceYearperiodMasterAPIController');

        Route::resource('outlet_users', 'OutletUsersAPIController');
        Route::post('getAssignedUsersOutlet', 'OutletUsersAPIController@getAssignedUsersOutlet');
        Route::get('getUnAssignUsersByOutlet', 'OutletUsersAPIController@getUnAssignUsersByOutlet');
        Route::post('uploadWarehouseImage', 'WarehouseMasterAPIController@uploadWarehouseImage');

        Route::resource('counter', 'CounterAPIController');
        Route::post('getCountersByCompany', 'CounterAPIController@getCountersByCompany');
        Route::get('getCounterFormData', 'CounterAPIController@getCounterFormData');

        Route::resource('posPaymentGlConfigMasters', 'GposPaymentGlConfigMasterAPIController');
        Route::resource('posPaymentGlConfigDetails', 'GposPaymentGlConfigDetailAPIController');
        Route::post('getPosGlConfigByCompany', 'GposPaymentGlConfigDetailAPIController@getConfigByCompany');
        Route::get('getPosGlConfigFormData', 'GposPaymentGlConfigDetailAPIController@getFormData');
        Route::get('getPosItemSearch', 'ItemMasterAPIController@getPosItemSearch');
        Route::get('getPosShiftDetails', 'ShiftDetailsAPIController@getPosShiftDetails');
        Route::resource('currency_denominations', 'CurrencyDenominationAPIController');
        Route::resource('shift_details', 'ShiftDetailsAPIController');
        Route::get('getPosCustomerSearch', 'CustomerMasterAPIController@getPosCustomerSearch');
        Route::post('getAllNonPosItemsByCompany', 'ItemAssignedAPIController@getAllNonPosItemsByCompany');
        Route::post('getItemsByMainCategoryAndSubCategory', 'ItemAssignedAPIController@getItemsByMainCategoryAndSubCategory');
        Route::post('savePullItemsFromInventory', 'ItemAssignedAPIController@savePullItemsFromInventory');

        Route::post('getAllCompanyEmailSendingPolicy', 'DocumentEmailNotificationDetailAPIController@getAllCompanyEmailSendingPolicy');
        Route::resource('docEmailNotificationMasters', 'DocumentEmailNotificationMasterAPIController');
        Route::resource('docEmailNotificationDetails', 'DocumentEmailNotificationDetailAPIController');
        Route::resource('customerMasterCategories', 'CustomerMasterCategoryAPIController');
        Route::post('getAllCustomerCategories', 'CustomerMasterCategoryAPIController@getAllCustomerCategories');
        Route::get('getNotAssignedCompaniesByCustomerCategory', 'CustomerMasterCategoryAPIController@getNotAssignedCompaniesByCustomerCategory');

        Route::resource('salesPersonMasters', 'SalesPersonMasterAPIController');
        Route::resource('salesPersonTargets', 'SalesPersonTargetAPIController');
        Route::post('getAllSalesPersons', 'SalesPersonMasterAPIController@getAllSalesPersons');
        Route::get('getSalesPersonFormData', 'SalesPersonMasterAPIController@getSalesPersonFormData');
        Route::get('checkSalesPersonLastTarget', 'SalesPersonTargetAPIController@checkSalesPersonLastTarget');
        Route::get('getSalesPersonTargetDetails', 'SalesPersonTargetAPIController@getSalesPersonTargetDetails');

        Route::resource('report_template_field_types', 'ReportTemplateFieldTypeAPIController');
        Route::resource('report_template_cash_banks', 'ReportTemplateCashBankAPIController');
        Route::resource('report_template_documents', 'ReportTemplateDocumentAPIController');

        Route::resource('quotationMasters', 'QuotationMasterAPIController');
        Route::resource('quotationDetails', 'QuotationDetailsAPIController');
        Route::get('getSalesQuotationFormData', 'QuotationMasterAPIController@getSalesQuotationFormData');
        Route::get('getItemsForSalesQuotation', 'QuotationMasterAPIController@getItemsForSalesQuotation');
        Route::get('getSalesQuotationDetails', 'QuotationDetailsAPIController@getSalesQuotationDetails');
        Route::post('storeSalesOrderFromSalesQuotation', 'QuotationDetailsAPIController@storeSalesOrderFromSalesQuotation');
        Route::post('getOrderDetailsForSQ', 'QuotationMasterAPIController@getOrderDetailsForSQ');
        Route::post('getAllSalesQuotation', 'QuotationMasterAPIController@getAllSalesQuotation');
        Route::post('checkItemExists','QuotationMasterAPIController@checkItemExists');
        Route::post('salesQuotationDetailsDeleteAll', 'QuotationDetailsAPIController@salesQuotationDetailsDeleteAll');
        Route::post('getSalesQuotationApprovals', 'QuotationMasterAPIController@getSalesQuotationApprovals');
        Route::post('getApprovedSalesQuotationForUser', 'QuotationMasterAPIController@getApprovedSalesQuotationForUser');
        Route::post('approveSalesQuotation', 'QuotationMasterAPIController@approveSalesQuotation');
        Route::post('rejectSalesQuotation', 'QuotationMasterAPIController@rejectSalesQuotation');
        Route::get('getSalesQuotationMasterRecord', 'QuotationMasterAPIController@getSalesQuotationMasterRecord');
        Route::post('salesQuotationReopen', 'QuotationMasterAPIController@salesQuotationReopen');
        Route::post('salesQuotationVersionCreate', 'QuotationMasterAPIController@salesQuotationVersionCreate');
        Route::post('salesQuotationAmend', 'QuotationMasterAPIController@salesQuotationAmend');
        Route::get('salesQuotationAudit', 'QuotationMasterAPIController@salesQuotationAudit');

        Route::resource('gposInvoices', 'GposInvoiceAPIController');
        Route::get('getInvoiceDetails', 'GposInvoiceAPIController@getInvoiceDetails');
        Route::post('getInvoicesByShift', 'GposInvoiceAPIController@getInvoicesByShift');
        Route::resource('gposInvoiceDetails', 'GposInvoiceDetailAPIController');
        Route::resource('gposInvoicePayments', 'GposInvoicePaymentsAPIController');

        Route::resource('quotationMasterVersions', 'QuotationMasterVersionAPIController');
        Route::resource('quotationVersionDetails', 'QuotationVersionDetailsAPIController');
        Route::post('getSalesQuotationRevisionHistory', 'QuotationMasterVersionAPIController@getSalesQuotationRevisionHistory');
        Route::get('getSQVDetailsHistory', 'QuotationVersionDetailsAPIController@getSQVDetailsHistory');


        Route::resource('quotationDetailsRefferedbacks', 'QuotationDetailsRefferedbackAPIController');
        Route::resource('quotationMasterRefferedbacks', 'QuotationMasterRefferedbackAPIController');
        Route::post('getSalesQuotationAmendHistory', 'QuotationMasterRefferedbackAPIController@getSalesQuotationAmendHistory');
        Route::get('getSQHDetailsHistory', 'QuotationDetailsRefferedbackAPIController@getSQHDetailsHistory');

        Route::resource('report_template_cash_banks', 'ReportTemplateCashBankAPIController');
        Route::resource('report_template_numbers', 'ReportTemplateNumbersAPIController');
        Route::get('printInvoice', 'GposInvoiceAPIController@printInvoice');

        Route::resource('stockAdjustmentRefferedBack', 'StockAdjustmentRefferedBackAPIController');
        Route::resource('sAdjustmentDetailsRefferedBack', 'StockAdjustmentDetailsRefferedBackAPIController');
        Route::post('getReferBackHistoryByStockAdjustments', 'StockAdjustmentRefferedBackAPIController@getReferBackHistoryByStockAdjustments');
        Route::get('getSADetailsReferBack', 'StockAdjustmentDetailsRefferedBackAPIController@getSADetailsReferBack');

        Route::resource('report_template_cash_banks', 'ReportTemplateCashBankAPIController');
        Route::resource('report_template_employees', 'ReportTemplateEmployeesAPIController');
        Route::post('getReportTemplateAssignedEmployee', 'ReportTemplateEmployeesAPIController@getReportTemplateAssignedEmployee');

        // console jv
        Route::resource('console_j_v_masters', 'ConsoleJVMasterAPIController');
        Route::resource('console_j_v_details', 'ConsoleJVDetailAPIController');
        Route::post('getAllConsoleJV', 'ConsoleJVMasterAPIController@getAllConsoleJV');
        Route::post('consoleJVReopen', 'ConsoleJVMasterAPIController@consoleJVReopen');
        Route::get('getConsoleJVGL', 'ConsoleJVMasterAPIController@getConsoleJVGL');
        Route::get('getConsoleJVMasterFormData', 'ConsoleJVMasterAPIController@getConsoleJVMasterFormData');
        Route::get('getConsoleJVDetailByMaster', 'ConsoleJVDetailAPIController@getConsoleJVDetailByMaster');
        Route::post('deleteAllConsoleJVDet', 'ConsoleJVDetailAPIController@deleteAllConsoleJVDet');
        Route::post('getConsoleJvApproval', 'ConsoleJVMasterAPIController@getConsoleJvApproval');
        Route::post('getApprovedConsoleJvForCurrentUser', 'ConsoleJVMasterAPIController@getApprovedConsoleJvForCurrentUser');
        Route::post('approveConsoleJV', 'ConsoleJVMasterAPIController@approveConsoleJV');
        Route::post('rejectConsoleJV', 'ConsoleJVMasterAPIController@rejectConsoleJV');

        Route::resource('customer_contact_details', 'CustomerContactDetailsAPIController');
        Route::get('contactDetailsByCustomer', 'CustomerContactDetailsAPIController@contactDetailsByCustomer');
        Route::resource('currency_conversion_histories', 'CurrencyConversionHistoryAPIController');
        Route::get('minAndMaxAnalysis', 'InventoryReportAPIController@minAndMaxAnalysis');

        Route::post('getAllProcurementCategory', 'TenderProcurementCategoryController@getAllProcurementCategory');
        Route::resource('procurement_categories', 'TenderProcurementCategoryController');
        Route::resource('document_attachment_type', 'DocumentAttachmentTypeController');
        Route::post('get_all_document_attachment_type', 'DocumentAttachmentTypeController@getAllDocumentAttachmentTypes');
        Route::post('remove_document_attachment_type', 'DocumentAttachmentTypeController@removeDocumentAttachmentType');
        Route::post('get_all_calendar_dates', 'TenderCalendarDatesController@getAllCalendarDates');
        Route::resource('calendar_date', 'TenderCalendarDatesController');

        /* For Profile -> Profile */
        Route::get('getProfileDetails', 'EmployeeAPIController@getProfileDetails');

        Route::resource('genders', 'GenderAPIController');

        Route::resource('maritial_statuses', 'MaritialStatusAPIController');

        Route::resource('religions', 'ReligionAPIController');

        Route::resource('salary_process_details', 'SalaryProcessDetailAPIController');

        Route::resource('leave_data_masters', 'LeaveDataMasterAPIController');

        Route::resource('leave_masters', 'LeaveMasterAPIController');

        Route::resource('calender_masters', 'CalenderMasterAPIController');

        Route::resource('schedule_masters', 'ScheduleMasterAPIController');

        Route::resource('leave_data_details', 'LeaveDataDetailAPIController');

        Route::resource('leave_application_types', 'LeaveApplicationTypeAPIController');

        Route::resource('leave_document_approveds', 'LeaveDocumentApprovedAPIController');

        Route::resource('employee_managers', 'EmployeeManagersAPIController');

        Route::resource('document_managements', 'DocumentManagementAPIController');

        Route::resource('hrms_document_attachments', 'HrmsDocumentAttachmentsAPIController');


        /* For Profile -> Payslip */
        Route::get('getPeriodsForPayslip', 'EmployeePayslipAPIController@getPeriodsForPayslip');
        Route::get('getEmployeePayslip', 'EmployeePayslipAPIController@getEmployeePayslip');

        /* For Profile -> Expenses Claim */
        Route::get('getExpenseClaim', 'ExpenseClaimAPIController@getExpenseClaim');
        Route::get('getExpenseClaimHistory', 'ExpenseClaimAPIController@getExpenseClaimHistory');
        Route::get('getExpenseClaimDepartment', 'ExpenseClaimAPIController@getExpenseClaimDepartment');
        Route::get('getExpenseDropDownData', 'ExpenseClaimAPIController@getExpenseDropDownData');
        Route::post('saveExpenseClaimDetails', 'ExpenseClaimDetailsAPIController@saveExpenseClaimDetailsSingle');
        Route::post('saveExpenseClaimAttachments', 'ExpenseClaimDetailsAPIController@saveAttachments');
        Route::get('getExpenseClaimDetails', 'ExpenseClaimAPIController@getExpenseClaimDetails');

        /* For Profile -> Leave Application */
        Route::get('getLeaveHistory', 'LeaveDataMasterAPIController@getLeaveHistory');
        Route::get('getLeaveTypes', 'LeaveMasterAPIController@getLeaveTypes');
        Route::get('getLeaveAvailability', 'LeaveDataMasterAPIController@getLeaveAvailability');
        Route::post('saveLeaveDetails', 'LeaveDataMasterAPIController@saveLeaveDetails');
        Route::post('updateLeaveDetails', 'LeaveDataMasterAPIController@updateLeaveDetails');
        Route::get('getLeaveDetails', 'LeaveDataMasterAPIController@getLeaveDetails');
        Route::get('downloadHrmsFile', 'HrmsDocumentAttachmentsAPIController@downloadFile');

        /*Company Document Attachments*/
        Route::post('getAllCompanyDocumentAttachment', 'CompanyDocumentAttachmentAPIController@getAllCompanyDocumentAttachment');
        Route::get('getCompanyDocumentFilterOptions', 'CompanyDocumentAttachmentAPIController@getCompanyDocumentFilterOptions');

        Route::post('updateGRVLogistic', 'ProcumentOrderAPIController@updateGRVLogistic');

        /* ChequeRegister */
        Route::resource('cheque_registers', 'ChequeRegisterAPIController');

        Route::resource('cheque_register_details', 'ChequeRegisterDetailAPIController');

        Route::get('getChequeRegisterFormData', 'ChequeRegisterAPIController@getChequeRegisterFormData');
        Route::get('getChequeRegisterByMasterID', 'ChequeRegisterAPIController@getChequeRegisterByMasterID');

        Route::post('getAllChequeRegistersByCompany', 'ChequeRegisterAPIController@getAllChequeRegistersByCompany');
        Route::get('chequeRegisterDetailsAudit', 'ChequeRegisterDetailAPIController@chequeRegisterDetailsAudit');
        Route::post('getAllChequeRegisterDetails', 'ChequeRegisterDetailAPIController@getAllChequeRegisterDetails');
        Route::get('getAllUnusedCheckDetails', 'ChequeRegisterDetailAPIController@getAllUnusedCheckDetails');
        Route::post('chequeRegisterDetailCancellation', 'ChequeRegisterDetailAPIController@chequeRegisterDetailCancellation');
        Route::post('chequeRegisterDetailSwitch', 'ChequeRegisterDetailAPIController@chequeRegisterDetailSwitch');
        Route::get('getChequeSwitchFormData', 'ChequeRegisterDetailAPIController@getChequeSwitchFormData');
        Route::post('exportChequeRegistry', 'ChequeRegisterAPIController@exportChequeRegistry');
        Route::get('revertChequePrint', 'BankLedgerAPIController@revertChequePrint');
        Route::get('getCancelledDetails', 'PurchaseRequestAPIController@getCancelledDetails');
        Route::get('getClosedDetails', 'PurchaseRequestAPIController@getClosedDetails');
        Route::get('getQtyOrderDetails', 'PurchaseRequestDetailsAPIController@getQtyOrderDetails');
        Route::get('getWarehouseStockDetails', 'PurchaseRequestDetailsAPIController@getWarehouseStockDetails');
        Route::post('updateQtyOnOrder', 'PurchaseRequestDetailsAPIController@updateQtyOnOrder');
        Route::post('prItemsUpload', 'PurchaseRequestDetailsAPIController@prItemsUpload');

        Route::post('purchase-request-add-all-items', 'PurchaseRequestDetailsAPIController@addAllItemsToPurchaseRequest');

        Route::get('copy_pr/{id}', 'PurchaseRequestDetailsAPIController@copyPr');
        
        Route::resource('allocation_masters', 'AllocationMasterAPIController');

        Route::resource('coa_allocation_masters', 'ChartOfAccountAllocationMasterAPIController');

        Route::resource('coa_allocation_details', 'ChartOfAccountAllocationDetailAPIController');

        Route::get('getAllocationConfigurationAssignFormData', 'ChartOfAccountAllocationMasterAPIController@getAllocationConfigurationAssignFormData');

        Route::get('getLeaveTypeWithBalance', 'LeaveDataMasterAPIController@getLeaveTypeWithBalance');

        Route::resource('hrms_leave_accrual_masters', 'HRMSLeaveAccrualMasterAPIController');

        Route::resource('hrms_leave_accrual_details', 'HRMSLeaveAccrualDetailAPIController');

        Route::resource('hrms_period_masters', 'HRMSPeriodMasterAPIController');

        Route::resource('hrms_personal_documents', 'HRMSPersonalDocumentsAPIController');

        Route::get('getHRMSApprovals', 'LeaveDocumentApprovedAPIController@getHRMSApprovals');
        Route::get('getLeaveApproval', 'LeaveDocumentApprovedAPIController@getLeaveApproval');
        Route::post('leaveReferBack', 'LeaveDocumentApprovedAPIController@leaveReferBack');
        Route::post('approveLeave', 'LeaveDocumentApprovedAPIController@approveLeave');
        Route::resource('hrms_leave_accrual_policy_types', 'HRMSLeaveAccrualPolicyTypeAPIController');

        Route::resource('employee_department_delegations', 'employeeDepartmentDelegationAPIController');
        Route::post('approveHRMSDocument', 'LeaveDocumentApprovedAPIController@approveHRMSDocument');
        Route::post('referBackHRMSDocument', 'LeaveDocumentApprovedAPIController@referBackHRMSDocument');

        Route::get('getFilteredDebitNote', 'CreditNoteAPIController@getFilteredDebitNote');
        Route::get('getFilteredDirectCustomerInvoice', 'BookInvSuppMasterAPIController@getFilteredDirectCustomerInvoice');

        Route::put('creditNoteLocalUpdate/{id}', 'CreditNoteAPIController@creditNoteLocalUpdate');
        Route::put('creditNoteReportingUpdate/{id}','CreditNoteAPIController@creditNoteReportingUpdate');

        Route::post('addBudgetAdjustment', 'BudgetAdjustmentAPIController@addBudgetAdjustment');

        Route::get('getDocumentAmendFormData', 'GeneralLedgerAPIController@getDocumentAmendFormData');
        Route::post('updateGLEntries', 'GeneralLedgerAPIController@updateGLEntries');
        Route::post('getDocumentAmendFromGL', 'GeneralLedgerAPIController@getDocumentAmendFromGL');
        Route::post('changePostingDate', 'GeneralLedgerAPIController@changePostingDate');
        Route::get('getUserCountData', 'EmployeeAPIController@getUserCountData');
        Route::post('getItemSavingReport', 'ReportAPIController@getItemSavingReport');
        Route::post('exportExcelSavingReport', 'ReportAPIController@exportExcelSavingReport');
        Route::post('generateSegmentGlReport', 'GeneralLedgerAPIController@generateSegmentGlReport');
        Route::post('generateSegmentGlReportExcel', 'GeneralLedgerAPIController@generateSegmentGlReportExcel');

        Route::resource('customer_invoice_trackings', 'CustomerInvoiceTrackingAPIController');

        Route::get('getBatchSubmissionFormData', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionFormData');
        Route::get('getContractServiceLine', 'CustomerInvoiceTrackingAPIController@getContractServiceLine');
        Route::post('getAllBatchSubmissionByCompany', 'CustomerInvoiceTrackingAPIController@getAllBatchSubmissionByCompany');
        Route::post('getCustomerInvoicesForBatchSubmission', 'CustomerInvoiceTrackingAPIController@getCustomerInvoicesForBatchSubmission');
        Route::post('addBatchSubmitDetails', 'CustomerInvoiceTrackingDetailAPIController@addBatchSubmitDetails');
        Route::get('getItemsByBatchSubmission', 'CustomerInvoiceTrackingDetailAPIController@getItemsByBatchSubmission');
        Route::post('exportBatchSubmissionDetails', 'CustomerInvoiceTrackingAPIController@exportBatchSubmissionDetails');
        Route::get('getInvoiceTrackerReportFilterData', 'AccountsReceivableReportAPIController@getInvoiceTrackerReportFilterData');
        Route::post('getContractByCustomer', 'AccountsReceivableReportAPIController@getContractByCustomer');
        Route::post('generateInvoiceTrackingReport', 'AccountsReceivableReportAPIController@generateInvoiceTrackingReport');

        Route::get('getItemByCustomerInvoiceItemDetail', 'CustomerInvoiceItemDetailsAPIController@getItemByCustomerInvoiceItemDetail');
        Route::get('getDeliveryTerms', 'CustomerInvoiceItemDetailsAPIController@getDeliveryTerms');
        Route::post('getDeliveryTermsFormData', 'CustomerInvoiceItemDetailsAPIController@getDeliveryTermsFormData');

        Route::get('getINVTrackingFormData', 'CustomerInvoiceTrackingAPIController@getINVTrackingFormData');
        Route::post('updateAllInvoiceTrackingDetail', 'CustomerInvoiceTrackingAPIController@updateAllInvoiceTrackingDetail');
        Route::post('deleteAllInvoiceTrackingDetail', 'CustomerInvoiceTrackingAPIController@deleteAllInvoiceTrackingDetail');

        Route::get('cancelGRVPreCheck', 'GRVMasterAPIController@cancelGRVPreCheck');
        Route::get('reverseGRVPreCheck', 'GRVMasterAPIController@reverseGRVPreCheck');
        Route::post('cancelGRV', 'GRVMasterAPIController@cancelGRV');
        Route::post('reverseGRV', 'GRVMasterAPIController@reverseGRV');

        Route::post('getUnassignedGLForReportTemplate', 'ReportTemplateDetailsAPIController@getUnassignedGLForReportTemplate');


        Route::resource('pre_defined_report_templates', 'PreDefinedReportTemplateAPIController');
        Route::resource('erp_print_template_masters', 'ErpPrintTemplateMasterAPIController');
        Route::resource('erp_document_templates', 'ErpDocumentTemplateAPIController');
        Route::resource('user_rights', 'UserRightsAPIController');
        Route::resource('lpt_permissions', 'LptPermissionAPIController');
        Route::resource('client_performa_app_types', 'ClientPerformaAppTypeAPIController');
        Route::resource('customer_invoice_tracking_details', 'CustomerInvoiceTrackingDetailAPIController');
        Route::resource('segment_rights', 'SegmentRightsAPIController');
        Route::post('getSegmentRightEmployees', 'SegmentRightsAPIController@getSegmentRightEmployees');
        Route::resource('service_lines', 'ServiceLineAPIController');
        Route::get('getServiceLineByCompany', 'ServiceLineAPIController@getServiceLineByCompany');
        Route::resource('warehouse_rights', 'WarehouseRightsAPIController');
        Route::post('getWarehouseRightEmployees', 'WarehouseRightsAPIController@getWarehouseRightEmployees');
        Route::resource('customer_invoice_item_details', 'CustomerInvoiceItemDetailsAPIController');
        Route::resource('chartOfAccount/allocation/histories', 'ChartOfAccountAllocationDetailHistoryAPIController');
        Route::resource('hrms_department_masters', 'HrmsDepartmentMasterAPIController');
        Route::resource('secondary_companies', 'SecondaryCompanyAPIController');

        Route::resource('warehouse_sub_levels', 'WarehouseSubLevelsAPIController');
        Route::post('getAllWarehouseSubLevels', 'WarehouseSubLevelsAPIController@getAllWarehouseSubLevels');
        Route::get('getSubLevelsByWarehouse', 'WarehouseSubLevelsAPIController@getSubLevelsByWarehouse');

        Route::resource('supplier_catalog_masters', 'SupplierCatalogMasterAPIController');

        Route::resource('supplier_catalog_details', 'SupplierCatalogDetailAPIController');

        Route::post('getAllCatalogsByCompany', 'SupplierCatalogMasterAPIController@getAllCatalogsByCompany');
        Route::get('getItemsOptionsSupplierCatalog', 'SupplierCatalogMasterAPIController@getItemsOptionsSupplierCatalog');
        Route::post('getSupplierCatalogDetailBySupplierItem', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierItem');

        Route::resource('customer_catalog_masters', 'CustomerCatalogMasterAPIController');

        Route::resource('customer_catalog_details', 'CustomerCatalogDetailAPIController');

        Route::post('getAllCustomerCatalogsByCompany', 'CustomerCatalogMasterAPIController@getAllCustomerCatalogsByCompany');
        Route::get('getItemsOptionsCustomerCatalog', 'CustomerCatalogMasterAPIController@getItemsOptionsCustomerCatalog');
        Route::post('getCustomerCatalogDetailByCustomerItem', 'CustomerCatalogMasterAPIController@getCustomerCatalogDetailByCustomerItem');
        Route::get('getAssignedCurrenciesByCustomer', 'CustomerCatalogMasterAPIController@getAssignedCurrenciesByCustomer');

        Route::resource('report_column_templates', 'ReportColumnTemplateAPIController');
        Route::post('getSupplierCatalogDetailBySupplierAllItem', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierAllItem');
        Route::post('getSupplierCatalogDetailBySupplierItemForPo', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierItemForPo');
        Route::resource('dashboard_widget_masters', 'DashboardWidgetMasterAPIController');
        Route::get('getWidgetMasterFormData', 'DashboardWidgetMasterAPIController@getWidgetMasterFormData');
        Route::get('getDashboardDepartment', 'DashboardWidgetMasterAPIController@getDashboardDepartment');
        Route::get('getDashboardWidget', 'DashboardWidgetMasterAPIController@getDashboardWidget');
        Route::post('getCustomWidgetGraphData', 'DashboardWidgetMasterAPIController@getCustomWidgetGraphData');
        Route::post('logoutApiUser', 'FcmTokenAPIController@logoutApiUser');
        Route::post('getCurrentHomeUrl', 'FcmTokenAPIController@redirectHome');
        Route::resource('delivery_orders', 'DeliveryOrderAPIController');
        Route::post('validateDeliveryOrder','DeliveryOrderAPIController@validateDeliveryOrder');
        Route::post('getCommonFormData','DeliveryOrderAPIController@getCommonFormData');
        Route::post('uploadItemsDeliveryOrder','DeliveryOrderDetailAPIController@uploadItemsDeliveryOrder');
        Route::post('uploadItems','QuotationMasterAPIController@poItemsUpload');

        
        Route::post('getAllDeliveryOrder', 'DeliveryOrderAPIController@getAllDeliveryOrder');
        Route::post('saveDeliveryOrderTaxDetails', 'DeliveryOrderDetailAPIController@saveDeliveryOrderTaxDetail');
        Route::get('getDeliveryOrderFormData', 'DeliveryOrderAPIController@getDeliveryOrderFormData');
        Route::resource('delivery_order_details', 'DeliveryOrderDetailAPIController');

        Route::get('salesQuotationForDO', 'DeliveryOrderAPIController@salesQuotationForDO');
        Route::get('getSalesQuoatationDetailForDO', 'DeliveryOrderAPIController@getSalesQuoatationDetailForDO');
        Route::post('cancelQuatation', 'QuotationMasterAPIController@cancelQuatation');
        Route::post('closeQuatation', 'QuotationMasterAPIController@closeQuatation');

        Route::post('getDeliveryOrderApprovals', 'DeliveryOrderAPIController@getDeliveryOrderApprovals');
        Route::post('getApprovedDeliveryOrderForUser', 'DeliveryOrderAPIController@getApprovedDeliveryOrderForUser');
        Route::post('approveDeliveryOrder', 'DeliveryOrderAPIController@approveDeliveryOrder');
        Route::post('rejectDeliveryOrder', 'DeliveryOrderAPIController@rejectDeliveryOrder');

        Route::post('storeDeliveryDetailFromSalesQuotation', 'DeliveryOrderDetailAPIController@storeDeliveryDetailFromSalesQuotation');
        Route::get('deliveryOrderAudit', 'DeliveryOrderAPIController@deliveryOrderAudit');
        Route::get('checkEOSPolicyAndSupplier', 'ProcumentOrderAPIController@checkEOSPolicyAndSupplier');
        Route::get('downloadPoItemUploadTemplate', 'ProcumentOrderAPIController@downloadPoItemUploadTemplate');
        Route::get('downloadQuotationItemUploadTemplate', 'QuotationMasterAPIController@downloadQuotationItemUploadTemplate');
        Route::get('downloadDeliveryOrderUploadTemplate', 'DeliveryOrderAPIController@downloadQuotationItemUploadTemplate');
        Route::post('poItemsUpload', 'ProcumentOrderAPIController@poItemsUpload');
        Route::post('checkBudgetCutOffForPo', 'ProcumentOrderAPIController@checkBudgetCutOffForPo');


        Route::post('sales-order/is-link-item', 'DeliveryOrderAPIController@isLinkItem');

        

        Route::resource('pre_defined_report_templates', 'PreDefinedReportTemplateAPIController');

        Route::resource('erp_print_template_masters', 'ErpPrintTemplateMasterAPIController');

        Route::resource('erp_document_templates', 'ErpDocumentTemplateAPIController');

        Route::resource('user_rights', 'UserRightsAPIController');

        Route::resource('lpt_permissions', 'LptPermissionAPIController');

        Route::resource('client_performa_app_types', 'ClientPerformaAppTypeAPIController');


        Route::resource('customer_invoice_tracking_details', 'CustomerInvoiceTrackingDetailAPIController');


        Route::resource('segment_rights', 'SegmentRightsAPIController');
        Route::post('getSegmentRightEmployees', 'SegmentRightsAPIController@getSegmentRightEmployees');

        Route::resource('service_lines', 'ServiceLineAPIController');
        Route::get('getServiceLineByCompany', 'ServiceLineAPIController@getServiceLineByCompany');

        Route::resource('warehouse_rights', 'WarehouseRightsAPIController');
        Route::post('getWarehouseRightEmployees', 'WarehouseRightsAPIController@getWarehouseRightEmployees');

        Route::resource('customer_invoice_item_details', 'CustomerInvoiceItemDetailsAPIController');


        //Route::resource('chart_of_account_allocation_detail_histories', 'ChartOfAccountAllocationDetailHistoryAPIController');

        Route::resource('hrms_department_masters', 'HrmsDepartmentMasterAPIController');


        Route::resource('secondary_companies', 'SecondaryCompanyAPIController');

        Route::resource('report_column_template_details', 'ReportColumnTemplateDetailAPIController');

        Route::get('deliveryOrderForCustomerInvoice','CustomerInvoiceItemDetailsAPIController@deliveryOrderForCustomerInvoice');
        Route::get('getDeliveryOrderDetailForInvoice','CustomerInvoiceItemDetailsAPIController@getDeliveryOrderDetailForInvoice');
        Route::post('storeInvoiceDetailFromDeliveryOrder','CustomerInvoiceItemDetailsAPIController@storeInvoiceDetailFromDeliveryOrder');

        Route::get('getDeliveryOrderRecord','CustomerInvoiceItemDetailsAPIController@getDeliveryOrderRecord');
        Route::get('getSupplierCatalog','ItemMasterAPIController@getSupplierByCatalogItemDetail');

        Route::post('deliveryOrderReopen', 'DeliveryOrderAPIController@deliveryOrderReopen');
        Route::post('getInvoiceDetailsForDO', 'DeliveryOrderAPIController@getInvoiceDetailsForDO');

        Route::get('getDeliveryOrderAmendHistory', 'DeliveryOrderRefferedbackAPIController@getDeliveryOrderAmendHistory');
        Route::post('getDeliveryOrderAmend', 'DeliveryOrderAPIController@getDeliveryOrderAmend');

        Route::resource('do_refferedbacks', 'DeliveryOrderRefferedbackAPIController');

        Route::resource('do_detail_refferedbacks', 'DeliveryOrderDetailRefferedbackAPIController');

        Route::get('checkDocumentAttachmentPolicy', 'CompanyDocumentAttachmentAPIController@checkDocumentAttachmentPolicy');
        Route::resource('customer_invoice_status_types', 'CustomerInvoiceStatusTypeAPIController');
        Route::resource('tax_masters', 'TaxMasterAPIController');
        Route::resource('fcm_tokens', 'FcmTokenAPIController');
        Route::resource('user_activity_logs', 'UserActivityLogAPIController');

        Route::get('getICFilterFormData', 'FinancialReportAPIController@getICFilterFormData');
        Route::post('validateICReport', 'FinancialReportAPIController@validateICReport');
        Route::post('generateICReport', 'FinancialReportAPIController@generateICReport');
        Route::post('exportICReport', 'FinancialReportAPIController@exportICReport');
        Route::post('getICDrillDownData', 'FinancialReportAPIController@getICDrillDownData');

        Route::resource('mobile_no_pools', 'MobileNoPoolAPIController');
        Route::post('getAllMobileNo', 'MobileNoPoolAPIController@getAllMobileNo');

        Route::resource('mobile_masters', 'MobileMasterAPIController');
        Route::post('getAllMobileMaster', 'MobileMasterAPIController@getAllMobileMaster');
        Route::get('getMobileMasterFormData', 'MobileMasterAPIController@getMobileMasterFormData');

        Route::resource('mobile_bill_masters', 'MobileBillMasterAPIController');
        Route::post('getAllMobileBill', 'MobileBillMasterAPIController@getAllMobileBill');
        Route::get('getMobileBillFormData', 'MobileBillMasterAPIController@getMobileBillFormData');

        Route::resource('mobile_bill_summaries', 'MobileBillSummaryAPIController');
        Route::post('importMobileBillDocument', 'MobileBillSummaryAPIController@importMobileBillDocument');

        Route::resource('mobile_details', 'MobileDetailAPIController');

        Route::resource('quotation_status_masters', 'QuotationStatusMasterAPIController');

        Route::resource('quotation_statuses', 'QuotationStatusAPIController');
        Route::get('getQuotationStatus', 'QuotationStatusAPIController@getQuotationStatus');
        Route::post('mobileSummaryDetailDelete', 'MobileBillMasterAPIController@mobileSummaryDetailDelete');

        Route::resource('employee_mobile_bill_masters', 'EmployeeMobileBillMasterAPIController');
        Route::post('generateEmployeeBill', 'EmployeeMobileBillMasterAPIController@generateEmployeeBill');

        Route::post('getAllMobileBillSummaries', 'MobileBillSummaryAPIController@getAllMobileBillSummaries');
        Route::post('getAllMobileBillDetail', 'MobileDetailAPIController@getAllMobileBillDetail');
        Route::post('getAllEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@getAllEmployeeMobileBill');

        Route::get('creditNoteReceiptStatus', 'CreditNoteAPIController@creditNoteReceiptStatus');
        Route::post('approvalPreCheckCreditNote', 'CreditNoteAPIController@approvalPreCheckCreditNote');
        Route::post('getMobileBillReport', 'MobileBillMasterAPIController@getMobileBillReport');
        Route::post('validateMobileReport', 'MobileBillMasterAPIController@validateMobileReport');
        Route::get('getMobileReportFormData', 'MobileBillMasterAPIController@getMobileReportFormData');
        Route::post('exportMobileReport', 'MobileBillMasterAPIController@exportMobileReport');
        Route::get('getInvoiceDetailsForDeliveryOrderPrintView', 'DeliveryOrderAPIController@getInvoiceDetailsForDeliveryOrderPrintView');

        Route::resource('custom_report_types', 'CustomReportTypeAPIController');
        Route::resource('custom_report_masters', 'CustomReportMasterAPIController');
        Route::resource('custom_report_columns', 'CustomReportColumnsAPIController');
        Route::resource('custom_user_reports', 'CustomUserReportsAPIController');
        Route::post('getCustomReportsByUser', 'CustomUserReportsAPIController@getCustomReportsByUser');
        Route::post('customReportView', 'CustomUserReportsAPIController@customReportView');
        Route::post('exportCustomReport', 'CustomUserReportsAPIController@exportCustomReport');
        Route::resource('custom_user_report_columns', 'CustomUserReportColumnsAPIController');
        Route::resource('custom_filters_columns', 'CustomFiltersColumnAPIController');
        Route::resource('custom_report_employees', 'CustomReportEmployeesAPIController');
        Route::post('getCustomReportAssignedEmployee', 'CustomReportEmployeesAPIController@getCustomReportAssignedEmployee');
        Route::get('getUnAssignEmployeeByReport', 'CustomReportEmployeesAPIController@getEmployees');
        Route::resource('custom_user_report_summarizes', 'CustomUserReportSummarizeAPIController');


        Route::get('salesQuotationForCustomerInvoice','QuotationMasterAPIController@salesQuotationForCustomerInvoice');
        Route::get('getSalesQuotationDetailForInvoice','QuotationDetailsAPIController@getSalesQuotationDetailForInvoice');
        Route::post('storeInvoiceDetailFromSalesQuotation','CustomerInvoiceItemDetailsAPIController@storeInvoiceDetailFromSalesQuotation');
        Route::post('validateCustomerInvoiceDetails','CustomerInvoiceItemDetailsAPIController@validateCustomerInvoiceDetails');

       
        Route::get('getSalesQuotationRecord','QuotationMasterAPIController@getSalesQuotationRecord');
        Route::post('getInvoiceDetailsForSQ', 'QuotationMasterAPIController@getInvoiceDetailsForSQ');
        Route::get('salesQuotationForSO', 'QuotationMasterAPIController@salesQuotationForSO');
        Route::get('getSalesQuoatationDetailForSO', 'QuotationMasterAPIController@getSalesQuoatationDetailForSO');
        Route::post('getDeliveryDetailsForSQ', 'DeliveryOrderAPIController@getDeliveryDetailsForSQ');
        Route::post('mapLineItemQo', 'QuotationDetailsAPIController@mapLineItemQo');



        Route::get('downloadSummaryTemplate', 'MobileBillSummaryAPIController@downloadSummaryTemplate');
        Route::get('downloadDetailTemplate', 'MobileDetailAPIController@downloadDetailTemplate');
        Route::post('getCompaniesByGroup', 'CompanyAPIController@getCompaniesByGroup');
        Route::post('getBillMastersByCompany', 'MobileBillMasterAPIController@getBillMastersByCompany');
        Route::post('exportEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@exportEmployeeMobileBill');
        Route::post('grvMarkupUpdate', 'GRVDetailsAPIController@grvMarkupUpdate');

        Route::resource('tax_vat_categories', 'TaxVatCategoriesAPIController');
        Route::post('getAllVatCategories', 'TaxVatCategoriesAPIController@getAllVatCategories');
        Route::get('getVatCategoriesFormData', 'TaxVatCategoriesAPIController@getVatCategoriesFormData');
        Route::get('getVatCategoryFormData', 'TaxVatCategoriesAPIController@getVatCategoryFormData');

        Route::resource('tax_vat_main_categories', 'TaxVatMainCategoriesAPIController');

        Route::post('getAllVatMainCategories', 'TaxVatMainCategoriesAPIController@getAllVatMainCategories');
        Route::post('grvMarkupfinalyze', 'GRVMasterAPIController@grvMarkupfinalyze');


        Route::post('getDigitalStamps', 'CompanyAPIController@getDigitalStamps');
        Route::post('uploadDigitalStamp', 'CompanyAPIController@uploadDigitalStamp');
        Route::post('updateDefaultStamp', 'CompanyAPIController@updateDefaultStamp');

        Route::resource('company_digital_stamps', 'CompanyDigitalStampAPIController');


        Route::resource('ci_item_details_refferedbacks', 'CustomerInvoiceItemDetailsRefferedbackAPIController');

        Route::get('getVatSubCategoryItemAssignFromData', 'TaxVatCategoriesAPIController@getVatSubCategoryItemAssignFromData');
        Route::post('getAllVatSubCategoryItemAssign', 'TaxVatCategoriesAPIController@getAllVatSubCategoryItemAssign');
        Route::post('assignVatSubCategoryToItem', 'TaxVatCategoriesAPIController@assignVatSubCategoryToItem');
        Route::post('removeAssignedItemFromVATSubCategory', 'TaxVatCategoriesAPIController@removeAssignedItemFromVATSubCategory');
        Route::post('updateItemVatCategories', 'TaxVatCategoriesAPIController@updateItemVatCategories');

        Route::post('generateSalesMarketReport', 'SalesMarketingReportAPIController@generateReport');
        Route::post('generateSalesMarketReportSoldQty', 'SalesMarketingReportAPIController@generateSoldQty');
        Route::post('validateSalesMarketReport', 'SalesMarketingReportAPIController@validateReport');
        Route::post('exportSalesMarketReport', 'SalesMarketingReportAPIController@exportReport');
        Route::post('getSalesMarketFilterData', 'SalesMarketingReportAPIController@getSalesMarketFilterData');
        Route::get('getSalesAnalysisFilterData', 'SalesMarketingReportAPIController@getSalesAnalysisFilterData');

        Route::post('reportSoToReceipt', 'SalesMarketingReportAPIController@reportSoToReceipt');
        Route::post('exportSoToReceiptReport', 'SalesMarketingReportAPIController@exportSoToReceiptReport');
        Route::get('reportSoToReceiptFilterOptions', 'SalesMarketingReportAPIController@reportSoToReceiptFilterOptions');

        Route::post('getUserActivityLog', 'UserActivityLogAPIController@getViewLog');

        Route::post('assetCostingRemove', 'FixedAssetMasterAPIController@assetCostingRemove');


        Route::resource('sales_returns', 'SalesReturnAPIController');
        Route::post('getAllSalesReturn', 'SalesReturnAPIController@getAllSalesReturn');
        Route::post('storeReturnDetailFromSIDO', 'SalesReturnAPIController@storeReturnDetailFromSIDO');
        Route::get('deliveryNoteForForSR', 'SalesReturnAPIController@deliveryNoteForForSR');
        Route::get('getSalesInvoiceDeliveryOrderDetail', 'SalesReturnAPIController@getSalesInvoiceDeliveryOrderDetail');
        Route::get('getSalesReturnRecord', 'SalesReturnAPIController@getSalesReturnRecord');
        Route::get('salesReturnAudit', 'SalesReturnAPIController@salesReturnAudit');
        Route::resource('sales_return_details', 'SalesReturnDetailAPIController');

        Route::post('getSalesReturnApprovals', 'SalesReturnAPIController@getSalesReturnApprovals');
        Route::post('salesReturnReopen', 'SalesReturnAPIController@salesReturnReopen');
        Route::post('getSalesReturnAmend', 'SalesReturnAPIController@getSalesReturnAmend');
        Route::post('approveSalesReturn', 'SalesReturnAPIController@approveSalesReturn');
        Route::post('rejectSalesReturn', 'SalesReturnAPIController@rejectSalesReturn');
        Route::post('getSalesReturnDetailsForDO', 'SalesReturnAPIController@getSalesReturnDetailsForDO');
        Route::post('getSalesReturnDetailsForSI', 'SalesReturnAPIController@getSalesReturnDetailsForSI');
        Route::post('getApprovedSalesReturnForUser', 'SalesReturnAPIController@getApprovedSalesReturnForUser');

        Route::resource('grv_details_prns', 'GrvDetailsPrnAPIController');
        Route::resource('so_payment_terms', 'SoPaymentTermsAPIController');
        Route::get('getSalesOrderPaymentTerms', 'SoPaymentTermsAPIController@getSalesOrderPaymentTerms');
        Route::resource('sales_order_adv_payments', 'SalesOrderAdvPaymentAPIController');
        Route::get('soPaymentTermsAdvanceDetailView', 'SalesOrderAdvPaymentAPIController@soPaymentTermsAdvanceDetailView');
        Route::get('getSoLogisticPrintDetail', 'SalesOrderAdvPaymentAPIController@getSoLogisticPrintDetail');


        Route::post('checkBRVDocumentActive', 'CustomerReceivePaymentAPIController@checkBRVDocumentActive');
        Route::get('getADVPaymentForBRV', 'CustomerReceivePaymentAPIController@getADVPaymentForBRV');

        Route::resource('advance_receipt_details', 'AdvanceReceiptDetailsAPIController');
        Route::get('getADVPReceiptDetails', 'AdvanceReceiptDetailsAPIController@getADVPReceiptDetails');
        Route::post('deleteAllADVReceiptDetail', 'AdvanceReceiptDetailsAPIController@deleteAllADVReceiptDetail');


        Route::get('getPRDetailsAmendHistory', 'PurchaseReturnDetailsRefferedBackAPIController@getPRDetailsAmendHistory');
        Route::resource('prMasterRefferedbacksCRUD', 'PurchaseReturnMasterRefferedBackAPIController');
        // Route::resource('purchase_return_details_reffered_backs', 'PurchaseReturnDetailsRefferedBackAPIController');

        Route::post('getAllAttachments', 'DocumentAttachmentsAPIController@getAllAttachments');
        Route::get('getAttachmentFormData', 'DocumentAttachmentsAPIController@getAttachmentFormData');

        Route::post('amendSalesQuotationReview', 'QuotationMasterAPIController@amendSalesQuotationReview');
        Route::post('getDocumentDetails', 'PurchaseRequestAPIController@getDocumentDetails');

        Route::get('getVATFilterFormData', 'VATReportAPIController@getVATFilterFormData');
        Route::post('validateVATReport', 'VATReportAPIController@validateVATReport');
        Route::post('generateVATReport', 'VATReportAPIController@generateVATReport');
        Route::post('generateVATDetailReport', 'VATReportAPIController@generateVATDetailReport');
        Route::post('exportVATReport', 'VATReportAPIController@exportVATReport');
        Route::post('exportVATDetailReport', 'VATReportAPIController@exportVATDetailReport');

        Route::resource('customer_category_assigneds', 'CustomerMasterCategoryAssignedAPIController');
        Route::get('assignedCompaniesByCustomerCategory', 'CustomerMasterCategoryAssignedAPIController@assignedCompaniesByCustomerCategory');

        Route::post('sentSupplierStatement', 'AccountsPayableReportAPIController@sentSupplierStatement');
        Route::post('sentSupplierLedger', 'AccountsPayableReportAPIController@sentSupplierLedger');
        Route::post('sentCustomerStatement', 'AccountsReceivableReportAPIController@sentCustomerStatement');
        Route::post('sentCustomerLedger', 'AccountsReceivableReportAPIController@sentCustomerLedger');

        Route::post('exportTransactionsRecord', 'TransactionsExportExcel@exportRecord');

        Route::resource('currency_conversion_masters', 'CurrencyConversionMasterAPIController');

        Route::resource('currency_conversion_details', 'CurrencyConversionDetailAPIController');


        Route::post('getAllCurrencyConversions', 'CurrencyConversionMasterAPIController@getAllCurrencyConversions');
        Route::post('currencyConversionReopen', 'CurrencyConversionMasterAPIController@currencyConversionReopen');
        Route::post('updateTempCrossExchange', 'CurrencyConversionDetailAPIController@updateTempCrossExchange');
        Route::get('getConversionMaster', 'CurrencyConversionMasterAPIController@getConversionMaster');
        Route::get('getAllTempConversionByCurrency', 'CurrencyConversionMasterAPIController@getAllTempConversionByCurrency');

        Route::post('getAllCurrencyConversionApproval', 'CurrencyConversionMasterAPIController@getAllCurrencyConversionApproval');
        Route::post('approveCurrencyConversion', 'CurrencyConversionMasterAPIController@approveCurrencyConversion');
        Route::post('rejectCurrencyConversion', 'CurrencyConversionMasterAPIController@rejectCurrencyConversion');

        Route::post('getCurrencyConversionHistory', 'CurrencyConversionHistoryAPIController@getCurrencyConversionHistory');

        Route::resource('stock_counts', 'StockCountAPIController');

        Route::resource('stock_count_details', 'StockCountDetailAPIController');
        Route::get('getItemsByStockCount', 'StockCountDetailAPIController@getItemsByStockCount');
        Route::post('removeAllStockCountItems', 'StockCountDetailAPIController@removeAllStockCountItems');
        Route::post('getAllStockCountsByCompany', 'StockCountAPIController@getAllStockCountsByCompany');
        Route::post('stockCountReopen', 'StockCountAPIController@stockCountReopen');
        Route::post('getStockCountApprovalByUser', 'StockCountAPIController@getStockCountApprovalByUser');
        Route::post('getStockCountApprovedByUser', 'StockCountAPIController@getStockCountApprovedByUser');
        Route::post('stockCountReferBack', 'StockCountAPIController@stockCountReferBack');
        Route::get('stockCountAudit', 'StockCountAPIController@getStockCountAudit');


        Route::resource('stock_count_reffered_backs', 'StockCountRefferedBackAPIController');
        Route::post('getReferBackHistoryByStockCounts', 'StockCountRefferedBackAPIController@getReferBackHistoryByStockCounts');

        Route::resource('stockcountdetailsreffered', 'StockCountDetailsRefferedBackAPIController');
        Route::get('getSCDetailsReferBack', 'StockCountDetailsRefferedBackAPIController@getSCDetailsReferBack');
        // contingency budget plan
        Route::resource('contingency_budget_plans', 'ContingencyBudgetPlanAPIController');
        Route::get('contingency_budget_list', 'ContingencyBudgetPlanAPIController@budget_list');
        Route::post('get_contingency_budget', 'ContingencyBudgetPlanAPIController@get_contingency_budget');
        Route::get('getContingencyBudgetFormData', 'ContingencyBudgetPlanAPIController@getFormData');
        Route::get('getBudgetAmount/{id}', 'ContingencyBudgetPlanAPIController@getBudgetAmount');
        Route::post('get_contingency_budget_approved', 'ContingencyBudgetPlanAPIController@get_contingency_budget_approved');
        Route::post('get_contingency_budget_not_approved', 'ContingencyBudgetPlanAPIController@get_contingency_budget_not_approved');
        Route::post('approve_contingency_budget', 'ContingencyBudgetPlanAPIController@approve_contingency_budget');
        Route::post('reject_contingency_budget', 'ContingencyBudgetPlanAPIController@reject_contingency_budget');

        Route::resource('budget_addition', 'ErpBudgetAdditionAPIController');
        Route::post('budget_additions', 'ErpBudgetAdditionAPIController@index');
        Route::get('getTemplatesDetailsByBudgetAddition', 'ErpBudgetAdditionAPIController@getTemplatesDetailsByBudgetAddition');
        Route::get('getAllGLCodesByBudgetAddition', 'ErpBudgetAdditionAPIController@getAllGLCodesByBudgetAddition');
        Route::get('getDetailsByBudgetAddition', 'ErpBudgetAdditionDetailAPIController@getDetailsByBudgetAddition');
        Route::get('getTemplateByGLCodeByBudgetAddition', 'ErpBudgetAdditionAPIController@getTemplateByGLCodeByBudgetAddition');
        Route::get('getBudgetAdditionFormData', 'ErpBudgetAdditionAPIController@getBudgetAdditionFormData');
        Route::resource('budget_addition_details', 'ErpBudgetAdditionDetailAPIController');
        Route::post('getBudgetAdditionApprovalByUser', 'ErpBudgetAdditionAPIController@getBudgetAdditionApprovalByUser');
        Route::post('getBudgetAdditionApprovedByUser', 'ErpBudgetAdditionAPIController@getBudgetAdditionApprovedByUser');

        Route::resource('budget_detail_histories', 'BudgetDetailHistoryAPIController');

        Route::resource('budget_review_transfer_additions', 'BudgetReviewTransferAdditionAPIController');
        Route::get('getBudgetReviewTransferAddition', 'BudgetReviewTransferAdditionAPIController@getBudgetReviewTransferAddition');



        Route::resource('segment_allocated_items', 'SegmentAllocatedItemAPIController');
        Route::post('allocateSegmentWiseItem', 'SegmentAllocatedItemAPIController@allocateSegmentWiseItem');
        Route::post('getSegmentAllocatedItems', 'SegmentAllocatedItemAPIController@getSegmentAllocatedItems');
        Route::post('getSegmentAllocatedFormData', 'SegmentAllocatedItemAPIController@getSegmentAllocatedFormData');
        Route::get('getVerificationFormData', 'AssetVerificationAPIController@getVerificationFormData');
        Route::post('getAllAssetVerification', 'AssetVerificationAPIController@index');
        Route::post('storeVerification', 'AssetVerificationAPIController@store');
        Route::delete('deleteAssetVerification/{id}', 'AssetVerificationAPIController@destroy');
        Route::get('getVerificationById/{id}', 'AssetVerificationAPIController@show');
        Route::post('getVerificationApprovalByUser', 'AssetVerificationAPIController@getVerificationApprovalByUser');
        Route::post('getVerificationApprovedByUser', 'AssetVerificationAPIController@getVerificationApprovedByUser');
        Route::put('updateAssetVerification/{id}', 'AssetVerificationAPIController@update');
        Route::post('getAllCostingByCompanyForVerification', 'AssetVerificationAPIController@getAllCostingByCompanyForVerification');
        Route::post('addAssetToVerification/{id}', 'AssetVerificationDetailAPIController@store');
        Route::post('getVerificationDetailsById', 'AssetVerificationDetailAPIController@index');
        Route::delete('deleteAssetFromVerification/{id}', 'AssetVerificationDetailAPIController@destroy');

        Route::post('erp_project_masters', 'ErpProjectMasterAPIController@index');
        Route::post('get_projects', 'ErpProjectMasterAPIController@get_projects');
        Route::post('erp_project_masters/create', 'ErpProjectMasterAPIController@store');
        Route::get('erp_project_masters/form', 'ErpProjectMasterAPIController@formData');
        Route::get('erp_project_masters/segments_by_company', 'ErpProjectMasterAPIController@segmentsByCompany');
        Route::get('erp_project_masters/{id}', 'ErpProjectMasterAPIController@show');
        Route::put('erp_project_masters/{id}', 'ErpProjectMasterAPIController@update');

        /* Asset Request */
        Route::resource('asset_requests', 'AssetRequestAPIController');
        Route::post('getAllAssetRequestList', 'AssetRequestAPIController@getAllAssetRequestList');
        Route::get('asset-request-details', 'AssetRequestDetailAPIController@getAssetRequestDetails');
        Route::get('getassetRequestMaster', 'AssetRequestDetailAPIController@getAssetRequestMaster');
        Route::get('getassetRequestDetailSelected', 'AssetRequestDetailAPIController@getAssetRequestDetailSelected');
        Route::get('getAssetDropData', 'AssetRequestDetailAPIController@getAssetDropData');

        /* Asset Transfer */
        Route::resource('asset_transfer', 'ERPAssetTransferAPIController');
        Route::post('getAllAssetTransferList', 'ERPAssetTransferAPIController@getAllAssetTransferList');
        Route::get('fetch-asset-transfer-master/{id}', 'ERPAssetTransferAPIController@fetchAssetTransferMaster');
        Route::post('add-asset-transfer-detail/{id}', 'ERPAssetTransferDetailAPIController@store');
        Route::get('get-employee-asset-transfer-details/{id}', 'ERPAssetTransferDetailAPIController@get_employee_asset_transfer_details');
        Route::resource('asset_transfer_detail', 'ERPAssetTransferDetailAPIController');
        Route::get('asset-transfer-drop', 'ERPAssetTransferDetailAPIController@assetTransferDrop');
        Route::post('add-employee-asset-transfer-asset-detail/{id}', 'ERPAssetTransferDetailAPIController@addEmployeeAsset');
        Route::get('asset-transfer-details', 'ERPAssetTransferDetailAPIController@getAssetTransferDetails');
        Route::post('getAssetTransferApprovalByUser', 'ERPAssetTransferAPIController@getAssetTransferApprovalByUser');
        Route::post('rejectAssetTransfer', 'ERPAssetTransferAPIController@rejectAssetTransfer');
        Route::post('approveAssetTransfer', 'ERPAssetTransferAPIController@approveAssetTransfer');
        Route::get('getAssetTransferData', 'ERPAssetTransferAPIController@getAssetTransferData');
        Route::post('asset_transfer_detail_asset', 'ERPAssetTransferDetailAPIController@assetTransferDetailAsset');
        Route::get('getAssetDropPR', 'ERPAssetTransferAPIController@getAssetDropPR');
        Route::post('getAssetTransferApprovalByUserApproved', 'ERPAssetTransferAPIController@getAssetTransferApprovalByUserApproved');
        Route::get('asset-location-value', 'ERPAssetTransferDetailAPIController@getAssetLocationValue');
        Route::get('getAssetTransferMasterRecord', 'ERPAssetTransferAPIController@getAssetTransferMasterRecord');
        Route::post('assetTransferReopen', 'ERPAssetTransferAPIController@assetTransferReopen');
        Route::post('amendAssetTrasfer', 'ERPAssetTransferAPIController@amendAssetTrasfer');
        Route::post('getAssetTransferAmendHistory', 'AssetTransferReferredbackAPIController@getAssetTransferAmendHistory');
        Route::get('fetch-asset-transfer-master-amend/{id}', 'AssetTransferReferredbackAPIController@fetchAssetTransferMasterAmend');
        Route::get('get-employee-asset-transfer-details-amend/{id}', 'ERPAssetTransferDetailsRefferedbackAPIController@get_employee_asset_transfer_details_amend');
        Route::post('amendAssetVerification', 'AssetVerificationAPIController@amendAssetVerification');
        Route::post('budgetAdditionReopen', 'ErpBudgetAdditionAPIController@budgetAdditionReopen');
        Route::get('getBudgetAdditionAudit', 'ErpBudgetAdditionAPIController@getBudgetAdditionAudit');
        Route::post('getAssetVerificationAmendHistory', 'ERPAssetVerificationReferredbackAPIController@getAssetVerificationAmendHistory');
        Route::get('fetchAssetVerification/{id}', 'ERPAssetVerificationReferredbackAPIController@fetchAssetVerification');
        Route::post('fetchAssetVerificationDetailAmend', 'ERPAssetVerificationDetailReferredbackAPIController@fetchAssetVerificationDetailAmend');
        Route::get('assetStatus', 'ERPAssetTransferAPIController@assetStatus');

        Route::post('amendBudgetTrasfer', 'BudgetTransferFormAPIController@amendBudgetTrasfer');
        Route::post('getBudgetTransferAmendHistory', 'BudgetTransferFormRefferedBackAPIController@getBudgetTransferAmendHistory');
        Route::get('budget_transfer_amend/{id}', 'BudgetTransferFormRefferedBackAPIController@budgetTransferAmend');
        Route::get('getDetailsByBudgetTransferAmend', 'BudgetTransferFormDetailRefferedBackAPIController@getDetailsByBudgetTransferAmend');
        Route::post('amendBudgetAddition', 'ErpBudgetAdditionAPIController@amendBudgetAddition');
        Route::post('getBudgetAdditionAmendHistory', 'BudgetAdditionRefferedBackAPIController@getBudgetAdditionAmendHistory');
        Route::get('budget_addition_amend/{id}', 'BudgetAdditionRefferedBackAPIController@budget_addition_amend');
        Route::get('getDetailsByBudgetAdditionAmend', 'BudgetAdditionRefferedBackAPIController@getDetailsByBudgetAdditionAmend');
        Route::post('amendContingencyBudget', 'ContingencyBudgetPlanAPIController@amendContingencyBudget');
        Route::post('getContingencyAmendHistory', 'ContingencyBudgetRefferedBackAPIController@getContingencyAmendHistory');
        Route::get('contingencyBudgetAmend/{id}', 'ContingencyBudgetRefferedBackAPIController@contingencyBudgetAmend');
        Route::resource('budget_master_reffered_histories', 'BudgetMasterRefferedHistoryAPIController');
        Route::resource('budget_details_reffered_histories', 'BudgetDetailsRefferedHistoryAPIController');
        Route::post('getBudgetAmendHistory', 'BudgetMasterRefferedHistoryAPIController@getBudgetAmendHistory');
        Route::post('getDetailsByBudgetRefereback', 'BudgetDetailsRefferedHistoryAPIController@getDetailsByBudgetRefereback');


        Route::resource('budget_detail_comments', 'BudgetDetailCommentAPIController');
        Route::post('getBudgetDetailComment', 'BudgetDetailCommentAPIController@getBudgetDetailComment');


        /* Chart Of Account Scenario configuration */
        Route::resource('system_gl_code_scenarios', 'SystemGlCodeScenarioAPIController');
        Route::resource('system_gl_code_scenario_details', 'SystemGlCodeScenarioDetailAPIController');

          /* Master Datas Bulk Upload */
        Route::get('downloadTemplate', 'CustomerMasterAPIController@downloadTemplate');
        Route::post('masterBulkUpload', 'CustomerMasterAPIController@masterBulkUpload');
        Route::resource('gl-config-scenario-details', 'SystemGlCodeScenarioDetailAPIController');
        Route::post('coa-config-scenario-assign', 'SystemGlCodeScenarioAPIController@scenario_assign');
        Route::get('coa-config-companies', 'SystemGlCodeScenarioAPIController@coa_config_companies');
        Route::post('coa-config-scenarios', 'SystemGlCodeScenarioDetailAPIController@list_config_scenarios');

        Route::resource('module_masters', 'ModuleMasterAPIController');

        Route::resource('sub_module_masters', 'SubModuleMasterAPIController');

        Route::resource('module_assigneds', 'ModuleAssignedAPIController');

        Route::resource('pdc_logs', 'PdcLogAPIController');
        Route::post('getPdcCheques', 'PdcLogAPIController@getPdcCheques');
        
        Route::post('get-all-issued-cheques', 'PdcLogAPIController@getIssuedCheques');

        Route::post('get-all-received-cheques', 'PdcLogAPIController@getAllReceivedCheques');

        Route::get('pdc-logs/banks', 'PdcLogAPIController@getAllBanks');

        Route::get('pdc-logs/get-form-data', 'PdcLogAPIController@getFormData');

        Route::post('deleteAllPDC', 'PdcLogAPIController@deleteAllPDC');
        Route::post('changePdcChequeStatus', 'PdcLogAPIController@changePdcChequeStatus');
        Route::post('reverseGeneratedChequeNo', 'PdcLogAPIController@reverseGeneratedChequeNo');
        Route::post('issueNewCheque', 'PdcLogAPIController@issueNewCheque');
        Route::get('getNextChequeNo', 'PdcLogAPIController@getNextChequeNo');
        Route::resource('cheque_template_masters', 'ChequeTemplateMasterAPIController');
        Route::resource('cheque_template_banks', 'ChequeTemplateBankAPIController');

        Route::post('assignedTemplatesByBank', 'ChequeTemplateBankAPIController@assignedTemplatesByBank');

        Route::post('bank/update/template', 'ChequeTemplateBankAPIController@updateBankAssingTemplate');

        Route::get('getBankTemplates/{id}', 'ChequeTemplateBankAPIController@getBankTemplates');
        
        Route::resource('vat_return_filling_masters', 'VatReturnFillingMasterAPIController');
        Route::post('getVatReturnFillings', 'VatReturnFillingMasterAPIController@getVatReturnFillings');
        Route::post('getVatReturnFillingDetails', 'VatReturnFillingMasterAPIController@getVatReturnFillingDetails');
        Route::post('updateVatReturnFillingDetails', 'VatReturnFillingMasterAPIController@updateVatReturnFillingDetails');
        Route::post('vatReturnFillingReopen', 'VatReturnFillingMasterAPIController@vatReturnFillingReopen');
        Route::get('getVATReturnFillingData', 'VatReturnFillingMasterAPIController@getVATReturnFillingData');
        Route::get('getVATReturnFillingFormData', 'VatReturnFillingMasterAPIController@getVATReturnFillingFormData');

        Route::post('getVRFApprovalByUser', 'VatReturnFillingMasterAPIController@getVRFApprovalByUser');
        Route::post('getVRFApprovedByUser', 'VatReturnFillingMasterAPIController@getVRFApprovedByUser');
        
        Route::post('getVRFAmend', 'VatReturnFillingMasterAPIController@getVRFAmend');

        Route::resource('vat_return_filling_categories', 'VatReturnFillingCategoryAPIController');
        Route::resource('vat_return_filled_categories', 'VatReturnFilledCategoryAPIController');
        Route::resource('vat_sub_category_types', 'VatSubCategoryTypeAPIController');
        Route::resource('vat_return_filling_details', 'VatReturnFillingDetailAPIController');


        Route::resource('supplier_invoice_item_details', 'SupplierInvoiceItemDetailAPIController');
        Route::get('getGRVDetailsForSupplierInvoice', 'SupplierInvoiceItemDetailAPIController@getGRVDetailsForSupplierInvoice');

        Route::resource('expense_asset_allocations', 'ExpenseAssetAllocationAPIController');
        Route::get('getCompanyAsset', 'ExpenseAssetAllocationAPIController@getCompanyAsset');
        Route::post('getAllocatedAssetsForExpense', 'ExpenseAssetAllocationAPIController@getAllocatedAssetsForExpense');
        Route::post('approveCalanderDelAppointment', 'AppointmentAPIController@approveCalanderDelAppointment');
        Route::post('rejectCalanderDelAppointment', 'AppointmentAPIController@rejectCalanderDelAppointment');
        Route::post('getAppointmentById', 'AppointmentAPIController@getAppointmentById');
        Route::post('checkAssetAllocation', 'ExpenseAssetAllocationAPIController@checkAssetAllocation');

        Route::resource('supplier-category-conf', 'SupplierCategoryConfigurationController');
        Route::resource('supplier-group-conf', 'SupplierGroupConfigurationController');

        
        Route::post('get-supplier-categories', 'SupplierCategoryConfigurationController@getSupplierCategories');
        Route::post('delete-category', 'SupplierCategoryConfigurationController@deleteCategory');
        Route::post('get-supplier-groups', 'SupplierGroupConfigurationController@getSupplierGroups');
        Route::post('delete-group', 'SupplierGroupConfigurationController@deleteGroup');

        
       
        
        /**
         * Supplier registration approval routes
         */
        Route::group(['prefix' => 'suppliers/registration'], function () {
            Route::post('/', 'SupplierRegistrationController@index');
            Route::post('/attach', 'SupplierRegistrationController@linkKYCWithSupplier');
            Route::post('approvals', 'SupplierRegistrationApprovalController@index');
            Route::post('approvals/status', 'SupplierRegistrationApprovalController@update');
            Route::post('/supplierCreation', 'SupplierRegistrationApprovalController@supplierCreation');
        });


        Route::resource('appointments', 'AppointmentAPIController');
        

        Route::resource('appointment_details', 'AppointmentDetailsAPIController');

        Route::resource('po_categories', 'PoCategoryAPIController');

        Route::resource('purchase_return_logistics', 'PurchaseReturnLogisticAPIController');

        Route::resource('item_serials', 'ItemSerialAPIController');
        Route::post('generateItemSerialNumbers', 'ItemSerialAPIController@generateItemSerialNumbers');
        Route::post('serialItemDeleteAllDetails', 'ItemSerialAPIController@serialItemDeleteAllDetails');
        Route::get('getGeneratedSerialNumbers', 'ItemSerialAPIController@getGeneratedSerialNumbers');
        Route::get('getSerialNumbersForOut', 'ItemSerialAPIController@getSerialNumbersForOut');
        Route::get('getSerialNumbersForReturn', 'ItemSerialAPIController@getSerialNumbersForReturn');
        Route::post('updateSoldStatusOfSerial', 'ItemSerialAPIController@updateSoldStatusOfSerial');
        Route::post('updateReturnStatusOfSerial', 'ItemSerialAPIController@updateReturnStatusOfSerial');

        Route::resource('item_batches', 'ItemBatchAPIController');
        Route::get('getBatchNumbersForOut', 'ItemBatchAPIController@getBatchNumbersForOut');
        Route::post('updateSoldStatusOfBatch', 'ItemBatchAPIController@updateSoldStatusOfBatch');
        Route::get('getBatchNumbersForReturn', 'ItemBatchAPIController@getBatchNumbersForReturn');
        Route::get('getWareHouseDataForItemOut', 'ItemBatchAPIController@getWareHouseDataForItemOut');
        Route::post('updateReturnStatusOfBatch', 'ItemBatchAPIController@updateReturnStatusOfBatch');

        Route::get('getEliminationLedgerReview', 'EliminationLedgerAPIController@getEliminationLedgerReview');

        Route::resource('document_sub_products', 'DocumentSubProductAPIController');


        Route::resource('payment_types', 'PaymentTypeAPIController');
        Route::resource('elimination_ledgers', 'EliminationLedgerAPIController');

        Route::resource('inter_company_stock_transfers', 'InterCompanyStockTransferAPIController');
        Route::resource('supplier_invoice_direct_items', 'SupplierInvoiceDirectItemAPIController');
        Route::get('getSupplierInvDirectItems', 'SupplierInvoiceDirectItemAPIController@getSupplierInvDirectItems');
        Route::post('deleteAllSIDirectItemDetail', 'SupplierInvoiceDirectItemAPIController@deleteAllSIDirectItemDetail');


        Route::post('getTenderBidFormats', 'TenderBidFormatMasterAPIController@getTenderBidFormats');
        Route::post('storeBidFormat', 'TenderBidFormatMasterAPIController@storeBidFormat');
        Route::post('loadBidFormatMaster', 'TenderBidFormatMasterAPIController@loadBidFormatMaster');
        Route::post('addPriceBidDetail', 'TenderBidFormatMasterAPIController@addPriceBidDetail');
        Route::post('updatePriceBidDetail', 'TenderBidFormatMasterAPIController@updatePriceBidDetail');
        Route::post('updateBidFormat', 'TenderBidFormatMasterAPIController@updateBidFormat');
        Route::post('deletePriceBideDetail', 'TenderBidFormatMasterAPIController@deletePriceBideDetail');
        Route::post('deletePriceBidMaster', 'TenderBidFormatMasterAPIController@deletePriceBidMaster');

        Route::post('getTenderMasterList', 'TenderMasterAPIController@getTenderMasterList');
        Route::post('getTenderDropDowns', 'TenderMasterAPIController@getTenderDropDowns');
        Route::post('createTender', 'TenderMasterAPIController@createTender');
        Route::post('deleteTenderMaster', 'TenderMasterAPIController@deleteTenderMaster');
        Route::post('getTenderMasterData', 'TenderMasterAPIController@getTenderMasterData');
        Route::post('loadTenderSubCategory', 'TenderMasterAPIController@loadTenderSubCategory');
        Route::post('loadTenderSubActivity', 'TenderMasterAPIController@loadTenderSubActivity');
        Route::post('loadTenderBankAccount', 'TenderMasterAPIController@loadTenderBankAccount');
        Route::post('updateTender', 'TenderMasterAPIController@updateTender');

        Route::post('getPricingScheduleList', 'PricingScheduleMasterAPIController@getPricingScheduleList');
        Route::post('getPricingScheduleDropDowns', 'PricingScheduleMasterAPIController@getPricingScheduleDropDowns');
        Route::post('addPricingSchedule', 'PricingScheduleMasterAPIController@addPricingSchedule');
        Route::post('getPricingScheduleMaster', 'PricingScheduleMasterAPIController@getPricingScheduleMaster');
        Route::post('deletePricingSchedule', 'PricingScheduleMasterAPIController@deletePricingSchedule');
        Route::post('getPriceBidFormatDetails', 'PricingScheduleMasterAPIController@getPriceBidFormatDetails');
        Route::post('addPriceBidDetails', 'PricingScheduleMasterAPIController@addPriceBidDetails');
        Route::post('getNotPulledPriceBidDetails', 'PricingScheduleMasterAPIController@getNotPulledPriceBidDetails');



        Route::resource('employee_ledgers', 'EmployeeLedgerAPIController');
        Route::resource('srp_erp_pay_shift_employees', 'SrpErpPayShiftEmployeesAPIController');

        Route::resource('srp_erp_pay_shift_masters', 'SrpErpPayShiftMasterAPIController');

        Route::resource('expense_employee_allocations', 'ExpenseEmployeeAllocationAPIController');
        Route::post('getAllocatedEmployeesForExpense', 'ExpenseEmployeeAllocationAPIController@getAllocatedEmployeesForExpense');

        Route::post('getMainWorksList', 'TenderMainWorksAPIController@getMainWorksList');
        Route::post('addMainWorks', 'TenderMainWorksAPIController@addMainWorks');
        Route::get('downloadMainWorksUploadTemplate', 'TenderMainWorksAPIController@downloadMainWorksUploadTemplate');
        Route::post('mainWorksItemsUpload', 'TenderMainWorksAPIController@mainWorksItemsUpload');
        Route::post('deleteMainWorks', 'TenderMainWorksAPIController@deleteMainWorks');
        Route::post('updateWorkOrderDescription', 'TenderMainWorksAPIController@updateWorkOrderDescription');

        Route::post('getFaqFormData', 'TenderMasterAPIController@getFaqFormData');
        Route::post('createFaq', 'TenderFaqAPIController@createFaq');
        Route::post('getFaqList', 'TenderFaqAPIController@getFaqList');
        Route::post('getFaq', 'TenderFaqAPIController@getFaq');
        Route::post('deleteFaq', 'TenderFaqAPIController@deleteFaq');

        Route::post('loadTenderBoqItems', 'TenderBoqItemsAPIController@loadTenderBoqItems');
        Route::post('addTenderBoqItems', 'TenderBoqItemsAPIController@addTenderBoqItems');
        Route::post('updateTenderBoqItem', 'TenderBoqItemsAPIController@updateTenderBoqItem');
        Route::get('downloadTenderBoqItemUploadTemplate', 'TenderBoqItemsAPIController@downloadTenderBoqItemUploadTemplate');
        Route::post('deleteTenderBoqItem', 'TenderBoqItemsAPIController@deleteTenderBoqItem');
        Route::post('tenderBoqItemsUpload', 'TenderBoqItemsAPIController@tenderBoqItemsUpload');
        Route::post('getPreBidClarifications', 'TenderBidClarificationsAPIController@getPreBidClarifications');
        Route::post('getPreBidClarificationsResponse', 'TenderBidClarificationsAPIController@getPreBidClarificationsResponse');
        Route::post('createResponse', 'TenderBidClarificationsAPIController@createResponse');
        Route::post('getTenderMasterApproval', 'TenderMasterAPIController@getTenderMasterApproval');
        Route::post('getTenderMasterFullApproved', 'TenderMasterAPIController@getTenderMasterFullApproved');
        Route::post('approveTender', 'TenderMasterAPIController@approveTender');
        Route::post('rejectTender', 'TenderMasterAPIController@rejectTender');
        Route::post('deletePreTender', 'TenderBidClarificationsAPIController@deletePreTender');
        Route::post('getPreBidEditData', 'TenderBidClarificationsAPIController@getPreBidEditData');
        Route::post('updatePreBid', 'TenderBidClarificationsAPIController@updatePreBid'); 
        Route::post('closeThread', 'TenderBidClarificationsAPIController@closeThread');
        Route::post('reOpenTender', 'TenderMasterAPIController@reOpenTender');
        Route::post('tenderMasterPublish', 'TenderMasterAPIController@tenderMasterPublish');

        Route::post('getEvaluationCriteriaDropDowns', 'EvaluationCriteriaDetailsAPIController@getEvaluationCriteriaDropDowns');
        Route::post('addEvaluationCriteria', 'EvaluationCriteriaDetailsAPIController@addEvaluationCriteria');
        Route::post('getEvaluationCriteriaDetails', 'EvaluationCriteriaDetailsAPIController@getEvaluationCriteriaDetails');
        Route::post('deleteEvaluationCriteria', 'EvaluationCriteriaDetailsAPIController@deleteEvaluationCriteria');
        Route::post('getEvaluationDetailById', 'EvaluationCriteriaDetailsAPIController@getEvaluationDetailById');
        Route::post('editEvaluationCriteria', 'EvaluationCriteriaDetailsAPIController@editEvaluationCriteria');
        Route::post('validateWeightage', 'EvaluationCriteriaDetailsAPIController@validateWeightage');
        Route::post('validateWeightageEdit', 'EvaluationCriteriaDetailsAPIController@validateWeightageEdit');

        Route::post('removeCriteriaConfig', 'EvaluationCriteriaScoreConfigAPIController@removeCriteriaConfig');
        Route::post('addEvaluationCriteriaConfig', 'EvaluationCriteriaScoreConfigAPIController@addEvaluationCriteriaConfig');
        Route::post('updateCriteriaScore', 'EvaluationCriteriaScoreConfigAPIController@updateCriteriaScore');

        Route::post('getSupplierList', 'TenderMasterAPIController@getSupplierList');
        Route::post('saveSupplierAssigned', 'TenderMasterAPIController@saveSupplierAssigned');
        Route::post('getSupplierAssignedList', 'TenderMasterAPIController@getSupplierAssignedList');
        Route::post('deleteSupplierAssign', 'TenderSupplierAssigneeAPIController@deleteSupplierAssign');
        Route::post('supplierAssignCRUD', 'TenderSupplierAssigneeAPIController@supplierAssignCRUD');
        Route::post('sendSupplierInvitation', 'TenderSupplierAssigneeAPIController@sendSupplierInvitation');
        Route::post('reSendInvitaitonLink', 'TenderSupplierAssigneeAPIController@reSendInvitaitonLink');
        Route::resource('barcode_configurations', 'BarcodeConfigurationAPIController');
        Route::get('getBarcodeConfigurationFormData', 'BarcodeConfigurationAPIController@getBarcodeConfigurationFormData');
        Route::post('getAllBarCodeConf', 'BarcodeConfigurationAPIController@getAllBarCodeConf');
        Route::get('checkConfigurationExit', 'BarcodeConfigurationAPIController@checkConfigurationExit');

        Route::post('getSupplierCategoryList', 'TenderMasterAPIController@getSupplierCategoryList');
        Route::post('removeCalenderDate', 'TenderMasterAPIController@removeCalenderDate');
        Route::post('updateCalenderDate', 'TenderMasterAPIController@updateCalenderDate');
        Route::post('getTenderAttachmentType', 'TenderDocumentTypesAPIController@getTenderAttachmentType');
        Route::post('getNotSentEmail', 'TenderSupplierAssigneeAPIController@getNotSentEmail');

        Route::resource('cash_flow_templates', 'CashFlowTemplateAPIController');
        Route::resource('cash_flow_template_details', 'CashFlowTemplateDetailAPIController');
        Route::post('getAllCashFlowTemplate', 'CashFlowTemplateAPIController@getAllCashFlowTemplate');
        Route::get('getCashFlowReportHeaderData', 'CashFlowTemplateAPIController@getCashFlowReportHeaderData');
        Route::get('getCashFlowTemplateSubCat', 'CashFlowTemplateDetailAPIController@getCashFlowTemplateSubCat');
        Route::post('deleteAllLinkedGLCodesCashFlow', 'CashFlowTemplateLinkAPIController@deleteAllLinkedGLCodesCashFlow');
        Route::post('cashFlowTemplateDetailSubCatLink', 'CashFlowTemplateLinkAPIController@cashFlowTemplateDetailSubCatLink');
        Route::post('addCashFlowTemplateSubCategory', 'CashFlowTemplateDetailAPIController@addCashFlowTemplateSubCategory');
        Route::get('getCashFlowTemplateDetail/{id}', 'CashFlowTemplateDetailAPIController@getCashFlowTemplateDetail');

        Route::resource('cash_flow_template_links', 'CashFlowTemplateLinkAPIController');


        Route::resource('cash_flow_reports', 'CashFlowReportAPIController');
        Route::get('getCashFlowFormData', 'CashFlowReportAPIController@getCashFlowFormData');
        Route::post('getCashFlowReports', 'CashFlowReportAPIController@getCashFlowReports');
        Route::post('cashFlowConfirmation', 'CashFlowReportAPIController@cashFlowConfirmation');
        Route::post('getCashFlowPullingItems', 'CashFlowReportAPIController@getCashFlowPullingItems');
        Route::post('getCashFlowPullingItemsForProceeds', 'CashFlowReportAPIController@getCashFlowPullingItemsForProceeds');
        Route::post('postCashFlowPulledItems', 'CashFlowReportAPIController@postCashFlowPulledItems');
        Route::post('postCashFlowPulledItemsForProceeds', 'CashFlowReportAPIController@postCashFlowPulledItemsForProceeds');
        Route::get('getCashFlowReportData', 'CashFlowReportAPIController@getCashFlowReportData');
        Route::post('updateTenderStrategy', 'TenderMasterAPIController@updateTenderStrategy');

        Route::post('getTenderCircularList', 'TenderCircularsAPIController@getTenderCircularList');
        Route::post('getAttachmentDropCircular', 'TenderCircularsAPIController@getAttachmentDropCircular');
        Route::post('addCircular', 'TenderCircularsAPIController@addCircular');
        Route::post('getCircularMaster', 'TenderCircularsAPIController@getCircularMaster');
        Route::post('deleteTenderCircular', 'TenderCircularsAPIController@deleteTenderCircular');
        Route::post('tenderCircularPublish', 'TenderCircularsAPIController@tenderCircularPublish');
        Route::post('getAllInvoicesPos', 'POS\PosAPIController@getAllInvoicesPos');
        Route::post('getPosInvoiceData', 'POS\PosAPIController@getPosInvoiceData');
        Route::post('getAllInvoicesPosReturn', 'POS\PosAPIController@getAllInvoicesPosReturn');
        Route::post('getPosInvoiceReturnData', 'POS\PosAPIController@getPosInvoiceReturnData');
        Route::post('getAllInvoicesRPos', 'POS\PosAPIController@getAllInvoicesRPos');
        Route::post('getRPOSInvoiceData', 'POS\PosAPIController@getRPOSInvoiceData');

    });

    Route::get('validateSupplierRegistrationLink', 'SupplierMasterAPIController@validateSupplierRegistrationLink');
    Route::get('getSupplierRegisterFormData', 'SupplierMasterAPIController@getSupplierRegisterFormData');
    Route::post('registerSupplier', 'SupplierMasterAPIController@registerSupplier');
    Route::post('supplierReOpen', 'SupplierMasterAPIController@supplierReOpen');
    Route::post('validateSupplierAmend', 'SupplierMasterAPIController@validateSupplierAmend');

    Route::get('getProcumentOrderPrintPDF', 'ProcumentOrderAPIController@getProcumentOrderPrintPDF');
    Route::get('goodReceiptVoucherPrintPDF', 'GRVMasterAPIController@goodReceiptVoucherPrintPDF');
    Route::post('getReportPDF', 'ReportAPIController@pdfExportReport');
    Route::post('generateARReportPDF', 'AccountsReceivableReportAPIController@pdfExportReport');
    Route::post('generateAPReportPDF', 'AccountsPayableReportAPIController@pdfExportReport');
    Route::get('printPurchaseRequest', 'PurchaseRequestAPIController@printPurchaseRequest');
    Route::get('printItemIssue', 'ItemIssueMasterAPIController@printItemIssue');
    Route::get('deliveryPrintItemIssue', 'ItemIssueMasterAPIController@deliveryPrintItemIssue');
    Route::get('printItemReturn', 'ItemReturnMasterAPIController@printItemReturn');
    Route::get('printStockReceive', 'StockReceiveAPIController@printStockReceive');
    Route::get('printStockTransfer', 'StockTransferAPIController@printStockTransfer');
    Route::get('getPoLogisticPrintPDF', 'PoAdvancePaymentAPIController@getPoLogisticPrintPDF');
    Route::get('printPurchaseReturn', 'PurchaseReturnAPIController@printPurchaseReturn');
    Route::get('printCustomerInvoice', 'CustomerInvoiceDirectAPIController@printCustomerInvoice');
    Route::get('printExpenseClaim', 'ExpenseClaimAPIController@printExpenseClaim');
    Route::get('printExpenseClaimMaster', 'ExpenseClaimMasterAPIController@printExpenseClaimMaster');
    Route::get('printCreditNote', 'CreditNoteAPIController@printCreditNote');
    Route::get('printDebitNote', 'DebitNoteAPIController@printDebitNote');
    Route::get('printSupplierInvoice', 'BookInvSuppMasterAPIController@printSupplierInvoice');
    Route::get('printBankReconciliation', 'BankReconciliationAPIController@printBankReconciliation');
    Route::get('printChequeItems', 'BankLedgerAPIController@printChequeItems');
    Route::get('printSuppliers', 'SupplierMasterAPIController@printSuppliers');
    Route::get('printReceiptVoucher', 'CustomerReceivePaymentAPIController@printReceiptVoucher');
    Route::get('printMaterielRequest', 'MaterielRequestAPIController@printMaterielRequest');
    Route::get('printPaymentVoucher', 'PaySupplierInvoiceMasterAPIController@printPaymentVoucher');
    Route::get('exportPaymentBankTransfer', 'PaymentBankTransferAPIController@exportPaymentBankTransfer');
    Route::get('printJournalVoucher', 'JvMasterAPIController@printJournalVoucher');
    Route::get('printPaymentMatching', 'MatchDocumentMasterAPIController@printPaymentMatching');
    Route::get('getSalesQuotationPrintPDF', 'QuotationMasterAPIController@getSalesQuotationPrintPDF');
    Route::post('updateSentCustomerDetail', 'QuotationMasterAPIController@updateSentCustomerDetail');
    Route::get('getBatchSubmissionDetailsPrintPDF', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionDetailsPrintPDF');
    Route::post('generateGeneralLedgerReportPDF', 'FinancialReportAPIController@pdfExportReport');
    Route::get('pvSupplierPrint', 'BankLedgerAPIController@pvSupplierPrint');
    Route::get('loginwithToken', 'UserAPIController@loginwithToken');
    Route::get('downloadFileFrom', 'DocumentAttachmentsAPIController@downloadFileFrom');
    Route::post('login', 'AuthAPIController@auth');
    Route::post('oauth/login_with_token', 'AuthAPIController@authWithToken');
    Route::get('printDeliveryOrder', 'DeliveryOrderAPIController@printDeliveryOrder');
    Route::get('printSalesReturn', 'SalesReturnAPIController@printSalesReturn');
    Route::get('printERPAssetTransfer', 'ERPAssetTransferDetailAPIController@printERPAssetTransfer');
    Route::resource('work_order_generation_logs', 'WorkOrderGenerationLogAPIController');
    Route::resource('external_link_hashes', 'ExternalLinkHashAPIController');
    Route::resource('registered_suppliers', 'RegisteredSupplierAPIController');

    Route::resource('tax_ledgers', 'TaxLedgerAPIController');

    Route::resource('employee_designations', 'EmployeeDesignationAPIController');

    Route::resource('hrms_designations', 'HrmsDesignationAPIController');

    Route::resource('hrms_employee_managers', 'HrmsEmployeeManagerAPIController');


    Route::resource('finance_category_serials', 'FinanceCategorySerialAPIController');

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::get('notification-service', 'NotificationCompanyScenarioAPIController@notification_service');
    Route::get('leave/accrual/service_test', 'LeaveAccrualMasterAPIController@accrual_service_test');
    Route::post('saveCalanderSlots', 'SlotMasterAPIController@saveCalanderSlots');
    Route::get('getFormDataCalander', 'SlotMasterAPIController@getFormDataCalander');
    Route::get('getCalanderSlotData', 'SlotMasterAPIController@getCalanderSlotData');
    Route::post('clanderSlotDateRangeValidation', 'SlotMasterAPIController@clanderSlotDateRangeValidation');
    Route::post('clanderSlotMasterData', 'SlotMasterAPIController@clanderSlotMasterData');
    Route::post('removeCalanderSlot', 'SlotMasterAPIController@removeCalanderSlot');
    Route::post('getAppointments', 'AppointmentAPIController@getAppointments');
    Route::post('getAppointmentList', 'AppointmentAPIController@getAppointmentList');
    Route::post('getAppointmentListSummaryView', 'AppointmentAPIController@getAppointmentListSummaryView');
    Route::get('test', 'TenantAPIController@test');
    Route::get('downloadFileSRM', 'DocumentAttachmentsAPIController@downloadFileSRM');
    Route::get('getSearchSupplierByCompanySRM', 'SupplierMasterAPIController@getSearchSupplierByCompanySRM'); 
    Route::get('updateExemptVATPos', 'ProcumentOrderAPIController@updateExemptVATPos');
    Route::get('downloadFileTender', 'DocumentAttachmentsAPIController@downloadFileTender');
    Route::post('genearetBarcode', 'BarcodeConfigurationAPIController@genearetBarcode');
});


Route::resource('tenants', 'TenantAPIController');

Route::post('sendEmail', 'Email\SendEmailAPIController@sendEmail');

//Route::resource('sales_return_reffered_backs', 'SalesReturnRefferedBackAPIController');

//Route::resource('sales_return_detail_reffered_backs', 'SalesReturnDetailRefferedBackAPIController');



Route::resource('srp_employee_details', 'SrpEmployeeDetailsAPIController');
Route::resource('asset_request_details', 'AssetRequestDetailAPIController');
Route::resource('tax_ledgers', 'TaxLedgerAPIController');
Route::resource('employee_designations', 'EmployeeDesignationAPIController');
Route::resource('hrms_designations', 'HrmsDesignationAPIController');
Route::resource('hrms_employee_managers', 'HrmsEmployeeManagerAPIController');
Route::resource('tax_ledger_details', 'TaxLedgerDetailAPIController');






Route::resource('srp_employee_details', 'SrpEmployeeDetailsAPIController');



Route::resource('monthly_declarations_types', 'MonthlyDeclarationsTypesAPIController');


Route::resource('hr_monthly_deduction_masters', 'HrMonthlyDeductionMasterAPIController');


Route::resource('hr_payroll_masters', 'HrPayrollMasterAPIController');

Route::resource('hr_payroll_header_details', 'HrPayrollHeaderDetailsAPIController');

Route::resource('hr_payroll_details', 'HrPayrollDetailsAPIController');


Route::resource('hr_monthly_deduction_details', 'HrMonthlyDeductionDetailAPIController');

Route::resource('hr_monthly_deduction_details', 'HrMonthlyDeductionDetailAPIController');



Route::resource('h_r_document_description_forms', 'HRDocumentDescriptionFormsAPIController');

Route::resource('h_r_document_description_masters', 'HRDocumentDescriptionMasterAPIController');

Route::resource('h_r_emp_contract_histories', 'HREmpContractHistoryAPIController');

Route::resource('srp_erp_template_masters', 'SrpErpTemplateMasterAPIController');

Route::resource('srp_erp_form_categories', 'SrpErpFormCategoryAPIController');

Route::resource('srp_erp_templates', 'SrpErpTemplatesAPIController');

/*
 * Start SRM related routes
 */

Route::group(['prefix' => 'srm'], function (){
    Route::group(['middleware' => ['tenantById']], function (){
        Route::post('requests', 'SRM\APIController@handleRequest');
        Route::get('getProcumentOrderPrintPDFSRM', 'ProcumentOrderAPIController@getProcumentOrderPrintPDF');
    });

    Route::group(['middleware' => ['tenant']], function (){
        Route::post('fetch', 'SRM\APIController@fetch');
    });
});

/*
 * End SRM related routes
 */

/*
 * Start external related routes
 */

Route::group(['prefix' => 'external'], function (){
    Route::group(['middleware' => ['tenantById','access_token']], function (){
        Route::post('createMaterielRequestsApi', 'MaterielRequestAPIController@createMaterialAPI');
        Route::post('createPurchaseRequestsApi', 'PurchaseRequestAPIController@createPurchaseAPI');
        Route::post('checkLedgerQty', 'ItemMasterAPIController@checkLedgerQty');
    });
});

/*
 * End external related routes
 */



Route::get('cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');

    return 'Cache (cache/config) cleared successfully';
});

Route::get('job-check', function(){
    \App\helper\CommonJobService::job_check();
    return '';
}); 

Route::get('runCronJob/{cron}', function ($cron) {
    Artisan::call($cron);
    return 'CRON Job run successfully';
});








Route::resource('tender_bid_format_masters', 'TenderBidFormatMasterAPIController');

Route::resource('tender_bid_format_details', 'TenderBidFormatDetailAPIController');


Route::resource('tender_field_types', 'TenderFieldTypeAPIController');


Route::resource('tender_masters', 'TenderMasterAPIController');


Route::resource('tender_types', 'TenderTypeAPIController');

Route::resource('envelop_types', 'EnvelopTypeAPIController');


Route::resource('evaluation_types', 'EvaluationTypeAPIController');


Route::resource('procument_activities', 'ProcumentActivityAPIController');

Route::resource('tender_site_visit_dates', 'TenderSiteVisitDatesAPIController');

Route::resource('pricing_schedule_masters', 'PricingScheduleMasterAPIController');


Route::resource('schedule_bid_format_details', 'ScheduleBidFormatDetailsAPIController');


Route::resource('tender_master_suppliers', 'TenderMasterSupplierAPIController');

Route::resource('tender_main_works', 'TenderMainWorksAPIController');  
Route::resource('tender_main_works', 'TenderMainWorksAPIController');
Route::resource('tender_boq_items', 'TenderBoqItemsAPIController');

/* Below two request must be always separated from tenant, auth middlewares */
Route::get('attendance-clock-out', 'HRJobInvokeAPIController@clockOutDebug');
Route::get('attendance-clock-in', 'HRJobInvokeAPIController@attendanceClockIn');
Route::get('attendance-notification-debug', 'HRJobInvokeAPIController@attendance_notification_debug');
/* end of separated from tenant, auth middlewares */

Route::resource('evaluation_criteria_details', 'EvaluationCriteriaDetailsAPIController');

Route::resource('evaluation_criteria_types', 'EvaluationCriteriaTypeAPIController');


Route::resource('tender_criteria_answer_types', 'TenderCriteriaAnswerTypeAPIController');


Route::resource('evaluation_criteria_score_configs', 'EvaluationCriteriaScoreConfigAPIController');




Route::resource('tender_supplier_assignees', 'TenderSupplierAssigneeAPIController');
Route::resource('tender_document_types', 'TenderDocumentTypesAPIController');


Route::resource('calendar_dates', 'CalendarDatesAPIController');

Route::resource('calendar_dates_details', 'CalendarDatesDetailAPIController');



Route::resource('bid_submission_masters', 'BidSubmissionMasterAPIController');

Route::resource('bid_submission_details', 'BidSubmissionDetailAPIController');

Route::resource('third_party_systems', 'ThirdPartySystemsAPIController');

Route::resource('third_party_integration_keys', 'ThirdPartyIntegrationKeysAPIController');








Route::resource('bid_schedules', 'BidScheduleAPIController');

Route::resource('bid_main_works', 'BidMainWorkAPIController');

Route::resource('bid_boqs', 'BidBoqAPIController');




Route::resource('cash_flow_report_details', 'CashFlowReportDetailAPIController');
Route::resource('tender_circulars', 'TenderCircularsAPIController');

Route::resource('po_cutoff_jobs', 'PoCutoffJobAPIController');

Route::resource('po_cutoff_job_datas', 'PoCutoffJobDataAPIController');

Route::resource('i_o_u_booking_masters', 'IOUBookingMasterAPIController');

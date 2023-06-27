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

Route::get('getConfigurationInfo', 'ConfigurationAPIController@getConfigurationInfo');


Route::group(['middleware' => ['tenant','locale']], function () {
    Route::get('getAppearance', 'CompanyAPIController@getAppearance');
    Route::post('postEmployeeFromPortal', 'HelpDesk\HelpDeskAPIController@postEmployee');

    Route::group(['middleware' => ['pos_api']], function (){
        Route::get('pull_tax_details', 'ClubManagement\ClubManagementAPIController@pullTaxDetails');
        Route::get('pull_bank_accounts', 'ClubManagement\ClubManagementAPIController@pullBankAccounts');
        Route::post('post_customer_category', 'ClubManagement\ClubManagementAPIController@createCustomerCategory');
        Route::post('post_receipt_voucher', 'ClubManagement\ClubManagementAPIController@createReceiptVoucher');
        Route::post('post_customer_invoice', 'ClubManagement\ClubManagementAPIController@createCustomerInvoice');
        Route::post('post_customer_master', 'ClubManagement\ClubManagementAPIController@createCustomerMaster');
        Route::post('pull_customer_category', 'POS\PosAPIController@pullCustomerCategory');
        Route::post('pull_location', 'POS\PosAPIController@pullLocation');
        Route::post('pull_segment', 'POS\PosAPIController@pullSegment');
        Route::post('pull_chart_of_account', 'POS\PosAPIController@pullChartOfAccount');
        Route::post('pull_chart_of_account_master', 'POS\PosAPIController@pullChartOfAccountMaster');
        Route::post('pull_unit_of_measure', 'POS\PosAPIController@pullUnitOfMeasure');
        Route::post('pull_unit_conversion', 'POS\PosAPIController@pullUnitConversion');
        Route::post('pull_warehouse', 'POS\PosAPIController@pullWarehouse');
        Route::post('pull_warehouse_item', 'POS\PosAPIController@pullWarehouseItem');
        Route::post('srp_erp_warehousebinlocation', 'POS\PosAPIController@pullWarehouseBinLocation');
        Route::post('pull_item', 'POS\PosAPIController@pullItem');
        Route::post('pull_item_bin_location', 'POS\PosAPIController@pullItemBinLocation');
        Route::post('pull_item_sub_category', 'POS\PosAPIController@pullItemSubCategory');
        Route::post('pull_items_by_sub_category', 'POS\PosAPIController@pullItemsBySubCategory');
        Route::post('pull_user', 'POS\PosAPIController@pullUser');
        Route::post('pull_item_category', 'POS\PosAPIController@pullItemCategory');
        Route::post('posMappingRequest', 'POS\PosAPIController@handleRequest');
        Route::post('pull_supplier_master', 'POS\PosAPIController@pullSupplierMaster');
        Route::post('pull_customer_master', 'POS\PosAPIController@pullCustomerMaster');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['middleware' => 'authorization:api'], function () {

            require __DIR__.'/../routes/systemAdmin/systemAdminRoutes.php';
            require __DIR__.'/../routes/general/generalRoutes.php';
            require __DIR__.'/../routes/srm/srmRoutes.php';
            require __DIR__.'/../routes/configuration/configurationRoutes.php';
            require __DIR__.'/../routes/approvalSetup/approvalSetupRoutes.php';
            require __DIR__.'/../routes/inventory/inventoryRoutes.php';
            require __DIR__.'/../routes/procurement/procurementRoutes.php';
            require __DIR__.'/../routes/accountsPayable/accountsPayableRoutes.php';
            require __DIR__.'/../routes/accountsReceivable/accountsReceivableRoutes.php';
            require __DIR__.'/../routes/salesAndMarketing/salesAndMarketingRoutes.php';

            Route::post('getAllEmployees', 'EmployeeAPIController@getAllEmployees');

            Route::resource('employeeMasterCRUD', 'EmployeeAPIController');
            Route::resource('employee_navigations', 'EmployeeNavigationAPIController');

            Route::resource('navigation_menuses', 'NavigationMenusAPIController');

            Route::resource('navigation_user_group_setups', 'NavigationUserGroupSetupAPIController');

            Route::get('user/companies', 'UserAPIController@userCompanies');
            Route::get('checkUser', 'UserAPIController@checkUser');

            Route::get('getPOSuppliers', 'SupplierMasterAPIController@getPOSuppliers');
            Route::get('getRetentionPercentage', 'SupplierMasterAPIController@getRetentionPercentage');
            Route::get('getSuppliersByCompany', 'SupplierMasterAPIController@getSuppliersByCompany');
            Route::post('getAllRegisteredSupplierApproval', 'SupplierMasterAPIController@getAllRegisteredSupplierApproval');

            Route::resource('registered_supplier_currencies', 'RegisteredSupplierCurrencyAPIController');
            Route::resource('registered_bank_memo_suppliers', 'RegisteredBankMemoSupplierAPIController');

            Route::get('user/menu', 'NavigationUserGroupSetupAPIController@userMenu');
            Route::get('getUserMenu', 'NavigationUserGroupSetupAPIController@getUserMenu');


            Route::group(['middleware' => 'max_memory_limit'], function () {
                Route::group(['middleware' => 'max_execution_limit'], function () {
                    Route::post('generateAMReport', 'AssetManagementReportAPIController@generateReport');
                    Route::post('exportAMReport', 'AssetManagementReportAPIController@exportReport');
                    Route::post('exportAssetMaster', 'FixedAssetMasterAPIController@exportAssetMaster');
                    Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');
                    Route::post('getAssetDepPeriodsByID', 'FixedAssetDepreciationPeriodAPIController@getAssetDepPeriodsByID');

                });
            });

            Route::get('getCompanyReportingCurrency', 'CurrencyMasterAPIController@getCompanyReportingCurrency');
            Route::get('getCompanyReportingCurrencyCode', 'CurrencyMasterAPIController@getCompanyReportingCurrencyCode');
            Route::get('getCompanyLocalCurrencyCode', 'CurrencyMasterAPIController@getCompanyLocalCurrencyCode');
            Route::get('checkSelectedSupplierIsActive', 'SupplierAssignedAPIController@checkSelectedSupplierIsActive');
            Route::resource('users', 'UserAPIController');
            Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');

            Route::resource('country_masters', 'CountryMasterAPIController');
            Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');
            Route::resource('supplier_category_subs', 'SupplierCategorySubAPIController');

            Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');

            Route::resource('supplier_importances', 'SupplierImportanceAPIController');

            Route::resource('suppliernatures', 'suppliernatureAPIController');

            Route::resource('supplier_types', 'SupplierTypeAPIController');

            Route::resource('supplier_currencies', 'SupplierCurrencyAPIController');

            Route::resource('supplier_criticals', 'SupplierCriticalAPIController');

            Route::resource('yes_no_selections', 'YesNoSelectionAPIController');

            Route::resource('document_masters', 'DocumentMasterAPIController');

            Route::resource('supplier_contact_types', 'SupplierContactTypeAPIController');

            Route::get('getBankMemoBySupplierCurrencyId', 'BankMemoSupplierAPIController@getBankMemoBySupplierCurrencyId');

            Route::resource('bank_memo_supplier_masters', 'BankMemoSupplierMasterAPIController');

            Route::post('getCurrencyDetails', 'SupplierCurrencyAPIController@getCurrencyDetails');

            Route::resource('units', 'UnitAPIController');

            Route::post('financeItemCategorySubsAttributesUpdate', 'FinanceItemCategorySubAPIController@financeItemCategorySubsAttributesUpdate');

            Route::resource('finance_item_category_masters', 'FinanceItemCategoryMasterAPIController');

            Route::resource('reasonCodeMasters', 'ReasonCodeMasterAPIController');
            Route::post('getAllReasonCodeMaster', 'ReasonCodeMasterAPIController@getAllReasonCodeMaster');
            Route::post('updateReasonCodeMaster', 'ReasonCodeMasterAPIController@update');
            Route::get('getAllGLCodesForReasonMaster', 'ReasonCodeMasterAPIController@getAllGLCodes');
            Route::get('reasonCodeMasterRecordSalesReturn/{id}', 'ReasonCodeMasterAPIController@reasonCodeMasterRecordSalesReturn');

            Route::resource('example_table_templates', 'ExampleTableTemplateAPIController');

            Route::get('getItemMasterPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@getItemMasterPurchaseRequestHistory');

            Route::post('getSubcategoriesBymainCategories', 'FinanceItemCategorySubAPIController@getSubcategoriesBymainCategories');
            
            Route::get('getDropdownValues', 'FinanceItemCategoryMasterAPIController@getDropdownValues');

            Route::post('addItemAttributes', 'FinanceItemCategoryMasterAPIController@addItemAttributes');

            Route::resource('erp_attributes', 'ErpAttributesAPIController');
            Route::post('itemAttributesIsMandotaryUpdate', 'ErpAttributesAPIController@itemAttributesIsMandotaryUpdate');
            Route::post('itemAttributesDelete', 'ErpAttributesAPIController@itemAttributesDelete');

            Route::resource('erp_attributes_dropdowns', 'ErpAttributesDropdownAPIController');
            Route::post('addDropdownData', 'ErpAttributesDropdownAPIController@addDropdownData');
            Route::post('getDropdownData', 'ErpAttributesDropdownAPIController@getDropdownData');

            Route::resource('erp_attributes_field_types', 'ErpAttributesFieldTypeAPIController');

            /** Company Navigation Menu access*/
            
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
            Route::resource('user_group_assigns', 'UserGroupAssignAPIController');
                        
            Route::resource('approval_roles', 'ApprovalRoleAPIController');
            Route::resource('department_masters', 'DepartmentMasterAPIController');            
            Route::get('getAllApprovalGroup', 'ApprovalGroupsAPIController@getAllApprovalGroup');

            /** Chart of Account Created by Shafri */

            Route::get('getAssignedChartOfAccounts', 'ChartOfAccountsAssignedAPIController@getAssignedChartOfAccounts');

            Route::resource('erp_locations', 'ErpLocationAPIController');
            Route::resource('accounts_types', 'AccountsTypeAPIController');

            /** Segment master Created by Nazir  */

            Route::post('getAllSegmentMaster', 'SegmentMasterAPIController@getAllSegmentMaster');
            Route::get('getSegmentMasterFormData', 'SegmentMasterAPIController@getSegmentMasterFormData');
            Route::get('getOrganizationStructure', 'SegmentMasterAPIController@getOrganizationStructure');
            Route::resource('segment/masters', 'SegmentMasterAPIController');

            Route::post('updateSegmentMaster', 'SegmentMasterAPIController@updateSegmentMaster');

            Route::post('getAllCustomersByCompany', 'CustomerAssignedAPIController@getAllCustomersByCompany');

            Route::get('getCustomerByCompany', 'CustomerMasterAPIController@getCustomerByCompany');

            //confirmation
            Route::post('confirmDocument', 'PurchaseRequestAPIController@confirmDocument');

            Route::get('getGRVBasedPODropdowns', 'ProcumentOrderAPIController@getGRVBasedPODropdowns');
            
            Route::resource('priorities', 'PriorityAPIController');

            Route::resource('locations', 'LocationAPIController');

            Route::resource('yes_no_selection_for_minuses', 'YesNoSelectionForMinusAPIController');

            Route::resource('months', 'MonthsAPIController');
            
            Route::post('delete-item-qnty-by-pr', 'PurchaseRequestAPIController@delteItemQntyPR');

            Route::resource('document_approveds', 'DocumentApprovedAPIController');

            Route::resource('bank_accounts', 'BankAccountAPIController');
            Route::post('getAllBankAccountByCompany', 'BankAccountAPIController@getAllBankAccountByCompany');

            Route::post('getBankBalance', 'BankAccountAPIController@getBankBalance');
            Route::get('getBankAccountsByBankID', 'BankAccountAPIController@getBankAccountsByBankID');
            Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');

            Route::resource('poPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');

            Route::resource('srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController');
            Route::get('get_srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController@geDocumentAttachments');

            Route::resource('sme-attachment', 'AttachmentSMEAPIController');
            Route::get('sme-attachment/{id}/{docID}/{companyID}', 'AttachmentSMEAPIController@show');

            Route::post('getAllSupplierMasterApproval', 'SupplierMasterAPIController@getAllSupplierMasterApproval');
            Route::post('getAllCustomerMasterApproval', 'CustomerMasterAPIController@getAllCustomerMasterApproval');
            Route::post('getAllChartOfAccountApproval', 'ChartOfAccountAPIController@getAllChartOfAccountApproval');

            Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');
            
            Route::post('updatePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@updatePoPaymentTermsLogistic');

            Route::post('approveSupplier', 'SupplierMasterAPIController@approveSupplier');
            Route::post('approveRegisteredSupplier', 'SupplierMasterAPIController@approveRegisteredSupplier');
            Route::post('rejectSupplier', 'SupplierMasterAPIController@rejectSupplier');
            Route::post('rejectRegisteredSupplier', 'SupplierMasterAPIController@rejectRegisteredSupplier');

            Route::post('getAllItemsMasterApproval', 'ItemMasterAPIController@getAllItemsMasterApproval')->name("Get All Items Master Approval");

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
            
            
            Route::post('getGRVDrilldownSpentAnalysis', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysis');
            Route::post('getGRVDrilldownSpentAnalysisTotal', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysisTotal');
            
            /** Po Related Tables Created by Nazir  */
            Route::resource('erp_addresses', 'ErpAddressAPIController');
            Route::resource('po_payment_terms', 'PoPaymentTermsAPIController');
            Route::resource('po_advance_payments', 'PoAdvancePaymentAPIController');
            
            Route::get('getAdvancePaymentRequestStatusHistory', 'ProcumentOrderAPIController@getAdvancePaymentRequestStatusHistory');

            Route::get('reportSpentAnalysisBySupplierFilter', 'ProcumentOrderAPIController@reportSpentAnalysisBySupplierFilter');
            Route::post('reportSpentAnalysis', 'ProcumentOrderAPIController@reportSpentAnalysis');
            Route::post('reportSpentAnalysisExport', 'ProcumentOrderAPIController@reportSpentAnalysisExport');
            Route::post('reportSpentAnalysisDrilldownExport', 'ProcumentOrderAPIController@reportSpentAnalysisDrilldownExport');
            Route::post('reportSpentAnalysisHeader', 'ProcumentOrderAPIController@reportSpentAnalysisHeader');
            Route::post('reportPoEmployeePerformance', 'ProcumentOrderAPIController@reportPoEmployeePerformance');
            
            Route::post('reportPrToGrv', 'PurchaseRequestAPIController@reportPrToGrv');
            Route::post('exportPrToGrvReport', 'PurchaseRequestAPIController@exportPrToGrvReport');

            Route::post('reportPoToPayment', 'ProcumentOrderAPIController@reportPoToPayment');
            Route::post('exportPoToPaymentReport', 'ProcumentOrderAPIController@exportPoToPaymentReport');
            Route::get('reportPoToPaymentFilterOptions', 'ProcumentOrderAPIController@reportPoToPaymentFilterOptions');
            Route::get('getReportSavingFliterData', 'ProcumentOrderAPIController@getReportSavingFliterData');

            Route::get('reportPrToGrvFilterOptions', 'PurchaseRequestAPIController@reportPrToGrvFilterOptions');

            Route::get('exchangerate', 'ApprovalLevelAPIController@confirmDocTest');

            Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');

            Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');

            Route::resource('purchase_order_process_details', 'PurchaseOrderProcessDetailsAPIController');

            Route::resource('tax_types', 'TaxTypeAPIController');

            Route::resource('advance_payment_details', 'AdvancePaymentDetailsAPIController');

            Route::resource('alerts', 'AlertAPIController');
            Route::resource('access_tokens', 'AccessTokensAPIController');
            Route::resource('users_log_histories', 'UsersLogHistoryAPIController');


            Route::resource('addresses', 'AddressAPIController');
            Route::post('getAllAddresses', 'AddressAPIController@getAllAddresses');
            Route::get('getAddressFormData', 'AddressAPIController@getAddressFormData');

            Route::resource('address_types', 'AddressTypeAPIController');
            
            Route::resource('company_policy_categories', 'CompanyPolicyCategoryAPIController');
            
            Route::post('exportPoEmployeePerformance', 'ProcumentOrderAPIController@exportPoEmployeePerformance');

            Route::post('reportOrderStatus', 'PurchaseOrderStatusAPIController@reportOrderStatus');
            Route::get('reportOrderStatusFilterOptions', 'PurchaseOrderStatusAPIController@reportOrderStatusFilterOptions');
            Route::post('reportOrderStatusPreCheck', 'PurchaseOrderStatusAPIController@reportOrderStatusPreCheck');
            Route::post('exportReportOrderStatus', 'PurchaseOrderStatusAPIController@exportReportOrderStatus');
         
            Route::resource('g_r_v_types', 'GRVTypesAPIController');
            Route::resource('budget_consumed_datas', 'BudgetConsumedDataAPIController');
            Route::post('getBudgetConsumptionForReview', 'BudgetConsumedDataAPIController@getBudgetConsumptionForReview');
            Route::post('getBudgetConsumptionByDoc', 'BudgetConsumedDataAPIController@getBudgetConsumptionByDoc');
            Route::post('changeBudgetConsumption', 'BudgetConsumedDataAPIController@changeBudgetConsumption');
            Route::resource('customer_invoices', 'CustomerInvoiceAPIController');
            Route::resource('company_finance_years', 'CompanyFinanceYearAPIController');
            Route::resource('company_finance_periods', 'CompanyFinancePeriodAPIController');
            Route::resource('customer_invoices', 'CustomerInvoiceAPIController');
            Route::resource('accounts_receivable_ledgers', 'AccountsReceivableLedgerAPIController');
      
            Route::get('getAllFinancePeriodBasedFY', 'CompanyFinancePeriodAPIController@getAllFinancePeriodBasedFY')->name("Get All Finance Period Based FY");
            Route::get('getAllFinancePeriodForYear', 'CompanyFinancePeriodAPIController@getAllFinancePeriodForYear')->name("Get All Finance Period For Year");
            
            Route::resource('item_issue_types', 'ItemIssueTypeAPIController');

            Route::resource('accounts_payable_ledgers', 'AccountsPayableLedgerAPIController');
            Route::get('getAPFilterData', 'AccountsPayableReportAPIController@getAPFilterData');
            Route::post('validateAPReport', 'AccountsPayableReportAPIController@validateAPReport');
            Route::post('generateAPReport', 'AccountsPayableReportAPIController@generateAPReport');
            Route::post('exportAPReport', 'AccountsPayableReportAPIController@exportReport');

            Route::post('exportNavigationeport', 'UserGroupAssignAPIController@exportNavigationeport');

            Route::get('getFRFilterData', 'FinancialReportAPIController@getFRFilterData');
            Route::get('getUtilizationFilterFormData', 'FinancialReportAPIController@getUtilizationFilterFormData');
            Route::post('getSubsidiaryCompanies', 'FinancialReportAPIController@getSubsidiaryCompanies');
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

            Route::get('getCurrentUserInfo', 'UserAPIController@getCurrentUserInfo')->name("Get Current User Info");
            Route::get('getNotifications', 'UserAPIController@getNotifications');
            Route::post('updateNotification', 'UserAPIController@updateNotification');
            Route::post('getAllNotifications', 'UserAPIController@getAllNotifications');
            Route::get('getImageByPath', 'DocumentAttachmentsAPIController@getImageByPath');

            Route::resource('poDetails_reffered_histories', 'PurchaseOrderDetailsRefferedHistoryAPIController');
            Route::resource('poAdv_payment_refferedbacks', 'PurchaseOrderAdvPaymentRefferedbackAPIController');
            Route::resource('po_payment_terms_refferedbacks', 'PoPaymentTermsRefferedbackAPIController');
            Route::resource('document_refered_histories', 'DocumentReferedHistoryAPIController');
            
            Route::resource('poAddonsReffered_backs', 'PoAddonsRefferedBackAPIController');
            Route::resource('years', 'YearAPIController');
            Route::resource('unbilled_grv_group_bies', 'UnbilledGrvGroupByAPIController');
            Route::resource('employee_profiles', 'EmployeeProfileAPIController');

            Route::resource('employee_details', 'EmployeeDetailsAPIController');
            Route::resource('designations', 'DesignationAPIController');

            Route::resource('prDetailsReferedHistories', 'PrDetailsReferedHistoryAPIController');
            Route::resource('contracts', 'ContractAPIController');
            
            Route::post('getAllDocumentApproval', 'DocumentApprovedAPIController@getAllDocumentApproval');
            Route::post('getAllDocumentApprovalTest', 'DocumentApprovedAPIController@getAllDocumentApproval');
            //Route::get('getTotalCountOfApproval', 'DocumentApprovedAPIController@getTotalCountOfApproval');

            // Payment Voucher
            Route::resource('pay_supplier_invoice_masters', 'PaySupplierInvoiceMasterAPIController', ['only' => ['store', 'show', 'update']]);
            Route::resource('pay_supplier_invoice_details', 'PaySupplierInvoiceDetailAPIController',['except' => ['index','store']]);
            Route::resource('direct_payment_details', 'DirectPaymentDetailsAPIController',['except' => ['index']]);
            Route::resource('advance_payment_details', 'AdvancePaymentDetailsAPIController',['except' => ['index','store']]);
            Route::post('addPVDetailsByInterCompany', 'DirectPaymentDetailsAPIController@addPVDetailsByInterCompany');
            Route::post('pv-md-deduction-type', 'DirectPaymentDetailsAPIController@updat_monthly_deduction');
            Route::post('generatePdcForPv', 'PaySupplierInvoiceMasterAPIController@generatePdcForPv');
            Route::get('validationsForPDC', 'PaySupplierInvoiceMasterAPIController@validationsForPDC');
            Route::post('updateBankBalance', 'PaySupplierInvoiceMasterAPIController@updateBankBalance');
            Route::put('paymentVoucherUpdateCurrency/{id}', 'PaySupplierInvoiceMasterAPIController@updateCurrency');
            Route::put('paymentVoucherProjectUpdate/{id}', 'PaySupplierInvoiceMasterAPIController@paymentVoucherProjectUpdate');
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
            Route::post('getAllInvReclassificationByCompany', 'InventoryReclassificationAPIController@getAllInvReclassificationByCompany');
            
            Route::get('getJobsByContractAndCustomer', 'CustomerMasterAPIController@getJobsByContractAndCustomer');
            Route::post('addADVPaymentDetailNotLinkPo', 'AdvancePaymentDetailsAPIController@addADVPaymentDetailNotLinkPo');

            Route::resource('performa_details', 'PerformaDetailsAPIController');
            Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');
            Route::resource('ticket_masters', 'TicketMasterAPIController');
            Route::resource('field_masters', 'FieldMasterAPIController');
            Route::resource('inv_reclassification_details', 'InventoryReclassificationDetailAPIController');
            Route::resource('inv_reclassifications', 'InventoryReclassificationAPIController');
            Route::get('getInvReclassificationAudit', 'InventoryReclassificationAPIController@getInvReclassificationAudit');
            
            Route::get('getItemsOptionForReclassification', 'InventoryReclassificationAPIController@getItemsOptionForReclassification');
            Route::get('getItemsByReclassification', 'InventoryReclassificationDetailAPIController@getItemsByReclassification');

            Route::post('invRecalssificationReopen', 'InventoryReclassificationAPIController@invRecalssificationReopen');

            Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');

            Route::resource('performa_details', 'PerformaDetailsAPIController');

            Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');

            Route::resource('ticket_masters', 'TicketMasterAPIController');

            Route::resource('field_masters', 'FieldMasterAPIController');
            Route::resource('inv_reclassification_details', 'InventoryReclassificationDetailAPIController');

            Route::resource('inv_reclassifications', 'InventoryReclassificationAPIController');

            Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');
            Route::get('getDebitNoteMasterRecord', 'DebitNoteAPIController@getDebitNoteMasterRecord');
            Route::resource('debit_notes', 'DebitNoteAPIController');
            Route::put('debitNoteUpdateCurrency/{id}', 'DebitNoteAPIController@updateCurrency');
            Route::put('updateDebiteNoteType/{id}', 'DebitNoteAPIController@updateDebiteNoteType');
            Route::post('getAllDebitNotes', 'DebitNoteAPIController@getAllDebitNotes');
            Route::post('exportDebitNotesByCompany', 'DebitNoteAPIController@exportDebitNotesByCompany');
            
            Route::post('debitNoteReopen', 'DebitNoteAPIController@debitNoteReopen');
            
            Route::resource('debit_note_details', 'DebitNoteDetailsAPIController');
            Route::get('getDetailsByDebitNote', 'DebitNoteDetailsAPIController@getDetailsByDebitNote');
            Route::get('getDebitNotePaymentStatusHistory', 'DebitNoteAPIController@getDebitNotePaymentStatusHistory');
            Route::post('amendDebitNote', 'DebitNoteAPIController@amendDebitNote');
            Route::post('amendDebitNoteReview', 'DebitNoteAPIController@amendDebitNoteReview');
            
            Route::post('checkPaymentStatusDNPrint', 'DebitNoteAPIController@checkPaymentStatusDNPrint');

            Route::resource('performa_masters', 'PerformaMasterAPIController');
            Route::resource('rig_masters', 'RigMasterAPIController');


            Route::get('getDirectInvoiceGL', 'ChartOfAccountsAssignedAPIController@getDirectInvoiceGL')->name("Get Direct Invoice GL");
            Route::get('getGLForJournalVoucherDirect', 'ChartOfAccountsAssignedAPIController@getGLForJournalVoucherDirect');


            Route::post('getglDetails','ChartOfAccountsAssignedAPIController@getglDetails');
            Route::post('erp_project_masters/get_gl_accounts','ChartOfAccountsAssignedAPIController@getGlAccounts');
            Route::resource('project_gl_details', 'ProjectGlDetailAPIController');

            //Logistic Configuration Master
            
            Route::resource('port_masters', 'PortMasterAPIController');
            Route::resource('delivery_terms_masters', 'DeliveryTermsMasterAPIController');
            
            Route::get('getPaymentVoucherGL', 'ChartOfAccountsAssignedAPIController@getPaymentVoucherGL');

            Route::get('getFilteredGRV', 'GRVMasterAPIController@getFilteredGRV')->name("Get Filtered GRV");

            Route::post('approveSupplierInvoice', 'BookInvSuppMasterAPIController@approveSupplierInvoice');
            


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

            Route::post('recieptVoucherDataTable', 'CustomerReceivePaymentAPIController@recieptVoucherDataTable');
            Route::get('getReceiptVoucherMasterRecord', 'CustomerReceivePaymentAPIController@getReceiptVoucherMasterRecord');
            Route::post('receiptVoucherReopen', 'CustomerReceivePaymentAPIController@receiptVoucherReopen');
            Route::post('amendReceiptVoucher', 'CustomerReceivePaymentAPIController@amendReceiptVoucher');
            Route::post('amendReceiptVoucherReview', 'CustomerReceivePaymentAPIController@amendReceiptVoucherReview');
            Route::post('receiptVoucherCancel', 'CustomerReceivePaymentAPIController@receiptVoucherCancel');
            Route::put('recieptVoucherLocalUpdate/{id}', 'CustomerReceivePaymentAPIController@recieptVoucherLocalUpdate');
            Route::put('recieptVoucherReportingUpdate/{id}','CustomerReceivePaymentAPIController@recieptVoucherReportingUpdate');

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
            Route::get('getSIDetailGRVAmendHistory', 'BookInvSuppDetRefferedBackAPIController@getSIDetailGRVAmendHistory');
            Route::get('getSIDetailDirectAmendHistory', 'DirectInvoiceDetailsRefferedBackAPIController@getSIDetailDirectAmendHistory');


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
            Route::get('assetCostingForPrint', 'FixedAssetMasterAPIController@assetCostingForPrint');
            Route::post('updateCustomerReciept', 'CustomerReceivePaymentDetailAPIController@updateCustomerReciept');
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
            
            Route::get('getBudgetConsumptionByDocument', 'BudgetMasterAPIController@getBudgetConsumptionByDocument');
            Route::post('syncGlBudget', 'BudjetdetailsAPIController@syncGlBudget');
            Route::post('getBudgetDetailHistory', 'BudjetdetailsAPIController@getBudgetDetailHistory');

            Route::get('checkPolicyForExchangeRates', 'CommonPoliciesAPIController@checkPolicyForExchangeRates');

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
            
            
            
            Route::resource('fixed_asset_depreciation_periods', 'FixedAssetDepreciationPeriodAPIController');
            Route::post('exportAMDepreciation', 'FixedAssetDepreciationPeriodAPIController@exportAMDepreciation');
            Route::resource('asset_types', 'AssetTypeAPIController');
            
            

            Route::resource('h_r_m_s_jv_details', 'HRMSJvDetailsAPIController');
            Route::resource('h_r_m_s_jv_masters', 'HRMSJvMasterAPIController');
            Route::resource('accruaval_from_o_p_masters', 'AccruavalFromOPMasterAPIController');
            Route::resource('fixed_asset_costs', 'FixedAssetCostAPIController');
            Route::resource('insurance_policy_types', 'InsurancePolicyTypeAPIController');
            Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');


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
            
            Route::get('downloadQuotationItemUploadTemplate','QuotationMasterAPIController@downloadQuotationItemUploadTemplate');

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

            Route::post('payeeBankMemoDeleteAll', 'BankMemoPayeeAPIController@payeeBankMemoDeleteAll');

            Route::post('getCreditNoteAmendHistory', 'CreditNoteReferredbackAPIController@getCreditNoteAmendHistory');
            Route::resource('creditNoteReferredbackCRUD', 'CreditNoteReferredbackAPIController');
            Route::resource('creditNoteDetailsRefferdbacks', 'CreditNoteDetailsRefferdbackAPIController');
            Route::get('getCapitalizationLinkedDocument', 'AssetCapitalizationAPIController@getCapitalizationLinkedDocument');
            Route::get('getCNDetailAmendHistory', 'CreditNoteDetailsRefferdbackAPIController@getCNDetailAmendHistory');

            Route::resource('customerInvoiceRefferedbacksCRUD', 'CustomerInvoiceDirectRefferedbackAPIController');
            Route::resource('customerInvoiceDetRefferedbacks', 'CustomerInvoiceDirectDetRefferedbackAPIController');
            Route::post('getCIMasterAmendHistory', 'CustomerInvoiceDirectRefferedbackAPIController@getCIMasterAmendHistory');
            Route::get('getCIDetailsForAmendHistory', 'CustomerInvoiceDirectDetRefferedbackAPIController@getCIDetailsForAmendHistory');

            Route::resource('supplier_category_icv_subs', 'SupplierCategoryICVSubAPIController');
            Route::resource('supplier_category_icv_masters', 'SupplierCategoryICVMasterAPIController');


            Route::resource('debitNoteDetailsRefferedbacks', 'DebitNoteDetailsRefferedbackAPIController');
            Route::resource('debitNoteMasterRefferedbacksCRUD', 'DebitNoteMasterRefferedbackAPIController');
            Route::post('getDebitNoteAmendHistory', 'DebitNoteMasterRefferedbackAPIController@getDebitNoteAmendHistory');
            Route::get('getDNDetailAmendHistory', 'DebitNoteDetailsRefferedbackAPIController@getDNDetailAmendHistory');
            Route::put('debitNoteLocalUpdate/{id}', 'DebitNoteAPIController@debitNoteLocalUpdate');
            Route::put('debitNoteReportingUpdate/{id}','DebitNoteAPIController@debitNoteReportingUpdate');

            Route::resource('jvMasterReferredbacks', 'JvMasterReferredbackAPIController');
            Route::resource('jvDetailsReferredbacks', 'JvDetailsReferredbackAPIController');
            Route::post('getJournalVoucherAmendHistory', 'JvMasterReferredbackAPIController@getJournalVoucherAmendHistory');
            Route::get('getJVDetailAmendHistory', 'JvDetailsReferredbackAPIController@getJVDetailAmendHistory');

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

            Route::resource('asset_capitalization_referreds', 'AssetCapitalizationReferredAPIController');
            Route::resource('asset_capitalizatio_det_referreds', 'AssetCapitalizatioDetReferredAPIController');
            Route::resource('asset_disposal_referreds', 'AssetDisposalReferredAPIController');
            Route::resource('asset_disposal_detail_referreds', 'AssetDisposalDetailReferredAPIController');

            Route::resource('bankTransferRefferedBack', 'PaymentBankTransferRefferedBackAPIController');
            Route::post('getReferBackHistoryByBankTransfer', 'PaymentBankTransferRefferedBackAPIController@getReferBackHistoryByBankTransfer');
            Route::resource('bankTransferDetailRefferedBacks', 'PaymentBankTransferDetailRefferedBackAPIController');

            Route::resource('bankRecRefferedBack', 'BankReconciliationRefferedBackAPIController');
            Route::post('getReferBackHistoryByBankRec', 'BankReconciliationRefferedBackAPIController@getReferBackHistoryByBankRec');

            Route::resource('grvDetailsRefferedbacks', 'GrvDetailsRefferedbackAPIController');
            Route::resource('document_restriction_assigns', 'DocumentRestrictionAssignAPIController');
            Route::resource('document_restriction_policies', 'DocumentRestrictionPolicyAPIController');

            Route::post('getEmployeeMasterView', 'EmployeeAPIController@getEmployeeMasterView');
            Route::post('confirmEmployeePasswordReset', 'EmployeeAPIController@confirmEmployeePasswordReset');
            Route::get('getEmployeeMasterData', 'EmployeeAPIController@getEmployeeMasterData');
            Route::get('getReferBackApprovedDetails', 'DocumentReferedHistoryAPIController@getReferBackApprovedDetails');

            Route::post('getBankMasterByCompany', 'BankAssignAPIController@getBankMasterByCompany');
            Route::post('getAccountsByBank', 'BankAccountAPIController@getAccountsByBank');
            Route::get('getBankLedgerFilterFormData', 'BankLedgerAPIController@getBankLedgerFilterFormData');
            Route::post('validateBankLedgerReport', 'BankLedgerAPIController@validateBankLedgerReport');
            Route::post('generateBankLedgerReport', 'BankLedgerAPIController@generateBankLedgerReport');
            Route::post('exportBankLedgerReport', 'BankLedgerAPIController@exportBankLedgerReport');
            Route::post('generateBankLedgerReportPDF', 'BankLedgerAPIController@generateBankLedgerReportPDF');
            
            
            Route::get('getBankAccountFormData', 'BankAccountAPIController@getBankAccountFormData');
            Route::post('getBankAccountApprovalByUser', 'BankAccountAPIController@getBankAccountApprovalByUser');
            Route::post('getBankAccountApprovedByUser', 'BankAccountAPIController@getBankAccountApprovedByUser');
            Route::get('bankAccountAudit', 'BankAccountAPIController@bankAccountAudit');
            Route::post('bankAccountReopen', 'BankAccountAPIController@bankAccountReopen');
            Route::post('bankAccountReferBack', 'BankAccountAPIController@bankAccountReferBack');
            Route::resource('bank_account_reffered_backs', 'BankAccountRefferedBackAPIController');
            
            Route::resource('bankAccountReferedBack', 'BankAccountRefferedBackAPIController');
            Route::post('getAccountsReferBackHistory', 'BankAccountRefferedBackAPIController@getAccountsReferBackHistory');

            Route::post('getFinancialYearsByCompany', 'CompanyFinanceYearAPIController@getFinancialYearsByCompany');
            Route::get('getFinanceYearFormData', 'CompanyFinanceYearAPIController@getFinanceYearFormData');
            Route::post('getFinancialPeriodsByYear', 'CompanyFinancePeriodAPIController@getFinancialPeriodsByYear');
            Route::resource('companyFinanceYearPeriodMasters', 'CompanyFinanceYearperiodMasterAPIController');

            Route::resource('counter', 'CounterAPIController');
            Route::post('getCountersByCompany', 'CounterAPIController@getCountersByCompany');
            Route::get('getCounterFormData', 'CounterAPIController@getCounterFormData');

            Route::resource('posPaymentGlConfigMasters', 'GposPaymentGlConfigMasterAPIController');
            Route::resource('posPaymentGlConfigDetails', 'GposPaymentGlConfigDetailAPIController');
            Route::post('getPosGlConfigByCompany', 'GposPaymentGlConfigDetailAPIController@getConfigByCompany');
            Route::get('getPosGlConfigFormData', 'GposPaymentGlConfigDetailAPIController@getFormData');
            Route::get('getPosShiftDetails', 'ShiftDetailsAPIController@getPosShiftDetails');

            Route::get('getPosSourceShiftDetails', 'ShiftDetailsAPIController@getPosSourceShiftDetails');
            Route::get('getPosCustomerMasterDetails', 'ShiftDetailsAPIController@getPosCustomerMasterDetails');
            Route::get('getPosCustomerMasterDetails', 'ShiftDetailsAPIController@getPosCustomerMasterDetails');
            Route::post('postPosCustomerMapping', 'ShiftDetailsAPIController@postPosCustomerMapping');
            Route::post('postPosTaxMapping', 'ShiftDetailsAPIController@postPosTaxMapping');
            Route::post('postPosPayMapping', 'ShiftDetailsAPIController@postPosPayMapping');
            Route::post('postPosEntries', 'ShiftDetailsAPIController@postPosEntries');

            Route::resource('currency_denominations', 'CurrencyDenominationAPIController');
            Route::resource('shift_details', 'ShiftDetailsAPIController');
            Route::get('getPosCustomerSearch', 'CustomerMasterAPIController@getPosCustomerSearch');
      
            Route::resource('docEmailNotificationMasters', 'DocumentEmailNotificationMasterAPIController');
            Route::resource('salesPersonMasters', 'SalesPersonMasterAPIController');
            Route::resource('salesPersonTargets', 'SalesPersonTargetAPIController');
            Route::post('getAllSalesPersons', 'SalesPersonMasterAPIController@getAllSalesPersons');
            Route::get('getSalesPersonFormData', 'SalesPersonMasterAPIController@getSalesPersonFormData');
            Route::get('checkSalesPersonLastTarget', 'SalesPersonTargetAPIController@checkSalesPersonLastTarget');
            Route::get('getSalesPersonTargetDetails', 'SalesPersonTargetAPIController@getSalesPersonTargetDetails');

            Route::post('getOrderDetailsForSQ', 'QuotationMasterAPIController@getOrderDetailsForSQ');
            Route::post('checkItemExists','QuotationMasterAPIController@checkItemExists');

            Route::resource('gposInvoices', 'GposInvoiceAPIController');
            Route::get('getInvoiceDetails', 'GposInvoiceAPIController@getInvoiceDetails');
            Route::post('getInvoicesByShift', 'GposInvoiceAPIController@getInvoicesByShift');
            Route::resource('gposInvoiceDetails', 'GposInvoiceDetailAPIController');
            Route::resource('gposInvoicePayments', 'GposInvoicePaymentsAPIController');

            Route::resource('quotationVersionDetails', 'QuotationVersionDetailsAPIController');

            Route::resource('quotationDetailsRefferedbacks', 'QuotationDetailsRefferedbackAPIController');
            Route::get('getSQHDetailsHistory', 'QuotationDetailsRefferedbackAPIController@getSQHDetailsHistory');
           
            Route::get('printInvoice', 'GposInvoiceAPIController@printInvoice');

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

            Route::resource('currency_conversion_histories', 'CurrencyConversionHistoryAPIController');
            
            Route::resource('document_attachment_type', 'DocumentAttachmentTypeController');
            Route::post('get_all_document_attachment_type', 'DocumentAttachmentTypeController@getAllDocumentAttachmentTypes');
            Route::post('remove_document_attachment_type', 'DocumentAttachmentTypeController@removeDocumentAttachmentType');
            
            Route::post('getAllNotDishachargeEmployeesDropdown', 'EmployeeAPIController@getAllNotDishachargeEmployeesDropdown');

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

            /* ChequeRegister */
            Route::resource('cheque_registers', 'ChequeRegisterAPIController');

            Route::resource('cheque_register_details', 'ChequeRegisterDetailAPIController');

            Route::get('getChequeRegisterFormData', 'ChequeRegisterAPIController@getChequeRegisterFormData');
            Route::post('chequeRegisterStatusChange', 'ChequeRegisterAPIController@chequeRegisterStatusChange');
            Route::post('checkChequeRegisterStatus', 'ChequeRegisterAPIController@checkChequeRegisterStatus');
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

            Route::get('getFilteredDirectCustomerInvoice', 'BookInvSuppMasterAPIController@getFilteredDirectCustomerInvoice');

            Route::post('addBudgetAdjustment', 'BudgetAdjustmentAPIController@addBudgetAdjustment');            
            
            Route::get('getUserCountData', 'EmployeeAPIController@getUserCountData');
            Route::post('getItemSavingReport', 'ReportAPIController@getItemSavingReport');
            Route::post('exportExcelSavingReport', 'ReportAPIController@exportExcelSavingReport');

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


            Route::get('getINVTrackingFormData', 'CustomerInvoiceTrackingAPIController@getINVTrackingFormData');
            Route::post('updateAllInvoiceTrackingDetail', 'CustomerInvoiceTrackingAPIController@updateAllInvoiceTrackingDetail');
            Route::post('deleteAllInvoiceTrackingDetail', 'CustomerInvoiceTrackingAPIController@deleteAllInvoiceTrackingDetail');

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
            Route::resource('chartOfAccount/allocation/histories', 'ChartOfAccountAllocationDetailHistoryAPIController');
            Route::resource('hrms_department_masters', 'HrmsDepartmentMasterAPIController');
            Route::resource('secondary_companies', 'SecondaryCompanyAPIController');

            Route::post('getSupplierCatalogDetailBySupplierItem', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierItem');
            
            Route::post('getSupplierCatalogDetailBySupplierItemForPo', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierItemForPo');
            Route::get('getDashboardDepartment', 'DashboardWidgetMasterAPIController@getDashboardDepartment');
            Route::get('getDashboardWidget', 'DashboardWidgetMasterAPIController@getDashboardWidget');
            Route::post('getCustomWidgetGraphData', 'DashboardWidgetMasterAPIController@getCustomWidgetGraphData');
            Route::post('getPreDefinedWidgetData', 'DashboardWidgetMasterAPIController@getPreDefinedWidgetData');
            Route::post('logoutApiUser', 'FcmTokenAPIController@logoutApiUser');
            Route::post('getCurrentHomeUrl', 'FcmTokenAPIController@redirectHome');
            Route::post('uploadItemsDeliveryOrder','DeliveryOrderDetailAPIController@uploadItemsDeliveryOrder');


            Route::post('getAllDeliveryOrder', 'DeliveryOrderAPIController@getAllDeliveryOrder');
            Route::post('saveDeliveryOrderTaxDetails', 'DeliveryOrderDetailAPIController@saveDeliveryOrderTaxDetail')->name("Save Delivery Order Tax Detail");
            Route::get('getDeliveryOrderFormData', 'DeliveryOrderAPIController@getDeliveryOrderFormData');

            Route::get('salesQuotationForDO', 'DeliveryOrderAPIController@salesQuotationForDO');
            Route::get('getSalesQuoatationDetailForDO', 'DeliveryOrderAPIController@getSalesQuoatationDetailForDO');
            Route::post('cancelQuatation', 'QuotationMasterAPIController@cancelQuatation');
            Route::post('closeQuatation', 'QuotationMasterAPIController@closeQuatation');


            Route::post('storeDeliveryDetailFromSalesQuotation', 'DeliveryOrderDetailAPIController@storeDeliveryDetailFromSalesQuotation');
            Route::get('deliveryOrderAudit', 'DeliveryOrderAPIController@deliveryOrderAudit');
            
            
            Route::get('downloadQuotationItemUploadTemplate', 'QuotationMasterAPIController@downloadQuotationItemUploadTemplate');
            Route::get('downloadDeliveryOrderUploadTemplate', 'DeliveryOrderAPIController@downloadQuotationItemUploadTemplate');

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
            //Route::resource('chart_of_account_allocation_detail_histories', 'ChartOfAccountAllocationDetailHistoryAPIController');

            Route::resource('hrms_department_masters', 'HrmsDepartmentMasterAPIController');
            Route::resource('secondary_companies', 'SecondaryCompanyAPIController');

            Route::post('deliveryOrderReopen', 'DeliveryOrderAPIController@deliveryOrderReopen');
            Route::post('getInvoiceDetailsForDO', 'DeliveryOrderAPIController@getInvoiceDetailsForDO');

            Route::get('getDeliveryOrderAmendHistory', 'DeliveryOrderRefferedbackAPIController@getDeliveryOrderAmendHistory');
            Route::post('getDeliveryOrderAmend', 'DeliveryOrderAPIController@getDeliveryOrderAmend');

            Route::resource('do_refferedbacks', 'DeliveryOrderRefferedbackAPIController');

            Route::resource('do_detail_refferedbacks', 'DeliveryOrderDetailRefferedbackAPIController');

            
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

            Route::post('mobileSummaryDetailDelete', 'MobileBillMasterAPIController@mobileSummaryDetailDelete');

            Route::resource('employee_mobile_bill_masters', 'EmployeeMobileBillMasterAPIController');
            Route::post('generateEmployeeBill', 'EmployeeMobileBillMasterAPIController@generateEmployeeBill');

            Route::post('getAllMobileBillSummaries', 'MobileBillSummaryAPIController@getAllMobileBillSummaries');
            Route::post('getAllMobileBillDetail', 'MobileDetailAPIController@getAllMobileBillDetail');
            Route::post('getAllEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@getAllEmployeeMobileBill');

            Route::post('getMobileBillReport', 'MobileBillMasterAPIController@getMobileBillReport');
            Route::post('validateMobileReport', 'MobileBillMasterAPIController@validateMobileReport');
            Route::get('getMobileReportFormData', 'MobileBillMasterAPIController@getMobileReportFormData');
            Route::post('exportMobileReport', 'MobileBillMasterAPIController@exportMobileReport');

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


            Route::get('salesQuotationForCustomerInvoice','QuotationMasterAPIController@salesQuotationForCustomerInvoice')->name("Sales Quotation For Customer Invoice");
            Route::get('getSalesQuotationDetailForInvoice','QuotationDetailsAPIController@getSalesQuotationDetailForInvoice')->name("Get Sales Quotation Detail For Invoice");

            Route::get('getSalesQuotationRecord','QuotationMasterAPIController@getSalesQuotationRecord')->name("Get Sales Quotation Record");

            Route::get('downloadSummaryTemplate', 'MobileBillSummaryAPIController@downloadSummaryTemplate');
            Route::get('downloadDetailTemplate', 'MobileDetailAPIController@downloadDetailTemplate');
            Route::post('getCompaniesByGroup', 'CompanyAPIController@getCompaniesByGroup');
            Route::post('getBillMastersByCompany', 'MobileBillMasterAPIController@getBillMastersByCompany');
            Route::post('exportEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@exportEmployeeMobileBill');
            

            Route::resource('ci_item_details_refferedbacks', 'CustomerInvoiceItemDetailsRefferedbackAPIController');

            Route::post('generateSalesMarketReport', 'SalesMarketingReportAPIController@generateReport');
            Route::post('generateSalesMarketReportSoldQty', 'SalesMarketingReportAPIController@generateSoldQty');
            Route::post('validateSalesMarketReport', 'SalesMarketingReportAPIController@validateReport');
            Route::post('exportSalesMarketReport', 'SalesMarketingReportAPIController@exportReport');
            Route::post('getSalesMarketFilterData', 'SalesMarketingReportAPIController@getSalesMarketFilterData');
            Route::get('getSalesAnalysisFilterData', 'SalesMarketingReportAPIController@getSalesAnalysisFilterData');

            Route::post('reportSoToReceipt', 'SalesMarketingReportAPIController@reportSoToReceipt');
            Route::post('exportSoToReceiptReport', 'SalesMarketingReportAPIController@exportSoToReceiptReport');
            Route::get('reportSoToReceiptFilterOptions', 'SalesMarketingReportAPIController@reportSoToReceiptFilterOptions');


            Route::post('assetCostingRemove', 'FixedAssetMasterAPIController@assetCostingRemove');


            Route::resource('sales_returns', 'SalesReturnAPIController');
            Route::post('getAllSalesReturn', 'SalesReturnAPIController@getAllSalesReturn');
            Route::post('storeReturnDetailFromSIDO', 'SalesReturnAPIController@storeReturnDetailFromSIDO');
            Route::get('deliveryNoteForForSR', 'SalesReturnAPIController@deliveryNoteForForSR');
            Route::get('getSalesInvoiceDeliveryOrderDetail', 'SalesReturnAPIController@getSalesInvoiceDeliveryOrderDetail');
            Route::get('salesReturnAudit', 'SalesReturnAPIController@salesReturnAudit');
            Route::resource('sales_return_details', 'SalesReturnDetailAPIController');

            Route::post('salesReturnReopen', 'SalesReturnAPIController@salesReturnReopen');
            Route::post('getSalesReturnAmend', 'SalesReturnAPIController@getSalesReturnAmend');
            Route::post('approveSalesReturn', 'SalesReturnAPIController@approveSalesReturn');
            Route::post('getSalesReturnDetailsForDO', 'SalesReturnAPIController@getSalesReturnDetailsForDO');
            Route::post('getSalesReturnDetailsForSI', 'SalesReturnAPIController@getSalesReturnDetailsForSI');

            Route::resource('grv_details_prns', 'GrvDetailsPrnAPIController');
            Route::post('appearanceSubmit', 'CompanyAPIController@appearanceSubmit');

            Route::post('checkBRVDocumentActive', 'CustomerReceivePaymentAPIController@checkBRVDocumentActive');
            Route::get('getADVPaymentForBRV', 'CustomerReceivePaymentAPIController@getADVPaymentForBRV');

            Route::resource('advance_receipt_details', 'AdvanceReceiptDetailsAPIController');
            Route::get('getADVPReceiptDetails', 'AdvanceReceiptDetailsAPIController@getADVPReceiptDetails');
            Route::post('deleteAllADVReceiptDetail', 'AdvanceReceiptDetailsAPIController@deleteAllADVReceiptDetail');


            Route::post('getDocumentDetails', 'PurchaseRequestAPIController@getDocumentDetails')->name("Get Document Details");

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


            Route::post('getAllCurrencyConversionApproval', 'CurrencyConversionMasterAPIController@getAllCurrencyConversionApproval');
            Route::post('approveCurrencyConversion', 'CurrencyConversionMasterAPIController@approveCurrencyConversion');
            Route::post('rejectCurrencyConversion', 'CurrencyConversionMasterAPIController@rejectCurrencyConversion');

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
            Route::get('getItemsOptionForAssetRequest', 'AssetRequestAPIController@getItemsOptionForAssetRequest');
            Route::post('mapLineItemAr', 'AssetRequestAPIController@mapLineItemAr')->name('Map line item Ar');
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
            Route::get('typeAheadAssetDrop', 'ERPAssetTransferDetailAPIController@typeAheadAssetDrop');
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


            Route::resource('module_masters', 'ModuleMasterAPIController');

            Route::resource('sub_module_masters', 'SubModuleMasterAPIController');

            Route::resource('module_assigneds', 'ModuleAssignedAPIController');

            Route::resource('pdc_logs', 'PdcLogAPIController');
            Route::post('getPdcCheques', 'PdcLogAPIController@getPdcCheques');
            Route::post('printPdcCheque', 'PdcLogAPIController@printPdcCheque');

            Route::post('get-all-issued-cheques', 'PdcLogAPIController@getIssuedCheques');

            Route::post('get-all-received-cheques', 'PdcLogAPIController@getAllReceivedCheques');

            Route::get('pdc-logs/banks', 'PdcLogAPIController@getAllBanks');

            Route::get('pdc-logs/get-form-data', 'PdcLogAPIController@getFormData');

            Route::post('deleteAllPDC', 'PdcLogAPIController@deleteAllPDC');
            Route::post('changePdcChequeStatus', 'PdcLogAPIController@changePdcChequeStatus');
            Route::post('reverseGeneratedChequeNo', 'PdcLogAPIController@reverseGeneratedChequeNo');
            Route::post('issueNewCheque', 'PdcLogAPIController@issueNewCheque');
            Route::get('getNextChequeNo', 'PdcLogAPIController@getNextChequeNo');
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
            Route::post('getAllocatedAssetsForExpense', 'ExpenseAssetAllocationAPIController@getAllocatedAssetsForExpense')->name("Get Allocated Assets For Expense");
            Route::post('approveCalanderDelAppointment', 'AppointmentAPIController@approveCalanderDelAppointment');
            Route::post('rejectCalanderDelAppointment', 'AppointmentAPIController@rejectCalanderDelAppointment');
            Route::post('getAppointmentById', 'AppointmentAPIController@getAppointmentById');
            Route::post('checkAssetAllocation', 'ExpenseAssetAllocationAPIController@checkAssetAllocation');
            Route::post('checkDeliveryAppoinrmentApproval', 'AppointmentAPIController@checkDeliveryAppoinrmentApproval');
            Route::post('createAppointmentGrv', 'AppointmentAPIController@createAppointmentGrv');


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

            Route::get('getEliminationLedgerReview', 'EliminationLedgerAPIController@getEliminationLedgerReview');

            Route::resource('document_sub_products', 'DocumentSubProductAPIController');
            Route::resource('payment_types', 'PaymentTypeAPIController');
            Route::resource('elimination_ledgers', 'EliminationLedgerAPIController');

            Route::resource('inter_company_stock_transfers', 'InterCompanyStockTransferAPIController');
            Route::resource('supplier_invoice_direct_items', 'SupplierInvoiceDirectItemAPIController');
            Route::post('deleteAllSIDirectItemDetail', 'SupplierInvoiceDirectItemAPIController@deleteAllSIDirectItemDetail');

            Route::post('getPricingScheduleList', 'PricingScheduleMasterAPIController@getPricingScheduleList');
            Route::post('getPricingScheduleDropDowns', 'PricingScheduleMasterAPIController@getPricingScheduleDropDowns');
            Route::post('addPricingSchedule', 'PricingScheduleMasterAPIController@addPricingSchedule');
            Route::post('getPricingScheduleMaster', 'PricingScheduleMasterAPIController@getPricingScheduleMaster');
            Route::post('deletePricingSchedule', 'PricingScheduleMasterAPIController@deletePricingSchedule');
            Route::resource('employee_ledgers', 'EmployeeLedgerAPIController');
            Route::resource('srp_erp_pay_shift_employees', 'SrpErpPayShiftEmployeesAPIController');

            Route::resource('srp_erp_pay_shift_masters', 'SrpErpPayShiftMasterAPIController');

            Route::resource('expense_employee_allocations', 'ExpenseEmployeeAllocationAPIController');
            Route::post('getAllocatedEmployeesForExpense', 'ExpenseEmployeeAllocationAPIController@getAllocatedEmployeesForExpense');


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

            Route::resource('job_error_logs', 'JobErrorLogAPIController');
            Route::resource('barcode_configurations', 'BarcodeConfigurationAPIController');
            Route::get('getBarcodeConfigurationFormData', 'BarcodeConfigurationAPIController@getBarcodeConfigurationFormData');
            Route::post('getAllBarCodeConf', 'BarcodeConfigurationAPIController@getAllBarCodeConf');
            Route::get('checkConfigurationExit', 'BarcodeConfigurationAPIController@checkConfigurationExit');

            Route::resource('cash_flow_reports', 'CashFlowReportAPIController');
            Route::get('getCashFlowFormData', 'CashFlowReportAPIController@getCashFlowFormData');
            Route::post('getCashFlowReports', 'CashFlowReportAPIController@getCashFlowReports');
            Route::post('cashFlowConfirmation', 'CashFlowReportAPIController@cashFlowConfirmation');
            Route::post('getCashFlowPullingItems', 'CashFlowReportAPIController@getCashFlowPullingItems');
            Route::post('getCashFlowPullingItemsForProceeds', 'CashFlowReportAPIController@getCashFlowPullingItemsForProceeds');
            Route::post('postCashFlowPulledItems', 'CashFlowReportAPIController@postCashFlowPulledItems');
            Route::post('postCashFlowPulledItemsForProceeds', 'CashFlowReportAPIController@postCashFlowPulledItemsForProceeds');
            Route::get('getCashFlowReportData', 'CashFlowReportAPIController@getCashFlowReportData');
            Route::post('getAllInvoicesPos', 'POS\PosAPIController@getAllInvoicesPos');
            Route::post('getPosInvoiceData', 'POS\PosAPIController@getPosInvoiceData');
            Route::post('getAllInvoicesPosReturn', 'POS\PosAPIController@getAllInvoicesPosReturn');
            Route::post('getPosInvoiceReturnData', 'POS\PosAPIController@getPosInvoiceReturnData');
            Route::post('getAllInvoicesRPos', 'POS\PosAPIController@getAllInvoicesRPos');
            Route::post('getRPOSInvoiceData', 'POS\PosAPIController@getRPOSInvoiceData');

            Route::post('generateGeneralLedgerReportPDF', 'FinancialReportAPIController@pdfExportReport');
            Route::post('generateFinancialTrialBalanceReportPDF', 'FinancialReportAPIController@pdfExportReport');
            Route::post('exportFinanceReportPDF', 'FinancialReportAPIController@pdfExportReport');

            Route::resource('envelop_types', 'EnvelopTypeAPIController');
            Route::resource('evaluation_types', 'EvaluationTypeAPIController');
            Route::resource('procument_activities', 'ProcumentActivityAPIController');
            Route::resource('pricing_schedule_masters', 'PricingScheduleMasterAPIController');
            Route::resource('evaluation_criteria_details', 'EvaluationCriteriaDetailsAPIController');
            Route::resource('evaluation_criteria_types', 'EvaluationCriteriaTypeAPIController');
            Route::resource('evaluation_criteria_score_configs', 'EvaluationCriteriaScoreConfigAPIController');
            Route::resource('calendar_dates', 'CalendarDatesAPIController');
            Route::resource('calendar_dates_details', 'CalendarDatesDetailAPIController');
            Route::resource('third_party_systems', 'ThirdPartySystemsAPIController');
            Route::resource('third_party_integration_keys', 'ThirdPartyIntegrationKeysAPIController');
            
            Route::resource('cash_flow_report_details', 'CashFlowReportDetailAPIController');
            Route::resource('po_cutoff_jobs', 'PoCutoffJobAPIController');
            Route::resource('po_cutoff_job_datas', 'PoCutoffJobDataAPIController');
            Route::resource('p_o_s_s_o_u_r_c_e_shift_details', 'POSSOURCEShiftDetailsAPIController');
            Route::resource('i_o_u_booking_masters', 'IOUBookingMasterAPIController');


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

            // erp_language
            Route::resource('erp_language_master', 'ERPLanguageMasterAPIController');
            Route::post('store-employee-language', 'ERPLanguageMasterAPIController@storeEmployeeLanguage');


        
        });
    });

    Route::get('validateSupplierRegistrationLink', 'SupplierMasterAPIController@validateSupplierRegistrationLink');
    Route::get('getSupplierRegisterFormData', 'SupplierMasterAPIController@getSupplierRegisterFormData');
    Route::post('registerSupplier', 'SupplierMasterAPIController@registerSupplier');

    Route::group(['middleware' => 'print_lang'], function () {
        Route::get('getProcumentOrderPrintPDF', 'ProcumentOrderAPIController@getProcumentOrderPrintPDF')->name('Get procurement order print pdf');
        Route::get('goodReceiptVoucherPrintPDF', 'GRVMasterAPIController@goodReceiptVoucherPrintPDF');
        Route::get('printItemIssue', 'ItemIssueMasterAPIController@printItemIssue');
        Route::get('deliveryPrintItemIssue', 'ItemIssueMasterAPIController@deliveryPrintItemIssue');
        Route::get('printCustomerInvoice', 'CustomerInvoiceDirectAPIController@printCustomerInvoice');
        Route::get('printReceiptVoucher', 'CustomerReceivePaymentAPIController@printReceiptVoucher');
        Route::get('printPaymentVoucher', 'PaySupplierInvoiceMasterAPIController@printPaymentVoucher');
    });

    Route::get('getPoLogisticPrintPDF', 'PoAdvancePaymentAPIController@getPoLogisticPrintPDF')->name('Get procurement order logistic print pdf');
    Route::post('getReportPDF', 'ReportAPIController@pdfExportReport');
    Route::post('generateARReportPDF', 'AccountsReceivableReportAPIController@pdfExportReport');
    Route::post('generateAPReportPDF', 'AccountsPayableReportAPIController@pdfExportReport');
    Route::get('printPurchaseRequest', 'PurchaseRequestAPIController@printPurchaseRequest');
    Route::get('printItemReturn', 'ItemReturnMasterAPIController@printItemReturn');
    Route::get('printStockReceive', 'StockReceiveAPIController@printStockReceive');
    Route::get('printStockTransfer', 'StockTransferAPIController@printStockTransfer');
    
    Route::get('printPurchaseReturn', 'PurchaseReturnAPIController@printPurchaseReturn');
    Route::get('printExpenseClaim', 'ExpenseClaimAPIController@printExpenseClaim');
    Route::get('printExpenseClaimMaster', 'ExpenseClaimMasterAPIController@printExpenseClaimMaster');
    Route::get('printCreditNote', 'CreditNoteAPIController@printCreditNote');
    Route::get('printDebitNote', 'DebitNoteAPIController@printDebitNote');
    Route::get('printBankReconciliation', 'BankReconciliationAPIController@printBankReconciliation');
    Route::get('printChequeItems', 'BankLedgerAPIController@printChequeItems');
    Route::get('printSuppliers', 'SupplierMasterAPIController@printSuppliers');
    Route::get('printMaterielRequest', 'MaterielRequestAPIController@printMaterielRequest');
    Route::get('exportPaymentBankTransfer', 'PaymentBankTransferAPIController@exportPaymentBankTransfer');
    Route::get('printJournalVoucher', 'JvMasterAPIController@printJournalVoucher');
    Route::get('printPaymentMatching', 'MatchDocumentMasterAPIController@printPaymentMatching');
    Route::get('getSalesQuotationPrintPDF', 'QuotationMasterAPIController@getSalesQuotationPrintPDF');
    Route::get('getBatchSubmissionDetailsPrintPDF', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionDetailsPrintPDF');
    Route::get('BidSummaryReport', 'BidSubmissionMasterAPIController@BidSummaryExportReport');
    Route::get('supplier-item-wise-report', 'BidSubmissionMasterAPIController@SupplierItemWiseExportReport');
    Route::post('schedule-wise-report', 'BidSubmissionMasterAPIController@SupplierSheduleWiseReport');
    Route::post('SupplierScheduleWiseExportReport', 'BidSubmissionMasterAPIController@SupplierScheduleWiseExportReport');

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
    Route::post('getTenderBitsDoc', 'DocumentAttachmentsAPIController@getTenderBitsDoc');
    Route::post('getConsolidatedDataAttachment', 'DocumentAttachmentsAPIController@getConsolidatedDataAttachment');

    Route::resource('tax_ledgers', 'TaxLedgerAPIController');

    Route::resource('employee_designations', 'EmployeeDesignationAPIController');

    Route::resource('hrms_designations', 'HrmsDesignationAPIController');

    Route::resource('hrms_employee_managers', 'HrmsEmployeeManagerAPIController');


    Route::resource('finance_category_serials', 'FinanceCategorySerialAPIController');

    if (env("APP_ENV") != "production") {
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    }

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
    Route::post('getAppointmentAttachmentList', 'AppointmentAPIController@getAppointmentAttachmentList');
    Route::get('test', 'TenantAPIController@test');
    Route::get('downloadFileSRM', 'DocumentAttachmentsAPIController@downloadFileSRM');
    Route::get('getSearchSupplierByCompanySRM', 'SupplierMasterAPIController@getSearchSupplierByCompanySRM');
    Route::get('updateExemptVATPos', 'ProcumentOrderAPIController@updateExemptVATPos');
    Route::get('downloadFileTender', 'DocumentAttachmentsAPIController@downloadFileTender');
    Route::post('genearetBarcode', 'BarcodeConfigurationAPIController@genearetBarcode');

});


Route::group(['middleware' => ['tenantById']], function (){

    Route::group(['middleware' => ['pos_api','hrms_employee']], function () {
        Route::post('postEmployee', 'HelpDesk\HelpDeskAPIController@postEmployee');
        Route::post('post_supplier_invoice', 'HRMS\HRMSAPIController@createSupplierInvoice');
    });
});

// Route::get('updateNotPostedGLEntries', 'GeneralLedgerAPIController@updateNotPostedGLEntries');

Route::post('sendEmail', 'Email\SendEmailAPIController@sendEmail');
Route::get('updateRoutes', 'RouteAPIController@updateRoutes');
Route::get('updateRoleRoutes', 'RouteAPIController@updateRoleRoutes');

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
        Route::post('checkLedgerQty', 'ItemMasterAPIController@checkLedgerQty')->name('Check Ledger Qty');
    });
});


require __DIR__.'/../routes/hrms/jobRoutes.php';


Route::post('documentUpload', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@documentUpload');
Route::get('viewDocument', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocument');
Route::get('viewDocumentEmployeeImg', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocumentEmployeeImg');
Route::get('viewDocumentEmployeeImgBulk', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocumentEmployeeImgBulk');
Route::post('documentUploadDelete', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@documentUploadDelete');
Route::get('viewHrDocuments', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewHrDocuments');
        

/*
 * End external related routes
 */



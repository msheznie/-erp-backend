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

use Illuminate\Support\Facades\Route;

Route::get('updateTaxLedgerForSupplierInvoice', 'TaxLedgerAPIController@updateTaxLedgerForSupplierInvoice');


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
            require __DIR__.'/../routes/treasuryManagement/treasuryManagementRoutes.php';
            require __DIR__.'/../routes/assetManagement/assetManagementRoutes.php';
            require __DIR__.'/../routes/general/customReport.php';
            require __DIR__.'/../routes/supplierManagement/supplierManagementRoutes.php';
            require __DIR__.'/../routes/sourcingManagement/sourcingManagementRoutes.php';
            require __DIR__.'/../routes/logistics/logisticsRoutes.php';
            require __DIR__.'/../routes/navigation/navigationRoutes.php';
            require __DIR__.'/../routes/groupReport/groupReportRoutes.php';

            Route::post('downloadBudgetTemplate', 'BudgetMasterAPIController@downloadBudgetTemplate')->name("Download budget template");
            Route::post('getBudgetUploads', 'BudgetMasterAPIController@getBudgetUploads')->name("Get upload budgets");


            Route::post('getAllEmployees', 'EmployeeAPIController@getAllEmployees');

            Route::resource('navigation_menuses', 'NavigationMenusAPIController');

            Route::resource('navigation_user_group_setups', 'NavigationUserGroupSetupAPIController');

            Route::get('user/companies', 'UserAPIController@userCompanies');
            Route::get('checkUser', 'UserAPIController@checkUser');

            Route::get('getSuppliersByCompany', 'SupplierMasterAPIController@getSuppliersByCompany');

            Route::resource('registered_supplier_currencies', 'RegisteredSupplierCurrencyAPIController');
            Route::resource('registered_bank_memo_suppliers', 'RegisteredBankMemoSupplierAPIController');

            Route::get('user/menu', 'NavigationUserGroupSetupAPIController@userMenu');
            Route::get('getUserMenu', 'NavigationUserGroupSetupAPIController@getUserMenu');


            Route::group(['middleware' => 'max_memory_limit'], function () {
                Route::group(['middleware' => 'max_execution_limit'], function () {
                    Route::post('uploadBudgets', 'BudgetMasterAPIController@uploadBudgets')->name("Upload budgets");
                    Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');
                    Route::post('getAssetDepPeriodsByID', 'FixedAssetDepreciationPeriodAPIController@getAssetDepPeriodsByID');
                    Route::post('exportAssetMaster', 'FixedAssetMasterAPIController@exportAssetMaster');
                    Route::post('deleteBudgetUploads', 'BudgetMasterAPIController@deleteBudgetUploads')->name("Delete budget uploads");
                });
            });

            Route::get('getCompanyReportingCurrency', 'CurrencyMasterAPIController@getCompanyReportingCurrency');
            Route::get('getCompanyLocalCurrencyCode', 'CurrencyMasterAPIController@getCompanyLocalCurrencyCode');
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

            Route::resource('bank_memo_supplier_masters', 'BankMemoSupplierMasterAPIController');

            Route::post('getCurrencyDetails', 'SupplierCurrencyAPIController@getCurrencyDetails');

            Route::resource('units', 'UnitAPIController');

            Route::post('financeItemCategorySubsAttributesUpdate', 'FinanceItemCategorySubAPIController@financeItemCategorySubsAttributesUpdate');

            Route::resource('finance_item_category_masters', 'FinanceItemCategoryMasterAPIController');

            Route::get('reasonCodeMasterRecordSalesReturn/{id}', 'ReasonCodeMasterAPIController@reasonCodeMasterRecordSalesReturn');

            Route::resource('example_table_templates', 'ExampleTableTemplateAPIController');

            Route::get('getItemMasterPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@getItemMasterPurchaseRequestHistory');
            
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
            
            Route::resource('company_navigation_menuses', 'CompanyNavigationMenusAPIController');
            /** Company user group*/
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
            Route::post('getAffectedDocuments', 'SegmentMasterAPIController@getAffectedDocuments');
            Route::post('getAssignedEmployees', 'SegmentMasterAPIController@getAssignedEmployees');
            Route::post('exportAssignedEmp', 'SegmentMasterAPIController@exportAssignedEmp');
            Route::post('exportProcessedSegments', 'SegmentMasterAPIController@exportProcessedSegments');


            Route::post('updateSegmentMaster', 'SegmentMasterAPIController@updateSegmentMaster');

            //confirmation
            Route::post('confirmDocument', 'PurchaseRequestAPIController@confirmDocument');

            Route::get('getGRVBasedPODropdowns', 'ProcumentOrderAPIController@getGRVBasedPODropdowns');
            
            Route::resource('priorities', 'PriorityAPIController');

            Route::resource('locations', 'LocationAPIController');

            Route::resource('yes_no_selection_for_minuses', 'YesNoSelectionForMinusAPIController');

            Route::resource('months', 'MonthsAPIController');
            
            Route::post('delete-item-qnty-by-pr', 'PurchaseRequestAPIController@delteItemQntyPR');

            Route::resource('document_approveds', 'DocumentApprovedAPIController');

            Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');

            Route::resource('poPaymentTermsRequestCRUD', 'PoAdvancePaymentAPIController');

            Route::resource('srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController');
            Route::get('get_srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController@geDocumentAttachments');

            Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');
            
            Route::post('updatePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@updatePoPaymentTermsLogistic');

            Route::post('approveSupplier', 'SupplierMasterAPIController@approveSupplier');
            
            Route::post('approveCustomer', 'CustomerMasterAPIController@approveCustomer');

            Route::post('approveChartOfAccount', 'ChartOfAccountAPIController@approveChartOfAccount');

            Route::post('approveProcurementOrder', 'ProcumentOrderAPIController@approveProcurementOrder');
            
            
            Route::post('getGRVDrilldownSpentAnalysis', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysis');
            Route::post('getGRVDrilldownSpentAnalysisTotal', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysisTotal');
            
            /** Po Related Tables Created by Nazir  */
            Route::resource('erp_addresses', 'ErpAddressAPIController');
            Route::resource('po_payment_terms', 'PoPaymentTermsAPIController');
            Route::resource('po_advance_payments', 'PoAdvancePaymentAPIController');

            Route::post('reportSpentAnalysisDrilldownExport', 'ProcumentOrderAPIController@reportSpentAnalysisDrilldownExport');
            

            Route::post('reportPoToPayment', 'ProcumentOrderAPIController@reportPoToPayment');
            Route::post('exportPoToPaymentReport', 'ProcumentOrderAPIController@exportPoToPaymentReport');
            Route::get('reportPoToPaymentFilterOptions', 'ProcumentOrderAPIController@reportPoToPaymentFilterOptions');


            Route::get('exchangerate', 'ApprovalLevelAPIController@confirmDocTest');

            Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');

            Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');

            Route::resource('purchase_order_process_details', 'PurchaseOrderProcessDetailsAPIController');

            Route::resource('tax_types', 'TaxTypeAPIController');

            Route::resource('alerts', 'AlertAPIController');
            Route::resource('access_tokens', 'AccessTokensAPIController');
            Route::resource('users_log_histories', 'UsersLogHistoryAPIController');


            Route::resource('addresses', 'AddressAPIController');
            Route::post('getAllAddresses', 'AddressAPIController@getAllAddresses');
            Route::get('getAddressFormData', 'AddressAPIController@getAddressFormData');

            Route::resource('address_types', 'AddressTypeAPIController');
            
            Route::resource('company_policy_categories', 'CompanyPolicyCategoryAPIController');
         
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
            
            Route::resource('item_issue_types', 'ItemIssueTypeAPIController');

            Route::resource('accounts_payable_ledgers', 'AccountsPayableLedgerAPIController');

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

            Route::get('getAllApprovalDocuments', 'DocumentMasterAPIController@getAllApprovalDocuments');
            
            Route::get('getJobsByContractAndCustomer', 'CustomerMasterAPIController@getJobsByContractAndCustomer');

            Route::resource('performa_details', 'PerformaDetailsAPIController');
            Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');
            Route::resource('ticket_masters', 'TicketMasterAPIController');
            Route::resource('field_masters', 'FieldMasterAPIController');
            Route::resource('inv_reclassification_details', 'InventoryReclassificationDetailAPIController');
            


            Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');

            Route::resource('performa_details', 'PerformaDetailsAPIController');

            Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');

            Route::resource('ticket_masters', 'TicketMasterAPIController');

            Route::resource('field_masters', 'FieldMasterAPIController');


            Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');

            Route::resource('performa_masters', 'PerformaMasterAPIController');
            Route::resource('rig_masters', 'RigMasterAPIController');


            Route::get('getGLForJournalVoucherDirect', 'ChartOfAccountsAssignedAPIController@getGLForJournalVoucherDirect');


            Route::post('getglDetails','ChartOfAccountsAssignedAPIController@getglDetails');
            Route::post('erp_project_masters/get_gl_accounts','ChartOfAccountsAssignedAPIController@getGlAccounts');
            Route::resource('project_gl_details', 'ProjectGlDetailAPIController');

            //Logistic Configuration Master
            
            Route::resource('port_masters', 'PortMasterAPIController');
            Route::resource('delivery_terms_masters', 'DeliveryTermsMasterAPIController');

            Route::post('approveSupplierInvoice', 'BookInvSuppMasterAPIController@approveSupplierInvoice');

            Route::resource('expense_claims', 'ExpenseClaimAPIController');
            Route::resource('expense_claim_types', 'ExpenseClaimTypeAPIController');
            Route::resource('expense_claim_categories', 'ExpenseClaimCategoriesAPIController');
            Route::get('getPaymentStatusHistory', 'ExpenseClaimAPIController@getPaymentStatusHistory');
            Route::get('getDetailsByExpenseClaim', 'ExpenseClaimDetailsAPIController@getDetailsByExpenseClaim');
            Route::get('preCheckECDetailEdit', 'ExpenseClaimDetailsAPIController@preCheckECDetailEdit');

            Route::resource('expense_claim_details_masters', 'ExpenseClaimDetailsMasterAPIController');

            Route::resource('expense_claim_categories_masters', 'ExpenseClaimCategoriesMasterAPIController');

            Route::resource('logistic_mode_of_imports', 'LogisticModeOfImportAPIController');
            Route::resource('logistic_shipping_modes', 'LogisticShippingModeAPIController');
            Route::resource('logistic_statuses', 'LogisticStatusAPIController');

            Route::get('customerRecieptDetailsRecords', 'CustomerReceivePaymentDetailAPIController@customerRecieptDetailsRecords');

        
            Route::get('getRVPaymentVoucherMatchItems', 'PaySupplierInvoiceMasterAPIController@getRVPaymentVoucherMatchItems');

            Route::post('updatePrintChequeItems', 'BankLedgerAPIController@updatePrintChequeItems');

            Route::post('referBackCosting', 'FixedAssetMasterAPIController@referBackCosting');

            // Receipt Voucher
            Route::resource('unbilled_g_r_vs', 'UnbilledGRVAPIController');
            Route::resource('performa_temps', 'PerformaTempAPIController');
            Route::resource('free_billings', 'FreeBillingAPIController');

            Route::post('capitalizationReopen', 'AssetCapitalizationAPIController@capitalizationReopen');
            Route::post('referBackCapitalization', 'AssetCapitalizationAPIController@referBackCapitalization');
            Route::post('deleteAllAssetCapitalizationDet', 'AssetCapitalizationDetailAPIController@deleteAllAssetCapitalizationDet');

            Route::resource('journalVoucherCRUD', 'JvMasterAPIController');
            Route::resource('jv_details', 'JvDetailAPIController');
            Route::get('getJournalVoucherMasterFormData', 'JvMasterAPIController@getJournalVoucherMasterFormData');
            Route::post('getJournalVoucherMasterView', 'JvMasterAPIController@getJournalVoucherMasterView');
            Route::post('copyJV', 'JvMasterAPIController@copyJV');
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

            Route::resource('bookInvSuppDetRefferedbacks', 'BookInvSuppDetRefferedBackAPIController');
            Route::resource('DirectInvoiceDetRefferedbacks', 'DirectInvoiceDetailsRefferedBackAPIController');

            Route::get('getAllcompaniesByDepartment', 'DocumentApprovedAPIController@getAllcompaniesByDepartment');

            Route::post('assetCostingReopen', 'FixedAssetMasterAPIController@assetCostingReopen');

            Route::post('amendAssetCostingReview', 'FixedAssetMasterAPIController@amendAssetCostingReview');

            Route::get('assetDepreciationByID/{id}', 'FixedAssetDepreciationMasterAPIController@assetDepreciationByID');
            Route::get('assetDepreciationMaster', 'FixedAssetDepreciationMasterAPIController@assetDepreciationMaster');
            Route::post('assetDepreciationReopen', 'FixedAssetDepreciationMasterAPIController@assetDepreciationReopen');
            Route::post('referBackDepreciation', 'FixedAssetDepreciationMasterAPIController@referBackDepreciation');
            Route::post('amendAssetDepreciationReview', 'FixedAssetDepreciationMasterAPIController@amendAssetDepreciationReview');

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

            Route::post('syncGlBudget', 'BudjetdetailsAPIController@syncGlBudget');
            Route::post('getBudgetDetailHistory', 'BudjetdetailsAPIController@getBudgetDetailHistory');

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
            Route::post('disposalReopen', 'AssetDisposalMasterAPIController@disposalReopen');
            Route::post('referBackDisposal', 'AssetDisposalMasterAPIController@referBackDisposal');
            Route::post('amendAssetDisposalReview', 'AssetDisposalMasterAPIController@amendAssetDisposalReview');

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

            
            
            
            Route::resource('fixed_asset_depreciation_periods', 'FixedAssetDepreciationPeriodAPIController');
            Route::resource('asset_types', 'AssetTypeAPIController');
            
            

            Route::resource('h_r_m_s_jv_details', 'HRMSJvDetailsAPIController');
            Route::resource('h_r_m_s_jv_masters', 'HRMSJvMasterAPIController');
            Route::resource('accruaval_from_o_p_masters', 'AccruavalFromOPMasterAPIController');
            Route::resource('fixed_asset_costs', 'FixedAssetCostAPIController');
            Route::resource('insurance_policy_types', 'InsurancePolicyTypeAPIController');
            Route::resource('fixed_asset_depreciation_masters', 'FixedAssetDepreciationMasterAPIController');


            Route::post('generateAssetDetailDrilldown', 'AssetManagementReportAPIController@generateAssetDetailDrilldown');
            Route::resource('monthly_addition_details', 'MonthlyAdditionDetailAPIController');
            Route::resource('employment_types', 'EmploymentTypeAPIController');
            Route::resource('period_masters', 'PeriodMasterAPIController');
            Route::resource('salary_process_masters', 'SalaryProcessMasterAPIController');
            Route::resource('salary_process_employment_types', 'SalaryProcessEmploymentTypesAPIController');

            Route::resource('hrms_chart_of_accounts', 'HRMSChartOfAccountsAPIController');
            Route::resource('hrms_department_masters', 'HRMSDepartmentMasterAPIController');

            Route::resource('advance_payment_referbacks', 'AdvancePaymentReferbackAPIController');
            Route::resource('direct_payment_referbacks', 'DirectPaymentReferbackAPIController');

            Route::resource('paymentVoucherDetailReferbacks', 'PaySupplierInvoiceDetailReferbackAPIController');

            Route::resource('directReceiptHistories', 'DirectReceiptDetailsRefferedHistoryAPIController');
            Route::resource('PaymentVoucherMasterReferbacks', 'PaySupplierInvoiceMasterReferbackAPIController');

            Route::resource('custreceivepaymentdethistories', 'CustReceivePaymentDetRefferedHistoryAPIController');

            Route::resource('advance_payment_referbacks', 'AdvancePaymentReferbackAPIController');
            Route::resource('direct_payment_referbacks', 'DirectPaymentReferbackAPIController');

            Route::post('getCreditNoteAmendHistory', 'CreditNoteReferredbackAPIController@getCreditNoteAmendHistory');
            Route::resource('creditNoteReferredbackCRUD', 'CreditNoteReferredbackAPIController');
            Route::resource('creditNoteDetailsRefferdbacks', 'CreditNoteDetailsRefferdbackAPIController');
            Route::get('getCapitalizationLinkedDocument', 'AssetCapitalizationAPIController@getCapitalizationLinkedDocument');
            Route::get('getCNDetailAmendHistory', 'CreditNoteDetailsRefferdbackAPIController@getCNDetailAmendHistory');

            Route::resource('customerInvoiceDetRefferedbacks', 'CustomerInvoiceDirectDetRefferedbackAPIController');

            Route::resource('supplier_category_icv_subs', 'SupplierCategoryICVSubAPIController');
            Route::resource('supplier_category_icv_masters', 'SupplierCategoryICVMasterAPIController');


            Route::resource('debitNoteDetailsRefferedbacks', 'DebitNoteDetailsRefferedbackAPIController');

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

            Route::resource('bankTransferDetailRefferedBacks', 'PaymentBankTransferDetailRefferedBackAPIController');


            Route::resource('grvDetailsRefferedbacks', 'GrvDetailsRefferedbackAPIController');
            Route::resource('document_restriction_assigns', 'DocumentRestrictionAssignAPIController');
            Route::resource('document_restriction_policies', 'DocumentRestrictionPolicyAPIController');

            Route::post('getEmployeeMasterView', 'EmployeeAPIController@getEmployeeMasterView');
            Route::post('confirmEmployeePasswordReset', 'EmployeeAPIController@confirmEmployeePasswordReset');
            
            Route::resource('bank_account_reffered_backs', 'BankAccountRefferedBackAPIController');
            

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
            Route::post('postPosCustomerMapping', 'ShiftDetailsAPIController@postPosCustomerMapping');
            Route::post('postPosTaxMapping', 'ShiftDetailsAPIController@postPosTaxMapping');
            Route::post('postPosPayMapping', 'ShiftDetailsAPIController@postPosPayMapping');
            Route::post('postPosEntries', 'ShiftDetailsAPIController@postPosEntries');
            Route::post('insufficientItems', 'ShiftDetailsAPIController@insufficientItems');
            Route::post('getPosMismatchEntries', 'ShiftDetailsAPIController@getPosMismatchEntries');
            Route::post('getPosMisMatchData', 'ShiftDetailsAPIController@getPosMisMatchData');
            Route::post('updatePosMismatch', 'ShiftDetailsAPIController@updatePosMismatch');
            Route::post('getGlMatchEntries', 'ShiftDetailsAPIController@getGlMatchEntries');
            Route::post('exportInsufficientItems', 'ShiftDetailsAPIController@exportInsufficientItems');

            Route::resource('currency_denominations', 'CurrencyDenominationAPIController');
            Route::resource('shift_details', 'ShiftDetailsAPIController');
            Route::get('getPosCustomerSearch', 'CustomerMasterAPIController@getPosCustomerSearch');
      
            Route::resource('docEmailNotificationMasters', 'DocumentEmailNotificationMasterAPIController');

            Route::resource('gposInvoices', 'GposInvoiceAPIController');
            Route::get('getInvoiceDetails', 'GposInvoiceAPIController@getInvoiceDetails');
            Route::post('getInvoicesByShift', 'GposInvoiceAPIController@getInvoicesByShift');
            Route::resource('gposInvoiceDetails', 'GposInvoiceDetailAPIController');
            Route::resource('gposInvoicePayments', 'GposInvoicePaymentsAPIController');

            Route::resource('quotationVersionDetails', 'QuotationVersionDetailsAPIController');

            Route::resource('quotationDetailsRefferedbacks', 'QuotationDetailsRefferedbackAPIController');
           
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

            Route::post('addBudgetAdjustment', 'BudgetAdjustmentAPIController@addBudgetAdjustment');            
            
            Route::get('getUserCountData', 'EmployeeAPIController@getUserCountData');

            Route::resource('customer_invoice_trackings', 'CustomerInvoiceTrackingAPIController');

            Route::get('getBatchSubmissionFormData', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionFormData');
            Route::get('getContractServiceLine', 'CustomerInvoiceTrackingAPIController@getContractServiceLine');
            Route::post('getAllBatchSubmissionByCompany', 'CustomerInvoiceTrackingAPIController@getAllBatchSubmissionByCompany');
            Route::post('getCustomerInvoicesForBatchSubmission', 'CustomerInvoiceTrackingAPIController@getCustomerInvoicesForBatchSubmission');
            Route::post('addBatchSubmitDetails', 'CustomerInvoiceTrackingDetailAPIController@addBatchSubmitDetails');
            Route::get('getItemsByBatchSubmission', 'CustomerInvoiceTrackingDetailAPIController@getItemsByBatchSubmission');
            Route::post('exportBatchSubmissionDetails', 'CustomerInvoiceTrackingAPIController@exportBatchSubmissionDetails');
            Route::post('getContractByCustomer', 'AccountsReceivableReportAPIController@getContractByCustomer');

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
            Route::post('exportWidgetExcel', 'DashboardWidgetMasterAPIController@exportWidgetExcel');


            Route::post('saveDeliveryOrderTaxDetails', 'DeliveryOrderDetailAPIController@saveDeliveryOrderTaxDetail')->name("Save Delivery Order Tax Detail");
            
            Route::get('downloadQuotationItemUploadTemplate', 'QuotationMasterAPIController@downloadQuotationItemUploadTemplate');

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

            Route::resource('custom_report_masters', 'CustomReportMasterAPIController');
            Route::resource('custom_report_columns', 'CustomReportColumnsAPIController');
            Route::resource('custom_user_report_columns', 'CustomUserReportColumnsAPIController');
            Route::resource('custom_filters_columns', 'CustomFiltersColumnAPIController');
            Route::resource('custom_user_report_summarizes', 'CustomUserReportSummarizeAPIController');

            Route::get('getSalesQuotationRecord','QuotationMasterAPIController@getSalesQuotationRecord')->name("Get Sales Quotation Record");

            Route::get('downloadSummaryTemplate', 'MobileBillSummaryAPIController@downloadSummaryTemplate');
            Route::get('downloadDetailTemplate', 'MobileDetailAPIController@downloadDetailTemplate');
            Route::post('getCompaniesByGroup', 'CompanyAPIController@getCompaniesByGroup');
            Route::post('getBillMastersByCompany', 'MobileBillMasterAPIController@getBillMastersByCompany');
            Route::post('exportEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@exportEmployeeMobileBill');
            

            Route::resource('ci_item_details_refferedbacks', 'CustomerInvoiceItemDetailsRefferedbackAPIController');

            Route::post('generateSalesMarketReportSoldQty', 'SalesMarketingReportAPIController@generateSoldQty');

            Route::post('assetCostingRemove', 'FixedAssetMasterAPIController@assetCostingRemove');

            Route::post('approveSalesReturn', 'SalesReturnAPIController@approveSalesReturn');
            Route::post('getSalesReturnDetailsForSI', 'SalesReturnAPIController@getSalesReturnDetailsForSI');

            Route::resource('grv_details_prns', 'GrvDetailsPrnAPIController');
            Route::post('appearanceSubmit', 'CompanyAPIController@appearanceSubmit');

            Route::get('getVATFilterFormData', 'VATReportAPIController@getVATFilterFormData');
            Route::post('validateVATReport', 'VATReportAPIController@validateVATReport');
            Route::post('generateVATReport', 'VATReportAPIController@generateVATReport');
            Route::post('generateVATDetailReport', 'VATReportAPIController@generateVATDetailReport');
            Route::post('exportVATReport', 'VATReportAPIController@exportVATReport');
            Route::post('exportVATDetailReport', 'VATReportAPIController@exportVATDetailReport');

            Route::resource('customer_category_assigneds', 'CustomerMasterCategoryAssignedAPIController');
            Route::get('assignedCompaniesByCustomerCategory', 'CustomerMasterCategoryAssignedAPIController@assignedCompaniesByCustomerCategory');

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

            Route::post('erp_project_masters', 'ErpProjectMasterAPIController@index');
            Route::post('get_projects', 'ErpProjectMasterAPIController@get_projects');
            Route::post('erp_project_masters/create', 'ErpProjectMasterAPIController@store');
            Route::get('erp_project_masters/form', 'ErpProjectMasterAPIController@formData');
            Route::get('erp_project_masters/segments_by_company', 'ErpProjectMasterAPIController@segmentsByCompany');
            Route::get('erp_project_masters/{id}', 'ErpProjectMasterAPIController@show');
            Route::put('erp_project_masters/{id}', 'ErpProjectMasterAPIController@update');

            /* Asset Request */
            Route::resource('asset_requests', 'AssetRequestAPIController');
            Route::get('getItemsOptionForAssetRequest', 'AssetRequestAPIController@getItemsOptionForAssetRequest');
            Route::post('mapLineItemAr', 'AssetRequestAPIController@mapLineItemAr')->name('Map line item Ar');

            /* Asset Transfer */
            Route::post('update-return-status', 'ERPAssetTransferDetailAPIController@UpdateReturnStatus');
            Route::get('asset-transfer-drop', 'ERPAssetTransferDetailAPIController@assetTransferDrop');
            Route::get('typeAheadAssetDrop', 'ERPAssetTransferDetailAPIController@typeAheadAssetDrop');
            Route::post('add-employee-asset-transfer-asset-detail/{id}', 'ERPAssetTransferDetailAPIController@addEmployeeAsset');
            Route::post('asset_transfer_detail_asset', 'ERPAssetTransferDetailAPIController@assetTransferDetailAsset');
            Route::get('getAssetDropPR', 'ERPAssetTransferAPIController@getAssetDropPR');
            Route::get('asset-location-value', 'ERPAssetTransferDetailAPIController@getAssetLocationValue');
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

            Route::get('pdc-logs/banks', 'PdcLogAPIController@getAllBanks');

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

            Route::post('checkAssetAllocation', 'ExpenseAssetAllocationAPIController@checkAssetAllocation');

            Route::resource('appointments', 'AppointmentAPIController');
            Route::resource('appointment_details', 'AppointmentDetailsAPIController');
            Route::resource('po_categories', 'PoCategoryAPIController');

            Route::get('getEliminationLedgerReview', 'EliminationLedgerAPIController@getEliminationLedgerReview');

            Route::resource('document_sub_products', 'DocumentSubProductAPIController');
            Route::resource('payment_types', 'PaymentTypeAPIController');
            Route::resource('elimination_ledgers', 'EliminationLedgerAPIController');

            Route::resource('inter_company_stock_transfers', 'InterCompanyStockTransferAPIController');
         
            Route::resource('employee_ledgers', 'EmployeeLedgerAPIController');
            Route::resource('srp_erp_pay_shift_employees', 'SrpErpPayShiftEmployeesAPIController');

            Route::resource('srp_erp_pay_shift_masters', 'SrpErpPayShiftMasterAPIController');

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
            Route::post('getAllShiftsRPOS', 'POS\PosAPIController@getAllShiftsRPOS');
            Route::post('getAllInvoicesPos', 'POS\PosAPIController@getAllInvoicesPos');
            Route::post('getPosInvoiceData', 'POS\PosAPIController@getPosInvoiceData');
            Route::post('getAllInvoicesPosReturn', 'POS\PosAPIController@getAllInvoicesPosReturn');
            Route::post('getPosInvoiceReturnData', 'POS\PosAPIController@getPosInvoiceReturnData');
            Route::post('getAllInvoicesRPos', 'POS\PosAPIController@getAllInvoicesRPos');
            Route::post('getRPOSInvoiceData', 'POS\PosAPIController@getRPOSInvoiceData');
            Route::post('getAllShiftsGPOS', 'POS\PosAPIController@getAllShiftsGPOS');
            Route::post('getAllBills', 'POS\PosAPIController@getAllBills');

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

            Route::resource('tax_ledgers', 'TaxLedgerAPIController');
            Route::resource('employee_designations', 'EmployeeDesignationAPIController');
            Route::resource('hrms_designations', 'HrmsDesignationAPIController');
            Route::resource('hrms_employee_managers', 'HrmsEmployeeManagerAPIController');
            Route::resource('finance_category_serials', 'FinanceCategorySerialAPIController');
        });
    });

    require __DIR__.'/../routes/printPdf/printPdfRoutes.php';
    
    Route::get('validateSupplierRegistrationLink', 'SupplierMasterAPIController@validateSupplierRegistrationLink');
    Route::get('getSupplierRegisterFormData', 'SupplierMasterAPIController@getSupplierRegisterFormData');
    Route::post('registerSupplier', 'SupplierMasterAPIController@registerSupplier');

    
    Route::get('loginwithToken', 'UserAPIController@loginwithToken');
    Route::post('login', 'AuthAPIController@auth');
    Route::post('oauth/login_with_token', 'AuthAPIController@authWithToken');
    
    Route::get('downloadFileFrom', 'DocumentAttachmentsAPIController@downloadFileFrom');
    Route::resource('work_order_generation_logs', 'WorkOrderGenerationLogAPIController');
    Route::resource('external_link_hashes', 'ExternalLinkHashAPIController');
    Route::resource('registered_suppliers', 'RegisteredSupplierAPIController');
    Route::post('getTenderBitsDoc', 'DocumentAttachmentsAPIController@getTenderBitsDoc');
    Route::post('getConsolidatedDataAttachment', 'DocumentAttachmentsAPIController@getConsolidatedDataAttachment');

    Route::get('notification-service', 'NotificationCompanyScenarioAPIController@notification_service');
    Route::get('leave/accrual/service_test', 'LeaveAccrualMasterAPIController@accrual_service_test');
    Route::post('getAppointmentList', 'AppointmentAPIController@getAppointmentList');
    Route::get('test', 'TenantAPIController@test');
    Route::get('downloadFileSRM', 'DocumentAttachmentsAPIController@downloadFileSRM'); 
    Route::get('updateExemptVATPos', 'ProcumentOrderAPIController@updateExemptVATPos');
    Route::get('downloadFileTender', 'DocumentAttachmentsAPIController@downloadFileTender');

    if (env("LOG_ENABLE", false)) {
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    }
});


Route::group(['middleware' => ['tenantById']], function (){

    Route::get('pull_company_details', 'POS\PosAPIController@pullCompanyDetails');
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

Route::group(['middleware' => 'max_memory_limit'], function () {
    Route::group(['middleware' => 'max_execution_limit'], function () {
        Route::post('documentUpload', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@documentUpload');
    });
});

Route::get('viewDocument', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocument');
Route::get('viewDocumentEmployeeImg', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocumentEmployeeImg');
Route::get('viewDocumentEmployeeImgBulk', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocumentEmployeeImgBulk');
Route::post('documentUploadDelete', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@documentUploadDelete');
Route::get('viewHrDocuments', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewHrDocuments');
 

if (env("LOG_ENABLE", false)) {
    Route::get('runCronJob/{cron}', function ($cron) {
        Artisan::call($cron);
        return 'CRON Job run successfully';
    });
}       



/*
 * End external related routes
 */

Route::resource('s_r_m_supplier_values', 'SRMSupplierValuesAPIController');
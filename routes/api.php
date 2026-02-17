<?php

use App\Http\Middleware\MobileAccessVerify;

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
use App\Http\Middleware\ExtractHeadersFromBody;


Route::group(['middleware' => ['mobileServer']], function () {
    Route::group(['middleware' => ['tenant','locale', 'cors']], function () {
        Route::get('getAppearance', 'CompanyAPIController@getAppearance')->middleware(MobileAccessVerify::class);
        Route::post('postEmployeeFromPortal', 'HelpDesk\HelpDeskAPIController@postEmployee');

        //thrid party APIs
        Route::group(['middleware' => ['thirdPartyApis', 'thirdPartyApiLogger']], function (){
            require __DIR__.'/../routes/externalApis/externalRoutes.php';
        });

        Route::post('customer_master_pull', 'CustomerMasterAPIController@pullCustomerMaster');
        
        Route::post('updateDocumentCodeTransaction', 'DocumentCodeMasterAPIController@updateDocumentCodeTransaction')->middleware([ExtractHeadersFromBody::class,'auth.api.keycloak','authorization:api','mobileAccess']);

        Route::group(['middleware' => ['auth.api.keycloak', 'csrf.api']], function () {

            Route::group(['middleware' => ['authorization:api','mobileAccess']], function () {
                Route::post('getAllCreatedByEmployees', 'FilterApiController@getAllCreatedByEmployees')->name("Get all created by employees");

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
                require __DIR__.'/../routes/generalLedger/generalLedgerRoutes.php';
                require __DIR__.'/../routes/thirdParty/thirdPartyRoutes.php';
                require __DIR__.'/../routes/budget/budgetRoutes.php';

                Route::post('downloadCITemplate', 'CustomerInvoiceDirectAPIController@downloadCITemplate')->name("Download ci template");
                Route::post('getCustomerInvoiceUploads', 'CustomerInvoiceDirectAPIController@getCustomerInvoiceUploads')->name("Get upload customer invoice");

                Route::resource('segment_assigneds', 'SegmentAssignedAPIController');

                Route::resource('navigation_menuses', 'NavigationMenusAPIController');

                Route::resource('navigation_user_group_setups', 'NavigationUserGroupSetupAPIController');

                Route::get('user/companies', 'UserAPIController@userCompanies')->name("Get user companies");
                Route::get('checkUser', 'UserAPIController@checkUser')->name("Check user");

                Route::get('getSuppliersByCompany', 'SupplierMasterAPIController@getSuppliersByCompany')->name("Get suppliers by company");

                Route::resource('registered_supplier_currencies', 'RegisteredSupplierCurrencyAPIController');
                Route::resource('registered_bank_memo_suppliers', 'RegisteredBankMemoSupplierAPIController');

                Route::get('user/menu', 'NavigationUserGroupSetupAPIController@userMenu')->name("Get user menu");
                Route::get('getUserMenu', 'NavigationUserGroupSetupAPIController@getUserMenu')->name("Get user menu by user");

                Route::group(['middleware' => 'max_memory_limit'], function () {
                    Route::group(['middleware' => 'max_execution_limit'], function () {
                        Route::post('getAllDocumentApproval', 'DocumentApprovedAPIController@getAllDocumentApproval')->name("Get all document approval");
                        Route::post('uploadBudgets', 'BudgetMasterAPIController@uploadBudgets')->name("Upload budgets");
                        Route::post('assetCostingUpload', 'FixedAssetMasterAPIController@assetCostingUpload')->name("Asset Costing Upload");
                        Route::post('generateAssetDepBulkPDF', 'FixedAssetDepreciationMasterAPIController@generateAssetDepBulkPDF')->name("Generate asset depreciation bulk PDF");
                        Route::post('uploadCustomerInvoice', 'CustomerInvoiceDirectAPIController@uploadCustomerInvoice')->name("Upload customer invoice");
                        Route::post('deleteBudgetUploads', 'BudgetMasterAPIController@deleteBudgetUploads')->name("Delete budget uploads");
                        Route::post('deleteCustomerInvoiceUploads', 'CustomerInvoiceDirectAPIController@deleteCustomerInvoiceUploads')->name("Delete budget uploads");
                    });
                });

                Route::get('getCompanyLocalCurrencyCode', 'CurrencyMasterAPIController@getCompanyLocalCurrencyCode')->name("Get company local currency code");
                Route::get('getCompanyCurrency', 'CurrencyMasterAPIController@getCompanyCurrency')->name("Get company currency");
                Route::resource('users', 'UserAPIController');
                Route::resource('supplier_category_masters', 'SupplierCategoryMasterAPIController');

                Route::resource('country_masters', 'CountryMasterAPIController');
                Route::resource('supplier_category_subs', 'SupplierCategorySubAPIController');


                Route::resource('supplier_importances', 'SupplierImportanceAPIController');

                Route::resource('suppliernatures', 'suppliernatureAPIController');

                Route::resource('supplier_types', 'SupplierTypeAPIController');

                Route::resource('supplier_currencies', 'SupplierCurrencyAPIController');

                Route::resource('supplier_criticals', 'SupplierCriticalAPIController');

                Route::resource('yes_no_selections', 'YesNoSelectionAPIController');

                Route::resource('document_masters', 'DocumentMasterAPIController');

                Route::resource('supplier_contact_types', 'SupplierContactTypeAPIController');

                Route::resource('bank_memo_supplier_masters', 'BankMemoSupplierMasterAPIController');

                Route::resource('user_types', 'UserTypeAPIController');

                Route::resource('mi_bulk_upload_error_logs', 'MiBulkUploadErrorLogAPIController');

                Route::post('getCurrencyDetails', 'SupplierCurrencyAPIController@getCurrencyDetails')->name("Get currency details");

                Route::resource('units', 'UnitAPIController');

                Route::post('financeItemCategorySubsAttributesUpdate', 'FinanceItemCategorySubAPIController@financeItemCategorySubsAttributesUpdate')->name("Update finance item category sub attributes");

                Route::resource('finance_item_category_masters', 'FinanceItemCategoryMasterAPIController');

                Route::get('reasonCodeMasterRecordSalesReturn/{id}', 'ReasonCodeMasterAPIController@reasonCodeMasterRecordSalesReturn')->name("Get reason code master record sales return");

                Route::resource('example_table_templates', 'ExampleTableTemplateAPIController');

                Route::get('getItemMasterPurchaseRequestHistory', 'PurchaseRequestDetailsAPIController@getItemMasterPurchaseRequestHistory')->name("Get item master purchase request history");

                Route::get('getDropdownValues', 'FinanceItemCategoryMasterAPIController@getDropdownValues')->name("Get dropdown values");


                Route::post('addItemAttributes', 'FinanceItemCategoryMasterAPIController@addItemAttributes')->name("Add item attributes");

                Route::resource('erp_attributes', 'ErpAttributesAPIController');
                Route::post('itemAttributesIsMandotaryUpdate', 'ErpAttributesAPIController@itemAttributesIsMandotaryUpdate')->name("Update item attributes is mandatory");
                Route::post('itemAttributesDelete', 'ErpAttributesAPIController@itemAttributesDelete')->name("Delete item attributes");

                Route::resource('erp_attributes_dropdowns', 'ErpAttributesDropdownAPIController');
                Route::post('addDropdownData', 'ErpAttributesDropdownAPIController@addDropdownData')->name("Add dropdown data");
                Route::post('getDropdownData', 'ErpAttributesDropdownAPIController@getDropdownData')->name("Get dropdown data");

                Route::resource('erp_attributes_field_types', 'ErpAttributesFieldTypeAPIController');

                Route::resource('item_category_type_masters', 'ItemCategoryTypeMasterAPIController');
                Route::resource('finance_item_category_types', 'FinanceItemCategoryTypesAPIController');
                /** Company Navigation Menu access*/

                Route::resource('company_navigation_menuses', 'CompanyNavigationMenusAPIController');
                /** Company user group*/
                Route::get('getAllCompanies', 'CompanyAPIController@getAllCompanies')->name("Get all companies");
                Route::resource('user_group_assigns', 'UserGroupAssignAPIController');

                Route::resource('approval_roles', 'ApprovalRoleAPIController');
                Route::resource('department_masters', 'DepartmentMasterAPIController');
                Route::get('getAllApprovalGroup', 'ApprovalGroupsAPIController@getAllApprovalGroup')->name("Get all approval group");

                /** Chart of Account Created by Shafri */

                Route::get('getAssignedChartOfAccounts', 'ChartOfAccountsAssignedAPIController@getAssignedChartOfAccounts')->name("Get assigned chart of accounts");

                Route::resource('erp_locations', 'ErpLocationAPIController');
                Route::resource('accounts_types', 'AccountsTypeAPIController');

                /** Segment master Created by Nazir  */

                Route::post('getAllSegmentMaster', 'SegmentMasterAPIController@getAllSegmentMaster')->name("Get all segment master");



                //confirmation
                Route::post('confirmDocument', 'PurchaseRequestAPIController@confirmDocument')->name("Confirm document");

                Route::resource('priorities', 'PriorityAPIController');

                Route::resource('locations', 'LocationAPIController');

                Route::resource('yes_no_selection_for_minuses', 'YesNoSelectionForMinusAPIController');

                Route::resource('months', 'MonthsAPIController');

                Route::post('delete-item-qnty-by-pr', 'PurchaseRequestAPIController@delteItemQntyPR')->name("Delete item quantity by purchase request");

                Route::resource('document_approveds', 'DocumentApprovedAPIController');

                Route::resource('procument_order_details', 'ProcumentOrderDetailAPIController');

                Route::resource('srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController');
                Route::get('get_srp_erp_document_attachments', 'SrpErpDocumentAttachmentsAPIController@geDocumentAttachments')->name("Get srp erp document attachments");



                Route::post('updatePoPaymentTermsLogistic', 'PoAdvancePaymentAPIController@updatePoPaymentTermsLogistic')->name("Update po payment terms logistic");

                Route::post('approveSupplier', 'SupplierMasterAPIController@approveSupplier')->name("Approve supplier");

                Route::post('approveCustomer', 'CustomerMasterAPIController@approveCustomer')->name("Approve customer");

                Route::post('approveChartOfAccount', 'ChartOfAccountAPIController@approveChartOfAccount')->name("Approve chart of account");

                Route::post('approveProcurementOrder', 'ProcumentOrderAPIController@approveProcurementOrder')->name("Approve procurement order");


                Route::post('getGRVDrilldownSpentAnalysis', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysis')->name("Get GRV drilldown spent analysis");
                Route::post('getGRVDrilldownSpentAnalysisTotal', 'ProcumentOrderAPIController@getGRVDrilldownSpentAnalysisTotal')->name("Get GRV drilldown spent analysis total");

                /** Po Related Tables Created by Nazir  */
                Route::resource('erp_addresses', 'ErpAddressAPIController');
                Route::resource('po_payment_terms', 'PoPaymentTermsAPIController');
                Route::resource('po_advance_payments', 'PoAdvancePaymentAPIController');

                Route::post('reportSpentAnalysisDrilldownExport', 'ProcumentOrderAPIController@reportSpentAnalysisDrilldownExport')->name("Report spent analysis drilldown export");

                Route::get('exchangerate', 'ApprovalLevelAPIController@confirmDocTest')->name("Exchange rate");

                Route::resource('po_payment_term_types', 'PoPaymentTermTypesAPIController');


                Route::resource('purchase_order_process_details', 'PurchaseOrderProcessDetailsAPIController');

                Route::resource('tax_types', 'TaxTypeAPIController');

                Route::resource('alerts', 'AlertAPIController');
                Route::resource('access_tokens', 'AccessTokensAPIController');
                Route::resource('users_log_histories', 'UsersLogHistoryAPIController');

                Route::resource('address_types', 'AddressTypeAPIController');

                Route::resource('company_policy_categories', 'CompanyPolicyCategoryAPIController');

                Route::resource('g_r_v_types', 'GRVTypesAPIController');
                Route::resource('budget_consumed_datas', 'BudgetConsumedDataAPIController');
                Route::resource('customer_invoices', 'CustomerInvoiceAPIController');

                Route::resource('accounts_receivable_ledgers', 'AccountsReceivableLedgerAPIController');

                Route::resource('item_issue_types', 'ItemIssueTypeAPIController');

                Route::resource('accounts_payable_ledgers', 'AccountsPayableLedgerAPIController');


                Route::get('getNotifications', 'UserAPIController@getNotifications')->name("Get notifications");
                Route::post('updateNotification', 'UserAPIController@updateNotification')->name("Update notification");
                Route::post('getAllNotifications', 'UserAPIController@getAllNotifications')->name("Get all notifications");
                Route::get('getImageByPath', 'DocumentAttachmentsAPIController@getImageByPath')->name("Get image by path");

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

                Route::post('getAllDocumentApprovalTest', 'DocumentApprovedAPIController@getAllDocumentApproval')->name("Get all document approval test");
                //Route::get('getTotalCountOfApproval', 'DocumentApprovedAPIController@getTotalCountOfApproval');

                Route::get('getAllApprovalDocuments', 'DocumentMasterAPIController@getAllApprovalDocuments')->name("Get all approval documents");

                Route::get('getJobsByContractAndCustomer', 'CustomerMasterAPIController@getJobsByContractAndCustomer')->name("Get jobs by contract and customer");

                Route::resource('performa_details', 'PerformaDetailsAPIController');
                Route::resource('free_billing_master_performas', 'FreeBillingMasterPerformaAPIController');
                Route::resource('ticket_masters', 'TicketMasterAPIController');
                Route::resource('field_masters', 'FieldMasterAPIController');



                Route::resource('item_client_reference', 'ItemClientReferenceNumberMasterAPIController');







                Route::resource('performa_masters', 'PerformaMasterAPIController');
                Route::resource('rig_masters', 'RigMasterAPIController');

                //Logistic Configuration Master

                Route::resource('port_masters', 'PortMasterAPIController');
                Route::resource('delivery_terms_masters', 'DeliveryTermsMasterAPIController');

                Route::post('approveSupplierInvoice', 'BookInvSuppMasterAPIController@approveSupplierInvoice')->name("Approve supplier invoice");

                Route::resource('expense_claims', 'ExpenseClaimAPIController');
                Route::resource('expense_claim_types', 'ExpenseClaimTypeAPIController');
                Route::resource('expense_claim_categories', 'ExpenseClaimCategoriesAPIController');
                Route::get('getPaymentStatusHistory', 'ExpenseClaimAPIController@getPaymentStatusHistory')->name("Get payment status history");
                Route::get('getDetailsByExpenseClaim', 'ExpenseClaimDetailsAPIController@getDetailsByExpenseClaim')->name("Get details by expense claim");
                Route::get('preCheckECDetailEdit', 'ExpenseClaimDetailsAPIController@preCheckECDetailEdit')->name("Pre check ecdetail edit");

                Route::resource('expense_claim_details_masters', 'ExpenseClaimDetailsMasterAPIController');

                Route::resource('expense_claim_categories_masters', 'ExpenseClaimCategoriesMasterAPIController');

                Route::resource('logistic_mode_of_imports', 'LogisticModeOfImportAPIController');
                Route::resource('logistic_shipping_modes', 'LogisticShippingModeAPIController');
                Route::resource('logistic_statuses', 'LogisticStatusAPIController');

                Route::get('customerRecieptDetailsRecords', 'CustomerReceivePaymentDetailAPIController@customerRecieptDetailsRecords')->name("Get customer reciept details records");


                Route::get('getRVPaymentVoucherMatchItems', 'PaySupplierInvoiceMasterAPIController@getRVPaymentVoucherMatchItems')->name("Get rv payment voucher match items");

                Route::post('updatePrintChequeItems', 'BankLedgerAPIController@updatePrintChequeItems')->name("Update print cheque items");

                Route::post('referBackCosting', 'FixedAssetMasterAPIController@referBackCosting')->name("Refer back costing");

                // Receipt Voucher
                Route::resource('unbilled_g_r_vs', 'UnbilledGRVAPIController');
                Route::resource('performa_temps', 'PerformaTempAPIController');
                Route::resource('free_billings', 'FreeBillingAPIController');

                Route::post('capitalizationReopen', 'AssetCapitalizationAPIController@capitalizationReopen')->name("Capitalization reopen");
                Route::post('referBackCapitalization', 'AssetCapitalizationAPIController@referBackCapitalization')->name("Refer back capitalization");
                Route::post('deleteAllAssetCapitalizationDet', 'AssetCapitalizationDetailAPIController@deleteAllAssetCapitalizationDet')->name("Delete all asset capitalization det");

                Route::resource('bookInvSuppDetRefferedbacks', 'BookInvSuppDetRefferedBackAPIController');
                Route::resource('DirectInvoiceDetRefferedbacks', 'DirectInvoiceDetailsRefferedBackAPIController');

                Route::get('getAllcompaniesByDepartment', 'DocumentApprovedAPIController@getAllcompaniesByDepartment')->name("Get all companies by department");

                Route::post('assetCostingReopen', 'FixedAssetMasterAPIController@assetCostingReopen')->name("Asset costing reopen");

                Route::post('amendAssetCostingReview', 'FixedAssetMasterAPIController@amendAssetCostingReview')->name("Amend asset costing review");

                Route::get('assetDepreciationByID/{id}', 'FixedAssetDepreciationMasterAPIController@assetDepreciationByID')->name("Get asset depreciation by id");
                Route::get('assetDepreciationMaster', 'FixedAssetDepreciationMasterAPIController@assetDepreciationMaster')->name("Get asset depreciation master");
                Route::post('assetDepreciationReopen', 'FixedAssetDepreciationMasterAPIController@assetDepreciationReopen')->name("Asset depreciation reopen");
                Route::post('referBackDepreciation', 'FixedAssetDepreciationMasterAPIController@referBackDepreciation')->name("Refer back depreciation");
                Route::post('amendAssetDepreciationReview', 'FixedAssetDepreciationMasterAPIController@amendAssetDepreciationReview')->name("Amend asset depreciation review");

                Route::resource('fixed_asset_insurance_details', 'FixedAssetInsuranceDetailAPIController');

                Route::post('deleteAllDisposalDetail', 'AssetDisposalDetailAPIController@deleteAllDisposalDetail')->name("Delete all disposal detail");

                Route::resource('budget_adjustments', 'BudgetAdjustmentAPIController');
                Route::resource('audit_trails', 'AuditTrailAPIController');




                Route::resource('fixed_asset_depreciation_periods', 'FixedAssetDepreciationPeriodAPIController');
                Route::resource('asset_types', 'AssetTypeAPIController');



                Route::resource('h_r_m_s_jv_details', 'HRMSJvDetailsAPIController');
                Route::resource('h_r_m_s_jv_masters', 'HRMSJvMasterAPIController');
                Route::resource('accruaval_from_o_p_masters', 'AccruavalFromOPMasterAPIController');
                Route::resource('fixed_asset_costs', 'FixedAssetCostAPIController');
                Route::resource('insurance_policy_types', 'InsurancePolicyTypeAPIController');


                Route::post('generateAssetDetailDrilldown', 'AssetManagementReportAPIController@generateAssetDetailDrilldown')->name("Generate asset detail drilldown");
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


                Route::post('getCreditNoteAmendHistory', 'CreditNoteReferredbackAPIController@getCreditNoteAmendHistory')->name("Get credit note amend history");
                Route::resource('creditNoteReferredbackCRUD', 'CreditNoteReferredbackAPIController');
                Route::resource('creditNoteDetailsRefferdbacks', 'CreditNoteDetailsRefferdbackAPIController');
                Route::get('getCapitalizationLinkedDocument', 'AssetCapitalizationAPIController@getCapitalizationLinkedDocument')->name("Get capitalization linked document");
                Route::get('getCNDetailAmendHistory', 'CreditNoteDetailsRefferdbackAPIController@getCNDetailAmendHistory')->name("Get cn detail amend history");

                Route::resource('customerInvoiceDetRefferedbacks', 'CustomerInvoiceDirectDetRefferedbackAPIController');

                Route::resource('supplier_category_icv_subs', 'SupplierCategoryICVSubAPIController');
                Route::resource('supplier_category_icv_masters', 'SupplierCategoryICVMasterAPIController');


                Route::resource('debitNoteDetailsRefferedbacks', 'DebitNoteDetailsRefferedbackAPIController');

                Route::resource('jvDetailsReferredbacks', 'JvDetailsReferredbackAPIController');

                Route::post('getAllCapitalizationAmendHistory', 'AssetCapitalizationReferredAPIController@getAllCapitalizationAmendHistory')->name("Get all capitalization amend history");
                Route::get('assetCapitalizationHistoryByID', 'AssetCapitalizationReferredAPIController@assetCapitalizationHistoryByID')->name("Get asset capitalization history by id");
                Route::get('getCapitalizationDetailsHistory', 'AssetCapitalizatioDetReferredAPIController@getCapitalizationDetailsHistory')->name("Get capitalization details history");
                Route::post('getAllAssetDisposalAmendHistory', 'AssetDisposalReferredAPIController@getAllAssetDisposalAmendHistory')->name("Get all asset disposal amend history");
                Route::get('assetDisposalHistoryByID', 'AssetDisposalReferredAPIController@assetDisposalHistoryByAutoID')->name("Get asset disposal history by auto id");
                Route::get('getAssetDisposalDetailHistory', 'AssetDisposalDetailReferredAPIController@getAssetDisposalDetailHistory')->name("Get asset disposal detail history");

                Route::resource('fixedassetmasterreferredhistory', 'FixedAssetMasterReferredHistoryAPIController');
                Route::post('getAllAssetCostingAmendHistory', 'FixedAssetMasterReferredHistoryAPIController@getAllAssetCostingAmendHistory')->name("Get all asset costing amend history");
                Route::get('assetCostingHistoryByAutoID', 'FixedAssetMasterReferredHistoryAPIController@assetCostingHistoryByAutoID')->name("Get asset costing history by auto id");
                Route::resource('depmasterreferredhistory', 'DepreciationMasterReferredHistoryAPIController');
                Route::post('getAllDepreciationAmendHistory', 'DepreciationMasterReferredHistoryAPIController@getAllDepreciationAmendHistory')->name("Get all depreciation amend history");
                Route::get('assetDepreciationHistoryByID', 'DepreciationMasterReferredHistoryAPIController@assetDepreciationHistoryByID')->name("Get depreciation history by id");
                Route::resource('depperiodsreferredhistory', 'DepreciationPeriodsReferredHistoryAPIController');
                Route::post('getAssetDepPeriodHistoryByID', 'DepreciationPeriodsReferredHistoryAPIController@getAssetDepPeriodHistoryByID')->name("Get asset dep period history by id");

                Route::resource('asset_capitalization_referreds', 'AssetCapitalizationReferredAPIController');
                Route::resource('asset_capitalizatio_det_referreds', 'AssetCapitalizatioDetReferredAPIController');
                Route::resource('asset_disposal_referreds', 'AssetDisposalReferredAPIController');
                Route::resource('asset_disposal_detail_referreds', 'AssetDisposalDetailReferredAPIController');

                Route::resource('bankTransferDetailRefferedBacks', 'PaymentBankTransferDetailRefferedBackAPIController');


                Route::resource('grvDetailsRefferedbacks', 'GrvDetailsRefferedbackAPIController');
                Route::resource('document_restriction_assigns', 'DocumentRestrictionAssignAPIController');
                Route::resource('document_restriction_policies', 'DocumentRestrictionPolicyAPIController');

                Route::post('getEmployeeMasterView', 'EmployeeAPIController@getEmployeeMasterView')->name("Get employee master view");
                Route::post('confirmEmployeePasswordReset', 'EmployeeAPIController@confirmEmployeePasswordReset')->name("Confirm employee password reset");

                Route::resource('bank_account_reffered_backs', 'BankAccountRefferedBackAPIController');


                Route::resource('companyFinanceYearPeriodMasters', 'CompanyFinanceYearperiodMasterAPIController');

                Route::resource('counter', 'CounterAPIController');
                Route::post('getCountersByCompany', 'CounterAPIController@getCountersByCompany')->name("Get counters by company");
                Route::get('getCounterFormData', 'CounterAPIController@getCounterFormData')->name("Get counter form data");

                Route::resource('posPaymentGlConfigMasters', 'GposPaymentGlConfigMasterAPIController');
                Route::resource('posPaymentGlConfigDetails', 'GposPaymentGlConfigDetailAPIController');
                Route::post('getPosGlConfigByCompany', 'GposPaymentGlConfigDetailAPIController@getConfigByCompany')->name("Get pos gl config by company");
                Route::get('getPosGlConfigFormData', 'GposPaymentGlConfigDetailAPIController@getFormData')->name("Get pos gl config form data");
                Route::get('getPosShiftDetails', 'ShiftDetailsAPIController@getPosShiftDetails')->name("Get pos shift details");

                Route::get('getPosSourceShiftDetails', 'ShiftDetailsAPIController@getPosSourceShiftDetails')->name("Get pos source shift details");
                Route::get('getPosCustomerMasterDetails', 'ShiftDetailsAPIController@getPosCustomerMasterDetails')->name("Get pos customer master details");
                Route::post('postPosCustomerMapping', 'ShiftDetailsAPIController@postPosCustomerMapping')->name("Post pos customer mapping");
                Route::post('postPosTaxMapping', 'ShiftDetailsAPIController@postPosTaxMapping')->name("Post pos tax mapping");
                Route::post('postPosPayMapping', 'ShiftDetailsAPIController@postPosPayMapping')->name("Post pos pay mapping");
                Route::post('postPosEntries', 'ShiftDetailsAPIController@postPosEntries')->name("Post pos entries");
                Route::post('insufficientItems', 'ShiftDetailsAPIController@insufficientItems')->name("Insufficient items");
                Route::post('getPosMismatchEntries', 'ShiftDetailsAPIController@getPosMismatchEntries')->name("Get pos mismatch entries");
                Route::post('getPosMisMatchData', 'ShiftDetailsAPIController@getPosMisMatchData')->name("Get pos mis match data");
                Route::post('updatePosMismatch', 'ShiftDetailsAPIController@updatePosMismatch')->name("Update pos mismatch");
                Route::post('getGlMatchEntries', 'ShiftDetailsAPIController@getGlMatchEntries')->name("Get gl match entries");
                Route::post('exportInsufficientItems', 'ShiftDetailsAPIController@exportInsufficientItems')->name("Export insufficient items");

                Route::resource('currency_denominations', 'CurrencyDenominationAPIController');
                Route::resource('shift_details', 'ShiftDetailsAPIController');
                Route::get('getPosCustomerSearch', 'CustomerMasterAPIController@getPosCustomerSearch')->name("Get pos customer search");

                Route::resource('docEmailNotificationMasters', 'DocumentEmailNotificationMasterAPIController');

                Route::resource('gposInvoices', 'GposInvoiceAPIController');
                Route::get('getInvoiceDetails', 'GposInvoiceAPIController@getInvoiceDetails')->name("Get invoice details");
                Route::post('getInvoicesByShift', 'GposInvoiceAPIController@getInvoicesByShift')->name("Get invoices by shift");
                Route::resource('gposInvoiceDetails', 'GposInvoiceDetailAPIController');
                Route::resource('gposInvoicePayments', 'GposInvoicePaymentsAPIController');

                Route::resource('quotationVersionDetails', 'QuotationVersionDetailsAPIController');

                Route::resource('quotationDetailsRefferedbacks', 'QuotationDetailsRefferedbackAPIController');

                Route::get('printInvoice', 'GposInvoiceAPIController@printInvoice')->name("Print invoice");

                Route::resource('currency_conversion_histories', 'CurrencyConversionHistoryAPIController');

                Route::post('getAllNotDishachargeEmployeesDropdown', 'EmployeeAPIController@getAllNotDishachargeEmployeesDropdown')->name("Get all not dishacharge employees dropdown");

                /* For Profile -> Profile */
                Route::get('getProfileDetails', 'EmployeeAPIController@getProfileDetails')->name("Get profile details");

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
                Route::get('getPeriodsForPayslip', 'EmployeePayslipAPIController@getPeriodsForPayslip')->name("Get periods for payslip");
                Route::get('getEmployeePayslip', 'EmployeePayslipAPIController@getEmployeePayslip')->name("Get employee payslip");

                /* For Profile -> Expenses Claim */
                Route::get('getExpenseClaim', 'ExpenseClaimAPIController@getExpenseClaim')->name("Get expense claim");
                Route::get('getExpenseClaimHistory', 'ExpenseClaimAPIController@getExpenseClaimHistory')->name("Get expense claim history");
                Route::get('getExpenseClaimDepartment', 'ExpenseClaimAPIController@getExpenseClaimDepartment')->name("Get expense claim department");
                Route::get('getExpenseDropDownData', 'ExpenseClaimAPIController@getExpenseDropDownData')->name("Get expense drop down data");
                Route::post('saveExpenseClaimDetails', 'ExpenseClaimDetailsAPIController@saveExpenseClaimDetailsSingle')->name("Save expense claim details single");
                Route::post('saveExpenseClaimAttachments', 'ExpenseClaimDetailsAPIController@saveAttachments')->name("Save expense claim attachments");
                Route::get('getExpenseClaimDetails', 'ExpenseClaimAPIController@getExpenseClaimDetails')->name("Get expense claim details");

                /* For Profile -> Leave Application */
                Route::get('getLeaveHistory', 'LeaveDataMasterAPIController@getLeaveHistory')->name("Get leave history");
                Route::get('getLeaveTypes', 'LeaveMasterAPIController@getLeaveTypes')->name("Get leave types");
                Route::get('getLeaveAvailability', 'LeaveDataMasterAPIController@getLeaveAvailability')->name("Get leave availability");
                Route::post('saveLeaveDetails', 'LeaveDataMasterAPIController@saveLeaveDetails')->name("Save leave details");
                Route::post('updateLeaveDetails', 'LeaveDataMasterAPIController@updateLeaveDetails')->name("Update leave details");
                Route::get('getLeaveDetails', 'LeaveDataMasterAPIController@getLeaveDetails')->name("Get leave details");
                Route::get('downloadHrmsFile', 'HrmsDocumentAttachmentsAPIController@downloadFile')->name("Download hrms file");

                /*Company Document Attachments*/

                /* ChequeRegister */



                Route::get('getCancelledDetails', 'PurchaseRequestAPIController@getCancelledDetails')->name("Get cancelled details");
                Route::get('getClosedDetails', 'PurchaseRequestAPIController@getClosedDetails')->name("Get closed details");

                Route::resource('allocation_masters', 'AllocationMasterAPIController');



                Route::get('getLeaveTypeWithBalance', 'LeaveDataMasterAPIController@getLeaveTypeWithBalance')->name("Get leave type with balance");

                Route::resource('hrms_leave_accrual_masters', 'HRMSLeaveAccrualMasterAPIController');

                Route::resource('hrms_leave_accrual_details', 'HRMSLeaveAccrualDetailAPIController');

                Route::resource('hrms_period_masters', 'HRMSPeriodMasterAPIController');

                Route::resource('hrms_personal_documents', 'HRMSPersonalDocumentsAPIController');

                Route::get('getHRMSApprovals', 'LeaveDocumentApprovedAPIController@getHRMSApprovals')->name("Get hrms approvals");
                Route::get('getLeaveApproval', 'LeaveDocumentApprovedAPIController@getLeaveApproval')->name("Get leave approval");
                Route::post('leaveReferBack', 'LeaveDocumentApprovedAPIController@leaveReferBack')->name("Leave refer back");
                Route::post('approveLeave', 'LeaveDocumentApprovedAPIController@approveLeave')->name("Approve leave");
                Route::resource('hrms_leave_accrual_policy_types', 'HRMSLeaveAccrualPolicyTypeAPIController');

                Route::resource('employee_department_delegations', 'employeeDepartmentDelegationAPIController');
                Route::post('approveHRMSDocument', 'LeaveDocumentApprovedAPIController@approveHRMSDocument')->name("Approve hrms document");
                Route::post('referBackHRMSDocument', 'LeaveDocumentApprovedAPIController@referBackHRMSDocument')->name("Refer back hrms document");

                Route::get('getUserCountData', 'EmployeeAPIController@getUserCountData')->name("Get user count data");

                Route::resource('customer_invoice_trackings', 'CustomerInvoiceTrackingAPIController');

                Route::get('getBatchSubmissionFormData', 'CustomerInvoiceTrackingAPIController@getBatchSubmissionFormData')->name("Get batch submission form data");
                Route::get('getContractServiceLine', 'CustomerInvoiceTrackingAPIController@getContractServiceLine')->name("Get contract service line");
                Route::post('getAllBatchSubmissionByCompany', 'CustomerInvoiceTrackingAPIController@getAllBatchSubmissionByCompany')->name("Get all batch submission by company");
                Route::post('getCustomerInvoicesForBatchSubmission', 'CustomerInvoiceTrackingAPIController@getCustomerInvoicesForBatchSubmission')->name("Get customer invoices for batch submission");
                Route::post('addBatchSubmitDetails', 'CustomerInvoiceTrackingDetailAPIController@addBatchSubmitDetails')->name("Add batch submit details");
                Route::get('getItemsByBatchSubmission', 'CustomerInvoiceTrackingDetailAPIController@getItemsByBatchSubmission')->name("Get items by batch submission");
                Route::post('exportBatchSubmissionDetails', 'CustomerInvoiceTrackingAPIController@exportBatchSubmissionDetails')->name("Export batch submission details");
                Route::post('getContractByCustomer', 'AccountsReceivableReportAPIController@getContractByCustomer')->name("Get contract by customer");

                Route::get('getINVTrackingFormData', 'CustomerInvoiceTrackingAPIController@getINVTrackingFormData')->name("Get inv tracking form data");
                Route::post('updateAllInvoiceTrackingDetail', 'CustomerInvoiceTrackingAPIController@updateAllInvoiceTrackingDetail')->name("Update all invoice tracking detail");
                Route::post('deleteAllInvoiceTrackingDetail', 'CustomerInvoiceTrackingAPIController@deleteAllInvoiceTrackingDetail')->name("Delete all invoice tracking detail");

                Route::resource('pre_defined_report_templates', 'PreDefinedReportTemplateAPIController');
                Route::resource('erp_print_template_masters', 'ErpPrintTemplateMasterAPIController');
                Route::resource('erp_document_templates', 'ErpDocumentTemplateAPIController');
                Route::resource('user_rights', 'UserRightsAPIController');
                Route::resource('lpt_permissions', 'LptPermissionAPIController');
                Route::resource('customer_invoice_tracking_details', 'CustomerInvoiceTrackingDetailAPIController');
                Route::resource('service_lines', 'ServiceLineAPIController');
                Route::resource('chartOfAccount/allocation/histories', 'ChartOfAccountAllocationDetailHistoryAPIController');
                Route::resource('secondary_companies', 'SecondaryCompanyAPIController');

                Route::post('getSupplierCatalogDetailBySupplierItem', 'SupplierCatalogMasterAPIController@getSupplierCatalogDetailBySupplierItem')->name("Get supplier catalog detail by supplier item");

                Route::get('getDashboardDepartment', 'DashboardWidgetMasterAPIController@getDashboardDepartment')->name("Get dashboard department");
                Route::get('getDashboardWidget', 'DashboardWidgetMasterAPIController@getDashboardWidget')->name("Get dashboard widget");
                Route::post('getCustomWidgetGraphData', 'DashboardWidgetMasterAPIController@getCustomWidgetGraphData')->name("Get custom widget graph data");
                Route::post('getPreDefinedWidgetData', 'DashboardWidgetMasterAPIController@getPreDefinedWidgetData')->name("Get pre defined widget data");
                Route::post('logoutApiUser', 'FcmTokenAPIController@logoutApiUser')->name("Logout api user");
                Route::post('getCurrentHomeUrl', 'FcmTokenAPIController@redirectHome')->name("Get current home url");
                Route::post('exportWidgetExcel', 'DashboardWidgetMasterAPIController@exportWidgetExcel')->name("Export widget excel");


                Route::post('saveDeliveryOrderTaxDetails', 'DeliveryOrderDetailAPIController@saveDeliveryOrderTaxDetail')->name("Save Delivery Order Tax Detail");







                Route::resource('client_performa_app_types', 'ClientPerformaAppTypeAPIController');





                //Route::resource('chart_of_account_allocation_detail_histories', 'ChartOfAccountAllocationDetailHistoryAPIController');


                Route::resource('do_detail_refferedbacks', 'DeliveryOrderDetailRefferedbackAPIController');


                Route::resource('customer_invoice_status_types', 'CustomerInvoiceStatusTypeAPIController');
                Route::resource('tax_masters', 'TaxMasterAPIController');
                Route::resource('fcm_tokens', 'FcmTokenAPIController');
                Route::resource('user_activity_logs', 'UserActivityLogAPIController');

                Route::resource('mobile_no_pools', 'MobileNoPoolAPIController');
                Route::post('getAllMobileNo', 'MobileNoPoolAPIController@getAllMobileNo')->name("Get all mobile no");

                Route::resource('mobile_masters', 'MobileMasterAPIController');
                Route::post('getAllMobileMaster', 'MobileMasterAPIController@getAllMobileMaster')->name("Get all mobile master");
                Route::get('getMobileMasterFormData', 'MobileMasterAPIController@getMobileMasterFormData')->name("Get mobile master form data");

                Route::resource('mobile_bill_masters', 'MobileBillMasterAPIController');
                Route::post('getAllMobileBill', 'MobileBillMasterAPIController@getAllMobileBill')->name("Get all mobile bill");
                Route::get('getMobileBillFormData', 'MobileBillMasterAPIController@getMobileBillFormData')->name("Get mobile bill form data");

                Route::resource('mobile_bill_summaries', 'MobileBillSummaryAPIController');
                Route::post('importMobileBillDocument', 'MobileBillSummaryAPIController@importMobileBillDocument')->name("Import mobile bill document");

                Route::resource('mobile_details', 'MobileDetailAPIController');

                Route::resource('quotation_status_masters', 'QuotationStatusMasterAPIController');

                Route::post('mobileSummaryDetailDelete', 'MobileBillMasterAPIController@mobileSummaryDetailDelete')->name("Mobile summary detail delete");

                Route::resource('employee_mobile_bill_masters', 'EmployeeMobileBillMasterAPIController');
                Route::post('generateEmployeeBill', 'EmployeeMobileBillMasterAPIController@generateEmployeeBill')->name("Generate employee bill");

                Route::post('getAllMobileBillSummaries', 'MobileBillSummaryAPIController@getAllMobileBillSummaries')->name("Get all mobile bill summaries");
                Route::post('getAllMobileBillDetail', 'MobileDetailAPIController@getAllMobileBillDetail')->name("Get all mobile bill detail");
                Route::post('getAllEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@getAllEmployeeMobileBill')->name("Get all employee mobile bill");

                Route::post('getMobileBillReport', 'MobileBillMasterAPIController@getMobileBillReport')->name("Get mobile bill report");
                Route::post('validateMobileReport', 'MobileBillMasterAPIController@validateMobileReport')->name("Validate mobile report");
                Route::get('getMobileReportFormData', 'MobileBillMasterAPIController@getMobileReportFormData')->name("Get mobile report form data");
                Route::post('exportMobileReport', 'MobileBillMasterAPIController@exportMobileReport')->name("Export mobile report");

                Route::resource('custom_report_masters', 'CustomReportMasterAPIController');
                Route::resource('custom_report_columns', 'CustomReportColumnsAPIController');
                Route::resource('custom_user_report_columns', 'CustomUserReportColumnsAPIController');
                Route::resource('custom_filters_columns', 'CustomFiltersColumnAPIController');
                Route::resource('custom_user_report_summarizes', 'CustomUserReportSummarizeAPIController');

                Route::get('getSalesQuotationRecord','QuotationMasterAPIController@getSalesQuotationRecord')->name("Get Sales Quotation Record");

                Route::get('downloadSummaryTemplate', 'MobileBillSummaryAPIController@downloadSummaryTemplate')->name("Download summary template");
                Route::get('downloadDetailTemplate', 'MobileDetailAPIController@downloadDetailTemplate')->name("Download detail template");
                Route::post('getCompaniesByGroup', 'CompanyAPIController@getCompaniesByGroup')->name("Get companies by group");
                Route::post('getBillMastersByCompany', 'MobileBillMasterAPIController@getBillMastersByCompany')->name("Get bill masters by company");
                Route::post('exportEmployeeMobileBill', 'EmployeeMobileBillMasterAPIController@exportEmployeeMobileBill')->name("Export employee mobile bill");


                Route::resource('ci_item_details_refferedbacks', 'CustomerInvoiceItemDetailsRefferedbackAPIController');

                Route::post('generateSalesMarketReportSoldQty', 'SalesMarketingReportAPIController@generateSoldQty')->name("Generate sales market report sold qty");

                Route::post('assetCostingRemove', 'FixedAssetMasterAPIController@assetCostingRemove')->name("Asset costing remove");

                Route::post('approveSalesReturn', 'SalesReturnAPIController@approveSalesReturn')->name("Approve sales return");
                Route::post('getSalesReturnDetailsForSI', 'SalesReturnAPIController@getSalesReturnDetailsForSI')->name("Get sales return details for si");

                Route::resource('grv_details_prns', 'GrvDetailsPrnAPIController');
                Route::post('appearanceSubmit', 'CompanyAPIController@appearanceSubmit')->name("Appearance submit");






                Route::post('approveCurrencyConversion', 'CurrencyConversionMasterAPIController@approveCurrencyConversion')->name("Approve currency conversion");
                Route::post('rejectCurrencyConversion', 'CurrencyConversionMasterAPIController@rejectCurrencyConversion')->name("Reject currency conversion");



                Route::resource('budget_detail_histories', 'BudgetDetailHistoryAPIController');


             

                /* Asset Request */
                Route::resource('asset_requests', 'AssetRequestAPIController');
                Route::get('getItemsOptionForAssetRequest', 'AssetRequestAPIController@getItemsOptionForAssetRequest')->name("Get items option for asset request");
                Route::post('mapLineItemAr', 'AssetRequestAPIController@mapLineItemAr')->name('Map line item Ar');

                /* Asset Transfer */
                Route::post('update-return-status', 'ERPAssetTransferDetailAPIController@UpdateReturnStatus')->name("Update return status");
                Route::get('asset-transfer-drop', 'ERPAssetTransferDetailAPIController@assetTransferDrop')->name("Asset transfer drop");
                Route::get('typeAheadAssetDrop', 'ERPAssetTransferDetailAPIController@typeAheadAssetDrop')->name("Type ahead asset drop");
                Route::post('add-employee-asset-transfer-asset-detail/{id}', 'ERPAssetTransferDetailAPIController@addEmployeeAsset')->name("Add employee asset transfer asset detail");
                Route::post('asset_transfer_detail_asset', 'ERPAssetTransferDetailAPIController@assetTransferDetailAsset')->name("Asset transfer detail asset");
                Route::get('getAssetDropPR', 'ERPAssetTransferAPIController@getAssetDropPR')->name("Get asset drop pr");
                Route::get('asset-location-value', 'ERPAssetTransferDetailAPIController@getAssetLocationValue')->name("Get asset location value");
                Route::post('amendAssetTrasfer', 'ERPAssetTransferAPIController@amendAssetTrasfer')->name("Amend asset trasfer");
                Route::post('getAssetTransferAmendHistory', 'AssetTransferReferredbackAPIController@getAssetTransferAmendHistory')->name("Get asset transfer amend history");
                Route::get('fetch-asset-transfer-master-amend/{id}', 'AssetTransferReferredbackAPIController@fetchAssetTransferMasterAmend')->name("Fetch asset transfer master amend");
                Route::get('get-employee-asset-transfer-details-amend/{id}', 'ERPAssetTransferDetailsRefferedbackAPIController@get_employee_asset_transfer_details_amend')->name("Get employee asset transfer details amend");
                Route::post('amendAssetVerification', 'AssetVerificationAPIController@amendAssetVerification')->name("Amend asset verification");
                Route::post('getAssetVerificationAmendHistory', 'ERPAssetVerificationReferredbackAPIController@getAssetVerificationAmendHistory')->name("Get asset verification amend history");
                Route::get('fetchAssetVerification/{id}', 'ERPAssetVerificationReferredbackAPIController@fetchAssetVerification')->name("Fetch asset verification");
                Route::post('fetchAssetVerificationDetailAmend', 'ERPAssetVerificationDetailReferredbackAPIController@fetchAssetVerificationDetailAmend')->name("Fetch asset verification detail amend");

                /* Chart Of Account Scenario configuration */
                Route::resource('system_gl_code_scenarios', 'SystemGlCodeScenarioAPIController');
                Route::resource('system_gl_code_scenario_details', 'SystemGlCodeScenarioDetailAPIController');

                Route::resource('module_masters', 'ModuleMasterAPIController');

                Route::resource('sub_module_masters', 'SubModuleMasterAPIController');

                Route::resource('module_assigneds', 'ModuleAssignedAPIController');

                Route::get('pdc-logs/banks', 'PdcLogAPIController@getAllBanks')->name("Get all banks");

                Route::get('getBankTemplates/{id}', 'ChequeTemplateBankAPIController@getBankTemplates')->name("Get bank templates");


                Route::resource('supplier_invoice_item_details', 'SupplierInvoiceItemDetailAPIController');

                Route::post('checkAssetAllocation', 'ExpenseAssetAllocationAPIController@checkAssetAllocation')->name("Check asset allocation");

                Route::resource('appointments', 'AppointmentAPIController');
                Route::resource('appointment_details', 'AppointmentDetailsAPIController');
                Route::resource('po_categories', 'PoCategoryAPIController');

                Route::resource('document_sub_products', 'DocumentSubProductAPIController');
                Route::resource('payment_types', 'PaymentTypeAPIController');
                Route::resource('elimination_ledgers', 'EliminationLedgerAPIController');

                Route::resource('inter_company_stock_transfers', 'InterCompanyStockTransferAPIController');

                Route::resource('employee_ledgers', 'EmployeeLedgerAPIController');
                Route::resource('srp_erp_pay_shift_employees', 'SrpErpPayShiftEmployeesAPIController');

                Route::resource('srp_erp_pay_shift_masters', 'SrpErpPayShiftMasterAPIController');


                Route::resource('job_error_logs', 'JobErrorLogAPIController');
                Route::get('checkConfigurationExit', 'BarcodeConfigurationAPIController@checkConfigurationExit')->name("Check configuration exit");


                Route::post('getAllShiftsRPOS', 'POS\PosAPIController@getAllShiftsRPOS')->name("Get all shifts rpos");
                Route::post('getAllInvoicesPos', 'POS\PosAPIController@getAllInvoicesPos')->name("Get all invoices pos");
                Route::post('getPosInvoiceData', 'POS\PosAPIController@getPosInvoiceData')->name("Get pos invoice data");
                Route::post('getAllInvoicesPosReturn', 'POS\PosAPIController@getAllInvoicesPosReturn')->name("Get all invoices pos return");
                Route::post('getPosInvoiceReturnData', 'POS\PosAPIController@getPosInvoiceReturnData')->name("Get pos invoice return data");
                Route::post('getAllInvoicesRPos', 'POS\PosAPIController@getAllInvoicesRPos')->name("Get all invoices rpos");
                Route::post('getRPOSInvoiceData', 'POS\PosAPIController@getRPOSInvoiceData')->name("Get rpos invoice data");
                Route::post('getAllShiftsGPOS', 'POS\PosAPIController@getAllShiftsGPOS')->name("Get all shifts gpos");
                Route::post('getAllBills', 'POS\PosAPIController@getAllBills')->name("Get all bills");

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
                Route::resource('monthly_declarations_types', 'MonthlyDeclarationsTypesAPIController');
                Route::resource('hr_monthly_deduction_masters', 'HrMonthlyDeductionMasterAPIController');
                Route::resource('hr_payroll_masters', 'HrPayrollMasterAPIController');
                Route::resource('hr_payroll_header_details', 'HrPayrollHeaderDetailsAPIController');
                Route::resource('hr_payroll_details', 'HrPayrollDetailsAPIController');
                Route::resource('hr_monthly_deduction_details', 'HrMonthlyDeductionDetailAPIController');
                Route::resource('h_r_document_description_forms', 'HRDocumentDescriptionFormsAPIController');
                Route::resource('h_r_document_description_masters', 'HRDocumentDescriptionMasterAPIController');
                Route::resource('h_r_emp_contract_histories', 'HREmpContractHistoryAPIController');
                Route::resource('srp_erp_template_masters', 'SrpErpTemplateMasterAPIController');
                Route::resource('srp_erp_form_categories', 'SrpErpFormCategoryAPIController');
                Route::resource('srp_erp_templates', 'SrpErpTemplatesAPIController');

                // erp_language
                Route::resource('erp_language_master', 'ERPLanguageMasterAPIController');
                Route::post('store-employee-language', 'ERPLanguageMasterAPIController@storeEmployeeLanguage')->name("Store employee language");


                Route::resource('finance_category_serials', 'FinanceCategorySerialAPIController');

                Route::resource('upload_customer_invoices', 'UploadCustomerInvoiceAPIController');
                Route::resource('log_upload_customer_invoices', 'LogUploadCustomerInvoiceAPIController');
                Route::resource('customer_invoice_upload_details', 'CustomerInvoiceUploadDetailAPIController');

                Route::post('checkCustomerInvoiceUploadStatus', 'CustomerInvoiceDirectAPIController@checkCustomerInvoiceUploadStatus')->name("Check customer invoice upload status");

                Route::resource('s_r_m_supplier_values', 'SRMSupplierValuesAPIController');
                Route::resource('credit_note_receipts', 'CreditNoteReceiptAPIController');
                Route::resource('pay_credit_note_details', 'PayCreditNoteDetailAPIController');

                Route::resource('workflow_configurations', 'WorkflowConfigurationAPIController');
                Route::resource('workflow_configuration_hod_actions', 'WorkflowConfigurationHodActionAPIController')->parameters(['workflow_configuration_hod_actions' => 'id']);
                Route::resource('hod_actions', 'HodActionAPIController');
                Route::resource('company_budget_plannings', 'CompanyBudgetPlanningAPIController');
                Route::resource('department_budget_plannings', 'DepartmentBudgetPlanningAPIController');
                Route::resource('dep_budget_pl_det_columns', 'DepBudgetPlDetColumnAPIController');
                Route::resource('dep_budget_pl_det_emp_columns', 'DepBudgetPlDetEmpColumnAPIController');
                require __DIR__.'/../routes/printPdf/printPdfRoutes.php';
                Route::post('pdf/signed-url', 'SignedPdfController@generateSignedUrl')->name('Generate signed url');

                Route::post('getThirdPartyApiLogDetail', 'AuditTrailAPIController@getThirdPartyApiLogDetail')->name("Get third party api log detail");

                Route::post('updateRouteAccess', 'RouteAPIController@updateRouteAccess')->name("Update route access");
            });
            Route::post('getConsolidatedDataAttachment', 'DocumentAttachmentsAPIController@getConsolidatedDataAttachment');
            Route::post('getAppointmentList', 'AppointmentAPIController@getAppointmentList');
            Route::get('downloadFileSRM', 'DocumentAttachmentsAPIController@downloadFileSRM');
            Route::get('downloadFileTender', 'DocumentAttachmentsAPIController@downloadFileTender');
            Route::get('downloadFileSRMTemplate', 'DocumentAttachmentsAPIController@downloadFileSRMTemplate');
        });

        Route::group(['middleware' => 'max_memory_limit'], function () {
            Route::group(['middleware' => 'max_execution_limit'], function () {
                Route::get('pdf/stream/{signature}', 'SignedPdfController@streamPdf')->where('signature', '[A-Za-z0-9_-]+');
            });
        });

        Route::get('validateSupplierRegistrationLink', 'SupplierMasterAPIController@validateSupplierRegistrationLink');
        Route::get('getSupplierRegisterFormData', 'SupplierMasterAPIController@getSupplierRegisterFormData');
        Route::post('registerSupplier', 'SupplierMasterAPIController@registerSupplier');
        Route::post('getSubCategoriesByMultipleMasterCategory', 'SupplierCategorySubAPIController@getSubCategoriesByMultipleMasterCategory');
        Route::post('getSupplierBusinessSubCategoriesByCategory', 'SupplierCategorySubAPIController@getSupplierBusinessSubCategoriesByCategory');

        Route::get('loginwithToken', 'UserAPIController@loginwithToken');

        if (env('APP_ENV') == 'local') {
            Route::post('login', 'AuthAPIController@auth')->middleware(MobileAccessVerify::class);
        }

        Route::post('oauth/login_with_token', 'AuthAPIController@authWithToken');
        
        Route::get('downloadFileFrom', 'DocumentAttachmentsAPIController@downloadFileFrom');
        Route::resource('work_order_generation_logs', 'WorkOrderGenerationLogAPIController');
        Route::resource('external_link_hashes', 'ExternalLinkHashAPIController');
        Route::resource('registered_suppliers', 'RegisteredSupplierAPIController');

        Route::get('notification-service', 'NotificationCompanyScenarioAPIController@notification_service');
        Route::get('leave/accrual/service_test', 'LeaveAccrualMasterAPIController@accrual_service_test');
        Route::get('test', 'TenantAPIController@test');
        Route::get('updateExemptVATPos', 'ProcumentOrderAPIController@updateExemptVATPos');
        Route::post('getCompanyTenderList', 'TenderMasterAPIController@getCompanyTenderList');
        if (env("LOG_ENABLE", false)) {
            Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
            // Route::get('/phpinfo', function () { phpinfo(); });
        }
        Route::post('getPortalRedirectUrl', 'FcmTokenAPIController@getPortalRedirectUrl');
    });


    Route::group(['middleware' => ['corsFree']], function () {
        Route::get('getConfigurationInfo', 'ConfigurationAPIController@getConfigurationInfo');
        Route::post('sendEmail', 'Email\SendEmailAPIController@sendEmail');
        Route::get('updateRoutes', 'RouteAPIController@updateRoutes');
        Route::get('updateRoleRoutes', 'RouteAPIController@updateRoleRoutes');

        require __DIR__.'/../routes/hrms/jobRoutes.php';
    });

    Route::group(['middleware' => ['tenantById', 'cors']], function (){
        Route::get('pull_company_details', 'POS\PosAPIController@pullCompanyDetails');
        Route::group(['middleware' => ['thirdPartyApis', 'thirdPartyApiLogger', 'hrms_employee']], function () {
            Route::post('postEmployee', 'HelpDesk\HelpDeskAPIController@postEmployee');
            Route::post('post_supplier_invoice', 'HRMS\HRMSAPIController@createSupplierInvoice');
            Route::post('create_supplier_invoices','BookInvSuppMasterAPIController@createSupplierInvoices');
            require __DIR__.'/../routes/osos_3_0/osos_3_0.php';
            Route::group(['middleware' => ['max_memory_limit']], function () {
                Route::group(['middleware' => ['max_execution_limit']], function () {
                    Route::post('documentUpload', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@documentUpload');
                });
                Route::get('viewDocument', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocument');
                Route::get('viewDocumentEmployeeImg', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocumentEmployeeImg');
                Route::get('viewDocumentEmployeeImgBulk', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewDocumentEmployeeImgBulk');
                Route::post('documentUploadDelete', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@documentUploadDelete');
                Route::get('viewHrDocuments', 'ThirdPartySystemsDocumentUploadAndDownloadAPIController@viewHrDocuments');
            });
        });
    });
    
    /*
     * Start SRM related routes
     */

    Route::group(['prefix' => 'srm'], function (){
        Route::group(['middleware' => ['tenantById', 'cors']], function (){
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
        Route::group(['middleware' => ['tenantById','access_token', 'cors']], function (){
            Route::post('createMaterielRequestsApi', 'MaterielRequestAPIController@createMaterialAPI');
            Route::post('createPurchaseRequestsApi', 'PurchaseRequestAPIController@createPurchaseAPI');
            Route::post('checkLedgerQty', 'ItemMasterAPIController@checkLedgerQty')->name('Check Ledger Qty');
        });
    });

    if (env("LOG_ENABLE", false)) {
        Route::get('updateUsersLoginType', 'EmployeeAPIController@updateUsersLoginType');
        Route::get('runCronJob/{cron}', function ($cron) {
            Artisan::call($cron);
            return 'CRON Job run successfully';
        });
        Route::get('confirmAPICreatedReceiptVouchers', 'ReceiptAPIController@confirmAPICreatedReceiptVouchers');
    }
});




/*
 * End external related routes
 */

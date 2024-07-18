<?php


/**
 * This file contains configuration module related routes
 * 
 * 
 * */


//report template
Route::group([], function(){
	Route::resource('report_templates', 'ReportTemplateAPIController');
    Route::resource('report_template_cash_banks', 'ReportTemplateCashBankAPIController');
    Route::resource('report_template_details', 'ReportTemplateDetailsAPIController');
    Route::resource('report_template_columns', 'ReportTemplateColumnsAPIController');
    Route::resource('report_template_column_links', 'ReportTemplateColumnLinkAPIController');
    Route::resource('report_template_field_types', 'ReportTemplateFieldTypeAPIController');
    Route::resource('report_template_cash_banks', 'ReportTemplateCashBankAPIController');
    Route::resource('report_template_documents', 'ReportTemplateDocumentAPIController');
    Route::resource('report_template_cash_banks', 'ReportTemplateCashBankAPIController');
    Route::resource('report_template_numbers', 'ReportTemplateNumbersAPIController');
    Route::resource('report_template_employees', 'ReportTemplateEmployeesAPIController');
    Route::resource('report_column_templates', 'ReportColumnTemplateAPIController');
    Route::resource('report_column_template_details', 'ReportColumnTemplateDetailAPIController');
    Route::resource('report_template_links', 'ReportTemplateLinksAPIController');


    Route::post('getAllReportTemplateForCopy', 'ReportTemplateAPIController@getAllReportTemplateForCopy')->name("Get all report templates for copy");
    Route::post('addTemplateSubCategory', 'ReportTemplateDetailsAPIController@addSubCategory')->name("Add template sub category");
    Route::post('getAllReportTemplate', 'ReportTemplateAPIController@getAllReportTemplate')->name("Get all report templates");
    Route::post('mirrorReportTemplateRowConfiguration', 'ReportTemplateDetailsAPIController@mirrorReportTemplateRowConfiguration')->name("Mirror report template row configurations");
    Route::post('linkPandLGLCodeValidation', 'ReportTemplateDetailsAPIController@linkPandLGLCodeValidation')->name("Link P&L GL Code Validation");
    Route::post('linkPandLGLCode', 'ReportTemplateDetailsAPIController@linkPandLGLCode')->name("Link P&L GL Codes");
    Route::post('reportTemplateDetailSubCatLink', 'ReportTemplateLinksAPIController@reportTemplateDetailSubCatLink')->name("Report template detail sub category link");
    Route::post('deleteAllLinkedGLCodes', 'ReportTemplateLinksAPIController@deleteAllLinkedGLCodes')->name("Delete all linked GL codes");
    Route::post('loadColumnTemplate', 'ReportTemplateColumnLinkAPIController@loadColumnTemplate')->name("Load column templates");
    Route::post('getReportTemplateAssignedEmployee', 'ReportTemplateEmployeesAPIController@getReportTemplateAssignedEmployee')->name("Get report template assigned employees");
 	Route::post('getUnassignedGLForReportTemplate', 'ReportTemplateDetailsAPIController@getUnassignedGLForReportTemplate')->name("Get unassigned GL codes for report template");
    Route::post('reAssignAndDeleteGlLink', 'ReportTemplateLinksAPIController@reAssignAndDeleteGlLink')->name("Re Assign And Delete Gl Link");
    
    Route::get('getReportTemplateFormData', 'ReportTemplateAPIController@getReportTemplateFormData')->name("Get report template form data");
    Route::get('getReportTemplateDetail/{id}', 'ReportTemplateDetailsAPIController@getReportTemplateDetail')->name("Get report template detail by report template id");
    Route::get('getReportTemplateSubCat', 'ReportTemplateDetailsAPIController@getReportTemplateSubCat')->name("Get report template sub category");
    Route::get('getEmployees', 'ReportTemplateAPIController@getEmployees')->name("Get employees for report template");
    Route::get('getReportHeaderData', 'ReportTemplateAPIController@getReportHeaderData')->name("Get report template header data");
    Route::get('getTemplateColumnLinks', 'ReportTemplateColumnLinkAPIController@getTemplateColumnLinks')->name("Get report template column links");
    Route::get('reportTemplateFormulaColumn', 'ReportTemplateColumnLinkAPIController@reportTemplateFormulaColumn')->name("Get report template formula column");
});


Route::resource('control_accounts', 'ControlAccountAPIController');
Route::get('getChartOfAccounts', 'ChartOfAccountAPIController@getChartOfAccounts')->name("Get chart of accounts");

//cash flow template
Route::group([], function(){
    Route::resource('cash_flow_templates', 'CashFlowTemplateAPIController');
    Route::resource('cash_flow_template_details', 'CashFlowTemplateDetailAPIController');
    Route::resource('cash_flow_template_links', 'CashFlowTemplateLinkAPIController');
    
    Route::get('getCashFlowReportHeaderData', 'CashFlowTemplateAPIController@getCashFlowReportHeaderData')->name("Get cash flow report header data");
    Route::get('getCashFlowTemplateSubCat', 'CashFlowTemplateDetailAPIController@getCashFlowTemplateSubCat')->name("Get cash flow template sub category");
    Route::get('getCashFlowTemplateDetail/{id}', 'CashFlowTemplateDetailAPIController@getCashFlowTemplateDetail')->name("Get cash flow template details");
    
    Route::post('deleteAllLinkedGLCodesCashFlow', 'CashFlowTemplateLinkAPIController@deleteAllLinkedGLCodesCashFlow')->name("Delete all lined GL codes of cash flow template");
    Route::post('getAllCashFlowTemplate', 'CashFlowTemplateAPIController@getAllCashFlowTemplate')->name("Get all cash flow templates");
    Route::post('cashFlowTemplateDetailSubCatLink', 'CashFlowTemplateLinkAPIController@cashFlowTemplateDetailSubCatLink')->name("Cash flow template detail sub category link");
    Route::post('addCashFlowTemplateSubCategory', 'CashFlowTemplateDetailAPIController@addCashFlowTemplateSubCategory')->name("Add cash flow template sub category");
});

//document policy
Route::group([], function(){

Route::get('getCompanyPolicyFilterOptions', 'CompanyPolicyMasterAPIController@getCompanyPolicyFilterOptions')->name('Get company policy filter options');
Route::post('getAllCompanyPolicy', 'CompanyPolicyMasterAPIController@getAllCompanyPolicy')->name('Get all company policy');
Route::post('getAllCompanyEmailSendingPolicy', 'DocumentEmailNotificationDetailAPIController@getAllCompanyEmailSendingPolicy')->name('Get all company email sending policy');
Route::resource('company_policy_masters', 'CompanyPolicyMasterAPIController');
Route::resource('docEmailNotificationDetails', 'DocumentEmailNotificationDetailAPIController');

});

//supplier configuration
Route::group([], function(){

    Route::post('get-supplier-categories', 'SupplierCategoryConfigurationController@getSupplierCategories')->name('Get supplier categories');
    Route::post('get-supplier-groups', 'SupplierGroupConfigurationController@getSupplierGroups')->name('Get supplier groups');
    Route::resource('supplier-category-conf', 'SupplierCategoryConfigurationController');
    Route::post('delete-category', 'SupplierCategoryConfigurationController@deleteCategory')->name('Delete supplier category');
    Route::resource('supplier-group-conf', 'SupplierGroupConfigurationController');
    Route::post('delete-group', 'SupplierGroupConfigurationController@deleteGroup')->name('Delete supplier group');

});


//company settings
Route::group([], function() {
    Route::post('getCompanies', 'CompanyAPIController@getCompanies')->name('Get companies');
    Route::get('getCompanySettingFormData', 'CompanyAPIController@getCompanySettingFormData')->name('Get company setting form data');
    Route::resource('companies', 'CompanyAPIController');
    Route::post('getDigitalStamps', 'CompanyAPIController@getDigitalStamps')->name('Get digital stamps');
    Route::post('uploadDigitalStamp', 'CompanyAPIController@uploadDigitalStamp')->name('Upload digital stamp');
    Route::post('updateDefaultStamp', 'CompanyAPIController@updateDefaultStamp')->name('Update default stamp');
    Route::resource('company_digital_stamps', 'CompanyDigitalStampAPIController');
});


//document control check
Route::group([], function() {
    Route::get('getDocumentControlFilterFormData', 'DocumentControlAPIController@getDocumentControlFilterFormData')->name('Get document control filter form data');
    Route::post('generateDocumentControlReport', 'DocumentControlAPIController@generateDocumentControlReport')->name('Generate document control report');
});

//document configuration
Route::group([], function() {
    Route::get('getCompanyDocumentFilterOptions', 'CompanyDocumentAttachmentAPIController@getCompanyDocumentFilterOptions')->name('Get company document filter options');
    Route::post('getAllCompanyDocumentAttachment', 'CompanyDocumentAttachmentAPIController@getAllCompanyDocumentAttachment')->name('Get all company document attachment');
    Route::resource('company_document_attachments', 'CompanyDocumentAttachmentAPIController');
});


//widget master
Route::group([], function() {
    Route::resource('dashboard_widget_masters', 'DashboardWidgetMasterAPIController');
    Route::get('getWidgetMasterFormData', 'DashboardWidgetMasterAPIController@getWidgetMasterFormData')->name('Get widget master form data');
});

//generate work order
Route::group([], function() {
    Route::post('generateWorkOrder', 'ProcumentOrderAPIController@generateWorkOrder')->name('Generate work order');
    Route::post('workOrderLog', 'ProcumentOrderAPIController@workOrderLog')->name('Work order log');
    Route::post('getProcumentOrderByDocumentType', 'ProcumentOrderAPIController@getProcumentOrderByDocumentType')->name('Get procument order by document type');
    Route::get('getProcumentOrderFormData', 'ProcumentOrderAPIController@getProcumentOrderFormData')->name('Get procument order from data');
});

//payment term template
Route::group([], function() {
    Route::resource('payment_term_templates', 'PaymentTermTemplateAPIController');
    Route::post('getAllPaymentTerms', 'PaymentTermTemplateAPIController@getAllPaymentTerms')->name('Get all payment terms');
    Route::put('paymentTermDefaultTemplateUpdate/{id}', 'PaymentTermTemplateAPIController@paymentTermDefaultTemplateUpdate')->name('Update default payment term');
    Route::post('getAllPaymentTermConfigs', 'PaymentTermConfigAPIController@getAllPaymentTermConfigs')->name('Get all payment term configs');
    Route::put('configDescriptionUpdate/{id}', 'PaymentTermConfigAPIController@configDescriptionUpdate')->name('Update configuration description');
    Route::post('deleteConfigDescription', 'PaymentTermConfigAPIController@deleteConfigDescription')->name('Delete config description');
    Route::post('updateConfigSelection', 'PaymentTermConfigAPIController@updateConfigSelection')->name('Update config selection for print');
    Route::post('updateSortOrder', 'PaymentTermConfigAPIController@updateSortOrder')->name('Update sort order');
    Route::post('getSupplierAssignFormData', 'PaymentTermTemplateAssignedAPIController@getSupplierAssignFormData')->name('Get supplier assign form data');
    Route::post('getSupplierList', 'PaymentTermTemplateAssignedAPIController@getSupplierList')->name('Get supplier list');
    Route::resource('template_assign_suppliers', 'PaymentTermTemplateAssignedAPIController');
    Route::post('getAllAssignedSuppliers', 'PaymentTermTemplateAssignedAPIController@getAllAssignedSuppliers')->name('Get all assigned suppliers');
    Route::post('changeActiveStatus', 'PaymentTermTemplateAPIController@changeActiveStatus')->name('Change template active status');
});

//Supplier Evaluation 
Route::group([], function() {

    Route::post('getAllSupplierEvaluationTemplateComments', 'SupplierEvaluationTemplateCommentAPIController@getAllSupplierEvaluationTemplateComments')->name("Get all supplier evaluation masters");
    Route::post('updateEvaluationTemplateComment', 'SupplierEvaluationTemplateCommentAPIController@updateEvaluationTemplateComment')->name("Get all supplier evaluation masters");
    Route::post('getAllSupplierEvaluationTemplates', 'SupplierEvaluationTemplateAPIController@getAllSupplierEvaluationTemplates')->name("Get all supplier evaluation masters");
    Route::post('getAllSupplierEvaluationMasters', 'SupplierEvaluationMastersAPIController@getAllSupplierEvaluationMasters')->name("Get all supplier evaluation masters");
    Route::post('getAllSupplierEvaluationDetails', 'SupplierEvaluationMasterDetailsAPIController@getAllSupplierEvaluationDetails')->name("Get all supplier evaluation masters");
    Route::post('addMasterColumns', 'SupplierEvaluationTemplateSectionTableAPIController@addMasterColumns')->name("Create evaluation master columns");
    Route::post('confirmTable', 'SupplierEvaluationTemplateSectionTableAPIController@confirmTable')->name("Confirm section table");
    Route::get('sectionTableData', 'SupplierEvaluationTemplateSectionTableAPIController@sectionTableData')->name("Get section table data");
    Route::post('updateRow', 'TemplateSectionTableRowAPIController@updateRow')->name("Update row data");
    Route::get('getEvaluationMasters', 'SupplierEvaluationMastersAPIController@index')->name("Get evaluation masters");
    Route::post('getTemplateSectionLabel', 'EvaluationTemplateSectionLabelAPIController@getTemplateSectionLabel')->name("Get template section label");
    Route::post('getTemplateSectionFormula', 'EvaluationTemplateSectionFormulaAPIController@getTemplateSectionFormula')->name("Get template section formula");
    Route::post('getTemplateSectionFormData', 'EvaluationTemplateSectionAPIController@getTemplateSectionFormData')->name("Get template section from data");
    Route::get('getEvaluationTemplateData', 'SupplierEvaluationTemplateAPIController@getEvaluationTemplateData')->name("Get evaluation template data");


    Route::resource('supplier_evaluation_masters', 'SupplierEvaluationMastersAPIController');
    Route::resource('evaluation_master_details', 'SupplierEvaluationMasterDetailsAPIController');
    Route::resource('supplier_evaluation_templates', 'SupplierEvaluationTemplateAPIController');
    Route::resource('evaluation_template_comments', 'SupplierEvaluationTemplateCommentAPIController');
    Route::resource('template_section_tables', 'SupplierEvaluationTemplateSectionTableAPIController');
    Route::resource('template_section_table_columns', 'SupplierEvaluationTemplateSectionTableColumnAPIController');
    Route::resource('template_section_table_rows', 'TemplateSectionTableRowAPIController');
    Route::resource('evaluation_template_sections', 'EvaluationTemplateSectionAPIController');
    Route::resource('template_section_labels', 'EvaluationTemplateSectionLabelAPIController');
    Route::resource('template_section_formulas', 'EvaluationTemplateSectionFormulaAPIController');

});

// Exchange setup Configurations

Route::group([], function() {
    Route::resource('exchange_setup_document', 'ExchangeSetup\ExchangeSetupDocumentController');
    Route::resource('exchange_setup_document_type', 'ExchangeSetup\ExchangeSetupDocumentTypeController');
    Route::resource('exhange_setup_config', 'ExchangeSetup\ExchangeSetupConfigurationController');

    Route::get('exchange_setup_document/{id}/types', 'ExchangeSetup\ExchangeSetupDocumentController@getTypesOfDocument');
    Route::post('checkDocumentExchangeRateConfigAccess', 'ExchangeSetup\ExchangeSetupConfigurationController@checkDocumentExchangeRateConfigAccess');
    Route::post('updateDocumentExchangeRate', 'ExchangeSetup\ExchangeSetupDocumentController@updateDocumentExchangeRate');
    Route::post('setDefaultExchangeRate', 'ExchangeSetup\ExchangeSetupDocumentController@setDefaultExchangeRate');

});

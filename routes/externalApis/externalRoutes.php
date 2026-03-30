<?php

use Illuminate\Support\Facades\Route;

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
Route::post('fetch_item_wac_amount', 'POS\PosAPIController@fetchItemWacAmount');
Route::post('create_receipts_voucher', 'ReceiptAPIController@store');
Route::post('push_budget_items', 'SRM\ThirdPartySystemsController@pushBudgetItems');
Route::post('create_customer_invoices', 'CustomerInvoiceAPIController@createCustomerInvoiceAPI');
Route::post('credit-note', 'CreditNoteAPIController@createCreditNoteAPI');
Route::post('receipt-matching', 'ReceiptMatchingAPIController@createReceiptMatchingAPI');
Route::post('cancel_customer_invoice', 'CustomerInvoiceDirectAPIController@customerInvoiceCancelAPI');
Route::post('supplier_invoice_create', 'BookInvSuppMasterAPIController@createSupplierInvoices');
Route::post('journal-voucher', 'JvMasterAPIController@createJournalVoucher');
Route::post('payment-voucher', 'PaySupplierInvoiceMasterAPIController@createPaymentVoucherAPI');
Route::get('employees/documents/status', 'EmployeeAPIController@employeeDocumentStatus');
Route::post('create-customer-master', 'CustomerMasterAPIController@createCustomerMasterAPI');
Route::post('asset-details', 'FixedAssetMasterAPIController@getAssetDetails');
Route::post('warehouse/items', 'ItemMasterAPIController@getWarehouseItemQuantity');

Route::prefix('integrations')->group(function () {
    //external integrations
    Route::post('customer-invoices','CustomerInvoiceAPIController@createCustomerInvoiceAPI');
    Route::post('credit-notes','CreditNoteAPIController@createCreditNoteAPI');
    Route::post('receipt-matchings', 'ReceiptMatchingAPIController@createReceiptMatchingAPI');
    Route::post('customer-invoices/cancel', 'CustomerInvoiceDirectAPIController@customerInvoiceCancelAPI');
    Route::post('supplier-invoices', 'BookInvSuppMasterAPIController@createSupplierInvoices');
    Route::post('journal-vouchers', 'JvMasterAPIController@createJournalVoucher');
    Route::post('payment-vouchers', 'PaySupplierInvoiceMasterAPIController@createPaymentVoucherAPI');
    Route::get('employees/document-status', 'EmployeeAPIController@employeeDocumentStatus');
    Route::post('customers', 'CustomerMasterAPIController@createCustomerMasterAPI');
    Route::post('assets/search', 'FixedAssetMasterAPIController@getAssetDetails');
    Route::post('warehouses/items/search', 'ItemMasterAPIController@getWarehouseItemQuantity');
    Route::post('segments/search', 'SegmentMasterAPIController@pullSegment');
    Route::post('customers/search', 'CustomerMasterAPIController@pullCustomerMaster');
    Route::post('users/search', 'UserAPIController@pullUserDetails');
    Route::post('banks/search', 'BankMasterAPIController@pullBankMaster');
    Route::post('chart-of-accounts/search', 'ChartOfAccountAPIController@pullChartOfAccounts');
    Route::post('pos/shifts', 'POS\PosSyncAPIController@syncShift');
    Route::post('customer-invoices/balances','CustomerInvoiceAPIController@getApprovedCustomerInvoiceBalancesAPI');

});

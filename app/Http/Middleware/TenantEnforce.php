<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\AuditRoutesTenantService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantEnforce
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if (env('IS_MULTI_TENANCY', false)) {
            $url = $request->getHttpHost();
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];

            if ($subDomain == 'www') {
                $subDomain = $url_array[1];
            }

            if ($subDomain != 'localhost:8000') {
                if (!$subDomain) {
                    return $subDomain . "Not found";
                }
                $tenant = Tenant::where('sub_domain', 'like', $subDomain)->first();
                if (!empty($tenant)) {
                    if (in_array($request->route()->uri, $this->apiKeyRoutes())) {
                        $request->request->add(['api_key' => $tenant->api_key]);
                    }

                    if (in_array($request->route()->uri, $this->dbRoutes()) || in_array($request->route()->uri, $this->externalApiDbRoutes()) || in_array($request->route()->uri, AuditRoutesTenantService::getTenantRoutes())) {
                        $request->request->add(['db' => $tenant->database]);
                    }

                    if (in_array($request->route()->uri, AuditRoutesTenantService::getTenantRoutes()) || in_array($request->route()->uri, $this->externalApiDbRoutes())) {
                        $request->request->add(['tenant_uuid' => $tenant->uuid]);
                    }

                    if (in_array($request->route()->uri, ['api/v1/getThirdPartyApiLogDetail', 'api/v1/auditLogsExternal', 'api/v1/createAuditLog'])) {
                        $subDomainArray = explode('-', $subDomain);
                        $partCount = count($subDomainArray);
                        if ($partCount > 1) {
                            $firstPart = $subDomainArray[0];
                            $remainingParts = array_slice($subDomainArray, 1);
                            $remainingPart = implode('-', $remainingParts);
                            $erpDomain = $firstPart . '-erp-' . $remainingPart;
                        } else {
                            $erpDomain = $subDomain . '-erp';
                        }

                        $erpTenant = Tenant::where('sub_domain', 'like', $erpDomain)->first();
                        if (!empty($erpTenant)) {
                            $request->request->add(['tenant_uuid' => $erpTenant->uuid]);
                        }
                    }

                    $loginData = DB::table('tenant_login')->where('tenantID', $tenant->id)->first();

                    if ($loginData && $loginData->loginType == 4) {
                        $loginConfig = json_decode($loginData->config, true);

                        if (isset($loginConfig['realm-public-key'])) {
                            Config::set("keycloak.realm_public_key", $loginConfig['realm-public-key']);
                        }
                    }

                    Config::set("database.connections.mysql.database", $tenant->database);
                    //DB::purge('mysql');
                    DB::reconnect('mysql');
                } else {
                    return "Sub domain " . $subDomain . " not found";
                }
            }
        } else {
            if (in_array($request->route()->uri, $this->apiKeyRoutes())) {
                $request->request->add(['api_key' => "fow0lrRWCKxVIB4fW3lR"]);
            }

            if (in_array($request->route()->uri, $this->dbRoutes()) || in_array($request->route()->uri, $this->externalApiDbRoutes())) {
                $request->request->add(['db' => ""]);
            }
        }

        return $next($request);
    }

    private function dbRoutes()
    {
        return [
            'api/v1/purchase-request-add-all-items',
            'api/v1/poItemsUpload',
            'api/v1/createPrMaterialRequest',
            'api/v1/uploadItemsDeliveryOrder',
            'api/v1/approveCalanderDelAppointment',
            'api/v1/approveJournalVoucher',
            'api/v1/updateGLEntries',
            'api/v1/suppliers/registration/approvals/status',
            'api/v1/approveDocument',
            'api/v1/postGLEntries',
            'api/v1/posMappingRequest',
            'api/v1/generateGeneralLedgerReportPDF',
            'api/v1/uploadItems',
            'api/v1/assignUserGroupNavigation',
            'api/v1/approvePurchaseRequest',
            'api/v1/post_receipt_voucher',
            'api/v1/post_customer_invoice',
            'api/v1/updateSentSupplierDetail',
            'api/v1/sentCustomerStatement',
            'api/v1/sentSupplierStatement',
            'api/v1/bank_ledgers/{bank_ledger}',
            'api/v1/updateSentCustomerDetail',
            'api/v1/post_supplier_invoice',
            'api/v1/fixed_asset_depreciation_masters',
            'api/v1/purchaseOrderDetailsAddAllItems',
            'api/v1/generateBankLedgerReportPDF',
            'api/v1/postPosEntries',
            'api/v1/requestDetailsAddAllItems',
            'api/v1/materialIssuetDetailsAddAllItems',
            'api/v1/store-employee-language',
            'api/v1/postEmployeeFromPortal',
            'api/v1/uploadBudgets',
            'api/v1/uploadCustomerInvoice',
            'api/v1/assetCostingUpload',
            'api/v1/create_receipts_voucher',
            'api/v1/generateARCAReportPDF',
            'api/v1/approveRecurringVoucher',
            'api/v1/create_customer_invoices',
            'api/v1/generateAPReportBulkPDF',
            'api/v1/generateDocumentAgainstVRF',
            'api/v1/approveDocumentBulk',
            'api/v1/stock_counts',
            'api/v1/mrItemsUpload',
            'api/v1/miItemsUpload',
            'api/v1/approveDocumentBulk',
            'api/v1/supplier_invoice_create',
            'api/v1/journal-voucher',
            'api/v1/sentCustomerLedger',
            'api/v1/uploadBankStatement',
            'api/v1/payment-voucher',
            'api/v1/credit-note',
            'api/v1/receipt-matching',
            'api/v1/chequeRegisterDetailSwitch',
            'api/v1/chequeRegisterDetailCancellation',
            'api/v1/supplierInvoiceDetailsAddAllItems',
            'api/v1/exportProcumentOrderMaster',
            'api/v1/quotation/add-multiple-items',
            'api/v1/exportReportOpenRequest',
            'api/v1/exportTransactionsRecord',
            'api/v1/validateWorkbookCreation',
            'api/v1/rematchWorkBook',
            'api/v1/generateBankReconciliation',
            'api/v1/updateBudgetPlanningStatus',
            'api/v1/departmentBudgetTemplates/assign-gl',
            'api/v1/postNotPostedSchedule',
            'api/v1/generateAssetDepBulkPDF',
            'api/v1/updateRouteAccess',
            'api/v1/exportCompanyBudgetPlanningDetailsAll',
        ];
    }

    /**
     * External API routes (routes/externalApis/externalRoutes.php) that need tenant db on request.
     *
     * @return array<string>
     */
    private function externalApiDbRoutes()
    {
        return [
            'api/v1/pull_tax_details',
            'api/v1/pull_bank_accounts',
            'api/v1/post_customer_category',
            'api/v1/post_receipt_voucher',
            'api/v1/post_customer_invoice',
            'api/v1/post_customer_master',
            'api/v1/pull_customer_category',
            'api/v1/pull_location',
            'api/v1/pull_segment',
            'api/v1/pull_chart_of_account',
            'api/v1/pull_chart_of_account_master',
            'api/v1/pull_unit_of_measure',
            'api/v1/pull_unit_conversion',
            'api/v1/pull_warehouse',
            'api/v1/pull_warehouse_item',
            'api/v1/srp_erp_warehousebinlocation',
            'api/v1/pull_item',
            'api/v1/pull_item_bin_location',
            'api/v1/pull_item_sub_category',
            'api/v1/pull_items_by_sub_category',
            'api/v1/pull_user',
            'api/v1/pull_item_category',
            'api/v1/posMappingRequest',
            'api/v1/pull_supplier_master',
            'api/v1/pull_customer_master',
            'api/v1/fetch_item_wac_amount',
            'api/v1/create_receipts_voucher',
            'api/v1/push_budget_items',
            'api/v1/create_customer_invoices',
            'api/v1/credit-note',
            'api/v1/receipt-matching',
            'api/v1/cancel_customer_invoice',
            'api/v1/supplier_invoice_create',
            'api/v1/journal-voucher',
            'api/v1/payment-voucher',
            'api/v1/employees/documents/status',
            'api/v1/create-customer-master',
            'api/v1/asset-details',
            'api/v1/warehouse/items',
            'api/v1/integrations/customer-invoices',
            'api/v1/integrations/credit-notes',
            'api/v1/integrations/receipt-matchings',
            'api/v1/integrations/customer-invoices/cancel',
            'api/v1/integrations/supplier-invoices',
            'api/v1/integrations/journal-vouchers',
            'api/v1/integrations/payment-vouchers',
            'api/v1/integrations/employees/document-status',
            'api/v1/integrations/customers',
            'api/v1/integrations/assets/search',
            'api/v1/integrations/warehouses/items/search',
        ];
    }

    private function apiKeyRoutes()
    {
        return [
            'api/v1/srmRegistrationLink',
            'api/v1/srm/fetch',
            'api/v1/suppliers/registration/approvals/status',
            'api/v1/sendSupplierInvitation',
            'api/v1/reSendInvitaitonLink',
            'api/v1/getMaterielIssueFormData',
            'api/v1/item_issue_masters/{item_issue_master}',
            'api/v1/item_return_details/{item_return_detail}',
            'api/v1/checkManWareHouse',
            'api/v1/approveDocument',
            'api/v1/rejectPurchaseRequest',
            'api/v1/reSendSupplierRegistrationsLink',
            'api/v1/saveSupplierPublicLink',
            'api/v1/requestKycSubmit',
        ];
    }
}

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
        $apiKeyRoutes = [
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

        $dbRoutes = [
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
            'api/v1/chequeRegisterDetailSwitch',
            'api/v1/chequeRegisterDetailCancellation',
            'api/v1/supplierInvoiceDetailsAddAllItems'
        ];

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
                    if (in_array($request->route()->uri, $apiKeyRoutes)) {
                        $request->request->add(['api_key' => $tenant->api_key]);
                    }

                    if (in_array($request->route()->uri, $dbRoutes) || in_array($request->route()->uri, AuditRoutesTenantService::getTenantRoutes())) {
                        $request->request->add(['db' => $tenant->database]);
                    }

                    if (in_array($request->route()->uri, AuditRoutesTenantService::getTenantRoutes())) {
                        $request->request->add(['tenant_uuid' => $tenant->uuid]);
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
            if (in_array($request->route()->uri, $apiKeyRoutes)) {
                $request->request->add(['api_key' => "fow0lrRWCKxVIB4fW3lR"]);
            }

            if (in_array($request->route()->uri, $dbRoutes)) {
                $request->request->add(['db' => ""]);
            }
        }

        return $next($request);
    }
}

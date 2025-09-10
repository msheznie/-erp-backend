<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfTokenForApi
{

    public function handle(Request $request, Closure $next): Response
    {
        $csrfEnabled = env('CSRF_ENABLED', false);
        $normalizedJson = '';
        
        if ($csrfEnabled) {
            if (!in_array($request->method(), ['GET', 'POST', 'PUT', 'DELETE'])) {
                return $next($request);
            }
            
            $routePrefix = $request->route()->uri;
            // Check if request is from portal and route should be ignored
            if ($request->header('From-Portal') && $request->header('From-Portal') == 1 && in_array($routePrefix, $this->portalIgnoreRoutes())) {
                return $next($request);
            }
            
            if (in_array($routePrefix, $this->ignoreRoutes())) {
                return $next($request);
            }

            $signature = $request->header('X-CSRF-TOKEN');
            if (!$signature) {
                return $this->sendError();
            }
            
            $parts = explode('|', $signature);

            if (count($parts) !== 2) {
                return $this->sendError();
            }
            
            [$csrfToken, $timestamp] = $parts;
            
            $timeExpiry = env('CSRF_TOKEN_EXPIRY_TIME', 5);
            
            if (!$timestamp || abs(time() - (int)($timestamp)) > $timeExpiry) {
                return $this->sendError();
            }
            
            //body data
            $data = json_decode($request->getContent(), true) ?: '{}';
            $normalizedJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            //params data
            $params = $request->query() ?: '{}';
            $normalizedParams = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            //operation data
            $operation = strtolower($request->method());
            
            $requestString = "{$normalizedJson}|{$normalizedParams}|{$operation}";
            $encodedRequest = ($data == "{}") ? base64_encode($data) : base64_encode($normalizedJson);
            // return response()->json(['success' => false, 'message' => $data], 403);
            
            $dataWithTimestamp = "{$encodedRequest}|{$timestamp}";
            $expectedToken = hash_hmac('sha256', $dataWithTimestamp, env('CSRF_SECRET_KEY'));

            if (!hash_equals($expectedToken, $csrfToken)) {
                return $this->sendError();
            }
        }
        return $next($request);
    }

    private function sendError(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    private function ignoreRoutes(): array
    {
        return [
            'api/v1/getConfigurationInfo',
        ];
    }

    private function portalIgnoreRoutes(): array
    {
        return [
            // ERP Service routes from Portal
            'api/v1/getAllDocumentApproval',
            'api/v1/getAssignedItemsForCompany',
            'api/v1/allItemFinanceCategories',
            'api/v1/allItemFinanceSubCategoriesByMainCategory',
            'api/v1/purchase-request-validate-item',
            'api/v1/purchase-request-add-all-items',
            'api/v1/downloadPrItemUploadTemplate',
            'api/v1/prItemsUpload',
            'api/v1/getExampleTableData',
            'api/v1/printPurchaseRequest',
            'api/v1/purchaseRequestsPOHistory',
            'api/v1/purchaseRequestAudit',
            'api/v1/copy_pr/{id}',
            'api/v1/item-specification-portal/{id}',
            'api/v1/getItemMasterPurchaseHistory',
            'api/v1/getQtyOrderDetails',
            'api/v1/updateQtyOnOrder',
            'api/v1/getWarehouseStockDetails',
            'api/v1/getSegmentAllocatedFormData',
            'api/v1/getSegmentAllocatedItems',
            'api/v1/allocateSegmentWiseItem',
            'api/v1/purchase_requests',
            'api/v1/purchase_requests/{id}',
            'api/v1/purchase_request_data',
            'api/v1/get-all-uom-options',
            'api/v1/getItemsOptionForPurchaseRequest',
            'api/v1/getItemsByPurchaseRequest',
            'api/v1/getPurchaseRequestTotal',
            'api/v1/currency_masters',
            'api/v1/getPurchaseRequestByDocumentType',
            'api/v1/isGettingCodeConfigured',
            'api/v1/purchase_request_details_update/{id}',
            'api/v1/update_segment_allocated_items/{id}',
            'api/v1/purchase_requests/pull/items/',
            'api/v1/purchase_request_details',
            'api/v1/purchase_request_details_delete/{id}',
            'api/v1/department_budget_plannings/{id}',
            'api/v1/delete_segment_allocated_items/{id}',
            'api/v1/purchase-request/remove-all-items/{id}',
            'api/v1/get-item-qnty-by-pr',
            'api/v1/getPurchaseRequestReopen',
            'api/v1/getPurchaseRequestReferBack',
            'api/v1/getPrMasterAmendHistory',
            'api/v1/get_purchase_request_referreds',
            'api/v1/getPrItemsForAmendHistory',
            'api/v1/getBudgetConsumptionByDocument',
            'api/v1/getTimeExtensionRequests',
            'api/v1/getReversions',
            'api/v1/getAllApprovalDocuments',
            'api/v1/postEmployeeFromPortal',
            'api/v1/getAllcompaniesByDepartment',
            'api/v1/approvePurchaseRequest',
            'api/v1/rejectPurchaseRequest',
            'api/v1/approvalPreCheckAllDoc',
            'api/v1/exportTransactionsRecord',
            'api/v1/getPurchaseRequestFormData',
            'api/v1/getCompanySettingFormData',
            'api/v1/getCompanies',
            'api/v1/attendance-clock-in',
            'api/v1/return-to-work-notification',
            'api/v1/getChartOfAccount/{autoID}',
            'api/v1/getBudgetPlanningMasterData',
            'api/v1/exportBudgetPlanning',
            'api/v1/getBudgetPlanningFormData',
            'api/v1/validateBudgetPlanning',
            'api/v1/company_budget_plannings',
            'api/v1/department_budget_plannings',
            'api/v1/getDepartmentBudgetPlanningDetails',
            'api/v1/getBudgetDelegateFormData',
            'api/v1/getAllDeptBudgetPlDetColumns',
            'api/v1/verifyBudgetTemplateConfiguration',
        ];
    }
}

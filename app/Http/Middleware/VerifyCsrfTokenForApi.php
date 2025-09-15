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

            // Get just the API path with query parameters (sorted for consistency)
            // Parse raw query string to preserve empty parameters
            $rawQueryString = $request->getQueryString();
            $queryParams = [];
            
            if ($rawQueryString) {
                $pairs = explode('&', $rawQueryString);
                foreach ($pairs as $pair) {
                    if (strpos($pair, '=') !== false) {
                        list($key, $value) = explode('=', $pair, 2);
                        $queryParams[urldecode($key)] = urldecode($value);
                    } else {
                        // Handle parameters without values
                        $queryParams[urldecode($pair)] = '';
                    }
                }
            }
            
            ksort($queryParams); // Sort query parameters alphabetically by key
            
            // Manually build query string without re-encoding to match frontend
            // Filter out empty parameters to match frontend behavior
            $queryParts = [];
            foreach ($queryParams as $key => $value) {
                // Only include non-empty parameters
                if ($value !== '' && $value !== null && $value !== 'null') {
                    $queryParts[] = $key . '=' . $value;
                }
            }
            $queryString = implode('&', $queryParts);
            
            $apiPath = $request->path() . ($queryString ? '?' . $queryString : '');
            

            $signature = $request->header('X-CSRF-TOKEN');
            if (!$signature) {
                return $this->sendError();
            }
            
            $parts = explode('|', $signature);

            if (count($parts) !== 2) {
                return $this->sendError();
            }
            
            [$csrfToken, $timestamp] = $parts;
            
            // Check if request contains files and adjust expiry time accordingly
            $hasFiles = $request->hasFile('file') || 
                       (is_array($request->allFiles()) && count($request->allFiles()) > 0) ||
                       $request->hasFile('files') ||
                       $request->hasFile('upload');
            
            $baseExpiry = env('CSRF_TOKEN_EXPIRY_TIME', 5);
            $timeExpiry = $hasFiles ? $baseExpiry * 3 : $baseExpiry; // 3x longer for file uploads
            
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
            $bodyString = ($data == "{}") ? $data : $normalizedJson;
            
            $requestString = "{$bodyString}|{$apiPath}|{$operation}";

            // \Log::info($requestString);

            $encodedRequest = base64_encode($requestString);
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
            'api/v1/department_budget_plannings/{department_budget_planning}',
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
            'api/v1/verifyBudgetTemplateConfiguration/{budgetTemplateId}',
            'api/v1/getBudgetPlanningUserPermissions',
            'api/v1/getDepBudgetPlDetEmpColumns',
            'api/v1/updateDepartmentBudgetPlanningDetailAmount',
            'api/v1/getAllDepartmentSegments',
            'api/v1/getTemplateDetailFormData',
            'api/v1/budget_template_comments_by_detail/{budgetDetailId}',
            'api/v1/auditLogs',
            'api/v1/getBudgetTemplateColumns/{templateId}',
            'api/v1/getDelegateAccessRecords',
            'api/v1/generateTimeExtensionRequestCode',
            'api/v1/getTimeExtensionRequestAttachments/{timeRequestId}',
            'api/v1/cancelDepartmentTimeExtensionRequests',
            'api/v1/updateBudgetPlanningStatus',
            'api/v1/getBudgetDetailTemplateEntries',
            'api/v1/saveBudgetDetailTemplateEntries',
            'api/v1/deleteBudgetPlanningTemplateDetailRow',
            'api/v1/budget_pl_temp_attachments/{budget_pl_temp_attachment}',
            'api/v1/budget_pl_temp_attachments',
            'api/v1/budget_template_comments',
            'api/v1/deleteBudgetTemplateComment',
            'api/v1/createOrUpdateDelegateAccess',
            'api/v1/updateDelegateStatus',
            'api/v1/deleteDelegateAccess',
        ];
    }
}

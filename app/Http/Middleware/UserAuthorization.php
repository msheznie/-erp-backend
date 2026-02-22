<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\EmployeeNavigation;
use App\Models\RoleRoute;
use App\Models\NavigationRoute;
use App\helper\Helper;

class UserAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!env('ENABLE_AUTHORIZATION', false)) {
            return $next($request);
        }

        $routeName = $request->route()->getName();

        $exceptionRoutes = $this->getExceptionRoutes();

        if (in_array($request->route()->uri, $exceptionRoutes)) {
            return $next($request);
        }

        if ($request->header('From-Portal') && $request->header('From-Portal') == 1 && in_array($request->route()->uri, $this->portalIgnoreRoutes())) {
            return $next($request);
        }

        $checkRouteName = NavigationRoute::where('routeName', $routeName)->first();

        // if (!$checkRouteName) {
        //     return $next($request);
        // }

        $employeeSystemID = Helper::getEmployeeSystemID();

        $userGroups = EmployeeNavigation::where('employeeSystemID', $employeeSystemID)
                                        ->get();

        $userGroupIDs = (count($userGroups) > 0) ? collect($userGroups)->pluck('userGroupID')->toArray() : [];

        $checkRoleRoute = RoleRoute::whereIn('userGroupID', $userGroupIDs)
                                    ->where('routeName', $routeName)
                                    ->first();

        if ($checkRoleRoute) {
            return $next($request);
        } else {
            // $navigationID = $request->header('X-nav-ID') ?? 0;
            // $accessType = $request->header('X-Access-Type') ?? 'None';

            if ($routeName != 'api.' && $navigationID > 0 && self::getActionType($accessType) > 0) {

                NavigationRoute::firstOrCreate(
                    [
                        'navigationID' => $navigationID,
                        'routeName' => $routeName,
                        'action' => self::getActionType($accessType),
                    ]
                );

                foreach ($userGroupIDs as $userGroupID) {
                    RoleRoute::create([
                        'routeName' => $routeName,
                        'userGroupID' => $userGroupID,
                        'companySystemID' => 0
                    ]);
                }
                

                $checkRoleRouteAfterCreate = RoleRoute::whereIn('userGroupID', $userGroupIDs)
                                    ->where('routeName', $routeName)
                                    ->first();

                if ($checkRoleRouteAfterCreate) {
                    return $next($request);
                } else {
                    return errorMsgs("Unauthorized Access");
                }
            }

            // \Log::channel('authorization')->info(json_encode([
            //     'navigationID' => $navigationID,
            //     'routeName' => $routeName,
            //     'routeURI' => $request->route()->uri,
            //     'accessType' => $accessType
            // ]));
            return errorMsgs("Unauthorized Access");
        }
    }

    private function getExceptionRoutes()
    {
        return [
            'api/v1/getCurrentUserInfo',
            'api/v1/checkUserGroupAccessRights',
            'api/v1/getUserMenu',
            'api/v1/user/companies',
            'api/v1/getNotifications',
            'api/v1/erp_language_master',
            'api/v1/user/menu',
            'api/v1/getDashboardDepartment',
            'api/v1/getDashboardWidget',
            'api/v1/getAllDocumentApproval',
            'api/v1/getCustomWidgetGraphData',
            'api/v1/getAllApprovalDocuments',
            'api/v1/getAllcompaniesByDepartment',
            'api/v1/getAllNotifications',
            'api/v1/logoutApiUser',
            'api/v1/updateNotification',
            'api/v1/getThirdPartyApiLogDetail',
            'api/v1/getCurrentHomeUrl',
        ];
    }

    private function portalIgnoreRoutes()
    {
        return [
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
            'api/v1/getBudgetPlanningFilterOptions',
            'api/v1/exportBudgetPlanningDetails',
            'api/v1/exportCompanyBudgetPlanningDetailsAll',
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
            'api/v1/auditLogsExternal',
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
            'api/v1/createTimeExtensionRequest',
            'api/v1/downloadTimeExtensionAttachment',
            'api/v1/getOptionsForSelectedUnit',
            'api/v1/saveDepBudgetPlEmpColumns',
            'api/v1/updateBudgetPlanningDelegateWorkStatus',
            'api/v1/deleteTimeExtensionRequest',
            'api/v1/acceptTimeExtensionRequest',
            'api/v1/department-budget-detail-comments/budget-detail/{budgetDetailId}',
            'api/v1/department-budget-detail-comments/save',
            'api/v1/department-budget-detail-comments/update/{id}',
            'api/v1/department-budget-detail-comments/delete/{id}',
            'api/v1/department-budget-detail-comments/count/{budgetDetailId}',
            'api/v1/department-budget-detail-comments/bulk',
            'api/v1/department-budget-detail-comments/paginated',
            'api/v1/department-budget-detail-comments/recent',
            'api/v1/department-budget-detail-comments/resolve',
            'api/v1/department-budget-detail-comments',
            'api/v1/company_budget_plannings/{company_budget_planning}',
            'api/v1/company_budget_plannings/{id}',
            'api/v1/getRevisionsByCompanyBudget',
            'api/v1/getTimeExtensionRequestsByCompanyBudget',
            'api/v1/getDepartmentBudgetPlanningStatusesByCompany',
            'api/v1/updateFinanceTeamStatus',
            'api/v1/getChartofAccountsByBudget',
            'api/v1/sendBackForRevision',
            'api/v1/getRevisionGL',
            'api/v1/workflow_configurations/{workflow_configuration}',
            'api/v1/download-revision-attachment',
            'api/v1/view-revision-attachment',
            'api/v1/getAllDepartmentEmployees',
            'api/v1/department-budget-detail-comments/delete',
            'api/v1/printAssetDepreciation',
            'api/v1/getDocumentTracingData',
            'api/v1/createAuditLog',
            'api/v1/requestBudgetPlanningReopen',
            'api/v1/returnBudgetPlanningPreCheck',
            'api/v1/returnBudgetPlanningToAmend',
            'api/v1/createAuditLog',
            'api/v1/getThirdPartyApiLogDetail',
            'api/v1/updateRouteAccess'
        ];
    }

    private function getActionType($accessType)
    {
        return match($accessType) {
            'None' => 0,
            'Read' => 1,
            'Create' => 2,
            'Edit' => 3,
        };
    }
}


function errorMsgs($messsage){
    return response()->json([
        'success' => false,
        'message' => $messsage
    ], 403);
}

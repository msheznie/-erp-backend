<?php

namespace App\Services;

class AuditRoutesTenantService
{
    public static function getTenantRoutes(){
        $lokiTenantRoutes = [
            'api/v1/auditLogs',
            'api/v1/financeItemCategorySubsExpiryUpdate',
            'api/v1/finance_item_category_subs',
            'api/v1/finance_item_category_subs_update',
            'api/v1/financeItemCategorySubsAttributesUpdate'
        ];

        return $lokiTenantRoutes;
    }

}

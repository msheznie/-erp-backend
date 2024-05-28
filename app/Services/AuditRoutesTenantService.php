<?php

namespace App\Services;

class AuditRoutesTenantService
{
    public static function getTenantRoutes(){
        $lokiTenantRoutes = [
            'api/v1/auditLogs',
            'api/v1/addItemAttributes',
            'api/v1/erp_attributes/{erp_attribute}',
            'api/v1/financeItemCategorySubsExpiryUpdate',
            'api/v1/finance_item_category_subs/{finance_item_category_sub}',
            'api/v1/finance_item_category_subs_update',
            'api/v1/itemcategory_sub_assigneds',
            'api/v1/financeItemCategorySubsAttributesUpdate',
            'api/v1/itemAttributesIsMandotaryUpdate',
            'api/v1/customer_masters',
            'api/v1/supplier/masters/update',
            'api/v1/chart_of_account',
            'api/v1/updateItemMaster',
            'api/v1/asset_finance_categories/{asset_finance_category}',
            'api/v1/gl-config-scenario-details/{gl_config_scenario_detail}',
            'api/v1/asset_disposal_types/{asset_disposal_type}',
            'api/v1/fixed_asset_masters/{fixed_asset_master}'
        ];

        return $lokiTenantRoutes;
    }

}

<?php
namespace App\Services\hrms\modules;

use App\Models\HrModuleAssign;

class HrModuleAssignService{
    public static function checkModuleAvailability($companyId, $moduleId){

        $result = HrModuleAssign::where('company_id', $companyId)
            ->where('module_id', $moduleId)
            ->value('module_id');
        return empty($result) ? 0 : 1;
    }

}
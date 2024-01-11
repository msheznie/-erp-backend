<?php
namespace App\Services\hrms\modules;

use App\Models\HrModuleAssign;

class HrModuleAssignService{
    public $moduleId;
    public $companyId;

    public function __construct($moduleId, $companyId)
    {
        $this->moduleId = $moduleId;
        $this->companyId = $companyId;
    }

    public function __destruct()
    {
        $result = HrModuleAssign::where('company_id', $this->companyId)
            ->where('module_id', $this->moduleId)
            ->value('module_id');
        return empty($result) ? 0 : 1;
    }

}
<?php

namespace App\helper;

use App\Models\DocumentRestrictionAssign;
use App\Models\EmployeeNavigation;
use App\Models\ExchangeSetupConfiguration;
use Auth;

class ExchangeSetupConfig
{

    public function checkPolicy($companyID)
    {
        $user = Auth::user();

        if(!isset($user))
            return ['sucess' => false, 'message' => "User details not found!", 'policy' => false];

        if(!isset($user->employee))
            return ['sucess' => false, 'message' => "Employee details not found!", 'policy' => false];

        $employeeNavigation = EmployeeNavigation::where('employeeSystemID',$user->employee->employeeSystemID)->where('companyID',$companyID)->first();

        if(!isset($employeeNavigation))
            return ['sucess' => false, 'message' => "Employee user group data not found!", 'policy' => false];

        $documentRestrictionPolicy = DocumentRestrictionAssign::where('documentRestrictionPolicyID',14)->where('userGroupID',$employeeNavigation->userGroupID)->where('companySystemID',$companyID)->first();

        if(!isset($documentRestrictionPolicy))
            return ['sucess' => false, 'message' => "User group dosen't have access to the document restriction policy!", 'policy' => false];

        return ['sucess' => true, 'message' => "Access available for the user group", 'policy' => true];
    }

    public function checkExchageSetupDocumentAllowERAccess($companySystemId, $exchangeSetupDocumentTypeId)
    {
        $exchangeSetupDocumentConfig = ExchangeSetupConfiguration::where('companyId',$companySystemId)->where('exchangeSetupDocumentTypeId',$exchangeSetupDocumentTypeId)->first();
        if(!isset($exchangeSetupDocumentConfig))
            return false;

        return $exchangeSetupDocumentConfig->allowErChanges ?? false;
    }

}

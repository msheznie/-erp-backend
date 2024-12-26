<?php

namespace App\Services\AuditLog;

use App\Models\ChartOfAccount;

class ChartOfAccountConfigAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "U"){

            if(isset($auditData['previosValue']['chartOfAccountSystemID']) && isset($auditData['newValue']['chartOfAccountSystemID'])){
                $newChartOfAccount = $auditData['newValue']['chartOfAccountSystemID'];
                $oldChartOfAccount = $auditData['previosValue']['chartOfAccountSystemID'];
            }
            else{
                $newChartOfAccount = isset($auditData['newValue']['chartOfAccountID']) ? $auditData['newValue']['chartOfAccountID'] : null;
                $oldChartOfAccount = isset($auditData['previosValue']['chartOfAccountID']) ? $auditData['previosValue']['chartOfAccountID']: null;
            }

            if($oldChartOfAccount != $newChartOfAccount) {
                $oldData = ChartOfAccount::where('chartOfAccountSystemID',$oldChartOfAccount)->first();
                $newData = ChartOfAccount::where('chartOfAccountSystemID',$newChartOfAccount)->first();
                $modifiedData[] = ['amended_field' => "account_mapping", 'previous_value' => $oldData ? $oldData->AccountDescription : '-', 'new_value' => $newData ? $newData->AccountDescription : '-'];
            }

        }

        return $modifiedData;
    }
}

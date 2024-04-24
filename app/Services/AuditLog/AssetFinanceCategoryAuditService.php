<?php

namespace App\Services\AuditLog;

use App\helper\Helper;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\YesNoSelection;
use Illuminate\Support\Arr;
class AssetFinanceCategoryAuditService
{

    public static function process($auditData)
    {



        $modifiedData = [];
        if ($auditData['crudType'] == "U"){

                if($auditData['previosValue']['financeCatDescription'] != $auditData['newValue']['financeCatDescription']) { 
                    $modifiedData[] = ['amended_field' => "category_description", 'previous_value' => ($auditData['previosValue']['financeCatDescription']) ? $auditData['previosValue']['financeCatDescription']: '', 'new_value' => ($auditData['newValue']['financeCatDescription']) ? $auditData['newValue']['financeCatDescription'] : ''];
                }

                if($auditData['previosValue']['COSTGLCODESystemID'] != $auditData['newValue']['COSTGLCODESystemID']) { 
                    $newCostAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['newValue']['COSTGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $previousCostAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['previosValue']['COSTGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $modifiedData[] = ['amended_field' => "cost_account", 'previous_value' => ($previousCostAccount) ? $previousCostAccount->label: '', 'new_value' => ($newCostAccount) ? $newCostAccount->label: ''];
                }

                if($auditData['previosValue']['DEPGLCODESystemID'] != $auditData['newValue']['DEPGLCODESystemID']) { 
                    $newDepreciationAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['newValue']['DEPGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $previousDepreciationAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['previosValue']['DEPGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $modifiedData[] = ['amended_field' => "depreciation_account", 'previous_value' => ($previousDepreciationAccount) ? $previousDepreciationAccount->label: '', 'new_value' => ($newDepreciationAccount) ? $newDepreciationAccount->label: ''];
                }

                if($auditData['previosValue']['lifeTimeInYears'] != $auditData['newValue']['lifeTimeInYears']) { 
                    $modifiedData[] = ['amended_field' => "life_time_in_years", 'previous_value' => ($auditData['previosValue']['lifeTimeInYears']) ? $auditData['previosValue']['lifeTimeInYears']: '', 'new_value' => ($auditData['newValue']['lifeTimeInYears']) ? $auditData['newValue']['lifeTimeInYears'] : ''];
                }

                if($auditData['previosValue']['ACCDEPGLCODESystemID'] != $auditData['newValue']['ACCDEPGLCODESystemID']) { 
                    $newAccDepreciationAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['newValue']['ACCDEPGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $previousAccDepreciationAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['previosValue']['ACCDEPGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $modifiedData[] = ['amended_field' => "acc_depreciation_account", 'previous_value' => ($previousAccDepreciationAccount) ? $previousAccDepreciationAccount->label: '', 'new_value' => ($newAccDepreciationAccount) ? $newAccDepreciationAccount->label: ''];
                }

                if($auditData['previosValue']['DISPOGLCODESystemID'] != $auditData['newValue']['DISPOGLCODESystemID']) { 
                    $newDisposalAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['newValue']['DISPOGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $previousDisposalAccount = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $auditData['previosValue']['DISPOGLCODESystemID'])->selectRaw('CONCAT(AccountCode, " | " ,AccountDescription) as label')->first();
                    $modifiedData[] = ['amended_field' => "disposal_account", 'previous_value' => ($previousDisposalAccount) ? $previousDisposalAccount->label: '', 'new_value' => ($newDisposalAccount) ? $newDisposalAccount->label: ''];
                }

                if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) { 
                    $newIsActive = YesNoSelection::where('idyesNoselection', $auditData['newValue']['isActive'])->selectRaw('YesNo as label')->first();
                    $previousIsActive = YesNoSelection::where('idyesNoselection', $auditData['previosValue']['isActive'])->selectRaw('YesNo as label')->first();
                    $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($previousIsActive) ? $previousIsActive->label: '', 'new_value' => ($newIsActive) ? $newIsActive->label: ''];
                }

                if($auditData['previosValue']['enableEditing'] != $auditData['newValue']['enableEditing']) {
                    $modifiedData[] = ['amended_field' => "enable_editing", 'previous_value' => ($auditData['previosValue']['enableEditing'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['enableEditing'] == 1) ? 'yes' : 'no'];
                }

        }

        return $modifiedData;
    }

}

<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\CompanyDepartmentSegment;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Models\SegmentMaster;
use App\Models\DepBudgetTemplateGl;

class ChartOfAccountService
{
    /**
     * Get chart of accounts by budget planning ID
     *
     * @param int $budgetPlanningId
     * @return array
     */
    public function getChartOfAccountsByBudgetPlanning($budgetPlanningId)
    {
        $budgetPlanning = DepartmentBudgetPlanning::with('budgetPlanningDetails.budgetTemplateGl', 'workflow')->find($budgetPlanningId);
        
        if (!$budgetPlanning) {
            return [];
        }

        $chartOfAccountSystemIDs = [];
        
        if ($budgetPlanning->workflow && $budgetPlanning->workflow->method == 1) {
            if ($budgetPlanning && $budgetPlanning->budgetPlanningDetails) {
                foreach ($budgetPlanning->budgetPlanningDetails as $budgetPlanningDetail) {
                    if (!empty($budgetPlanningDetail->budgetTemplateGl)) {
                        if ($budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID']) {
                            $chartOfAccounts = ChartOfAccount::where('chartOfAccountSystemID', $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID'])
                                ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription')
                                ->first();
                            
                            $companySegment = CompanyDepartmentSegment::find($budgetPlanningDetail->department_segment_id);
                            
                            if ($chartOfAccounts) {
                                array_push($chartOfAccountSystemIDs, [
                                    'chartOfAccountSystemID' => $budgetPlanningDetail->id,
                                    'AccountDescription' => $chartOfAccounts->AccountCode . ' - ' . $chartOfAccounts->AccountDescription,
                                    'AccountCode' => SegmentMaster::find($companySegment->serviceLineSystemID)->ServiceLineDes
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            if ($budgetPlanning && $budgetPlanning->budgetPlanningDetails) {
                foreach ($budgetPlanning->budgetPlanningDetails as $budgetPlanningDetail) {
                    if (!empty($budgetPlanningDetail->budgetTemplateGl)) {
                        if ($budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID']) {
                            $chartOfAccounts = ChartOfAccount::where('chartOfAccountSystemID', $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID'])
                                ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription')
                                ->first();
                            
                            if ($chartOfAccounts) {
                                array_push($chartOfAccountSystemIDs, [
                                    'chartOfAccountSystemID' => $budgetPlanningDetail->budgetTemplateGl['depBudgetTemplateGlID'],
                                    'AccountDescription' => $chartOfAccounts->AccountDescription,
                                    'AccountCode' => $chartOfAccounts->AccountCode
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        return $chartOfAccountSystemIDs;
    }

    /**
     * Get chart of accounts by revision GL sections
     *
     * @param array $selectedGlSections Array of GL section IDs from revision
     * @param int $budgetPlanningId Budget planning ID to get workflow method
     * @return array
     */
    public function getChartOfAccountsByRevisionGlSections($selectedGlSections, $budgetPlanningId)
    {
        if (empty($selectedGlSections) || !is_array($selectedGlSections)) {
            return [];
        }

        $budgetPlanning = DepartmentBudgetPlanning::with('workflow')->find($budgetPlanningId);
        
        if (!$budgetPlanning) {
            return [];
        }

        $chartOfAccountSystemIDs = [];
        
        if ($budgetPlanning->workflow && $budgetPlanning->workflow->method == 1) {
            // Method 1: Include segment information
            foreach ($selectedGlSections as $glSectionId) {
                $budgetPlanningDetail = DepartmentBudgetPlanningDetail::with('budgetTemplateGl')
                    ->where('id', $glSectionId)
                    ->first();
                if ($budgetPlanningDetail && !empty($budgetPlanningDetail->budgetTemplateGl)) {
                    if ($budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID']) {
                        $chartOfAccounts = ChartOfAccount::where('chartOfAccountSystemID', $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID'])
                            ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription')
                            ->first();
                        
                        $companySegment = CompanyDepartmentSegment::find($budgetPlanningDetail->department_segment_id);
                        
                        if ($chartOfAccounts) {
                            array_push($chartOfAccountSystemIDs, [
                                'chartOfAccountSystemID' => $budgetPlanningDetail->id,
                                'AccountDescription' => $chartOfAccounts->AccountCode . ' - ' . $chartOfAccounts->AccountDescription,
                                'AccountCode' => SegmentMaster::find($companySegment->serviceLineSystemID)->ServiceLineDes
                            ]);
                        }
                    }
                }
            }
        } else {
            // Method 2: Standard chart of accounts
            foreach ($selectedGlSections as $glSectionId) {
                $budgetPlanningDetail = DepBudgetTemplateGl::where('depBudgetTemplateGlID', $glSectionId)
                    ->first();
                
                    $chartOfAccounts = ChartOfAccount::where('chartOfAccountSystemID', $budgetPlanningDetail->chartOfAccountSystemID)
                    ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription')
                    ->first();
                
                if ($chartOfAccounts) {
                    array_push($chartOfAccountSystemIDs, [
                        'chartOfAccountSystemID' => $budgetPlanningDetail->depBudgetTemplateGlID,
                        'AccountDescription' => $chartOfAccounts->AccountDescription,
                        'AccountCode' => $chartOfAccounts->AccountCode
                    ]);
                }
            }
        }
        
        return $chartOfAccountSystemIDs;
    }

    /**
     * Get chart of accounts by GL section IDs (for revision details)
     *
     * @param array $glSectionIds Array of GL section IDs
     * @param int $budgetPlanningId Budget planning ID to get workflow method
     * @return array
     */
    public function getChartOfAccountsByGlSectionIds($glSectionIds, $budgetPlanningId)
    {
        return $this->getChartOfAccountsByRevisionGlSections($glSectionIds, $budgetPlanningId);
    }
}

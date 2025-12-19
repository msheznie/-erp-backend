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
        
        if (!$budgetPlanning || !$budgetPlanning->budgetPlanningDetails) {
            return [];
        }

        $chartOfAccountSystemIDs = [];
        $isMethod1 = $budgetPlanning->workflow && $budgetPlanning->workflow->method == 1;
        
        // Collect all chartOfAccountSystemIDs and department_segment_ids first
        $chartOfAccountIds = [];
        $departmentSegmentIds = [];
        
        foreach ($budgetPlanning->budgetPlanningDetails as $budgetPlanningDetail) {
            if (!empty($budgetPlanningDetail->budgetTemplateGl) && 
                $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID']) {
                $chartOfAccountIds[] = $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID'];
                
                if ($isMethod1 && $budgetPlanningDetail->department_segment_id) {
                    $departmentSegmentIds[] = $budgetPlanningDetail->department_segment_id;
                }
            }
        }
        
        if (empty($chartOfAccountIds)) {
            return [];
        }
        
        // Bulk query for all ChartOfAccounts
        $chartOfAccountsMap = ChartOfAccount::whereIn('chartOfAccountSystemID', array_unique($chartOfAccountIds))
            ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription')
            ->get()
            ->keyBy('chartOfAccountSystemID');
        
        // For method 1, bulk query for segments
        $segmentsMap = [];
        if ($isMethod1 && !empty($departmentSegmentIds)) {
            $companySegments = CompanyDepartmentSegment::whereIn('departmentSegmentSystemID', array_unique($departmentSegmentIds))
                ->get()
                ->keyBy('departmentSegmentSystemID');
            
            $serviceLineIds = $companySegments->pluck('serviceLineSystemID')->filter()->unique()->toArray();
            
            if (!empty($serviceLineIds)) {
                $segmentMasters = SegmentMaster::whereIn('serviceLineSystemID', $serviceLineIds)
                    ->get()
                    ->keyBy('serviceLineSystemID');
                
                // Build segments map
                foreach ($companySegments as $segment) {
                    if ($segment->serviceLineSystemID && isset($segmentMasters[$segment->serviceLineSystemID])) {
                        $segmentsMap[$segment->departmentSegmentSystemID] = $segmentMasters[$segment->serviceLineSystemID]->ServiceLineDes;
                    }
                }
            }
        }
        
        // Build result array using the maps
        foreach ($budgetPlanning->budgetPlanningDetails as $budgetPlanningDetail) {
            if (!empty($budgetPlanningDetail->budgetTemplateGl) && 
                $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID']) {
                
                $chartOfAccountId = $budgetPlanningDetail->budgetTemplateGl['chartOfAccountSystemID'];
                $chartOfAccount = $chartOfAccountsMap[$chartOfAccountId] ?? null;
                
                if ($chartOfAccount) {
                    if ($isMethod1) {
                        $segmentDescription = $segmentsMap[$budgetPlanningDetail->department_segment_id] ?? 'N/A';
                        
                        $chartOfAccountSystemIDs[] = [
                            'chartOfAccountSystemID' => $budgetPlanningDetail->id,
                            'AccountDescription' => $chartOfAccount->AccountCode . ' - ' . $chartOfAccount->AccountDescription,
                            'AccountCode' => $segmentDescription
                        ];
                    } else {
                        $chartOfAccountSystemIDs[] = [
                            'chartOfAccountSystemID' => $budgetPlanningDetail->budgetTemplateGl['depBudgetTemplateGlID'],
                            'AccountDescription' => $chartOfAccount->AccountDescription,
                            'AccountCode' => $chartOfAccount->AccountCode
                        ];
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
                                'AccountCode' => (!empty($companySegment) && !empty($companySegment->serviceLineSystemID)) ? SegmentMaster::find($companySegment->serviceLineSystemID)->ServiceLineDes : 'N/A'
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

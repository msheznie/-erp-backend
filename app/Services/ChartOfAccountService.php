<?php

namespace App\Services;

use App\Http\Requests\API\PullChartofAccountAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\CompanyDepartmentSegment;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Models\SegmentMaster;
use App\Models\DepBudgetTemplateGl;
use App\Models\ReportTemplateDetails;
use App\Repositories\ChartOfAccountRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ChartOfAccountService
{
    /**
     * @var ChartOfAccountRepository
     */
    protected $chartOfAccountRepository;

    public function __construct(ChartOfAccountRepository $chartOfAccountRepository)
    {
        $this->chartOfAccountRepository = $chartOfAccountRepository;
    }

    /**
     * Pull chart of accounts with filters and pagination.
     *
     * Validation stays in the FormRequest; this method receives the request
     * and handles business logic and pagination.
     *
     * @param PullChartofAccountAPIRequest $request
     * @return array
     */
    public function pullChartOfAccounts($request): array
    {
        $input = $request->all();
        $companySystemID = $input['company_id'];
        $categoryFilter = isset($input['category']) ? ($input['category'] === 'Balance sheet' ? 'BS' : 'PL') : null;
        $controlAccountCodes = $input['controlAccounts'] ?? [];

        $accountCodeFilter = array_values(array_filter(array_map('trim', $input['accountCode'] ?? [])));
        if (!empty($accountCodeFilter)) {
            $validCodes = ChartOfAccount::where('primaryCompanySystemID', $companySystemID)
                ->where('isApproved', 1)
                ->where('isActive', 1)
                ->pluck('AccountCode')
                ->toArray();

            $invalidCodes = array_diff($accountCodeFilter, $validCodes);
            if (!empty($invalidCodes)) {
                throw new \Exception('The Account Code not matching: ' . implode(', ', $invalidCodes));
            }

            $assignedCodes = ChartOfAccount::where('primaryCompanySystemID', $companySystemID)
                ->where('isApproved', 1)
                ->where('isActive', 1)
                ->whereHas('chartofaccount_assigned', function ($q) use ($companySystemID) {
                    $q->where('companySystemID', $companySystemID)
                        ->where('isActive', 1)
                        ->where('isAssigned', -1);
                })
                ->pluck('AccountCode')
                ->toArray();

            $unassignedCodes = array_diff($accountCodeFilter, $assignedCodes);
            if (!empty($unassignedCodes)) {
                throw new \Exception('The account is not assigned: ' . implode(', ', $unassignedCodes));
            }
        }

        $controlAccountYNFilter = null;
        if (isset($input['controlAccountYN']) && $input['controlAccountYN'] !== '') {
            $controlAccountYNFilter = $input['controlAccountYN'] === 'Yes' ? 1 : 0;
        }
        $isBankFilter = null;
        if (isset($input['isBank']) && $input['isBank'] !== '') {
            $isBankFilter = $input['isBank'] === 'Yes' ? 1 : 0;
        }

        $defaultTemplateCategoryDescriptions = array_values(array_filter(array_map('trim', $input['defaultTemplateCategory'] ?? [])));
        if (!empty($defaultTemplateCategoryDescriptions)) {
            $validDescriptions = ReportTemplateDetails::where('companySystemID', $companySystemID)
                ->pluck('description')
                ->toArray();
            $invalidDescriptions = array_diff($defaultTemplateCategoryDescriptions, $validDescriptions);
            if (!empty($invalidDescriptions)) {
                throw new \Exception('The Default Template Category not matching: ' . implode(', ', $invalidDescriptions));
            }
        }

        $usePagination = $request->has('page');
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $accounts = $this->chartOfAccountRepository->getPullChartOfAccounts(
            [
                'company_id' => $companySystemID,
                'categoryFilter' => $categoryFilter,
                'controlAccountCodes' => $controlAccountCodes,
                'accountCodeFilter' => $accountCodeFilter,
                'controlAccountYNFilter' => $controlAccountYNFilter,
                'isBankFilter' => $isBankFilter,
                'defaultTemplateCategoryDescriptions' => $defaultTemplateCategoryDescriptions,
            ],
            $usePagination,
            $page,
            $perPage
        );

        $result = [];
        foreach ($accounts as $account) {
            $result[] = [
                'companyName' => $account->chartofaccount_assigned && $account->chartofaccount_assigned->company
                    ? ($account->chartofaccount_assigned->company->CompanyName ?? null)
                    : null,
                'accountCode' => $account->AccountCode,
                'accountDescription' => $account->AccountDescription,
                'category' => $account->accountType->description,
                'controlAccount' => $account->controlAccount->description,
                'controlAccountYN' => $account->controllAccountYN == 1 ? 'Yes' : 'No',
                'defaultTemplateCategory' => $account->templateCategoryDetails->description ?? null,
                'isActive' => $account->isActive == 1 ? 'Yes' : 'No',
                'isBank' => $account->isBank == 1 ? 'Yes' : 'No',
                'allocationType' => $account->allocation->Desciption ?? null,
                'relatedPartyYN' => $account->relatedPartyYN == 1 ? 'Yes' : 'No',
                
            ];
        }

        if ($usePagination) {
            $transformedItems = collect($result);
            $paginatedResult = new LengthAwarePaginator(
                $transformedItems,
                $accounts->total(),
                $accounts->perPage(),
                $accounts->currentPage(),
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return $paginatedResult->toArray();
        }

        return $result;
    }

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

<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentBudgetPlanningDetail;
use App\Models\DepartmentBudgetTemplate;
use App\Models\DepBudgetTemplateGl;
use App\Models\CompanyBudgetPlanning;
use App\Models\CompanyDepartmentSegment;
use App\Models\CompanyDepartmentEmployee;
use App\Models\CompanyFinanceYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessDepartmentBudgetPlanningDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $db;
    public $departmentBudgetPlanningId;
    public $userId;
    public $chartOfAccountSystemIDs;

    /**
     * Create a new job instance.
     *
     * @param string $db
     * @param int $departmentBudgetPlanningId
     * @param int $userId
     */
    public function __construct($db, $departmentBudgetPlanningId, $userId = null,$chartOfAccountSystemIDs =null)
    {
        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->db = $db;
        $this->departmentBudgetPlanningId = $departmentBudgetPlanningId;
        $this->userId = $userId ?? auth()->id() ?? 1;
        $this->chartOfAccountSystemIDs = $chartOfAccountSystemIDs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/department_budget_planning_details_process.log');

        DB::beginTransaction();

        $db = $this->db;

        try {
            CommonJobService::db_switch($db);

            // Get department budget planning with relationships
            $departmentBudgetPlanning = DepartmentBudgetPlanning::with([
                'masterBudgetPlannings.company',
                'department',
                'workflow'
            ])->find($this->departmentBudgetPlanningId);

            if (!$departmentBudgetPlanning) {
                throw new \Exception('Department Budget Planning not found');
            }

            // Get budget template GLs (validation already done in controller)
            $budgetTemplateGls = $this->getBudgetTemplateGls($departmentBudgetPlanning);

            // Process each GL
            foreach ($budgetTemplateGls as $templateGl) {
                $this->processTemplateGl($departmentBudgetPlanning, $templateGl);
            }

            DB::commit();

            Log::info('Department Budget Planning Details processed successfully', [
                'department_planning_id' => $this->departmentBudgetPlanningId,
                'user_id' => $this->userId
            ]);

        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            DB::rollBack();

            Log::error('Error processing Department Budget Planning Details', [
                'department_planning_id' => $this->departmentBudgetPlanningId,
                'error' => $e->getMessage(),
                'user_id' => $this->userId
            ]);
        }
    }

    /**
     * Get budget template GLs
     */
    private function getBudgetTemplateGls($departmentBudgetPlanning)
    {
        // Get company budget planning type
        $companyBudgetPlanning = CompanyBudgetPlanning::find($departmentBudgetPlanning->masterBudgetPlannings->id);
        $budgetType = $companyBudgetPlanning->typeID;

        // Get department budget template
        $departmentBudgetTemplate = DepartmentBudgetTemplate::where('departmentSystemID', $departmentBudgetPlanning->departmentID)
            ->whereHas('budgetTemplate', function ($query) use ($budgetType) {
                $query->where('type', $budgetType);
            })
            ->first();

        if (!$departmentBudgetTemplate) {
            return collect([]);
        }

        if(empty($this->chartOfAccountSystemIDs))
        {
            // Get GLs assigned to this template
            $budgetTemplateGls = DepBudgetTemplateGl::where('departmentBudgetTemplateID', $departmentBudgetTemplate->departmentBudgetTemplateID)
                ->with(['chartOfAccount','departmentBudgetTemplate'])
                ->get();

        }else {
            // Get GLs assigned to this template
            $budgetTemplateGls = DepBudgetTemplateGl::where('departmentBudgetTemplateID', $departmentBudgetTemplate->departmentBudgetTemplateID)
                ->whereIn('chartOfAccountSystemID',$this->chartOfAccountSystemIDs)
                ->with(['chartOfAccount','departmentBudgetTemplate'])
                ->get();

        }

        return $budgetTemplateGls;
    }

    /**
     * Process individual template GL
     */
    private function processTemplateGl($departmentBudgetPlanning, $templateGl)
    {

        // check if the company budget planning typeId is 1 then get the company department segemnts


        if ($departmentBudgetPlanning->workflow->method == 1) {
            $companyDepartmentSegments = CompanyDepartmentSegment::where('departmentSystemID', $departmentBudgetPlanning->departmentID)->get();
            foreach ($companyDepartmentSegments as $companyDepartmentSegment) {
                $this->createNewDetail($departmentBudgetPlanning, $templateGl, $companyDepartmentSegment);
            }
        } else {
            // Check if detail already exists
            $existingDetail = DepartmentBudgetPlanningDetail::where('department_planning_id', $departmentBudgetPlanning->id)
                ->where('budget_template_gl_id', $templateGl->depBudgetTemplateGlID)
                ->first();

            if (!$existingDetail) {
                $this->createNewDetail($departmentBudgetPlanning, $templateGl);
            }
        }

    }

    /**
     * Create new department budget planning detail
     */
    private function createNewDetail($departmentBudgetPlanning, $templateGl, $companyDepartmentSegment = null)
    {
        // Calculate previous year and current year budgets
        $budgetCalculations = $this->calculateBudgetAmounts($departmentBudgetPlanning, $templateGl);

        // Get responsible person (HOD of the department)
        $responsiblePerson = $this->getResponsiblePerson($departmentBudgetPlanning);

        // Create detail
        DepartmentBudgetPlanningDetail::create([
            'department_planning_id' => $departmentBudgetPlanning->id,
            'budget_template_id' => $templateGl->departmentBudgetTemplate->budgetTemplateID,
            'department_segment_id' => $companyDepartmentSegment->departmentSegmentSystemID ?? null,
            'budget_template_gl_id' => $templateGl->depBudgetTemplateGlID,
            'request_amount' => 0.00,
            'responsible_person' => $responsiblePerson['id'] ?? null,
            'responsible_person_type' => $responsiblePerson['type'] ?? 1,
            'time_for_submission' => $departmentBudgetPlanning->masterBudgetPlannings->submissionDate,
            'previous_year_budget' => $budgetCalculations['previous_year'],
            'current_year_budget' => $budgetCalculations['current_year'],
            'difference_last_current_year' => $budgetCalculations['difference'],
            'amount_given_by_finance' => 0.00,
            'amount_given_by_hod' => 0.00,
            'internal_status' => 1, // Pending
            'difference_current_request' => 0.00 // Will be calculated when request amount is set
        ]);
    }

    /**
     * Update existing department budget planning detail
     */
    private function updateExistingDetail($existingDetail, $departmentBudgetPlanning, $templateGl)
    {
        // Recalculate budget amounts
        $budgetCalculations = $this->calculateBudgetAmounts($departmentBudgetPlanning, $templateGl);

        // Update only calculated fields, preserve user inputs
        $existingDetail->update([
            'previous_year_budget' => $budgetCalculations['previous_year'],
            'current_year_budget' => $budgetCalculations['current_year'],
            'difference_last_current_year' => $budgetCalculations['difference'],
            'time_for_submission' => $this->calculateSubmissionTime($departmentBudgetPlanning)
        ]);

        // Recalculate difference with request amount
        $existingDetail->calculateDifferences();
        $existingDetail->save();
    }

    /**
     * Calculate budget amounts for previous and current year
     */
    private function calculateBudgetAmounts($departmentBudgetPlanning, $templateGl)
    {
        // Get budget year from finance year
        $financeYear = CompanyFinanceYear::find($departmentBudgetPlanning->masterBudgetPlannings->yearID);

        // carban format the ending date to year
        $budgetYear = Carbon::parse($financeYear->endingDate)->year;

        //get the budget year - 1 year from the budget year
        $currentYear = $budgetYear - 1;
        $previousYear = $budgetYear - 2;

        //get the current year finance year id from the finance year table
        $currentYearFinanceYear = CompanyFinanceYear::whereYear('endingDate', $currentYear)->where('companySystemID', $financeYear->companySystemID)->first();
        $previousYearFinanceYear = CompanyFinanceYear::whereYear('endingDate', $previousYear)->where('companySystemID', $financeYear->companySystemID)->first();

        // Get GL account code
        $glCode = $templateGl->chartOfAccountSystemID ?? null;
        $departmentId = $departmentBudgetPlanning->departmentID;

        // Calculate previous year budget (you may need to adjust this query based on your actual budget data structure)
        $previousYearBudget = $this->getBudgetAmountForYear($departmentId, $glCode, $previousYearFinanceYear,$financeYear->companySystemID);

        // Calculate current year budget
        $currentYearBudget = $this->getBudgetAmountForYear($departmentId, $glCode, $currentYearFinanceYear,$financeYear->companySystemID);

        // Calculate difference
        $difference = $currentYearBudget - $previousYearBudget;

        return [
            'previous_year' => $previousYearBudget,
            'current_year' => $currentYearBudget,
            'difference' => $difference
        ];
    }

    /**
     * Get budget amount for a specific year
     * This method should be adjusted based on your actual budget data structure
     */
    private function getBudgetAmountForYear($departmentId, $glCode, $year,$companySystemID)
    {
        try {

            if($year) {
                //get the sum of request_amount from the department budget planning details table
                $budgetAmount = DepartmentBudgetPlanningDetail::whereHas('departmentBudgetPlanning', function($query) use ($departmentId, $year, $companySystemID) {
                    $query->where('yearID', $year->companyFinanceYearID)
                        ->whereHas('masterBudgetPlannings', function ($q) use ($companySystemID) {
                            $q->where('companySystemID', $companySystemID);
                        });
                })->whereHas('budgetTemplateGl', function($query) use ($glCode) {
                        $query->where('chartOfAccountSystemID', $glCode);
                })->sum('request_amount');

                return $budgetAmount ?? 0.00;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            Log::warning('Could not retrieve budget amount', [
                'department_id' => $departmentId,
                'gl_code' => $glCode,
                'year' => $year,
                'error' => $e->getMessage()
            ]);

            return 0.00;
        }
    }

    /**
     * Get responsible person for the department
     */
    private function getResponsiblePerson($departmentBudgetPlanning)
    {
        // Get HOD of the department
        try {
            //get the hod of the department from Company department employees table
            $hod = CompanyDepartmentEmployee::where('departmentSystemID', $departmentBudgetPlanning->departmentID)
                ->where('isHOD', 1)
                ->first();

            if ($hod) {
                return [
                    'id' => $hod->employeeSystemID,
                    'type' => 1 // HOD
                ];
            }

            // Fallback to current user
            return [
                'id' => $this->userId,
                'type' => 1 // Delegate
            ];
        } catch (\Exception $e) {
            Log::warning('Could not determine responsible person', [
                'department_id' => $departmentBudgetPlanning->departmentSystemID,
                'error' => $e->getMessage()
            ]);

            return [
                'id' => $this->userId,
                'type' => 1
            ];
        }
    }

    /**
     * Calculate submission time based on budget planning schedule
     */
    private function calculateSubmissionTime($departmentBudgetPlanning)
    {
        // Calculate submission deadline (e.g., 30 days from budget initiation)
        $budgetInitiateDate = Carbon::parse($departmentBudgetPlanning->masterBudgetPlannings->budgetInitiateDate);
        return $budgetInitiateDate->addDays(30)->format('Y-m-d');
    }


}

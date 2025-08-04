<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\BudgetTemplate;
use App\Models\DepartmentBudgetTemplate;
use App\Models\CompanyDepartment;
use App\Models\DepBudgetTemplateGl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignBudgetTemplateToAllDepartments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $budgetTemplateId;
    protected $userId;
    protected $db;

    /**
     * Create a new job instance.
     *
     * @param int $budgetTemplateId
     * @param int $userId
     * @param string $db
     * @return void
     */
    public function __construct($budgetTemplateId, $userId, $db = '')
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

        $this->budgetTemplateId = $budgetTemplateId;
        $this->userId = $userId;
        $this->db = $db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            CommonJobService::db_switch($this->db);

            $budgetTemplate = BudgetTemplate::find($this->budgetTemplateId);
            
            if (!$budgetTemplate) {
                Log::error("Budget template {$this->budgetTemplateId} not found");
                return;
            }

            $departments = CompanyDepartment::where('isActive', 1)->get();
            
            foreach ($departments as $department) {
                try {
                    // Check if the template is already assigned to the department
                    $departmentBudgetTemplateCheck = DepartmentBudgetTemplate::where('budgetTemplateID', $this->budgetTemplateId)
                        ->where('departmentSystemID', $department->departmentSystemID)
                        ->first();

                    if (!$departmentBudgetTemplateCheck) {
                        $departmentBudgetTemplate = new DepartmentBudgetTemplate();
                        $departmentBudgetTemplate->budgetTemplateID = $this->budgetTemplateId;
                        $departmentBudgetTemplate->departmentSystemID = $department->departmentSystemID;
                        $departmentBudgetTemplate->isActive = 0;
                        $departmentBudgetTemplate->createdUserSystemID = $this->userId;
                        $departmentBudgetTemplate->modifiedUserSystemID = $this->userId;
                        $departmentBudgetTemplate->save();

                        $departmentBudgetTemplateID = $departmentBudgetTemplate->departmentBudgetTemplateID;

                        // Get chart of accounts for this department
                        $items = \App\Models\ChartOfAccount::where('isActive', 1)
                            ->where('isApproved', 1)
                            ->whereHas('chartofaccount_assigned', function($query) use ($department) {
                                $query->where('companySystemID', $department->companySystemID)
                                    ->where('isAssigned', -1)
                                    ->where('isActive', 1);
                            })->when($budgetTemplate->type == 1, function ($query) {
                                $query->where('catogaryBLorPL', 'PL');
                            })->when($budgetTemplate->type == 2, function ($query) {
                                $query->where('catogaryBLorPL', 'BS');
                            })
                            ->whereNotNull('reportTemplateCategory')
                            ->select('chartOfAccountSystemID', 'AccountCode', 'AccountDescription', 'catogaryBLorPL', 'controlAccounts')
                            ->get();

                        foreach ($items as $item) {
                            // Assign the GL to DepBudgetTemplateGl
                            $depBudgetTemplateGl = new DepBudgetTemplateGl();
                            $depBudgetTemplateGl->departmentBudgetTemplateID = $departmentBudgetTemplateID;
                            $depBudgetTemplateGl->chartOfAccountSystemID = $item->chartOfAccountSystemID;
                            $depBudgetTemplateGl->createdUserSystemID = $this->userId;
                            $depBudgetTemplateGl->modifiedUserSystemID = $this->userId;
                            $depBudgetTemplateGl->save();
                        }

                    }
                } catch (\Exception $e) {
                    Log::error("Error assigning budget template to department {$department->departmentSystemID}: " . $e->getMessage());
                    // Continue with other departments even if one fails
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            Log::error("Error in AssignBudgetTemplateToAllDepartments job: " . $e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("AssignBudgetTemplateToAllDepartments job failed for template {$this->budgetTemplateId}: " . $exception->getMessage());
    }
} 
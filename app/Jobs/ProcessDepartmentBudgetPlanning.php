<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CompanyBudgetPlanning;
use App\Models\CompanyDepartment;
use App\Models\DepartmentBudgetPlanning;
use App\Traits\AuditLogsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDepartmentBudgetPlanning implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, AuditLogsTrait;

    public $db;
    public $companyBudgetPlanningID;
    public $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $companyBudgetPlanningID, $uuid)
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
        $this->companyBudgetPlanningID = $companyBudgetPlanningID;
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/department_budget_process.log');

        DB::beginTransaction();

        $db = $this->db;

        try {
            CommonJobService::db_switch($db);

            $companyBudgetPlanning = CompanyBudgetPlanning::find($this->companyBudgetPlanningID);
            if ($companyBudgetPlanning) {
                $finalDepartments = CompanyDepartment::where('companySystemID', $companyBudgetPlanning->companySystemID)->whereNotNull('parentDepartmentID')->doesntHave('children')->get();
                foreach ($finalDepartments as $department) {
                    $data = [
                        'companyBudgetPlanningID' => $companyBudgetPlanning->id,
                        'departmentID' => $department->departmentSystemID,
                        'planningCode' => $companyBudgetPlanning->planningCode,
                        'initiatedDate' => $companyBudgetPlanning->initiatedDate,
                        'periodID' => $companyBudgetPlanning->periodID,
                        'yearID' => $companyBudgetPlanning->yearID,
                        'typeID' => $companyBudgetPlanning->typeID,
                        'submissionDate' => $companyBudgetPlanning->submissionDate,
                        'workflowID' => $companyBudgetPlanning->workflowID,
                        'status' => 0
                    ];

                    $budgetPlanning = DepartmentBudgetPlanning::create($data);

                    $this->auditLog(
                        $db,
                        $budgetPlanning->id,
                        $this->uuid,
                        "department_budget_plannings",
                        "Department budget planning ".$budgetPlanning->planningCode." has been created",
                        "C",
                        $budgetPlanning->toArray(),
                        [],
                        0
                    );
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::warning($exception->getMessage());
            DB::rollBack();
        }
    }
}

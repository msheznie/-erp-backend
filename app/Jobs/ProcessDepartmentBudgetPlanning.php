<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\CompanyBudgetPlanning;
use App\Models\CompanyDepartment;
use App\Models\DepartmentBudgetPlanning;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDepartmentBudgetPlanning implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $db;
    public $companyBudgetPlanningID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $companyBudgetPlanningID)
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

                    DepartmentBudgetPlanning::create($data);
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::warning($exception->getMessage());
            DB::rollBack();
        }
    }
}

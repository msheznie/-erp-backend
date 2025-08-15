<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentUserBudgetControlAPIRequest;
use App\Http\Requests\API\UpdateDepartmentUserBudgetControlAPIRequest;
use App\Models\DepartmentUserBudgetControl;
use App\Models\BudgetControl;
use App\Repositories\DepartmentUserBudgetControlRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Illuminate\Support\Facades\DB;
use App\Traits\AuditLogsTrait;

class DepartmentUserBudgetControlAPIController extends AppBaseController
{
    use AuditLogsTrait;

    private $departmentUserBudgetControlRepository;

    public function __construct(DepartmentUserBudgetControlRepository $departmentUserBudgetControlRepo)
    {
        $this->departmentUserBudgetControlRepository = $departmentUserBudgetControlRepo;
    }

    /**
     * Get budget controls for a specific department employee
     */
    public function getUserBudgetControls(Request $request)
    {
        $departmentEmployeeSystemID = $request->get('departmentEmployeeSystemID');
        
        if (!$departmentEmployeeSystemID) {
            return $this->sendError('Department Employee ID is required');
        }

        // Get all budget controls
        $allBudgetControls = BudgetControl::where('isActive', 1)->get();

        // Get user's assigned budget controls
        $userBudgetControls = $this->departmentUserBudgetControlRepository
                                  ->getUserBudgetControls($departmentEmployeeSystemID);

        // Create array of assigned budget control IDs
        $assignedControlIds = $userBudgetControls->pluck('budgetControlID')
                                                ->toArray();

        // Format response
        $budgetControlsWithAssignment = $allBudgetControls->map(function ($control) use ($assignedControlIds) {
            return [
                'budgetControlID' => $control->budgetControlID,
                'controlName' => $control->controlName,
                'controlDescription' => $control->controlDescription,
                'isAssigned' => in_array($control->budgetControlID, $assignedControlIds)
            ];
        });

        return $this->sendResponse([
            'budgetControls' => $budgetControlsWithAssignment
        ], 'Budget controls retrieved successfully');
    }

    /**
     * Save user budget controls
     */
    public function saveUserBudgetControls(CreateDepartmentUserBudgetControlAPIRequest $request)
    {
        $input = $request->all();

        try {
            DB::beginTransaction();

            $departmentEmployeeSystemID = $input['departmentEmployeeSystemID'];
            $selectedControlIds = $input['budgetControlIds'] ?? [];

            // Sync user budget controls
            $this->departmentUserBudgetControlRepository
                 ->syncUserBudgetControls($departmentEmployeeSystemID, $selectedControlIds);

            // Audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $departmentEmployeeSystemID, $uuid, "department_user_budget_control", "Budget controls updated for user", "U", $selectedControlIds, []);

            DB::commit();

            return $this->sendResponse([], 'Budget controls saved successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error saving budget controls - '.$e->getMessage());
        }
    }
} 
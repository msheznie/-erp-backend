<?php

namespace App\Services;

use App\Models\BudgetControl;
use App\Models\BudgetDelegateAccess;
use App\Models\BudgetDelegateAccessRecord;
use App\Models\CompanyDepartment;
use App\Models\CompanyDepartmentEmployee;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DepartmentUserBudgetControl;
use App\Models\WorkflowConfigurationHodAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BudgetPermissionService
{
    /**
     * Get budget planning user permissions for a given company and user
     *
     * @param array $input
     * @return array
     */
    public function getBudgetPlanningUserPermissions(array $input): array
    {
        $companyId = $input['companyId'] ?? null;

        if (!isset($companyId)) {
            return [
                'success' => false,
                'message' => 'Company ID not found',
                'data' => null
            ];
        }

        $employeeID = isset($input['delegateUser']) ? $input['delegateUser'] : \Helper::getEmployeeSystemID();

        $userPermissions = [
            'financeUser' => [
                'status' => false,
                'access' => [],
                'isActive' => true
            ],
            'hodUser' => [
                'status' => false,
                'access' => [],
                'isActive' => true
            ],
            'delegateUser' => [
                'status' => false,
                'access' => [],
                'isActive' => true
            ]
        ];

        $assignDepartments = CompanyDepartmentEmployee::where('employeeSystemID', $employeeID)
            ->where('isActive', 1)
            ->whereHas('department', function($query) use ($companyId) {
                $query->where('companySystemID', $companyId);
            });

        if ($assignDepartments) {
            // Check if user is HOD
            $isHODUser = (clone $assignDepartments)->whereHas('department', function($query) {
                $query->where('isFinance', 0);
            })
            ->where('isHOD',1)->exists();

            if ($isHODUser) {
                $userPermissions['hodUser']['status'] = true;

                // only get permission if budgetPlanningID is set
                if (isset($input['budgetPlanningID'])) {
                    $budgetPlanning = DepartmentBudgetPlanning::find($input['budgetPlanningID']);

                    $assignedDepartmentByBudget = CompanyDepartmentEmployee::with(['department'])->where('employeeSystemID',Auth::user()->employee_id)
                                                    ->where('isHOD',true)
                                                    ->where('isActive', 1)
                                                    ->first();

                    if($budgetPlanning->submissionDate <= Carbon::today()->format('Y-m-d'))
                    {
                        $userPermissions['hodUser']['isActive'] = false;
                    }


                    $actions = WorkflowConfigurationHodAction::with('hodAction')->where('workflowConfigurationID',$budgetPlanning->workflowID);

                    if($assignedDepartmentByBudget->department->departmentSystemID === CompanyDepartment::getRootParentDepartmentID($budgetPlanning->departmentID))
                    {
                        $actions = $actions->where('parent',1);
                    }else {
                        $actions = $actions->where('child',1);
                    }

                    $actions = $actions->pluck('hodActionID')->toArray();

                    $userPermissions['hodUser']['access'] = $actions;

                }


            }

            // Check if user is Finance User
            $isFinanceUser = (clone $assignDepartments)->whereHas('department', function($query) {
                $query->where('isFinance', 1);
            })->exists();

            if ($isFinanceUser) {
                $userPermissions['financeUser']['status'] = true;

                $allBudgetControls = BudgetControl::where('isActive', 1)->get();

                $departmentEmployeeID = (clone $assignDepartments)->whereHas('department', function($query) {
                    $query->where('isFinance', 1);
                })->pluck('departmentEmployeeSystemID');
                $financeUserPermissions = DepartmentUserBudgetControl::where('departmentEmployeeSystemID', $departmentEmployeeID->first())->pluck('budgetControlID')->toArray();

                $userPermissions['financeUser']['access'] = $allBudgetControls->mapWithKeys( function($budgetControl) use ($financeUserPermissions) {
                    return [Str::slug($budgetControl->controlName, "_") => in_array($budgetControl->budgetControlID, $financeUserPermissions)];
                });
            }

            // Check if user is delegate & assign permissions
            if (isset($input['departmentSystemID'])) {
                $delegateeID = (clone $assignDepartments)->where('departmentSystemID', $input['departmentSystemID'])->pluck('departmentEmployeeSystemID')->first();
                $delegateUserAccess = BudgetDelegateAccessRecord::where('delegatee_id', $delegateeID);
            }
            else {
                $delegateeID = (clone $assignDepartments)->pluck('departmentEmployeeSystemID')->toArray();
                $delegateUserAccess = BudgetDelegateAccessRecord::whereIn('delegatee_id', $delegateeID);
            }

            if ((count((clone $delegateUserAccess)->get()) > 0)) {
                $userPermissions['delegateUser']['status'] = true;

                // only get permission if departmentBudgetPlanningDetailID is set
                if (isset($input['departmentBudgetPlanningDetailID'])) {
                    $delegateUserAccessData = $delegateUserAccess->where('budget_planning_detail_id', $input['departmentBudgetPlanningDetailID'])->first();
                    if(!empty($delegateUserAccessData) && ($delegateUserAccessData->status != 1 || $delegateUserAccessData->submission_time <= Carbon::today()->format('Y-m-d')))
                    {
                        $userPermissions['delegateUser']['isActive'] = false;
                    }
                    if (!empty($delegateUserAccessData)) {
                        $preDelegateUserAccessData = BudgetDelegateAccess::where('is_active', 1)->get();
                        $userExistingPermissions = is_array($delegateUserAccessData->access_permissions) ? $delegateUserAccessData->access_permissions : json_decode($delegateUserAccessData->access_permissions);
                        $userPermissions['delegateUser']['access'] = $preDelegateUserAccessData->mapWithKeys(function($preDelegateUserAccess) use ($userExistingPermissions) {
                            return [$preDelegateUserAccess->slug => in_array($preDelegateUserAccess->slug, $userExistingPermissions)];
                        });
                    }
                }
            }
        }

        return [
            'success' => true,
            'message' => 'User access retrieved successfully',
            'data' => $userPermissions
        ];
    }

    /**
     * Check if user has finance user permissions
     *
     * @param int $companyId
     * @param int|null $employeeId
     * @return bool
     */
    public function isFinanceUser(int $companyId, ?int $employeeId = null): bool
    {
        $employeeId = $employeeId ?? \Helper::getEmployeeSystemID();
        
        return CompanyDepartmentEmployee::where('employeeSystemID', $employeeId)
            ->where('isActive', 1)
            ->whereHas('department', function($query) use ($companyId) {
                $query->where('companySystemID', $companyId)
                      ->where('isFinance', 1);
            })->exists();
    }

    /**
     * Check if user has HOD permissions
     *
     * @param int $companyId
     * @param int|null $employeeId
     * @return bool
     */
    public function isHODUser(int $companyId, ?int $employeeId = null): bool
    {
        $employeeId = $employeeId ?? \Helper::getEmployeeSystemID();
        
        return CompanyDepartmentEmployee::where('employeeSystemID', $employeeId)
            ->where('isActive', 1)
            ->where('isHOD', 1)
            ->whereHas('department', function($query) use ($companyId) {
                $query->where('companySystemID', $companyId)
                      ->where('isFinance', 0);
            })->exists();
    }

    /**
     * Check if user has delegate permissions
     *
     * @param int $companyId
     * @param int|null $employeeId
     * @return bool
     */
    public function isDelegateUser(int $companyId, ?int $employeeId = null): bool
    {
        $employeeId = $employeeId ?? \Helper::getEmployeeSystemID();
        
        $assignDepartments = CompanyDepartmentEmployee::where('employeeSystemID', $employeeId)
            ->where('isActive', 1)
            ->whereHas('department', function($query) use ($companyId) {
                $query->where('companySystemID', $companyId);
            });

        $delegateeID = $assignDepartments->pluck('departmentEmployeeSystemID')->toArray();
        $delegateUserAccess = BudgetDelegateAccessRecord::whereIn('delegatee_id', $delegateeID);

        return (count($delegateUserAccess->get()) > 0);
    }

    /**
     * Get user's assigned departments for a company
     *
     * @param int $companyId
     * @param int|null $employeeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserAssignedDepartments(int $companyId, ?int $employeeId = null)
    {
        $employeeId = $employeeId ?? \Helper::getEmployeeSystemID();
        
        return CompanyDepartmentEmployee::where('employeeSystemID', $employeeId)
            ->where('isActive', 1)
            ->whereHas('department', function($query) use ($companyId) {
                $query->where('companySystemID', $companyId);
            })->get();
    }
}

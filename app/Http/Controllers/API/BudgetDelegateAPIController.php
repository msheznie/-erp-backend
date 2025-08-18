<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Services\BudgetDelegateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class BudgetDelegateAPIController extends AppBaseController
{
    protected $budgetDelegateService;

    public function __construct(BudgetDelegateService $budgetDelegateService)
    {
        $this->budgetDelegateService = $budgetDelegateService;
    }

    /**
     * Get all active access types
     *
     * @return Response
     */
    public function getActiveAccessTypes()
    {
        try {
            $accessTypes = $this->budgetDelegateService->getActiveAccessTypes();
            
            return $this->sendResponse($accessTypes, 'Access types retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving access types: ' . $e->getMessage());
        }
    }

    /**
     * Get department employees for delegation
     *
     * @param Request $request
     * @return Response
     */
    public function getDepartmentEmployees(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|integer|exists:company_departments,departmentSystemID'
            ]);

            $employees = $this->budgetDelegateService->getDepartmentEmployees($request->department_id);
            
            return $this->sendResponse($employees, 'Department employees retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving department employees: ' . $e->getMessage());
        }
    }

    /**
     * Get existing delegate access records
     *
     * @param Request $request
     * @return Response
     */
    public function getDelegateAccessRecords(Request $request)
    {
        try {
            $request->validate([
                'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id'
            ]);

            $records = $this->budgetDelegateService->getDelegateAccessRecords($request->budget_planning_detail_id);
            
            return $this->sendResponse($records, 'Delegate access records retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving delegate access records: ' . $e->getMessage());
        }
    }

    /**
     * Create or update delegate access
     *
     * @param Request $request
     * @return Response
     */
    public function createOrUpdateDelegateAccess(Request $request)
    {
        try {
            $request->validate([
                'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id',
                'delegatee_id' => 'required|integer|exists:company_departments_employees,departmentEmployeeSystemID',
                'submission_time' => 'required|date',
                'access_permissions' => 'required|array|min:1',
                'access_permissions.*' => 'string|exists:budget_delegate_access,slug'
            ]);


            $result = $this->budgetDelegateService->createOrUpdateDelegateAccess($request->all());
            
            if ($result['success']) {
                return $this->sendResponse($result['data'], $result['message']);
            } else {
                return $this->sendError($result['message']);
            }
        } catch (Exception $e) {
            return $this->sendError('Error updating delegate access: ' . $e->getMessage());
        }
    }

    /**
     * Remove delegate access
     *
     * @param Request $request
     * @return Response
     */
    public function removeDelegateAccess(Request $request)
    {
        try {
            $request->validate([
                'record_id' => 'required|integer|exists:dep_budget_pl_delegate_details,id'
            ]);

            $result = $this->budgetDelegateService->removeDelegateAccess($request->record_id);
            
            if ($result['success']) {
                return $this->sendResponse(null, $result['message']);
            } else {
                return $this->sendError($result['message']);
            }
        } catch (Exception $e) {
            return $this->sendError('Error removing delegate access: ' . $e->getMessage());
        }
    }

    /**
     * Get delegate access summary
     *
     * @param Request $request
     * @return Response
     */
    public function getDelegateAccessSummary(Request $request)
    {
        try {
            $request->validate([
                'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id'
            ]);

            $summary = $this->budgetDelegateService->getDelegateAccessSummary($request->budget_planning_detail_id);
            
            return $this->sendResponse($summary, 'Delegate access summary retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error retrieving delegate access summary: ' . $e->getMessage());
        }
    }
} 
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\CompanyBudgetPlanning;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentApproved;
use App\Models\EmployeesDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class BudgetPlanningApprovalAPIController
 * @package App\Http\Controllers\API
 * 
 * Controller for handling budget planning approval operations
 * Note: Field names may need to be adjusted based on actual database schema
 */
class BudgetPlanningApprovalAPIController extends AppBaseController
{
    /**
     * Convert array to selected value helper
     */
    public function convertArrayToSelectedValue($input, $array)
    {
        foreach ($array as $key) {
            if (array_key_exists($key, $input)) {
                if (is_array($input[$key])) {
                    if (count($input[$key]) > 0) {
                        $input[$key] = $input[$key][0];
                    } else {
                        $input[$key] = null;
                    }
                }
            }
        }
        return $input;
    }

    /**
     * Get Budget Planning Approval By User
     * POST /getBudgetPlanningApprovalByUser
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getBudgetPlanningApprovalByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approvedYN', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $documentId = 133; // Budget Planning Document ID
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $budgets = DB::table('erp_documentapproved')
            ->select(
                'employeesdepartments.approvalDeligated',
                'company_budget_plannings.*',
                'erp_documentapproved.documentApprovedID',
                'erp_documentapproved.rollLevelOrder',
                'erp_documentapproved.approvalLevelID',
                'erp_documentapproved.documentSystemCode',
                'company_budget_plannings.id as companyBudgetPlanningID',
                'companyfinanceyear.*'
            )
            ->join('employeesdepartments', function ($query) use ($companyId, $empID, $documentId) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', $documentId)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    // Service line approval logic if needed
                }

                $query->whereIn('employeesdepartments.documentSystemID', [$documentId])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID)
                    ->where('employeesdepartments.isActive', 1)
                    ->where('employeesdepartments.removedYN', 0);
            })
            ->join('company_budget_plannings', function ($query) use ($companyId) {
                // Join on documentSystemCode matching the budget planning ID
                $query->on('erp_documentapproved.documentSystemCode', '=', DB::raw('CAST(company_budget_plannings.id AS CHAR)'))
                    ->where('company_budget_plannings.companySystemID', $companyId);
                
                // Filter for confirmed but not approved records
                // Note: Adjust field names based on actual schema (confirmed_yn vs confirmedYN, etc.)
                $query->where(function($q) {
                    $q->where('company_budget_plannings.confirmed_yn', 1);                });
                
                $query->where(function($q) {
                    $q->where('company_budget_plannings.approved_yn', 0)
                      ->orWhereNull('company_budget_plannings.approved_yn');
                });
            })
            ->join('companyfinanceyear', 'company_budget_plannings.yearID', '=', 'companyfinanceyear.companyFinanceYearID')
            ->where('erp_documentapproved.approvedYN', 0)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [$documentId])
            ->where('erp_documentapproved.companySystemID', $companyId);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgets = $budgets->where(function($q) use ($input) {
                    $q->where('company_budget_plannings.confirmed_yn', $input['confirmedYN']);
                });
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgets = $budgets->where(function($q) use ($input) {
                    $q->where('company_budget_plannings.approved_yn', $input['approvedYN']);
                });
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgets = $budgets->whereMonth('company_budget_plannings.created_at', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgets = $budgets->whereYear('company_budget_plannings.created_at', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
        }

        $isEmployeeDischarched = \Helper::checkEmployeeDischarchedYN();

        if ($isEmployeeDischarched == 'true') {
            $budgets = [];
        }

        return \DataTables::of($budgets)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('finance_year', function ($row) {
                // Extract all finance year related columns and create nested object
                $financeYearFields = [
                    'bigginingDate',
                    'endingDate',
                ];
                
                $financeYear = [];
                foreach ($financeYearFields as $field) {
                    if (isset($row->$field)) {
                        $financeYear[$field] = $row->$field;
                    }
                }
                
                return !empty($financeYear) ? $financeYear : null;
            })
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('company_budget_plannings.id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Get Budget Planning Approved By User
     * POST /getBudgetPlanningApprovedByUser
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getBudgetPlanningApprovedByUser(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approvedYN', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $documentId = isset($input['documentId']) ? $input['documentId'] : 1044; // Budget Planning Document ID
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $budgets = DB::table('erp_documentapproved')
            ->select(
                'company_budget_plannings.*',
                'erp_documentapproved.documentApprovedID',
                'erp_documentapproved.rollLevelOrder',
                'erp_documentapproved.approvalLevelID',
                'erp_documentapproved.documentSystemCode',
                'company_budget_plannings.id as companyBudgetPlanningID',
                'companyfinanceyear.bigginingDate',
                'companyfinanceyear.endingDate'
            )
            ->join('company_budget_plannings', function ($query) use ($companyId) {
                // Join on documentSystemCode matching the budget planning ID
                $query->on('erp_documentapproved.documentSystemCode', '=', DB::raw('CAST(company_budget_plannings.id AS CHAR)'))
                    ->where('company_budget_plannings.companySystemID', $companyId);
                
                // Filter for confirmed but not approved records
                // Note: Adjust field names based on actual schema (confirmed_yn vs confirmedYN, etc.)
                $query->where(function($q) {
                    $q->where('company_budget_plannings.confirmed_yn', 1);                });
                
                $query->where(function($q) {
                    $q->where('company_budget_plannings.approved_yn', 0)
                      ->orWhereNull('company_budget_plannings.approved_yn');
                });
            })
            ->join('companyfinanceyear', 'company_budget_plannings.yearID', '=', 'companyfinanceyear.companyFinanceYearID')
            ->where('erp_documentapproved.approvedYN', -1)
            ->where('erp_documentapproved.rejectedYN', 0)
            ->where('erp_documentapproved.documentSystemID', 133)
            ->where('erp_documentapproved.companySystemID', $companyId);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $budgets = $budgets->where(function($q) use ($input) {
                    $q->where('company_budget_plannings.confirmed_yn', $input['confirmedYN']);
                });
            }
        }

        if (array_key_exists('approvedYN', $input)) {
            if (($input['approvedYN'] == 0 || $input['approvedYN'] == -1) && !is_null($input['approvedYN'])) {
                $budgets = $budgets->where(function($q) use ($input) {
                    $q->where('company_budget_plannings.approved_yn', $input['approvedYN']);
                });
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $budgets = $budgets->whereMonth('company_budget_plannings.created_at', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $budgets = $budgets->whereYear('company_budget_plannings.created_at', '=', $input['year']);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $budgets = $budgets->where(function ($query) use ($search) {
                $query->where('company_budget_plannings.planningCode', 'like', "%{$search}%");
            });
        }

        $budgets = $budgets->groupBy('id');

        return \DataTables::of($budgets)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addColumn('finance_year', function ($row) {
                // Extract all finance year related columns and create nested object
                $financeYearFields = [
                    'bigginingDate',
                    'endingDate',
                ];
                
                $financeYear = [];
                foreach ($financeYearFields as $field) {
                    if (isset($row->$field)) {
                        $financeYear[$field] = $row->$field;
                    }
                }
                
                return !empty($financeYear) ? $financeYear : null;
            })
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('company_budget_plannings.id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function rejectBudgetPlanning(Request $request)
    {

        $reject = \Helper::rejectDocument($request);
            if (!$reject["success"]) {
                return $this->sendError($reject["message"]);
            } else {
                return $this->sendResponse(array(), $reject["message"]);
            }
    }
}

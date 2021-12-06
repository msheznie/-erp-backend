<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SupplierRegistrationApproval extends AppBaseController
{
    public function index(Request $request) {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $poMasters = DB::table('erp_documentapproved')
            ->select(
            'srm_supplier_registration_link.id',
            'erp_documentapproved.documentSystemID',
            'srm_supplier_registration_link.name',
            'srm_supplier_registration_link.email',
            'srm_supplier_registration_link.registration_number',
            'srm_supplier_registration_link.company_id',
            'srm_supplier_registration_link.token',
            'srm_supplier_registration_link.token_expiry_date_time',
            'srm_supplier_registration_link.created_by',
            'srm_supplier_registration_link.updated_by',
            'srm_supplier_registration_link.created_at',
            'srm_supplier_registration_link.updated_at',
            'srm_supplier_registration_link.uuid',
            'srm_supplier_registration_link.supplier_master_id',
            'srm_supplier_registration_link.confirmed_by_emp_id',
            'srm_supplier_registration_link.confirmed_by_name',
            'srm_supplier_registration_link.confirmed_date',
            'erp_documentapproved.documentApprovedID',
            'erp_documentapproved.rollLevelOrder',
            'approvalLevelID',
            'documentSystemCode'
            // 'employees.empName As created_user'
        )->join('employeesdepartments', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

            // 107 mean "Supplier registration" id of document master table id
            $query->whereIn('employeesdepartments.documentSystemID', [107])
                ->where('employeesdepartments.companySystemID', $companyID)
                ->where('employeesdepartments.employeeSystemID', $empID)
                ->where('employeesdepartments.isActive', 1)
                ->where('employeesdepartments.removedYN', 0);
        })->join('srm_supplier_registration_link', function ($query) use ($companyID, $empID) {
            $query->on('erp_documentapproved.documentSystemCode', '=', 'id')
                ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                ->where('srm_supplier_registration_link.company_id', $companyID)
                ->where('srm_supplier_registration_link.approved_yn', 0)
                ->where('srm_supplier_registration_link.confirmed_yn', 1);
        })->where('erp_documentapproved.approvedYN', 0)
            // ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [107])
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $poMasters = $poMasters->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($poMasters)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('documentApprovedID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            //->addColumn('Index', 'Index', "Index")
            ->make(true);
    }

    public function update(Request $request){
        $approve = Helper::approveDocument($request);

        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            return $this->sendResponse(array(), $approve["message"]);
        }
    }
}

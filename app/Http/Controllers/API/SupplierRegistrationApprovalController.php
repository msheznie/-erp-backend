<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Services\SRMService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Throwable;

// supplier KYC status
define('PENDING', 0);
define('SUBMITTED', 1);
define('PENDING_FOR_APPROVAL', 2);
define('APPROVED', 3);
define('REJECT', 4);

class SupplierRegistrationApprovalController extends AppBaseController
{
    private $srmService = null;

    public function __construct(SRMService $srmService)
    {
        $this->srmService = $srmService;
    }

    /**
     * get KYC list
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request) {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = $request->companyId;
        $empID = \Helper::getEmployeeSystemID();

        $suppliersDetail = DB::table('erp_documentapproved')
            ->select(
            'srm_supplier_registration_link.id',
            'erp_documentapproved.documentSystemID',
            'srm_supplier_registration_link.name',
            'srm_supplier_registration_link.approved_yn',
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
                ->where('srm_supplier_registration_link.confirmed_yn', 1);
        })
            // ->join('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [107])
            ->where('erp_documentapproved.companySystemID', $companyID);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $suppliersDetail = $suppliersDetail->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('registration_number', 'LIKE', "%{$search}%");
            });
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $suppliersDetail->where('srm_supplier_registration_link.approved_yn', $input['approved']);
            }
        }

        return \DataTables::of($suppliersDetail)
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

    /**
     * handle KYC Status
     * @param Request $request
     * @throws Throwable
     */
    public function update(Request $request){
        switch ($request->input('mode')){
            case 'approve': {
                $this->approveSupplierKYC($request);
                break;
            }
            case 'reject': {
                $this->rejectSupplierKYC($request);
                break;
            }
            default: {

            }
        }
    }

    /**
     * approve KYC
     * @param $request
     * @return mixed
     * @throws Throwable
     */
    public function approveSupplierKYC($request){
        $approve = Helper::approveDocument($request);

        if (!$approve["success"]) {
            return $this->sendError($approve["message"]);
        } else {
            if($approve['data'] && $approve['data']['numberOfLevels'] == $approve['data']['currentLevel']){
                $response = $this->srmService->callSRMAPIs([
                    'apiKey' => $request->input('api_key'),
                    'request' => 'UPDATE_KYC_STATUS',
                    'extra' => [
                        'status'    => APPROVED,
                        'auth'      => $request->user(),
                        'uuid'      => $request->input('uuid')
                    ]
                ]);

                if($response && $response->success === false) return $this->sendError("Something went wrong!, Supplier status couldn't be updated");
            } elseif ($approve['data'] && $approve['data']['currentLevel'] > 0){
                    $response = $this->srmService->callSRMAPIs([
                        'apiKey' => $request->input('api_key'),
                        'request' => 'UPDATE_KYC_STATUS',
                        'extra' => [
                            'status'    => PENDING_FOR_APPROVAL,
                            'auth'      => $request->user(),
                            'uuid'      => $request->input('uuid')
                        ]
                    ]);

                    if($response && $response->success === false) return $this->sendError("Something went wrong!, Supplier status couldn't be updated");
            }

            return $this->sendResponse(array(), $approve["message"]);
        }
    }

    /**
     * reject KYC
     * @param $request
     * @return mixed
     */
    public function rejectSupplierKYC($request)
    {
        $reject = Helper::rejectDocument($request);

        if (!$reject["success"]) {
            return $this->sendError($reject["message"]);
        } else {
            return $this->sendResponse(array(), $reject["message"]);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Models\Employee;
use App\Models\SRMTenderUserAccess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use App\Models\SrmEmployees;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use App\Services\SrmDocumentModifyService;
use App\Services\SrmCommonService;

class TenderCommitteeController extends AppBaseController
{
    protected $documentModifyService;
    protected $srmCommonService;
    public function __construct(SrmDocumentModifyService $documentModifyService, SrmCommonService $srmCommonService){
        $this->documentModifyService = $documentModifyService;
        $this->srmCommonService = $srmCommonService;
    }
    public function getAll(Request $request) {
        $input = $request->all();
        $company_id = $request['companyId'];
        $emp_id = Auth::user()->employee_id;
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $srm_employees = SrmEmployees::where('company_id',$company_id)->with('employee');

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $srm_employees = $srm_employees->whereHas('employee', function ($query) use ($search){
                $query->where('empFullName', 'like', '%'.$search.'%')
                ->orWhere('empID', 'like', '%'.$search.'%');
            });
        }

        return \DataTables::of($srm_employees)
        ->order(function ($query) use ($input) {
            if (request()->has('order')) {
                if ($input['order'][0]['column'] == 0) {
                    $query->orderBy('id', $input['order'][0]['dir']);
                }
            }
        })
        ->addIndexColumn()
        ->with('orderCondition', $sort)
        ->make(true);

    }
    

    public function assignEmployeesToTenderCommitee(Request $request) {
        $company_id = $request['companyID'];

        $data = [];
        if(count($request['selectedEmployees']) > 0) {
            $emp_ids = collect($request['selectedEmployees'])->pluck('id')->toArray();

            $exists_employees = SrmEmployees::where('company_id',$company_id)->pluck('emp_id')->toArray();
            $result = array_diff($emp_ids,$exists_employees);
            foreach($result as $emp_id) {
                $data[] = [
                    'created_by' => Auth::user()->employee_id,
                    'created_at' => Carbon::now(),
                    'emp_id' => $emp_id,
                    'is_active' => true,
                    'company_id' => $company_id
                ];
            }

            $srm_employees = SrmEmployees::insert($data);

            if($srm_employees) {
                return $this->sendResponse($data,trans('srm_masters.employee_saved_successfully'));
            }

        }else {
            return $this->sendError(trans('srm_masters.employee_not_selected'));
        }
    }

    public function delete(Request $request) {

        $delteRecord = SrmEmployees::where('company_id',$request['companyID'])->where('id',$request['id'])->delete();
        
        if($delteRecord) {
            return $this->sendResponse($delteRecord,trans('srm_masters.data_deleted_successfully'));
        }else {
            return $this->sendError(trans('srm_masters.record_not_found'));
        }
    }

    public function update($id,Request $request) {

        $srm = SrmEmployees::find($id);
        $input = $request['item'];
        unset($input['employee']);
        $srm->update($input);

        return $this->sendResponse($srm,trans('srm_masters.data_updated_successfully'));

    }

    public function getActiveEmployeesForBid(Request $request) {

        $requestData = $this->documentModifyService->checkForEditOrAmendRequest($request['tender_id']);
        $data = $this->srmCommonService->getActiveEmployeesForBid($request, $requestData);
        return $this->sendResponse($data,trans('custom.data_retrieved_successfully'));
    }


}

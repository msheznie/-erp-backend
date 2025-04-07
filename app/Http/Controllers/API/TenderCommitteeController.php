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

class TenderCommitteeController extends AppBaseController
{

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
                return $this->sendResponse($data,'Employee saved successfully');
            }

        }else {
            return $this->sendError('Employee not selected');
        }
    }

    public function delete(Request $request) {

        $delteRecord = SrmEmployees::where('company_id',$request['companyID'])->where('id',$request['id'])->delete();
        
        if($delteRecord) {
            return $this->sendResponse($delteRecord,'Data deleted successfully');
        }else {
            return $this->sendError('Record not found');
        }
    }

    public function update($id,Request $request) {

        $srm = SrmEmployees::find($id);
        $input = $request['item'];
        unset($input['employee']);
        $srm->update($input);

        return $this->sendResponse($srm,'Data updated successfully');

    }

    public function getActiveEmployeesForBid(Request $request) {

        $exisitingEmployeeIDs = SrmTenderBidEmployeeDetails::where('tender_id',$request['tender_id'])->pluck('emp_id')->toArray();
        $srmEmployees = SrmEmployees::where('company_id',$request['companyId'])->where('is_active',true)->whereNotIn('emp_id',$exisitingEmployeeIDs)->whereHas('employee', function ($query) {
            $query->where('empActive', 1)->where('discharegedYN','!=',-1);
        })->with('employee')->get();
        $data['employeeApproval'] = [];
        $data['bidOpeningUserDrop'] = $this->tenderUserAccessData($request['tender_id'],$request['companyId'],1);
        $data['commercialBidOpeningUserDrop'] = $this->tenderUserAccessData($request['tender_id'],$request['companyId'],2);
        $data['supplierRankingUserDrop'] = $this->tenderUserAccessData($request['tender_id'],$request['companyId'],3);
        $data['tenderUserAccessDetails'] = $this->getUserAccessDetails($request['tender_id'],$request['companyId']);

        if($srmEmployees) {
            foreach($srmEmployees as $emp) {
                array_push($data['employeeApproval'],["employeeSystemID"  => $emp->employee->employeeSystemID, "empFullName" => $emp->employee->empID." | ".$emp->employee->empFullName]);
            }
        }

        return $this->sendResponse($data,'Data retrieved successfully');
    }

    public function tenderUserAccessData($tenderId,$companyId,$moduleId)
    {
        $employees = SrmEmployees::select('id', 'emp_id', 'company_id', 'is_active')
            ->whereHas('employee', function ($query) {
                $query->where('empActive', 1)->where('discharegedYN','!=',-1);
            })
            ->with(['employee' => function ($q) {
                $q->select('employeeSystemID', DB::raw("CONCAT(empID, ' | ', empFullName) as empFullDetails"));
            }])
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereDoesntHave('tenderUserAccess', function ($query) use ($tenderId, $companyId, $moduleId) {
                $query->where('tender_id', $tenderId)
                    ->where('company_id', $companyId)
                    ->where('module_id', $moduleId);
            })
            ->get();

        return $employees->pluck('employee')->map(function ($employee) {
            return [
                'employeeSystemID' => $employee->employeeSystemID,
                'employeeFullName' => $employee->empFullDetails,
            ];
        });
    }

    public function getUserAccessDetails($tenderId,$companyId)
    {
        return SRMTenderUserAccess::select('id','tender_id','user_id','company_id','module_id')
            ->with(['employee' => function ($q){
                $q->select('employeeSystemID',DB::raw("CONCAT(empID, ' | ', empFullName) as empFullDetails"));
            }])
            ->where('tender_id',$tenderId)
            ->where('company_id',$companyId)
            ->get();
    }


}

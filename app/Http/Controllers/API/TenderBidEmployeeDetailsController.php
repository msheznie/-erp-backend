<?php

namespace App\Http\Controllers\API;

use App\Models\SRMTenderUserAccess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;
use App\Http\Requests\API\CreateTenderBidEmployeeDetailsAPIRequest;
use App\Http\Requests\API\UserAccessEmployeeRequest;
use App\Services\TenderBidEmployeeService;

class TenderBidEmployeeDetailsController extends AppBaseController
{
    protected $tenderBidEmployeeService;

    public function __construct(TenderBidEmployeeService $tenderBidEmployeeService)
    {
        $this->tenderBidEmployeeService = $tenderBidEmployeeService;
    }
    public function store(CreateTenderBidEmployeeDetailsAPIRequest $request) {
        $input = $request->all();
        $tenderBidEmpCreate = $this->tenderBidEmployeeService->storeTenderBidEmployees($input);
        if($tenderBidEmpCreate['status']){
            return $this->sendResponse([], $tenderBidEmpCreate['message']);
        } else {
            return $this->sendError($tenderBidEmpCreate['message'], $tenderBidEmpCreate['code']);
        }
    }

    public function getEmployees(Request $request) {
        
        $data = SrmTenderBidEmployeeDetails::where('tender_id', $request['tender_id'])->with('employee')->get();
        return $this->sendResponse($data, 'Employee reterived successfully');


    }

    public function deleteEmp(Request $request) {
        $tenderEmployeeDetails = SrmTenderBidEmployeeDetails::where('tender_id',$request['tender_id'])->where('emp_id',$request['emp_id'])->first();
        $result = SrmTenderBidEmployeeDetails::find($tenderEmployeeDetails->id);

        if (empty($result)) {
            return $this->sendError('Employee Details not found');
        }
        $result->delete();

        return $this->sendResponse($tenderEmployeeDetails, 'Employee deleted successfully');
    }

    public function getEmployeesApproval(Request $request) {
        
        $data = SrmTenderBidEmployeeDetails::where('tender_id', $request['tender_id'])->where('status', true)->count();
        return $this->sendResponse($data, 'Employee reterived successfully');

    }

    public function getEmployeesCommercialApproval(Request $request) {
        
        $data = SrmTenderBidEmployeeDetails::where('tender_id', $request['tender_id'])->where('commercial_eval_status', true)->count();
        return $this->sendResponse($data, 'Employee reterived successfully');

    }

    public function getEmployeesTenderAwardinglApproval(Request $request) {
        
        $data = SrmTenderBidEmployeeDetails::where('tender_id', $request['tender_id'])->where('tender_award_commite_mem_status', true)->count();
        return $this->sendResponse($data, 'Employee reterived successfully');

    }

    public function removeTenderUserAccess(Request $request) {
        $input = $request->all();
        $id = $input['id'];
        try {
            $tenderUser = SRMTenderUserAccess::find($id);
            if (empty($tenderUser)){
                return $this->sendError('User access details not found');
            }
            $tenderUser->delete();
            return $this->sendResponse([], 'User access details deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function addUserAccessEmployee(UserAccessEmployeeRequest $request){
        $input = $request->all();

        $createUserAccess = $this->tenderBidEmployeeService->addUserAccessEmployees($input);
        if($createUserAccess['status']){
            return $this->sendResponse([], $createUserAccess['message']);
        } else {
            return $this->sendError($createUserAccess['message'], $createUserAccess['code']);
        }
    }

    public function deleteAllBidMinimumApprovalDetails(Request $request){
        $deleteEmployees = $this->tenderBidEmployeeService->deleteAllBidMinimumApprovalDetails($request);
        if($deleteEmployees['status']){
            return $this->sendResponse([], $deleteEmployees['message']);
        } else {
            return $this->sendError($deleteEmployees['message'], $deleteEmployees['code']);
        }
    }
    public function deleteAllTenderUserAccess(Request $request)
    {
        $deleteAccessUsers = $this->tenderBidEmployeeService->deleteAllTenderUserAccess($request);
        if($deleteAccessUsers['status']){
            return $this->sendResponse([], $deleteAccessUsers['message']);
        } else {
            return $this->sendError($deleteAccessUsers['message'], $deleteAccessUsers['code']);
        }
    }
}

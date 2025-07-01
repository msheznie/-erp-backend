<?php

namespace App\Http\Controllers\API;

use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\Models\SRMTenderUserAccess;
use App\Models\SrmTenderUserAccessEditLog;
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
use App\Services\SrmDocumentModifyService;

class TenderBidEmployeeDetailsController extends AppBaseController
{
    protected $tenderBidEmployeeService;
    protected $srmDocumentModifyService;

    public function __construct(TenderBidEmployeeService $tenderBidEmployeeService, SrmDocumentModifyService $srmDocumentModifyService)
    {
        $this->tenderBidEmployeeService = $tenderBidEmployeeService;
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }
    public function store(CreateTenderBidEmployeeDetailsAPIRequest $request) {
        $input = $request->all();
        $tenderBidEmpCreate = $this->tenderBidEmployeeService->storeTenderBidEmployees($input);
        if($tenderBidEmpCreate['success']){
            return $this->sendResponse([], $tenderBidEmpCreate['message']);
        } else {
            return $this->sendError($tenderBidEmpCreate['message'], $tenderBidEmpCreate['code']);
        }
    }

    public function getEmployees(Request $request) {
        $data = $this->tenderBidEmployeeService->getEmployees($request);
        return $this->sendResponse($data, 'Employee reterived successfully');
    }

    public function deleteEmp(Request $request) {
        try{
            $response = $this->tenderBidEmployeeService->deleteTenderBidEmployees($request);
            if(!$response['success']){
                return $this->sendError($response['message']);
            } else {
                return $this->sendResponse([], $response['message']);
            }
        } catch(\Exception $exception){
            return $this->sendError('Unexpected Error: ' . $exception->getMessage());
        }
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
        try {
            $response = $this->tenderBidEmployeeService->removeTenderUserAccess($input);
            if(!$response['success']){
                return $this->sendError($response['message']);
            } else {
                return $this->sendResponse([], 'User access details deleted successfully');
            }
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

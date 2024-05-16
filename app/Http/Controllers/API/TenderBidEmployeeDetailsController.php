<?php

namespace App\Http\Controllers\API;

use App\Models\SRMTenderUserAccess;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TenderBidEmployeeDetailsController extends AppBaseController
{
    public function store(Request $request) {
        $input = $request->all();

 
        $validator = \Validator::make($input['data'],[
            'emp_id' => 'required',
            'tender_id' => 'required',
        ]);
        if ($validator->fails()) {           
            return $this->sendError($validator->errors()->first());
        }

        $result = SrmTenderBidEmployeeDetails::create($request['data']);
        return $this->sendResponse($result, 'Employee saved successfully');

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

    public function addUserAccessEmployee(Request $request){
        $input = $request->all();

        $validateInput = $this->validateUserAccessInputs($input);
        if(!$validateInput['status']) {
            return $this->sendError($validateInput['message'],$validateInput['code']);
        }

        DB::beginTransaction();
        try{
            $userId = $input['userId'];
            $moduleId = $input['moduleId'];
            $tenderId = $input['tenderId'];
            $companyId = $input['companyId'];

            $data = [
                'tender_id'=> $tenderId,
                'user_id'=> $userId,
                'module_id'=> $moduleId,
                'company_id'=>$companyId
            ];
            SRMTenderUserAccess::insert($data);
            DB::commit();
            return $this->sendResponse([], 'Employee added successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function validateUserAccessInputs($input)
    {
        $messages = array(
            'userId.required' => 'Employee field is required.',
            'moduleId.required' => 'Module id field is required.',
            'tenderId.required' => 'Tender id is required.',
            'companyId.required' => 'Company id is required.'
        );

        $validator = \Validator::make($input, [
            'userId' => 'required',
            'moduleId' => 'required',
            'tenderId' => 'required',
            'companyId' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return ['status'=> false, 'message'=> $validator->messages(), 'code'=> 422];
        }

        return ['status'=> true, 'message'=> 'Success'];
    }

}

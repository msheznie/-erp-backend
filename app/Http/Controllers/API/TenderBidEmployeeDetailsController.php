<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SrmTenderBidEmployeeDetails;
use App\Http\Controllers\AppBaseController;
use Carbon\Carbon; 

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

}

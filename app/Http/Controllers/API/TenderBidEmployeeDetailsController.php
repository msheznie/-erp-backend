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

        SrmTenderBidEmployeeDetails::insert($request['data']);
        return $this->sendResponse([], 'Employee saved successfully');

    }

    public function getEmployees(Request $request) {
        
        $data = SrmTenderBidEmployeeDetails::where('tender_id', $request['tender_id'])->with('employee')->get();
        return $this->sendResponse($data, 'Employee reterived successfully');


    }

    public function deleteEmp(Request $request) {
        $data = SrmTenderBidEmployeeDetails::where('tender_id',$request['tender_id'])->where('emp_id',$request['emp_id'])->delete();
        return $this->sendResponse($data, 'Employee deleted successfully');
    }
}

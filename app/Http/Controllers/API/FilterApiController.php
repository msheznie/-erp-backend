<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Employee;
use Illuminate\Http\Request;

class FilterApiController extends AppBaseController
{
    public function getAllCreatedByEmployees(Request  $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];

        $employees = Employee::select(['employeeSystemID','empName'])->where('empCompanySystemID',$companyId)->where('empActive',1)->get();


        $output = array(
            'employees' => $employees
        );
        return $this->sendResponse($output, 'Record retrieved successfully');


    }
}

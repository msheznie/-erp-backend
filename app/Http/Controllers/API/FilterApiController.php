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
        $documentSystemId = $input['documentSystemID'];

        $employees = Employee::select(['employeeSystemID','empName'])->where('empCompanySystemID',$companyId)->where('empActive',1)->get();
        //scenario
        // 1. employee Steve registered in company ASASS  - should not load here
        // 2. employee Steve created document in GuTech - should load here

        $query = Employee::where('empActive', 1)
            ->select(['employeeSystemID', 'empName']);


        switch ($documentSystemId) {
            case 3:
                $query->whereHas('grv', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 61:
                $query->whereHas('payment_voucher', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 11:
                $query->whereHas('supplier_invoice', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 20:
                $query->whereHas('customer_invoice', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 22:
                $query->whereHas('asset_costing', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 17:
                $query->whereHas('jv', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 21:
                $query->whereHas('receiptVoucher', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
        }

        $employees = $query->get();

        $output = array(
            'employees' => $employees
        );
        return $this->sendResponse($output, 'Record retrieved successfully');


    }
}

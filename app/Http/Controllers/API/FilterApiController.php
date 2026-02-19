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
        $companyId = data_get($input, 'companyId');
        $documentSystemId = data_get($input, 'documentSystemID');

        $employees = Employee::select(['employeeSystemID','empName'])->where('empCompanySystemID',$companyId)->where('empActive',1)->get();
        //scenario
        // 1. employee Steve registered in company ASASS  - should not load here
        // 2. employee Steve created document in GuTech - should load here

        $query = Employee::where('empActive', 1)
            ->select(['employeeSystemID', 'empName']);


        switch ($documentSystemId) {
            case 1:
                $query->whereHas('purchase_request', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 2:
                $query->whereHas('purchase_order', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 3:
                $query->whereHas('grv', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 6:
                $query->whereHas('expense_claim', function($q) use ($companyId) {
                    $q->where('companyID', $companyId);
                });
                break;
            case 5:
                $query->whereHas('work_order', function($q) use ($companyId) {
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
            case 28:
                $query->whereHas('monthly_addition', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 56:
                $query->whereHas('supplier_master', function($q) use ($companyId) {
                    $q->where('primaryCompanySystemID', $companyId);
                });
                break;
            case 57:
                $query->whereHas('item_master', function($q) use ($companyId) {
                    $q->where('primaryCompanySystemID', $companyId);
                });
                break;
            case 58:
                $query->whereHas('customer_master', function($q) use ($companyId) {
                    $q->where('primaryCompanySystemID', $companyId);
                });
                break;
            case 132:
                $query->whereHas('segment', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 9:
                $query->whereHas('material_request', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 8:
                $query->whereHas('material_issue', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 12:
                $query->whereHas('material_return', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 13:
                $query->whereHas('stock_transfer', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 10:
                $query->whereHas('stock_receive', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 7:
                $query->whereHas('stock_adjustment', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 24:
                $query->whereHas('purchase_return', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 97:
                $query->whereHas('stock_count', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 65:
                $query->whereHas('budget_master', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 15:
                $query->whereHas('debit_note', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 19:
                $query->whereHas('credit_note', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 50:
                $query->whereHas('work_request', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 51:
                $query->whereHas('direct_request', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 52:
                $query->whereHas('direct_order', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 39:
                $query->whereHas('batch_submission', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 67:
                $query->whereHas('quotation', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 68:
                $query->whereHas('sales_order', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 71:
                $query->whereHas('delivery_order', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 87:
                $query->whereHas('sales_return', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 69:
                $query->whereHas('console_jv', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 119:
                $query->whereHas('recurring_voucher', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 62:
                $query->whereHas('bank_reconciliation', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 64:
                $query->whereHas('bank_transfer', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 46:
                $query->whereHas('budget_transfer', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 102:
                $query->whereHas('budget_addition', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 23:
                $query->whereHas('asset_depreciation', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 41:
                $query->whereHas('asset_disposal', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 63:
                $query->whereHas('asset_capitalization', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 99:
                $query->whereHas('asset_verification', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
                break;
            case 17:
                $query->whereHas('jv', function($q) use ($companyId) {
                    $q->where('companySystemID', $companyId);
                });
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
        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));


    }
}

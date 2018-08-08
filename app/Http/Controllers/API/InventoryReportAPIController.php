<?php
/**
 * =============================================
 * -- File Name : InventoryReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 8 - August 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 * -- Date: 04-June 2018 By: Mubashir Description:
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\WarehouseMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InventoryReportAPIController extends AppBaseController
{
    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'INVST':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'ST') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required|date',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'warehouse' => 'required',
                        'document' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                }
                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }


    public function getInventoryFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $warehouse = WarehouseMaster::whereIN('companySystemID', $companiesByGroup)->where('isActive', 1)->get();
        $document = DocumentMaster::where('departmentSystemID', 10)->get();

        $output = array(
            'warehouse' => $warehouse,
            'document' => $document,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'INVST': //Stock Transaction Report
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ST') { //Stock Transaction Report
                    $input = $request->all();
                    if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                        $sort = 'asc';
                    } else {
                        $sort = 'desc';
                    }

                    $startDate = new Carbon($request->fromDate);
                    $startDate = $startDate->format('Y-m-d');

                    $endDate = new Carbon($request->toDate);
                    $endDate = $endDate->format('Y-m-d');

                    $companyID = "";
                    $checkIsGroup = Company::find($request->companySystemID);
                    if ($checkIsGroup->isGroup) {
                        $companyID = \Helper::getGroupCompany($request->companySystemID);
                    } else {
                        $companyID = (array)$request->companySystemID;
                    }

                    $warehouse = (array)$request->warehouse;
                    $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

                    $document = (array)$request->document;
                    $document = collect($document)->pluck('documentSystemID');


                    $output = DB::table("erp_itemledger")->selectRaw("erp_itemledger.itemLedgerAutoID,
	erp_itemledger.companySystemID,
	erp_itemledger.companyID,
	erp_itemledger.serviceLineCode,
	erp_itemledger.documentID,
	erp_documentmaster.documentDescription,
	erp_itemledger.documentSystemCode,
	erp_itemledger.documentCode,
	erp_itemledger.referenceNumber,
	erp_itemledger.wareHouseSystemCode,
	warehousemaster.wareHouseDescription,
	erp_itemledger.itemSystemCode,
	erp_itemledger.itemPrimaryCode,
	itemassigned.secondaryItemCode as partNumber,
	erp_itemledger.itemDescription,
	erp_itemledger.unitOfMeasure as UOM,
	erp_itemledger.inOutQty,
	erp_itemledger.wacRpt as cost,
	erp_itemledger.comments,
	erp_itemledger.transactionDate,
	units.UnitShortCode,
	employees.empName,
	itemassigned.maximunQty,
	itemassigned.minimumQty,
	financeitemcategorysub.financeGLcodePL as AccountCode,
	chartofaccounts.AccountDescription")
                        ->join('units', 'erp_itemledger.unitOfMeasure', '=', 'units.UnitID')
                        ->leftJoin('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=','warehousemaster.wareHouseSystemCode')
                        ->join('employees', 'erp_itemledger.createdUserID', '=', 'employees.empID')
                        ->leftJoin('erp_documentmaster', 'erp_itemledger.documentID', '=', 'erp_documentmaster.documentID')
                        ->leftJoin('itemassigned', function ($query) {
                            $query->on('erp_itemledger.itemSystemCode', '=', 'itemassigned.itemCodeSystem');
                            $query->on('erp_itemledger.companyID', '=', 'itemassigned.companyID');
                        })
                        ->leftJoin('financeitemcategorysub', function ($query) {
                            $query->on('itemassigned.financeCategoryMaster', '=', 'financeitemcategorysub.itemCategoryID');
                            $query->on('itemassigned.financeCategorySub', '=', 'financeitemcategorysub.itemCategorySubID');
                        })
                        ->leftJoin('chartofaccounts', 'financeitemcategorysub.financeGLcodePL', '=', 'chartofaccounts.AccountCode')
                        ->whereIN('erp_itemledger.companySystemID', $companyID)
                        ->whereIN('erp_itemledger.wareHouseSystemCode', $warehouse)
                        ->whereIN('erp_itemledger.documentSystemID', $document)
                        ->whereBetween(DB::raw("DATE(transactionDate)"), array($startDate, $endDate))
                        ->orderBy('erp_itemledger.transactionDate', 'ASC');


                    return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    $query->orderBy('itemLedgerAutoID', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                      /*  ->with('orderCondition', $sort)*/
                        ->make(true);
                }
                break;
            default:
                return $this->sendError('No report ID found');

        }
    }


}

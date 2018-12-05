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
use App\Models\ErpItemLedger;
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
                } else if ($reportTypeID == 'SA') {
                    $validator = \Validator::make($request->all(), [
                        'asOfDate' => 'required|date',
                        'warehouse' => 'required',
                        'currencyID' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                }
                if ($validator->fails()) {
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

        $warehouse = WarehouseMaster::whereIN('companySystemID', $companiesByGroup)->get();
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
                        ->leftJoin('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
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
                        ->where('itemassigned.financeCategoryMaster', 1)
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
                } else if ($reportTypeID == 'SA') {
                    $input = $request->all();
                    if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                        $sort = 'asc';
                    } else {
                        $sort = 'desc';
                    }

                    $output = $this->stockAgingQry($input);
                    return $this->sendResponse($output, 'Items retrieved successfully');
                }
                break;
            default:
                return $this->sendError('No report ID found');

        }
    }


    public function stockAgingQry($request)
    {

        $date = new Carbon($request['asOfDate']);
        $date = $date->format('Y-m-d');

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $input = $request;
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }

        $aging = ['0-30', '31-60', '61-90', '91-120', '121-365', '366-730', '> 730'];
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "SUM(if(ItemLedger.ageDays   > " . 730 . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(ItemLedger.ageDays >= " . $list[0] . " AND ItemLedger.ageDays <= " . $list[1] . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                }
                $c++;
            }
        }

        $agingField .= "if(ItemLedger.ageDays <= 0,ItemLedger.Qty,0) as `current`";

        //DB::enableQueryLog();
        $sql = "SELECT * FROM (SELECT
                ItemLedger.companySystemID,
                ItemLedger.companyID,
                ItemLedger.itemSystemCode,
                ItemLedger.itemPrimaryCode,
                ItemLedger.itemDescription,
                ItemLedger.unitOfMeasure,
                ItemLedger.secondaryItemCode,
                ItemLedger.UnitShortCode,
                ItemLedger.categoryDescription,
                ItemLedger.transactionDate,
                ItemLedger.LocalCurrencyDecimals,
                ItemLedger.RptCurrencyDecimals,
                sum( Qty ) AS Qty,
                LocalCurrency,
            IF
                ( sum( localAmount ) / sum( Qty ) IS NULL, 0, sum( localAmount ) / sum( Qty ) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / sum( Qty ) IS NULL, 0, sum( rptAmount ) / sum( Qty ) ) AS WACRpt,
                sum( rptAmount ) AS WacRptAmount,
                " . $agingField . " 
            FROM
                (
            SELECT
                erp_itemledger.companySystemID,
                erp_itemledger.companyID,
                erp_itemledger.documentSystemID,
                erp_itemledger.documentSystemCode,
                erp_itemledger.itemSystemCode,
                erp_itemledger.itemPrimaryCode,
                erp_itemledger.itemDescription,
                erp_itemledger.unitOfMeasure,
                erp_itemledger.transactionDate,
                financeitemcategorysub.categoryDescription,
                itemmaster.secondaryItemCode,
                units.UnitShortCode,
                erp_itemledger.inOutQty AS Qty,
                currencymaster.CurrencyName AS LocalCurrency,
                erp_itemledger.inOutQty * erp_itemledger.wacLocal AS localAmount,
                currencymaster_1.CurrencyName AS RepCurrency,
                erp_itemledger.inOutQty * erp_itemledger.wacRpt AS rptAmount,
                currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
                currencymaster_1.DecimalPlaces AS RptCurrencyDecimals,
                DATEDIFF('" . $date . "',DATE(erp_itemledger.transactionDate)) as ageDays
            FROM
                `erp_itemledger`
                INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
                INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
                LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
                LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
                LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID` 
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND itemmaster.financeCategoryMaster = 1 
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
               
                ) AS ItemLedger 
            GROUP BY
                ItemLedger.companySystemID,
                ItemLedger.itemSystemCode) as grandFinal";
        $items = DB::select($sql);


        $issuedSql = "SELECT
                erp_itemledger.itemSystemCode,
                SUM(erp_itemledger.inOutQty) AS Qty
            FROM
                `erp_itemledger`
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
                AND erp_itemledger.inOutQty < 0
        
            GROUP BY
                erp_itemledger.itemSystemCode";


        $issuedItems = DB::select($issuedSql);

        foreach ($items as $item) {

            $issuedQty = 0;
            foreach ($issuedItems as $issue) {
                if ($issue->itemSystemCode == $item->itemSystemCode) {
                    $issuedQty = abs($issue->Qty);
                    break;
                }
            }

            if ($issuedQty > 0 && $item->case7 > 0) {
                if ($item->case7 >= $issuedQty) {
                    $item->case7 = $item->case7 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case7;
                    $item->case7 = 0;
                }
            }

            if ($issuedQty > 0 && $item->case6 > 0) {
                if ($item->case6 >= $issuedQty) {
                    $item->case6 = $item->case6 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case6;
                    $item->case6 = 0;
                }
            }

            if ($issuedQty > 0 && $item->case5 > 0) {
                if ($item->case5 >= $issuedQty) {
                    $item->case5 = $item->case5 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case5;
                    $item->case5 = 0;
                }
            }

            if ($issuedQty > 0 && $item->case4 > 0) {
                if ($item->case4 >= $issuedQty) {
                    $item->case4 = $item->case4 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case4;
                    $item->case4 = 0;
                }
            }

            if ($issuedQty > 0 && $item->case3 > 0) {
                if ($item->case3 >= $issuedQty) {
                    $item->case3 = $item->case3 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case3;
                    $item->case3 = 0;
                }
            }

            if ($issuedQty > 0 && $item->case2 > 0) {
                if ($item->case2 >= $issuedQty) {
                    $item->case2 = $item->case2 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case2;
                    $item->case2 = 0;
                }
            }

            if ($issuedQty > 0 && $item->case1 > 0) {
                if ($item->case1 >= $issuedQty) {
                    $item->case1 = $item->case1 - $issuedQty;
                    $issuedQty = 0;
                } else {
                    $issuedQty = $issuedQty - $item->case1;
                    $item->case1 = 0;
                }
            }

            $item->issuedQty = $issuedQty;
        }

        $finalArray = array();
        if (!empty($items)) {
            foreach ($items as $element) {
                $finalArray[$element->categoryDescription][] = $element;
            }
        }

        $GrandWacLocal = collect($items)->pluck('WacLocalAmount')->toArray();
        $GrandWacLocal = array_sum($GrandWacLocal);

        $GrandWacRpt = collect($items)->pluck('WacRptAmount')->toArray();
        $GrandWacRpt = array_sum($GrandWacRpt);

        $output = array(
            'categories' => $finalArray,
            'grandWacLocal' => $GrandWacLocal,
            'grandWacRpt' => $GrandWacRpt
        );


        return $output;

    }

    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'INVST': //Stock Transaction Report
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ST') { //Stock Transaction Report
                    $type = $request->type;
                    $input = $request->all();

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
                        ->leftJoin('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
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
                        ->where('itemassigned.financeCategoryMaster', 1)
                        ->whereBetween(DB::raw("DATE(transactionDate)"), array($startDate, $endDate))
                        ->orderBy('erp_itemledger.transactionDate', 'ASC')->get();

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $x++;
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Trans Date'] = \Helper::dateFormat($val->transactionDate);
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Warehouse'] = $val->wareHouseDescription;
                            $data[$x]['Ref Number'] = $val->referenceNumber;
                            $data[$x]['Processed By'] = $val->empName;
                            $data[$x]['Item Code'] = $val->itemPrimaryCode;
                            $data[$x]['Item Desc'] = $val->itemDescription;
                            $data[$x]['UOM'] = $val->UnitShortCode;
                            $data[$x]['Part #'] = $val->partNumber;
                            $data[$x]['Qty'] = $val->inOutQty;
                            $data[$x]['Cost (USD)'] = round($val->cost, 2);
                            $data[$x]['Account Code'] = $val->AccountCode;
                            $data[$x]['Account Desc'] = $val->AccountDescription;
                        }
                    }

                    $csv = \Excel::create('stock_transaction', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    return $this->sendResponse(array(), 'successfully export');

                }else if ($reportTypeID == 'SA') { //Stock Aging Report

                    $type = $request->type;
                    $input = $request->all();
                    $input = $this->convertArrayToSelectedValue($input, array('currencyID'));
                    $output = $this->stockAgingQry($input);
                    $data = array();
                    if ($output) {
                        $x = 0;

                        foreach ($output['categories'] as $key ) {
                            foreach ($key as $val) {
                                $x++;

                                $data[$x]['Item Code'] = $val->itemPrimaryCode;
                                $data[$x]['Item Description'] = $val->itemDescription;
                                $data[$x]['Category'] = $val->categoryDescription;
                                $data[$x]['UOM'] = $val->UnitShortCode;
                                $data[$x]['Qty'] =  $val->Qty;

                                if($input['currencyID'] == 1){
                                    $data[$x]['WAC Local'] = number_format($val->WACLocal,$val->LocalCurrencyDecimals);
                                    $data[$x]['Local Amount'] = number_format($val->WacLocalAmount,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['WAC Rep'] = number_format($val->WACRpt,$val->RptCurrencyDecimals);
                                    $data[$x]['Rep Amount'] = number_format($val->WacRptAmount,$val->RptCurrencyDecimals);
                                }

                                $data[$x]['<=30 (Qty)'] = $val->case1;
                                if($input['currencyID'] == 1){
                                    $data[$x]['<=30 (Value)'] = number_format($val->WACLocal * $val->case1,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['<=30 (Value)'] = number_format($val->WACRpt * $val->case1,$val->RptCurrencyDecimals);
                                }


                                $data[$x]['31 to 60 (Qty)'] = $val->case2;
                                if($input['currencyID'] == 1){
                                    $data[$x]['31 to 60 (Value)'] = number_format($val->WACLocal * $val->case2,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['31 to 60 (Value)'] = number_format($val->WACRpt * $val->case2,$val->RptCurrencyDecimals);
                                }

                                $data[$x]['61 to 90 (Qty)'] = $val->case3;
                                if($input['currencyID'] == 1){
                                    $data[$x]['61 to 90 (Value)'] = number_format($val->WACLocal * $val->case3,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['61 to 90 (Value)'] = number_format($val->WACRpt * $val->case3,$val->RptCurrencyDecimals);
                                }

                                $data[$x]['91 to 120 (Qty)'] = $val->case4;
                                if($input['currencyID'] == 1){
                                    $data[$x]['91 to 120 (Value)'] = number_format($val->WACLocal * $val->case4,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['91 to 120 (Value)'] = number_format($val->WACRpt * $val->case4,$val->RptCurrencyDecimals);
                                }

                                $data[$x]['121 to 365 (Qty)'] = $val->case5;
                                if($input['currencyID'] == 1){
                                    $data[$x]['121 to 365 (Value)'] = number_format($val->WACLocal * $val->case5,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['121 to 365 (Value)'] = number_format($val->WACRpt * $val->case5,$val->RptCurrencyDecimals);
                                }

                                $data[$x]['366 to 730 (Qty)'] = $val->case6;
                                if($input['currencyID'] == 1){
                                    $data[$x]['366 to 730 (Value)'] = number_format($val->WACLocal * $val->case6,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['366 to 730 (Value)'] = number_format($val->WACRpt * $val->case6,$val->RptCurrencyDecimals);
                                }

                                $data[$x]['Over 730 (Qty)'] = $val->case7;
                                if($input['currencyID'] == 1){
                                    $data[$x]['Over 730 (Value)'] = number_format($val->WACLocal * $val->case7,$val->LocalCurrencyDecimals);
                                }else if($input['currencyID'] == 2){
                                    $data[$x]['Over 730 (Value)'] = number_format($val->WACRpt * $val->case7,$val->RptCurrencyDecimals);
                                }
                            }
                        }
                    }

                    $csv = \Excel::create('stock_aging', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download('csv');

                    return $this->sendResponse(array(), 'successfully export');
                }
                break;
            default:
                return $this->sendError('No report ID found');

        }
    }


}

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
use App\Models\SegmentMaster;
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
                        'reportTypeID' => 'required'
                    ]);
                }
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'INVSD':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'SD') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required|date',
                        'warehouse' => 'required',
                        'segment' => 'required',
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
        $segment = SegmentMaster::ofCompany($companiesByGroup)->get();

        $output = array(
            'warehouse' => $warehouse,
            'document' => $document,
            'segment' => $segment,
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
                                    (erp_itemledger.inOutQty* erp_itemledger.wacRpt) as totalCost,
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
                    $input = $this->convertArrayToSelectedValue($input, array('currencyID','reportCategory'));
                    $input['reportCategory'] = isset($input['reportCategory'])?$input['reportCategory']:1;

                    $output = $this->stockAgingQry($input, 0);
                    return $this->sendResponse($output, 'Items retrieved successfully');
                }
                break;
            case 'INVSD':
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'SD') {
                    $output = $this->stockDetailQry($request);
                    return $this->sendResponse($output, 'Items retrieved successfully');
                }
                break;
            default:
                return $this->sendError('No report ID found');

        }
    }


    public function stockAgingQry($request, $forExcel = 0)
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

        if($input['reportCategory'] == 2){
            $aging = ['0-365', '366-730', '730-1095', '1096-1460', '1461-1825', '> 1826‬'];
        }

        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c && $input['reportCategory'] == 1) {
                    $agingField .= "SUM(if(ItemLedger.ageDays   > " . 730 . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                }
                else if($count == $c && $input['reportCategory'] == 2){
                    $agingField .= "SUM(if(ItemLedger.ageDays   > " . 1826 . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                }
                else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(ItemLedger.ageDays > " . $list[0] . " AND ItemLedger.ageDays <= " . $list[1] . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                }
                $c++;
            }
        }

        $agingField .= "if(ItemLedger.ageDays <= 0,ItemLedger.Qty,0) as `current`";

        $groupByCompanyPlus = "";
        $groupByCompanyMinus = "";

        if ($forExcel) {
            $groupByCompanyPlus = ",ItemLedger.companySystemID";
            $groupByCompanyMinus = ",erp_itemledger.companySystemID";
        }

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
                ItemLedger.itemMovementCategory,
                ItemLedger.movementCatDescription,
                ItemLedger.categoryDescription,
                ItemLedger.transactionDate,
                ItemLedger.LocalCurrencyDecimals,
                ItemLedger.RptCurrencyDecimals,
                round(sum(Qty),3) AS Qty,
                LocalCurrency,
            IF
                ( sum( localAmount ) / round(sum(Qty),3) IS NULL, 0, sum( localAmount ) / round(sum(Qty),3) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / round(sum(Qty),3) IS NULL, 0, sum( rptAmount ) / round(sum(Qty),3) ) AS WACRpt,
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
                itemassigned.itemMovementCategory,
                itemmovementcategory.description as movementCatDescription,
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
                LEFT JOIN `itemassigned` ON `erp_itemledger`.`itemSystemCode` = `itemassigned`.`itemCodeSystem` AND `erp_itemledger`.`companySystemID` = `itemassigned`.`companySystemID`
                LEFT JOIN `itemmovementcategory` ON `itemmovementcategory`.`id` = `itemassigned`.`itemMovementCategory`
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
                ItemLedger.itemSystemCode" . $groupByCompanyPlus . ") as grandFinal";
        $items = DB::select($sql);


        $issuedSql = "SELECT
                erp_itemledger.itemSystemCode,
                erp_itemledger.companySystemID,
                SUM(erp_itemledger.inOutQty) AS Qty
            FROM
                `erp_itemledger`
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
                AND erp_itemledger.inOutQty < 0
            GROUP BY
                erp_itemledger.itemSystemCode" . $groupByCompanyMinus;


        $issuedItems = DB::select($issuedSql);

        foreach ($items as $item) {

            $issuedQty = 0;
            foreach ($issuedItems as $issue) {
                if ($issue->itemSystemCode == $item->itemSystemCode && (($issue->companySystemID == $item->companySystemID && $forExcel) || !$forExcel)) {
                    $issuedQty = abs($issue->Qty);
                    break;
                }
            }

            if ($issuedQty > 0 && $input['reportCategory'] == 1 && $item->case7 > 0) {
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

    public function stockDetailQry($request)
    {
        $input = $request->all();
        $date = new Carbon($request->date);
        $date = $date->format('Y-m-d');

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $warehouse = [];
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }
        $segment = [];
        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');
        }
        //DB::enableQueryLog();
        $sql = "SELECT
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
                round(sum(Qty),3) AS Qty,
                ItemLedger.minimumQty,               
                ItemLedger.maximunQty,      
                LocalCurrency,
            IF
                ( sum( localAmount ) / round(sum(Qty),3) IS NULL, 0, sum( localAmount ) / round(sum(Qty),3) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / round(sum(Qty),3) IS NULL, 0, sum( rptAmount ) / round(sum(Qty),3) ) AS WACRpt,
                sum( rptAmount ) AS WacRptAmount,
                round(lastRDate.inOutQty,2) as lastReceiptQty,            
                lastRDate.transactionDate as lastReceiptDate, 
                round(lastIDate.inOutQty,2) as lastIssuedQty,            
                lastIDate.transactionDate as lastIssuedDate
              
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
                round( erp_itemledger.inOutQty, 2 ) AS Qty,
                currencymaster.CurrencyCode AS LocalCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
                currencymaster_1.CurrencyCode AS RepCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
                currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
                currencymaster_1.DecimalPlaces AS RptCurrencyDecimals,               
                itemassigned.minimumQty as minimumQty,               
                itemassigned.maximunQty as maximunQty      
            FROM
                `erp_itemledger`
                INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
                INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
                LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
                LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
                LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID` 
                LEFT JOIN `itemassigned` ON `erp_itemledger`.`itemSystemCode` = `itemassigned`.`itemCodeSystem` AND itemassigned.companySystemID = erp_itemledger.companySystemID
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                AND itemmaster.financeCategoryMaster = 1 
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
                ) AS ItemLedger 
                 LEFT JOIN (SELECT
	erp_itemledger.transactionDate,
	erp_itemledger.itemSystemCode,
	round( erp_itemledger.inOutQty, 2 ) AS inOutQty
FROM
	(
	( SELECT MAX( itemLedgerAutoID ) AS itemLedgerAutoID, itemSystemCode FROM erp_itemledger WHERE documentSystemID = 3 GROUP BY itemSystemCode ) a
	LEFT JOIN erp_itemledger ON a.itemLedgerAutoID = erp_itemledger.itemLedgerAutoID 
	) ) lastRDate ON lastRDate.itemSystemCode =  ItemLedger.itemSystemCode
	LEFT JOIN (SELECT
	erp_itemledger.transactionDate,
	erp_itemledger.itemSystemCode,
	round( erp_itemledger.inOutQty, 2 ) AS inOutQty
FROM
	(
	( SELECT MAX( itemLedgerAutoID ) AS itemLedgerAutoID, itemSystemCode FROM erp_itemledger WHERE documentSystemID = 8 GROUP BY itemSystemCode ) a
	LEFT JOIN erp_itemledger ON a.itemLedgerAutoID = erp_itemledger.itemLedgerAutoID 
	) ) lastIDate ON lastIDate.itemSystemCode =  ItemLedger.itemSystemCode
            GROUP BY
                ItemLedger.itemSystemCode";
        $items = DB::select($sql);
        //dd(DB::getQueryLog());
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
            'date' => $date,
            'subCompanies' => $subCompanies,
            'grandWacLocal' => $GrandWacLocal,
            'grandWacRpt' => $GrandWacRpt,
            'warehouse' => $request->warehouse
        );

        return $output;
    }


    public function stockDetailCompanyQry($request)
    {
        $input = $request->all();
        $date = new Carbon($request->date);
        $date = $date->format('Y-m-d');

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $warehouse = [];
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }
        $segment = [];
        if (array_key_exists('segment', $input)) {
            $segment = (array)$input['segment'];
            $segment = collect($segment)->pluck('serviceLineSystemID');
        }
        //DB::enableQueryLog();
        $sql = "SELECT
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
                round(sum(Qty),3) AS Qty,
                ItemLedger.minimumQty,               
                ItemLedger.maximunQty,      
                LocalCurrency,
            IF
                ( sum( localAmount ) / round(sum(Qty),3) IS NULL, 0, sum( localAmount ) / round(sum(Qty),3) ) AS WACLocal,
                sum( localAmount ) AS WacLocalAmount,
                RepCurrency,
            IF
                ( sum( rptAmount ) / round(sum(Qty),3) IS NULL, 0, sum( rptAmount ) / round(sum(Qty),3) ) AS WACRpt,
                sum( rptAmount ) AS WacRptAmount,
                round(lastRDate.inOutQty,2) as lastReceiptQty,            
                lastRDate.transactionDate as lastReceiptDate, 
                round(lastIDate.inOutQty,2) as lastIssuedQty,            
                lastIDate.transactionDate as lastIssuedDate
              
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
                round( erp_itemledger.inOutQty, 2 ) AS Qty,
                currencymaster.CurrencyCode AS LocalCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacLocal, 3 ) AS localAmount,
                currencymaster_1.CurrencyCode AS RepCurrency,
                round( erp_itemledger.inOutQty * erp_itemledger.wacRpt, 2 ) AS rptAmount,
                currencymaster.DecimalPlaces AS LocalCurrencyDecimals,
                currencymaster_1.DecimalPlaces AS RptCurrencyDecimals,               
                itemassigned.minimumQty as minimumQty,               
                itemassigned.maximunQty as maximunQty      
            FROM
                `erp_itemledger`
                INNER JOIN `itemmaster` ON `erp_itemledger`.`itemSystemCode` = `itemmaster`.`itemCodeSystem`
                INNER JOIN `financeitemcategorysub` ON `itemmaster`.`financeCategorySub` = `financeitemcategorysub`.`itemCategorySubID`
                LEFT JOIN `currencymaster` ON `erp_itemledger`.`wacLocalCurrencyID` = `currencymaster`.`currencyID`
                LEFT JOIN `currencymaster` AS `currencymaster_1` ON `erp_itemledger`.`wacRptCurrencyID` = `currencymaster_1`.`currencyID`
                LEFT JOIN `units` ON `erp_itemledger`.`unitOfMeasure` = `units`.`UnitID` 
                LEFT JOIN `itemassigned` ON `erp_itemledger`.`itemSystemCode` = `itemassigned`.`itemCodeSystem` AND itemassigned.companySystemID = erp_itemledger.companySystemID
            WHERE
                erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                AND itemmaster.financeCategoryMaster = 1 
                AND DATE(erp_itemledger.transactionDate) <= '$date' 
                ) AS ItemLedger 
                 LEFT JOIN (SELECT
	erp_itemledger.transactionDate,
	erp_itemledger.itemSystemCode,
	round( erp_itemledger.inOutQty, 2 ) AS inOutQty,
	erp_itemledger.companySystemID 
FROM
	(
	( SELECT MAX( itemLedgerAutoID ) AS itemLedgerAutoID, itemSystemCode, companySystemID FROM erp_itemledger WHERE documentSystemID = 3 GROUP BY itemSystemCode,companySystemID ) a
	LEFT JOIN erp_itemledger ON a.itemLedgerAutoID = erp_itemledger.itemLedgerAutoID AND a.companySystemID = erp_itemledger.companySystemID 
	) ) lastRDate ON lastRDate.itemSystemCode =  ItemLedger.itemSystemCode AND lastRDate.companySystemID =  ItemLedger.companySystemID
	LEFT JOIN (SELECT
	erp_itemledger.transactionDate,
	erp_itemledger.itemSystemCode,
	round( erp_itemledger.inOutQty, 2 ) AS inOutQty,
	erp_itemledger.companySystemID 
FROM
	(
	( SELECT MAX( itemLedgerAutoID ) AS itemLedgerAutoID, itemSystemCode, companySystemID FROM erp_itemledger WHERE documentSystemID = 8 GROUP BY itemSystemCode,companySystemID ) a
	LEFT JOIN erp_itemledger ON a.itemLedgerAutoID = erp_itemledger.itemLedgerAutoID AND a.companySystemID = erp_itemledger.companySystemID 
	) ) lastIDate ON lastIDate.itemSystemCode =  ItemLedger.itemSystemCode AND lastIDate.companySystemID =  ItemLedger.companySystemID
            GROUP BY
                ItemLedger.companySystemID,
                ItemLedger.itemSystemCode ORDER BY ItemLedger.companySystemID";
        $items = DB::select($sql);
        //dd(DB::getQueryLog());
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
            'date' => $date,
            'subCompanies' => $subCompanies,
            'grandWacLocal' => $GrandWacLocal,
            'grandWacRpt' => $GrandWacRpt,
            'warehouse' => $request->warehouse
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
                                    (erp_itemledger.inOutQty* erp_itemledger.wacRpt) as totalCost,
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
                            $data[$x]['Doc ID'] = $val->documentID;
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
                            $data[$x]['Total Cost (USD)'] = round($val->totalCost, 2);
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

                }
                else if ($reportTypeID == 'SA') { //Stock Aging Report

                    $type = $request->type;
                    $input = $request->all();
                    $input = $this->convertArrayToSelectedValue($input, array('currencyID','reportCategory'));
                    $input['reportCategory'] = isset($input['reportCategory'])?$input['reportCategory']:1;
                    $output = $this->stockAgingQry($input, 1);
                    $data = array();
                    if ($output) {
                        $x = 0;

                        foreach ($output['categories'] as $key) {
                            foreach ($key as $val) {
                                $x++;
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Item Code'] = $val->itemPrimaryCode;
                                $data[$x]['Item Description'] = $val->itemDescription;
                                $data[$x]['Category'] = $val->categoryDescription;
                                $data[$x]['Movement Category'] = $val->movementCatDescription;
                                $data[$x]['UOM'] = $val->UnitShortCode;
                                $data[$x]['Qty'] = $val->Qty;

                                if ($input['currencyID'] == 1) {
                                    $data[$x]['WAC Local'] = number_format($val->WACLocal, $val->LocalCurrencyDecimals);
                                    $data[$x]['Local Amount'] = number_format($val->WacLocalAmount, $val->LocalCurrencyDecimals);
                                } else if ($input['currencyID'] == 2) {
                                    $data[$x]['WAC Rep'] = number_format($val->WACRpt, $val->RptCurrencyDecimals);
                                    $data[$x]['Rep Amount'] = number_format($val->WacRptAmount, $val->RptCurrencyDecimals);
                                }

                                if($input['reportCategory'] == 2){ // yearly
                                    //$aging = ['0-365', '366-730', '730-1095', '1096-1460', '1461-1825', '> 1826‬'];

                                    $data[$x]['<= 1 year (Qty)'] = $val->case1;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['<= 1 year (Value)'] = number_format($val->WACLocal * $val->case1, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['<= 1 year (Value)'] = number_format($val->WACRpt * $val->case1, $val->RptCurrencyDecimals);
                                    }


                                    $data[$x]['1 to 2 years (Qty)'] = $val->case2;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['1 to 2 years (Value)'] = number_format($val->WACLocal * $val->case2, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['1 to 2 years (Value)'] = number_format($val->WACRpt * $val->case2, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['2 to 3 years (Qty)'] = $val->case3;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['2 to 3 years (Value)'] = number_format($val->WACLocal * $val->case3, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['2 to 3 years (Value)'] = number_format($val->WACRpt * $val->case3, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['3 to 4 years (Qty)'] = $val->case4;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['3 to 4 years (Value)'] = number_format($val->WACLocal * $val->case4, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['3 to 4 years (Value)'] = number_format($val->WACRpt * $val->case4, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['4 to 5 years (Qty)'] = $val->case5;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['4 to 5 years (Value)'] = number_format($val->WACLocal * $val->case5, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['4 to 5 years (Value)'] = number_format($val->WACRpt * $val->case5, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['Over 5 years (Qty)'] = $val->case6;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['Over 5 years (Value)'] = number_format($val->WACLocal * $val->case6, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['Over 5 years (Value)'] = number_format($val->WACRpt * $val->case6, $val->RptCurrencyDecimals);
                                    }

                                }else{ // 0 - 730 days

                                    $data[$x]['<=30 (Qty)'] = $val->case1;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['<=30 (Value)'] = number_format($val->WACLocal * $val->case1, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['<=30 (Value)'] = number_format($val->WACRpt * $val->case1, $val->RptCurrencyDecimals);
                                    }


                                    $data[$x]['31 to 60 (Qty)'] = $val->case2;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['31 to 60 (Value)'] = number_format($val->WACLocal * $val->case2, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['31 to 60 (Value)'] = number_format($val->WACRpt * $val->case2, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['61 to 90 (Qty)'] = $val->case3;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['61 to 90 (Value)'] = number_format($val->WACLocal * $val->case3, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['61 to 90 (Value)'] = number_format($val->WACRpt * $val->case3, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['91 to 120 (Qty)'] = $val->case4;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['91 to 120 (Value)'] = number_format($val->WACLocal * $val->case4, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['91 to 120 (Value)'] = number_format($val->WACRpt * $val->case4, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['121 to 365 (Qty)'] = $val->case5;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['121 to 365 (Value)'] = number_format($val->WACLocal * $val->case5, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['121 to 365 (Value)'] = number_format($val->WACRpt * $val->case5, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['366 to 730 (Qty)'] = $val->case6;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x]['366 to 730 (Value)'] = number_format($val->WACLocal * $val->case6, $val->LocalCurrencyDecimals);
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x]['366 to 730 (Value)'] = number_format($val->WACRpt * $val->case6, $val->RptCurrencyDecimals);
                                    }

                                    $data[$x]['Over 730 (Qty)'] = $val->case7;
                                    if ($input['currencyID'] == 1) {
                                        if ($val->Qty == 0) {
                                            $data[$x]['Over 730 (Value)'] = number_format($val->WacLocalAmount, $val->LocalCurrencyDecimals);
                                        } else {
                                            $data[$x]['Over 730 (Value)'] = number_format($val->WACLocal * $val->case7, $val->LocalCurrencyDecimals);
                                        }
                                    } else if ($input['currencyID'] == 2) {
                                        if ($val->Qty == 0) {
                                            $data[$x]['Over 730 (Value)'] = number_format($val->WacRptAmount, $val->RptCurrencyDecimals);
                                        } else {
                                            $data[$x]['Over 730 (Value)'] = number_format($val->WACRpt * $val->case7, $val->RptCurrencyDecimals);
                                        }
                                    }



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
            case 'INVSD':
                $data = [];
                if($request->detail == 1) {
                    $output = $this->stockDetailQry($request);
                    if ($output['categories']) {
                        foreach ($output['categories'] as $key => $vale) {
                            foreach ($output['categories'][$key] as $val) {
                                $data[] = array(
                                    'Item Code' => $val->itemPrimaryCode,
                                    'Item Description' => $val->itemDescription,
                                    'UOM' => $val->UnitShortCode,
                                    'Part Number' => $val->secondaryItemCode,
                                    'Sub Category' => $val->categoryDescription,
                                    'Stock Qty' => $val->Qty,
                                    'Total Value (USD)' => number_format($val->WacRptAmount, $val->RptCurrencyDecimals),
                                    'Last Receipt Date' => \Helper::dateFormat($val->lastReceiptDate),
                                    'Last Receipt Qty' => $val->lastReceiptQty,
                                    'Last Issued Date' => \Helper::dateFormat($val->lastIssuedDate),
                                    'Last Issued Qty' => $val->lastIssuedQty
                                );
                            }
                        }
                    }
                }else{
                    $output = $this->stockDetailCompanyQry($request);
                    if ($output['categories']) {
                        foreach ($output['categories'] as $key => $vale) {
                            foreach ($output['categories'][$key] as $val) {
                                $data[] = array(
                                    'Company' => $val->companyID,
                                    'Item Code' => $val->itemPrimaryCode,
                                    'Item Description' => $val->itemDescription,
                                    'UOM' => $val->UnitShortCode,
                                    'Part Number' => $val->secondaryItemCode,
                                    'Sub Category' => $val->categoryDescription,
                                    'Stock Qty' => $val->Qty,
                                    'Total Value (USD)' => number_format($val->WacRptAmount, $val->RptCurrencyDecimals),
                                    'Last Receipt Date' => \Helper::dateFormat($val->lastReceiptDate),
                                    'Last Receipt Qty' => $val->lastReceiptQty,
                                    'Last Issued Date' => \Helper::dateFormat($val->lastIssuedDate),
                                    'Last Issued Qty' => $val->lastIssuedQty
                                );
                            }
                        }
                    }
                }
                $csv = \Excel::create('stock_Detail', function ($excel) use ($data) {
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
                break;
            default:
                return $this->sendError('No report ID found');

        }
    }


}

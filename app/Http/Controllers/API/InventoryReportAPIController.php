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

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\ErpItemLedger;
use App\Models\GRVDetails;
use App\Models\GrvDetailsPrn;
use App\Models\ItemAssigned;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\helper\CreateExcel;
use App\Models\ItemCategoryTypeMaster;

class InventoryReportAPIController extends AppBaseController
{
    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'INVSI':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'items' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
                case 'INVIS':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'items' => 'required',
                    'warehouse' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
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
            case 'INVIM':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }

                $messages = [
                    'fastMovingTo.greater_than_field' => 'The Fast Moving To must be a greater than to Fast Moving From',
                    'slowMovingTo.greater_than_field' => 'The Slow Moving To must be a greater than to Slow Moving From',
                    'slowMovingFrom.greater_than_field' => 'The Slow Moving To must be a greater than to Fast Moving To',
                    'nonMoving.greater_than_or_equal_field' => 'The None Moving To must be a greater than or equal to Slow Moving To',
                ];

                if ($reportTypeID == 'IMI') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required|date',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'warehouse' => 'required',
                        'segment' => 'required',
                        'reportTypeID' => 'required',
                        'fastMovingFrom' => 'required|numeric',
                        'fastMovingTo' => 'required|numeric|greater_than_field:fastMovingFrom',
                        'nonMoving' => 'required|numeric|greater_than_or_equal_field:slowMovingTo',
                        'slowMovingFrom' => 'required|numeric|greater_than_field:fastMovingTo',
                        'slowMovingTo' => 'required:numeric|greater_than_field:slowMovingFrom',
                    ],$messages);
                }else if($reportTypeID == 'IMHV'){
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required|date',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'warehouse' => 'required',
                        'segment' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                }else if($reportTypeID == 'IMSM'){
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
                return $this->sendError(trans('custom.no_report_id_found'));
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

        
        $company = Company::find($companiesByGroup[0]);
        $company_name = $company->CompanyName;

        $warehouse = WarehouseMaster::whereIN('companySystemID', $companiesByGroup)->get();
        $document = DocumentMaster::where('departmentSystemID', 10)->get();
        $segment = SegmentMaster::approved()->withAssigned($companiesByGroup)->get();

        $categoryType = $request->input('categoryType');
        $categoryTypeID = collect($categoryType)->pluck('id')->toArray();

        $item = ErpItemLedger::select('erp_itemledger.companySystemID', 'erp_itemledger.itemSystemCode', 'erp_itemledger.itemPrimaryCode', 'itemmaster.itemDescription', 'itemmaster.secondaryItemCode')
        ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
        ->whereIn('erp_itemledger.companySystemID', $companiesByGroup)
        ->where('itemmaster.financeCategoryMaster', 1)
        ->when(!empty($categoryTypeID), function ($query) use ($categoryTypeID) {
            $query->whereHas('item_master.item_category_type', function ($query) use ($categoryTypeID) {
                $query->whereIn('categoryTypeID', $categoryTypeID);
            });
        })
        ->groupBy('erp_itemledger.itemSystemCode')
        ->get();

        $categoryTypeData = ItemCategoryTypeMaster::all();

        $output = array(
            'warehouse' => $warehouse,
            'document' => $document,
            'segment' => $segment,
            'item' => $item,
            'categoryTypeData' => $categoryTypeData,
            'company_name' => $company_name
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getScarpInventoryFilterData(Request $request)
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


        $item = DB::table('erp_grvdetails')->select('erp_grvdetails.companySystemID', 'erp_grvdetails.itemPrimaryCode', 'erp_grvdetails.itemDescription')
        ->whereIn('erp_grvdetails.companySystemID', $companiesByGroup)
        ->groupBy('erp_grvdetails.itemPrimaryCode')
        //->take(50)
        ->get();


        $output = array(
            'warehouse' => $warehouse,
            'document' => $document,
            'segment' => $segment,
            'item' => $item,
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }
    private function itemSummaryReport($from, $toDate, $warhouse,$category,$items,$currency)
    {

        $fromDate = new Carbon($from);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($toDate);
        $toDate = $toDate->format('Y-m-d');

        $warehouse = (array)$warhouse;
        $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        $items = (array)$items;
        $items = collect($items)->pluck('itemPrimaryCode');


        if($currency == 1)
        {
            $cur = 'wacLocal';
            $cur_id = 'erp_itemledger.wacLocalCurrencyID';
        }
        else
        {
            $cur = 'wacRpt';
            $cur_id = 'erp_itemledger.wacRptCurrencyID';
        }
 
        return ErpItemLedger::join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
                                    ->join('financeitemcategorysub', 'itemmaster.financeCategorySub', '=', 'financeitemcategorysub.itemCategorySubID')
                                    ->join('currencymaster', $cur_id, '=', 'currencymaster.currencyID')
                                    ->join('units', 'erp_itemledger.unitOfMeasure', '=', 'units.UnitID')
                                    ->whereIn('wareHouseSystemCode', $warehouse)
                                    ->whereIn('itemPrimaryCode', $items)
                                    ->groupBy('itemPrimaryCode')
                                    ->selectRaw('itemLedgerAutoID,unitOfMeasure, units.UnitShortCode, currencymaster.CurrencyName AS LocalCurrency,currencymaster.DecimalPlaces AS LocalCurrencyDecimals,itemLedgerAutoID,itemmaster.itemDescription,itemPrimaryCode,transactionDate,sum(case when transactionDate<"'.$toDate.'" then inOutQty else 0 end) as closing_balance_quantity,sum(case when transactionDate<"'.$toDate.'" then '.$cur.' * inOutQty else 0 end) as closing_balance_value,sum(case when transactionDate<"'.$fromDate.'" then inOutQty else 0 end) as opening_balance_quantity,sum(case when transactionDate<"'.$fromDate.'" then '.$cur.' * inOutQty else 0 end) as opening_balance_value,sum(case when (inOutQty<0 && transactionDate>"'.$fromDate.'" && transactionDate<"'.$toDate.'") then inOutQty else 0 end) as outwards_quantity,sum(case when (inOutQty<0 && transactionDate>"'.$fromDate.'" && transactionDate<"'.$toDate.'") then '.$cur.' * inOutQty else 0 end) as outwards_value,sum(case when (inOutQty>0 && transactionDate>"'.$fromDate.'" && transactionDate<"'.$toDate.'") then inOutQty else 0 end) as inwards_quantity,sum(case when (inOutQty>0 && transactionDate>"'.$fromDate.'" && transactionDate<"'.$toDate.'") then '.$cur.' * inOutQty else 0 end) as inwards_value,itemmaster.financeCategorySub,financeitemcategorysub.categoryDescription,itemmaster.secondaryItemCode')
                                    ->get();

    }

    private function scrapInventoryReport($from, $toDate,$category,$items, $suppliers,$currency,$companySystemID)
    {

        $fromDate = new Carbon($from);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($toDate);
        $toDate = $toDate->format('Y-m-d');

        $items = (array)$items;
        $items = collect($items)->pluck('itemPrimaryCode');

        $suppliers = (array)$suppliers;
        $suppliers = collect($suppliers)->pluck('primarySupplierCode');


        if($currency == 1)
        {
            $cur = 'wacLocal';
            $cur_id = 'erp_itemledger.wacLocalCurrencyID';
        }
        else
        {
            $cur = 'wacRpt';
            $cur_id = 'erp_itemledger.wacRptCurrencyID';
        }

        return GRVDetails::with(['grv_master'])->whereHas('grv_master', function($q) use($suppliers,$companySystemID) {
                $q->whereIn('supplierPrimaryCode', $suppliers);
                $q->where('companySystemID', $companySystemID);
        })->whereIn('itemPrimaryCode',$items)->where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->get();

    }

    private function scrapInventoryReportSupplierWise($from, $toDate,$category,$items,$suppliers,$currency,$companySystemID)
    {

        $fromDate = new Carbon($from);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($toDate);
        $toDate = $toDate->format('Y-m-d');

        $items = (array)$items;
        $items = collect($items)->pluck('itemPrimaryCode');

        $suppliers = (array)$suppliers;
        $suppliers = collect($suppliers)->pluck('primarySupplierCode');

        if($currency == 1)
        {
            $cur = 'wacLocal';
            $cur_id = 'erp_itemledger.wacLocalCurrencyID';
        }
        else
        {
            $cur = 'wacRpt';
            $cur_id = 'erp_itemledger.wacRptCurrencyID';
        }

        return GRVDetails::with(['grv_master','unit'])->whereHas('grv_master', function($q) use($suppliers,$companySystemID) {
            $q->where('companySystemID', $companySystemID);
            $q->whereIn('supplierPrimaryCode', $suppliers);
        })->whereIn('itemPrimaryCode',$items)->where('createdDateTime', '>=', $fromDate)->where('createdDateTime', '<=', $toDate)->get();

    }

    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {

            case 'INVSIS':
                
                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                $currency_id = $input['currency'];
                $filter_val = $this->scrapInventoryReportSupplierWise($request->fromDate, $request->toDate,$request['category'],$request['Items'],$request['Suppliers'],$currency_id,$request->companySystemID);
                $array = array();
                if (!empty($filter_val)) {
                    foreach ($filter_val as $element)
                    {
                        $array[$element->grv_master->supplierName][]  = $element;
                    }
                }
        
                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID); 
                $output = array(
                    'items' => $array,
                    'company' => $company,
                    'currencyID' => $currency_id,
                );
                return $this->sendResponse($output, trans('custom.data_retrieved_retrieved_successfully'));

                 break;

                 case 'INVSI':

                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                $currency_id = $input['currency'];
                $filter_val = $this->scrapInventoryReport($request->fromDate, $request->toDate,$request['category'],$request['Items'],$request['Suppliers'],$currency_id,$request->companySystemID);

                $array = array();
                if (!empty($filter_val)) {
                    foreach ($filter_val as $element)
                    {
                        $array[$element->itemPrimaryCode][]  = $element;
                    }
                }

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $output = array(
                    'items' => $array,
                    'company' => $company,
                    'currencyID' => $currency_id,
                );
                return $this->sendResponse($output, trans('custom.data_retrieved_retrieved_successfully'));

                 break;

                 case 'INVIS':

                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                $currency_id = $input['currency'];
                $filter_val = $this->itemSummaryReport($request->fromDate, $request->toDate, $request['Warehouse'],$request['category'],$request['Items'],$currency_id);

                $array = array();
                if (!empty($filter_val)) {
                    foreach ($filter_val as $element)
                     {
                        $array[$element->categoryDescription][]  = $element;
                     }
                }



                $GrandOpeningBalance = collect($filter_val)->pluck('opening_balance_value')->toArray();
                $GrandOpeningBalance = array_sum($GrandOpeningBalance);

                $GrandInwards= collect($filter_val)->pluck('inwards_value')->toArray();
                $GrandInwards = array_sum($GrandInwards);

                $GrandOutwards = collect($filter_val)->pluck('outwards_value')->toArray();
                $GrandOutwards = array_sum($GrandOutwards);

                $GrandClosing = collect($filter_val)->pluck('closing_balance_value')->toArray();
                $GrandClosing = array_sum($GrandClosing);

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $output = array(
                    'categories' => $array,
                    'grandOpeningBalance' => $GrandOpeningBalance,
                    'grandInwards' => $GrandInwards,
                    'grandOutwards' => ($GrandOutwards < 0?$GrandOutwards*-1:$GrandOutwards),
                    'company' => $company,
                    'currencyID' => $currency_id,
                    'grandClosing' => $GrandClosing,
                );
                return $this->sendResponse($output, trans('custom.data_retrieved_retrieved_successfully'));

                 break;
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
                    
                    $company = Company::find($companyID[0]);
                    $company_name = $company->CompanyName;


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

                    $data['order'] = [];
                    $data['search']['value'] = '';
                    $request->merge($data);

                    return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    $query->orderBy('itemLedgerAutoID', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->with('company_name', $company_name)
                        ->addIndexColumn()
                        /*  ->with('orderCondition', $sort)*/
                        ->make(true);
                }
                else if ($reportTypeID == 'SA') {
                    $input = $request->all();
                    if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                        $sort = 'asc';
                    } else {
                        $sort = 'desc';
                    }
                    $input = $this->convertArrayToSelectedValue($input, array('currencyID', 'reportCategory'));
                    $input['reportCategory'] = isset($input['reportCategory']) ? $input['reportCategory'] : 1;

                    $output = $this->stockAgingQry($input, 0);
                    return $this->sendResponse($output, trans('custom.items_retrieved_successfully'));
                }
                break;
            case 'INVSD':
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'SD') {
                    $output = $this->stockDetailQry($request);
                    return $this->sendResponse($output, trans('custom.items_retrieved_successfully'));
                }
                break;
            case 'INVMMA':
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'INVMMA') {
                    $output = $this->minAndMaxAnalysis($request);

                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;

                    $detail['output'] = $output;
                    $detail['company_name'] = $company_name;

                    return $this->sendResponse($detail, trans('custom.items_retrieved_successfully'));
                }
                break;
            case 'INVIM':
                $reportTypeID = '';
                $output = array();
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'IMI') {
                    $output = $this->itemMovementBasedOnIssues($request);
                }else if($reportTypeID == 'IMHV'){
                    $output = $this->itemMovementBasedOnIssues($request);
                }else if($reportTypeID == 'IMSM'){

                }
                return $this->sendResponse($output, trans('custom.items_retrieved_successfully'));
                break;
            default:
                return $this->sendError(trans('custom.no_report_id_found'));

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

        
        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;
        
        $input = $request;
        if (array_key_exists('warehouse', $input)) {
            $warehouse = (array)$input['warehouse'];
            $warehouse = collect($warehouse)->pluck('wareHouseSystemCode');

        }

        $inputItems = [];
        if (array_key_exists('Items', $input)) {
            $inputItems = (array)$input['Items'];
            $inputItems = collect($inputItems)->pluck('itemSystemCode');
        }

        $aging = ['0-30', '31-60', '61-90', '91-120', '121-365', '366-730', '> 730'];

        if ($input['reportCategory'] == 2) {
            $aging = ['0-365', '366-730', '731-1095', '1096-1460', '1461-1826', '> 1826‬'];
        }

        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c && $input['reportCategory'] == 1) {
                    $agingField .= "SUM(if(ItemLedger.ageDays   > " . 730 . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                } else if ($count == $c && $input['reportCategory'] == 2) {
                    $agingField .= "SUM(if(ItemLedger.ageDays   > " . 1826 . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(ItemLedger.ageDays >= " . $list[0] . " AND ItemLedger.ageDays <= " . $list[1] . " AND ItemLedger.Qty >0,ItemLedger.Qty,0)) as `case" . $c . "`,";
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
                itemmaster.itemDescription,
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
	            AND erp_itemledger.itemSystemCode IN (" . join(',', json_decode($inputItems)) . ") 
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
            'grandWacRpt' => $GrandWacRpt,
            'company_name' => $company_name
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
        $company = Company::find($subCompanies[0]);
        $company_name = $company->CompanyName;

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

        $items=[];
        if (array_key_exists('Items', $input)) {
            $items = (array)$input['Items'];
            $items = collect($items)->pluck('itemSystemCode');
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
                itemmaster.itemDescription,
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
                IFNULL(currencymaster.DecimalPlaces,0) AS LocalCurrencyDecimals,
                IFNULL(currencymaster_1.DecimalPlaces,0) AS RptCurrencyDecimals,               
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
                AND erp_itemledger.itemSystemCode IN (" . join(',', json_decode($items)) . ")
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
            'warehouse' => $request->warehouse,
            'company_name' => $company_name
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

    public function exportReport(Request $request,ExportReportToExcelService $exportReportToExcelService)
    {
        $reportID = $request->reportID;
        switch ($reportID) {

            case 'INVSIS':

                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                $currency_id = $input['currency'];
                $items = $request['Items'];
                $suppliers = $request['Suppliers'];
                $companySystemID = $request->companySystemID;
                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);

                $items = (array)$items;
                $items = collect($items)->pluck('itemPrimaryCode');

                $suppliers = (array)$suppliers;
                $suppliers = collect($suppliers)->pluck('primarySupplierCode');

                $fromDate = $request->fromDate;
                $toDate = $request->toDate;

                $templateName = "export_report.scrap_inventory_supplier_wise_report";
                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);

                $reportData = ['scrapDetails' => $items, 'Title'=> trans('custom.scrap_inventory_report'), 'companyName' => $company->CompanyName,'fromDate' => $fromDate, 'toDate' => $toDate, 'suppliers' => $suppliers, 'companySystemID' => $companySystemID, 'currency_id' => $currency_id, 'company'=> $company];


                $file_type = $request->type;  
                $fileName = 'scrap_inventory_report';
                $path = 'inventory/report/scrap-inventory/excel/';
                $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);
        
                if($basePath == '')
                {
                    return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                    return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                

                break;

            case 'INVSI':

                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                $currency_id = $input['currency'];
                 $items = $request['Items'];
                $suppliers = $request['Suppliers'];
                $companySystemID = $request->companySystemID;

                $checkIsGroup = Company::find($request->companySystemID);

                $items = (array)$items;
                $items = collect($items)->pluck('itemPrimaryCode');

                $suppliers = (array)$suppliers;
                $suppliers = collect($suppliers)->pluck('primarySupplierCode');
                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);

                $fromDate = $request->fromDate;
                $toDate = $request->toDate;

                $templateName = "export_report.scrap_inventory_report";

                $companyCode = isset($company->CompanyID) ? $company->CompanyID: null;

                $reportData = ['scrapDetails' => $items, 'Title'=> trans('custom.scrap_inventory_report'), 'companyName' => $company->CompanyName, 'companyCode' => $companyCode, 'fromDate' => $fromDate, 'toDate' => $toDate, 'suppliers' => $suppliers, 'companySystemID' => $companySystemID, 'currency_id' => $currency_id, 'company'=> $company];

                $file_type = $request->type;  
                $fileName = 'scrap_inventory_report';
                $path = 'inventory/report/scrap-inventory/excel/';
                $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);
        
                if($basePath == '')
                {
                    return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                    return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                

                break;

            case 'INVIS':

                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                $currency_id = $input['currency'];

                $filter_val = $this->itemSummaryReport($request->fromDate, $request->toDate, $request['Warehouse'],$request['category'],$request['Items'],$currency_id);

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);

                $currencyCode = ($currency_id == 1) ? $company->localcurrency->CurrencyCode : $company->reportingcurrency->CurrencyCode;

                $decimal_val = ($currency_id == 1) ? $company->localcurrency->DecimalPlaces : $company->reportingcurrency->DecimalPlaces;


                
                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;

                $from_date =  ((new Carbon($request->fromDate))->format('d/m/Y'));
                $to_date =  ((new Carbon($request->toDate))->format('d/m/Y'));

                $openeingBalanceTotal = 0;
                $closingBalanceTotal = 0;
                $InwardsTotal = 0;
                $outwardTotal = 0;

                foreach ($filter_val as $val) {

                    $openeingBalanceTotal = round($openeingBalanceTotal,$decimal_val) + round($val->opening_balance_value,$decimal_val);
                    $closingBalanceTotal = round($closingBalanceTotal,$decimal_val) + round($val->closing_balance_value,$decimal_val);
                    $InwardsTotal = round($InwardsTotal,$decimal_val) + round($val->inwards_value,$decimal_val);
                    $outwardTotal = round($outwardTotal,$decimal_val) + round($val->outwards_value,$decimal_val);

                    $data[] = array(
                        trans('custom.category') => $val->categoryDescription,
                        trans('custom.item_code') => $val->itemPrimaryCode,
                        trans('custom.Part No / Reference No') => $val->secondaryItemCode,
                        trans('custom.item_description') => $val->itemDescription,
                        trans('custom.uom') => $val->UnitShortCode,
                        trans('custom.Opening Balance Qty') => $val->opening_balance_quantity,
                        trans('custom.Opening Balance Val') . ' (' . $currencyCode . ')' =>  number_format($val->opening_balance_value, $decimal_val, '.', ','),
                        trans('custom.Inwards Qty') => $val->inwards_quantity,
                        trans('custom.Inwards Val') . ' (' . $currencyCode . ')' => number_format($val->inwards_value, $decimal_val, '.', ','),
                        trans('custom.Outwards Qty') => ($val->outwards_quantity < 0)?$val->outwards_quantity*-1:$val->outwards_quantity,
                        trans('custom.Outwards Val') . ' (' . $currencyCode . ')' => number_format(($val->outwards_value < 0)?$val->outwards_value*-1:$val->outwards_value, $decimal_val, '.', ','),
                        trans('custom.Closing Balance Qty') => $val->closing_balance_quantity,
                        trans('custom.Closing Balance Val') . ' (' . $currencyCode . ')' => number_format($val->closing_balance_value, $decimal_val, '.', ','),

                    );
                }




                $dataSorted = collect($data)->sortBy('Category');

                $GrandTotal =  array(
                    trans('custom.category') => '',
                    trans('custom.item_code') => '',
                    trans('custom.Part No / Reference No') => '',
                    trans('custom.item_description') => trans('custom.grand_total'),
                    trans('custom.uom') => '',
                    trans('custom.Opening Balance Qty') => '',
                    trans('custom.Opening Balance Val') . ' (' . $currencyCode . ')' =>  round($openeingBalanceTotal,3),
                    trans('custom.Inwards Qty') => '',
                    trans('custom.Inwards Val') . ' (' . $currencyCode . ')' => $InwardsTotal,
                    trans('custom.Outwards Qty') => '',
                    trans('custom.Outwards Val') . ' (' . $currencyCode . ')' => $outwardTotal,
                    trans('custom.Closing Balance Qty') => '',
                    trans('custom.Closing Balance Val') . ' (' . $currencyCode . ')' => round($closingBalanceTotal,3),

                );

                $dataSorted->push($GrandTotal);

                $fileName = 'inventory_summary_report';
                $title = trans('custom.inventory_summary_report');
                $path = 'inventory/report/inventory_summary_report/excel/';
                $cur = NULL;
                $companyID = isset($company->CompanyID) ? $company->CompanyID: 'common';

                $detail_array = array('type' => 1,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'company_code'=>$companyID,'cur'=>$cur,'title'=>$title);
                $basePath = CreateExcel::process($dataSorted,$request->type,$fileName,$path,$detail_array);
        
                if($basePath == '')
                {
                     return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }



                break;

            case 'INVST': //Stock Transaction Report
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ST') { //Stock Transaction Report
                    $type = $request->type;
                    $input = $request->all();
                    $data = array();
                    $startDate = new Carbon($request->fromDate);
                    $startDate = $startDate->format('Y-m-d');

                    $endDate = new Carbon($request->toDate);
                    $endDate = $endDate->format('Y-m-d');


                    $from_date =  ((new Carbon($request->fromDate))->format('d/m/Y'));
                    $to_date =  ((new Carbon($request->toDate))->format('d/m/Y'));

                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;


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


                    $output = DB::table("erp_itemledger")
                                 ->selectRaw("erp_itemledger.itemLedgerAutoID,
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
                                    chartofaccounts.AccountDescription,
                                    ticketmaster.ticketNo,
                                    erp_itemissuemaster.issueRefNo,
                                    customermaster.CustomerName,
                                    contractmaster.ContractNumber
                                    ")
                        ->join('units', 'erp_itemledger.unitOfMeasure', '=', 'units.UnitID')
                        ->leftJoin('warehousemaster', 'erp_itemledger.wareHouseSystemCode', '=', 'warehousemaster.wareHouseSystemCode')
                        ->join('employees', 'erp_itemledger.createdUserID', '=', 'employees.empID')
                        ->leftJoin('erp_documentmaster', 'erp_itemledger.documentID', '=', 'erp_documentmaster.documentID')
                        ->leftJoin('itemassigned', function ($query) {
                            $query->on('erp_itemledger.itemSystemCode', '=', 'itemassigned.itemCodeSystem');
                            $query->on('erp_itemledger.companyID', '=', 'itemassigned.companyID');
                        })
                        ->leftJoin('erp_itemissuemaster', function ($query) {
                            $query->on('erp_itemledger.documentSystemCode', '=', 'erp_itemissuemaster.itemIssueAutoID');
                            $query->on('erp_itemledger.companySystemID', '=', 'erp_itemissuemaster.companySystemID');
                            $query->on('erp_itemledger.documentSystemID', '=', 'erp_itemissuemaster.documentSystemID');
                        })
                        ->leftJoin('financeitemcategorysub', function ($query) {
                            $query->on('itemassigned.financeCategoryMaster', '=', 'financeitemcategorysub.itemCategoryID');
                            $query->on('itemassigned.financeCategorySub', '=', 'financeitemcategorysub.itemCategorySubID');
                        })
                        ->leftJoin('chartofaccounts', 'financeitemcategorysub.financeGLcodePL', '=', 'chartofaccounts.AccountCode')
                        ->leftJoin('customermaster', 'erp_itemissuemaster.customerSystemID', '=', 'customermaster.customerCodeSystem')
                        ->leftJoin('ticketmaster', 'erp_itemissuemaster.jobNo', '=', 'ticketmaster.ticketidAtuto')
                        ->leftJoin('contractmaster', 'erp_itemissuemaster.contractUIID', '=', 'contractmaster.contractUID')
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
                            $data[$x][trans('custom.doc_id')] = $val->documentID;
                            $data[$x][trans('custom.document_code')] = $val->documentCode;
                            $data[$x][trans('custom.trans_date')] = \Helper::dateFormat($val->transactionDate);
                            $data[$x][trans('custom.service_line')] = $val->serviceLineCode;
                            $data[$x][trans('custom.warehouse')] = $val->wareHouseDescription;
                            $data[$x][trans('custom.ref_number')] = $val->referenceNumber;
                            $data[$x][trans('custom.processed_by')] = $val->empName;
                            $data[$x][trans('custom.item_code')] = $val->itemPrimaryCode;
                            $data[$x][trans('custom.item_desc')] = $val->itemDescription;
                            $data[$x][trans('custom.uom')] = $val->UnitShortCode;
                            $data[$x][trans('custom.part_no_ref_number')] = $val->partNumber;
                            $data[$x][trans('custom.qty')] = $val->inOutQty;
                            $data[$x][trans('custom.cost_usd')] = round($val->cost, 2);
                            $data[$x][trans('custom.total_cost_usd')] = round($val->totalCost, 2);
                            $data[$x][trans('custom.account_code')] = $val->AccountCode;
                            $data[$x][trans('custom.account_desc')] = $val->AccountDescription;
                            $data[$x][trans('custom.customer')] = $val->CustomerName;
                            $data[$x][trans('custom.contract')] = $val->ContractNumber;
                            $data[$x][trans('custom.jobs')] = $val->ticketNo;
                            $data[$x][trans('custom.job_ref_no')] = $val->issueRefNo;
                        }
                    }

                    //  \Excel::create('stock_transaction', function ($excel) use ($data) {
                    //     $excel->sheet('sheet name', function ($sheet) use ($data) {
                    //         $sheet->fromArray($data, null, 'A1', true);
                    //         //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                    //         $sheet->setAutoSize(true);
                    //         $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    //     });
                    //     $lastrow = $excel->getActiveSheet()->getHighestRow();
                    //     $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    // })->download($type);


                    $fileName = 'stock_transaction';
                    $title = trans('custom.stock_transaction');
                    $path = 'inventory/report/stock_transaction/excel/';
                    $cur = NULL;
                    $companyCode = isset($company->CompanyID) ? $company->CompanyID: 'common';

                    $detail_array = array('type' => 1,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'company_code'=>$companyCode, 'cur'=>$cur,'title'=>$title);
                    $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);
            
                    if($basePath == '')
                    {
                         return $this->sendError(trans('custom.unable_to_export_excel'));
                    }
                    else
                    {
                         return $this->sendResponse($basePath, trans('custom.success_export'));
                    }
    

                }
                else if ($reportTypeID == 'SA') { //Stock Aging Report

                    $type = $request->type;
                    $input = $request->all();
                    $input = $this->convertArrayToSelectedValue($input, array('currencyID', 'reportCategory'));
                    $input['reportCategory'] = isset($input['reportCategory']) ? $input['reportCategory'] : 1;
                    $output = $this->stockAgingQry($input, 1);
                    $from_date =  ((new Carbon($request->asOfDate))->format('d/m/Y'));
                    $to_date =  ((new Carbon($request->asOfDate))->format('d/m/Y'));

                    $company = Company::find($request->companySystemID);
                    $company_name = $company->CompanyName;
                    $data = array();    
                    if ($output) {
                        $x = 0;

                        foreach ($output['categories'] as $key) {
                            foreach ($key as $val) {
                                $x++;
                                $data[$x][trans('custom.company_id')] = $val->companyID;
                                $data[$x][trans('custom.item_code')] = $val->itemPrimaryCode;
                                $data[$x][trans('custom.item_description')] = $val->itemDescription;
                                $data[$x][trans('custom.part_no_ref_number')] = $val->secondaryItemCode;
                                $data[$x][trans('custom.category')] = $val->categoryDescription;
                                $data[$x][trans('custom.movement_category')] = $val->movementCatDescription;
                                $data[$x][trans('custom.uom')] = $val->UnitShortCode;
                                $data[$x][trans('custom.qty')] = $val->Qty;

                                if ($input['currencyID'] == 1) {
                                    $data[$x][trans('custom.wac_local')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal, $val->LocalCurrencyDecimals));
                                    $data[$x][trans('custom.local_amount')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WacLocalAmount, $val->LocalCurrencyDecimals));
                                } else if ($input['currencyID'] == 2) {
                                    $data[$x][trans('custom.wac_rep')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt, $val->RptCurrencyDecimals));
                                    $data[$x][trans('custom.reporting_amount')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WacRptAmount, $val->RptCurrencyDecimals));
                                }

                                if ($input['reportCategory'] == 2) { // yearly
                                    //$aging = ['0-365', '366-730', '730-1095', '1096-1460', '1461-1825', '> 1826‬'];
                                    $excelColumnFormat = [
                                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                    ];
                                    $data[$x][trans('custom.aging_1_year_qty')] = $val->case1;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_1_year_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case1, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_1_year_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case1, $val->RptCurrencyDecimals));
                                    }


                                    $data[$x][trans('custom.aging_1_to_2_years_qty')] = $val->case2;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_1_to_2_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case2, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_1_to_2_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case2, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_2_to_3_years_qty')] = $val->case3;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_2_to_3_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case3, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_2_to_3_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case3, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_3_to_4_years_qty')] = $val->case4;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_3_to_4_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case4, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_3_to_4_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case4, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_4_to_5_years_qty')] = $val->case5;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_4_to_5_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case5, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_4_to_5_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case5, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_over_5_years_qty')] = $val->case6;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_over_5_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case6, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_over_5_years_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case6, $val->RptCurrencyDecimals));
                                    }

                                } else { // 0 - 730 days

                                    $excelColumnFormat = [
                                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                        'X' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                                    ];

                                    $data[$x][trans('custom.aging_30_days_qty')] = $val->case1;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_30_days_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case1, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_30_days_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case1, $val->RptCurrencyDecimals));
                                    }


                                    $data[$x][trans('custom.aging_31_to_60_qty')] = $val->case2;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_31_to_60_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case2, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_31_to_60_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case2, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_61_to_90_qty')] = $val->case3;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_61_to_90_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case3, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_61_to_90_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case3, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_91_to_120_qty')] = $val->case4;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_91_to_120_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case4, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_91_to_120_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case4, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_121_to_365_qty')] = $val->case5;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_121_to_365_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case5, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_121_to_365_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case5, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_366_to_730_qty')] = $val->case6;
                                    if ($input['currencyID'] == 1) {
                                        $data[$x][trans('custom.aging_366_to_730_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case6, $val->LocalCurrencyDecimals));
                                    } else if ($input['currencyID'] == 2) {
                                        $data[$x][trans('custom.aging_366_to_730_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case6, $val->RptCurrencyDecimals));
                                    }

                                    $data[$x][trans('custom.aging_over_730_qty')] = $val->case7;
                                    if ($input['currencyID'] == 1) {
                                        if ($val->Qty == 0) {
                                            $data[$x][trans('custom.aging_over_730_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WacLocalAmount, $val->LocalCurrencyDecimals));
                                        } else {
                                            $data[$x][trans('custom.aging_over_730_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACLocal * $val->case7, $val->LocalCurrencyDecimals));
                                        }
                                    } else if ($input['currencyID'] == 2) {
                                        if ($val->Qty == 0) {
                                            $data[$x][trans('custom.aging_over_730_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WacRptAmount, $val->RptCurrencyDecimals));
                                        } else {
                                            $data[$x][trans('custom.aging_over_730_value')] = CurrencyService::convertNumberFormatToNumber(number_format($val->WACRpt * $val->case7, $val->RptCurrencyDecimals));
                                        }
                                    }


                                }
                            }
                        }
                    }

                    $fileName = 'stock_aging';
                    $title = trans('custom.stock_aging_report');
                    $path = 'inventory/report/stock_aging/excel/';
                    $cur = NULL;
                    $companyCode = isset($company->CompanyID) ? $company->CompanyID: 'common';

                    $exportToExcel = $exportReportToExcelService
                        ->setTitle($title)
                        ->setFileName($fileName)
                        ->setPath($path)
                        ->setCompanyCode($companyCode)
                        ->setCompanyName($company_name)
                        ->setFromDate($from_date)
                        ->setToDate($to_date)
                        ->setData($data)
                        ->setReportType(2)
                        ->setType('xls')
                        ->setExcelFormat($excelColumnFormat)
                        ->setCurrency($cur)
                        ->setDateType(1)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError(trans('custom.unable_to_export_excel'));

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
                }
            case 'INVSD':
                $data = [];

                $from_date =  ((new Carbon($request->date))->format('d/m/Y'));
                $to_date =  ((new Carbon($request->date))->format('d/m/Y'));

                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;


                if ($request->detail == 1) {
                    $output = $this->stockDetailQry($request);
                    if ($output['categories']) {
                        foreach ($output['categories'] as $key => $vale) {
                            foreach ($output['categories'][$key] as $val) {
                                $data[] = array(
                                    trans('custom.excel_item_code') => $val->itemPrimaryCode,
                                    trans('custom.excel_item_description') => $val->itemDescription,
                                    trans('custom.excel_uom') => $val->UnitShortCode,
                                    trans('custom.part_no_ref_number') => $val->secondaryItemCode,
                                    trans('custom.sub_category') => $val->categoryDescription,
                                    trans('custom.stock_qty') => $val->Qty,
                                    trans('custom.total_value_usd') => CurrencyService::convertNumberFormatToNumber(number_format($val->WacRptAmount, $val->RptCurrencyDecimals)),
                                    trans('custom.last_receipt_date') => ($val->lastReceiptDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->lastReceiptDate)) : null,
                                    trans('custom.last_receipt_qty') => $val->lastReceiptQty,
                                    trans('custom.last_issued_date') => ($val->lastIssuedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->lastIssuedDate)) : null,
                                    trans('custom.last_issued_qty') => $val->lastIssuedQty
                                );
                            }
                        }
                    }
                }
                else {
                    $output = $this->stockDetailCompanyQry($request);
                    if ($output['categories']) {
                        foreach ($output['categories'] as $key => $vale) {
                            foreach ($output['categories'][$key] as $val) {
                                $data[] = array(
                                    trans('custom.company') => $val->companyID,
                                    trans('custom.excel_item_code') => $val->itemPrimaryCode,
                                    trans('custom.excel_item_description') => $val->itemDescription,
                                    trans('custom.excel_uom') => $val->UnitShortCode,
                                    trans('custom.part_no_ref_number') => $val->secondaryItemCode,
                                    trans('custom.sub_category') => $val->categoryDescription,
                                    trans('custom.stock_qty') => $val->Qty,
                                    trans('custom.total_value_usd') => CurrencyService::convertNumberFormatToNumber(number_format($val->WacRptAmount, $val->RptCurrencyDecimals)),
                                    trans('custom.last_receipt_date') => ($val->lastReceiptDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->lastReceiptDate)) : null,
                                    trans('custom.last_receipt_qty') => $val->lastReceiptQty,
                                    trans('custom.last_issued_date') => ($val->lastIssuedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->lastIssuedDate)) : null,
                                    trans('custom.last_issued_qty') => $val->lastIssuedQty
                                );
                            }
                        }
                    }
                }
                $excelColumnFormat = [
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ];

                $fileName = 'stock_detail';
                $title = trans('custom.stock_details_report');
                $path = 'inventory/report/stock_Detail/excel/';
                $cur = NULL;
                $companyCode = isset($company->CompanyID) ? $company->CompanyID: 'common';


                $exportToExcel = $exportReportToExcelService
                    ->setTitle($title)
                    ->setFileName($fileName)
                    ->setPath($path)
                    ->setCompanyCode($companyCode)
                    ->setCompanyName($company_name)
                    ->setFromDate($from_date)
                    ->setToDate($to_date)
                    ->setData($data)
                    ->setReportType(2)
                    ->setType('xls')
                    ->setExcelFormat($excelColumnFormat)
                    ->setCurrency($cur)
                    ->setDateType(1)
                    ->setDetails()
                    ->generateExcel();

                if(!$exportToExcel['success'])
                    return $this->sendError(trans('custom.unable_to_export_excel'));

                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                
                break;
            case 'INVMMA':
                $reportTypeID = $request->reportTypeID;
                $data = array();
                if ($reportTypeID == 'INVMMA') {
                    $output = $this->minAndMaxAnalysis($request);
                    $x = 0;
                    foreach ($output as $item){
                        $data[$x][trans('custom.item_code')] = $item->itemPrimaryCode;
                        $data[$x][trans('custom.item_description')] = $item->itemDescription;
                        $data[$x][trans('custom.part_no_ref_number')] = $item->secondaryItemCode;
                        $data[$x][trans('custom.uom')] = $item->unit? $item->unit->UnitShortCode: '-';
                        $data[$x][trans('custom.stock_qty')] = $item->stock;
                        $data[$x][trans('custom.qty_on_order')] = $item->onOrder;
                        $data[$x][trans('custom.max_qty')] = $item->maximunQty;
                        $data[$x][trans('custom.min_qty')] = $item->minimumQty;
                        $data[$x][trans('custom.rol_qty')] = $item->rolQuantity;
                        $x ++;
                    }
                }
                //  \Excel::create('stock_Detail', function ($excel) use ($data) {
                //     $excel->sheet('sheet name', function ($sheet) use ($data) {
                //         $sheet->fromArray($data, null, 'A1', true);
                //         //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                //         $sheet->setAutoSize(true);
                //         $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                //     });
                //     $lastrow = $excel->getActiveSheet()->getHighestRow();
                //     $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                // })->download('csv');
                // return $this->sendResponse(array(), trans('custom.success_export'));

                    
                $company = Company::find($request->companySystemID);
                $company_name = $company->CompanyName;
                $companyCode = isset($company->CompanyID) ? $company->CompanyID: 'common';

                $fileName = 'min_max_analysis';
                $title = trans('custom.min_max_analysis');
                $path = 'inventory/report/min_max_analysis/excel/';
                $cur = NULL;
                $from_date = null;
                $to_date = null;
                $detail_array = array('type' => 1,'from_date'=>$from_date,'to_date'=>$to_date,'company_name'=>$company_name,'company_code'=>$companyCode,'cur'=>$cur,'title'=>$title);
                $basePath = CreateExcel::process($data,$request->type,$fileName,$path,$detail_array);
        
                if($basePath == '')
                {
                     return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            case 'INVIM':

                $reportTypeID = $request->reportTypeID;
                $data = array();
                if ($reportTypeID == 'IMI' || $reportTypeID == 'IMHV') {
                    $output = $this->itemMovementBasedOnIssues($request);
                    $fromDate = new Carbon($request->fromDate);
                    $fromDate = $fromDate->format('d/m/Y');

                    $toDate = new Carbon($request->toDate);
                    $toDate = $toDate->format('d/m/Y');
                    $x = 0;
                    foreach ($output as $item){
                        $data[$x][trans('custom.item_code')] = $item->itemPrimaryCode;
                        $data[$x][trans('custom.description')] = $item->itemDescription;
                        $data[$x][trans('custom.uom')] = $item->UnitShortCode;
                        $data[$x][trans('custom.part_no_ref_number')] = $item->secondaryItemCode;
                        $data[$x][trans('custom.category')] = $item->categoryLabel;
                        if($reportTypeID == 'IMI'){
                            $data[$x][trans('custom.total_units_issued') . ' ' . $fromDate . ' - ' . $toDate] = $item->TotalUnitsIssue;
                            $data[$x][trans('custom.cost_per_unit_period') . ' ' . $fromDate . ' - ' . $toDate] = $item->CostPerUnitIssue_Rpt;
                        }

                        $data[$x][trans('custom.total_cost_period') . ' ' . $fromDate . ' - ' . $toDate] = $item->TotalCostIssue_Rpt;
                        if($reportTypeID == 'IMI') {
                            $data[$x][trans('custom.quantity_as_of') . ' ' . $toDate] = $item->totalQty;
                        }
                        if($reportTypeID == 'IMHV'){
                            $data[$x][trans('custom.cost_per_unit')] = $item->costPerUnitRpt;
                        }
                        $data[$x][trans('custom.total_cost_as_of') . ' ' . $toDate] = $item->wacValueRpt;
                        $x ++;
                    }
                }


                 \Excel::create('item_movements', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download('xlsx');
                return $this->sendResponse(array(), trans('custom.success_export'));
                break;
            default:
                return $this->sendError(trans('custom.no_report_id_found'));

        }
    }

    public function minAndMaxAnalysis(Request $request)
    {

        $input = $request->all();
        $companySystemID = isset($input['companySystemID']) ? $input['companySystemID'] : 0;
        $items = ItemAssigned::where('companySystemID', $companySystemID)->with(['unit' => function($q){
               $q->select('UnitID','UnitShortCode');
            },'item_ledger' => function ($q) use ($companySystemID) {
                $q->where('companySystemID', $companySystemID)
                    ->groupBy('itemSystemCode')
                    ->selectRaw('Round(sum(inOutQty)) AS stock,itemSystemCode');
            },'po_detail' => function ($q) use ($companySystemID) {
                $q->where('companySystemID', $companySystemID)
                    ->whereHas('order',function ($q){
                        $q->where('approved',-1)
                            ->where('poCancelledYN',0);
                    })
                    ->groupBy('itemCode')
                    ->selectRaw('sum(noQty) AS po_total,itemCode');
            },'grv_detail' => function ($q) use ($companySystemID) {
                $q->where('companySystemID', $companySystemID)
                    ->whereHas('grv_master',function ($q){
                        $q->where('approved',-1)
                          ->where('grvTypeID',2);
                    })
                    ->groupBy('itemCode')
                    ->selectRaw('sum(noQty) AS grv_total,itemCode');
            }])
            ->where('financeCategoryMaster',1)
            //->limit(100)
            ->get(["idItemAssigned",
                "companySystemID",
                "itemUnitOfMeasure",
                "itemCodeSystem",
                "itemPrimaryCode",
                "itemDescription",
                "secondaryItemCode",
                "maximunQty",
                "minimumQty",
                "rolQuantity"]);

        foreach ($items as $item){
            $item->po_total = 0;
            $item->grv_total = 0;
            $item->stock = 0;
            if(count($item['item_ledger']) > 0){
                $item->stock = $item['item_ledger'][0]['stock'];
            }
            if(count($item['po_detail']) > 0){
                $item->po_total = $item['po_detail'][0]['po_total'];
            }
            if(count($item['grv_detail']) > 0){
                $item->grv_total = $item['grv_detail'][0]['grv_total'];
            }
            $item->onOrder =  round(($item->po_total - $item->grv_total),2);
        }

        return $items;
    }

    public function itemMovementBasedOnIssues(Request $request){
        $input = $request->all();
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('d/m/Y');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('d/m/Y');

        $selectedCompanyId = $request['companySystemID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        $fastMovingFrom = isset($input['fastMovingFrom'])?$input['fastMovingFrom']:0;
        $fastMovingTo= isset($input['fastMovingTo'])?$input['fastMovingTo']:0;
        $slowMovingFrom = isset($input['slowMovingFrom'])?$input['slowMovingFrom']:0;
        $slowMovingTo = isset($input['slowMovingTo'])?$input['slowMovingTo']:0;
        $nonMoving = isset($input['nonMoving'])?$input['nonMoving']:0;

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

        $finalOrderBy = '';

        if ($request->reportTypeID == 'IMI') {
            $finalOrderBy = 'ORDER By finalquery.category,finalquery.itemSystemCode';
        }else if($request->reportTypeID == 'IMHV'){
            $finalOrderBy = 'ORDER By getTotalQtyandCost.wacValueRpt Desc';
        }

        $sql = "SELECT
                finalquery.companyID,
                finalquery.companySystemID,
                finalquery.itemSystemCode,
                finalquery.itemPrimaryCode,
                itemmaster.itemDescription,
	            itemmaster.secondaryItemCode,
	            units.UnitShortCode,
                finalquery.category,
                IF(finalquery.category = 1,'Fast Moving', IF(finalquery.category = 2,'Slow Moving',IF(finalquery.category = 3,'Non Moving','') )) as categoryLabel,
                getTotalQtyandCost.totalQty,
                getTotalQtyandCost.wacValueRpt,
                getTotalQtyandCost.wacValueLocal,
                getTotalQtyandCost.costPerUnitRpt,
                getTotalQtyandCost.costPerUnitLocal,
                if(getIssuedQtyandCost.TotalUnitsIssue is null,0,getIssuedQtyandCost.TotalUnitsIssue) AS TotalUnitsIssue,
                if(getIssuedQtyandCost.TotalCostIssue_Rpt is null,0,getIssuedQtyandCost.TotalCostIssue_Rpt) AS TotalCostIssue_Rpt,
                if(getIssuedQtyandCost.CostPerUnitIssue_Rpt is null,0,getIssuedQtyandCost.CostPerUnitIssue_Rpt) AS CostPerUnitIssue_Rpt,
                if(getIssuedQtyandCost.TotalCostIssue_local is null,0,getIssuedQtyandCost.TotalCostIssue_local) AS TotalCostIssue_local,
                if(getIssuedQtyandCost.CostPerUnitIssue_Local is null,0,getIssuedQtyandCost.CostPerUnitIssue_Local) AS CostPerUnitIssue_Local
                FROM
                (
                SELECT
                    stockMainQuery.companyID,
                    stockMainQuery.companySystemID,
                    stockMainQuery.itemSystemCode,
                    stockMainQuery.itemPrimaryCode,
                    movementIssue.MIMaxDate,
                    movementIssue.TotalMonth,
                    movementGRV.GRVMaxDate,
                    movementGRV.TotalGRVMonth,
                    If(movementIssue.itemSystemCode Is Null And (TotalGRVMonth Between $fastMovingFrom  And $fastMovingTo),1,If(movementIssue.itemSystemCode Is Null And (TotalGRVMonth Between $slowMovingFrom And $slowMovingTo),2,If(TotalMonth Between $fastMovingFrom And  $fastMovingTo,1,If(TotalMonth Between $slowMovingFrom And $slowMovingTo,2,If(TotalMonth> $nonMoving,3,3))))) as category
                FROM
                    (
                    SELECT
                        erp_itemledger.companySystemID,
                        erp_itemledger.companyID,
                    IF
                        ( erp_stockreceive.interCompanyTransferYN =- 1, 3, erp_itemledger.documentSystemID ) AS documentSystemID,
                    IF
                        ( erp_stockreceive.interCompanyTransferYN =- 1, 'GRV', erp_itemledger.documentID ) AS documentID,
                        erp_itemledger.documentSystemCode,
                        erp_itemledger.documentCode,
                        erp_itemledger.transactionDate,
                        erp_itemledger.wareHouseSystemCode,
                        erp_itemledger.itemSystemCode,
                        erp_itemledger.itemPrimaryCode,
                        erp_itemledger.unitOfMeasure,
                        erp_itemledger.inOutQty,
                        erp_itemledger.wacRptCurrencyID,
                        erp_itemledger.wacRpt,
                        round( ( erp_itemledger.inOutQty * erp_itemledger.wacRpt ), 2 ) AS wacValue,
                        DATE_FORMAT( erp_itemledger.transactionDate, '%d/%m/%Y' ) AS documentDate,
                        erp_itemledger.fromDamagedTransactionYN 
                    FROM
                        erp_itemledger
                        LEFT JOIN erp_stockreceive ON erp_itemledger.companySystemID = erp_stockreceive.companySystemID 
                        AND erp_itemledger.documentSystemID = erp_stockreceive.documentSystemID 
                        AND erp_itemledger.documentSystemCode = erp_stockreceive.stockReceiveAutoID 
                    WHERE
                        erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ")  
                        AND erp_itemledger.fromDamagedTransactionYN = 0
                        AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                        AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                        AND STR_TO_DATE( DATE_FORMAT( erp_itemledger.transactionDate, '%d/%m/%Y' ), '%d/%m/%Y' ) <= STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' ) 
                    ) AS stockMainQuery
                    LEFT JOIN ( /*FROM ISSUE*/
                    SELECT 
                    companySystemID,
                    documentSystemID,
                    itemSystemCode,
                    MIMaxDate,
                    Round(DATEDIFF(STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' )  ,  STR_TO_DATE( DATE_FORMAT( MIMaxDate, '%d/%m/%Y' ), '%d/%m/%Y' ))/30,0) as TotalMonth
                    FROM (
                    SELECT
                        erp_itemledger.companySystemID,
                        erp_itemledger.documentSystemID,
                        erp_itemledger.itemSystemCode,
                        MAX( erp_itemledger.transactionDate ) as MIMaxDate
                    FROM
                        erp_itemledger 
                    WHERE
                        erp_itemledger.documentSystemID = 8 
                        AND erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                        AND erp_itemledger.fromDamagedTransactionYN = 0 
                        AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                        AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                        AND STR_TO_DATE( DATE_FORMAT( erp_itemledger.transactionDate, '%d/%m/%Y' ), '%d/%m/%Y' ) <= STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' ) 
                    GROUP BY
                        erp_itemledger.companySystemID,
                        erp_itemledger.itemSystemCode ) as movementIssue_base
                    ) AS movementIssue ON movementIssue.itemSystemCode=stockMainQuery.itemSystemCode
                        LEFT JOIN ( /*FROM GRV*/
                    SELECT 
                    companySystemID,
                    documentSystemID,
                    itemSystemCode,
                    GRVMaxDate,
                    Round(DATEDIFF(STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' )  ,  STR_TO_DATE( DATE_FORMAT( GRVMaxDate, '%d/%m/%Y' ), '%d/%m/%Y' ))/30,0) as TotalGRVMonth
                    FROM (
                    SELECT
                        erp_itemledger.companySystemID,
                        erp_itemledger.documentSystemID,
                        erp_itemledger.itemSystemCode,
                        MAX( erp_itemledger.transactionDate ) as GRVMaxDate
                    FROM
                        erp_itemledger 
                    WHERE
                        (erp_itemledger.documentSystemID = 3 or erp_itemledger.documentSystemID = 7)
                        AND erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                        AND erp_itemledger.fromDamagedTransactionYN = 0 
                        AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                        AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                        AND erp_itemledger.inOutQty>0
                        AND STR_TO_DATE( DATE_FORMAT( erp_itemledger.transactionDate, '%d/%m/%Y' ), '%d/%m/%Y' ) <= STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' ) 
                    GROUP BY
                        erp_itemledger.companySystemID,
                        erp_itemledger.itemSystemCode ) as movementGRV_base
                    ) AS movementGRV ON movementGRV.itemSystemCode=stockMainQuery.itemSystemCode
                GROUP BY
                    stockMainQuery.companySystemID,
                    stockMainQuery.itemSystemCode 
                    ) AS finalquery
                    LEFT JOIN (
                    SELECT
                    erp_itemledger.companySystemID,
                    erp_itemledger.companyID,
                    erp_itemledger.itemSystemCode,
                    /*erp_itemledger.wacRpt as costPerUnitRpt,
                    erp_itemledger.wacLocal as costPerUnitLocal,*/
                    if(round(sum(erp_itemledger.inOutQty),2)=0,0,round((sum((erp_itemledger.inOutQty*erp_itemledger.wacRpt))/round(sum(erp_itemledger.inOutQty),2)),2)) as costPerUnitRpt,
                    if(round(sum(erp_itemledger.inOutQty),2)=0,0,round((sum((erp_itemledger.inOutQty*erp_itemledger.wacLocal))/round(sum(erp_itemledger.inOutQty),2)),2)) as costPerUnitLocal,
                    sum( erp_itemledger.inOutQty ) totalQty,
                    sum( round( ( erp_itemledger.inOutQty * erp_itemledger.wacRpt ), 2 ) ) AS wacValueRpt,
                    sum( round( ( erp_itemledger.inOutQty * erp_itemledger.wacLocal ), 2 ) ) AS wacValueLocal 
                FROM
                    erp_itemledger 
                WHERE
                    erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                    AND erp_itemledger.fromDamagedTransactionYN = 0 
                    AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                    AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                    AND STR_TO_DATE( DATE_FORMAT( erp_itemledger.transactionDate, '%d/%m/%Y' ), '%d/%m/%Y' ) <= STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' ) 
                GROUP BY
                    erp_itemledger.companySystemID,
                    erp_itemledger.itemSystemCode
                    ) AS getTotalQtyandCost ON getTotalQtyandCost.companySystemID=finalquery.companySystemID AND getTotalQtyandCost.itemSystemCode=finalquery.itemSystemCode
                    LEFT JOIN (
                    SELECT
                    erp_itemledger.companySystemID,
                    erp_itemledger.companyID,
                    erp_itemledger.itemSystemCode,
                    sum( erp_itemledger.inOutQty ) * -1 TotalUnitsIssue,
                    sum( round( ( erp_itemledger.inOutQty * erp_itemledger.wacRpt ), 2 ) ) * -1 AS TotalCostIssue_Rpt,
                    sum( round( ( erp_itemledger.inOutQty * erp_itemledger.wacLocal ), 3 ) ) * -1 AS TotalCostIssue_local,
                    round(sum( ( erp_itemledger.inOutQty * erp_itemledger.wacRpt ) ) / sum( erp_itemledger.inOutQty ),2)  AS CostPerUnitIssue_Rpt,
                    round(sum( ( erp_itemledger.inOutQty * erp_itemledger.wacLocal ) ) / sum( erp_itemledger.inOutQty ),3) AS CostPerUnitIssue_Local 
                FROM
                    erp_itemledger 
                WHERE
                    erp_itemledger.companySystemID IN (" . join(',', $subCompanies) . ") 
                    AND erp_itemledger.documentSystemID = 8 
                    AND erp_itemledger.fromDamagedTransactionYN = 0 
                    AND erp_itemledger.wareHouseSystemCode IN (" . join(',', json_decode($warehouse)) . ")
                    AND erp_itemledger.serviceLineSystemID IN (" . join(',', json_decode($segment)) . ")
                    AND STR_TO_DATE( DATE_FORMAT( erp_itemledger.transactionDate, '%d/%m/%Y' ), '%d/%m/%Y' ) BETWEEN STR_TO_DATE( '".$fromDate ."', '%d/%m/%Y' ) 
                    AND  STR_TO_DATE( '".$toDate ."', '%d/%m/%Y' ) 
                GROUP BY
                    erp_itemledger.companySystemID,
                    erp_itemledger.itemSystemCode
                    ) AS getIssuedQtyandCost ON getIssuedQtyandCost.companySystemID=finalquery.companySystemID AND getIssuedQtyandCost.itemSystemCode=finalquery.itemSystemCode
                    INNER JOIN
                    itemmaster ON itemmaster.itemCodeSystem=finalquery.itemSystemCode AND itemmaster.financeCategoryMaster=1
                    LEFT JOIN units ON units.UnitID = itemmaster.unit $finalOrderBy";
        return DB::select($sql);
    }
}

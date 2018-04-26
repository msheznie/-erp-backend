<?php
/**
 * =============================================
 * -- File Name : ReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 9 - April 2018
 * -- Description : This file contains the all the repord generation code
 * -- REVISION HISTORY
 * --
 * --
 * --
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportAPIController extends AppBaseController
{
    /*validate each report*/
    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'POA':
                $validatedData = $request->validate([
                    'daterange' => 'required',
                    'suppliers' => 'required',
                    'reportType' => 'required',
                ]);
                break;
            default:
                return $this->sendError('Error Occurred');
        }

    }

    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = 'POA';
        switch ($reportID) {
            case 'POA': //PO Analysis Report
                $validatedData = $request->validate([
                    'daterange' => 'required',
                    'suppliers' => 'required',
                    'reportType' => 'required',
                ]);

                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }


                $startDate = new Carbon($request->daterange[0]);
                $startDate = $startDate->addDays(1);
                $startDate = $startDate->format('Y-m-d');

                $endDate = new Carbon($request->daterange[1]);
                $endDate = $endDate->addDays(1);
                $endDate = $endDate->format('Y-m-d');


                $companyID = "";
                $checkIsGroup = Company::find($request->companySystemID);
                if ($checkIsGroup->isGroup) {
                    $companyID = \Helper::getGroupCompany($request->companySystemID);
                } else {
                    $companyID = (array)$request->companySystemID;
                }

                $suppliers = (array)$request->suppliers;
                $suppliers = collect($suppliers)->pluck('supplierCodeSytem');


                if($request->reportType == 1) { //PO Analysis Item Detail Report
                    $output = DB::table('erp_purchaseorderdetails')
                        ->join(DB::raw('(SELECT locationName,
                    ServiceLineDes as segment,
                    purchaseOrderID,
                    erp_purchaseordermaster.companyID,
                    locationName as location,
                    approved,
                    YEAR ( approvedDate ) AS postingYear,
                    approvedDate AS orderDate,
                    purchaseOrderCode,IF( sentToSupplier = 0, "Not Released", "Released" ) AS STATUS,
                    supplierID,
                    supplierPrimaryCode,
                    supplierName,
                    creditPeriod,
                    deliveryTerms,
                    paymentTerms,
                    expectedDeliveryDate,
                    narration,
                    approvedDate,
                    erp_purchaseordermaster.companySystemID
                     FROM erp_purchaseordermaster 
                     LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID 
                     LEFT JOIN erp_location ON poLocation = erp_location.locationID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderMasterID', '=', 'podet.purchaseOrderID');
                            $query->whereBetween('podet.approvedDate', [$startDate, $endDate])
                                ->where('podet.approved', -1)
                                ->whereIN('podet.companySystemID', $companyID);
                        })->leftJoin('financeitemcategorymaster', function ($query) {
                            $query->on('itemFinanceCategoryID', '=', 'itemCategoryID');
                            $query->select('categoryDescription');
                        })->leftJoin(DB::raw('(SELECT categoryDescription as financecategorysub,AccountDescription AS finance_gl_code_pl,AccountCode,itemCategorySubID FROM financeitemcategorysub LEFT JOIN chartofaccounts ON financeGLcodePLSystemID = chartOfAccountSystemID) as catSub'), function ($query) {
                            $query->on('itemFinanceCategorySubID', '=', 'catSub.itemCategorySubID');
                        })
                        ->leftJoin('units', 'unitOfMeasure', '=', 'UnitID')
                        ->leftJoin('erp_grvdetails as gdet', function ($join) use ($companyID) {
                            $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet.purchaseOrderDetailsID');
                            $join->whereIN('gdet.companySystemID', $companyID);
                            $join->selectRaw('SUM(gdet.noQty) as noQty');
                            $join->groupBy('gdet.purchaseOrderDetailsID');
                        })->leftJoin(
                            DB::raw('(SELECT
    max(erp_grvmaster.grvDate) AS lastOfgrvDate,
    erp_grvdetails.purchaseOrderDetailsID 
FROM
    (
    erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
    ) 
WHERE
    erp_grvmaster.approved=- 1 and purchaseOrderDetailsID>0 GROUP BY erp_grvdetails.purchaseOrderMastertID,erp_grvdetails.purchaseOrderDetailsID,erp_grvdetails.itemCode) as gdet2'),
                            function ($join) use ($companyID) {
                                $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet2.purchaseOrderDetailsID');
                            })->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,
                        erp_purchaseorderdetails.purchaseOrderDetailsID,
                        gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((erp_purchaseorderdetails.noQty-gdet.noQty) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Recieved","Partially Recieved")) as receivedStatus,
                    IFNULL((erp_purchaseorderdetails.noQty-gdet.noQty),0) as qtyToReceive,
                    IFNULL(gdet.noQty,0) as qtyReceived,
                    erp_purchaseorderdetails.itemFinanceCategoryID,
                    erp_purchaseorderdetails.itemFinanceCategorySubID,
                    erp_purchaseorderdetails.itemPrimaryCode,
                    erp_purchaseorderdetails.itemDescription,
                    erp_purchaseorderdetails.supplierPartNumber,
                    erp_purchaseorderdetails.noQty,( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) AS unitCostWithOutDiscount,
                    erp_purchaseorderdetails.GRVcostPerUnitComRptCur as unitCostWithDiscount,
                    erp_purchaseorderdetails.discountPercentage,
                    ( ( ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) ) - erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS discountAmount,
                    ( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS total,
                    financeitemcategorymaster.categoryDescription as financecategory,
                    catSub.*,
                    units.UnitShortCode AS unitShortCode,
                    podet.*')
                        ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID);

                    $search = $request->input('search.value');
                    if ($search) {
                        $output = $output->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$search}%")
                            ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$search}%");
                    }


                    return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    $query->orderBy('purchaseOrderDetailsID', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->make(true);

                }else if($request->reportType == 2){  //PO Wise Analysis Report
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('erp_purchaseordermaster.companyID,
                            erp_purchaseordermaster.purchaseOrderCode,
                            erp_purchaseordermaster.narration,
                            erp_purchaseordermaster.approvedDate as orderDate,
                            erp_purchaseordermaster.serviceLine,
                            erp_purchaseordermaster.supplierPrimaryCode,
                            erp_purchaseordermaster.supplierName,
                            erp_purchaseordermaster.expectedDeliveryDate,
                            erp_purchaseordermaster.budgetYear,
                            erp_purchaseordermaster.purchaseOrderID,
                            IFNULL(podet.TotalPOVal,0) as TotalPOVal,
                            IFNULL(podet.POQty,0) as POQty,
                            podet.Type,
                            IFNULL(podet.POCapex,0) as POCapex,
                            IFNULL(podet.POOpex,0) as POOpex,
                            IFNULL(grvdet.GRVQty,0) as GRVQty,
                            IFNULL(grvdet.TotalGRVValue,0) as TotalGRVValue,
                            IFNULL(grvdet.GRVCapex,0) as GRVCapex,
                            IFNULL(grvdet.GRVOpex,0) as GRVOpex,
                            (IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            (IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance,
                            ServiceLineDes as segment'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, 0, ( noQty * GRVcostPerUnitComRptCur ) ) ) AS POOpex
                     FROM erp_purchaseorderdetails GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                            $query->whereIN('podet.companySystemID', $companyID);
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID = 3, 0, ( noQty * GRVcostPerUnitComRptCur ) ) ) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                            $join->where('grvdet.purchaseOrderMastertID','<>', 0);
                            $join->where('grvdet.approved','=', -1);
                            $join->whereIN('grvdet.companySystemID', $companyID);
                        })
                        ->leftJoin('serviceline','erp_purchaseordermaster.serviceLineSystemID','=','serviceline.serviceLineSystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N','<>',5)->where('erp_purchaseordermaster.approved','=',-1)->where('erp_purchaseordermaster.poCancelledYN','=',0);

                    $search = $request->input('search.value');
                    $search = str_replace("\\","\\\\",$search);
                    if ($search) {
                        $output = $output->where('erp_purchaseordermaster.purchaseOrderCode', 'LIKE', "%{$search}%")
                            ->orWhere('erp_purchaseordermaster.supplierPrimaryCode', 'LIKE', "%{$search}%")->orWhere('erp_purchaseordermaster.supplierName', 'LIKE', "%{$search}%");
                    }

                    $outputSUM = $output->get();

                    $POCapex = collect($outputSUM)->pluck('POCapex')->toArray();
                    $POCapex = array_sum($POCapex);

                    $POCapex = collect($outputSUM)->pluck('POCapex')->toArray();
                    $POCapex = array_sum($POCapex);

                    $TotalPOVal = collect($outputSUM)->pluck('TotalPOVal')->toArray();
                    $TotalPOVal = array_sum($TotalPOVal);

                    $TotalGRVValue = collect($outputSUM)->pluck('TotalGRVValue')->toArray();
                    $TotalGRVValue = array_sum($TotalGRVValue);

                    $GRVCapex = collect($outputSUM)->pluck('GRVCapex')->toArray();
                    $GRVCapex = array_sum($GRVCapex);

                    $GRVOpex = collect($outputSUM)->pluck('GRVOpex')->toArray();
                    $GRVOpex = array_sum($GRVOpex);

                    $capexBalance = collect($outputSUM)->pluck('capexBalance')->toArray();
                    $capexBalance = array_sum($capexBalance);

                    $opexBalance = collect($outputSUM)->pluck('opexBalance')->toArray();
                    $opexBalance = array_sum($opexBalance);


                    $dataRec = \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->with('totalAmount', [
                            'POCapex' => $POCapex,
                            'POOpex' => $POCapex,
                            'TotalGRVValue' => $TotalGRVValue,
                            'GRVCapex' => $GRVCapex,
                            'GRVOpex' => $GRVOpex,
                            'capexBalance' => $capexBalance,
                            'opexBalance' => $opexBalance,
                            'TotalPOVal' => $TotalPOVal,
                        ])
                        ->make(true);

                    return $dataRec;
                }

            default:
                return $this->sendError('Error Occurred');
        }
    }

    public function exportReport(Request $request)
    {
        $reportID = 'POA';
        switch ($reportID) {
            case 'POA':
                $validatedData = $request->validate([
                    'daterange' => 'required',
                    'suppliers' => 'required',
                ]);


                $startDate = new Carbon($request->daterange[0]);
                $startDate = $startDate->addDays(1);
                $startDate = $startDate->format('Y-m-d');

                $endDate = new Carbon($request->daterange[1]);
                $endDate = $endDate->addDays(1);
                $endDate = $endDate->format('Y-m-d');

                $companyID = "";
                $checkIsGroup = Company::find($request->companySystemID);
                if ($checkIsGroup->isGroup) {
                    $companyID = \Helper::getGroupCompany($request->companySystemID);
                } else {
                    $companyID = (array)$request->companySystemID;
                }
                $type = $request->type;

                $suppliers = (array)$request->suppliers;
                $suppliers = collect($suppliers)->pluck('supplierCodeSytem');

                if($request->reportType == 1) {
                    $output = DB::table('erp_purchaseorderdetails')
                        ->join(DB::raw('(SELECT locationName,
                    ServiceLineDes as segment,
                    purchaseOrderID,
                    erp_purchaseordermaster.companyID,
                    locationName as location,
                    approved,
                    YEAR ( approvedDate ) AS postingYear,
                    approvedDate AS orderDate,
                    purchaseOrderCode,IF( sentToSupplier = 0, "Not Released", "Released" ) AS STATUS,
                    supplierID,
                    supplierPrimaryCode,
                    supplierName,
                    creditPeriod,
                    deliveryTerms,
                    paymentTerms,
                    expectedDeliveryDate,
                    narration,
                    approvedDate,
                    erp_purchaseordermaster.companySystemID
                     FROM erp_purchaseordermaster 
                     LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID 
                     LEFT JOIN erp_location ON poLocation = erp_location.locationID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderMasterID', '=', 'podet.purchaseOrderID');
                            $query->whereBetween('podet.approvedDate', [$startDate, $endDate])
                                ->where('podet.approved', -1)
                                ->whereIN('podet.companySystemID', $companyID);
                        })->leftJoin('financeitemcategorymaster', function ($query) {
                            $query->on('itemFinanceCategoryID', '=', 'itemCategoryID');
                            $query->select('categoryDescription');
                        })->leftJoin(DB::raw('(SELECT categoryDescription as financecategorysub,AccountDescription AS finance_gl_code_pl,AccountCode,itemCategorySubID FROM financeitemcategorysub LEFT JOIN chartofaccounts ON financeGLcodePLSystemID = chartOfAccountSystemID) as catSub'), function ($query) {
                            $query->on('itemFinanceCategorySubID', '=', 'catSub.itemCategorySubID');
                        })
                        ->leftJoin('units', 'unitOfMeasure', '=', 'UnitID')
                        ->leftJoin('erp_grvdetails as gdet', function ($join) use ($companyID) {
                            $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet.purchaseOrderDetailsID');
                            $join->whereIN('gdet.companySystemID', $companyID);
                            $join->selectRaw('SUM(gdet.noQty) as noQty');
                            $join->groupBy('gdet.purchaseOrderDetailsID');
                        })->leftJoin(
                            DB::raw('(SELECT
    max(erp_grvmaster.grvDate) AS lastOfgrvDate,
    erp_grvdetails.purchaseOrderDetailsID 
FROM
    (
    erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
    ) 
WHERE
    erp_grvmaster.approved=- 1 and purchaseOrderDetailsID>0 GROUP BY erp_grvdetails.purchaseOrderMastertID,erp_grvdetails.purchaseOrderDetailsID,erp_grvdetails.itemCode) as gdet2'),
                            function ($join) use ($companyID) {
                                $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet2.purchaseOrderDetailsID');
                            })->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,
                        erp_purchaseorderdetails.purchaseOrderDetailsID,
                        gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((erp_purchaseorderdetails.noQty-gdet.noQty) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Recieved","Partially Recieved")) as receivedStatus,
                    IFNULL((erp_purchaseorderdetails.noQty-gdet.noQty),0) as qtyToReceive,
                    IFNULL(gdet.noQty,0) as qtyReceived,
                    erp_purchaseorderdetails.itemFinanceCategoryID,
                    erp_purchaseorderdetails.itemFinanceCategorySubID,
                    erp_purchaseorderdetails.itemPrimaryCode,
                    erp_purchaseorderdetails.itemDescription,
                    erp_purchaseorderdetails.supplierPartNumber,
                    erp_purchaseorderdetails.noQty,
                    IFNULL(( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ),"0") AS unitCostWithOutDiscount,
                    IFNULL(erp_purchaseorderdetails.GRVcostPerUnitComRptCur,"0") as unitCostWithDiscount,
                    IFNULL(erp_purchaseorderdetails.discountPercentage,"0") as discountPercentage,
                    IFNULL(( ( ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) ) - erp_purchaseorderdetails.GRVcostPerUnitComRptCur ),"0") AS discountAmount,
                    IFNULL(( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ),"0") AS total,
                    financeitemcategorymaster.categoryDescription as financecategory,
                    catSub.*,
                    units.UnitShortCode AS unitShortCode,
                    podet.*')
                        ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID)->get();

                    foreach ($output as $val) {
                        $data[] = array(
                            'CompanyID' => $val->companyID,
                            'Posting Year' => $val->postingYear,
                            'Order Date' => \Helper::dateFormat($val->orderDate),
                            'PO Code' => $val->purchaseOrderCode,
                            'Status' => $val->STATUS,
                            'Location' => $val->location,
                            'Supplier Code' => $val->supplierPrimaryCode,
                            'Supplier Name' => $val->supplierName,
                            'Credit Period' => $val->creditPeriod,
                            'Delivery Terms' => $val->deliveryTerms,
                            'Payment Terms' => $val->paymentTerms,
                            'Expected Delivery Date' => \Helper::dateFormat($val->expectedDeliveryDate),
                            'Narration' => $val->narration,
                            'Segment' => $val->segment,
                            'Item Code' => $val->itemPrimaryCode,
                            'Item Description' => $val->itemDescription,
                            'Unit' => $val->unitShortCode,
                            'Part No' => $val->supplierPartNumber,
                            'Finance Category' => $val->financecategory,
                            'Finance Category Sub' => $val->financecategorysub,
                            'Account Code' => $val->AccountCode,
                            'Account Description' => $val->finance_gl_code_pl,
                            'PO Qty' => $val->noQty,
                            'Unit Cost without Discount' => number_format($val->unitCostWithOutDiscount, 2),
                            'Unit Cost with Discount' => number_format($val->unitCostWithDiscount, 2),
                            'Discount Percentage' => $val->discountPercentage,
                            'Discount Amount' => number_format($val->discountAmount, 2),
                            'Total' => number_format($val->total, 2),
                            'Qty Received' => $val->qtyReceived,
                            'Qty To Receive' => $val->qtyToReceive,
                            'Received Status' => $val->receivedStatus,
                            'Receipt Date' => $val->lastOfgrvDate,
                        );
                    }

                    $csv = \Excel::create('item_wise_po_analysis', function ($excel) use ($data) {

                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data);
                            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    return $this->sendResponse(array(), 'successfully export');
                }else  if($request->reportType == 2) {
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('erp_purchaseordermaster.companyID,
                            erp_purchaseordermaster.purchaseOrderCode,
                            erp_purchaseordermaster.narration,
                            erp_purchaseordermaster.approvedDate as orderDate,
                            erp_purchaseordermaster.serviceLine,
                            erp_purchaseordermaster.supplierPrimaryCode,
                            erp_purchaseordermaster.supplierName,
                            erp_purchaseordermaster.expectedDeliveryDate,
                            erp_purchaseordermaster.budgetYear,
                            erp_purchaseordermaster.purchaseOrderID,
                            IFNULL(podet.TotalPOVal,0) as TotalPOVal,
                            IFNULL(podet.POQty,0) as POQty,
                            podet.Type,
                            IFNULL(podet.POCapex,"0") as POCapex,
                            IFNULL(podet.POOpex,"0") as POOpex,
                            IFNULL(grvdet.GRVQty,"0") as GRVQty,
                            IFNULL(grvdet.TotalGRVValue,"0") as TotalGRVValue,
                            IFNULL(grvdet.GRVCapex,"0") as GRVCapex,
                            IFNULL(grvdet.GRVOpex,"0") as GRVOpex,
                            IFNULL((IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)),"0") as capexBalance,
                            IFNULL((IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)),"0") as opexBalance,
                            ServiceLineDes as segment,
                            erp_purchaseordermaster.purchaseOrderID'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, 0, ( noQty * GRVcostPerUnitComRptCur ) ) ) AS POOpex
                     FROM erp_purchaseorderdetails GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                            $query->whereIN('podet.companySystemID', $companyID);
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID = 3, 0, ( noQty * GRVcostPerUnitComRptCur ) ) ) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                            $join->where('grvdet.purchaseOrderMastertID','<>', 0);
                            $join->where('grvdet.approved','=', -1);
                            $join->whereIN('grvdet.companySystemID', $companyID);
                        })
                        ->leftJoin('serviceline','erp_purchaseordermaster.serviceLineSystemID','=','serviceline.serviceLineSystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N','<>',5)->where('erp_purchaseordermaster.approved','=',-1)->where('erp_purchaseordermaster.poCancelledYN','=',0)->orderBy('purchaseOrderID','desc')->get();

                    foreach ($output as $val) {
                        $data[] = array(
                            'CompanyID' => $val->companyID,
                            'PO Code' => $val->purchaseOrderCode,
                            'Segment' => $val->segment,
                            'Narration' => $val->narration,
                            'Order Date' => \Helper::dateFormat($val->orderDate),
                            'Expected Delivery Date' => \Helper::dateFormat($val->expectedDeliveryDate),
                            'Supplier Code' => $val->supplierPrimaryCode,
                            'Supplier Name' => $val->supplierName,
                            'Budget Year' => $val->budgetYear,
                            'PO Capex Amount' => $val->POCapex,
                            'PO Opex Amount' => $val->POOpex,
                            'Total PO Amount' => $val->TotalPOVal,
                            'GRV Capex Amount' => $val->GRVCapex,
                            'GRV Opex Amount' => $val->GRVOpex,
                            'Total GRV Amount' => $val->TotalGRVValue,
                            'Capex Balance' => $val->capexBalance,
                            'Opex Balance' => $val->opexBalance
                        );
                    }

                    $csv = \Excel::create('po_wise_analysis', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data);
                            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    return $this->sendResponse(array(), 'successfully export');
                }
            default:
                return $this->sendError('Error Occurred');
        }
    }

}

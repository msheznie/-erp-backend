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
 * -- Date: 18-june 2018 By: Mubashir Description: Added new functions named as pdfExportReport(),
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
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
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'suppliers' => 'required',
                    'reportType' => 'required',
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            default:
                return $this->sendError('No report ID found');
        }

    }

    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'POA': //PO Analysis Report

                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }

                $startDate = new Carbon($request->fromDate);
                //$startDate = $startDate->addDays(1);
                $startDate = $startDate->format('Y-m-d');

                $endDate = new Carbon($request->toDate);
                //$endDate = $endDate->addDays(1);
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

                if (isset($request->selectedSupplier)) {
                    if (!empty($request->selectedSupplier)) {
                        $suppliers = collect($request->selectedSupplier);
                    }
                }

                if ($request->reportType == 1) { //PO Analysis Item Detail Report
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
                    erp_purchaseordermaster.companySystemID,
                    supCont.countryName
                     FROM erp_purchaseordermaster 
                     LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID
                     LEFT JOIN (SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont ON  supCont.supplierCodeSystem = erp_purchaseordermaster.supplierID
                     LEFT JOIN erp_location ON poLocation = erp_location.locationID WHERE approved = -1 AND poType_N <>5 AND (approvedDate BETWEEN "' . $startDate . '" AND "' . $endDate . '") AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ') AND erp_purchaseordermaster.supplierID IN (' . join(',', json_decode($suppliers)) . ')) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderMasterID', '=', 'podet.purchaseOrderID');
                        })->leftJoin('financeitemcategorymaster', function ($query) {
                            $query->on('itemFinanceCategoryID', '=', 'itemCategoryID');
                            $query->select('categoryDescription');
                        })->leftJoin(DB::raw('(SELECT categoryDescription as financecategorysub,AccountDescription AS finance_gl_code_pl,AccountCode,itemCategorySubID FROM financeitemcategorysub LEFT JOIN chartofaccounts ON financeGLcodePLSystemID = chartOfAccountSystemID) as catSub'), function ($query) {
                            $query->on('itemFinanceCategorySubID', '=', 'catSub.itemCategorySubID');
                        })
                        ->leftJoin('units', 'unitOfMeasure', '=', 'UnitID')
                        ->leftJoin(DB::raw('(SELECT SUM(noQty) as noQty,purchaseOrderDetailsID FROM erp_grvdetails WHERE erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderDetailsID) as gdet'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet.purchaseOrderDetailsID');
                        })->leftJoin(
                            DB::raw('(SELECT
    max(erp_grvmaster.grvDate) AS lastOfgrvDate,
    erp_grvdetails.purchaseOrderDetailsID 
FROM
    (
    erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
    ) 
WHERE
    purchaseOrderDetailsID>0 AND erp_grvmaster.companySystemID IN (' . join(',', $companyID) . ') GROUP BY erp_grvdetails.purchaseOrderMastertID,erp_grvdetails.purchaseOrderDetailsID,erp_grvdetails.itemCode) as gdet2'),
                            function ($join) use ($companyID) {
                                $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet2.purchaseOrderDetailsID');
                            })->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,
                        erp_purchaseorderdetails.purchaseOrderDetailsID,
                        gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((erp_purchaseorderdetails.noQty-gdet.noQty) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Received","Partially Received")) as receivedStatus,
                    IFNULL((erp_purchaseorderdetails.noQty-gdet.noQty),0) as qtyToReceive,
                    IFNULL(gdet.noQty,0) as qtyReceived,
                    erp_purchaseorderdetails.itemFinanceCategoryID,
                    erp_purchaseorderdetails.itemFinanceCategorySubID,
                    erp_purchaseorderdetails.itemCode,
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

                    /*$search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $output = $output->where(function ($query) use ($search) {
                            $query->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$search}%")
                                ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$search}%")
                                ->orWhere('supplierName', 'LIKE', "%{$search}%")
                                ->orWhere('purchaseOrderCode', 'LIKE', "%{$search}%");
                        });
                    }*/

                    if (isset($request->searchText)) {
                        if (!empty($request->searchText)) {
                            $search = str_replace("\\", "\\\\", $request->searchText);
                            $output = $output->where(function ($query) use ($search) {
                                $query->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$search}%")
                                    ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$search}%")
                                    ->orWhere('supplierName', 'LIKE', "%{$search}%")
                                    ->orWhere('purchaseOrderCode', 'LIKE', "%{$search}%");
                            });
                        }
                    }

                    if (isset($request->grvStatus)) {
                        if (!empty($request->grvStatus)) {
                            $output = $output->having('receivedStatus', $request->grvStatus);
                        }
                    }

                    $output->orderBy('podet.approvedDate', 'ASC');

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

                } else if ($request->reportType == 2) {  //PO Wise Analysis Report
                    //DB::enableQueryLog();
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
                            supCont.countryName,
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
                            ServiceLineDes as segment,
                            IFNULL(adv.AdvanceReleased,0) as advanceReleased,
                            IFNULL(adv.LogisticAdvanceReleased,0) as logisticAdvanceReleased,
                            IFNULL(pr.paymentComRptAmount,0) as paymentReleased,
                            (IFNULL(podet.TotalPOVal,0) - IFNULL(pr.paymentComRptAmount,0) - IFNULL(adv.AdvanceReleased,0)) as balanceToBePaid'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                        })
                        ->leftJoin(DB::raw('(SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.supplierID', '=', 'supCont.supplierCodeSystem');
                        })
                        ->leftJoin(DB::raw('(SELECT
	erp_paysupplierinvoicemaster.companySystemID,
	erp_paysupplierinvoicemaster.companyID,
	erp_advancepaymentdetails.purchaseOrderID,
	sum( erp_advancepaymentdetails.comRptAmount ),
IF
	( poTermID = 0, 0, ( sum( comRptAmount ) ) ) AS AdvanceReleased,
IF
	( poTermID = 0, ( sum( comRptAmount ) ), 0 ) AS LogisticAdvanceReleased 
FROM
	erp_paysupplierinvoicemaster
	INNER JOIN erp_advancepaymentdetails ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_advancepaymentdetails.PayMasterAutoId
	INNER JOIN erp_purchaseorderadvpayment ON erp_advancepaymentdetails.poAdvPaymentID = erp_purchaseorderadvpayment.poAdvPaymentID 
WHERE
	erp_advancepaymentdetails.purchaseOrderID > 0 
	AND erp_paysupplierinvoicemaster.approved =- 1 
	AND erp_paysupplierinvoicemaster.cancelYN = 0 
	AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
GROUP BY
	purchaseOrderID,companySystemID) adv'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'adv.purchaseOrderID');
                            $join->on('erp_purchaseordermaster.companySystemID', '=', 'adv.companySystemID');
                        })
                        ->leftJoin(DB::raw('(SELECT
    erp_paysupplierinvoicemaster.companySystemID,
    erp_paysupplierinvoicemaster.companyID,
    erp_bookinvsuppdet.purchaseOrderID,
    sum(erp_bookinvsuppdet.totRptAmount) as paymentComRptAmount
FROM
    erp_paysupplierinvoicemaster
    INNER JOIN erp_paysupplierinvoicedetail ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId
    INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppdet.bookingSuppMasInvAutoID=erp_paysupplierinvoicedetail.bookingInvSystemCode
WHERE
    erp_paysupplierinvoicemaster.approved=- 1 AND  erp_paysupplierinvoicemaster.cancelYN=0
    AND erp_paysupplierinvoicedetail.addedDocumentSystemID=11
    AND erp_paysupplierinvoicedetail.matchingDocID = 0
     AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
Group By erp_paysupplierinvoicemaster.companySystemID,erp_bookinvsuppdet.purchaseOrderID) pr'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'pr.purchaseOrderID');
                            $join->on('erp_purchaseordermaster.companySystemID', '=', 'pr.companySystemID');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween(DB::raw("DATE(approvedDate)"), array($startDate, $endDate));

                    $search = $request->input('search.value');
                    $search = str_replace("\\", "\\\\", $search);
                    if ($search) {
                        $output = $output->where('erp_purchaseordermaster.purchaseOrderCode', 'LIKE', "%{$search}%")
                            ->orWhere('erp_purchaseordermaster.supplierPrimaryCode', 'LIKE', "%{$search}%")->orWhere('erp_purchaseordermaster.supplierName', 'LIKE', "%{$search}%");
                    }
                    $output->orderBy('approvedDate', 'ASC');
                    $outputSUM = $output->get();
                    //dd(DB::getQueryLog());


                    $POCapex = collect($outputSUM)->pluck('POCapex')->toArray();
                    $POCapex = array_sum($POCapex);

                    $POOpex = collect($outputSUM)->pluck('POOpex')->toArray();
                    $POOpex = array_sum($POOpex);

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

                    $advanceReleased = collect($outputSUM)->pluck('advanceReleased')->toArray();
                    $advanceReleased = array_sum($advanceReleased);

                    $logisticAdvanceReleased = collect($outputSUM)->pluck('logisticAdvanceReleased')->toArray();
                    $logisticAdvanceReleased = array_sum($logisticAdvanceReleased);

                    $paymentReleased = collect($outputSUM)->pluck('paymentReleased')->toArray();
                    $paymentReleased = array_sum($paymentReleased);

                    $balanceToBePaid = collect($outputSUM)->pluck('balanceToBePaid')->toArray();
                    $balanceToBePaid = array_sum($balanceToBePaid);

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
                            'POOpex' => $POOpex,
                            'TotalGRVValue' => $TotalGRVValue,
                            'GRVCapex' => $GRVCapex,
                            'GRVOpex' => $GRVOpex,
                            'capexBalance' => $capexBalance,
                            'opexBalance' => $opexBalance,
                            'TotalPOVal' => $TotalPOVal,
                            'advanceReleased' => $advanceReleased,
                            'logisticAdvanceReleased' => $logisticAdvanceReleased,
                            'paymentReleased' => $paymentReleased,
                            'balanceToBePaid' => $balanceToBePaid,
                        ])
                        ->make(true);

                    return $dataRec;
                }
                else if ($request->reportType == 3) { //PO Wise Analysis Company wise Report
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('
                            companymaster.CompanyID,                      
                            companymaster.CompanyName,                      
                            SUM(IFNULL(podet.TotalPOVal,0)) as TotalPOVal,
                            SUM(IFNULL(podet.POQty,0)) as POQty, 
                            SUM(IFNULL(podet.POCapex,0)) as POCapex,
                            SUM(IFNULL(podet.POOpex,0)) as POOpex,
                            SUM(IFNULL(grvdet.GRVQty,0)) as GRVQty,
                            SUM(IFNULL(grvdet.TotalGRVValue,0)) as TotalGRVValue,
                            SUM(IFNULL(grvdet.GRVCapex,0)) as GRVCapex,
                            SUM(IFNULL(grvdet.GRVOpex,0)) as GRVOpex,
                            SUM(IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            SUM(IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->leftJoin('companymaster', 'erp_purchaseordermaster.companySystemID', '=', 'companymaster.companySystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween(DB::raw("DATE(approvedDate)"), array($startDate, $endDate))->groupBy('erp_purchaseordermaster.companySystemID');

                    $search = $request->input('search.value');
                    $search = str_replace("\\", "\\\\", $search);
                    if ($search) {
                        $output = $output->where('companymaster.CompanyName', 'LIKE', "%{$search}%");
                    }
                    $output->orderBy('companymaster.CompanyName', 'ASC');
                    $outputSUM = $output->get();
                    //dd(DB::getQueryLog());


                    $POCapex = collect($outputSUM)->pluck('POCapex')->toArray();
                    $POCapex = array_sum($POCapex);

                    $POOpex = collect($outputSUM)->pluck('POOpex')->toArray();
                    $POOpex = array_sum($POOpex);

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
                            'POOpex' => $POOpex,
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
                else if ($request->reportType == 4) { //PO Wise Analysis Supplier wise Report
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('
                            companymaster.CompanyID,                      
                            companymaster.CompanyName, 
                            supplierPrimaryCode as supplierID,
                            supplierName,                     
                            supCont.countryName,                     
                            SUM(IFNULL(podet.TotalPOVal,0)) as TotalPOVal,
                            SUM(IFNULL(podet.POQty,0)) as POQty, 
                            SUM(IFNULL(podet.POCapex,0)) as POCapex,
                            SUM(IFNULL(podet.POOpex,0)) as POOpex,
                            SUM(IFNULL(grvdet.GRVQty,0)) as GRVQty,
                            SUM(IFNULL(grvdet.TotalGRVValue,0)) as TotalGRVValue,
                            SUM(IFNULL(grvdet.GRVCapex,0)) as GRVCapex,
                            SUM(IFNULL(grvdet.GRVOpex,0)) as GRVOpex,
                            SUM(IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            SUM(IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                        })
                        ->leftJoin(DB::raw('(SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.supplierID', '=', 'supCont.supplierCodeSystem');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->leftJoin('companymaster', 'erp_purchaseordermaster.companySystemID', '=', 'companymaster.companySystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween(DB::raw("DATE(approvedDate)"), array($startDate, $endDate))->groupBy('supplierID');

                    $search = $request->input('search.value');
                    $search = str_replace("\\", "\\\\", $search);
                    if ($search) {
                        $output = $output->where('supplierName', 'LIKE', "%{$search}%");
                    }
                    $output->orderBy('supplierPrimaryCode', 'ASC');
                    $outputSUM = $output->get();
                    //dd(DB::getQueryLog());


                    $POCapex = collect($outputSUM)->pluck('POCapex')->toArray();
                    $POCapex = array_sum($POCapex);

                    $POOpex = collect($outputSUM)->pluck('POOpex')->toArray();
                    $POOpex = array_sum($POOpex);

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
                            'POOpex' => $POOpex,
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
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function exportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'POA':
                $validatedData = $request->validate([
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'suppliers' => 'required',
                ]);


                $startDate = new Carbon($request->fromDate);
                //$startDate = $startDate->addDays(1);
                $startDate = $startDate->format('Y-m-d');

                $endDate = new Carbon($request->toDate);
                //$endDate = $endDate->addDays(1);
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

                if (isset($request->selectedSupplier)) {
                    if (!empty($request->selectedSupplier)) {
                        $suppliers = collect($request->selectedSupplier);
                    }
                }

                if ($request->reportType == 1) {
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
                    erp_purchaseordermaster.companySystemID,
                    supCont.countryName
                     FROM erp_purchaseordermaster 
                     LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID 
                     LEFT JOIN (SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont ON  supCont.supplierCodeSystem = erp_purchaseordermaster.supplierID
                     LEFT JOIN erp_location ON poLocation = erp_location.locationID WHERE approved = -1 AND poType_N <>5 AND (approvedDate BETWEEN "' . $startDate . '" AND "' . $endDate . '") AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ') AND erp_purchaseordermaster.supplierID IN (' . join(',', json_decode($suppliers)) . ')) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderMasterID', '=', 'podet.purchaseOrderID');
                        })->leftJoin('financeitemcategorymaster', function ($query) {
                            $query->on('itemFinanceCategoryID', '=', 'itemCategoryID');
                            $query->select('categoryDescription');
                        })->leftJoin(DB::raw('(SELECT categoryDescription as financecategorysub,AccountDescription AS finance_gl_code_pl,AccountCode,itemCategorySubID FROM financeitemcategorysub LEFT JOIN chartofaccounts ON financeGLcodePLSystemID = chartOfAccountSystemID) as catSub'), function ($query) {
                            $query->on('itemFinanceCategorySubID', '=', 'catSub.itemCategorySubID');
                        })
                        ->leftJoin('units', 'unitOfMeasure', '=', 'UnitID')
                        ->leftJoin(DB::raw('(SELECT SUM(noQty) as noQty,purchaseOrderDetailsID FROM erp_grvdetails WHERE erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderDetailsID) as gdet'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet.purchaseOrderDetailsID');
                        })->leftJoin(
                            DB::raw('(SELECT
    max(erp_grvmaster.grvDate) AS lastOfgrvDate,
    erp_grvdetails.purchaseOrderDetailsID 
FROM
    (
    erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID 
    ) 
WHERE
    purchaseOrderDetailsID>0 AND erp_grvmaster.companySystemID IN (' . join(',', $companyID) . ') GROUP BY erp_grvdetails.purchaseOrderMastertID,erp_grvdetails.purchaseOrderDetailsID,erp_grvdetails.itemCode) as gdet2'),
                            function ($join) use ($companyID) {
                                $join->on('erp_purchaseorderdetails.purchaseOrderDetailsID', '=', 'gdet2.purchaseOrderDetailsID');
                            })->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,
                        erp_purchaseorderdetails.purchaseOrderDetailsID,
                        gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((erp_purchaseorderdetails.noQty-gdet.noQty) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Received","Partially Received")) as receivedStatus,
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
                        ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID)->orderBy('podet.approvedDate', 'ASC');

                    if (isset($request->grvStatus)) {
                        if (!empty($request->grvStatus)) {
                            $output = $output->having('receivedStatus', $request->grvStatus);
                        }
                    }

                    if (isset($request->searchText)) {
                        if (!empty($request->searchText)) {
                            $search = str_replace("\\", "\\\\", $request->searchText);
                            $output = $output->where(function ($query) use ($search) {
                                $query->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$search}%")
                                    ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$search}%")
                                    ->orWhere('supplierName', 'LIKE', "%{$search}%")
                                    ->orWhere('purchaseOrderCode', 'LIKE', "%{$search}%");
                            });
                        }
                    }

                    $output = $output->get();
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
                            'Supplier Country' => $val->countryName,
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
                            $sheet->fromArray($data, null, 'A1', true);
                            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    return $this->sendResponse(array(), 'Successfully export');
                } else if ($request->reportType == 2) { //PO Wise Analysis Report
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
                            supCont.countryName,
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
                            ServiceLineDes as segment,
                            IFNULL(adv.AdvanceReleased,0) as advanceReleased,
                            IFNULL(adv.LogisticAdvanceReleased,0) as logisticAdvanceReleased,
                            IFNULL(pr.paymentComRptAmount,0) as paymentReleased,
                            (IFNULL(podet.TotalPOVal,0) - IFNULL(pr.paymentComRptAmount,0) - IFNULL(adv.AdvanceReleased,0)) as balanceToBePaid'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                        })
                        ->leftJoin(DB::raw('(SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.supplierID', '=', 'supCont.supplierCodeSystem');
                        })
                        ->leftJoin(DB::raw('(SELECT
	erp_paysupplierinvoicemaster.companySystemID,
	erp_paysupplierinvoicemaster.companyID,
	erp_advancepaymentdetails.purchaseOrderID,
	sum( erp_advancepaymentdetails.comRptAmount ),
IF
	( poTermID = 0, 0, ( sum( comRptAmount ) ) ) AS AdvanceReleased,
IF
	( poTermID = 0, ( sum( comRptAmount ) ), 0 ) AS LogisticAdvanceReleased 
FROM
	erp_paysupplierinvoicemaster
	INNER JOIN erp_advancepaymentdetails ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_advancepaymentdetails.PayMasterAutoId
	INNER JOIN erp_purchaseorderadvpayment ON erp_advancepaymentdetails.poAdvPaymentID = erp_purchaseorderadvpayment.poAdvPaymentID 
WHERE
	erp_advancepaymentdetails.purchaseOrderID > 0 
	AND erp_paysupplierinvoicemaster.approved =- 1 
	AND erp_paysupplierinvoicemaster.cancelYN = 0 
	AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
GROUP BY
	purchaseOrderID,companySystemID) adv'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'adv.purchaseOrderID');
                            $join->on('erp_purchaseordermaster.companySystemID', '=', 'adv.companySystemID');
                        })
                        ->leftJoin(DB::raw('(SELECT
    erp_paysupplierinvoicemaster.companySystemID,
    erp_paysupplierinvoicemaster.companyID,
    erp_bookinvsuppdet.purchaseOrderID,
    sum(erp_bookinvsuppdet.totRptAmount) as paymentComRptAmount
FROM
    erp_paysupplierinvoicemaster
    INNER JOIN erp_paysupplierinvoicedetail ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId
    INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppdet.bookingSuppMasInvAutoID=erp_paysupplierinvoicedetail.bookingInvSystemCode
WHERE
    erp_paysupplierinvoicemaster.approved=- 1 AND  erp_paysupplierinvoicemaster.cancelYN=0
    AND erp_paysupplierinvoicedetail.addedDocumentSystemID=11
    AND erp_paysupplierinvoicedetail.matchingDocID = 0
     AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
Group By erp_paysupplierinvoicemaster.companySystemID,erp_bookinvsuppdet.purchaseOrderID) pr'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'pr.purchaseOrderID');
                            $join->on('erp_purchaseordermaster.companySystemID', '=', 'pr.companySystemID');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween(DB::raw("DATE(approvedDate)"), array($startDate, $endDate))->orderBy('approvedDate', 'ASC')->get();

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
                            'Supplier Country' => $val->countryName,
                            'Budget Year' => $val->budgetYear,
                            'PO Capex Amount' => $val->POCapex,
                            'PO Opex Amount' => $val->POOpex,
                            'Total PO Amount' => $val->TotalPOVal,
                            'GRV Capex Amount' => $val->GRVCapex,
                            'GRV Opex Amount' => $val->GRVOpex,
                            'Total GRV Amount' => $val->TotalGRVValue,
                            'Capex Balance' => $val->capexBalance,
                            'Opex Balance' => $val->opexBalance,
                            'Advance Released' => $val->advanceReleased,
                            'Logistic Advance Released' => $val->logisticAdvanceReleased,
                            'Payment Released (From Invoice)' => $val->paymentReleased,
                            'Balance To Be Paid' => $val->balanceToBePaid,
                        );
                    }

                    $csv = \Excel::create('po_wise_analysis', function ($excel) use ($data) {
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
                else if ($request->reportType == 3) {
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('
                            companymaster.CompanyID,                      
                            companymaster.CompanyName,                      
                            SUM(IFNULL(podet.TotalPOVal,0)) as TotalPOVal,
                            SUM(IFNULL(podet.POQty,0)) as POQty, 
                            SUM(IFNULL(podet.POCapex,0)) as POCapex,
                            SUM(IFNULL(podet.POOpex,0)) as POOpex,
                            SUM(IFNULL(grvdet.GRVQty,0)) as GRVQty,
                            SUM(IFNULL(grvdet.TotalGRVValue,0)) as TotalGRVValue,
                            SUM(IFNULL(grvdet.GRVCapex,0)) as GRVCapex,
                            SUM(IFNULL(grvdet.GRVOpex,0)) as GRVOpex,
                            SUM(IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            SUM(IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->leftJoin('companymaster', 'erp_purchaseordermaster.companySystemID', '=', 'companymaster.companySystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween(DB::raw("DATE(approvedDate)"), array($startDate, $endDate))->groupBy('erp_purchaseordermaster.companySystemID')->orderBy('CompanyName', 'ASC')->get();

                    foreach ($output as $val) {
                        $data[] = array(
                            'CompanyID' => $val->CompanyID,
                            'Company Name' => $val->CompanyName,
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

                    $csv = \Excel::create('po_wise_analysis_company', function ($excel) use ($data) {
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
                }else if ($request->reportType == 4) {
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('
                            companymaster.CompanyID,                      
                            companymaster.CompanyName, 
                            supplierPrimaryCode as supplierID,
                            supplierName,
                            supCont.countryName,                     
                            SUM(IFNULL(podet.TotalPOVal,0)) as TotalPOVal,
                            SUM(IFNULL(podet.POQty,0)) as POQty, 
                            SUM(IFNULL(podet.POCapex,0)) as POCapex,
                            SUM(IFNULL(podet.POOpex,0)) as POOpex,
                            SUM(IFNULL(grvdet.GRVQty,0)) as GRVQty,
                            SUM(IFNULL(grvdet.TotalGRVValue,0)) as TotalGRVValue,
                            SUM(IFNULL(grvdet.GRVCapex,0)) as GRVCapex,
                            SUM(IFNULL(grvdet.GRVOpex,0)) as GRVOpex,
                            SUM(IFNULL(podet.POCapex,0)-IFNULL(grvdet.GRVCapex,0)) as capexBalance,
                            SUM(IFNULL(podet.POOpex,0)-IFNULL(grvdet.GRVOpex,0)) as opexBalance'
                        )
                        ->join(DB::raw('(SELECT 
                        erp_purchaseorderdetails.companySystemID,
                    erp_purchaseorderdetails.purchaseOrderMasterID,
                    SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS TotalPOVal,
                    SUM( erp_purchaseorderdetails.noQty ) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POOpex
                     FROM erp_purchaseorderdetails WHERE companySystemID IN (' . join(',', $companyID) . ') GROUP BY purchaseOrderMasterID) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
                            $query->on('purchaseOrderID', '=', 'podet.purchaseOrderMasterID');
                        })
                        ->leftJoin(DB::raw('(SELECT 
                    SUM( erp_grvdetails.noQty ) GRVQty,
	                SUM( noQty * GRVcostPerUnitComRptCur ) AS TotalGRVValue,
	                SUM( IF ( itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 )) AS GRVCapex,
	                SUM( IF ( itemFinanceCategoryID != 3,( noQty * GRVcostPerUnitComRptCur ),0 )) AS GRVOpex,
	                erp_grvdetails.purchaseOrderMastertID,
	                approved,
	                erp_grvdetails.companySystemID
                     FROM erp_grvdetails 
                     INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1
                     GROUP BY erp_grvdetails.purchaseOrderMastertID) as grvdet'), function ($join) use ($companyID) {
                            $join->on('purchaseOrderID', '=', 'grvdet.purchaseOrderMastertID');
                        })
                        ->leftJoin(DB::raw('(SELECT countrymaster.countryName,supplierCodeSystem FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont'), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.supplierID', '=', 'supCont.supplierCodeSystem');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->leftJoin('companymaster', 'erp_purchaseordermaster.companySystemID', '=', 'companymaster.companySystemID')
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween(DB::raw("DATE(approvedDate)"), array($startDate, $endDate))->groupBy('supplierID')->orderBy('supplierPrimaryCode', 'ASC')->get();

                    foreach ($output as $val) {
                        $data[] = array(
                            'SupplierID' => $val->supplierID,
                            'Supplier Name' => $val->supplierName,
                            'Supplier Country' => $val->countryName,
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

                    $csv = \Excel::create('po_wise_analysis_supplier', function ($excel) use ($data) {
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
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }
}

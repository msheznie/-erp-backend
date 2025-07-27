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

use App\Exports\Procument\ItemwisePoAnalysisReport;
use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\ProcumentOrder;
use App\Services\Excel\ExportReportToExcelService;
use App\Services\Procument\Report\PoAnalysisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\helper\CreateExcel;
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
                    'controlAccount' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'POI':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'suppliers' => 'required',
                    'segment' => 'required',
                    'option' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'SAVING':
                $validator = \Validator::make($request->all(), [
                    'suppliers' => 'required',
                    'categories' => 'required',
                    'subCategories' => 'required',
                    'year' => 'required'
                ]);

                if ($validator->fails()) {
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


                $controlAccountsSystemID = collect($request->controlAccountsSystemID)->pluck('id')->toArray();
                $controlAccountsSystemID = array_unique($controlAccountsSystemID);
                if (isset($request->selectedSupplier)) {
                    if (!empty($request->selectedSupplier)) {
                        $suppliers = collect($request->selectedSupplier);
                    }
                }

                if ($request->reportType == 1) { //PO Analysis Item Detail Report
                    $output = DB::table('erp_purchaseorderdetails')
                        ->join(DB::raw('(SELECT locationName,
                    manuallyClosed,
                    ServiceLineDes as segment,
                    purchaseOrderID,
                    erp_purchaseordermaster.companyID,
                    locationName as location,
                    approved,
                    YEAR ( approvedDate ) AS postingYear,
                    approvedDate AS orderDate,
                    erp_purchaseordermaster.createdDateTime,
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
                    supCont.countryName,
                    IFNULL(suppliercategoryicvmaster.categoryDescription,"-") as icvMasterDes,
                    IFNULL(suppliercategoryicvsub.categoryDescription,"-") as icvSubDes,
                    IF( supCont.isLCCYN = 1, "YES", "NO" ) AS isLcc,
                    IF( supCont.isSMEYN = 1, "YES", "NO" ) AS isSme
                    FROM erp_purchaseordermaster 
                    LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID
                    LEFT JOIN suppliercategoryicvmaster ON erp_purchaseordermaster.supCategoryICVMasterID = suppliercategoryicvmaster.supCategoryICVMasterID
                    LEFT JOIN suppliercategoryicvsub ON erp_purchaseordermaster.supCategorySubICVID = suppliercategoryicvsub.supCategorySubICVID
                 
                     INNER JOIN (SELECT supplierCodeSystem FROM suppliermaster WHERE liabilityAccountSysemID IN (' . join(',', $controlAccountsSystemID) . ')) supp ON erp_purchaseordermaster.supplierID = supp.supplierCodeSystem
                     LEFT JOIN (SELECT countrymaster.countryName,supplierCodeSystem,isSMEYN,isLCCYN FROM suppliermaster LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID) supCont ON  supCont.supplierCodeSystem = erp_purchaseordermaster.supplierID
                     LEFT JOIN erp_location ON poLocation = erp_location.locationID WHERE poCancelledYN=0 AND approved = -1 AND poType_N <>5 AND (approvedDate BETWEEN "' . $startDate . '" AND "' . $endDate . '") AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyID) . ') AND erp_purchaseordermaster.supplierID IN (' . join(',', json_decode($suppliers)) . ')) as podet'), function ($query) use ($companyID, $startDate, $endDate) {
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
                            })
                        ->selectRaw('erp_purchaseorderdetails.purchaseOrderMasterID,
                        erp_purchaseorderdetails.purchaseOrderDetailsID,
                        gdet2.lastOfgrvDate,
                    erp_purchaseorderdetails.unitOfMeasure,
                    IF((IF(podet.manuallyClosed = 1,IFNULL(gdet.noQty,0),IFNULL(erp_purchaseorderdetails.noQty,0))-IFNULL(gdet.noQty,0)) = 0,"Fully Received",if(ISNULL(gdet.noQty) OR gdet.noQty=0 ,"Not Received","Partially Received")) as receivedStatus,
                    /*IFNULL((erp_purchaseorderdetails.noQty-IFNULL(gdet.noQty,0)),0) as qtyToReceive,*/
                    IF(podet.manuallyClosed = 1,0,(IFNULL((erp_purchaseorderdetails.noQty-IFNULL(gdet.noQty,0)),0))) as qtyToReceive,
                    IF(podet.manuallyClosed = 1,IFNULL(gdet.noQty,0),IFNULL(erp_purchaseorderdetails.noQty,0)) as noQty,
                    IFNULL(gdet.noQty,0) as qtyReceived,
                    erp_purchaseorderdetails.itemFinanceCategoryID,
                    erp_purchaseorderdetails.itemFinanceCategorySubID,
                    erp_purchaseorderdetails.itemCode,
                    erp_purchaseorderdetails.itemPrimaryCode,
                    erp_purchaseorderdetails.itemDescription,
                    erp_purchaseorderdetails.supplierPartNumber,
                    IF( erp_purchaseorderdetails.manuallyClosed = 0, " ", "Manually Closed" ) AS detManuallyClosed,
                    IF( erp_purchaseorderdetails.madeLocallyYN = -1, "YES", "NO" ) AS isLocalMade,
                    /*erp_purchaseorderdetails.noQty,*/
                    ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) AS unitCostWithOutDiscount,
                    erp_purchaseorderdetails.GRVcostPerUnitComRptCur as unitCostWithDiscount,
                    erp_purchaseorderdetails.discountPercentage,
                    ( ( ( ( erp_purchaseorderdetails.GRVcostPerUnitComRptCur / ( 100- erp_purchaseorderdetails.discountPercentage ) ) * 100 ) ) - erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS discountAmount,
                    ( IF(podet.manuallyClosed = 1,IFNULL(gdet.noQty,0),IFNULL(erp_purchaseorderdetails.noQty,0)) * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ) AS total,
                    financeitemcategorymaster.categoryDescription as financecategory,
                    catSub.*,
                    units.UnitShortCode AS unitShortCode,
                    podet.*')
                        ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID);

                    $globalSearch = $request->input('search.value');
                    if ($globalSearch) {
                        $globalSearch = str_replace("\\", "\\\\", $globalSearch);
                        $output = $output->where(function ($query) use ($globalSearch) {
                            $query->where('erp_purchaseorderdetails.itemPrimaryCode', 'LIKE', "%{$globalSearch}%")
                                ->orWhere('erp_purchaseorderdetails.itemDescription', 'LIKE', "%{$globalSearch}%")
                                ->orWhere('supplierName', 'LIKE', "%{$globalSearch}%")
                                ->orWhere('supplierPrimaryCode', 'LIKE', "%{$globalSearch}%")
                                ->orWhere('purchaseOrderCode', 'LIKE', "%{$globalSearch}%");
                        });
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

                    if (isset($request->grvStatus)) {
                        if (!empty($request->grvStatus)) {
                            $output = $output->having('receivedStatus', $request->grvStatus);
                        }
                    }

                    if (isset($request->lcc)) {
                        if (!empty($request->lcc)) {
                            $output = $output->having('isLcc', $request->lcc);
                        }
                    }

                    if (isset($request->sme)) {
                        if (!empty($request->sme)) {
                            $output = $output->having('isSme', $request->sme);
                        }
                    }

                    if (isset($request->segment)) {
                        if (!empty($request->segment) && is_array($request->segment)) {
                            $output = $output->whereIN('erp_purchaseorderdetails.serviceLineSystemID', $request->segment);
                        }
                    }

                    $output->orderBy('podet.approvedDate', 'ASC');

                    $data['order'] = [];
                    $data['search']['value'] = '';
                    $request->merge($data);

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

                }
                else if ($request->reportType == 2) {  //PO Wise Analysis Report
                    //DB::enableQueryLog();
                    $output = DB::table('erp_purchaseordermaster')
                        ->selectRaw('erp_purchaseordermaster.companyID,
                            erp_purchaseordermaster.purchaseOrderCode,
                            erp_purchaseordermaster.manuallyClosed,
                            erp_purchaseordermaster.narration,
                            erp_purchaseordermaster.approvedDate as orderDate,
                            erp_purchaseordermaster.createdDateTime,
                            erp_purchaseordermaster.serviceLine,
                            erp_purchaseordermaster.supplierPrimaryCode,
                            erp_purchaseordermaster.supplierName,
                            erp_purchaseordermaster.expectedDeliveryDate,
                            erp_purchaseordermaster.budgetYear,
                            erp_purchaseordermaster.purchaseOrderID,
                            IFNULL(suppliercategoryicvmaster.categoryDescription,"-") as icvMasterDes,
                            IFNULL(suppliercategoryicvsub.categoryDescription,"-") as icvSubDes,
                            supCont.countryName,
                            /*IFNULL(podet.TotalPOVal,0) as TotalPOVal,*/
                            IF( erp_purchaseordermaster.manuallyClosed = 1, IFNULL(grvdet.TotalGRVValue,0), IFNULL(podet.TotalPOVal,0) ) AS TotalPOVal,
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
                            IF( suppliermaster.isLCCYN = 1, "YES", "NO" ) AS isLcc,
                            IF( suppliermaster.isSMEYN = 1, "YES", "NO" ) AS isSme,
                            IFNULL(adv.AdvanceReleased,0) as advanceReleased,
                            IFNULL(adv.LogisticAdvanceReleased,0) as logisticAdvanceReleased,
                            IFNULL(lg.logisticAmount,0) as logisticAmount,
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
                             INNER JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID WHERE erp_grvdetails.purchaseOrderMastertID <> 0 AND erp_grvdetails.companySystemID IN (' . join(',', $companyID) . ') AND erp_grvmaster.approved = -1 AND erp_grvmaster.grvCancelledYN = 0
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
                        ->leftJoin(DB::raw('
                                            (select
                                            purchaseOrderID,
                                            sum(totRptAmount) as paymentComRptAmount
                                            from
                                            (SELECT
                                                erp_bookinvsuppdet.purchaseOrderID,
                                                erp_bookinvsuppdet.totRptAmount,
                                                qry.bookingInvSystemCode 
                                            FROM
                                                (
                                            SELECT
                                                erp_paysupplierinvoicemaster.companySystemID,
                                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                                erp_paysupplierinvoicemaster.BPVcode,
                                                erp_paysupplierinvoicedetail.bookingInvSystemCode 
                                            FROM
                                                erp_paysupplierinvoicemaster
                                                INNER JOIN erp_paysupplierinvoicedetail ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId 
                                            WHERE
                                                erp_paysupplierinvoicemaster.approved = - 1 
                                                AND erp_paysupplierinvoicedetail.addedDocumentSystemID = 11 
                                                AND erp_paysupplierinvoicedetail.matchingDocID = 0 
                                                AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyID) . ')
                                                ) AS qry
                                                INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppdet.bookingSuppMasInvAutoID = qry.bookingInvSystemCode 
                                            GROUP BY
                                                qry.companySystemID,
                                                qry.bookingInvSystemCode,
                                                erp_bookinvsuppdet.purchaseOrderID,
                                                erp_bookinvsuppdet.totRptAmount
                                                ) as pr1  GROUP BY purchaseOrderID) pr '), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'pr.purchaseOrderID');
                        })
                        ->leftJoin(DB::raw('(SELECT
                                                poID,
                                                SUM( reqAmountInPORptCur ) as logisticAmount
                                            FROM
                                                `erp_purchaseorderadvpayment` 
                                            WHERE
                                                poTermID = 0 
                                                AND confirmedYN = 1 
                                                AND isAdvancePaymentYN = 1 
                                                AND approvedYN = - 1 
                                            GROUP BY
                                                poID) lg '), function ($join) use ($companyID) {
                            $join->on('erp_purchaseordermaster.purchaseOrderID', '=', 'lg.poID');
                        })
                        ->leftJoin('serviceline', 'erp_purchaseordermaster.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                        ->leftJoin('suppliermaster', 'erp_purchaseordermaster.supplierID', '=', 'suppliermaster.supplierCodeSystem')
                        ->leftJoin('suppliercategoryicvmaster', 'erp_purchaseordermaster.supCategoryICVMasterID', '=', 'suppliercategoryicvmaster.supCategoryICVMasterID')
                        ->leftJoin('suppliercategoryicvsub', 'erp_purchaseordermaster.supCategorySubICVID', '=', 'suppliercategoryicvsub.supCategorySubICVID')
                        ->whereIn('liabilityAccountSysemID', $controlAccountsSystemID)
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)
                        ->where('poCancelledYN', 0)
                        ->where('erp_purchaseordermaster.poType_N', '<>', 5)
                        ->where('erp_purchaseordermaster.approved', '=', -1)
                        ->where('erp_purchaseordermaster.poCancelledYN', '=', 0)
                        ->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))
                        ->whereBetween(DB::raw("DATE(erp_purchaseordermaster.approvedDate)"), array($startDate, $endDate));

                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $output = $output->where(function ($q) use($search){
                           $q->where('erp_purchaseordermaster.purchaseOrderCode', 'LIKE', "%{$search}%")
                               ->orWhere('erp_purchaseordermaster.supplierPrimaryCode', 'LIKE', "%{$search}%")
                               ->orWhere('erp_purchaseordermaster.supplierName', 'LIKE', "%{$search}%");
                        });
                    }
                    $output->orderBy('erp_purchaseordermaster.approvedDate', 'ASC');
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

                    $TotalLogisticAmount = collect($outputSUM)->pluck('logisticAmount')->toArray();
                    $TotalLogisticAmount = array_sum($TotalLogisticAmount);

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

                    $data['order'] = [];
                    $data['search']['value'] = '';
                    $request->merge($data);

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
                            'TotalLogisticAmount' => $TotalLogisticAmount,
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
                            SUM(IF(erp_purchaseordermaster.manuallyClosed =1,IFNULL(grvdet.TotalGRVValue,0),IFNULL(podet.TotalPOVal,0))) as TotalPOVal,
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
                        ->leftJoin('suppliermaster', 'erp_purchaseordermaster.supplierID', '=', 'suppliermaster.supplierCodeSystem')
                        ->whereIN('liabilityAccountSysemID', $controlAccountsSystemID)
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)
                        ->where('poCancelledYN', 0)
                        ->where('erp_purchaseordermaster.poType_N', '<>', 5)
                        ->where('erp_purchaseordermaster.approved', '=', -1)
                        ->where('erp_purchaseordermaster.poCancelledYN', '=', 0)
                        ->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))
                        ->whereBetween(DB::raw("DATE(erp_purchaseordermaster.approvedDate)"), array($startDate, $endDate))
                        ->groupBy('erp_purchaseordermaster.companySystemID');

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

                    $data['order'] = [];
                    $data['search']['value'] = '';
                    $request->merge($data);

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
                            erp_purchaseordermaster.supplierName,                     
                            supCont.countryName,                     
                            SUM(IF(erp_purchaseordermaster.manuallyClosed =1,IFNULL(grvdet.TotalGRVValue,0),IFNULL(podet.TotalPOVal,0))) as TotalPOVal,
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
                        ->leftJoin('suppliermaster', 'erp_purchaseordermaster.supplierID', '=', 'suppliermaster.supplierCodeSystem')
                        ->whereIn('liabilityAccountSysemID', $controlAccountsSystemID)
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)
                        ->where('poCancelledYN', 0)
                        ->where('erp_purchaseordermaster.poType_N', '<>', 5)
                        ->where('erp_purchaseordermaster.approved', '=', -1)
                        ->where('erp_purchaseordermaster.poCancelledYN', '=', 0)
                        ->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))
                        ->whereBetween(DB::raw("DATE(erp_purchaseordermaster.approvedDate)"), array($startDate, $endDate))
                        ->groupBy('supplierID');

                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $output = $output->where(function ($q) use($search){
                            $q->where('erp_purchaseordermaster.supplierName', 'LIKE', "%{$search}%")
                                ->orWhere('erp_purchaseordermaster.supplierPrimaryCode', 'LIKE', "%{$search}%");
                        });

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
                    $data['order'] = [];
                    $data['search']['value'] = '';
                    $request->merge($data);

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
            case 'POI': //Order Inquiry

                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }

                $output = $this->orderInquiry($input);

                return \DataTables::eloquent($output)
                    ->addColumn('Actions', 'Actions', "Actions")
                    ->order(function ($query) use ($input) {
                        if (request()->has('order')) {
                            if ($input['order'][0]['column'] == 0) {
                                $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                            }
                        }
                    })
                    ->addIndexColumn()
                    ->with('orderCondition', $sort)
                    ->make(true);
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function exportReport(Request $request, PoAnalysisService $poAnalysisService,ExportReportToExcelService $exportReportToExcelService)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'POA':
                $validatedData = $request->validate([
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'suppliers' => 'required',
                ]);

                $exportToExcel = $poAnalysisService->getPOAExportData($request,$exportReportToExcelService);
                if(!$exportToExcel['success'])
                    return $this->sendError('Unable to export excel');

                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                break;
            case 'POI': //Order Inquiry

                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }
                $type = $request->type;
                $output = $this->orderInquiry($input)
                    ->orderBy('purchaseOrderID', $sort)
                    ->get();

                $data = array();
                foreach ($output as $val) {
                    $data[] = array(
                        'Company' => $val->companyID,
                        'PO Code' => $val->purchaseOrderCode,
                        'Segment' => $val->segment ? $val->segment->ServiceLineDes : '',
                        'Created Date' => Helper::dateFormat($val->createdDateTime),
                        'Created By' => $val->created_by ? $val->created_by->empFullName : '',
                        'Supplier Code' => $val->supplierPrimaryCode,
                        'Supplier Name' => $val->supplierName,
                        'LCC' => $val->supplier ? $val->supplier->isLcc : '',
                        'SME' => $val->supplier ? $val->supplier->isSme : '',
                        'JSRS Number' => $val->supplier ? $val->supplier->jsrsNo : '',
                        'JSRS Expiry' =>($val->supplier && $val->supplier->jsrsExpiry)  ? $val->supplier->jsrsExpiry : '',
                        'ICV Category' => $val->icv_category ? $val->icv_category->categoryDescription : '',
                        'ICV Sub Category' => $val->icv_sub_category ? $val->icv_sub_category->categoryDescription : '',
                        'Expected Delivery Date' => Helper::dateFormat($val->expectedDeliveryDate),
                        'Narration' => $val->narration,
                        'Currency' => $val->currency ? $val->currency->CurrencyCode : '',
                        'Amount' => number_format($val->poTotalSupplierTransactionCurrency, ($val->currency ? $val->currency->DecimalPlaces : 2)),
                        'Approved Date' => Helper::dateFormat($val->approvedDate),
                        'Status' => $val->manuallyClosed ? 'Manually Closed' : ''
                    );
                }

                //  \Excel::create('order_inquiry', function ($excel) use ($data) {
                //     $excel->sheet('sheet name', function ($sheet) use ($data) {
                //         $sheet->fromArray($data, null, 'A1', true);
                //         //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                //         $sheet->setAutoSize(true);
                //         $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                //     });
                //     $lastrow = $excel->getActiveSheet()->getHighestRow();
                //     $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                // })->download($type);

                // return $this->sendResponse(array(), 'successfully export');



                $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
                $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                );
                $doc_name = 'order_inquiry';
                $path = 'procurement/report/order_inquiry/excel/';
                $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);
        
                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }


                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function orderInquiry($input)
    {


        $startDate = new Carbon($input['fromDate']);
        $startDate = $startDate->format('Y-m-d');

        $endDate = new Carbon($input['toDate']);
        $endDate = $endDate->format('Y-m-d');


        $companyID = "";
        $checkIsGroup = Company::find($input['companySystemID']);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($input['companySystemID']);
        } else {
            $companyID = (array)$input['companySystemID'];
        }

        $option = isset($input['option']) ? $input['option'] : -1;

        $suppliers = (array)$input['suppliers'];
        $suppliers = collect($suppliers)->pluck('supplierCodeSytem');

        $segment = (array)$input['segment'];
        $segment = collect($segment)->pluck('serviceLineSystemID');
        //poType_N: 5  -- main work order
        //poType_N: 6  -- sub work order

        $current_date = date("Y-m-d");

        $data = ProcumentOrder::selectRaw('*')
            ->with(['created_by', 'icv_category', 'icv_sub_category', 'currency', 'segment', 'supplier' => function ($q) {
                $q->selectRaw('IF(isLCCYN = 1, "YES", "NO" ) AS isLcc,jsrsExpiry,jsrsNo,
                            IF(isSMEYN = 1, "YES", "NO" ) AS isSme,supplierCodeSystem');
            }])
            ->whereIn('companySystemID', $companyID)
            ->where('poCancelledYN', 0)
            ->where('refferedBackYN', 0)
            ->where(function ($q) {
                $q->where('poType_N', '!=', 5);
                //->orWhere('documentSystemID','!=',5);
            })
            ->whereBetween('createdDateTime', [$startDate, $endDate])
            ->whereIn('supplierID', $suppliers)
            ->whereIn('serviceLineSystemID', $segment)
            ->when($option >= 0, function ($q) use ($option,$current_date) {
                if ($option == 0 || $option == 1 || $option == 2) {
                    $q->where('grvRecieved', $option)
                        ->where('poClosedYN', 0)
                        ->where('poConfirmedYN', 1)
                        ->where('approved', -1);
                } else if ($option == 3) {
                    $q->where('poConfirmedYN', 0)
                        ->where('approved', 0);
                } else if ($option == 4) {
                    $q->where('poConfirmedYN', 1)
                        ->where('approved', 0);
                }
                else if ($option == 5) {
                    $q->where('grvRecieved','!=',2)
                        ->where('poClosedYN', 0)
                        ->where('poConfirmedYN', 1)
                        ->where('approved', -1)
                        ->whereDate('expectedDeliveryDate','<',$current_date);
                }
            });
        return $data;
    }

    public function getSavingReportData($input){

        $companyID = "";
        $checkIsGroup = Company::find($input['companySystemID']);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($input['companySystemID']);
        } else {
            $companyID = (array)$input['companySystemID'];
        }

        $year = isset($input['year']) ? $input['year'] : date("Y");

        $suppliers = (array)$input['suppliers'];
        $supplierSystemID = collect($suppliers)->pluck('supplierCodeSytem')->toArray();

        $categories = (array)$input['categories'];
        $categorySystemID = collect($categories)->pluck('value')->toArray();

        $subCategories = (array)$input['subCategories'];
        $subCategorySystemID = collect($subCategories)->pluck('itemCategorySubID')->toArray();


        $finalQry = "SELECT
                    itemCode,
                    itemPrimaryCode,
                    itemDescription,
                    units.UnitShortCode,
                    SUM( IF ( DocMONTH = 1, noQty, 0 ) ) AS Jan_qty,
                    SUM( IF ( DocMONTH = 1, wacUnitCostRpt, 0 ) ) AS Jan_UnitCost,
                    0 AS Jan_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 2, noQty, 0 ) ) AS Feb_qty,
                    SUM( IF ( DocMONTH = 2, wacUnitCostRpt, 0 ) ) AS Feb_UnitCost,
                    0 AS Feb_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 3, noQty, 0 ) ) AS March_qty,
                    SUM( IF ( DocMONTH = 3, wacUnitCostRpt, 0 ) ) AS March_UnitCost,
                    0 AS March_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 4, noQty, 0 ) ) AS April_qty,
                    SUM( IF ( DocMONTH = 4, wacUnitCostRpt, 0 ) ) AS April_UnitCost,
                    0 AS April_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 5, noQty, 0 ) ) AS May_qty,
                    SUM( IF ( DocMONTH = 5, wacUnitCostRpt, 0 ) ) AS May_UnitCost,
                    0 AS May_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 6, noQty, 0 ) ) AS June_qty,
                    SUM( IF ( DocMONTH = 6, wacUnitCostRpt, 0 ) ) AS June_UnitCost,
                    0 AS June_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 7, noQty, 0 ) ) AS July_qty,
                    SUM( IF ( DocMONTH = 7, wacUnitCostRpt, 0 ) ) AS July_UnitCost,
                    0 AS July_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 8, noQty, 0 ) ) AS Aug_qty,
                    SUM( IF ( DocMONTH = 8, wacUnitCostRpt, 0 ) ) AS Aug_UnitCost,
                    0 AS Aug_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 9, noQty, 0 ) ) AS Sept_qty,
                    SUM( IF ( DocMONTH = 9, wacUnitCostRpt, 0 ) ) AS Sept_UnitCost,
                    0 AS Sept_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 10, noQty, 0 ) ) AS Oct_qty,
                    SUM( IF ( DocMONTH = 10, wacUnitCostRpt, 0 ) ) AS Oct_UnitCost,
                    0 AS Oct_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 11, noQty, 0 ) ) AS Nov_qty,
                    SUM( IF ( DocMONTH = 11, wacUnitCostRpt, 0 ) ) AS Nov_UnitCost,
                    0 AS Nov_SavingAmount,
                    
                    SUM( IF ( DocMONTH = 12, noQty, 0 ) ) AS Dece_qty,
                    SUM( IF ( DocMONTH = 12, wacUnitCostRpt, 0 ) ) AS Dece_UnitCost,
                    0 AS Dece_SavingAmount 
                FROM
                    (
                SELECT
                    purchaseOrderDetailsID,
                    itemCode,
                    itemPrimaryCode,
                    itemDescription,
                    itemFinanceCategoryID,
                    itemFinanceCategorySubID,
                    unitOfMeasure,
                    MONTH ( erp_purchaseordermaster.approvedDate ) AS DocMONTH,
                    SUM( noQty ) AS noQty,
                IF
                    ( SUM( noQty ) > 0, ( SUM( GRVcostPerUnitComRptCur * noQty ) / SUM( noQty ) ), 0 ) AS wacUnitCostRpt 
                FROM
                    erp_purchaseorderdetails
                    LEFT JOIN erp_purchaseordermaster ON erp_purchaseorderdetails.purchaseOrderMasterID = erp_purchaseordermaster.purchaseOrderID 
                WHERE
                    erp_purchaseordermaster.companySystemID IN (" . join(',', $companyID) . ")
                    AND erp_purchaseordermaster.poConfirmedYN = 1 
                    AND erp_purchaseordermaster.approved = - 1 
                    AND YEAR ( erp_purchaseordermaster.approvedDate ) = $year 
                    AND erp_purchaseordermaster.supplierID IN (" . join(',', $supplierSystemID) . ")
                    AND erp_purchaseorderdetails.itemFinanceCategoryID IN (" . join(',', $categorySystemID) . ")
                    AND erp_purchaseorderdetails.itemFinanceCategorySubID IN (" . join(',', $subCategorySystemID) . ")
                    -- AND erp_purchaseorderdetails.itemCode = 1895
                GROUP BY
                    erp_purchaseorderdetails.itemCode,
                    MONTH ( erp_purchaseordermaster.approvedDate ) 
                ORDER BY
                    MONTH ( erp_purchaseordermaster.approvedDate ) 
                    ) AS final 
                    LEFT JOIN	units ON unitOfMeasure = units.UnitID
                GROUP BY
                itemCode";
        $output = \DB::select($finalQry);

        foreach ($output as $item) {


            //Feb Saving
            if ($item->Jan_qty != 0) {
                $item->Feb_SavingAmount = ($item->Feb_UnitCost - $item->Jan_UnitCost) * $item->Feb_qty * -1;
            }


            //Mar Saving
            if ($item->Feb_qty != 0) {
                $item->March_SavingAmount = ($item->March_UnitCost - $item->Feb_UnitCost) * $item->March_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->March_SavingAmount = ($item->March_UnitCost - $item->Jan_UnitCost) * $item->March_qty * -1;
            }

            //April Saving
            if ($item->March_qty != 0) {
                $item->April_SavingAmount = ($item->April_UnitCost - $item->March_UnitCost) * $item->April_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->April_SavingAmount = ($item->April_UnitCost - $item->Feb_UnitCost) * $item->April_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->April_SavingAmount = ($item->April_UnitCost - $item->Jan_UnitCost) * $item->April_qty * -1;
            }

            //May Saving
            if ($item->April_qty != 0) {
                $item->May_SavingAmount = ($item->May_UnitCost - $item->April_UnitCost) * $item->May_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->May_SavingAmount = ($item->May_UnitCost - $item->March_UnitCost) * $item->May_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->May_SavingAmount = ($item->May_UnitCost - $item->Feb_UnitCost) * $item->May_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->May_SavingAmount = ($item->May_UnitCost - $item->Jan_UnitCost) * $item->May_qty * -1;
            }

            //June Saving
            if ($item->May_qty != 0) {
                $item->June_SavingAmount = ($item->June_UnitCost - $item->May_UnitCost) * $item->June_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->June_SavingAmount = ($item->June_UnitCost - $item->April_UnitCost) * $item->June_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->June_SavingAmount = ($item->June_UnitCost - $item->March_UnitCost) * $item->June_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->June_SavingAmount = ($item->June_UnitCost - $item->Feb_UnitCost) * $item->June_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->June_SavingAmount = ($item->June_UnitCost - $item->Jan_UnitCost) * $item->June_qty * -1;
            }


            //July Saving
            if ($item->June_qty != 0) {
                $item->July_SavingAmount = ($item->July_UnitCost - $item->June_UnitCost) * $item->July_qty * -1;
            } else if ($item->May_qty != 0) {
                $item->July_SavingAmount = ($item->July_UnitCost - $item->May_UnitCost) * $item->July_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->July_SavingAmount = ($item->July_UnitCost - $item->April_UnitCost) * $item->July_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->July_SavingAmount = ($item->July_UnitCost - $item->March_UnitCost) * $item->July_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->July_SavingAmount = ($item->July_UnitCost - $item->Feb_UnitCost) * $item->July_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->July_SavingAmount = ($item->July_UnitCost - $item->Jan_UnitCost) * $item->July_qty * -1;
            }

            //Aug Saving
            if ($item->July_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->July_UnitCost) * $item->Aug_qty * -1;
            } else if ($item->June_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->June_UnitCost) * $item->Aug_qty * -1;
            } else if ($item->May_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->May_UnitCost) * $item->Aug_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->April_UnitCost) * $item->Aug_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->March_UnitCost) * $item->Aug_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->Feb_UnitCost) * $item->Aug_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->Aug_SavingAmount = ($item->Aug_UnitCost - $item->Jan_UnitCost) * $item->Aug_qty * -1;
            }

            //Sep Saving
            if ($item->Aug_qty != 0) {
                $item->Sept_SavingAmount = ($item->Sept_UnitCost - $item->Aug_UnitCost) * $item->Sept_qty * -1;
            }
            if ($item->July_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->July_UnitCost) * $item->Sept_qty * -1;
            } else if ($item->June_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->June_UnitCost) * $item->Sept_qty * -1;
            } else if ($item->May_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->May_UnitCost) * $item->Sept_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->April_UnitCost) * $item->Sept_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->March_UnitCost) * $item->Sept_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->Feb_UnitCost) * $item->Sept_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->Sept_SavingAmount = ($item->Aug_UnitCost - $item->Jan_UnitCost) * $item->Sept_qty * -1;
            }

            //Oct Saving
            if ($item->Sept_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->Sept_UnitCost) * $item->Oct_qty * -1;
            }
            if ($item->Aug_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->Aug_UnitCost) * $item->Oct_qty * -1;
            }
            if ($item->July_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->July_UnitCost) * $item->Oct_qty * -1;
            } else if ($item->June_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->June_UnitCost) * $item->Oct_qty * -1;
            } else if ($item->May_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->May_UnitCost) * $item->Oct_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->April_UnitCost) * $item->Oct_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->March_UnitCost) * $item->Oct_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->Feb_UnitCost) * $item->Oct_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->Oct_SavingAmount = ($item->Oct_UnitCost - $item->Jan_UnitCost) * $item->Oct_qty * -1;
            }

            //Nov Saving
            if ($item->Oct_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->Oct_UnitCost) * $item->Nov_qty * -1;
            }
            if ($item->Sept_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->Sept_UnitCost) * $item->Nov_qty * -1;
            }
            if ($item->Aug_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->Aug_UnitCost) * $item->Nov_qty * -1;
            }
            if ($item->July_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->July_UnitCost) * $item->Nov_qty * -1;
            } else if ($item->June_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->June_UnitCost) * $item->Nov_qty * -1;
            } else if ($item->May_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->May_UnitCost) * $item->Nov_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->April_UnitCost) * $item->Nov_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->March_UnitCost) * $item->Nov_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->Feb_UnitCost) * $item->Nov_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->Nov_SavingAmount = ($item->Nov_UnitCost - $item->Jan_UnitCost) * $item->Nov_qty * -1;
            }

            //Dec Saving
            if ($item->Nov_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->Nov_UnitCost) * $item->Dece_qty * -1;
            } else if ($item->Oct_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->Oct_UnitCost) * $item->Dece_qty * -1;
            }
            if ($item->Sept_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->Sept_UnitCost) * $item->Dece_qty * -1;
            }
            if ($item->Aug_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->Aug_UnitCost) * $item->Dece_qty * -1;
            }
            if ($item->July_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->July_UnitCost) * $item->Dece_qty * -1;
            } else if ($item->June_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->June_UnitCost) * $item->Dece_qty * -1;
            } else if ($item->May_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->May_UnitCost) * $item->Dece_qty * -1;
            } else if ($item->April_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->April_UnitCost) * $item->Dece_qty * -1;
            }
            if ($item->March_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->March_UnitCost) * $item->Dece_qty * -1;
            } else if ($item->Feb_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->Feb_UnitCost) * $item->Dece_qty * -1;
            } else if ($item->Jan_qty != 0) {
                $item->Dece_SavingAmount = ($item->Dece_UnitCost - $item->Jan_UnitCost) * $item->Dece_qty * -1;
            }

            $item->total_SavingAmount = $item->Jan_SavingAmount +
                $item->Feb_SavingAmount +
                $item->March_SavingAmount +
                $item->April_SavingAmount +
                $item->May_SavingAmount +
                $item->June_SavingAmount +
                $item->July_SavingAmount +
                $item->Aug_SavingAmount +
                $item->Sept_SavingAmount +
                $item->Oct_SavingAmount +
                $item->Nov_SavingAmount +
                $item->Dece_SavingAmount;
        }

        return $output;
    }

    public function getItemSavingReport(Request $request)
    {
        $input = $request->all();
        $output = $this->getSavingReportData($input);

        return $this->sendResponse($output, 'successfully generated report');
    }

    public function exportExcelSavingReport(Request $request)
    {
        $input = $request->all();
        $type = $request->type;
        $output = $this->getSavingReportData($input);
        if ($output) {
            $x = 0;
            foreach ($output as $val) {
                /*$data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Company Name'] = $val->CompanyName;*/
                $data[$x]['Item Code'] = $val->itemPrimaryCode;
                $data[$x]['Item Description'] = $val->itemDescription;
                $data[$x]['UOM'] = $val->UnitShortCode;
                $data[$x]['Jan'] = '';
                $data[$x]['Feb'] = round($val->Feb_UnitCost, 2);
                $data[$x]['Mar'] = round($val->March_UnitCost, 2);
                $data[$x]['Apr'] = round($val->April_UnitCost, 2);
                $data[$x]['May'] = round($val->May_UnitCost, 2);
                $data[$x]['Jun'] = round($val->June_UnitCost, 2);
                $data[$x]['Jul'] = round($val->July_UnitCost, 2);
                $data[$x]['Aug'] = round($val->Aug_UnitCost, 2);
                $data[$x]['Sep'] = round($val->Sept_UnitCost, 2);
                $data[$x]['Oct'] = round($val->Oct_UnitCost, 2);
                $data[$x]['Nov'] = round($val->Nov_UnitCost, 2);
                $data[$x]['Dec'] = round($val->Dece_UnitCost, 2);
                $data[$x]['Saving Total'] = '';
                $x++;

                $data[$x]['Item Code'] = '';
                $data[$x]['Item Description'] = '';
                $data[$x]['UOM'] = '';
                $data[$x]['Jan'] = 'QTY';
                $data[$x]['Feb'] = round($val->Feb_qty, 2);
                $data[$x]['Mar'] = round($val->March_qty, 2);
                $data[$x]['Apr'] = round($val->April_qty, 2);
                $data[$x]['May'] = round($val->May_qty, 2);
                $data[$x]['Jun'] = round($val->June_qty, 2);
                $data[$x]['Jul'] = round($val->July_qty, 2);
                $data[$x]['Aug'] = round($val->Aug_qty, 2);
                $data[$x]['Sep'] = round($val->Sept_qty, 2);
                $data[$x]['Oct'] = round($val->Oct_qty, 2);
                $data[$x]['Nov'] = round($val->Nov_qty, 2);
                $data[$x]['Dec'] = round($val->Dece_qty, 2);
                $data[$x]['Saving Total'] = '';

                $x++;

                $data[$x]['Item Code'] = '';
                $data[$x]['Item Description'] = '';
                $data[$x]['UOM'] = '';
                $data[$x]['Jan'] = 'Saving';
                $data[$x]['Feb'] = round($val->Feb_SavingAmount, 2);
                $data[$x]['Mar'] = round($val->March_SavingAmount, 2);
                $data[$x]['Apr'] = round($val->April_SavingAmount, 2);
                $data[$x]['May'] = round($val->May_SavingAmount, 2);
                $data[$x]['Jun'] = round($val->June_SavingAmount, 2);
                $data[$x]['Jul'] = round($val->July_SavingAmount, 2);
                $data[$x]['Aug'] = round($val->Aug_SavingAmount, 2);
                $data[$x]['Sep'] = round($val->Sept_SavingAmount, 2);
                $data[$x]['Oct'] = round($val->Oct_SavingAmount, 2);
                $data[$x]['Nov'] = round($val->Nov_SavingAmount, 2);
                $data[$x]['Dec'] = round($val->Dece_SavingAmount, 2);
                $data[$x]['Saving Total'] = round($val->total_SavingAmount, 2);

                $x++;
            }
        }

        else {
            $data = array();
        }
        $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );
        $doc_name = 'saving_report';
        $path = 'procurement/report/saving_report/excel/';
        $basePath = CreateExcel::process($data,$type,$doc_name,$path,$detail_array);

        if($basePath == '')
        {
            return $this->sendError('Unable to export excel');
        }
        else
        {
            return $this->sendResponse($basePath, trans('custom.success_export'));
        }
    }

}

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
 * -- Date: 04-June 2018 By: Mubashir Description: Added Grvmaster approved filter from reports
 * -- Date: 06-June 2018 By: Mubashir Description: Removed Grvmaster approved filter for item analaysis report
 * -- Date: 08-june 2018 By: Mubashir Description: Added new functions named as getAcountReceivableFilterData(),
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ControlAccount;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoice;
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
                    'daterange' => 'required',
                    'suppliers' => 'required',
                    'reportType' => 'required',
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CS':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'customers' => 'required',
                    'reportTypeID' => 'required',
                ]);

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            default:
                return $this->sendError('Error Occurred');
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
                    erp_purchaseordermaster.companySystemID
                     FROM erp_purchaseordermaster 
                     LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID 
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
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween('approvedDate', array($startDate, $endDate));

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
            case 'CS': //Customer Statement Report
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CBS') {

                    $request = $this->convertArrayToSelectedValue($request, array('currencyID'));

                    $asOfDate = new Carbon($request->fromDate);
                    $asOfDate = $asOfDate->addDays(1);
                    $asOfDate = $asOfDate->format('Y-m-d');

                    $companyID = "";
                    $checkIsGroup = Company::find($request->companySystemID);
                    if ($checkIsGroup->isGroup) {
                        $companyID = \Helper::getGroupCompany($request->companySystemID);
                    } else {
                        $companyID = (array)$request->companySystemID;
                    }

                    $customers = (array)$request->customers;
                    $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

                    $controlAccountsSystemID = $request->controlAccountsSystemID;


                    $currency = $request->currencyID;
                    $currencyQry = '';
                    $amountQry = '';
                    $decimalPlaceQry = '';
                    if($currency == 1){
                        $currencyQry = "final.documentTransCurrency AS documentCurrency";
                        $amountQry = "round( final.balanceRpt, 1 ) AS balanceAmount";
                        $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
                    }else if($currency == 2){
                        $currencyQry = "final.documentLocalCurrency AS documentCurrency";
                        $amountQry = "round( final.balanceLocal, 1 ) AS balanceAmount";
                        $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
                    }else{
                        $currencyQry = "final.documentRptCurrency AS documentCurrency";
                        $amountQry = "round( final.balanceTrans, 1 ) AS balanceAmount";
                        $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
                    }
                    $currencyID = $request->currencyID;
                    $output = \DB::select('SELECT
	final.documentCode AS DocumentCode,
	final.documentDate AS PostedDate,
	final.documentNarration AS DocumentNarration,
	final.clientContractID AS Contract,
	final.invoiceNumber AS invoiceNumber,
	final.invoiceDate AS InvoiceDate,
	'.$amountQry.',
	'.$currencyQry.',
	'.$decimalPlaceQry.',
	final.customerName AS customerName 
FROM
	(
SELECT
	mainQuery.companySystemID,
	mainQuery.companyID,
	mainQuery.serviceLineSystemID,
	mainQuery.serviceLineCode,
	mainQuery.documentSystemID,
	mainQuery.documentID,
	mainQuery.documentSystemCode,
	mainQuery.documentCode,
	mainQuery.documentDate,
	mainQuery.documentDateFilter,
	mainQuery.invoiceNumber,
	mainQuery.invoiceDate,
	mainQuery.chartOfAccountSystemID,
	mainQuery.glCode,
	mainQuery.documentNarration,
	mainQuery.clientContractID,
	mainQuery.supplierCodeSystem,
	mainQuery.documentTransCurrencyID,
	mainQuery.documentTransCurrency,
	mainQuery.documentTransAmount,
	mainQuery.documentTransDecimalPlaces,
	mainQuery.documentLocalCurrencyID,
	mainQuery.documentLocalCurrency,
	mainQuery.documentLocalAmount,
	mainQuery.documentLocalDecimalPlaces,
	mainQuery.documentRptCurrencyID,
	mainQuery.documentRptCurrency,
	mainQuery.documentRptAmount,
	mainQuery.documentRptDecimalPlaces,
IF( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) AS MatchedBRVTransAmount,
IF( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) AS MatchedBRVLocalAmount,
IF( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) AS MatchedBRVRptAmount,
IF( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) AS BRVTransAmount,
IF( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) AS BRVLocalAmount,
IF( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) AS BRVRptAmount,
IF( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) AS InvoiceTransAmount,
IF( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) AS InvoiceLocalAmount,
IF( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) AS InvoiceRptAmount,
	(
	mainQuery.documentRptAmount + ( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) ) 
	) AS balanceRpt,
	(
	mainQuery.documentLocalAmount + ( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) ) 
	) AS balanceLocal,
	(
	mainQuery.documentTransAmount + ( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) ) 
	) AS balanceTrans,
	mainQuery.customerName   
FROM
	(
SELECT
	erp_generalledger.companySystemID,
	erp_generalledger.companyID,
	erp_generalledger.serviceLineSystemID,
	erp_generalledger.serviceLineCode,
	erp_generalledger.documentSystemID,
	erp_generalledger.documentID,
	erp_generalledger.documentSystemCode,
	erp_generalledger.documentCode,
	erp_generalledger.documentDate,
	DATE_FORMAT( documentDate, "%d/%m/%Y" ) AS documentDateFilter,
	erp_generalledger.documentYear,
	erp_generalledger.documentMonth,
	erp_generalledger.chequeNumber,
	erp_generalledger.invoiceNumber,
	erp_generalledger.invoiceDate,
	erp_generalledger.chartOfAccountSystemID,
	erp_generalledger.glCode,
	erp_generalledger.documentNarration,
	erp_generalledger.clientContractID,
	erp_generalledger.supplierCodeSystem,
	erp_generalledger.documentTransCurrencyID,
	currTrans.CurrencyCode as documentTransCurrency,
	currTrans.DecimalPlaces as documentTransDecimalPlaces,
	erp_generalledger.documentTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	currLocal.CurrencyCode as documentLocalCurrency,
	currLocal.DecimalPlaces as documentLocalDecimalPlaces,
	erp_generalledger.documentLocalAmount,
	erp_generalledger.documentRptCurrencyID,
	currRpt.CurrencyCode as documentRptCurrency,
	currRpt.DecimalPlaces as documentRptDecimalPlaces,
	erp_generalledger.documentRptAmount,
	erp_generalledger.documentType,
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName
FROM
	erp_generalledger 
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
WHERE
	( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
	AND erp_generalledger.documentDate <= "'.$asOfDate.'"
	AND ( erp_generalledger.chartOfAccountSystemID = '.$controlAccountsSystemID.' )
	AND erp_generalledger.companySystemID IN ('.join(',',$companyID).') 
	AND erp_generalledger.supplierCodeSystem IN ('.join(',',$customerSystemID).')
	) AS mainQuery
	LEFT JOIN (
	SELECT
		erp_matchdocumentmaster.companySystemID,
		erp_matchdocumentmaster.documentSystemID,
		erp_matchdocumentmaster.PayMasterAutoId,
		erp_matchdocumentmaster.BPVcode,
		sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS MatchedBRVTransAmount,
		sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS MatchedBRVLocalAmount,
		sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS MatchedBRVRptAmount 
	FROM
		erp_matchdocumentmaster
		INNER JOIN erp_custreceivepaymentdet ON erp_matchdocumentmaster.companyID = erp_custreceivepaymentdet.companyID 
		AND erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
	WHERE
		erp_matchdocumentmaster.matchingConfirmedYN = 1 
		AND erp_matchdocumentmaster.matchingDocdate <= "'.$asOfDate.'" 
		AND erp_custreceivepaymentdet.companySystemID IN ('.join(',',$companyID).')
		AND erp_matchdocumentmaster.BPVsupplierID IN ('.join(',',$customerSystemID).')
	GROUP BY
		erp_matchdocumentmaster.PayMasterAutoId,
		erp_matchdocumentmaster.BPVcode 
	) AS matchedBRV ON mainQuery.documentSystemID = matchedBRV.documentSystemID 
	AND mainQuery.companySystemID = matchedBRV.companySystemID 
	AND matchedBRV.PayMasterAutoId = mainQuery.documentSystemCode
	LEFT JOIN (
	SELECT
		erp_customerreceivepayment.custReceivePaymentAutoID,
		erp_customerreceivepayment.companySystemID,
		erp_customerreceivepayment.documentSystemID,
		erp_customerreceivepayment.custPaymentReceiveCode,
		sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS BRVTransAmount,
		sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS BRVLocalAmount,
		sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS BRVRptAmount 
	FROM
		erp_customerreceivepayment
		INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
	WHERE
		erp_custreceivepaymentdet.bookingInvCode <> "0" 
		AND erp_custreceivepaymentdet.matchingDocID = 0 
		AND erp_customerreceivepayment.approved =- 1 
		AND erp_custreceivepaymentdet.companySystemID IN ('.join(',',$companyID).')
		AND erp_customerreceivepayment.postedDate <= "'.$asOfDate.'"
		AND erp_customerreceivepayment.customerID IN ('.join(',',$customerSystemID).')
	GROUP BY
		custReceivePaymentAutoID 
	) AS InvoicedBRV ON mainQuery.documentSystemID = InvoicedBRV.documentSystemID 
	AND mainQuery.documentSystemCode = InvoicedBRV.custReceivePaymentAutoID
	LEFT JOIN (
	SELECT
		companySystemID,
		companyID,
		addedDocumentSystemID,
		addedDocumentID,
		bookingInvCodeSystem,
		bookingInvCode,
		sum( receiveAmountTrans ) AS InvoiceTransAmount,
		sum( receiveAmountLocal ) AS InvoiceLocalAmount,
		sum( receiveAmountRpt ) AS InvoiceRptAmount 
	FROM
		(
		SELECT
			* 
		FROM
			(
			SELECT
				erp_customerreceivepayment.custPaymentReceiveCode,
				erp_custreceivepaymentdet.companySystemID,
				erp_custreceivepaymentdet.companyID,
				erp_custreceivepaymentdet.addedDocumentSystemID,
				erp_custreceivepaymentdet.addedDocumentID,
				erp_custreceivepaymentdet.bookingInvCodeSystem,
				erp_custreceivepaymentdet.bookingInvCode,
				erp_custreceivepaymentdet.receiveAmountTrans,
				erp_custreceivepaymentdet.receiveAmountLocal,
				erp_custreceivepaymentdet.receiveAmountRpt 
			FROM
				erp_customerreceivepayment
				INNER JOIN erp_custreceivepaymentdet ON erp_customerreceivepayment.custReceivePaymentAutoID = erp_custreceivepaymentdet.custReceivePaymentAutoID 
				AND erp_custreceivepaymentdet.matchingDocID = 0 
				AND erp_customerreceivepayment.approved =- 1 
			WHERE
				erp_custreceivepaymentdet.bookingInvCode <> "0" 
				AND erp_custreceivepaymentdet.matchingDocID = 0 
				AND erp_customerreceivepayment.approved =- 1 
				AND erp_custreceivepaymentdet.companySystemID IN ('.join(',',$companyID).')
				AND erp_customerreceivepayment.postedDate <= "'.$asOfDate.'" 
				AND erp_customerreceivepayment.customerID IN ('.join(',',$customerSystemID).')
			) AS InvoiceFromBRV UNION ALL
		SELECT
			* 
		FROM
			(
			SELECT
				erp_matchdocumentmaster.matchingDocCode,
				erp_custreceivepaymentdet.companySystemID,
				erp_custreceivepaymentdet.companyID,
				erp_custreceivepaymentdet.addedDocumentSystemID,
				erp_custreceivepaymentdet.addedDocumentID,
				erp_custreceivepaymentdet.bookingInvCodeSystem,
				erp_custreceivepaymentdet.bookingInvCode,
				erp_custreceivepaymentdet.receiveAmountTrans,
				erp_custreceivepaymentdet.receiveAmountLocal,
				erp_custreceivepaymentdet.receiveAmountRpt 
			FROM
				erp_custreceivepaymentdet
				INNER JOIN erp_matchdocumentmaster ON erp_matchdocumentmaster.matchDocumentMasterAutoID = erp_custreceivepaymentdet.matchingDocID 
				AND erp_custreceivepaymentdet.companySystemID = erp_matchdocumentmaster.companySystemID 
			WHERE
				erp_matchdocumentmaster.matchingConfirmedYN = 1 
				AND erp_custreceivepaymentdet.companySystemID IN ('.join(',',$companyID).')
				AND erp_matchdocumentmaster.matchingDocdate <= "'.$asOfDate.'" 
				AND erp_matchdocumentmaster.BPVsupplierID IN ('.join(',',$customerSystemID).')
			) AS InvoiceFromMatching 
		) AS InvoiceFromUNION 
	GROUP BY
		bookingInvCode 
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
	) AS final 
WHERE
round( final.balanceRpt, 1 ) <> 0;');
                    $outputArr = array();
                    if($output){
                        foreach ($output as $val){
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr,'companyName' => $checkIsGroup->CompanyName);
                }
                break;
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
                    erp_purchaseordermaster.companySystemID
                     FROM erp_purchaseordermaster 
                     LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID 
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
                        ->whereIN('erp_purchaseorderdetails.companySystemID', $companyID)->orderBy('podet.approvedDate', 'ASC')->get();

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
                            $sheet->fromArray($data, null, 'A1', true);
                            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    return $this->sendResponse(array(), 'successfully export');
                } else if ($request->reportType == 2) {
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
                    IFNULL(SUM( erp_purchaseorderdetails.noQty * erp_purchaseorderdetails.GRVcostPerUnitComRptCur ),0) AS TotalPOVal,
                    IFNULL(SUM( erp_purchaseorderdetails.noQty ),0) AS POQty,
                    IF( erp_purchaseorderdetails.itemFinanceCategoryID = 3, "Capex", "Others" ) AS Type,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID = 3, ( noQty * GRVcostPerUnitComRptCur ), 0 ) ) AS POCapex,
	                SUM( IF ( erp_purchaseorderdetails.itemFinanceCategoryID != 3, ( noQty * GRVcostPerUnitComRptCur ),0 ) ) AS POOpex
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
                        ->whereIN('erp_purchaseordermaster.companySystemID', $companyID)->where('erp_purchaseordermaster.poType_N', '<>', 5)->where('erp_purchaseordermaster.approved', '=', -1)->where('erp_purchaseordermaster.poCancelledYN', '=', 0)->whereIN('erp_purchaseordermaster.supplierID', json_decode($suppliers))->whereBetween('approvedDate', array($startDate, $endDate))->orderBy('approvedDate', 'ASC')->get();

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
            default:
                return $this->sendError('Error Occurred');
        }
    }

    public function getAcountReceivableFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $controlAccount = ChartOfAccount::where('controllAccountYN', 1)->get();

        $departments = \Helper::getCompanyServiceline($selectedCompanyId);

        $filterCustomers = CustomerInvoice::whereIN('companySystemID', $companiesByGroup)
            ->select('customerID')
            ->groupBy('customerID')
            ->pluck('customerID');
        $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)->whereIN('customerCodeSystem', $filterCustomers)->groupBy('customerCodeSystem')->get();

        $output = array(
            'controlAccount' => $controlAccount,
            'customers' => $customerMaster,
            'departments' => $departments,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}

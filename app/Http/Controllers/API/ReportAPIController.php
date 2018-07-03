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
 * -- Date: 18-june 2018 By: Mubashir Description: Added new functions named as pdfExportReport(),
 * -- Date: 19-june 2018 By: Mubashir Description: Added new functions named as getCustomerStatementAccountQRY(),
 * -- Date: 19-june 2018 By: Mubashir Description: Added new functions named as getCustomerBalanceStatementQRY(),
 * -- Date: 20-june 2018 By: Mubashir Description: Added new functions named as getCustomerAgingDetailQRY(),
 * -- Date: 22-june 2018 By: Mubashir Description: Added new functions named as getCustomerAgingSummaryQRY(),
 * -- Date: 29-june 2018 By: Nazir Description: Added new functions named as getCustomerCollectionQRY(),
 * -- Date: 29-june 2018 By: Mubashir Description: Added new functions named as getCustomerLedgerTemplate1QRY(),
 * -- Date: 02-july 2018 By: Fayas Description: Added new functions named as getCustomerBalanceSummery(),getCustomerRevenueMonthlySummary(),
 * -- Date: 02-July 2018 By: Nazir Description: Added new functions named as getCustomerCollectionMonthlyQRY(),
 * -- Date: 02-july 2018 By: Mubashir Description: Added new functions named as getCustomerLedgerTemplate2QRY(),
 * -- Date: 03-july 2018 By: Mubashir Description: Added new functions named as getCustomerSalesRegisterQRY(),
 * -- Date: 03-july 2018 By: Nazir Description: Added new functions named as getCustomerCollectionCNExcelQRY(),
 * -- Date: 03-july 2018 By: Nazir Description: Added new functions named as getCustomerCollectionBRVExcelQRY()
 * -- Date: 03-july 2018 By: Fayas Description: Added new functions named as getRevenueByCustomer()
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountsReceivableLedger;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ControlAccount;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoice;
use App\Models\CustomerMaster;
use App\Models\GeneralLedger;
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
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CBS') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                } else {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required|date',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'singleCustomer' => 'required',
                        'reportTypeID' => 'required',
                    ]);
                }

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CA':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CAD' || $reportTypeID == 'CAS') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'interval' => 'required',
                        'through' => 'required',
                    ]);
                }

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CC':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CCR') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'currencyID' => 'required'
                    ]);
                } else if ($reportTypeID == 'CMR') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'servicelines' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'currencyID' => 'required',
                        'year' => 'required'
                    ]);

                    $fromDate = new Carbon($request->fromDate);
                    $fromDate = $fromDate->format('d/m/Y');
                    $year = explode("/", $fromDate);
                    if ($year['2'] != $request->year) {
                        return $this->sendError('As of date is not in selected year');
                    }
                }

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CL':
                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'CLT1') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'controlAccountsSystemID' => 'required',
                    ]);
                } else if ($reportTypeID == 'CLT2') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'controlAccountsSystemID' => 'required'
                    ]);
                }

                if ($validator->fails()) {//echo 'in';exit;
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CBSUM':

                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'customers' => 'required',
                    'reportTypeID' => 'required',
                    'controlAccountsSystemID' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CR':

                $reportTypeID = '';
                if (isset($request->reportTypeID)) {
                    $reportTypeID = $request->reportTypeID;
                }
                if ($reportTypeID == 'RC') {
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'customers' => 'required',
                        'reportTypeID' => 'required',

                    ]);
                }else if($reportTypeID == 'RMS'){
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'customers' => 'required',
                        'reportTypeID' => 'required',
                        'year' => 'required',
                        'currencyID' => 'required',
                    ]);

                    $fromDate = new Carbon($request->fromDate);
                    $fromDate = $fromDate->format('d/m/Y');
                    $year = explode("/", $fromDate);
                    if ($year['2'] != $request->year) {
                        return $this->sendError('As of date is not in selected year');
                    }
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'CSR':
                $validator = \Validator::make($request->all(), [
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'customers' => 'required',
                    'reportTypeID' => 'required',
                    'controlAccountsSystemID' => 'required',
                    'currencyID' => 'required'
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

                $startDate = new Carbon($request->daterange[0]);
                //$startDate = $startDate->addDays(1);
                $startDate = $startDate->format('Y-m-d');

                $endDate = new Carbon($request->daterange[1]);
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
                if ($reportTypeID == 'CBS') { //customer balance statement

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    //dd(DB::getQueryLog());
                    $outputArr = array();
                    $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $grandTotal = array_sum($grandTotal);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotal, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2);
                } else {
                    //customer statement of account
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                    $checkIsGroup = Company::find($request->companySystemID);
                    $customerName = CustomerMaster::find($request->singleCustomer);

                    $output = $this->getCustomerStatementAccountQRY($request);
                    //dd(DB::getQueryLog());

                    $balanceTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceTotal = array_sum($balanceTotal);

                    $receiptAmount = collect($output)->pluck('receiptAmount')->toArray();
                    $receiptAmount = array_sum($receiptAmount);

                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    $currencyCode = "";
                    $currency = \Helper::companyCurrency($request->companySystemID);

                    if ($request->currencyID == 2) {
                        $currencyCode = $currency->localcurrency->CurrencyCode;
                    }
                    if ($request->currencyID == 3) {
                        $currencyCode = $currency->reportingcurrency->CurrencyCode;
                    }

                    $outputArr = array();

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->documentCurrency][] = $val;
                        }
                    }

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceTotal, 'receiptAmount' => $receiptAmount, 'invoiceAmount' => $invoiceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'customerName' => $customerName->customerShortCode . ' - ' . $customerName->CustomerName, 'reportDate' => date('d/m/Y H:i:s A'), 'currency' => 'Currency: ' . $currencyCode);
                }
                break;
            case 'CA': //Customer Aging
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CAD') { //customer aging detail

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingDetailQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    $invoiceAmountTotal = collect($output['data'])->pluck('invoiceAmount')->toArray();
                    $invoiceAmountTotal = array_sum($invoiceAmountTotal);

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging'], 'invoiceAmountTotal' => $invoiceAmountTotal);
                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingSummaryQRY($request);

                    $outputArr = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->documentCurrency][] = $val;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'agingRange' => $output['aging']);
                }
                break;
            case 'CC': //Customer Collection
                $reportTypeID = $request->reportTypeID;
                $selectedCurrency = '';

                $fromDate = new Carbon($request->fromDate);
                $fromDate = $fromDate->format('d/m/Y');

                $toDate = new Carbon($request->toDate);
                $toDate = $toDate->format('d/m/Y');

                $currencyMaster = CurrencyMaster::where('currencyID', $request->currencyID)->first();

                if ($currencyMaster) {
                    $selectedCurrency = $currencyMaster->CurrencyName;
                }

                if ($reportTypeID == 'CCR') { //Customer collection report

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionQRY($request);

                    $bankPaymentTotal = collect($output)->pluck('BRVDocumentAmount')->toArray();
                    $bankPaymentTotal = array_sum($bankPaymentTotal);

                    $creditNoteTotal = collect($output)->pluck('CNDocumentAmount')->toArray();
                    $creditNoteTotal = array_sum($creditNoteTotal);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                        }
                    }
                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => $decimalPlaces, 'fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCurrency' => $selectedCurrency, 'bankPaymentTotal' => $bankPaymentTotal, 'creditNoteTotal' => $creditNoteTotal);
                } else {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionMonthlyQRY($request);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                        }
                    }

                    $janTotal = collect($output)->pluck('Jan')->toArray();
                    $janTotal = array_sum($janTotal);

                    $febTotal = collect($output)->pluck('Feb')->toArray();
                    $febTotal = array_sum($febTotal);

                    $marTotal = collect($output)->pluck('March')->toArray();
                    $marTotal = array_sum($marTotal);

                    $aprTotal = collect($output)->pluck('April')->toArray();
                    $aprTotal = array_sum($aprTotal);

                    $mayTotal = collect($output)->pluck('May')->toArray();
                    $mayTotal = array_sum($mayTotal);

                    $juneTotal = collect($output)->pluck('June')->toArray();
                    $juneTotal = array_sum($juneTotal);

                    $julyTotal = collect($output)->pluck('July')->toArray();
                    $julyTotal = array_sum($julyTotal);

                    $augTotal = collect($output)->pluck('Aug')->toArray();
                    $augTotal = array_sum($augTotal);

                    $sepTotal = collect($output)->pluck('Sept')->toArray();
                    $sepTotal = array_sum($sepTotal);

                    $octTotal = collect($output)->pluck('Oct')->toArray();
                    $octTotal = array_sum($octTotal);

                    $novTotal = collect($output)->pluck('Nov')->toArray();
                    $novTotal = array_sum($novTotal);

                    $decTotal = collect($output)->pluck('Dece')->toArray();
                    $decTotal = array_sum($decTotal);

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => $decimalPlaces, 'fromDate' => $fromDate, 'toDate' => $toDate, 'selectedCurrency' => $selectedCurrency, 'selectedYear' => $request->year, 'janTotal' => $janTotal, 'febTotal' => $febTotal, 'marTotal' => $marTotal, 'aprTotal' => $aprTotal, 'mayTotal' => $mayTotal, 'juneTotal' => $juneTotal, 'julyTotal' => $julyTotal, 'augTotal' => $augTotal, 'sepTotal' => $sepTotal, 'octTotal' => $octTotal, 'novTotal' => $novTotal, 'decTotal' => $decTotal);

                }
                break;
            case 'CL': //Customer Ledger
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CLT1') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerLedgerTemplate1QRY($request);

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $paidAmount = collect($output)->pluck('paidAmount')->toArray();
                    $paidAmount = array_sum($paidAmount);

                    $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceAmount = array_sum($balanceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount);
                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerLedgerTemplate2QRY($request);

                    $outputArr = array();
                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->concatCustomerName][$val->documentCurrency][] = $val;
                        }
                    }
                    return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'invoiceAmount' => $invoiceAmount);
                }
                break;
            case 'CBSUM': //Customer Balance Summery
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CBSUM') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceSummery($request);

                    $outputArr = array();
                    $localAmount = collect($output)->pluck('localAmount')->toArray();
                    $localAmountTotal = array_sum($localAmount);

                    $rptAmount = collect($output)->pluck('RptAmount')->toArray();
                    $rptAmountTotal = array_sum($rptAmount);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }


                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();


                    return array('reportData' => $output,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlaceLocal' => !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2,
                        'decimalPlaceRpt' => !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2,
                        'localAmountTotal' => $localAmountTotal,
                        'rptAmountTotal' => $rptAmountTotal);
                }
                break;
            case 'CR': //Customer Balance Summery
                $reportTypeID = $request->reportTypeID;

                if ($reportTypeID == 'RMS') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);

                    $currency = $request->currencyID;
                    $currencyId = 2;

                    if($currency == 2){
                        $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }else{
                        $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }

                    if(!empty($decimalPlaceUnique) ){
                        $currencyId = $decimalPlaceUnique[0];
                    }


                    $requestCurrency = CurrencyMaster::where('currencyID',$currencyId )->first();

                    $decimalPlace = !empty($requestCurrency) ? $requestCurrency->DecimalPlaces : 2;

                    $total = array();

                    $total['Jan'] = array_sum(collect($output)->pluck('Jan')->toArray());
                    $total['Feb'] = array_sum(collect($output)->pluck('Feb')->toArray());
                    $total['March'] = array_sum(collect($output)->pluck('March')->toArray());
                    $total['April'] = array_sum(collect($output)->pluck('April')->toArray());
                    $total['May'] = array_sum(collect($output)->pluck('May')->toArray());
                    $total['June'] = array_sum(collect($output)->pluck('June')->toArray());
                    $total['July'] = array_sum(collect($output)->pluck('July')->toArray());
                    $total['Aug'] = array_sum(collect($output)->pluck('Aug')->toArray());
                    $total['Sept'] = array_sum(collect($output)->pluck('Sept')->toArray());
                    $total['Oct'] = array_sum(collect($output)->pluck('Oct')->toArray());
                    $total['Nov'] = array_sum(collect($output)->pluck('Nov')->toArray());
                    $total['Dece'] = array_sum(collect($output)->pluck('Dece')->toArray());
                    $total['Total'] = array_sum(collect($output)->pluck('Total')->toArray());

                    return array('reportData' => $output,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlace' => $decimalPlace,
                        'total' => $total,
                        'currency' => $requestCurrency->CurrencyCode
                    );
                }else{

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getRevenueByCustomer($request);


                    $localAmount = collect($output)->pluck('localAmount')->toArray();
                    $localAmountTotal = array_sum($localAmount);

                    $rptAmount = collect($output)->pluck('RptAmount')->toArray();
                    $rptAmountTotal = array_sum($rptAmount);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }


                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();

                    $outputArr = array();
                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CustomerName][] = $val;
                        }
                    }

                    return array('reportData' => $outputArr,
                        'companyName' => $checkIsGroup->CompanyName,
                        'decimalPlaceLocal' => !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2,
                        'decimalPlaceRpt' => !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2,
                        'localAmountTotal' => $localAmountTotal,
                        'rptAmountTotal' => $rptAmountTotal
                    );

                }
                break;
            case 'CSR': //Customer Sales Register
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getCustomerSalesRegisterQRY($request);

                $outputArr = array();
                $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                $invoiceAmount = array_sum($invoiceAmount);

                $paidAmount = collect($output)->pluck('receiptAmount')->toArray();
                $paidAmount = array_sum($paidAmount);

                $balanceAmount = collect($output)->pluck('balanceAmount')->toArray();
                $balanceAmount = array_sum($balanceAmount);

                $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                    }
                }
                return array('reportData' => $outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'paidAmount' => $paidAmount, 'invoiceAmount' => $invoiceAmount);

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
                    'daterange' => 'required',
                    'suppliers' => 'required',
                ]);


                $startDate = new Carbon($request->daterange[0]);
                //$startDate = $startDate->addDays(1);
                $startDate = $startDate->format('Y-m-d');

                $endDate = new Carbon($request->daterange[1]);
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
                break;
            case 'CS': //Customer Statement Report
                $reportTypeID = $request->reportTypeID;
                $data = array();
                $type = $request->type;
                if ($reportTypeID == 'CBS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    //dd(DB::getQueryLog());
                    $outputArr = array();
                    $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $grandTotal = array_sum($grandTotal);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);
                    $decimalPlace = !empty($decimalPlace) ? $decimalPlace[0] : 2;

                    /*if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }

                    if (!empty($outputArr)) {
                        $x = 0;
                        foreach ($outputArr as $customerName => $currency) {
                            $data[$x][''] = $customerName;
                            if (!empty($currency)) {
                                $x++;
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                $data[$x][''] = '';
                                foreach ($currency as $key => $val) {
                                    $x++;
                                    $data[$x]['Document Code'] = 'Document Code';
                                    $data[$x]['Posted Date'] = 'Posted Date';
                                    $data[$x]['Narration'] = 'Narration';
                                    $data[$x]['Contract'] = 'Contract';
                                    $data[$x]['PO Number'] = 'PO Number';
                                    $data[$x]['Invoice Number'] = 'Invoice Number';
                                    $data[$x]['Invoice Date'] = 'Invoice Date';
                                    $data[$x]['Currency'] = 'Currency';
                                    $data[$x]['Balance Amount'] = 'Balance Amount';
                                    if (!empty($val)) {
                                        $subTotal = 0;
                                        foreach ($val as $key2 => $values) {
                                            $x++;
                                            $data[$x]['Document Code'] = $values->DocumentCode;
                                            $data[$x]['Posted Date'] = $values->PostedDate;
                                            $data[$x]['Narration'] = $values->DocumentNarration;
                                            $data[$x]['Contract'] = $values->Contract;
                                            $data[$x]['PO Number'] = '';
                                            $data[$x]['Invoice Number'] = $values->invoiceNumber;
                                            $data[$x]['Invoice Date'] = $values->InvoiceDate;
                                            $data[$x]['Currency'] = $values->documentCurrency;
                                            $data[$x]['Balance Amount'] = round($values->balanceAmount,$values->balanceDecimalPlaces);
                                            $subTotal += $values->balanceAmount;
                                        }
                                        $x++;
                                        $data[$x]['Document Code'] = '';
                                        $data[$x]['Posted Date'] = '';
                                        $data[$x]['Narration'] = '';
                                        $data[$x]['Contract'] = '';
                                        $data[$x]['PO Number'] = '';
                                        $data[$x]['Invoice Number'] = '';
                                        $data[$x]['Invoice Date'] = '';
                                        $data[$x]['Currency'] = 'Total';
                                        $data[$x]['Balance Amount'] = round($subTotal,$val[0]->balanceDecimalPlaces);

                                        $x++;
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                        $data[$x][''] = '';
                                    }
                                }
                            }
                            $x++;
                        }
                        $x++;
                        $data[$x]['Document Code'] = '';
                        $data[$x]['Posted Date'] = '';
                        $data[$x]['Narration'] = '';
                        $data[$x]['Contract'] = '';
                        $data[$x]['PO Number'] = '';
                        $data[$x]['Invoice Number'] = '';
                        $data[$x]['Invoice Date'] = '';
                        $data[$x]['Currency'] = 'Grand Total';
                        $data[$x]['Balance Amount'] = round($grandTotal,$decimalPlace);
                    }
                }*/

                    if ($output) {
                        foreach ($output as $val) {
                            $data[] = array(
                                'Customer Name' => $val->customerName,
                                'Document Code' => $val->DocumentCode,
                                'Posted Date' => $val->PostedDate,
                                'Narration' => $val->DocumentNarration,
                                'Contract' => $val->Contract,
                                'PO Number' => $val->PONumber,
                                'Invoice Number' => $val->invoiceNumber,
                                'Invoice Date' => \Helper::dateFormat($val->InvoiceDate),
                                'Currency' => $val->documentCurrency,
                                'Balance Amount' => round($val->balanceAmount, $val->balanceDecimalPlaces)
                            );
                        }
                    }
                } else if ($request->reportTypeID == 'CSA') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerStatementAccountQRY($request);
                    //dd(DB::getQueryLog());
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $x++;
                            $data[$x]['Customer Name'] = $val->customerName;
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Posted Date'] = $val->postedDate;
                            $data[$x]['Contract'] = $val->clientContractID;
                            $data[$x]['PO Number'] = $val->PONumber;
                            $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                            $data[$x]['Narration'] = $val->documentNarration;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Invoice Amount'] = round($val->invoiceAmount, $val->balanceDecimalPlaces);
                            $data[$x]['Receipt/CN Code'] = $val->ReceiptCode;
                            $data[$x]['Receipt/CN Date'] = \Helper::dateFormat($val->ReceiptDate);
                            $data[$x]['Receipt Amount'] = round($val->receiptAmount, $val->balanceDecimalPlaces);
                            $data[$x]['Balance Amount'] = round($val->balanceAmount, $val->balanceDecimalPlaces);
                        }
                    }
                }

                $csv = \Excel::create('customer_balance_statement', function ($excel) use ($data) {
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
                break;
            case 'CA': //Customer Aging
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                if ($reportTypeID == 'CAD') { //customer aging detail
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerAgingDetailQRY($request);

                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $lineTotal = 0;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Document Code'] = $val->DocumentCode;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->PostedDate);
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->customerName2;
                            $data[$x]['Contract ID'] = $val->Contract;
                            $data[$x]['PO Number'] = $val->PONumber;
                            $data[$x]['Invoice Number'] = $val->invoiceNumber;
                            $data[$x]['Invoice Date'] = \Helper::dateFormat($val->InvoiceDate);
                            $data[$x]['Invoice Due Date'] = \Helper::dateFormat($val->invoiceDueDate);
                            $data[$x]['Document Narration'] = $val->DocumentNarration;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Invoice Amount'] = $val->invoiceAmount;
                            foreach ($output['aging'] as $val2) {
                                $lineTotal += $val->$val2;
                            }
                            $data[$x]['Outstanding'] = $lineTotal;
                            $data[$x]['Age Days'] = $val->age;
                            foreach ($output['aging'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                            }
                            $data[$x]['Subsequent Collection Amount'] = $val->subsequentAmount;
                            $data[$x]['Current Outstanding'] = $val->subsequentBalanceAmount;
                            $data[$x]['Receipt Matching/BRVNo'] = $val->brvInv;
                            $x++;
                        }
                    }

                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerAgingSummaryQRY($request);

                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $lineTotal = 0;
                            $data[$x]['Cust. Code'] = $val->DocumentCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            foreach ($output['aging'] as $val2) {
                                $lineTotal += $val->$val2;
                            }
                            $data[$x]['Amount'] = $lineTotal;
                            foreach ($output['aging'] as $val2) {
                                $data[$x][$val2] = $val->$val2;
                            }
                            $x++;
                        }
                    }
                }

                $csv = \Excel::create('customer_aging', function ($excel) use ($data) {
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
                break;
            case 'CL': //Customer Ledger
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                if ($reportTypeID == 'CLT1') { //customer aging detail
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerLedgerTemplate1QRY($request);

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Document Code'] = $val->DocumentCode;
                            $data[$x]['Posted Date'] = \Helper::dateFormat($val->PostedDate);
                            $data[$x]['Invoice Number'] = $val->invoiceNumber;
                            $data[$x]['Invoice Date'] = \Helper::dateFormat($val->InvoiceDate);
                            $data[$x]['Contract'] = $val->Contract;
                            $data[$x]['Narration'] = $val->DocumentNarration;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Invoice Amount'] = $val->invoiceAmount;
                            $data[$x]['Paid Amount'] = $val->paidAmount;
                            $data[$x]['Balance Amount'] = $val->balanceAmount;
                            $data[$x]['Age Days'] = $val->ageDays;
                            $x++;
                        }
                    }

                } else {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerLedgerTemplate2QRY($request);

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Document Code'] = $val->DocumentCode;
                            if ($val->PostedDate == '1970-01-01') {
                                $data[$x]['Posted Date'] = '';
                            } else {
                                $data[$x]['Posted Date'] = \Helper::dateFormat($val->PostedDate);
                            }
                            $data[$x]['Invoice Number'] = $val->invoiceNumber;
                            $data[$x]['Invoice Date'] = \Helper::dateFormat($val->InvoiceDate);
                            $data[$x]['Document Narration'] = $val->DocumentNarration;
                            $data[$x]['Currency'] = $val->documentCurrency;
                            $data[$x]['Amount'] = $val->invoiceAmount;
                            $x++;
                        }
                    }
                }

                $csv = \Excel::create('customer_ledger', function ($excel) use ($data) {
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
                break;
            case 'CBSUM': //Customer Balance Summery
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'CBSUM') { //customer ledger template 1

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceSummery($request);

                    $localAmount = collect($output)->pluck('localAmount')->toArray();
                    $localAmountTotal = array_sum($localAmount);

                    $rptAmount = collect($output)->pluck('RptAmount')->toArray();
                    $rptAmountTotal = array_sum($rptAmount);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }


                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();

                    $currencyID = $request->currencyID;
                    $type = $request->type;

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Cust. Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;

                            $decimalPlace = 2;
                            if ($currencyID == '2') {
                                $decimalPlace = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            } else if ($currencyID == '3') {
                                $decimalPlace = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentRptCurrency;
                                $data[$x]['Amount'] = round($val->RptAmount, $decimalPlace);
                            } else {
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = $val->localAmount;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            }
                            $x++;
                        }
                    } else {
                        $data = array();
                    }

                    $csv = \Excel::create('customer_balance_summary', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);

                    return $this->sendResponse(array(), 'successfully export');
                }
                break;
            case 'CSR': //Customer Sales Register
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getCustomerSalesRegisterQRY($request);

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Posted Date'] = \Helper::dateFormat($val->PostedDate);
                        $data[$x]['Service Line'] = $val->serviceLineCode;
                        $data[$x]['Contract'] = $val->clientContractID;
                        $data[$x]['PO Number'] = $val->PONumber;
                        $data[$x]['SE No'] = $val->wanNO;
                        $data[$x]['Rig No'] = $val->rigNo;
                        $data[$x]['Service Period'] = $val->servicePeriod;
                        $data[$x]['Start Date'] = \Helper::dateFormat($val->serviceStartDate);
                        $data[$x]['End Date'] = \Helper::dateFormat($val->serviceEndDate);
                        $data[$x]['Invoice Number'] = $val->invoiceNumber;
                        $data[$x]['Invoice Date'] = \Helper::dateFormat($val->invoiceDate);
                        $data[$x]['Narration'] = $val->documentNarration;
                        $data[$x]['Currency'] = $val->documentCurrency;
                        $data[$x]['Invoice Amount'] = $val->invoiceAmount;
                        $data[$x]['Receipt Code'] = $val->ReceiptCode;
                        $data[$x]['Receipt Date'] = \Helper::dateFormat($val->ReceiptDate);
                        $data[$x]['Amount Matched'] = $val->receiptAmount;
                        $data[$x]['Balance'] = $val->balanceAmount;
                        $x++;
                    }
                }

                $csv = \Excel::create('customer_sales_register', function ($excel) use ($data) {
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
                break;

            case 'CC': //Customer Collection
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if ($companyCurrency) {
                    if ($request->currencyID == 2) {
                        $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                    } else if ($request->currencyID == 3) {
                        $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                    }
                }

                if ($reportTypeID == 'CCR') { //customer aging detail

                    if ($request->excelForm == 'bankReport') {

                        $output = $this->getCustomerCollectionBRVExcelQRY($request);

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Customer Code'] = $val->CutomerCode;
                                $data[$x]['Customer Short Code'] = $val->customerShortCode;
                                $data[$x]['Customer Name'] = $val->CustomerName;
                                $data[$x]['Document Code'] = $val->documentCode;
                                $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                                $data[$x]['Bank Name'] = $val->bankName;
                                $data[$x]['Account No'] = $val->AccountNo;
                                $data[$x]['Bank Currency'] = $val->bankCurrencyCode;
                                $data[$x]['Document Narration'] = $val->documentNarration;
                                $data[$x]['Currency Code'] = $selectedCurrency;
                                $data[$x]['BRV Document Amount'] = $val->BRVDocumentAmount;
                                $x++;
                            }
                        }

                    } else if ($request->excelForm == 'creditNoteReport') {

                        $output = $this->getCustomerCollectionCNExcelQRY($request);

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                                $data[$x]['Customer Code'] = $val->CutomerCode;
                                $data[$x]['Customer Short Code'] = $val->customerShortCode;
                                $data[$x]['Customer Name'] = $val->CustomerName;
                                $data[$x]['Document Code'] = $val->documentCode;
                                $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                                $data[$x]['Document Narration'] = $val->documentNarration;
                                $data[$x]['Currency Code'] = $selectedCurrency;
                                $data[$x]['CN Document Amount'] = $val->CNDocumentAmount;
                                $x++;
                            }
                        }
                    }

                } else {
                    $output = $this->getCustomerCollectionMonthlyQRY($request);

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Jan'] = $val->Jan;
                            $data[$x]['Feb'] = $val->Feb;
                            $data[$x]['March'] = $val->March;
                            $data[$x]['April'] = $val->April;
                            $data[$x]['May'] = $val->May;
                            $data[$x]['Jun'] = $val->June;
                            $data[$x]['July'] = $val->July;
                            $data[$x]['Aug'] = $val->Aug;
                            $data[$x]['Sept'] = $val->Sept;
                            $data[$x]['Oct'] = $val->Oct;
                            $data[$x]['Nov'] = $val->Nov;
                            $data[$x]['Dec'] = $val->Dece;
                            $data[$x]['Tot'] = ($val->Jan + $val->Feb + $val->March + $val->April + $val->May + $val->June + $val->July + $val->Aug + $val->Sept + $val->Oct + $val->Nov + $val->Dece);
                            $x++;
                        }
                    }
                }

                $csv = \Excel::create('customer_collection', function ($excel) use ($data) {
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
                break;
            case 'CR': //Customer Revenue
                $reportTypeID = $request->reportTypeID;

                if ($reportTypeID == 'RC') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getRevenueByCustomer($request);

                    $decimalPlaceLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                    $decimalPlaceL = array_unique($decimalPlaceLocal);

                    $decimalPlaceRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                    $decimalPlaceR = array_unique($decimalPlaceRpt);

                    $localCurrencyId = 2;
                    $rptCurrencyId = 2;

                    if (!empty($decimalPlaceL)) {
                        $localCurrencyId = $decimalPlaceL[0];
                    }

                    if (!empty($decimalPlaceR)) {
                        $rptCurrencyId = $decimalPlaceR[0];
                    }

                    $localCurrency = CurrencyMaster::where('currencyID', $localCurrencyId)->first();
                    $rptCurrency = CurrencyMaster::where('currencyID', $rptCurrencyId)->first();

                    $currencyID = $request->currencyID;
                    $type = $request->type;

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {

                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Contract No'] = $val->ContractNumber;
                            $data[$x]['Contract Description'] = $val->contractDescription;
                            $data[$x]['Contract/PO'] = $val->CONTRACT_PO;
                            $data[$x]['Contract Description'] = \Helper::dateFormat($val->ContEndDate);
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['GL Desc'] = $val->AccountDescription;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Posting Month'] = $val->PostingMonth;
                            $data[$x]['Posting Year'] = $val->PostingYear;
                            $data[$x]['Narration'] = $val->documentNarration;

                            $decimalPlace = 2;
                            if ($currencyID == '2') {
                                $decimalPlace = !empty($localCurrency) ? $localCurrency->DecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            } else if ($currencyID == '3') {
                                $decimalPlace = !empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentRptCurrency;
                                $data[$x]['Amount'] = round($val->RptAmount, $decimalPlace);
                            } else {
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = $val->localAmount;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            }
                            $x++;
                        }
                    } else {
                        $data = array();
                    }

                    $csv = \Excel::create('revenue_by_customer', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);


                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function pdfExportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS':
                if ($request->reportTypeID == 'CSA') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $customerName = CustomerMaster::find($request->singleCustomer);

                    /*$fromDate = new Carbon("2016-03-25T12:00:00Z");
                    $fromDate = $fromDate->addDays(1);
                    $request->fromDate = $fromDate->format('Y-m-d');

                    $toDate = new Carbon("2018-03-25T12:00:00Z");
                    $toDate = $toDate->addDays(1);
                    $request->toDate = $toDate->format('Y-m-d');*/

                    /*$request->fromDate = date('Y-m-d',strtotime($request->fromDate));
                    $request->toDate =  date('Y-m-d',strtotime($request->toDate));*/

                    $output = $this->getCustomerStatementAccountQRY($request);

                    $balanceTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceTotal = array_sum($balanceTotal);

                    $receiptAmount = collect($output)->pluck('receiptAmount')->toArray();
                    $receiptAmount = array_sum($receiptAmount);

                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    $currencyCode = "";
                    $currency = \Helper::companyCurrency($request->companySystemID);

                    if ($request->currencyID == 2) {
                        $currencyCode = $currency->localcurrency->CurrencyCode;
                    }
                    if ($request->currencyID == 3) {
                        $currencyCode = $currency->reportingcurrency->CurrencyCode;
                    }

                    $outputArr = array();

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->documentCurrency][] = $val;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'balanceAmount' => $balanceTotal, 'receiptAmount' => $receiptAmount, 'invoiceAmount' => $invoiceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'customerName' => $customerName->customerShortCode . ' - ' . $customerName->CustomerName, 'reportDate' => date('d/m/Y H:i:s A'), 'currency' => 'Currency: ' . $currencyCode, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'currencyID' => $request->currencyID);

                    $html = view('print.customer_statement_of_account_pdf', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            default:
                return $this->sendError('No report ID found');
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

        $controlAccount = CustomerMaster::groupBy('custGLAccountSystemID')->pluck('custGLAccountSystemID');
        $controlAccount = ChartOfAccount::whereIN('chartOfAccountSystemID', $controlAccount)->get();

        $departments = \Helper::getCompanyServiceline($selectedCompanyId);

        $departments[] = array("serviceLineSystemID" => 24, "ServiceLineCode" => 'X', "serviceLineMasterCode" => 'X', "ServiceLineDes" => 'X');

        $filterCustomers = AccountsReceivableLedger::whereIN('companySystemID', $companiesByGroup)
            ->select('customerID')
            ->groupBy('customerID')
            ->pluck('customerID');

        $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)->whereIN('customerCodeSystem', $filterCustomers)->groupBy('customerCodeSystem')->orderBy('CustomerName', 'ASC')->get();

        $years = GeneralLedger::select(DB::raw("YEAR(documentDate) as year"))
            ->whereNotNull('documentDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get(['year']);

        $output = array(
            'controlAccount' => $controlAccount,
            'customers' => $customerMaster,
            'departments' => $departments,
            'years' => $years,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    function getCustomerStatementAccountQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $currency = $request->currencyID;
        $customer = $request->singleCustomer;

        $balanceAmountQry = '';
        $receiptAmountQry = '';
        $decimalPlaceQry = '';
        $invoiceAmountQry = '';
        $currencyQry = '';
        if ($currency == 1) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount,0),MainQuery.documentTransDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentTransCurrency AS documentCurrency";
        } else if ($currency == 2) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentLocalCurrency AS documentCurrency";
        } else {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount,0),MainQuery.documentRptDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentRptCurrency AS documentCurrency";
        }
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
	MainQuery.companyID,
	MainQuery.documentCode,
	MainQuery.documentDate AS postedDate,
	MainQuery.clientContractID,
	MainQuery.invoiceDate,
	MainQuery.documentNarration,
	InvoiceFromBRVAndMatching.ReceiptCode,
	InvoiceFromBRVAndMatching.ReceiptDate,
' . $balanceAmountQry . ',
	' . $receiptAmountQry . ',
	' . $invoiceAmountQry . ',
	' . $currencyQry . ',
    ' . $decimalPlaceQry . ',
    MainQuery.customerName,
    MainQuery.PONumber
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
	DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ) AS documentDateFilter,
	erp_generalledger.invoiceNumber,
	erp_generalledger.invoiceDate,
	erp_generalledger.chartOfAccountSystemID,
	erp_generalledger.glCode,
	erp_generalledger.documentNarration,
	erp_generalledger.clientContractID,
	erp_generalledger.supplierCodeSystem,
	erp_generalledger.documentTransCurrencyID,
	erp_generalledger.documentTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	erp_generalledger.documentLocalAmount,
	erp_generalledger.documentRptCurrencyID,
	erp_generalledger.documentRptAmount,
	currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
	currRpt.DecimalPlaces AS documentRptDecimalPlaces,
	currTrans.DecimalPlaces AS documentTransDecimalPlaces,
	currRpt.CurrencyCode AS documentRptCurrency,
	currLocal.CurrencyCode AS documentLocalCurrency,
	currTrans.CurrencyCode AS documentTransCurrency,
	erp_custinvoicedirect.PONumber,
	CONCAT( customermaster.CutomerCode, " - ", customermaster.CustomerName ) AS customerName 
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
	erp_generalledger.documentSystemID = 20 
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '"
	AND "' . $toDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ')
	AND erp_generalledger.supplierCodeSystem = ' . $customer . '
	) AS MainQuery
	LEFT JOIN (
SELECT
	InvoiceFromUNION.companySystemID,
	InvoiceFromUNION.companyID,
	max( InvoiceFromUNION.custPaymentReceiveCode ) AS ReceiptCode,
	max( InvoiceFromUNION.postedDate ) AS ReceiptDate,
	InvoiceFromUNION.addedDocumentSystemID,
	InvoiceFromUNION.addedDocumentID,
	InvoiceFromUNION.bookingInvCodeSystem,
	InvoiceFromUNION.bookingInvCode,
	sum( InvoiceFromUNION.receiveAmountTrans ) AS InvoiceTransAmount,
	sum( InvoiceFromUNION.receiveAmountLocal ) AS InvoiceLocalAmount,
	sum( InvoiceFromUNION.receiveAmountRpt ) AS InvoiceRptAmount 
FROM
	(
SELECT
	* 
FROM
	(
SELECT
	erp_customerreceivepayment.custPaymentReceiveCode,
	erp_customerreceivepayment.postedDate,
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
	AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				/*AND DATE(erp_customerreceivepayment.postedDate) <= "' . $toDate . '"*/
	) AS InvoiceFromBRV UNION ALL
SELECT
	* 
FROM
	(
SELECT
	erp_matchdocumentmaster.matchingDocCode,
	erp_matchdocumentmaster.matchingDocdate,
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
	AND erp_custreceivepaymentdet.companySystemID  IN (' . join(',', $companyID) . ')
				/*AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $toDate . '"*/
	) AS InvoiceFromMatching 
	) AS InvoiceFromUNION 
GROUP BY
	bookingInvCode 
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem ORDER BY postedDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }

    function getCustomerBalanceStatementQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
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
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
        }
        $currencyID = $request->currencyID;
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
	final.documentCode AS DocumentCode,
	final.documentDate AS PostedDate,
	final.documentNarration AS DocumentNarration,
	final.clientContractID AS Contract,
	final.invoiceNumber AS invoiceNumber,
	final.invoiceDate AS InvoiceDate,
	' . $amountQry . ',
	' . $currencyQry . ',
	' . $decimalPlaceQry . ',
	final.customerName AS customerName, 
	final.PONumber
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
	mainQuery.customerName,   
	mainQuery.PONumber 
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
	erp_custinvoicedirect.PONumber,
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName
FROM
	erp_generalledger 
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
	LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
	( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
	AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ' )
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
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
		AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
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
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
		AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
				AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
				AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
			) AS InvoiceFromMatching 
		) AS InvoiceFromUNION 
	GROUP BY
		bookingInvCode 
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
	) AS final 
WHERE
' . $whereQry . ' <> 0 ORDER BY PostedDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }

    // Customer Aging detail report
    function getCustomerAgingDetailQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
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

        $z = 1;
        $aging = array();
        $interval = $request->interval;
        $through = $request->through;
        $agingRange = range(0, $through, $interval);
        $rangeAmount = $interval;
        $agingAgeCount = count($agingRange);
        foreach ($agingRange as $val) {
            if ($z == $agingAgeCount) {
                $aging[] = $val + 1 . "-" . $through;
            } else {
                if ($z == 1) {
                    $aging[] = $val . "-" . $rangeAmount;
                } else {
                    $aging[] = $val + 1 . "-" . $rangeAmount;
                }
                $rangeAmount += $interval;
            }
            $z++;
        }

        $aging[] = "> " . ($through);
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "if(grandFinal.age > " . $through . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "if(grandFinal.age >= " . $list[0] . " AND grandFinal.age <= " . $list[1] . ",grandFinal.balanceAmount,0) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "if(grandFinal.age <= 0,grandFinal.balanceAmount,0) as `current`";


        $currencyQry = '';
        $amountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        $subsequentBalanceQry = '';
        $subsequentQry = '';
        $invoiceQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
            $subsequentBalanceQry = "round( final.balanceSubsequentCollectionTrans, final.documentTransDecimalPlaces ) as subsequentBalanceAmount";
            $subsequentQry = "round( final.SubsequentCollectionTransAmount, final.documentTransDecimalPlaces ) AS subsequentAmount";
            $invoiceQry = "round( final.documentTransAmount, final.documentTransDecimalPlaces ) AS invoiceAmount";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
            $subsequentBalanceQry = "round( final.balanceSubsequentCollectionLocal, final.documentLocalDecimalPlaces ) as subsequentBalanceAmount";
            $subsequentQry = "round( final.SubsequentCollectionLocalAmount, final.documentLocalDecimalPlaces ) AS subsequentAmount";
            $invoiceQry = "round( final.documentLocalAmount, final.documentLocalDecimalPlaces ) AS invoiceAmount";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
            $subsequentBalanceQry = "round( final.balanceSubsequentCollectionRpt, final.documentRptDecimalPlaces ) as subsequentBalanceAmount";
            $subsequentQry = "round( final.SubsequentCollectionRptAmount, final.documentRptDecimalPlaces ) AS subsequentAmount";
            $invoiceQry = "round( final.documentRptAmount, final.documentRptDecimalPlaces ) AS invoiceAmount";
        }
        $currencyID = $request->currencyID;
        //DB::enableQueryLog();
        $output = \DB::select('SELECT 
        DocumentCode,PostedDate,DocumentNarration,Contract,invoiceNumber,InvoiceDate,' . $agingField . ',documentCurrency,balanceDecimalPlaces,customerName,age,glCode,customerName2,CutomerCode,PONumber,invoiceDueDate,subsequentBalanceAmount,brvInv,subsequentAmount,companyID,invoiceAmount FROM (SELECT
	final.documentCode AS DocumentCode,
	final.documentDate AS PostedDate,
	final.documentNarration AS DocumentNarration,
	final.clientContractID AS Contract,
	final.invoiceNumber AS invoiceNumber,
	final.invoiceDate AS InvoiceDate,
	' . $amountQry . ',
	' . $subsequentQry . ',
	' . $subsequentBalanceQry . ',
	' . $currencyQry . ',
	' . $decimalPlaceQry . ',
	' . $invoiceQry . ',
	final.customerName AS customerName,
	final.customerName2 AS customerName2,
	final.CutomerCode AS CutomerCode,
	DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as age,
	final.glCode, 
	final.PONumber, 
	final.invoiceDueDate, 
	final.brvInv, 
	final.companyID 
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
	
	(
	mainQuery.documentRptAmount + ( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionRptAmount,0)) 
	) AS balanceSubsequentCollectionRpt,
	(
	mainQuery.documentLocalAmount + ( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionLocalAmount,0)) 
	) AS balanceSubsequentCollectionLocal,
	(
	mainQuery.documentTransAmount + ( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) -  IFNULL(Subsequentcollection.SubsequentCollectionTransAmount,0)) 
	) AS balanceSubsequentCollectionTrans,
	
	mainQuery.customerName,   
	mainQuery.customerName2,   
	mainQuery.CutomerCode,   
	mainQuery.PONumber,   
	mainQuery.invoiceDueDate,
	IFNULL(Subsequentcollection.SubsequentCollectionRptAmount,0) as SubsequentCollectionRptAmount,
	IFNULL(Subsequentcollection.SubsequentCollectionLocalAmount,0) as SubsequentCollectionLocalAmount,
	IFNULL(Subsequentcollection.SubsequentCollectionTransAmount,0) as SubsequentCollectionTransAmount,
	Subsequentcollection.docCode as brvInv
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
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName,
	customermaster.CustomerName as customerName2,
	customermaster.CutomerCode,
	erp_custinvoicedirect.PONumber,
	erp_custinvoicedirect.invoiceDueDate
FROM
	erp_generalledger 
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
	LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
	( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
	AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ' )
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
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
		AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
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
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
		AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
				AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
				AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
			) AS InvoiceFromMatching 
		) AS InvoiceFromUNION 
	GROUP BY
		bookingInvCode 
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
	LEFT JOIN (
	SELECT
		erp_custreceivepaymentdet.companySystemID,
		erp_custreceivepaymentdet.companyID,
		max( erp_custreceivepaymentdet.custReceivePaymentAutoID ) AS ReceiptSystemID,
		max( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.custPaymentReceiveCode, erp_matchdocumentmaster.matchingDocCode ) ) AS docCode,
		erp_custreceivepaymentdet.addedDocumentSystemID,
		erp_custreceivepaymentdet.addedDocumentID,
		erp_custreceivepaymentdet.bookingInvCodeSystem,
		sum( erp_custreceivepaymentdet.receiveAmountTrans ) AS SubsequentCollectionTransAmount,
		sum( erp_custreceivepaymentdet.receiveAmountLocal ) AS SubsequentCollectionLocalAmount,
		sum( erp_custreceivepaymentdet.receiveAmountRpt ) AS SubsequentCollectionRptAmount 
	FROM
		erp_custreceivepaymentdet
		LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
		LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID 
	WHERE
		erp_custreceivepaymentdet.bookingInvCodeSystem > 0 
		AND DATE(
				( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.postedDate, erp_matchdocumentmaster.matchingDocdate ) )
			)
		 > "' . $asOfDate . '" 
		AND ( IF ( erp_custreceivepaymentdet.matchingDocID = 0 OR erp_custreceivepaymentdet.matchingDocID IS NULL, erp_customerreceivepayment.approved, erp_matchdocumentmaster.matchingConfirmedYN ) ) <> 0 
	GROUP BY
		addedDocumentSystemID,
		bookingInvCodeSystem 
	) AS Subsequentcollection ON Subsequentcollection.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = Subsequentcollection.bookingInvCodeSystem 
	) AS final 
WHERE
' . $whereQry . ' <> 0) as grandFinal ORDER BY PostedDate ASC');
        //dd(DB::getQueryLog());
        return ['data' => $output, 'aging' => $aging];
    }


    function getCustomerAgingSummaryQRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
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

        $z = 1;
        $aging = array();
        $interval = $request->interval;
        $through = $request->through;
        $agingRange = range(0, $through, $interval);
        $rangeAmount = $interval;
        $agingAgeCount = count($agingRange);
        foreach ($agingRange as $val) {
            if ($z == $agingAgeCount) {
                $aging[] = $val + 1 . "-" . $through;
            } else {
                if ($z == 1) {
                    $aging[] = $val . "-" . $rangeAmount;
                } else {
                    $aging[] = $val + 1 . "-" . $rangeAmount;
                }
                $rangeAmount += $interval;
            }
            $z++;
        }

        $aging[] = "> " . ($through);
        $agingField = '';
        if (!empty($aging)) { /*calculate aging range in query*/
            $count = count($aging);
            $c = 1;
            foreach ($aging as $val) {
                if ($count == $c) {
                    $agingField .= "SUM(if(grandFinal.age > " . $through . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                } else {
                    $list = explode("-", $val);
                    $agingField .= "SUM(if(grandFinal.age >= " . $list[0] . " AND grandFinal.age <= " . $list[1] . ",grandFinal.balanceAmount,0)) as `" . $val . "`,";
                }
                $c++;
            }
        }
        $agingField .= "SUM(if(grandFinal.age <= 0,grandFinal.balanceAmount,0)) as `current`";


        $currencyQry = '';
        $amountQry = '';
        $decimalPlaceQry = '';
        $whereQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $amountQry = "round( final.balanceTrans, final.documentTransDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceTrans, final.documentTransDecimalPlaces )";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $amountQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceLocal, final.documentLocalDecimalPlaces )";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $amountQry = "round( final.balanceRpt, final.documentRptDecimalPlaces ) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $whereQry = "round( final.balanceRpt, final.documentRptDecimalPlaces )";
        }
        $currencyID = $request->currencyID;
        //DB::enableQueryLog();
        $output = \DB::select('SELECT DocumentCode,PostedDate,DocumentNarration,Contract,invoiceNumber,InvoiceDate,' . $agingField . ',documentCurrency,balanceDecimalPlaces,CustomerName,CustomerCode,customerCodeSystem FROM (SELECT
	final.documentCode AS DocumentCode,
	final.documentDate AS PostedDate,
	final.documentNarration AS DocumentNarration,
	final.clientContractID AS Contract,
	final.invoiceNumber AS invoiceNumber,
	final.invoiceDate AS InvoiceDate,
	' . $amountQry . ',
	' . $currencyQry . ',
	' . $decimalPlaceQry . ',
	final.CustomerName,
	final.CutomerCode as CustomerCode,
	final.supplierCodeSystem AS customerCodeSystem,
	DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as age 
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
	mainQuery.CustomerName,
	mainQuery.CutomerCode
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
	customermaster.CustomerName,
	customermaster.CutomerCode
FROM
	erp_generalledger 
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
WHERE
	( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
	AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ' )
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
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
		AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
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
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
		AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
				AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
				AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
			) AS InvoiceFromMatching 
		) AS InvoiceFromUNION 
	GROUP BY
		bookingInvCode 
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
	) AS final 
WHERE
' . $whereQry . ' <> 0 ORDER BY PostedDate ASC) as grandFinal GROUP BY customerCodeSystem ORDER BY CustomerName');
        //dd(DB::getQueryLog());
        return ['data' => $output, 'aging' => $aging];
    }

    // Customer Collection report
    function getCustomerCollectionQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        if ($currency == 1) {
            $currencyBRVAmount = "SUM( collectionDetail.BRVTransAmount) AS BRVDocumentAmount";
            $currencyCNAmount = "SUM( collectionDetail.CNTransAmount) AS CNDocumentAmount";
        } else if ($currency == 2) {
            $currencyBRVAmount = "SUM( collectionDetail.BRVLocalAmount) AS BRVDocumentAmount";
            $currencyCNAmount = "SUM( collectionDetail.CNLocalAmount) AS CNDocumentAmount";
        } else {
            $currencyBRVAmount = "SUM( collectionDetail.BRVRptAmount) AS BRVDocumentAmount";
            $currencyCNAmount = "SUM( collectionDetail.CNRptAmount) AS CNDocumentAmount";
        }

        $output = \DB::select('SELECT
	collectionDetail.companyID,
	collectionDetail.CutomerCode,
	collectionDetail.CustomerName,
	' . $currencyBRVAmount . ',
	' . $currencyCNAmount . '
FROM
	(
		SELECT
			erp_generalledger.companyID,
			erp_generalledger.documentID,
			erp_generalledger.serviceLineCode,
			erp_generalledger.documentSystemCode,
			erp_generalledger.documentCode,
			erp_generalledger.documentDate,
			MONTH (
				erp_generalledger.documentDate
			) AS DocMONTH,
			YEAR (
				erp_generalledger.documentDate
			) AS DocYEAR,
			erp_generalledger.supplierCodeSystem,
			customermaster.CutomerCode,
			customermaster.customerShortCode,
			customermaster.CustomerName,

		IF (
			erp_generalledger.documentSystemID = "21",
			ROUND(documentTransAmount, 0),
			0
		) BRVTransAmount,

	IF (
		erp_generalledger.documentSystemID = "21",
		ROUND(documentLocalAmount, 0),
		0
	) BRVLocalAmount,

IF (
	erp_generalledger.documentSystemID = "21",
	ROUND(documentRptAmount, 0),
	0
) BRVRptAmount,

IF (
	erp_generalledger.documentSystemID = "19",
	ROUND(documentTransAmount, 0),
	0
) CNTransAmount,

IF (
	erp_generalledger.documentSystemID = "19",
	ROUND(documentLocalAmount, 0),
	0
) CNLocalAmount,

IF (
	erp_generalledger.documentSystemID = "19",
	ROUND(documentRptAmount, 0),
	0
) CNRptAmount
FROM
	erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
WHERE
	(
		erp_generalledger.documentSystemID = 21
		OR erp_generalledger.documentSystemID = 19
	)
 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0
	) AS collectionDetail
GROUP BY
	collectionDetail.companyID,
	collectionDetail.CutomerCode;');

        return $output;

    }


    function getCustomerLedgerTemplate1QRY($request)
    {
        $asOfDate = new Carbon($request->fromDate);
        //$asOfDate = $asOfDate->addDays(1);
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
        $invoiceAmountQry = '';
        $paidAmountQry = '';
        $balanceAmountQry = '';
        $decimalPlaceQry = '';
        if ($currency == 1) {
            $currencyQry = "final.documentTransCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( final.documentTransAmount, final.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "IFNULL(round( final.paidTransAmount, final.documentTransDecimalPlaces ),0) AS paidAmount";
            $balanceAmountQry = "IFNULL(round( final.balanceTrans, final.documentTransDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "final.documentTransDecimalPlaces AS balanceDecimalPlaces";
        } else if ($currency == 2) {
            $currencyQry = "final.documentLocalCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( final.documentLocalAmount, final.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "IFNULL(round( final.paidLocalAmount, final.documentLocalDecimalPlaces ),0) AS paidAmount";
            $balanceAmountQry = "IFNULL(round( final.balanceLocal, final.documentLocalDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "final.documentLocalDecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "final.documentRptCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( final.documentRptAmount, final.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $paidAmountQry = "IFNULL(round( final.paidRptAmount, final.documentRptDecimalPlaces ),0) AS paidAmount";
            $balanceAmountQry = "IFNULL(round( final.balanceRpt, final.documentRptDecimalPlaces ),0) AS balanceAmount";
            $decimalPlaceQry = "final.documentRptDecimalPlaces AS balanceDecimalPlaces";
        }
        $currencyID = $request->currencyID;
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
	final.documentCode AS DocumentCode,
	final.documentDate AS PostedDate,
	final.documentNarration AS DocumentNarration,
	final.clientContractID AS Contract,
	final.invoiceNumber AS invoiceNumber,
	final.invoiceDate AS InvoiceDate,
	' . $invoiceAmountQry . ',
	' . $paidAmountQry . ',
	' . $balanceAmountQry . ',
	' . $currencyQry . ',
	' . $decimalPlaceQry . ',
	final.customerName AS customerName, 
	final.PONumber,
	DATEDIFF("' . $asOfDate . '",DATE(final.documentDate)) as ageDays
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
	(( IF ( matchedBRV.MatchedBRVRptAmount IS NULL, 0, matchedBRV.MatchedBRVRptAmount ) ) + ( IF ( InvoicedBRV.BRVRptAmount IS NULL, 0, InvoicedBRV.BRVRptAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceRptAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceRptAmount *- 1 ))) as paidRptAmount,
	(( IF ( matchedBRV.MatchedBRVLocalAmount IS NULL, 0, matchedBRV.MatchedBRVLocalAmount ) ) + ( IF ( InvoicedBRV.BRVLocalAmount IS NULL, 0, InvoicedBRV.BRVLocalAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceLocalAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceLocalAmount *- 1 ) )) as paidLocalAmount,
	(( IF ( matchedBRV.MatchedBRVTransAmount IS NULL, 0, matchedBRV.MatchedBRVTransAmount ) ) + ( IF ( InvoicedBRV.BRVTransAmount IS NULL, 0, InvoicedBRV.BRVTransAmount ) ) + ( IF ( InvoiceFromBRVAndMatching.InvoiceTransAmount IS NULL, 0, InvoiceFromBRVAndMatching.InvoiceTransAmount *- 1 ) )) as paidTransAmount,
	mainQuery.customerName,   
	mainQuery.PONumber 
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
	erp_custinvoicedirect.PONumber,
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as customerName
FROM
	erp_generalledger 
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
	LEFT JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
	LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
WHERE
	( erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21" ) 
	AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ' )
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
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
		AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
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
		AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '"
		AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_customerreceivepayment.postedDate) <= "' . $asOfDate . '" 
				AND erp_customerreceivepayment.customerID IN (' . join(',', $customerSystemID) . ')
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
				AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
				AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $asOfDate . '" 
				AND erp_matchdocumentmaster.BPVsupplierID IN (' . join(',', $customerSystemID) . ')
			) AS InvoiceFromMatching 
		) AS InvoiceFromUNION 
	GROUP BY
		bookingInvCode 
	) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
	AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem 
	) AS final 
 ORDER BY PostedDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }

    function getCustomerLedgerTemplate2QRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

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

        $currencyID = $request->currencyID;
        $currencyQry = '';
        $invoiceAmountQry = '';
        $decimalPlaceQry = '';
        if ($currencyID == 1) {
            $currencyQry = "CustomerBalanceSummary_Detail.documentTransCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( CustomerBalanceSummary_Detail.documentTransAmount, CustomerBalanceSummary_Detail.documentTransDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "CustomerBalanceSummary_Detail.documentTransDecimalPlaces AS balanceDecimalPlaces";
        } else if ($currencyID == 2) {
            $currencyQry = "CustomerBalanceSummary_Detail.documentLocalCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( CustomerBalanceSummary_Detail.documentLocalAmount, CustomerBalanceSummary_Detail.documentLocalDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "CustomerBalanceSummary_Detail.documentLocalDecimalPlaces AS balanceDecimalPlaces";
        } else {
            $currencyQry = "CustomerBalanceSummary_Detail.documentRptCurrency AS documentCurrency";
            $invoiceAmountQry = "IFNULL(round( CustomerBalanceSummary_Detail.documentRptAmount, CustomerBalanceSummary_Detail.documentRptDecimalPlaces ),0) AS invoiceAmount";
            $decimalPlaceQry = "CustomerBalanceSummary_Detail.documentRptDecimalPlaces AS balanceDecimalPlaces";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT
    CustomerBalanceSummary_Detail.documentCode AS DocumentCode,
	CustomerBalanceSummary_Detail.documentDate AS PostedDate,
	CustomerBalanceSummary_Detail.documentNarration AS DocumentNarration,
	CustomerBalanceSummary_Detail.invoiceNumber AS invoiceNumber,
	CustomerBalanceSummary_Detail.invoiceDate AS InvoiceDate,
	CustomerBalanceSummary_Detail.CutomerCode,
	CustomerBalanceSummary_Detail.CustomerName,
	CustomerBalanceSummary_Detail.documentLocalCurrencyID,
	CustomerBalanceSummary_Detail.concatCustomerName,
	 ' . $currencyQry . ',
	' . $decimalPlaceQry . ',
	' . $invoiceAmountQry . '
FROM
(
SELECT
	erp_generalledger.companySystemID,
	erp_generalledger.companyID,
	erp_generalledger.documentID,
	erp_generalledger.documentSystemCode,
	erp_generalledger.documentCode,
	erp_generalledger.documentDate,
	erp_generalledger.glCode,
	erp_generalledger.supplierCodeSystem,
	customermaster.CutomerCode,
	customermaster.CustomerName,
	erp_generalledger.invoiceNumber,
	erp_generalledger.invoiceDate,
	erp_generalledger.chartOfAccountSystemID,
	erp_generalledger.documentNarration,
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
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as concatCustomerName
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem=erp_generalledger.supplierCodeSystem
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
WHERE
	(erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21")
	AND DATE( erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ')
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
	UNION ALL 
	SELECT
	erp_generalledger.companySystemID,
	erp_generalledger.companyID,
	erp_generalledger.documentID,
	erp_generalledger.documentSystemCode,
	"Opening Balance" as documentCode,
	"1970-01-01" as documentDate,
	erp_generalledger.glCode,
	erp_generalledger.supplierCodeSystem,
	customermaster.CutomerCode,
	customermaster.CustomerName,
	"" as invoiceNumber,
	"" as invoiceDate,
	erp_generalledger.chartOfAccountSystemID,
	"" as documentNarration,
	erp_generalledger.documentTransCurrencyID,
	currTrans.CurrencyCode as documentTransCurrency,
	currTrans.DecimalPlaces as documentTransDecimalPlaces,
	SUM(erp_generalledger.documentTransAmount) as documentTransAmount,
	erp_generalledger.documentLocalCurrencyID,
	currLocal.CurrencyCode as documentLocalCurrency,
	currLocal.DecimalPlaces as documentLocalDecimalPlaces,
	SUM(erp_generalledger.documentLocalAmount) as documentLocalAmount,
	erp_generalledger.documentRptCurrencyID,
	currRpt.CurrencyCode as documentRptCurrency,
	currRpt.DecimalPlaces as documentRptDecimalPlaces,
	SUM(erp_generalledger.documentRptAmount) as documentRptAmount,
	erp_generalledger.documentType,
	CONCAT(customermaster.CutomerCode," - ",customermaster.CustomerName) as concatCustomerName
FROM
	erp_generalledger
	INNER JOIN customermaster ON customermaster.customerCodeSystem=erp_generalledger.supplierCodeSystem
	LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
	LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
	LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
WHERE
	(erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21")
	AND DATE( erp_generalledger.documentDate) < "' . $fromDate . '"
	AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ')
	AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
	AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
	GROUP BY erp_generalledger.supplierCodeSystem) AS CustomerBalanceSummary_Detail ORDER BY CustomerBalanceSummary_Detail.documentDate ASC');
        //dd(DB::getQueryLog());
        return $output;
    }

    function getCustomerBalanceSummery($request)
    {
        $asOfDate = new Carbon($request->fromDate);
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
       // DB::enableQueryLog();
        $output = \DB::select('SELECT
                    CustomerBalanceSummary_Detail.companySystemID,
                    CustomerBalanceSummary_Detail.companyID,
                    CustomerBalanceSummary_Detail.supplierCodeSystem,
                    CustomerBalanceSummary_Detail.CutomerCode,
                    CustomerBalanceSummary_Detail.CustomerName,
                    CustomerBalanceSummary_Detail.documentLocalCurrencyID,
                    sum(CustomerBalanceSummary_Detail.documentLocalAmount) as localAmount,
                    CustomerBalanceSummary_Detail.documentRptCurrencyID,
                    sum(CustomerBalanceSummary_Detail.documentRptAmount) as RptAmount,
                    CustomerBalanceSummary_Detail.documentLocalCurrency,
                    CustomerBalanceSummary_Detail.documentRptCurrency
                FROM
                (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    erp_generalledger.glCode,
                    erp_generalledger.supplierCodeSystem,
                    customermaster.CutomerCode,
                    customermaster.CustomerName,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentLocalAmount,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentRptAmount,
                    currLocal.CurrencyCode as documentLocalCurrency,
                    currRpt.CurrencyCode as documentRptCurrency
                FROM
                    erp_generalledger
                    INNER JOIN customermaster ON customermaster.customerCodeSystem=erp_generalledger.supplierCodeSystem
                    LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                    LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                WHERE
                    (erp_generalledger.documentSystemID = "20" OR erp_generalledger.documentSystemID = "19" OR erp_generalledger.documentSystemID = "21")
                    AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ')
      
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
		            AND DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
		            AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . '))
                    AS CustomerBalanceSummary_Detail
                    GROUP BY CustomerBalanceSummary_Detail.companySystemID,CustomerBalanceSummary_Detail.supplierCodeSystem;');

        //dd(DB::getQueryLog());
        return $output;
    }

    function getCustomerRevenueMonthlySummary($request)
    {
        $asOfDate = new Carbon($request->fromDate);
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
        $year = $request->year;

        $currencyClm = "MyRptAmount";

        if($currency == 2){
            $currencyClm = "MyLocalAmount";
        }else if($currency == 3){
            $currencyClm = "MyRptAmount";
        }

        //DB::enableQueryLog();
        $output = \DB::select('SELECT
                    revenueDataSummary.companyID,
                    revenueDataSummary.CutomerCode,
                    revenueDataSummary.CustomerName,
                    revenueDataSummary.DocYEAR,
                    documentLocalCurrencyID,
                    documentRptCurrencyID,
                    sum(Jan) as Jan,
                    sum(Feb) as Feb,
                    sum(March) as March,
                    sum(April) as April,
                    sum(May) as May,
                    sum(June) as June,
                    sum(July) as July,
                    sum(Aug) as Aug,
                    sum(Sept) as Sept,
                    sum(Oct) as Oct,
                    sum(Nov) as Nov,
                    sum(Dece) as Dece,
                    sum(Total) as Total
                FROM
                (
                SELECT
                    revenueDetailData.documentLocalCurrencyID,
                    revenueDetailData.documentRptCurrencyID,
                    revenueDetailData.companySystemID,
                    revenueDetailData.companyID,
                    revenueDetailData.mySupplierCode,
                    customermaster.CutomerCode,
                    customermaster.CustomerName,
                    revenueDetailData.DocYEAR,
                IF
                    ( revenueDetailData.DocMONTH = 1, '.$currencyClm.', 0 ) AS Jan,
                IF
                    ( revenueDetailData.DocMONTH = 2, '.$currencyClm.', 0 ) AS Feb,
                IF
                    ( revenueDetailData.DocMONTH = 3, '.$currencyClm.', 0 ) AS March,
                IF
                    ( revenueDetailData.DocMONTH = 4, '.$currencyClm.', 0 ) AS April,
                IF
                    ( revenueDetailData.DocMONTH = 5, '.$currencyClm.', 0 ) AS May,
                IF
                    ( revenueDetailData.DocMONTH = 6, '.$currencyClm.', 0 ) AS June,
                IF
                    ( revenueDetailData.DocMONTH = 7, '.$currencyClm.', 0 ) AS July,
                IF
                    ( revenueDetailData.DocMONTH = 8, '.$currencyClm.', 0 ) AS Aug,
                IF
                    ( revenueDetailData.DocMONTH = 9, '.$currencyClm.', 0 ) AS Sept,
                IF
                    ( revenueDetailData.DocMONTH = 10, '.$currencyClm.', 0 ) AS Oct,
                IF
                    ( revenueDetailData.DocMONTH = 11, '.$currencyClm.', 0 ) AS Nov,
                IF
                    ( revenueDetailData.DocMONTH = 12, '.$currencyClm.', 0 ) AS Dece,
                    MyRptAmount as Total
                FROM
                    (
                SELECT
                    erp_generalledger.companySystemID,
                    erp_generalledger.companyID,
                    companymaster.CompanyName,
                    erp_generalledger.serviceLineSystemID,
                    erp_generalledger.serviceLineCode,
                    erp_generalledger.clientContractID,
                    contractmaster.ContractNumber,
                    erp_generalledger.documentID,
                    erp_generalledger.documentSystemCode,
                    erp_generalledger.documentCode,
                    erp_generalledger.documentDate,
                    MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                    YEAR ( erp_generalledger.documentDate ) AS DocYEAR,
                    erp_generalledger.documentNarration,
                    erp_generalledger.chartOfAccountSystemID,
                    erp_generalledger.glCode,
                    erp_generalledger.glAccountType,
                    chartofaccounts.controlAccounts,
                    revenueGLCodes.controlAccountID,
                    erp_generalledger.supplierCodeSystem,
                IF
                    (
                    erp_generalledger.clientContractID = "X" 
                    AND erp_generalledger.supplierCodeSystem = 0,
                    0,
                IF
                    (
                    erp_generalledger.clientContractID <> "X" 
                    AND erp_generalledger.supplierCodeSystem = 0,
                    contractmaster.clientID,
                IF
                    ( erp_generalledger.documentID = "SI" OR erp_generalledger.documentID = "DN" OR erp_generalledger.documentID = "PV", contractmaster.clientID, erp_generalledger.supplierCodeSystem ) 
                    ) 
                    ) AS mySupplierCode,
                    erp_generalledger.documentLocalCurrencyID,
                    erp_generalledger.documentRptCurrencyID,
                    erp_generalledger.documentLocalAmount,
                    erp_generalledger.documentLocalAmount *- 1 AS MyLocalAmount,
                    erp_generalledger.documentRptAmount,
                    erp_generalledger.documentRptAmount *- 1 AS MyRptAmount 
                FROM
                    erp_generalledger
                    INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                    LEFT JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    LEFT JOIN contractmaster ON erp_generalledger.clientContractID = contractmaster.ContractNumber 
                    AND erp_generalledger.companyID = contractmaster.CompanyID
                    INNER JOIN (
                SELECT
                    erp_templatesdetails.templatesDetailsAutoID,
                    erp_templatesdetails.templatesMasterAutoID,
                    erp_templatesdetails.templateDetailDescription,
                    erp_templatesdetails.controlAccountID,
                    erp_templatesdetails.controlAccountSubID,
                    erp_templatesglcode.chartOfAccountSystemID,
                    erp_templatesglcode.glCode 
                FROM
                    erp_templatesdetails
                    INNER JOIN erp_templatesglcode ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID 
                WHERE
                    ( ( ( erp_templatesdetails.templatesMasterAutoID ) = 15 ) AND ( ( erp_templatesdetails.controlAccountID ) = "PLI" ) ) 
                    ) AS revenueGLCodes ON erp_generalledger.chartOfAccountSystemID = revenueGLCodes.chartOfAccountSystemID 
                WHERE
                    DATE(erp_generalledger.documentDate) <= "' . $asOfDate . '"
                    AND YEAR ( erp_generalledger.documentDate ) = "' . $year . '"
                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
		          
                    ) AS revenueDetailData
                    LEFT JOIN customermaster ON customermaster.customerCodeSystem = revenueDetailData.mySupplierCode
                    WHERE revenueDetailData.mySupplierCode IN (' . join(',', $customerSystemID) . ')
                    ) AS revenueDataSummary
                    GROUP BY
                    revenueDataSummary.companySystemID,
                    revenueDataSummary.mySupplierCode');


         // DB::getQueryLog();

        return $output;
    }

    // Customer Collection Monthly report
    function getCustomerCollectionMonthlyQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $fromYear = $request->year;

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;
        $servicelines = (array)$request->servicelines;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();
        $serviceLineSystemID = collect($servicelines)->pluck('serviceLineSystemID')->toArray();

        $currency = $request->currencyID;

        if ($currency == 2) {
            $currencyDocAmount = "IF (erp_generalledger.documentSystemID = '21',documentLocalAmount,0) AS BRVDocumentAmount";

        } else if ($currency == 3) {
            $currencyDocAmount = "IF (erp_generalledger.documentSystemID = '21',documentRptAmount,0) AS BRVDocumentAmount";
        }

        $output = \DB::select('SELECT
	collectionMonthWise.companyID CutomerCode,
	CustomerName,
	DocYEAR,
	sum(Jan) AS Jan,
	sum(Feb) AS Feb,
	sum(March) AS March,
	sum(April) AS April,
	sum(May) AS May,
	sum(June) AS June,
	sum(July) AS July,
	sum(Aug) AS Aug,
	sum(Sept) AS Sept,
	sum(Oct) AS Oct,
	sum(Nov) AS Nov,
	sum(Dece) AS Dece
FROM
	(
		SELECT
			collectionDetail.companyID,
			collectionDetail.CutomerCode,
			collectionDetail.CustomerName,
			collectionDetail.DocYEAR,

		IF (
			collectionDetail.DocMONTH = 1,
			BRVDocumentAmount,
			0
		) AS Jan,

	IF (
		collectionDetail.DocMONTH = 2,
		BRVDocumentAmount,
		0
	) AS Feb,

IF (
	collectionDetail.DocMONTH = 3,
	BRVDocumentAmount,
	0
) AS March,

IF (
	collectionDetail.DocMONTH = 4,
	BRVDocumentAmount,
	0
) AS April,

IF (
	collectionDetail.DocMONTH = 5,
	BRVDocumentAmount,
	0
) AS May,

IF (
	collectionDetail.DocMONTH = 6,
	BRVDocumentAmount,
	0
) AS June,

IF (
	collectionDetail.DocMONTH = 7,
	BRVDocumentAmount,
	0
) AS July,

IF (
	collectionDetail.DocMONTH = 8,
	BRVDocumentAmount,
	0
) AS Aug,

IF (
	collectionDetail.DocMONTH = 9,
	BRVDocumentAmount,
	0
) AS Sept,

IF (
	collectionDetail.DocMONTH = 10,
	BRVDocumentAmount,
	0
) AS Oct,

IF (
	collectionDetail.DocMONTH = 11,
	BRVDocumentAmount,
	0
) AS Nov,

IF (
	collectionDetail.DocMONTH = 12,
	BRVDocumentAmount,
	0
) AS Dece
FROM
	(
		SELECT
			erp_generalledger.companyID,
			erp_generalledger.documentID,
			erp_generalledger.serviceLineCode,
			erp_generalledger.documentSystemCode,
			erp_generalledger.documentCode,
			erp_generalledger.documentDate,
			MONTH (
				erp_generalledger.documentDate
			) AS DocMONTH,
			YEAR (
				erp_generalledger.documentDate
			) AS DocYEAR,
			erp_generalledger.supplierCodeSystem,
			customermaster.CutomerCode,
			customermaster.customerShortCode,
			customermaster.CustomerName,
			' . $currencyDocAmount . '
FROM
	erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
WHERE
	erp_generalledger.documentSystemID = 21
AND DATE(erp_generalledger.documentDate) <= "' . $fromDate . '"
AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineSystemID) . ')
AND erp_generalledger.documentRptAmount > 0
AND YEAR (
	erp_generalledger.documentDate
) = ' . $fromYear . '
	) AS collectionDetail
	) AS collectionMonthWise
GROUP BY
	collectionMonthWise.companyID,
	collectionMonthWise.CutomerCode,
	collectionMonthWise.DocYEAR;');

        return $output;

    }

    // Customer Collection report
    function getCustomerCollectionCNExcelQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        if ($currency == 2) {
            $currencyBRVAmount = " collectionDetail.BRVLocalAmount AS BRVDocumentAmount";
            $currencyCNAmount = " collectionDetail.CNLocalAmount AS CNDocumentAmount";
        } else if ($currency == 3) {
            $currencyBRVAmount = " collectionDetail.BRVRptAmount AS BRVDocumentAmount";
            $currencyCNAmount = " collectionDetail.CNRptAmount AS CNDocumentAmount";
        }

        $output = \DB::select('SELECT
	collectionDetail.companyID,
	collectionDetail.CompanyName,
	collectionDetail.CutomerCode,
	collectionDetail.customerShortCode,
	collectionDetail.CustomerName,
	collectionDetail.documentCode,
	collectionDetail.documentDate,
	collectionDetail.documentNarration,
	' . $currencyCNAmount . '
FROM
	(
		SELECT
			erp_generalledger.companyID,
			erp_generalledger.documentID,
			erp_generalledger.serviceLineCode,
			erp_generalledger.documentSystemCode,
			erp_generalledger.documentCode,
			erp_generalledger.documentDate,
			erp_generalledger.documentNarration,
			companymaster.CompanyName,
			MONTH (
				erp_generalledger.documentDate
			) AS DocMONTH,
			YEAR (
				erp_generalledger.documentDate
			) AS DocYEAR,
			erp_generalledger.supplierCodeSystem,
			customermaster.CutomerCode,
			customermaster.customerShortCode,
			customermaster.CustomerName,
IF (
	erp_generalledger.documentSystemID = "19",
	ROUND(documentLocalAmount, 0),
	0
) CNLocalAmount,

IF (
	erp_generalledger.documentSystemID = "19",
	ROUND(documentRptAmount, 0),
	0
) CNRptAmount
FROM
	erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
WHERE erp_generalledger.documentSystemID = 19
 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0 ORDER BY erp_generalledger.documentDate ASC
	) AS collectionDetail');

        return $output;

    }


    // Customer Collection report
    function getCustomerCollectionBRVExcelQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        if ($currency == 2) {
            $currencyBRVAmount = " collectionDetail.BRVLocalAmount AS BRVDocumentAmount";
        } else if ($currency == 3) {
            $currencyBRVAmount = " collectionDetail.BRVRptAmount AS BRVDocumentAmount";
        }

        $output = \DB::select('SELECT
	collectionDetail.companyID,
	collectionDetail.CompanyName,
	collectionDetail.CutomerCode,
	collectionDetail.customerShortCode,
	collectionDetail.CustomerName,
	collectionDetail.documentCode,
	collectionDetail.documentDate,
	collectionDetail.documentNarration,
	collectionDetail.bankName,
	collectionDetail.AccountNo,
	collectionDetail.CurrencyCode AS bankCurrencyCode,
	' . $currencyBRVAmount . '
FROM
	(
		SELECT
			erp_generalledger.companyID,
			erp_generalledger.documentID,
			erp_generalledger.serviceLineCode,
			erp_generalledger.documentSystemCode,
			erp_generalledger.documentCode,
			erp_generalledger.documentDate,
			erp_generalledger.documentNarration,
			companymaster.CompanyName,
			MONTH (
				erp_generalledger.documentDate
			) AS DocMONTH,
			YEAR (
				erp_generalledger.documentDate
			) AS DocYEAR,
			erp_generalledger.supplierCodeSystem,
			customermaster.CutomerCode,
			customermaster.customerShortCode,
			customermaster.CustomerName,
			erp_bankmaster.bankName,
			erp_bankaccount.AccountNo,
			currencymaster.CurrencyCode,
	IF (
		erp_generalledger.documentSystemID = "21",
		ROUND(documentLocalAmount, 0),
		0
	) BRVLocalAmount,

IF (
	erp_generalledger.documentSystemID = "21",
	ROUND(documentRptAmount, 0),
	0
) BRVRptAmount
FROM
	erp_generalledger
INNER JOIN customermaster ON erp_generalledger.supplierCodeSystem = customermaster.customerCodeSystem
INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
INNER JOIN erp_customerreceivepayment ON erp_generalledger.documentSystemCode = erp_customerreceivepayment.custReceivePaymentAutoID AND erp_generalledger.companySystemID = erp_customerreceivepayment.companySystemID AND erp_generalledger.documentSystemID = erp_customerreceivepayment.documentSystemID
INNER JOIN erp_bankmaster ON erp_customerreceivepayment.bankID = erp_bankmaster.bankmasterAutoID
INNER JOIN erp_bankaccount ON erp_customerreceivepayment.bankAccount = erp_bankaccount.bankAccountAutoID
INNER JOIN currencymaster ON erp_bankaccount.accountCurrencyID = currencymaster.currencyID
WHERE erp_generalledger.documentSystemID = 21 AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
AND erp_generalledger.documentRptAmount > 0 ORDER BY erp_generalledger.documentDate ASC
	) AS collectionDetail');

        return $output;

    }


  // Revenue By Customer
                            function getRevenueByCustomer($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $customers = (array)$request->customers;

        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $currency = $request->currencyID;

        $output = \DB::select('SELECT
                                revenueCustomerDetail.companySystemID,
                                revenueCustomerDetail.companyID,
                                revenueCustomerDetail.CompanyName,
                                customermaster.CutomerCode,
                                customermaster.CustomerName,
                                revenueCustomerDetail.documentCode,
                                revenueCustomerDetail.serviceLineCode,
                                revenueCustomerDetail.ContractNumber,
                                revenueCustomerDetail.contractDescription,
                                revenueCustomerDetail.CONTRACT_PO,
                                revenueCustomerDetail.ContEndDate,
                                revenueCustomerDetail.glCode,
                                revenueCustomerDetail.AccountDescription,
                                revenueCustomerDetail.documentDate,
                                documentLocalCurrency,
                                documentLocalDecimalPlaces,
                                documentRptCurrency,
                                documentRptDecimalPlaces,
                                month(revenueCustomerDetail.documentDate) as PostingMonth,
                                year(revenueCustomerDetail.documentDate) as PostingYear,
                                revenueCustomerDetail.documentNarration,
                                round(revenueCustomerDetail.MyLocalAmount,0) localAmount,
                                round(revenueCustomerDetail.MyRptAmount,0) RptAmount
                            FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.serviceLineCode,
                                erp_generalledger.clientContractID,
                                contractmaster.ContractNumber,
                                contractmaster.contractDescription,
                                contractmaster.ContEndDate,
                                erp_generalledger.documentID,
                                erp_generalledger.documentSystemCode,
                                erp_generalledger.documentCode,
                                erp_generalledger.documentDate,
                                erp_generalledger.documentNarration,
                                erp_generalledger.glCode,
                                erp_generalledger.glAccountType,
                                chartofaccounts.controlAccounts,
                                chartofaccounts.AccountDescription,
                                erp_generalledger.supplierCodeSystem,
                                currLocal.CurrencyCode as documentLocalCurrency,
                                currLocal.DecimalPlaces as documentLocalDecimalPlaces,
                                currRpt.CurrencyCode as documentRptCurrency,
                                currRpt.DecimalPlaces as documentRptDecimalPlaces,
                            IF
                                (
                                erp_generalledger.clientContractID = "X" 
                                AND erp_generalledger.supplierCodeSystem = 0,
                                0,
                            IF
                                (
                                erp_generalledger.clientContractID <> "X" 
                                AND erp_generalledger.supplierCodeSystem = 0,
                                contractmaster.clientID,
                            IF
                                ( erp_generalledger.documentID = "SI" OR erp_generalledger.documentID = "DN" OR erp_generalledger.documentID = "PV", contractmaster.clientID, erp_generalledger.supplierCodeSystem ) 
                                ) 
                                ) AS mySupplierCode,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalAmount,
                                (documentLocalAmount * -1) AS MyLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptAmount,
                                (documentRptAmount * -1) AS MyRptAmount,
                            IF
                                ( contractmaster.isContract = 1, "Contract", "PO" ) AS CONTRACT_PO 
                            FROM
                                erp_generalledger
                                INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                                LEFT JOIN contractmaster ON erp_generalledger.companyID = contractmaster.CompanyID 
                                AND erp_generalledger.clientContractID = contractmaster.ContractNumber
                                LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                                LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                                INNER JOIN (
                            SELECT
                                erp_templatesdetails.templatesDetailsAutoID,
                                erp_templatesdetails.templatesMasterAutoID,
                                erp_templatesdetails.templateDetailDescription,
                                erp_templatesdetails.controlAccountID,
                                erp_templatesdetails.controlAccountSubID,
                                erp_templatesglcode.chartOfAccountSystemID,
                                erp_templatesglcode.glCode 
                            FROM
                                erp_templatesdetails
                                INNER JOIN erp_templatesglcode ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID 
                            WHERE
                                erp_templatesdetails.templatesMasterAutoID = 15 AND erp_templatesdetails.controlAccountID = "PLI"
                                ) AS revenueGLCodes ON erp_generalledger.chartOfAccountSystemID = revenueGLCodes.chartOfAccountSystemID
                                WHERE erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                            
                                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" 
                                AND "' . $toDate . '"
                                ) AS revenueCustomerDetail
                                LEFT JOIN customermaster ON revenueCustomerDetail.mySupplierCode = customermaster.customerCodeSystem
                                WHERE revenueCustomerDetail.mySupplierCode IN (' . join(',', $customerSystemID) . ')');

        return $output;
    }

    function getCustomerSalesRegisterQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $fromDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $controlAccountsSystemID = $request->controlAccountsSystemID;
        $currency = $request->currencyID;

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $balanceAmountQry = '';
        $receiptAmountQry = '';
        $decimalPlaceQry = '';
        $invoiceAmountQry = '';
        $currencyQry = '';
        if ($currency == 1) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentTransAmount,0),MainQuery.documentTransDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount,0),MainQuery.documentTransDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentTransAmount, 0 ),MainQuery.documentTransDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentTransDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentTransCurrency AS documentCurrency";
        } else if ($currency == 2) {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount,0),MainQuery.documentLocalDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount,0),MainQuery.documentLocalDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentLocalAmount, 0 ),MainQuery.documentLocalDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentLocalDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentLocalCurrency AS documentCurrency";
        } else {
            $balanceAmountQry = "round(IFNULL(MainQuery.documentRptAmount,0),MainQuery.documentRptDecimalPlaces) - round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount,0),MainQuery.documentRptDecimalPlaces) AS balanceAmount";
            $receiptAmountQry = "round(IFNULL(InvoiceFromBRVAndMatching.InvoiceRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS receiptAmount";
            $invoiceAmountQry = "round(IFNULL(MainQuery.documentRptAmount, 0 ),MainQuery.documentRptDecimalPlaces) AS invoiceAmount";
            $decimalPlaceQry = "MainQuery.documentRptDecimalPlaces AS balanceDecimalPlaces";
            $currencyQry = "MainQuery.documentRptCurrency AS documentCurrency";
        }
        //DB::enableQueryLog();
        $output = \DB::select('SELECT
                MainQuery.companyID,
                MainQuery.documentCode,
                MainQuery.documentDate AS PostedDate,
                MainQuery.clientContractID,
                MainQuery.invoiceDate,
                MainQuery.documentNarration,
                InvoiceFromBRVAndMatching.ReceiptCode,
                InvoiceFromBRVAndMatching.ReceiptDate,
            ' . $balanceAmountQry . ',
                ' . $receiptAmountQry . ',
                ' . $invoiceAmountQry . ',
                ' . $currencyQry . ',
                ' . $decimalPlaceQry . ',
                MainQuery.customerName,
                MainQuery.PONumber,
                MainQuery.serviceLineCode,
                MainQuery.rigNo,
                MainQuery.servicePeriod,
                MainQuery.serviceStartDate,
                MainQuery.serviceEndDate,
                MainQuery.wanNO,
                MainQuery.invoiceNumber,
                MainQuery.invoiceDate
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
                DATE_FORMAT( erp_generalledger.documentDate, "%d/%m/%Y" ) AS documentDateFilter,
                erp_generalledger.invoiceNumber,
                erp_generalledger.invoiceDate,
                erp_generalledger.chartOfAccountSystemID,
                erp_generalledger.glCode,
                erp_generalledger.documentNarration,
                erp_generalledger.clientContractID,
                erp_generalledger.supplierCodeSystem,
                erp_generalledger.documentTransCurrencyID,
                erp_generalledger.documentTransAmount,
                erp_generalledger.documentLocalCurrencyID,
                erp_generalledger.documentLocalAmount,
                erp_generalledger.documentRptCurrencyID,
                erp_generalledger.documentRptAmount,
                currLocal.DecimalPlaces AS documentLocalDecimalPlaces,
                currRpt.DecimalPlaces AS documentRptDecimalPlaces,
                currTrans.DecimalPlaces AS documentTransDecimalPlaces,
                currRpt.CurrencyCode AS documentRptCurrency,
                currLocal.CurrencyCode AS documentLocalCurrency,
                currTrans.CurrencyCode AS documentTransCurrency,
                erp_custinvoicedirect.PONumber,
                erp_custinvoicedirect.rigNo,
                erp_custinvoicedirect.servicePeriod,
                erp_custinvoicedirect.serviceStartDate,
                erp_custinvoicedirect.serviceEndDate,
                erp_custinvoicedirect.wanNO,
                CONCAT( customermaster.CutomerCode, " - ", customermaster.CustomerName ) AS customerName 
            FROM
                erp_generalledger
                INNER JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                LEFT JOIN currencymaster currTrans ON erp_generalledger.documentTransCurrencyID = currTrans.currencyID
                LEFT JOIN currencymaster currLocal ON erp_generalledger.documentLocalCurrencyID = currLocal.currencyID
                LEFT JOIN currencymaster currRpt ON erp_generalledger.documentRptCurrencyID = currRpt.currencyID
                LEFT JOIN erp_custinvoicedirect ON erp_generalledger.documentSystemCode = erp_custinvoicedirect.custInvoiceDirectAutoID AND erp_generalledger.documentSystemID = erp_custinvoicedirect.documentSystemiD AND erp_generalledger.companySystemID = erp_custinvoicedirect.companySystemID
            WHERE
                erp_generalledger.documentSystemID = 20 
                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '"
                AND "' . $toDate . '"
                AND ( erp_generalledger.chartOfAccountSystemID = ' . $controlAccountsSystemID . ')
                AND erp_generalledger.supplierCodeSystem IN (' . join(',', $customerSystemID) . ')
                ) AS MainQuery
                LEFT JOIN (
            SELECT
                InvoiceFromUNION.companySystemID,
                InvoiceFromUNION.companyID,
                max( InvoiceFromUNION.custPaymentReceiveCode ) AS ReceiptCode,
                max( InvoiceFromUNION.postedDate ) AS ReceiptDate,
                InvoiceFromUNION.addedDocumentSystemID,
                InvoiceFromUNION.addedDocumentID,
                InvoiceFromUNION.bookingInvCodeSystem,
                InvoiceFromUNION.bookingInvCode,
                sum( InvoiceFromUNION.receiveAmountTrans ) AS InvoiceTransAmount,
                sum( InvoiceFromUNION.receiveAmountLocal ) AS InvoiceLocalAmount,
                sum( InvoiceFromUNION.receiveAmountRpt ) AS InvoiceRptAmount 
            FROM
                (
            SELECT
                * 
            FROM
                (
            SELECT
                erp_customerreceivepayment.custPaymentReceiveCode,
                erp_customerreceivepayment.postedDate,
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
                AND erp_custreceivepaymentdet.companySystemID IN (' . join(',', $companyID) . ')
                            /*AND DATE(erp_customerreceivepayment.postedDate) <= "' . $toDate . '"*/
                ) AS InvoiceFromBRV UNION ALL
            SELECT
                * 
            FROM
                (
            SELECT
                erp_matchdocumentmaster.matchingDocCode,
                erp_matchdocumentmaster.matchingDocdate,
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
                AND erp_custreceivepaymentdet.companySystemID  IN (' . join(',', $companyID) . ')
                            /*AND DATE(erp_matchdocumentmaster.matchingDocdate) <= "' . $toDate . '"*/
                ) AS InvoiceFromMatching 
                ) AS InvoiceFromUNION 
            GROUP BY
                bookingInvCode 
                ) AS InvoiceFromBRVAndMatching ON InvoiceFromBRVAndMatching.addedDocumentSystemID = mainQuery.documentSystemID 
                AND mainQuery.documentSystemCode = InvoiceFromBRVAndMatching.bookingInvCodeSystem ORDER BY postedDate ASC;');
        //dd(DB::getQueryLog());
        return $output;
    }
}

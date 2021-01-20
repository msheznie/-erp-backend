<?php
/**
 * =============================================
 * -- File Name : AccountsReceivableReportAPIControllerroller.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 9 - April 2018
 * -- Description : This file contains the all the report generation code
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
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryCollectionQRY()
 * -- Date: 11-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryOutstandingQRY()
 * -- Date: 11-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueServiceLineBaseQRY()
 * -- Date: 13-February 2019 By: Nazir Description: Added new functions named as getCustomerSummaryOutstandingUpdatedQRY()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\FreeBillingMasterPerforma;
use App\Models\QuotationMaster;
use App\Models\QuotationStatus;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesMarketingReportAPIController extends AppBaseController
{
    /*validate each report*/
    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'qso':
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'customers' => 'required'
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

            case 'qso':
                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }
                
                $search = $request->input('search.value');

                $convertedRequest = (object)$this->convertArrayToSelectedValue($request->all(), array('approved_status','invoice_status','delivery_status'));
                $checkIsGroup = Company::find($convertedRequest->companySystemID);
                $output = $this->getQSOQRY($convertedRequest, $search);

                $outputArr = array();
                $invoiceAmount = collect($output)->pluck('invoice_amount')->toArray();
                $invoiceAmount = array_sum($invoiceAmount);

                $paidAmount = collect($output)->pluck('paid_amount')->toArray();
                $paidAmount = array_sum($paidAmount);

                $document_amount = collect($output)->pluck('document_amount')->toArray();
                $document_amount = array_sum($document_amount);

                $decimalPlace = collect($output)->pluck('dp')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                $request->request->remove('order');
                $data['order'] = [];
                $data['search']['value'] = '';
                $request->merge($data);
                $request->request->remove('search.value');
                
                return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    // $query->orderBy('quiz_usermaster.id', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->with('companyName', $checkIsGroup->CompanyName)
                        ->with('document_amount', $document_amount)
                        ->with('paidAmount', $paidAmount)
                        ->with('invoiceAmount', $invoiceAmount)
                        ->with('currencyDecimalPlace', !empty($decimalPlace) ? $decimalPlace[0] : 2)
                        ->make(true);
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function exportReport(Request $request)
    {

        $input = $request->all();
        $reportID = $request->reportID;
        $type = $request->type;
        switch ($reportID) {

            case 'qso':

                $convertedRequest = (object)$this->convertArrayToSelectedValue($request->all(), array('approved_status','invoice_status','delivery_status'));
                $checkIsGroup = Company::find($convertedRequest->companySystemID);
                $output = $this->getQSOQRY($convertedRequest);

                $outputArr = array();
                $invoiceAmount = collect($output)->pluck('invoice_amount')->toArray();
                $invoiceAmount = array_sum($invoiceAmount);

                $paidAmount = collect($output)->pluck('paid_amount')->toArray();
                $paidAmount = array_sum($paidAmount);

                $document_amount = collect($output)->pluck('document_amount')->toArray();
                $document_amount = array_sum($document_amount);

                $decimalPlace = collect($output)->pluck('dp')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                if ($output) {
                    foreach ($output as $val) {
                        // doc status
                        $doc_status = '';
                         if ($val['confirmedYN'] == 0 && $val['approvedYN'] == 0) {
                             $doc_status = "Not Confirmed";
                        }
                        else if ($val['confirmedYN'] == 1 && $val['approvedYN'] == 0 && $val['refferedBackYN'] == 0) {
                            $doc_status = "Pending Approval";
                        } else if ($val['confirmedYN'] == 1 && $val['approvedYN'] == 0 && $val['refferedBackYN'] == -1) {
                            $doc_status = "Referred Back";
                        }
                        else if ($val['confirmedYN'] == 1 && ($val['approvedYN'] == -1 || $val['refferedBackYN'] == 1 )) {
                            $doc_status = "Fully Approved";
                        }

                        //deliveryStatus
                        $delivery_status = '';
                        if ($val['deliveryStatus'] == 0) {
                            $delivery_status = 'Not Delivered';
                        } else if ($val['deliveryStatus'] == 1) {
                            $delivery_status = 'Partially Delivered';
                        } else if ($val['deliveryStatus'] == 2) {
                            $delivery_status = 'Fully Delivered';
                        }

                        $dp = (isset($val['dp']) && $val['dp'])?$val['dp']:3;


                        $data[] = array(
                            'Document Code' => $val['quotationCode'],
                            'Document Date' => Helper::dateFormat($val['documentDate']),
                            'Segment' => $val['serviceLine'],
                            'Ref No' => $val['referenceNo'],
                            'Customer' => $val['customer'],
                            'Currency' => $val['currency'],
                            'Expire Date' => Helper::dateFormat($val['documentExpDate']),
                            'Document Status' => $doc_status,
                            'Customer Status' => $val['customer_status'],
                            'Document Amount' => round($val['document_amount'], $dp),
                            'Delivery Status' => $delivery_status,
                            'Invoice Amount' => round($val['invoice_amount'], $dp),
                            'Paid Amount' => round($val['paid_amount'], $dp)
                        );
                    }


                }

                \Excel::create('quotation_so_report', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');

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

                    $companyLogo = $checkIsGroup->logo_url;

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

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'balanceAmount' => $balanceTotal, 'receiptAmount' => $receiptAmount, 'invoiceAmount' => $invoiceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'customerName' => $customerName->customerShortCode . ' - ' . $customerName->CustomerName, 'reportDate' => date('d/m/Y H:i:s A'), 'currency' => 'Currency: ' . $currencyCode, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'currencyID' => $request->currencyID);

                    $html = view('print.customer_statement_of_account_pdf', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                } elseif ($request->reportTypeID == 'CBS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

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

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'grandTotal' => $grandTotal, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.customer_balance_statement', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CR':
                if ($request->reportTypeID == 'RMS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $currency = $request->currencyID;
                    $currencyId = 2;

                    if ($currency == 2) {
                        $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    } else {
                        $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }

                    if (!empty($decimalPlaceUnique)) {
                        $currencyId = $decimalPlaceUnique[0];
                    }


                    $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

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


                    $outputArr = array();
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][] = $val;
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlace, 'total' => $total, 'currency' => $requestCurrency->CurrencyCode, 'year' => $request->year, 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.revenue_monthly_summary', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CA':
                if ($request->reportTypeID == 'CAS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingSummaryQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

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
                            $outputArr[$val->concatCompanyName][$val->documentCurrency][] = $val;
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

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $output['aging'], 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.customer_aging_summary', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();

                } elseif ($request->reportTypeID == 'CAD') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingDetailQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();
                    $customerCreditDays = array();
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
                            $customerCreditDays[$val->customerName] = $val->creditDays;
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

                    $dataArr = array('reportData' => (object)$outputArr, 'customerCreditDays' => $customerCreditDays, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'currencyDecimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $output['aging'], 'fromDate' => \Helper::dateFormat($request->fromDate), 'invoiceAmountTotal' => $invoiceAmountTotal);

                    $html = view('print.customer_aging_detail', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CC':
                if ($request->reportTypeID == 'CCR') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();

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

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CompanyName][$val->companyID][] = $val;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlaces' => $decimalPlaces, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'selectedCurrency' => $selectedCurrency, 'bankPaymentTotal' => $bankPaymentTotal, 'creditNoteTotal' => $creditNoteTotal);

                    $html = view('print.customer_collection', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function getSalesMarketFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

//        $departments = Helper::getCompanyServiceline($selectedCompanyId);
//
//        $departments[] = array("serviceLineSystemID" => 24, "ServiceLineCode" => 'X', "serviceLineMasterCode" => 'X', "ServiceLineDes" => 'X');

        $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)
            ->groupBy('customerCodeSystem')
            ->orderBy('CustomerName', 'ASC')
            ->WhereNotNull('customerCodeSystem')
            ->get();

        $output = array(
            'customers' => $customerMaster
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    function getQSOQRY($request, $search = "")
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $approved_status = isset($request->approved_status)?$request->approved_status:null;
        $invoice_status = isset($request->invoice_status)?$request->invoice_status:null;
        $delivery_status = isset($request->delivery_status)?$request->delivery_status:null;

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $details = QuotationMaster::whereIn('companySystemID',$companyID)
            ->whereIn('customerSystemCode',$customerSystemID)
                ->whereDate('createdDateTime', '>=', $fromDate)
                ->whereDate('createdDateTime', '<=', $toDate)
            ->where(function ($query) use($approved_status,$invoice_status,$delivery_status){

                if($approved_status != null){
                    if($approved_status == 1){
                        $query->where('confirmedYN',1);
                    }elseif ($approved_status == 2){
                        $query->where('approvedYN',0);
                    }elseif ($approved_status == 3){
                        $query->where('approvedYN',-1);
                    }
                }

                if($invoice_status != null){
                    if($invoice_status == 1){
                        $query->where('invoiceStatus',0);
                    }elseif ($invoice_status == 2){
                        $query->where('invoiceStatus',1);
                    }elseif ($invoice_status == 3){
                        $query->where('invoiceStatus',2);
                    }
                }

                if($delivery_status != null){
                    if($delivery_status == 1){
                        $query->where('deliveryStatus',0);
                    }elseif ($delivery_status == 2){
                        $query->where('deliveryStatus',1);
                    }elseif ($delivery_status == 3){
                        $query->where('deliveryStatus',2);
                    }
                }
            })
            ->with(['segment' => function($query){
                $query->select('serviceLineSystemID','ServiceLineCode','ServiceLineDes');
            },'detail'=> function($query){

                $query->with([
                    'invoice_detail' => function($q1){

                    $q1->with(['master'=> function($q2){

                        $q2->with(['receipt_detail' =>function($q3){
                            $q3->select('bookingInvCodeSystem','receiveAmountTrans');
                        }])
                            ->select('custInvoiceDirectAutoID');

                    }])
                    ->select('sellingTotal','customerItemDetailID','quotationDetailsID','custInvoiceDirectAutoID','custInvoiceDirectAutoID');

                },
                    'delivery_order_detail'=> function($q1){

                        $q1->with(['invoice_detail' => function($q2){

                            $q2->with(['master' => function($q3){

                                $q3->with(['receipt_detail' => function($q4){
                                    $q4->select('bookingInvCodeSystem','receiveAmountTrans');
                                }])
                                    ->select('custInvoiceDirectAutoID');
                            }])
                                ->select('sellingTotal','customerItemDetailID','quotationDetailsID','deliveryOrderDetailID','custInvoiceDirectAutoID');
                        }])
                            ->select('deliveryOrderDetailID','quotationDetailsID');

                    }
                ])
                    ->select('quotationDetailsID','quotationMasterID','transactionAmount');
            }])
            ->select('quotationMasterID','quotationCode','referenceNo','documentDate','serviceLineSystemID','customerName','transactionCurrency','transactionCurrencyDecimalPlaces','documentExpDate','confirmedYN','approvedYN','refferedBackYN','deliveryStatus','invoiceStatus','refferedBackYN','confirmedYN','approvedYN')
            ->get()
            ->toArray();

        $output = [];
        $x = 0;
        if(!empty($details) && $details != []){
            foreach ($details as $data){
                $output[$x]['quotationCode'] = isset($data['quotationCode'])?$data['quotationCode']:'';
                $output[$x]['documentDate'] = isset($data['documentDate'])?$data['documentDate']:'';
                $output[$x]['serviceLine'] = isset($data['segment']['ServiceLineDes'])?$data['segment']['ServiceLineDes']:'';
                $output[$x]['referenceNo'] = isset($data['referenceNo'])?$data['referenceNo']:'';
                $output[$x]['customer'] = isset($data['customerName'])?$data['customerName']:'';
                $output[$x]['currency'] = isset($data['transactionCurrency'])?$data['transactionCurrency']:'';
                $output[$x]['dp'] = isset($data['transactionCurrencyDecimalPlaces'])?$data['transactionCurrencyDecimalPlaces']:'';
                $output[$x]['documentExpDate'] = isset($data['documentExpDate'])?$data['documentExpDate']:'';
                $output[$x]['confirmedYN'] = isset($data['confirmedYN'])?$data['confirmedYN']:null;
                $output[$x]['approvedYN'] = isset($data['approvedYN'])?$data['approvedYN']:null;
                $output[$x]['refferedBackYN'] = isset($data['refferedBackYN'])?$data['refferedBackYN']:null;
                $output[$x]['customer_status'] = isset($data['quotationMasterID'])?QuotationStatus::getLastStatus($data['quotationMasterID']):'';
                $output[$x]['document_amount'] = 0;
                $output[$x]['invoice_amount'] = 0;
                $output[$x]['paid_amount'] = 0;
                $paid1 = 0;
                $paid2 = 0;
                $invoiceArray = [];
                if(isset($data['detail']) && count($data['detail'])> 0){
                    foreach ($data['detail'] as $qdetail){
                        $output[$x]['document_amount'] += isset($qdetail['transactionAmount'])?$qdetail['transactionAmount']:0;

                        // quotation -> delovery order -> invoice

                        if(isset($qdetail['delivery_order_detail']) && count($qdetail['delivery_order_detail'])> 0){

                            foreach ($qdetail['delivery_order_detail'] as $deliverydetail){

                                if(isset($deliverydetail['invoice_detail']) && count($deliverydetail['invoice_detail'])> 0){

                                    foreach ($deliverydetail['invoice_detail'] as $invoiceDetails){
                                        $invoiceArray[] = $invoiceDetails['custInvoiceDirectAutoID'];
                                        $output[$x]['invoice_amount'] += isset($invoiceDetails['sellingTotal'])?$invoiceDetails['sellingTotal']:0;

                                        if(isset($invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans']) && $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'] > 0){
                                            $paid1 = $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'];
                                        }

                                        /*$paymentsInvoice = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                            ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                            ->where('matchingDocID', 0)
                                            ->groupBy('custReceivePaymentAutoID')
                                            ->first();
                                        if(!empty($paymentsInvoice)){
                                            $output[$x]['paid_amount'] += $paymentsInvoice->receiveAmountTrans;
                                        }

                                        $paymentsInvoiceMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                            ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                            ->where('matchingDocID','>', 0)
                                            ->groupBy('custReceivePaymentAutoID')
                                            ->first();
                                        if(!empty($paymentsInvoiceMatch)){
                                            $output[$x]['paid_amount'] += $paymentsInvoiceMatch->receiveAmountTrans;
                                        }*/

                                    }
                                }

                            }
                        }

                        // quotation -> invoice
                        if(isset($qdetail['invoice_detail']) && count($qdetail['invoice_detail'])> 0){

                            foreach ($qdetail['invoice_detail'] as $invoiceDetails){
                                $invoiceArray[] = $invoiceDetails['custInvoiceDirectAutoID'];
                                $output[$x]['invoice_amount'] += isset($invoiceDetails['sellingTotal'])?$invoiceDetails['sellingTotal']:0;
                                if(isset($invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans']) && $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'] > 0){
                                    $paid2 = $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'];
                                }

                                /*$paymentsInvoice = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                    ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                    ->where('matchingDocID', 0)
                                    ->groupBy('custReceivePaymentAutoID')
                                    ->first();
                                if(!empty($paymentsInvoice)){
                                    $output[$x]['paid_amount'] += $paymentsInvoice->receiveAmountTrans;
                                }

                                $paymentsInvoiceMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                    ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                    ->where('matchingDocID','>', 0)
                                    ->groupBy('custReceivePaymentAutoID')
                                    ->first();
                                if(!empty($paymentsInvoiceMatch)){
                                    $output[$x]['paid_amount'] += $paymentsInvoiceMatch->receiveAmountTrans;
                                }*/

                            }
                        }

                    }
                }

                // get paid amount
                $invoiceArray = array_unique($invoiceArray);
                if(!empty($invoiceArray) && count($invoiceArray)>0){
                    foreach ($invoiceArray as $invoice){
                        if($invoice > 0){
                            $paymentsInvoice = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                ->where('bookingInvCodeSystem', $invoice)
                                ->where('matchingDocID', 0)
                                ->groupBy('custReceivePaymentAutoID')
                                ->first();
                            if(!empty($paymentsInvoice)){
                                $output[$x]['paid_amount'] += $paymentsInvoice->receiveAmountTrans;
                            }

                            $paymentsInvoiceMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                ->where('bookingInvCodeSystem', $invoice)
                                ->where('matchingDocID','>', 0)
                                ->groupBy('custReceivePaymentAutoID')
                                ->first();
                            if(!empty($paymentsInvoiceMatch)){
                                $output[$x]['paid_amount'] += $paymentsInvoiceMatch->receiveAmountTrans;
                            }
                        }

                    }
                }
                $output[$x]['deliveryStatus'] = isset($data['deliveryStatus'])?$data['deliveryStatus']:0;
                $x++;
            }
        }
        return $output;

    }

}

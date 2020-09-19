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
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS': //Customer Statement Report
                $reportTypeID = $request->reportTypeID;
                $data = array();
                $type = $request->type;
                if ($reportTypeID == 'CBS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    $outputArr = array();
                    $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $grandTotal = array_sum($grandTotal);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);
                    $decimalPlace = !empty($decimalPlace) ? $decimalPlace[0] : 2;

                    if ($output) {
                        foreach ($output as $val) {
                            $data[] = array(
                                'Company ID' => $val->companyID,
                                'Company Name' => $val->CompanyName,
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
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $x++;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
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

                 \Excel::create('customer_balance_statement', function ($excel) use ($data) {
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
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Document Code'] = $val->DocumentCode;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->PostedDate);
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->customerName2;
                            $data[$x]['Credit Days'] = $val->creditDays;
                            $data[$x]['Department'] = $val->serviceLineName;
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
                            $data[$x]['Current Outstanding'] = $val->subsequentBalanceAmount;
                            $data[$x]['Subsequent Collection Amount'] = $val->subsequentAmount;
                            $data[$x]['Receipt Matching/BRVNo'] = $val->brvInv;
                            $data[$x]['Collection Tracker Status'] = $val->commentAndStatus;
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
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Credit Days'] = $val->creditDays;
                            $data[$x]['Cust. Code'] = $val->CustomerCode;
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

                 \Excel::create('customer_aging', function ($excel) use ($data) {
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
            case 'CL': //Customer Ledger
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                if ($reportTypeID == 'CLT1') { //customer ledger template 1
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $output = $this->getCustomerLedgerTemplate1QRY($request);

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
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
                    $output = $this->getCustomerLedgerTemplate2QRY($request); //customer ledger template 2

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
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

                 \Excel::create('customer_ledger', function ($excel) use ($data) {
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

                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
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

                     \Excel::create('customer_balance_summary', function ($excel) use ($data) {
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
                        $data[$x]['Invoice Type'] = $val->invoiceType;
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Customer Code'] = $val->CutomerCode;
                        $data[$x]['Customer Name'] = $val->CustomerName;
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

                 \Excel::create('customer_sales_register', function ($excel) use ($data) {
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
                $data = [];
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
                            $data[$x]['Company ID'] = $val->companyCode;
                            $data[$x]['Company Name'] = $val->CompanyName;
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

                 \Excel::create('customer_collection', function ($excel) use ($data) {
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
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Contract No'] = $val->ContractNumber;
                            $data[$x]['Contract Description'] = $val->contractDescription;
                            $data[$x]['Contract/PO'] = $val->CONTRACT_PO;
                            $data[$x]['Contract End Date'] = \Helper::dateFormat($val->ContEndDate);
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['GL Desc'] = $val->AccountDescription;
                            $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Posting Month'] = Carbon::parse($val->documentDate)->shortEnglishMonth;
                            $data[$x]['Posting Year'] = $val->PostingYear;
                            $data[$x]['Narration'] = $val->documentNarration;

                            $decimalPlace = 0;
                            if ($currencyID == '2') {
                                $decimalPlace = 0; //!empty($localCurrency) ? $localCurrency->DecimalPlaces : 2;
                                $data[$x]['Currency'] = $val->documentLocalCurrency;
                                $data[$x]['Amount'] = round($val->localAmount, $decimalPlace);
                            } else if ($currencyID == '3') {
                                $decimalPlace = 0; //!empty($rptCurrency) ? $rptCurrency->DecimalPlaces : 2;
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

                     \Excel::create('revenue_by_customer', function ($excel) use ($data) {
                        $excel->sheet('sheet name', function ($sheet) use ($data) {
                            $sheet->fromArray($data, null, 'A1', true);
                            $sheet->setAutoSize(true);
                            $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                        });
                        $lastrow = $excel->getActiveSheet()->getHighestRow();
                        $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                    })->download($type);


                } elseif ($reportTypeID == 'RMS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);
                    $type = $request->type;

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

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Currency'] = $requestCurrency->CurrencyCode;
                            $data[$x]['Jan'] = $val->Jan;
                            $data[$x]['Feb'] = $val->Feb;
                            $data[$x]['March'] = $val->March;
                            $data[$x]['April'] = $val->April;
                            $data[$x]['May'] = $val->May;
                            $data[$x]['June'] = $val->June;
                            $data[$x]['July'] = $val->July;
                            $data[$x]['Aug'] = $val->Aug;
                            $data[$x]['Sept'] = $val->Sept;
                            $data[$x]['Oct'] = $val->Oct;
                            $data[$x]['Nov'] = $val->Nov;
                            $data[$x]['Dec'] = $val->Dece;
                            $data[$x]['Total'] = $val->Total;
                            $x++;
                        }
                    } else {
                        $data = array();
                    }

                     \Excel::create('revenue_by_customer', function ($excel) use ($data) {
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
            case 'CNR': //Credit Note Register
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getCreditNoteRegisterQRY($request);
                $type = $request->type;
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $x++;
                        $matchingDocdate = '';
                        $matchingDocCode = '';
                        if ($val->custReceiptDate == null && $val->matchingDocdate == null) {
                            $matchingDocCode = $val->matchingDocCode;
                        } else if ($val->custReceiptDate != null && $val->matchingDocdate == null) {
                            $matchingDocCode = $val->custReceiptCode;
                        } else if ($val->custReceiptDate == null && $val->matchingDocdate != null) {
                            $matchingDocCode = $val->matchingDocCode;
                        } else if ($val->custReceiptDate > $val->matchingDocdate) {
                            $matchingDocCode = $val->custReceiptCode;
                        } else if ($val->matchingDocdate > $val->custReceiptDate) {
                            $matchingDocCode = $val->matchingDocCode;
                        }
                        if ($val->custReceiptDate == null && $val->matchingDocdate == null) {
                            $matchingDocdate = $val->matchingDocdate;
                        } else if ($val->custReceiptDate != null && $val->matchingDocdate == null) {
                            $matchingDocdate = $val->custReceiptDate;
                        } else if ($val->custReceiptDate == null && $val->matchingDocdate != null) {
                            $matchingDocdate = $val->matchingDocdate;
                        } else if ($val->custReceiptDate > $val->matchingDocdate) {
                            $matchingDocdate = $val->custReceiptDate;
                        } else if ($val->matchingDocdate > $val->custReceiptDate) {
                            $matchingDocdate = $val->matchingDocdate;
                        }
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Customer Short Code'] = $val->CutomerCode;
                        $data[$x]['Customer Name'] = $val->CustomerName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Posted Date'] = \Helper::dateFormat($val->postedDate);
                        $data[$x]['Comments'] = $val->documentNarration;
                        $data[$x]['Department'] = $val->ServiceLineDes;
                        $data[$x]['Client Contract ID'] = $val->clientContractID;
                        $data[$x]['GL Code'] = $val->AccountCode;
                        $data[$x]['GL Description'] = $val->AccountDescription;
                        $data[$x]['Currency'] = $val->CurrencyCode;
                        $data[$x]['Credit Note Total Amount'] = round($val->documentRptAmount, $val->DecimalPlaces);
                        $data[$x]['Receipt Matching Code'] = $matchingDocCode;
                        $data[$x]['Receipt Matching Date'] = $matchingDocdate;
                        $data[$x]['Receipt Amount'] = round(($val->detailSum + $val->custReceiptSum), $val->DecimalPlaces);
                    }
                }

                 \Excel::create('customer_collection', function ($excel) use ($data) {
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
            case 'INVTRACK': //Customer Invoice Tracker
                $type = 'csv';
                $input = $request->all();

                $validator = \Validator::make($input, [
                    'customerID' => 'required',
                    'contractID' => 'required',
                    'yearID' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $output = $this->getInvoiceTrackerQRY($request);

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x]['Rig'] = $val->RigDescription." ".$val->regNo;
                        $data[$x]['Year'] = $val->myRentYear;
                        $data[$x]['Month'] = $val->myRentMonth;
                        $data[$x]['Start Date'] = \Helper::dateFormat($val->rentalStartDate);
                        $data[$x]['End Date'] = \Helper::dateFormat($val->rentalEndDate);
                        $data[$x]['Rental'] = $val->billingCode;
                        $data[$x]['Amount'] = $val->performaValue;
                        $data[$x]['Proforma'] = $val->PerformaCode;
                        $data[$x]['Pro Date'] = \Helper::dateFormat($val->performaOpConfirmedDate);
                        $data[$x]['Client Status'] = $val->description;
                        $data[$x]['Client App Date'] = \Helper::dateFormat($val->myClientapprovedDate);
                        $data[$x]['Batch No'] = $val->batchNo;
                        $data[$x]['Submitted Date'] = \Helper::dateFormat($val->mySubmittedDate);
                        $data[$x]['Invoice No'] = $val->bookingInvCode;
                        $data[$x]['Invoice App Date'] = \Helper::dateFormat($val->myApprovedDate);
                        $data[$x]['Status'] = $val->status;
                        $data[$x]['Receipt Code'] = $val->ReceiptCode;
                        $data[$x]['Receipt Date'] = \Helper::dateFormat($val->ReceiptDate);
                        $data[$x]['Receipt Amount'] = $val->ReceiptAmount;
                        $x++;
                    }
                } else {
                    $data = [];
                }

                 \Excel::create('invoice_tracker_', function ($excel) use ($data) {
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

                    $companyLogo = $checkIsGroup->companyLogo;

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

                    $companyLogo = $checkIsGroup->companyLogo;

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

                    $companyLogo = $checkIsGroup->companyLogo;

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

                    $companyLogo = $checkIsGroup->companyLogo;

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

                    $companyLogo = $checkIsGroup->companyLogo;

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

                    $companyLogo = $checkIsGroup->companyLogo;

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
            ->select('quotationMasterID','quotationCode','referenceNo','documentDate','serviceLineSystemID','customerName','transactionCurrency','transactionCurrencyDecimalPlaces','documentExpDate','confirmedYN','approvedYN','refferedBackYN','deliveryStatus','invoiceStatus')
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

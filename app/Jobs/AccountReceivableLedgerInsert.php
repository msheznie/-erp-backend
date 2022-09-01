<?php

namespace App\Jobs;

use App\Models\AccountsReceivableLedger;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\DirectReceiptDetail;
use App\Models\SalesReturn;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\Employee;
use App\Models\Taxdetail;
use App\Models\CustomerInvoiceDirectDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountReceivableLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel)
    {
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/accounts_receivable_ledger_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                switch ($masterModel["documentSystemID"]) {
                    case 19: // Credit Note
                        $masterData = CreditNote::with(['details' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(creditAmount) as transAmount,creditNoteAutoID,serviceLineSystemID,serviceLineCode,clientContractID,contractUID');
                        }, 'finance_period_by'])->find($masterModel["autoID"]);

                        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER")->WHERE('documentSystemCode', $masterModel["autoID"])->WHERE('documentSystemID', $masterModel["documentSystemID"])->first();

                        //all details
                        $detailsCreditNote = CreditNoteDetails::with(['chartofaccount'])
                            ->selectRaw("netAmountLocal as localAmount, netAmountRpt as rptAmount, netAmount as transAmount, VATAmount as transTax, VATAmountLocal as localTax, VATAmountRpt as rptTax, chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,clientContractID,contractUID,comments,chartOfAccountSystemID")
                            ->WHERE('creditNoteAutoID', $masterModel["autoID"])
                            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'clientContractID', 'comments')
                            ->get();

                        $taxLocal = 0;
                        $taxRpt = 0;
                        $taxTrans = 0;

                        if ($tax) {
                            $taxLocal = $tax->localAmount;
                            $taxRpt = $tax->rptAmount;
                            $taxTrans = $tax->transAmount;
                        }

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->creditNoteDate;
                        }

                        if ($masterData) {
                            foreach ($detailsCreditNote as $detail) {

                                $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentCodeSystem'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->creditNoteCode;
                            $data['documentDate'] = $masterDocumentDate;
                                $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                                $data['serviceLineCode'] = $detail->serviceLineCode;
                            $data['customerID'] = $masterData->customerID;
                            $data['InvoiceNo'] = null;
                            $data['InvoiceDate'] = null;
                            $data['custTransCurrencyID'] = $masterData->customerCurrencyID;
                            $data['custTransER'] = $masterData->customerCurrencyER;
                            $data['custInvoiceAmount'] = ABS($detail->transAmount + $detail->transTax) * -1;
                            $data['custDefaultCurrencyID'] = 0;
                            $data['custDefaultCurrencyER'] = 0;
                            $data['custDefaultAmount'] = 0;
                            $data['localCurrencyID'] = $masterData->localCurrencyID;
                            $data['localER'] = $masterData->localCurrencyER;
                            $data['localAmount'] = \Helper::roundValue(ABS($detail->localAmount + $detail->localTax) * -1);
                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingER;
                            $data['comRptAmount'] = \Helper::roundValue(ABS($detail->rptAmount + $detail->rptTax) * -1);
                            $data['isInvoiceLockedYN'] = 0;
                            $data['documentType'] = $masterData->documentType;
                            $data['selectedToPaymentInv'] = 0;
                            $data['fullyInvoiced'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timeStamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                            }
                        }
                        break;
                    case 20: // Customer Invoice
                        $masterData = CustomerInvoiceDirect::with(['invoicedetails' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(invoiceAmount) as transAmount,custInvoiceDirectID,serviceLineSystemID,serviceLineCode');
                        }, 'finance_period_by'])->find($masterModel["autoID"]);

                        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER")->WHERE('documentSystemCode', $masterModel["autoID"])->WHERE('documentSystemID', $masterModel["documentSystemID"])->first();

                        $taxLocal = 0;
                        $taxRpt = 0;
                        $taxTrans = 0;

                        if ($tax) {
                            $taxLocal = $tax->localAmount;
                            $taxRpt = $tax->rptAmount;
                            $taxTrans = $tax->transAmount;
                        }

                        if ($masterData) {
                            $masterDocumentDate = date('Y-m-d H:i:s');
                            if ($masterData->isPerforma == 1 || $masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5) {
                                $masterDocumentDate = date('Y-m-d H:i:s');
                            } else {
                                if ($masterData->finance_period_by->isActive == -1) {
                                    $masterDocumentDate = $masterData->bookingDate;
                                }
                            }

                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentCodeSystem'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->bookingInvCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['customerID'] = $masterData->customerID;
                            $data['InvoiceNo'] = $masterData->customerInvoiceNo;
                            $data['InvoiceDate'] = $masterData->customerInvoiceDate;
                            $data['custTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                            $data['custTransER'] = $masterData->custTransactionCurrencyER;

                            $data['custDefaultCurrencyID'] = 0;
                            $data['custDefaultCurrencyER'] = 0;
                            $data['custDefaultAmount'] = 0;
                            $data['localCurrencyID'] = $masterData->localCurrencyID;
                            $data['localER'] = $masterData->localCurrencyER;

                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingER;

                            $data['isInvoiceLockedYN'] = 0;
                            $data['documentType'] = $masterData->documentType;
                            $data['selectedToPaymentInv'] = 0;
                            $data['fullyInvoiced'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timeStamp'] = \Helper::currentDateTime();

                            if($masterData->isPerforma == 2 || $masterData->isPerforma == 3|| $masterData->isPerforma == 4|| $masterData->isPerforma == 5){// item sales invoice
                                $data['custInvoiceAmount'] = ABS($masterData->bookingAmountTrans + $taxTrans);
                                $data['localAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountLocal + $taxLocal));
                                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->bookingAmountRpt + $taxRpt));
                                array_push($finalData, $data);
                            }else if( $masterData->isPerforma == 1){
                                $data['custInvoiceAmount'] = ABS($masterData->invoicedetails[0]->transAmount);
                                $data['localAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->localAmount));
                                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->rptAmount));
                                array_push($finalData, $data);
                            }else{
                                if ($masterData->isPerforma == 0) {
                                    $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt, sum(VATAmount*invoiceQty) as VATAmountTotal, sum(VATAmountLocal*invoiceQty) as VATAmountLocalTotal, sum(VATAmountRpt*invoiceQty) as VATAmountRptTotal")->with(['contract'])->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('glCode', 'serviceLineCode', 'comments')->get();


                                    foreach ($detail as $item) {
                                        $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                                        $data['serviceLineCode'] = $item->serviceLineCode;
                                        
                                        $data['custInvoiceAmount'] = ABS($item->invoiceAmount + $item->VATAmountTotal);
                                        $data['localAmount'] = \Helper::roundValue(ABS($item->localAmount + $item->VATAmountLocalTotal));
                                        $data['comRptAmount'] = \Helper::roundValue(ABS($item->comRptAmount + $item->VATAmountRptTotal));
                                        array_push($finalData, $data);
                                    }
                                } else {
                                    $data['custInvoiceAmount'] = ABS($masterData->invoicedetails[0]->transAmount + $taxTrans);
                                    $data['localAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->localAmount + $taxLocal));
                                    $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->rptAmount + $taxRpt));
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 21: // Receipt Voucher
                        $masterData = CustomerReceivePayment::with(['details' => function ($query) {
                            $query->selectRaw('SUM(receiveAmountLocal) as localAmount, SUM(receiveAmountRpt) as rptAmount,SUM(receiveAmountTrans) as transAmount,custReceivePaymentAutoID');
                        }, 'directdetails' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,directReceiptAutoID,serviceLineSystemID,serviceLineCode');
                        },'advance_receipt_details' => function($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(paymentAmount) as transAmount,custReceivePaymentAutoID');
                        },'finance_period_by'])->find($masterModel["autoID"]);

                        $directReceipts = DirectReceiptDetail::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")
                            ->WHERE('directReceiptAutoID', $masterModel["autoID"])
                            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID')
                            ->get();

                        $masterDocumentDate = date('Y-m-d H:i:s');

                        if ($masterData) {
                            if ($masterData->documentType == 13 || $masterData->documentType == 15) {
                                if ($masterData->finance_period_by->isActive == -1) {
                                    $masterDocumentDate = $masterData->custPaymentReceiveDate;
                                }

                                $transAmount = 0;
                                $transAmountLocal = 0;
                                $transAmountRpt = 0;

                                if (isset($masterData->details) && count($masterData->details) > 0) {
                                    $transAmount = $transAmount + $masterData->details[0]->transAmount;
                                    $transAmountLocal = $transAmountLocal + $masterData->details[0]->localAmount;
                                    $transAmountRpt = $transAmountRpt + $masterData->details[0]->rptAmount;
                                }

                                if (isset($masterData->directdetails) && count($masterData->directdetails) > 0) {
                                    $transAmount = $transAmount + $masterData->directdetails[0]->transAmount;
                                    $transAmountLocal = $transAmountLocal + $masterData->directdetails[0]->localAmount;
                                    $transAmountRpt = $transAmountRpt + $masterData->directdetails[0]->rptAmount;
                                }

                                if (isset($masterData->advance_receipt_details) && count($masterData->advance_receipt_details) > 0) {
                                    $transAmount = $transAmount + $masterData->advance_receipt_details[0]->transAmount;
                                    $transAmountLocal = $transAmountLocal + $masterData->advance_receipt_details[0]->localAmount;
                                    $transAmountRpt = $transAmountRpt + $masterData->advance_receipt_details[0]->rptAmount;
                                }


                                $transAmountLocal = \Helper::roundValue($transAmountLocal);
                                $transAmountRpt = \Helper::roundValue($transAmountRpt);


                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['documentSystemID'] = $masterData->documentSystemID;
                                $data['documentID'] = $masterData->documentID;
                                $data['documentCodeSystem'] = $masterModel["autoID"];
                                $data['documentCode'] = $masterData->custPaymentReceiveCode;
                                $data['documentDate'] = $masterDocumentDate;
                                $data['customerID'] = $masterData->customerID;
                                $data['InvoiceNo'] = null;
                                $data['InvoiceDate'] = null;
                                $data['custTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                $data['custTransER'] = $masterData->custTransactionCurrencyER;
                                $data['custDefaultCurrencyID'] = 0;
                                $data['custDefaultCurrencyER'] = 0;
                                $data['custDefaultAmount'] = 0;
                                $data['localCurrencyID'] = $masterData->localCurrencyID;
                                $data['localER'] = $masterData->localCurrencyER;
                                $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                $data['comRptER'] = $masterData->companyRptCurrencyER;
                                $data['isInvoiceLockedYN'] = 0;
                                $data['documentType'] = $masterData->documentType;
                                $data['selectedToPaymentInv'] = 0;
                                $data['fullyInvoiced'] = 0;
                                $data['createdDateTime'] = \Helper::currentDateTime();
                                $data['createdUserID'] = $empID->empID;
                                $data['createdUserSystemID'] = $empID->employeeSystemID;
                                $data['createdPcID'] = gethostname();
                                $data['timeStamp'] = \Helper::currentDateTime();

                                if ($masterData->documentType == 13) {
                                    $receiptDetails = CustomerReceivePaymentDetail::with(['ar_data'])
                                        ->WHERE('custReceivePaymentAutoID', $masterModel["autoID"])
                                        ->get();

                                    foreach ($receiptDetails as $key => $valueRe) {

                                        $data['serviceLineSystemID'] = ($valueRe->ar_data) ? $valueRe->ar_data->serviceLineSystemID : 24;
                                        $data['serviceLineCode'] = ($valueRe->ar_data) ? $valueRe->ar_data->serviceLineCode : 'X';

                                        $data['custInvoiceAmount'] = $valueRe->receiveAmountTrans;
                                        $data['localAmount'] = $valueRe->receiveAmountLocal;
                                        $data['comRptAmount'] = $valueRe->receiveAmountRpt;
                                        array_push($finalData, $data);
                                    }

                                } elseif ($masterData->documentType == 15) {
                                    foreach ($directReceipts as $detail) {
                                        $data['serviceLineSystemID'] = $detail->serviceLineSystemID;
                                        $data['serviceLineCode'] = $detail->serviceLineCode;
                                        $data['custInvoiceAmount'] = $detail->transAmount;
                                        $data['localAmount'] = $detail->localAmount;
                                        $data['comRptAmount'] = $detail->rptAmount;
                                        array_push($finalData, $data);
                                    }

                                }


                                else {
                                    $data['custInvoiceAmount'] = ($masterData->documentType == 15) ? (ABS($transAmount) * -1) : $transAmount;
                                    $data['localAmount'] = ($masterData->documentType == 15) ? (ABS($transAmountLocal) * -1) : $transAmountLocal;
                                    $data['comRptAmount'] = ($masterData->documentType == 15) ? (ABS($transAmountRpt) * -1) : $transAmountRpt;
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 87: // Sales Return
                        $masterData = SalesReturn::with(['detail' => function ($query) {
                            $query->selectRaw('SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,salesReturnID');
                        }, 'finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->salesReturnDate;
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentCodeSystem'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->salesReturnCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['customerID'] = $masterData->customerID;
                            $data['InvoiceNo'] = null;
                            $data['InvoiceDate'] = null;
                            $data['custTransCurrencyID'] = $masterData->transactionCurrencyID;
                            $data['custTransER'] = $masterData->transactionCurrencyER;
                            $data['custInvoiceAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount) + ((!is_null($masterData->VATAmount)) ? $masterData->VATAmount : 0)) * -1;
                            $data['custDefaultCurrencyID'] = 0;
                            $data['custDefaultCurrencyER'] = 0;
                            $data['custDefaultAmount'] = 0;
                            $data['localCurrencyID'] = $masterData->companyLocalCurrencyID;
                            $data['localER'] = $masterData->companyLocalCurrencyER;
                            $data['localAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount) + ((!is_null($masterData->VATAmountLocal)) ? $masterData->VATAmountLocal : 0)) * -1;
                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingCurrencyER;
                            $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount) + ((!is_null($masterData->VATAmountRpt)) ? $masterData->VATAmountRpt : 0)) * -1;
                            $data['isInvoiceLockedYN'] = 0;
                            $data['documentType'] = null;
                            $data['selectedToPaymentInv'] = 0;
                            $data['fullyInvoiced'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timeStamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }
                if ($finalData) {
                    Log::info($finalData);
                    //$apLedgerInsert = AccountsReceivableLedger::insert($finalData);
                    foreach ($finalData as $data)
                    {
                        AccountsReceivableLedger::create($data);
                    }
                    Log::info('Successfully inserted to AR table ' . date('H:i:s'));
                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

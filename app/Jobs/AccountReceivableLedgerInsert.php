<?php

namespace App\Jobs;

use App\Models\AccountsReceivableLedger;
use App\Models\CreditNote;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\Employee;
use App\Models\Taxdetail;
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
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentCodeSystem'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->creditNoteCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['customerID'] = $masterData->customerID;
                            $data['InvoiceNo'] = null;
                            $data['InvoiceDate'] = null;
                            $data['custTransCurrencyID'] = $masterData->customerCurrencyID;
                            $data['custTransER'] = $masterData->customerCurrencyER;
                            $data['custInvoiceAmount'] = ABS($masterData->details[0]->transAmount) * -1;
                            $data['custDefaultCurrencyID'] = 0;
                            $data['custDefaultCurrencyER'] = 0;
                            $data['custDefaultAmount'] = 0;
                            $data['localCurrencyID'] = $masterData->localCurrencyID;
                            $data['localER'] = $masterData->localCurrencyER;
                            $data['localAmount'] = \Helper::roundValue(ABS($masterData->details[0]->localAmount + $taxLocal) * -1);
                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingER;
                            $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->details[0]->rptAmount + $taxRpt) * -1);
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
                            if ($masterData->isPerforma == 1) {
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
                            $data['custInvoiceAmount'] = ABS($masterData->invoicedetails[0]->transAmount + $taxTrans);
                            $data['custDefaultCurrencyID'] = 0;
                            $data['custDefaultCurrencyER'] = 0;
                            $data['custDefaultAmount'] = 0;
                            $data['localCurrencyID'] = $masterData->localCurrencyID;
                            $data['localER'] = $masterData->localCurrencyER;
                            $data['localAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->localAmount + $taxLocal));
                            $data['comRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['comRptER'] = $masterData->companyReportingER;
                            $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->invoicedetails[0]->rptAmount + $taxRpt));
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
                        break;
                    case 21: // Receipt Voucher
                        $masterData = CustomerReceivePayment::with(['details' => function ($query) {
                            $query->selectRaw('SUM(receiveAmountLocal) as localAmount, SUM(receiveAmountRpt) as rptAmount,SUM(receiveAmountTrans) as transAmount,custReceivePaymentAutoID');
                        }, 'directdetails' => function ($query) {
                            $query->selectRaw('SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,directReceiptAutoID,serviceLineSystemID,serviceLineCode');
                        }, 'finance_period_by'])->find($masterModel["autoID"]);

                        $masterDocumentDate = date('Y-m-d H:i:s');

                        if ($masterData) {
                            if ($masterData->documentType == 13) {
                                if ($masterData->finance_period_by->isActive == -1) {
                                    $masterDocumentDate = $masterData->custPaymentReceiveDate;
                                }
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
                                $data['custInvoiceAmount'] = ABS($masterData->details[0]->transAmount + $masterData->directdetails[0]->transAmount);
                                $data['custDefaultCurrencyID'] = 0;
                                $data['custDefaultCurrencyER'] = 0;
                                $data['custDefaultAmount'] = 0;
                                $data['localCurrencyID'] = $masterData->localCurrencyID;
                                $data['localER'] = $masterData->localCurrencyER;
                                $data['localAmount'] = \Helper::roundValue(ABS($masterData->details[0]->localAmount + $masterData->directdetails[0]->localAmount));
                                $data['comRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                $data['comRptER'] = $masterData->companyRptCurrencyER;
                                $data['comRptAmount'] = \Helper::roundValue(ABS($masterData->details[0]->rptAmount + $masterData->directdetails[0]->rptAmount));
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
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }
                if ($finalData) {
                    Log::info($finalData);
                    $apLedgerInsert = AccountsReceivableLedger::insert($finalData);
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

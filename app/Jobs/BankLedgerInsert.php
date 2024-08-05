<?php

namespace App\Jobs;

use App\helper\TaxService;
use App\Models\BankAccount;
use App\Models\BankLedger;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\Employee;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\POSBankGLEntries;
use App\Models\POSGLEntries;
use App\Models\POSInvoiceSource;
use App\Models\POSSourceMenuSalesMaster;
use App\Models\SupplierMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BankLedgerInsert implements ShouldQueue
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
        Log::useFiles(storage_path() . '/logs/bank_ledger_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                switch ($masterModel["documentSystemID"]) {
                    case 4: // Payment Voucher
                        $masterData = PaySupplierInvoiceMaster::with('financeperiod_by')->find($masterModel["autoID"]);
                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->BPVdate;
                        }
                        if (isset($masterModel['pdcFlag']) && $masterModel['pdcFlag']) {
                            $masterDocumentDate = Carbon::parse($masterModel['pdcDate']);

                            $currencyConvertionData = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $masterModel['pdcAmount']);

                            $masterData->payAmountBank = $masterModel['pdcAmount'];
                            $masterData->payAmountSuppTrans = $masterModel['pdcAmount'];
                            $masterData->payAmountCompLocal = $currencyConvertionData['localAmount'];
                            $masterData->payAmountCompRpt = $currencyConvertionData['reportingAmount'];
                        }
                        $retationVATAmount = 0;
                        $retentionLocalVatAmount = 0;
                        $retentionRptVatAmount = 0;
                        $retationVATAmount = TaxService::calculateRetentionVatAmount($masterModel["autoID"]);

                        if ($retationVATAmount > 0) {
                            $currencyConvertionRetention = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $retationVATAmount);

                            $retentionLocalVatAmount = $currencyConvertionRetention['localAmount'];
                            $retentionRptVatAmount = $currencyConvertionRetention['reportingAmount'];
                        }

                       
                        $data['companySystemID'] = $masterData->companySystemID;
                        $data['companyID'] = $masterData->companyID;
                        $data['documentSystemID'] = $masterData->documentSystemID;
                        $data['documentID'] = $masterData->documentID;
                        $data['documentSystemCode'] = $masterModel["autoID"];
                        $data['documentCode'] = $masterData->BPVcode;
                        $data['documentDate'] = $masterData->BPVdate;
                        $data['postedDate'] = $masterDocumentDate;
                        $data['documentNarration'] = $masterData->BPVNarration;
                        $data['bankID'] = $masterData->BPVbank;
                        $data['bankAccountID'] = $masterData->BPVAccount;
                        $data['bankCurrency'] = $masterData->BPVbankCurrency;
                        $data['bankCurrencyER'] = $masterData->BPVbankCurrencyER;
                        $data['documentChequeNo'] = (isset($masterModel['pdcFlag']) && $masterModel['pdcFlag']) ? $masterModel['pdcChequeNo'] : $masterData->BPVchequeNo;
                        $data['documentChequeDate'] = (isset($masterModel['pdcFlag']) && $masterModel['pdcFlag']) ? $masterModel['pdcChequeDate'] : $masterData->BPVchequeDate;
                        $data['pdcID'] = isset($masterModel['pdcID']) ? $masterModel['pdcID'] : null;
                        $data['payeeID'] = $masterData->BPVsupplierID;

                        $payee = SupplierMaster::find($masterData->BPVsupplierID);
                        if ($payee) {
                            $data['payeeCode'] = $payee->primarySupplierCode;
                        } else {
                            $data['payeeCode'] = null;
                        }
                        $data['payeeName'] = $masterData->directPaymentPayee;
                        $data['payeeGLCodeID'] = $masterData->supplierGLCodeSystemID;
                        $data['payeeGLCode'] = $masterData->supplierGLCode;
                        $data['supplierTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                        $data['supplierTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                        $data['localCurrencyID'] = $masterData->localCurrencyID;
                        $data['localCurrencyER'] = $masterData->localCurrencyER;
                        $data['companyRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['companyRptCurrencyER'] = $masterData->companyRptCurrencyER;

                        if (isset($masterModel['pdcFlag']) && $masterModel['pdcFlag']) {
                            $data['payAmountBank'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ($masterData->payAmountBank) * -1 : $masterData->payAmountBank;
                            $data['payAmountSuppTrans'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ?($masterData->payAmountSuppTrans) * -1 : $masterData->payAmountSuppTrans;
                            $data['payAmountCompLocal'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ($masterData->payAmountCompLocal) * -1 : $masterData->payAmountCompLocal;
                            $data['payAmountCompRpt'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ($masterData->payAmountCompRpt) * -1 : $masterData->payAmountCompRpt;
                        } else {
                            $data['payAmountBank'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ($masterData->payAmountBank + $retationVATAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmountBank)) * -1 : $masterData->payAmountBank + $retationVATAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmountBank);
                            $data['payAmountSuppTrans'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ?($masterData->payAmountSuppTrans + $retationVATAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmount)) * -1 : $masterData->payAmountSuppTrans + $retationVATAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmount);
                            $data['payAmountCompLocal'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ($masterData->payAmountCompLocal + $retentionLocalVatAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmountLocal)) * -1 : $masterData->payAmountCompLocal + $retentionLocalVatAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmountLocal);
                            $data['payAmountCompRpt'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ($masterData->payAmountCompRpt + $retentionRptVatAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmountRpt)) * -1 : $masterData->payAmountCompRpt + $retentionRptVatAmount + ($masterData->rcmActivated ? 0 : $masterData->VATAmountRpt);
                        }



                        if ($masterData->chequePaymentYN == 0) {
                            $data['chequePaymentYN'] = -1;
                        } else {
                            $data['chequePaymentYN'] = $masterData->chequePaymentYN;
                        }

                        if ($masterData->trsCollectedYN == 0) {
                            $data['trsCollectedYN'] = -1;
                        } else {
                            $data['trsCollectedYN'] = $masterData->trsCollectedYN;
                        }

                        $data['trsCollectedByEmpSystemID'] = $masterData->trsCollectedByEmpSystemID;
                        $data['trsCollectedByEmpID'] = $masterData->trsCollectedByEmpID;
                        $data['trsCollectedByEmpName'] = $masterData->trsCollectedByEmpName;
                        $data['trsCollectedDate'] = $masterData->trsCollectedDate;

                        $data['invoiceType'] = $masterData->invoiceType;
                        $data['createdUserID'] = $empID->empID;
                        $data['createdUserSystemID'] = $empID->employeeSystemID;
                        $data['createdPcID'] = gethostname();
                        $data['timestamp'] = NOW();
                        array_push($finalData, $data);

                        if ($masterData->invoiceType == 3) {
                            $custReceivePayment = CustomerReceivePayment::where('companySystemID', $masterData->companySystemID)->where('documentSystemID', $masterData->documentSystemID)->where('PayMasterAutoId', $masterModel["autoID"])->first();


                            if ($custReceivePayment) {
                                if (isset($masterModel['pdcFlag']) && $masterModel['pdcFlag']) {
                                    $masterDocumentDate = Carbon::parse($masterModel['pdcDate']);
                                    $currencyConvertionData = \Helper::currencyConversion($custReceivePayment->companySystemID, $custReceivePayment->custTransactionCurrencyID, $custReceivePayment->custTransactionCurrencyID, $masterModel['pdcAmount']);

                                    $custReceivePayment->bankAmount = $masterModel['pdcAmount'];
                                    $custReceivePayment->localAmount = $currencyConvertionData['localAmount'];
                                    $custReceivePayment->companyRptAmount = $currencyConvertionData['reportingAmount'];
                                }
                                
                                $data['companySystemID'] = $custReceivePayment->companySystemID;
                                $data['companyID'] = $custReceivePayment->companyID;
                                $data['documentSystemID'] = $custReceivePayment->documentSystemID;
                                $data['documentID'] = $custReceivePayment->documentID;
                                $data['documentSystemCode'] = $custReceivePayment->PayMasterAutoId;
                                $data['documentCode'] = $custReceivePayment->custPaymentReceiveCode;
                                $data['documentDate'] = $custReceivePayment->custPaymentReceiveDate;
                                $data['postedDate'] = $masterDocumentDate;
                                $data['documentNarration'] = $custReceivePayment->narration;
                                $data['bankID'] = $custReceivePayment->bankID;
                                $data['bankAccountID'] = $custReceivePayment->bankAccount;
                                $data['bankCurrency'] = $custReceivePayment->bankCurrency;
                                $data['bankCurrencyER'] = $custReceivePayment->bankCurrencyER;
                                $data['documentChequeNo'] = $custReceivePayment->custChequeNo;
                                $data['documentChequeDate'] = $custReceivePayment->custChequeDate;
                                $data['payeeID'] = $custReceivePayment->customerID;

                                $payee = CustomerMaster::find($custReceivePayment->customerID);
                                if ($payee) {
                                    $data['payeeCode'] = $payee->CutomerCode;
                                } else {
                                    $data['payeeCode'] = null;
                                }
                                $data['payeeName'] = $custReceivePayment->PayeeName;
                                $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
                                $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
                                $data['supplierTransCurrencyID'] = $custReceivePayment->custTransactionCurrencyID;
                                $data['supplierTransCurrencyER'] = $custReceivePayment->custTransactionCurrencyER;
                                $data['localCurrencyID'] = $custReceivePayment->localCurrencyID;
                                $data['localCurrencyER'] = $custReceivePayment->localCurrencyER;
                                $data['companyRptCurrencyID'] = $custReceivePayment->companyRptCurrencyID;
                                $data['companyRptCurrencyER'] = $custReceivePayment->companyRptCurrencyER;
                                $data['payAmountBank'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ABS($custReceivePayment->bankAmount) : ABS($custReceivePayment->bankAmount) * -1;
                                $data['payAmountSuppTrans'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ABS($custReceivePayment->bankAmount) : ABS($custReceivePayment->bankAmount) * -1;
                                $data['payAmountCompLocal'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ABS($custReceivePayment->localAmount) : ABS($custReceivePayment->localAmount) * -1;
                                $data['payAmountCompRpt'] = (isset($masterModel['reversePdc']) && $masterModel['reversePdc']) ? ABS($custReceivePayment->companyRptAmount) : ABS($custReceivePayment->companyRptAmount) * -1;
                                $data['chequePaymentYN'] = -1;

                                if ($custReceivePayment->trsCollectedYN == 0) {
                                    $data['trsCollectedYN'] = -1;
                                } else {
                                    $data['trsCollectedYN'] = $custReceivePayment->trsCollectedYN;
                                }

                                $data['trsCollectedByEmpSystemID'] = $custReceivePayment->trsCollectedByEmpSystemID;
                                $data['trsCollectedByEmpID'] = $custReceivePayment->trsCollectedByEmpID;
                                $data['trsCollectedByEmpName'] = $custReceivePayment->trsCollectedByEmpName;
                                $data['trsCollectedDate'] = $custReceivePayment->trsCollectedDate;

                                $data['invoiceType'] = $custReceivePayment->documentType;
                                $data['createdUserID'] = $empID->empID;
                                $data['createdUserSystemID'] = $empID->employeeSystemID;
                                $data['createdPcID'] = gethostname();
                                $data['timestamp'] = NOW();
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 21: // Receipt Voucher
                        $custReceivePayment = CustomerReceivePayment::find($masterModel["autoID"]);
                        if ($custReceivePayment) {
                            $data['companySystemID'] = $custReceivePayment->companySystemID;
                            $data['companyID'] = $custReceivePayment->companyID;
                            $data['documentSystemID'] = $custReceivePayment->documentSystemID;
                            $data['documentID'] = $custReceivePayment->documentID;
                            $data['documentSystemCode'] = $custReceivePayment->custReceivePaymentAutoID;
                            $data['documentCode'] = $custReceivePayment->custPaymentReceiveCode;
                            $data['documentDate'] = $custReceivePayment->custPaymentReceiveDate;
                            $data['postedDate'] = $custReceivePayment->postedDate;
                            $data['documentNarration'] = $custReceivePayment->narration;
                            $data['bankID'] = $custReceivePayment->bankID;
                            $data['bankAccountID'] = $custReceivePayment->bankAccount;
                            $data['bankCurrency'] = $custReceivePayment->bankCurrency;
                            $data['bankCurrencyER'] = $custReceivePayment->bankCurrencyER;
                            $data['documentChequeNo'] = $custReceivePayment->custChequeNo;
                            $data['documentChequeDate'] = $custReceivePayment->custChequeDate;
                            $data['payeeID'] = $custReceivePayment->customerID;

                            $payee = CustomerMaster::find($custReceivePayment->customerID);
                            if ($payee) {
                                $data['payeeCode'] = $payee->CutomerCode;
                                $data['payeeName'] = $payee->CustomerName;
                            } else {
                                $employeeSystemIDForPV = floatval($custReceivePayment->PayeeEmpID);
                                $employeeData = Employee::find($employeeSystemIDForPV);

                                $data['payeeName'] = $employeeData ? $employeeData->empName : $custReceivePayment->PayeeName;                                    
                            }

                            $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
                            $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
                            $data['supplierTransCurrencyID'] = $custReceivePayment->custTransactionCurrencyID;
                            $data['supplierTransCurrencyER'] = $custReceivePayment->custTransactionCurrencyER;
                            $data['localCurrencyID'] = $custReceivePayment->localCurrencyID;
                            $data['localCurrencyER'] = $custReceivePayment->localCurrencyER;
                            $data['companyRptCurrencyID'] = $custReceivePayment->companyRptCurrencyID;
                            $data['companyRptCurrencyER'] = $custReceivePayment->companyRptCurrencyER;
                            $data['payAmountBank'] = $custReceivePayment->bankAmount;
                            $data['payAmountSuppTrans'] = $custReceivePayment->bankAmount;
                            $data['payAmountCompLocal'] = $custReceivePayment->localAmount;
                            $data['payAmountCompRpt'] = $custReceivePayment->companyRptAmount;
                            $data['chequePaymentYN'] = -1;

                            if ($custReceivePayment->trsCollectedYN == 0) {
                                $data['trsCollectedYN'] = -1;
                            } else {
                                $data['trsCollectedYN'] = $custReceivePayment->trsCollectedYN;
                            }

                            $data['trsCollectedByEmpSystemID'] = $custReceivePayment->trsCollectedByEmpSystemID;
                            $data['trsCollectedByEmpID'] = $custReceivePayment->trsCollectedByEmpID;
                            $data['trsCollectedByEmpName'] = $custReceivePayment->trsCollectedByEmpName;
                            $data['trsCollectedDate'] = $custReceivePayment->trsCollectedDate;

                            $data['invoiceType'] = $custReceivePayment->documentType;
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timestamp'] = NOW();
                            array_push($finalData, $data);
                        }
                        break;
                    case 110: // GPOS
                        $gl = POSGLEntries::where('shiftId', $masterModel["autoID"])->first();
                        $bankGL = POSBankGLEntries::where('shiftId', $masterModel["autoID"])->first();
                        if ($gl && $bankGL) {
                            $data['companySystemID'] = $masterModel['companySystemID'];
                            $data['companyID'] = $masterModel["companyID"];
                            $data['documentSystemID'] = 110;
                            $data['documentID'] = 'GPOS';
                            $data['documentSystemCode'] = $gl->documentSystemId;
                            $data['documentCode'] = $gl->documentCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['postedDate'] = date('Y-m-d H:i:s');
                            $data['documentNarration'] = null;
                            $data['bankID'] = $bankGL->bankAccId;
                            $bank = BankAccount::find($bankGL->bankAccId);
                            if($bank){
                                $data['bankAccountID'] = $bank->bankmasterAutoID;
                            }
                            $posInvoiceMaster = POSInvoiceSource::where('shiftID', $masterModel["autoID"])->first();

//                            $data['bankCurrency'] = $custReceivePayment->bankCurrency;
//                            $data['bankCurrencyER'] = $custReceivePayment->bankCurrencyER;
//                            $data['documentChequeNo'] = $custReceivePayment->custChequeNo;
//                            $data['documentChequeDate'] = $custReceivePayment->custChequeDate;
//                            $data['payeeID'] = $custReceivePayment->customerID;
//
//                            $payee = CustomerMaster::find($custReceivePayment->customerID);
//                            if ($payee) {
//                                $data['payeeCode'] = $payee->CutomerCode;
//                            }
//                            $data['payeeName'] = $custReceivePayment->PayeeName;
//                            $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
//                            $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
                            $data['supplierTransCurrencyID'] = $posInvoiceMaster->transactionCurrencyID;
                            $data['supplierTransCurrencyER'] = $posInvoiceMaster->transactionExchangeRate;
                            $data['localCurrencyID'] = $posInvoiceMaster->companyLocalCurrencyID;
                            $data['localCurrencyER'] = $posInvoiceMaster->companyLocalExchangeRate;
                            $data['companyRptCurrencyID'] = $posInvoiceMaster->companyReportingCurrencyID;
                            $data['companyRptCurrencyER'] = $posInvoiceMaster->companyReportingExchangeRate;
                            $data['payAmountBank'] = $bankGL->amount;
                            $data['payAmountSuppTrans'] = $bankGL->amount;
                            $data['payAmountCompLocal'] = $bankGL->amount;
                            $data['payAmountCompRpt'] = $bankGL->amount / $posInvoiceMaster->companyReportingExchangeRate;
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timestamp'] = NOW();
                            array_push($finalData, $data);
                        }
                        break;
                    case 111: // RPOS
                        $gl = POSGLEntries::where('shiftId', $masterModel["autoID"])->first();
                        $bankGL = POSBankGLEntries::where('shiftId', $masterModel["autoID"])->first();
                        if ($gl && $bankGL) {
                            $data['companySystemID'] = $masterModel['companySystemID'];
                            $data['companyID'] = $masterModel["companyID"];
                            $data['documentSystemID'] = 111;
                            $data['documentID'] = 'RPOS';
                            $data['documentSystemCode'] = $gl->documentSystemId;
                            $data['documentCode'] = $gl->documentCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['postedDate'] = date('Y-m-d H:i:s');
                            $data['documentNarration'] = null;
                            $data['bankID'] = $bankGL->bankAccId;
                            $bank = BankAccount::find($bankGL->bankAccId);
                            if($bank){
                                $data['bankAccountID'] = $bank->bankmasterAutoID;
                            }
                            $menuSalesMaster = POSSourceMenuSalesMaster::where('shiftID', $masterModel["autoID"])->first();
                            $data['bankCurrency'] = $menuSalesMaster->bankCurrency;
                            $data['bankCurrencyER'] = $menuSalesMaster->bankCurrencyExchangeRate;
//                            $data['documentChequeNo'] = $custReceivePayment->custChequeNo;
//                            $data['documentChequeDate'] = $custReceivePayment->custChequeDate;
//                            $data['payeeID'] = $custReceivePayment->customerID;
//
//                            $payee = CustomerMaster::find($custReceivePayment->customerID);
//                            if ($payee) {
//                                $data['payeeCode'] = $payee->CutomerCode;
//                            }
//                            $data['payeeName'] = $custReceivePayment->PayeeName;
//                            $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
//                            $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
                            $data['supplierTransCurrencyID'] = $menuSalesMaster->transactionCurrencyID;
                            $data['supplierTransCurrencyER'] = $menuSalesMaster->transactionExchangeRate;
                            $data['localCurrencyID'] = $menuSalesMaster->companyLocalCurrencyID;
                            $data['localCurrencyER'] = $menuSalesMaster->companyLocalExchangeRate;
                            $data['companyRptCurrencyID'] = $menuSalesMaster->companyReportingCurrencyID;
                            $data['companyRptCurrencyER'] = $menuSalesMaster->companyReportingExchangeRate;
                            $data['payAmountBank'] = $bankGL->amount;
                            $data['payAmountSuppTrans'] = $bankGL->amount;
                            $data['payAmountCompLocal'] = $bankGL->amount;
                            $data['payAmountCompRpt'] = $bankGL->amount / $menuSalesMaster->companyReportingExchangeRate;
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdPcID'] = gethostname();
                            $data['timestamp'] = NOW();
                            array_push($finalData, $data);
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }
                if ($finalData) {
                    //$bankLedgerInsert = BankLedger::insert($finalData);
                    foreach ($finalData as $data)
                    {
                        BankLedger::create($data);
                    }
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

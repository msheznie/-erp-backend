<?php

namespace App\Jobs;

use App\Models\BankLedger;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\Employee;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\SupplierMaster;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                        $masterData = PaySupplierInvoiceMaster::find($masterModel["autoID"]);
                        $data['companySystemID'] = $masterData->companySystemID;
                        $data['companyID'] = $masterData->companyID;
                        $data['documentSystemID'] = $masterData->documentSystemID;
                        $data['documentID'] = $masterData->documentID;
                        $data['documentSystemCode'] = $masterModel["autoID"];
                        $data['documentCode'] = $masterData->BPVcode;
                        $data['documentDate'] = $masterData->BPVdate;
                        $data['postedDate'] = $masterData->postedDate;
                        $data['documentNarration'] = $masterData->BPVNarration;
                        $data['bankID'] = $masterData->BPVbank;
                        $data['bankAccountID'] = $masterData->BPVAccount;
                        $data['bankCurrency'] = $masterData->BPVbankCurrency;
                        $data['bankCurrencyER'] = $masterData->BPVbankCurrencyER;
                        $data['documentChequeNo'] = $masterData->BPVchequeNo;
                        $data['documentChequeDate'] = $masterData->BPVchequeDate;
                        $data['payeeID'] = $masterData->BPVsupplierID;

                        $payee = SupplierMaster::find($masterData->BPVsupplierID);
                        if($payee){
                            $data['payeeCode'] = $payee->primarySupplierCode;
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
                        $data['payAmountBank'] = $masterData->payAmountBank;
                        $data['payAmountSuppTrans'] = $masterData->payAmountSuppTrans;
                        $data['payAmountCompLocal'] = $masterData->payAmountCompLocal;
                        $data['payAmountCompRpt'] = $masterData->payAmountCompRpt;
                        $data['invoiceType'] = $masterData->invoiceType;
                        $data['createdUserID'] = $empID->empID;
                        $data['createdUserSystemID'] = $empID->employeeSystemID;
                        $data['createdPcID'] = gethostname();
                        $data['timestamp'] = NOW();
                        array_push($finalData, $data);

                        if($masterData->invoiceType == 3) {
                            $custReceivePayment = CustomerReceivePayment::where('companySystemID',$masterData->companySystemID)->where('documentSystemID',$masterData->documentSystemID)->where('PayMasterAutoId',$masterModel["autoID"])->first();
                            if($custReceivePayment){
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
                                if($payee){
                                    $data['payeeCode'] = $payee->CutomerCode;
                                }else{
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
                                $data['payAmountBank'] = $custReceivePayment->bankAmount;
                                $data['payAmountSuppTrans'] = $custReceivePayment->bankAmount;
                                $data['payAmountCompLocal'] = $custReceivePayment->localAmount;
                                $data['payAmountCompRpt'] = $custReceivePayment->companyRptAmount;
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
                        if($custReceivePayment){
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
                            if($payee){
                                $data['payeeCode'] = $payee->CutomerCode;
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
                            $data['payAmountBank'] = $custReceivePayment->bankAmount;
                            $data['payAmountSuppTrans'] = $custReceivePayment->bankAmount;
                            $data['payAmountCompLocal'] = $custReceivePayment->localAmount;
                            $data['payAmountCompRpt'] = $custReceivePayment->companyRptAmount;
                            $data['invoiceType'] = $custReceivePayment->documentType;
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
                    Log::info($finalData);
                    $bankLedgerInsert = BankLedger::insert($finalData);
                    Log::info('Successfully inserted to bank ledger table ' . date('H:i:s'));
                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($e->getMessage());
            }
        }
    }
}

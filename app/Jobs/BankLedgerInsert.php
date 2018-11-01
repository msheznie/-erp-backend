<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\PaySupplierInvoiceMaster;
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
                        if($masterData->invoiceType == 5 || $masterData->invoiceType == 2) {
                            $data['bankRecAutoID'] =
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
                            $data['payeeID'] = 'NA';
                            $data['payeeCode'] = 'NA';
                            $data['payeeName'] = 'NA';
                            $data['payeeGLCodeID'] = 'NA';
                            $data['payeeGLCode'] = 'NA';
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
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }
                if ($finalData) {
                    Log::info($finalData);
                    $bankLedgerInsert = BankLedgerInsert::insert($finalData);
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

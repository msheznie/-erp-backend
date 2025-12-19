<?php

namespace App\Jobs;

use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\POSTaxGLEntries;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\TaxLedgerService;
use App\Services\JobErrorLogService;
use App\helper\CommonJobService;

class TaxLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $taxLedgerData;
    protected $dataBase;
    private $tag = "tax-ledger";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel, $taxLedgerData, $dataBase)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                 self::onConnection('database_main');
            }else{
                 self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }


        $this->dataBase = $dataBase;
        $this->masterModel = $masterModel;
        $this->taxLedgerData = $taxLedgerData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        Log::useFiles(storage_path() . '/logs/tax_ledger_jobs.log');
        $masterModel = $this->masterModel;
        $taxLedgerData = $this->taxLedgerData;

        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $res = TaxLedgerService::postLedgerEntry($taxLedgerData, $masterModel);
                if (!$res['status']) {
                    DB::rollback();
                    Log::error($res['error']['message']);

                    JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 1, $res['error']['message']);
                } else {
                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));

                JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());            
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

<?php

namespace App\Jobs;

use App\Models\GRVDetails;
use App\Models\UnbilledGrvGroupBy;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnbilledGRVInsert implements ShouldQueue
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
        //
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/unbilled_grv_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                Log::warning('Parameter document id is missing' . date('H:i:s'));
            }
            DB::beginTransaction();
            try {
                $output = GRVDetails::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_grvmaster.supplierID,purchaseOrderMastertID as purchaseOrderID,erp_grvdetails.grvAutoID,erp_grvmaster.grvDate,erp_grvmaster.supplierTransactionCurrencyID,erp_grvmaster.supplierTransactionER as supplierTransactionCurrencyER,erp_grvmaster.companyReportingCurrencyID,erp_grvmaster.companyReportingER,erp_grvmaster.localCurrencyID,erp_grvmaster.localCurrencyER,SUM(landingCost_TransCur*noQty) as totTransactionAmount,SUM(landingCost_LocalCur*noQty) as totLocalAmount, SUM(landingCost_RptCur*noQty) as totRptAmount,'POG' as grvType,NOW() as timeStamp")->leftJoin('erp_grvmaster', 'erp_grvdetails.grvAutoID', '=', 'erp_grvmaster.grvAutoID')->WHERE('erp_grvdetails.grvAutoID', $masterModel["autoID"])->groupBy('purchaseOrderMastertID')->get();
                Log::info($output);
                if ($output) {
                    UnbilledGrvGroupBy::insert($output->toArray());
                    DB::commit();
                    Log::info('Successfully updated to unbilled grv table' . date('H:i:s'));
                }else{
                    DB::rollback();
                    Log::info('No records found in unbilled grv table' . date('H:i:s'));
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error occurred when updating to unbilled grv table' . date('H:i:s'));
            }
        }
    }
}

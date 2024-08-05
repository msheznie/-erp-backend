<?php

namespace App\Jobs;

use App\Models\ErpItemLedger;
use App\Models\ItemAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemAssignInsert implements ShouldQueue
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
        Log::useFiles(storage_path().'/logs/item_assign_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                Log::warning('Parameter document id is missing' . date('H:i:s'));
            }
            DB::beginTransaction();
            try {
                $itemLedgerRec = ErpItemLedger::selectRaw('companySystemID, 
itemSystemCode, 
round(sum(inOutQty),2) as inOutQty,
if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacLocal))/round(sum(inOutQty),2)),9)) as wacCostLocal, 
if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacRpt))/round(sum(inOutQty),2)),9)) as wacCostRpt,
round(sum(inOutQty),2) * if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacLocal))/round(sum(inOutQty),2)),9)) as TotalwacCostLocal,
round(sum(inOutQty),2) * if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacRpt))/round(sum(inOutQty),2)),9)) as TotalwacCostRpt')
                    ->where('companySystemID', $masterModel['companySystemID'])
                    ->where('fromDamagedTransactionYN', 0)
                    ->whereIN('itemSystemCode', $masterModel['items'])
                    ->groupBy('companySystemID','itemSystemCode')->get();
                if ($itemLedgerRec) {
                    foreach ($itemLedgerRec as $val) {
                        if ($val->inOutQty == 0) {
                            $itemAssignRec = ItemAssigned::where('companySystemID', $val->companySystemID)->where('itemCodeSystem', $val->itemSystemCode)
                                ->update(['wacValueLocal' => 0,
                                    'wacValueReporting' => 0,
                                    'totalQty' => $val->inOutQty,
                                    'totalValueLocal' => 0,
                                    'totalValueRpt' => 0
                                ]);
                        } else {
                            $itemAssignRec = ItemAssigned::where('companySystemID', $val->companySystemID)->where('itemCodeSystem', $val->itemSystemCode)
                                ->update(['wacValueLocal' => $val->wacCostLocal,
                                    'wacValueReporting' => $val->wacCostRpt,
                                    'totalQty' => $val->inOutQty,
                                    'totalValueLocal' => $val->TotalwacCostLocal,
                                    'totalValueRpt' => $val->TotalwacCostRpt
                                ]);
                        }
                    }
                    DB::commit();
                }else{
                    DB::rollback();
                    Log::error('No records found in itemledger ' . date('H:i:s'));
                }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error occurred when updating to item assign table ' . date('H:i:s'));
            }
        }
    }
}

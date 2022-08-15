<?php

namespace App\Jobs;

use App\helper\Helper;
use App\Models\BankLedger;
use App\Models\Employee;
use App\Models\ItemMaster;
use App\Models\POSGLEntries;
use App\Models\POSItemGLEntries;
use Exception;
use App\Models\ErpItemLedger;
use App\Models\StockCount;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSItemLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $masterModel;

    public function __construct($masterModel)
    {
        $this->masterModel = $masterModel;
    }

    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/item_ledger_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                $gl = POSGLEntries::where('shiftId', $masterModel["autoID"])->first();
                $items = POSItemGLEntries::where('shiftId', $masterModel["autoID"])->get();
                foreach ($items as $item){

                    if ($gl) {
                        $data['companySystemID'] = $masterModel['companySystemID'];
                        $data['companyID'] = $masterModel["companyID"];
                        $data['documentSystemID'] = 110;
                        $data['documentID'] = 'GPOSS';
                        $data['documentSystemCode'] = $gl->documentSystemId;
                        $data['documentCode'] = $gl->documentCode;
                        $data['itemSystemCode'] = $item->itemAutoId;
                        $itemMaster = ItemMaster::find($item->itemAutoId);
                        if($itemMaster){
                            $data['itemPrimaryCode'] = $itemMaster->primaryCode;
                            $data['itemDescription'] = $itemMaster->itemDescription;
                            $data['itemShortDescription'] = $itemMaster->itemDescription;

                        }
                        $invItems = DB::table('pos_source_invoicedetail')
                            ->selectRaw('pos_source_invoicedetail.*')
                            ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                            ->where('pos_source_invoice.shiftID', $masterModel["autoID"])
                            ->where('pos_source_invoicedetail.itemAutoID',$item->itemAutoId)
                            ->first();
                        $data['unitOfMeasure'] = $item->uom;
                        $data['inOutQty'] = $item->qty * -1;
                        if($invItems){
                            $data['wacLocalCurrencyID'] = $invItems->companyLocalCurrencyID;
                            $data['wacLocal'] = $invItems->companyLocalAmount;
                            $data['wacRptCurrencyID'] = $invItems->companyReportingCurrencyID;
                            $data['wacRpt'] = $invItems->transactionAmount / $invItems->companyReportingExchangeRate;
                        }
                        $data['transactionDate'] =  \Helper::currentDateTime();
                        $data['timestamp'] =  \Helper::currentDateTime();

                        array_push($finalData, $data);
                    }
                }
                Log::info($data);
                if ($finalData) {
                    Log::info($finalData);
                    //$bankLedgerInsert = BankLedger::insert($finalData);
                    foreach ($finalData as $data)
                    {
                        ErpItemLedger::create($data);
                    }
                    Log::info('Successfully inserted to item ledger table ' . date('H:i:s'));
                    DB::commit();
                }
            }
            catch (\Exception $e){

            }
        }
    }

}

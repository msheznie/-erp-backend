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
                switch ($masterModel["documentSystemID"]) {
                case 110: // GPOS
                $gl = POSGLEntries::where('shiftId', $masterModel["autoID"])->first();
                $items = POSItemGLEntries::where('shiftId', $masterModel["autoID"])->get();
                foreach ($items as $item) {

                    if ($gl) {
                        $data['companySystemID'] = $masterModel['companySystemID'];
                        $data['companyID'] = $masterModel["companyID"];
                        $data['documentSystemID'] = 110;
                        $data['documentID'] = 'GPOS';
                        $data['documentSystemCode'] = $gl->documentSystemId;
                        $data['documentCode'] = $gl->documentCode;
                        $data['itemSystemCode'] = $item->itemAutoId;
                        $itemMaster = ItemMaster::find($item->itemAutoId);
                        if ($itemMaster) {
                            $data['itemPrimaryCode'] = $itemMaster->primaryCode;
                            $data['itemDescription'] = $itemMaster->itemDescription;
                            $data['itemShortDescription'] = $itemMaster->itemDescription;

                        }
                        $invItems = DB::table('pos_source_invoicedetail')
                            ->selectRaw('pos_source_invoicedetail.*,pos_source_invoice.wareHouseAutoID as wareHouseID')
                            ->join('pos_source_invoice', 'pos_source_invoice.invoiceID', '=', 'pos_source_invoicedetail.invoiceID')
                            ->where('pos_source_invoice.shiftID', $masterModel["autoID"])
                            ->where('pos_source_invoicedetail.itemAutoID', $item->itemAutoId)
                            ->where('pos_source_invoicedetail.invoiceID', $item->invoiceID)
                            ->first();
                        $data['unitOfMeasure'] = $item->uom;
                        if($item->isReturnYN == 1) {
                            $data['inOutQty'] = $item->qty;
                        }
                            else{
                            $data['inOutQty'] = $invItems->defaultQty * -1;
                        }
                        if ($invItems) {
                            $data['wareHouseSystemCode'] = $invItems->wareHouseID;
                            $data['wacLocalCurrencyID'] = $invItems->companyLocalCurrencyID;
                            $data['wacLocal'] = $invItems->totalCost / $invItems->defaultQty;
                            $data['wacRptCurrencyID'] = $invItems->companyReportingCurrencyID;
                            $data['wacRpt'] = $invItems->totalCost / $invItems->defaultQty / $invItems->companyReportingExchangeRate;
                        }
                        $data['transactionDate'] = \Helper::currentDateTime();
                        $data['timestamp'] = \Helper::currentDateTime();

                        array_push($finalData, $data);
                    }
                }
                break;
                    case 111: // RPOS
                        Log::warning('11Id RPOS' . date('H:i:s'));

                        $gl = POSGLEntries::where('shiftId', $masterModel["autoID"])->first();
                        Log::warning($gl . date('H:i:s'));

                        $items = POSItemGLEntries::where('shiftId', $masterModel["autoID"])->get();
                        Log::warning($items . date('H:i:s'));

                        foreach ($items as $item) {

                            if ($gl) {
                                $data['companySystemID'] = $masterModel['companySystemID'];
                                $data['companyID'] = $masterModel["companyID"];
                                $data['documentSystemID'] = 111;
                                $data['documentID'] = 'RPOS';
                                $data['documentSystemCode'] = $gl->documentSystemId;
                                $data['documentCode'] = $gl->documentCode;
                                $data['itemSystemCode'] = $item->itemAutoId;
                                $itemMaster = ItemMaster::find($item->itemAutoId);
                                if ($itemMaster) {
                                    $data['itemPrimaryCode'] = $itemMaster->primaryCode;
                                    $data['itemDescription'] = $itemMaster->itemDescription;
                                    $data['itemShortDescription'] = $itemMaster->itemDescription;

                                }
                                $invItems = DB::table('pos_source_menusalesitemdetails')
                                    ->selectRaw('pos_source_menusalesitems.*, (pos_source_menusalesitemdetails.cost / pos_source_menusalesitemdetails.qty) as amount,pos_source_menusalesmaster.wareHouseAutoID as wareHouseID')
                                    ->join('pos_source_menusalesmaster', 'pos_source_menusalesmaster.menuSalesID', '=', 'pos_source_menusalesitemdetails.menuSalesID')
                                    ->join('pos_source_menusalesitems', 'pos_source_menusalesitems.menuSalesID', '=', 'pos_source_menusalesmaster.menuSalesID')
                                    ->where('pos_source_menusalesmaster.shiftID', $masterModel["autoID"])
                                    ->where('pos_source_menusalesitemdetails.itemAutoID', $item->itemAutoId)
                                    ->first();
                                $data['unitOfMeasure'] = $item->uom;
                                $data['inOutQty'] = $item->qty * -1;
                                if ($invItems) {
                                    $data['wareHouseSystemCode'] = $invItems->wareHouseID;
                                    $data['wacLocalCurrencyID'] = $invItems->companyLocalCurrencyID;
                                    $data['wacLocal'] = $invItems->amount;
                                    $data['wacRptCurrencyID'] = $invItems->companyReportingCurrencyID;
                                    $data['wacRpt'] = $invItems->amount / $invItems->companyReportingExchangeRate;
                                }
                                $data['transactionDate'] = \Helper::currentDateTime();
                                $data['timestamp'] = \Helper::currentDateTime();

                                array_push($finalData, $data);
                            }
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
            }
                if ($finalData) {
                    //$bankLedgerInsert = BankLedger::insert($finalData);
                    foreach ($finalData as $data)
                    {
                        ErpItemLedger::create($data);
                    }
                    DB::commit();
                }
            }
            catch (\Exception $e){
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

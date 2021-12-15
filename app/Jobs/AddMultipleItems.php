<?php

namespace App\Jobs;


use App\helper\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemMaster;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\helper\CommonJobService;


class AddMultipleItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $purchaseOrder;
    public $db;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record,$purchaseOrder,$db)
    {
        Log::info('Add Mutiple Items Started in Constructor - DB - '.$db);

        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->record = $record;
        $this->purchaseOrder = $purchaseOrder;
        $this->db = $db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Add Mutiple Items Started');

        CommonJobService::db_switch($this->db);
        Log::info('DB Name'.$this->db);
        $items = $this->record;
        $valiatedItems = $this->validateItem($items);

        $procumentOrder = ProcumentOrder::find($this->purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 0;
        $procumentOrder->save();
        $procumentOrderDetails = PurchaseOrderDetails::insert($valiatedItems);
        Log::info('Add Mutiple Items End');
        $procumentOrder = ProcumentOrder::find($this->purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 1;
        $procumentOrder->save();

    }


    public function validateItem($items) {
        $validatedItemsArray = [];
        foreach($items as $item) {
            if(array_key_exists('item_code',$item)) {
                $orgItem = ItemMaster::where('primaryCode',trim($item['item_code']))->first();
                if($orgItem) {
                    $item['purchaseOrderMasterID'] = $this->purchaseOrder['purchaseOrderID'];
                    $item['companyID'] = $this->purchaseOrder['companyID'];
                    $item['companySystemID'] = $this->purchaseOrder['companySystemID'];
                    $item['serviceLineSystemID'] = $this->purchaseOrder['serviceLineSystemID'];
                    $item['serviceLineCode'] = $this->purchaseOrder['serviceLine'];
                    $item['itemCode'] = $orgItem['itemCodeSystem'];
                    $item['unitCost'] = trim($item['unit_cost']);
                    $item['noQty'] = trim($item['no_qty']);
                    $item['itemPrimaryCode'] = trim($orgItem['primaryCode']);
                    $item['itemDescription'] = trim($orgItem['itemDescription']);
                    $item['netAmount'] =   $item['unitCost'] * $item['noQty'];
                    $item['itemCode'] = $item['item_code'];
                    unset($item['item_code'], $item['unit_cost'], $item['no_qty']);
                    $item['unitOfMeasure'] = trim($orgItem['unit']);
                    $item['altUnit'] = trim($orgItem['unit']);
                    $item['altUnitValue'] = trim($item['noQty']);
                    array_push($validatedItemsArray,$item);
                }
            }
        }

        return $validatedItemsArray;
    }
}

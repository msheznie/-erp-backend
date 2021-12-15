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
        CommonJobService::db_switch($this->db);
        
        $items = $this->record;
        $valiatedItems = $this->validateItem($items);
        Log::info('Add Mutiple Items Started');
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
            if($item['item_code']) {
                $orgItem = ItemMaster::find(trim($item['item_code']));
                if($orgItem && Helper::IsItemAssigned($item['item_code'],$this->purchaseOrder['companySystemID'])) {
                    $item['purchaseOrderMasterID'] = $this->purchaseOrder['purchaseOrderID'];
                    $item['companyID'] = $this->purchaseOrder['companyID'];
                    $item['companySystemID'] = $this->purchaseOrder['companySystemID'];
                    $item['serviceLineSystemID'] = $this->purchaseOrder['serviceLineSystemID'];
                    $item['serviceLineCode'] = $this->purchaseOrder['serviceLine'];
                    $item['itemCode'] = trim($item['item_code']);
                    $item['unitCost'] = trim($item['unit_cost']);
                    $item['noQty'] = trim($item['no_qty']);
                    $item['itemPrimaryCode'] = trim($orgItem['primaryCode']);
                    $item['itemDescription'] = trim($orgItem['itemDescription']);
                    $item['netAmount'] =   $item['unitCost'] * $item['noQty'];
                    unset($item['item_code'], $item['unit_cost'], $item['no_qty']);
                    $item['unitOfMeasure'] = trim($orgItem['unit']);
                    $item['altUnit'] = trim($orgItem['unit']);
                    $item['altUnitValue'] = trim($item['noQty']);
                    array_push($validatedItemsArray,$item);
                }
                return $validatedItemsArray;
            }
            
        }

    }

}

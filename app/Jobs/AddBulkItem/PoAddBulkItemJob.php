<?php

namespace App\Jobs\AddBulkItem;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\ItemMaster;
use App\Models\ProcumentOrder;
use App\Services\ProcurementOrder\ProcurementOrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PoAddBulkItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $dispatch_db;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $input)
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


        $this->data = $input;
        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;

        Log::useFiles(storage_path() . '/logs/po_bulk_item.log');

        CommonJobService::db_switch($db);
        $input = $this->data;

        DB::beginTransaction();
        try {

            $companyId = $input['companySystemID'];
            $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                                        return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1);
                                     })->where('isActive',1)
                                     ->where('itemApprovedYN',1)
                                     ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                                        $query->where('financeCategoryMaster', $input['financeCategoryMaster']);
                                     })
                                     ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                                        $query->where('financeCategorySub', $input['financeCategorySub']);
                                     })
                                     ->whereDoesntHave('purchase_order_details', function($query) use ($input) {
                                        $query->where('purchaseOrderMasterID', $input['purchaseOrderID']);
                                     })
                                     ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])
                                     ->get();

            $invalidItems = [];
            foreach ($itemMasters as $key => $value) {
                $res = ProcurementOrderService::validatePoItem($value->itemCodeSystem, $input['companySystemID'], $input['purchaseOrderID']);
                        
                if ($res['status']) {
                    ProcurementOrderService::savePoItem($value->itemCodeSystem, $input['companySystemID'], $input['purchaseOrderID'], $input['empID'], $input['employeeSystemID']);
                }
            }


            ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])->update(['isBulkItemJobRun' => 0]);
            

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error');
            Log::error($exception->getMessage());
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\DeliveryOrder;
use App\Models\ItemCategoryTypeMaster;
use App\Services\Sales\DeliveryOrderService;
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

class DeliveryOrderAddMutipleItemsJob implements ShouldQueue
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
                ->where('financeCategoryMaster', '!=' ,3)
                ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                    $query->where('financeCategoryMaster',$input['financeCategoryMaster']);
                })
                ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                    $query->where('financeCategorySub', $input['financeCategorySub']);
                })->whereHas('item_category_type', function ($query) {
                    $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::salesItems());
                })
                ->whereDoesntHave('deliveryOrderDetails', function($query) use ($input) {
                    $query->where('deliveryOrderID', $input['deliveryOrderID']);
                })
                ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory','itemAssigned'])
                ->get();

            $invalidItems = [];

            foreach ($itemMasters as $key => $value) {
                $res = DeliveryOrderService::validatePoItem($value->itemCodeSystem, $input['companySystemID'], $input['deliveryOrderID']);
                if ($res['status']) {
                    DeliveryOrderService::savePoItem($value->itemCodeSystem, $input['companySystemID'], $input['deliveryOrderID'], $input['empID'], $input['employeeSystemID']);
                }
            }


            DeliveryOrder::where('deliveryOrderID', $input['deliveryOrderID'])->update(['isBulkItemJobRun' => 0]);


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error');
            Log::error($exception->getMessage());
        }
    }
}

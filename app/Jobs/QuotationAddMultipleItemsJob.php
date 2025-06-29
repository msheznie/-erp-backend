<?php

namespace App\Jobs;

use App\Models\QuotationMaster;
use App\Models\ItemCategoryTypeMaster;
use App\Services\Sales\QuotationService;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuotationAddMultipleItemsJob implements ShouldQueue
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

        Log::useFiles(storage_path() . '/logs/quotation_bulk_item.log');

        CommonJobService::db_switch($db);
        $input = $this->data;

        DB::beginTransaction();
        try {

            $companyId = $input['companySystemID'];
            $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1);
            })->where('isActive',1)
                ->where('itemApprovedYN',1)
                ->where('financeCategoryMaster', '!=' ,3) // Exclude fixed assets
                ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                    $query->where('financeCategoryMaster',$input['financeCategoryMaster']);
                })
                ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                    $query->where('financeCategorySub', $input['financeCategorySub']);
                })->whereHas('item_category_type', function ($query) {
                    $query->whereIn('categoryTypeID', ItemCategoryTypeMaster::salesItems());
                })
                ->whereDoesntHave('quotationDetails', function($query) use ($input) {
                    $query->where('quotationMasterID', $input['quotationId']);
                })
                ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory','itemAssigned'])
                ->get();

            $invalidItems = [];

            foreach ($itemMasters as $key => $value) {
                $res = QuotationService::validateQuotationItem($value->itemCodeSystem, $input['companySystemID'], $input['quotationId']);
                if ($res['status']) {
                    QuotationService::saveQuotationItem($value->itemCodeSystem, $input['companySystemID'], $input['quotationId'], $input['empID'], $input['employeeSystemID']);
                }
            }

            QuotationMaster::where('quotationMasterID', $input['quotationId'])->update(['isBulkItemJobRun' => 0]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error in QuotationAddMultipleItemsJob');
            Log::error($exception->getMessage());
        }
    }
} 
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
use App\Models\MaterielRequest;
use App\Services\MaterialRequestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Jobs\AddBulkItem\ProcessMaterialRequestQuery;

class MaterialRequestAddBulkItemJob implements ShouldQueue
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

        Log::useFiles(storage_path() . '/logs/material_request_bulk_item.log');
        Log::info('---- Job  Start-----' . date('H:i:s'));

        CommonJobService::db_switch($db);
        $input = $this->data;

        DB::beginTransaction();
        try {

            $companyId = $input['companySystemID'];
            $isSearched = $input['isSearched'];
            $searchVal = $input['searchVal'];
            $chunkSize = 100;
         
            $financeCategoryMaster = isset($input['financeCategoryMaster'])?$input['financeCategoryMaster']:null;
            $financeCategorySub = isset($input['financeCategorySub'])?$input['financeCategorySub']:null;
         
            $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                                        return $query->where('companySystemID', '=', $companyId);
                                     })->where('isActive',1)
                                     ->where('itemApprovedYN',1)
                                     ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                                        $query->where('financeCategoryMaster', $input['financeCategoryMaster']);
                                     })
                                     ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                                        $query->where('financeCategorySub', $input['financeCategorySub']);
                                     })
                                     ->whereDoesntHave('material_request_details', function($query) use ($input) {
                                        $query->where('RequestID', $input['RequestID']);
                                     })
                                     ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory']);


                                    if ($isSearched) {
                                        $itemMasters = $itemMasters->where(function ($query) use ($searchVal) {
                                            $query->where('primaryCode', 'LIKE', "%{$searchVal}%")
                                                ->orWhere('secondaryItemCode', 'LIKE', "%{$searchVal}%")
                                                ->orWhere('barcode', 'LIKE', "%{$searchVal}%")
                                                ->orWhere('itemDescription', 'LIKE', "%{$searchVal}%");
                                        });
                                    }
            $count = $itemMasters->count();
                        
            $invalidItems = [];
            $chunkDataSizeCounts = ceil($count / $chunkSize);
            for ($i = 1; $i <= $chunkDataSizeCounts; $i++) {
                Log::info('started '.$i);
                ProcessMaterialRequestQuery::dispatch($i, $db, $companyId, $financeCategoryMaster,$financeCategorySub,$input['RequestID'],$chunkDataSizeCounts,$input['empID'], $input['employeeSystemID'],$isSearched,$searchVal)->onQueue('single');
            }

            Log::info('Successfully completed');

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error');
            Log::error($exception->getMessage());
        }
    }
}

<?php

namespace App\Jobs\AddBulkItem;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Jobs\AddBulkItem\ProcessMaterialRequestBulk;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\ItemMaster;
use App\Models\MaterielRequest;

class ProcessMaterialRequestQuery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $page;
    protected $dataBase;
    protected $companyId;
    protected $financeCategoryMaster;
    protected $financeCategorySub;
    protected $requestID;
    protected $empID;
    protected $employeeSystemID;
    protected $chunkDataSizeCounts;
    protected $isSearched;
    protected $searchVal;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page,$dataBase,$companyId,$financeCategoryMaster,$financeCategorySub,$requestID,$chunkDataSizeCounts,$empID,$employeeSystemID,$isSearched,$searchVal)
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


        $this->page = $page;
        $this->dataBase = $dataBase;
        $this->companyId = $companyId;
        $this->financeCategoryMaster = $financeCategoryMaster;
        $this->financeCategorySub = $financeCategorySub;
        $this->requestID = $requestID;
        $this->empID = $empID;
        $this->employeeSystemID = $employeeSystemID;
        $this->chunkDataSizeCounts = $chunkDataSizeCounts;
        $this->isSearched = $isSearched;
        $this->searchVal = $searchVal;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dataBase;
        CommonJobService::db_switch($db);
        DB::beginTransaction();
        try {

            $perPage = 100;
            $page = $this->page;
            $companyId = $this->companyId;
            $financeCategoryMaster = $this->financeCategoryMaster;
            $financeCategorySub = $this->financeCategorySub;
            $requestID = $this->requestID;
     
            $empID = $this->empID;
            $employeeSystemID = $this->employeeSystemID;
            $chunkDataSizeCounts = $this->chunkDataSizeCounts;

            $isSearched = $this->isSearched;
            $searchVal = $this->searchVal;

            $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                                        return $query->where('companySystemID', '=', $companyId)->where('isAssigned', '=', -1)->whereIn('categoryType', ['[{"id":1,"itemName":"Purchase"}]','[{"id":1,"itemName":"Purchase"},{"id":2,"itemName":"Sale"}]','[{"id":2,"itemName":"Sale"},{"id":1,"itemName":"Purchase"}]']);
                                     })->where('isActive',1)
                                     ->where('itemApprovedYN',1)
                                     ->when((isset($financeCategoryMaster) && $financeCategoryMaster), function($query) use ($financeCategoryMaster){
                                        $query->where('financeCategoryMaster', $financeCategoryMaster);
                                     })
                                     ->when((isset($financeCategorySub) && $financeCategorySub), function($query) use ($financeCategorySub){
                                        $query->where('financeCategorySub', $financeCategorySub);
                                     })
                                     ->whereDoesntHave('material_request_details', function($query) use ($requestID) {
                                        $query->where('RequestID', $requestID);
                                     })
                                     ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])
                                     ->orderBy('itemCodeSystem', 'desc')
                                     ->take($perPage);

                                     if ($isSearched) {
                                        $itemMasters = $itemMasters->where(function ($query) use ($searchVal) {
                                            $query->where('primaryCode', 'LIKE', "%{$searchVal}%")
                                                ->orWhere('secondaryItemCode', 'LIKE', "%{$searchVal}%")
                                                ->orWhere('barcode', 'LIKE', "%{$searchVal}%")
                                                ->orWhere('itemDescription', 'LIKE', "%{$searchVal}%");
                                        });
                                    }

                    $output = $itemMasters
                                ->get()
                                ->toArray();                   
                        
                if (count($output) > 0) {
                    ProcessMaterialRequestBulk::dispatch($db, $output, $companyId, $empID, $employeeSystemID,$chunkDataSizeCounts,$requestID)->onQueue('single');
                } else {

                    MaterielRequest::where('RequestID', $requestID)->update(['isBulkItemJobRun' => 0]);               
                 }
            
            

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

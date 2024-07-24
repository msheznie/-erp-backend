<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemAssigned;
use App\Models\AssetFinanceCategory;
use App\Models\GRVDetails;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;
use App\helper\PurcahseRequestDetail;
use App\Http\Controllers\AppBaseController;
use App\helper\CommonJobService;
use App\Jobs\PrBulkBulkItemQuery;

class PrBulkBulkItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $data;
    public $dispatch_db;
    public $timeout = 500;
    public function __construct($input,$dispatch_db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
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
        Log::useFiles(storage_path() . '/logs/pr_bulk_item.log');
        CommonJobService::db_switch($db);

        $input = $this->data;
        DB::beginTransaction();
        try {
        $base_controller = app()->make(AppBaseController::class);
        $input = $base_controller->convertArrayToSelectedValue($input, ['financeCategoryMaster', 'financeCategorySub']);

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
                                          ->first();

        if (!$purchaseRequest) {
            Log::error('PR not found');
            return;
        }
        $budgetYear = $purchaseRequest->budgetYear;
        $companyID = $purchaseRequest->companyID;
            
        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                                                    ->where('companySystemID', $purchaseRequest->companySystemID)
                                                    ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;
            if ($policy == 0) {
                if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                    return ['status' => false , 'message' => 'Category is not found.'];
                }
                $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                    ->first();
                if ($pRDetailExistSameItem) {
                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                        return ['status' => false , 'message' => 'You cannot add different category item'];
                    }
                }
            }
        }
           
        $companyId = $input['companySystemID'];
        $isSearched = $input['isSearched'];
        $searchVal = $input['searchVal'];
        $chunkSize = 100;

        $financeCategoryMaster = isset($input['financeCategoryMaster'])?$input['financeCategoryMaster']:null;
        $financeCategorySub = isset($input['financeCategorySub'])?$input['financeCategorySub']:null;

        $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                                    return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1)->whereIn('categoryType', ['[{"id":1,"itemName":"Purchase"}]','[{"id":1,"itemName":"Purchase"},{"id":2,"itemName":"Sale"}]','[{"id":2,"itemName":"Sale"},{"id":1,"itemName":"Purchase"}]']);
                                 })->where('isActive',1)
                                 ->where('itemApprovedYN',1)
                                 ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                                    $query->where('financeCategoryMaster', $input['financeCategoryMaster']);
                                 })
                                 ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                                    $query->where('financeCategorySub', $input['financeCategorySub']);
                                 })
                                 ->whereDoesntHave('purchase_request_details', function($query) use ($input) {
                                    $query->where('purchaseRequestID', $input['purchaseRequestID']);
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

        $chunkDataSizeCounts = ceil($count / $chunkSize);
        for ($i = 1; $i <= $chunkDataSizeCounts; $i++) {
            PrBulkBulkItemQuery::dispatch($i, $db, $companyId, $financeCategoryMaster,$financeCategorySub,$input['purchaseRequestID'],$chunkDataSizeCounts,$isSearched,$searchVal,$budgetYear)->onQueue('single');
        }
                                
                                
        DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error');
            Log::error($exception->getMessage());
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

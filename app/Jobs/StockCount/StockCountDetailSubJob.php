<?php

namespace App\Jobs\StockCount;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;
use App\Models\StockCountDetail;
use App\Models\StockCount;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\helper\Helper;
use App\helper\inventory;

class StockCountDetailSubJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $db;
     protected $dataSubArray;
     protected $count;

    public function __construct($db, $dataSubArray, $count)
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
        $this->db = $db;
        $this->dataSubArray = $dataSubArray;
        $this->count = $count;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $db = $this->db;

        CommonJobService::db_switch($db);

        Log::useFiles(storage_path().'/logs/stock_count_job.log');

        Log::info("Starting Sub Job". $db);

        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);

        $dataSubArray = $this->dataSubArray;

        $itemCodeSystem = $dataSubArray['itemCodeSystem'];
        $stockCount = $dataSubArray['stockCount'];
        $companySystemID = $dataSubArray['companySystemID'];
        $count = $this->count;
        $stockCountAutoID = $dataSubArray['stockCountAutoID'];

        $isValid = true;


        $stockCounter = StockCount::where('stockCountAutoID',$stockCountAutoID)->first();
        $item = ItemAssigned::where('itemCodeSystem', $itemCodeSystem)
        ->where('companySystemID', $companySystemID)
        ->first();

        if (!empty($item)) {
            $input['stockCountAutoIDCode'] = $stockCount->stockCountCode;
            $input['stockCountAutoID'] = $stockCount->stockCountAutoID;
            $input['comments'] = null;
            $input['noQty'] = null;

            $input['itemCodeSystem'] = $item->itemCodeSystem;
            $input['itemPrimaryCode'] = $item->itemPrimaryCode;
            $input['itemDescription'] = $item->itemDescription;
            $input['itemUnitOfMeasure'] = $item->itemUnitOfMeasure;
            $input['partNumber'] = $item->secondaryItemCode;
            $input['itemFinanceCategoryID'] = $item->financeCategoryMaster;
            $input['itemFinanceCategorySubID'] = $item->financeCategorySub;

            $checkWhether = StockCount::where('stockCountAutoID', '!=', $stockCount->stockCountAutoID)
                                        ->where('companySystemID', $companySystemID)
                                        ->where('location', $stockCount->location)
                                        ->select([
                                            'stockCountAutoID',
                                            'companySystemID',
                                            'location',
                                            'stockCountCode',
                                            'approved'
                                        ])
                                        ->whereHas('details', function ($query) use ($itemCodeSystem, $input) {
                                            $query->where('itemCodeSystem', $itemCodeSystem);
                                        })
                                        ->where('approved', 0)
                                        ->where('refferedBackYN', 0)
                                        ->first();

            if (!empty($checkWhether)) {
                $isValid = false;
                Log::info( "Item ".$item->itemDescription." cannot be used, Since, Stock Count (" . $checkWhether->stockCountCode . ") pending for approval with this item.");
            }

            $data = array('companySystemID' => $companySystemID,
                        'itemCodeSystem' => $itemCodeSystem,
                        'wareHouseId' => $stockCount->location);

            $input['currentWacLocalCurrencyID'] = $item->wacValueLocalCurrencyID;
            $input['currentWacRptCurrencyID'] = $item->wacValueReportingCurrencyID;

            $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($data);
            $input['currenctStockQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];
            $input['systemQty'] = $itemCurrentCostAndQty['currentWareHouseStockQty'];

            $input['wacAdjRpt'] = $itemCurrentCostAndQty['wacValueReporting'];
            $input['currentWacRpt'] = $itemCurrentCostAndQty['wacValueReporting'];


            $companyCurrencyConversion = Helper::currencyConversion($stockCount->companySystemID,$item->wacValueReportingCurrencyID,$item->wacValueReportingCurrencyID,$itemCurrentCostAndQty['wacValueReporting']);

            $input['currentWaclocal'] = $companyCurrencyConversion['localAmount'];
            $input['wacAdjLocal'] = $companyCurrencyConversion['localAmount'];
            $input['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];
            $input['wacAdjLocalER'] = 1;

            $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
                                                                        ->where('mainItemCategoryID', $input['itemFinanceCategoryID'])
                                                                        ->where('itemCategorySubID', $input['itemFinanceCategorySubID'])
                                                                        ->first();

            if (!empty($financeItemCategorySubAssigned)) {
                $input['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                $input['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                $input['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                $input['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                $input['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
            } else {
                $isValid = false;
                Log::info("Item ".$item->itemDescription." cannot be used, Since, Account code not updated.");
            }

            if (!isset($input['financeGLcodebBS']) || !isset($input['financeGLcodebBSSystemID']) || !isset($input['financeGLcodePL']) || !isset($input['financeGLcodePLSystemID'])) {
                $isValid = false;
                Log::info("Item ".$item->itemDescription." cannot be used, Since, Account code not updated.");
            }

            if ($input['itemFinanceCategoryID'] == 1) {
                $alreadyAdded = StockCount::where('stockCountAutoID', $input['stockCountAutoID'])
                                            ->whereHas('details', function ($query) use ($itemCodeSystem) {
                                                $query->where('itemCodeSystem', $itemCodeSystem);
                                            })
                                            ->first();

                if ($alreadyAdded) {
                    $isValid = false;
                    Log::info("Item ".$item->itemDescription." cannot be used, Since, Item is already added.");
                }
            }
            if($isValid)
            {
                $res = StockCountDetail::create($input);
            }
           

        }

        $newCounterValue = 0;
        if(isset($stockCounter)) {
            $stockCounter->counter += 1;
            $stockCounter->save();
            $newCounterValue = $stockCounter->counter;
        }

        Log::info('new value '.$newCounterValue);
        if ($newCounterValue == $count) {
            StockCount::where('stockCountAutoID', $stockCountAutoID)->update(['detailStatus' => 1]);
        }


    }
}

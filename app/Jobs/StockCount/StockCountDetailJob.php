<?php

namespace App\Jobs\StockCount;

use App\Models\StockCount;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;
use App\Jobs\StockCount\StockCountDetailSubJob;
use App\Models\ItemAssigned;

class StockCountDetailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $db;
    protected $stockCount;
    protected $companyId;
    protected $autoId;
    protected $skipItemIds;
    protected $dataArray;

    public function __construct($db, $dataArray)
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
        $this->dataArray = $dataArray;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $db = $this->db;
        $dataArray = $this->dataArray;

        CommonJobService::db_switch($db);

        Log::useFiles(storage_path().'/logs/stock_count_job.log');

        Log::info($db);

        $companyId = $dataArray['companySystemID'];
        $skipItemIds = $dataArray['skipItemIds'];
        $stockCountAutoID = $dataArray['stockCountAutoID'];
        $stockCount = $dataArray['stockCount'];



        $finalItems = ItemAssigned::where('companySystemID', $companyId)
        ->where('financeCategoryMaster', 1)
        ->whereNotIn('itemCodeSystem', $skipItemIds)
        ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode'])
        ->get();

        $count = count($finalItems);
        Log::info('count '.$count);

        if($count == 0)
        {
            StockCount::where('stockCountAutoID', $stockCountAutoID)->update(['detailStatus' => 1]);
        }
        foreach ($finalItems as $key => $value) {
            $dataSubArray = array(
                'itemCodeSystem' => $value->itemCodeSystem,
                'stockCount' => $stockCount,
                'companySystemID' => $companyId,
                'stockCountAutoID' => $stockCountAutoID
            );

            StockCountDetailSubJob::dispatch($db, $dataSubArray, $count)->onQueue('single');
        }

        
    }
}

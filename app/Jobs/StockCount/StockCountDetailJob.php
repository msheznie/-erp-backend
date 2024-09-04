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

    public function __construct($db,$stockCount,$companyId,$autoId,$skipItemIds)
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
        $this->stockCount = $stockCount;
        $this->companyId = $companyId;
        $this->autoId = $autoId;
        $this->skipItemIds = $skipItemIds;
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
        $stockCount = $this->stockCount;
        $db = $this->db;
        $companyId = $this->companyId;
        $autoId = $this->autoId;
        $skipItemIds = $this->skipItemIds;
        CommonJobService::db_switch($db);

        Log::useFiles(storage_path().'/logs/stock_count_job.log');
  


        $finalItems = ItemAssigned::where('companySystemID', $companyId)
        ->where('financeCategoryMaster', 1)
        ->whereNotIn('itemCodeSystem', $skipItemIds)
        ->select(['itemPrimaryCode', 'itemDescription', 'itemCodeSystem', 'secondaryItemCode'])
        ->get();

        $count = count($finalItems);
        if($count == 0)
        {
            StockCount::where('stockCountAutoID', $autoId)->update(['detailStatus' => 1]);
        }
        foreach ($finalItems as $key => $value) {
            StockCountDetailSubJob::dispatch($db, $value->itemCodeSystem, $stockCount, $companyId,$count,$autoId)->onQueue('single');
        }

        
    }
}

<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\FixedAssetDepreciationPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DepreciationInsert
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $outputChunkData;
    public $outputData;


    public function __construct($dispatch_db, $outputData)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->outputData = $outputData;
    }


    public function handle()
    {
        ini_set('max_execution_time', 6000);
        ini_set('memory_limit', -1);
        $db = $this->dispatch_db;
        $output = $this->outputData;


        CommonJobService::db_switch($db);

        foreach ($output as $t) {

            FixedAssetDepreciationPeriod::insert($t);

        }



    }


}

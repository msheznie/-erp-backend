<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Services\ProcurementOrder\ExportPODetailExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ExportDetailedPoList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $dispatch_db;
    public $userLang;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $input, $userLang = 'en')
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
        $this->userLang = $userLang;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        try {
            (new ExportPODetailExcel($this->data, $this->userLang))->export();
        } catch (\Exception $e) {
            Log::channel('po_detail_excel_export')->error('Export failed.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

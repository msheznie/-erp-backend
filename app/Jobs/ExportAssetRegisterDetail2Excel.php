<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Services\AssetManagement\AssetRegister\ExportAssetRegisterDetail2ExcelService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ExportAssetRegisterDetail2Excel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $dispatch_db;
    public $userLang;

    public function __construct($dispatch_db, $input, $userLang = 'en')
    {
        if (env('QUEUE_DRIVER_CHANGE', 'database') == 'database') {
            if (env('IS_MULTI_TENANCY', false)) {
                self::onConnection('database_main');
            } else {
                self::onConnection('database');
            }
        } else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE', 'database'));
        }

        $this->data = $input;
        $this->dispatch_db = $dispatch_db;
        $this->userLang = $userLang;
    }

    public function handle()
    {
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        try {
            (new ExportAssetRegisterDetail2ExcelService($this->data, $this->userLang))->exportAndNotify();
        } catch (\Exception $e) {
            Log::channel('asset_register_detail2_excel_export')->error('Export failed.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}


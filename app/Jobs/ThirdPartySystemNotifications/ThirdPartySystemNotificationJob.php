<?php

namespace App\Jobs\ThirdPartySystemNotifications;

use App\Jobs\Report\GenerateGlPdfReport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Company;
use App\Models\CurrencyMaster;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\WebPushNotificationService;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use File;

class ThirdPartySystemNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $documentCode;
    public $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $documentCode,$userId)
    {
        if (env('IS_MULTI_TENANCY', false)) {
            self::onConnection('database_main');
        } else {
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->documentCode = $documentCode;
        $this->userId = $userId;

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

        $data = [];
        $type = 'user';

        switch ($this->documentCode) {
            case 107:
                $data = [
                    'title' => 'Supplier Registration',
                    'body' => 'Supplier Registration is approved.',
                    'url' => "/suppliers/KYC",
                ];
                $type = 'supplier';
                break;

            default:
                Log::error('Document id not found in third party notifications' . date('H:i:s'));
                break;
        }

        WebPushNotificationService::sendNotification($data, 4, $this->userId,"",$type);

        return true;

    }
}

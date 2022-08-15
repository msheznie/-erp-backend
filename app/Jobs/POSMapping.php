<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseRequest;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;
use App\helper\PurcahseRequestDetail;
use App\Http\Controllers\AppBaseController;
use App\helper\CommonJobService;
use App\Services\POSService;

class POSMapping implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $data;
    public $mappingId;
    public $logId;
    public $dispatch_db;
    public $timeout = 500;
    public function __construct($logId, $dispatch_db)
    {
        if (env('IS_MULTI_TENANCY', false)) {
            self::onConnection('database_main');
        } else {
            self::onConnection('database');
        }

        $this->logId = $logId;
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
        CommonJobService::db_switch($db);
        POSService::createSourceTransaction($this->logId);
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

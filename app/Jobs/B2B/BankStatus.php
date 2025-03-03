<?php

namespace App\Jobs\B2B;

use App\helper\CommonJobService;
use App\Models\BankConfig;
use App\Models\Deligation;
use App\Models\EmployeeNavigation;
use App\Models\EmployeesDepartment;
use App\Models\PaymentBankTransfer;
use App\Models\UserGroup;
use App\Models\UserGroupAssign;
use App\Services\B2B\BankConfigService;
use App\Services\B2B\CheckBankStatusService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;

class BankStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tenantDb;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb)
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

        $this->tenantDb = $tenantDb;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->tenantDb);
        $batchConfigService = new CheckBankStatusService($this->tenantDb);
        $batchConfigService->updateStatusOfFilesFromSuccessPath();
        $batchConfigService->updateStatusOfFilesFromfailurePath();
    }
}

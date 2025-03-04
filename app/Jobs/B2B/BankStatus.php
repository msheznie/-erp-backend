<?php

namespace App\Jobs\B2B;

use App\helper\CommonJobService;
use App\Models\BankConfig;
use App\Models\PaymentBankTransfer;
use App\Services\B2B\BankTransferService;
use App\Services\B2B\CheckBankStatusService;
use App\Services\WebPushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $this->updateStatusOfFilesFromSuccessPath();
        $this->updateStatusOfFilesFromfailurePath();

    }

    public function updateStatusOfFilesFromSuccessPath()
    {
        $this->updateStatusFromPath('success_path', 1);
    }

    public function updateStatusOfFilesFromFailurePath()
    {
        $this->updateStatusFromPath('failure_path', 0, 2);
    }

    private function updateStatusFromPath(string $path, int $portalStatus, int $submittedStatus = null)
    {
        try {
            $getConfigDetails = BankConfig::where('slug', 'ahlibank')->first();

            if (!$getConfigDetails) return;

            $config = collect($getConfigDetails['details'])->where('fileType', 0)->first();
            $this->bankTransferService = new BankTransferService();
            $configDetails = [
                'driver'   => 'sftp',
                'host'     => $config['connectionDetails']['host'] ?? '',
                'username' => $config['connectionDetails']['username'] ?? '',
                'password' => $config['connectionDetails']['password'] ?? '',
                'port'     => $config['connectionDetails']['port'] ?? 22,
                'root'     => $config['connectionDetails']['root'] ?? '/',
                'timeout'  => 50,
            ];
            config(['filesystems.disks.sftp' => $configDetails]);
            $storage = \Storage::disk('sftp');
            $paymentTransfers = PaymentBankTransfer::whereNotNull('batchReference')
                ->select(['paymentBankTransferID', 'batchReference', 'portalStatus'])
                ->get();

            if (empty($config[$path])) return;

            $disk = $storage;
            $files = $disk->files($config[$path]);

            if (empty($files)) return;

            foreach ($paymentTransfers as $paymentTransfer) {
                foreach ($files as $file) {
                    try {
                        $fileContent = file_get_contents($file);
                        $batchReference = preg_quote($paymentTransfer->batchReference, '/');
                        $pattern = "/Batch Number:\s*" . $batchReference . "/";

                        if (preg_match($pattern, $fileContent)) {
                            $paymentTransfer->portalStatus = $portalStatus;
                            if ($submittedStatus !== null) {
                                $paymentTransfer->submittedStatus = $submittedStatus;
                            }
                            $paymentTransfer->save();

                            if (isset($this->db)) {
                                $webPushData = [
                                    'title' => "Bank Transfer portal status updated",
                                    'body' => "",
                                    'url' => "treasury/bank-transfer-list",
                                    'path' => "",
                                ];
                                WebPushNotificationService::sendNotification($webPushData, 2, [$paymentTransfer->createdUserSystemID], $this->db);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error processing file {$batchReference}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error in processing bank transfers: " . $e->getMessage());
        }
    }
}

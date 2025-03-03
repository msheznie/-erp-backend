<?php

namespace App\Services\B2B;

use App\helper\CommonJobService;
use App\Models\BankConfig;
use App\Models\PaymentBankTransfer;
use App\Services\WebPushNotificationService;

class CheckBankStatusService
{
    private $configDetails;

    private $bankTransferService;

    private $pathDetails;

    private $storage;

    private $db;

    public function __construct($db)
    {

        $this->db = $db;
        CommonJobService::db_switch($db);

        $getConfigDetails = BankConfig::where('slug', 'ahlibank')->first();
        $config = collect($getConfigDetails['details'])->where('fileType', 0)->first();
        $this->pathDetails = $getConfigDetails;
        $this->bankTransferService = new BankTransferService();
        $this->configDetails = [
            'driver'   => 'sftp',
            'host'     => $config['connectionDetails']['host'] ?? '',
            'username' => $config['connectionDetails']['username'] ?? '',
            'password' => $config['connectionDetails']['password'] ?? '',
            'port'     => $config['connectionDetails']['port'] ?? 22,
            'root'     => $config['connectionDetails']['root'] ?? '/',
            'timeout'  => 50,
        ];
        config(['filesystems.disks.sftp' => $this->configDetails]);
        $this->storage = \Storage::disk('sftp');
    }

    private function updateStatusFromPath(string $path, int $portalStatus, int $submittedStatus = null)
    {
        $paymentTransfers = PaymentBankTransfer::whereNotNull('batchReference')
            ->select(['paymentBankTransferID', 'batchReference', 'portalStatus'])
            ->get();

        $configDetails = BankConfig::where('slug', 'ahlibank')->first();
        if (!$configDetails) return;
        $config = collect($configDetails['details'])->where('fileType', 0)->first();
        if (empty($config[$path])) return;
        $disk = $this->storage;
        if($path == "success_path")
        {
            $files = $disk->files($config[$path]);
        }else {
            $files = $disk->files($config[$path]);
        }


        if (empty($files)) return;

        foreach ($paymentTransfers as $paymentTransfer) {
            foreach ($files as $file) {
                $fileContent = file_get_contents($file);
                $batchReference = preg_quote($paymentTransfer->batchReference, '/');
                $pattern = "/Batch Number:\s*" . $batchReference . "/";

                if (preg_match($pattern, $fileContent)) {
                    $paymentTransfer->portalStatus = $portalStatus;
                    if ($submittedStatus !== null) {
                        $paymentTransfer->submittedStatus = $submittedStatus;
                    }
                    $paymentTransfer->save();

                    if(isset($this->db))
                    {
                        $webPushData = [
                            'title' => "Bank Transfer portal status updated",
                            'body' => "",
                            'url' => "treasury/bank-transfer-list",
                            'path' => "",
                        ];
                        WebPushNotificationService::sendNotification($webPushData, 2, [$paymentTransfer->createdUserSystemID], $this->db);
                    }
                }

            }
        }
    }

    public function updateStatusOfFilesFromSuccessPath()
    {
        $this->updateStatusFromPath('success_path', 1);
    }

    public function updateStatusOfFilesFromFailurePath()
    {
        $this->updateStatusFromPath('failure_path', 0, 2);
    }

}

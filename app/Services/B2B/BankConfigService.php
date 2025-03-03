<?php

namespace App\Services\B2B;

use App\Models\BankConfig;
use App\Models\PaymentBankTransfer;
use App\Services\WebPushNotificationService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BankConfigService
{
    private $configDetails;

    private $bankTransferService;

    private $pathDetails;

    private $storage;

    public function __construct()
    {
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

    private function updateStatusFromPath(string $path, int $portalStatus, int $submittedStatus = null,$database = null)
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

                    if(isset($database))
                    {
                        $webPushData = [
                            'title' => "Bank Transfer portal status updated",
                            'body' => "",
                            'url' => "treasury/bank-transfer-list",
                            'path' => "",
                        ];
                        WebPushNotificationService::sendNotification($webPushData, 2, [$paymentTransfer->createdUserSystemID], $database);
                    }
                }

            }
        }
    }

    public function updateStatusOfFilesFromSuccessPath($database)
    {
        $this->updateStatusFromPath('success_path', 1,null,$database);
    }

    public function updateStatusOfFilesFromFailurePath($database)
    {
        $this->updateStatusFromPath('failure_path', 0, 2,$database);
    }

    public function uploadFileToBank($fileName,$bankTransferID)
    {
        $disk = $this->storage;
        try {

            if(!isset($this->configDetails))
                throw new \Exception("The vendor file format is not available for the selected bank");

            if(!isset($this->pathDetails) || !isset($this->pathDetails->details[0]['upload_path']))
                throw new \Exception("Upload path not found!");

            $filePath = storage_path('app/temp/'.$fileName).'.xlsx';

            $remotePath = $this->pathDetails->details[0]['upload_path']."/".$fileName.'.xlsx';
            if (file_exists($filePath)) {
                $disk->put($remotePath,file_get_contents($filePath));
                $this->bankTransferService->updateStatus($bankTransferID,'success');
            } else {
                $this->bankTransferService->updateStatus($bankTransferID,'failed');
            }

        }catch (\Exception $exception)
        {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function downloadErrorLogFile($bankTransferID)
    {
        $supplierBankTransfer = PaymentBankTransfer::find($bankTransferID);
        $getConfigDetails = BankConfig::where('slug','ahlibank')->where('bank_master_id',$supplierBankTransfer->bankMasterID)->first();
        if(!isset($getConfigDetails))
            throw new \Exception("The vendor file format is not available for the selected bank");

        $config = collect($getConfigDetails['details'])->where('fileType',0)->first();
        if($config['failure_path'])
        {
            try {
                $disk = $this->storage;
                $files = $disk->files($config['failure_path']);
                foreach ($files as $file)
                {
                    $filePath = $file->getRealPath();
                    $file = file_get_contents($filePath);
                    $batchReference = preg_quote($supplierBankTransfer->batchReference, '/'); // Escape special characters
                    $pattern = "/Batch Number:\s*" . $batchReference . "/"; // Proper regex with delimiters
                    if (preg_match($pattern, $file, $matches)) {
                        return response()->file($filePath);
                    }
                }
            }catch (\Exception $exception)
            {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
    }
}

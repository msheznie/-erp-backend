<?php

namespace App\Services\B2B;

use App\Models\BankConfig;
use App\Models\PaymentBankTransfer;

class BankConfigService
{
    private $configDetails;

    private $bankTransferService;

    private $pathDetails;

    private $storage;

    private $db;

    public function __construct(BankTransferService $bankTransferService)
    {
        $this->bankTransferService = $bankTransferService;
        $this->setBankConfig();
    }

    public function setBankConfig()
    {
        $getConfigDetails = BankConfig::where('slug', 'ahlibank')->first();
        $config = collect($getConfigDetails['details'])->where('fileType', 0)->first();
        $this->pathDetails = $getConfigDetails;
        $this->configDetails = [
            'driver' => 'sftp',
            'host' => $config['connectionDetails']['host'] ?? '',
            'username' => $config['connectionDetails']['username'] ?? '',
            'password' => $config['connectionDetails']['password'] ?? '',
            'port' => $config['connectionDetails']['port'] ?? 22,
            'root' => $config['connectionDetails']['root'] ?? '/',
            'timeout' => 50,
        ];
        config(['filesystems.disks.sftp' => $this->configDetails]);
        $this->storage = \Storage::disk('sftp');
    }

    public function uploadFileToBank($fileName, $bankTransferID)
    {
        $disk = $this->storage;
        try {

            if (!isset($this->configDetails))
                throw new \Exception("The vendor file format is not available for the selected bank");

            if (!isset($this->pathDetails) || !isset($this->pathDetails->details[0]['upload_path']))
                throw new \Exception("Upload path not found!");

            $filePath = storage_path('app/temp/' . $fileName) . '.xlsx';

            $remotePath = $this->pathDetails->details[0]['upload_path'] . "/" . $fileName . '.xlsx';
            if (file_exists($filePath)) {
                $disk->put($remotePath, file_get_contents($filePath));
                $this->bankTransferService->updateStatus($bankTransferID, 'success');
            } else {
                $this->bankTransferService->updateStatus($bankTransferID, 'failed');
            }

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }


    public function downloadErrorLogFile($bankTransferID)
    {
        $supplierBankTransfer = PaymentBankTransfer::find($bankTransferID);
        $getConfigDetails = BankConfig::where('slug', 'ahlibank')->where('bank_master_id', $supplierBankTransfer->bankMasterID)->first();
        if (!isset($getConfigDetails))
            throw new \Exception("The vendor file format is not available for the selected bank");

        $config = collect($getConfigDetails['details'])->where('fileType', 0)->first();
        if ($config['failure_path']) {
            try {
                $disk = $this->storage;
                $files = $disk->files($config['failure_path']);
                foreach ($files as $file) {
                    $filePath = $file->getRealPath();
                    $file = file_get_contents($filePath);
                    $batchReference = preg_quote($supplierBankTransfer->batchReference, '/'); // Escape special characters
                    $pattern = "/Batch Number:\s*" . $batchReference . "/"; // Proper regex with delimiters
                    if (preg_match($pattern, $file, $matches)) {
                        return response()->file($filePath);
                    }
                }
            } catch (\Exception $exception) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
    }
}

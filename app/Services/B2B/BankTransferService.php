<?php

namespace App\Services\B2B;

use App\Models\BankConfig;
use App\Models\Company;
use App\Models\PaymentBankTransfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Illuminate\Support\Facades\File;

class BankTransferService
{
    public static function getBankTransferType($transferDetails)
    {
        if (!isset($transferDetails))
            throw new \Exception("Trasnfer details not found");

        if (!isset($transferDetails['from']))
            throw new \Exception("From bank details not found");

        if (!isset($transferDetails['to']))
            throw new \Exception("To bank details not found");


        if ($transferDetails['from']['bankID'] == $transferDetails['to']['bankID']) {
            return "TRF";
        }

    }


    public function generateBatchNo($companyID, $documentCode = null, $documentDate, $field, $bankTransferID)
    {
        if (!isset($bankTransferID))
            return new \Exception("Cannot generate Doc Code");


        $parts = explode('\\', $documentCode);

        $latest = PaymentBankTransfer::find($bankTransferID);
        if ($field == "header") {
            if (!is_null($latest->batchReference)) {
                $array = explode('\\', $latest->batchReference);
                $nextNumber = !is_null($latest->batchReference) ? end($array) + 1 : 1;
            } else {
                $nextNumber = 1;
            }

        } else {

            if (!is_null($latest->batchReferencePV)) {
                $array = explode('\\', $latest->batchReferencePV);
                $nextNumber = !is_null($latest->batchReferencePV) ? end($array) + 1 : 1;
            } else {
                $nextNumber = 1;
            }

        }
        $lastPart = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $output = Carbon::make($documentDate)->year . '\\' . end($parts) . '\\' . $lastPart;

        return $output;
    }

    public function updateStatus($bankTransferID, $status)
    {
        $bankTransfer = PaymentBankTransfer::find($bankTransferID);

        $bankTransfer->submittedDate = Carbon::now();
        switch ($status) {
            case "success" :
                $bankTransfer->submittedStatus = 1;
                break;
            case "failed" :
                $bankTransfer->submittedStatus = 0;
                break;
            case "resubmitted" :
                $bankTransfer->submittedStatus = 2;
                break;
        }
        $bankTransfer->portalStatus = NULL;
        $bankTransfer->save();


    }


}

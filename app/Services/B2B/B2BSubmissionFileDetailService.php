<?php

namespace App\Services\B2B;

use App\Models\B2BSubmissionFileDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

class B2BSubmissionFileDetailService
{

    public function generateDownloadRecord($bankTransferID, $generationDate)
    {

        $submisisonFileDetail = B2BSubmissionFileDetail::where('document_date', Carbon::parse($generationDate)->format('Y-m-d'))->max('latest_downloaded_id');

        try {
            if (is_null($submisisonFileDetail) || $submisisonFileDetail == 0) {
                $result = B2BSubmissionFileDetail::create([
                    'bank_transfer_id' => $bankTransferID,
                    'document_date' => Carbon::now(),
                    'latest_downloaded_id' => 1
                ]);
            } else {
                $lastestSubmitedID = $submisisonFileDetail + 1;
                $res = B2BSubmissionFileDetail::where('bank_transfer_id',$bankTransferID)->first();

                if($res)
                {
                    $res->document_date = Carbon::now();
                    $res->latest_downloaded_id = $lastestSubmitedID;
                    $res->save();
                    $result = $res;
                }else {
                    $result = B2BSubmissionFileDetail::create([
                        'bank_transfer_id' => $bankTransferID,
                        'document_date' => Carbon::now(),
                        'latest_downloaded_id' => $lastestSubmitedID
                    ]);
                }
            }

            if (!$result)
                throw new \Exception("Cannot generate download file record");

            return $result;
        } catch (\Exception $exception) {
            return $exception;
        }

    }


    public function generateSubmittedReccord($bankTransferID,$generationDate)
    {
        $submisisonFileDetail = B2BSubmissionFileDetail::where('document_date', Carbon::parse($generationDate)->format('Y-m-d'))->max('latest_submitted_id');

        try {
            if (is_null($submisisonFileDetail) || $submisisonFileDetail == 0) {
                $result = B2BSubmissionFileDetail::create([
                    'bank_transfer_id' => $bankTransferID,
                    'document_date' => Carbon::now(),
                    'latest_submitted_id' => 1,
                    'submittedBy' => Auth::user()->employee_id
                ]);
            } else {
                $lastestSubmitedID = $submisisonFileDetail + 1;
                $result = B2BSubmissionFileDetail::create([
                    'bank_transfer_id' => $bankTransferID,
                    'document_date' => Carbon::now(),
                    'latest_submitted_id' => $lastestSubmitedID,
                    'submittedBy' => Auth::user()->employee_id
                ]);

            }

            if (!$result)
                throw new \Exception("Cannot generate download file record");

            return $result;
        } catch (\Exception $exception) {
            return $exception;
        }
    }

    public function getSubmissionFileName($bankTransferID,$generationDate)
    {
        $submisisonFileDetail = B2BSubmissionFileDetail::where('document_date', Carbon::parse($generationDate)->format('Y-m-d'))->max('latest_submitted_id');

        return "VENPAYMEOI_".Carbon::now()->format('dmY')."_".str_pad($submisisonFileDetail + 1, 3, '0', STR_PAD_LEFT).".txt";

    }

    public function getFileNameByPath($detail,$path)
    {
        if ($path == "success_path") {
            $filename = "VENPAYMEOI_" . Carbon::parse($detail->document_date)->format('dmY') . "_" . str_pad($detail->latest_submitted_id, 3, '0', STR_PAD_LEFT) . "_ACK.txt";
        }

        if ($path == "failure_path") {
            $filename = "VENPAYMEOI_" . Carbon::parse($detail->document_date)->format('dmY') . "_" . str_pad($detail->latest_submitted_id, 3, '0', STR_PAD_LEFT) . "_NACK.txt";
        }

        return $filename;
    }

    public function convertErrorLogToReadableFormat($csvData)
    {
        $lines = explode("\r\n", $csvData);
        $headers = str_getcsv(array_shift($lines)); // Extract headers

        $records = [];
        foreach ($lines as $line) {
            $data = str_getcsv($line);
            if (count($data) === count($headers)) {
                $records[] = [
                    'status' => $data[0],
                    'file_name' => $data[1],
                    'batch_reference_no' => $data[2],
                    'reason' => $data[3],
                ];
            }
        }

       return ['headers' => $headers, 'data' => $records];
    }
}

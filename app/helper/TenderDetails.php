<?php

namespace App\helper;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;

class TenderDetails
{

    public static function validateTenderEdit($id)
    {

        $tenderObj = self::getTenderMasterData($id);

        if(!isset($tenderObj))
        {
            return false;
        }

        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $version_id = $tenderObj->getOriginal('tender_edit_version_id');
        if (!isset($date) && !isset($version_id)) {
            return false;
        }

        $currentDate = Carbon::now()->format('Y-m-d H:i:s');
        $openingDateFormat = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        $result = $openingDateFormat->gt($currentDate);

   
        if (!$result) {
            return false;
        }

        $tendeEditLog = self::getDocumentModifyRequest($id);

        if (!isset($tendeEditLog)) {
            return false;
        }

        return true;
    }

    public static function getTenderMasterData($id)
    {
        return TenderMaster::select('id', 'bid_submission_opening_date', 'tender_edit_version_id','tender_type_id','document_system_id')
            ->where('id', $id)
            ->first();
    }

    public static function getDocumentModifyRequest($id)
    {

        return DocumentModifyRequest::select('id')
            ->where('documentSystemCode', $id)
            ->where('status', 1)
            ->where('approved', -1)
            ->orderBy('id', 'desc')->first();
    }
}

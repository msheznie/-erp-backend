<?php

namespace App\helper;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
class TenderDetails
{

    public static function getTenderMasterData($id)
    {

        return TenderMaster::where('id',$id)->select('id','bid_submission_opening_date','tender_edit_version_id')->first();
      

    }

    public static function validateTenderEdit($tender_id)
    {
        $tenderObj = TenderMaster::where('id',$tender_id)->select('id','bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $id = $tenderObj->getOriginal('tender_edit_version_id');

      
        if(isset($date) && isset($id))
        {
            $currentDate =  Carbon::now()->format('Y-m-d H:i:s');
            $openingDateFormat = Carbon::createFromFormat('Y-m-d H:i:s', $date);
            $result = $openingDateFormat->gt($currentDate);
      
            if($result)
            {      
                $tendeEditLog = DocumentModifyRequest::where('documentSystemCode',$tender_id)->where('status',1)->where('approved',-1)->orderBy('id','desc')->first();
                if(isset($tendeEditLog))
                {
                    return true;
                }
    
                return false;
    
            }
                return false;
        }
        else
        {
            return false;
            Log::info('not valid');
        }
    }



}

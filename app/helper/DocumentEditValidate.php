<?php

namespace App\helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentModifyRequest;
use App\Models\TenderMaster;
class DocumentEditValidate
{

    public static function process($date,$tender_id)
    {

        $tenderObj = TenderMaster::where('id',$tender_id)->select('id','tender_edit_version_id')->first();
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

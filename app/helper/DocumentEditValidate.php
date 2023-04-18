<?php

namespace App\helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentModifyRequest;
class DocumentEditValidate
{

    public static function process($date,$tender_id)
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


}

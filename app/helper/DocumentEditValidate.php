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

        $current_date_obj = date('Y-m-d H:i:s');
        $current_date = Carbon::createFromFormat('Y-m-d H:i:s', $current_date_obj);

        $opening_date_format = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        $result_obj = $opening_date_format->gt($current_date);

        if($result_obj)
        {   
            $tende_edit_log = DocumentModifyRequest::where('documentSystemCode',$tender_id)->where('status',1)->where('approved',-1)->orderBy('id','desc')->first();
            if(isset($tende_edit_log))
            {
                return true;
            }

            return false;

        }

            return false;
    }


}

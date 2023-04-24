<?php

namespace App\helper;

use App\Models\TenderMaster;
class TenderDetails
{

    public static function process($id)
    {

        $tenderObj = TenderMaster::where('id',$id)->select('id','bid_submission_opening_date','tender_edit_version_id')->first();
        return $tenderObj;

    }


}

<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\DocumentEditValidate;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\helper\TenderDetails;
class CircularAmendmentsObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  CircularAmendments $tender
     * @return void
     */
    public function created(CircularAmendments $tender)
    {
        $tenderObj = TenderDetails::process($tender->getAttribute('tender_id'));
        $obj = DocumentEditValidate::process($tender->getAttribute('tender_id'));

        if($obj)
        {
            $circularId = $tender->getAttribute('circular_id');
            $cirularObj = TenderCircularsEditLog::where('master_id',$circularId)->first();

            $result = $this->process($tender,$cirularObj,2,$tenderObj,null);
        

            if($result)
            {
                Log::info('tender circular created successfully');
            }
        }

        

    }

    public function deleted(CircularAmendments $tender)
    {
       
        $tenderObj = TenderDetails::process($tender->getAttribute('tender_id'));
        $obj = DocumentEditValidate::process($tender->getAttribute('tender_id'));
      
        if($obj)
        {
            $amend = CircularAmendmentsEditLog::where('master_id',$tender->getAttribute('id'))->first();

            if($amend)
            {
                $result = $this->process($tender,$amend,1,$tenderObj,$amend->getAttribute('id'));

                
                if($result)
                {
                    Log::info('tender circular deleted successfully');
                }
            }
        }


    }


    public function process($tender,$obj,$type,$tenderObj,$reflog)
    {
        $data['tender_id']=$tender->getAttribute('tender_id');
        $data['circular_id']=$obj->getAttribute('circular_id');
        $data['amendment_id']=$tender->getAttribute('amendment_id');
        $data['attachment_id']=$tender->getAttribute('attachment_id');
        $data['master_id']=$tender->getAttribute('id');
        $data['modify_type']=$type;
        $data['ref_log_id']= $reflog;
        $data['vesion_id']=$tenderObj->getAttribute('tender_edit_version_id');
        $result = CircularAmendmentsEditLog::create($data);

        return $result;
    }

}
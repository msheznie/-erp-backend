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
   

        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tender_obj->getOriginal('bid_submission_opening_date');
        $employee = \Helper::getEmployeeInfo();

        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));

        if($obj)
        {

            $circular_id = $tender->getAttribute('circular_id');

            $cirular_obj = TenderCircularsEditLog::where('master_id',$circular_id)->first();

            $result = $this->process($tender,$cirular_obj,2,$tender_obj,null);
        

            if($result)
            {
                Log::info('tender circular created successfully');
            }
        }

        

    }

    public function deleted(CircularAmendments $tender)
    {
       
        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tender_obj->getOriginal('bid_submission_opening_date');
        $employee = \Helper::getEmployeeInfo();

        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));
      
        if($obj)
        {
            $amend = CircularAmendmentsEditLog::where('master_id',$tender->getAttribute('id'))->first();

            if($amend)
            {
                $result = $this->process($tender,$amend,1,$tender_obj,$amend->getAttribute('id'));

                
                if($result)
                {
                    Log::info('tender circular deleted successfully');
                }
            }
        }


    }


    public function process($tender,$obj,$type,$tender_obj,$reflog)
    {

        $data['tender_id']=$tender->getAttribute('tender_id');
        $data['circular_id']=$obj->getAttribute('circular_id');
        $data['amendment_id']=$tender->getAttribute('amendment_id');
        $data['attachment_id']=$tender->getAttribute('attachment_id');
        $data['master_id']=$tender->getAttribute('id');
        $data['modify_type']=$type;
        $data['ref_log_id']= $reflog;
        $data['vesion_id']=$tender_obj->getAttribute('tender_edit_version_id');
        $result = CircularAmendmentsEditLog::create($data);

        return $result;
    }

}
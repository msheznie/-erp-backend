<?php

namespace App\Observers;

use App\Models\SrmTenderBidEmployeeDetails;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\DocumentEditValidate;
use App\Models\TenderMaster;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;

class TenderBidEmployeeObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  SrmTenderBidEmployeeDetails $tender
     * @return void
     */
    public function deleted(SrmTenderBidEmployeeDetails $tender)
    {

        $tenderObj = TenderMaster::where('id',$tender->getOriginal('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tenderObj->getOriginal('bid_submission_opening_date');
     
        $obj = DocumentEditValidate::process($date,$tender->getOriginal('tender_id'));

            if($obj)
            {
                $result = $this->eveluate($tender->getOriginal('emp_id'),$tender->getOriginal('status'),$tender->getOriginal('commercial_eval_remarks'),$tender->getOriginal('remarks'),$tender->getOriginal('commercial_eval_status'),$tender->getOriginal('tender_id'),$tenderObj->getOriginal('tender_edit_version_id'),1);
                if($result)
                {
                    Log::info('deleted succesfully');
                }
            }

    }

    public function created(SrmTenderBidEmployeeDetails $tender)
    {
    
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tenderObj->getOriginal('bid_submission_opening_date');
     
        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));
            if($obj)
            {
                $result = $this->eveluate($tender->getAttribute('emp_id'),0,null,null,0,$tender->getAttribute('tender_id'),$tenderObj->getOriginal('tender_edit_version_id'),2);
                if($result)
                {
                    Log::info('created succesfully');
                }

            }
    }

    public function eveluate($emp_id,$status,$comm,$remarks,$comm_stat,$tender_id,$version_id,$type)
    {
            $data['emp_id'] = $emp_id;
            $data['status'] = $status;
            $data['commercial_eval_remarks'] = $comm;
            $data['remarks'] = $remarks;
            $data['commercial_eval_status'] = $comm_stat;
            $data['tender_id'] = $tender_id;
            $data['tender_edit_version_id'] = $version_id;
            $data['modify_type'] = $type;
            $data['created_at'] = now();
            $result = SrmTenderBidEmployeeDetailsEditLog::insert($data);
            return $result;
        
    }

}
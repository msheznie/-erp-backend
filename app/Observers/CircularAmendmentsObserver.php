<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\helper\TenderDetails;
use App\helper\Helper;
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
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

        if($obj)
        {
            $circularId = $tender->getAttribute('circular_id');
            $cirularObj = TenderCircularsEditLog::select('id')->where('master_id',$circularId)->first();
            $editCircularID = null;
            if(isset($cirularObj) && !empty($cirularObj))
            {
                $editCircularID = $cirularObj->getAttribute('id');
            }

            $result = $this->process($tender,$editCircularID,2,$tenderObj,null);
        

            if($result)
            {
                Log::info('tender circular created successfully');
            }
        }

        

    }

    public function deleted(CircularAmendments $tender)
    {
       
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
      
        if($obj)
        {
            $amend = CircularAmendmentsEditLog::select('circular_id','id')->where('master_id',$tender->getAttribute('id'))->first();
            $editCircularID = null;
            $reflogId = null;
            if(isset($amend) && !empty($amend))
            {
                $editCircularID = $amend->getAttribute('circular_id');
                $reflogId = $amend->getAttribute('id');
            }

            $result = $this->process($tender,$editCircularID,1,$tenderObj,$reflogId);

            
            if($result)
            {
                Log::info('tender circular deleted successfully');
            }
        }


    }


    public function process($tender,$obj,$type,$tenderObj,$reflog)
    {
        
        $employee = Helper::getEmployeeInfo();
        if(isset($employee))
        {
            $empId = $employee->employeeSystemID;

            $data['tender_id']=$tender->getAttribute('tender_id');
            $data['circular_id']=$obj;
            $data['amendment_id']=$tender->getAttribute('amendment_id');
            $data['attachment_id']=$tender->getAttribute('attachment_id');
            $data['master_id']=$tender->getAttribute('id');
            $data['modify_type']=$type;
            $data['ref_log_id']= $reflog;
            $data['vesion_id']=$tenderObj->getAttribute('tender_edit_version_id');
            $data['updated_by'] = $empId;
            $result = CircularAmendmentsEditLog::create($data);
    
            return $result;
        }

    }

}
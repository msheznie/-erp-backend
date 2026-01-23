<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use App\helper\TenderDetails;
use App\helper\Helper;
class TenderCircularsObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  TenderCirculars $tender
     * @return void
     */
    public function created(TenderCirculars $tender)
    {
 
        $employee = Helper::getEmployeeInfo();
    
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

        if($obj && isset($employee))
        {
            $empId = $employee->employeeSystemID;
            $result = $this->process($tender,$empId,$tenderObj,2,null);

            if($result)
            {
                Log::info('tender circular created successfully');
            }
        }

        

    }

    public function updated(TenderCirculars $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
      

        if($obj && isset($employee))
        {
           $empId = $employee->employeeSystemID;
           $modifyType = 3;
           $boqItems = TenderCircularsEditLog::where('master_id',$tender->getAttribute('id'))->where('vesion_id',$tenderObj->getOriginal('tender_edit_version_id'))->first();
           if(isset($boqItems))
           {
               $modifyType = 4;
           }

           $reflogId = null;
           $output = TenderCircularsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
           if(isset($output))
           {
              $reflogId = $output->getAttribute('id');
           }

           $result = $this->process($tender,$empId,$tenderObj,$modifyType,$reflogId);

           if($result)
           {    
            Log::info('tender circular updated successfully');
           }

        }
    }

    public function deleted(TenderCirculars $tender)
    {
       
        $employee = Helper::getEmployeeInfo();
       
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        if($obj && isset($employee))
        {
            $empId = $employee->employeeSystemID;
            $reflogId = null;
            $output = TenderCircularsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
            if(isset($output))
            {
               $reflogId = $output->getAttribute('id');
            }
            $result = $this->process($tender,$empId,$tenderObj,1,$reflogId);

            if($result)
            {
                Log::info('tender circular deleted successfully');
            }
        }
    }

    public function process($tender,$empId,$tenderObj,$type,$ref)
    {
        $data['tender_id']=$tender->getAttribute('tender_id');
        $data['circular_name']=$tender->getAttribute('circular_name');
        $data['description']=$tender->getAttribute('description');
        $data['attachment_id']=$tender->getAttribute('attachment_id');
        $data['master_id']=$tender->getAttribute('id');
        $data['modify_type']= $type;
        $data['updated_by'] = $empId;
        $data['vesion_id']=$tenderObj->getAttribute('tender_edit_version_id');
        $data['company_id']=$tender->getAttribute('company_id');
        $data['ref_log_id']=$ref;
        $result = TenderCircularsEditLog::create($data);

        if($result)
        {
            return true;
        }
    }

}
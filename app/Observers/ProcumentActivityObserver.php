<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\ProcumentActivityEditLog;
use App\Models\ProcumentActivity;
use App\helper\TenderDetails;
use App\helper\Helper;

class ProcumentActivityObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  ProcumentActivity $tender
     * @return void
     */
    public function created(ProcumentActivity $tender)
    {
       
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
     
            if($obj)
            {
                $reflogId = null;
                $modifyType = 2;
                $output = $this->process($tender,$reflogId,$modifyType,$tenderObj->getOriginal('tender_edit_version_id'),1);
                if($output)
                {
                    Log::info('created succesfully 2');
                }

               
            }
    
    }

    public function deleted(ProcumentActivity $tender)
    {
       
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

            if($obj)
            {
                $reflogId = null;
                $activity = ProcumentActivityEditLog::where('master_id',$tender->getAttribute('id'))->where('modify_type',2)->select('id')->first();
                if(isset($activity))
                {
                   $reflogId = $activity->getAttribute('id');
                }
                $modifyType = 1;
                $output = $this->process($tender,$reflogId,$modifyType,$tenderObj->getOriginal('tender_edit_version_id'),1);
                if($output)
                {
                    Log::info('created succesfully 2');
                }

               
            }

    }

    public function process($tender,$reflog_id,$modify_type_val,$version_id,$type)
    {
        $employee = Helper::getEmployeeInfo();
        if(isset($employee))
        {
            $empId = $employee->employeeSystemID;
            $data1['tender_id'] = $tender->getAttribute('tender_id');
            $data1['category_id'] = $tender->getAttribute('category_id');
            $data1['company_id'] = $tender->getAttribute('company_id');
            $data1['version_id'] = $version_id;
            $data1['modify_type'] = $modify_type_val;
            $data1['master_id'] = $tender->getAttribute('id');
            $data1['ref_log_id'] = $reflog_id;
            $data1['created_at'] = now();
            $data1['updated_by'] = $empId;
            $result = ProcumentActivityEditLog::create($data1);
    
            if($result)
            {
                return true;
            }
        }

          return false;
    }



}
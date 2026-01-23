<?php

namespace App\Observers;

use App\Models\SrmTenderBidEmployeeDetails;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\TenderMaster;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use App\helper\TenderDetails;
use App\helper\Helper;


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
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
       

           if($obj && isset($employee))
            {
                $empId = $employee->employeeSystemID;
                $result = $this->eveluate($empId,$tender->getOriginal('status'),$tender->getOriginal('commercial_eval_remarks'),$tender->getOriginal('remarks'),$tender->getOriginal('commercial_eval_status'),$tender->getOriginal('tender_id'),$tenderObj->getOriginal('tender_edit_version_id'),1);
                if($result)
                {
                    Log::info('deleted succesfully');
                }
            }

    }

    public function created(SrmTenderBidEmployeeDetails $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
       
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
           if($obj && isset($employee))
            {
                $empId = $employee->employeeSystemID;
                $result = $this->eveluate($empId,0,null,null,0,$tender->getAttribute('tender_id'),$tenderObj->getOriginal('tender_edit_version_id'),2);
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
            $data['updated_by'] = $emp_id;
            $result = SrmTenderBidEmployeeDetailsEditLog::insert($data);
            return $result;
        
    }

}
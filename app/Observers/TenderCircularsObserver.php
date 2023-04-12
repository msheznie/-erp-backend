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
   

        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tender_obj->getOriginal('bid_submission_opening_date');
        $employee = \Helper::getEmployeeInfo();

        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));

        if($obj)
        {
            $data['tender_id']=$tender->getAttribute('tender_id');
            $data['circular_name']=$tender->getAttribute('circular_name');
            $data['description']=$tender->getAttribute('description');
            $data['attachment_id']=$tender->getAttribute('attachment_id');
            $data['master_id']=$tender->getAttribute('id');
            $data['modify_type']=2;
            $data['created_by'] = $employee->employeeSystemID;
            $data['vesion_id']=$tender_obj->getAttribute('tender_edit_version_id');
            $data['company_id']=$tender->getAttribute('company_id');
            $result = TenderCircularsEditLog::create($data);

            if($result)
            {
                Log::info('tender circular created successfully');
            }
        }

        

    }

    public function updated(TenderCirculars $tender)
    {
   
        
        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tender_obj->getOriginal('bid_submission_opening_date');
        $employee = \Helper::getEmployeeInfo();

        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));

        if($obj)
        {
           $circular =  TenderCircularsEditLog::where('master_id',$tender->getAttribute('id'))->first();

           $data['circular_name'] = $tender->getAttribute('circular_name');
           $data['description'] = $tender->getAttribute('description');
           $result = TenderCircularsEditLog::where('id',$circular->getAttribute('id'))->update($data);

           if($result)
           {    
            Log::info('tender circular updated successfully');
           }

        }
    }

    public function deleted(TenderCirculars $tender)
    {
        Log::info(print_r($tender, true));
        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tender_obj->getOriginal('bid_submission_opening_date');
        $employee = \Helper::getEmployeeInfo();

        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));

        if($obj)
        {
            $data['tender_id']=$tender->getAttribute('tender_id');
            $data['circular_name']=$tender->getAttribute('circular_name');
            $data['description']=$tender->getAttribute('description');
            $data['attachment_id']=$tender->getAttribute('attachment_id');
            $data['master_id']=$tender->getAttribute('id');
            $data['modify_type']=1;
            $data['created_by'] = $employee->employeeSystemID;
            $data['vesion_id']=$tender_obj->getAttribute('tender_edit_version_id');
            $data['company_id']=$tender->getAttribute('company_id');
            $data['ref_log_id']=$tender->getAttribute('id');
            $result = TenderCircularsEditLog::create($data);

            if($result)
            {
                Log::info('tender circular deleted successfully');
            }
        }


    }

}
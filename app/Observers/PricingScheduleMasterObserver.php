<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\DocumentEditValidate;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\TenderBidFormatDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleDetail;

class PricingScheduleMasterObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  PricingScheduleMaster $tender
     * @return void
     */
    public function created(PricingScheduleMaster $tender)
    {
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));
            if($obj)
            {
                        $reflog_id = null;
                        $modify_type_val = 2;
                        $output = $this->process($tender,$reflog_id,$modify_type_val,$tenderObj->getOriginal('tender_edit_version_id'));
                        if($output)
                        {
                            Log::info('created succesfully');
                        }
                    
            }
    
    }

    public function deleted(PricingScheduleMaster $tender)
    {
       
      
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();

        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));
        if($obj)
        {
            $id = $tender->getAttribute('id');

            $reflog_id = null;
            $shedule_master = PricingScheduleMasterEditLog::where('master_id',$id)->orderBy('id','desc')->first();
            if(isset($shedule_master))
            {
                $reflog_id = $shedule_master->getOriginal('id');
            }
            $modify_type_val = 1;
    
            $employee = \Helper::getEmployeeInfo();
            $empId = $employee->employeeSystemID;
            $data1['tender_id'] = $tender->getAttribute('tender_id');
            $data1['scheduler_name'] = $tender->getAttribute('scheduler_name');
            $data1['price_bid_format_id'] = $tender->getAttribute('price_bid_format_id');
            $data1['schedule_mandatory'] = $tender->getAttribute('schedule_mandatory');
            $data1['status'] = 0;
            $data1['company_id'] = $tender->getAttribute('company_id');
            $data1['tender_edit_version_id'] = $tenderObj->getOriginal('tender_edit_version_id');
            $data1['modify_type'] = $modify_type_val;
            $data1['master_id'] = $tender->getAttribute('id');
            $data1['red_log_id'] = $reflog_id;
            $data1['created_at'] = now();
            $result = PricingScheduleMasterEditLog::create($data1);
            if($result)
            {
    
                $details = PricingScheduleDetailEditLog::where('pricing_schedule_master_id',$reflog_id)->get();
                foreach($details as $key=>$bid)
                {
                    $dataBidShed['tender_id']=$bid->getAttribute('tender_id');
                    $dataBidShed['bid_format_id']=$bid->getAttribute('bid_format_id');
                    $dataBidShed['bid_format_detail_id']=$bid->getAttribute('bid_format_detail_id');
                    $dataBidShed['label']=$bid->getAttribute('label');
                    $dataBidShed['field_type']=$bid->getAttribute('field_type');
                    $dataBidShed['is_disabled']=$bid->getAttribute('is_disabled');
                    $dataBidShed['boq_applicable']=$bid->getAttribute('boq_applicable');
                    $dataBidShed['pricing_schedule_master_id']=$result['id'];
                    $dataBidShed['company_id']=$bid->getAttribute('company_id');
                    $dataBidShed['formula_string']=$bid->getAttribute('formula_string');
                    $dataBidShed['created_by']=$empId;
                    $dataBidShed['tender_edit_version_id'] = $bid->getAttribute('tender_edit_version_id');
                    $dataBidShed['modify_type'] = 1;
                    $dataBidShed['description'] = $bid->getAttribute('description');
                    $dataBidShed['master_id'] = $bid->getAttribute('master_id');
                    $dataBidShed['ref_log_id'] = $bid->getAttribute('id');
                    $result1 = PricingScheduleDetailEditLog::create($dataBidShed);
    
                    if($result1)
                    {
                        Log::info('deleted succesfully');
                    }
                }
    
            }
        }


    }

    public function updated(PricingScheduleMaster $tender)
    {
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));
        if($obj)
        {
            foreach($tender->fillable as $key=>$val)
            {
                $oldValue = $tender->getOriginal($val);
                $newValue = $tender->getAttribute($val);

                if($oldValue != $newValue)
                {
                    $data['attribute'] = $val;
                    $data['new_value'] = $newValue;
                    $data['old_value'] = $oldValue;
                    $data['tender_id'] = $tender->getAttribute('tender_id');
                    $data['version_id'] = $tenderObj->getOriginal('tender_edit_version_id');
                    $data['table'] = 'PricingScheduleMaster';
                    $data['master_id'] = $tender->getAttribute('id');
                    $data['created_at'] = now();
                    $result = DocumentModifyRequestDetail::insert($data);
                    if($result)
                    {//start
                        $reflog_id = null;
                        $output = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
                        if(isset($output))
                        {
                           $reflog_id = $output->getAttribute('id');
                        }

                        $modify_type_val = 3;
                        $modify_type = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('id'))->where('tender_edit_version_id',$tenderObj->getOriginal('tender_edit_version_id'))->first();
                        if(isset($modify_type))
                        {
                            $modify_type_val = 4;
                        }

                        $output = $this->process($tender,$reflog_id,$modify_type_val,$tenderObj->getOriginal('tender_edit_version_id'));
                        if($output)
                        {
                            Log::info('updated succesfully');
                        }


                    }//end
                }

                


            }
        }
       

    }


    public function process($tender,$reflog_id,$modify_type_val,$version_id)
    {
        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        $data1['tender_id'] = $tender->getAttribute('tender_id');
        $data1['scheduler_name'] = $tender->getAttribute('scheduler_name');
        $data1['price_bid_format_id'] = $tender->getAttribute('price_bid_format_id');
        $data1['schedule_mandatory'] = $tender->getAttribute('schedule_mandatory');
        $data1['status'] = 0;
        $data1['company_id'] = $tender->getAttribute('company_id');
        $data1['tender_edit_version_id'] = $version_id;
        $data1['modify_type'] = $modify_type_val;
        $data1['master_id'] = $tender->getAttribute('id');
        $data1['red_log_id'] = $reflog_id;
        $data1['created_at'] = now();
        $result = PricingScheduleMasterEditLog::create($data1);
        
        if($result)
        {

            $is_complete = true;
            $modifyType = 2;
            $priceBidShe = TenderBidFormatDetail::where('tender_id',$tender->getAttribute('price_bid_format_id'))->get();

            foreach ($priceBidShe as $bid){

                if(($bid->getOriginal('is_disabled') == 1 || $bid->getOriginal('boq_applicable') == 1) && $bid->getOriginal('field_type') != 4)
                {
                    $is_complete = false;
                }
                
                $shedule_detail = PricingScheduleDetail::where('tender_id',$tender->getAttribute('tender_id'))->where('bid_format_detail_id',$bid->getOriginal('id'))->first();

                $dataBidShed['tender_id']=$tender->getAttribute('tender_id');
                $dataBidShed['bid_format_id']=$bid->getOriginal('tender_id');
                $dataBidShed['bid_format_detail_id']=$bid->getOriginal('id');
                $dataBidShed['label']=$bid->getOriginal('label');
                $dataBidShed['field_type']=$bid->getOriginal('field_type');
                $dataBidShed['is_disabled']=$bid->getOriginal('is_disabled');
                $dataBidShed['boq_applicable']=$bid->getOriginal('boq_applicable');
                $dataBidShed['pricing_schedule_master_id']=$result['id'];
                $dataBidShed['company_id']=$tender->getAttribute('company_id');
                $dataBidShed['formula_string']=$bid->getOriginal('formula_string');
                $dataBidShed['created_by']=$empId;
                $dataBidShed['tender_edit_version_id'] = $version_id;
                $dataBidShed['modify_type'] = $modifyType;
                $dataBidShed['description'] = $shedule_detail->getAttribute('description');
                $dataBidShed['master_id'] = $shedule_detail->getAttribute('id');
                $result1 = PricingScheduleDetailEditLog::create($dataBidShed);

                if($result1)
                {
                   

                }

            }

            return true;
        }
    }


}
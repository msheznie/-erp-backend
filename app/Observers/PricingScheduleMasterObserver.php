<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\TenderBidFormatDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleDetail;
use App\helper\TenderDetails;

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

    public function deleted(PricingScheduleMaster $tender)
    {
       
      
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        if($obj)
        {
            $id = $tender->getAttribute('id');

            $reflogId = null;
            $shedule_master = PricingScheduleMasterEditLog::where('master_id',$id)->orderBy('id','desc')->first();
            if(isset($shedule_master))
            {
                $reflogId = $shedule_master->getOriginal('id');
            }
            $modifyType = 1;
    
            $employee = \Helper::getEmployeeInfo();
            $empId = $employee->employeeSystemID;
            $data1['tender_id'] = $tender->getAttribute('tender_id');
            $data1['scheduler_name'] = $tender->getAttribute('scheduler_name');
            $data1['price_bid_format_id'] = $tender->getAttribute('price_bid_format_id');
            $data1['schedule_mandatory'] = $tender->getAttribute('schedule_mandatory');
            $data1['status'] = 0;
            $data1['company_id'] = $tender->getAttribute('company_id');
            $data1['tender_edit_version_id'] = $tenderObj->getOriginal('tender_edit_version_id');
            $data1['modify_type'] = $modifyType;
            $data1['master_id'] = $tender->getAttribute('id');
            $data1['red_log_id'] = $reflogId;
            $data1['created_at'] = now();
            $result = PricingScheduleMasterEditLog::create($data1);
            if($result)
            {
    
                $details = PricingScheduleDetailEditLog::where('pricing_schedule_master_id',$reflogId)->get();
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
        
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        if($obj)
        {

                        $reflogId = null;
                        $output = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
                        if(isset($output))
                        {
                           $reflogId = $output->getAttribute('id');
                        }

                        $modifyType= 3;
                        $pricingObj = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('id'))->where('tender_edit_version_id',$tenderObj->getOriginal('tender_edit_version_id'))->first();
                        if(isset($pricingObj))
                        {
                            $modifyType = 4;
                        }

                        $output = $this->process($tender,$reflogId,$modifyType,$tenderObj->getOriginal('tender_edit_version_id'),2);
                        if($output)
                        {
                            Log::info('updated succesfully');
                        }

        }
       

    }


    public function process($tender,$reflog_id,$modify_type_val,$version_id,$type)
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
            if($type == 2)
            {
                $modifyType = 2;
                $priceBidShe = TenderBidFormatDetail::where('tender_id',$tender->getAttribute('price_bid_format_id'))->get();
    
                foreach ($priceBidShe as $bid){
    
               
                    
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
                       
                        Log::info('deleted ccccccccccccccc');
                    }
    
                }
            }
            return true;
        }
    }


}
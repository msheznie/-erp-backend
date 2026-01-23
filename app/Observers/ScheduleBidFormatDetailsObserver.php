<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\ScheduleBidFormatDetails;
use App\Models\PricingScheduleMaster;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\TenderBidFormatDetail;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\helper\TenderDetails;
use App\helper\Helper;

class ScheduleBidFormatDetailsObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  ScheduleBidFormatDetails $tender
     * @return void
     */
    public function created(ScheduleBidFormatDetails $tender)
    {

        $shedule_master = PricingScheduleMaster::where('id',$tender->getAttribute('schedule_id'))->select('tender_id')->first();
        $tenderObj = TenderDetails::getTenderMasterData($shedule_master->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
      
        $versionId = $tenderObj->getOriginal('tender_edit_version_id');
        $obj = TenderDetails::validateTenderEdit($shedule_master->getAttribute('tender_id'));

        if($obj && isset($employee))
        {    
            $empId = $employee->employeeSystemID;
            $master = null;
            $sheduleID = $tender->getAttribute('schedule_id');
            $sheduleMasterID = PricingScheduleMasterEditLog::select('id')->where('master_id',$sheduleID)->orderBy('id','desc')->first();
            if(isset($sheduleMasterID))
            {
                $master = ScheduleBidFormatDetailsLog::select('id','schedule_id')->where('schedule_id',$sheduleMasterID->id)
                ->where('bid_format_detail_id',$tender->getAttribute('bid_format_detail_id'))
                ->where('tender_edit_version_id',$versionId)
                ->orderBy('id','desc')->first();
            }

                                                                   
            if(isset($master))
            {
                $refLogId = null;
                $formatDetails = ScheduleBidFormatDetailsLog::where('bid_format_detail_id',$tender->getAttribute('bid_format_detail_id'))->where('schedule_id',$master->getAttribute('id'))->select('id')->orderBy('id','desc')->first();
                
                if(isset($formatDetails))
                {
                    $refLogId = $formatDetails->getAttribute('id');
                }

                $modifyType = 3;
                $sheduleLog = ScheduleBidFormatDetailsLog::select('id')->where('bid_format_detail_id',$tender->getAttribute('bid_format_detail_id'))
                                                            ->where('schedule_id',$master->getAttribute('schedule_id'))
                                                            ->where('tender_edit_version_id',$versionId)
                                                            ->first();
                if(isset($sheduleLog))
                {
                    $modifyType = 4;
                }


                $output = $this->createFormatDetails($tender,$master->getAttribute('schedule_id'),$empId,$tenderObj,$modifyType,$refLogId);

                if($output)
                {
                    Log::info('created succefully');
                }
            }
            else
            {
               $sheduleMaster =  PricingScheduleMasterEditLog::where('master_id',$sheduleID)
                                            ->where('tender_edit_version_id',$versionId)  
                                            ->where('modify_type','!=',1)
                                            ->orderBy('id','desc')->first();

                if(!$sheduleMaster)
                {   
                    
                    $result =  $this->process($tender);
                    if($result)
                    {
                        Log::info('boq items created succsfully');
                    }
                }   
                else    
                {     

                    $output = $this->createFormatDetails($tender,$sheduleMaster->getOriginal('id'),$empId,$versionId,2,null);
                }                         
             
            }
        }
           
    }

    public function deleted(ScheduleBidFormatDetails $tender)
    {   
        $employee = Helper::getEmployeeInfo();

        if(isset($employee))
        {
            $empId = $employee->employeeSystemID;
      
            $versionId = null;
            $shedule_master = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('schedule_id'))->select('tender_id')->first();
            if(isset($shedule_master))
            {   
                $tenderObj = TenderDetails::getTenderMasterData($shedule_master->getAttribute('tender_id'));
                $versionId = $tenderObj->getOriginal('tender_edit_version_id');
            }
       
           
         
            $formatDetails = ScheduleBidFormatDetailsLog::where('master_id',$tender->getAttribute('id'))->select('id')->orderBy('id','desc')->first();
         
            $refLogId = null;
            if(isset($formatDetails))
            {
                $refLogId = $formatDetails->getOriginal('id');
            }
    
            $shedule_id = null;
            $master = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('schedule_id'))->orderBy('id','desc')->first();
            if(isset($master))
            {
                $shedule_id = $master->getOriginal('id');
            }
          
            $output = $this->createFormatDetails($tender,$shedule_id,$empId,$versionId,1,$refLogId);
    
            if($output)
            {
                return true;
    
            }
        }

    }

    public function process($tender)
    {

        $result = PricingScheduleMaster::where('id',$tender->getAttribute('schedule_id'))->first();
        $tenderObj = TenderMaster::where('id',$result->getAttribute('tender_id'))->select('tender_edit_version_id')->first();

        $employee = Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        $data1['tender_id'] = $result->getAttribute('tender_id');
        $data1['scheduler_name'] = $result->getAttribute('scheduler_name');
        $data1['price_bid_format_id'] = $result->getAttribute('price_bid_format_id');
        $data1['schedule_mandatory'] = $result->getAttribute('schedule_mandatory');
        $data1['status'] = 0;
        $data1['company_id'] = $result->getAttribute('company_id');
        $data1['tender_edit_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');
        $data1['modify_type'] = 2;
        $data1['master_id'] = $tender->getAttribute('schedule_id');
        $data1['red_log_id'] = null;
        $data1['created_at'] = now();
        $data1['updated_by'] = $empId;
        $shedule_master = PricingScheduleMasterEditLog::create($data1);

        if($shedule_master)
        {
            $priceBidShe = TenderBidFormatDetail::where('tender_id',$result->getAttribute('price_bid_format_id'))->get();

            foreach ($priceBidShe as $bid){

         
                $sheduleDetailMaster = PricingScheduleDetail::where('tender_id',$result->getAttribute('tender_id'))
                        ->where('bid_format_detail_id',$bid->getOriginal('id'))
                        ->select('id','company_id','description')
                        ->first();

                $dataBidShed['tender_id']=$result->getAttribute('tender_id');
                $dataBidShed['bid_format_id']=$bid->getOriginal('tender_id');
                $dataBidShed['bid_format_detail_id']=$bid->getOriginal('id');
                $dataBidShed['label']=$bid->getOriginal('label');
                $dataBidShed['field_type']=$bid->getOriginal('field_type');
                $dataBidShed['is_disabled']=$bid->getOriginal('is_disabled');
                $dataBidShed['boq_applicable']=$bid->getOriginal('boq_applicable');
                $dataBidShed['pricing_schedule_master_id']=$shedule_master['id'];
                $dataBidShed['company_id']=$sheduleDetailMaster->getAttribute('company_id');
                $dataBidShed['formula_string']=$bid->getOriginal('formula_string');
                $dataBidShed['updated_by']=$empId;
                $dataBidShed['tender_edit_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');
                $dataBidShed['modify_type'] = 2;
                $dataBidShed['description'] = null;
                $dataBidShed['master_id'] = $sheduleDetailMaster->getAttribute('id');
                $dataBidShed['description'] = $sheduleDetailMaster->getAttribute('description');
                $result1 = PricingScheduleDetailEditLog::create($dataBidShed);


            }
            
            $output = $this->createFormatDetails($tender,$shedule_master['id'],$empId,$tenderObj,2,null);

            if($output)
            {
                return true;

            }

           
        }
        
    }


    public function createFormatDetails($tender,$master_id,$emp_id,$versionId,$modify_type_val,$ref_log_id)
    {
        $data['bid_format_detail_id'] = $tender->getAttribute('bid_format_detail_id');
        $data['schedule_id'] = $master_id;
        $data['value'] =  $tender->getAttribute('value');
        $data['updated_by'] = $emp_id;
        $data['company_id'] = $tender->getAttribute('company_id');
        $data['tender_edit_version_id'] = $versionId;
        $data['master_id'] = $tender->getAttribute('id');
        $data['modify_type'] = $modify_type_val;
        $data['red_log_id'] = $ref_log_id;
        $result = ScheduleBidFormatDetailsLog::create($data);

        return $result;

    }


}
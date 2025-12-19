<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleMaster;
use App\Models\TenderBidFormatDetail;
use App\Models\PricingScheduleMasterEditLog;
use App\helper\TenderDetails;

class TenderBoqItemsObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  TenderBoqItemsObserver $tender
     * @return void
     */
    public function created(TenderBoqItems $tender)
    {

        Log::info('test');
        $pricingDetails = PricingScheduleDetail::where('id',$tender->getAttribute('main_work_id'))->select('tender_id')->first();
        $obj = TenderDetails::validateTenderEdit($pricingDetails->getAttribute('tender_id'));
        $tenderObj = TenderDetails::getTenderMasterData($pricingDetails->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();
       
        if($obj && isset($employee))
        {
            $sheduleDetail = PricingScheduleDetailEditLog::where('master_id',$tender->getAttribute('main_work_id'))->orderBy('id','desc')->first();
          
            $empId = $employee->employeeSystemID;
            if(isset($sheduleDetail))
            {
             
                $data['main_work_id']=$sheduleDetail->getAttribute('id');
                $data['item_name']=$tender->getAttribute('item_name');
                if(($tender->getAttribute('description'))){
                    $data['description']=$tender->getAttribute('description');
                }
                $data['uom']=$tender->getAttribute('uom');
                $data['qty']=$tender->getAttribute('qty');
                $data['tender_edit_version_id']=$tenderObj->getAttribute('tender_edit_version_id');
                $data['tender_id']=$sheduleDetail->getAttribute('tender_id');
                $data['master_id']=$tender->getAttribute('id');
                $data['modify_type']=2;
                $data['updated_by'] = $empId;
    
                $result = TenderBoqItemsEditLog::create($data);
            }
            else
            {
               $result =  $this->process($tender);
                if($result)
                {
                    Log::info('boq items created succsfully');
                }
               
            }   
        }

    }


    public function deleted(TenderBoqItems $tender)
    {
        $tender_id = null;
        if(($tender->getAttribute('tender_id')))
        {
            $tender_id = $tender->getAttribute('tender_id');
        }
   
        if(($tender_id == null || empty($tender_id)))
        {   
            $pricingDetails = PricingScheduleDetail::select('tender_id')->where('id',$tender->getAttribute('main_work_id'))->first();
            if(isset($pricingDetails) && !empty($pricingDetails))
            {
                $tender_id = $pricingDetails->getAttribute('tender_id');
            }
           
        }
       
        if(!isset($tender_id) || $tender_id == null || empty($tender_id))
        {
            return false;
        }
        
        $tenderObj = TenderDetails::getTenderMasterData($tender_id);
        $obj = TenderDetails::validateTenderEdit($tender_id);
        $employee = \Helper::getEmployeeInfo();

       
        if($obj && isset($employee) && !empty($employee))
        {
            $empId = $employee->employeeSystemID;
            $detailLog = PricingScheduleDetailEditLog::where('master_id',$tender->getAttribute('main_work_id'))->where('tender_id',$tenderObj->getAttribute('id'))->first();
            $mainWorkId = null;
            if(isset($detailLog))
            {
                $mainWorkId = $detailLog->getAttribute('id');
            }
            $reflogId = null;
            $activity = TenderBoqItemsEditLog::where('master_id',$tender->getAttribute('id'))->where('modify_type',2)->select('id')->first();
            if(isset($activity))
            {
               $reflogId = $activity->getAttribute('id');
            }
           
           
            $data['main_work_id']=$mainWorkId;
            $data['item_name']=$tender->getAttribute('item_name');
            if(($tender->getAttribute('description'))){
                $data['description']=$tender->getAttribute('description');
            }
            $data['uom']=$tender->getAttribute('uom');
            $data['qty']=$tender->getAttribute('qty');
            $data['tender_edit_version_id']=$tenderObj->getAttribute('tender_edit_version_id');
            $data['tender_id']=$tenderObj->getAttribute('id');
            $data['master_id']=$tender->getAttribute('id');
            $data['modify_type']=1;
            $data['ref_log_id']=$reflogId;
            $data['updated_by'] = $empId;
    
            $result = TenderBoqItemsEditLog::create($data);
    
            if($result)
            {
                Log::info('boq items deleted succsfully');
            }
        }


    }


    public function updated(TenderBoqItems $tender)
    {

        $pricingDetails = PricingScheduleDetail::where('id',$tender->getAttribute('main_work_id'))->select('tender_id')->first();
        $tenderObj = TenderDetails::getTenderMasterData($pricingDetails->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($pricingDetails->getAttribute('tender_id'));
        $employee = \Helper::getEmployeeInfo();
        if($obj && isset($employee))
        {   
            $sheduleDetail = PricingScheduleDetailEditLog::where('master_id',$tender->getAttribute('main_work_id'))->orderBy('id','desc')->first();
           
            $empId = $employee->employeeSystemID;
            if(isset($sheduleDetail))
            {

                $modifyType = 3;
                $boqItems = TenderBoqItemsEditLog::where('master_id',$tender->getAttribute('id'))->where('tender_edit_version_id',$tenderObj->getOriginal('tender_edit_version_id'))->first();
                if(isset($boqItems))
                {
                    $modifyType = 4;
                }
    
                $reflogId = null;
                $output = TenderBoqItemsEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
                if(isset($output))
                {
                   $reflogId = $output->getAttribute('id');
                }
    
    
                $data['main_work_id']=$sheduleDetail->getAttribute('id');
                $data['item_name']=$tender->getAttribute('item_name');
                if(($tender->getAttribute('description'))){
                    $data['description']=$tender->getAttribute('description');
                }
                $data['uom']=$tender->getAttribute('uom');
                $data['qty']=$tender->getAttribute('qty');
                $data['tender_edit_version_id']=$tenderObj->getAttribute('tender_edit_version_id');
                $data['tender_id']=$tenderObj->getAttribute('id');
                $data['master_id']=$tender->getAttribute('id');
                $data['modify_type']=$modifyType;
                $data['ref_log_id']=$reflogId;
                $data['updated_by'] = $empId;
    
                $result = TenderBoqItemsEditLog::create($data);
                if($result)
                {
                    Log::info('boq items updated succsfully');
                }
    
            }
            else
            {
               $result =  $this->process($tender);
                if($result)
                {
                    Log::info('boq items updated succsfully');
                }
               
            } 
        }

    }


    public function process($tender)
    {

        $sheduleDetailId = $tender->getAttribute('main_work_id');
        $details =  PricingScheduleDetail::where('id',$sheduleDetailId)->first();
       
        $result = PricingScheduleMaster::where('id',$details->getAttribute('pricing_schedule_master_id'))->first();
        $tenderObj = TenderMaster::where('id',$details->getAttribute('tender_id'))->first();

        $employee = \Helper::getEmployeeInfo();
        $empId = $employee->employeeSystemID;
        $data1['tender_id'] = $details->getAttribute('tender_id');
        $data1['scheduler_name'] = $result->getAttribute('scheduler_name');
        $data1['price_bid_format_id'] = $result->getAttribute('price_bid_format_id');
        $data1['schedule_mandatory'] = $result->getAttribute('schedule_mandatory');
        $data1['status'] = 0;
        $data1['company_id'] = $result->getAttribute('company_id');
        $data1['tender_edit_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');
        $data1['modify_type'] = 2;
        $data1['master_id'] = $result->getAttribute('id');
        $data1['red_log_id'] = null;
        $data1['created_at'] = now();
        $data1['updated_by'] = $empId;
        $sheduleMaster = PricingScheduleMasterEditLog::create($data1);

        if($sheduleMaster)
        {
            $mainWorkId = null;
            $priceBidShe = TenderBidFormatDetail::where('tender_id',$details->getAttribute('bid_format_id'))->get();

            foreach ($priceBidShe as $bid){

            
                $sheduleDetailMaster = PricingScheduleDetail::where('tender_id',$details->getAttribute('tender_id'))->where('bid_format_detail_id',$bid->getOriginal('id'))->first();

                $dataBidShed['tender_id']=$details->getAttribute('tender_id');
                $dataBidShed['bid_format_id']=$bid->getOriginal('tender_id');
                $dataBidShed['bid_format_detail_id']=$bid->getOriginal('id');
                $dataBidShed['label']=$bid->getOriginal('label');
                $dataBidShed['field_type']=$bid->getOriginal('field_type');
                $dataBidShed['is_disabled']=$bid->getOriginal('is_disabled');
                $dataBidShed['boq_applicable']=$bid->getOriginal('boq_applicable');
                $dataBidShed['pricing_schedule_master_id']=$sheduleMaster['id'];
                $dataBidShed['company_id']=$sheduleDetailMaster->getAttribute('company_id');
                $dataBidShed['formula_string']=$bid->getOriginal('formula_string');
                $dataBidShed['tender_edit_version_id'] = $tenderObj->getAttribute('tender_edit_version_id');
                $dataBidShed['modify_type'] = 2;
                $dataBidShed['description'] = null;
                $dataBidShed['master_id'] = $sheduleDetailMaster->getAttribute('id');
                $dataBidShed['description'] = $sheduleDetailMaster->getAttribute('description');
                $dataBidShed['updated_by'] = $empId;
                $result1 = PricingScheduleDetailEditLog::create($dataBidShed);

                if($sheduleDetailId == $sheduleDetailMaster->getAttribute('id'))
                {
                    $mainWorkId = $result1['id'];
                }

            }

            if($result1)
            {
                $data['main_work_id']=$mainWorkId;
                $data['item_name']=$tender->getAttribute('item_name');
                if(($tender->getAttribute('description'))){
                    $data['description']=$tender->getAttribute('description');
                }
                $data['uom']=$tender->getAttribute('uom');
                $data['qty']=$tender->getAttribute('qty');
                $data['tender_edit_version_id']=$tenderObj->getAttribute('tender_edit_version_id');
                $data['tender_id']=$details->getAttribute('tender_id');
                $data['master_id']=$tender->getAttribute('id');
                $data['modify_type']=2;
                $data['updated_by'] = $empId;
    
                $boq_items = TenderBoqItemsEditLog::create($data);

                if($boq_items)
                {
                    return true;
                }

            }

           
        }
        
    }


}
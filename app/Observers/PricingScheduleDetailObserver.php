<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\TenderBidFormatDetail;
use App\helper\TenderDetails;
use App\helper\Helper;
class PricingScheduleDetailObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  PricingScheduleDetailObserver $tender
     * @return void
     */
    public function updated(PricingScheduleDetail $tender)
    {   
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $employee = Helper::getEmployeeInfo();
       
        $modifyType = 2;
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $bidFormatDetailId = $tender->getAttribute('bid_format_detail_id');
        if($obj && isset($employee))
        {
                $empId = $employee->employeeSystemID;
                $tenderId = $tender->getAttribute('tender_id');
                $bidId = $tender->getAttribute('bid_format_id');
                $versionId = $tenderObj->getAttribute('tender_edit_version_id');
                $masterId = $tender->getAttribute('pricing_schedule_master_id');

                $output = PricingScheduleMasterEditLog::where('tender_id',$tenderId)
                            ->where('price_bid_format_id',$bidId)
                            ->where('tender_edit_version_id',$versionId)
                            ->where('master_id',$masterId)->orderBy('id','desc')->first();
                            if($output)
                            {
                                $detailMasterId =  $output->getAttribute('id');
                                $updated_data['description'] = $tender->getAttribute('description');
                                $PricingScheduleDetailEditLog = PricingScheduleDetailEditLog::where('pricing_schedule_master_id',$detailMasterId)
                                                                ->where('bid_format_detail_id',$bidFormatDetailId)
                                                                ->where('tender_id',$tenderId)->update($updated_data);
                                
                              
                            }
                            else
                            {
                                $result = PricingScheduleMaster::where('id',$tender->getAttribute('pricing_schedule_master_id'))->first();
                                $data1['tender_id'] = $tenderId;
                                $data1['scheduler_name'] = $result->getAttribute('scheduler_name');
                                $data1['price_bid_format_id'] = $result->getAttribute('price_bid_format_id');
                                $data1['schedule_mandatory'] = $result->getAttribute('schedule_mandatory');
                                $data1['status'] = 0;
                                $data1['company_id'] = $result->getAttribute('company_id');
                                $data1['tender_edit_version_id'] = $versionId;
                                $data1['modify_type'] = $modifyType;
                                $data1['master_id'] = $tender->getAttribute('pricing_schedule_master_id');
                                $data1['red_log_id'] = null;
                                $data1['created_at'] = now();
                                $data1['updated_by'] = $empId;
                                $sheduleMaster = PricingScheduleMasterEditLog::create($data1);

                                if($sheduleMaster)
                                {
                        
                                    $isComplete = true;
                                    $priceBidShe = TenderBidFormatDetail::where('tender_id',$bidId)->get();
                        
                                    foreach ($priceBidShe as $bid){
                        
                                        if(($bid->getOriginal('is_disabled') == 1 || $bid->getOriginal('boq_applicable') == 1) && $bid->getOriginal('field_type') != 4)
                                        {
                                            $isComplete = false;
                                        }
                                        $sheduleDetail = PricingScheduleDetail::where('tender_id',$tender->getAttribute('tender_id'))->where('bid_format_detail_id',$bid->getOriginal('id'))->first();

                                        $dataBidShed['tender_id']=$tender->getAttribute('tender_id');
                                        $dataBidShed['bid_format_id']=$bid->getOriginal('tender_id');
                                        $dataBidShed['bid_format_detail_id']=$bid->getOriginal('id');
                                        $dataBidShed['label']=$bid->getOriginal('label');
                                        $dataBidShed['field_type']=$bid->getOriginal('field_type');
                                        $dataBidShed['is_disabled']=$bid->getOriginal('is_disabled');
                                        $dataBidShed['boq_applicable']=$bid->getOriginal('boq_applicable');
                                        $dataBidShed['pricing_schedule_master_id']=$sheduleMaster['id'];
                                        $dataBidShed['company_id']=$result->getAttribute('company_id');
                                        $dataBidShed['formula_string']=$bid->getOriginal('formula_string');
                                        $dataBidShed['created_by']=$empId;
                                        $dataBidShed['tender_edit_version_id'] = $versionId;
                                        $dataBidShed['modify_type'] = $modifyType;
                                        $dataBidShed['description'] = null;
                                        $dataBidShed['master_id'] = $sheduleDetail->getAttribute('id');
                                        $dataBidShed['updated_by'] = $empId;
                                        if($bid->getOriginal('id') == $bidFormatDetailId)
                                        {
                                            $dataBidShed['description'] = $tender->getAttribute('description');
                                        }
                                        $result1 = PricingScheduleDetailEditLog::create($dataBidShed);
                        
                                        if($result1)
                                        {
                                            Log::info('updated succefully');
                        
                                        }
                        
                                    }
                        
                                   
                                }

                                
                            }
            
        }
      

    
    }


    public function created(PricingScheduleDetail $tender)
    {

        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

        $employee = Helper::getEmployeeInfo();
       
        $versionId = $tenderObj->getOriginal('tender_edit_version_id');
        $modifyType = 2;
        $editLog = PricingScheduleMasterEditLog::where('master_id',$tender->getAttribute('pricing_schedule_master_id'))->first();
        if($obj && isset($employee))
        {
                $empId = $employee->employeeSystemID;
                $masterId = null;
                if(isset($editLog))
                {
                    $masterId = $editLog->getAttribute('id');
                }
    
                $dataBidShed['tender_id']=$tender->getAttribute('tender_id');
                $dataBidShed['bid_format_id']=$tender->getAttribute('bid_format_id');
                $dataBidShed['bid_format_detail_id']=$tender->getAttribute('bid_format_detail_id');
                $dataBidShed['label']=$tender->getAttribute('label');
                $dataBidShed['field_type']=$tender->getAttribute('field_type');
                $dataBidShed['is_disabled']=$tender->getAttribute('is_disabled');
                $dataBidShed['boq_applicable']=$tender->getAttribute('boq_applicable');
                $dataBidShed['pricing_schedule_master_id']= $masterId;
                $dataBidShed['company_id']=$tender->getAttribute('company_id');
                $dataBidShed['formula_string']=$tender->getAttribute('formula_string');
                $dataBidShed['updated_by']=$empId;
                $dataBidShed['tender_edit_version_id'] = $versionId;
                $dataBidShed['modify_type'] = $modifyType;
                $dataBidShed['master_id'] = $tender->getAttribute('id');
                $result1 = PricingScheduleDetailEditLog::create($dataBidShed);
    
                if($result1)
                {
                   
                    Log::info('creted succesfully');
                }
    
         
        }
  

    }


}
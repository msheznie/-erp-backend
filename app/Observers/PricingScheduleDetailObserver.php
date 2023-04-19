<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\DocumentEditValidate;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\TenderBidFormatDetail;
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
        $tender_obj = TenderMaster::where('id',$tender->getAttribute('tender_id'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
        $date = $tender_obj->getOriginal('bid_submission_opening_date');
        $employee = \Helper::getEmployeeInfo();
        $obj = DocumentEditValidate::process($date,$tender->getAttribute('tender_id'));
        $bid_format_detail_id = $tender->getAttribute('bid_format_detail_id');
        if($obj)
        {
                $tender_id = $tender->getAttribute('tender_id');
                $bid_id = $tender->getAttribute('bid_format_id');
                $version_id = $tender_obj->getAttribute('tender_edit_version_id');
                $master_id = $tender->getAttribute('pricing_schedule_master_id');

                $output = PricingScheduleMasterEditLog::where('tender_id',$tender_id)
                            ->where('price_bid_format_id',$bid_id)
                            ->where('tender_edit_version_id',$version_id)
                            ->where('master_id',$master_id)->orderBy('id','desc')->first();
                            if($output)
                            {
                                $detail_master_id =  $output->getAttribute('id');
                                $updated_data['description'] = $tender->getAttribute('description');
                                $PricingScheduleDetailEditLog = PricingScheduleDetailEditLog::where('pricing_schedule_master_id',$detail_master_id)
                                                                ->where('bid_format_detail_id',$bid_format_detail_id)
                                                                ->where('tender_id',$tender_id)->update($updated_data);
                                
                              
                            }
                            else
                            {
                                $result = PricingScheduleMaster::where('id',$tender->getAttribute('pricing_schedule_master_id'))->first();
                                $employee = \Helper::getEmployeeInfo();
                                $data1['tender_id'] = $tender_id;
                                $data1['scheduler_name'] = $result->getAttribute('scheduler_name');
                                $data1['price_bid_format_id'] = $result->getAttribute('price_bid_format_id');
                                $data1['schedule_mandatory'] = $result->getAttribute('schedule_mandatory');
                                $data1['status'] = 0;
                                $data1['company_id'] = $result->getAttribute('company_id');
                                $data1['tender_edit_version_id'] = $version_id;
                                $data1['modify_type'] = 2;
                                $data1['master_id'] = $tender->getAttribute('pricing_schedule_master_id');
                                $data1['red_log_id'] = null;
                                $data1['created_at'] = now();
                                $shedule_master = PricingScheduleMasterEditLog::create($data1);

                                if($shedule_master)
                                {
                        
                                    $is_complete = true;
                                    $priceBidShe = TenderBidFormatDetail::where('tender_id',$bid_id)->get();
                        
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
                                        $dataBidShed['pricing_schedule_master_id']=$shedule_master['id'];
                                        $dataBidShed['company_id']=$result->getAttribute('company_id');
                                        $dataBidShed['formula_string']=$bid->getOriginal('formula_string');
                                        $dataBidShed['created_by']=$employee->employeeSystemID;
                                        $dataBidShed['tender_edit_version_id'] = $version_id;
                                        $dataBidShed['modify_type'] = 2;
                                        $dataBidShed['description'] = null;
                                        $dataBidShed['master_id'] = $shedule_detail->getAttribute('id');
                                        if($bid->getOriginal('id') == $bid_format_detail_id)
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

}
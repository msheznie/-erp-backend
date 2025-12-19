<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\CalendarDatesDetail;
use App\Models\CalendarDatesDetailEditLog;
use App\helper\TenderDetails;

class CalendarDatesDetailObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  CalendarDatesDetail $tender
     * @return void
     */
    public function created(CalendarDatesDetail $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $date = $tenderObj->getOriginal('bid_submission_opening_date');
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

            if($obj)
            {
                $reflog_id = null;
                $this->process($tender,2,$tenderObj,$reflog_id);

               
            }
    
    }

    public function deleted(CalendarDatesDetail $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

            if($obj)
            {
                $reflog_id = null;
                $output = CalendarDatesDetailEditLog::where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
                if(isset($output))
                {
                   $reflog_id = $output->getAttribute('id');
                }


                $this->process($tender,1,$tenderObj,$reflog_id);
               
            }
    
    }

    public function updated(CalendarDatesDetail $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));

        if($obj)
        {   
          
            $modifyType = 3;
            $calender = CalendarDatesDetailEditLog::select('id')->where('master_id',$tender->getAttribute('id'))->where('version_id',$tenderObj->getOriginal('tender_edit_version_id'))->first();
            if(isset($calender))
            {
                $modifyType = 4;
            }

            $reflogId = null;
            $output = CalendarDatesDetailEditLog::select('id')->where('master_id',$tender->getAttribute('id'))->orderBy('id','desc')->first();
            if(isset($output))
            {
               $reflogId = $output->getAttribute('id');
            }

            $this->process($tender,$modifyType,$tenderObj,$reflogId);

        }

    }


    public function process($tender,$type,$tenderObj,$reflog_id)
    {
        $employee = \Helper::getEmployeeInfo();
        if(isset($employee))
        {
            $empId = $employee->employeeSystemID;
            $data['tender_id'] = $tender->getAttribute('tender_id');
            $data['version_id'] = $tenderObj->getAttribute('tender_edit_version_id');;
            $data['calendar_date_id'] =$tender->getAttribute('calendar_date_id');
            $data['from_date'] = $tender->getAttribute('from_date');
            $data['to_date'] = $tender->getAttribute('to_date');
            $data['company_id'] = $tender->getAttribute('company_id');
            $data['modify_type'] =$type;
            $data['ref_log_id'] =$reflog_id;
            $data['master_id'] = $tender->getAttribute('id');
            $data['updated_by'] = $empId;
            $result = CalendarDatesDetailEditLog::create($data);
        }

    }

}
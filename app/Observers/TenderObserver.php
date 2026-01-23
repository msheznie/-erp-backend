<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\TenderDetails;
use App\helper\Helper;
class TenderObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  TenderMaster $tender
     * @return void
     */
    public function updated(TenderMaster $tender)
    {
        $obj = TenderDetails::validateTenderEdit($tender->getOriginal('id'));
        $employee = Helper::getEmployeeInfo();
       
          if($obj && isset($employee))
            {
                $empId = $employee->employeeSystemID;
                foreach($tender->fillable as $key=>$val)
                {
                    $oldValue = $tender->getOriginal($val);
                    $newValue = $tender->getAttribute($val);

                    if($oldValue != $newValue)
                    {
                        $data['attribute'] = $val;
                        $data['new_value'] = $newValue;
                        $data['old_value'] = $oldValue;
                        $data['tender_id'] = $tender->getOriginal('id');
                        $data['version_id'] = $tender->getOriginal('tender_edit_version_id');
                        $data['table'] = 'Tender Master';
                        $data['master_id'] = $tender->getOriginal('id');
                        $data['created_at'] = now();
                        $data['updated_at'] = now();
                        $data['updated_by'] = $empId;
                        
                        $result = DocumentModifyRequestDetail::insert($data);
                        if($result)
                        {
                            Log::info('updated succesfully');
                        }
                    }
                }

               
            }
    
    }

}
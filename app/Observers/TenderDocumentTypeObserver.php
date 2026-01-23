<?php

namespace App\Observers;

use App\Models\TenderMaster;
use App\Models\TenderDocumentTypeAssign;
use App\Models\TenderDocumentTypeAssignLog;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\TenderDetails;
use App\helper\Helper;
class TenderDocumentTypeObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  TenderMaster $tender
     * @return void
     */
    public function created(TenderDocumentTypeAssign $tender)
    {   
        $result = $this->process($tender,2,null);

        if($result)
        {
            Log::info('Document type created successfully');
        }
    }

    public function deleted(TenderDocumentTypeAssign $tender)
    {
 
        $deletedRecord = TenderDocumentTypeAssignLog::where('master_id',$tender->getAttribute('id'))->select('id')->orderBy('id','desc')->first();
    
        $refLogId = null;
        if(isset($deletedRecord) && !empty($deletedRecord))
        {
            $refLogId = $deletedRecord->getOriginal('id');
        }

        $result = $this->process($tender,1,$refLogId);

        if($result)
        {
            Log::info('Document type Deleted successfully');
        }
    
    }

    
    public function process($tender,$type,$ref)
    {           
        $employee = Helper::getEmployeeInfo();
      
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('tender_id'));
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('tender_id'));
    
        if($obj && isset($employee) && !empty($employee))
        { 
            $empId = $employee->employeeSystemID;
            $data['document_type_id']=$tender->getAttribute('document_type_id');
            $data['tender_id']=$tender->getAttribute('tender_id');
            $data['master_id']=$tender->getAttribute('id');
            $data['modify_type']=$type;
            $data['ref_log_id']=$ref;
            $data['version_id']= $tenderObj->getOriginal('tender_edit_version_id');
            $data['updated_by']= $empId;
          
            $result = TenderDocumentTypeAssignLog::create($data);
    
            if($result)
            {
                return true;
            }
        }

    }

}
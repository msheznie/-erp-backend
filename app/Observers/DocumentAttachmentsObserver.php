<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentsEditLog;
use App\helper\TenderDetails;

class DocumentAttachmentsObserver
{
    /**
     * Listen to the Tender update event.
     *
     * @param  DocumentAttachments $tender
     * @return void
     */
    public function created(DocumentAttachments $tender)
    {
        
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('documentSystemCode'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('documentSystemCode'));
        $employee = \Helper::getEmployeeInfo();
       
        $type = 2;
        if($obj && isset($employee))
        {
           $empId = $employee->employeeSystemID ?? -1;
           $data['companySystemID'] =$tender->getAttribute('companySystemID');
           $data['documentSystemID'] = $tender->getAttribute('documentSystemID');
           $data['documentID'] = $tender->getAttribute('documentID');
           $data['documentSystemCode'] =$tender->getAttribute('documentSystemCode');
           $data['attachmentDescription'] = $tender->getAttribute('attachmentDescription');
           $data['originalFileName'] = $tender->getAttribute('originalFileName');
           $data['attachmentType'] = $tender->getAttribute('attachmentType');
           $data['sizeInKbs'] = $tender->getAttribute('sizeInKbs');
           $data['modify_type'] = $type;
           $data['version_id'] = $tenderObj->getOriginal('tender_edit_version_id');
           $data['master_id'] = $tender->getAttribute('attachmentID');
           $data['updated_by'] = $empId;
           $result = DocumentAttachmentsEditLog::create($data);
           
           if($result)
           {
            Log::info('created successfullu');
           }

        }

    
    }

    public function updated(DocumentAttachments $tender)
    {
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('documentSystemCode'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('documentSystemCode'));
        $employee = \Helper::getEmployeeInfo();
     
        if($obj && isset($employee))
        {
            $empId = $employee->employeeSystemID;
            $outputExist = DocumentAttachmentsEditLog::where('master_id',$tender->getAttribute('attachmentID'))->where('modify_type',2)->where('path',null)->orderBy('id','desc')->first();
           
            if($outputExist)
            {
                $data1['path'] = $tender->getAttribute('path');
                $data1['myFileName'] = $tender->getAttribute('myFileName');
                $data1['isUploaded'] = $tender->getAttribute('isUploaded');
                $data1['pullFromAnotherDocument'] = $tender->getAttribute('pullFromAnotherDocument');
                $data['parent_id'] = $tender->getAttribute('parent_id');
                $data['envelopType'] = $tender->getAttribute('envelopType');
                $data['updated_by'] = $empId;
                $result = $outputExist->update($data1);
            }
            else
            {
                $reflog_id = null;
                $output = DocumentAttachmentsEditLog::where('master_id',$tender->getAttribute('attachmentID'))->orderBy('id','desc')->first();
                if(isset($output))
                {
                   $reflog_id = $output->getAttribute('id');
                
                }
    
                $modify_type_val = 3;
                $modify_type = DocumentAttachmentsEditLog::where('master_id',$tender->getAttribute('attachmentID'))->where('version_id',$tenderObj->getOriginal('tender_edit_version_id'))->first();
                if(isset($modify_type))
                {
                    $modify_type_val = 4;
                }
    
                $result = $this->process($tender,$tenderObj,$reflog_id,$modify_type_val);
    
             
            }
            if($result)
            {
              Log::info('updated successfully');
            }
        }
        
    }

    public function deleted(DocumentAttachments $tender)
    {      
        $tenderObj = TenderDetails::getTenderMasterData($tender->getAttribute('documentSystemCode'));
        $obj = TenderDetails::validateTenderEdit($tender->getAttribute('documentSystemCode'));

        if($obj)
        {
            $reflogId = null;
            $document =  DocumentAttachmentsEditLog::where('master_id',$tender->getAttribute('attachmentID'))->first();
            if(isset($document))
            {
                $reflogId = $document->getAttribute('id');
            }
            
            $result = $this->process($tender,$tenderObj,$reflogId,1);
            if($result)
            {
             Log::info('deleted successfully');
            }

        }

    }


    public function process($tender,$tenderObj,$reflogId,$type)
    {
        $employee = \Helper::getEmployeeInfo();
        if(isset($employee))
        {
            $empId = $employee->employeeSystemID;
            $data['companySystemID'] =$tender->getAttribute('companySystemID');
            $data['documentSystemID'] = $tender->getAttribute('documentSystemID');
            $data['documentID'] = $tender->getAttribute('documentID');
            $data['documentSystemCode'] =$tender->getAttribute('documentSystemCode');
            $data['approvalLevelOrder'] = $tender->getAttribute('approvalLevelOrder');
            $data['attachmentDescription'] = $tender->getAttribute('attachmentDescription');
            $data['path'] = $tender->getAttribute('path');
            $data['originalFileName'] = $tender->getAttribute('originalFileName');
            $data['myFileName'] = $tender->getAttribute('myFileName');
            $data['docExpirtyDate'] = $tender->getAttribute('docExpirtyDate');
            $data['attachmentType'] = $tender->getAttribute('attachmentType');
            $data['sizeInKbs'] = $tender->getAttribute('sizeInKbs');
            $data['isUploaded'] = $tender->getAttribute('isUploaded');
            $data['pullFromAnotherDocument'] = $tender->getAttribute('pullFromAnotherDocument');
            $data['parent_id'] = $tender->getAttribute('parent_id');
            $data['envelopType'] = $tender->getAttribute('envelopType');
            $data['modify_type'] = $type;
            $data['master_id'] = $tender->getAttribute('attachmentID');
            $data['ref_log_id'] = $reflogId;
            $data['version_id'] = $tenderObj->getOriginal('tender_edit_version_id');
            $data['updated_by'] = $empId;
            $result = DocumentAttachmentsEditLog::create($data);
            if($result)
            {
                return true;
            }
        }
 
    }

}

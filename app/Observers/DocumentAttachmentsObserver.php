<?php

namespace App\Observers;

use App\Models\TenderMaster;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\DocumentModifyRequest;
use App\Models\DocumentModifyRequestDetail;
use App\helper\DocumentEditValidate;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentsEditLog;

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
       
        $tenderObj = TenderMaster::where('id',$tender->getAttribute('documentSystemCode'))->select('bid_submission_opening_date','tender_edit_version_id')->first();
   
        $obj = DocumentEditValidate::process($tenderObj->getOriginal('bid_submission_opening_date'),$tender->getAttribute('documentSystemCode'));

     
        if($obj)
        {
         
           $data['companySystemID'] =$tender->getAttribute('companySystemID');
           $data['documentSystemID'] = $tender->getAttribute('documentSystemID');
           $data['documentID'] = $tender->getAttribute('documentID');
           $data['documentSystemCode'] =$tender->getAttribute('documentSystemCode');
           $data['attachmentDescription'] = $tender->getAttribute('attachmentDescription');
           $data['originalFileName'] = $tender->getAttribute('originalFileName');
           $data['attachmentType'] = $tender->getAttribute('attachmentType');
           $data['sizeInKbs'] = $tender->getAttribute('sizeInKbs');
           $data['modify_type'] = 2;
           $data['master_id'] = $tender->getAttribute('attachmentID');

           $result = DocumentAttachmentsEditLog::create($data);
           if($result)
           {
            Log::info('created successfullu');
           }

        }

    
    }

    public function updated(DocumentAttachments $tender)
    {

        $document =  DocumentAttachmentsEditLog::where('master_id',$tender->getAttribute('attachmentID'))->first();
        $document->path = $tender->getAttribute('path');
        $document->myFileName = $tender->getAttribute('myFileName');
        $document->isUploaded = $tender->getAttribute('isUploaded');
        $document->pullFromAnotherDocument = $tender->getAttribute('pullFromAnotherDocument');
        $document->parent_id = $tender->getAttribute('parent_id');
        $document->envelopType = $tender->getAttribute('envelopType');
        $document->update();

        Log::info('updated successfullu');

    }

    public function deleted(DocumentAttachments $tender)
    {
      

        $reflogId = null;
        $document =  DocumentAttachmentsEditLog::where('master_id',$tender->getAttribute('attachmentID'))->first();
        if(isset($document))
        {
            $reflogId = $document->getAttribute('id');
        }

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
        $data['modify_type'] = 1;
        $data['master_id'] = $tender->getAttribute('attachmentID');
        $data['ref_log_id'] = $reflogId;

        $result = DocumentAttachmentsEditLog::create($data);
        if($result)
        {
         Log::info('created successfullu');
        }

    }

}
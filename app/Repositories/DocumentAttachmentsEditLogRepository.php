<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\DocumentAttachmentsEditLog;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\DocumentAttachmentsRepository;
use App\Services\SrmDocumentModifyService;

/**
 * Class DocumentAttachmentsEditLogRepository
 * @package App\Repositories
 * @version April 11, 2023, 8:46 am +04
 *
 * @method DocumentAttachmentsEditLog findWithoutFail($id, $columns = ['*'])
 * @method DocumentAttachmentsEditLog find($id, $columns = ['*'])
 * @method DocumentAttachmentsEditLog first($columns = ['*'])
*/
class DocumentAttachmentsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'approvalLevelOrder',
        'attachmentDescription',
        'attachmentType',
        'companySystemID',
        'companySystemID',
        'docExpirtyDate',
        'documentID',
        'documentSystemCode',
        'documentSystemID',
        'envelopType',
        'order_number',
        'isAutoCreateDocument',
        'isUploaded',
        'master_id',
        'modify_type',
        'myFileName',
        'originalFileName',
        'parent_id',
        'path',
        'pullFromAnotherDocument',
        'ref_log_id',
        'sizeInKbs'
    ];
    protected  $documentAttachmentRepo;
    protected  $srmDocumentModifyService;
    public function __construct(DocumentAttachmentsRepository $documentAttachmentsRepository, Application $app, SrmDocumentModifyService $srmDocumentModifyService)
    {
        parent::__construct($app);
        $this->documentAttachmentRepo = $documentAttachmentsRepository;
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentAttachmentsEditLog::class;
    }

    public function saveDocumentAttachments($tenderID, $documentSystemID, $versionID = null){
        try{
            $attachmentData = $this->documentAttachmentRepo->getTenderDocumentForAmd($tenderID, $documentSystemID);
            if(!empty($attachmentData)){
                foreach($attachmentData as $record){
                    $levelNo = $this->model->getLevelNo($record['attachmentID']);
                    $recordData = $record->toArray();
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['attachmentID'];
                    $recordData['version_id'] = $versionID;
                    $recordData['modify_type'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function getDocumentAttachmentEditLogData(Request $request){
        $documentSystemID = $request->input('documentSystemID');
        $documentSystemCode = $request->input('documentSystemCode');
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($documentSystemCode);

        return DocumentAttachmentsEditLog::getDocumentAttachmentEditLog($documentSystemID, $documentSystemCode, $requestData['versionID']);
    }
    public function deleteAttachment($documentAttachmentsEditLog){
        try {
            return DB::transaction(function () use ($documentAttachmentsEditLog) {
                $path = $documentAttachmentsEditLog->path;
                Helper::policyWiseDisk($documentAttachmentsEditLog->companySystemID, 'public');

                $documentAttachmentsEditLog->is_deleted = 1;
                $documentAttachmentsEditLog->save();

                if($documentAttachmentsEditLog->attachmentType === 3){
                    $exitingAmendmentRecords = DocumentAttachmentsEditLog::getAttachmentDocumentTypeBase(
                        $documentAttachmentsEditLog['companySystemID'],
                        $documentAttachmentsEditLog['documentSystemID'],
                        $documentAttachmentsEditLog['attachmentType'],
                        $documentAttachmentsEditLog['documentSystemCode'],
                        $documentAttachmentsEditLog['version_id']
                    );
                    $i = 1;
                    foreach ($exitingAmendmentRecords as $exitingAmendmentRecord){
                        DocumentAttachmentsEditLog::where('amd_id', $exitingAmendmentRecord['amd_id'])->update(['order_number' => $i]);
                        $i++;
                    }
                }
                return ['success'=> true, 'message' => 'Document Attachments deleted successfully'];
            });
        }catch (\Exception $exception){
            return ['success'=> false, 'message' => 'Unexpected Error: '. $exception->getMessage()];
        }
    }
    public function prepareNewAttachmentRecord($versionID, $input){
        $input['version_id'] = $versionID;
        $input['id'] = null;
        $input['level_no'] = 1;
        $input['approvalLevelOrder'] = $input['approvalLevelOrder'] ?? 0;
        $input['pullFromAnotherDocument'] = $input['pullFromAnotherDocument'] ?? 0;
        $input['isAutoCreateDocument'] = $input['isAutoCreateDocument'] ?? 0;
        return $input;
    }
}

<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentsEditLog;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Common\BaseRepository;
use App\Services\SrmDocumentModifyService;

/**
 * Class DocumentAttachmentsRepository
 * @package App\Repositories
 * @version April 3, 2018, 12:18 pm UTC
 *
 * @method DocumentAttachments findWithoutFail($id, $columns = ['*'])
 * @method DocumentAttachments find($id, $columns = ['*'])
 * @method DocumentAttachments first($columns = ['*'])
*/
class DocumentAttachmentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'docExpirtyDate',
        'attachmentType',
        'sizeInKbs',
        'timeStamp'
    ];

    protected $srmDocumentModifyService;
    public function __construct(Application $app, SrmDocumentModifyService $srmDocumentModifyService)
    {
        parent::__construct($app);
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentAttachments::class;
    }

    public static function getTenderDocumentForAmd($documentSystemCode, $documentSystemID){
        return DocumentAttachments::getTenderAttachments($documentSystemCode, $documentSystemID);
    }
    public function documentExistsValidation(
        $attachmentType, $attachmentDescription, $companySystemID, $documentSystemID, $documentSystemCode, $requestData, $id = 0, $masterID = 0
    ){
        $editOrAmend = $requestData['enableRequestChange'] ?? false;
        $isExist = $editOrAmend ?
            DocumentAttachmentsEditLog::checkDocumentExists($companySystemID, $documentSystemID, $attachmentType, $documentSystemCode, $attachmentDescription, $requestData['versionID'], $id, $masterID) :
            DocumentAttachments::checkDocumentExists($companySystemID, $documentSystemID, $attachmentType, $documentSystemCode, $attachmentDescription, $id);
        if($isExist){
            return ['success' => false, 'message' => 'Description already exists'];
        } else {
            return ['success' => true, 'message' => 'Validation checked successfully'];
        }
    }
    public function getExistingDocumentAttachmentRecords($attachmentType, $companySystemID, $documentSystemID, $documentSystemCode, $requestData){
        return $requestData['enableRequestChange'] ?
            DocumentAttachmentsEditLog::getAttachmentDocumentTypeBase($companySystemID, $documentSystemID, $attachmentType, $documentSystemCode, $requestData['versionID']) :
            DocumentAttachments::getAttachmentDocumentTypeBase($companySystemID, $documentSystemID, $attachmentType, $documentSystemCode);
    }
    public function updateExistAttachmentOrderNumber($exitingAmendmentRecords, $editOrAmend){
        $orderNumber = 1 ;
        foreach($exitingAmendmentRecords as $record){
            if($editOrAmend){
                DocumentAttachmentsEditLog::where('amd_id', $record['amd_id'])->update(['order_number' => $orderNumber]);
                $orderNumber++;
            }
        }
        return $orderNumber;
    }
    public function getAttachmentPreview($documentAttachments): array{
        try{
            $disk = Helper::policyWiseDisk($documentAttachments->companySystemID, 'public');

            if(!is_null($documentAttachments->path)) {
                if (Storage::disk($disk)->exists($documentAttachments->path)) {
                    $url = Storage::disk($disk)->temporaryUrl($documentAttachments->path, Carbon::now()->addHours(3));
                    return [
                        'success' => true,
                        'message' => 'Attachment retrieved successfully',
                        'data' => $url,
                        'code' => 200
                    ];
                } else {
                    return [
                        'success' => true,
                        'message' => 'Attachments not found',
                        'data' => null,
                        'code' => 200
                    ];
                }
            }else{
                return [
                    'success' => false,
                    'message' => 'Attachment is not attached',
                    'data' => null,
                    'code' => 404
                ];
            }
        } catch (\Exception $exception){
            return [
                'success' => false,
                'message' => 'Unexpected Error: '. $exception->getMessage(),
                'data' => null,
                'code' => 404
            ];
        }
    }
}

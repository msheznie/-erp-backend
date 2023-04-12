<?php

namespace App\Repositories;

use App\Models\DocumentAttachmentsEditLog;
use InfyOm\Generator\Common\BaseRepository;

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
        'docExpirtyDate',
        'documentID',
        'documentSystemCode',
        'documentSystemID',
        'envelopType',
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

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentAttachmentsEditLog::class;
    }
}

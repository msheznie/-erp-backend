<?php

namespace App\Repositories;

use App\Models\SrpErpDocumentAttachments;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrpErpDocumentAttachmentsRepository
 * @package App\Repositories
 * @version January 6, 2022, 3:53 pm +04
 *
 * @method SrpErpDocumentAttachments findWithoutFail($id, $columns = ['*'])
 * @method SrpErpDocumentAttachments find($id, $columns = ['*'])
 * @method SrpErpDocumentAttachments first($columns = ['*'])
*/
class SrpErpDocumentAttachmentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'documentSubID',
        'documentSystemCode',
        'attachmentDescription',
        'myFileName',
        'docExpiryDate',
        'dateofIssued',
        'fileType',
        'fileSize',
        'segmentID',
        'segmentCode',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrpErpDocumentAttachments::class;
    }
}

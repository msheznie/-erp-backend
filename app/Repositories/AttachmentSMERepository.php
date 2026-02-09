<?php

namespace App\Repositories;

use App\Models\AttachmentSME;
use App\Repositories\BaseRepository;

/**
 * Class AttachmentSMERepository
 * @package App\Repositories
 * @version January 21, 2022, 9:53 am +04
 *
 * @method AttachmentSME findWithoutFail($id, $columns = ['*'])
 * @method AttachmentSME find($id, $columns = ['*'])
 * @method AttachmentSME first($columns = ['*'])
*/
class AttachmentSMERepository extends BaseRepository
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
        return AttachmentSME::class;
    }
}

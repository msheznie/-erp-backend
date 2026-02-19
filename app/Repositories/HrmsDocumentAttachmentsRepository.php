<?php

namespace App\Repositories;

use App\Models\HrmsDocumentAttachments;
use App\Repositories\BaseRepository;

/**
 * Class HrmsDocumentAttachmentsRepository
 * @package App\Repositories
 * @version September 18, 2019, 12:06 pm +04
 *
 * @method HrmsDocumentAttachments findWithoutFail($id, $columns = ['*'])
 * @method HrmsDocumentAttachments find($id, $columns = ['*'])
 * @method HrmsDocumentAttachments first($columns = ['*'])
*/
class HrmsDocumentAttachmentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'companySystemID',
        'documentID',
        'documentSystemID',
        'documentSystemCode',
        'attachmentDescription',
        'myFileName',
        'docExpirtyDate',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HrmsDocumentAttachments::class;
    }
}

<?php

namespace App\Repositories;

use App\Models\DocumentAttachments;
use InfyOm\Generator\Common\BaseRepository;

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

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentAttachments::class;
    }
}

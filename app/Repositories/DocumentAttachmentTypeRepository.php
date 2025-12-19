<?php

namespace App\Repositories;

use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentAttachmentType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentAttachmentTypeRepository
 * @package App\Repositories
 * @version April 3, 2018, 12:19 pm UTC
 *
 * @method DocumentAttachmentType findWithoutFail($id, $columns = ['*'])
 * @method DocumentAttachmentType find($id, $columns = ['*'])
 * @method DocumentAttachmentType first($columns = ['*'])
*/
class DocumentAttachmentTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'description',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentAttachmentType::class;
    }

}

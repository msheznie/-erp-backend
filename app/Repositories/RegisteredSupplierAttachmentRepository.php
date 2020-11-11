<?php

namespace App\Repositories;

use App\Models\RegisteredSupplierAttachment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RegisteredSupplierAttachmentRepository
 * @package App\Repositories
 * @version November 9, 2020, 4:01 pm +04
 *
 * @method RegisteredSupplierAttachment findWithoutFail($id, $columns = ['*'])
 * @method RegisteredSupplierAttachment find($id, $columns = ['*'])
 * @method RegisteredSupplierAttachment first($columns = ['*'])
*/
class RegisteredSupplierAttachmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'resgisteredSupplierID',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'sizeInKbs',
        'path',
        'isUploaded'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RegisteredSupplierAttachment::class;
    }
}

<?php

namespace App\Repositories;

use App\Models\FieldMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FieldMasterRepository
 * @package App\Repositories
 * @version August 10, 2018, 8:33 am UTC
 *
 * @method FieldMaster findWithoutFail($id, $columns = ['*'])
 * @method FieldMaster find($id, $columns = ['*'])
 * @method FieldMaster first($columns = ['*'])
*/
class FieldMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'fieldShortCode',
        'fieldName',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'companyId'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FieldMaster::class;
    }
}

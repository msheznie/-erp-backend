<?php

namespace App\Repositories;

use App\Models\TaxFormulaMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TaxFormulaMasterRepository
 * @package App\Repositories
 * @version April 24, 2018, 6:13 am UTC
 *
 * @method TaxFormulaMaster findWithoutFail($id, $columns = ['*'])
 * @method TaxFormulaMaster find($id, $columns = ['*'])
 * @method TaxFormulaMaster first($columns = ['*'])
*/
class TaxFormulaMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'Description',
        'taxType',
        'companySystemID',
        'companyID',
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
        return TaxFormulaMaster::class;
    }
}
